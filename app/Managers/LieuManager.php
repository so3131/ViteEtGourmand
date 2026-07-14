<?php
namespace App\Managers;
class LieuManager {
    // 1. Récupérer tous les lieux pour les afficher dans le <select> du formulaire
    public static function getAll(\PDO $db): array {
        $stmt = $db->query("SELECT * FROM vg_lieu_prestation");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // 2. Récupérer un lieu précis (pour le calcul du prix lors du POST)
    public static function getById(\PDO $db, int $id): ?array {
        $stmt = $db->prepare("SELECT * FROM vg_lieu_prestation WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}