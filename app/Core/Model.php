<?php
namespace App\Core;

// Classe parente de tous les modèles (Vehicle, Client, Reservation...).
// Fournit les opérations CRUD génériques (Create, Read, Update, Delete)
// basées sur des requêtes PDO préparées (sécurisées contre l'injection SQL).
abstract class Model
{
    protected \PDO $db;
    protected string $table  = ''; // nom de la table SQL (défini dans chaque modèle enfant)
    protected string $pk     = 'id'; // nom de la clé primaire

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    // Récupère une ligne par son ID, ou null si elle n'existe pas
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$this->pk}` = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // Comme find(), mais lève une exception si la ligne n'existe pas
    public function findOrFail(int $id): array
    {
        $row = $this->find($id);
        if (!$row) {
            throw new \RuntimeException("{$this->table} #{$id} not found");
        }
        return $row;
    }

    // Récupère toutes les lignes de la table, avec un tri optionnel
    public function all(string $order = ''): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($order) $sql .= " ORDER BY $order";
        return $this->db->query($sql)->fetchAll();
    }

    // Insère une nouvelle ligne. $data = ['colonne' => valeur, ...]
    // Retourne l'ID auto-incrémenté généré.
    public function create(array $data): int
    {
        $cols   = implode(', ', array_map(fn($k) => "`$k`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $this->db->prepare("INSERT INTO `{$this->table}` ($cols) VALUES ($placeholders)")
                 ->execute(array_values($data));
        return (int)$this->db->lastInsertId();
    }

    // Met à jour la ligne d'ID $id avec les colonnes/valeurs de $data
    public function update(int $id, array $data): void
    {
        $set = implode(', ', array_map(fn($k) => "`$k` = ?", array_keys($data)));
        $this->db->prepare("UPDATE `{$this->table}` SET $set WHERE `{$this->pk}` = ?")
                 ->execute([...array_values($data), $id]);
    }

    // Supprime la ligne d'ID $id
    public function delete(int $id): void
    {
        $this->db->prepare("DELETE FROM `{$this->table}` WHERE `{$this->pk}` = ?")
                 ->execute([$id]);
    }

    // Compte le nombre de lignes correspondant à une condition WHERE (utilisé par paginate)
    public function count(string $where = '1=1', array $params = []): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `{$this->table}` WHERE $where");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    // Pagination générique : calcule le nombre total de pages et retourne
    // les lignes de la page demandée avec LIMIT/OFFSET, plus les métadonnées
    // (total, page courante, nombre de pages...) utilisées par les vues.
    public function paginate(string $where, array $params, int $perPage, int $page, string $order = ''): array
    {
        if ($order === '') $order = "{$this->pk} DESC";
        $total  = $this->count($where, $params);
        $pages  = max(1, (int)ceil($total / $perPage));
        $page   = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->table}` WHERE $where ORDER BY $order LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        return [
            'rows'     => $rows,
            'total'    => $total,
            'per_page' => $perPage,
            'page'     => $page,
            'pages'    => $pages,
            'offset'   => $offset,
        ];
    }

    /** Exécute une requête SQL préparée arbitraire et retourne toutes les lignes */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
