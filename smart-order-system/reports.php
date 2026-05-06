<?php
// reports.php – JOIN query display page
session_start();
require_once 'classes/Auth.php';
require_once 'classes/Order.php';
require_once 'classes/Customer.php';

$auth = new Auth();
$auth->requireLogin();

$orderModel    = new Order();
$customerModel = new Customer();

$orders    = $orderModel->getAllWithDetails();
$pageTitle = 'Reports';
include 'partials/header.php';
?>

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="text-muted mb-1" style="font-size:.8rem;font-weight:600;text-transform:uppercase;">Total Orders</div>
            <div style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;"><?= count($orders) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="text-muted mb-1" style="font-size:.8rem;font-weight:600;text-transform:uppercase;">Total Revenue</div>
            <?php
                $rev = array_reduce($orders, fn($carry, $o) => $carry + ($o['status'] !== 'cancelled' ? $o['price'] * $o['quantity'] : 0), 0);
            ?>
            <div style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;">₱<?= number_format($rev, 2) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="text-muted mb-1" style="font-size:.8rem;font-weight:600;text-transform:uppercase;">Completed Orders</div>
            <?php $completed = count(array_filter($orders, fn($o) => $o['status'] === 'completed')); ?>
            <div style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;"><?= $completed ?></div>
        </div>
    </div>
</div>

<!-- Main JOIN Results Table -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-diagram-3-fill me-2 text-warning"></i>
        Transaction Report
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="10">
                        <div class="empty-state">
                            <i class="bi bi-bar-chart"></i>
                            <p>No data yet. Add customers and orders first.</p>
                        </div>
                    </td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><strong>#<?= $o['id'] ?></strong></td>
                        <td><?= htmlspecialchars($o['customer_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($o['customer_email'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($o['product_name']) ?></td>
                        <td><?= $o['quantity'] ?></td>
                        <td>₱<?= number_format($o['price'], 2) ?></td>
                        <td>₱<?= number_format($o['price'] * $o['quantity'], 2) ?></td>
                        <td><span class="status-badge status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                        <td><?= htmlspecialchars($o['created_by_name'] ?? '—') ?></td>
                        <td><?= date('M d, Y g:ia', strtotime($o['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Status Breakdown -->
<div class="card mt-4">
    <div class="card-header"><i class="bi bi-pie-chart-fill me-2 text-warning"></i>Orders by Status</div>
    <div class="card-body">
        <?php
        $statuses = ['pending','processing','completed','cancelled'];
        $counts = [];
        foreach ($statuses as $s) {
            $counts[$s] = count(array_filter($orders, fn($o) => $o['status'] === $s));
        }
        $total = count($orders) ?: 1;
        ?>
        <div class="row g-3">
            <?php foreach ($statuses as $s): ?>
            <div class="col-sm-6 col-md-3">
                <div class="p-3 rounded-3 text-center" style="background:var(--surface-2);">
                    <span class="status-badge status-<?= $s ?>"><?= ucfirst($s) ?></span>
                    <div style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;margin-top:8px;">
                        <?= $counts[$s] ?>
                    </div>
                    <div class="text-muted" style="font-size:.78rem;">
                        <?= round($counts[$s] / $total * 100) ?>%
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
