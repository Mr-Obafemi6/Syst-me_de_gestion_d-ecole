<?php
// app/core/Model.php — Modèle de base avec accès PDO

class Model {
    protected PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Récupère tous les enregistrements
     */
    public function findAll(string $orderBy = 'id', string $dir = 'ASC'): array {
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->table}` ORDER BY `{$orderBy}` {$dir}"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère un enregistrement par son ID
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Récupère des enregistrements selon une condition simple
     */
    public function findWhere(string $column, mixed $value): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->table}` WHERE `{$column}` = ?"
        );
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    /**
     * Insère un enregistrement et retourne son ID
     */
    public function insert(array $data): int {
        $columns = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare(
            "INSERT INTO `{$this->table}` (`{$columns}`) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));
        return (int) $this->db->lastInsertId();
    }

    /**
     * Met à jour un enregistrement par ID
     */
    public function update(int $id, array $data): bool {
        $sets = implode(' = ?, ', array_map(fn($col) => "`{$col}`", array_keys($data))) . ' = ?';
        $stmt = $this->db->prepare(
            "UPDATE `{$this->table}` SET {$sets} WHERE `{$this->primaryKey}` = ?"
        );
        $values = array_values($data);
        $values[] = $id;
        return $stmt->execute($values);
    }

    /**
     * Supprime un enregistrement par ID
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Compte le nombre total d'enregistrements
     */
    public function count(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM `{$this->table}`");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Pagination : retourne une page de résultats
     */
    public function paginate(int $page = 1, int $perPage = ITEMS_PER_PAGE): array {
        $offset = ($page - 1) * $perPage;
        $total  = $this->count();
        $stmt   = $this->db->prepare(
            "SELECT * FROM `{$this->table}` LIMIT ? OFFSET ?"
        );
        $stmt->execute([$perPage, $offset]);

        return [
            'data'         => $stmt->fetchAll(),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Exécute une requête SQL brute (requêtes complexes)
     */
    protected function query(string $sql, array $params = []): array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Exécute une requête et retourne un seul enregistrement
     */
    protected function queryOne(string $sql, array $params = []): ?array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Exécute une requête et retourne une valeur scalaire
     */
    protected function queryScalar(string $sql, array $params = []): mixed {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
}
