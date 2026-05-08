<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../connection.php';
    $uid = (int)$_SESSION['user_id'];
    $r   = mysqli_query($con, "SELECT SUM(quantity) as cnt FROM cart_items WHERE user_id=$uid");
    if ($r) { $row = mysqli_fetch_assoc($r); $cart_count = (int)($row['cnt'] ?? 0); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | Habesha Market' : 'Habesha Market — Ethiopian Online Marketplace' ?></title>
    <meta name="description" content="Shop authentic Ethiopian products — coffee, traditional clothing, spices, handcrafts and more.">
    <link rel="icon" href="assets/glow-cart.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<canvas id="particles-canvas"></canvas>

<!-- NAVBAR -->
<nav class="navbar-custom" id="main-navbar">
    <a href="index.php" class="navbar-brand-custom">
        <div class="brand-icon"><img class="logo" src="assets/glow-cart.svg" alt="Habesha Market"> </div>
        <span class="brand-text">Habesha Market</span>
    </a>

    <div class="hamburger" id="hamburger">
        <span></span><span></span><span></span>
    </div>

    <ul class="nav-links" id="nav-links">
        <li><a href="index.php"    class="<?= $current_page === 'index.php'    ? 'active' : '' ?>"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="products.php" class="<?= $current_page === 'products.php' ? 'active' : '' ?>"><i class="fas fa-store"></i> Shop</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <li>
                <a href="cart.php" class="<?= $current_page === 'cart.php' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <?php if ($cart_count > 0): ?><span class="cart-badge"><?= $cart_count ?></span><?php endif; ?>
                </a>
            </li>
            <li><a href="orders.php"  class="<?= $current_page === 'orders.php'  ? 'active' : '' ?>"><i class="fas fa-box"></i> Orders</a></li>
            <li><a href="profile.php" class="<?= $current_page === 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['full_name'] ?? 'Profile') ?></a></li>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <li><a href="admin/index.php" class="nav-btn-admin"><i class="fas fa-cog"></i> <span>Admin</span></a></li>
            <?php endif; ?>
            <li><a href="logout.php" class="nav-btn-logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        <?php else: ?>
            <li><a href="login.php"  class="<?= $current_page === 'login.php'  ? 'active' : '' ?>"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <li><a href="signup.php" class="nav-btn"><i class="fas fa-user-plus"></i> <span>Sign Up</span></a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Blue accent stripe -->
<div class="hero-stripe"><span></span><span></span><span></span></div>
