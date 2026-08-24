<?php

namespace App\Controllers\StaffCommon;

require_once dirname(__DIR__, 2) . '/config/constants.php';

use App\Controllers\AuthController\Auth;

// fonction qu'on appelle pour afficher la page depuis l'index.php
class ConflictAdminController
{
    public static function Conflicts(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        // Sécurité : On vérifie si l'utilisateur est bien ADMIN grâce à la constante globale
        Auth::check([ROLE_ADMIN]);

        $title = "Gestion des conflits - Vite&Gourmand";
        $userRole = $_SESSION['role_id'] ?? null;
        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_header.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_header.php';
        }
        require_once ROOT_PATH . '/app/views/StaffCommon/conflict.view.php';
        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
        }
    }
}
