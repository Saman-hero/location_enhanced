<?php
namespace App\Models;

use App\Core\Model;

class Reservation extends Model
{
    protected string $table = 'reservations';

    public function withDetails(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*,
                    v.marque, v.modele, v.immatriculation, v.numero AS vehicle_numero,
                    v.categorie AS vehicle_categorie, v.carburant AS vehicle_carburant,
                    v.image_url, v.annee AS vehicle_annee,
                    c.nom AS client_nom, c.prenom AS client_prenom,
                    c.telephone AS client_tel, c.email AS client_email, c.cin AS client_cin
             FROM reservations r
             JOIN vehicles v ON v.id = r.vehicle_id
             JOIN clients  c ON c.id = r.client_id
             WHERE r.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function paginatedList(string $search, string $statut, int $perPage, int $page): array
    {
        $where  = ['1=1'];
        $params = [];
        if ($search) {
            $where[]  = "(r.reference LIKE ? OR c.nom LIKE ? OR c.prenom LIKE ? OR v.immatriculation LIKE ?)";
            $params   = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%"]);
        }
        if ($statut) { $where[] = "r.statut = ?"; $params[] = $statut; }

        $whereStr = implode(' AND ', $where);

        $cntStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM reservations r
             JOIN clients c ON c.id = r.client_id
             JOIN vehicles v ON v.id = r.vehicle_id
             WHERE $whereStr"
        );
        $cntStmt->execute($params);
        $total  = (int)$cntStmt->fetchColumn();
        $pages  = max(1, (int)ceil($total / $perPage));
        $page   = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT r.*, v.marque, v.modele, v.numero AS vehicle_numero,
                    c.nom AS client_nom, c.prenom AS client_prenom
             FROM reservations r
             JOIN clients c ON c.id = r.client_id
             JOIN vehicles v ON v.id = r.vehicle_id
             WHERE $whereStr
             ORDER BY r.id DESC
             LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute($params);

        return [
            'rows'     => $stmt->fetchAll(),
            'total'    => $total,
            'per_page' => $perPage,
            'page'     => $page,
            'pages'    => $pages,
            'offset'   => $offset,
        ];
    }

    public function generateReference(): string
    {
        return 'LOC-' . date('Y') . '-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}
