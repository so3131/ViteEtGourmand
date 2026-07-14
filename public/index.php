<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ce fichier est le point d'entrée de l'application. Il reçoit toutes les requêtes, gère la session, et redirige vers le bon contrôleur en fonction de la page demandée.

require_once dirname(__DIR__) . '/app/config/constants.php';
require_once ROOT_PATH . '/app/Autoloader.php';
\App\Autoloader::register();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once ROOT_PATH . '/app/config/Database.php';
$db = (new Database())->connect();

// On gère la déconnexion IMMEDIATEMENT avant d'afficher quoi que ce soit ou de charger la sécurité
$page = $_GET['page'] ?? 'home';

if ($page === 'logout') {
    require_once ROOT_PATH . '/app/controllers/authController/logoutController.php';
    \App\Controllers\AuthController\LogoutController::logOut($db);  //Le script s'arrête net ici en cas de deconnexion, pas de risque d'affichage fantôme
}

// 3. SÉCURITÉ GLOBALE

$config = require ROOT_PATH . '/app/config/pages.php';
$pagesPubliques   = $config['publiques'];
$pagesQuiExistent = $config['existante'];
$pagesAdmin       = $config['admin'];
$pagesEmployee    = $config['employee'];
$pagesUser        = $config['user'];
// Maintenant, tu appelles ta fonction avec ces variables
$page = \App\Helpers\SecurityManager::checkAccess(
    $_GET['page'] ?? 'home',
    $_SESSION['role_id'] ?? null,
    $pagesPubliques,
    $pagesQuiExistent,
    $pagesAdmin,
    $pagesEmployee,
    $pagesUser
);
$route = match ($page) {
    // Général
    'home' => ['class' => '\App\Controllers\UserController\HomeController', 'action' => 'home'],
    'login'         => ['class' => '\App\Controllers\AuthController\LoginController', 'action' => 'LogIn'],
    'signin'        => ['class' => '\App\Controllers\AuthController\SigninController', 'action' => 'SignIn'],
    'logout'        => ['class' => '\App\Controllers\AuthController\LogoutController', 'action' => 'logOut'],
    'search'            => ['class' => '\App\Controllers\UserController\MenuController', 'action' => 'searchMenu'],
    'details-menu'      => ['class' => '\App\Controllers\UserController\MenuController', 'action' => 'showMenuDetails'],
    'filter'            => ['class' => '\App\Controllers\UserController\MenuController', 'action' => 'filterJson'],
    'ajax-frais-livraison' => ['class' => '\App\Controllers\UserController\OrderMenuController', 'action' => 'ajaxFraisLivraison'],
    'contact'           => ['class' => '\App\Controllers\UserController\ContactController', 'action' => 'contactUs'],
    'contact-success'   => ['class' => '\App\Controllers\SuccessController', 'action' => 'successMessage'],
    'mention'           => ['class' => '\App\Controllers\UserController\MentionLegalesController', 'action' => 'mentionsLegales'],
    'error-ban'         => ['class' => '\App\Controllers\banViewController', 'action' => 'errorBanMessage'],
    // Authentification
    'forgot-password'   => ['class' => '\App\Controllers\AuthController\ForgotPasswordController', 'action' => 'forgotPassword'],
    'reset-password'    => ['class' => '\App\Controllers\AuthController\ResetPasswordController', 'action' => 'resetPassword'],


    // Utilisateur 
    'dashboard-user'     => ['class' => '\App\Controllers\UserController\DashboardUserController', 'action' => 'userDashboard'],
    'update-profil'     => ['class' => '\App\Controllers\AuthController\updateProfilController', 'action' => 'updateProfil'],
    'erase-order' => ['class' => '\App\Controllers\UserController\EraseOrderController','params' => ['db', 'commande_id'], 'action' => 'eraseOrder'],
    'edit-order' => ['class' => '\App\Controllers\UserController\UpdateOrderController','params' => ['db', 'commande_id'], 'action' => 'editOrderView'],
    'recalculer-prix' => ['class' => '\App\Controllers\UserController\UpdateOrderController', 'action' => 'recalculerPrix'],
    'cancel-edit-order' => ['class' => '\App\Controllers\UserController\UpdateOrderController', 'action' => 'cancelEditOrder'],
    'update-order' => ['class' => '\App\Controllers\UserController\UpdateOrderController', 'params' => ['db', 'commande_id'], 'action' => 'updateOrder'],

    // Tunnel de commande
    'order-menu'    => ['class' => '\App\Controllers\UserController\OrderMenuController',
    'params' => ['db', 'menuID'], 'action' => 'orderMenu'],
    'order-success' => ['class' => '\App\Controllers\UserController\OrderMenuController', 'action' => 'orderSuccess'],
    'cancel-order'  => ['class' => '\App\Controllers\UserController\OrderMenuController', 'action' => 'cancelOrder'],

    // Admin

    'dashboard-admin'    => ['class' => '\App\Controllers\Admin\DashboardAdminController', 'action' => 'adminDashboard'],
    'stats-admin'        => ['class' => '\App\Controllers\Admin\StatsAdminController', 'action' => 'adminStats'],
    'rh-admin'           => ['class' => '\App\Controllers\Admin\RHAdminController', 'action' => 'adminRH'],
    'ban-user-admin'     => ['class' => '\App\Controllers\Admin\BanUserAdminController', 'action' => 'adminUserList'],
    'ban-action-admin'   => ['class' => '\App\Controllers\Admin\BanUserAdminController', 'action' => 'banUser'],
    'unban-action-admin' => ['class' => '\App\Controllers\Admin\BanUserAdminController', 'action' => 'unBanUser'],
    'moderation-admin'   => ['class' => '\App\Controllers\Admin\ModerationAdminController', 'action' => 'adminModeration'],
    'conflict-admin'     => ['class' => '\App\Controllers\Admin\ConflictAdminController', 'action' => 'adminConflicts'],
    'tickets-admin'      => ['class' => '\App\Controllers\Admin\ticketsAdminController', 'action' => 'adminTickets'],
    'delete-tickets-admin' => ['class' => '\App\Controllers\Admin\ticketsAdminController', 'action' => 'deleteTicket'],
    // Employé
    'dashboard-employee' => ['class' => '\App\Controllers\Employee\DashboardEmployeeController', 'action' => 'employeeDashboard'],
    'conflict-employee'  => ['class' => '\App\Controllers\Employee\ConflictEmployeeController', 'action' => 'employeeConflict'],
    'moderation-employee' => ['class' => '\App\Controllers\Employee\ModerationEmployeeController', 'action' => 'employeeModeration'],

    // Default 404
    default         => ['class' => '\App\Controllers\ErrorController', 'action' => 'notFound'],
};
$controllerClass = $route['class'];
$action = $route['action'];

// On vérifie si la classe existe
if (!class_exists($controllerClass)) {
    die("ERREUR : La classe '$controllerClass' est introuvable. Vérifie ton Autoloader et le nom du fichier.");
}

//  On vérifie si la méthode existe
if (!method_exists($controllerClass, $action)) {
    die("ERREUR : La méthode '$action' n'existe pas dans la classe '$controllerClass'.");
}
$params = [$db];

if (isset($route['params']) && is_array($route['params'])) {
    foreach ($route['params'] as $param) {
        if ($param !== 'db') {
            $params[] = $_GET[$param] ?? null;
        }
    }
}

$controllerClass::$action(...$params);
