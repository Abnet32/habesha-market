<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'connection.php';
global $con;


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT p.*, c.name as cat_name, c.slug as cat_slug
    FROM products p JOIN categories c ON c.id=p.category_id
    WHERE p.id=$id AND p.is_active=1
"));
if (!$product) { header('Location: products.php'); exit; }
$page_title = $product['name'];

// Check if in cart
$in_cart = false;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $cr = mysqli_query($con, "SELECT id FROM cart_items WHERE user_id=$uid AND product_id=$id");
    $in_cart = mysqli_num_rows($cr) > 0;
}

// Handle review submission
$review_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $rating = max(1, min(5, (int)$_POST['rating']));
    $comment = mysqli_real_escape_string($con, trim($_POST['comment']));
    mysqli_query($con, "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES ($id, $uid, $rating, '$comment')
                        ON DUPLICATE KEY UPDATE rating=$rating, comment='$comment'");
    $avg = mysqli_fetch_assoc(mysqli_query($con, "SELECT AVG(rating) as avg, COUNT(*) as cnt FROM reviews WHERE product_id=$id"));
    mysqli_query($con, "UPDATE products SET rating=" . round($avg['avg'], 1) . ", review_count=" . $avg['cnt'] . " WHERE id=$id");
    $review_msg = 'Your review has been submitted!';
}

