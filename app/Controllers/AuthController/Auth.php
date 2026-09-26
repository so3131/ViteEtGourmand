<?php

namespace App\Controllers\AuthController;

class Auth
{
    /**
     * @param array $requiredRoles
     * @return void
     */

    //function pour vérifier si l'utilisateur a le rôle requis pour accéder à une page
    public static function check(array $requiredRoles)
    {
        // sécurité supplementaire pour vérifier si la session est démarrée avant de vérifier le rôle de l'utilisateur
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role_id']) || !in_array((int)$_SESSION['role_id'], $requiredRoles)) {
            header('Location: index.php?page=home');
            exit();
        }
    }

    /**
     * @param array $user Données utilisateur de la BD
     */
    //function pour définir les variables de session de l'utilisateur après la connexion
    public static function setUserSession(array $user): void
    {
        $_SESSION['user_id'] = $user['utilisateur_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['telephone'] = $user['telephone'];
        $_SESSION['adresse_postale'] = $user['adresse_postale'];
        $_SESSION['ville'] = $user['ville'];
        $_SESSION['pays'] = $user['pays'];
    }

    /**
     * @return void
     */
    // function pour vérifier si l'utilisateur est connecté et rediriger vers la page de login si ce n'est pas le cas
    public static function checkLogin(): void
    {
        // sécurité supplementaire pour vérifier si la session est démarrée avant de vérifier le rôle de l'utilisateur
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }
    }
}
