<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function usernameExists(string $username, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $excludeId]);
        return (bool)$stmt->fetch();
    }
}
