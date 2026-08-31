<?php

namespace App\Controllers\StaffCommon;

use App\Controllers\AuthController\Auth;
use App\Managers\OrderManager;
use App\Helpers\MailService;
class OrderManagementController
{

     //function pour afficher la page de gestion des commandes
    public static function OrderManagement(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        // Nettoyage et récupération des filtres
        $clientNom = $_GET['client_nom'] ?? null;
        $status = $_GET['status'] ?? null;

        $filters = [
            'client_nom' => !empty($clientNom) ? $clientNom : null,
            'status'     => !empty($status) ? $status : null
        ];

        $orders = OrderManager::getAllOrders($db, $filters);

        $specific_styles = [
            'assets/css/AdminEmployee/OrderManagement.css',
            'assets/css/AdminEmployee/AdminEmployee.css'
        ];
        $specific_scripts = [
            'assets/javascript/OrderManagement.js'
        ];



        // Chargement de la vue
        $userRole = $_SESSION['role_id'] ?? null;
        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_header.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_header.php';
        }
        require_once ROOT_PATH . '/app/views/StaffCommon/order.management.view.php';

        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
        }
    }
     //function pour annuler une commande
   public static function cancelOrder(\PDO $db)
{
    Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);

    // 1. Vérification de la méthode POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?page=order-management&error=invalid_method');
        exit();
    }

    // 2. Vérification du jeton CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: index.php?page=order-management&error=csrf_failed');
        exit();
    }

    $commande_id = intval($_POST['commande_id'] ?? 0);
    $motif = trim($_POST['motif'] ?? '');
    $mode_contact = trim($_POST['mode_contact'] ?? '');

    if ($commande_id > 0) {
        try {
            // 3. Mettre à jour le statut en "annulée" en base de données
            $stmt = $db->prepare("UPDATE vg_commande SET statut = 'annulee', motif_annulation = ?, mode_contact = ? WHERE commande_id = ?");
            $stmt->execute([$motif, $mode_contact, $commande_id]);

            // 4. Récupérer les informations nécessaires pour envoyer l'e-mail au client
            $sqlDetails = "SELECT c.*, u.email, u.prenom, u.nom, m.titre as menu_titre 
                           FROM vg_commande c
                           JOIN vg_utilisateur u ON c.utilisateur_id = u.utilisateur_id
                           JOIN vg_menu m ON c.menu_id = m.menu_id
                           WHERE c.commande_id = :id";
            
            $stmtDetails = $db->prepare($sqlDetails);
            $stmtDetails->execute(['id' => $commande_id]);
            $orderData = $stmtDetails->fetch(\PDO::FETCH_ASSOC);
 // 5. Restituer le stock du menu annulé par le staff
            if ($orderData) {
                $stmtRestituer = $db->prepare("UPDATE vg_menu SET quantite_restante = quantite_restante + :quantite WHERE menu_id = :menu_id");
                $stmtRestituer->execute([
                    'quantite' => (int)$orderData['nombre_personne'],
                    'menu_id'  => (int)$orderData['menu_id']
                ]);
            }
            // 6. Envoyer l'e-mail d'annulation si le client a un e-mail valide
            if ($orderData && !empty($orderData['email'])) {
                // Structurer le tableau de détails attendu par ton MailService
                $orderDetails = [
                    'commande_id' => $orderData['commande_id'],
                    'prenom' => $orderData['prenom'],
                    'menu' => [
                        'titre' => $orderData['menu_titre']
                    ]
                ];

                // Appel du service de mail avec le motif de l'équipe en commentaire
                MailService::sendOrderCancellationEmail($orderData['email'], $orderDetails, $motif);
            }

            header('Location: index.php?page=order-management&success=order_cancelled');
            exit();
        } catch (\Exception $e) {
            header('Location: index.php?page=order-management&error=cancel_failed');
            exit();
        }
    } else {
        header('Location: index.php?page=order-management&error=invalid_data');
        exit();
    }
}
 //function pour mettre à jour le statut d'une commande
