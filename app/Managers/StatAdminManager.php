<?php

namespace App\Managers;
// class StatAdminManager pour gérer les statistiques globales depuis la base de données SQL
class StatAdminManager
{
    //function pour récupérer les statistiques globales depuis la base de données SQL
    public static function getStatsFromSQL(\PDO $db)
    {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM vg_menu) AS total_menus,
                    (SELECT COUNT(*) FROM vg_plat) AS total_plats,
                    (SELECT COUNT(*) FROM vg_commande) AS total_commandes,
                    (SELECT COUNT(*) FROM vg_commande WHERE statut = 'en_attente') AS pending_orders,
                    (SELECT COUNT(*) FROM vg_commande WHERE statut = 'terminee') AS finished_orders,
                    (SELECT COUNT(*) FROM vg_utilisateur) AS total_utilisateurs,
                    (SELECT COUNT(*) FROM vg_theme) AS total_themes,
                    (SELECT COUNT(*) FROM vg_regime) AS total_regimes,
                    (SELECT COUNT(*) FROM vg_utilisateur) AS total_users";

        $stmt = $db->query($sql);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
