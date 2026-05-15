<?php
header('Content-Type: application/json');
require_once '../connection.php';

// Normalize connection variable 
if (!isset($con) && isset($conn)) {
	$con = $conn;
}

if (!isset($con) || !($con instanceof mysqli) && !is_resource($con)) {
	echo json_encode(['error' => 'database connection not available']);
	exit;
}

$email = isset($_GET['email']) ? mysqli_real_escape_string($con, trim($_GET['email'])) : '';
$result = mysqli_query($con, "SELECT id FROM users WHERE email='$email' LIMIT 1");
if ($result === false) {
	echo json_encode(['error' => 'query_failed']);
	exit;
}

echo json_encode(['taken' => mysqli_num_rows($result) > 0]);
