<?php
// Classe de gestion de la base de données avec PDO

require_once dirname(__DIR__) . '/config/Constants.php';

class Database
{
    private $pdo;

    public function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    /**
     * Retourne l'instance PDO
     */
    public function connect()
    {
        return $this->pdo;
    }

    /**
     * Ferme la connexion
     */
    public function close()
    {
        $this->pdo = null;
    }
}
