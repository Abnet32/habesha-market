# Habesha Market — Ethiopian Online Marketplace

## Project Overview
A full-featured PHP/MySQL e-commerce web application built for an Ethiopian university course project. The marketplace sells authentic Ethiopian goods: coffee, traditional fashion, spices, handcrafts, jewelry, and books.

## Tech Stack
- **Backend**: PHP 8.2 (built-in server via `start.sh`)
- **Database**: MariaDB (socket at `/tmp/mysql.sock`, DB: `habesha_market`)
- **Frontend**: HTML5, CSS3 (custom, no Bootstrap grid), Vanilla JavaScript
- **Icons**: Font Awesome 6
- **Images**: Unsplash (via CDN URLs)

## Running the App
The workflow "Start application" runs `bash start.sh` which:
1. Initializes MariaDB data directory (if needed)
2. Starts MariaDB daemon
3. Creates the database + all 7 tables + seed data (if DB doesn't exist)
4. Starts PHP built-in server on **port 5000**

## Database Schema (7 tables)
| Table | Purpose |
|-------|---------|
| `users` | Customer and admin accounts (bcrypt passwords) |
| `categories` | 6 product categories (coffee, fashion, spices, crafts, jewelry, books) |
| `products` | 20 seed products with images, ratings, badges |
| `cart_items` | Per-user shopping cart (session-based, DB-backed) |
| `orders` | Order records with status tracking |
| `order_items` | Line items for each order |
| `reviews` | Product reviews (one per user per product) |

## Demo Accounts (password: `password`)
- `abebe@example.com` — customer
- `tigist@example.com` — customer
- `admin@habeshamarket.et` — admin

## Course Requirements Met
- HTML/CSS/JS/PHP/MySQL ✓
- 7 database tables (5+ required) ✓
- CRUD operations (products, cart, orders, reviews, users) ✓
- PHP Sessions (login/logout/cart) ✓
- AJAX (live search, cart actions, email validation) ✓
- Client + server-side form validation ✓
- `password_hash()` / `password_verify()` ✓
- SQL injection prevention (prepared statements / int casting / `real_escape_string`) ✓
- Mobile-responsive design ✓
- Animations (CSS keyframes, scroll reveal, particle canvas, counters) ✓

## Key Files
- `index.php` — Homepage: hero, categories, featured products, stats, testimonials
- `products.php` — Shop with AJAX live search + category/sort filters
- `product_detail.php` — Product page with image, pricing, add-to-cart, reviews
- `cart.php` — Shopping cart with quantity controls
- `checkout.php` — Checkout form with address, payment method
- `orders.php` — Order history for logged-in users
- `profile.php` — Account settings
- `login.php` / `signup.php` — Auth pages
- `connection.php` — MySQLi DB connection (socket + TCP fallback)
- `includes/header.php` — Navbar with session-aware links
- `includes/footer.php` — Footer with links
- `css/style.css` — Full custom stylesheet (Ethiopian green/gold/red theme, glassmorphism)
- `js/main.js` — AJAX search, cart actions, animations, form validation
- `ajax/search.php` — AJAX product search endpoint
- `ajax/cart_action.php` — AJAX add/remove/update cart
- `ajax/validate_email.php` — AJAX email uniqueness check
- `actions/login_action.php` — Login form handler
- `actions/signup_action.php` — Registration handler
- `actions/checkout_action.php` — Order placement handler
- `router.php` — PHP built-in server router
- `start.sh` — MariaDB + PHP server startup script

## User Preferences
- Ethiopian flag color scheme: green (#2D6A4F / #4CAF50), gold (#FFB800), red (#E63946)
- Dark theme with glassmorphism cards
- Particle background animations
- ETB (Ethiopian Birr) currency
- Mobile-first responsive layout
