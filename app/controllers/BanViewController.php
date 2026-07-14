<?php

namespace App\Controllers;

require_once dirname(__DIR__) . '/config/constants.php';

class BanViewController
{
public static function errorBanMessage(\PDO $db)
{
    $title = "Utilisateur désactivé - EcoRide";
    $pdo = $db;
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    // __DIR__ est : C:\xampp\htdocs\Projet_Ecoride\app\controllers
    // dirname(__DIR__) remonte d'un cran et donne : C:\xampp\htdocs\Projet_Ecoride\app
    $dossierApp = dirname(__DIR__);

    // Maintenant on cible le dossier views qui est bien DANS le dossier app/
    require_once $dossierApp . '/views/layout/header.php';
    require_once $dossierApp . '/views/ban.errormessage.view.php';
    require_once $dossierApp . '/views/layout/footer.php';
    exit();
}
}