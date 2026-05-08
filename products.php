<?php
$page_title = 'Shop';
require 'includes/header.php';
require_once 'connection.php';
global $con;

// Filters
$category = isset($_GET['category']) ? mysqli_real_escape_string($con, $_GET['category']) : '';
$search = isset($_GET['q']) ? mysqli_real_escape_string($con, trim($_GET['q'])) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'featured';

$where = ['p.is_active=1'];
if ($category) $where[] = "c.slug='$category'";
if ($search) $where[] = "(p.name LIKE '%$search%' OR p.description LIKE '%$search%' OR c.name LIKE '%$search%')";

$order_map = [
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'rating' => 'p.rating DESC',
    'newest' => 'p.id DESC',
    'featured' => 'p.rating DESC, p.review_count DESC'
];
$order = $order_map[$sort] ?? 'p.rating DESC, p.review_count DESC';

$where_sql = implode(' AND ', $where);
$sql = "SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE $where_sql ORDER BY $order";
$result = mysqli_query($con, $sql);
$total = mysqli_num_rows($result);

// Categories for filter
$cats = mysqli_query($con, "SELECT * FROM categories ORDER BY sort_order");

// Check items already in cart
$in_cart = [];
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $cr = mysqli_query($con, "SELECT product_id FROM cart_items WHERE user_id=$uid");
    while ($crow = mysqli_fetch_assoc($cr)) $in_cart[] = (int)$crow['product_id'];
}
?>

<div class="page-hero">
    <div class="container-custom">
        <div class="section-badge"><i class="fas fa-shopping-bag"></i> Ethiopian Products</div>
        <h1 class="page-hero-title">
            <?php if ($search): ?>
                Results for "<span class="search-highlight"><?= htmlspecialchars($search) ?></span>"
            <?php elseif ($category): ?>
                <?php
                $cname = mysqli_query($con, "SELECT name FROM categories WHERE slug='$category'");
                $crow = mysqli_fetch_assoc($cname);
                echo htmlspecialchars($crow['name'] ?? 'Category');
                ?>
            <?php else: ?>
                Our Shop
            <?php endif; ?>
        </h1>
        <p class="page-hero-subtitle"><?= $total ?> product<?= $total !== 1 ? 's' : '' ?> found</p>
        <div class="breadcrumb-custom">
            <a href="index.php">Home</a> <i class="fas fa-chevron-right fa-xs"></i>
            <span>Shop</span>
            <?php if ($category): ?>
                <i class="fas fa-chevron-right fa-xs"></i>
                <span><?= htmlspecialchars($crow['name'] ?? $category) ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container-custom page-pad-bottom-4">

    <!-- SEARCH & FILTER BAR -->
    <div class="search-section">
        <div class="search-bar-wrapper">
            <div class="search-input-group">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="live-search" class="search-input"
                    placeholder="Search coffee, habesha kemis, berbere..."
                    value="<?= htmlspecialchars($search) ?>"
                    autocomplete="off">
                <div id="search-results-live"></div>
            </div>

            <select class="filter-select" id="category-filter">
                <option value="">All Categories</option>
                <?php
                mysqli_data_seek($cats, 0);
                while ($cat = mysqli_fetch_assoc($cats)):
                ?>
                <option value="<?= $cat['slug'] ?>" <?= $category === $cat['slug'] ? 'selected' : '' ?>>
                    <?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endwhile; ?>
            </select>

            <select class="filter-select" id="sort-filter">
                <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>><i class="fas fa-star"></i> Featured</option>
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>><i class="fas fa-sparkles"></i> Newest</option>
                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>><i class="fas fa-arrow-up"></i> Price: Low to High</option>
                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>><i class="fas fa-arrow-down"></i> Price: High to Low</option>
                <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>><i class="fas fa-crown"></i> Top Rated</option>
            </select>

            <?php if ($search || $category): ?>
            <a href="products.php" class="btn-outline-custom btn-nowrap admin-filter-compact">
                <i class="fas fa-times"></i> Clear
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- PRODUCTS GRID -->
    <?php if ($total === 0): ?>
        <div class="empty-state">
            <div class="empty-icon">🔍</div>
            <div class="empty-title">No products found</div>
            <div class="empty-desc">Try a different search or browse all categories.</div>
            <a href="products.php" class="btn-primary-custom"><span>Browse All</span></a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php while ($p = mysqli_fetch_assoc($result)): ?>
            <div class="product-card animate-in">
                <div class="product-img-wrapper">
                    <img src="<?= htmlspecialchars($p['image_url']) ?>"
                         alt="<?= htmlspecialchars($p['name']) ?>"
                         loading="lazy"
                         onerror="this.src='https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&q=80'">
                    <?php if ($p['badge']): ?>
                        <span class="product-badge badge-<?= $p['badge'] ?>"><?= strtoupper($p['badge']) ?></span>
                    <?php endif; ?>
                    <a href="product_detail.php?id=<?= $p['id'] ?>" class="product-wishlist" title="View Details">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
                <div class="product-info">
                    <div class="product-category-tag"><?= htmlspecialchars($p['cat_name']) ?></div>
                    <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
                    <div class="product-rating">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?= $i <= round($p['rating']) ? 'rating-star-filled' : 'rating-star-empty' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-count">(<?= $p['review_count'] ?>)</span>
                    </div>
                    <div class="product-footer">
                        <div class="product-price">
                            <span class="price-current">ETB <?= number_format($p['price'], 2) ?></span>
                            <?php if ($p['original_price']): ?>
                                <span class="price-old">ETB <?= number_format($p['original_price'], 2) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (in_array($p['id'], $in_cart)): ?>
                            <a href="cart.php" class="btn-add-cart in-cart" title="Already in cart">
                                <i class="fas fa-check"></i>
                            </a>
                        <?php else: ?>
                            <a href="#" class="btn-add-cart add-to-cart-ajax" data-id="<?= $p['id'] ?>" title="Add to Cart">
                                <i class="fas fa-cart-plus"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
