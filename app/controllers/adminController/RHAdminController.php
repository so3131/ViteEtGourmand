<?php

namespace App\Controllers\AdminController;

require_once dirname(__DIR__, 2) . '/config/constants.php';

use App\Controllers\AuthController\Auth;

class RhAdminController
{
    // Affiche la page principale RH avec la liste et le formulaire
    public static function adminRH(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);

        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // MODIFICATION ICI : on utilise role_id (adapte '2' si l'ID de l'employé est différent dans ta base)
        $stmt = $pdo->query("SELECT * FROM vg_utilisateur WHERE role_id = 2");
        $employes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $title = "Gestion des employés - Vite & Gourmand";
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

    // Traite la soumission du formulaire de création
    public static function createEmploye(\PDO $db)
    {
        Auth::check([ROLE_ADMIN]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';


            if (!empty($email) && !empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $roleId = 2; // Mets ici l'ID exact qui correspond au rôle "employé" dans ta table des rôles

                try {
                    // MODIFICATION ICI : on insère dans role_id
                    $stmt = $db->prepare("INSERT INTO vg_utilisateur (email, password, role_id) VALUES (?, ?, ?)");
                    $success = $stmt->execute([$email, $hashedPassword, $roleId]);

                    if ($success) {
                        // Envoi du mail via MailHog (SANS le mot de passe)
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
    public static function deleteEmploye(\PDO $db, int $id)
    {
        // Sécurité : Vérifier que l'utilisateur connecté est bien admin
        Auth::check([ROLE_ADMIN]);

        if (!empty($id)) {
            try {
                // Requête pour supprimer l'employé par son ID
                $stmt = $db->prepare("DELETE FROM vg_utilisateur WHERE utilisateur_id = :id");
                $stmt->execute(['id' => $id]);

                $_SESSION['success'] = "L'employé a été supprimé avec succès.";
            } catch (\PDOException $e) {
                $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
            }
        }

        // Redirection vers la page RH
        header('Location: ?page=rh-admin');
        exit;
    }
    public static function toggleEmployeStatus(\PDO $db, $id)
    {
        Auth::check([ROLE_ADMIN]);

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
}
