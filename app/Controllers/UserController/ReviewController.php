<?php

namespace App\Controllers\UserController;

use App\Controllers\AuthController\Auth;
use App\Managers\ReviewManager;

require_once dirname(__DIR__, 2) . '/Config/Constants.php';
// class ReviewController pour gérer la soumission et le stockage des avis
class ReviewController
{
    //function pour afficher le formulaire d'avis pour une commande spécifique après vérification de l'état de la commande
    public static function submitReview(\PDO $db)
    {
        Auth::check([ROLE_USER]);

        $commande_id = intval($_GET['commande_id'] ?? 0);
        $utilisateur_id = $_SESSION['user_id'] ?? null;

        // Verifier que la commande appartient bien à l'utilisateur et qu'elle est sur le statut "terminee"
        $order = ReviewManager::getCompletedOrderForUser($db, $commande_id, $utilisateur_id);

        if (!$order) {
            header('Location: index.php?page=dashboard-user&error=invalid_order');
            exit();
        }
    }

    //function pour stocker un avis dans la base de données SQL après validation du formulaire
    public static function storeReview(\PDO $db)
    {
        Auth::check([ROLE_USER]);

        // Vérification CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            header('Location: index.php?page=dashboard-user&error=csrf_failed');
            exit();
        }

        $commande_id = intval($_POST['commande_id'] ?? 0);
        $rating = intval($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        $utilisateur_id = $_SESSION['user_id'] ?? null;

        // Vérifier que la commande appartient à l'utilisateur et qu'elle est terminée
        $order = ReviewManager::getCompletedOrderForUser($db, $commande_id, $utilisateur_id);


        if (!$order || $commande_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
            header('Location: index.php?page=dashboard-user&error=invalid_data');
            exit();
        }

        if (ReviewManager::alreadyReviewedOrder($db, $commande_id)) {
            header('Location: index.php?page=dashboard-user&error=already_reviewed');
            exit();
        }

        try {
            ReviewManager::insertReview($db, [
                'commande_id' => $commande_id,
                'user_id'     => $utilisateur_id,
                'rating'      => $rating,
                'comment'     => htmlspecialchars($comment),
            ]);
            header('Location: index.php?page=dashboard-user&success=review_sent');
            exit();
        } catch (\Exception $e) {
            header('Location: index.php?page=dashboard-user&error=review_failed');
            exit();
        }
    }
}
