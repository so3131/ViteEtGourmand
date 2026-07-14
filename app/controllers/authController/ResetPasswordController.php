<?php 
namespace App\Controllers\AuthController;
use App\Helpers\MailService;
require_once dirname(__DIR__, 2) . '/config/constants.php';
require_once ROOT_PATH . '/app/helpers/FormHelper.php';


class ResetPasswordController
{
    public static function resetPassword(\PDO $db)
{

    $errors = [];
    $token = $_GET['token'] ?? null;
    $success = null;
if (empty($_GET['token'])) {
    header('Location: index.php?page=forgot-password');
    exit;
}
    // 1. Vérification initiale du token
    $stmt = $db->prepare("SELECT email FROM vg_password_resets WHERE token = :token AND expires_at > NOW()");
    $stmt->execute(['token' => $token]);
    $resetData = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$resetData) {
        $errors['general'] = "Ce lien est invalide ou a expiré.";
    }

    // 2. Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validation mot de passe
        if ($password !== $password_confirm) {
            $errors['password_confirm'] = "Les mots de passe ne correspondent pas.";
        } elseif (strlen($password) < 10) {
            $errors['password'] = "Le mot de passe doit faire au moins 10 caractères.";
        } else {
            // Mise à jour en base
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE vg_utilisateur SET password = :password WHERE email = :email");
            $update->execute(['password' => $hashedPassword, 'email' => $resetData['email']]);

            $db->prepare("DELETE FROM vg_password_resets WHERE token = :token")->execute(['token' => $token]);
            
            $success = "Votre mot de passe a été modifié.";
        }
    }
    require_once ROOT_PATH . '/app/views/layout/header.php';
    require_once ROOT_PATH . '/app/views/Auth/reset.password.view.php';
    require_once ROOT_PATH . '/app/views/layout/footer.php';
}
}