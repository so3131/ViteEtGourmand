<?php

namespace App\Models;

// La classe Menu représente un menu avec ses propriétés
// Utilisée pour structurer les données des menus récupérées de la base de données. 
//* Logique métier
class Menu
{

    public ?int $menu_id;
    public string $titre;
    public int $nombre_personne_minimum;
     public float $prix_par_personne;
    public string $description_menu;
    public int $quantite_restante;
    public ?int $theme_id;
    public ?int $regime_id;
    public string $libelle_theme;
    public string $libelle_regime;
    public int $plat_id;
    public int $allergene_id;
    public string $libelle_allergene;

    public function __construct(

        int $menu_id,
        int $nombre_personne_minimum,
        string $titre,
        string $description_menu,
        float $prix_par_personne,
        int $quantite_restante,
        int $theme_id,
        int $regime_id,
         string $libelle_theme = '',
        string $libelle_regime = '',
        int $plat_id = 0,
         int $allergene_id = 0,
         string $libelle_allergene = ''
    ) {
        $this->menu_id = $menu_id;
        $this->nombre_personne_minimum = $nombre_personne_minimum;
        $this->titre = $titre;
        $this->description_menu = $description_menu;
        $this->prix_par_personne = $prix_par_personne;
        $this->quantite_restante = $quantite_restante;
        $this->theme_id = $theme_id;
        $this->regime_id = $regime_id;
        $this->libelle_theme = $libelle_theme;
        $this->libelle_regime = $libelle_regime;
        $this->plat_id = $plat_id;
        $this->allergene_id = $allergene_id;
        $this->libelle_allergene = $libelle_allergene;
    }
     public function estQuantiteValide(int $nombrePersonne): bool 
    {
        // La règle métier est ici, dans le modèle
        return $nombrePersonne >= $this->nombre_personne_minimum;
    }
    public function getMinimumRequis(): int {
    return $this->nombre_personne_minimum;
}
public function calculerPrix(int $quantite): float {
    // 1. Calcul de base
    $prixTotal = $this->prix_par_personne * $quantite;

    // 2. Application de la réduction (10% si quantité >= min + 5)
    if ($quantite >= ($this->nombre_personne_minimum + 5)) {
        $prixTotal = $prixTotal * 0.9;
    }

    return $prixTotal;
}
public function calculerTotal(int $quantite, float $fraisLivraison = 0, float $depotGarantie = 0): float 
{
    // On appelle votre logique de prix de base (avec les -10%)
    $prixMenu = $this->calculerPrix($quantite);
    
    // On ajoute le reste
    return $prixMenu + $fraisLivraison + $depotGarantie;
}
public function hasDiscount(int $quantite): bool 
{
    return $quantite >= ($this->nombre_personne_minimum + 5);
}
}