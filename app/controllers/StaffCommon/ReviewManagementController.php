<?php

namespace App\Controllers\StaffCommon;

require_once dirname(__DIR__, 2) . '/config/constants.php';

use App\Controllers\AuthController\Auth;

// fonction qu'on appelle pour afficher la page depuis l'index.php
class ReviewManagementController
{ 
    //function pour afficher la page de gestion des avis
    public static function manageReview(\PDO $db)
    {
        // Sécurité : Réservé aux administrateurs et employés
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        
        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $reviewManager = new \App\Managers\MongoReviewManager();

        $statusFilter = $_GET['status'] ?? null;

        
        $reviews = $reviewManager->getAllReviews($statusFilter);

        $pendingReviews = $reviewManager->getAllReviews('pending');
        $countPendingReviews = count($pendingReviews);

        $title = "Gestion des avis - Vite&Gourmand";
        $userRole = $_SESSION['role_id'] ?? null;

        // Affichage dynamique du header selon le rôle
        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_header.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_header.php';
        }

        // Vue dédiée aux avis
        require_once ROOT_PATH . '/app/views/StaffCommon/review.management.view.php';

        // Affichage dynamique du footer selon le rôle
        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
        }
    }
     //function pour mettre à jour le statut d'un avis
    public static function updateReviewStatus()
{
    Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?page=review-management&error=invalid_method');
        exit();
    }

    $reviewId = $_POST['review_id'] ?? '';
    $newStatus = $_POST['status'] ?? '';

    if (!in_array($newStatus, ['approved', 'rejected']) || empty($reviewId)) {
        header('Location: index.php?page=review-management&error=invalid_data');
        exit();
    }

    $reviewManager = new \App\Managers\MongoReviewManager();
    
    // Récupérer l'avis actuel pour vérifier son statut
    $currentReview = $reviewManager->getReviewById($reviewId); 
    $currentUserRoleId = $_SESSION['role_id'] ?? null;

    if ($currentUserRoleId === ROLE_EMPLOYE && $currentReview['status'] !== 'pending') {
        header('Location: index.php?page=review-management&error=unauthorized_action');
        exit();
    }
// Récupération des informations de l'utilisateur connecté pour la traçabilité
$userId = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;
$userName = trim(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? ''));

try {
    // On passe l'ID et le nom au manager
    $success = $reviewManager->updateReviewStatus($reviewId, $newStatus, $userId, $userName);

    if ($success) {
        header('Location: index.php?page=review-management&success=review_updated');
    } else {
        header('Location: index.php?page=review-management&error=update_failed');
    }
    exit();
} catch (\Exception $e) {
    header('Location: index.php?page=review-management&error=server_error');
    exit();
}
}
}
