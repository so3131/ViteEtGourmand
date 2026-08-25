<?php

namespace App\Controllers\EmployeeController;

use App\Controllers\AuthController\Auth;
use App\Managers\MongoReviewManager;

class DashboardEmployeeController
{
    public static function employeeDashboard(\PDO $db)
    { 
        Auth::check([ROLE_EMPLOYE]);

        $mongoReviewManager = new MongoReviewManager();

        // --- COMPTEURS POUR LES KPIS ---

        // 1. Nombre total de commandes
        $stmtTotalOrders = $db->query("SELECT COUNT(*) FROM vg_commande");
        $totalOrders = $stmtTotalOrders->fetchColumn();

        // 2. Nombre de commandes en attente
        $stmtPendingOrders = $db->query("SELECT COUNT(*) FROM vg_commande WHERE statut = 'en_attente'");
        $pendingOrders = $stmtPendingOrders->fetchColumn();

        // 3. Nombre de commandes terminées
        $stmtFinishedOrders = $db->query("SELECT COUNT(*) FROM vg_commande WHERE statut = 'terminee'");
        $finishedOrders = $stmtFinishedOrders->fetchColumn();

        // 4. Récupération des avis et comptage des avis en attente (depuis MongoDB)
        $reviewsList = $mongoReviewManager->getAllReviews();
        $pendingReviews = count(array_filter($reviewsList, fn($r) => ($r['status'] ?? 'pending') === 'pending'));

        // Données annexes pour les listes
        $stmtCommandes = $db->query("SELECT c.*, u.nom, u.prenom FROM vg_commande c JOIN vg_utilisateur u ON c.utilisateur_id = u.utilisateur_id ORDER BY c.commande_id DESC LIMIT 10");
        $commandesList = $stmtCommandes->fetchAll(\PDO::FETCH_ASSOC);

        $title = "Tableau de bord Employé - Vite & Gourmand";

        require_once ROOT_PATH . '/app/views/layout/employee_header.php';
        require_once ROOT_PATH . '/app/views/employee/dashboard.employee.view.php';
        require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
    }
}