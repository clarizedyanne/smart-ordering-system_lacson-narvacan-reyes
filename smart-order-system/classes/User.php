<?php
// classes/User.php

require_once __DIR__ . '/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByUsername(string $username): array|false {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT id, username, full_name, role, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT id, username, full_name, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function create(string $username, string $password, string $full_name, string $role = 'staff'): bool {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, password, full_name, role) VALUES (:username, :password, :full_name, :role)"
        );
        return $stmt->execute([
            ':username'  => $username,
            ':password'  => $hashed,
            ':full_name' => $full_name,
            ':role'      => $role,
        ]);
    }

    public function update(int $id, string $full_name, string $role): bool {
        $stmt = $this->db->prepare(
            "UPDATE users SET full_name = :full_name, role = :role WHERE id = :id"
        );
        return $stmt->execute([':full_name' => $full_name, ':role' => $role, ':id' => $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
