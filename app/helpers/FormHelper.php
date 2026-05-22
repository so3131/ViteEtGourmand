<?php
// FormHelper - Aide pour les formulaires

class FormHelper
{
    /**
     * Valider un email
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Nettoyer un input
     */
    public static function sanitize($input)
    {
        return htmlspecialchars(strip_tags(trim($input)));
    }

    /**
     * Valider un nombre
     */
    public static function validateNumber($number, $min = 0, $max = PHP_INT_MAX)
    {
        return is_numeric($number) && $number >= $min && $number <= $max;
    }

    /**
     * Générer un token CSRF (exemple simplifié)
     */
    public static function generateToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifier un token CSRF
     */
    public static function verifyToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
