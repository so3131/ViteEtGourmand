<?php

namespace App\Controllers;
require_once dirname(__DIR__) . '/config/constants.php';
class ErrorController
{
public static function notFound(\PDO $db)
{
    $title = "Page introuvable - EcoRide";
    $pdo = $db;
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    

    require_once ROOT_PATH . '/app/views/layout/header.php';
    require_once ROOT_PATH . '/app/views/404.view.php';
    require_once ROOT_PATH . '/app/views/layout/footer.php';
    exit();
}
}