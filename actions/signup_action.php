<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../signup.php'); exit; }

$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$city = trim($_POST['city'] ?? 'Addis Ababa');
$address = trim($_POST['address'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

// Validation
if (strlen($full_name) < 3) {
    $_SESSION['signup_error'] = 'Name must be at least 3 characters.';
    header('Location: ../signup.php'); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['signup_error'] = 'Please enter a valid email address.';
    header('Location: ../signup.php'); exit;
}
if (strlen($password) < 8) {
    $_SESSION['signup_error'] = 'Password must be at least 8 characters.';
    header('Location: ../signup.php'); exit;
}
if ($password !== $confirm) {
    $_SESSION['signup_error'] = 'Passwords do not match.';
    header('Location: ../signup.php'); exit;
}

// Check email uniqueness
$email_safe = mysqli_real_escape_string($con, $email);
$existing = mysqli_query($con, "SELECT id FROM users WHERE email='$email_safe' LIMIT 1");
if (mysqli_num_rows($existing) > 0) {
    $_SESSION['signup_error'] = 'An account with this email already exists.';
    header('Location: ../signup.php'); exit;
}

// Insert user (password hashed)
$name_safe = mysqli_real_escape_string($con, $full_name);
$phone_safe = mysqli_real_escape_string($con, $phone);
$city_safe = mysqli_real_escape_string($con, $city);
$addr_safe = mysqli_real_escape_string($con, $address);
$hashed = password_hash($password, PASSWORD_DEFAULT);

$result = mysqli_query($con, "INSERT INTO users (full_name, email, phone, city, address, password)
    VALUES ('$name_safe', '$email_safe', '$phone_safe', '$city_safe', '$addr_safe', '$hashed')");

if ($result) {
    $_SESSION['signup_success'] = 'Account created successfully! Please login.';
    header('Location: ../login.php');
} else {
    $_SESSION['signup_error'] = 'Registration failed. Please try again.';
    header('Location: ../signup.php');
}
exit;
