<?php
// DashboardAdminController

require_once dirname(__DIR__, 2) . '/config/Constants.php';
require_once __DIR__ . '/../authController/Auth.php';

use App\Controllers\AuthController\Auth;

class DashboardAdminController
{
    public static function adminDashboard(PDO $db)
    {
        Auth::check([ROLE_ADMIN]);

        $title = "Dashboard Admin - Vite & Gourmand";
        $specific_fonts = ["https://fonts.googleapis.com/css?family=Lexend&display=swap"];
        $specific_styles = ["assets/css/admin-dashboard.css"];

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/admin/dashboard.admin.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
