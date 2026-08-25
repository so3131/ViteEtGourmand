<?php

namespace App\Controllers\StaffCommon;
use App\Controllers\AuthController\Auth;
use App\Managers\OrderManager;
use App\Managers\MenuManager;
use App\models\Menu;

class EditOrderController
{
    public static function renderEditForm(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
       

        $commande_id = $_GET['commande_id'] ?? null;
        if (!$commande_id) {
            $_SESSION['error'] = "Identifiant de commande manquant.";
            header('Location: index.php?page=order-management');
            exit();
        }

        // 2. Récupérer les données de la commande
        $stmt = $db->prepare("SELECT * FROM vg_commande WHERE commande_id = :id");
        $stmt->execute(['id' => $commande_id]);
        $commande = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$commande) {
            $_SESSION['error'] = "Commande introuvable en base de données.";
            header('Location: index.php?page=order-management');
            exit();
        }

        // 3. Récupérer les données nécessaires pour le formulaire (menus, lieux, etc.)
        $menuData = MenuManager::getById($db, $commande['menu_id']);

        // Récupérer tous les lieux disponibles 
        $stmtLieux = $db->query("SELECT * FROM vg_lieu_prestation");
        $lieux = $stmtLieux->fetchAll(\PDO::FETCH_ASSOC);

        $menus = MenuManager::get($db);
        $specific_styles = [
            'assets/css/Admin/OrderManagement.css',
            'assets/css/Admin/AdminEmployee.css'
        ];
        $specific_scripts = [
            'assets/javascript/EditOrderCommon.js'
        ];
            // Chargement de la vue
      $userRole = $_SESSION['role_id'] ?? null;

$userRole = $_SESSION['role_id'] ?? null;

if ((int)$userRole === ROLE_ADMIN) {
    require_once ROOT_PATH . '/app/views/layout/admin_header.php';
} else {
    require_once ROOT_PATH . '/app/views/layout/employee_header.php';
}
        require_once ROOT_PATH . '/app/views/StaffCommon/edit.order.view.php';

