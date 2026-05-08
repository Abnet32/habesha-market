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

<div class="page-hero page-pad-bottom-2">
    <div class="container-custom">
        <div class="breadcrumb-custom">
            <a href="index.php">Home</a> <i class="fas fa-chevron-right fa-xs"></i>
            <a href="products.php">Shop</a> <i class="fas fa-chevron-right fa-xs"></i>
            <a href="products.php?category=<?= urlencode($product['cat_slug']) ?>"><?= htmlspecialchars($product['cat_name']) ?></a> <i class="fas fa-chevron-right fa-xs"></i>
            <span><?= htmlspecialchars($product['name']) ?></span>
        </div>
    </div>
</div>

<div class="container-custom page-pad-bottom-5">
    <!-- Product Detail -->
    <div class="product-detail-grid animate-in">

        <!-- Image -->
        <div class="product-detail-media">
            <div class="product-detail-image-wrap">
                <img id="main-product-img"
                    src="<?= htmlspecialchars($product['image_url']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                    class="product-detail-image"
                    onerror="this.src='https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&q=80'">
            </div>
            <?php if ($product['badge']): ?>
                <div class="product-badge badge-<?= $product['badge'] ?> product-detail-badge">
                    <?= strtoupper($product['badge']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div>
            <div class="product-category-tag product-category-compact">
                <i class="fas fa-tag fa-xs"></i> <?= htmlspecialchars($product['cat_name']) ?>
            </div>
            <h1 class="product-title-display">
                <?= htmlspecialchars($product['name']) ?>
            </h1>

            <!-- Rating -->
            <div class="product-rating-row">
                <div class="stars stars-medium">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?= $i <= round($product['rating']) ? 'rating-star-filled' : 'rating-star-empty' ?>"></i>
                    <?php endfor; ?>
                </div>
                <span class="rating-value"><?= $product['rating'] ?></span>
                <span class="product-meta">(<?= $product['review_count'] ?> reviews)</span>
                <span class="product-meta">|</span>
                <span class="stock-text <?= $product['stock'] > 0 ? 'stock-in' : 'stock-out' ?>">
                    <?= $product['stock'] > 0 ? "<i class=\"fas fa-check-circle mr-4\"></i> In Stock ({$product['stock']} left)" : '<i class="fas fa-times-circle mr-4"></i> Out of Stock' ?>
                </span>
            </div>

            <!-- Price -->
            <div class="product-price-hero">
                <div class="product-price-main">ETB <?= number_format($product['price'], 2) ?></div>
                <?php if ($product['original_price']): ?>
                    <div class="product-old-price-row">
                        <span class="product-old-price">ETB <?= number_format($product['original_price'], 2) ?></span>
                        <span class="discount-badge">
                            <?= round((1 - $product['price']/$product['original_price'])*100) ?>% OFF
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Description -->
            <div class="product-description-box">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>

            <!-- Actions -->
            <?php if ($product['stock'] > 0): ?>
                <div class="product-actions-row">
                    <?php if ($in_cart): ?>
                        <a href="cart.php" class="btn-primary-custom">
                            <span><i class="fas fa-check"></i> View in Cart</span>
                        </a>
                    <?php else: ?>
                        <a href="#" class="btn-primary-custom add-to-cart-ajax product-buy-link" data-id="<?= $product['id'] ?>">
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
            <div class="delivery-list">
                <div><i class="fas fa-truck delivery-icon"></i> Delivered in 2–5 business days across Ethiopia</div>
                <div><i class="fas fa-undo delivery-icon"></i> 7-day return policy</div>
                <div><i class="fas fa-shield-alt delivery-icon"></i> Authentic product guaranteed</div>
            </div>
        </div>
    </div>

    <!-- REVIEWS SECTION -->
    <div class="review-container">
        <h2 class="section-title section-title-compact">
            Customer <span class="accent">Reviews</span>
        </h2>

        <?php if ($review_msg): ?>
            <div class="alert-custom alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($review_msg) ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="form-card review-card-wide">
            <h3 class="review-heading">Write a Review</h3>
            <form method="POST">
                <input type="hidden" name="submit_review" value="1">
                <div class="form-group-custom">
                    <label class="form-label-custom">Rating</label>
                    <div class="star-rating-control" id="star-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star star-rating-icon <?= $i <= 4 ? 'active' : '' ?>" data-val="<?= $i ?>"
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
                <button type="submit" class="btn-primary-custom btn-clean review-submit">
                    <span><i class="fas fa-paper-plane"></i> Submit Review</span>
                </button>
            </form>
        </div>
        <?php else: ?>
        <div class="alert-custom alert-info review-alert">
            <i class="fas fa-info-circle"></i> <a href="login.php" class="text-primary-light">Login</a> to write a review.
        </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($reviews) === 0): ?>
            <div class="review-empty">No reviews yet. Be the first!</div>
        <?php else: ?>
            <div class="testimonials-grid">
                <?php while ($rev = mysqli_fetch_assoc($reviews)): ?>
                <div class="testimonial-card">
                    <div class="stars stars-small">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= $rev['rating'] ? 'rating-star-filled' : 'rating-star-empty' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="testimonial-text"><?= htmlspecialchars($rev['comment'] ?: 'Great product!') ?></p>
                    <div class="testimonial-author">
                        <div class="author-avatar author-avatar-small"><?= strtoupper(substr($rev['full_name'],0,1)) ?></div>
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
        <h2 class="section-title section-title-compact">
            You May Also <span class="accent-green">Like</span>
        </h2>
        <div class="products-grid product-grid-narrow">
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
        s.classList.toggle('active', i < n);
        s.classList.toggle('hovered', i < n);
    });
}
function resetStars() {
    document.querySelectorAll('#star-rating i').forEach((s, i) => {
        s.classList.toggle('active', i < selectedStar);
        s.classList.remove('hovered');
    });
}
function selectStar(n) {
    selectedStar = n;
    document.getElementById('rating-val').value = n;
    resetStars();
}
</script>

<?php require 'includes/footer.php'; ?>
