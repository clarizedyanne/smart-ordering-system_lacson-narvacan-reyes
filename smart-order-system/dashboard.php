<?php
// dashboard.php
session_start();
require_once 'classes/Auth.php';
require_once 'classes/Customer.php';
require_once 'classes/Order.php';

$auth = new Auth();
$auth->requireLogin();

$customerModel = new Customer();
$orderModel    = new Order();

$totalCustomers = $customerModel->count();
$totalOrders    = $orderModel->count();
$totalRevenue   = $orderModel->totalRevenue();
$recentOrders   = $orderModel->recentOrders(5);

$pageTitle = 'Dashboard';
include 'partials/header.php';
?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-value"><?= $totalCustomers ?></div>
                <div class="stat-label">Total Customers</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-cart-fill"></i></div>
            <div>
                <div class="stat-value"><?= $totalOrders ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-currency-dollar"></i></div>
            <div>
                <div class="stat-value">₱<?= number_format($totalRevenue, 2) ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="bi bi-person-badge-fill"></i></div>
            <div>
                <div class="stat-value"><?= htmlspecialchars($auth->currentUser()['role']) ?></div>
                <div class="stat-label">Your Role</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table (JOIN query demo) -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-clock-history me-2 text-warning"></i>Recent Orders</span>
        <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="8">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>No orders yet. <a href="orders.php">Add one</a></p>
                        </div>
                    </td></tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td><strong>#<?= $order['id'] ?></strong></td>
                        <td><?= htmlspecialchars($order['customer_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td>₱<?= number_format($order['price'], 2) ?></td>
                        <td><span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                        <td><?= htmlspecialchars($order['created_by_name'] ?? '—') ?></td>
                        <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
