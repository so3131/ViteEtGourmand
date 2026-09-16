<?php

namespace App\Controllers\AuthController;

use App\Helpers\MailService;
use App\Helpers\SecurityManager;
use App\Managers\UserManager;

require_once dirname(__DIR__, 2) . '/Config/constants.php';
require_once ROOT_PATH . '/app/Helpers/FormHelper.php';

// Class ResetPasswordController pour gérer la réinitialisation du mot de passe ( suite logique de ForgotPasswordController )
class ResetPasswordController
{
    // function pour afficher la page de réinitialisation du mot de passe et gérer le processus de réinitialisation
    public static function resetPassword(\PDO $db)
    {

        $errors = [];
        $token = $_GET['token'] ?? null;
        $tokenHash = hash('sha256', $token ?? '');
        $success = null;
        if (empty($_GET['token'])) {
            header('Location: index.php?page=forgot-password');
            exit;
        }
        // Vérification du token 

        $resetData = UserManager::findPasswordResetToken($db, $tokenHash);

        if (!$resetData) {
            $errors['general'] = "Ce lien est invalide ou a expiré.";
        } else {
            // Récupèrer le prénom de l'utilisateur séparément pour éviter les conflit
            // Récupérer le prénom de l'utilisateur pour l'email de confirmation
            $userRecord = UserManager::findByEmail($db, $resetData['email']);
            $resetData['prenom'] = $userRecord['prenom'] ?? 'client';
        }
        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
            SecurityManager::validatePost(
                '?page=reset-password&token=' . urlencode($token)
            );
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            // Validation mot de passe
            if ($password !== $password_confirm) {
                $errors['password_confirm'] = "Les mots de passe ne correspondent pas.";
            } elseif (strlen($password) < 10) {
                $errors['password'] = "Le mot de passe doit faire au moins 10 caractères.";
            } else {
                $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.]).{10,}$/";
                if (!preg_match($passwordRegex, $password)) {
                    $errors['password'] = "Le mot de passe doit contenir au moins 10 caractères, une majuscule, un minuscule, un chiffre et un caractère spécial.";
                } else {
                    // Mise à jour en base
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    UserManager::updatePassword($db, $resetData['email'], $hashedPassword);
                    UserManager::deletePasswordResetToken($db, $tokenHash);

                    $success = "Votre mot de passe a été modifié.";
                    \App\Helpers\MailService::sendResetConfirmationEmail($resetData['email'], $resetData['prenom'] ?? 'client');
                }
            }
        }
        require_once ROOT_PATH . '/app/Views/layout/header.php';
        require_once ROOT_PATH . '/app/Views/Auth/reset.password.view.php';
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
    }
}
