<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once '../connection.php';

$pid = (int)($_GET['id'] ?? 0);
$uid = (int)$_SESSION['user_id'];

if ($pid > 0) {
    $p = mysqli_fetch_assoc(mysqli_query($con, "SELECT id, stock FROM products WHERE id=$pid AND is_active=1"));
    if ($p) {
        $existing = mysqli_fetch_assoc(mysqli_query($con, "SELECT id, quantity FROM cart_items WHERE user_id=$uid AND product_id=$pid"));
        if ($existing) {
            if ($existing['quantity'] < $p['stock']) {
                mysqli_query($con, "UPDATE cart_items SET quantity=quantity+1 WHERE user_id=$uid AND product_id=$pid");
            }
        } else {
            mysqli_query($con, "INSERT INTO cart_items (user_id, product_id, quantity) VALUES ($uid, $pid, 1)");
        }
    }
}
header('Location: ../cart.php');
exit;
