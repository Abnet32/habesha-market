<?php
$page_title = 'Checkout';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once 'connection.php';
global $con;


$uid = (int)$_SESSION['user_id'];
$items = mysqli_query($con, "
    SELECT ci.quantity, p.id as product_id, p.name, p.price, p.image_url
    FROM cart_items ci JOIN products p ON p.id=ci.product_id
    WHERE ci.user_id=$uid
");
$cart = [];
$subtotal = 0;
while ($r = mysqli_fetch_assoc($items)) {
    $r['line_total'] = $r['price'] * $r['quantity'];
    $subtotal += $r['line_total'];
    $cart[] = $r;
}
if (empty($cart)) { header('Location: cart.php'); exit; }

$shipping = 50;
$total = $subtotal + $shipping;

$user = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE id=$uid"));
$error = $_SESSION['checkout_error'] ?? '';
unset($_SESSION['checkout_error']);

require 'includes/header.php';
?>

<div class="page-hero">
    <div class="container-custom">
        <div class="section-badge"><i class="fas fa-lock"></i> Secure Checkout</div>
        <h1 class="page-hero-title">Checkout</h1>
        <div class="breadcrumb-custom">
            <a href="index.php">Home</a> <i class="fas fa-chevron-right fa-xs"></i>
            <a href="cart.php">Cart</a> <i class="fas fa-chevron-right fa-xs"></i>
            <span>Checkout</span>
        </div>
    </div>
</div>

<div class="container-custom page-pad-bottom-5">
    <?php if ($error): ?>
        <div class="alert-custom alert-error max-w-900 mb-1-5">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="actions/checkout_action.php" id="checkout-form">
        <div class="checkout-grid">
            <!-- LEFT: SHIPPING & PAYMENT -->
            <div>
                <!-- Shipping Info -->
                <div class="form-card max-w-none mb-1-5">
                    <div class="checkout-section-title">
                        <i class="fas fa-map-marker-alt"></i> Shipping Information
                    </div>
                    <div class="grid-2">
                        <div class="form-group-custom grid-full">
                            <label class="form-label-custom">Full Name</label>
                            <input type="text" name="full_name" class="form-control-custom"
                                value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">Phone Number</label>
                            <input type="tel" name="phone" class="form-control-custom"
                                value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required placeholder="+251 911 ...">
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">City</label>
                            <select name="city" class="form-control-custom">
                                <?php foreach (['Addis Ababa','Hawassa','Bahir Dar','Mekelle','Dire Dawa','Gondar','Jimma','Adama','Dessie','Shashamane','Other'] as $c): ?>
                                    <option value="<?= $c ?>" <?= ($user['city'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group-custom grid-full">
                            <label class="form-label-custom">Delivery Address</label>
                            <textarea name="address" class="form-control-custom" rows="2" required
                                placeholder="Kebele, Sub-city, landmark..."><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group-custom grid-full">
                            <label class="form-label-custom">Order Notes (optional)</label>
                            <textarea name="notes" class="form-control-custom" rows="2" placeholder="Special instructions for delivery..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment -->
                <div class="form-card max-w-none">
                    <div class="checkout-section-title">
                        <i class="fas fa-credit-card"></i> Payment Method
                    </div>
                    <div class="payment-options">
                        <?php $methods = [
                            ['cash_on_delivery', '💵', 'Cash on Delivery', 'Pay when your order arrives'],
                            ['bank_transfer', '🏦', 'Bank Transfer (CBE / Awash)', 'Transfer to our account & send receipt'],
                            ['mobile_money', '📱', 'Mobile Money (M-Birr / HelloCash)', 'Transfer via mobile payment app']
                        ]; foreach ($methods as $i => [$val, $icon, $label, $desc]): ?>
                        <label class="payment-option">
                            <input type="radio" id="pm<?= $i ?>" name="payment_method" value="<?= $val ?>" <?= $i === 0 ? 'checked' : '' ?>
                                   onchange="document.querySelectorAll('.payment-option').forEach((label)=>label.classList.remove('selected')); this.closest('label').classList.add('selected');">
                            <span class="payment-option-icon"><?= $icon ?></span>
                            <div>
                                <div class="payment-option-title"><?= $label ?></div>
                                <div class="payment-option-desc"><?= $desc ?></div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT: ORDER SUMMARY -->
            <div class="cart-summary sticky-top-90">
                <div class="summary-title"><i class="fas fa-shopping-bag summary-title-icon"></i> Order Summary</div>

                <?php foreach ($cart as $item): ?>
                <div class="summary-item">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>"
                         class="summary-image"
                         onerror="this.src='https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=100&q=60'">
                    <div class="summary-item-main">
                        <div class="summary-item-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="summary-item-meta">Qty: <?= $item['quantity'] ?></div>
                    </div>
                    <div class="summary-item-total">
                        ETB <?= number_format($item['line_total'], 2) ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>ETB <?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>ETB <?= number_format($shipping, 2) ?></span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span class="amount">ETB <?= number_format($total, 2) ?></span>
                </div>

                <div class="mt-1-5">
                    <button type="submit" class="btn-primary-custom btn-full checkout-submit">
                        <span><i class="fas fa-check-circle"></i> Place Order</span>
                    </button>
                </div>
                <div class="text-center text-78 text-muted mt-0-5">
                    <i class="fas fa-lock"></i> Your information is secure and encrypted
                </div>
            </div>
        </div>
    </form>
</div>

<?php require 'includes/footer.php'; ?>
