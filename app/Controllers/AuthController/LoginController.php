<?php

namespace App\Controllers\AuthController;

require_once dirname(__DIR__, 2) . '/Config/Constants.php';

use App\Controllers\AuthController\Auth;
use App\Managers\UserManager;
// Class LoginController pour gérer la connexion des utilisateurs
class LoginController
{
    //function pour vérifier si la connexion est bloquée (anti-bruteforce) et private pour eviter attaque par deni de service
    private static function isLoginLocked(): bool
    {
        $attempts = $_SESSION['login_attempts'] ?? 0;
        $lockedUntil = $_SESSION['login_locked_until'] ?? 0;

        if ($lockedUntil > time()) {
            return true;
        }
        // Le délai est écoulé : remise à zéro
        if ($lockedUntil > 0 && $lockedUntil <= time()) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_locked_until'] = 0;
        }
        return false;
    }

    //function pour enregistree un échec de connexion et bloquer après 5 tentatives
    private static function recordLoginFailure(): void
    {
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;

        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_locked_until'] = time() + 300;
        }
    }

    // function pour afficher la page de connexion et gérer le processus de connexion
    public static function LogIn(\PDO $db)
    {

        $error = null;

        if (isset($_GET['redirect'])) {
            // Nettoyage de l'URL pour retirer d'éventuels ancrages qui pourraient causer des bugs
            $cleanRedirect = strtok($_GET['redirect'], '#');

            // On autorise uniquement les redirections vers nos pages internes
            if (strpos($cleanRedirect, 'index.php?page=order-menu') === 0) {
                $_SESSION['redirect_after_login'] = $cleanRedirect;
            }
        }

        // On vérifie si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification CSRF
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
                $error = "Session expirée ou requête invalide. Veuillez recharger la page.";
            } else {
                // Anti-bruteforce : blocage après 5 échecs
                if (self::isLoginLocked()) {
                    $error = "Trop de tentatives. Réessayez dans quelques minutes.";
                } else {
                    // Récupération et nettoyage des données
                    $email = trim($_POST['email'] ?? '');
                    $password = $_POST['password'] ?? '';


                    $user = UserManager::findByEmail($db, $email);

                    // Vérif : l'utilisateur existe-t-il ET le mot de passe est-il correct ?
                    if ($user && password_verify($password, $user['password'])) {

                        if (isset($user['est_actif']) && (int)$user['est_actif'] === 0) {
                            // Le compte est banni ou suspendu
                            $error = "Votre compte a été suspendu par un administrateur. Veuillez contacter le support.";
                            require_once ROOT_PATH . '/app/Views/layout/header.php';
                            require_once ROOT_PATH . '/app/Views/ban.errormessage.view.php';
                            require_once ROOT_PATH . '/app/Views/layout/footer.php';
                            exit();
                        }

                        // SI COMPTE ACTIF, CA REPREND LE FLUX 

                        // Régénération de l'ID de session pour prévenir le Session Fixation
                        session_regenerate_id(true);
                        $_SESSION['login_attempts'] = 0;
                        $_SESSION['login_locked_until'] = 0;

                        //On remplit la session avec les données utiles
                        $_SESSION['user_id'] = $user['utilisateur_id'];
                        $_SESSION['email']   = $user['email'];
                        $_SESSION['role_id'] = $user['role_id'];
                        $_SESSION['nom']     = $user['nom'];
                        $_SESSION['prenom']  = $user['prenom'];
                        $_SESSION['telephone'] = $user['telephone'];
                        $_SESSION['adresse_postale'] = $user['adresse_postale'];
                        $_SESSION['ville']   = $user['ville'];
                        $_SESSION['pays']    = $user['pays'];


                        $_SESSION['success'] = "Bienvenue sur Vite & Gourmand !";



                        //On gère la redirection prioritaire (commande en attente)
                        if (isset($_SESSION['redirect_after_login'])) {
                            $url = $_SESSION['redirect_after_login'];
                            unset($_SESSION['redirect_after_login']);

                            if (ob_get_length()) ob_clean();
                            header('Location: ' . $url);
                            exit();
                        }

                        //Sinon, on gère la redirection par rôle
                        $destination = match ((int)($_SESSION['role_id'] ?? 0)) {
                            1 => 'index.php?page=dashboard-admin',
                            2 => 'index.php?page=dashboard-employee',
                            3 => 'index.php?page=dashboard-user',
                            default => 'index.php?page=home',
                        };

                        // Nettoyage avant redirection
                        if (ob_get_length()) ob_clean();

                        header('Location: ' . $destination);
                        exit();
                    } else {
                        self::recordLoginFailure();
                        flashError("Identifiants invalides.", '?page=login');
                    }
                }
            }
        }
        // PRÉPARATION DE LA VUE
        $title = "Se connecter - Vite&Gourmand";

        $specific_styles = [
            "../public/assets/css/loginsignin.css",
        ];

        $specific_scripts = [
            "../public/assets/javascript/auth.js",
        ];

        // Inclusion des fichiers de template pour l'affichage
        require_once ROOT_PATH . '/app/Views/layout/header.php';
        require_once ROOT_PATH . '/app/Views/Auth/login.view.php';
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
    }
}
