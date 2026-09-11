<?php

namespace App\Controllers\UserController;



require_once dirname(__DIR__, 2) . '/config/constants.php';
use App\Helpers\SecurityManager;
use App\Controllers\AuthController\Auth;
use App\Managers\OrderManager;
// Class EraseOrderController pour gérer l'annulation des commandes par les utilisateurs
class EraseOrderController
{
    //function pour annuler une commande depuis le tableau de bord de l'utilisateur
    public static function eraseOrder(\PDO $db, ?int $commande_id)
    {

        Auth::check([ROLE_USER]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json; charset=utf-8');
            SecurityManager::validatePost('?page=dashboard-user');

            if (!$commande_id) {
                echo json_encode(['success' => false, 'message' => 'Identifiant de commande manquant.']);
                exit();
            }

            
            try {
                // Récupérer les infos nécessaires
                $result = OrderManager::getOrderWithDetailsForUser($db, (int)$commande_id, (int)$_SESSION['user_id']);

                if (!$result) {
                    throw new \Exception("Commande non trouvée ou accès refusé.");
                }

                if ($result['statut'] !== 'en_attente') {
                    throw new \Exception("Seules les commandes en attente peuvent être annulées.");
                }

               

OrderManager::cancelOrderForUser($db, (int)$commande_id, (int)$_SESSION['user_id']);


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
