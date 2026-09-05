<?php

namespace App\Controllers\UserController;

require_once dirname(__DIR__, 2) . '/config/Constants.php';

use App\Managers\ReviewManager;
// class HomeController pour gérer l'affichage de la page d'accueil
class HomeController
{
    //function pour afficher la page d'accueil avec les avis approuvés depuis la base de données MongoDB
    public static function home(\PDO $db)
    {
        $approvedReviews = \App\Managers\ReviewManager::getApprovedReviews($db, 6);
        $title = " Accueil - Vite & Gourmand";

        $specific_fonts = ["https://fonts.googleapis.com/css?family=Lexend&display=swap"];

        $specific_styles = [
            "assets/css/styleAccueil.css",
        ];

        $specific_scripts = [
            "",
        ];

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/home.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
