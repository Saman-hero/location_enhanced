<?php
namespace App\Models;

use App\Core\Model;

class Client extends Model
{
    protected string $table = 'clients';

    public function paginatedList(string $search, string $statut, int $perPage, int $page): array
    {
        $where  = ['1=1'];
        $params = [];
        if ($search) {
            $where[]  = "(nom LIKE ? OR prenom LIKE ? OR cin LIKE ? OR telephone LIKE ? OR email LIKE ?)";
            $params   = array_merge($params, array_fill(0, 5, "%$search%"));
        }
        if ($statut) { $where[] = "statut = ?"; $params[] = $statut; }
        return $this->paginate(implode(' AND ', $where), $params, $perPage, $page, 'id DESC');
    }

    public function forSelect(): array
    {
        return $this->query("SELECT id, nom, prenom, cin FROM clients ORDER BY nom, prenom");
    }
}
