<?php
// Classe parente pour tous les Managers (Data Access Objects)
// Contient les méthodes CRUD génériques

require_once dirname(__DIR__) . '/config/constants.php';

class BaseManager
{
    protected $db;
    protected $table;

    public function __construct(PDO $db, $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    /**
     * Récupérer tous les enregistrements
     */
    public function getAll($limit = null, $offset = 0)
    {
        $query = "SELECT * FROM {$this->table} LIMIT :offset, :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit ?? 1000, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer par ID
     */
    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE " . $this->getIdColumn() . " = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insérer un enregistrement
     */
    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }

    /**
     * Mettre à jour un enregistrement
     */
    public function update($id, $data)
    {
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "$key = :$key";
        }
        $setSql = implode(', ', $sets);
        $query = "UPDATE {$this->table} SET $setSql WHERE " . $this->getIdColumn() . " = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }

    /**
     * Supprimer un enregistrement
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE " . $this->getIdColumn() . " = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Obtenir la colonne ID (à surcharger si nécessaire)
     */
    protected function getIdColumn()
    {
        return 'id';
    }
}
