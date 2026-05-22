<?php
// Contrôleur pour la page d'accueil

require_once dirname(__DIR__, 2) . '/config/Constants.php';

class HomeController
{
    public static function home(PDO $db)
    {
        // Variables pour la vue
        $title = "Accueil - Vite & Gourmand";
        $specific_fonts = ["https://fonts.googleapis.com/css?family=Lexend&display=swap"];
        $specific_styles = ["assets/css/home.css"];

        // Charger les templates
        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/home.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
