<?php

namespace App\Controllers\AuthController;
use App\Helpers\MailService;
require_once dirname(__DIR__, 2) . '/config/constants.php';
require_once ROOT_PATH . '/app/helpers/FormHelper.php';

class ForgotPasswordController
{
    public static function forgotPassword(\PDO $db)
{
    $errors = []; // Initialisation ici
    $success = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');

        // 1. Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Veuillez entrer une adresse email valide.";
        } else {
            // 2. Recherche utilisateur
           $stmt = $db->prepare("SELECT email FROM vg_utilisateur WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($user!== false) {
                // Génération du token
                $token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));

                $stmt = $db->prepare("INSERT INTO vg_password_resets (email, token, expires_at) 
                                      VALUES (:email, :token, :expires_at)
                                      ON DUPLICATE KEY UPDATE token = :token, expires_at = :expires_at");
                $stmt->execute(['email' => $email, 'token' => $token, 'expires_at' => $expires_at]);

                $resetLink = "http://localhost/Projet_Vite_Gourmand_Finale/public/index.php?page=reset-password&token=" . $token;
                
               MailService::sendResetEmail($email, $resetLink);
            }
            else {
    // Si $user est false, c'est ici que ça bloque
    echo "Utilisateur non trouvé en base malgré la requête.";
    die();
}

            // 3. Message de succès identique dans les deux cas (sécurité)
            $success = "Si un compte est associé à cette adresse, vous avez reçu un lien de réinitialisation.";
        }
    }
    // Chargement de la vue
    require_once ROOT_PATH . '/app/views/layout/header.php';
    require_once ROOT_PATH . '/app/views/Auth/forgot.password.view.php';
    require_once ROOT_PATH . '/app/views/layout/footer.php';
}
    }
