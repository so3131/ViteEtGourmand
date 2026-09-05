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

        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        try {
            // Récupération des employés (rôle 2)
            $stmtEmp = $pdo->query("SELECT * FROM vg_utilisateur WHERE role_id = 2");
            $employes = $stmtEmp->fetchAll(\PDO::FETCH_ASSOC);

            // Récupération des utilisateurs pour la modération (avec filtres de recherche)
            $searchTerm = isset($_GET['search-user']) ? trim($_GET['search-user']) : '';
            $roleFilter = isset($_GET['filter-role']) ? trim($_GET['filter-role']) : '';

            require_once ROOT_PATH . '/app/managers/UserAdminManager.php';

            if (!empty($searchTerm) || !empty($roleFilter)) {
                $listeUtilisateurs = UserAdminManager::search($pdo, $searchTerm, $roleFilter);
            } else {
                $listeUtilisateurs = UserAdminManager::findAll($pdo);
            }

            // Récupération des rôles pour le menu déroulant
            $stmtRoles = $pdo->query("SELECT * FROM vg_role ORDER BY libelle ASC");
            $listeRoles = $stmtRoles->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die($e->getMessage());
        }

        $title = "Gestion des employés et des utilisateurs - Vite & Gourmand";
        $specific_styles = [
            'assets/css/bootstrap/bootstrap.min.css',
            'assets/css/Admin/AdminEmployee.css',
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
                $stmt = $db->prepare("INSERT INTO vg_utilisateur (email, password, role_id) VALUES (?, ?, ?)");
                $success = $stmt->execute([$email, $hashedPassword, $roleId]);

                if ($success) {
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
        $id = (int)($_POST['id'] ?? 0);
        $id = SecurityManager::validatePost('?page=rh-admin');
        if (!empty($id)) {
            try {
                $stmt = $db->prepare("DELETE FROM vg_utilisateur WHERE utilisateur_id = :id");
                $stmt->execute(['id' => $id]);

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
        $id = (int)($_POST['id'] ?? $id ?? 0);
        $id = SecurityManager::validatePost('?page=rh-admin');

        if (!empty($id)) {
            try {
                $stmt = $db->prepare("UPDATE vg_utilisateur SET est_actif = NOT est_actif WHERE utilisateur_id = :id");
                $stmt->execute(['id' => $id]);

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
            require_once ROOT_PATH . '/app/managers/UserAdminManager.php';
            UserAdminManager::ban($db, $id);
            $_SESSION['success_message'] = 'Utilisateur désactivé avec succès !';
        } catch (\PDOException $e) {
            die($e->getMessage());
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
            require_once ROOT_PATH . '/app/managers/UserAdminManager.php';
            UserAdminManager::unBan($db, $id);
            $_SESSION['success_message'] = 'Utilisateur réactivé avec succès !';
        } catch (\PDOException $e) {
            die($e->getMessage());
        }

        header('Location: index.php?page=rh-admin');
        exit();
    }
}
