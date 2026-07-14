<?php

namespace App\Models;

class User
{
    // la fonction SQL qui récupère tout
    public static function findAll(\PDO $pdo)
    {
        // requête avec inner join pour récupérer tous les utilisateurs en fonction de deux tables utilisateur et role, triés du plus récent au plus ancien

        $stmt = $pdo->query("SELECT u.*, r.libelle AS role_nom 
                FROM utilisateurs u
                INNER JOIN role r ON u.role_id = r.role_id
                ORDER BY utilisateur_id DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function ban(\PDO $pdo, int $id)
    {
        try {
            // On récupère l'id de la voiture possédée par l'utilisateur
            $sqlVoiture = "SELECT voiture_id FROM voiture WHERE proprietaire_id = :id";
            $stmtVoiture = $pdo->prepare($sqlVoiture);
            $stmtVoiture->execute(['id' => $id]);
            $voiture = $stmtVoiture->fetch(\PDO::FETCH_ASSOC);

            // Si l'utilisateur a une voiture, la condition supprime ses covoiturages
            if ($voiture && !empty($voiture['voiture_id'])) {
                $sqlCovoiturage = "UPDATE covoiturage 
                               SET statut = 'annule' 
                               WHERE voiture_id = :voiture_id AND statut = 'publie'";
                $stmtCovoiturage = $pdo->prepare($sqlCovoiturage);
                $stmtCovoiturage->execute(['voiture_id' => $voiture['voiture_id']]);
            }

            // On désactive l'utilisateur

            $sqlUser = "UPDATE utilisateurs SET est_actif = 0 WHERE utilisateur_id = :id";

            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute(['id' => $id]);

            return true;
        } catch (\PDOException $e) {
            die("Erreur lors de la suppression des covoiturages en cascade : " . $e->getMessage());
        }
    }

    public static function unBan(\PDO $pdo, int $id)
    {
        try {


            // On reactive l'utilisateur

            $sqlUser = "UPDATE utilisateurs SET est_actif = 1 WHERE utilisateur_id = :id";

            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute(['id' => $id]);

            return true;
        } catch (\PDOException $e) {
            die("Erreur lors du debannissement de l'utilisateur : " . $e->getMessage());
        }
    }

    public static function search(\PDO $pdo, string $searchTerm, ?int $roleId = null)
    {
        $sql = "SELECT u.*, r.libelle AS role_nom 
            FROM utilisateurs u
            INNER JOIN role r ON u.role_id = r.role_id
            WHERE 1=1"; // 1=1 est une astuce pour faciliter l'ajout de conditions dynamiques

        $params = [];

        // 1. Si on a un terme de recherche en texte
        if (!empty($searchTerm)) {
            $sql .= " AND (u.nom LIKE :term OR u.prenom LIKE :term OR u.email LIKE :term OR u.pseudo LIKE :term)";
            $params['term'] = "%$searchTerm%";
        }

        // 2. Si on a sélectionné un rôle spécifique
        if (!empty($roleId)) {
            $sql .= " AND u.role_id = :role_id";
            $params['role_id'] = (int)$roleId;
        }

        // 3. On met l'ORDER BY UNE SEULE FOIS, tout à la fin, après tous les "AND"
        $sql .= " ORDER BY u.utilisateur_id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

// ajouter la recup des pref pour les afficher dans le dashboard