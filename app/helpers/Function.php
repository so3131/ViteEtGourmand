<?php
// condition pour vérifier si la fonction error_message n'existe pas déjà avant de la définir
if (!function_exists('error_message')) {
    function error_message(string $message): void
    {
        $_SESSION['flash_error'] = $message;
    }
}
// condition pour vérifier si la fonction success_message n'existe pas déjà avant de la définir
if (!function_exists('success_message')) {
    function success_message(string $message): void
    {
        $_SESSION['flash_success'] = $message;
    }
}
