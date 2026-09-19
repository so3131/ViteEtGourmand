<?php

namespace App\Controllers\AuthController;

require_once dirname(__DIR__, 2) . '/Config/Constants.php';
require_once __DIR__ . '/Auth.php';

use App\Helpers\MailService;
use App\Controllers\AuthController\Auth;
use App\Managers\UserManager;

// Class SigninController pour gérer l'inscription des utilisateurs
class SigninController
{
    //function pour afficher la page d'inscription et pour s'inscrire
    public static function SignIn(\PDO $db)

    {
        $errors = [];

        if (isset($_GET['redirect'])) {
            $_SESSION['redirect_after_login'] = $_GET['redirect'];
        }




        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification CSRF
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
                $errors['general'] = "Session expirée ou requête invalide. Veuillez recharger la page.";
            }
            $email = trim($_POST['email']);
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            // Nettoyage complet des entrées
            $nom = strip_tags(trim($_POST['nom'] ?? ''));
            $prenom = strip_tags(trim($_POST['prenom'] ?? ''));
            $gsm = strip_tags(trim($_POST['gsm'] ?? ''));
            $adresse_postale = strip_tags(trim($_POST['adresse_postale'] ?? ''));
            $ville = strip_tags(trim($_POST['ville'] ?? ''));
            $pays = strip_tags(trim($_POST['pays'] ?? ''));

            // Vérifier que les mots de passe sont bons.
            if ($password !== $password_confirm) {
                $errors['password_confirm'] = "Les mots de passe ne correspondent pas.";
            } elseif (strlen($password) < 10) {
                $errors['password'] = "Le mot de passe doit faire au moins 10 caractères.";
            } else {
                $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.]).{10,}$/";
                if (!preg_match($passwordRegex, $password)) {
                    $errors['password'] = "Le mot de passe doit contenir au moins 10 caractères, une majuscule, un minuscule, un chiffre et un caractère spécial.";
                }
            }

            // Insérer en DB

            if (empty($errors)) {

                $existingUser = UserManager::findByEmail($db, $email);
                if ($existingUser) {

                    $errors['email'] = "Cet email est déjà utilisé par un autre compte.";
                } else {


                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    try {
                        $userId = UserManager::create($db, [
                            'nom' => $nom,
                            'prenom' => $prenom,
                            'telephone' => $gsm,
                            'email' => $email,
                            'password' => $hashedPassword,
                            'adresse_postale' => $adresse_postale,
                            'ville' => $ville,
                            'pays' => $pays
                        ]);
                        $user = UserManager::findById($db, $userId);


                        if ($user) {
                            Auth::setUserSession($user);
                            $_SESSION['show_welcome'] = true;
                            \App\Helpers\MailService::sendWelcomeEmail($email);
                        }
                        if (ob_get_length()) ob_clean();

                        if (isset($_SESSION['redirect_after_login'])) {
                            $url = $_SESSION['redirect_after_login'];
                            // Sécurité : Vérifier que l'URL est valide
                            if (strpos($url, 'index.php?page=order-menu') === 0) {
                                unset($_SESSION['redirect_after_login']);
                                header('Location: ' . $url);
                                exit();
                            }
                        }

                        // Redirection classique si pas de redirection spécifique
                        header('Location: index.php?page=dashboard-user');
                        exit();
                    } catch (\PDOException $e) {
                        $errors['general'] = ($e->getCode() == 23000) ? "Ces identifiants sont déjà utilisés." : "Une erreur est survenue.";
                    }
                }
            }
        }

        $title = "S'inscrire - V&G";



        // fichiers CSS spécifiques à cette page
        $specific_styles = [
            "assets/css/loginsignin.css",
        ];

        // Pareil pour le JS
        $specific_scripts = ["assets/javascript/auth.js",];
        
        $errors = $errors ?? [];
        require_once ROOT_PATH . '/app/Views/layout/header.php';
        require_once ROOT_PATH . '/app/Views/Auth/signin.view.php';
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
    }
}
