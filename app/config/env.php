<?php
// Chargement des variables d'environnement depuis le fichier .env
// Utiliser la fonction putenv pour définir les variables d'environnement et $_ENV pour les rendre accessibles dans le script.
$envFile = ROOT_PATH . '/.env';


if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }
}
