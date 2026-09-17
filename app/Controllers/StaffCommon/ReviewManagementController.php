<?php

namespace App\Controllers\StaffCommon;

require_once dirname(__DIR__, 2) . '/Config/constants.php';

use App\Managers\ReviewManager;
use App\Controllers\AuthController\Auth;

// class ReviewManagementController pour gérer la gestion des avis
class ReviewManagementController
{
    //function pour afficher la page de gestion des avis
    public static function manageReview(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);


        $statusFilter = $_GET['status'] ?? null;


        $reviews = \App\Managers\ReviewManager::getAllReviews($db, $statusFilter);
        $pendingReviews = \App\Managers\ReviewManager::getAllReviews($db, 'pending');
        $countPendingReviews = count($pendingReviews);

        $title = "Gestion des avis - Vite&Gourmand";
        $userRole = $_SESSION['role_id'] ?? null;

        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/Views/layout/admin_header.php';
        } else {
            require_once ROOT_PATH . '/app/Views/layout/employee_header.php';
        }

        require_once ROOT_PATH . '/app/Views/StaffCommon/review.management.view.php';

        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/Views/layout/admin_footer.php';
        } else {
            require_once ROOT_PATH . '/app/Views/layout/employee_footer.php';
        }
    }
    //function pour mettre à jour le statut d'un avis
    public static function updateReviewStatus(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=review-management&error=invalid_method');
            exit();
        }

        // Vérification CSRF
        \App\Helpers\SecurityManager::validatePost('?page=review-management');

        $reviewId = (int)($_POST['review_id'] ?? 0);
        $newStatus = $_POST['status'] ?? '';

        if (!in_array($newStatus, ['approved', 'rejected'], true) || $reviewId <= 0) {
            header('Location: index.php?page=review-management&error=invalid_data');
            exit();
        }

        // Récupérer les informations de l'utilisateur connecté pour la traçabilité
        $userId = $_SESSION['user_id'] ?? null;
        $userName = trim(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? ''));

        // Récupérer l'avis actuel pour vérifier les droits de l'employé
        $currentReview = \App\Managers\ReviewManager::getReviewById($db, $reviewId);
        $currentUserRoleId = $_SESSION['role_id'] ?? null;

        if ($currentUserRoleId === ROLE_EMPLOYE && ($currentReview === null || $currentReview['statut'] !== 'pending')) {
            header('Location: index.php?page=review-management&error=unauthorized_action');
            exit();
        }

        try {
            $success = \App\Managers\ReviewManager::updateReviewStatus($db, $reviewId, $newStatus, $userId, $userName);

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
