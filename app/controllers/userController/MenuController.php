<?php
// MenuController - Gestion des menus et recherche

require_once dirname(__DIR__, 2) . '/config/Constants.php';
require_once ROOT_PATH . '/app/models/MenuManager.php';

class MenuController
{
    /**
     * Afficher la page de recherche
     */
    public static function searchMenu(PDO $db)
    {
        $menus = MenuManager::get($db);
        
        $title = "Recherche - Vite & Gourmand";
        $specific_fonts = ["https://fonts.googleapis.com/css?family=Lexend&display=swap"];
        $specific_styles = ["assets/css/search.css"];
        $specific_scripts = ["assets/javascript/recherche.js"];

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/search.Menu.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Afficher les détails d'un menu
     */
    public static function showMenuDetails(PDO $db)
    {
        $menuID = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : 0;
        $menu = MenuManager::get($db, $menuID);

        if (!$menu) {
            header('Location: index.php?page=search');
            exit();
        }

        $title = htmlspecialchars($menu[0]['titre']) . " - Vite & Gourmand";
        $specific_fonts = ["https://fonts.googleapis.com/css?family=Lexend&display=swap"];
        $specific_styles = ["assets/css/details.css"];

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/details.menu.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }

    /**
     * Retourner les menus filtrés en JSON
     */
    public static function filterJson(PDO $db)
    {
        $filters = [
            'prix_max' => $_GET['prix_max_slider'] ?? null,
            'nombre_personne_minimum' => $_GET['nombre_personne_minimum'] ?? null,
        ];

        $menus = MenuManager::get($db, null, $filters);

        header('Content-Type: application/json');
        echo json_encode($menus);
        exit;
    }
}
