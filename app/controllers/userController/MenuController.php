<?php

namespace App\Controllers\UserController;

require_once dirname(__DIR__, 2) . '/Config/constants.php';

use App\Managers\MenuManager;
// class MenuController pour gérer l'affichage et la recherche des menus
class MenuController
{
    //function pour afficher la page de recherche de menu
    public static function searchMenu(\PDO $db)
    {
        $nomMenu = isset($_GET['nomMenu']) ? trim($_GET['nomMenu']) : '';

        try {
            if (!empty($nomMenu)) {
                $menus = MenuManager::get(
                    $db,
                    null,
                    ['titre' => $nomMenu],
                    true
                );
            } else {
                $menus = MenuManager::get($db, null, [], true);
            }
        } catch (\PDOException $e) {
            error_log("Erreur lors de la recherche du menu : " . $e->getMessage());
            $menus = [];
        }

        $title = "Rechercher un menu - Vite Gourmand";

        $specific_styles = ["assets/css/styleSearch.css", "assets/css/trame.css"];
        $specific_scripts = ["assets/javascript/recherche.js"];

        require_once ROOT_PATH . '/app/Views/layout/header.php';
        require_once ROOT_PATH . '/app/Views/user/search.Menu.view.php';
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
    }

    //function pour filtrer les menus en JSON pour l'interface utilisateur
    public static function filterJson(\PDO $db)
    {
        $filters = [
            'theme_id'                => $_GET['theme_id'] ?? null,
            'regime_id'               => $_GET['regime_id'] ?? null,
            'prix_min'                => $_GET['prix_min'] ?? null,
            'prix_max'                => $_GET['prix_max_slider'] ?? null,
            'nombre_personne_minimum' => $_GET['nombre_personne_minimum'] ?? null
        ];

        header('Content-Type: application/json');

        try {
            $menus = MenuManager::get($db, null, $filters, true);
            echo json_encode($menus);
        } catch (\Exception $e) {
            error_log("Erreur filterJson : " . $e->getMessage());
            echo json_encode([]);
        }

        exit;
    }
    //function pour afficher les détails d'un menu spécifique avec ses plats et allergènes
    public static function showMenuDetails(\PDO $db)
    {
        $canOrder = (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === ROLE_USER);
        //Récupérer l'ID depuis l'URL
        $menuID = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : 0;

        //Récupérer les données
        $result = MenuManager::get($db, $menuID);
        $menu = !empty($result) ? $result[0] : false;

        // Si le menu n'existe pas, rediriger vers la page de recherche
        if (!$menu) {
            header('Location: index.php?page=search');
            exit();
        }

        $plats = MenuManager::getPlatsByMenuId($db, $menuID);
        $allergenes = MenuManager::getAllergenesByMenuId($db, $menuID);
        $title = "Détail du menu - Vite Gourmand";


        $specific_styles = ["assets/css/styleSearch.css"];
        $specific_scripts = [];
        require_once ROOT_PATH . '/app/Views/layout/header.php';
        require_once ROOT_PATH . '/app/Views/user/detail.menu.view.php';
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
    }
}
