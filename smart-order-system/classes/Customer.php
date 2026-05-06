<?php
// classes/Customer.php

require_once __DIR__ . '/Database.php';

class Customer {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT c.*, u.full_name AS created_by_name
             FROM customers c
             LEFT JOIN users u ON c.created_by = u.id
             ORDER BY c.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.full_name AS created_by_name
             FROM customers c
             LEFT JOIN users u ON c.created_by = u.id
             WHERE c.id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(string $name, string $email, string $phone, string $address, int $created_by): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO customers (name, email, phone, address, created_by)
             VALUES (:name, :email, :phone, :address, :created_by)"
        );
        return $stmt->execute([
            ':name'       => $name,
            ':email'      => $email,
            ':phone'      => $phone,
            ':address'    => $address,
            ':created_by' => $created_by,
        ]);
    }

    public function update(int $id, string $name, string $email, string $phone, string $address): bool {
        $stmt = $this->db->prepare(
            "UPDATE customers SET name = :name, email = :email, phone = :phone, address = :address WHERE id = :id"
        );
        return $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':phone'   => $phone,
            ':address' => $address,
            ':id'      => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM customers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function count(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM customers")->fetchColumn();
    }
}
