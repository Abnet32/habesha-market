<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../connection.php';

if (!isset($con) || !$con) {
    $_SESSION['login_error'] = 'Database connection error.';
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../login.php'); exit; }

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Please fill in all fields.';
    header('Location: ../login.php');
    exit;
}

$email_safe = mysqli_real_escape_string($con, $email);
$user = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE email='$email_safe' AND is_active=1 LIMIT 1"));

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = 'Invalid email or password. Please try again.';
    header('Location: ../login.php');
    exit;
}

// Set session
$_SESSION['user_id'] = $user['id'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];

header('Location: ../index.php');
exit;
