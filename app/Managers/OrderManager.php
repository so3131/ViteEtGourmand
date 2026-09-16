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


            self::create($db, $order);
            $commandeId = (int)$db->lastInsertId();
            $sqlHistorique = "INSERT INTO vg_commande_statut_historique (commande_id, statut) VALUES (:commande_id, :statut)";
            $stmtHistorique = $db->prepare($sqlHistorique);
            $stmtHistorique->execute([
                'commande_id' => $commandeId,
                'statut'      => 'en_attente',
            ]);

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
    // Léger : menu_id, utilisateur_id, lieu_prestation_id (recalcul AJAX côté client)
    public static function getOrderLight(\PDO $db, int $commandeId): ?array
    {
        $stmt = $db->prepare("SELECT menu_id, utilisateur_id, lieu_prestation_id, nombre_personne FROM vg_commande WHERE commande_id = ?");

        $stmt->execute([$commandeId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
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
    // Récupère une commande avec tous ses détails (lieu, utilisateur, menu)
    public static function getOrderWithDetails(\PDO $db, int $commandeId): ?array
    {
        $sql = "SELECT c.*, l.adresse, l.ville, l.code_postal, l.latitude, l.longitude,
                   u.prenom AS prenom, u.nom AS nom, u.email AS email,
                   m.titre AS menu_titre
            FROM vg_commande c
            JOIN vg_lieu_prestation l ON c.lieu_prestation_id = l.id
            JOIN vg_utilisateur u ON c.utilisateur_id = u.utilisateur_id
            JOIN vg_menu m ON c.menu_id = m.menu_id
            WHERE c.commande_id = :orderID";
        $stmt = $db->prepare($sql);
        $stmt->execute(['orderID' => $commandeId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $order ?: null;
    }
    // Variante client : ne renvoie la commande QUE si elle appartient à l'utilisateur
    public static function getOrderWithDetailsForUser(\PDO $db, int $commandeId, int $userId): ?array
    {
        $sql = "SELECT c.*, l.adresse, l.ville, l.code_postal, l.latitude, l.longitude,
                       u.prenom AS prenom, u.nom AS nom, u.email AS email,
                       m.titre AS menu_titre
                FROM vg_commande c
                JOIN vg_lieu_prestation l ON c.lieu_prestation_id = l.id
                JOIN vg_utilisateur u ON c.utilisateur_id = u.utilisateur_id
                JOIN vg_menu m ON c.menu_id = m.menu_id
                WHERE c.commande_id = :orderID
                  AND c.utilisateur_id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['orderID' => $commandeId, 'user_id' => $userId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $order ?: null;
    }

    // Récupère les 10 dernières commandes pour le dashboard
    public static function getRecentOrders(\PDO $db, int $limit = 10): array
    {
        $stmt = $db->query("SELECT c.*, u.nom, u.prenom 
                        FROM vg_commande c 
                        JOIN vg_utilisateur u ON c.utilisateur_id = u.utilisateur_id 
                        ORDER BY c.commande_id DESC LIMIT $limit");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Met à jour le statut d'une commande et historise
    public static function updateStatus(\PDO $db, int $commandeId, string $newStatus): bool
    {
        $sql = "UPDATE vg_commande SET statut = ? WHERE commande_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$newStatus, $commandeId]);

        $stmtHistorique = $db->prepare(
            "INSERT INTO vg_commande_statut_historique (commande_id, statut) VALUES (?, ?)"
        );
        $stmtHistorique->execute([$commandeId, $newStatus]);
        return true;
    }

    // Annule une commande (avec restitution du stock)
    public static function cancelOrder(\PDO $db, int $commandeId, string $motif, string $modeContact): ?array
    {
        $db->beginTransaction();
        try {
            // Récupérer la commande
            $orderData = self::getOrderWithDetails($db, $commandeId);
            // ... mais il faut le query avec u.email etc pour l'email
            // Solution : réutiliser getAllOrders ou faire une version "for cancel"

            // Annuler
            $sqlUpdate = "UPDATE vg_commande SET statut = 'annulee', motif_annulation = :motif, 
                      mode_contact = :mode_contact WHERE commande_id = :id AND statut <> 'annulee'";
            $stmtUpdate = $db->prepare($sqlUpdate);
            $stmtUpdate->execute(['motif' => $motif, 'mode_contact' => $modeContact, 'id' => $commandeId]);

            // Restituer le stock
            $sqlRestituer = "UPDATE vg_menu SET quantite_restante = quantite_restante + :quantite WHERE menu_id = :menu_id";
            $stmtRestituer = $db->prepare($sqlRestituer);
            $stmtRestituer->execute(['quantite' => $orderData['nombre_personne'], 'menu_id' => $orderData['menu_id']]);

            $db->commit();
            return $orderData;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    // Met à jour les champs modifiables d'une commande  
    public static function updateOrder(\PDO $db, int $commandeId, array $data, ?int $userId = null): bool
    {
        $sql = "UPDATE vg_commande
                SET date_prestation    = :date,
                    heure_livraison    = :heure,
                    lieu_prestation_id = :lieu_id,
                    prix_total         = :prix,
                    pret_materiel      = :materiel,
                    depot_garantie     = :depot,
                    nombre_personne    = :nombre_personne
                WHERE commande_id = :id";
        $params = [
            'date'            => $data['date_prestation'],
            'heure'           => $data['heure_livraison'],
            'lieu_id'         => (int)$data['lieu_prestation_id'],
            'prix'            => (float)$data['prix_total'],
            'materiel'        => (int)$data['pret_materiel'],
            'depot'           => (float)$data['depot_garantie'],
            'nombre_personne' => (int)$data['nombre_personne'],
            'id'              => $commandeId,
        ];

        if ($userId !== null) {
            $sql .= " AND utilisateur_id = :user_id";
            $params['user_id'] = $userId;
        }

        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    // Récupère uniquement le menu_id d'une commande (léger, pour le recalcul AJAX)
    public static function getMenuIdByCommandeId(\PDO $db, int $commandeId): ?int
    {
        $stmt = $db->prepare("SELECT menu_id FROM vg_commande WHERE commande_id = ?");
        $stmt->execute([$commandeId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? (int)$row['menu_id'] : null;
    }
    // Annulation côté CLIENT : 
    public static function cancelOrderForUser(\PDO $db, int $commandeId, int $userId): bool
    {
        $db->beginTransaction();
        try {
            // Annulation uniquement si la commande est encore en attente ET appartient au client
            $stmtUpdate = $db->prepare("
                UPDATE vg_commande
                SET statut = 'annulee'
                WHERE commande_id = :orderID
                  AND utilisateur_id = :userID
                  AND statut = 'en_attente'
            ");
            $stmtUpdate->execute([
                'orderID' => $commandeId,
                'userID'  => $userId
            ]);

            if ($stmtUpdate->rowCount() !== 1) {
                throw new \Exception("La commande a déjà été annulée ou n'est plus modifiable.");
            }

            // Restitution du stock dans la transaction
            $orderData = self::getOrderLight($db, $commandeId);

            $stmtRestituer = $db->prepare("
                UPDATE vg_menu
                SET quantite_restante = quantite_restante + :quantite
                WHERE menu_id = :menu_id
            ");
            $stmtRestituer->execute([
                'quantite' => (int)$orderData['nombre_personne'],
                'menu_id'  => (int)$orderData['menu_id']
            ]);

            $db->commit();
            return true;
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
    // Compteurs du tableau de bord 
    public static function getDashboardStats(\PDO $db): array
    {
        $row = $db->query("
            SELECT COUNT(*) AS total,
                   SUM(statut = 'en_attente') AS en_attente,
                   SUM(statut = 'terminee') AS terminee,
                   SUM(statut = 'en_attente_retour_materiel') AS en_attente_retour_materiel
            FROM vg_commande
        ")->fetch(\PDO::FETCH_ASSOC);

        return [
            'total'      => (int)($row['total'] ?? 0),
            'en_attente' => (int)($row['en_attente'] ?? 0),
            'terminee'   => (int)($row['terminee'] ?? 0),
            'en_attente_retour_materiel' => (int)($row['en_attente_retour_materiel'] ?? 0),
        ];
    }
}
