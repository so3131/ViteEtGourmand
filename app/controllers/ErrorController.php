<?php

namespace App\Controllers;

require_once dirname(__DIR__) . '/config/constants.php';
// class ErrorController pour gérer l'affichage de la page d'erreur 404
class ErrorController
{
    //function pour afficher la page d'erreur 404
    public static function notFound(\PDO $db)
    {
        $title = "Page introuvable - Vite & Gourmand";
   

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/404.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
        exit();
    }
}