     if ((int)$userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
        }
    }
    public static function processUpdate(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération de l'ID de la commande (soit par POST, soit par GET selon ton formulaire)
            $commande_id = $_POST['commande_id'] ?? $_GET['commande_id'] ?? null;

            if (!$commande_id) {
                $_SESSION['error'] = "Identifiant de commande manquant.";
                header('Location: index.php?page=edit-order-common&commande_id=' . $commande_id);
                exit();
            }

            // RÈGLE MÉTIER ADMIN : Vérification du contact et du motif obligatoires
            $mode_contact = $_POST['mode_contact'] ?? null;
            $motif = $_POST['motif'] ?? null;

            if (empty($mode_contact) || empty($motif)) {
                $_SESSION['error'] = "Le mode de contact et le motif sont obligatoires pour un administrateur.";
                header('Location: index.php?page=edit-order-common&commande_id=' . $commande_id);
                exit();
            }

            $rental = !empty($_POST['pret_materiel']) ? 1 : 0;
            $depot = ($rental === 1) ? DEPOT_GARANTIE_MATERIEL : 0.0;

            $pdo = $db;
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            try {

                $sqlCheck = "SELECT c.utilisateur_id, c.statut, m.titre as menu_titre, 
                                c.prix_total, c.date_prestation, c.heure_livraison, 
                                l.adresse, l.ville, l.code_postal
                         FROM vg_commande c
                         JOIN vg_menu m ON c.menu_id = m.menu_id
                         JOIN vg_lieu_prestation l ON c.lieu_prestation_id = l.id
                         WHERE c.commande_id = :orderID";

                $stmtCheck = $pdo->prepare($sqlCheck);
                $stmtCheck->execute(['orderID' => $commande_id]);
                $result = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

                if (!$result) {
                    throw new \Exception("Commande non trouvée.");
                }

                $menu_id = (int)($_POST['menu_id'] ?? 0);
                $menuData = MenuManager::getById($db, $menu_id);
                if (!$menuData) {
                    throw new \Exception("Menu introuvable.");
                }

                $lieu_id = (int)($_POST['lieu_prestation_id'] ?? 0);
                $nombre_personne = (int)($_POST['nombre_personne'] ?? 0);

                // Validation des délais
                $delaiCommande = (int)($menuData['delai_commande'] ?? 0);
                $dateMinimale = new \DateTime('today');
                $dateMinimale->modify('+' . $delaiCommande . ' days');

                if (empty($_POST['date_prestation'])) {
                    throw new \Exception("La date de prestation est obligatoire.");
                }

                $dateSelectionnee = new \DateTime($_POST['date_prestation']);
                $dateSelectionnee->setTime(0, 0, 0);

                if ($dateSelectionnee < $dateMinimale) {
                    throw new \Exception("La date de prestation doit être au minimum à J+" . $delaiCommande . ".");
                }

                // Construction de l'objet Menu pour calcul
                $menu = new Menu(
                    (int)$menuData['menu_id'],
                    (int)$menuData['nombre_personne_minimum'],
                    (string)$menuData['titre'],
                    (string)$menuData['description_menu'],
                    (float)$menuData['prix_par_personne'],
                    (int)$menuData['quantite_restante'],
                    (int)$menuData['theme_id'],
                    (int)$menuData['regime_id'],
                    $menuData['theme_libelle'] ?? '',
                    $menuData['regime_libelle'] ?? ''
                );

                if (!$menu->estQuantiteValide($nombre_personne)) {
                    throw new \Exception("Le nombre de personnes doit être au minimum de " . $menu->getMinimumRequis());
                }

                $fraisLivraison = OrderManager::EstimerFraisLivraison($db, $lieu_id);
                $nouveauPrix = $menu->calculerTotal($nombre_personne, (float)$fraisLivraison, (float)$depot);


                $sqlUpdate = "UPDATE vg_commande 
                          SET date_prestation = :date, 
                              heure_livraison = :heure, 
                              menu_id = :menu_id, 
                              lieu_prestation_id = :lieu_id, 
                              prix_total = :prix,
                              pret_materiel = :materiel, 
                              depot_garantie = :depot
                          WHERE commande_id = :id";

                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    'date'     => $_POST['date_prestation'],
                    'heure'    => $_POST['heure_livraison'],
                    'menu_id'  => $menu_id,
                    'lieu_id'  => $lieu_id,
                    'prix'     => $nouveauPrix,
                    'materiel' => $rental,
                    'depot'    => $depot,
                    'id'       => $commande_id
                ]);

                // Optionnel : enregistrer le motif et le mode de contact dans une table d'historique 


                $_SESSION['success'] = "La commande a été modifiée avec succès après contact client ($mode_contact).";
                header('Location: index.php?page=order-management');
                exit();
            } catch (\Exception $e) {
                $_SESSION['error'] = "Erreur : " . $e->getMessage();
                header('Location: index.php?page=edit-order-common&commande_id=' . $commande_id);
                exit();
            }
        }
    }
    public static function recalculerPrix(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        $data = json_decode(file_get_contents('php://input'), true);
        $pret_materiel = !empty($data['pret_materiel']) ? 1 : 0;
        $montant_depot = ($pret_materiel === 1) ? DEPOT_GARANTIE_MATERIEL : 0.0;
        error_log(print_r($data, true));
        $lieu_id = (int)($data['lieu_prestation_id'] ?? 0);
        $quantite = (int)($data['nombre_personne'] ?? 0);
        $menu_id = (int)($data['menu_id'] ?? 0);

        $menuData = MenuManager::getById($db, $menu_id);


        // Vérification AVANT d'utiliser $menuData
        if (!$menuData) {
            echo json_encode(['nouveau_prix' => '0.00']);
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
    }
    public static function cancelEditOrder(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        header('Location: index.php?page=order-management');
        exit();
    }
}
