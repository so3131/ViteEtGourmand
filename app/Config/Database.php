<?php
// Classe pour gérer la connexion à la base de données
class Database
{
    public function connect()
    {
        //  Vérifier si une URL distante est fournie 
        $url = getenv('JAWSDB_URL') ?: $_ENV['JAWSDB_URL'] ?? null;

        if ($url) {
            $parsed = parse_url($url);
            $host = $parsed['host'] ?? 'localhost';
            $port = $parsed['port'] ?? '3306';
            $username = urldecode($parsed['user'] ?? '');
            $password = urldecode($parsed['pass'] ?? '');
            $dbname = ltrim($parsed['path'] ?? '', '/');
        } else {
            // Fallback local depuis le .env (getenv() en priorité pour Docker, $_ENV en repli pour env.php custom)
            $host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? null);
            $dbname = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? null);
            $username = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? null);
            $password = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? null);
            $port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3306');
        }

        try {
            $pdo = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            error_log("Erreur connexion DB : " . $e->getMessage());
            die("Une erreur est survenue lors de la connexion à la base de données.");
        }
    }
}
