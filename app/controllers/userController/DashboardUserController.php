<?php
// DashboardUserController

require_once dirname(__DIR__, 2) . '/config/Constants.php';
require_once __DIR__ . '/../../controllers/authController/Auth.php';

use App\Controllers\AuthController\Auth;

class DashboardUserController
{
    public static function userDashboard(PDO $db)
    {
        Auth::check([ROLE_USER]);

        $title = "Mon Dashboard - Vite & Gourmand";
        $specific_fonts = ["https://fonts.googleapis.com/css?family=Lexend&display=swap"];
        $specific_styles = ["assets/css/dashboard.css"];

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/dashboard.user.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
