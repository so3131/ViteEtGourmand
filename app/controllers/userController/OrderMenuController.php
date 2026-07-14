<?php

namespace App\Controllers\UserController;

require_once dirname(__DIR__, 2) . '/config/constants.php';
require_once ROOT_PATH . '/app/helpers/Function.php';

use App\Models\Menu;
use App\Managers\MenuManager;
use App\Controllers\AuthController\Auth;
use App\Managers\LieuManager;
use App\Models\Order;
use App\Managers\OrderManager;


class OrderMenuController
{
    public static function orderMenu(\PDO $db, ?int $menuID = 0)
    {
        $menuID = (int)$menuID;
        if ($menuID === 0 && isset($_GET['menu_id'])) {
            $menuID = (int)$_GET['menu_id'];
        }

        Auth::check([ROLE_USER]);


        // INITIALISATION DE TOUTES LES VARIABLES
        $menu = null;
        $menuInfo = null;
        $total_general = 0;
        $order = [];


        // 1. Définition constante du step
        $step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

        // 2. PRÉPARATION GLOBALE DES DONNÉES (Pour éviter les erreurs de variable non définie)
        $user = (object) [
            'user_id' => $_SESSION['user_id'] ?? null,
            'nom'     => $_SESSION['nom'] ?? 'Inconnu',
            'prenom'  => $_SESSION['prenom'] ?? 'Inconnu'
        ];

        $orderData = $_SESSION['current_order'] ?? [];
        $prestation = $orderData['prestation'] ?? [];
        $menuData = $orderData['menu'] ?? [];
        $totalCommande = $orderData['total_final'] ?? 0;

        // 1. TRAITEMENT DES DONNÉES (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            switch ($step) {

                case 0:

                    // 1. Gestion du POST de l'étape 0
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                        $lieuId = (int)($_POST['lieu_id'] ?? 0);

                        $lieu = LieuManager::getById($db, $lieuId);

                        try {
                            $dateSelectionnee = new \DateTime($datePrestation);
                            $dateAujourdhui = new \DateTime('today'); // 'today' met l'heure à 00:00:00 pour une comparaison juste sur le jour

                            if ($dateSelectionnee < $dateAujourdhui) {
                                error_message("La date de prestation ne peut pas être dans le passé.");
                                header('Location: index.php?page=order-menu&step=0');
                                exit();
                            }
                        } catch (\Exception $e) {
                            error_message("Format de date invalide.");
                            header('Location: index.php?page=order-menu&step=0');
                            exit();
                        }

                        $menu = MenuManager::getById($db, $menuID);

                        // 3. Validation
                        if (empty($datePrestation) || !$lieu) {
                            error_message("Veuillez remplir la date et choisir un lieu de prestation valide.");
                            header('Location: index.php?page=order-menu&step=0&menu_id=' . $menuID);
                            exit();
                        }

                        $fraisLivraison = Order::calculerFraisLivraison($lieu['ville'], (float)$lieu['distance_bordeaux']);

                        // 5. Stockage complet dans la session
                        $_SESSION['current_order']['prestation'] = [
                            'nom' => $user->nom,
                            'prenom' => $user->prenom,
                            'email' => $user->email,
                            'gsm' => $user->telephone,
                            'date_prestation' => $datePrestation,
                            'heure_livraison' => $_POST['heure_livraison'] ?? '12:00',
                            'lieu' => $lieu,
                            'frais_livraison' => $fraisLivraison,
                            'nom_menu' => $menu['titre'] ?? 'Menu inconnu',
                            'adresse_precise' => $_POST['adresse_precise'] ?? ''
                        ];
                        session_write_close();
                        header('Location: index.php?page=order-menu&menu_id=' . $menuID . '&step=1');
                        exit();
                    }

                    break;

                case 1:

                    // 1. Récupération des données du menu via MenuManager
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


                    // 3. Création d'une instance de Menu pour utiliser sa logique métier
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

                    // 4. Récupération de la quantité choisie par l'utilisateur
                    $quantite = (int)($_POST['nombre_personne'] ?? 0);
                    error_log("--- DEBUG ---");
                    error_log("Quantité reçue via POST: " . $quantite);
                    error_log("Minimum requis via getter: " . $menu->getMinimumRequis());
                    if ($menu->estQuantiteValide($quantite)) {
                        $_SESSION['current_order']['menu'] = [
                            'menu_id' => $menu->menu_id,
                            'titre' => $menu->titre,
                            'prix_unitaire' => $menu->prix_par_personne,
                            'quantite' => $quantite
                        ];
                        $quantite = (int)$_POST['nombre_personne'];
                        // Calcul du prix total avec la logique métier du menu
                        $prixMenuTotal = $menu->calculerPrix($quantite);

                        $_SESSION['current_order']['menu']['prix_menu_total'] = $prixMenuTotal;

                        if (!empty($menuID)) {
                            session_write_close();
                            header('Location: index.php?page=order-menu&menu_id=' . $menuID . '&step=2');
                            exit();
                        } else {
                            error_log("Erreur : Tentative de redirection vers étape 2 sans menuID.");
                            header('Location: index.php?page=home');
                            exit();
                        }
                    }
                    break;

