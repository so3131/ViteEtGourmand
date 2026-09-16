<?php

namespace App\Controllers\StaffCommon;

use App\Controllers\AuthController\Auth;
use App\Managers\MenuManager;
use App\Managers\PlatManager;
use App\Helpers\SecurityManager;
// Class EditMenuController pour gérer l'édition des menus (accessible aux admins et employés)
class EditMenuController
{
    //function pour afficher la page d'édition d'un menu
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

        // Récupérer les données du menu
        $menu = MenuManager::getById($db, (int)$menu_id);

        if (!$menu) {
            $_SESSION['error'] = "Menu introuvable en base de données.";
            header('Location: index.php?page=menu-management');
            exit();
        }

        // Récupérer tous les plats disponibles pour les checkboxes
        $all_plats = PlatManager::getAll($db);

        // Récupérer les thèmes et régimes pour les selects
        $all_themes = MenuManager::getAllThemes($db);
        $all_regimes = MenuManager::getAllRegimes($db);

        // Récupérer les plats associés au menu a éditer pour pré-cocher les checkboxes
        $platsAssocies = MenuManager::getPlatsByMenuId($db, (int)$menu_id);

        // Extraire uniquement un tableau simple contenant les IDs des plats associés pour faciliter la vérification dans la vue
        $selected_plats_ids = [];
        if (is_array($platsAssocies)) {
            $selected_plats_ids = array_column($platsAssocies, 'plat_id');
        }


        $specific_styles = [
            'assets/css/AdminEmployee/MenuManagement.css',
            'assets/css/AdminEmployee/AdminEmployee.css'
        ];
        $specific_scripts = [
            'assets/javascript/EditMenu.js'
        ];


        $userRole = $_SESSION['role_id'] ?? null;
        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/Views/layout/admin_header.php';
        } else {
            require_once ROOT_PATH . '/app/Views/layout/employee_header.php';
        }
        require_once ROOT_PATH . '/app/Views/StaffCommon/edit.menu.view.php';
        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/Views/layout/admin_footer.php';
        } else {
            require_once ROOT_PATH . '/app/Views/layout/employee_footer.php';
        }
    }
    //function pour mettre à jour un menu
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
            $menu_id = $_GET['menu_id'] ?? $_GET['id'] ?? null;

            SecurityManager::validatePost('?page=edit-menu&id=' . $menu_id);

            $titre = $_POST['titre'] ?? '';
            $prix = $_POST['prix'] ?? 0;
            $quantite = max(0, (int)($_POST['quantite'] ?? 0));
            $nb_min = $_POST['nombre_personne_minimum'] ?? null;
            $description = $_POST['description_menu'] ?? '';
            $theme_id = !empty($_POST['theme_id']) ? $_POST['theme_id'] : null;
            $regime_id = !empty($_POST['regime_id']) ? $_POST['regime_id'] : null;
            $delai = $_POST['delai_commande'] ?? 0;
            $stockage = $_POST['conditions_stockage'] ?? null;
            $plats = $_POST['plats'] ?? [];

            try {
                MenuManager::update($db, (int)$menu_id, [
                    'titre'                   => $titre,
                    'prix'                    => $prix,
                    'quantite'                => $quantite,
                    'nombre_personne_minimum' => $nb_min,
                    'description'             => $description,
                    'theme_id'                => $theme_id,
                    'regime_id'               => $regime_id,
                    'delai_commande'          => $delai,
                    'conditions_stockage'     => $stockage,
                ], $plats);

                $_SESSION['success'] = "Le menu a été modifié avec succès !";
                header('Location: index.php?page=menu-management');
                exit();
            } catch (\Exception $e) {
                $_SESSION['error'] = "Erreur lors de la modification : " . $e->getMessage();
                header('Location: index.php?page=edit-menu&id=' . $menu_id);
                exit();
            }
        }
    }
}
