<?php
namespace App\Controllers\UserController;
require_once dirname(__DIR__, 2) . '/config/Constants.php';
 use App\Managers\MongoReviewManager;

class HomeController
{//function pour afficher la page d'accueil avec les avis approuvés depuis la base de données MongoDB
    public static function home(\PDO $db)
    {
        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
       


$mongoReviewManager = new MongoReviewManager();
$approvedReviews = $mongoReviewManager->getApprovedReviews(6);
        $title = " Accueil - Vite & Gourmand";

        $specific_fonts = ["https://fonts.googleapis.com/css?family=Lexend&display=swap"];

        // fichiers CSS spécifiques à cette page
        $specific_styles = [
            "assets/css/styleAccueil.css",
        ];

        // Pareil pour le JS
        $specific_scripts = [
            "",
        ];

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/home.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
