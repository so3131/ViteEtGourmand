<?php
namespace App\Controllers\AdminController;
// require_once dirname(__DIR__, 2) . '/config/constants.php';
// use App\Controllers\AuthController\Auth;
// // fonction qu'on appelle pour afficher la page depuis l'index.php
// class DashboardAdminController
// {
// public static function adminDashboard(\PDO $db)
// {
//     // Sécurité : On vérifie si l'utilisateur est bien ADMIN grâce à la constante globale
//   Auth::check([ROLE_ADMIN]);  

//     // 1. Initialisation des variables pour éviter le "Undefined variable" si la BDD est vide
//     $activeUsers = 0;
//     $ecoTrips = 0;
//     $creditVolume = 0.00;
//     $totalTickets = 0;
//     $listeTickets = [];
//     $title = "Accueil du tableau de bord - EcoRide";

//     try {
//         $pdo = $db;
//         $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

//         require_once ROOT_PATH . '/app/models/Tickets.php';
//         // Récupérer le nombre d'utilisateurs actifs
//         $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE est_actif = 1");
//         $activeUsers = (int)$stmt->fetchColumn();

//         // Récupérer le nombre de trajets écologiques du mois
//         $stmt = $pdo->query("SELECT COUNT(*) FROM covoiturage WHERE is_eco = 1 AND YEAR(date_depart) = YEAR(CURDATE()) AND MONTH(date_depart) = MONTH(CURDATE())");
//         $ecoTrips = (int)$stmt->fetchColumn();

//         // Récupérer le volume de crédits (on force à 0 si le SUM renvoie NULL)
//         $stmt = $pdo->query("SELECT SUM(solde_credits) FROM utilisateurs WHERE solde_credits IS NOT NULL");
//         $creditVolume = (float)($stmt->fetchColumn() ?? 0);


//         // On appelle la méthode statique en lui transmettant le $pdo de cette fonction
//         $stmt = $pdo->query("SELECT COUNT(*) FROM tickets");
//         $totalTickets = (int)($stmt->fetchColumn() ?? 0);
//         $listeTickets = \Ticket::findAll($pdo);
//     } catch (\PDOException $e) {
//         // En cas d'erreur de base de données, on évite le crash et on peut logguer l'erreur
//         die($e->getMessage());
//     }

//     // 2. Inclusion des vues en utilisant ROOT_PATH (plus propre et sécurisé)
//     require ROOT_PATH . '/app/views/layout/admin_header.php';
//     require ROOT_PATH . '/app/views/admin/dashboard.admin.view.php';
//     require ROOT_PATH . '/app/views/layout/admin_footer.php';
// }
// }