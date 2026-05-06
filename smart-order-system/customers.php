<?php
// customers.php – Full CRUD for Customers
session_start();
require_once 'classes/Auth.php';
require_once 'classes/Customer.php';

$auth = new Auth();
$auth->requireLogin();
$currentUser = $auth->currentUser();

$customerModel = new Customer();
$message = '';
$messageType = 'success';
$editData = null;

// ── Handle POST actions ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = $_POST['action']  ?? '';
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $address = trim($_POST['address'] ?? '');

    // Basic validation
    if (in_array($action, ['create', 'update']) && (empty($name) || empty($email))) {
        $message = 'Name and Email are required.';
        $messageType = 'danger';
    } elseif ($action === 'create') {
        if ($customerModel->create($name, $email, $phone, $address, $currentUser['id'])) {
            $message = 'Customer added successfully!';
        } else {
            $message = 'Failed to add customer. Email might already exist.';
            $messageType = 'danger';
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        if ($customerModel->update($id, $name, $email, $phone, $address)) {
            $message = 'Customer updated successfully!';
        } else {
            $message = 'Failed to update customer.';
            $messageType = 'danger';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($customerModel->delete($id)) {
            $message = 'Customer deleted.';
        } else {
            $message = 'Failed to delete customer.';
            $messageType = 'danger';
        }
    }
}

// ── Edit mode ────────────────────────────────────────────────
if (isset($_GET['edit'])) {
    $editData = $customerModel->findById((int)$_GET['edit']);
}

$customers = $customerModel->getAll();
$pageTitle  = 'Customers';
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
                <i class="bi bi-person-plus-fill me-2 text-warning"></i>
                <?= $editData ? 'Edit Customer' : 'Add New Customer' ?>
            </div>
            <div class="card-body p-3">
                <form method="POST" action="customers.php">
                    <input type="hidden" name="action" value="<?= $editData ? 'update' : 'create' ?>">
                    <?php if ($editData): ?>
                        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control"
                               value="<?= htmlspecialchars($editData['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($editData['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="<?= htmlspecialchars($editData['phone'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($editData['address'] ?? '') ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-<?= $editData ? 'pencil' : 'plus-lg' ?> me-1"></i>
                            <?= $editData ? 'Update' : 'Add Customer' ?>
                        </button>
                        <?php if ($editData): ?>
                            <a href="customers.php" class="btn btn-outline-secondary">Cancel</a>
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
                <i class="bi bi-people-fill me-2 text-warning"></i>
                All Customers (<?= count($customers) ?>)
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Added By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr><td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-people"></i>
                                    <p>No customers yet.</p>
                                </div>
                            </td></tr>
                        <?php else: ?>
                            <?php foreach ($customers as $c): ?>
                            <tr>
                                <td><strong>#<?= $c['id'] ?></strong></td>
                                <td><?= htmlspecialchars($c['name']) ?></td>
                                <td><?= htmlspecialchars($c['email']) ?></td>
                                <td><?= htmlspecialchars($c['phone'] ?: '—') ?></td>
                                <td><?= htmlspecialchars($c['created_by_name'] ?? '—') ?></td>
                                <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                                <td>
                                    <a href="customers.php?edit=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="customers.php" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                data-confirm="Delete this customer and all their orders?">
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