public static function updateStatus(\PDO $db)
{
    Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?page=order-management&error=invalid_method');
        exit();
    }

    if (
        !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        header('Location: index.php?page=order-management&error=csrf_failed');
        exit();
    }

    $commande_id = (int) ($_POST['commande_id'] ?? 0);
    $nouveauStatut = trim($_POST['nouveau_statut'] ?? '');

    $statutsAutorises = [
        'en_attente',
        'acceptee',
        'en_preparation',
        'en_cours_livraison',
        'livree',
        'en_attente_retour_materiel',
        'terminee'
    ];

    if (!in_array($nouveauStatut, $statutsAutorises, true)) {
        header('Location: index.php?page=order-management&error=invalid_status');
        exit();
    }

    if ($commande_id <= 0) {
        header('Location: index.php?page=order-management&error=invalid_data');
        exit();
    }

    try {
        // 1. Récupérer la commande actuelle pour connaître son ancien statut
        $order = OrderManager::getOrderById($db, $commande_id);
        if (!$order) {
            header('Location: index.php?page=order-management&error=order_not_found');
            exit();
        }

        // 2. Vérifier si le changement de statut est autorisé (interdit de reculer)
        if (!OrderManager::canUpdateStatus($order['statut'], $nouveauStatut)) {
            header('Location: index.php?page=order-management&error=status_progression_invalid');
            exit();
        }

        // 3. Mise à jour du statut en base
        $stmt = $db->prepare(
            'UPDATE vg_commande SET statut = ? WHERE commande_id = ?'
        );
        $stmt->execute([$nouveauStatut, $commande_id]);

        $stmtHistorique = $db->prepare(
            "INSERT INTO vg_commande_statut_historique (commande_id, statut) VALUES (?, ?)"
        );
        $stmtHistorique->execute([$commande_id, $nouveauStatut]);

        // ENVOI DES MAILS SELON LE STATUT (on réutilise les données de $order déjà récupérées)
        if (!empty($order['client_email'])) {
            $orderDetails = [
                'commande_id' => $commande_id,
                'prenom' => $order['prenom'] ?? $order['client_prenom'] ?? 'client'
            ];

            // 1. Cas particulier : Retour de matériel
            if ($nouveauStatut === 'en_attente_retour_materiel') {
                MailService::sendMaterialReturnReminderEmail(
                    $order['client_email'],
                    $order,
                    ''
                );
            } 
            // 2. Cas particulier : Commande terminée
            elseif ($nouveauStatut === 'terminee') {
                MailService::sendReviewEmail(
                    $order['client_email'],
                    $orderDetails,
                    $orderDetails['prenom']
                );
            } 
            // 3. Pour tous les autres statuts
            else {
                MailService::sendOrderStatusUpdateEmail(
                    $order['client_email'], 
                    $orderDetails, 
                    $nouveauStatut, 
                    $orderDetails['prenom']
                );
            }
        }

        header('Location: index.php?page=order-management&success=status_updated');
        exit();
    } catch (\Exception $e) {
        error_log('Erreur mise à jour statut : ' . $e->getMessage());
        header('Location: index.php?page=order-management&error=update_failed');
        exit();
    }
}
 //function pour contacter le client pour le retour du matériel
public static function contactMaterialClient(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=order-management&error=invalid_method');
            exit();
        }

        if (
            !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            header('Location: index.php?page=order-management&error=csrf_failed');
            exit();
        }

        $commande_id = (int) ($_POST['commande_id'] ?? 0);
        $commentaire = trim($_POST['commentaire_contact'] ?? '');

        if ($commande_id <= 0) {
            header('Location: index.php?page=order-management&error=invalid_data');
            exit();
        }

        try {
            // On récupère la commande (grâce à la méthode getOrderById qu'on vient d'ajouter dans OrderManager)
            $order = OrderManager::getOrderById($db, $commande_id);

            if ($order && !empty($order['client_email'])) {
                // On envoie l'e-mail via le MailService
                $emailEnvoye = MailService::sendMaterialReturnReminderEmail(
    $order['client_email'],
    $order,
    $commentaire
);

                if ($emailEnvoye) {
                    header('Location: index.php?page=order-management&success=mail_sent');
                    exit();
                } else {
                    header('Location: index.php?page=order-management&error=mail_send_failed');
                    exit();
                }
            } else {
                header('Location: index.php?page=order-management&error=order_or_email_not_found');
                exit();
            }
        } catch (\Exception $e) {
            error_log('Erreur contact matériel client : ' . $e->getMessage());
            header('Location: index.php?page=order-management&error=system_error');
            exit();
        }
    }
}
