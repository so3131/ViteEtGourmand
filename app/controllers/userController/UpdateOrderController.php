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
    //function pour mettre à jour une commande spécifique après vérification de l'état de la commande et des données fournies par l'utilisateur
public static function updateOrder(\PDO $db, ?int $commande_id)
    {
        if (ob_get_length()) {
            ob_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
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
                // On récupère aussi le prénom et l'email de l'utilisateur connecté via sa table
                $sqlCheck = "SELECT c.utilisateur_id, c.statut, m.titre as menu_titre, 
                    c.prix_total, c.nombre_personne,
                    c.date_prestation, c.heure_livraison, 
                    l.adresse, l.ville, l.code_postal, l.id as lieu_id,
                    u.prenom as utilisateur_prenom, u.email as utilisateur_email
               FROM vg_commande c
               JOIN vg_menu m ON c.menu_id = m.menu_id
               JOIN vg_lieu_prestation l ON c.lieu_prestation_id = l.id
               JOIN vg_utilisateur u ON c.utilisateur_id = u.utilisateur_id
               WHERE c.commande_id = :orderID";
                
                $stmtCheck = $pdo->prepare($sqlCheck);
                $stmtCheck->execute(['orderID' => $commande_id]);
                $result = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

                if (!$result || (int)$result['utilisateur_id'] !== (int)$_SESSION['user_id']) {
                    throw new \Exception("Commande non trouvée ou accès refusé.");
                }

                //double securité pour éviter les annulations intempestives
                if ($result['statut'] !== 'en_attente') {
                    throw new \Exception("Seules les commandes en attente peuvent être modifiées ou supprimées.");
                }
                
                $menuData = MenuManager::getById($db, (int)$data['menu_id']);
               if (!empty($data['adresse_livraison'])) {
    $lieu_id = \App\Managers\LieuManager::getOrInsert(
        $db,
        $data['adresse_livraison'] ?? '',
        $data['code_postal'] ?? '',
        $data['ville'] ?? '',
        !empty($data['lat']) ? (float)$data['lat'] : null,
        !empty($data['lon']) ? (float)$data['lon'] : null
    );
} else {
    // Sinon, on retombe sur l'ID brut envoyé 
    $lieu_id = (int)($data['lieu_prestation_id'] ?? 0);
}
                $delaiCommande = (int)($menuData['delai_commande'] ?? 0);
                
                $dateMinimale = new \DateTime('today');
                $dateMinimale->modify('+' . $delaiCommande . ' days');
                
                if (empty($data['date_prestation'])) {
                    throw new \Exception("La date de prestation est obligatoire.");
                }
                
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
                
                // 1. Récupération des infos du lieu pour calculer les frais
        $stmtLieu = $db->prepare("SELECT ville, latitude, longitude FROM vg_lieu_prestation WHERE id = ?");
        $stmtLieu->execute([$lieu_id]);
        $lieu = $stmtLieu->fetch(\PDO::FETCH_ASSOC);

        $fraisLivraison = 0.00;
        if ($lieu && !empty($lieu['latitude']) && !empty($lieu['longitude'])) {
            $distance = \App\Models\Order::calculerDistanceRouteVersClient(
                (float)$lieu['latitude'], 
                (float)$lieu['longitude']
            );
            $fraisLivraison = \App\Models\Order::calculerFraisLivraisonParKm(
                $lieu['ville'], 
                $distance
            );
        }

        // 2. Validation de la quantité et calcul du total
        if (!$menu->estQuantiteValide((int)$data['nombre_personne'])) {
            throw new \Exception("Le nombre de personnes doit être au minimum de " . $menu->getMinimumRequis());
        }

        $nouveauPrix = $menu->calculerTotal(
            (int)$data['nombre_personne'],
            (float)$fraisLivraison,
            (float)$depot
        );
              // modifier la commande de la db vg_commande
                $sqlUpdate = "UPDATE vg_commande 
              SET date_prestation = :date, 
                  heure_livraison = :heure, 
                  menu_id = :menu_id, 
                  lieu_prestation_id = :lieu_id, 
                  prix_total = :prix,
                  pret_materiel = :materiel, 
                  depot_garantie = :depot,
                  nombre_personne = :nb_personne
              WHERE commande_id = :id AND utilisateur_id = :user_id";

                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    'date'       => $data['date_prestation'],
                    'heure'      => $data['heure_livraison'],
                    'menu_id'    => (int)$data['menu_id'],
                    'lieu_id'    => $lieu_id,
                    'prix'       => $nouveauPrix,
                    'materiel'   => $rental,
                    'depot'      => $depot,
                    'nb_personne'=> (int)$data['nombre_personne'],
                    'id'         => $commande_id,
                    'user_id'    => $_SESSION['user_id']
                ]);

                try {
                    $stmtLieu = $pdo->prepare("SELECT adresse, code_postal, ville FROM vg_lieu_prestation WHERE id = :id");
                    $stmtLieu->execute(['id' => $lieu_id]);
                    $lieuInfo = $stmtLieu->fetch(\PDO::FETCH_ASSOC);

                    $lieuTexte = $lieuInfo['adresse'] . ', ' . $lieuInfo['code_postal'] . ' ' . $lieuInfo['ville'];

                    // Envoi de l'e-mail avec les bonnes variables issues de la BDD et du formulaire
                    \App\Helpers\MailService::sendOrderUpdateEmail($result['utilisateur_email'], [
                        'prenom'          => $result['utilisateur_prenom'],
                        'menu'            => $menuData['titre'],
                        'quantite'        => (int)$data['nombre_personne'],
                        'date_prestation' => $data['date_prestation'],
                        'heure_livraison' => $data['heure_livraison'],
                        'lieu'            => $lieuTexte,
                        'prix_total'      => $nouveauPrix
                    ]);
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
//function pour recalculer le prix total d'une commande en fonction des modifications apportées par l'utilisateur
  public static function recalculerPrix(\PDO $db, ?int $commande_id = null)
{
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
        
        $commande_id = isset($_GET['commande_id']) ? (int)$_GET['commande_id'] : 0;

        // 1. Gestion propre du lieu_id
        $lieu_id = 0;
        if (!empty($data['adresse_livraison']) && !empty($data['lat']) && !empty($data['lon'])) {
            // Si l'utilisateur a saisi une nouvelle adresse via l'autocomplétion
            $lieu_id = \App\Managers\LieuManager::getOrInsert(
                $db,
                $data['adresse_livraison'],
                $data['code_postal'] ?? '',
                $data['ville'] ?? '',
                (float)$data['lat'],
                (float)$data['lon']
            );
        } elseif ($commande_id > 0) {
            // Sinon, on va chercher le lieu_prestation_id d'origine de la commande en base
            $stmtOrder = $db->prepare("SELECT lieu_prestation_id FROM vg_commande WHERE commande_id = ?");
            $stmtOrder->execute([$commande_id]);
            $lieu_id = (int)$stmtOrder->fetchColumn();
        } else {
            $lieu_id = (int)($data['lieu_prestation_id'] ?? 0);
        }

        // 2. Récupération des informations du lieu pour calculer les frais
        $stmtLieu = $db->prepare("SELECT ville, latitude, longitude FROM vg_lieu_prestation WHERE id = ?");
        $stmtLieu->execute([$lieu_id]);
        $lieu = $stmtLieu->fetch(\PDO::FETCH_ASSOC);

        $frais = 0.00;
        if ($lieu && !empty($lieu['latitude']) && !empty($lieu['longitude'])) {
            // Calcul de la distance route via ton modèle Order
            $distance = \App\Models\Order::calculerDistanceRouteVersClient(
                (float)$lieu['latitude'], 
                (float)$lieu['longitude']
            );

            // Calcul des frais selon la ville et les kilomètres
            $frais = \App\Models\Order::calculerFraisLivraisonParKm(
                $lieu['ville'], 
                $distance
            );
        }

        // 3. Récupération du menu et calcul du total
        $quantite = (int)($data['nombre_personne'] ?? 0);
        $menu_id = (int)($data['menu_id'] ?? 0);

        $menuData = MenuManager::getById($db, $menu_id);

        if (!$menuData) {
            echo json_encode(['nouveau_prix' => '0.00', 'error' => 'Menu introuvable']);
            exit();
        }

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

        echo json_encode([
            'nouveau_prix' => number_format($total, 2, '.', ''),
            'frais_livraison' => number_format((float)$frais, 2, '.', '') // <-- Ajouté ici
        ]);
        exit();

    } catch (\Exception $e) {
        echo json_encode(['nouveau_prix' => '0.00', 'error' => $e->getMessage()]);
        exit();
    }
}
//function pour revenir à la page de modification d'une commande spécifique avec les détails du menu et les lieux disponibles
    public static function cancelEditOrder(\PDO $db)
    {
        //Si l'utilisateur appuie sur le bouton annuler la modification, on le redirige vers le dashboard
        header('Location: index.php?page=dashboard-user');
        exit();
    }

//function pour afficher la page de modification d'une commande spécifique avec les détails du menu et les lieux disponibles
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
