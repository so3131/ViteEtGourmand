<?php

namespace App\Controllers\AuthController;

require_once dirname(__DIR__, 2) . '/Config/Constants.php';

// Class LogoutController pour gérer la déconnexion des utilisateurs
class LogoutController
{
    // function pour gérer la déconnexion des utilisateurs
    public static function logOut(\PDO $db)
    {
        //  Récupère la session avant de tester son statut
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Si une session est active, elle est détruite
        if (session_status() == PHP_SESSION_ACTIVE) {
            $_SESSION = array(); // On vide la session
            // Efface le cookie de session dans le navigateur pour éviter que le navigateur ne renvoie un ID de session obsolète
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    // Date d'expiration passée pour forcer l'effacement du cookie
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            // Détruit la session
            session_destroy();
        }
        header('Location: index.php?page=home');
        exit();
    }
}
