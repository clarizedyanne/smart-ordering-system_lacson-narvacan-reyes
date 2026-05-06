<?php
// users.php – Staff User Management (Admin only)
session_start();
require_once 'classes/Auth.php';
require_once 'classes/User.php';

$auth = new Auth();
$auth->requireLogin();

if (!$auth->isAdmin()) {
    header('Location: dashboard.php');
    exit;
}

$userModel   = new User();
$currentUser = $auth->currentUser();
$message     = '';
$messageType = 'success';
$editData    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = $_POST['action']    ?? '';
    $username  = trim($_POST['username']  ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password  = $_POST['password']  ?? '';
    $role      = $_POST['role']      ?? 'staff';

    if ($action === 'create') {
        if (empty($username) || empty($full_name) || empty($password)) {
            $message = 'All fields are required.';
            $messageType = 'danger';
        } elseif ($userModel->create($username, $password, $full_name, $role)) {
            $message = 'User created successfully!';
        } else {
            $message = 'Failed. Username might already exist.';
            $messageType = 'danger';
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === (int)$currentUser['id']) {
            $message = 'You cannot edit your own account here.';
            $messageType = 'warning';
        } elseif ($userModel->update($id, $full_name, $role)) {
            $message = 'User updated!';
        } else {
            $message = 'Update failed.';
            $messageType = 'danger';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === (int)$currentUser['id']) {
            $message = 'You cannot delete your own account.';
            $messageType = 'warning';
        } elseif ($userModel->delete($id)) {
            $message = 'User deleted.';
        } else {
            $message = 'Failed to delete user.';
            $messageType = 'danger';
        }
    }
}

if (isset($_GET['edit'])) {
    $editData = $userModel->findById((int)$_GET['edit']);
}

$users     = $userModel->getAll();
$pageTitle = 'Staff Users';
include 'partials/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-info-circle-fill"></i>
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-plus-fill me-2 text-warning"></i>
                <?= $editData ? 'Edit User' : 'Add Staff User' ?>
            </div>
            <div class="card-body p-3">
                <form method="POST" action="users.php">
                    <input type="hidden" name="action" value="<?= $editData ? 'update' : 'create' ?>">
                    <?php if ($editData): ?>
                        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
                    <?php endif; ?>

                    <?php if (!$editData): ?>
                    <div class="mb-3">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control"
                               value="<?= htmlspecialchars($editData['full_name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="staff"  <?= (isset($editData['role']) && $editData['role'] === 'staff')  ? 'selected' : '' ?>>Staff</option>
                            <option value="admin"  <?= (isset($editData['role']) && $editData['role'] === 'admin')  ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <?= $editData ? 'Update User' : 'Create User' ?>
                        </button>
                        <?php if ($editData): ?>
                            <a href="users.php" class="btn btn-outline-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people-fill me-2 text-warning"></i>All Users (<?= count($users) ?>)
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><strong>#<?= $u['id'] ?></strong></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['full_name']) ?></td>
                            <td><span class="status-badge <?= $u['role'] === 'admin' ? 'status-completed' : 'status-processing' ?>">
                                <?= ucfirst($u['role']) ?>
                            </span></td>
                            <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <?php if ($u['id'] != $currentUser['id']): ?>
                                    <a href="users.php?edit=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="users.php" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                data-confirm="Delete this user?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size:.78rem;">(you)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
