<?php 
if (!function_exists('error_message')) {
    function error_message(string $message): void {
        $_SESSION['flash_error'] = $message;
    }
}

if (!function_exists('success_message')) {
    function success_message(string $message): void {
        $_SESSION['flash_success'] = $message;
    }
}