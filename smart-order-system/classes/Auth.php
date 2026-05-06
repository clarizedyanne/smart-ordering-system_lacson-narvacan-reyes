<?php
// classes/Auth.php

require_once __DIR__ . '/User.php';

class Auth {
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new User();
    }

    public function login(string $username, string $password): bool {
        $user = $this->userModel->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['username']   = $user['username'];
            $_SESSION['full_name']  = $user['full_name'];
            $_SESSION['role']       = $user['role'];
            return true;
        }
        return false;
    }

    public function logout(): void {
        session_destroy();
        header('Location: login.php');
        exit;
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public function requireLogin(): void {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    public function isAdmin(): bool {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public function currentUser(): array {
        return [
            'id'        => $_SESSION['user_id']   ?? null,
            'username'  => $_SESSION['username']  ?? null,
            'full_name' => $_SESSION['full_name'] ?? null,
            'role'      => $_SESSION['role']      ?? null,
        ];
    }
}
