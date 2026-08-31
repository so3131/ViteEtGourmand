<?php

namespace App\Controllers\StaffCommon;
use App\Controllers\AuthController\Auth;
use App\Managers\OrderManager;
use App\Managers\MenuManager;
use App\Models\Order; 
use App\Models\Menu;
use App\Helpers\MailService;

class EditOrderController
{
    // Fonction pour afficher le formulaire de modification de la commande
    public static function renderEditForm(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
       
        $commande_id = $_GET['commande_id'] ?? null;
        if (!$commande_id) {
            $_SESSION['error'] = "Identifiant de commande manquant.";
            header('Location: index.php?page=order-management');
            exit();
        }

        // Récupérer les données de la commande avec l'adresse associée
        $stmt = $db->prepare("SELECT c.*, m.titre as menu_titre, l.adresse, l.ville, l.code_postal, l.latitude, l.longitude 
                              FROM vg_commande c 
                              LEFT JOIN vg_menu m ON c.menu_id = m.menu_id 
                              LEFT JOIN vg_lieu_prestation l ON c.lieu_prestation_id = l.id 
                              WHERE c.commande_id = :id");
        $stmt->execute(['id' => $commande_id]);
        $commande = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$commande) {
            $_SESSION['error'] = "Commande introuvable en base de données.";
            header('Location: index.php?page=order-management');
            exit();
        }

        $menuData = MenuManager::getById($db, $commande['menu_id']);
        $menus = MenuManager::get($db);
        
        $specific_styles = [
            'assets/css/Admin/OrderManagement.css',
            'assets/css/Admin/AdminEmployee.css'
        ];
        $specific_scripts = [
            'assets/javascript/EditOrderCommon.js'
        ];
        $stmtLieux = $db->query("SELECT * FROM vg_lieu_prestation");
$lieux = $stmtLieux->fetchAll(\PDO::FETCH_ASSOC);
        $userRole = $_SESSION['role_id'] ?? null;

        if ((int)$userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_header.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_header.php';
        }
        
        require_once ROOT_PATH . '/app/views/StaffCommon/edit.order.view.php';

        if ((int)$userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/views/layout/admin_footer.php';
        } else {
            require_once ROOT_PATH . '/app/views/layout/employee_footer.php';
        }
    }

    // Fonction pour traiter la mise à jour de la commande avec l'API OpenRoute
    // Fonction pour traiter la mise à jour de la commande avec l'API OpenRoute
public static function processUpdate(\PDO $db)
{
    Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);

