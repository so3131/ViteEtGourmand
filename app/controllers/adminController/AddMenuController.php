<?php
namespace App\Controllers\AdminController;
require_once dirname(__DIR__, 2) . '/config/constants.php';

// Sécurité : Si pas connecté, interdiction d'être ici

use App\Controllers\AuthController\Auth;

/**
 * Gère l'ajout d'un nouveau véhicule dans le garage de l'utilisateur
 */
function addMenu(\PDO $db) // 💡 Un seul paramètre ($db), exactement comme ton routeur l'envoie !
{
    $pdo = $db;
    // On récupère l'ID directement depuis la session 
    $id = (int)$_SESSION['user_id'];

    try {
Auth::check([ROLE_ADMIN]);        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Récupération et nettoyage des informations du nouveau véhicule
            $brandId   = (int)$_POST['new_car_brand'];
            $model     = trim($_POST['new_car_model']);
            $plate     = trim($_POST['new_car_plate']);
            $energy    = trim($_POST['new_car_energy']);
            $color     = trim($_POST['new_car_color']);
            $dateImmat = trim($_POST['new_car_first_registration_date']);

            // 2. Calcul automatique du statut éco
            $isEco = ($energy === 'electrique' || $energy === 'hybride') ? 1 : 0;

            // 3. Requête d'insertion de la nouvelle voiture
            $sqlNewCar = "INSERT INTO voiture (marque_id, proprietaire_id, modele, immatriculation, energie, is_eco, couleur, date_premiere_immatriculation) 
                          VALUES (:marque_id, :proprietaire_id, :modele, :immatriculation, :energie, :is_eco, :couleur, :date_premiere_immatriculation)";

            $stmtNewCar = $pdo->prepare($sqlNewCar);

            $stmtNewCar->execute([
                'marque_id'                     => $brandId,
                'proprietaire_id'               => $id,
                'modele'                        => $model,
                'immatriculation'               => $plate,
                'energie'                       => $energy,
                'is_eco'                        => $isEco,
                'couleur'                       => $color,
                'date_premiere_immatriculation' => $dateImmat
            ]);

            // Récupérer l'ID généré
            $newVehicleId = $pdo->lastInsertId();

            // Vérifier si c'est une requête AJAX
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'vehicle' => [
                        'id' => $newVehicleId,
                        'modele' => htmlspecialchars($model),
                        'immatriculation' => htmlspecialchars($plate)
                    ]
                ]);
                exit();
            }

            // Fallback classique (sans JS)
            header('Location: index.php?page=dashboard-user&success=car_added');
            exit();
        }
    } catch (\PDOException $e) {
        die("Erreur lors de l'enregistrement du véhicule : " . $e->getMessage());
    }
}
