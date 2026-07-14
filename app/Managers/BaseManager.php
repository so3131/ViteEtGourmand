<?php

namespace App\Managers;

abstract class BaseManager
{
    /**
     * Exécute une requête SQL sécurisée
     * @param \PDO $db Connexion PDO
     * @param string $sql Requête SQL avec placeholders :param
     * @param array $params Paramètres 
     * @return array Résultats ou array vide en cas d'erreur
     */
    protected static function executeQuery(\PDO $db, string $sql, array $params = []): array
    {
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur DB: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère une seule ligne
     */
    protected static function fetchOne(\PDO $db, string $sql, array $params = []): ?array
    {
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("Erreur DB: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Exécute une requête INSERT/UPDATE/DELETE
     */
    protected static function execute(\PDO $db, string $sql, array $params = []): bool
    {
        try {
            $stmt = $db->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            error_log("Erreur DB: " . $e->getMessage());
            return false;
        }
    }
}