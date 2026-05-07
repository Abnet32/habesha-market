# Project Documentation

## 1. Objective

This update focused on:

- cleaning unused repository artifacts,
- preserving runtime-critical dependencies,
- implementing a cohesive light aqua storefront/admin color system based on the provided design direction,
- validating functionality after cleanup.

## 2. Dependency and Usage Analysis

### 2.1 Verified runtime entry points

- `start.sh` boots MariaDB, seeds `habesha_market`, and starts PHP built-in server.
- `router.php` is required by the PHP built-in server command.
- `connection.php` is required by runtime pages/actions for DB access.

### 2.2 Verified UI/runtime asset dependencies

- Shared templates load:
  - `css/style.css`
  - `js/main.js`
  - CDN fonts and Font Awesome
- AJAX endpoints used by client JS:
  - `ajax/search.php`
  - `ajax/cart_action.php`
  - `ajax/validate_email.php`

### 2.3 Database dependency checks

- Runtime DB is `habesha_market` (configured in `connection.php`).
- Seed data in `start.sh` and `database/habesha_market.sql` uses external image URLs.
- No runtime code path references legacy `store` schema.

## 3. Cleanup Changes

### 3.1 Removed as unused

- `bootstrap/` (entire folder): legacy Bootstrap assets not referenced by any runtime templates.
- `img/` (entire folder): legacy local product images not referenced by runtime code or current seeded DB.
- `database/store.sql`: obsolete legacy schema (`store`) not used by current runtime (`habesha_market`).
- `attached_assets/` (entire folder): design/instruction artifacts not part of runtime.

### 3.2 Kept intentionally

- `start.sh`, `router.php`: active startup/server flow.
- `replit.md`, `replit.nix`: environment metadata and setup context (not runtime-critical, but useful for workspace reproducibility).
- `database/habesha_market.sql`: current schema snapshot.

## 4. Updated Design System

### 4.1 Theme direction

Implemented a light, e-commerce-oriented aqua palette matching the provided reference intent:

- bright white/light-cyan surfaces,
- teal primary actions,
- warm accent for destructive/highlight states,
- removed purple-heavy accents for consistency.

### 4.2 Core token updates

In `css/style.css`, the root color tokens were migrated to a light theme:

- `--dark` / `--dark2` now represent light surface/background values.
- Primary/secondary/accent values tuned for aqua + orange contrast.
- Shadows and glass values adjusted for light surfaces.

### 4.3 Component-level visual updates

- Navbar surface moved from dark translucent to light translucent.
- Hero gradient/background changed to light atmospheric gradients.
- Footer, admin sidebar, and mobile nav surfaces shifted to light mode.
- Purple admin/jewelry accents replaced by aqua-consistent accents.
- JS particle colors adjusted for visibility on a light background.

## 5. File Roles (Post-Update)

### 5.1 Public storefront pages

- `index.php`: homepage and featured sections.
- `products.php`: shop listing and filters.
- `product_detail.php`: item detail and reviews.
- `cart.php`: cart state and quantity controls.
- `checkout.php`: shipping/payment form.
- `orders.php`: user orders.
- `profile.php`: profile and password updates.
- `order_success.php`: successful checkout confirmation.

### 5.2 Authentication and action handlers

- `login.php`, `signup.php`, `logout.php`.
- `actions/login_action.php`, `actions/signup_action.php`, `actions/checkout_action.php`, `actions/cart_add.php`, `actions/cart_remove.php`.

### 5.3 Admin area

- `admin/index.php`: dashboard overview.
- `admin/products.php`, `admin/add_product.php`, `admin/edit_product.php`.
- `admin/orders.php`, `admin/users.php`.
- `admin/actions/save_product.php`, `admin/actions/delete_product.php`.

### 5.4 Shared and client assets

- `includes/header.php`, `includes/footer.php`.
- `css/style.css`.
- `js/main.js`.
- `ajax/*.php` endpoints.

### 5.5 Configuration and infrastructure

- `connection.php`: DB connection.
- `start.sh`: local DB+server bootstrap.
- `router.php`: built-in PHP routing behavior.

## 6. Configuration Notes

- DB name: `habesha_market`.
- Server command is centralized in `start.sh`.
- No build pipeline, package manager, or asset compilation step required.

## 7. Testing and Validation

Recommended validation commands after each major change:

- `find . -name '*.php' -print0 | xargs -0 -n1 php -l`
- `bash -n start.sh`
- reference scan for removed artifacts:
  - `rg -n "bootstrap/|img/|attached_assets|database/store.sql"`

This ensures:

- PHP syntax integrity,
- startup script validity,
- no stale file references post-cleanup.
