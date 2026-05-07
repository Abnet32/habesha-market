# Habesha Market (PHP + MariaDB)

Habesha Market is a full-stack e-commerce course project for authentic Ethiopian products. The app includes storefront browsing, cart and checkout flows, profile management, and an admin dashboard for products, orders, and users.

## Stack

- PHP 8.2 (built-in server)
- MariaDB/MySQL (MySQLi)
- Vanilla JavaScript
- Custom CSS theme (light aqua e-commerce style)
- Font Awesome (CDN)

## Quick Start

1. Run `bash start.sh`
2. Open `http://localhost:5000`

`start.sh` initializes MariaDB data, creates the `habesha_market` database if missing, seeds core tables/data, and starts the PHP server using `router.php`.

## Runtime Configuration

- Database connection: `connection.php`
- Default DB name: `habesha_market`
- Socket fallback: `/tmp/mysql.sock`, then TCP
- PHP server: `php -S 0.0.0.0:5000 -t . router.php`

## Main Project Structure

- `index.php`: Home page (hero, categories, featured products, testimonials)
- `products.php`: Product listing with filter/search/sort
- `product_detail.php`: Product details and reviews
- `cart.php`: Cart management
- `checkout.php`: Checkout and payment method submission
- `orders.php`: User order history
- `profile.php`: Account profile and password update
- `login.php`, `signup.php`, `logout.php`: Authentication pages
- `order_success.php`: Post-checkout confirmation
- `actions/`: Form/action handlers (auth, cart, checkout)
- `ajax/`: Async endpoints (`search`, `cart_action`, `validate_email`)
- `admin/`: Admin dashboard and product/order/user management
- `includes/`: Shared header/footer templates
- `css/style.css`: Global theme and component styles
- `js/main.js`: Client-side interactions, animations, AJAX hooks
- `database/habesha_market.sql`: Current schema/data snapshot

## Dependencies

- External CDN:
  - Google Fonts (`Poppins`, `Playfair Display`)
  - Font Awesome 6
- No npm/build pipeline required.

## Validation and Quality Checks

- PHP syntax check:
  - `find . -name '*.php' -print0 | xargs -0 -n1 php -l`
- Startup script syntax check:
  - `bash -n start.sh`

## Cleanup and Theme Update Notes

See `PROJECT_DOCUMENTATION.md` for:

- file/folder cleanup decisions and removed artifacts,
- dependency/reference analysis summary,
- full documentation of design token and color scheme changes,
- post-change testing results.
