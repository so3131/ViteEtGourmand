<?php
namespace App\Controllers\AuthController;
require_once dirname(__DIR__, 2) . '/config/constants.php';

/**
 * Gère la connexion des utilisateurs (Authentification)
 */
class LoginController
{
    public static function LogIn(\PDO $db)
    {
        // Initialisation de l'erreur à null pour éviter les "undefined variable" dans la vue
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
            // 🛡️ Vérification CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                $error = "Session expirée ou requête invalide. Veuillez recharger la page.";
            } else {
                // Récupération et nettoyage des données
                $email = trim($_POST['email'] ?? '');     
                $password = $_POST['password'] ?? '';

                // Connexion à la base de données
                $pdo = $db;
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $stmt = $pdo->prepare("SELECT * FROM vg_utilisateur WHERE email = :email");
                $stmt->execute(['email' => $email]);
                
                // Récupération du résultat 
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                // Vérif : l'utilisateur existe-t-il ET le mot de passe est-il correct ?
                if ($user && password_verify($password, $user['password'])) {
                    
                    if (isset($user['est_actif']) && (int)$user['est_actif'] === 0) {                
                        // Le compte est banni ou suspendu
                        $error = "Votre compte a été suspendu par un administrateur. Veuillez contacter le support.";
                        require_once ROOT_PATH . '/app/views/layout/header.php';
                        require_once ROOT_PATH . '/app/views/ban.errormessage.view.php';
                        require_once ROOT_PATH . '/app/views/layout/footer.php';
                        exit();
                    }

                    // SI LE COMPTE EST ACTIF, CA REPREND LE FLUX NORMAL 

                    // Régénération de l'ID de session pour prévenir le Session Fixation
                    session_regenerate_id(true);

                    // Succès : On remplit la session avec les données utiles
                    $_SESSION['user_id'] = $user['utilisateur_id'];
                    $_SESSION['email']   = $user['email'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['nom']     = $user['nom'];
                    $_SESSION['prenom']  = $user['prenom'];
                    $_SESSION['telephone'] = $user['telephone'];
                    $_SESSION['adresse_postale'] = $user['adresse_postale'];
                    $_SESSION['ville']   = $user['ville'];
                    $_SESSION['pays']    = $user['pays'];

                    $_SESSION['show_welcome'] = true;

                    // 1. D'abord, on gère la redirection prioritaire (commande en attente)
                    if (isset($_SESSION['redirect_after_login'])) {
                        $url = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                        
                        if (ob_get_length()) ob_clean();
                        header('Location: ' . $url);
                        exit();
                    }

                    // 2. Sinon, on gère la redirection par rôle
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
                    // Échec : message d'erreur générique
                    $error = "Identifiants invalides. Veuillez vérifier votre email et votre mot de passe.";
                }
            }
        }

        // PRÉPARATION DE LA VUE
        $title = "Se connecter - Vite&Gourmand";

        // Chargement des polices et styles spécifiques à la page de connexion
        $specifics_fonts = "https://fonts.googleapis.com/css?family=Lexend&display=swap";

        $specific_styles = [
            "../public/assets/css/loginsignin.css",
        ];

        $specific_scripts = [ 
            "../public/assets/javascript/auth.js",
        ];

        // Inclusion des fichiers de template pour l'affichage
        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/Auth/login.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }

}