<?php
// developers.php
session_start();
require_once 'classes/Auth.php';
$auth = new Auth();
$auth->requireLogin();
$pageTitle = 'Developers';
include 'partials/header.php';

$developers = [
    [
        'name'         => 'Lacson, Jennifer P.',
        'role'         => 'Full-Stack Developer',
        'contribution' => 'Authentication system, Database design, OOP classes (Auth, User, Database)',
        'github'       => '#',
        'initial'      => 'JL',
        'photo'        => 'assets/images/eper.jpg',
    ],
    [
        'name'         => 'Narvacan, Lea Chelsy K.',
        'role'         => 'Backend Developer',
        'contribution' => 'Customer & Order CRUD, JOIN queries, Transaction flow logic',
        'github'       => '#',
        'initial'      => 'LN',
        'photo'        => 'assets/images/lea.jpg',
    ],
    [
        'name'         => 'Reyes, Clarize Dyanne R.',
        'role'         => 'Frontend Developer',
        'contribution' => 'UI/UX design with Bootstrap, Reports page, Deployment to InfinityFree',
        'github'       => '#',
        'initial'      => 'CR',
        'photo'        => 'assets/images/claire.jpg',
    ],
];
?>

<div class="mb-4 text-center">
    <h2 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;letter-spacing:-.5px;">Meet the Team</h2>
    <p class="text-muted" style="font-size:.9rem;">ITEL 203 Group Performance Task 3 – Order Management System</p>
</div>

<div class="row g-4 justify-content-center mb-5">
    <?php foreach ($developers as $dev): ?>
    <div class="col-md-4">
        <div class="dev-card">
        <?php if (!empty($dev['photo'])): ?>
            <img src="<?= htmlspecialchars($dev['photo']) ?>"
                alt="<?= htmlspecialchars($dev['name']) ?>"
                style="width:72px;height:72px;border-radius:50%;object-fit:cover;margin:0 auto 16px;display:block;border:3px solid var(--accent);">
        <?php else: ?>
        <div class="dev-avatar"><?= htmlspecialchars($dev['initial']) ?></div>
        <?php endif; ?> 
            <div class="dev-name"><?= htmlspecialchars($dev['name']) ?></div>
            <div class="dev-role"><?= htmlspecialchars($dev['role']) ?></div>
            <hr style="margin:16px 0;">
            <p style="font-size:.82rem;color:#64748b;line-height:1.7;">
                <?= htmlspecialchars($dev['contribution']) ?>
            </p>
            <?php if ($dev['github'] !== '#'): ?>
            <a href="<?= htmlspecialchars($dev['github']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">
                <i class="bi bi-github me-1"></i>GitHub
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- OOP Structure -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-code-slash me-2 text-warning"></i>OOP Class Structure
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <?php
            $classes = [
                ['Database.php',  '#6366f1', 'Singleton PDO connection. Provides a shared database instance across the entire application.'],
                ['User.php',      '#f59e0b', 'Handles user CRUD operations: findByUsername, findById, getAll, create, update, delete.'],
                ['Auth.php',      '#ef4444', 'Session-based authentication: login(), logout(), isLoggedIn(), requireLogin(), isAdmin().'],
                ['Customer.php',  '#22c55e', 'Customer entity management with JOIN to users. Full CRUD + count().'],
                ['Order.php',     '#3b82f6', 'Transaction class. Full CRUD with multi-table JOINs (orders + customers + users).'],
            ];
            foreach ($classes as $c): ?>
            <div class="col-md-6 col-lg-4">
                <div class="p-3 rounded-3" style="background:var(--surface-2);border:1px solid var(--border);height:100%;">
                    <div style="font-family:monospace;font-weight:700;color:<?= $c[1] ?>;font-size:.9rem;margin-bottom:8px;">
                        📄 <?= $c[0] ?>
                    </div>
                    <p style="font-size:.8rem;color:#64748b;margin:0;line-height:1.6;"><?= $c[2] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
