<?php
// about.php
session_start();
require_once 'classes/Auth.php';
$auth = new Auth();
$auth->requireLogin();
$pageTitle = 'About';
include 'partials/header.php';
?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-info-circle-fill me-2 text-warning"></i>About This System
            </div>
            <div class="card-body p-4">
                <h2 style="font-family:'Verdana',sans-serif;font-weight:800;font-size:1.6rem;letter-spacing:-.5px;">
                    Smart Order Management System
                </h2>
                <p class="text-muted mt-2 mb-4">ITEL 203 – Web Systems and Technologies | Group Performance Task 3</p>

                <p style="font-size:.93rem;line-height:1.8;color:#374151;">
                    This system was built to solve real-world problems faced by small businesses using manual recording.
                    It provides a centralized, secure, and efficient platform to track customers, manage orders,
                    and monitor which staff member handled each transaction.
                </p>

                <hr class="my-4">

                <h5 style="font-family:'Syne',sans-serif;font-weight:700;font-size:.95rem;margin-bottom:16px;">
                    Problems Solved
                </h5>
                <div class="row g-3">
                    <?php
                    $problems = [
                        ['bi-x-circle-fill text-danger', 'Lost Customer Records', 'All customer data is securely stored in a relational database.'],
                        ['bi-x-circle-fill text-danger', 'Confusion in Tracking Orders', 'Orders are linked to customers and staff for full traceability.'],
                        ['bi-x-circle-fill text-danger', 'No Accountability', 'Every transaction records which logged-in user created it.'],
                    ];
                    foreach ($problems as $p): ?>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background:var(--surface-2);border:1px solid var(--border);">
                            <i class="bi <?= $p[0] ?> mb-2 d-block fs-4"></i>
                            <div style="font-weight:700;font-size:.85rem;"><?= $p[1] ?></div>
                            <div class="text-muted mt-1" style="font-size:.78rem;"><?= $p[2] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <hr class="my-4">

                <h5 style="font-family:'Syne',sans-serif;font-weight:700;font-size:.95rem;margin-bottom:16px;">
                    Core Features
                </h5>
                <ul style="font-size:.9rem;line-height:2;padding-left:20px;color:#374151;">
                    <li>Secure login/logout with session-based authentication</li>
                    <li>Password hashing using <code>password_hash()</code> and <code>password_verify()</code></li>
                    <li>Full CRUD operations for Customers and Orders</li>
                    <li>Multi-table JOIN queries for relational data display</li>
                    <li>OOP PHP architecture (Database, User, Auth, Customer, Order classes)</li>
                    <li>Role-based access control (Admin vs Staff)</li>
                    <li>Clean UI using Bootstrap 5</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-stack me-2 text-warning"></i>Tech Stack
            </div>
            <div class="card-body p-3">
                <?php
                $stack = [
                    ['bi-filetype-php', '#6366f1', 'PHP 8+', 'Object-Oriented Programming'],
                    ['bi-database-fill', '#f59e0b', 'MySQL', 'Relational Database via PDO'],
                    ['bi-bootstrap-fill', '#8b5cf6', 'Bootstrap 5', 'Responsive UI Framework'],
                    ['bi-github', '#1e293b', 'GitHub', 'Version Control & Collaboration'],
                    ['bi-globe', '#22c55e', 'InfinityFree', 'Free Web Hosting & Deployment'],
                ];
                foreach ($stack as $s): ?>
                <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid var(--border);">
                    <div style="width:38px;height:38px;border-radius:10px;background:<?= $s[1] ?>22;display:flex;align-items:center;justify-content:center;color:<?= $s[1] ?>;font-size:1.2rem;flex-shrink:0;">
                        <i class="bi <?= $s[0] ?>"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:.88rem;"><?= $s[2] ?></div>
                        <div class="text-muted" style="font-size:.75rem;"><?= $s[3] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-diagram-2-fill me-2 text-warning"></i>Database Schema
            </div>
            <div class="card-body p-3">
                <div style="font-size:.82rem;font-family:monospace;background:var(--primary);color:#e2e8f0;padding:16px;border-radius:10px;line-height:2;">
                    <span style="color:#f97316;">users</span> (id, username, password, full_name, role)<br>
                    &nbsp;&nbsp;↓ <span style="color:#94a3b8;">ONE-TO-MANY</span><br>
                    <span style="color:#f97316;">customers</span> (id, name, email, phone, address, <span style="color:#fbbf24;">created_by→users</span>)<br>
                    &nbsp;&nbsp;↓ <span style="color:#94a3b8;">ONE-TO-MANY</span><br>
                    <span style="color:#f97316;">orders</span> (id, <span style="color:#fbbf24;">customer_id→customers</span>, product_name, qty, price, status, <span style="color:#fbbf24;">created_by→users</span>)
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
