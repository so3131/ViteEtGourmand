<?php

namespace App\Controllers\AuthController;

use App\Controllers\AuthController\Auth;
use App\Helpers\SecurityManager;
use App\Managers\UserManager;

class DeactivateAccountController
{
    //function pour désactiver le compte utilisateur    
    public static function deactivateAccount(\PDO $db)
    {
        Auth::check([ROLE_USER]);
        SecurityManager::validatePost('?page=dashboard-user');

        $userId = $_SESSION['user_id'];

        // Vérifier qu'il n'y a pas de commandes en cours
        $activeOrders = UserManager::countActiveOrders($db, $userId);
        if ($activeOrders > 0) {
            $_SESSION['error'] = "Vous avez des commandes en cours. Veuillez attendre leur finalisation avant de désactiver votre compte.";

            header('Location: index.php?page=dashboard-user');
            exit();
        }

        // Désactiver le compte
        UserManager::deactivate($db, $userId);

        // 3. Déconnexion
        session_destroy();
        header('Location: ?page=home&success=account_deactivated');
        exit();
    }
}
