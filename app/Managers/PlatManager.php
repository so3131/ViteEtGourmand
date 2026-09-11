<?php

namespace App\Managers;

class PlatManager
{
    // Récupère tous les plats avec le nombre de menus associés
    public static function getAllWithMenuCount(\PDO $db): array
    {
        try {
            $sql = "SELECT p.plat_id, p.titre_plat, p.description_plat, p.categorie, p.photo, p.is_active,
                           COUNT(mp.menu_id) as nb_menus
                    FROM vg_plat p
                    LEFT JOIN vg_menu_plat mp ON p.plat_id = mp.plat_id
                    GROUP BY p.plat_id
                    ORDER BY p.categorie ASC, p.titre_plat ASC";
            return $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur PlatManager::getAllWithMenuCount() : " . $e->getMessage());
            return [];
        }
    }

    // Récupère les plats pour la liste déroulante (sans comptage)
    public static function getAll(\PDO $db): array
    {
        $sql = "SELECT * FROM vg_plat ORDER BY titre_plat ASC";
        return $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Récupère un plat par ID (avec photo)
    public static function getById(\PDO $db, int $platId): ?array
    {
        $stmt = $db->prepare("SELECT photo FROM vg_plat WHERE plat_id = ?");
        $stmt->execute([$platId]);
        $plat = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $plat ?: null;
    }

    // Crée un plat et ses allergènes associés (gère la transaction)
    public static function create(\PDO $db, string $titre, string $description, string $categorie, ?string $photo, array $allergenes): int
    {
        $db->beginTransaction();
        try {
            $sql = "INSERT INTO vg_plat (titre_plat, description_plat, categorie, photo) 
                    VALUES (:titre_plat, :description_plat, :categorie, :photo)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'titre_plat'      => $titre,
                'description_plat' => $description,
                'categorie'       => $categorie,
                'photo'           => $photo,
            ]);
            $platId = (int)$db->lastInsertId();

            if (!empty($allergenes)) {
                $sqlPivot = "INSERT INTO vg_allergene_plat (plat_id, allergene_id) VALUES (?, ?)";
                $stmtPivot = $db->prepare($sqlPivot);
                foreach ($allergenes as $allergeneId) {
                    $stmtPivot->execute([$platId, (int)$allergeneId]);
                }
            }

            $db->commit();
            return $platId;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    // Désactive un plat (soft delete)
    public static function deactivate(\PDO $db, int $platId): bool
    {
        $stmt = $db->prepare("UPDATE vg_plat SET is_active = 0 WHERE plat_id = ?");
        return $stmt->execute([$platId]);
    }

    // Réactive un plat
    public static function activate(\PDO $db, int $platId): bool
    {
        $stmt = $db->prepare("UPDATE vg_plat SET is_active = 1 WHERE plat_id = ?");
        return $stmt->execute([$platId]);
    }

    // Compte les menus liés à un plat
    public static function countLinkedMenus(\PDO $db, int $platId): int
    {
        $stmt = $db->prepare("SELECT COUNT(*) FROM vg_menu_plat WHERE plat_id = ?");
        $stmt->execute([$platId]);
        return (int)$stmt->fetchColumn();
    }

    // Supprime un plat définitivement
    public static function delete(\PDO $db, int $platId): bool
    {
        $stmt = $db->prepare("DELETE FROM vg_plat WHERE plat_id = ?");
        return $stmt->execute([$platId]);
    }
}
