<?php
namespace App\Controllers\AuthController;
require_once dirname(__DIR__, 2) . '/config/constants.php';
use App\Controllers\AuthController\Auth; // On importe la classe Auth pour pouvoir utiliser la méthode setUserSession() après une connexion réussie

/**
 * Gère la modification du profil utilisateur
    */
class UpdateProfilController
{
public static function updateProfil(\PDO $db)
{
    Auth::checkLogin();

    $pdo = $db;
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userRole = (int)$_SESSION['role_id'];

        $updates = [];
        $params = ['id' => $_SESSION['user_id']];

        // Charger les données actuelles depuis la DB pour comparer
        $sqlGet = "SELECT nom, prenom, pseudo, date_naissance FROM utilisateurs WHERE utilisateur_id = :id";
        $stmtGet = $pdo->prepare($sqlGet);
        $stmtGet->execute(['id' => $_SESSION['user_id']]);
        $currentData = $stmtGet->fetch(\PDO::FETCH_ASSOC);

        // ===== CHAMPS LIBRES (modifiables toujours) =====
        $champsLibres = [
            'numero_tel'  => 'telephone',
            'adresse'     => 'adresse',
            'ville'       => 'ville',
            'code_postal' => 'code_postal'
        ];

        foreach ($champsLibres as $postKey => $dbCol) {
            if (isset($_POST[$postKey]) && $_POST[$postKey] !== '') {
                $value = htmlspecialchars(trim($_POST[$postKey]));
                $updates[] = "$dbCol = :$dbCol";
                $params[$dbCol] = $value;
                $_SESSION[$dbCol] = $value;
            }
        }

        // ===== CHAMPS VERROUILLÉS (1x modifiables, sauf ADMIN) =====
        // Les colonnes individuelles pour tracker chaque modification
        $champsVerrouilles = [
            'nom'                 => ['colonne' => 'nom', 'flag' => 'nom_modifie'],
            'prenom'              => ['colonne' => 'prenom', 'flag' => 'prenom_modifie'],
            'pseudo'              => ['colonne' => 'pseudo', 'flag' => 'pseudo_modifie'],
            'Date_de_naissance'   => ['colonne' => 'date_naissance', 'flag' => 'date_naiss_modifiee']
        ];

        foreach ($champsVerrouilles as $postKey => $config) {
            $flagValue = (int)($_SESSION[$config['flag']] ?? 0);
            $dbCol = $config['colonne'];
            $flag = $config['flag'];

            // Vérifier si l'utilisateur peut modifier ce champ
            // Si le flag est 1 ET ce n'est pas un admin → pas de modif
            if ($flagValue == 1 && $userRole !== 1) {
                continue; // Skip ce champ
            }

            // Vérifier si le champ a VRAIMENT changé
            if (isset($_POST[$postKey]) && $_POST[$postKey] !== '') {
                $newValue = htmlspecialchars(trim($_POST[$postKey]));
                $oldValue = $currentData[$dbCol] ?? '';

                // SEULEMENT si la valeur a changé
                if ($newValue !== $oldValue) {
                    $updates[] = "$dbCol = :$dbCol";
                    $params[$dbCol] = $newValue;
                    $_SESSION[$dbCol] = $newValue;

                    // Activer le verrou pour ce champ (si c'est pas admin)
                    if ($userRole !== 1) {
                        $updates[] = "$flag = 1";
                        $_SESSION[$flag] = 1;
                    }
                }
            }
        }

        // ===== EXÉCUTION =====
        if ($error === null && !empty($updates)) {
            try {
                $sql = "UPDATE utilisateurs SET " . implode(', ', $updates) . " WHERE utilisateur_id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                header('Location: index.php?page=dashboard&success=1');
                exit();
            } catch (\PDOException $e) {
                $error = "Erreur lors de la mise à jour";
            }
        }
    }

    // ===== PRÉPARATION VUE =====
    $title = "Modifier profil - EcoRide";
    $specific_styles = [];
    $specific_scripts = [];

     require_once ROOT_PATH . '/app/views/layout/header.php';
    require_once ROOT_PATH . '/app/views/auth/update.profile.view.php';
    require_once ROOT_PATH . '/app/views/layout/footer.php';
}
}