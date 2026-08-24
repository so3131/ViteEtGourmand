<?php

namespace App\Controllers\AdminController;

use App\Controllers\AuthController\Auth;
use App\Managers\MongoStatsManager;

class DashboardAdminController
{
    public static function adminDashboard(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_horaire'])) {
            $jour = $_POST['jour'];
            $ouverture = !empty($_POST['heure_ouverture']) ? $_POST['heure_ouverture'] : null;
            $fermeture = !empty($_POST['heure_fermeture']) ? $_POST['heure_fermeture'] : null;

            $stmt = $db->prepare("UPDATE vg_horaire SET heure_ouverture = ?, heure_fermeture = ? WHERE jour = ?");
            $stmt->execute([$ouverture, $fermeture, $jour]);

            header('Location: ?page=dashboard-admin');
            exit();
        }
        $mongoManager = new MongoStatsManager();
        $stats = $mongoManager->getSummaryStats();
        $todayOrders = $stats['todayStats']['orders'] ?? 0;
        $todaySales  = $stats['todayStats']['sales'] ?? 0.0;
        $stmtHoraires = $db->query("SELECT * FROM vg_horaire");
        $horairesList = $stmtHoraires->fetchAll(\PDO::FETCH_ASSOC);

        $specific_styles = [
            'assets/css/bootstrap/bootstrap.min.css',
            'assets/css/Admin/OrderManagement.css',
            'assets/css/Admin/AdminEmployee.css'
        ];
        $specific_scripts = [
            ''
        ];
        require ROOT_PATH . '/app/views/layout/admin_header.php';
        require ROOT_PATH . '/app/views/admin/dashboard.admin.view.php';
        require ROOT_PATH . '/app/views/layout/admin_footer.php';
    }
}
