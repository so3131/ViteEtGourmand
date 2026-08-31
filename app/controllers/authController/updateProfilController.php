<?php
namespace App\Controllers\AuthController;
require_once dirname(__DIR__, 2) . '/config/constants.php';
use App\Controllers\AuthController\Auth;

class UpdateProfilController
{
    public static function updateProfil(\PDO $db)
    {
        Auth::checkLogin();

        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 🛡️ Vérification CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                $error = "Session expirée ou requête invalide. Veuillez recharger la page.";
            }
            $updates = [];
            $params = ['id' => $_SESSION['user_id']];

            // Charger les données actuelles depuis la DB
            $sqlGet = "SELECT nom, prenom, telephone, ville, pays, adresse_postale FROM vg_utilisateur WHERE utilisateur_id = :id";
            $stmtGet = $pdo->prepare($sqlGet);
            $stmtGet->execute(['id' => $_SESSION['user_id']]);
            $currentData = $stmtGet->fetch(\PDO::FETCH_ASSOC);

            // Mapping des champs du formulaire vers les colonnes exactes de ta table
            $champsModifiables = [
                'nom'           => 'nom',
                'prenom'        => 'prenom',
                'numero_tel'    => 'telephone',
                'adresse'       => 'adresse_postale',
                'ville'         => 'ville',
                'pays'          => 'pays'
            ];

            foreach ($champsModifiables as $postKey => $dbCol) {
                if (isset($_POST[$postKey]) && $_POST[$postKey] !== '') {
                    $value = htmlspecialchars(trim($_POST[$postKey]));
                    
                    // Vérifier si la valeur a changé par rapport à la base
                    if (!isset($currentData[$dbCol]) || $value !== $currentData[$dbCol]) {
                        $updates[] = "$dbCol = :$dbCol";
                        $params[$dbCol] = $value;
                        $_SESSION[$dbCol] = $value;
                    }
                }
            }

            // ===== EXÉCUTION =====
            if ($error === null && !empty($updates)) {
                try {
                    $sql = "UPDATE vg_utilisateur SET " . implode(', ', $updates) . " WHERE utilisateur_id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);

                    header('Location: index.php?page=dashboard-user&success=1');
exit();
                } catch (\PDOException $e) {
                    $error = "Erreur lors de la mise à jour";
                }
            }
        }

        // ===== PRÉPARATION VUE =====
        $title = "Modifier profil - Vite&Gourmand";
        $specific_styles = [];
        $specific_scripts = ["../public/assets/javascript/auth.js",];

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/auth/update.profile.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}