<?php
namespace App\Controllers\AdminController;
require_once dirname(__DIR__, 2) . '/config/constants.php';
use App\Controllers\AuthController\Auth;
// fonction qu'on appelle pour afficher la page depuis l'index.php
class ModerationAdminController
{
public static function adminModeration(\PDO $db)
{
    

    $pdo = $db;
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
    // Sécurité : On vérifie si l'utilisateur est bien ADMIN    
    Auth::check([ROLE_ADMIN]);
    $title = "Gestion de la modération - EcoRide";

    require_once ROOT_PATH . '/app/views/layout/admin_header.php';
    require_once ROOT_PATH . '/app/views/admin/moderation.admin.view.php';
    require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
}
}