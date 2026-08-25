<?php

namespace App\Controllers\UserController;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once dirname(__DIR__, 2) . '/config/constants.php';

use App\Managers\MenuManager;
use App\Managers\OrderManager;
use App\models\Menu;
use App\Config\constants;

use App\Controllers\AuthController\Auth;






class UpdateOrderController
{
    public static function updateOrder(\PDO $db, ?int $commande_id)
    {
        //verif de secu
        Auth::check([ROLE_USER]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $rental = !empty($data['pret_materiel']) ? 1 : 0;
            $depot = ($rental === 1) ? DEPOT_GARANTIE_MATERIEL : 0.0;
            if (!$commande_id) {
                echo json_encode(['success' => false, 'message' => 'Identifiant de commande manquant.']);
                exit();
            }

            $pdo = $db;
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            try {
                // Vérifier que la commande appartient à l'utilisateur connecté et que statut = 'en_attente'
                $sqlCheck = "SELECT c.utilisateur_id, c.statut, m.titre as menu_titre, 
                    c.prix_total, 
                    c.date_prestation, c.heure_livraison, 
                    l.adresse, l.ville, l.code_postal
             FROM vg_commande c
             JOIN vg_menu m ON c.menu_id = m.menu_id
             JOIN vg_lieu_prestation l ON c.lieu_prestation_id = l.id
             WHERE c.commande_id = :orderID";
                $stmtCheck = $pdo->prepare($sqlCheck);
                $stmtCheck->execute(['orderID' => $commande_id]);
                $result = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

                if (!$result || (int)$result['utilisateur_id'] !== (int)$_SESSION['user_id']) {
                    throw new \Exception("Commande non trouvée ou accès refusé.");
                }

                //double securité pour éviter les annulations intempestives
                if ($result['statut'] !== 'en_attente') {
                    throw new \Exception("Seules les commandes en attente peuvent être supprimées.");
                }
                $menuData = MenuManager::getById($db, (int)$data['menu_id']);
                $lieu_id = (int)$data['lieu_prestation_id'];
$delaiCommande = (int)($menuData['delai_commande'] ?? 0);
            $dateMinimale = new \DateTime('today');
$dateMinimale->modify('+' . $delaiCommande . ' days');
            if (empty($data['date_prestation'])) {
    throw new \Exception("La date de prestation est obligatoire.");}
            $dateSelectionnee = new \DateTime($data['date_prestation']);
$dateSelectionnee->setTime(0, 0, 0);

           if ($dateSelectionnee < $dateMinimale) {
    throw new \Exception("La date de prestation doit être au minimum à J+" . $delaiCommande . ".");
            }
                if (!$menuData) {
                    throw new \Exception("Menu introuvable.");
                }

                $menu = new Menu(
                    (int)$menuData['menu_id'],
                    (int)$menuData['nombre_personne_minimum'],
                    (string)$menuData['titre'],
                    (string)$menuData['description_menu'],
                    (float)$menuData['prix_par_personne'],
                    (int)$menuData['quantite_restante'],
                    !empty($menuData['theme_id']) ? (int)$menuData['theme_id'] : null,
!empty($menuData['regime_id']) ? (int)$menuData['regime_id'] : null,
                    $menuData['theme_libelle'] ?? '',
                    $menuData['regime_libelle'] ?? ''
                );
                $fraisLivraison = OrderManager::EstimerFraisLivraison($db, $lieu_id);

                $nouveauPrix = $menu->calculerTotal(
                    (int)$data['nombre_personne'],
                    (float)$fraisLivraison,
                    (float)$depot
                );
                if (!$menu->estQuantiteValide((int)$data['nombre_personne'])) {
                    throw new \Exception("Le nombre de personnes doit être au minimum de " . $menu->getMinimumRequis());
                }
                // modifier la commande de la db vg_commande

                $sqlUpdate = "UPDATE vg_commande 
              SET date_prestation = :date, 
                  heure_livraison = :heure, 
                  menu_id = :menu_id, 
                  lieu_prestation_id = :lieu_id, 
                  prix_total = :prix,
                 pret_materiel = :materiel, 
    depot_garantie = :depot
              WHERE commande_id = :id AND utilisateur_id = :user_id";

                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    'date'     => $data['date_prestation'],
                    'heure'    => $data['heure_livraison'],
                    'menu_id'  => (int)$data['menu_id'],
                    'lieu_id'  => $lieu_id,
                    'prix'     => $nouveauPrix,
                    'materiel' => $rental,
                    'depot'    => $depot,
                    'id'       => $commande_id,
                    'user_id'  => $_SESSION['user_id']
                ]);

                try {
                    $stmtLieu = $pdo->prepare("SELECT adresse, code_postal, ville FROM vg_lieu_prestation WHERE id = :id");
                    $stmtLieu->execute(['id' => $lieu_id]);
                    $lieuInfo = $stmtLieu->fetch(\PDO::FETCH_ASSOC);

                    $orderDetails = [
                        'commande_id'     => $commande_id,
                        'menu'            => ['titre' => $menuData['titre']],
                        'total_final'     => $nouveauPrix,
                        'date_prestation' => $data['date_prestation'],
                        'heure_livraison' => $data['heure_livraison'],
                        'lieu'            => $lieuInfo['adresse'] . ', ' . $lieuInfo['code_postal'] . ' ' . $lieuInfo['ville']
                    ];

                    \App\Helpers\MailService::sendOrderUpdateEmail($_SESSION['email'], $orderDetails);
                } catch (\Exception $e) {
                    error_log("Erreur envoi mail : " . $e->getMessage());
                }
                echo json_encode(['success' => true, 'message' => '✅ Commande modifiée']);
                exit();
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit();
            }
        }
    }

    public static function recalculerPrix(\PDO $db)
{
    // Nettoie tout buffer de sortie précédent pour éviter du HTML parasite
    if (ob_get_length()) {
        ob_clean();
    }

    header('Content-Type: application/json; charset=utf-8');

    try {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) {
            echo json_encode(['nouveau_prix' => '0.00', 'error' => 'Données JSON invalides']);
            exit();
        }

        $pret_materiel = !empty($data['pret_materiel']) ? 1 : 0;
        $montant_depot = ($pret_materiel === 1) ? DEPOT_GARANTIE_MATERIEL : 0.0;
        
        $lieu_id = (int)($data['lieu_prestation_id'] ?? 0);
        $quantite = (int)($data['nombre_personne'] ?? 0);
        $menu_id = (int)($data['menu_id'] ?? 0);

        $menuData = MenuManager::getById($db, $menu_id);

        if (!$menuData) {
            echo json_encode(['nouveau_prix' => '0.00', 'error' => 'Menu introuvable']);
            exit();
        }

        $frais = OrderManager::EstimerFraisLivraison($db, $lieu_id);

        $menu = new Menu(
            (int)$menuData['menu_id'],
            (int)$menuData['nombre_personne_minimum'],
            (string)$menuData['titre'],
            (string)$menuData['description_menu'],
            (float)$menuData['prix_par_personne'],
            (int)$menuData['quantite_restante'],
            isset($menuData['theme_id']) ? (int)$menuData['theme_id'] : null,
            isset($menuData['regime_id']) ? (int)$menuData['regime_id'] : null,
            $menuData['theme_libelle'] ?? '',
            $menuData['regime_libelle'] ?? ''
        );

        $total = $menu->calculerTotal($quantite, (float)$frais, $montant_depot);

        echo json_encode(['nouveau_prix' => number_format($total, 2, '.', '')]);
        exit();

    } catch (\Exception $e) {
        // En cas d'erreur PHP, on renvoie un JSON avec l'erreur au lieu d'un HTML
        echo json_encode(['nouveau_prix' => '0.00', 'error' => $e->getMessage()]);
        exit();
    }
}
    public static function cancelEditOrder(\PDO $db)
    {
        //Si l'utilisateur appuie sur le bouton annuler la modification, on le redirige vers le dashboard
        header('Location: index.php?page=dashboard-user');
        exit();
    }


    public static function editOrderView(\PDO $db, int $commande_id)
    {
        // 1. Récupérer la commande
        $stmt = $db->prepare("SELECT * FROM vg_commande WHERE commande_id = :id");
        $stmt->execute(['id' => $commande_id]);
        $commande = $stmt->fetch(\PDO::FETCH_ASSOC);
if (!$commande) {
        throw new \Exception("Commande introuvable.");
    }

    $menuData = MenuManager::getById($db, (int)$commande['menu_id']);
    if (!$menuData) {
        throw new \Exception("Menu introuvable.");
    }
        // 2. Récupérer les lieux pour le select
        $lieux = $db->query("SELECT * FROM vg_lieu_prestation")->fetchAll();
        // 3. Afficher la vue
        $specific_scripts = ["assets/javascript/editOrder.js"];
        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/order/edit.order.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
