<?php

namespace App\Controllers\UserController;

require_once dirname(__DIR__, 2) . '/config/constants.php';

use App\Managers\MenuManager;
use App\Managers\OrderManager;
use App\models\Menu;
use App\Config\constants;
use App\Helpers\SecurityManager;
use App\Managers\LieuManager;

###


use App\Controllers\AuthController\Auth;
// class UpdateOrderController pour gérer la mise à jour des commandes
class UpdateOrderController
{
    //function pour mettre à jour une commande spécifique après vérification de l'état de la commande et des données fournies par l'utilisateur
    public static function updateOrder(\PDO $db, ?int $commande_id)
    {
        if (ob_get_length()) {
            ob_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        Auth::check([ROLE_USER]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!is_array($data)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Données JSON invalides.'
                ]);
                exit();
            }

            SecurityManager::validateJson($data);
            $rental = !empty($data['pret_materiel']) ? 1 : 0;
            $depot = ($rental === 1) ? DEPOT_GARANTIE_MATERIEL : 0.0;

            if (!$commande_id) {
                echo json_encode(['success' => false, 'message' => 'Identifiant de commande manquant.']);
                exit();
            }
            $pdo = $db;

            $result = OrderManager::getOrderWithDetailsForUser($db, (int)$commande_id, (int)$_SESSION['user_id']);
            try {
                if (!$result) {
                    throw new \Exception("Commande non trouvée ou accès refusé.");
                }

                //Empecher les annulations hors statut "en_attente"
                if ($result['statut'] !== 'en_attente') {
                    throw new \Exception("Seules les commandes en attente peuvent être modifiées ou supprimées.");
                }

                $menuId = (int)$result['menu_id'];
                $menuData = MenuManager::getById($db, $menuId);

                $ancienneAdresse = trim((string)$result['adresse']);
                $ancienneVille = trim((string)$result['ville']);

                $adresse = trim($data['adresse_livraison'] ?? '');
                $ville = trim($data['ville'] ?? '');

                $adresseIdentique =
                    $adresse === $ancienneAdresse &&
                    $ville === $ancienneVille;

                if ($adresseIdentique) {

                    $lieu_id = (int)$result['lieu_prestation_id'];
                } else {
                    if (
                        empty($adresse) ||
                        empty($ville) ||
                        empty($data['lat']) ||
                        empty($data['lon'])
                    ) {
                        throw new \Exception(
                            "Veuillez sélectionner une nouvelle adresse dans la liste."
                        );
                    }

                    $lieu_id = \App\Managers\LieuManager::getOrInsert(
                        $db,
                        $adresse,
                        $data['code_postal'] ?? '',
                        $ville,
                        (float)$data['lat'],
                        (float)$data['lon']
                    );
                }
                $delaiCommande = (int)($menuData['delai_commande'] ?? 0);

                $dateMinimale = new \DateTime('today');
                $dateMinimale->modify('+' . $delaiCommande . ' days');

                if (empty($data['date_prestation'])) {
                    throw new \Exception("La date de prestation est obligatoire.");
                }

                $dateSelectionnee = new \DateTime($data['date_prestation']);
                $dateSelectionnee->setTime(0, 0, 0);

                if ($dateSelectionnee < $dateMinimale) {
                    throw new \Exception("La date de prestation doit être au minimum à J+" . $delaiCommande . ".");
                }

                if (!$menuData) {
                    throw new \Exception("Menu introuvable.");
                }

                $menu = new Menu(
                    (int)$menuData['menu_id'],
                    (int)$menuData['nombre_personne_minimum'],
                    (string)$menuData['titre'],
                    (string)$menuData['description_menu'],
                    (float)$menuData['prix_par_personne'],
                    (int)$menuData['quantite_restante'],
                    !empty($menuData['theme_id']) ? (int)$menuData['theme_id'] : null,
                    !empty($menuData['regime_id']) ? (int)$menuData['regime_id'] : null,
                    $menuData['theme_libelle'] ?? '',
                    $menuData['regime_libelle'] ?? ''
                );

                // Récupération des infos du lieu pour calculer les frais de livraison
                $lieu = LieuManager::getById($db, (int)$lieu_id);





                $fraisLivraison = 0.00;
                if ($lieu && !empty($lieu['latitude']) && !empty($lieu['longitude'])) {
                    $distance = \App\Models\Order::calculerDistanceRouteVersClient(
                        (float)$lieu['latitude'],
                        (float)$lieu['longitude']
                    );
                    $fraisLivraison = \App\Models\Order::calculerFraisLivraisonParKm(
                        $lieu['ville'],
                        $distance
                    );
                }

                // Validation de la quantité et calcul du total
                if (!$menu->estQuantiteValide((int)$data['nombre_personne'])) {
                    throw new \Exception("Le nombre de personnes doit être au minimum de " . $menu->getMinimumRequis());
                }

                $nouveauPrix = $menu->calculerTotal(
                    (int)$data['nombre_personne'],
                    (float)$fraisLivraison,
                    (float)$depot
                );

                $ancienneQuantite = (int)$result['nombre_personne'];
                $nouvelleQuantite = (int)$data['nombre_personne'];
                $delta = $nouvelleQuantite - $ancienneQuantite;
                $pdo->beginTransaction();

                try {

                    MenuManager::ajusterStock($db, $menuId, $delta);


                    OrderManager::updateOrder($db, (int)$commande_id, [
                        'date_prestation'    => $data['date_prestation'],
                        'heure_livraison'    => $data['heure_livraison'],
                        'lieu_prestation_id' => $lieu_id,
                        'prix_total'         => $nouveauPrix,
                        'pret_materiel'      => $rental,
                        'depot_garantie'     => $depot,
                        'nombre_personne'    => $nouvelleQuantite,
                    ], (int)$_SESSION['user_id']);



                    $pdo->commit();
                } catch (\Exception $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }

                    throw $e;
                }

