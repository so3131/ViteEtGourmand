<?php
// Constantes de configuration pour l'application
// Définis le chemin racine de l'application
define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/app/Config/env.php';
// definis l'environnement de l'application
define('APP_ENV', getenv('APP_ENV') ?: $_ENV['APP_ENV'] ?? 'production');
define('BASE_URL', APP_ENV === 'local' ? '/Projet_Vite_Gourmand_Finale/public' : '');

// Définis les rôles d'utilisateur
define('ROLE_ADMIN', 1);
define('ROLE_EMPLOYE', 2);
define('ROLE_USER', 3);
define('ROLE_VISITEUR', 0);
// Définir les statuts de commande
define('DEPOT_GARANTIE_MATERIEL', 600.00);
// Définis les coordonnées de l'entreprise
define('COMPANY_LAT', 44.837789);
define('COMPANY_LON', -0.579180);

