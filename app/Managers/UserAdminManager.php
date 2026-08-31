<?php

namespace App\Managers;

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
        // 1. On vérifie d'abord le rôle de l'utilisateur cible
        $checkStmt = $pdo->prepare("SELECT role_id FROM vg_utilisateur WHERE utilisateur_id = :id");
        $checkStmt->execute(['id' => $id]);
        $userTarget = $checkStmt->fetch(\PDO::FETCH_ASSOC);

        // Supposons que 1 est l'ID du rôle Administrateur
        if ($userTarget && $userTarget['role_id'] == 1) {
            throw new \Exception("Action impossible : vous ne pouvez pas bannir un administrateur.");
        }

        // 2. Si ce n'est pas un admin, on procède au bannissement
        $sqlUser = "UPDATE vg_utilisateur SET est_actif = 0 WHERE utilisateur_id = :id";
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute(['id' => $id]);

        return true;
    } catch (\Exception $e) {
        // Gère l'erreur proprement (tu peux stocker dans $_SESSION['error'] ou laisser l'exception remonter)
        $_SESSION['error'] = $e->getMessage();
        return false;
    }
}

  //function pour réactiver un utilisateur (unban)
    public static function unBan(\PDO $pdo, int $id)
    {
        try {
            $sqlUser = "UPDATE vg_utilisateur SET est_actif = 1 WHERE utilisateur_id = :id";
            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute(['id' => $id]);

            return true;
        } catch (\PDOException $e) {
            die("Erreur lors de la réactivation de l'utilisateur : " . $e->getMessage());
        }
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
}