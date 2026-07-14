<?php

namespace App\Managers;


// La classe MenuManager gère les opérations liées aux menus.
//* Logique de stockage.
class MenuManager
{
    public static function get(\PDO $db, ?int $id = null, array $filters = [], bool $format = false, bool $includePhotos = true)
    {
        try {
            $sql = "SELECT vg_menu.*, vg_theme.libelle as theme_libelle, vg_regime.libelle as regime_libelle, 
       delai_commande, conditions_stockage, categorie,
       GROUP_CONCAT(DISTINCT vg_plat.photo SEPARATOR ',') as photos
        FROM vg_menu 
        LEFT JOIN vg_theme ON vg_menu.theme_id = vg_theme.theme_id
        LEFT JOIN vg_regime ON vg_menu.regime_id = vg_regime.regime_id
        LEFT JOIN vg_menu_plat ON vg_menu.menu_id = vg_menu_plat.menu_id
        LEFT JOIN vg_plat ON vg_menu_plat.plat_id = vg_plat.plat_id
        WHERE 1=1 ";
            $params = [];
            //  Si $id n'est pas null, ajouter le filtre id
            if ($id !== null) {
                $sql .= " AND vg_menu.menu_id = :id";
                $params['id'] = $id;
            }



            // Appliquer les filtres (theme_id, regime_id, prix_min, prix_max, nombre_personne_minimum, titre)
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
            // : Ajouter GROUP BY pour GROUP_CONCAT
            $sql .= " GROUP BY vg_menu.menu_id";
            // : Ajouter ORDER BY
            $sql .= " ORDER BY vg_menu.titre ASC";
            // : Exécuter et récupérer résultats
            $stmt = $db->prepare($sql);
            error_log("DEBUG SQL: " . $sql);
            error_log("DEBUG Params: " . json_encode($params));
            $stmt->execute($params);
            error_log("DEBUG: Execute réussi");

            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            error_log("DEBUG: Results bruts count = " . count($results));
            error_log("DEBUG: Results bruts = " . json_encode($results));

            // Si $format = true, reformater avec array_map
            if ($format === true) {
                error_log("DEBUG: Avant array_map");

                $results = array_map(function ($row) use ($includePhotos) {
                    return [
                        'menu_id' => $row['menu_id'],
                        'titre' => $row['titre'],
                        'nombre_personne_minimum' => $row['nombre_personne_minimum'],
                        'prix_par_personne' => $row['prix_par_personne'],
                        'description_menu' => $row['description_menu'],
                        'quantite_restante' => $row['quantite_restante'],
                        'theme_id' => $row['theme_id'],
                        'regime_id' => $row['regime_id'],
                        'photos' => ($includePhotos && !empty($row['photos'])) ? explode(',', $row['photos']) : [],
                        'vg_theme' => [
                            'libelle' => $row['theme_libelle'] ?? 'Non défini'
                        ],
                        'regime' => $row['regime_libelle'] ?? 'Non défini',
                        'delai_commande' => $row['delai_commande'] ?? '24h',
                        'conditions_stockage' => $row['conditions_stockage'] ?? 'Aucune précaution particulière',
                        'categorie' => $row['categorie'] ?? 'Non définie'
                    ];
                }, $results);
                error_log("DEBUG: Après array_map, count = " . count($results));
            }
            return $results;
        } catch (\PDOException $e) {
            error_log("ERREUR MenuManager::get() : " . $e->getMessage());
            return false;
        }
    }
public static function getById(\PDO $db, int $menuID): ?array {
    $sql = "SELECT vg_menu.*, 
                   vg_theme.libelle as theme_libelle, 
                   vg_regime.libelle as regime_libelle,
               delai_commande
            FROM vg_menu 
            LEFT JOIN vg_theme ON vg_menu.theme_id = vg_theme.theme_id
            LEFT JOIN vg_regime ON vg_menu.regime_id = vg_regime.regime_id
            WHERE vg_menu.menu_id = ?";
            
    $stmt = $db->prepare($sql);
    $stmt->execute([$menuID]);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    return $result ?: null;
}
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
}
