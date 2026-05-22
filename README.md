# Habesha Market

Habesha Market is a complete PHP + MySQL/MariaDB e-commerce web application built as a course project. It simulates an Ethiopian online marketplace with a storefront, cart and checkout flow, user authentication, profile/order pages, and an admin area for managing products, orders, and users.

This README gives a concise overview, quick-start setup instructions, demo accounts, and links to the full documentation included in the repository.

---

## Quick Links

- Documentation (HTML): `HABESHA_MARKET_DOCUMENTATION.html`
- Full markdown docs: `DOCUMENTATION.md`
- Enhanced README: `README_ENHANCED.md`
- Submission checklist: `SUBMISSION_CHECKLIST.md`
- Setup & guide: `DOCUMENTATION_GUIDE.md`

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

## Quick Start — Local Development

Choose one of the following ways to run the project locally.

1. Recommended: use the provided helper script (sets up DB and starts built-in PHP server)

```bash
# from project root
bash start.sh

# open in browser
http://localhost:5000
```

2. Manual (import DB + PHP built-in server)

```bash
# 1. Import the database
# replace USER and PATH as appropriate
mysql -u root -p < database/habesha_market.sql

# 2. Configure database credentials in connection.php
#    - open connection.php and set DB_HOST, DB_USER, DB_PASSWORD, DB_NAME

# 3. Start the PHP built-in server
php -S localhost:8000 router.php

# 4. Visit the site
http://localhost:8000
```

Notes:

- `start.sh` prepares the local DB and starts the PHP server using `router.php`.
- If you use Apache/Nginx, point the document root to the project folder and ensure `mod_rewrite`/routing rules allow the app's URLs.

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

## Demo Accounts

Use these accounts for testing the app locally:

| Role     | Email                  | Password |
| -------- | ---------------------- | -------- |
| Admin    | admin@habeshamarket.et | password |
| Customer | abebe@example.com      | password |
| Customer | tigist@example.com     | password |

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

Run a quick PHP syntax check and validate the helper script:

```bash
find . -name '*.php' -print0 | xargs -0 -n1 php -l
bash -n start.sh
```

---

## Documentation & Submission

- Primary: `HABESHA_MARKET_DOCUMENTATION.html` (open in browser — print-friendly)
- Markdown: `DOCUMENTATION.md` (GitHub-ready)
- Quick README: `README_ENHANCED.md`
- Checklist: `SUBMISSION_CHECKLIST.md`
- Usage guide: `DOCUMENTATION_GUIDE.md`

Please include these files when creating the submission ZIP.

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
