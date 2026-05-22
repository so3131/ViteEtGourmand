<?php
namespace App\Controllers\AuthController;

class Auth
{
    /**
     * Vérifier l'authentification
     */
    public static function check(array $requiredRoles)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role_id']) || !in_array((int)$_SESSION['role_id'], $requiredRoles)) {
            header('Location: index.php?page=login');
            exit();
        }
    }

    /**
     * Créer la session utilisateur
     */
    public static function setUserSession(array $user): void
    {
        $_SESSION['user_id'] = $user['utilisateur_id'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
    }

    /**
     * Vérifier si un email existe
     */
    public static function emailExists(PDO $db, $email)
    {
        $stmt = $db->prepare("SELECT * FROM vg_utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() !== false;
    }

    /**
     * Valider un mot de passe
     */
    public static function validatePassword($password)
    {
        if (strlen($password) < 8) return false;
        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/";
        return preg_match($regex, $password);
    }
}
