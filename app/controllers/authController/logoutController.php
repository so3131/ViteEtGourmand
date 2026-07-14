<?php
namespace App\Controllers\AuthController;
require_once dirname(__DIR__, 2) . '/config/constants.php';
class LogoutController
{
public static function logOut(\PDO $db)
{
    $pdo = $db;
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    //  on récupère la session AVANT de tester son statut
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    // Si une session est active, on la détruit de fond en comble
    if (session_status() == PHP_SESSION_ACTIVE) {
        $_SESSION = array(); // On vide la session
        // 2. On efface le cookie de session dans le navigateur pour éviter que le navigateur ne renvoie un ID de session obsolète
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000, // Date d'expiration dans le passé pour le forcer à s'effacer
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy(); // On détruit la session 
    }
    header('Location: index.php?page=home');
    exit();
}
}