<?php

namespace App\Managers;

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectId;


class MongoReviewManager {
    private ?Client $mongoClient = null;

    public function __construct()
    {
        try {
            $uri = getenv('MONGODB_URI');
            if (!$uri) {
                throw new \RuntimeException('MONGODB_URI est absente.');
            }
            $this->mongoClient = new Client($uri);
        } catch (\Exception $e) {
            error_log("Erreur de connexion MongoDB (Reviews) : " . $e->getMessage());
        }
    }

    public function insertReview(array $data) 
    {
        if ($this->mongoClient) {
            $database = $this->mongoClient->selectDatabase('vite_gourmand');
            $collection = $database->selectCollection('reviews');
            return $collection->insertOne($data);
        }
        throw new \RuntimeException("Client MongoDB non initialisé.");
    }
    /**
     * Vérifie si un avis existe déjà pour une commande
     */
    public function alreadyReviewedOrder(int $commandeId): bool 
    {
        if ($this->mongoClient === null) {
            return false;
        }

        try {
            $database = $this->mongoClient->selectDatabase('vite_gourmand');
            $collection = $database->selectCollection('reviews');
            
            // On cherche un document avec ce commande_id
            $existingReview = $collection->findOne(['commande_id' => $commandeId]);

            return $existingReview !== null;
        } catch (\Exception $e) {
            error_log("Erreur vérification avis MongoDB : " . $e->getMessage());
            return false;
        }
    }
    /**
     * Récupère tous les avis clients, triés du plus récent au plus ancien
     */
public function getAllReviews(?string $status = null): array
    {
        if ($this->mongoClient === null) {
            return [];
        }

        try {
            $database = $this->mongoClient->selectDatabase('vite_gourmand');
            $collection = $database->selectCollection('reviews');

            // On prépare le filtre de recherche
            $filter = [];
            if (!empty($status)) {
                $filter['status'] = $status;
            }

            // Récupère les documents filtrés (ou tous) avec un tri décroissant sur 'created_at'
            $cursor = $collection->find($filter, [
    'sort' => ['created_at' => -1]
]);

$reviews = [];

foreach ($cursor as $review) {
    $reviews[] = $review;
}

return $reviews;
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération des avis MongoDB : " . $e->getMessage());
            return [];
        }
    }
    /**
     * Met à jour le statut d'un avis (ex: 'approved', 'rejected', etc.)
     */
    public function updateReviewStatus(string $reviewId, string $status): bool
    {
        if ($this->mongoClient === null) {
            return false;
        }

        try {
            $database = $this->mongoClient->selectDatabase('vite_gourmand');
            $collection = $database->selectCollection('reviews');

            $result = $collection->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($reviewId)],
                ['$set' => ['status' => $status]]
            );

            // On accepte si ça a modifié ou si c'était déjà dans le bon état (matched)
            return $result->getMatchedCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur mise à jour statut avis MongoDB : " . $e->getMessage());
            return false;
        }
    }
    /**
     * Récupère un avis unique par son ID MongoDB
     */
    public function getReviewById(string $reviewId)
    {
        if ($this->mongoClient === null) {
            return null;
        }

        try {
            $database = $this->mongoClient->selectDatabase('vite_gourmand');
            $collection = $database->selectCollection('reviews');

            return $collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($reviewId)]);
        } catch (\Exception $e) {
            error_log("Erreur récupération avis par ID MongoDB : " . $e->getMessage());
            return null;
        }
    }
    public function getApprovedReviews(int $limit = 6): array
    {
        if ($this->mongoClient === null) {
            return [];
        }

        try {
            $database = $this->mongoClient->selectDatabase('vite_gourmand');
            $collection = $database->selectCollection('reviews');

            // On cherche uniquement les avis approuvés
            $cursor = $collection->find(
                ['status' => 'approved'], 
                [
                    'sort' => ['created_at' => -1],
                    'limit' => $limit // Limite par exemple aux 6 derniers avis
                ]
            );

            $reviews = [];

            foreach ($cursor as $review) {
                $reviews[] = $review;
            }

            return $reviews;
        } catch (\Exception $e) {
            error_log("Erreur récupération avis approuvés : " . $e->getMessage());
            return [];
        }
    }
}
