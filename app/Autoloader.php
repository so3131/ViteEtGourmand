<?php 

namespace App;

class Autoloader {
    public static function register() {
        spl_autoload_register(function ($class) {
            // Retire le prefixe "App\" si nécessaire
            $class = str_replace('App\\', '', $class);
$path = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';            
            if (file_exists($path)) {
                require_once $path;
            }
        });
    }
}