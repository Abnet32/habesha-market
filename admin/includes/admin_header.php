<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}
$admin_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | Admin' : 'Admin Panel | Habesha Market' ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚙️</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<canvas id="particles-canvas"></canvas>

<!-- TOP NAVBAR -->
<nav class="navbar-custom" id="main-navbar">
    <a href="../index.php" class="navbar-brand-custom">
        <div class="brand-icon">⚙️</div>
        <span class="brand-text">HM Admin</span>
    </a>
    <ul class="nav-links">
        <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a></li>
        <li><a href="../logout.php" class="nav-btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>
<div class="hero-stripe"><span></span><span></span><span></span></div>

<div class="admin-layout">
    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
        <nav class="admin-nav">
            <div class="admin-nav-section">Dashboard</div>
            <a href="index.php" class="admin-nav-item <?= $admin_page === 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Overview
            </a>

            <div class="admin-nav-section">Catalogue</div>
            <a href="products.php" class="admin-nav-item <?= $admin_page === 'products.php' ? 'active' : '' ?>">
                <i class="fas fa-box-open"></i> Products
            </a>
            <a href="add_product.php" class="admin-nav-item <?= $admin_page === 'add_product.php' ? 'active' : '' ?>">
                <i class="fas fa-plus-circle"></i> Add Product
            </a>

            <div class="admin-nav-section">Users & Orders</div>
            <a href="orders.php" class="admin-nav-item <?= $admin_page === 'orders.php' ? 'active' : '' ?>">
                <i class="fas fa-receipt"></i> Orders
            </a>
            <a href="users.php" class="admin-nav-item <?= $admin_page === 'users.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Users
            </a>

            <div class="admin-nav-section">Account</div>
            <a href="../profile.php" class="admin-nav-item">
                <i class="fas fa-user-cog"></i> My Profile
            </a>
            <a href="../logout.php" class="admin-nav-item" style="color:#fb7185">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT (opened by admin pages) -->
    <main class="admin-main">
