<?php

namespace App\Controllers\AdminController;

require_once dirname(__DIR__, 2) . '/config/constants.php';

use App\Controllers\AuthController\Auth;
use App\Managers\UserAdminManager;
use App\Helpers\MailService;
use app\Helpers\SecurityManager;

class RhAdminController
{
    //function pour afficher la page de gestion des employés et des utilisateurs
    public static function adminRH(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);

        try {
            // Récupération des employés (rôle 2)
            $employes = UserAdminManager::getByRoleId($db, 2);

            // Récupération des utilisateurs pour la modération (avec filtres de recherche)
          $searchTerm = isset($_GET['search-user']) ? trim($_GET['search-user']) : '';
            $roleFilter = (isset($_GET['filter-role']) && $_GET['filter-role'] !== '') ? (int)$_GET['filter-role'] : null;

            if ($searchTerm !== '' || $roleFilter !== null) {
                $listeUtilisateurs = UserAdminManager::search($db, $searchTerm, $roleFilter);
            } else {
                $listeUtilisateurs = UserAdminManager::findAll($db);
            }

            // Récupération des rôles pour le menu déroulant
            $listeRoles = UserAdminManager::getAllRoles($db);
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur lors du chargement de la page : " . $e->getMessage();
            header('Location: index.php?page=dashboard-admin');
            exit();
        }

        $title = "Gestion des employés et des utilisateurs - Vite & Gourmand";
        $specific_styles = [
            'assets/css/bootstrap/bootstrap.min.css',
            'assets/css/AdminEmployee/AdminEmployee.css',
            'https://cdn.datatables.net/1.13.6/dataTables.bootstrap5.min.css'
        ];
        $specific_scripts = [
            "assets/javascript/tables.js"
        ];

        require_once ROOT_PATH . '/app/views/layout/admin_header.php';
        require_once ROOT_PATH . '/app/views/admin/rh.admin.view.php';
        require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
    }

    //function pour créer un employé
    public static function createEmploye(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);
        SecurityManager::validatePost('?page=rh-admin');

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!empty($email) && !empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $roleId = 2; // ID du rôle employé

            try {
                if (UserAdminManager::createStaff($db, $email, $hashedPassword, $roleId)) {
                    $_SESSION['success_message'] = "L'employé a été créé avec succès et averti par mail.";



                    MailService::sendAccountCreationEmail($email);
                    $_SESSION['success'] = "L'employé a été créé avec succès et averti par mail.";
                }
            } catch (\PDOException $e) {
                $_SESSION['error'] = "Cet email est déjà utilisé par un autre compte.";
            }
        } else {
            $_SESSION['error'] = "Veuillez remplir tous les champs.";
        }


        header('Location: ?page=rh-admin');
        exit;
    }
    //function pour supprimer un employé
    public static function deleteEmploye(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);
        $id = SecurityManager::validatePost('?page=rh-admin');
        if (!empty($id)) {
            try {
                UserAdminManager::deleteById($db, (int)$id);

                $_SESSION['success'] = "L'employé a été supprimé avec succès.";
            } catch (\PDOException $e) {
                $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
            }
        }

        header('Location: ?page=rh-admin');
        exit;
    }

    //function pour activer/désactiver un employé
    public static function toggleEmployeStatus(\PDO $db, ?int $id)
    {
        Auth::check([ROLE_ADMIN]);

        $id = SecurityManager::validatePost('?page=rh-admin');

        if (!empty($id)) {
            try {

                UserAdminManager::toggleActive($db, (int)$id);

                $_SESSION['success'] = "Le statut de l'employé a été mis à jour avec succès.";
            } catch (\PDOException $e) {
                $_SESSION['error'] = "Erreur lors de la modification du statut : " . $e->getMessage();
            }
        }

        header('Location: index.php?page=rh-admin');
        exit();
    }

    //function pour désactiver un utilisateur
    public static function banUser(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);
        $id = SecurityManager::validatePost('?page=rh-admin');
        try {
            if (UserAdminManager::ban($db, $id)) {
                $_SESSION['success_message'] = 'Utilisateur désactivé avec succès !';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur lors de la désactivation de l'utilisateur : " . $e->getMessage();

            header('Location: index.php?page=rh-admin');
            exit();
        }

        header('Location: index.php?page=rh-admin');
        exit();
    }

    // Réactive un utilisateur globalement
    public static function unbanUser(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);
        $id = SecurityManager::validatePost('?page=rh-admin');

        try {

            UserAdminManager::unBan($db, $id);
            $_SESSION['success_message'] = 'Utilisateur réactivé avec succès !';
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur lors de la réactivation de l'utilisateur : " . $e->getMessage();
            header('Location: index.php?page=rh-admin');
            exit();
        }

        header('Location: index.php?page=rh-admin');
        exit();
    }
}
