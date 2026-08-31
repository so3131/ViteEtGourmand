<?php

namespace App\Managers;

use App\Models\Order;




class OrderManager
{
    //function pour creer une commande à partir des données fournies, en gérant la transaction et la mise à jour du stock
    public static function createOrderFromData(\PDO $db, object $user, array $menuData, array $prestation, float $totalCommande)

    {
        try {
            // 1. On ouvre la transaction
            $db->beginTransaction();
$lieuPrestationId = LieuManager::getOrInsert(
    $db,
    $prestation['adresse_livraison'] ?? '',
    $prestation['code_postal'] ?? '',
    $prestation['ville'] ?? '',
    $prestation['lat'] ?? null,
    $prestation['lon'] ?? null
);
            // 2. On crée l'objet
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
    //function pour créer une commande dans la base de données à partir d'un objet Order
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


 //function pour récupérer toutes les commandes avec des filtres optionnels pour le dashboard staff
public static function getAllOrders(\PDO $db, $filters = []) {
    // 1. Initialisation de la requête de base avec les JOIN (on rajoute u.email)
    $sql = "SELECT c.*, u.nom as client_nom, u.email as client_email, m.titre as menu_titre, 
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
    public static function getOrderHistory(\PDO $db, int $commande_id): array {
    $stmt = $db->prepare("SELECT * FROM vg_commande_statut_historique WHERE commande_id = ? ORDER BY date_changement ASC");
    $stmt->execute([$commande_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
// function pour vérifier si l'on peut changer de statut sans reculer dans le workflow
    public static function canUpdateStatus(string $oldStatus, string $newStatus): bool
    {
        // Définition de l'ordre hiérarchique des statuts
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

        // Si l'un des statuts n'existe pas dans le tableau, on bloque
        if (!isset($workflow[$oldStatus]) || !isset($workflow[$newStatus])) {
            return false;
        }

        // Si la commande est déjà terminée ou annulée, on ne peut plus modifier son statut
        if ($oldStatus === 'terminee' || $oldStatus === 'annulee') {
            return false;
        }

        // Autoriser le passage à "annulee" depuis n'importe quel statut actif
        if ($newStatus === 'annulee') {
            return true;
        }

        // Règle principale : Le nouveau statut doit avancer dans le workflow, pas reculer
        return $workflow[$newStatus] >= $workflow[$oldStatus];
    }
}