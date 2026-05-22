<?php
// DashboardEmployeeController

require_once dirname(__DIR__, 2) . '/config/Constants.php';
require_once __DIR__ . '/../authController/Auth.php';

use App\Controllers\AuthController\Auth;

class DashboardEmployeeController
{
    public static function employeeDashboard(PDO $db)
    {
        Auth::check([ROLE_EMPLOYE]);

        $title = "Dashboard Employé - Vite & Gourmand";
        $specific_fonts = ["https://fonts.googleapis.com/css?family=Lexend&display=swap"];
        $specific_styles = ["assets/css/employee-dashboard.css"];

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/employee/dashboard.employee.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
