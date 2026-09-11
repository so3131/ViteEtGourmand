<?php

namespace App\Controllers\EmployeeController;

use App\Controllers\AuthController\Auth;
use App\Managers\ReviewManager;
use App\Managers\MenuManager;
use App\Managers\HoraireManager;
use App\Managers\OrderManager;




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
            HoraireManager::update($db, $jour, $ouverture, $fermeture);
            header('Location: ?page=dashboard-employee');
            exit();
        }
       
   // COMPTEURS 
        $stats = OrderManager::getDashboardStats($db);
        $totalOrders = $stats['total'];
        $pendingOrders = $stats['en_attente'];
        $finishedOrders = $stats['terminee'];
        // Récupérer les avis et comptage des avis en attente (depuis MongoDB)
       $reviewsList = ReviewManager::getAllReviews($db);
$pendingReviews = count(array_filter($reviewsList, fn($r) => ($r['statut'] ?? 'pending') === 'pending'));


        // Données annexes pour les listes
        
        $commandesList = OrderManager::getRecentOrders($db, 10);


        // Récupération des horaires
         $horairesList = HoraireManager::getAll($db);

        // Récupération du nombre de menus en rupture de stock
$ruptureCount = MenuManager::countRuptureStock($db);


        $title = "Tableau de bord Employé - Vite & Gourmand";

        require_once ROOT_PATH . '/app/views/layout/employee_header.php';
        require_once ROOT_PATH . '/app/views/employee/dashboard.employee.view.php';
        require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
    }
}
