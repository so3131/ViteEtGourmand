<?php

namespace App\Managers;

use App\Models\Timetable;

class HoraireManager
{
    //function pour récupérer tous les horaires depuis la base de données SQL et les retourner sous forme de tableau associatif
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
//function pour récupérer tous les horaires depuis la base de données SQL et les retourner sous forme d'objets Timetable
    public static function getAllTimetables(\PDO $db): array
    {
        try {
            $stmt = $db->query("SELECT * FROM vg_horaire");
            $timetables = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $timetables[] = new Timetable(
                    (int)$row['horaire_id'],
                    $row['jour'],
                    $row['heure_ouverture'],
                    $row['heure_fermeture']
                );
            }
            return $timetables;
        } catch (\PDOException $e) {
            error_log("Erreur HoraireManager::getAllTimetables() : " . $e->getMessage());
            return [];
        }
    }
//function pour mettre à jour les horaires d'ouverture et de fermeture pour un jour spécifique dans la base de données SQL
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