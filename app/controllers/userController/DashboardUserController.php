<?php
namespace App\Controllers\UserController;
require_once dirname(__DIR__, 2) . '/config/constants.php';
require_once ROOT_PATH . '/app/helpers/DashboardDisplay.php';
use App\Controllers\AuthController\Auth;
use App\Managers\OrderManager;

// On importe la classe Auth pour pouvoir utiliser la méthode check() pour sécuriser l'accès à la page


// class qu'on appelle pour afficher la page depuis l'index.php
class DashboardUserController 
{  
    //function pour afficher le tableau de bord de l'utilisateur avec ses commandes
public static function userDashboard(\PDO $db)
{
    // 1. Sécurité
    Auth::check([ROLE_USER]);
    $userId = $_SESSION['user_id'];

    // 2. Récupération des données (Logique métier)
    $ordersData = OrderManager::getOrdersByUser($db, $userId);
    
    $viewData = [
        'ordersData' => $ordersData,
        'db' => $db, // On passe $db à la vue pour renderOrderRow
        'title' => "Mon Tableau de bord - Vite et Gourmand"
    ];
    extract($viewData);

    // 3. Préparation des assets (Configuration vue)
    $specific_styles = ["assets/css/styleGestion.css", "assets/css/MQGestion.css"];
    $specific_scripts = ["assets/javascript/DashboardUser.js"];

    // 4. Affichage
    require_once ROOT_PATH . '/app/views/layout/header.php';
    require_once ROOT_PATH . '/app/views/user/dashboardUser.view.php';
    require_once ROOT_PATH . '/app/views/layout/footer.php';
}
}