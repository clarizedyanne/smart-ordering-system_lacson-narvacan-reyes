<?php
// partials/header.php
// Usage: include after Auth check at top of each page
// $pageTitle should be set before including this file.
$pageTitle = $pageTitle ?? 'Smart Order System';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$user = (new Auth())->currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> – Smart Order System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon"><i class="bi bi-box-seam-fill"></i></span>
        <span class="brand-text">OrderEase</span>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>
        <a href="customers.php" class="nav-link <?= $currentPage === 'customers' ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i><span>Customers</span>
        </a>
        <a href="orders.php" class="nav-link <?= $currentPage === 'orders' ? 'active' : '' ?>">
            <i class="bi bi-cart-fill"></i><span>Orders</span>
        </a>
        <a href="reports.php" class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-line-fill"></i><span>Reports</span>
        </a>
        <a href="ml_dashboard.php" class="nav-link <?= $currentPage === 'ml_dashboard' ? 'active' : '' ?>">
            <i class="bi bi-cpu-fill"></i><span>Staff Insights</span>
        </a>
        <?php if ($user['role'] === 'admin'): ?>
        <a href="users.php" class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>">
            <i class="bi bi-person-badge-fill"></i><span>Staff Users</span>
        </a>
        <?php endif; ?>
        <a href="about.php" class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>">
            <i class="bi bi-info-circle-fill"></i><span>About</span>
        </a>
        <a href="developers.php" class="nav-link <?= $currentPage === 'developers' ? 'active' : '' ?>">
            <i class="bi bi-code-slash"></i><span>Developers</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
            <div class="user-details">
                <span class="user-name"><?= htmlspecialchars($user['full_name']) ?></span>
                <span class="user-role badge-role-<?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span>
            </div>
        </div>
        <a href="logout.php" class="logout-btn" title="Logout"><i class="bi bi-box-arrow-right"></i></a>
    </div>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <div class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
        <div class="topbar-right">
            <span class="topbar-time" id="clock"></span>
        </div>
    </div>
    <div class="content-area">
