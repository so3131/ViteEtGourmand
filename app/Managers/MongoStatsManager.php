<?php

namespace App\Managers;

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;

class MongoStatsManager {
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
            error_log("Erreur de connexion MongoDB : " . $e->getMessage());
        }
    }
//function pour insérer l'historique d'une commande dans la collection MongoDB
    public function insertOrderHistory(array $orderData): bool {
        if ($this->mongoClient === null) {
            return false;
        }

       try {
        $collection = $this->mongoClient->vite_gourmand->historique_commandes;

        $collection->insertOne([
            'commande_id_sql' => $orderData['id'] ?? null,
            'client' => [
                'nom' => $orderData['client_nom'] ?? 'Inconnu',
                'email' => $orderData['client_email'] ?? 'Inconnu'
            ],
            'articles' => $orderData['items'] ?? [],
            
           
            'montant_details' => $orderData['montant_details'] ?? [],
            
            
            'ca_reel_entreprise' => $orderData['ca_reel_entreprise'] ?? 0.00,
            'montant_total_paye_par_client' => $orderData['montant_total_paye_par_client'] ?? 0.00,
            
            'date_creation' => new UTCDateTime()
        ]);

        return true;
    } catch (\Exception $e) {
        error_log("Erreur insertion MongoDB : " . $e->getMessage());
        return false;
    }
}
//function pour avoir le nombre total de commandes dans MongoDB
    
public function getTotalOrdersFromMongoDB(): int {
    if ($this->mongoClient === null) return 0;

    try {
        $collection = $this->mongoClient->vite_gourmand->historique_commandes;
        
        // Compte tous les documents de la collection
        return $collection->countDocuments([]);

    } catch (\Exception $e) {
        error_log("Erreur comptage commandes MongoDB : " . $e->getMessage());
        return 0;
    }
}
//function pour récupérer les statistiques par menu depuis MongoDB, avec des filtres optionnels sur la période et le menu
public function getStatsByMenuFromMongoDB($dateDebut = null, $dateFin = null, $menuId = null): array {
    if ($this->mongoClient === null) return [];

    try {
        $collection = $this->mongoClient->vite_gourmand->historique_commandes;
        
        $matchStage = [];

        // Filtre par période sur 'date_creation'
        if (!empty($dateDebut) || !empty($dateFin)) {
            $dateFilter = [];
            if (!empty($dateDebut)) {
                $dateFilter['$gte'] = new UTCDateTime(strtotime($dateDebut . ' 00:00:00') * 1000);
            }
            if (!empty($dateFin)) {
                $dateFilter['$lte'] = new UTCDateTime(strtotime($dateFin . ' 23:59:59') * 1000);
            }
            $matchStage['date_creation'] = $dateFilter;
        }

        // Filtre par menu (si stocké dans le tableau 'articles')
        if (!empty($menuId)) {
            // MongoDB va chercher si un des articles correspond au menu_id (ou au titre selon ton stockage)
            // Si tu stockes l'id du menu dans tes articles :
            $matchStage['articles.menu_id'] = (int)$menuId; 
        }

        $pipeline = [];

        // On applique le filtre global s'il y en a un
        if (!empty($matchStage)) {
            $pipeline[] = ['$match' => $matchStage];
        }

        // Pipeline d'agrégation habituel
        $pipeline[] = ['$unwind' => '$articles'];

        // Si tu filtres par menu_id via l'article, on peut affiner ici si besoin
        if (!empty($menuId)) {
            $pipeline[] = ['$match' => ['articles.menu_id' => (int)$menuId]];
        }

        $pipeline[] = [
            '$group' => [
                '_id' => '$articles.titre',
                'titreMenu' => ['$first' => '$articles.titre'],
                'nombreCommandes' => ['$sum' => 1],
                'quantiteTotaleVendue' => ['$sum' => '$articles.quantite'],
                'caTotalMenu' => ['$sum' => '$articles.sous_total']
            ]
        ];

        $result = $collection->aggregate($pipeline);
        return iterator_to_array($result);

    } catch (\Exception $e) {
        error_log("Erreur agrégation MongoDB filtrée : " . $e->getMessage());
        return [];
    }
}

