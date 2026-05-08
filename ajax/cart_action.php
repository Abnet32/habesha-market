<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../connection.php';

if (!isset($con) && isset($conn)) {
    $con = $conn;
}

// Ensure $con is defined and valid
if (!isset($con) || !$con) {
    // If an alternative variable exists use it, otherwise return error
    if (isset($conn) && $conn) {
        $con = $conn;
    } else {
        echo json_encode(['success' => false, 'message' => 'Database connection not found']);
        exit;
    }
}

function cart_count(mysqli $con, int $uid): int {
    $r = mysqli_query($con, "SELECT SUM(quantity) as c FROM cart_items WHERE user_id=$uid");
    return (int) (mysqli_fetch_assoc($r)['c'] ?? 0);
}

function cart_total(mysqli $con, int $uid): float {
    $r = mysqli_query($con, "SELECT SUM(p.price * ci.quantity) as t FROM cart_items ci JOIN products p ON p.id=ci.product_id WHERE ci.user_id=$uid");
    return (float) (mysqli_fetch_assoc($r)['t'] ?? 0);
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
    $stmt = mysqli_prepare($con, "SELECT id, stock FROM products WHERE id=? AND is_active=1");
    mysqli_stmt_bind_param($stmt, 'i', $pid);
    mysqli_stmt_execute($stmt);
    $p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$p) { echo json_encode(['success'=>false,'message'=>'Product not found']); exit; }

    $stmt = mysqli_prepare($con, "SELECT id, quantity FROM cart_items WHERE user_id=? AND product_id=?");
    mysqli_stmt_bind_param($stmt, 'ii', $uid, $pid);
    mysqli_stmt_execute($stmt);
    $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($existing) {
        if ($existing['quantity'] >= $p['stock']) {
            echo json_encode(['success'=>false,'message'=>'Max stock reached']);
            exit;
        }
        $stmt = mysqli_prepare($con, "UPDATE cart_items SET quantity=quantity+1 WHERE user_id=? AND product_id=?");
        mysqli_stmt_bind_param($stmt, 'ii', $uid, $pid);
        mysqli_stmt_execute($stmt);
    } else {
        $stmt = mysqli_prepare($con, "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, 1)");
        mysqli_stmt_bind_param($stmt, 'ii', $uid, $pid);
        mysqli_stmt_execute($stmt);
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
