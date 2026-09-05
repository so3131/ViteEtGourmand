<?php

namespace App\Controllers\AdminController;

use App\Controllers\AuthController\Auth;
use App\Managers\MongoStatsManager;
use App\Managers\ReviewManager;
use App\Managers\StatAdminManager;

class DashboardAdminController
{
    //function pour afficher la page du tableau de bord admin
    public static function adminDashboard(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_horaire'])) {
            $jour = $_POST['jour'];
           
            if (!empty($_POST['est_ferme'])) {
                $ouverture = null;
                $fermeture = null;
            } else {
                $ouverture = !empty($_POST['heure_ouverture']) ? str_replace(':', 'h', $_POST['heure_ouverture']) : null;
                $fermeture = !empty($_POST['heure_fermeture']) ? str_replace(':', 'h', $_POST['heure_fermeture']) : null;
            }
            $stmt = $db->prepare("UPDATE vg_horaire SET heure_ouverture = ?, heure_fermeture = ? WHERE jour = ?");
            $stmt->execute([$ouverture, $fermeture, $jour]);
            header('Location: ?page=dashboard-admin');
            exit();
        }

        // Récupération des statistiques depuis MongoDB (stats commandes — exigence NoSQL ECF)
        $mongoManager = new MongoStatsManager();

        // Avis en attente de modération (depuis la base SQL)
        $pendingReviews = count(ReviewManager::getAllReviews($db, 'pending'));

        $stats = $mongoManager->getSummaryStats();
        $todayOrders = $stats['todayStats']['orders'] ?? 0;
        $todaySales  = $stats['todayStats']['sales'] ?? 0.0;

        // Statistiques SQL (totaux et statuts des commandes)
        $sqlStats = StatAdminManager::getStatsFromSQL($db);
        $totalOrders    = $sqlStats['total_commandes'];
        $pendingOrders  = $sqlStats['pending_orders'];
        $finishedOrders = $sqlStats['finished_orders'];

        // Récupération des horaires
        $stmtHoraires = $db->query("SELECT * FROM vg_horaire");
        $horairesList = $stmtHoraires->fetchAll(\PDO::FETCH_ASSOC);

        $specific_styles = [
            'assets/css/bootstrap/bootstrap.min.css',
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
