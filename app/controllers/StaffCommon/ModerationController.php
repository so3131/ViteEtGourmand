<?php

namespace App\Controllers\StaffCommon;

require_once dirname(__DIR__, 2) . '/config/constants.php';

use App\Controllers\AuthController\Auth;

class ModerationController
{
     //function pour afficher la page de gestion de la modération
    public static function moderation(\PDO $db)
    {

        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Sécurité : On vérifie si l'utilisateur est bien ADMIN    
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        $title = "Gestion de la modération - Vite&Gourmand";
        $specific_styles = [
            'assets/css/bootstrap/bootstrap.min.css',
            'assets/css/Admin/OrderManagement.css',
            'assets/css/Admin/AdminEmployee.css'
        ];
        $specific_scripts = [
            ''
        ];

        // Chargement de la vue
        $userRole = $_SESSION['role_id'] ?? null;
        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_header.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_header.php';
        }
        require_once ROOT_PATH . '/app/views/StaffCommon/moderation.view.php';

        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
        }
    }
}
