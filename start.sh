#!/bin/bash
set -e

MYSQL_DIR="/home/runner/mysql_data"
MYSQL_SOCKET="/tmp/mysql.sock"
DB_NAME="habesha_market"

# Initialize MariaDB data directory if it doesn't exist
if [ ! -d "$MYSQL_DIR" ]; then
    echo "Initializing MariaDB..."
    mysql_install_db --user=runner --datadir="$MYSQL_DIR" --skip-test-db --auth-root-authentication-method=normal > /dev/null 2>&1
    echo "MariaDB initialized."
fi

# Start MariaDB if not running
if ! mysqladmin --socket="$MYSQL_SOCKET" ping --silent 2>/dev/null; then
    echo "Starting MariaDB..."
    mysqld --user=runner --datadir="$MYSQL_DIR" \
        --socket="$MYSQL_SOCKET" \
        --pid-file="/tmp/mysql.pid" \
        --port=3306 \
        --skip-networking=0 \
        --bind-address=127.0.0.1 \
        --skip-grant-tables \
        --character-set-server=utf8mb4 \
        --collation-server=utf8mb4_unicode_ci \
        --innodb-buffer-pool-size=64M \
        --log-error=/tmp/mysql_error.log \
        &

    for i in {1..30}; do
        if mysqladmin --socket="$MYSQL_SOCKET" ping --silent 2>/dev/null; then
            echo "MariaDB started."
            break
        fi
        sleep 1
    done
fi

# Setup database schema and seed data
DB_EXISTS=$(mysql --socket="$MYSQL_SOCKET" -e "SHOW DATABASES LIKE '$DB_NAME';" 2>/dev/null | grep -c "$DB_NAME" || true)

