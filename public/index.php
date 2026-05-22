<?php
// Point d'entrée principal de l'application
// Gère le routage et la sécurité

require_once dirname(__DIR__) . '/app/config/Constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once ROOT_PATH . '/app/config/Database.php';
$db = (new Database())->connect();

// Récupérer la page demandée
$page = $_GET['page'] ?? 'home';

// Routage simple
$route = match ($page) {
    'home' => [
        'controller' => ROOT_PATH . '/app/controllers/userController/HomeController.php',
        'action' => 'home'
    ],
    default => [
        'controller' => ROOT_PATH . '/app/controllers/errorController.php',
        'action' => 'notFound'
    ]
};

// Charger et exécuter le contrôleur
if (file_exists($route['controller'])) {
    require_once $route['controller'];
    
    $controllerClass = basename($route['controller'], '.php');
    $action = $route['action'];
    
    if (class_exists($controllerClass) && method_exists($controllerClass, $action)) {
        $controllerClass::$action($db);
    }
} else {
    echo "Erreur 404: Page non trouvée";
}
