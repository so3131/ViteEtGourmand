<?php

class Database
{
    public function connect()
    {
        // Récupération depuis le .env (ou valeurs par défaut)
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname = $_ENV['DB_NAME'] ?? 'test_transit_ecf';
        $username = $_ENV['DB_USER'] ?? 'vg_creator';
        $password = $_ENV['DB_PASS'] ?? 'test_transit_ecf';

        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }
}