//function pour récupérer le chiffre d'affaires total depuis MongoDB
    public function getSalesFromMongoDB(): float {
        if ($this->mongoClient === null) return 0.0;

        try {
            $collection = $this->mongoClient->vite_gourmand->historique_commandes;

            $pipeline = [
                [
                    '$group' => [
                        '_id' => null,
                        'totalCA' => ['$sum' => '$ca_reel_entreprise']
                    ]
                ]
            ];

            $result = iterator_to_array($collection->aggregate($pipeline));
            return $result[0]['totalCA'] ?? 0.0;

        } catch (\Exception $e) {
            error_log("Erreur calcul CA global : " . $e->getMessage());
            return 0.0;
        }
    }
//function pour récupérer le chiffre d'affaires et le nombre de commandes filtrés depuis MongoDB
   public function getSalesFilteredFromMongoDB($dateDebut = null, $dateFin = null, $menuId = null): float {
    if ($this->mongoClient === null) return 0.0;

    try {
        $collection = $this->mongoClient->vite_gourmand->historique_commandes;
        
        $match = [];
        
        // 1. Filtre par période
        if (!empty($dateDebut) || !empty($dateFin)) {
            $dateFilter = [];
            if (!empty($dateDebut)) {
                $dateFilter['$gte'] = new UTCDateTime(strtotime($dateDebut . ' 00:00:00') * 1000);
            }
            if (!empty($dateFin)) {
                $dateFilter['$lte'] = new UTCDateTime(strtotime($dateFin . ' 23:59:59') * 1000);
            }
            $match['date_creation'] = $dateFilter;
        }

        $pipeline = [];
        if (!empty($match)) {
            $pipeline[] = ['$match' => $match];
        }

        // 2. Si on filtre par menu, on filtre directement sur le tableau des articles de la commande
        if (!empty($menuId)) {
            $pipeline[] = [
                '$match' => [
                    'articles' => [
                        '$elemMatch' => [
                            '$or' => [
                                ['menu_id' => (int)$menuId],
                                ['id' => (int)$menuId]
                            ]
                        ]
                    ]
                ]
            ];
        }

        // 3. On somme toujours le 'ca_reel_entreprise' de la commande (excluant la caution)
        // De cette façon, la somme des menus correspondra exactement à la logique globale.
        $pipeline[] = [
            '$group' => [
                '_id' => null,
                'totalCA' => ['$sum' => '$ca_reel_entreprise']
            ]
        ];

        $result = iterator_to_array($collection->aggregate($pipeline));
        return $result[0]['totalCA'] ?? 0.0;

    } catch (\Exception $e) {
        error_log("Erreur calcul CA filtré MongoDB : " . $e->getMessage());
        return 0.0;
    }
}
    //function pour récupérer les ventes et le nombre de commandes d'aujourd'hui depuis MongoDB
    public function getTodaySalesAndOrdersFromMongoDB(): array {
    if ($this->mongoClient === null) return ['orders' => 0, 'sales' => 0.0];

    try {
        $collection = $this->mongoClient->vite_gourmand->historique_commandes;

        // Début et fin de la journée d'aujourd'hui
        $startOfDay = new UTCDateTime(strtotime('today 00:00:00') * 1000);
        $endOfDay = new UTCDateTime(strtotime('tomorrow 00:00:00') * 1000);

        $pipeline = [
            [
                '$match' => [
                    'date_creation' => ['$gte' => $startOfDay, '$lt' => $endOfDay]
                ]
            ],
            [
                '$group' => [
                    '_id' => null,
                    'totalOrders' => ['$sum' => 1],
                    'totalSales' => ['$sum' => '$ca_reel_entreprise']
                ]
            ]
        ];

        $result = iterator_to_array($collection->aggregate($pipeline));

        if (!empty($result)) {
            return [
                'orders' => $result[0]['totalOrders'] ?? 0,
                'sales' => $result[0]['totalSales'] ?? 0.0
            ];
        }

        return ['orders' => 0, 'sales' => 0.0];
    } catch (\Exception $e) {
        error_log("Erreur stats du jour MongoDB : " . $e->getMessage());
        return ['orders' => 0, 'sales' => 0.0];
    }
}
//function pour récupérer un résumé des statistiques globales depuis MongoDB
    public function getSummaryStats(): array {
        return [
            'totalOrders' => $this->getTotalOrdersFromMongoDB(),
            'totalSales' => $this->getSalesFromMongoDB(),
            'todayStats' => $this->getTodaySalesAndOrdersFromMongoDB(),
            'statsByMenu' => $this->getStatsByMenuFromMongoDB()
        ];
    }
    //function pour récupérer le chiffre d'affaires et le nombre de commandes filtrés depuis MongoDB, avec des filtres optionnels sur la période et le menu
    public function getCountFilteredFromMongoDB($dateDebut = null, $dateFin = null, $menuId = null): int {
    if ($this->mongoClient === null) return 0;

    try {
        $collection = $this->mongoClient->vite_gourmand->historique_commandes;
        
        $match = [];
        
        if (!empty($dateDebut) || !empty($dateFin)) {
            $dateFilter = [];
            if (!empty($dateDebut)) {
                $dateFilter['$gte'] = new UTCDateTime(strtotime($dateDebut . ' 00:00:00') * 1000);
            }
            if (!empty($dateFin)) {
                $dateFilter['$lte'] = new UTCDateTime(strtotime($dateFin . ' 23:59:59') * 1000);
            }
            $match['date_creation'] = $dateFilter;
        }

        if (!empty($menuId)) {
            $match['articles'] = [
                '$elemMatch' => [
                    '$or' => [
                        ['menu_id' => (int)$menuId],
                        ['id' => (int)$menuId]
                    ]
                ]
            ];
        }

        return $collection->countDocuments($match);

    } catch (\Exception $e) {
        error_log("Erreur comptage commandes filtrées MongoDB : " . $e->getMessage());
        return 0;
    }
}
//function pour recuperer le nombre total de themes et de régimes depuis MongoDB
public function getTotalThemesFromMongoDB(): int {
    if ($this->mongoClient === null) return 0;
    try {
        $collection = $this->mongoClient->vite_gourmand->themes; // ou le nom exact de ta collection MongoDB
        return $collection->countDocuments([]);
    } catch (\Exception $e) {
        return 0;
    }
}

