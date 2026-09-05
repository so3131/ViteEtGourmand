<?php

namespace App\Controllers\AdminController;

require_once dirname(__DIR__, 2) . '/config/constants.php';

use App\Controllers\AuthController\Auth;
use App\Managers\MongoStatsManager;
use App\Managers\MenuManager;

class StatsAdminController
{
    //function pour afficher la page de statistiques admin
    public static function adminStats(\PDO $db)
    {
        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        Auth::check([ROLE_ADMIN]);

        //Récupération des filtres depuis l'URL
        $menuId = $_GET['menu_id'] ?? null;
        $dateDebut = $_GET['date_debut'] ?? null;
        $dateFin = $_GET['date_fin'] ?? null;

        $mongoManager = new MongoStatsManager();

        //Données pour les graphiques : 
        $statsMenus = $mongoManager->getStatsByMenuFromMongoDB($dateDebut, $dateFin, null);

        // Données textuelles globales
        $periodStats = $mongoManager->getSalesAndOrdersByPeriodFromMongoDB($dateDebut, $dateFin);
        $caGlobal = $periodStats['totalCA'];
        $totalOrders = $periodStats['totalOrders'];


        //Données textuelles filtrées
        $chiffreAffaires = $mongoManager->getSalesFilteredFromMongoDB($dateDebut, $dateFin, $menuId);
        $totalCommandesFiltrees = $mongoManager->getCountFilteredFromMongoDB($dateDebut, $dateFin, $menuId);

        //Récupération du nom du menu sélectionné 
        $nomMenuSelectionne = null;
        if (!empty($menuId)) {
            $menusList = MenuManager::get($db, null, [], true); // Si tu veux garder ça juste pour le <select> et le libellé
            foreach ($menusList as $m) {
                if ($m['menu_id'] == $menuId) {
                    $nomMenuSelectionne = $m['titre'];
                    break;
                }
            }
        }

        $menus = MenuManager::get($db, null, [], true);
        //Compteurs structurels (Thèmes, Régimes, etc.) depuis le SQL
        $statsSql = \App\Managers\StatAdminManager::getStatsFromSQL($db);


        $title = "Statistiques - Vite&Gourmand";
        $specific_styles = [
            'assets/css/bootstrap/bootstrap.min.css',
           'assets/css/Admin/AdminEmployee.css'
        ];
        $specific_scripts = ["https://cdn.jsdelivr.net/npm/chart.js", 'assets/javascript/chart.js'];

        require_once ROOT_PATH . '/app/views/layout/admin_header.php';
        require_once ROOT_PATH . '/app/views/admin/stats.admin.view.php';
        require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
    }
}