                $lieuInfo = LieuManager::getById($db, (int)$lieu_id);



                $lieuTexte = '';

                if ($lieuInfo) {
                    $lieuTexte = trim(
                        $lieuInfo['adresse'] . ', ' .
                            $lieuInfo['code_postal'] . ' ' .
                            $lieuInfo['ville']
                    );
                }
                try {
                    \App\Helpers\MailService::sendOrderUpdateEmail($result['email'], [
                        'prenom'          => $result['prenom'],
                        'menu'            => $menuData['titre'],
                        'quantite'        => (int)$data['nombre_personne'],
                        'date_prestation' => $data['date_prestation'],
                        'heure_livraison' => $data['heure_livraison'],
                        'lieu'            => $lieuTexte,
                        'prix_total'      => $nouveauPrix
                    ]);
                } catch (\Exception $e) {
                    error_log("Erreur envoi mail : " . $e->getMessage());
                }

                echo json_encode(['success' => true, 'message' => '✅ Commande modifiée']);
                exit();
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit();
            }
        }
    }
    //function pour recalculer le prix total d'une commande en fonction des modifications apportées par l'utilisateur
    public static function recalculerPrix(\PDO $db, ?int $commande_id = null)
    {
        if (ob_get_length()) {
            ob_clean();
        }

        header('Content-Type: application/json; charset=utf-8');

        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            if (!is_array($data)) {
                echo json_encode([
                    'nouveau_prix' => '0.00',
                    'error' => 'Données JSON invalides'
                ]);
                exit();
            }
            SecurityManager::validateJson($data);
            $pret_materiel = !empty($data['pret_materiel']) ? 1 : 0;
            $montant_depot = ($pret_materiel === 1) ? DEPOT_GARANTIE_MATERIEL : 0.0;


            $commande_id = isset($_GET['commande_id']) ? (int)$_GET['commande_id'] : 0;

            $orderData = OrderManager::getOrderLight($db, $commande_id);

            if (!$orderData || (int)$orderData['utilisateur_id'] !== (int)$_SESSION['user_id']) {
                echo json_encode(['nouveau_prix' => '0.00', 'error' => 'Commande non trouvée ou accès refusé.']);
                exit();
            }

            $menu_id = (int)$orderData['menu_id'];

            $lieu_id = 0;
            if (!empty($data['adresse_livraison']) && !empty($data['lat']) && !empty($data['lon'])) {
                $lieu_id = LieuManager::getOrInsert(
                    $db,
                    $data['adresse_livraison'],
                    $data['code_postal'] ?? '',
                    $data['ville'] ?? '',
                    (float)$data['lat'],
                    (float)$data['lon']
                );
            } else {
                $lieu_id = (int)$orderData['lieu_prestation_id'];
            }

            $lieu = LieuManager::getById($db, $lieu_id);





            $frais = 0.00;
            if ($lieu && !empty($lieu['latitude']) && !empty($lieu['longitude'])) {
                $distance = \App\Models\Order::calculerDistanceRouteVersClient(
                    (float)$lieu['latitude'],
                    (float)$lieu['longitude']
                );

                $frais = \App\Models\Order::calculerFraisLivraisonParKm(
                    $lieu['ville'],
                    $distance
                );
            }



            $menu_id = (int)$orderData['menu_id'];

            $menuData = MenuManager::getById($db, $menu_id);

            if (!$menuData) {
                echo json_encode(['nouveau_prix' => '0.00', 'error' => 'Menu introuvable']);
                exit();
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
            $quantite = (int)($data['nombre_personne'] ?? 0);

            $total = $menu->calculerTotal($quantite, (float)$frais, $montant_depot);


            echo json_encode([
                'nouveau_prix' => number_format($total, 2, '.', ''),
                'frais_livraison' => number_format((float)$frais, 2, '.', '')
            ]);
            exit();
        } catch (\Exception $e) {
            echo json_encode(['nouveau_prix' => '0.00', 'error' => $e->getMessage()]);
            exit();
        }
    }
    //function pour revenir à la page de modification d'une commande spécifique avec les détails du menu et les lieux disponibles
    public static function cancelEditOrder(\PDO $db)
    {
        Auth::check([ROLE_USER]);
        header('Location: index.php?page=dashboard-user');
        exit();
    }

    //function pour afficher la page de modification d'une commande spécifique avec les détails du menu et les lieux disponibles
    public static function editOrderView(\PDO $db, int $commande_id)
    {
        Auth::check([ROLE_USER]);
        $commande = OrderManager::getOrderWithDetailsForUser($db, (int)$commande_id, (int)$_SESSION['user_id']);

        if (!$commande) {
            throw new \Exception("Commande introuvable.");
        }

        $menuData = MenuManager::getById($db, (int)$commande['menu_id']);
        if (!$menuData) {
            throw new \Exception("Menu introuvable.");
        }
        // 2. Récupérer les lieux pour le select
        $lieux = LieuManager::getAll($db);
        $specific_scripts = ["assets/javascript/editOrder.js"];
        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/order/edit.order.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
