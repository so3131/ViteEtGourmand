<?php

namespace App\Controllers\UserController;
use App\Models\Timetable;

class TimeTableController
{
    public static function timetable(\PDO $db)
    {
        try {
            $timetables = Timetable::ShowTimetable($db);
            
            if (!$timetables) {
                $timetables = [];
            }
        } catch (\Exception $e) {
            error_log("Erreur horaires : " . $e->getMessage());
            $timetables = [];
        }

        // Variables pour la vue
        $title = "Horaires d'ouverture - Vite Gourmand";
        $specifics_fonts = "https://fonts.googleapis.com/css?family=Lexend&display=swap";
        $specific_styles = ["assets/css/styleSearch.css"];
        $specific_scripts = [];

        // Chargement des composants
        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/timetable.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}