<?php

namespace App\Managers;


// La classe MenuManager gère les opérations liées aux menus.
//* Logique de stockage.
class MenuManager
{
    //function pour récupérer les menus avec des filtres optionnels et un formatage des résultats
    public static function get(\PDO $db, ?int $id = null, array $filters = [], bool $format = false)
    {
        try {
            $sql = "SELECT vg_menu.*, vg_theme.libelle as theme_libelle, vg_regime.libelle as regime_libelle, 
            delai_commande, conditions_stockage, vg_plat.plat_id,
 vg_plat.titre_plat, vg_plat.photo, vg_plat.categorie
        FROM vg_menu
        LEFT JOIN vg_theme ON vg_menu.theme_id = vg_theme.theme_id
        LEFT JOIN vg_regime ON vg_menu.regime_id = vg_regime.regime_id
        LEFT JOIN vg_menu_plat ON vg_menu.menu_id = vg_menu_plat.menu_id
        LEFT JOIN vg_plat ON vg_menu_plat.plat_id = vg_plat.plat_id
        WHERE 1=1 
        AND vg_menu.is_active = 1";
            $params = [];

            if ($id !== null) {
                $sql .= " AND vg_menu.menu_id = :id";
                $params['id'] = $id;
            }

            if (!empty($filters['theme_id']) && is_array($filters['theme_id'])) {
                $placeholders = [];
                foreach ($filters['theme_id'] as $k => $id) {
                    $key = ":theme" . $k;
                    $placeholders[] = $key;
                    $params[$key] = $id;
                }
                $sql .= " AND vg_menu.theme_id IN (" . implode(',', $placeholders) . ")";
            }
            if (!empty($filters['regime_id'])) {
                $sql .= " AND vg_menu.regime_id = :regime_id";
                $params['regime_id'] = $filters['regime_id'];
            }
            if (!empty($filters['prix_min'])) {
                $sql .= " AND vg_menu.prix_par_personne >= :prix_min";
                $params['prix_min'] = $filters['prix_min'];
            }
            if (!empty($filters['prix_max'])) {
                $sql .= " AND vg_menu.prix_par_personne <= :prix_max";
                $params['prix_max'] = $filters['prix_max'];
            }
            if (!empty($filters['nombre_personne_minimum'])) {
                $sql .= " AND vg_menu.nombre_personne_minimum >= :nombre_personne_minimum";
                $params['nombre_personne_minimum'] = $filters['nombre_personne_minimum'];
            }
            if (!empty($filters['titre'])) {
                $sql .= " AND vg_menu.titre LIKE :titre";
                $params['titre'] = '%' . $filters['titre'] . '%';
            }

            $sql .= " ORDER BY vg_menu.titre ASC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $rawResults = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($format === true) {
                $menusGroupes = [];

                foreach ($rawResults as $row) {
                    $menuId = $row['menu_id'];

                    if (!isset($menusGroupes[$menuId])) {
                        $menusGroupes[$menuId] = [
                            'menu_id' => $row['menu_id'],
                            'titre' => $row['titre'],
                            'description_menu' => $row['description_menu'],
                            'prix_par_personne' => $row['prix_par_personne'],
                            'nombre_personne_minimum' => $row['nombre_personne_minimum'],
                            'vg_theme' => ['libelle' => $row['theme_libelle'] ?? 'Non défini'],
                            'regime' => $row['regime_libelle'] ?? 'Non défini',
                            'plats_structures' => [
                                'Entrée' => null,
                                'Plat' => null,
                                'Dessert' => null
                            ]
                        ];
                    }

                    if (!empty($row['plat_id'])) {
                        $categorie = trim($row['categorie']);

                        $categories = [
                            'entrée' => 'Entrée',
                            'entree' => 'Entrée',
                            'plat' => 'Plat',
                            'dessert' => 'Dessert'
                        ];

                        $categorieNormalisee = $categories[mb_strtolower($categorie)] ?? null;

                        if ($categorieNormalisee !== null) {
                            $menusGroupes[$menuId]['plats_structures'][$categorieNormalisee] = [
                                'plat_id' => $row['plat_id'],
                                'titre_plat' => $row['titre_plat'],
                                'photo' => $row['photo']
                            ];
                        }



                        if (array_key_exists($categorie, $menusGroupes[$menuId]['plats_structures'])) {
                            if ($menusGroupes[$menuId]['plats_structures'][$categorie] === null) {
                                $menusGroupes[$menuId]['plats_structures'][$categorie] = [
                                    'plat_id' => $row['plat_id'],
                                    'titre_plat' => $row['titre_plat'],
                                    'photo' => $row['photo']
                                ];
                            }
                        }
                    }
                }

                return array_values($menusGroupes);
            }

            return $rawResults;
        } catch (\PDOException $e) {
            error_log("ERREUR MenuManager::get() : " . $e->getMessage());
            return false;
        }
    }

    //function pour récupérer un menu par son ID
    public static function getById(\PDO $db, int $menuID): ?array
    {
        try {
            $sql = "SELECT vg_menu.*, 
                    vg_theme.libelle as theme_libelle, 
                    vg_regime.libelle as regime_libelle,
                    delai_commande, conditions_stockage, 
                    vg_plat.plat_id, vg_plat.titre_plat, vg_plat.photo, vg_plat.categorie
                FROM vg_menu 
                LEFT JOIN vg_theme ON vg_menu.theme_id = vg_theme.theme_id
                LEFT JOIN vg_regime ON vg_menu.regime_id = vg_regime.regime_id
                LEFT JOIN vg_menu_plat ON vg_menu.menu_id = vg_menu_plat.menu_id
                LEFT JOIN vg_plat ON vg_menu_plat.plat_id = vg_plat.plat_id
                WHERE vg_menu.menu_id = ?";

            $stmt = $db->prepare($sql);
            $stmt->execute([$menuID]);
            $rawResults = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rawResults)) {
                return null;
            }
            $menuData = null;

            foreach ($rawResults as $row) {
                if ($menuData === null) {
                    $menuData = [
                        'menu_id' => $row['menu_id'],
                        'titre' => $row['titre'],
                        'description_menu' => $row['description_menu'],
                        'prix_par_personne' => $row['prix_par_personne'],
                        'nombre_personne_minimum' => $row['nombre_personne_minimum'],
                        'quantite_restante' => $row['quantite_restante'] ?? 0,
                        'delai_commande' => $row['delai_commande'] ?? '24h',
                        'conditions_stockage' => $row['conditions_stockage'] ?? 'Aucune précaution particulière',
                        'theme_id' => $row['theme_id'] ?? null,
                        'regime_id' => $row['regime_id'] ?? null,
                        'vg_theme' => ['libelle' => $row['theme_libelle'] ?? 'Non défini'],
                        'regime' => $row['regime_libelle'] ?? 'Non défini',
                        'plats_structures' => [
                            'Entrée' => null,
                            'Plat' => null,
                            'Dessert' => null
                        ]
                    ];
                }

                // Placement du plat dans sa catégorie correspondante
                if (!empty($row['plat_id'])) {
                    $categorie = $row['categorie'];
                    if (array_key_exists($categorie, $menuData['plats_structures'])) {
                        if ($menuData['plats_structures'][$categorie] === null) {
                            $menuData['plats_structures'][$categorie] = [
                                'plat_id' => $row['plat_id'],
                                'titre_plat' => $row['titre_plat'],
                                'photo' => $row['photo']
                            ];
                        }
                    }
                }
            }

            return $menuData;
        } catch (\PDOException $e) {
            error_log("ERREUR MenuManager::getById() : " . $e->getMessage());
            return null;
        }
    }
    //function pour récupérer les plats d'un menu par son ID
    public static function getPlatsByMenuId(\PDO $db, int $menu_id)
    {
        try {
            $sql = "SELECT vg_plat.* 
            FROM vg_plat 
            JOIN vg_menu_plat ON vg_plat.plat_id = vg_menu_plat.plat_id
             WHERE vg_menu_plat.menu_id = :menu_id";

            $stmt = $db->prepare($sql);
            $stmt->execute(['menu_id' => $menu_id]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des plats : " . $e->getMessage());
            return false;
        }
    }
    // Fonction pour récupérer TOUS les allergènes disponibles
    public static function getAllAllergenes(\PDO $db)
    {
        try {
            $sql = "SELECT * FROM vg_allergene";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de tous les allergènes : " . $e->getMessage());
            return [];
        }
    }
    //function pour récupérer les allergènes d'un menu par son ID
    public static function getAllergenesByMenuId(\PDO $db, int $menu_id)
    {
        try {
            $sql = "SELECT DISTINCT vg_allergene.*
        FROM vg_menu
        JOIN vg_menu_plat ON vg_menu.menu_id = vg_menu_plat.menu_id
        JOIN vg_allergene_plat ON vg_menu_plat.plat_id = vg_allergene_plat.plat_id
        JOIN vg_allergene ON vg_allergene_plat.allergene_id = vg_allergene.allergene_id
        WHERE vg_menu.menu_id = :menu_id";

            $stmt = $db->prepare($sql);
            $stmt->execute(['menu_id' => $menu_id]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des allergènes : " . $e->getMessage());
            return false;
        }
    }
    //function pour compter les menus en rupture de stock
    public static function countRuptureStock(\PDO $db): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM vg_menu WHERE quantite_restante <= 0 AND is_active = 1";
            $stmt = $db->query($sql);
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("ERREUR MenuManager::countRuptureStock() : " . $e->getMessage());
            return 0;
        }
    }
    public static function getAllThemes(\PDO $db): array
    {

        $stmt = $db->query("SELECT * FROM vg_theme ORDER BY libelle ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    // Récupérer les thèmes et régimes pour les selects
    public static function getAllRegimes(\PDO $db): array
    {
        $stmt = $db->query("SELECT * FROM vg_regime ORDER BY libelle ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    // Crée un menu avec ses plats associés (gère la transaction)
    public static function create(\PDO $db, array $menuData, array $plats = []): int
    {
        $db->beginTransaction();
        try {
            $sqlMenu = "INSERT INTO vg_menu (titre, nombre_personne_minimum, prix_par_personne, 
                    description_menu, quantite_restante, delai_commande, conditions_stockage, 
                    theme_id, regime_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sqlMenu);
            $stmt->execute([
                $menuData['titre'],
                $menuData['min_personne'],
                $menuData['prix'],
                $menuData['description'],
                $menuData['quantite'],
                $menuData['delai'],
                $menuData['conditions'] ?? null,
                $menuData['theme_id'] ?? null,
                $menuData['regime_id'] ?? null,
            ]);
            $menuId = (int)$db->lastInsertId();

            if (!empty($plats)) {
                $sqlLiaison = "INSERT INTO vg_menu_plat (menu_id, plat_id) VALUES (?, ?)";
                $stmtLiaison = $db->prepare($sqlLiaison);
                foreach ($plats as $platId) {
                    $stmtLiaison->execute([$menuId, (int)$platId]);
                }
            }

            $db->commit();
            return $menuId;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    // Met à jour un menu et synchronise ses plats
    public static function update(\PDO $db, int $menuId, array $menuData, array $plats = []): bool
    {
        $db->beginTransaction();
        try {
            $sql = "UPDATE vg_menu SET titre = :titre, prix_par_personne = :prix, 
                quantite_restante = :quantite, nombre_personne_minimum = :nb_min,
                description_menu = :description, theme_id = :theme_id, 
                regime_id = :regime_id, delai_commande = :delai, 
                conditions_stockage = :stockage WHERE menu_id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'titre'       => $menuData['titre'],
                'prix'        => $menuData['prix'],
                'quantite'    => $menuData['quantite'],
                'nb_min'      => $menuData['nombre_personne_minimum'],
                'description' => $menuData['description'],
                'theme_id'    => $menuData['theme_id'] ?? null,
                'regime_id'   => $menuData['regime_id'] ?? null,
                'delai'       => $menuData['delai_commande'],
                'stockage'    => $menuData['conditions_stockage'] ?? null,
                'id'          => $menuId,
            ]);

            // Synchroniser les plats (supprimer anciens, insérer nouveaux)
            $stmtDelete = $db->prepare("DELETE FROM vg_menu_plat WHERE menu_id = :id");
            $stmtDelete->execute(['id' => $menuId]);

            if (!empty($plats)) {
                $stmtInsert = $db->prepare("INSERT INTO vg_menu_plat (menu_id, plat_id) VALUES (:menu_id, :plat_id)");
                foreach ($plats as $platId) {
                    $stmtInsert->execute(['menu_id' => $menuId, 'plat_id' => (int)$platId]);
                }
            }

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    public static function ajusterStock(\PDO $db, int $menuId, int $delta): void
    {
        if ($delta === 0) {
            return;
        }

        if ($delta > 0) {
            $stmt = $db->prepare("
                UPDATE vg_menu
                SET quantite_restante = quantite_restante - :quantite
                WHERE menu_id = :menu_id
                  AND quantite_restante >= :quantite
            ");
            $stmt->execute(['quantite' => $delta, 'menu_id' => $menuId]);

            if ($stmt->rowCount() !== 1) {
                throw new \Exception("Stock insuffisant pour ce menu.");
            }
        } else {
            $stmt = $db->prepare("
                UPDATE vg_menu
                SET quantite_restante = quantite_restante + :quantite
                WHERE menu_id = :menu_id
            ");
            $stmt->execute(['quantite' => abs($delta), 'menu_id' => $menuId]);
        }
    }
    public static function getMenusManagement(\PDO $db): array
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

    // Nombre de commandes EN COURS liées à un menu (bloque la suppression)
    public static function countActiveOrders(\PDO $db, int $menuId): int
    {
        $stmt = $db->prepare("SELECT COUNT(*) FROM vg_commande WHERE menu_id = ? AND statut NOT IN ('annulee', 'terminee')");
        $stmt->execute([$menuId]);
        return (int)$stmt->fetchColumn();
    }

    // Soft delete (désactivation)
    public static function deactivate(\PDO $db, int $menuId): bool
    {
        $stmt = $db->prepare("UPDATE vg_menu SET is_active = 0 WHERE menu_id = ?");
        return $stmt->execute([$menuId]);
    }

    // Réactivation d'un menu désactivé
    public static function activate(\PDO $db, int $menuId): bool
    {
        $stmt = $db->prepare("UPDATE vg_menu SET is_active = 1 WHERE menu_id = ?");
        return $stmt->execute([$menuId]);
    }

    // Suppression des liaisons menu-plat
    public static function deleteLinks(\PDO $db, int $menuId): void
    {
        $stmt = $db->prepare("DELETE FROM vg_menu_plat WHERE menu_id = ?");
        $stmt->execute([$menuId]);
    }

    // Suppression définitive du menu (APRÈS deleteLinks)
    public static function deleteHard(\PDO $db, int $menuId): bool
    {
        $stmt = $db->prepare("DELETE FROM vg_menu WHERE menu_id = ?");
        return $stmt->execute([$menuId]);
    }
}