if [ "$DB_EXISTS" -eq "0" ]; then
    echo "Creating database..."
    mysql --socket="$MYSQL_SOCKET" -e "
    CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    USE $DB_NAME;

    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(150) NOT NULL,
        email VARCHAR(200) NOT NULL UNIQUE,
        phone VARCHAR(20),
        password VARCHAR(255) NOT NULL,
        city VARCHAR(100) DEFAULT 'Addis Ababa',
        address TEXT,
        role ENUM('customer','admin') DEFAULT 'customer',
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        icon VARCHAR(20) DEFAULT NULL,
        description TEXT,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(200) NOT NULL,
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
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS cart_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_cart_item (user_id, product_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

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
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        product_name VARCHAR(200) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        quantity INT NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        user_id INT NOT NULL,
        rating TINYINT NOT NULL,
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_review (product_id, user_id),
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    INSERT IGNORE INTO categories (name, slug, icon, description, sort_order) VALUES
    ('Ethiopian Coffee', 'coffee', 'coffee', 'World-famous Ethiopian coffee beans, blends and accessories', 1),
    ('Traditional Fashion', 'fashion', 'fashion', 'Habesha Kemis, Netela, Gabi and traditional Ethiopian garments', 2),
    ('Spices & Food', 'spices', 'spices', 'Authentic Ethiopian spices, injera flour, berbere and more', 3),
    ('Handcrafts', 'crafts', 'crafts', 'Handmade Ethiopian baskets, pottery, woodwork and decor', 4),
    ('Jewelry', 'jewelry', 'jewelry', 'Traditional Ethiopian silver and gold jewelry, crosses and ornaments', 5),
    ('Books & Education', 'books', 'books', 'Ethiopian literature, Amharic books, history and educational materials', 6);

    INSERT IGNORE INTO users (full_name, email, phone, password, city, role) VALUES
    ('Admin User', 'admin@habeshamarket.et', '+251911000001', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Addis Ababa', 'admin'),
    ('Abebe Girma', 'abebe@example.com', '+251912345678', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Addis Ababa', 'customer'),
    ('Tigist Bekele', 'tigist@example.com', '+251923456789', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hawassa', 'customer');
    " 2>&1

    # Insert products using category slugs
    mysql --socket="$MYSQL_SOCKET" "$DB_NAME" -e "
    INSERT IGNORE INTO products (category_id, name, description, price, original_price, stock, image_url, badge, rating, review_count)
    SELECT c.id, p.name, p.description, p.price, p.original_price, p.stock, p.image_url, p.badge, p.rating, p.review_count
    FROM categories c JOIN (
        SELECT 'coffee' as slug, 'Yirgacheffe Single Origin Coffee' as name, 'Premium single-origin coffee from Yirgacheffe, Sidama region. Known for its bright floral aroma and wine-like acidity. 500g bag.' as description, 450.00 as price, 600.00 as original_price, 85 as stock, 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=600&q=80' as image_url, 'hot' as badge, 4.9 as rating, 142 as review_count UNION ALL
        SELECT 'coffee','Harrar Wild Coffee Beans','Wild-harvested coffee from the highlands of Harrar. Earthy, fruity flavor with a distinctive blueberry note. 250g.',280.00,NULL,50,'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=600&q=80','new',4.7,89 UNION ALL
        SELECT 'coffee','Limu Organic Coffee Blend','Certified organic coffee from Limu forest. Smooth, balanced with mild spice notes. 500g.',380.00,480.00,60,'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=600&q=80','sale',4.6,67 UNION ALL
        SELECT 'coffee','Ethiopian Coffee Ceremony Set','Traditional Ethiopian coffee ceremony set including jebena clay pot, 6 cini cups and incense tray.',1200.00,1500.00,25,'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600&q=80','hot',4.8,53 UNION ALL
        SELECT 'fashion','Habesha Kemis White Classic','Beautiful hand-woven white Habesha dress with traditional Tibeb border. Available in S, M, L, XL. Ideal for ceremonies.',2500.00,3200.00,40,'https://images.unsplash.com/photo-1604328698692-f76ea9498e76?w=600&q=80','hot',4.9,78 UNION ALL
        SELECT 'fashion','Netela Ethiopian Shawl','Authentic hand-loomed Ethiopian cotton shawl with colorful border patterns. One size fits all.',850.00,NULL,120,'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&q=80','new',4.5,34 UNION ALL
        SELECT 'fashion','Mens Gabi Wrap','Traditional thick cotton Gabi perfect for cool Ethiopian evenings. Woven by artisans in Dembecha.',1100.00,1400.00,65,'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&q=80','sale',4.6,45 UNION ALL
        SELECT 'spices','Berbere Spice Blend Premium','The king of Ethiopian spices. Premium berbere blend with over 15 hand-selected spices. 250g bag.',150.00,NULL,200,'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=600&q=80','hot',4.8,203 UNION ALL
        SELECT 'spices','Mitmita Hot Pepper Powder','Fiery birds eye chili powder blended with cardamom and cloves. 100g.',90.00,120.00,150,'https://images.unsplash.com/photo-1588514912908-6f99a0f4a9c2?w=600&q=80','sale',4.7,118 UNION ALL
        SELECT 'spices','Teff Flour Authentic','Stone-ground injera-grade teff flour from Tigray region. Gluten-free. 2kg bag.',180.00,NULL,300,'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600&q=80','new',4.5,67 UNION ALL
        SELECT 'spices','Niter Kibbeh Spiced Butter','Authentic Ethiopian spiced clarified butter the base of all great Ethiopian cooking. 400g jar.',220.00,280.00,80,'https://images.unsplash.com/photo-1598839177447-da8f3c0e8416?w=600&q=80','',4.6,44 UNION ALL
        SELECT 'crafts','Ethiopian Mesob Basket','Hand-woven colorful Ethiopian injera basket Mesob. Height 40cm. Made by Dorze artisans.',680.00,900.00,30,'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80','hot',4.9,56 UNION ALL
        SELECT 'crafts','Clay Jebena Coffee Pot','Traditional Ethiopian clay jebena for coffee ceremony. Handmade in Addis Ababa.',350.00,NULL,45,'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600&q=80','new',4.7,38 UNION ALL
        SELECT 'crafts','Ethiopian Wall Art Panel','Hand-painted traditional Ethiopian art panel Lalibela churches theme. 60x40cm.',1500.00,1900.00,15,'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?w=600&q=80','',4.8,22 UNION ALL
        SELECT 'jewelry','Lalibela Cross Pendant','Sterling silver replica of the famous Lalibela cross pendant. Handcrafted by Lalibela artisans. With chain.',1800.00,2200.00,20,'https://images.unsplash.com/photo-1611085583191-a3b181a88401?w=600&q=80','hot',4.9,41 UNION ALL
        SELECT 'jewelry','Ethiopian Silver Bracelet','Traditional Ethiopian silver-plated bracelet with Coptic cross engravings. Adjustable size.',950.00,NULL,35,'https://images.unsplash.com/photo-1602173574767-37ac01994b2a?w=600&q=80','new',4.6,29 UNION ALL
        SELECT 'jewelry','Amber and Silver Necklace','Traditional amber bead necklace with silver spacers worn for centuries in Ethiopian culture.',2200.00,2800.00,12,'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=600&q=80','',4.8,18 UNION ALL
        SELECT 'books','Kebra Nagast English Translation','The Glory of Kings Ethiopias sacred national epic. English translation with annotations. Hardcover.',580.00,NULL,100,'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&q=80','new',4.7,54 UNION ALL
        SELECT 'books','Learn Amharic Complete Guide','Comprehensive Amharic language learning book for beginners to advanced. Includes audio QR codes.',420.00,550.00,200,'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&q=80','hot',4.8,89 UNION ALL
        SELECT 'books','Ethiopian History and Culture','In-depth exploration of Ethiopian civilization from Axum to modern times. Illustrated edition.',650.00,NULL,75,'https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=600&q=80','',4.6,37
    ) p ON c.slug = p.slug;
    " 2>&1

    echo "Database setup complete."
else
    echo "Database already exists."
fi

echo ""
echo "Starting Habesha Market on port 5000..."
php -S 0.0.0.0:5000 -t . router.php
