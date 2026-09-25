<?php
// Code PHP qui lit le fichier .env local et remplit $_ENV — ce n'est pas un fichier de config, c'est un script chargé par l'app a chaque requete
// Utiliser la fonction putenv pour définir les variables d'environnement et $_ENV pour les rendre accessibles dans le script.
$envFile = ROOT_PATH . '/.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Ne définir que si la variable n'existe pas déjà (ex: fournie par Docker)
        if (getenv($name) === false) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}
