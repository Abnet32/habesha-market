-- ============================================================
-- Habesha Market Database
-- Ethiopian Online Marketplace
-- ============================================================

CREATE DATABASE IF NOT EXISTS habesha_market CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE habesha_market;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(200) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    city VARCHAR(100) DEFAULT 'Addis Ababa',
    address TEXT,
    profile_pic VARCHAR(255) DEFAULT NULL,
    role ENUM('customer','admin') DEFAULT 'customer',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: categories
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(20) DEFAULT NULL,
    description TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: products
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2) DEFAULT NULL,
    stock INT DEFAULT 0,
    image_url VARCHAR(500) DEFAULT NULL,
    badge ENUM('new','sale','hot','') DEFAULT '',
    rating DECIMAL(3,1) DEFAULT 4.5,
    review_count INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: cart_items
-- ============================================================
CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_cart_item (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: orders
-- ============================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(30) NOT NULL UNIQUE,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address TEXT NOT NULL,
    city VARCHAR(100),
    phone VARCHAR(20),
    payment_method ENUM('cash_on_delivery','bank_transfer','mobile_money') DEFAULT 'cash_on_delivery',
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: order_items
-- ============================================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: reviews
-- ============================================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review (product_id, user_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -- ============================================================
-- -- SAMPLE DATA: categories
-- -- ============================================================
-- -- INSERT INTO categories (name, slug, icon, description, sort_order) VALUES
-- -- ('Ethiopian Coffee', 'ethiopian-coffee', 'coffee', 'World-famous Ethiopian coffee beans, blends and accessories', 1),
-- -- ('Traditional Fashion', 'traditional-fashion', 'fashion', 'Habesha Kemis, Netela, Gabi and traditional Ethiopian garments', 2),
-- -- ('Spices & Food', 'spices-and-food', 'spices', 'Authentic Ethiopian spices, injera flour, berbere and more', 3),
-- -- ('Handcrafts', 'handcrafts', 'crafts', 'Handmade Ethiopian baskets, pottery, woodwork and decor', 4),
-- -- ('Jewelry', 'beauty-jewelry', 'jewelry', 'Traditional Ethiopian silver and gold jewelry, crosses and ornaments', 5),
-- -- ('Books & Education', 'books-and-education', 'books', 'Ethiopian literature, Amharic books, history and educational materials', 6);

-- -- -- ============================================================
-- -- -- SAMPLE DATA: products
-- -- -- ============================================================
-- -- INSERT INTO products (category_id, name,slug, description, price, original_price, stock, image_url, badge, rating, review_count) VALUES
-- -- -- Coffee
-- -- (1, 'Yirgacheffe Single Origin Coffee','yirgacheffe-single-origin-coffee', 'Premium single-origin coffee from Yirgacheffe, Sidama region. Known for its bright floral aroma and wine-like acidity. 500g bag.', 450.00, 600.00, 85, 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=600&q=80', 'hot', 4.9, 142),
-- -- (1, 'Harrar Wild Coffee Beans','harrar-wild-coffee-beans', 'Wild-harvested coffee from the highlands of Harrar. Earthy, fruity flavor with a distinctive blueberry note. 250g.', 280.00, NULL, 50, 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=600&q=80', 'new', 4.7, 89),
-- -- (1, 'Limu Organic Coffee Blend', 'limu-organic-coffee-blend', 'Certified organic coffee from Limu forest. Smooth, balanced with mild spice notes. 500g.', 380.00, 480.00, 60, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=600&q=80', 'sale', 4.6, 67),
-- -- (1, 'Ethiopian Coffee Ceremony Set', 'ethiopian-coffee-celebration-set', 'Traditional Ethiopian coffee ceremony set including jebena (clay pot), 6 cini cups and incense tray.', 1200.00, 1500.00, 25, 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600&q=80', 'hot', 4.8, 53),

-- -- -- Fashion
-- -- (2, 'Habesha Kemis (White Classic)','habesha-kemis-white-classic', 'Beautiful hand-woven white Habesha dress with traditional Tibeb border. Available in S, M, L, XL. Ideal for ceremonies.', 2500.00, 3200.00, 40, 'https://images.unsplash.com/photo-1604328698692-f76ea9498e76?w=600&q=80', 'hot', 4.9, 78),
-- -- (2, 'Netela Ethiopian Shawl', 'netela-ethiopian-shawl', 'Authentic hand-loomed Ethiopian cotton shawl with colorful border patterns. One size fits all.', 850.00, NULL, 120, 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&q=80', 'new', 4.5, 34),
-- -- (2, 'Men\'s Gabi Wrap', 'men-s-gabi-wrap', 'Traditional thick cotton Gabi — perfect for cool Ethiopian evenings. Woven by artisans in Dembecha.', 1100.00, 1400.00, 65, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&q=80', 'sale', 4.6, 45),

-- -- -- Spices
-- -- (3, 'Berbere Spice Blend (Premium)','berbere-spice-blend-premium', 'The king of Ethiopian spices. Premium berbere blend with over 15 hand-selected spices. 250g bag.', 150.00, NULL, 200, 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=600&q=80', 'hot', 4.8, 203),
-- -- (3, 'Mitmita Hot Pepper Powder','mitmita-hot-pepper-powder', 'Fiery bird\'s eye chili powder blended with cardamom and cloves. 100g.', 90.00, 120.00, 150, 'https://images.unsplash.com/photo-1588514912908-6f99a0f4a9c2?w=600&q=80', 'sale', 4.7, 118),
-- -- (3, 'Teff Flour (Authentic)','teff-flour-authentic', 'Stone-ground injera-grade teff flour from Tigray region. Gluten-free. 2kg bag.', 180.00, NULL, 300, 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600&q=80', 'new', 4.5, 67),
-- -- (3, 'Niter Kibbeh (Spiced Butter)','niter-kibbeh-spiced-butter', 'Authentic Ethiopian spiced clarified butter — the base of all great Ethiopian cooking. 400g jar.', 220.00, 280.00, 80, 'https://images.unsplash.com/photo-1598839177447-da8f3c0e8416?w=600&q=80', '', 4.6, 44),

-- -- -- Crafts
-- -- (4, 'Ethiopian Mesob Basket', 'ethiopian-mesob-basket', 'Hand-woven colorful Ethiopian injera basket (Mesob). Height: 40cm. Made by Dorze artisans.', 680.00, 900.00, 30, 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80', 'hot', 4.9, 56),
-- -- (4, 'Clay Jebena Coffee Pot', 'clay-jebena-coffee-pot', 'Traditional Ethiopian clay jebena for coffee ceremony. Handmade in Addis Ababa.', 350.00, NULL, 45, 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600&q=80', 'new', 4.7, 38),
-- -- (4, 'Ethiopian Wall Art Panel', 'ethiopian-wall-art-panel', 'Hand-painted traditional Ethiopian art panel — Lalibela churches theme. 60x40cm.', 1500.00, 1900.00, 15, 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?w=600&q=80', '', 4.8, 22),

-- -- -- Jewelry
-- -- (5, 'Lalibela Cross Pendant', 'lalibela-cross-pendant', 'Sterling silver replica of the famous Lalibela cross pendant. Handcrafted by Lalibela artisans. With chain.', 1800.00, 2200.00, 20, 'https://images.unsplash.com/photo-1611085583191-a3b181a88401?w=600&q=80', 'hot', 4.9, 41),
-- -- (5, 'Ethiopian Silver Bracelet', 'ethiopian-silver-bracelet', 'Traditional Ethiopian silver-plated bracelet with Coptic cross engravings. Adjustable size.', 950.00, NULL, 35, 'https://images.unsplash.com/photo-1602173574767-37ac01994b2a?w=600&q=80', 'new', 4.6, 29),
-- -- (5, 'Amber & Silver Necklace', 'amber-silver-necklace', 'Traditional amber bead necklace with silver spacers — worn for centuries in Ethiopian culture.', 2200.00, 2800.00, 12, 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=600&q=80', '', 4.8, 18),

-- -- -- Books
-- -- (6, 'Ethiopian Cookbook: Traditional Recipes', 'ethiopian-cookbook-traditional-recipes', 'Authentic Ethiopian recipes with step-by-step instructions and photos. Covers dishes like Doro Wat, Kitfo, and more.', 350.00, 450.00, 90, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&q=80', 'hot', 4.9, 64),
-- -- (6, 'Learn Amharic: Complete Guide', 'learn-amharic-complete-guide', 'Comprehensive Amharic language learning book for beginners to advanced. Includes audio QR codes.', 420.00, 550.00, 200, 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&q=80', 'hot', 4.8, 89),
-- -- (6, 'Ethiopian History & Culture', 'ethiopian-history-culture', 'In-depth exploration of Ethiopian civilization — from Axum to modern times. Illustrated edition.', 650.00, NULL, 75, 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=600&q=80', '', 4.6, 37);

-- -- ============================================================
-- -- SAMPLE DATA: users (password = 'password123' hashed)
-- -- ============================================================
-- INSERT INTO users (full_name, email, phone, password, city, role) VALUES
-- ('Admin User', 'admin@habeshamarket.et', '+251911000001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Addis Ababa', 'admin'),
-- ('Abebe Girma', 'abebe@example.com', '+251912345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Addis Ababa', 'customer'),
-- ('Tigist Bekele', 'tigist@example.com', '+251923456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hawassa', 'customer');
