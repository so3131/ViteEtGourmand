<?php

namespace App\Controllers;

require_once dirname(__DIR__) . '/Config/Constants.php';
// class BanViewController pour gérer l'affichage de la page d'erreur de bannissement
class BanViewController
{
    //function pour afficher la page d'erreur de bannissement
    public static function errorBanMessage(\PDO $db)
    {
        $title = "Utilisateur désactivé - Vite & Gourmand";

        $dossierApp = dirname(__DIR__);

        require_once $dossierApp . '/Views/layout/header.php';
        require_once $dossierApp . '/Views/ban.errormessage.view.php';
        require_once $dossierApp . '/Views/layout/footer.php';
        exit();
    }
}
