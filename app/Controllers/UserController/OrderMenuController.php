<?php

namespace App\Controllers\UserController;


require_once dirname(__DIR__, 2) . '/config/Constants.php';
require_once ROOT_PATH . '/app/helpers/Function.php';

use App\Models\Menu;
use App\Managers\MenuManager;
use App\Controllers\AuthController\Auth;
use App\Managers\LieuManager;
use App\Models\Order;
use App\Managers\OrderManager;
use App\Helpers\MailService;
use App\Managers\MongoStatsManager;
use App\Helpers\SecurityManager;

// class OrderMenuController pour gérer le processus de commande d'un menu
class OrderMenuController
{
    //function pour afficher le processus de commande d'un menu avec plusieurs étapes(tunnel) et gérer les données de session
    public static function orderMenu(\PDO $db, ?int $menuID = 0)
    {
        $menuID = (int)$menuID;
        if ($menuID === 0 && isset($_GET['menu_id'])) {
            $menuID = (int)$_GET['menu_id'];
        }

        Auth::check([ROLE_USER]);

        // Initialisation des variables
        $menu = null;
        $menuInfo = null;
        $total_general = 0;
        $order = [];
        $Discount = false;

        // Définition initiale du step
        $step = isset($_GET['step']) ? (int)$_GET['step'] : 0;

        // Preparation des données pour la vue
        $user = (object) [
            'user_id' => $_SESSION['user_id'] ?? null,
            'nom'     => $_SESSION['nom'] ?? 'Inconnu',
            'prenom'  => $_SESSION['prenom'] ?? 'Inconnu'
        ];

        $orderData = $_SESSION['current_order'] ?? [];
        $prestation = $orderData['prestation'] ?? [];
        $menuData = $orderData['menu'] ?? [];
        $totalCommande = $orderData['total_final'] ?? 0;

        // Chargement des données nécessaires pour le step 0 et le step 1
        $menuInfo = MenuManager::getById($db, $menuID);
        // Bloquer l'accès au tunnel de commande si le menu est désactivé
        if ($menuID > 0 && (!$menuInfo || (int)($menuInfo['is_active'] ?? 1) === 0)) {
            error_message("Ce menu n'est plus disponible.");
            header('Location: index.php?page=search');
            exit();
        }
        // Bloquer l'accès au tunnel de commande si le menu est en rupture de stock
        if ($menuID > 0 && (!$menuInfo || (int)($menuInfo['quantite_restante'] ?? 0) <= 0)) {
            error_message("Ce menu est temporairement en rupture de stock.");
            header('Location: index.php?page=search');
            exit();
        }
        $tousLesLieux = LieuManager::getAll($db);
        $delaiCommande = (int)($menuInfo['delai_commande'] ?? 0);
        $dateMinimale = ($delaiCommande > 0) ? (new \DateTime('today'))->modify('+' . $delaiCommande . ' days')->format('Y-m-d') : null;
        $timetables = \App\Models\Timetable::ShowTimetable($db);

        //Lancement des différents steps du tunnel de commande
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            SecurityManager::validatePost(
                '?page=order-menu&menu_id=' . (int)$menuID . '&step=' . $step
            );

            switch ($step) {

                // --- STEP 0 : Récupération des informations de prestation et validation de la date ---
                case 0:

                    $_SESSION['current_order'] = [];
                    $menu = MenuManager::getById($db, $menuID);
                    if (!isset($_SESSION['user_id'])) {
                        header('Location: index.php?page=login');
                        exit();
                    }

                    $user = (object) [
                        'nom'       => $_SESSION['nom'],
                        'prenom'    => $_SESSION['prenom'],
                        'email'     => $_SESSION['email'],
                        'telephone' => $_SESSION['telephone']
                    ];

                    $datePrestation = $_POST['date_prestation'] ?? '';
                    $ville = $_POST['ville'] ?? '';
                    $codePostal = $_POST['code_postal'] ?? '';
                    $adresseLivraison = $_POST['adresse_livraison'] ?? '';

                    $latClient = (float)($_POST['lat'] ?? 0);
                    $lonClient = (float)($_POST['lon'] ?? 0);

                    $menuInfo = MenuManager::getById($db, $menuID);

                    if (!$menuInfo || (int)($menuInfo['is_active'] ?? 1) === 0) {
                        error_message("Ce menu n'est plus disponible.");
                        header('Location: index.php?page=search');
                        exit();
                    }
                    if (!$menuInfo) {
                        error_message("Menu introuvable.");
                        header('Location: index.php?page=home');
                        exit();
                    }

                    try {
                        $delaiCommande = (int)($menuInfo['delai_commande'] ?? 0);
                        $dateMinimale = new \DateTime('today');
                        $dateMinimale->modify('+' . $delaiCommande . ' days');

                        $dateSelectionnee = new \DateTime($datePrestation);
                        $dateSelectionnee->setTime(0, 0, 0);

                        if ($dateSelectionnee < $dateMinimale) {
                            error_message("La date de prestation doit être au minimum à J+" . $delaiCommande . ".");
                            header('Location: index.php?page=order-menu&step=0&menu_id=' . $menuID);
                            exit();
                        }
                    } catch (\Exception $e) {
                        error_message("Format de date invalide.");
                        header('Location: index.php?page=order-menu&step=0&menu_id=' . $menuID);
                        exit();
                    }
                    if (
                        empty($datePrestation) || empty($ville) || empty($codePostal)
                        || $latClient === 0.0 || $lonClient === 0.0
                    ) {
                        error_message("Veuillez remplir la date et sélectionner une adresse de livraison complete.");
                        header('Location: index.php?page=order-menu&step=0&menu_id=' . $menuID);
                        exit();
                    }
                    // Une adresse de livraison doit commencer par un numéro de rue
                    if (!preg_match('/^\s*\d+/', $adresseLivraison)) {
                        error_message("Adresse de livraison invalide : un numéro de rue est requis.");
                        header('Location: index.php?page=order-menu&step=0&menu_id=' . $menuID);
                        exit();
                    }


                    // Calcul des frais de livraison si la ville n'est pas Bordeaux
                    $fraisLivraison = 0.00;
                    if (mb_strtolower(trim($ville)) !== 'bordeaux' && $latClient && $lonClient) {
                        $distanceKm = Order::calculerDistanceRouteVersClient($latClient, $lonClient);
                        $fraisLivraison = Order::calculerFraisLivraisonParKm($ville, $distanceKm);
                    }

                    // Stockage complet dans la session
                    $_SESSION['current_order']['prestation'] = [
                        'nom' => $user->nom,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'gsm' => $user->telephone,
                        'date_prestation' => $datePrestation,
                        'heure_livraison' => $_POST['heure_livraison'] ?? '12:00',
                        'ville' => $ville,
                        'code_postal' => $codePostal,
                        'adresse_livraison' => $adresseLivraison,
                        'lat' => $latClient,  // <-- INDISPENSABLE
                        'lon' => $lonClient,  // <-- INDISPENSABLE
                        'frais_livraison' => round($fraisLivraison, 2),
                        'nom_menu' => $menu['titre'] ?? 'Menu inconnu'
                    ];

                    session_write_close();
                    header('Location: index.php?page=order-menu&menu_id=' . $menuID . '&step=1');
                    exit();
                    break;

                // --- STEP 1 : Récupération de la quantité et validation du minimum requis ---
                case 1:
                    $menuID = $_GET['menu_id'] ?? $_SESSION['current_order']['menu']['menu_id'] ?? null;

                    if (!$menuID) {
                        error_message("Menu introuvable.");
                        header('Location: index.php?page=home');
                        exit();
                    }
                    $menuInfo = MenuManager::getById($db, $menuID);
                    if (!$menuInfo) {
                        error_message("Menu introuvable.");
                        header('Location: index.php?page=home');
                        exit();
                    }

                    $menu = new Menu(
                        (int) $menuInfo['menu_id'],
                        (int) $menuInfo['nombre_personne_minimum'],
                        (string) $menuInfo['titre'],
                        (string) ($menuInfo['description_menu'] ?? ''),
                        (float) $menuInfo['prix_par_personne'],
                        (int) ($menuInfo['quantite_restante'] ?? 0),
                        (int) ($menuInfo['theme_id'] ?? 0),
                        (int) ($menuInfo['regime_id'] ?? 0),
                        (string) ($menuInfo['theme_libelle'] ?? ''),
                        (string) ($menuInfo['regime_libelle'] ?? '')
                    );

                    $quantite = (int)($_POST['nombre_personne'] ?? 0);

                    if ($menu->estQuantiteValide($quantite)) {
                        $_SESSION['current_order']['menu'] = [
                            'menu_id' => $menu->menu_id,
                            'titre' => $menu->titre,
                            'prix_unitaire' => $menu->prix_par_personne,
                            'quantite' => $quantite
                        ];

                        $prixMenuTotal = $menu->calculerPrix($quantite);
                        $_SESSION['current_order']['menu']['prix_menu_total'] = $prixMenuTotal;

                        header('Location: index.php?page=order-menu&menu_id=' . $menuID . '&step=2');
                        exit();
                    } else {
                        error_message("Minimum requis: " . $menu->getMinimumRequis());
                        header('Location: index.php?page=order-menu&menu_id=' . $menuID . '&step=1');
                        exit();
                    }
                    break;

                // --- STEP 2 : Gestion de la location de matériel et validation des conditions ---
                case 2:

                    if (!isset($_SESSION['current_order']['menu'])) {
                        error_message("Votre session a expiré ou le menu n'a pas été sélectionné.");
                        header('Location: index.php?page=home');
                        exit();
                    }
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $needRental = (($_POST['location_materiel'] ?? "0") === "1");

                        if ($needRental && !isset($_POST['accept_conditions'])) {
                            error_message("Vous devez accepter les conditions pour louer du matériel.");
                            header('Location: index.php?page=order-menu&menu_id=' . $menuID . '&step=2');
                            exit();
                        }

                        $_SESSION['current_order']['prestation']['location_materiel'] = $needRental;
                        $_SESSION['current_order']['prestation']['depot_garantie'] = $needRental ? DEPOT_GARANTIE_MATERIEL : 0.00;

                        header('Location: index.php?page=order-menu&menu_id=' . $menuID . '&step=3');
                        exit();
                    }
                    break;

                // --- STEP 3 : Recalcul et mise à jour des frais de livraison ---
                case 3:
                    if (!isset($_SESSION['current_order']['menu'])) {
                        error_message("Votre session a expiré ou le menu n'a pas été sélectionné.");
                        header('Location: index.php?page=home');
                        exit();
                    }

                    $prestationData = $_SESSION['current_order']['prestation'] ?? [];
                    $ville = $prestationData['ville'] ?? '';
                    $latClient = $prestationData['lat'] ?? null;
                    $lonClient = $prestationData['lon'] ?? null;

                    if (mb_strtolower(trim($ville)) !== 'bordeaux' && $latClient !== null && $lonClient !== null) {
                        $distanceKm = Order::calculerDistanceRouteVersClient((float)$latClient, (float)$lonClient);
                        $fraisLivraisonRecalcules = Order::calculerFraisLivraisonParKm($ville, $distanceKm);

                        $_SESSION['current_order']['prestation']['frais_livraison'] = round($fraisLivraisonRecalcules, 2);
                    }
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        header('Location: index.php?page=order-menu&menu_id=' . $menuID . '&step=4');
                        exit();
                    }
                    break;

