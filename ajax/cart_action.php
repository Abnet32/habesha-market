<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../connection.php';

function cart_count($con, $uid) {
    $r = mysqli_query($con, "SELECT SUM(quantity) as c FROM cart_items WHERE user_id=$uid");
    return (int)(mysqli_fetch_assoc($r)['c'] ?? 0);
}

function cart_total($con, $uid) {
    $r = mysqli_query($con, "SELECT SUM(p.price * ci.quantity) as t FROM cart_items ci JOIN products p ON p.id=ci.product_id WHERE ci.user_id=$uid");
    return (float)(mysqli_fetch_assoc($r)['t'] ?? 0);
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'login' => true]);
    exit;
}

$uid = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $pid = (int)($_POST['product_id'] ?? 0);
    // Check product exists and has stock
    $p = mysqli_fetch_assoc(mysqli_query($con, "SELECT id, stock FROM products WHERE id=$pid AND is_active=1"));
    if (!$p) { echo json_encode(['success'=>false,'message'=>'Product not found']); exit; }

    $existing = mysqli_fetch_assoc(mysqli_query($con, "SELECT id, quantity FROM cart_items WHERE user_id=$uid AND product_id=$pid"));
    if ($existing) {
        if ($existing['quantity'] >= $p['stock']) {
            echo json_encode(['success'=>false,'message'=>'Max stock reached']);
            exit;
        }
        mysqli_query($con, "UPDATE cart_items SET quantity=quantity+1 WHERE user_id=$uid AND product_id=$pid");
    } else {
        mysqli_query($con, "INSERT INTO cart_items (user_id, product_id, quantity) VALUES ($uid, $pid, 1)");
    }
    echo json_encode(['success'=>true,'count'=>cart_count($con,$uid)]);

} elseif ($action === 'update') {
    $item_id = (int)($_POST['item_id'] ?? 0);
    $qty = max(1, (int)($_POST['qty'] ?? 1));

    // Get price for subtotal
    $item = mysqli_fetch_assoc(mysqli_query($con, "SELECT ci.id, p.price FROM cart_items ci JOIN products p ON p.id=ci.product_id WHERE ci.id=$item_id AND ci.user_id=$uid"));
    if (!$item) { echo json_encode(['success'=>false]); exit; }

    mysqli_query($con, "UPDATE cart_items SET quantity=$qty WHERE id=$item_id AND user_id=$uid");
    $subtotal = $item['price'] * $qty;
    $total = cart_total($con, $uid) + 50; // +shipping

    echo json_encode([
        'success' => true,
        'subtotal' => $subtotal,
        'total' => $total,
        'count' => cart_count($con, $uid)
    ]);

} elseif ($action === 'remove') {
    $item_id = (int)($_POST['item_id'] ?? 0);
    mysqli_query($con, "DELETE FROM cart_items WHERE id=$item_id AND user_id=$uid");
    echo json_encode(['success'=>true,'count'=>cart_count($con,$uid)]);

} else {
    echo json_encode(['success'=>false,'message'=>'Unknown action']);
}
