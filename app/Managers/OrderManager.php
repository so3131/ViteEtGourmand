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
                statut: 'en_attente',
                pret_materiel: isset($prestation['location_materiel']) && $prestation['location_materiel'] ? '1' : '0',
                restitution_materiel: '0',
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
        prix_menu, nombre_personne, prix_livraison, statut, 
        pret_materiel, restitution_materiel, utilisateur_id, menu_id,lieu_prestation_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        var_dump($order->heure_livraison);
        // On passe les valeurs dans le même ordre
        return $stmt->execute([
            $order->numero_commande,
            $order->date_commande,
            $order->date_prestation,
            $order->heure_livraison,
            $order->prix_menu,
            $order->nombre_personne,
            $order->prix_livraison,
            $order->statut,
            (int)$order->pret_materiel,      // Conversion forcée en int (0 ou 1)
            (int)$order->restitution_materiel, // Conversion forcée en int (0 ou 1)
            (int)$order->utilisateur_id,
            (int)$order->menu_id,
            (int)$order->lieu_prestation_id
        ]);
    }

    public static function getOrdersByUser(\PDO $db, int $userId)
    {
        $sql = "SELECT * FROM vg_commande WHERE utilisateur_id = :user_id ORDER BY date_prestation DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $allOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Tri manuel par statut 
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
            } else {
                error_log("Statut inconnu trouvé : " . $order['statut']);
            }
        }
        return $orders;
    }
public static function EstimerFraisLivraison(\PDO $db, int $lieu_id): float {
    // 1. Récupération des données du lieu
    $lieu = LieuManager::getById($db, $lieu_id);
    
    // 2. Appel de ton modèle Order pour le calcul
    return Order::calculerFraisLivraison($lieu['ville'], $lieu['distance_bordeaux']);
}

    // public static function eraseOrder(\PDO $db, ?int $commande_id)
    // {
    //     Auth::check([ROLE_USER]);
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $pdo = $db;
    //         $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    //         try {
    //             // Vérifier que la commande appartient à l'utilisateur connecté et que statut = 'en_attente'
    //             $sqlCheck = "SELECT utilisateur_id, statut FROM vg_commande WHERE commande_id = :orderID";
    //             $stmtCheck = $pdo->prepare($sqlCheck);
    //             $stmtCheck->execute(['orderID' => $commande_id]);
    //             $result = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

    //             if (!$result || (int)$result['utilisateur_id'] !== (int)$_SESSION['user_id']) {
    //                 throw new \Exception("Commande non trouvée ou accès refusé.");
    //             }

    //             //double securité pour éviter les annulations intempestives
    //             if ($result['statut'] !== 'en_attente') {
    //                 throw new \Exception("Seules les commandes en attente peuvent être supprimées.");
    //             }

    //             // Supprimer la commande de la db vg_commande
    //             $sqlUpdate = "UPDATE vg_commande SET statut = 'annulee' WHERE commande_id = :orderID";
    //             $stmtUpdate = $pdo->prepare($sqlUpdate);
    //             $stmtUpdate->execute(['orderID' => $commande_id]);

    //             echo json_encode(['success' => true, 'message' => '✅ Commande annulée']);
    //             exit();
    //         } catch (\Exception $e) {
    //             header("Location: index.php?page=dashboard-user&error=" . urlencode($e->getMessage()));
    //             exit();
    //         }
    //     }
    // }
}