// Reviews
$reviews = mysqli_query($con, "
    SELECT r.*, u.full_name FROM reviews r
    JOIN users u ON u.id=r.user_id
    WHERE r.product_id=$id ORDER BY r.created_at DESC LIMIT 10
");

// Related products
$related = mysqli_query($con, "
    SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON c.id=p.category_id
    WHERE p.category_id={$product['category_id']} AND p.id!=$id AND p.is_active=1
    ORDER BY p.rating DESC LIMIT 4
");

require 'includes/header.php';
?>

<div class="page-hero" style="padding-bottom:2rem">
    <div class="container-custom">
        <div class="breadcrumb-custom">
            <a href="index.php">Home</a> <i class="fas fa-chevron-right fa-xs"></i>
            <a href="products.php">Shop</a> <i class="fas fa-chevron-right fa-xs"></i>
            <a href="products.php?category=<?= urlencode($product['cat_slug']) ?>"><?= htmlspecialchars($product['cat_name']) ?></a> <i class="fas fa-chevron-right fa-xs"></i>
            <span><?= htmlspecialchars($product['name']) ?></span>
        </div>
    </div>
</div>

<div class="container-custom" style="padding-bottom:5rem">
    <!-- Product Detail -->
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:3rem; align-items:start; margin-bottom:4rem;" class="animate-in">

        <!-- Image -->
        <div style="position:relative;">
            <div style="border-radius:var(--radius); overflow:hidden; background:var(--dark2); aspect-ratio:1; border:1px solid var(--glass-border);">
                <img id="main-product-img"
                    src="<?= htmlspecialchars($product['image_url']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                    style="width:100%; height:100%; object-fit:cover; transition:transform 0.5s ease;"
                    onerror="this.src='https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&q=80'"
                    onmouseover="this.style.transform='scale(1.04)'"
                    onmouseout="this.style.transform='scale(1)'">
            </div>
            <?php if ($product['badge']): ?>
                <div class="product-badge badge-<?= $product['badge'] ?>" style="position:absolute; top:16px; left:16px; font-size:0.85rem; padding:6px 16px;">
                    <?= strtoupper($product['badge']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div>
            <div class="product-category-tag" style="font-size:0.85rem; margin-bottom:0.8rem;">
                <i class="fas fa-tag fa-xs"></i> <?= htmlspecialchars($product['cat_name']) ?>
            </div>
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(1.5rem,3vw,2.2rem); font-weight:800; line-height:1.2; margin-bottom:1rem;">
                <?= htmlspecialchars($product['name']) ?>
            </h1>

            <!-- Rating -->
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:1.2rem;">
                <div class="stars" style="font-size:1rem;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star" style="<?= $i <= round($product['rating']) ? 'color:var(--secondary)' : 'color:rgba(255,255,255,0.15)' ?>"></i>
                    <?php endfor; ?>
                </div>
                <span style="font-weight:700; color:var(--secondary);"><?= $product['rating'] ?></span>
                <span style="color:var(--text-muted); font-size:0.85rem;">(<?= $product['review_count'] ?> reviews)</span>
                <span style="color:var(--text-muted); font-size:0.85rem;">|</span>
                <span style="font-size:0.85rem; color:<?= $product['stock'] > 0 ? 'var(--primary-light)' : 'var(--accent)' ?>;">
                    <?= $product['stock'] > 0 ? "✅ In Stock ({$product['stock']} left)" : '❌ Out of Stock' ?>
                </span>
            </div>

            <!-- Price -->
            <div style="margin-bottom:1.5rem;">
                <div style="font-size:2rem; font-weight:800; color:var(--secondary);">ETB <?= number_format($product['price'], 2) ?></div>
                <?php if ($product['original_price']): ?>
                    <div style="display:flex; align-items:center; gap:12px; margin-top:4px;">
                        <span style="color:var(--text-muted); text-decoration:line-through; font-size:1rem;">ETB <?= number_format($product['original_price'], 2) ?></span>
                        <span style="background:rgba(218,18,26,0.15); color:#ff8a80; border-radius:50px; padding:2px 10px; font-size:0.8rem; font-weight:700;">
                            <?= round((1 - $product['price']/$product['original_price'])*100) ?>% OFF
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Description -->
            <div style="color:var(--text-muted); line-height:1.8; margin-bottom:2rem; padding:1.2rem; background:var(--glass); border-radius:var(--radius-sm); border:1px solid var(--glass-border);">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>

            <!-- Actions -->
            <?php if ($product['stock'] > 0): ?>
                <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                    <?php if ($in_cart): ?>
                        <a href="cart.php" class="btn-primary-custom">
                            <span><i class="fas fa-check"></i> View in Cart</span>
                        </a>
                    <?php else: ?>
                        <a href="#" class="btn-primary-custom add-to-cart-ajax" data-id="<?= $product['id'] ?>" style="text-decoration:none;">
                            <span><i class="fas fa-cart-plus"></i> Add to Cart</span>
                        </a>
                    <?php endif; ?>
                    <a href="checkout.php" class="btn-outline-custom">
                        <i class="fas fa-bolt"></i> Buy Now
                    </a>
                </div>
            <?php else: ?>
                <div class="alert-custom alert-error"><i class="fas fa-times-circle"></i> This product is currently out of stock.</div>
            <?php endif; ?>

            <!-- Delivery info -->
            <div style="margin-top:1.5rem; display:flex; flex-direction:column; gap:8px; font-size:0.85rem; color:var(--text-muted);">
                <div><i class="fas fa-truck" style="color:var(--primary-light); width:18px;"></i> Delivered in 2–5 business days across Ethiopia</div>
                <div><i class="fas fa-undo" style="color:var(--primary-light); width:18px;"></i> 7-day return policy</div>
                <div><i class="fas fa-shield-alt" style="color:var(--primary-light); width:18px;"></i> Authentic product guaranteed</div>
            </div>
        </div>
    </div>

    <!-- REVIEWS SECTION -->
    <div style="margin-bottom:4rem;">
        <h2 class="section-title" style="font-size:1.5rem; margin-bottom:2rem;">
            Customer <span class="accent">Reviews</span>
        </h2>

        <?php if ($review_msg): ?>
            <div class="alert-custom alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($review_msg) ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="form-card" style="max-width:none; margin-bottom:2rem;">
            <h3 style="font-weight:700; font-size:0.95rem; margin-bottom:1rem;">Write a Review</h3>
            <form method="POST">
                <input type="hidden" name="submit_review" value="1">
                <div class="form-group-custom">
                    <label class="form-label-custom">Rating</label>
                    <div style="display:flex; gap:6px; font-size:1.5rem; cursor:pointer;" id="star-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star" data-val="<?= $i ?>"
                               style="color:<?= $i <= 4 ? 'var(--secondary)' : 'rgba(255,255,255,0.2)' ?>; transition:all 0.2s;"
                               onmouseover="highlightStars(<?= $i ?>)"
                               onmouseout="resetStars()"
                               onclick="selectStar(<?= $i ?>)"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="rating-val" value="4">
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Comment</label>
                    <textarea name="comment" class="form-control-custom" rows="3"
                        placeholder="Share your experience with this product..."></textarea>
                </div>
                <button type="submit" class="btn-primary-custom" style="border:none; cursor:pointer;">
                    <span><i class="fas fa-paper-plane"></i> Submit Review</span>
                </button>
            </form>
        </div>
        <?php else: ?>
        <div class="alert-custom alert-info" style="margin-bottom:1.5rem;">
            <i class="fas fa-info-circle"></i> <a href="login.php" style="color:var(--primary-light)">Login</a> to write a review.
        </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($reviews) === 0): ?>
            <div style="text-align:center; color:var(--text-muted); padding:2rem;">No reviews yet. Be the first!</div>
        <?php else: ?>
            <div class="testimonials-grid">
                <?php while ($rev = mysqli_fetch_assoc($reviews)): ?>
                <div class="testimonial-card">
                    <div class="stars" style="margin-bottom:0.8rem; font-size:0.9rem;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star" style="<?= $i <= $rev['rating'] ? 'color:var(--secondary)' : 'color:rgba(255,255,255,0.15)' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="testimonial-text"><?= htmlspecialchars($rev['comment'] ?: 'Great product!') ?></p>
                    <div class="testimonial-author">
                        <div class="author-avatar" style="width:38px;height:38px;font-size:0.9rem;"><?= strtoupper(substr($rev['full_name'],0,1)) ?></div>
                        <div>
                            <div class="author-name"><?= htmlspecialchars($rev['full_name']) ?></div>
                            <div class="author-location"><?= date('d M Y', strtotime($rev['created_at'])) ?></div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- RELATED PRODUCTS -->
    <?php if (mysqli_num_rows($related) > 0): ?>
    <div>
        <h2 class="section-title" style="font-size:1.5rem; margin-bottom:2rem;">
            You May Also <span class="accent-green">Like</span>
        </h2>
        <div class="products-grid" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr))">
            <?php while ($rp = mysqli_fetch_assoc($related)): ?>
            <div class="product-card animate-in">
                <div class="product-img-wrapper">
                    <img src="<?= htmlspecialchars($rp['image_url']) ?>" alt="<?= htmlspecialchars($rp['name']) ?>"
                         loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&q=60'">
                    <?php if ($rp['badge']): ?><span class="product-badge badge-<?= $rp['badge'] ?>"><?= strtoupper($rp['badge']) ?></span><?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-category-tag"><?= htmlspecialchars($rp['cat_name']) ?></div>
                    <div class="product-name"><?= htmlspecialchars($rp['name']) ?></div>
                    <div class="product-footer">
                        <span class="price-current">ETB <?= number_format($rp['price'], 2) ?></span>
                        <a href="product_detail.php?id=<?= $rp['id'] ?>" class="btn-add-cart" title="View"><i class="fas fa-eye"></i></a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
let selectedStar = 4;
function highlightStars(n) {
    document.querySelectorAll('#star-rating i').forEach((s, i) => {
        s.style.color = i < n ? 'var(--secondary)' : 'rgba(255,255,255,0.2)';
        s.style.transform = i < n ? 'scale(1.2)' : 'scale(1)';
    });
}
function resetStars() {
    document.querySelectorAll('#star-rating i').forEach((s, i) => {
        s.style.color = i < selectedStar ? 'var(--secondary)' : 'rgba(255,255,255,0.2)';
        s.style.transform = 'scale(1)';
    });
}
function selectStar(n) {
    selectedStar = n;
    document.getElementById('rating-val').value = n;
    resetStars();
}
</script>

<?php require 'includes/footer.php'; ?>
