<?php

namespace App\Controllers;

require_once dirname(__DIR__) . '/config/constants.php';

// app/controllers/SuccessController.php
class SuccessController
{
     //function pour afficher la page de succès après l'envoi d'un message de contact
public static function successMessage(\PDO $db)
{
    $title = "Message envoyé - Vite & Gourmand";
    $pdo = $db;
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    
    require_once ROOT_PATH . '/app/views/layout/header.php';
    require_once ROOT_PATH . '/app/views/contact.success.view.php';
    require_once ROOT_PATH . '/app/views/layout/footer.php';
    exit();
}
}