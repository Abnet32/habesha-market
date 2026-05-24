# Habesha Market

Habesha Market is a complete PHP + MySQL/MariaDB e-commerce web application built as a course project. It simulates an Ethiopian online marketplace with a storefront, cart and checkout flow, user authentication, profile/order pages, and an admin area for managing products, orders, and users.

This README gives a concise overview, quick-start setup instructions, demo accounts, and links to the full documentation included in the repository.

---

## Features

- Product browsing, category filtering and sorting
- Product detail pages with reviews and ratings
- Live AJAX search and email validation
- Shopping cart with AJAX quantity updates
- Checkout with multiple payment options (Cash on Delivery, Bank Transfer, Mobile Money)
- User registration, login, profile management and order history
- Admin dashboard: manage products, orders, users, and view analytics
- Responsive UI with glassmorphism design and particle background
- Security measures: input sanitization, password hashing, session guards

---

## Technology Stack

- PHP 7.4 / 8.x (vanilla PHP, MySQLi)
- MySQL / MariaDB (utf8mb4)
- HTML5, CSS3 (responsive, custom theme in `css/style.css`)
- Vanilla JavaScript (`js/main.js`) with Fetch/AJAX
- Font Awesome, Google Fonts

---

## Quick Start — Local Development (XAMPP)

This project is designed to run on a standard PHP + MySQL stack. The recommended, standard approach for local development is to install XAMPP and run the app under Apache + MySQL provided by XAMPP.

1. Download and install XAMPP from https://www.apachefriends.org/ (Windows, macOS, Linux).

2. Start Apache and MySQL using the XAMPP Control Panel (or start the services on macOS/Linux).

3. Copy the project folder to XAMPP's web root:

- Windows: `C:\xampp\htdocs\habesha-market`
- macOS (XAMPP): `/Applications/XAMPP/htdocs/habesha-market`
- Linux (XAMPP): `/opt/lampp/htdocs/habesha-market`

4. Import the database using phpMyAdmin (`http://localhost/phpmyadmin`) or the MySQL CLI:

```bash
mysql -u root -p < database/habesha_market.sql
```

5. Update `connection.php` with your database credentials. Default XAMPP credentials are typically `user=root` and an empty password (or as configured in your XAMPP installation).

6. Open the site in your browser:

- http://localhost/habesha-market/

If you prefer a different environment (native Apache/Nginx or PHP built-in server), you can still host the project there — just ensure the document root points at the project folder and that the database is imported and configured.

---

## Configuration

- Edit `connection.php` to update database credentials (or use environment variables). Example snippet in `connection.php`:

```php
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('DB_USER') ?: 'habesha_user';
$dbPassword = getenv('DB_PASSWORD') ?: 'secret';
$dbName = getenv('DB_NAME') ?: 'habesha_market';
```

- Ensure `mysqli` extension is enabled in your PHP installation.

---

## Project Structure (short)

- `index.php` — Home page
- `products.php` — Product listing & filters
- `product_detail.php` — Product detail & reviews
- `cart.php` — Shopping cart
- `checkout.php` — Checkout flow
- `login.php`, `signup.php`, `logout.php` — Auth pages
- `profile.php`, `orders.php` — User pages
- `admin/` — Admin dashboard & management
- `actions/` — Form handlers (POST actions)
- `ajax/` — JSON endpoints for AJAX
- `includes/` — `header.php`, `footer.php`
- `css/` — `style.css` (theme & responsive layout)
- `js/` — `main.js` (validation, AJAX, UI behavior)
- `database/habesha_market.sql` — Schema + seed data

---

## Security & Best Practices

- All user inputs are validated and escaped. Outputs are encoded with `htmlspecialchars()` to prevent XSS.
- Database queries use proper escaping and integer casting to reduce SQL injection risk.
- Passwords are hashed using `password_hash()` and verified with `password_verify()`.
- Protected pages check `$_SESSION['user_id']` and admin pages verify `$_SESSION['role'] === 'admin'`.

---

## Testing & Validation

Run a quick PHP syntax check:

```bash
find . -name '*.php' -print0 | xargs -0 -n1 php -l
```

---


## Contributing & Notes

- This project was built as a course group assignment. If you re-use code, cite appropriately.
- For local development, ensure your PHP version and MySQL/MariaDB are compatible.

If you'd like I can also:

- Add direct links to screenshots in the HTML doc (if you provide the final screenshot files),
- Generate a zip with documentation and the SQL dump for submission, or
- Create a short README tailored for GitHub Pages hosting.

---

## Contact

Habesha Market Team — group@habeshamarket.et

---

Enjoy! 🚀
