<?php

namespace App\Models;
    // La classe Timetable représente un horaire d'ouverture et de fermeture pour un jour spécifique. Elle contient les propriétés suivantes :
    // - timetable_id : l'identifiant unique de l'horaire (int) 
    // - jour : le jour de l'horaire (string)
    // - heure_ouverture : l'heure d'ouverture de l'horaire (string)
    // - heure_fermeture : l'heure de fermeture de l'horaire (string) 
class Timetable
{
    public int $timetable_id;
    public string $jour;
    public ?string $heure_ouverture;
    public ?string $heure_fermeture;

    public function __construct(
        int $timetable_id,
        string $jour,
        ?string $heure_ouverture,
        ?string $heure_fermeture
    ) {
        $this->timetable_id = $timetable_id;
        $this->jour = $jour;
        $this->heure_ouverture = $heure_ouverture;
        $this->heure_fermeture = $heure_fermeture;
    }
//function pour récupérer tous les horaires depuis la base de données SQL
    public static function ShowTimetable(\PDO $db, array $timetables = [])
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
            error_log("Erreur lors de la récupération des horaires : " . $e->getMessage());
            return [];
        }
    }
}
