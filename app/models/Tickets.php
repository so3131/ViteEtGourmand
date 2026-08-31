<?php

namespace App\Models;

class Ticket
{
    //function pour récupérer tous les tickets depuis la base de données SQL
    public static function findAll(\PDO $pdo)
    {
        // requête simple pour récupérer tous les tickets, triés du plus récent au plus ancien
        $stmt = $pdo->query("SELECT * FROM tickets ORDER BY id DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    //function pour supprimer un ticket
    public static function delete(\PDO $pdo, int $id)
    {
        // requête préparée pour éviter les injections SQL !
        $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
