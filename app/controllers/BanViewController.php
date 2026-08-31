<?php

namespace App\Controllers;

require_once dirname(__DIR__) . '/config/constants.php';

class BanViewController
{
     //function pour afficher la page d'erreur de bannissement
public static function errorBanMessage(\PDO $db)
{
    $title = "Utilisateur désactivé - Vite & Gourmand";
    $pdo = $db;
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    
    $dossierApp = dirname(__DIR__);

    // Maintenant on cible le dossier views qui est bien DANS le dossier app/
    require_once $dossierApp . '/views/layout/header.php';
    require_once $dossierApp . '/views/ban.errormessage.view.php';
    require_once $dossierApp . '/views/layout/footer.php';
    exit();
}
}