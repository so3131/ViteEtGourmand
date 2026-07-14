<?php
namespace App\Controllers\AdminController;
// require_once dirname(__DIR__, 2) . '/config/constants.php';
// use App\Controllers\AuthController\Auth;
// class BanUserAdminController
// {
// public static function adminUserList(\PDO $db)
// {
//     // Sécurité d'accès
//     Auth::check([ROLE_ADMIN]);

//     $title = "Gestion des utilisateurs - EcoRide";
//     $listeUtilisateurs = [];
//     $searchTerm = isset($_GET['search-user']) ? trim($_GET['search-user']) : '';
//     $roleFilter = isset($_GET['filter-role']) ? trim($_GET['filter-role']) : '';
//     $listeRoles = [];

//     try {

//         $pdo = $db;
//         $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

//         require_once ROOT_PATH . '/app/models/UserAdmin.php';


//         // Si l'un des deux filtres est actif, on utilise la fonction search, sinon on affiche tout
//         // Aiguillage pour les utilisateurs admin
//         if (!empty($searchTerm) || !empty($roleFilter)) {
//             $listeUtilisateurs = User::search($pdo, $searchTerm, $roleFilter);
//         } else {
//             // Si aucun filtre, on affiche tout comme avant
//             $listeUtilisateurs = User::findAll($pdo);
//         }
//         // Pour le menu déroulant <select> de rôles 
//         $stmtRoles = $pdo->query("SELECT * FROM role ORDER BY libelle ASC");
//         $listeRoles = $stmtRoles->fetchAll(\PDO::FETCH_ASSOC);
//     } catch (\PDOException $e) {
//         die($e->getMessage());
//     }

//     // Inclusion des vues dédiées aux tickets
//     require ROOT_PATH . '/app/views/layout/admin_header.php';

//     require ROOT_PATH . '/app/views/admin/ban.admin.view.php';

//     require ROOT_PATH . '/app/views/layout/admin_footer.php';
// }

// public static function banUser(\PDO $db)
// {
//     // Sécurité d'accès
//     Auth::check([ROLE_ADMIN]);
//     //  On vérifie si le formulaire POST est soumis ET que l'id n'est PAS vide
//     if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
//         $id = (int)($_POST['id'] ?? 0);

//         try {
//             $pdo = $db;
//             $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
//             require_once ROOT_PATH . '/app/models/UserAdmin.php';
//             User::ban($pdo, $id);
//             $_SESSION['success_message'] = 'Utilisateur desactivé avec succès !';

//         } catch (\PDOException $e) {
//             die($e->getMessage());
//         }
//     }
//     //redirection après suppression
//     header('Location: index.php?page=ban-user-admin');
//     exit();
// }

// public static function unbanUser(\PDO $db)
// {
//     // Sécurité d'accès
//     Auth::check([ROLE_ADMIN]);
//     //  On vérifie si le formulaire POST est soumis ET que l'id n'est PAS vide
//     if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
//         $id = (int)($_POST['id'] ?? 0);

//         try {
//             $pdo = $db;
//             $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
//             require_once ROOT_PATH . '/app/models/UserAdmin.php';
//             User::unBan($pdo, $id);
//             $_SESSION['success_message'] = 'Utilisateur réactivé avec succès !';
//         } catch (\PDOException $e) {
//             die($e->getMessage());
//         }
//     }
//     //redirection après debannissement
//     header('Location: index.php?page=ban-user-admin');
//     exit();
// }
// }