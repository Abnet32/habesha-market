<?php
$page_title = 'Order Placed!';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$order_number = $_SESSION['order_success'] ?? '';
unset($_SESSION['order_success']);
if (!$order_number) { header('Location: orders.php'); exit; }
require 'includes/header.php';
?>

<div class="order-success-shell">
    <div class="order-success-card">
        <!-- Animated checkmark -->
        <div class="success-mark">
            <i class="fas fa-check-circle success-mark-icon"></i>
        </div>

        <div class="section-badge success-badge-space"><i class="fas fa-party-horn"></i> Order Confirmed</div>
        <h1 class="success-title">
            Thank You! <span class="success-title-highlight">Your Order is Placed</span>
        </h1>
        <p class="success-copy">
            Your order <strong class="success-order-number">#<?= htmlspecialchars($order_number) ?></strong> has been received and is being processed. You'll receive updates via phone.
        </p>

        <!-- Order info box -->
        <div class="card-info card-info-left mb-2">
            <div class="info-row">
                <span class="text-muted">Order Number</span>
                <span class="text-primary-light font-semibold">#<?= htmlspecialchars($order_number) ?></span>
            </div>
            <div class="info-row">
                <span class="text-muted">Estimated Delivery</span>
                <span class="font-medium">2–5 Business Days</span>
            </div>
            <div class="info-row-plain">
                <span class="text-muted">Status</span>
                <span class="order-status status-pending">Pending</span>
            </div>
        </div>

        <div class="success-actions">
            <a href="orders.php" class="btn-primary-custom">
                <span><i class="fas fa-box"></i> View My Orders</span>
            </a>
            <a href="products.php" class="btn-outline-custom">
                <i class="fas fa-store"></i> Continue Shopping
            </a>
        </div>

        <p class="success-help">
            Need help? Contact us at <a href="tel:+251911000000" class="success-help-link">+251 911 000 000</a>
        </p>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