    // Récupération des données JSON envoyées par fetch
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (str_contains($contentType, 'application/json')) {
        $_POST = json_decode(file_get_contents('php://input'), true) ?? [];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $commande_id = $_GET['commande_id'] ?? $_POST['commande_id'] ?? null;

        if (!$commande_id) {
            echo json_encode(['success' => false, 'message' => 'Identifiant de commande manquant.']);
            exit();
        }

        $mode_contact = $_POST['mode_contact'] ?? null;
        $motif = $_POST['motif'] ?? null;

        if (empty($mode_contact) || empty($motif)) {
            echo json_encode(['success' => false, 'message' => 'Le mode de contact et le motif sont obligatoires.']);
            exit();
        }

        $rental = !empty($_POST['pret_materiel']) ? 1 : 0;
        $depot = ($rental === 1) ? DEPOT_GARANTIE_MATERIEL : 0.0;

        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        try {
            $sqlCheck = "SELECT c.utilisateur_id, c.statut, c.prix_total 
                         FROM vg_commande c
                         WHERE c.commande_id = :orderID";

            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->execute(['orderID' => $commande_id]);
            $result = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

            if (!$result) {
                throw new \Exception("Commande non trouvée.");
            }

            $menu_id = (int)($_POST['menu_id'] ?? 0);
            $menuData = MenuManager::getById($db, $menu_id);
            if (!$menuData) {
                throw new \Exception("Menu introuvable.");
            }

            $nombre_personne = (int)($_POST['nombre_personne'] ?? 0);
            $adresse = trim($_POST['adresse_livraison'] ?? '');
            $ville = trim($_POST['ville'] ?? '');
            $code_postal = trim($_POST['code_postal'] ?? '');
            $lat = (float)($_POST['lat'] ?? 0);
            $lon = (float)($_POST['lon'] ?? 0);

            if (empty($adresse) || empty($ville) || empty($lat) || empty($lon)) {
                throw new \Exception("L'adresse de livraison et ses coordonnées géographiques sont obligatoires.");
            }

            $delaiCommande = (int)($menuData['delai_commande'] ?? 0);
            $dateMinimale = new \DateTime('today');
            $dateMinimale->modify('+' . $delaiCommande . ' days');

            if (empty($_POST['date_prestation'])) {
                throw new \Exception("La date de prestation est obligatoire.");
            }

            $dateSelectionnee = new \DateTime($_POST['date_prestation']);
            $dateSelectionnee->setTime(0, 0, 0);

            if ($dateSelectionnee < $dateMinimale) {
                throw new \Exception("La date de prestation doit être au minimum à J+" . $delaiCommande . ".");
            }

            $menu = new Menu(
                (int)$menuData['menu_id'],
                (int)$menuData['nombre_personne_minimum'],
                (string)$menuData['titre'],
                (string)$menuData['description_menu'],
                (float)$menuData['prix_par_personne'],
                (int)$menuData['quantite_restante'],
                isset($menuData['theme_id']) ? (int)$menuData['theme_id'] : null,   
                isset($menuData['regime_id']) ? (int)$menuData['regime_id'] : null, 
                $menuData['theme_libelle'] ?? '',
                $menuData['regime_libelle'] ?? ''
            );

            if (!$menu->estQuantiteValide($nombre_personne)) {
                throw new \Exception("Le nombre de personnes doit être au minimum de " . $menu->getMinimumRequis());
            }

            // Gestion ou insertion du lieu de prestation
            $stmtFindLieu = $pdo->prepare("SELECT id FROM vg_lieu_prestation WHERE latitude = :lat AND longitude = :lon");
            $stmtFindLieu->execute(['lat' => $lat, 'lon' => $lon]);
            $lieuId = $stmtFindLieu->fetchColumn();

            if (!$lieuId) {
                $stmtInsertLieu = $pdo->prepare("INSERT INTO vg_lieu_prestation (adresse, ville, code_postal, latitude, longitude) VALUES (:adresse, :ville, :code_postal, :lat, :lon)");
                $stmtInsertLieu->execute([
                    'adresse'     => $adresse,
                    'ville'       => $ville,
                    'code_postal' => $code_postal,
                    'lat'         => $lat,
                    'lon'         => $lon
                ]);
                $lieuId = $pdo->lastInsertId();
            }

            $fraisLivraison = 0.00;
            if ($lat && $lon) {
                $distance = Order::calculerDistanceRouteVersClient($lat, $lon);
                $fraisLivraison = Order::calculerFraisLivraisonParKm($ville, $distance);
            }

            $nouveauPrix = $menu->calculerTotal($nombre_personne, (float)$fraisLivraison, (float)$depot);

            // Mise à jour de la commande
            $sqlUpdate = "UPDATE vg_commande 
                          SET date_prestation = :date, 
                              heure_livraison = :heure, 
                              menu_id = :menu_id, 
                              lieu_prestation_id = :lieu_id, 
                              prix_total = :prix,
                              pret_materiel = :materiel, 
                              depot_garantie = :depot
                          WHERE commande_id = :id";

            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([
                'date'     => $_POST['date_prestation'],
                'heure'    => $_POST['heure_livraison'],
                'menu_id'  => $menu_id,
                'lieu_id'  => $lieuId,
                'prix'     => $nouveauPrix,
                'materiel' => $rental,
                'depot'    => $depot,
                'id'       => $commande_id
            ]);

            // Envoi de l'e-mail au client
            $stmtUser = $pdo->prepare("SELECT email, prenom, nom FROM vg_utilisateur WHERE utilisateur_id = :uid");
            $stmtUser->execute(['uid' => $result['utilisateur_id']]);
            $client = $stmtUser->fetch(\PDO::FETCH_ASSOC);

            if ($client && !empty($client['email'])) {
                $modeContactLibelle = ($mode_contact === 'tel') ? 'Appel GSM' : 'Email';
                
                $sujet = "Modification de votre commande n°" . $commande_id;
                $message = "Bonjour " . $client['prenom'] . ",\n\n";
                $message .= "Votre commande a été modifiée par notre équipe.\n";
                $message .= "Mode de contact utilisé : " . $modeContactLibelle . "\n";
                $message .= "Motif de la modification : " . $motif . "\n\n";
                $message .= "Vous pouvez consulter les détails mis à jour depuis votre espace client.\n\n";
                $message .= "Cordialement,\nL'équipe Vite Gourmand";

                MailService::sendEmail($client['email'], $client['prenom'], $sujet, $message);
            }

            // Réponse JSON de succès pour le JavaScript
            echo json_encode(['success' => true]);
            exit();

        } catch (\Exception $e) {
            // Réponse JSON d'erreur pour le JavaScript
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit();
        }
    }
}
    // Fonction pour recalculer le prix en AJAX (via l'API OpenRoute)
    public static function recalculerPrix(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        $data = json_decode(file_get_contents('php://input'), true);
        
        $pret_materiel = !empty($data['pret_materiel']) ? 1 : 0;
        $montant_depot = ($pret_materiel === 1) ? DEPOT_GARANTIE_MATERIEL : 0.0;
        
        $quantite = (int)($data['nombre_personne'] ?? 0);
        $menu_id = (int)($data['menu_id'] ?? 0);
        $lat = (float)($data['lat'] ?? 0);
        $lon = (float)($data['lon'] ?? 0);
        $ville = trim($data['ville'] ?? '');

        $menuData = MenuManager::getById($db, $menu_id);

        if (!$menuData) {
            echo json_encode(['nouveau_prix' => '0.00', 'frais_livraison' => '0.00']);
            exit();
        }

        // Calcul dynamique par la route basé sur les coordonnées GPS reçues du front
        $frais = 0.00;
        if ($lat && $lon) {
            $distance = Order::calculerDistanceRouteVersClient($lat, $lon);
            $frais = Order::calculerFraisLivraisonParKm($ville, $distance);
        }

        $menu = new Menu(
            (int)$menuData['menu_id'],
            (int)$menuData['nombre_personne_minimum'],
            (string)$menuData['titre'],
            (string)$menuData['description_menu'],
            (float)$menuData['prix_par_personne'],
            (int)$menuData['quantite_restante'],
            isset($menuData['theme_id']) ? (int)$menuData['theme_id'] : null,
            isset($menuData['regime_id']) ? (int)$menuData['regime_id'] : null,
            $menuData['theme_libelle'] ?? '',
            $menuData['regime_libelle'] ?? ''
        );

        $total = $menu->calculerTotal($quantite, (float)$frais, $montant_depot);

        echo json_encode([
            'nouveau_prix' => number_format($total, 2, '.', ''),
            'frais_livraison' => number_format($frais, 2, '.', '')
        ]);
        exit();
    }

    public static function cancelEditOrder(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        header('Location: index.php?page=order-management');
        exit();
    }
}