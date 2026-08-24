<?php

namespace App\Controllers\StaffCommon;

use App\Controllers\AuthController\Auth;

class MenuManagementController
{


    public static function addMenu(\PDO $db)
    {
        // Sécurité d'accès
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // 1. Démarrer la transaction avec $db (la variable passée en argument)
                $db->beginTransaction();

                // 2. Préparation de l'insertion dans 'vg_menu'
                $sqlMenu = "INSERT INTO vg_menu (
                titre, 
                nombre_personne_minimum, 
                prix_par_personne, 
                description_menu, 
                quantite_restante, 
                delai_commande, 
                conditions_stockage
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";

                $stmt = $db->prepare($sqlMenu);
                $stmt->execute([
                    $_POST['titre'],
                    $_POST['min_personne'],
                    $_POST['prix'],
                    $_POST['description'],
                    $_POST['quantite'],
                    $_POST['delai'],
                    $_POST['conditions']
                ]);

                // 3. Récupérer l'ID du menu fraîchement créé
                $menu_id = $db->lastInsertId();

                // 4. Insertion des liens dans 'vg_menu_plat'
                if (!empty($_POST['plats']) && is_array($_POST['plats'])) {
                    $sqlLiaison = "INSERT INTO vg_menu_plat (menu_id, plat_id) VALUES (?, ?)";
                    $stmtLiaison = $db->prepare($sqlLiaison);

                    foreach ($_POST['plats'] as $plat_id) {
                        $stmtLiaison->execute([(int)$menu_id, (int)$plat_id]);
                    }
                }

                // 5. Validation
                $db->commit();

                header("Location: index.php?page=menu-management&success=1");
                exit();
            } catch (\Exception $e) {
                // Annulation en cas d'erreur
                if ($db->inTransaction()) {
                    $db->rollBack();
                }

                error_log("Erreur création menu : " . $e->getMessage());
                header("Location: index.php?page=menu-management&error=1");
                exit();
            }
        }
    }
    public static function getAllPlats(\PDO $db)
    {
        return $db->query("SELECT plat_id, titre_plat FROM vg_plat")->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function getMenus(\PDO $db)
    {
        // On récupère le menu et on concatène les titres des plats associés
        $sql = "SELECT m.*, 
            GROUP_CONCAT(p.titre_plat SEPARATOR ', ') as liste_plats
            FROM vg_menu m
            LEFT JOIN vg_menu_plat mp ON m.menu_id = mp.menu_id
            LEFT JOIN vg_plat p ON mp.plat_id = p.plat_id
            GROUP BY m.menu_id
            ORDER BY m.menu_id DESC";

        return $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function deleteMenu(\PDO $db, $menu_id)
{
    Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
if (empty($menu_id)) {
    die("Erreur : Aucun ID de menu reçu !");
}
    // 1. Vérification de sécurité : on s'assure que c'est bien une requête POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?page=menu-management&error=invalid_method');
        exit();
    }

    // 2. Vérification du jeton CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: index.php?page=menu-management&error=csrf_failed');
        exit();
    }

    try {
        $db->beginTransaction();

        // Suppression des liens d'abord
        $stmt = $db->prepare("DELETE FROM vg_menu_plat WHERE menu_id = ?");
        $stmt->execute([$menu_id]);

        // Suppression du menu
        $stmt = $db->prepare("DELETE FROM vg_menu WHERE menu_id = ?");
        $stmt->execute([$menu_id]);

        $db->commit();
        header('Location: index.php?page=menu-management&success=deleted');
    } catch (\Exception $e) {
        $db->rollBack();
        header('Location: index.php?page=menu-management&error=delete_failed');
    }
    exit();
}

    public static function MenuManagement(\PDO $db)
    {

        // Inclure la vue correspondante
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        $menus = self::getMenus($db);
        $all_plats = self::getAllPlats($db);
        $specific_styles = [
            'assets/css/bootstrap/bootstrap.min.css',
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

        require_once ROOT_PATH . '/app/views/StaffCommon/menu.management.view.php';

        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
        }
    }
}
