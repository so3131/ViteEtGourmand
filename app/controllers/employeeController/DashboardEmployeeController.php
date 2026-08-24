<?php

namespace App\Controllers\EmployeeController;
use App\Controllers\AuthController\Auth;
use App\Managers\MongoStatsManager;

class DashboardEmployeeController
{
public static function employeeDashboard(\PDO $db)
{    Auth::check([ROLE_EMPLOYE]);

$mongoManager = new MongoStatsManager();
$stats = $mongoManager->getSummaryStats();
        $todayOrders = $stats['todayStats']['orders'] ?? 0;
$todaySales  = $stats['todayStats']['sales'] ?? 0.0;
$stmtHoraires = $db->query("SELECT * FROM vg_horaire");
        $horairesList = $stmtHoraires->fetchAll(\PDO::FETCH_ASSOC);
    
    $title = "Tableau de bord Employé- Vite & Gourmand";

   require_once ROOT_PATH . '/app/views/layout/employee_header.php';
    require_once ROOT_PATH . '/app/views/employee/dashboard.employee.view.php';
    require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
}
}