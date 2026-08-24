<?php
namespace App\Controllers\UserController;
use app\Controllers\AuthController\Auth;
require_once dirname(__DIR__, 2) . '/config/constants.php';


class ReviewController
{
    public static function submitReview(\PDO $db)
    {
        auth::check([ROLE_USER]);
        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $title = "Avis - Vite&Gourmand";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connexion MongoDB (via ta configuration existante)
    $mongoClient = new MongoDB\Client("mongodb://localhost:27017"); // Adapte selon ton client
    $collection = $mongoClient->nom_de_base->reviews;

    $collection->insertOne([
        'user_id' => $_SESSION['user_id'],
        'author_name' => $_SESSION['prenom'] . ' ' . $_SESSION['nom'],
        'rating' => (int)$_POST['rating'],
        'comment' => htmlspecialchars($_POST['comment']),
        'status' => 'pending', // 'pending', 'approved', 'rejected'
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ]);

    header('Location: index.php?page=dashboard-user&success=review_sent');
    exit();
}
        $specifics_fonts = "https://fonts.googleapis.com/css?family=Lexend&display=swap";

        // fichiers CSS spécifiques à cette page
        $specific_styles = [
            "assets/css/StyleReview.css",
        ];

        // Pareil pour le JS
        $specific_scripts = [];

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/review.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}