// Compter le nombre de régimes dans MongoDB
public function getTotalRegimesFromMongoDB(): int {
    if ($this->mongoClient === null) return 0;
    try {
        $collection = $this->mongoClient->vite_gourmand->regimes; // ou le nom exact de ta collection MongoDB
        return $collection->countDocuments([]);
    } catch (\Exception $e) {
        return 0;
    }
}
//function pour récupérer le chiffre d'affaires et le nombre de commandes filtrés depuis MongoDB, avec des filtres optionnels sur la période
public function getSalesAndOrdersByPeriodFromMongoDB($dateDebut = null, $dateFin = null): array {
    if ($this->mongoClient === null) return ['totalCA' => 0.0, 'totalOrders' => 0];

    try {
        $collection = $this->mongoClient->vite_gourmand->historique_commandes;
        
        $match = [];
        if (!empty($dateDebut) || !empty($dateFin)) {
            $dateFilter = [];
            if (!empty($dateDebut)) {
                $dateFilter['$gte'] = new UTCDateTime(strtotime($dateDebut . ' 00:00:00') * 1000);
            }
            if (!empty($dateFin)) {
                $dateFilter['$lte'] = new UTCDateTime(strtotime($dateFin . ' 23:59:59') * 1000);
            }
            $match['date_creation'] = $dateFilter;
        }

        $pipeline = [];
        if (!empty($match)) {
            $pipeline[] = ['$match' => $match];
        }

        $pipeline[] = [
            '$group' => [
                '_id' => null,
                'totalCA' => ['$sum' => '$ca_reel_entreprise'],
                'totalOrders' => ['$sum' => 1]
            ]
        ];

        $result = iterator_to_array($collection->aggregate($pipeline));
        
        if (!empty($result)) {
            return [
                'totalCA' => $result[0]['totalCA'] ?? 0.0,
                'totalOrders' => $result[0]['totalOrders'] ?? 0
            ];
        }

        return ['totalCA' => 0.0, 'totalOrders' => 0];

    } catch (\Exception $e) {
        return ['totalCA' => 0.0, 'totalOrders' => 0];
    }
}
}