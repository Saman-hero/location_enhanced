<?php
namespace App\Core;

abstract class Model
{
    protected \PDO $db;
    protected string $table  = '';
    protected string $pk     = 'id';

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$this->pk}` = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findOrFail(int $id): array
    {
        $row = $this->find($id);
        if (!$row) {
            throw new \RuntimeException("{$this->table} #{$id} not found");
        }
        return $row;
    }

    public function all(string $order = ''): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($order) $sql .= " ORDER BY $order";
        return $this->db->query($sql)->fetchAll();
    }

    public function create(array $data): int
    {
        $cols   = implode(', ', array_map(fn($k) => "`$k`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $this->db->prepare("INSERT INTO `{$this->table}` ($cols) VALUES ($placeholders)")
                 ->execute(array_values($data));
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $set = implode(', ', array_map(fn($k) => "`$k` = ?", array_keys($data)));
        $this->db->prepare("UPDATE `{$this->table}` SET $set WHERE `{$this->pk}` = ?")
                 ->execute([...array_values($data), $id]);
    }

    public function delete(int $id): void
    {
        $this->db->prepare("DELETE FROM `{$this->table}` WHERE `{$this->pk}` = ?")
                 ->execute([$id]);
    }

    public function count(string $where = '1=1', array $params = []): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `{$this->table}` WHERE $where");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

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

    /** Run a raw prepared query and return all rows */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
