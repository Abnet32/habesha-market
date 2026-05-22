<?php
$page_title = 'Home';
require 'includes/header.php';
require_once 'connection.php';

global $con;

// Fetch featured products (top rated)
$featured = mysqli_query($con, "SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE p.is_active=1 ORDER BY p.rating DESC, p.review_count DESC LIMIT 9");

// Fetch categories with product count
$cats = mysqli_query($con, "SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON p.category_id=c.id AND p.is_active=1 GROUP BY c.id ORDER BY c.sort_order");
?>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <div class="hero-text">
            <div class="hero-badge">
                <span class="dot"></span>
                #1 Ethiopian Online Marketplace 
            </div>
            <h1 class="hero-title">
                Discover <span class="highlight">Authentic</span><br>
                <span class="highlight-green">Ethiopian</span> Products
            </h1>
            <p class="hero-subtitle">
                From the highlands of Yirgacheffe to the markets of Addis Ababa — shop handcrafted goods, organic coffee, traditional fashion and more, delivered acros
            </p>
            <div class="hero-actions">
                <a href="produfcts.php" class="btn-primary-custom">
                    <span><i class="fas fa-store"></i> Shop Now</span>
                </a>
                <a href="products.php?category=ethiopian-coffee" class="btn-outline-custom">
                    <i class="fas fa-coffee"></i> Explore Coffee
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-image-wrapper">
                <div class="hero-orbit">
                    <div class="hero-orbit-dot"></div>
                    <div class="hero-orbit-dot hero-orbit-dot-2"></div>
                </div>
                <div class="hero-orbit hero-orbit-2"></div>
                <div class="hero-center-image">
                    <img src="assets/hero.png" alt="Ethiopian Coffee">
                </div>
                <div class="floating-card floating-card-1">
                    <div class="fc-icon green"><i class="fas fa-mug-hot"></i></div>
                    <div class="fc-text">
                        <strong>Yirgacheffe Coffee</strong>
                        <span>Just ordered!</span>
                    </div>
                </div>
                <div class="floating-card floating-card-2">
                    <div class="fc-icon yellow"><i class="fas fa-star"></i></div>
                    <div class="fc-text">
                        <strong>4.9 / 5 Rating</strong>
                        <span>142 reviews</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES STRIP -->
<section class="section-padding page-pad-3">
    <div class="container-custom">
        <div class="features-grid animate-in">
            <div class="feature-card delay-1">
                <div class="feature-icon"><i class="fas fa-truck"></i></div>
                <div class="feature-title">Fast Delivery</div>
                <div class="feature-desc">Delivering across all Ethiopian regions within 2–5 business days.</div>
            </div>
          
            <div class="feature-card delay-3">
                <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                <div class="feature-title">Quality Assured</div>
                <div class="feature-desc">Every product is verified for authenticity and quality.</div>
            </div>
            <div class="feature-card delay-4">
                <div class="feature-icon"><i class="fas fa-handshake"></i></div>
                <div class="feature-title">Local Artisans</div>
                <div class="feature-desc">We partner with 50+ local artisans and small businesses.</div>
            </div>
            <div class="feature-card delay-5">
                <div class="feature-icon"><i class="fas fa-undo"></i></div>
                <div class="feature-title">Easy Returns</div>
                <div class="feature-desc">Not happy? Return within 7 days, no questions asked.</div>
            </div>
        </div>
    </div>
</section>

<!-- CATEGORIES -->
<section class="section-padding">
    <div class="container-custom">
        <div class="section-header animate-in">
            <div class="section-badge"><i class="fas fa-tag"></i> Browse by Category</div>
            <h2 class="section-title">Shop by <span class="accent">Category</span></h2>
            <p class="section-desc">From aromatic coffee to traditional handcrafts — find what speaks to your heart.</p>
        </div>
        <div class="categories-grid animate-in">
            <?php
            $cat_visuals = [
                'coffee' => ['icon' => 'fas fa-mug-hot', 'tone' => 'coffee'],
                'fashion' => ['icon' => 'fas fa-shirt', 'tone' => 'fashion'],
                'spices' => ['icon' => 'fas fa-pepper-hot', 'tone' => 'spices'],
                'crafts' => ['icon' => 'fas fa-basket-shopping', 'tone' => 'crafts'],
                'jewelry' => ['icon' => 'fas fa-gem', 'tone' => 'jewelry'],
                'books' => ['icon' => 'fas fa-book-open', 'tone' => 'books']
            ];
            while ($cat = mysqli_fetch_assoc($cats)):
                $cat_key = $cat['icon'] ?: ($cat['slug'] ?? 'coffee');
                $cat_visual = $cat_visuals[$cat_key] ?? $cat_visuals['coffee'];
            ?>
            <a href="products.php?category=<?= urlencode($cat['slug']) ?>" class="category-card">
                <div class="cat-icon <?= $cat_visual['tone'] ?>"><i class="<?= $cat_visual['icon'] ?>"></i></div>
                <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
                <div class="cat-count"><?= $cat['product_count'] ?> items</div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="section-padding page-pad-top-0">
    <div class="container-custom">
        <div class="section-header animate-in">
            <div class="section-badge"><i class="fas fa-star"></i> Top Picks</div>
            <h2 class="section-title">Featured <span class="accent-green">Products</span></h2>
            <p class="section-desc">Handpicked bestsellers loved by our community of Ethiopian shoppers.</p>
        </div>
        <div class="products-grid">
            <?php while ($p = mysqli_fetch_assoc($featured)): ?>
            <div class="product-card animate-in">
                <div class="product-img-wrapper">
                    <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&q=80'">
                    <?php if ($p['badge']): ?>
                        <span class="product-badge badge-<?= $p['badge'] ?>"><?= strtoupper($p['badge']) ?></span>
                    <?php endif; ?>
                    <a href="product_detail.php?id=<?= $p['id'] ?>" class="product-wishlist"><i class="fas fa-eye"></i></a>
                </div>
                <div class="product-info">
                    <div class="product-category-tag"><?= htmlspecialchars($p['cat_name']) ?></div>
                    <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
                    <div class="product-rating">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?= $i <= round($p['rating']) ? '' : '-o' ?>"></i>
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
                        <a href="#" class="btn-add-cart add-to-cart-ajax <?= isset($_SESSION['user_id']) ? '' : '' ?>" data-id="<?= $p['id'] ?>" title="Add to Cart">
                            <i class="fas fa-cart-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="hero-cta-center animate-in">
            <a href="products.php" class="btn-primary-custom">
                <span><i class="fas fa-store"></i> View All Products</span>
            </a>
        </div>
    </div>
