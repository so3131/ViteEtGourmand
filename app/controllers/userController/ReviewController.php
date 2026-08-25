<?php
namespace App\Controllers\UserController;

use App\Controllers\AuthController\Auth;
use MongoDB\BSON\UTCDateTime;

require_once dirname(__DIR__, 2) . '/config/constants.php';

class ReviewController
{
    /**
     * Affiche le formulaire d'avis (en GET)
     */
    public static function submitReview(\PDO $db)
    {
        Auth::check([ROLE_USER]);
        
        $commande_id = intval($_GET['commande_id'] ?? 0);
        $utilisateur_id = $_SESSION['user_id'] ?? null;

        // Sécurité : On vérifie que la commande appartient bien à l'utilisateur et qu'elle est "terminee"
        $stmt = $db->prepare("SELECT * FROM vg_commande WHERE commande_id = ? AND utilisateur_id = ? AND statut = 'terminee'");
        $stmt->execute([$commande_id, $utilisateur_id]);
        $order = $stmt->fetch();

        if (!$order) {
            header('Location: index.php?page=dashboard-user&error=invalid_order');
            exit();
        }

    }

    /**
     * Traite l'enregistrement de l'avis dans MongoDB (en POST)
     */
    public static function storeReview(\PDO $db)
    {
        Auth::check([ROLE_USER]);
// Test de passage : est-ce qu'on arrive bien ici ?
    echo "Je suis bien dans storeReview !<br>";
    var_dump($_POST);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=dashboard-user&error=invalid_method');
            exit();
        }

        // Vérification CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            header('Location: index.php?page=dashboard-user&error=csrf_failed');
            exit();
        }

        $commande_id = intval($_POST['commande_id'] ?? 0);
        $rating = intval($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        $utilisateur_id = $_SESSION['user_id'] ?? null;
        
        // Récupération sécurisée du nom avec les variables de session
        $prenom = $_SESSION['prenom'] ?? '';
        $nom = $_SESSION['nom'] ?? '';
        $author_name = trim("$prenom $nom") !== '' ? trim("$prenom $nom") : 'Client';

        // Double vérification en BDD MySQL
        $stmt = $db->prepare("SELECT * FROM vg_commande WHERE commande_id = ? AND utilisateur_id = ? AND statut = 'terminee'");
        $stmt->execute([$commande_id, $utilisateur_id]);
        $order = $stmt->fetch();

$reviewManager = new \App\Managers\MongoReviewManager();
        if ($reviewManager->alreadyReviewedOrder($commande_id)) {
            header('Location: index.php?page=dashboard-user&error=already_reviewed');
            exit();
        }
        if (!$order || $commande_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
            header('Location: index.php?page=dashboard-user&error=invalid_data');
            exit();
        }

      try {
    $mongoManager = new \App\Managers\MongoReviewManager();
    
    $mongoManager->insertReview([
        'commande_id' => $commande_id,
        'user_id' => $utilisateur_id,
        'author_name' => $author_name,
        'rating' => $rating,
        'comment' => htmlspecialchars($comment),
        'status' => 'pending',
        'created_at' => new \MongoDB\BSON\UTCDateTime()
    ]);

    header('Location: index.php?page=dashboard-user&success=review_sent');
    exit();
} catch (\Exception $e) {
    header('Location: index.php?page=dashboard-user&error=review_failed');
    exit();
}
    }
}