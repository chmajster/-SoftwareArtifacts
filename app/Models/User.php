<?php

declare(strict_types=1);

namespace App\Models;

final class User extends BaseModel
{
    public function create(string $username, string $email, string $password, int $roleId = 3): int
    {
        $stmt = $this->db->prepare('INSERT INTO users (username, email, password_hash, role_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $roleId]);
        return (int)$this->db->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function all(): array
    {
        return $this->db->query('SELECT u.id, u.username, u.email, u.is_blocked, u.created_at, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id ORDER BY u.id DESC')->fetchAll();
    }

    public function setBlocked(int $id, bool $blocked): void
    {
        $stmt = $this->db->prepare('UPDATE users SET is_blocked = ? WHERE id = ?');
        $stmt->execute([$blocked ? 1 : 0, $id]);
    }
}
