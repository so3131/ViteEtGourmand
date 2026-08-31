<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ce fichier est le point d'entrée de l'application. Il reçoit toutes les requêtes, gère la session, et redirige vers le bon contrôleur en fonction de la page demandée.
require_once dirname(__DIR__) . '/app/config/env.php';
require_once dirname(__DIR__) . '/app/config/constants.php';
require_once ROOT_PATH . '/app/Autoloader.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/app/Helpers/FormHelper.php';


\App\Autoloader::register();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once ROOT_PATH . '/app/config/Database.php';
$db = (new Database())->connect();

// On gère la déconnexion IMMEDIATEMENT avant d'afficher quoi que ce soit ou de charger la sécurité
$page = $_GET['page'] ?? 'home';

if ($page === 'logout') {
    require_once ROOT_PATH . '/app/controllers/authController/logoutController.php';
    \App\Controllers\AuthController\LogoutController::logOut($db);
}

// 3. SÉCURITÉ GLOBALE

$config = require ROOT_PATH . '/app/config/pages.php';
$pagesPubliques   = $config['publiques'];
$pagesQuiExistent = $config['existante'];
$pagesAdmin       = $config['admin'];
$pagesEmployee    = $config['employee'];
$pagesUser        = $config['user'];
$pagesStaff       = $config['staff'];
// fonction avec ces variables

$page = \App\Helpers\SecurityManager::checkAccess(
    $_GET['page'] ?? 'home',
    $_SESSION['role_id'] ?? null,
    $pagesPubliques,
    $pagesQuiExistent,
    $pagesAdmin,
    $pagesEmployee,
    $pagesUser,
    $pagesStaff
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
    'erase-order' => ['class' => '\App\Controllers\UserController\EraseOrderController', 'params' => ['db', 'commande_id'], 'action' => 'eraseOrder'],
    'edit-order' => ['class' => '\App\Controllers\UserController\UpdateOrderController', 'params' => ['db', 'commande_id'], 'action' => 'editOrderView'],
    'recalculer-prix' => [
        'class' => '\App\Controllers\UserController\UpdateOrderController', 
        'action' => 'recalculerPrix', 
        'params' => ['db', 'commande_id']
    ],
    'cancel-edit-order' => ['class' => '\App\Controllers\UserController\UpdateOrderController', 'action' => 'cancelEditOrder'],
    'update-order' => ['class' => '\App\Controllers\UserController\UpdateOrderController', 'params' => ['db', 'commande_id'], 'action' => 'updateOrder'],

    // Tunnel de commande
    'order-menu'    => [
        'class' => '\App\Controllers\UserController\OrderMenuController',
        'params' => ['db', 'menuID'],
        'action' => 'orderMenu'
    ],
    'order-success' => ['class' => '\App\Controllers\UserController\OrderMenuController', 'action' => 'orderSuccess'],
    'cancel-order'  => ['class' => '\App\Controllers\UserController\OrderMenuController', 'action' => 'cancelOrder'],

    // Avis'
    'review' => [
    'class' => '\App\Controllers\UserController\ReviewController',
    'action' => 'submitReview',
    'params' => ['db']
],
'store-review' => [
    'class' => '\App\Controllers\UserController\ReviewController',
    'action' => 'storeReview',
    'params' => ['db']
],

    // Admin

    'dashboard-admin'    => ['class' => '\App\Controllers\AdminController\DashboardAdminController', 'action' => 'adminDashboard'],
    'stats-admin'        => ['class' => '\App\Controllers\AdminController\StatsAdminController', 'action' => 'adminStats'],
    'rh-admin'           => ['class' => '\App\Controllers\AdminController\RHAdminController', 'action' => 'adminRH'],
    'rh-admin-create' => ['class' => '\App\Controllers\AdminController\RHAdminController', 'action' => 'createEmploye'],
    'rh-admin-delete' => [
        'class' => '\App\Controllers\AdminController\RHAdminController',
        'action' => 'deleteEmploye',
        'params' => ['db', 'id']
    ],
    'rh-admin-toggle' => [
        'class' => '\App\Controllers\AdminController\RHAdminController',
        'action' => 'toggleEmployeStatus',
        'params' => ['db', 'id']
    ],
   'ban-user'           => ['class' => '\App\Controllers\AdminController\RHAdminController', 'action' => 'banUser'],
'unban-user'         => ['class' => '\App\Controllers\AdminController\RHAdminController', 'action' => 'unBanUser'],



    // Employé
    'dashboard-employee' => ['class' => '\App\Controllers\EmployeeController\DashboardEmployeeController', 'action' => 'employeeDashboard'],


    // Admin et Employee
   

    'order-management'     => ['class' => '\App\Controllers\StaffCommon\OrderManagementController', 'action' => 'OrderManagement'],
    'menu-management'      => ['class' => '\App\Controllers\StaffCommon\MenuManagementController', 'action' => 'MenuManagement'],
    'cancel-order-common'   => ['class' => '\App\Controllers\StaffCommon\OrderManagementController', 'action' => 'cancelOrder'],
    'edit-order-common'     => ['class' => '\App\Controllers\StaffCommon\EditOrderController', 'action' => 'renderEditForm'],
    'recalculer-prix-common' => ['class' => '\App\Controllers\StaffCommon\EditOrderController', 'action' => 'recalculerPrix'],
    'update-order-common'   => [
        'class' => '\App\Controllers\StaffCommon\EditOrderController',
        'action' => 'processUpdate',
        'params' => ['db', 'commande_id']
    ],
    'update-order-status' => [
    'class' => '\App\Controllers\StaffCommon\OrderManagementController',
    'action' => 'updateStatus',
    'params' => ['db']
],
 'contact-material-client' => [
    'class' => '\App\Controllers\StaffCommon\OrderManagementController',
    'action' => 'contactMaterialClient',
    'params' => ['db']
],
    'edit-menu' => [
        'class' => '\App\Controllers\StaffCommon\EditMenuController',
        'action' => 'editMenu',
        'params' => ['db', 'menu_id']
    ],
    'update-menu-process'  => ['class' => '\App\Controllers\StaffCommon\EditMenuController', 'action' => 'updateMenu'],
    'add-menu-process' => [
        'class' => '\App\Controllers\StaffCommon\MenuManagementController',
        'action' => 'addMenu'
    ],
    'create-plat-ajax' => [
        'class' => '\App\Controllers\StaffCommon\MenuManagementController',
        'action' => 'createPlatAjax',
        'params' => ['db']
    ],
    'delete-menu' => [
        'class' => '\App\Controllers\StaffCommon\MenuManagementController',
        'action' => 'deleteMenu',
        'params' => ['db', 'menu_id']
    ],
    'activate-menu' => [
        'class' => '\App\Controllers\StaffCommon\MenuManagementController',
        'action' => 'activateMenu',
        'params' => ['db', 'menu_id']
    ],
    'review-management' => [
        'class' => '\App\Controllers\StaffCommon\ReviewManagementController',
        'action' => 'manageReview',
        'params' => ['db']
    ],
    'update-review-status' => [
        'class' => '\App\Controllers\StaffCommon\ReviewManagementController',
        'action' => 'updateReviewStatus',
        'params' => ['db']
    ],

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
