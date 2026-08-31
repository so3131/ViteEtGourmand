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
//function pour insérer un nouvel avis dans la collection MongoDB
    public function insertReview(array $data) 
    {
        if ($this->mongoClient) {
            $database = $this->mongoClient->selectDatabase('vite_gourmand');
            $collection = $database->selectCollection('reviews');
            return $collection->insertOne($data);
        }
        throw new \RuntimeException("Client MongoDB non initialisé.");
    }
//function pour vérifier si une commande a déjà un avis associé
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
//function pour récupérer tous les avis, avec un filtre optionnel sur le statut
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
    //function pour mettre à jour le statut d'un avis par son ID MongoDB et enregistrer la traçabilité si un employé est connecté
public function updateReviewStatus(string $reviewId, string $status, ?int $userId = null, ?string $userName = null): bool
{
    if ($this->mongoClient === null) {
        return false;
    }

    try {
        $database = $this->mongoClient->selectDatabase('vite_gourmand');
        $collection = $database->selectCollection('reviews');

        $updateData = [
            'status' => $status
        ];

        // Ajout de la traçabilité si les infos sont fournies
        if ($userId !== null) {
            $updateData['validated_by'] = $userId;
            $updateData['validated_by_name'] = $userName;
            $updateData['validated_at'] = new UTCDateTime();
        }

        $result = $collection->updateOne(
            ['_id' => new ObjectId($reviewId)],
            ['$set' => $updateData]
        );

        return $result->getMatchedCount() > 0;
    } catch (\Exception $e) {
        error_log("Erreur mise à jour statut avis MongoDB : " . $e->getMessage());
        return false;
    }
}
//function pour récupérer un avis par son ID MongoDB
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
//function pour récupérer les derniers avis approuvés, avec une limite sur le nombre d'avis retournés
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
