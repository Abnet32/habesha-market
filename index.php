<?php
$page_title = 'Home';
require 'includes/header.php';
require_once 'connection.php';

global $con;

// Fetch featured products (top rated)
$featured = mysqli_query($con, "SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE p.is_active=1 ORDER BY p.rating DESC, p.review_count DESC LIMIT 8");

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
                From the highlands of Yirgacheffe to the markets of Addis Ababa — shop handcrafted goods, organic coffee, traditional fashion and more, delivered across Ethiopia.
            </p>
            <div class="hero-actions">
                <a href="products.php" class="btn-primary-custom">
                    <span><i class="fas fa-store"></i> Shop Now</span>
                </a>
                <a href="products.php?category=coffee" class="btn-outline-custom">
                    <i class="fas fa-coffee"></i> Explore Coffee
                </a>
            </div>
            <!-- <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Products</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Artisans</div>
                </div>
            </div> -->
        </div>

        <div class="hero-visual">
            <div class="hero-image-wrapper">
                <div class="hero-orbit">
                    <div class="hero-orbit-dot"></div>
                    <div class="hero-orbit-dot hero-orbit-dot-2"></div>
                </div>
                <div class="hero-orbit hero-orbit-2"></div>
                <div class="hero-center-image">
                    <img src="https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=600&q=80" alt="Ethiopian Coffee">
                </div>
                <div class="floating-card floating-card-1">
                    <div class="fc-icon green">☕</div>
                    <div class="fc-text">
                        <strong>Yirgacheffe Coffee</strong>
                        <span>Just ordered!</span>
                    </div>
                </div>
                <div class="floating-card floating-card-2">
                    <div class="fc-icon yellow">⭐</div>
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
<section class="section-padding" style="padding: 3rem 0;">
    <div class="container-custom">
        <div class="features-grid animate-in">
            <div class="feature-card delay-1">
                <div class="feature-icon">🚚</div>
                <div class="feature-title">Fast Delivery</div>
                <div class="feature-desc">Delivering across all Ethiopian regions within 2–5 business days.</div>
            </div>
            <div class="feature-card delay-2">
                <div class="feature-icon">🔒</div>
                <div class="feature-title">Secure Payments</div>
                <div class="feature-desc">Cash on delivery, mobile money & bank transfer — all secure.</div>
            </div>
            <div class="feature-card delay-3">
                <div class="feature-icon">✅</div>
                <div class="feature-title">Quality Assured</div>
                <div class="feature-desc">Every product is verified for authenticity and quality.</div>
            </div>
            <div class="feature-card delay-4">
                <div class="feature-icon">🤝</div>
                <div class="feature-title">Local Artisans</div>
                <div class="feature-desc">We partner with 50+ local artisans and small businesses.</div>
            </div>
            <div class="feature-card delay-5">
                <div class="feature-icon">↩️</div>
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
            <div class="section-badge">🏷️ Browse by Category</div>
            <h2 class="section-title">Shop by <span class="accent">Category</span></h2>
            <p class="section-desc">From aromatic coffee to traditional handcrafts — find what speaks to your heart.</p>
        </div>
        <div class="categories-grid animate-in">
            <?php
            $cat_emojis = ['coffee' => '☕', 'fashion' => '👗', 'spices' => '🌶️', 'crafts' => '🪴', 'jewelry' => '💍', 'books' => '📚'];
            while ($cat = mysqli_fetch_assoc($cats)):
                $icon_class = $cat['slug'] ?? 'coffee';
                $emoji = $cat_emojis[$cat['slug']] ?? '📦';
            ?>
            <a href="products.php?category=<?= urlencode($cat['slug']) ?>" class="category-card">
                <div class="cat-icon <?= $icon_class ?>"><?= $emoji ?></div>
                <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
                <div class="cat-count"><?= $cat['product_count'] ?> items</div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="section-padding" style="padding-top:0">
    <div class="container-custom">
        <div class="section-header animate-in">
            <div class="section-badge">⭐ Top Picks</div>
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
        <div style="text-align:center; margin-top:2.5rem;" class="animate-in">
            <a href="products.php" class="btn-primary-custom">
                <span><i class="fas fa-store"></i> View All Products</span>
            </a>
        </div>
    </div>
</section>

<!-- COUNTER SECTION -->
<section class="section-padding" style="padding-top:0">
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
<section class="section-padding" style="padding-top:0">
    <div class="container-custom">
        <div class="section-header animate-in">
            <div class="section-badge">💬 Customer Stories</div>
            <h2 class="section-title">What Our <span class="accent">Customers</span> Say</h2>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card animate-in delay-1">
                <div class="stars" style="margin-bottom:1rem;">★★★★★</div>
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
                <div class="stars" style="margin-bottom:1rem;">★★★★★</div>
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
                <div class="stars" style="margin-bottom:1rem;">★★★★☆</div>
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
<section class="section-padding" style="padding-top:0; padding-bottom:4rem">
    <div class="container-custom">
        <div class="animate-in" style="background: linear-gradient(135deg, rgba(7,137,48,0.15), rgba(252,221,9,0.08)); border: 1px solid rgba(7,137,48,0.25); border-radius: var(--radius); padding: 3rem 2rem; text-align:center;">
            <div class="section-badge" style="margin-bottom:1rem;">📧 Newsletter</div>
            <h2 style="font-family:'Playfair Display',serif; font-size:1.8rem; font-weight:800; margin-bottom:0.5rem;">Stay in the Loop</h2>
            <p style="color:var(--text-muted); margin-bottom:1.5rem;">Get exclusive deals, new arrivals and Ethiopian cultural stories delivered to your inbox.</p>
            <form style="display:flex; gap:1rem; max-width:480px; margin:0 auto; flex-wrap:wrap;" onsubmit="event.preventDefault(); showToast('Thank you for subscribing!','success')">
                <input type="email" placeholder="Enter your email" required style="flex:1;min-width:220px;background:rgba(255,255,255,0.08);border:1.5px solid rgba(255,255,255,0.15);border-radius:50px;padding:0.8rem 1.2rem;color:var(--text-light);font-family:'Poppins',sans-serif;font-size:0.9rem;outline:none;">
                <button type="submit" class="btn-primary-custom" style="border:none;cursor:pointer;white-space:nowrap;">
                    <span><i class="fas fa-paper-plane"></i> Subscribe</span>
                </button>
            </form>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
