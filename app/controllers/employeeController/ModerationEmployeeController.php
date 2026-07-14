<?php
namespace App\Controllers\EmployeeController;
require_once dirname(__DIR__, 2) . '/config/constants.php';
use App\Controllers\AuthController\Auth;
// fonction qu'on appelle pour afficher la page depuis l'index.php
Class ModerationEmployeeController
{
public static function employeeModeration(\PDO $db)
{

    // Autorise le rôle 1 et le rôle 2 (employé et admin) à accéder à cette page, sinon redirige vers l'accueil
    Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
    $pdo = $db;
    $title = "Gestion de la modération - EcoRide";

    require_once ROOT_PATH . '/app/views/layout/employee_header.php';
    require_once ROOT_PATH . '/app/views/employee/moderation.employee.view.php';
    require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
}
}