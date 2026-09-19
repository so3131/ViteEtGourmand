<?php

namespace App\Controllers\AuthController;

require_once dirname(__DIR__, 2) . '/Config/Constants.php';

use App\Controllers\AuthController\Auth;
use App\Helpers\SecurityManager;
use App\Managers\UserManager;

// Class UpdateProfilController pour gérer la mise à jour du profil des utilisateurs
class UpdateProfilController
{
    // function pour afficher la page de mise à jour du profil et gérer la mise à jour
    public static function updateProfil(\PDO $db)
    {
        Auth::checkLogin();


        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification CSRF
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
                $error = "Session expirée ou requête invalide. Veuillez recharger la page.";
            }



            // Charger les données actuelles depuis la DB
            $currentData = UserManager::findById($db, $_SESSION['user_id']);

            // Mapping des champs du formulaire vers les colonnes de la base de données
            $champsModifiables = [
                'nom'           => 'nom',
                'prenom'        => 'prenom',
                'numero_tel'    => 'telephone',
                'adresse'       => 'adresse_postale',
                'ville'         => 'ville',
                'pays'          => 'pays'
            ];
            $updates = [];
            foreach ($champsModifiables as $postKey => $dbCol) {
                if (isset($_POST[$postKey]) && $_POST[$postKey] !== '') {
                    $value = htmlspecialchars(trim($_POST[$postKey]));

                    // Vérifier si la valeur a changé par rapport à la base
                    if (!isset($currentData[$dbCol]) || $value !== $currentData[$dbCol]) {
                        $updates[$dbCol] = $value;
                        $_SESSION[$dbCol] = $value;
                    }
                }
            }

            // Si pas d'erreurs et qu'il y a des mises à jour à faire, on exécute
            if ($error === null && !empty($updates)) {
                try {
                    UserManager::updateProfil($db, $_SESSION['user_id'], $updates);
                    header('Location: index.php?page=dashboard-user&success=1');
                    exit();
                } catch (\PDOException $e) {
                    $error = "Erreur lors de la mise à jour";
                }
            }
        }

        $title = "Modifier profil - Vite&Gourmand";
        $specific_styles = [];
        $specific_scripts = ["assets/javascript/auth.js",];

        require_once ROOT_PATH . '/app/Views/layout/header.php';
        require_once ROOT_PATH . '/app/Views/Auth/update.profile.view.php';
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
    }
}
