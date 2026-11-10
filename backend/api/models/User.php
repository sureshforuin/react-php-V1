<?php
/**
 * User model — wraps all DB queries for the users table.
 */

declare(strict_types=1);

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM users ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $name, string $email): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, status, created_at) VALUES (?, ?, ?, NOW())'
        );
        $stmt->execute([$name, $email, 'active']);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $email, string $status): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET name = ?, email = ?, status = ? WHERE id = ?'
        );
        $stmt->execute([$name, $email, $status, $id]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
