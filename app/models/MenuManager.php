<?php
// Manager pour les Menus

namespace App\Models;

require_once __DIR__ . '/BaseManager.php';

class MenuManager extends BaseManager
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'vg_menu');
    }

    /**
     * Obtenir des menus avec filtres
     */
    public static function get(PDO $db, $id = null, $filters = [], $return_array = true, $test = false)
    {
        $query = "SELECT * FROM vg_menu WHERE 1=1";

        if ($id) {
            $query .= " AND menu_id = :id";
        }

        if (!empty($filters['titre'])) {
            $query .= " AND titre LIKE :titre";
        }

        if (!empty($filters['prix_max'])) {
            $query .= " AND prix_par_personne <= :prix_max";
        }

        if (!empty($filters['nombre_personne_minimum'])) {
            $query .= " AND nombre_personne_minimum >= :nombre_personne_minimum";
        }

        $stmt = $db->prepare($query);

        if ($id) $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if (!empty($filters['titre'])) $stmt->bindValue(':titre', '%' . $filters['titre'] . '%');
        if (!empty($filters['prix_max'])) $stmt->bindValue(':prix_max', $filters['prix_max']);
        if (!empty($filters['nombre_personne_minimum'])) {
            $stmt->bindValue(':nombre_personne_minimum', $filters['nombre_personne_minimum']);
        }

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $return_array ? $result : (!empty($result) ? $result[0] : null);
    }

    /**
     * Obtenir la colonne ID
     */
    protected function getIdColumn()
    {
        return 'menu_id';
    }
}
