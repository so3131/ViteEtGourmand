<?php

namespace App\Controllers;

require_once dirname(__DIR__) . '/config/constants.php';

// app/controllers/SuccessController.php
class SuccessController
{
public static function successMessage(\PDO $db)
{
    $title = "Message envoyé - EcoRide";
    $pdo = $db;
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    
    require_once ROOT_PATH . '/app/views/layout/header.php';
    require_once ROOT_PATH . '/app/views/contact.success.view.php';
    require_once ROOT_PATH . '/app/views/layout/footer.php';
    exit();
}
}