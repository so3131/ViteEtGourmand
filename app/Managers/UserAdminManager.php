<?php

namespace App\Managers;

// class UserAdminManager pour gérer les utilisateurs dans la base de données (recherche, bannissement, réactivation)
class UserAdminManager
{
    //function pour récupérer tous les utilisateurs avec leur rôle
    public static function findAll(\PDO $pdo)
    {
        $stmt = $pdo->query("SELECT u.*, r.libelle AS role_nom 
                FROM vg_utilisateur u
                LEFT JOIN vg_role r ON u.role_id = r.role_id
                ORDER BY u.utilisateur_id DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //function pour desactiver un utilisateur (bannir) en vérifiant d'abord qu'il n'est pas administrateur
    public static function ban(\PDO $pdo, int $id)
    {
        try {
            // 1. Vérification si l'utilisateur est un administrateur
            $checkStmt = $pdo->prepare("SELECT role_id FROM vg_utilisateur WHERE utilisateur_id = :id");
            $checkStmt->execute(['id' => $id]);
            $userTarget = $checkStmt->fetch(\PDO::FETCH_ASSOC);

            
            if ($userTarget && $userTarget['role_id'] == 1) {
                throw new \Exception("Action impossible : vous ne pouvez pas bannir un administrateur.");
            }

            //  Si ce n'est pas un admin, on ban.
            $sqlUser = "UPDATE vg_utilisateur SET est_actif = 0 WHERE utilisateur_id = :id";
            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute(['id' => $id]);

            return true;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return false;
        }
    }

    //function pour réactiver un utilisateur (unban)
  //function pour réactiver un utilisateur (unban)
    public static function unBan(\PDO $pdo, int $id)
    {
        $stmtUser = $pdo->prepare("UPDATE vg_utilisateur SET est_actif = 1 WHERE utilisateur_id = :id");
        return $stmtUser->execute(['id' => $id]);
    }

    //function pour rechercher des utilisateurs par nom, prénom, email ou rôle avec des filtres optionnels
    public static function search(\PDO $pdo, string $searchTerm, ?int $roleId = null)
    {
        $sql = "SELECT u.*, r.libelle AS role_nom 
            FROM vg_utilisateur u
            LEFT JOIN vg_role r ON u.role_id = r.role_id
            WHERE 1=1";

        $params = [];

        if (!empty($searchTerm)) {
            $sql .= " AND (u.nom LIKE :term OR u.prenom LIKE :term OR u.email LIKE :term)";
            $params['term'] = "%$searchTerm%";
        }

        if (!empty($roleId)) {
            $sql .= " AND u.role_id = :role_id";
            $params['role_id'] = (int)$roleId;
        }

        $sql .= " ORDER BY u.utilisateur_id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    // Utilisateurs d'un rôle donné (page RH : employés = role_id 2)
    public static function getByRoleId(\PDO $db, int $roleId): array
    {
        $stmt = $db->prepare("SELECT * FROM vg_utilisateur WHERE role_id = :role_id");
        $stmt->execute(['role_id' => $roleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Liste des rôles (menu déroulant page RH)
    public static function getAllRoles(\PDO $db): array
    {
        return $db->query("SELECT * FROM vg_role ORDER BY libelle ASC")->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Crée un compte staff (employé/admin) : email + mdp + rôle, sans champs client obligatoires
    public static function createStaff(\PDO $db, string $email, string $hashedPassword, int $roleId): bool
    {
        $stmt = $db->prepare("INSERT INTO vg_utilisateur (email, password, role_id) VALUES (:email, :password, :role_id)");
        return $stmt->execute(['email' => $email, 'password' => $hashedPassword, 'role_id' => $roleId]);
    }

    // Supprime définitivement un compte
    public static function deleteById(\PDO $db, int $userId): bool
    {
        $stmt = $db->prepare("DELETE FROM vg_utilisateur WHERE utilisateur_id = :id");
        return $stmt->execute(['id' => $userId]);
    }

    // Inverse le statut actif d'un compte (activation/désactivation rapide)
    public static function toggleActive(\PDO $db, int $userId): bool
    {
        $stmt = $db->prepare("UPDATE vg_utilisateur SET est_actif = NOT est_actif WHERE utilisateur_id = :id");
        return $stmt->execute(['id' => $userId]);
    }
}
