<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../../login.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../products.php'); exit; }

require_once '../../connection.php';

// Ensure $con is available (some connection files use different variable names)
if (!isset($con)) {
    if (isset($conn)) {
        $con = $conn;
    } elseif (isset($mysqli)) {
        $con = $mysqli;
    } elseif (isset($link)) {
        $con = $link;
    } else {
        // Attempt to create a connection if common DB constants are defined
        if (defined('DB_HOST') && defined('DB_USER') && defined('DB_NAME')) {
            $dbpass = defined('DB_PASS') ? DB_PASS : '';
            $con = mysqli_connect(DB_HOST, DB_USER, $dbpass, DB_NAME);
        }
    }
}

// Validate connection exists
if (!isset($con) || $con === false || $con === null) {
    header('Location: ../products.php?error=Database+connection+failed');
    exit;
}

$action       = $_POST['action'] ?? '';
$id           = (int)($_POST['id'] ?? 0);
$name         = trim($_POST['name'] ?? '');
$category_id  = (int)($_POST['category_id'] ?? 0);
$price        = (float)($_POST['price'] ?? 0);
$orig_price   = $_POST['original_price'] !== '' ? (float)$_POST['original_price'] : null;
$stock        = (int)($_POST['stock'] ?? 0);
$image_url    = trim($_POST['image_url'] ?? '');
$description  = trim($_POST['description'] ?? '');
$badge        = trim($_POST['badge'] ?? '');

// Validation
$errors = [];
if (strlen($name) < 2)        $errors[] = 'Product name is required.';
if ($category_id <= 0)        $errors[] = 'Please select a category.';
if ($price <= 0)               $errors[] = 'Price must be greater than 0.';
if ($stock < 0)                $errors[] = 'Stock cannot be negative.';
if (!filter_var($image_url, FILTER_VALIDATE_URL)) $errors[] = 'Please enter a valid image URL.';
if (strlen($description) < 5) $errors[] = 'Description is required.';

if (!empty($errors)) {
    $_SESSION['old_input'] = $_POST;
    $errMsg = urlencode($errors[0]);
    if ($action === 'edit') {
        header("Location: ../edit_product.php?id=$id&error=$errMsg"); exit;
    } else {
        header("Location: ../add_product.php?error=$errMsg"); exit;
    }
}

$name_esc  = mysqli_real_escape_string($con, $name);
$img_esc   = mysqli_real_escape_string($con, $image_url);
$desc_esc  = mysqli_real_escape_string($con, $description);
$badge_esc = mysqli_real_escape_string($con, $badge);
$orig_sql  = $orig_price !== null ? $orig_price : 'NULL';

if ($action === 'add') {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    $slug_esc = mysqli_real_escape_string($con, $slug);
    $sql = "INSERT INTO products (category_id, name, slug, description, price, original_price, image_url, badge, stock, is_active, rating, review_count)
            VALUES ($category_id, '$name_esc', '$slug_esc', '$desc_esc', $price, $orig_sql, '$img_esc', '$badge_esc', $stock, 1, 0.0, 0)";
    mysqli_query($con, $sql);
    header('Location: ../products.php?success=added'); exit;
}

if ($action === 'edit') {
    if ($id <= 0) { header('Location: ../products.php?error=Invalid+product'); exit; }
    $sql = "UPDATE products SET
            category_id=$category_id, name='$name_esc', description='$desc_esc',
            price=$price, original_price=$orig_sql, image_url='$img_esc',
            badge='$badge_esc', stock=$stock
            WHERE id=$id AND is_active=1";
    mysqli_query($con, $sql);
    header('Location: ../products.php?success=updated'); exit;
}

header('Location: ../products.php');
