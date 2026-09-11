<?php

namespace App\Controllers\StaffCommon;

use App\Controllers\AuthController\Auth;
use App\Managers\OrderManager;
use App\Helpers\MailService;
use App\Helpers\SecurityManager;


###

// class OrderManagementController pour gérer la gestion des commandes
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
            'assets/css/AdminEmployee/AdminEmployee.css'
        ];
        $specific_scripts = [
            'assets/javascript/tables.js'
        ];

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
        SecurityManager::validatePost('?page=order-management');

        $commande_id = intval($_POST['commande_id'] ?? 0);
        $motif = trim($_POST['motif'] ?? '');
        $mode_contact = trim($_POST['mode_contact'] ?? '');

        if ($commande_id > 0) {

            try {


                // Récupérer la commande avant modification
                $orderData = OrderManager::cancelOrder($db, $commande_id, $motif, $mode_contact);

                // Envoyer l'e-mail après validation de la transaction
                if (!empty($orderData['email'])) {
                    $orderDetails = [
                        'commande_id' => $orderData['commande_id'],
                        'prenom' => $orderData['prenom'],
                        'menu' => [
                            'titre' => $orderData['menu_titre']
                        ]
                    ];

                    MailService::sendOrderCancellationEmail(
                        $orderData['email'],
                        $orderDetails,
                        $motif
                    );
                }

                header('Location: index.php?page=order-management&success=order_cancelled');
                exit();
            } catch (\Exception $e) {
                error_log('Erreur annulation commande staff : ' . $e->getMessage());
                header('Location: index.php?page=order-management&error=cancel_failed');
                exit();
            }
        }
    }
    //function pour mettre à jour le statut d'une commande
    public static function updateStatus(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);

        SecurityManager::validatePost('?page=order-management');

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
            // Récupérer la commande  pour connaître son ancien statut
            $order = OrderManager::getOrderById($db, $commande_id);
            if (!$order) {
                header('Location: index.php?page=order-management&error=order_not_found');
                exit();
            }

            // Vérifier si le changement de statut est autorisé
            if (!OrderManager::canUpdateStatus($order['statut'], $nouveauStatut)) {
                header('Location: index.php?page=order-management&error=status_progression_invalid');
                exit();
            }

            // Mise à jour du statut en base
            OrderManager::updateStatus($db, $commande_id, $nouveauStatut);


            // Envoi d'e-mail au client selon le nouveau statut
            if (!empty($order['client_email'])) {
                $orderDetails = [
                    'commande_id' => $commande_id,
                    'prenom' => $order['prenom'] ?? $order['client_prenom'] ?? 'client'
                ];

                // Cas particulier 1: Retour de matériel
                if ($nouveauStatut === 'en_attente_retour_materiel') {
                    MailService::sendMaterialReturnReminderEmail(
                        $order['client_email'],
                        $order,
                        ''
                    );
                }
                // Cas particulier 2 : Commande terminée
                elseif ($nouveauStatut === 'terminee') {
                    MailService::sendReviewEmail(
                        $order['client_email'],
                        $orderDetails,
                        $orderDetails['prenom']
                    );
                }
                // Cas particulier 3 : Pour tous les autres statuts
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
    //function pour contacter le client pour demander le retour du matériel
    public static function contactMaterialClient(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        SecurityManager::validatePost('?page=order-management');

        $commande_id = (int) ($_POST['commande_id'] ?? 0);
        $commentaire = trim($_POST['commentaire_contact'] ?? '');

        if ($commande_id <= 0) {
            header('Location: index.php?page=order-management&error=invalid_data');
            exit();
        }

        try {
            // Récupèrer la commande)
            $order = OrderManager::getOrderById($db, $commande_id);

            if ($order && !empty($order['client_email'])) {
                // Envoyer l'e-mail
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
