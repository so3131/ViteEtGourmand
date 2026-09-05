<?php

namespace App\Controllers\StaffCommon;

use App\Controllers\AuthController\Auth;
use App\Managers\MenuManager;
use App\Helpers\SecurityManager;
// Class MenuManagementController pour gérer la gestion des menus (accessible aux admins et employés)
class MenuManagementController
{

    // Fonction pour ajouter un menu
    public static function addMenu(\PDO $db)
    {
        // Sécurité d'accès
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);



        SecurityManager::validatePost('?page=menu-management');
        if (isset($_POST['submit'])) {
            try {
                //Démarrer la transaction
                $db->beginTransaction();

                // Récupérer et sécuriser les champs optionnels
                $theme_id = !empty($_POST['theme_id']) ? $_POST['theme_id'] : null;
                $regime_id = !empty($_POST['regime_id']) ? $_POST['regime_id'] : null;

                // Préparation de l'insertion
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

                //Récupérer l'ID du menu fraîchement créé
                $menu_id = $db->lastInsertId();

                // Inserer les liens dans 'vg_menu_plat'
                if (!empty($_POST['plats']) && is_array($_POST['plats'])) {
                    $sqlLiaison = "INSERT INTO vg_menu_plat (menu_id, plat_id) VALUES (?, ?)";
                    $stmtLiaison = $db->prepare($sqlLiaison);

                    foreach ($_POST['plats'] as $plat_id) {
                        $stmtLiaison->execute([(int)$menu_id, (int)$plat_id]);
                    }
                }

                // Valider de la transaction
                $db->commit();

                header("Location: index.php?page=menu-management&success=1");
                exit();
            } catch (\Exception $e) {
                // Annulation en cas d'erreur
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                $_SESSION['error'] = "Erreur lors de l'ajout du menu : " . $e->getMessage();
                header("Location: index.php?page=menu-management&error=1");
                exit();
            }
        }
    }
    //function pour récupérer tous les plats avec leur nombre de menus associés
    public static function getAllPlats(\PDO $db)
    {
        $sql = "SELECT p.plat_id, p.titre_plat, p.description_plat, p.categorie, p.photo, p.is_active,
                       COUNT(mp.menu_id) as nb_menus
                FROM vg_plat p
                LEFT JOIN vg_menu_plat mp ON p.plat_id = mp.plat_id
                GROUP BY p.plat_id
                ORDER BY p.categorie ASC, p.titre_plat ASC";
        return $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
    // Fonction pour supprimer un plat (Soft delete si lié à un menu)
    public static function deletePlat(\PDO $db, $plat_id = null)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        SecurityManager::validatePost('?page=menu-management');


        $plat_id = $plat_id ?? $_POST['plat_id'] ?? $_GET['plat_id'] ?? null;

        if (empty($plat_id)) {
            $_SESSION['error'] = "Erreur : Aucun identifiant de plat reçu.";
            header('Location: index.php?page=menu-management');
            exit();
        }

        try {

            $stmtCheck = $db->prepare("SELECT COUNT(*) FROM vg_menu_plat WHERE plat_id = ?");
            $stmtCheck->execute([$plat_id]);
            $hasMenus = $stmtCheck->fetchColumn() > 0;

            if ($hasMenus) {

                $stmtUpdate = $db->prepare("UPDATE vg_plat SET is_active = 0 WHERE plat_id = ?");
                $stmtUpdate->execute([$plat_id]);
                $_SESSION['success'] = "Ce plat est lié à un menu : il a été désactivé (archivé).";
            } else {

                $stmtPhoto = $db->prepare("SELECT photo FROM vg_plat WHERE plat_id = ?");
                $stmtPhoto->execute([$plat_id]);
                $photo = $stmtPhoto->fetchColumn();


                $stmtDel = $db->prepare("DELETE FROM vg_plat WHERE plat_id = ?");
                $stmtDel->execute([$plat_id]);


                if (!empty($photo) && file_exists(ROOT_PATH . '/public/' . $photo)) {
                    @unlink(ROOT_PATH . '/public/' . $photo);
                }

                $_SESSION['success'] = "Le plat a été définitivement supprimé.";
            }
        } catch (\PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
        }

        header('Location: index.php?page=menu-management');
        exit();
    }
    // Function pour ajouter un plat
    public static function addPlatProcess(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);

        SecurityManager::validatePost('?page=menu-management');

        $titre = trim($_POST['titre_plat'] ?? '');
        $categorie = trim($_POST['categorie'] ?? '');
        $description = trim($_POST['description_plat'] ?? '');
        $allergenes = $_POST['allergenes'] ?? [];

        if (empty($titre) || empty($categorie)) {
            $_SESSION['error'] = "Le nom du plat et sa catégorie sont obligatoires.";
            header('Location: index.php?page=menu-management');
            exit();
        }

        $photoPath = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $fileName = $_FILES['photo']['name'];
            $fileTmp = $_FILES['photo']['tmp_name'];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $newFileName = md5(time() . $fileName) . '.' . $ext;
                $uploadDir = ROOT_PATH . '/public/assets/uploads/plats/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
                    $photoPath = 'assets/uploads/plats/' . $newFileName;
                }
            }
        }

        try {
            $db->beginTransaction();


            $stmt = $db->prepare("INSERT INTO vg_plat (titre_plat, description_plat, categorie, photo, is_active) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$titre, $description, $categorie, $photoPath]);
            $platId = $db->lastInsertId();

            if (!empty($allergenes) && is_array($allergenes)) {
                $stmtAllergenes = $db->prepare("INSERT INTO vg_allergene_plat (plat_id, allergene_id) VALUES (?, ?)");
                foreach ($allergenes as $allergeneId) {
                    $stmtAllergenes->execute([$platId, (int)$allergeneId]);
                }
            }

            $db->commit();
            $_SESSION['success'] = "Le plat a été ajouté avec succès !";
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $_SESSION['error'] = "Erreur lors de l'ajout du plat : " . $e->getMessage();
        }


        header('Location: index.php?page=menu-management');
        exit();
    }
    // Fonction pour réactiver un plat désactivé
    public static function activatePlat(\PDO $db, $plat_id = null)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        SecurityManager::validatePost('?page=menu-management');
        $plat_id = $plat_id ?? $_POST['plat_id'] ?? $_GET['plat_id'] ?? null;
        if (empty($plat_id)) {
            die("Erreur : Aucun ID de plat reçu !");
        }

        try {
            $stmt = $db->prepare("UPDATE vg_plat SET is_active = 1 WHERE plat_id = ?");
            $stmt->execute([$plat_id]);
            header('Location: index.php?page=menu-management&success=activated');
        } catch (\Exception $e) {
            header('Location: index.php?page=menu-management&error=activation_failed');
        }
        exit();
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
    // function pour effacer un menu (soft delete si lié à des commandes, sinon suppression physique)
    public static function deleteMenu(\PDO $db, $menu_id)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);

        if (empty($menu_id)) {
            die("Erreur : Aucun ID de menu reçu !");
        }

        SecurityManager::validatePost('?page=menu-management');


        try {
            //Vérifier si le menu est lié à au moins une commande
            $stmtCheck = $db->prepare("SELECT COUNT(*) FROM vg_commande WHERE menu_id = ?");
            $stmtCheck->execute([$menu_id]);
            $hasOrders = $stmtCheck->fetchColumn() > 0;

            $db->beginTransaction();

            if ($hasOrders) {
                // Si commandes : soft delete

                $stmtUpdate = $db->prepare("UPDATE vg_menu SET is_active = 0 WHERE menu_id = ?");
                $stmtUpdate->execute([$menu_id]);

                $db->commit();
                header('Location: index.php?page=menu-management&success=deactivated');
            } else {
                // Si aucune commande: Suppression ok
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
    // function pour activer un menu (si desactivée par un soft delete)
    public static function activateMenu(\PDO $db, $menu_id)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        SecurityManager::validatePost('?page=menu-management');

        if (empty($menu_id)) {
            die("Erreur : Aucun ID de menu reçu !");
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
    // function pour ajouter un plat pendant la creation d'un menu via AJAX
    public static function createPlatAjax(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre_plat = $_POST['titre_plat'] ?? '';
            $description = $_POST['description_plat'] ?? '';
            $categorie = $_POST['categorie'] ?? '';
            $allergenes = $_POST['allergenes'] ?? [];


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
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                        $photoPath = 'assets/uploads/plats/' . $newFileName;
                    }
                }
            }

            try {
                $db->beginTransaction();

                // Insertion du plat
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

                //Insertion des liens dans la table pivot des allergènes
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
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit();
            }
        }
    }
    //function pour afficher la page de gestion des menus
    public static function MenuManagement(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        $menus = self::getMenus($db);
        $all_plats = self::getAllPlats($db);
        $all_allergenes = MenuManager::getAllAllergenes($db);
        $specific_styles = [
            'assets/css/bootstrap/bootstrap.min.css',
            'assets/css/Admin/AdminEmployee.css'
        ];
        $specific_scripts = [
            'assets/javascript/AddMenu.js',
            'assets/javascript/tables.js'
        ];

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
