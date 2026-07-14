<?php
namespace App\Controllers\AdminController;
// require_once dirname(__DIR__, 2) . '/config/constants.php';
// use App\Controllers\AuthController\Auth;
// // app/controllers/AdminTicketController.php
// class TicketsAdminController
// {
// public static function adminTickets(\PDO $db)
// {
//     // Sécurité d'accès
//     Auth::check([ROLE_ADMIN]);

//     $title = "Gestion des tickets - EcoRide";
//     $listeTickets = [];

//     try {
//         $pdo = $db;
//         $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

//         require_once ROOT_PATH . '/app/models/Tickets.php';
//         $listeTickets = Ticket::findAll($pdo);
//     } catch (\PDOException $e) {
//         die($e->getMessage());
//     }

//     // Inclusion des vues dédiées aux tickets
//     require ROOT_PATH . '/app/views/layout/admin_header.php';

//     require ROOT_PATH . '/app/views/admin/tickets.admin.view.php';

//     require ROOT_PATH . '/app/views/layout/admin_footer.php';
// }
// public static function deleteTicket(\PDO $db)
// {
//     // Sécurité d'accès
//     Auth::check([ROLE_ADMIN]);
//     //  On vérifie si le formulaire POST est soumis ET que l'id n'est PAS vide
//     if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
//         $id = (int)($_POST['id'] ?? 0);

//         try {
//             $pdo = $db;
//             $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
//             require_once ROOT_PATH . '/app/models/Tickets.php';
//             Ticket::delete($pdo, $id);
//         } catch (\PDOException $e) {
//             die($e->getMessage());
//         }
//     }
//     //redirection après suppression
//     header('Location: index.php?page=tickets-admin');
//     exit();
// }
// }