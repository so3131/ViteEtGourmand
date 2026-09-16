<?php

namespace App\Controllers\AuthController;

use App\Helpers\MailService;
use App\Helpers\SecurityManager;

require_once dirname(__DIR__, 2) . '/Config/constants.php';
require_once ROOT_PATH . '/app/Helpers/FormHelper.php';

use App\Controllers\AuthController\Auth;
use App\Managers\UserManager;

class ForgotPasswordController
{
    // function pour afficher la page de mot de passe oublié et gérer l'envoi du formulaire
    public static function forgotPassword(\PDO $db)
    {
        $errors = [];
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            SecurityManager::validatePost('?page=forgot-password');
            $email = trim($_POST['email'] ?? '');

            // Validation de l'email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Veuillez entrer une adresse email valide.";
            } else {
                //Recherche utilisateur (on récupère aussi le prénom pour le mail)

                // findByEmail retourne null si le compte n'existe pas.
                $user = UserManager::findByEmail($db, $email);

                if ($user) {
                    // Génération + stockage du token (hashé) via le Manager
                    $token = UserManager::createPasswordResetToken($db, $email);

                    $appUrl = rtrim(getenv('APP_URL'), '/');
                    $resetLink = $appUrl
                        . '/index.php?page=reset-password&token='
                        . urlencode($token);

                    // Appel du service mail en passant l'e-mail et le prénom
                    $prenom = $user['prenom'] ?? 'Utilisateur';
                    MailService::sendResetEmail($email, $resetLink, $prenom);
                }

                // Message de succès identique dans tous les cas par sécurité
                // pour ne pas révéler si l'email est enregistré ou non
                $success = "Si un compte est associé à cette adresse, vous avez reçu un lien de réinitialisation par mail.";
            }
        }

        // Chargement de la vue
        require_once ROOT_PATH . '/app/Views/layout/header.php';
        require_once ROOT_PATH . '/app/Views/Auth/forgot.password.view.php';
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
    }
}
