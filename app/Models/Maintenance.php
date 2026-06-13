<?php
namespace App\Models;

use App\Core\Model;

class Maintenance extends Model
{
    protected string $table = 'maintenance';

    public function paginatedList(string $search, string $statut, int $perPage, int $page): array
    {
        $where  = ['1=1'];
        $params = [];
        if ($search) {
            $where[]  = "(v.numero LIKE ? OR v.marque LIKE ? OR v.modele LIKE ? OR m.type_maintenance LIKE ?)";
            $params   = array_merge($params, array_fill(0, 4, "%$search%"));
        }
        if ($statut) { $where[] = "m.statut = ?"; $params[] = $statut; }

        $whereStr = implode(' AND ', $where);
        $cntStmt  = $this->db->prepare(
            "SELECT COUNT(*) FROM maintenance m JOIN vehicles v ON v.id = m.vehicle_id WHERE $whereStr"
        );
        $cntStmt->execute($params);
        $total  = (int)$cntStmt->fetchColumn();
        $pages  = max(1, (int)ceil($total / $perPage));
        $page   = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT m.*, v.numero, v.marque, v.modele
             FROM maintenance m
             JOIN vehicles v ON v.id = m.vehicle_id
             WHERE $whereStr
             ORDER BY m.id DESC
             LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        return ['rows' => $stmt->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'page' => $page, 'pages' => $pages, 'offset' => $offset];
    }
}
