<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../../login.php'); exit;
}

require_once '../../connection.php';

if (!isset($conn) || !$conn) {
    header('Location: ../products.php?error=Database+connection+failed'); exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: ../products.php?error=Invalid+product+ID'); exit; }

// Soft delete — keeps order history intact
$stmt = $conn->prepare("UPDATE products SET is_active=0 WHERE id=?");
$stmt->bind_param("i", $id);
if ($stmt->execute() && $stmt->affected_rows > 0) {
    header('Location: ../products.php?success=deleted');
} else {
    header('Location: ../products.php?error=Could+not+delete+product');
}
$stmt->close();
exit;
