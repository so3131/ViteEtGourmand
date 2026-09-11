<?php

namespace App\Managers;

// class ReviewManager pour gérer les avis dans la base SQL (conforme MCD ECF)
class ReviewManager
{
    // Insère un nouvel avis (appelé après vérification commande terminée + propriétaire)
    public static function insertReview(\PDO $db, array $data): bool
    {
        $stmt = $db->prepare(
            "INSERT INTO vg_avis (note, description, statut, utilisateur_id, commande_id) 
             VALUES (:note, :description, 'pending', :utilisateur_id, :commande_id)"
        );
        return $stmt->execute([
            'note'           => (int)$data['rating'],
            'description'    => $data['comment'],
            'utilisateur_id' => (int)$data['user_id'],
            'commande_id'    => (int)$data['commande_id'],
        ]);
    }

    // Vérifie si une commande a déjà un avis (contrainte UNIQUE aussi en base)
    public static function alreadyReviewedOrder(\PDO $db, int $commandeId): bool
    {
        $stmt = $db->prepare("SELECT COUNT(*) FROM vg_avis WHERE commande_id = ?");
        $stmt->execute([$commandeId]);
        return $stmt->fetchColumn() > 0;
    }

    // Récupère tous les avis, avec filtre optionnel sur le statut
    public static function getAllReviews(\PDO $db, ?string $status = null): array
    {
        $sql = "SELECT a.*, 
                       CONCAT(u.prenom, ' ', u.nom) AS nom_auteur,
                       u.email AS auteur_email,
                       c.numero_commande,
                       m.titre AS nom_menu
                FROM vg_avis a
                JOIN vg_utilisateur u ON a.utilisateur_id = u.utilisateur_id
                LEFT JOIN vg_commande c ON a.commande_id = c.commande_id
                LEFT JOIN vg_menu m ON c.menu_id = m.menu_id
                WHERE 1=1";
        $params = [];

        if (!empty($status)) {
            $sql .= " AND a.statut = :statut";
            $params['statut'] = $status;
        }
        $sql .= " ORDER BY a.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Récupère un avis par son ID
    public static function getReviewById(\PDO $db, int $avisId): ?array
    {
        $stmt = $db->prepare(
            "SELECT a.*, CONCAT(u.prenom, ' ', u.nom) AS nom_auteur
             FROM vg_avis a
             JOIN vg_utilisateur u ON a.utilisateur_id = u.utilisateur_id
             WHERE a.avis_id = :id"
        );
        $stmt->execute(['id' => $avisId]);
        $review = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $review ?: null;
    }

    // Met à jour le statut d'un avis avec traçabilité de la modération
    public static function updateReviewStatus(\PDO $db, int $avisId, string $status, ?int $userId = null, ?string $userName = null): bool
    {
        if (!in_array($status, ['approved', 'rejected'], true)) {
            return false;
        }

        $sql = "UPDATE vg_avis 
                SET statut = :statut, 
                    validated_by = :validated_by, 
                    validated_by_name = :validated_by_name, 
                    validated_at = NOW() 
                WHERE avis_id = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'statut'           => $status,
            'validated_by'     => $userId,
            'validated_by_name'=> $userName,
            'id'               => $avisId,
        ]);
        return $stmt->rowCount() > 0;
    }

    // Récupère les derniers avis approuvés pour la page d'accueil
    public static function getApprovedReviews(\PDO $db, int $limit = 6): array
    {
        $stmt = $db->prepare(
            "SELECT a.*, 
                    CONCAT(u.prenom, ' ', u.nom) AS nom_auteur,
                    m.titre AS titre
             FROM vg_avis a
             JOIN vg_utilisateur u ON a.utilisateur_id = u.utilisateur_id
             LEFT JOIN vg_commande c ON a.commande_id = c.commande_id
             LEFT JOIN vg_menu m ON c.menu_id = m.menu_id
             WHERE a.statut = 'approved'
             ORDER BY a.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function getCompletedOrderForUser(\PDO $db, int $commandeId, int $userId): ?array
{
    $stmt = $db->prepare("SELECT * FROM vg_commande 
                          WHERE commande_id = ? AND utilisateur_id = ? AND statut = 'terminee'");
    $stmt->execute([$commandeId, $userId]);
    $order = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $order ?: null;
}
}
