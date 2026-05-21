# Habesha Market

Habesha Market is a PHP + MariaDB e-commerce project for Ethiopian products. It includes a storefront, cart and checkout flow, user authentication, profile/order pages, and an admin area for managing products, orders, and users.

## Features

- Product browsing, search, and category filtering
- Product detail pages with reviews
- Cart, checkout, and order confirmation flow
- User signup, login, logout, and profile updates
- Admin dashboard with product, order, and user management
- AJAX helpers for search, cart actions, and email validation
- Split-screen auth pages with a left visual panel and right form panel

## Tech Stack

- PHP 8.2
- MariaDB / MySQLi
- Vanilla JavaScript
- Custom CSS in `css/style.css`
- Font Awesome CDN
- Google Fonts CDN

## Run Locally

1. Start the app:

```bash
bash start.sh
```

2. Open the site:

```text
http://localhost:5000
```

`start.sh` prepares the local database and starts the PHP built-in server using `router.php`.

## Main Structure

- `index.php`: home page
- `products.php`: product listing, filtering, and sorting
- `product_detail.php`: individual product details
- `cart.php`: cart management
- `checkout.php`: checkout form
- `orders.php`: order history
- `profile.php`: account settings
- `login.php`, `signup.php`, `logout.php`: authentication pages
- `actions/`: form handlers for auth, cart, and checkout
- `ajax/`: asynchronous endpoints used by JavaScript
- `admin/`: admin dashboard and management screens
- `includes/`: shared header and footer templates
- `css/style.css`: global styling and responsive layout
- `js/main.js`: UI behavior, validation, and AJAX interactions
- `database/habesha_market.sql`: schema and seed data

## Validation

```bash
find . -name '*.php' -print0 | xargs -0 -n1 php -l
bash -n start.sh
```

## Notes

- No frontend build step is required.
- Database connection settings live in `connection.php`.
- Auth pages use a dedicated split-screen layout and hide the shared site chrome while they are open.
