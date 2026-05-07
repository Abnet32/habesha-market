<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once '../connection.php';

$cart_id = (int)($_GET['id'] ?? 0);
$uid = (int)$_SESSION['user_id'];

if ($cart_id > 0) {
    mysqli_query($con, "DELETE FROM cart_items WHERE id=$cart_id AND user_id=$uid");
}
header('Location: ../cart.php');
exit;
