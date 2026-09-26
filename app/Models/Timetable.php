<?php

namespace App\Models;

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
}
