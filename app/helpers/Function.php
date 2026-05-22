<?php
// Fonctions utilitaires générales

/**
 * Rediriger vers une page
 */
function redirect($page)
{
    header("Location: index.php?page=$page");
    exit();
}

/**
 * Formater une date
 */
function formatDate($date, $format = 'd/m/Y')
{
    try {
        $datetime = new DateTime($date);
        return $datetime->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Formater un prix
 */
function formatPrice($price)
{
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Vérifier si l'utilisateur est authentifié
 */
function isAuthenticated()
{
    return isset($_SESSION['user_id']);
}

/**
 * Obtenir le rôle actuel
 */
function getCurrentRole()
{
    return $_SESSION['role_id'] ?? null;
}

/**
 * Vérifier le rôle
 */
function hasRole($role)
{
    return isAuthenticated() && getCurrentRole() === $role;
}
