<?php
namespace App\Controllers\AdminController;
require_once dirname(__DIR__, 2) . '/config/constants.php';
use App\Controllers\AuthController\Auth;


function deleteMenu(\PDO $db)
{
    // Sécurité d'accès
    Auth::check([ROLE_ADMIN]);

    //  On vérifie si le formulaire POST est soumis ET que l'id n'est PAS vide
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['id'])) {
        $carID = (int)($_GET['id'] ?? 0);

        try {
            $pdo = $db;
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            require_once ROOT_PATH . '/app/models/Car.php';

            // $success = Car::deleteCar($pdo, $carID, $_SESSION['user_id']);
            if ($success) {
                header('Location: index.php?page=dashboard-user&success=car_deleted');
                exit();
            } else {
                // Si false -> La voiture est liée à des trajets
                header('Location: index.php?page=dashboard-user&error=car_has_rides');
                exit();
            }
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }
    header('Location: index.php?page=dashboard-user&error=missing_id');
    exit();
}
