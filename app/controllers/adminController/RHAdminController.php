<?php

namespace App\Controllers\AdminController;

require_once dirname(__DIR__, 2) . '/config/constants.php';

use App\Controllers\AuthController\Auth;
use App\Managers\UserAdminManager;

class RhAdminController
{
    // Affiche la page principale combinée (RH en haut, Modération des users en bas)
    public static function adminRH(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);

        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        try {
            // 1. Récupération des employés (rôle 2)
            $stmtEmp = $pdo->query("SELECT * FROM vg_utilisateur WHERE role_id = 2");
            $employes = $stmtEmp->fetchAll(\PDO::FETCH_ASSOC);

            // 2. Récupération des utilisateurs pour la modération (avec filtres de recherche éventuels)
            $searchTerm = isset($_GET['search-user']) ? trim($_GET['search-user']) : '';
            $roleFilter = isset($_GET['filter-role']) ? trim($_GET['filter-role']) : '';

            require_once ROOT_PATH . '/app/managers/UserAdminManager.php';

            if (!empty($searchTerm) || !empty($roleFilter)) {
                $listeUtilisateurs = UserAdminManager::search($pdo, $searchTerm, $roleFilter);
            } else {
                $listeUtilisateurs = UserAdminManager::findAll($pdo);
            }

            // 3. Récupération des rôles pour le menu déroulant (CORRIGÉ : vg_role au lieu de role)
            $stmtRoles = $pdo->query("SELECT * FROM vg_role ORDER BY libelle ASC");
            $listeRoles = $stmtRoles->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            die($e->getMessage());
        }

        $title = "Gestion des employés et des utilisateurs - Vite & Gourmand";
        $specific_styles = [
            'assets/css/bootstrap/bootstrap.min.css',
            'assets/css/Admin/OrderManagement.css',
            'assets/css/Admin/AdminEmployee.css',
            'https://cdn.datatables.net/1.13.6/dataTables.bootstrap5.min.css'
        ];
        $specific_scripts = [
            "assets/javascript/Rh.js"
        ];

        require_once ROOT_PATH . '/app/views/layout/admin_header.php';
        require_once ROOT_PATH . '/app/views/admin/rh.admin.view.php';
        require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
    }

    // Traite la soumission du formulaire de création d'employé
    public static function createEmploye(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!empty($email) && !empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $roleId = 2; // ID du rôle employé

                try {
                    $stmt = $db->prepare("INSERT INTO vg_utilisateur (email, password, role_id) VALUES (?, ?, ?)");
                    $success = $stmt->execute([$email, $hashedPassword, $roleId]);

                    if ($success) {
                        $to = $email;
                        $subject = "Création de votre compte Vite & Gourmand";
                        $message = "Bonjour,\n\nUn compte employé vient d'être créé pour vous sur Vite & Gourmand.\nVotre identifiant est : " . $email . "\n\nVeuillez vous rapprocher de votre administrateur pour obtenir votre mot de passe.\n\nCordialement,\nL'équipe Vite & Gourmand";
                        $headers = "From: no-reply@vite-gourmand.com";

                        @mail($to, $subject, $message, $headers);

                        $_SESSION['success'] = "L'employé a été créé avec succès et averti par mail.";
                    }
                } catch (\PDOException $e) {
                    $_SESSION['error'] = "Cet email est déjà utilisé par un autre compte.";
                }
            } else {
                $_SESSION['error'] = "Veuillez remplir tous les champs.";
            }
        }

        header('Location: ?page=rh-admin');
        exit;
    }

    // Supprime un employé
    public static function deleteEmploye(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);
        $id = (int)($_GET['id'] ?? 0);

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

    // Alterne le statut actif/inactif d'un employé
    public static function toggleEmployeStatus(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);
        $id = (int)($_GET['id'] ?? 0);

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

    // Désactive (Bannit) un utilisateur globalement
    public static function banUser(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
            $id = (int)($_POST['id'] ?? 0);

            try {
                require_once ROOT_PATH . '/app/managers/UserAdminManager.php';
                UserAdminManager::ban($db, $id);
                $_SESSION['success_message'] = 'Utilisateur désactivé avec succès !';
            } catch (\PDOException $e) {
                die($e->getMessage());
            }
        }
        header('Location: index.php?page=rh-admin');
        exit();
    }

    // Réactive un utilisateur globalement
    public static function unbanUser(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
            $id = (int)($_POST['id'] ?? 0);

            try {
                require_once ROOT_PATH . '/app/managers/UserAdminManager.php';
                UserAdminManager::unBan($db, $id);
                $_SESSION['success_message'] = 'Utilisateur réactivé avec succès !';
            } catch (\PDOException $e) {
                die($e->getMessage());
            }
        }
        header('Location: index.php?page=rh-admin');
        exit();
    }
}