                case 2:

                    if (!isset($_SESSION['current_order']['menu'])) {
                        error_message("Votre session a expiré ou le menu n'a pas été sélectionné.");
                        header('Location: index.php?page=home');
                        exit();
                    }
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $needRental = (($_POST['location_materiel'] ?? "0") === "1");

                        // 1. Validation métier
                        if ($needRental && !isset($_POST['accept_conditions'])) {
                            error_message("Vous devez accepter les conditions pour louer du matériel.");
                            header('Location: index.php?page=order-menu&menu_id=' . $menuID . '&step=2');
                            exit();
                        }

                        // 2. Mise à jour de la session uniquement après validation
                        $_SESSION['current_order']['prestation']['location_materiel'] = $needRental;
                        $_SESSION['current_order']['prestation']['depot_garantie'] = $needRental ? 600.00 : 0.00;

                        // 3. Redirection
                        header('Location: index.php?page=order-menu&menu_id=' . $menuID . '&step=3');
                        exit();
                    }
                    break;

                case 3:
                   
                    // Une vue détaillée du prix visible avant validation (prix menu ainsi que le prix de la livraison).

                    break;
                case 4:



                    break;

                case 5:

                    if (!isset($_SESSION['current_order'])) {
        header('Location: index.php?page=home');
        exit();
    }

    // 1. RECONSTRUCTION DE L'OBJET USER
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

    // 4. PAIEMENT / ENREGISTREMENT AVEC GESTION D'ERREURS
    try {
        if (OrderManager::createOrderFromData($db, $user, $menuData, $prestation, $totalCommande)) {
            unset($_SESSION['current_order']);
            header('Location: index.php?page=order-success');
            exit();
        }
    } catch (\Exception $e) {
        //  message "Stock insuffisant"
        error_log("Echec création commande pour user " . $user->user_id . ": " . $e->getMessage());
        
        // On affiche le message réel à l'utilisateur
        error_message($e->getMessage()); 
        
        // Redirection vers l'étape précédente avec l'erreur
        header('Location: index.php?page=order-menu&step=4&error=' . urlencode($e->getMessage()));
        exit();
    }
    break;
            } // Fin du switch
        } // Fin du if(POST)

        // 1. CALCUL DE SÉCURITÉ DU TOTAL
        // On récupère le menu pour avoir accès à la méthode calculerTotal
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

        // On calcule le total une seule fois, de façon fiable
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

        // 2. PRÉPARATION DES VARIABLES POUR LA VUE
        $data = [
            'step' => (int)($_GET['step'] ?? 0),
            'menuID' => $menuID,
            'menu' => $menu,
            'menuInfo' => $menuInfo,
            'db' => $db,
            'total_general' => $total_general,
            'order' => $orderData
        ];

        $step = $data['step'];
        $menuID = $data['menuID'];
        $menu = $data['menu'];
        $menuInfo = $data['menuInfo'];
        $db = $data['db'];
        $total_general = $data['total_general'];
        $order = $data['order'];

        /** @var int $step */
        /** @var int $menuID */
        /** @var Menu|null $menu */
        /** @var array $menuInfo */
        /** @var \PDO $db */
        /** @var float $total_general */
        /** @var array $order */

        // 3. AFFICHAGE
        $specific_scripts = ["assets/javascript/orderMenu.js"];
        $vue = ROOT_PATH . '/app/views/user/order/bookmenu_step' . $step . '.view.php';
        require_once ROOT_PATH . '/app/views/layout/header.php';
        if (file_exists($vue)) {
            require_once $vue;
        } else {
            echo "<p>Erreur : Étape introuvable.</p>";
        }
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
    public static function ajaxFraisLivraison($db) {
        $lieuId = $_GET['lieu_id'] ?? 0;
        
        if ($lieuId > 0) {
            // Utilisation de la méthode que tu as créée dans OrderManager
            $frais = OrderManager::EstimerFraisLivraison($db, (int)$lieuId);
            
            header('Content-Type: application/json');
            echo json_encode(['frais' => number_format($frais, 2)]);
            exit();
        }
        
        // Cas d'erreur ou lieu absent
        header('Content-Type: application/json');
        echo json_encode(['frais' => '0.00']);
        exit();
    }
    public static function orderSuccess(\PDO $db)
    {
        // Affiche simplement une vue de succès
        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/order/order-success.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
    public static function cancelOrder()
    {
        // 1. On nettoie la session
        unset($_SESSION['current_order']);
        // Ajoute ici tes autres 'unset' si nécessaire (ex: $_SESSION['step'])

        // 2. On redirige
        header('Location: index.php?page=search');
        exit();
    }
}
