<?php
// orders.php – Full CRUD for Orders
session_start();
require_once 'classes/Auth.php';
require_once 'classes/Order.php';
require_once 'classes/Customer.php';

$auth = new Auth();
$auth->requireLogin();
$currentUser = $auth->currentUser();

$orderModel    = new Order();
$customerModel = new Customer();
$message = '';
$messageType = 'success';
$editData = null;

// ── Handle POST ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action       = $_POST['action']       ?? '';
    $customer_id  = (int)($_POST['customer_id']  ?? 0);
    $product_name = trim($_POST['product_name'] ?? '');
    $quantity     = (int)($_POST['quantity']     ?? 1);
    $price        = (float)($_POST['price']      ?? 0);
    $status       = $_POST['status']       ?? 'pending';
    $notes        = trim($_POST['notes']   ?? '');

    if (in_array($action, ['create', 'update']) && (empty($customer_id) || empty($product_name) || $price <= 0)) {
        $message = 'Customer, Product, and Price are required.';
        $messageType = 'danger';
    } elseif ($action === 'create') {
        if ($orderModel->create($customer_id, $product_name, $quantity, $price, $status, $notes, $currentUser['id'])) {
            $message = 'Order created successfully!';
        } else {
            $message = 'Failed to create order.';
            $messageType = 'danger';
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        if ($orderModel->update($id, $customer_id, $product_name, $quantity, $price, $status, $notes)) {
            $message = 'Order updated successfully!';
        } else {
            $message = 'Failed to update order.';
            $messageType = 'danger';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($orderModel->delete($id)) {
            $message = 'Order deleted.';
        } else {
            $message = 'Failed to delete order.';
            $messageType = 'danger';
        }
    }
}

// ── Edit mode ─────────────────────────────────────────────────
if (isset($_GET['edit'])) {
    $editData = $orderModel->findById((int)$_GET['edit']);
}

$orders    = $orderModel->getAllWithDetails();
$customers = $customerModel->getAll();
$pageTitle  = 'Orders';
include 'partials/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-<?= $messageType === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill' ?>"></i>
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Form -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cart-plus-fill me-2 text-warning"></i>
                <?= $editData ? 'Edit Order' : 'New Order' ?>
            </div>
            <div class="card-body p-3">
                <form method="POST" action="orders.php">
                    <input type="hidden" name="action" value="<?= $editData ? 'update' : 'create' ?>">
                    <?php if ($editData): ?>
                        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Customer *</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">— Select Customer —</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?= $c['id'] ?>"
                                    <?= (isset($editData['customer_id']) && $editData['customer_id'] == $c['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="product_name" class="form-control"
                               value="<?= htmlspecialchars($editData['product_name'] ?? '') ?>" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Quantity *</label>
                            <input type="number" name="quantity" class="form-control" min="1"
                                   value="<?= $editData['quantity'] ?? 1 ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Price (₱) *</label>
                            <input type="number" name="price" class="form-control" min="0.01" step="0.01"
                                   value="<?= $editData['price'] ?? '' ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <?php foreach (['pending','processing','completed','cancelled'] as $s): ?>
                                <option value="<?= $s ?>" <?= (isset($editData['status']) && $editData['status'] === $s) ? 'selected' : '' ?>>
                                    <?= ucfirst($s) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($editData['notes'] ?? '') ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-<?= $editData ? 'pencil' : 'plus-lg' ?> me-1"></i>
                            <?= $editData ? 'Update Order' : 'Create Order' ?>
                        </button>
                        <?php if ($editData): ?>
                            <a href="orders.php" class="btn btn-outline-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cart-fill me-2 text-warning"></i>
                All Orders (<?= count($orders) ?>)
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="8">
                                <div class="empty-state">
                                    <i class="bi bi-cart-x"></i>
                                    <p>No orders yet.</p>
                                </div>
                            </td></tr>
                        <?php else: ?>
                            <?php foreach ($orders as $o): ?>
                            <tr>
                                <td><strong>#<?= $o['id'] ?></strong></td>
                                <td><?= htmlspecialchars($o['customer_name'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($o['product_name']) ?></td>
                                <td><?= $o['quantity'] ?></td>
                                <td>₱<?= number_format($o['price'], 2) ?></td>
                                <td><span class="status-badge status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                                <td><?= htmlspecialchars($o['created_by_name'] ?? '—') ?></td>
                                <td>
                                    <a href="orders.php?edit=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="orders.php" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                data-confirm="Delete this order?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
