<?php

require_once __DIR__ . '/config.php';

$dbHost = getenv('DB_HOST');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASSWORD');
$dbName = getenv('DB_NAME');
$dbPort = (int)getenv('DB_PORT');

$con = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName, $dbPort);

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($con, 'utf8mb4');