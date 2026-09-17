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
            // Fallback local depuis le .env
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $dbname = $_ENV['DB_NAME'] ?? 'test_transit_ecf';
            $username = $_ENV['DB_USER'] ?? 'vg_creator';
            $password = $_ENV['DB_PASS'] ?? 'test_transit_ecf';
            $port = $_ENV['DB_PORT'] ?? '3306';
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
