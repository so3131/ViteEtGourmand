<?php
// condition pour vérifier si la fonction error_message n'existe pas déjà avant de la définir
if (!function_exists('error_message')) {
    function error_message(string $message): void
    {
        $_SESSION['error']   = $message;
    }
}
// condition pour vérifier si la fonction success_message n'existe pas déjà avant de la définir
if (!function_exists('success_message')) {

    function success_message(string $message): void
    {
        $_SESSION['success'] = $message;
    }


    // Définit un message flash et redirige
    function flash(string $type, string $message, string $redirect): void
    {
        if (!in_array($type, ['error', 'success', 'info', 'warning'])) {
            $type = 'info';
        }
        $_SESSION[$type] = $message;
        header('Location: index.php' . $redirect);
        exit();
    }

    // Raccourcis pratiques
    function flashError(string $message, string $redirect = '?page=home'): void
    {
        flash('error', $message, $redirect);
    }

    function flashSuccess(string $message, string $redirect = '?page=home'): void
    {
        flash('success', $message, $redirect);
    }
}
