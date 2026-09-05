<?php

namespace App\Managers;

use App\Models\Order;
// class OrderManager pour gérer les commandes dans la base de données
class OrderManager
{
    //function pour creer une commande à partir des données fournies, en gérant la transaction et la mise à jour du stock
    public static function createOrderFromData(\PDO $db, object $user, array $menuData, array $prestation, float $totalCommande)

    {
        try {

            $db->beginTransaction();
            $lieuPrestationId = LieuManager::getOrInsert(
                $db,
                $prestation['adresse_livraison'] ?? '',
                $prestation['code_postal'] ?? '',
                $prestation['ville'] ?? '',
                $prestation['lat'] ?? null,
                $prestation['lon'] ?? null
            );

            $order = new Order(
                commande_id: null,
                numero_commande: 'CMD-' . date('Ymd') . '-' . rand(1000, 9999),
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
                lieu_prestation_id: (int)$lieuPrestationId

            );


            $result = self::create($db, $order);

            // MISE À JOUR DU STOCK

            $sqlStock = "UPDATE vg_menu SET quantite_restante = quantite_restante - :quantite WHERE menu_id = :menu_id AND quantite_restante >= :quantite";
            $stmtStock = $db->prepare($sqlStock);
            $stmtStock->execute([
                'quantite' => $menuData['quantite'],
                'menu_id'  => $menuData['menu_id']
            ]);


            if ($stmtStock->rowCount() === 0) {
                throw new \Exception("Stock insuffisant pour ce menu.");
            }

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();

            error_log("Erreur lors de la commande : " . $e->getMessage());

            throw $e;
        }
    }

    //function pour créer une commande dans la base de données à partir d'un objet Order
    public static function create(\PDO $db, Order $order)
    {
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
            (int)$order->pret_materiel,
            (int)$order->restitution_materiel,
            (float)$order->depot_garantie,
            (int)$order->utilisateur_id,
            (int)$order->menu_id,
            (int)$order->lieu_prestation_id
        ]);
    }

    //function pour récupérer toutes les commandes d'un utilisateur
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

        $orders = [
            'en_attente' => [],
            'acceptee' => [],
            'en_preparation' => [],
            'en_cours_livraison' => [],
            'livree' => [],
            'en_attente_retour_materiel' => [],
            'terminee' => [],
            'annulee' => []
        ];

        foreach ($allOrders as $order) {
            if (isset($orders[$order['statut']])) {
                $orders[$order['statut']][] = $order;
            }
        }
        return $orders;
    }


    //function pour récupérer toutes les commandes avec des filtres optionnels pour le dashboard staff
    public static function getAllOrders(\PDO $db, $filters = [])
    {

        $sql = "SELECT c.*, u.nom as client_nom, u.email as client_email, m.titre as menu_titre, 
                    l.adresse as adresse_prestation, l.ville as ville_prestation, DATE_ADD(date_prestation, INTERVAL 10 DAY) AS date_limite_restitution
            FROM vg_commande c 
            JOIN vg_utilisateur u ON c.utilisateur_id = u.utilisateur_id 
            JOIN vg_menu m ON c.menu_id = m.menu_id 
            JOIN vg_lieu_prestation l ON c.lieu_prestation_id = l.id 
            WHERE 1=1";

        $params = [];

        if (!empty($filters['client_nom'])) {
            $sql .= " AND u.nom LIKE :client_nom";
            $params[':client_nom'] = '%' . $filters['client_nom'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND c.statut = :status";
            $params[':status'] = $filters['status'];
        }

        $sql .= " ORDER BY c.date_commande DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    //function pour récupérer une commande spécifique par son ID, avec les détails du client, du menu et du lieu de prestation
    public static function getOrderById(\PDO $db, int $commandeId)
    {
        $sql = "SELECT c.*, u.nom as client_nom, u.email as client_email, m.titre as menu_titre, 
                       l.adresse as adresse_prestation, l.ville as ville_prestation, 
                       DATE_ADD(date_prestation, INTERVAL 10 DAY) AS date_limite_restitution
                FROM vg_commande c 
                JOIN vg_utilisateur u ON c.utilisateur_id = u.utilisateur_id 
                JOIN vg_menu m ON c.menu_id = m.menu_id 
                JOIN vg_lieu_prestation l ON c.lieu_prestation_id = l.id 
                WHERE c.commande_id = :commande_id";

        $stmt = $db->prepare($sql);
        $stmt->execute([':commande_id' => $commandeId]);

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
    //function pour récupérer l'historique des statuts d'une commande spécifique
    public static function getOrderHistory(\PDO $db, int $commande_id): array
    {
        $stmt = $db->prepare("SELECT * FROM vg_commande_statut_historique WHERE commande_id = ? ORDER BY date_changement ASC");
        $stmt->execute([$commande_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    // function pour vérifier si l'on peut changer de statut sans reculer dans le workflow
    public static function canUpdateStatus(string $oldStatus, string $newStatus): bool
    {
        $workflow = [
            'en_attente' => 1,
            'acceptee' => 2,
            'en_preparation' => 3,
            'en_cours_livraison' => 4,
            'livree' => 5,
            'en_attente_retour_materiel' => 6,
            'terminee' => 7,
            'annulee' => 0
        ];

        if (!isset($workflow[$oldStatus]) || !isset($workflow[$newStatus])) {
            return false;
        }

        if ($oldStatus === 'terminee' || $oldStatus === 'annulee') {
            return false;
        }

        if ($newStatus === 'annulee') {
            return true;
        }

        return $workflow[$newStatus] >= $workflow[$oldStatus];
    }
}
