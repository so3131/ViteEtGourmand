<?php
// StatsAdminController

require_once dirname(__DIR__, 2) . '/config/Constants.php';
require_once __DIR__ . '/../authController/Auth.php';

use App\Controllers\AuthController\Auth;

class StatsAdminController
{
    public static function adminStats(PDO $db)
    {
        Auth::check([ROLE_ADMIN]);

        // Récupérer les stats
        $statsQuery = "SELECT COUNT(*) as total_commandes FROM vg_commande";
        $stmt = $db->prepare($statsQuery);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $title = "Statistiques - Admin";
        $specific_styles = ["assets/css/admin-stats.css"];

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/admin/stats.admin.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
