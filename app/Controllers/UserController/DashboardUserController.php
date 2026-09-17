<?php

namespace App\Controllers\UserController;

require_once dirname(__DIR__, 2) . '/Config/Constants.php';
require_once ROOT_PATH . '/app/Helpers/DashboardDisplay.php';

use App\Controllers\AuthController\Auth;
use App\Managers\OrderManager;

// class DashboardUserController pour gérer le tableau de bord de l'utilisateur
class DashboardUserController
{
    //function pour afficher le tableau de bord de l'utilisateur avec ses commandes
    public static function userDashboard(\PDO $db)
    {
        Auth::check([ROLE_USER]);
        $userId = $_SESSION['user_id'];

        $ordersData = OrderManager::getOrdersByUser($db, $userId);

        $viewData = [
            'ordersData' => $ordersData,
            'db' => $db,
            'title' => "Mon Tableau de bord - Vite et Gourmand"
        ];
        extract($viewData);

        $specific_styles = ["assets/css/styleGestion.css"];
        $specific_scripts = ["assets/javascript/DashboardUser.js"];

        require_once ROOT_PATH . '/app/Views/layout/header.php';
        require_once ROOT_PATH . '/app/Views/user/dashboardUser.view.php';
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
    }
}
