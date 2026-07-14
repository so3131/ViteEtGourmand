<?php

namespace App\Models;

class Order
{
    public function __construct(
        public ?int $commande_id = null,
        public string $numero_commande = '',
        public string $date_commande = '',
        public string $date_prestation = '',
        public string $heure_livraison = '',
        public float $prix_menu = 0.0,
        public int $nombre_personne = 0,
        public float $prix_livraison = 0.0,
        public float $prix_total = 0.0,
        
        public string $statut = 'en attente',
        public string $pret_materiel = 'non',
        public float $depot_garantie = 0.0,
        public string $restitution_materiel = '',
        public int $utilisateur_id = 0,
        public int $menu_id = 0,
        public int $lieu_prestation_id = 0,
        public int $nombre_personne_min = 0,
        public ?int $id = null,
        public string $nom = '',
        public string $ville = '',
        public float $km = 0.0,
    ) {}
    public static function calculerFraisLivraison(string $ville, float $km): float
    {
        if (strtolower(trim($ville)) === 'bordeaux') {
            return 0.0;
        }
        return 5.0 + ($km * 0.59);
    }
}
