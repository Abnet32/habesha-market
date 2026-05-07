<?php
$page_title = 'Order Placed!';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$order_number = $_SESSION['order_success'] ?? '';
unset($_SESSION['order_success']);
if (!$order_number) { header('Location: orders.php'); exit; }
require 'includes/header.php';
?>

<div style="min-height:80vh; display:flex; align-items:center; justify-content:center; padding:100px 1rem 3rem;">
    <div style="text-align:center; max-width:550px;">
        <!-- Animated checkmark -->
        <div style="width:100px; height:100px; border-radius:50%; background:linear-gradient(135deg,var(--primary),var(--primary-light)); display:flex; align-items:center; justify-content:center; font-size:3rem; margin:0 auto 2rem; animation:float 3s ease-in-out infinite; box-shadow:0 20px 50px rgba(7,137,48,0.4);">
            <i class="fas fa-check-circle" style="color:white;"></i>
        </div>

        <div class="section-badge" style="margin-bottom:1rem;"><i class="fas fa-party-horn"></i> Order Confirmed</div>
        <h1 style="font-family:'Playfair Display',serif; font-size:2.2rem; font-weight:800; margin-bottom:0.8rem;">
            Thank You! <span style="background:linear-gradient(135deg,var(--secondary),#ff9500);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Your Order is Placed</span>
        </h1>
        <p style="color:var(--text-muted); font-size:1rem; line-height:1.7; margin-bottom:2rem;">
            Your order <strong style="color:var(--primary-light);">#<?= htmlspecialchars($order_number) ?></strong> has been received and is being processed. You'll receive updates via phone.
        </p>

        <!-- Order info box -->
        <div style="background:var(--glass); border:1px solid var(--glass-border); border-radius:var(--radius); padding:1.5rem; margin-bottom:2rem; text-align:left;">
            <div style="display:flex; justify-content:space-between; margin-bottom:0.8rem; font-size:0.88rem;">
                <span style="color:var(--text-muted);">Order Number</span>
                <span style="font-weight:700; color:var(--primary-light);">#<?= htmlspecialchars($order_number) ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:0.8rem; font-size:0.88rem;">
                <span style="color:var(--text-muted);">Estimated Delivery</span>
                <span style="font-weight:600;">2–5 Business Days</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:0.88rem;">
                <span style="color:var(--text-muted);">Status</span>
                <span class="order-status status-pending">Pending</span>
            </div>
        </div>

        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="orders.php" class="btn-primary-custom">
                <span><i class="fas fa-box"></i> View My Orders</span>
            </a>
            <a href="products.php" class="btn-outline-custom">
                <i class="fas fa-store"></i> Continue Shopping
            </a>
        </div>

        <p style="margin-top:2rem; font-size:0.82rem; color:var(--text-muted);">
            Need help? Contact us at <a href="tel:+251911000000" style="color:var(--primary-light);">+251 911 000 000</a>
        </p>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
