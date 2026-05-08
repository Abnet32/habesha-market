<?php
$page_title = 'My Orders';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once 'connection.php';
global $con;

$uid = (int)$_SESSION['user_id'];
$orders = mysqli_query($con, "SELECT * FROM orders WHERE user_id=$uid ORDER BY created_at DESC");

require 'includes/header.php';
?>

<div class="page-hero">
    <div class="container-custom">
        <div class="section-badge"><i class="fas fa-box"></i> Order History</div>
        <h1 class="page-hero-title">My Orders</h1>
        <div class="breadcrumb-custom">
            <a href="index.php">Home</a> <i class="fas fa-chevron-right fa-xs"></i>
            <span>My Orders</span>
        </div>
    </div>
</div>

<div class="container-custom page-pad-bottom-5">
    <?php if (mysqli_num_rows($orders) === 0): ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-box"></i></div>
            <div class="empty-title">No orders yet</div>
            <div class="empty-desc">You haven't placed any orders. Start shopping for authentic Ethiopian products!</div>
            <a href="products.php" class="btn-primary-custom"><span><i class="fas fa-store"></i> Shop Now</span></a>
        </div>
    <?php else: ?>
        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
        <?php
        $oid = (int)$order['id'];
        $order_items = mysqli_query($con, "SELECT * FROM order_items WHERE order_id=$oid");
        $status_class = [
            'pending' => 'status-pending',
            'processing' => 'status-processing',
            'shipped' => 'status-processing',
            'delivered' => 'status-delivered',
            'cancelled' => 'status-cancelled'
        ][$order['status']] ?? 'status-pending';
        ?>
        <div class="order-card animate-in">
            <div class="order-header">
                <div>
                    <div class="order-id">#<?= htmlspecialchars($order['order_number']) ?></div>
                    <div class="order-date">
                        <i class="fas fa-calendar-alt fa-xs"></i>
                        <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                    </div>
                </div>
                <span class="order-status <?= $status_class ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
                <div class="text-82 text-muted">
                    <i class="fas fa-map-marker-alt fa-xs"></i> <?= htmlspecialchars($order['city']) ?>
                </div>
                <div class="text-82 text-muted">
                    <i class="fas fa-credit-card fa-xs"></i> <?= ucwords(str_replace('_', ' ', $order['payment_method'])) ?>
                </div>
            </div>
            <div class="order-body">
                <?php while ($oi = mysqli_fetch_assoc($order_items)): ?>
                <div class="order-item-row">
                    <span><?= htmlspecialchars($oi['product_name']) ?> &times; <?= $oi['quantity'] ?></span>
                    <span>ETB <?= number_format($oi['price'] * $oi['quantity'], 2) ?></span>
                </div>
                <?php endwhile; ?>
                <div class="order-total-row">
                    <span>Order Total</span>
                    <span class="amt">ETB <?= number_format($order['total_amount'], 2) ?></span>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
