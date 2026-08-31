<?php

namespace App\Controllers\StaffCommon;

use App\Controllers\AuthController\Auth;
use App\Managers\MenuManager;

class MenuManagementController
{

 // Fonction pour ajouter un menu
    public static function addMenu(\PDO $db)
    {
        // Sécurité d'accès
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
   
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // 1. Démarrer la transaction
                $db->beginTransaction();

                // Récupération et sécurisation des champs optionnels
                $theme_id = !empty($_POST['theme_id']) ? $_POST['theme_id'] : null;
                $regime_id = !empty($_POST['regime_id']) ? $_POST['regime_id'] : null;

                // 2. Préparation de l'insertion dans 'vg_menu'
                $sqlMenu = "INSERT INTO vg_menu (
                    titre, 
                    nombre_personne_minimum, 
                    prix_par_personne, 
                    description_menu, 
                    quantite_restante, 
                    delai_commande, 
                    conditions_stockage,
                    theme_id,
                    regime_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $db->prepare($sqlMenu);
                $stmt->execute([
                    $_POST['titre'],
                    $_POST['min_personne'],
                    $_POST['prix'],
                    $_POST['description'],
                    $_POST['quantite'],
                    $_POST['delai'],
                    $_POST['conditions'] ?? null,
                    $theme_id,
                    $regime_id
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

                // 5. Validation de la transaction
                $db->commit();

                header("Location: index.php?page=menu-management&success=1");
                exit();
                
            } catch (\Exception $e) {
                // Annulation en cas d'erreur
                if ($db->inTransaction()) {
                    $db->rollBack();
                }

                // Affiche l'erreur explicitement à l'écran pour le debug
                echo "<pre>Erreur SQL / Exception : " . $e->getMessage() . "</pre>";
                exit();
            }
        }
    }
   //function pour récupérer tous les plats
    public static function getAllPlats(\PDO $db)
    {
        return $db->query("SELECT plat_id, titre_plat, categorie FROM vg_plat")->fetchAll(\PDO::FETCH_ASSOC);
    }
    //function pour récupérer tous les menus
    public static function getMenus(\PDO $db)
{
    $sql = "SELECT m.*, 
            GROUP_CONCAT(p.titre_plat SEPARATOR ', ') as liste_plats,
            (SELECT COUNT(*) FROM vg_commande c WHERE c.menu_id = m.menu_id) as nb_commandes
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

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?page=menu-management&error=invalid_method');
        exit();
    }

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: index.php?page=menu-management&error=csrf_failed');
        exit();
    }

    try {
        // 1. Vérifier si le menu est lié à au moins une commande
        $stmtCheck = $db->prepare("SELECT COUNT(*) FROM vg_commande WHERE menu_id = ?");
        $stmtCheck->execute([$menu_id]);
        $hasOrders = $stmtCheck->fetchColumn() > 0;

        $db->beginTransaction();

        if ($hasOrders) {
            // 2. S'il y a des commandes : Désactivation du menu (soft delete)
          
            $stmtUpdate = $db->prepare("UPDATE vg_menu SET is_active = 0 WHERE menu_id = ?");
            $stmtUpdate->execute([$menu_id]);
            
            $db->commit();
            header('Location: index.php?page=menu-management&success=deactivated');
        } else {
            // 3. Aucune commande liée : Suppression physique sécurisée
            $stmtDelLinks = $db->prepare("DELETE FROM vg_menu_plat WHERE menu_id = ?");
            $stmtDelLinks->execute([$menu_id]);

            $stmtDelMenu = $db->prepare("DELETE FROM vg_menu WHERE menu_id = ?");
            $stmtDelMenu->execute([$menu_id]);

            $db->commit();
            header('Location: index.php?page=menu-management&success=deleted');
        }
    } catch (\Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        header('Location: index.php?page=menu-management&error=delete_failed');
    }
    exit();
}
public static function activateMenu(\PDO $db, $menu_id)
{
    Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
    
    if (empty($menu_id)) {
        die("Erreur : Aucun ID de menu reçu !");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?page=menu-management&error=invalid_method');
        exit();
    }

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: index.php?page=menu-management&error=csrf_failed');
        exit();
    }

    try {
        $stmt = $db->prepare("UPDATE vg_menu SET is_active = 1 WHERE menu_id = ?");
        $stmt->execute([$menu_id]);
        
        header('Location: index.php?page=menu-management&success=activated');
    } catch (\Exception $e) {
        header('Location: index.php?page=menu-management&error=activation_failed');
    }
    exit();
}
// function pour ajouter un plat via AJAX
public static function createPlatAjax(\PDO $db)
{
    Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titre_plat = $_POST['titre_plat'] ?? '';
        $description = $_POST['description_plat'] ?? '';
        $categorie = $_POST['categorie'] ?? '';
        $allergenes = $_POST['allergenes'] ?? []; // Tableau des IDs d'allergènes cochés
      

        if (empty($titre_plat) || empty($categorie)) {
            echo json_encode(['success' => false, 'message' => 'Le titre et la catégorie sont obligatoires.']);
            exit();
        }

        $photoPath = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = ROOT_PATH . '/public/assets/uploads/plats/';
                if (!is_dir($uploadFileDir)) { mkdir($uploadFileDir, 0755, true); }
                if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                    $photoPath = 'assets/uploads/plats/' . $newFileName;
                }
            }
        }

        try {
            $db->beginTransaction();

            // 1. Insertion du plat
            $sql = "INSERT INTO vg_plat (titre_plat, description_plat, categorie, photo) 
                    VALUES (:titre_plat, :description_plat, :categorie, :photo)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'titre_plat' => $titre_plat,
                'description_plat' => $description,
                'categorie' => $categorie,
                'photo' => $photoPath
            ]);

            $plat_id = $db->lastInsertId();

            // 2. Insertion des liens dans la table pivot des allergènes
            if (!empty($allergenes) && is_array($allergenes)) {
                $sqlPivot = "INSERT INTO vg_allergene_plat (plat_id, allergene_id) VALUES (?, ?)";
                $stmtPivot = $db->prepare($sqlPivot);
                foreach ($allergenes as $allergene_id) {
                    $stmtPivot->execute([(int)$plat_id, (int)$allergene_id]);
                }
            }

            $db->commit();
            echo json_encode(['success' => true, 'plat_id' => $plat_id]);
            exit();

        } catch (\Exception $e) {
            if ($db->inTransaction()) { $db->rollBack(); }
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit();
        }
    }
}
 //function pour afficher la page de gestion des menus
    public static function MenuManagement(\PDO $db)
    {

        // Inclure la vue correspondante
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        $menus = self::getMenus($db);
        $all_plats = self::getAllPlats($db);
        $all_allergenes = MenuManager::getAllAllergenes($db);
        $specific_styles = [
            'assets/css/bootstrap/bootstrap.min.css',
            'assets/css/Admin/OrderManagement.css',
            'assets/css/Admin/AdminEmployee.css'
        ];
        $specific_scripts = [
            'assets/javascript/OrderManagement.js','assets/javascript/AddMenu.js'
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
