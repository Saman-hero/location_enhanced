<?php
namespace App\Models;

use App\Core\Model;

class AuditLog extends Model
{
    protected string $table = 'audit_log';

    public function log(string $action, string $module, string $details = ''): void
    {
        $this->db->prepare(
            "INSERT INTO audit_log (user_id, username, action, module, details, ip_address)
             VALUES (?,?,?,?,?,?)"
        )->execute([
            $_SESSION['user_id']  ?? null,
            $_SESSION['username'] ?? 'system',
            $action, $module, $details,
            $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }

    public function paginatedList(string $search, string $module, string $dateFrom, string $dateTo, int $perPage, int $page): array
    {
        $where  = ['1=1'];
        $params = [];
        if ($module)   { $where[] = "module = ?";                    $params[] = $module; }
        if ($search)   { $where[] = "(username LIKE ? OR action LIKE ? OR details LIKE ?)"; $params = array_merge($params, array_fill(0, 3, "%$search%")); }
        if ($dateFrom) { $where[] = "DATE(created_at) >= ?";         $params[] = $dateFrom; }
        if ($dateTo)   { $where[] = "DATE(created_at) <= ?";         $params[] = $dateTo; }
        return $this->paginate(implode(' AND ', $where), $params, $perPage, $page, 'created_at DESC');
    }

    public function distinctModules(): array
    {
        return $this->db->query("SELECT DISTINCT module FROM audit_log ORDER BY module")
                        ->fetchAll(\PDO::FETCH_COLUMN);
    }
}