</section>

<!-- COUNTER SECTION -->
<section class="section-padding page-pad-top-0">
    <div class="container-custom">
        <div class="counter-section animate-in">
            <div class="counter-grid">
                <div class="counter-item">
                    <div class="counter-number" data-target="500" data-suffix="+">0</div>
                    <div class="counter-label">Products Available</div>
                </div>
                <div class="counter-item">
                    <div class="counter-number" data-target="10000" data-suffix="+">0</div>
                    <div class="counter-label">Happy Customers</div>
                </div>
                <div class="counter-item">
                    <div class="counter-number" data-target="50" data-suffix="+">0</div>
                    <div class="counter-label">Local Artisan Partners</div>
                </div>
                <div class="counter-item">
                    <div class="counter-number" data-target="11" data-suffix="">0</div>
                    <div class="counter-label">Regions Covered</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="section-padding page-pad-top-0">
    <div class="container-custom">
        <div class="section-header animate-in">
            <div class="section-badge"><i class="fas fa-comments"></i> Customer Stories</div>
            <h2 class="section-title">What Our <span class="accent">Customers</span> Say</h2>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card animate-in delay-1">
                <div class="stars hero-star-row">★★★★★</div>
                <p class="testimonial-text">I ordered Yirgacheffe coffee and it arrived perfectly packaged. The aroma is incredible — it tastes exactly like the coffee ceremony at my grandmother's house. Truly authentic!</p>
                <div class="testimonial-author">
                    <div class="author-avatar">A</div>
                    <div>
                        <div class="author-name">Abebe Girma</div>
                        <div class="author-location">📍 Addis Ababa</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card animate-in delay-2">
                <div class="stars hero-star-row">★★★★★</div>
                <p class="testimonial-text">The Habesha Kemis I bought for my sister's wedding was absolutely stunning. The quality of the weaving and the Tibeb patterns were exceptional. Will definitely order again!</p>
                <div class="testimonial-author">
                    <div class="author-avatar">T</div>
                    <div>
                        <div class="author-name">Tigist Bekele</div>
                        <div class="author-location">📍 Hawassa</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card animate-in delay-3">
                <div class="stars hero-star-row">★★★★☆</div>
                <p class="testimonial-text">The berbere spice blend is exactly what I've been looking for! My family always complained that the spices we found in diaspora stores were not authentic. This is the real deal.</p>
                <div class="testimonial-author">
                    <div class="author-avatar">M</div>
                    <div>
                        <div class="author-name">Mekdes Hailu</div>
                        <div class="author-location">📍 Bahir Dar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- NEWSLETTER -->
<section class="section-padding page-pad-top-0-bottom-4">
    <div class="container-custom">
        <div class="animate-in newsletter-panel">
            <div class="section-badge newsletter-title"><i class="fas fa-envelope"></i> Newsletter</div>
            <h2 class="newsletter-loop">Stay in the Loop</h2>
            <p class="newsletter-subtitle">Get exclusive deals, new arrivals and Ethiopian cultural stories delivered to your inbox.</p>
            <form class="newsletter-form" onsubmit="event.preventDefault(); showToast('Thank you for subscribing!','success')">
                <input type="email" placeholder="Enter your email" required class="newsletter-input">
                <button type="submit" class="btn-primary-custom newsletter-subscribe-button">
                    <span><i class="fas fa-paper-plane"></i> Subscribe</span>
                </button>
            </form>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
