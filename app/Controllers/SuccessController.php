<?php

namespace App\Controllers;

require_once dirname(__DIR__) . '/Config/Constants.php';

// class SuccessController pour gérer l'affichage de la page de succès après l'envoi d'un message de contact
class SuccessController
{
    //function pour afficher la page de succès après l'envoi d'un message de contact
    public static function successMessage(\PDO $db)
    {
        $title = "Message envoyé - Vite & Gourmand";

        require_once ROOT_PATH . '/app/Views/layout/header.php';
        require_once ROOT_PATH . '/app/Views/user/contact.success.view.php';
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
        exit();
    }
}
