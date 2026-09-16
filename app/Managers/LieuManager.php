<?php

namespace App\Managers;
// class LieuManager pour gérer les lieux de prestation
class LieuManager
{
    // Fonction pour récupérer tous les lieux de prestation
    public static function getAll(\PDO $db): array
    {
        $stmt = $db->query("SELECT * FROM vg_lieu_prestation");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Fonction pour récupérer un lieu de prestation par son ID
    public static function getById(\PDO $db, int $id): ?array
    {
        $stmt = $db->prepare("SELECT * FROM vg_lieu_prestation WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    // Fonction pour insérer un nouveau lieu de prestation ou récupérer l'ID s'il existe déjà
    public static function getOrInsert(\PDO $db, string $adresse, string $codePostal, string $ville, ?float $lat = null, ?float $lon = null): int
    {
        $adresse = trim($adresse);
        $ville = trim($ville);
        // EMPÊCHER l'insertion d'un lieu incomplet
        if (empty($ville) || empty($adresse)) {
            throw new \Exception("Adresse ou ville manquante : lieu de livraison incomplet.");
        }
        if ($lat === null || $lon === null || $lat === 0.0 || $lon === 0.0) {
            throw new \Exception("Coordonnées GPS manquantes : lieu de livraison incomplet.");
        }
        if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
            throw new \Exception("Coordonnées GPS invalides.");
        }

        // Verifier si la ville est déjà à la fin de l'adresse pour éviter les doublons
        if (!empty($ville) && str_ends_with(mb_strtolower($adresse), mb_strtolower($ville))) {
            // Nettoie la ville de la fin de la chaîne si elle s'y trouve déjà en double
            $adresse = trim(preg_replace('/' . preg_quote($ville, '/') . '$/ui', '', $adresse));
            // Nettoie la virgule finale s'il en reste une
            $adresse = rtrim($adresse, ', ');
        }

        // On cherche par adresse et ville nettoyées
        $stmt = $db->prepare("SELECT id FROM vg_lieu_prestation WHERE adresse = ? AND ville = ? LIMIT 1");
        $stmt->execute([$adresse, $ville]);
        $lieu = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($lieu) {
            return (int)$lieu['id'];
        }

        // Si le lieu n'existe pas, on l'insère
        $stmtInsert = $db->prepare("
        INSERT INTO vg_lieu_prestation (adresse, code_postal, ville, latitude, longitude) 
        VALUES (?, ?, ?, ?, ?)
    ");
        $stmtInsert->execute([
            $adresse,
            trim($codePostal),
            $ville,
            $lat,
            $lon
        ]);

        return (int)$db->lastInsertId();
    }
}
