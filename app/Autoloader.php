<?php 

namespace App;
// La classe Autoloader est responsable du chargement automatique des classes dans l'application
class Autoloader {
    // function pour enregistrer l'autoloader
    public static function register() {
        spl_autoload_register(function ($class) {
            $class = str_replace('App\\', '', $class);
$path = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';            
            if (file_exists($path)) {
                require_once $path;
            }
        });
    }
} 