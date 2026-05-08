<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once '../connection.php';

// Ensure $con is available (some connection files use $conn or $mysqli)
if (!isset($con)) {
    if (isset($conn)) {
        $con = $conn;
    } elseif (isset($mysqli) && $mysqli instanceof mysqli) {
        $con = $mysqli;
    } else {
        die('Database connection not found.');
    }
}

$uid = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../checkout.php'); exit; }

// Validate inputs
$full_name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$city = trim($_POST['city'] ?? '');
$address = trim($_POST['address'] ?? '');
$payment = $_POST['payment_method'] ?? 'cash_on_delivery';
$notes = trim($_POST['notes'] ?? '');

$allowed_payments = ['cash_on_delivery', 'bank_transfer', 'mobile_money'];
if (!in_array($payment, $allowed_payments)) $payment = 'cash_on_delivery';

if (empty($full_name) || empty($phone) || empty($address)) {
    $_SESSION['checkout_error'] = 'Please fill in all required fields.';
    header('Location: ../checkout.php'); exit;
}

// Get cart items
$items = mysqli_query($con, "
    SELECT ci.quantity, p.id as pid, p.name, p.price, p.stock
    FROM cart_items ci JOIN products p ON p.id=ci.product_id
    WHERE ci.user_id=$uid
");
$cart = [];
$subtotal = 0;
while ($r = mysqli_fetch_assoc($items)) {
    $cart[] = $r;
    $subtotal += $r['price'] * $r['quantity'];
}

if (empty($cart)) { header('Location: ../cart.php'); exit; }

$shipping = 50;
$total = $subtotal + $shipping;

// Generate order number
$order_number = 'HM-' . strtoupper(substr(md5(uniqid()), 0, 8));

// Sanitize
$fn = mysqli_real_escape_string($con, $full_name);
$ph = mysqli_real_escape_string($con, $phone);
$ci = mysqli_real_escape_string($con, $city);
$ad = mysqli_real_escape_string($con, $address);
$no = mysqli_real_escape_string($con, $notes);

// Insert order
$ok = mysqli_query($con, "INSERT INTO orders (user_id, order_number, total_amount, shipping_address, city, phone, payment_method, notes)
    VALUES ($uid, '$order_number', $total, '$ad', '$ci', '$ph', '$payment', '$no')");

if (!$ok) {
    $_SESSION['checkout_error'] = 'Order failed. Please try again.';
    header('Location: ../checkout.php'); exit;
}

$order_id = mysqli_insert_id($con);

// Insert order items
foreach ($cart as $item) {
    $name = mysqli_real_escape_string($con, $item['name']);
    $price = (float)$item['price'];
    $qty = (int)$item['quantity'];
    $pid = (int)$item['pid'];
    mysqli_query($con, "INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES ($order_id, $pid, '$name', $price, $qty)");
    // Reduce stock
    mysqli_query($con, "UPDATE products SET stock=GREATEST(0, stock-$qty) WHERE id=$pid");
}

// Clear cart
mysqli_query($con, "DELETE FROM cart_items WHERE user_id=$uid");

$_SESSION['order_success'] = $order_number;
header('Location: ../order_success.php');
exit;
