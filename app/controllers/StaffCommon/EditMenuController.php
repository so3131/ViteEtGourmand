<?php

namespace App\Controllers\StaffCommon;

use App\Controllers\AuthController\Auth;
use App\Managers\MenuManager;

class EditMenuController
{
    public static function editMenu(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        // Récupération de l'ID (gère ?menu_id= ou ?id=)
        $menu_id = $_GET['menu_id'] ?? $_GET['id'] ?? null;

        if (!$menu_id) {
            $_SESSION['error'] = "Identifiant de menu manquant.";
            header('Location: index.php?page=menu-management');
            exit();
        }

        // 1. Récupérer les données du menu via le Manager existant
        $menu = MenuManager::getById($db, (int)$menu_id);

        if (!$menu) {
            $_SESSION['error'] = "Menu introuvable en base de données.";
            header('Location: index.php?page=menu-management');
            exit();
        }

        // 2. Récupérer TOUS les plats disponibles pour les cases à cocher de la vue
        $stmtPlats = $db->query("SELECT * FROM vg_plat ORDER BY titre_plat ASC");
        $all_plats = $stmtPlats->fetchAll(\PDO::FETCH_ASSOC);

        // 3. Récupérer les plats associés à ce menu précis pour pré-cocher les cases
        $platsAssocies = MenuManager::getPlatsByMenuId($db, (int)$menu_id);

        // On extrait uniquement un tableau simple contenant les IDs (ex: [1, 4, 12])
        $selected_plats_ids = [];
        if (is_array($platsAssocies)) {
            $selected_plats_ids = array_column($platsAssocies, 'plat_id');
        }

        // Styles et scripts spécifiques
        $specific_styles = [
            'assets/css/AdminEmployee/MenuManagement.css',
            'assets/css/AdminEmployee/AdminEmployee.css'
        ];
        $specific_scripts = [
            'assets/javascript/EditMenu.js'
        ];

        // Chargement de la vue
        $userRole = $_SESSION['role_id'] ?? null;
        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_header.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_header.php';
        }
        require_once ROOT_PATH . '/app/views/StaffCommon/edit.menu.view.php';
        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
        }
    }
    public static function updateMenu(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        $menu_id = $_GET['menu_id'] ?? $_GET['id'] ?? null;

        if (!$menu_id) {
            $_SESSION['error'] = "Identifiant de menu manquant.";
            header('Location: index.php?page=menu-management');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $prix = $_POST['prix'] ?? 0;
            $quantite = $_POST['quantite'] ?? 0;
            $plats = $_POST['plats'] ?? []; // Tableau des IDs des plats cochés

            try {
                $db->beginTransaction();

                // 1. Mettre à jour les informations principales du menu
                $sql = "UPDATE vg_menu 
                        SET titre = :titre, 
                            prix_par_personne = :prix, 
                            quantite_restante = :quantite 
                        WHERE menu_id = :id";

                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'titre' => $titre,
                    'prix' => $prix,
                    'quantite' => $quantite,
                    'id' => $menu_id
                ]);

                // 2. Mettre à jour les plats associés (Supprimer les anciens, insérer les nouveaux)
                $stmtDelete = $db->prepare("DELETE FROM vg_menu_plat WHERE menu_id = :id");
                $stmtDelete->execute(['id' => $menu_id]);

                if (!empty($plats)) {
                    $stmtInsert = $db->prepare("INSERT INTO vg_menu_plat (menu_id, plat_id) VALUES (:menu_id, :plat_id)");
                    foreach ($plats as $plat_id) {
                        $stmtInsert->execute([
                            'menu_id' => $menu_id,
                            'plat_id' => $plat_id
                        ]);
                    }
                }

                $db->commit();

                // Succès
                $_SESSION['success'] = "Le menu a été modifié avec succès !";
                header('Location: index.php?page=menu-management');
                exit();
            } catch (\Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = "Erreur lors de la modification : " . $e->getMessage();
                header('Location: index.php?page=edit-menu&id=' . $menu_id);
                exit();
            }
        }
    }
}
