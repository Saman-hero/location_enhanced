<?php
namespace App\Models;

use App\Core\Model;

class Incident extends Model
{
    protected string $table = 'sinistres';

    public function paginatedList(string $search, string $statut, int $perPage, int $page): array
    {
        $where  = ['1=1'];
        $params = [];
        if ($search) {
            $where[]  = "(s.reference LIKE ? OR v.marque LIKE ? OR v.modele LIKE ?)";
            $params   = array_merge($params, array_fill(0, 3, "%$search%"));
        }
        if ($statut) { $where[] = "s.statut = ?"; $params[] = $statut; }

        $whereStr = implode(' AND ', $where);
        $cntStmt  = $this->db->prepare(
            "SELECT COUNT(*) FROM sinistres s JOIN vehicles v ON v.id = s.vehicle_id LEFT JOIN clients c ON c.id = s.client_id WHERE $whereStr"
        );
        $cntStmt->execute($params);
        $total  = (int)$cntStmt->fetchColumn();
        $pages  = max(1, (int)ceil($total / $perPage));
        $page   = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT s.*, v.numero, v.marque, v.modele, c.nom AS client_nom, c.prenom AS client_prenom
             FROM sinistres s
             JOIN vehicles v ON v.id = s.vehicle_id
             LEFT JOIN clients c ON c.id = s.client_id
             WHERE $whereStr
             ORDER BY s.id DESC
             LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        return ['rows' => $stmt->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'page' => $page, 'pages' => $pages, 'offset' => $offset];
    }
}
