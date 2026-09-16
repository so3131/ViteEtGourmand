<?php

namespace App\Managers;


class UserManager
{
    // Vérifie si un email existe déjà
    public static function findByEmail(\PDO $db, string $email): ?array
    {
        $stmt = $db->prepare("SELECT * FROM vg_utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // Crée un nouvel utilisateur
    public static function create(\PDO $db, array $data): int
    {
        $sql = "INSERT INTO vg_utilisateur 
                (nom, prenom, telephone, email, password, role_id, adresse_postale, ville, pays) 
                VALUES (:nom, :prenom, :telephone, :email, :password, 3, :adresse_postale, :ville, :pays)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'nom'             => $data['nom'],
            'prenom'          => $data['prenom'],
            'telephone'       => $data['telephone'],
            'email'           => $data['email'],
            'password'        => $data['password'],
            'adresse_postale' => $data['adresse_postale'],
            'ville'           => $data['ville'],
            'pays'            => $data['pays'],
        ]);
        return (int)$db->lastInsertId();
    }

    // Récupère un utilisateur par ID
    public static function findById(\PDO $db, int $id): ?array
    {
        $stmt = $db->prepare("SELECT * FROM vg_utilisateur WHERE utilisateur_id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // Met à jour le profil utilisateur
    public static function updateProfil(\PDO $db, int $userId, array $updates): bool
    {
        if (empty($updates)) return false;
        $setParts = [];
        $params = ['id' => $userId];
        foreach ($updates as $column => $value) {
            $setParts[] = "$column = :$column";
            $params[$column] = $value;
        }
        $sql = "UPDATE vg_utilisateur SET " . implode(', ', $setParts) . " WHERE utilisateur_id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    // Génère et stocke un token de réinitialisation
    public static function createPasswordResetToken(\PDO $db, string $email): string
    {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $sql = "INSERT INTO vg_password_resets (email, token, expires_at)
                VALUES (:email, :token, :expires_at)
                ON DUPLICATE KEY UPDATE
                    token = :token, expires_at = :expires_at";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'email'      => $email,
            'token'      => $tokenHash,
            'expires_at' => $expiresAt,
        ]);
        return $token;
    }

    // Récupère un utilisateur par token de réinitialisation
    public static function findByResetToken(\PDO $db, string $tokenHash): ?array
    {
        $stmt = $db->prepare(
            "SELECT email FROM vg_password_resets WHERE token = :token AND expires_at > NOW()"
        );
        $stmt->execute(['token' => $tokenHash]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    // Met à jour le mot de passe
    public static function updatePassword(\PDO $db, string $email, string $hashedPassword): bool
    {
        $stmt = $db->prepare("UPDATE vg_utilisateur SET password = :password WHERE email = :email");
        return $stmt->execute(['password' => $hashedPassword, 'email' => $email]);
    }

    // Supprime un token de réinitialisation
    public static function deletePasswordResetToken(\PDO $db, string $tokenHash): bool
    {
        $stmt = $db->prepare("DELETE FROM vg_password_resets WHERE token = :token");
        return $stmt->execute(['token' => $tokenHash]);
    }

    // Compte les commandes actives (non terminées) d'un utilisateur
    public static function countActiveOrders(\PDO $db, int $userId): int
    {
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM vg_commande 
             WHERE utilisateur_id = ? 
             AND statut NOT IN ('terminee', 'annulee', 'livree')"
        );
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    // Désactive un compte utilisateur
    public static function deactivate(\PDO $db, int $userId): bool
    {
        $stmt = $db->prepare("UPDATE vg_utilisateur SET est_actif = 0 WHERE utilisateur_id = ?");
        return $stmt->execute([$userId]);
    }
    // Vérifie un token de réinitialisation encore valide ; retourne l'email associé ou null
    public static function findPasswordResetToken(\PDO $db, string $tokenHash): ?array
    {
        $stmt = $db->prepare("
            SELECT email
            FROM vg_password_resets
            WHERE token = :token
              AND expires_at > NOW()
        ");
        $stmt->execute(['token' => $tokenHash]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
