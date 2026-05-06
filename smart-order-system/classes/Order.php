<?php
// classes/Order.php

require_once __DIR__ . '/Database.php';

class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // JOIN query showing Order ID, Customer Name, Product, Created By
    public function getAllWithDetails(): array {
        $stmt = $this->db->query(
            "SELECT o.id, o.product_name, o.quantity, o.price, o.status, o.notes, o.created_at,
                    c.name AS customer_name, c.email AS customer_email,
                    u.full_name AS created_by_name
             FROM orders o
             LEFT JOIN customers c ON o.customer_id = c.id
             LEFT JOIN users u ON o.created_by = u.id
             ORDER BY o.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT o.*, c.name AS customer_name, u.full_name AS created_by_name
             FROM orders o
             LEFT JOIN customers c ON o.customer_id = c.id
             LEFT JOIN users u ON o.created_by = u.id
             WHERE o.id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(int $customer_id, string $product_name, int $quantity, float $price, string $status, string $notes, int $created_by): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO orders (customer_id, product_name, quantity, price, status, notes, created_by)
             VALUES (:customer_id, :product_name, :quantity, :price, :status, :notes, :created_by)"
        );
        return $stmt->execute([
            ':customer_id'   => $customer_id,
            ':product_name'  => $product_name,
            ':quantity'      => $quantity,
            ':price'         => $price,
            ':status'        => $status,
            ':notes'         => $notes,
            ':created_by'    => $created_by,
        ]);
    }

    public function update(int $id, int $customer_id, string $product_name, int $quantity, float $price, string $status, string $notes): bool {
        $stmt = $this->db->prepare(
            "UPDATE orders SET customer_id = :customer_id, product_name = :product_name,
             quantity = :quantity, price = :price, status = :status, notes = :notes
             WHERE id = :id"
        );
        return $stmt->execute([
            ':customer_id'  => $customer_id,
            ':product_name' => $product_name,
            ':quantity'     => $quantity,
            ':price'        => $price,
            ':status'       => $status,
            ':notes'        => $notes,
            ':id'           => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM orders WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function count(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    }

    public function totalRevenue(): float {
        return (float) $this->db->query("SELECT SUM(price * quantity) FROM orders WHERE status != 'cancelled'")->fetchColumn();
    }

    public function recentOrders(int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT o.id, o.product_name, o.quantity, o.price, o.status, o.created_at,
                    c.name AS customer_name, u.full_name AS created_by_name
             FROM orders o
             LEFT JOIN customers c ON o.customer_id = c.id
             LEFT JOIN users u ON o.created_by = u.id
             ORDER BY o.created_at DESC LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
