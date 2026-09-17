<?php

namespace App\Controllers\StaffCommon;

use App\Controllers\AuthController\Auth;
use App\Managers\OrderManager;
use App\Managers\MenuManager;
use App\Models\Order;
use App\Models\Menu;
use App\Helpers\MailService;
use App\Helpers\SecurityManager;
use App\Managers\LieuManager;
use App\Managers\UserManager;
// Class EditOrderController pour gérer l'édition des commandes (accessible aux admins et employés)
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
        $commande = OrderManager::getOrderWithDetails($db, (int)$commande_id);
        if (!$commande) {
            $_SESSION['error'] = "Commande introuvable.";
            header('Location: index.php?page=order-management');
            exit();
        }

        if (in_array($commande['statut'], ['annulee', 'terminee'], true)) {
            $_SESSION['error'] = "Cette commande ne peut plus être modifiée.";
            header('Location: index.php?page=order-management');
            exit();
        }

        $menuData = MenuManager::getById($db, $commande['menu_id']);
        $menus = MenuManager::get($db);

        $specific_styles = [
            'assets/css/AdminEmployee/AdminEmployee.css'
        ];
        $specific_scripts = [
            'assets/javascript/EditOrderCommon.js'
        ];
        $lieux = LieuManager::getAll($db);
        $userRole = $_SESSION['role_id'] ?? null;

        if ((int)$userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/Views/layout/admin_header.php';
        } else {
            require_once ROOT_PATH . '/app/Views/layout/employee_header.php';
        }

        require_once ROOT_PATH . '/app/Views/StaffCommon/edit.order.view.php';

        if ((int)$userRole === ROLE_ADMIN) {
            require_once ROOT_PATH . '/app/Views/layout/admin_footer.php';
        } else {
            require_once ROOT_PATH . '/app/Views/layout/employee_footer.php';
        }
    }

    // Fonction pour traiter la mise à jour de la commande (via l'API OpenRoute)
    public static function processUpdate(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);

        // Récupérer les données JSON envoyées par fetch
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $_POST = json_decode(file_get_contents('php://input'), true) ?? [];
        }
        SecurityManager::validateJson($_POST);

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

            try {

                $result = OrderManager::getOrderWithDetails($db, (int)$commande_id);

                if (!$result) {
                    throw new \Exception("Commande introuvable.");
                }

                if (in_array($result['statut'], ['annulee', 'terminee'], true)) {
                    throw new \Exception(
                        "Cette commande ne peut plus être modifiée."
                    );
                }


                $menu_id = (int)$result['menu_id'];
                $menuData = MenuManager::getById($db, $menu_id);
                if (!$menuData) {
                    throw new \Exception("Menu introuvable.");
                }

                $ancienneQuantite = (int)$result['nombre_personne'];

                $nouvelleQuantite = (int)$_POST['nombre_personne'];
                $delta = $nouvelleQuantite - $ancienneQuantite;
                $nombre_personne = (int)($_POST['nombre_personne'] ?? 0);
                $adresse = trim($_POST['adresse_livraison'] ?? '');
                $ville = trim($_POST['ville'] ?? '');
                $code_postal = trim($_POST['code_postal'] ?? '');
                $lat = (float)($_POST['lat'] ?? 0);
                $lon = (float)($_POST['lon'] ?? 0);



                $ancienneAdresse = trim((string)$result['adresse']);
                $ancienneVille = trim((string)$result['ville']);

                $adresseIdentique =
                    $adresse === $ancienneAdresse &&
                    $ville === $ancienneVille;

                $coordonneesInvalides = $lat === 0.0 || $lon === 0.0;

                if ($adresseIdentique && $coordonneesInvalides) {
                    $adresse = $result['adresse'];
                    $ville = $result['ville'];
                    $code_postal = $result['code_postal'];
                    $lat = (float)$result['latitude'];
                    $lon = (float)$result['longitude'];
                } elseif (!$adresseIdentique && $coordonneesInvalides) {
                    throw new \Exception(
                        "Veuillez sélectionner une adresse dans la liste proposée."
                    );
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



                $ancienneAdresse = trim((string)$result['adresse']);
                $ancienneVille = trim((string)$result['ville']);

                $adresseModifiee = $adresse !== $ancienneAdresse || $ville !== $ancienneVille;

                $lieuId = $adresseModifiee
                    ? LieuManager::getOrInsert($db, $adresse, $code_postal, $ville, $lat, $lon)
                    : (int)$result['lieu_prestation_id'];

                $fraisLivraison = 0.00;
                if ($adresseIdentique) {

                    $fraisLivraison = (float)($result['prix_total'] ?? 0) - (float)$menu->calculerPrix($nombre_personne) - (float)$depot;
                    if ($fraisLivraison < 0) $fraisLivraison = 0.00;
                } elseif ($lat && $lon) {
                    $distance = Order::calculerDistanceRouteVersClient($lat, $lon);
                    $fraisLivraison = Order::calculerFraisLivraisonParKm($ville, $distance);
                }

                $nouveauPrix = $menu->calculerTotal($nombre_personne, (float)$fraisLivraison, (float)$depot);

                // Mise à jour de la commande


                // Mise à jour du stock et de la commande
                $pdo->beginTransaction();

                try {
                    MenuManager::ajusterStock($db, $menu_id, $delta);

                    OrderManager::updateOrder($db, (int)$commande_id, [
                        'date_prestation'    => $_POST['date_prestation'],
                        'heure_livraison'    => $_POST['heure_livraison'],
                        'lieu_prestation_id' => $lieuId,
                        'prix_total'         => $nouveauPrix,
                        'pret_materiel'      => $rental,
                        'depot_garantie'     => $depot,
                        'nombre_personne'    => $nouvelleQuantite,
                    ]);

                    $pdo->commit();
                } catch (\Exception $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }

                    throw $e;
                }

                // Envoi de l'e-mail au client

                $client = UserManager::findById($db, (int)$result['utilisateur_id']);



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


                echo json_encode(['success' => true]);
                exit();
            } catch (\Exception $e) {

                error_log("Erreur processUpdate : " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de la commande.']);
                exit();
            }
        }
    }
    // Fonction pour recalculer le prix en AJAX (via l'API OpenRoute)
    public static function recalculerPrix(\PDO $db)
    {
        Auth::check([ROLE_ADMIN, ROLE_EMPLOYE]);
        $data = json_decode(file_get_contents('php://input'), true);
        SecurityManager::validateJson($data);

        $pret_materiel = !empty($data['pret_materiel']) ? 1 : 0;
        $montant_depot = ($pret_materiel === 1) ? DEPOT_GARANTIE_MATERIEL : 0.0;

        $quantite = (int)($data['nombre_personne'] ?? 0);
        $commande_id = (int)($_GET['commande_id'] ?? 0);

        $menu_id = OrderManager::getMenuIdByCommandeId($db, $commande_id);

        if (!$menu_id) {
            echo json_encode(['nouveau_prix' => '0.00', 'error' => 'Commande introuvable.']);
            exit();
        }


        $lat = (float)($data['lat'] ?? 0);
        $lon = (float)($data['lon'] ?? 0);
        $ville = trim($data['ville'] ?? '');

        $menuData = MenuManager::getById($db, $menu_id);

        if (!$menuData) {
            echo json_encode(['nouveau_prix' => '0.00', 'frais_livraison' => '0.00']);
            exit();
        }

        // Calcul dynamique par la route basé sur les coordonnées GPS reçues
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
