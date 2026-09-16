<?php

namespace App\Controllers\UserController;

require_once dirname(__DIR__, 2) . '/Config/constants.php';
// class MentionLegalesController pour gérer l'affichage des mentions légales
class MentionLegalesController
{
    //function pour afficher la page des mentions légales
    public static function mentionsLegales(\PDO $db)
    {

        $title = "Mentions Légales - Vite&Gourmand";


        $specific_styles = [
            "assets/css/StyleMentionLegales.css",
        ];
        $specific_scripts = [];

        require_once ROOT_PATH . '/app/Views/layout/header.php';
        require_once ROOT_PATH . '/app/Views/user/mentionLegales.view.php';
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
    }
}
