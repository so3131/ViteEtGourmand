<?php

namespace App\Managers;

class HoraireManager
{
    public static function getAll(\PDO $db): array
    {
        try {
            $stmt = $db->query("SELECT * FROM vg_horaire");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur HoraireManager::getAll() : " . $e->getMessage());
            return [];
        }
    }

    public static function update(\PDO $db, string $jour, ?string $ouverture, ?string $fermeture): bool
    {
        try {
            $stmt = $db->prepare(
                "UPDATE vg_horaire SET heure_ouverture = ?, heure_fermeture = ? WHERE jour = ?"
            );
            return $stmt->execute([$ouverture, $fermeture, $jour]);
        } catch (\PDOException $e) {
            error_log("Erreur HoraireManager::update() : " . $e->getMessage());
            return false;
        }
    }
}
