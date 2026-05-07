<?php
header('Content-Type: application/json');
require_once '../connection.php';

$email = isset($_GET['email']) ? mysqli_real_escape_string($con, trim($_GET['email'])) : '';
$result = mysqli_query($con, "SELECT id FROM users WHERE email='$email' LIMIT 1");
echo json_encode(['taken' => mysqli_num_rows($result) > 0]);
