<?php
// Classe parente pour tous les modèles
// Contient les propriétés et méthodes communes

class BaseModel
{
    protected $data = [];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Obtenir une propriété
     */
    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Définir une propriété
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Convertir le modèle en tableau
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Convertir le modèle en JSON
     */
    public function toJson()
    {
        return json_encode($this->data);
    }
}
