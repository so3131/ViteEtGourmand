<?php

namespace App\Controllers\StaffCommon;

use App\Controllers\AuthController\Auth;
use App\Managers\OrderManager;

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
            'assets/css/Admin/OrderManagement.css',
            'assets/css/Admin/AdminEmployee.css'
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

        $commande_id = $_POST['commande_id'] ?? null;
        $motif       = $_POST['motif'] ?? null;
        $mode        = $_POST['mode_contact'] ?? null;

        if ($commande_id && $motif && $mode) {
            $sql = "UPDATE vg_commande 
                SET statut = 'annulée', 
                    motif_annulation = :motif, 
                    mode_contact = :mode 
                WHERE commande_id = :id";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':motif' => $motif,
                ':mode'  => $mode,
                ':id'    => $commande_id
            ]);
        }

        // Redirection après traitement
        header('Location: index.php?page=order-management');
        exit;
    }
}
