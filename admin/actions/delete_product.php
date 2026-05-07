<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../../login.php'); exit;
}

require_once '../../connection.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: ../products.php?error=Invalid+product+ID'); exit; }

// Soft delete — keeps order history intact
$sql = "UPDATE products SET is_active=0 WHERE id=$id";
if (mysqli_query($con, $sql) && mysqli_affected_rows($con) > 0) {
    header('Location: ../products.php?success=deleted');
} else {
    header('Location: ../products.php?error=Could+not+delete+product');
}
exit;
