<?php

namespace App\Controllers\EmployeeController;

use App\Controllers\AuthController\Auth;
use App\Managers\ReviewManager;


// Class DashboardEmployeeController pour gérer le tableau de bord des employés
class DashboardEmployeeController
{    //function pour afficher la page du tableau de bord employé
    public static function employeeDashboard(\PDO $db)
    {
        Auth::check([ROLE_EMPLOYE]);
         if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_horaire'])) {
            $jour = $_POST['jour'];
            // Si la case "Fermé" est cochée, on enregistre NULL pour les deux heures
            if (!empty($_POST['est_ferme'])) {
                $ouverture = null;
                $fermeture = null;
            } else {
                $ouverture = !empty($_POST['heure_ouverture']) ? str_replace(':', 'h', $_POST['heure_ouverture']) : null;
                $fermeture = !empty($_POST['heure_fermeture']) ? str_replace(':', 'h', $_POST['heure_fermeture']) : null;
            }
            $stmt = $db->prepare("UPDATE vg_horaire SET heure_ouverture = ?, heure_fermeture = ? WHERE jour = ?");
            $stmt->execute([$ouverture, $fermeture, $jour]);
            header('Location: ?page=dashboard-employee');
            exit();
        }
       

        // COMPTEURS

        // Nombre total de commandes
        $stmtTotalOrders = $db->query("SELECT COUNT(*) FROM vg_commande");
        $totalOrders = $stmtTotalOrders->fetchColumn();

        // Nombre de commandes en attente
        $stmtPendingOrders = $db->query("SELECT COUNT(*) FROM vg_commande WHERE statut = 'en_attente'");
        $pendingOrders = $stmtPendingOrders->fetchColumn();

        // Nombre de commandes terminées
        $stmtFinishedOrders = $db->query("SELECT COUNT(*) FROM vg_commande WHERE statut = 'terminee'");
        $finishedOrders = $stmtFinishedOrders->fetchColumn();

        // Récupérer les avis et comptage des avis en attente (depuis MongoDB)
       $reviewsList = ReviewManager::getAllReviews($db);
$pendingReviews = count(array_filter($reviewsList, fn($r) => ($r['statut'] ?? 'pending') === 'pending'));


        // Données annexes pour les listes
        $stmtCommandes = $db->query("SELECT c.*, u.nom, u.prenom FROM vg_commande c JOIN vg_utilisateur u ON c.utilisateur_id = u.utilisateur_id ORDER BY c.commande_id DESC LIMIT 10");
        $commandesList = $stmtCommandes->fetchAll(\PDO::FETCH_ASSOC);

        // Récupération des horaires
        $stmtHoraires = $db->query("SELECT * FROM vg_horaire");
        $horairesList = $stmtHoraires->fetchAll(\PDO::FETCH_ASSOC);

        $title = "Tableau de bord Employé - Vite & Gourmand";

        require_once ROOT_PATH . '/app/views/layout/employee_header.php';
        require_once ROOT_PATH . '/app/views/employee/dashboard.employee.view.php';
        require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
    }
}
