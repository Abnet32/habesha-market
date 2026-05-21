# Project Documentation

## 1. Overview

Habesha Market is a PHP-based e-commerce application backed by MariaDB/MySQL. The repository contains the storefront, authentication flows, cart and checkout logic, user profile and order pages, plus an admin area for managing catalog and users.

## 2. Runtime Flow

- `start.sh` prepares the local database and launches the PHP built-in server.
- `router.php` handles routing for the built-in PHP server.
- `connection.php` centralizes the database connection.
- Shared UI is loaded through `includes/header.php` and `includes/footer.php`.
- Client interactions live in `js/main.js` and call the `ajax/` endpoints.

## 3. Page Structure

### Public storefront

- `index.php`: homepage and featured content
- `products.php`: catalog listing with filters and sorting
- `product_detail.php`: product detail view and reviews
- `cart.php`: cart view and quantity controls
- `checkout.php`: checkout form
- `order_success.php`: post-checkout confirmation
- `orders.php`: order history
- `profile.php`: profile management

### Authentication

- `login.php`: sign-in page
- `signup.php`: registration page
- `logout.php`: session cleanup and redirect

### Admin area

- `admin/index.php`: admin dashboard
- `admin/products.php`: product management list
- `admin/add_product.php`: add product form
- `admin/edit_product.php`: edit product form
- `admin/orders.php`: order management
- `admin/users.php`: user management

### Action and async endpoints

- `actions/login_action.php`
- `actions/signup_action.php`
- `actions/checkout_action.php`
- `actions/cart_add.php`
- `actions/cart_remove.php`
- `ajax/search.php`
- `ajax/cart_action.php`
- `ajax/validate_email.php`

## 4. Shared Assets

- `css/style.css`: global theme, layout, and responsive rules
- `js/main.js`: UI behavior, validation, particles, and AJAX hooks
- `assets/`: shared images and icons used by the pages

## 5. UI Notes

- The auth pages use a dedicated split-screen layout with a visual panel on the left and forms on the right.
- The shared site chrome is hidden on auth pages so login and signup remain focused.
- The signup layout is tightened so the form fits in the viewport more cleanly.

## 6. Database

- Current database name: `habesha_market`
- Schema snapshot: `database/habesha_market.sql`
- Database connection: `connection.php`

## 7. Cleanup Notes

Low-risk cleanup was limited to removing obvious commented-out leftovers from the auth template and stylesheet. No runtime code path was removed unless it was clearly unused or dead.

## 8. Validation

Recommended checks after code changes:

```bash
find . -name '*.php' -print0 | xargs -0 -n1 php -l
bash -n start.sh
```

For quick reference scans:

```bash
rg -n "bootstrap/|attached_assets|database/store.sql|commented-out" .
```
