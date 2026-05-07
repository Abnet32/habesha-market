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
        <div class="section-badge">🔒 Secure Checkout</div>
        <h1 class="page-hero-title">Checkout</h1>
        <div class="breadcrumb-custom">
            <a href="index.php">Home</a> <i class="fas fa-chevron-right fa-xs"></i>
            <a href="cart.php">Cart</a> <i class="fas fa-chevron-right fa-xs"></i>
            <span>Checkout</span>
        </div>
    </div>
</div>

<div class="container-custom" style="padding-bottom:5rem">
    <?php if ($error): ?>
        <div class="alert-custom alert-error" style="max-width:900px; margin:0 auto 1.5rem;">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="actions/checkout_action.php" id="checkout-form">
        <div class="checkout-grid">
            <!-- LEFT: SHIPPING & PAYMENT -->
            <div>
                <!-- Shipping Info -->
                <div class="form-card" style="max-width:none; margin-bottom:1.5rem;">
                    <div class="checkout-section-title">
                        <i class="fas fa-map-marker-alt"></i> Shipping Information
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group-custom" style="grid-column:1/-1">
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
                        <div class="form-group-custom" style="grid-column:1/-1">
                            <label class="form-label-custom">Delivery Address</label>
                            <textarea name="address" class="form-control-custom" rows="2" required
                                placeholder="Kebele, Sub-city, landmark..."><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group-custom" style="grid-column:1/-1">
                            <label class="form-label-custom">Order Notes (optional)</label>
                            <textarea name="notes" class="form-control-custom" rows="2" placeholder="Special instructions for delivery..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment -->
                <div class="form-card" style="max-width:none;">
                    <div class="checkout-section-title">
                        <i class="fas fa-credit-card"></i> Payment Method
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.8rem;">
                        <?php $methods = [
                            ['cash_on_delivery', '💵', 'Cash on Delivery', 'Pay when your order arrives'],
                            ['bank_transfer', '🏦', 'Bank Transfer (CBE / Awash)', 'Transfer to our account & send receipt'],
                            ['mobile_money', '📱', 'Mobile Money (M-Birr / HelloCash)', 'Transfer via mobile payment app']
                        ]; foreach ($methods as $i => [$val, $icon, $label, $desc]): ?>
                        <label style="display:flex; align-items:center; gap:14px; padding:1rem 1.2rem; border:1.5px solid var(--glass-border); border-radius:var(--radius-sm); cursor:pointer; transition:all 0.3s;"
                               onmouseover="this.style.borderColor='var(--primary)'"
                               onmouseout="this.style.borderColor=(document.getElementById('pm<?= $i ?>').checked?'var(--primary)':'var(--glass-border)')">
                            <input type="radio" id="pm<?= $i ?>" name="payment_method" value="<?= $val ?>" <?= $i === 0 ? 'checked' : '' ?>
                                   onchange="document.querySelectorAll('[name=payment_method]').forEach((r,j)=>{r.closest('label').style.borderColor=j==<?= $i ?>?'var(--primary)':'var(--glass-border)'})">
                            <span style="font-size:1.5rem"><?= $icon ?></span>
                            <div>
                                <div style="font-weight:700; font-size:0.9rem;"><?= $label ?></div>
                                <div style="font-size:0.78rem; color:var(--text-muted);"><?= $desc ?></div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT: ORDER SUMMARY -->
            <div class="cart-summary" style="position:sticky; top:90px;">
                <div class="summary-title"><i class="fas fa-shopping-bag" style="color:var(--primary-light); margin-right:8px;"></i> Order Summary</div>

                <?php foreach ($cart as $item): ?>
                <div style="display:flex; gap:10px; margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid rgba(255,255,255,0.05);">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>"
                         style="width:50px; height:50px; border-radius:8px; object-fit:cover; background:var(--dark2);"
                         onerror="this.src='https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=100&q=60'">
                    <div style="flex:1">
                        <div style="font-size:0.85rem; font-weight:600;"><?= htmlspecialchars($item['name']) ?></div>
                        <div style="font-size:0.78rem; color:var(--text-muted);">Qty: <?= $item['quantity'] ?></div>
                    </div>
                    <div style="font-size:0.88rem; font-weight:700; color:var(--secondary);">
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

                <div style="margin-top:1.5rem;">
                    <button type="submit" class="btn-primary-custom" style="width:100%; justify-content:center; border:none; cursor:pointer; font-size:1rem; padding:1rem 2rem;">
                        <span><i class="fas fa-check-circle"></i> Place Order</span>
                    </button>
                </div>
                <div style="margin-top:0.8rem; text-align:center; font-size:0.78rem; color:var(--text-muted);">
                    <i class="fas fa-lock"></i> Your information is secure and encrypted
                </div>
            </div>
        </div>
    </form>
</div>

<?php require 'includes/footer.php'; ?>
