<?php
$page_title = 'My Cart';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once 'connection.php';
global $con;


$uid = (int)$_SESSION['user_id'];
$items = mysqli_query($con, "
    SELECT ci.id as cart_id, ci.quantity, p.id as product_id, p.name, p.price, p.image_url, p.stock, c.name as cat_name
    FROM cart_items ci
    JOIN products p ON p.id = ci.product_id
    JOIN categories c ON c.id = p.category_id
    WHERE ci.user_id = $uid
    ORDER BY ci.added_at DESC
");

$subtotal = 0;
$cart_data = [];
while ($row = mysqli_fetch_assoc($items)) {
    $row['line_total'] = $row['price'] * $row['quantity'];
    $subtotal += $row['line_total'];
    $cart_data[] = $row;
}
$shipping = $subtotal > 0 ? 50 : 0;
$total = $subtotal + $shipping;

require 'includes/header.php';
?>

<div class="page-hero">
    <div class="container-custom">
        <div class="section-badge"><i class="fas fa-shopping-cart"></i> Shopping Cart</div>
        <h1 class="page-hero-title">My Cart</h1>
        <div class="breadcrumb-custom">
            <a href="index.php">Home</a> <i class="fas fa-chevron-right fa-xs"></i>
            <a href="products.php">Shop</a> <i class="fas fa-chevron-right fa-xs"></i>
            <span>Cart</span>
        </div>
    </div>
</div>

<div class="container-custom page-pad-bottom-5">
    <?php if (empty($cart_data)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="empty-title">Your cart is empty</div>
            <div class="empty-desc">Looks like you haven't added anything yet. Browse our Ethiopian products!</div>
            <a href="products.php" class="btn-primary-custom"><span><i class="fas fa-store"></i> Start Shopping</span></a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <!-- CART ITEMS -->
            <div>
                <div class="cart-table">
                    <div class="cart-table-header">
                        <span>Product</span>
                        <span>Price</span>
                        <span>Quantity</span>
                        <span>Subtotal</span>
                        <span></span>
                    </div>

                    <?php foreach ($cart_data as $item): ?>
                    <div class="cart-item" id="cart-row-<?= $item['cart_id'] ?>">
                        <div class="cart-item-product">
                            <img class="cart-item-img"
                                src="<?= htmlspecialchars($item['image_url']) ?>"
                                alt="<?= htmlspecialchars($item['name']) ?>"
                                onerror="this.src='https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=200&q=60'">
                            <div>
                                <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="cart-item-cat"><?= htmlspecialchars($item['cat_name']) ?></div>
                            </div>
                        </div>
                        <div class="text-85 font-medium">ETB <?= number_format($item['price'], 2) ?></div>
                        <div class="qty-control">
                            <button class="qty-btn" data-action="minus" data-id="<?= $item['cart_id'] ?>">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="qty-value"><?= $item['quantity'] ?></span>
                            <button class="qty-btn" data-action="plus" data-id="<?= $item['cart_id'] ?>">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div id="subtotal-<?= $item['cart_id'] ?>" class="summary-item-total">
                            ETB <?= number_format($item['line_total'], 2) ?>
                        </div>
                        <a href="actions/cart_remove.php?id=<?= $item['cart_id'] ?>" class="remove-btn"
                           onclick="return confirm('Remove this item from cart?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex-between-wrap mt-1">
                    <a href="products.php" class="btn-outline-custom">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <!-- CART SUMMARY -->
            <div class="cart-summary">
                <div class="summary-title"><i class="fas fa-receipt summary-title-icon"></i> Order Summary</div>

                <div class="summary-row">
                    <span>Subtotal (<?= count($cart_data) ?> items)</span>
                    <span>ETB <?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>ETB <?= number_format($shipping, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span>Tax</span>
                    <span>Included</span>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span class="amount" id="cart-total">ETB <?= number_format($total, 2) ?></span>
                </div>

                <div class="flex-column-gap-08 mt-1-5">
                    <a href="checkout.php" class="btn-primary-custom btn-full">
                        <span><i class="fas fa-lock"></i> Proceed to Checkout</span>
                    </a>
                </div>

                <div class="summary-security-box">
                    <i class="fas fa-shield-alt text-primary-light"></i>
                    Secure checkout &mdash; Cash on Delivery available
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
