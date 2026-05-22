<?php
// Modèle Menu

namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

class Menu extends BaseModel
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * Obtenir le titre formaté
     */
    public function getTitre()
    {
        return htmlspecialchars($this->data['titre'] ?? 'Sans titre');
    }

    /**
     * Obtenir la description
     */
    public function getDescription()
    {
        return htmlspecialchars($this->data['description_menu'] ?? '');
    }

    /**
     * Vérifier si le menu est disponible
     */
    public function isAvailable()
    {
        return ($this->data['quantite_restante'] ?? 0) > 0;
    }
}
