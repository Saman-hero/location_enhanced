<?php
namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected string $table = 'paiements';

    public function paginatedList(string $search, int $perPage, int $page): array
    {
        $where  = ['1=1'];
        $params = [];
        if ($search) {
            $where[]  = "(r.reference LIKE ? OR c.nom LIKE ? OR c.prenom LIKE ?)";
            $params   = array_merge($params, array_fill(0, 3, "%$search%"));
        }
        $whereStr = implode(' AND ', $where);
        $cntStmt  = $this->db->prepare(
            "SELECT COUNT(*) FROM paiements p
             JOIN reservations r ON r.id = p.reservation_id
             JOIN clients c ON c.id = r.client_id
             WHERE $whereStr"
        );
        $cntStmt->execute($params);
        $total  = (int)$cntStmt->fetchColumn();
        $pages  = max(1, (int)ceil($total / $perPage));
        $page   = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT p.*, r.reference, c.nom AS client_nom, c.prenom AS client_prenom
             FROM paiements p
             JOIN reservations r ON r.id = p.reservation_id
             JOIN clients c ON c.id = r.client_id
             WHERE $whereStr
             ORDER BY p.id DESC
             LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);
        return ['rows' => $stmt->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'page' => $page, 'pages' => $pages, 'offset' => $offset];
    }
}
