<?php

namespace App\Controllers\StaffCommon;

use App\Controllers\AuthController\Auth;
use App\Managers\OrderManager;
use App\Helpers\MailService;
class OrderManagementController
{


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
            // Ton code de mise à jour du statut en "annulée" en base de données...
            $stmt = $db->prepare("UPDATE vg_commande SET statut = 'annulee', motif_annulation = ?, mode_contact = ? WHERE commande_id = ?");
            $stmt->execute([$motif, $mode_contact, $commande_id]);

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
        $stmt = $db->prepare(
            'UPDATE vg_commande SET statut = ? WHERE commande_id = ?'
        );
        $stmt->execute([$nouveauStatut, $commande_id]);

        header('Location: index.php?page=order-management&success=status_updated');
        exit();
    } catch (\Exception $e) {
        error_log('Erreur mise à jour statut : ' . $e->getMessage());
        header('Location: index.php?page=order-management&error=update_failed');
        exit();
    }
}
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
