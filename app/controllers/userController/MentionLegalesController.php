<?php
namespace App\Controllers\UserController;
// fonction qu'on appelle pour afficher la page depuis l'index.php
require_once dirname(__DIR__, 2) . '/config/constants.php';
class MentionLegalesController
{

public static function mentionsLegales(\PDO $db)
{
    $pdo = $db;
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $title = "Mentions Légales - EcoRide";

    $specifics_fonts = "https://fonts.googleapis.com/css?family=Lexend&display=swap";

    // fichiers CSS spécifiques à cette page
    $specific_styles = [
        "assets/css/StyleMentionLegales.css",
    ];

    // Pareil pour le JS
    $specific_scripts = [];

    require_once ROOT_PATH . '/app/views/layout/header.php';
    require_once ROOT_PATH . '/app/views/user/mentionLegales.view.php';
    require_once ROOT_PATH . '/app/views/layout/footer.php';
}
}