                // --- STEP 4 : Recapitulatif et confirmation finale et redirection vers le paiement ---
                case 4:



                    break;

                // --- STEP 5 : Paiement et finalisation de la commande ---
                case 5:

                    if (!isset($_SESSION['current_order'])) {
                        header('Location: index.php?page=home');
                        exit();
                    }

                    $user = (object) [
                        'user_id'   => $_SESSION['user_id'] ?? null,
                        'nom'       => $_SESSION['nom'] ?? 'Inconnu',
                        'prenom'    => $_SESSION['prenom'] ?? 'Inconnu',
                        'email'     => $_SESSION['email'] ?? ''
                    ];

                    if (!$user->user_id) {
                        error_message("Session utilisateur invalide.");
                        header('Location: index.php?page=login');
                        exit();
                    }

                    $prestation = $_SESSION['current_order']['prestation'];
                    $menuData = $_SESSION['current_order']['menu'];
                    $totalCommande = $_SESSION['current_order']['total_final'];

                    // Paiement simulé et enregistrement de la commande
                    try {
                        if (OrderManager::createOrderFromData($db, $user, $menuData, $prestation, $totalCommande)) {

                            // === Ajout de la commande dans MongoDB pour les statistiques ===


                            try {
                                //Récupération des données depuis la session
                                $prixMenus = ($menuData['prix_unitaire'] ?? 0) * ($menuData['quantite'] ?? 0);
                                $fraisLivraison = $prestation['frais_livraison'] ?? 0.00;

                                // Récupération de la caution gérée au case 2
                                $montantCaution = $prestation['depot_garantie'] ?? 0.00;

                                $caReelEntreprise = $prixMenus + $fraisLivraison;
                                $montantTotalPaye = $caReelEntreprise + $montantCaution;

                                //Insertion dans MongoDB
                                $mongoManager = new MongoStatsManager();
                                $mongoManager->insertOrderHistory([
                                    'id' => null,
                                    'client_nom' => $user->nom . ' ' . $user->prenom,
                                    'client_email' => $user->email,
                                    'items' => [
                                        [
                                            'menu_id' => $menuData['menu_id'],
                                            'titre' => $menuData['titre'] ?? 'Menu',
                                            'quantite' => $menuData['quantite'] ?? 0,
                                            'prix_unitaire' => $menuData['prix_unitaire'] ?? 0,
                                            'sous_total' => $prixMenus
                                        ]
                                    ],
                                    'montant_details' => [
                                        'prix_menus' => $prixMenus,
                                        'frais_livraison' => $fraisLivraison,
                                        'depot_garantie_caution' => $montantCaution
                                    ],
                                    'ca_reel_entreprise' => $caReelEntreprise,
                                    'montant_total_paye_par_client' => $montantTotalPaye
                                ]);
                            } catch (\Exception $mongoError) {
                                error_log("Erreur synchro MongoDB : " . $mongoError->getMessage());
                            }

                            // Tentative d'envoyer l'email, mais ne bloque pas la redirection si échec
                            try {
                                $orderDetails = [
                                    'nom' => $user->nom,
                                    'prenom' => $user->prenom,
                                    'menu'   => $menuData['titre'] ?? 'Menu inconnu',
                                    'quantite' => $menuData['quantite'] ?? 0,
                                    'prix_total' => $totalCommande,
                                    'date_prestation' => $prestation['date_prestation'] ?? 'Non défini',
                                    'heure_livraison' => $prestation['heure_livraison'] ?? 'Non défini',
                                    'lieu' => $prestation['ville'] ?? 'Non défini',
                                ];

                                // Appeller la fonction avec le tableau
                                MailService::sendOrderConfirmationEmail($user->email, $orderDetails);
                            } catch (\Exception $mailError) {
                                error_log("Erreur envoi email confirmation : " . $mailError->getMessage());
                            }

                            unset($_SESSION['current_order']);
                            header('Location: index.php?page=order-success');
                            exit();
                        }
                    } catch (\Exception $e) {
                        // Erreur lors de la création de la commande
                        error_log("Echec création commande pour user " . $user->user_id . ": " . $e->getMessage());

                        error_message($e->getMessage());
                        header('Location: index.php?page=order-menu&step=4&error=' . urlencode($e->getMessage()));
                        exit();
                    }
                    break;
            }
        }

        // Calculer le total général si les données sont présentes
        $menuID = (int)($_GET['menu_id'] ?? $_SESSION['current_order']['menu']['menu_id'] ?? 0);
        $menuInfo = ($menuID > 0) ? MenuManager::getById($db, $menuID) : null;
        $menu = null;

        if ($menuInfo) {
            $menu = new Menu(
                (int)$menuInfo['menu_id'],
                (int)$menuInfo['nombre_personne_minimum'],
                (string)$menuInfo['titre'],
                (string)($menuInfo['description_menu'] ?? ''),
                (float)$menuInfo['prix_par_personne'],
                (int)($menuInfo['quantite_restante'] ?? 0),
                (int)($menuInfo['theme_id'] ?? 0),
                (int)($menuInfo['regime_id'] ?? 0),
                (string)($menuInfo['theme_libelle'] ?? ''),
                (string)($menuInfo['regime_libelle'] ?? '')
            );
        }

        $orderData = $_SESSION['current_order'] ?? [];
        $total_general = 0;

        if ($menu && isset($orderData['menu']['quantite'])) {
            $total_general = $menu->calculerTotal(
                (int)$orderData['menu']['quantite'],
                (float)($orderData['prestation']['frais_livraison'] ?? 0),
                (float)($orderData['prestation']['depot_garantie'] ?? 0)
            );
            $_SESSION['current_order']['total_final'] = $total_general;
        }

        if (!empty($orderData['menu']['quantite']) && $menu !== null) {
            $Discount = $menu->hasDiscount((int)$orderData['menu']['quantite']);
        }

        // Préparer les données pour la vue
        $data = [
            'step' => (int)($_GET['step'] ?? 0),
            'menuID' => $menuID,
            'menu' => $menu,
            'menuInfo' => $menuInfo,
            'db' => $db,
            'total_general' => $total_general,
            'order' => $orderData,
            'hasDiscount' => $Discount,
            'tousLesLieux' => $tousLesLieux,
            'dateMinimale' => $dateMinimale,
            'delaiCommande' => $delaiCommande,
            'timetables' => $timetables
        ];

        $step = $data['step'];
        $menuID = $data['menuID'];
        $menu = $data['menu'];
        $menuInfo = $data['menuInfo'];
        $db = $data['db'];
        $total_general = $data['total_general'];
        $order = $data['order'];
        $Discount = $data['hasDiscount'];
        $tousLesLieux = $data['tousLesLieux'];
        $dateMinimale = $data['dateMinimale'];
        $delaiCommande = $data['delaiCommande'];
        $timetables = $data['timetables'];

        /** @var int $step */
        /** @var int $menuID */
        /** @var Menu|null $menu */
        /** @var array $menuInfo */
        /** @var \PDO $db */
        /** @var float $total_general */
        /** @var array $order */
        /** @var bool $Discount */
        /** @var array $tousLesLieux */
        /** @var string|null $dateMinimale */
        /** @var int $delaiCommande */
        /** @var array $timetables */

        $specific_scripts = ["assets/javascript/orderMenu.js"];
        $vue = ROOT_PATH . '/app/Views/user/order/bookmenu_step' . $step . '.view.php';
        require_once ROOT_PATH . '/app/Views/layout/header.php';
        if (file_exists($vue)) {
            require_once $vue;
        } else {
            header('Location: index.php?page=404');
            exit();
        }
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
    }
    //function pour afficher les frais de livraison estimés en fonction du lieu sélectionné par l'utilisateur
    public static function ajaxFraisLivraison($db)
    {
        $ville = $_GET['ville'] ?? '';
        $latClient = $_GET['lat'] ?? null;
        $lonClient = $_GET['lon'] ?? null;

        $frais = 0.00;

        if ($latClient && $lonClient) {
            $distanceKm = Order::calculerDistanceRouteVersClient((float)$latClient, (float)$lonClient);
            $frais = Order::calculerFraisLivraisonParKm($ville, $distanceKm);
        }
        header('Content-Type: application/json');
        echo json_encode(['frais' => $frais]);
        exit;
    }
    //function pour afficher la page de succès après la validation d'une commande
    public static function orderSuccess(\PDO $db)
    {
        // Affiche simplement une vue de succès
        require_once ROOT_PATH . '/app/Views/layout/header.php';
        require_once ROOT_PATH . '/app/Views/user/order/order-success.view.php';
        require_once ROOT_PATH . '/app/Views/layout/footer.php';
    }
    //function pour annuler une commande en cours et nettoyer la session
    public static function cancelOrder()
    {
        unset($_SESSION['current_order']);
        header('Location: index.php?page=search');
        exit();
    }
}
