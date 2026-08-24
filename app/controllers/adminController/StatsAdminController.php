<?php

namespace App\Controllers\AdminController;

require_once dirname(__DIR__, 2) . '/config/constants.php';

use App\Controllers\AuthController\Auth;
use App\Managers\MongoStatsManager;
use App\Managers\MenuManager; // Gardé uniquement pour alimenter le <select> du formulaire HTML si les noms des menus y sont stockés, ou tu peux aussi les lister via Mongo si tu préfères.

class StatsAdminController
{
    public static function adminStats(\PDO $db)
    {
        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        Auth::check([ROLE_ADMIN]);
        
        // 1. Récupération des filtres depuis l'URL
        $menuId = $_GET['menu_id'] ?? null;
        $dateDebut = $_GET['date_debut'] ?? null;
        $dateFin = $_GET['date_fin'] ?? null;

        $mongoManager = new MongoStatsManager();

        // 2. Données pour les graphiques : 
        $statsMenus = $mongoManager->getStatsByMenuFromMongoDB($dateDebut, $dateFin, null);

        // 3. Données textuelles globales (pour les cartes du haut qui ne bougent pas)
      $periodStats = $mongoManager->getSalesAndOrdersByPeriodFromMongoDB($dateDebut, $dateFin);
$caGlobal = $periodStats['totalCA'];
$totalOrders = $periodStats['totalOrders'];
        

        // 4. Données textuelles filtrées (quand l'admin choisit un menu et/ou des dates)
        $chiffreAffaires = $mongoManager->getSalesFilteredFromMongoDB($dateDebut, $dateFin, $menuId);
        $totalCommandesFiltrees = $mongoManager->getCountFilteredFromMongoDB($dateDebut, $dateFin, $menuId);

        // 5. Récupération du nom du menu sélectionné 
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
        // 6. Compteurs structurels (Thèmes, Régimes, etc.) depuis le SQL
     $statsSql = \App\Managers\StatAdminManager::getStatsFromSQL($db);
$totalThemes = $statsSql['total_themes'] ?? 0;
$totalRegimes = $statsSql['total_regimes'] ?? 0;
        

        $title = "Statistiques - Vite&Gourmand";
        $specific_styles = [
            'assets/css/bootstrap/bootstrap.min.css',
            'assets/css/Admin/OrderManagement.css',
            'assets/css/Admin/AdminEmployee.css'
        ];
        $specific_scripts = ["https://cdn.jsdelivr.net/npm/chart.js", 'assets/javascript/chart.js'];

        require_once ROOT_PATH . '/app/views/layout/admin_header.php';
        require_once ROOT_PATH . '/app/views/admin/stats.admin.view.php';
        require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
    }
}