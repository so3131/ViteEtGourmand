<?php
namespace App\Controllers\EmployeeController;
require_once dirname(__DIR__, 2) . '/config/constants.php';
use App\Controllers\AuthController\Auth;
// fonction qu'on appelle pour afficher la page depuis l'index.php
class DashboardEmployeeController
{
public static function employeeDashboard(\PDO $db)
{    Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);

    $pdo = $db;
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    // Autorise le rôle 1 et le rôle 2 (employé et admin) à accéder à cette page, sinon redirige vers l'accueil
    $activeUsers = 0;
    $ecoTrips = 0;
    $creditVolume = 0.00;
    $title = "Accueil du tableau de bord - EcoRide";

    try {

        // Récupérer les avis en attente
        $stmt = $pdo->query("SELECT COUNT(*) FROM avis WHERE statut = 'en attente'");
        $pendingAvis = (int)$stmt->fetchColumn();

        // Récupérer les signalements en attente
        $stmt = $pdo->query("SELECT COUNT(*) FROM signalements WHERE statut = 'en attente'");
        $pendingSignalements = (int)$stmt->fetchColumn();
    } catch (\PDOException $e) {
        // En cas d'erreur de base de données, on évite le crash et on peut logguer l'erreur
        die($e->getMessage());
    }

    $title = "Tableau de bord Employé- EcoRide";

   require_once ROOT_PATH . '/app/views/layout/employee_header.php';
    require_once ROOT_PATH . '/app/views/employee/dashboard.employee.view.php';
    require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
}
}