<?php

namespace App\Controllers\UserController;

require_once dirname(__DIR__, 2) . '/config/constants.php';

use App\Controllers\AuthController\Auth;



//! reprendre la requete sql ( ne recupere pas orderID) a corriger



class EraseOrderController
{
    public static function eraseOrder(\PDO $db, ?int $commande_id)
    {
        //verif de secu
        Auth::check([ROLE_USER]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json; charset=utf-8');

            if (!$commande_id) {
                echo json_encode(['success' => false, 'message' => 'Identifiant de commande manquant.']);
                exit();
            }

            $pdo = $db;
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            try {
                // Vérifier que la commande appartient à l'utilisateur connecté et que statut = 'en_attente'
                $sqlCheck = "SELECT c.utilisateur_id, c.statut, m.titre as menu_titre, 
                    c.prix_total, 
                    c.date_prestation, c.heure_livraison, 
                    l.adresse, l.ville, l.code_postal
             FROM vg_commande c
             JOIN vg_menu m ON c.menu_id = m.menu_id
             JOIN vg_lieu_prestation l ON c.lieu_prestation_id = l.id
             WHERE c.commande_id = :orderID";
                $stmtCheck = $pdo->prepare($sqlCheck);
                $stmtCheck->execute(['orderID' => $commande_id]);
                $result = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

                if (!$result || (int)$result['utilisateur_id'] !== (int)$_SESSION['user_id']) {
                    throw new \Exception("Commande non trouvée ou accès refusé.");
                }

                //double securité pour éviter les annulations intempestives
                if ($result['statut'] !== 'en_attente') {
                    throw new \Exception("Seules les commandes en attente peuvent être supprimées.");
                }

                // Supprimer la commande de la db vg_commande
                $sqlUpdate = "UPDATE vg_commande SET statut = 'annulee' WHERE commande_id = :orderID";
                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->execute(['orderID' => $commande_id]);
try {
    $orderDetails = [
        'commande_id'     => $commande_id,
        'menu'            => ['titre' => $result['menu_titre'] ?? 'Non défini'],
        'total_final'     => $result['prix_total'] ?? 0,
        'date_prestation' => $result['date_prestation'],
        'heure_livraison' => $result['heure_livraison'],
        'lieu'            => $result['adresse'] . ', ' . $result['code_postal'] . ' ' . $result['ville']
    ];

            \App\Helpers\MailService::sendOrderCancellationEmail($_SESSION['email'], $orderDetails);
        } catch (\Exception $e) {
            error_log("Erreur envoi mail : " . $e->getMessage());
        }
                echo json_encode(['success' => true, 'message' => '✅ Commande annulée']);
                exit();
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit();
            }
        }
    }
}
