<?php
namespace App\Controllers\UserController;
require_once dirname(__DIR__, 2) . '/config/constants.php';

use App\Managers\MenuManager;

class MenuController
{
    // page de recherche
    public static function searchMenu(\PDO $db)
    {
        $nomMenu = isset($_GET['nomMenu']) ? trim($_GET['nomMenu']) : '';

        try {
            if (!empty($nomMenu)) {
                $menus = MenuManager::get($db, null, ['titre' => $nomMenu],
        true);
            } else {
                $menus = MenuManager::get($db, null, [], true);
            }
        } catch (\PDOException $e) {
            error_log("Erreur lors de la recherche du menu : " . $e->getMessage());
            $menus = [];
        }

        // Variables pour la vue
        $title = "Rechercher un menu - Vite Gourmand";
        $specific_fonts = ["https://fonts.googleapis.com/css?family=Lexend&display=swap"];
        $specific_styles = ["assets/css/styleSearch.css"];
        $specific_scripts = ["assets/javascript/recherche.js"];

        // Chargement des composants
        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/search.Menu.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }

    //(Filtres dynamiques)
    public static function filterJson(\PDO $db)
    {
        // Si select a une valeur, l'utiliser. Sinon utiliser le slider.
        error_log("DEBUG: filterJson appelée");

        $filters = [
            'theme_id'                => $_GET['theme_id'] ?? null,
            'regime_id'               => $_GET['regime_id'] ?? null,
            'prix_min'                => $_GET['prix_min'] ?? null,
            'prix_max'                => $_GET['prix_max_slider'] ?? null,
            'nombre_personne_minimum' => $_GET['nombre_personne_minimum'] ?? null
        ];
        error_log("DEBUG: Filters = " . json_encode($filters));


        $menus = MenuManager::get($db, null, $filters, true);

        error_log("DEBUG: Menus retournés = " . print_r($menus, true));

        header('Content-Type: application/json');
        error_log("DEBUG: Avant json_encode");
        $json = json_encode($menus);
        error_log("JSON error: " . json_last_error_msg());
        error_log("JSON result length: " . strlen($json));
        error_log("PHOTO DEBUG = " . print_r($menus[0]['plats_structures'] ?? null, true));

        echo $json;
        exit;
    }
    public static function showMenuDetails(\PDO $db)
    {
        $canOrder = (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === ROLE_USER);
        // 1. Récupération de l'ID depuis l'URL
        $menuID = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : 0;

        // 2. Récupération des données via tes Managers
        $result = MenuManager::get($db, $menuID);
        $menu = !empty($result) ? $result[0] : false;


        if (!$menu) {
            // Le menu n'existe pas en base : on redirige vers search
            header('Location: index.php?page=search');
            exit();
        }

        $plats = MenuManager::getPlatsByMenuId($db, $menuID);
        $allergenes = MenuManager::getAllergenesByMenuId($db, $menuID);
        $title = "Détail du menu - Vite Gourmand";

        $specific_fonts = ["https://fonts.googleapis.com/css?family=Lexend&display=swap"];
        $specific_styles = ["assets/css/styleSearch.css"];
        $specific_scripts = [];
        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/detail.menu.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
