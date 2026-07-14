<?php

namespace App\Controllers\AuthController;

class Auth
{
/**
* Vérifie que l'utilisateur a le rôle requis pour accéder à une page
* @param array $requiredRoles
* @return void
*/
    public static function check(array $requiredRoles)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role_id']) || !in_array((int)$_SESSION['role_id'], $requiredRoles)) {
            header('Location: index.php?page=home');
            exit();
        }
    }

/**
* Remplit la session utilisateur après login/signin
* @param array $user Données utilisateur de la BD
*/
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
 * 
 * Vérifie si l'utilisateur est connecté et si la session est valide, sinon redirige vers la page de login
 * Utilisée pour protéger les pages privées accessibles uniquement aux utilisateurs connectés
 * Redirige vers login si pas connecté
 * @return void
 */
public static function checkLogin(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit();
    }
}
}
