<?php

namespace App\Managers;

use App\Models\Order;
use App\Managers\LieuManager;




class OrderManager
{
    public static function createOrderFromData(\PDO $db, object $user, array $menuData, array $prestation, float $totalCommande)

    {
        try {
            // 1. On ouvre la transaction
            $db->beginTransaction();

            // 2. On crée l'objet
            $order = new Order(
                commande_id: null,
                numero_commande: uniqid('CMD_'),
                date_commande: date('Y-m-d'),
                date_prestation: $prestation['date_prestation'],
                heure_livraison: $prestation['heure_livraison'] ?? '12:00',
                prix_menu: (float)$menuData['prix_menu_total'],
                nombre_personne: (int)$menuData['quantite'],
                prix_livraison: (float)$prestation['frais_livraison'],
                prix_total: (float)$totalCommande,
                statut: 'en_attente',
                pret_materiel: isset($prestation['location_materiel']) && $prestation['location_materiel'] ? '1' : '0',
                restitution_materiel: '0',
                depot_garantie: (float)$prestation['depot_garantie'],
                utilisateur_id: (int)$user->user_id,
                menu_id: (int)$menuData['menu_id'],
                lieu_prestation_id: (int)$prestation['lieu']['id']

            );

            // 3. On exécute l'insertion à l'intérieur de la transaction
            $result = self::create($db, $order);

            // 2. MISE À JOUR DU STOCK
            // On réduit la quantité disponible du menu commandé
            $sqlStock = "UPDATE vg_menu SET quantite_restante = quantite_restante - :quantite WHERE menu_id = :menu_id AND quantite_restante >= :quantite";
            $stmtStock = $db->prepare($sqlStock);
            $stmtStock->execute([
                'quantite' => $menuData['quantite'],
                'menu_id'  => $menuData['menu_id']
            ]);

            // Vérifier si la ligne a bien été mise à jour (si rowCount est 0, stock insuffisant)
            if ($stmtStock->rowCount() === 0) {
                throw new \Exception("Stock insuffisant pour ce menu.");
            }

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            // Loggez l'erreur réelle
            error_log("Erreur lors de la commande : " . $e->getMessage());

            // Relancez l'exception pour que le contrôleur puisse afficher le message à l'utilisateur
            throw $e;
        }
    }
    public static function create(\PDO $db, Order $order)
    {
        // On ne liste QUE les colonnes présentes dans ta table vg_commande
        $sql = "INSERT INTO vg_commande (
        numero_commande, date_commande, date_prestation, heure_livraison, 
        prix_menu, nombre_personne, prix_livraison, prix_total, statut, 
        pret_materiel, restitution_materiel,depot_garantie, utilisateur_id, menu_id,lieu_prestation_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
     
        return $stmt->execute([
            $order->numero_commande,
            $order->date_commande,
            $order->date_prestation,
            $order->heure_livraison,
            $order->prix_menu,
            $order->nombre_personne,
            $order->prix_livraison,
            $order->prix_total,
            $order->statut,
            (int)$order->pret_materiel,      // Conversion forcée en int (0 ou 1)
            (int)$order->restitution_materiel, // Conversion forcée en int (0 ou 1)
            (float)$order->depot_garantie,
            (int)$order->utilisateur_id,
            (int)$order->menu_id,
            (int)$order->lieu_prestation_id
        ]);
    }

public static function getOrdersByUser(\PDO $db, int $userId)
{
    $sql = "SELECT c.*, 
                   m.titre AS menu_titre, 
                   l.adresse, l.code_postal, l.ville 
            FROM vg_commande c
            LEFT JOIN vg_menu m ON c.menu_id = m.menu_id
            LEFT JOIN vg_lieu_prestation l ON c.lieu_prestation_id = l.id
            WHERE c.utilisateur_id = :user_id 
            ORDER BY c.date_prestation DESC";
            
    $stmt = $db->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $allOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Le reste de ta fonction reste identique...
    $orders = [
        'en_attente' => [], 'acceptee' => [], 'en_preparation' => [],
        'en_cours_livraison' => [], 'livree' => [], 
        'en_attente_retour_materiel' => [], 'terminee' => [], 'annulee' => []
    ];

    foreach ($allOrders as $order) {
        if (isset($orders[$order['statut']])) {
            $orders[$order['statut']][] = $order;
        }
    }
    return $orders;
}
    public static function EstimerFraisLivraison(\PDO $db, int $lieu_id): float
    {
        // 1. Récupération des données du lieu
        $lieu = LieuManager::getById($db, $lieu_id);

        // 2. Appel de ton modèle Order pour le calcul
        return Order::calculerFraisLivraison($lieu['ville'], $lieu['distance_bordeaux']);
    }


 
public static function getAllOrders(\PDO $db, $filters = []) {
    // 1. Initialisation de la requête de base avec les JOIN
    $sql = "SELECT c.*, u.nom as client_nom, m.titre as menu_titre, 
                   l.adresse as adresse_prestation, l.ville as ville_prestation, DATE_ADD(date_prestation, INTERVAL 10 DAY) AS date_limite_restitution
            FROM vg_commande c 
            JOIN vg_utilisateur u ON c.utilisateur_id = u.utilisateur_id 
            JOIN vg_menu m ON c.menu_id = m.menu_id 
            JOIN vg_lieu_prestation l ON c.lieu_prestation_id = l.id 
            WHERE 1=1";

    $params = [];

    // 2. Ajout dynamique des filtres
    if (!empty($filters['client_nom'])) {
        $sql .= " AND u.nom LIKE :client_nom";
        $params[':client_nom'] = '%' . $filters['client_nom'] . '%';
    }

    if (!empty($filters['status'])) {
        $sql .= " AND c.statut = :status";
        $params[':status'] = $filters['status'];
    }

    // 3. Tri
    $sql .= " ORDER BY c.date_commande DESC";

    // 4. Préparation et exécution
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
    }

