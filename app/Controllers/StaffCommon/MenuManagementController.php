<?php

namespace App\Controllers\StaffCommon;

use App\Controllers\AuthController\Auth;
use App\Managers\MenuManager;
use App\Helpers\SecurityManager;
use App\Managers\PlatManager;
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
                // Champs optionnels (null si non renseignés)
                $menuData = $_POST;
                $menuData['theme_id'] = !empty($_POST['theme_id']) ? (int)$_POST['theme_id'] : null;
                $menuData['regime_id'] = !empty($_POST['regime_id']) ? (int)$_POST['regime_id'] : null;
                $menuData['conditions'] = $_POST['conditions'] ?? null;

                $plats = (!empty($_POST['plats']) && is_array($_POST['plats'])) ? $_POST['plats'] : [];

                MenuManager::create($db, $menuData, $plats);

                header("Location: index.php?page=menu-management&success=1");
                exit();
            } catch (\Exception $e) {
                error_log("Erreur addMenu : " . $e->getMessage());
                $_SESSION['error'] = "Impossible d'ajouter le menu. Vérifie les données saisies.";
                header("Location: index.php?page=menu-management&error=1");
                exit();
            }
        }
    }

    //function pour récupérer tous les plats avec leur nombre de menus associés
    public static function getAllPlats(\PDO $db)
    {
        $all_plats = PlatManager::getAllWithMenuCount($db);
        return $all_plats;
    }

    // Fonction pour supprimer un plat (Soft delete possiblesi lié à un menu)
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
            $hasMenus = PlatManager::countLinkedMenus($db, $plat_id) > 0;
            if ($hasMenus) {
                PlatManager::deactivate($db, $plat_id);
                $_SESSION['success'] = "Ce plat est lié à un menu : il a été désactivé (archivé).";
            } else {
                $plat = PlatManager::getById($db, $plat_id);
                $photo = $plat['photo'] ?? '';
                PlatManager::delete($db, $plat_id);
                // suppression du fichier photo
                if (!empty($photo) && file_exists(ROOT_PATH . '/public/' . $photo)) {
                    @unlink(ROOT_PATH . '/public/' . $photo);
                }
                $_SESSION['success'] = "Le plat a été définitivement supprimé.";
            }
        } catch (\PDOException $e) {
            error_log("Erreur deletePlat : " . $e->getMessage());
            $_SESSION['error'] = "Impossible de supprimer le plat.";
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

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($fileTmp);

            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            $maxFileSize = 5 * 1024 * 1024;

            if (!isset($allowedMimeTypes[$mime])) {
                $_SESSION['error'] = "Format d'image non supporté (JPG, PNG ou WebP uniquement). Le plat n'a pas été créé.";
                header('Location: index.php?page=menu-management');
                exit();
            }

            if ($_FILES['photo']['size'] > $maxFileSize) {
                $_SESSION['error'] = "L'image ne doit pas dépasser 5 Mo. Le plat n'a pas été créé.";
                header('Location: index.php?page=menu-management');
                exit();
            }

            $safeExtension = $allowedMimeTypes[$mime];
            $newFileName = bin2hex(random_bytes(16)) . '.' . $safeExtension;

            $uploadDir = ROOT_PATH . '/public/assets/img/plats/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
                $_SESSION['error'] = "Impossible d'enregistrer l'image sur le serveur. Le plat n'a pas été créé.";
                header('Location: index.php?page=menu-management');
                exit();
            }

            $photoPath = '/public/assets/img/plats/' . $newFileName;
        }

        try {
            PlatManager::create($db, $titre, $description, $categorie, $photoPath, $allergenes);
            $_SESSION['success'] = "Le plat a été ajouté avec succès.";
            header('Location: index.php?page=menu-management&success=1');
            exit();
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erreur lors de l'ajout du plat : " . $e->getMessage();
            header('Location: index.php?page=menu-management');
            exit();
        }
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
            PlatManager::activate($db, (int)$plat_id);
            header('Location: index.php?page=menu-management&success=activated');
        } catch (\Exception $e) {
            header('Location: index.php?page=menu-management&error=activation_failed');
        }
        exit();
    }

    // function pour effacer un menu (soft delete si commande en cours, sinon choix soft delete ou suppression physique)
    public static function deleteMenu(\PDO $db, $menu_id = null)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        SecurityManager::validatePost('?page=menu-management');

        $menu_id = $menu_id ?? $_POST['menu_id'] ?? $_GET['menu_id'] ?? $_POST['id'] ?? null;
        $actionType = $_POST['action_type'] ?? 'delete'; // 'delete' ou 'disable'

        if (empty($menu_id)) {
            die("Erreur : Aucun ID de menu reçu !");
        }

        try {
            // Vérifier si le menu est lié à au moins une commande EN COURS

            $hasActiveOrders = MenuManager::countActiveOrders($db, (int)$menu_id) > 0;


            $db->beginTransaction();

            if ($hasActiveOrders) {
                // Si commande en cours : Soft delete obligatoire
                MenuManager::deactivate($db, (int)$menu_id);

                $db->commit();
                header('Location: index.php?page=menu-management&success=deactivated_active_orders');
            } else {
                // Pas de commande en cours : Application du choix (soft delete ou suppression définitive)
                if ($actionType === 'disable') {
                    MenuManager::deactivate($db, (int)$menu_id);


                    $db->commit();
                    header('Location: index.php?page=menu-management&success=deactivated');
                } else {
                    // Suppression définitive
                    MenuManager::deleteLinks($db, (int)$menu_id);
                    MenuManager::deleteHard($db, (int)$menu_id);

                    $db->commit();
                    header('Location: index.php?page=menu-management&success=deleted');
                }
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

            MenuManager::activate($db, (int)$menu_id);



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

        $csrfToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if (
            empty($sessionToken) ||
            empty($csrfToken) ||
            !hash_equals($sessionToken, $csrfToken)
        ) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Requête invalide ou session expirée.'
            ]);
            exit();
        }

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
                $fileSize = (int)$_FILES['photo']['size'];

                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($fileTmpPath);

                $allowedMimeTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp'
                ];

                if (!isset($allowedMimeTypes[$mime])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Format d’image non autorisé. Utilisez JPG, PNG ou WebP.'
                    ]);
                    exit();
                }

                if ($fileSize > 5 * 1024 * 1024) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'L’image ne doit pas dépasser 5 Mo.'
                    ]);
                    exit();
                }

                $safeExtension = $allowedMimeTypes[$mime];
                $newFileName = bin2hex(random_bytes(16)) . '.' . $safeExtension;
                $uploadDir = ROOT_PATH . '/public/assets/img/plats/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (!move_uploaded_file($fileTmpPath, $uploadDir . $newFileName)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Impossible d’enregistrer l’image.'
                    ]);
                    exit();
                }

                $photoPath = 'assets/img/plats/' . $newFileName;
            }
            try {
                $plat_id = PlatManager::create($db, $titre_plat, $description, $categorie, $photoPath, $allergenes);
                echo json_encode(['success' => true, 'plat_id' => $plat_id]);
                exit();
            } catch (\Exception $e) {
                error_log("Erreur createPlatAjax : " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Impossible d’enregistrer le plat.'
                ]);
                exit();
            }
        }
    }
    //function pour afficher la page de gestion des menus
    public static function MenuManagement(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        $menus = MenuManager::getMenusManagement($db);
        $all_plats = PlatManager::getAllWithMenuCount($db);
        $all_allergenes = MenuManager::getAllAllergenes($db);
        $all_themes = MenuManager::getAllThemes($db);
        $all_regimes = MenuManager::getAllRegimes($db);
        $specific_styles = [
            'assets/css/bootstrap/bootstrap.min.css',
            'assets/css/AdminEmployee/AdminEmployee.css'
        ];
        $specific_scripts = [
            'assets/javascript/AddMenu.js',
            'assets/javascript/tables.js'
        ];

        $userRole = $_SESSION['role_id'] ?? null;
        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/Views/layout/admin_header.php';
        } else {
            require_once ROOT_PATH . '/app/Views/layout/employee_header.php';
        }

        require_once ROOT_PATH . '/app/Views/StaffCommon/menu.management.view.php';

        if ($userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/Views/layout/admin_footer.php';
        } else {
            require_once ROOT_PATH . '/app/Views/layout/employee_footer.php';
        }
    }
}
