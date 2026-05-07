<?php
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('DB_USER') ?: 'habesha_market_user';
$dbPassword = getenv('DB_PASSWORD') ?: 'habesha_market_password';
$dbName = getenv('DB_NAME') ?: 'habesha_market';
$dbPort = (int) (getenv('DB_PORT') ?: 3306);
$dbSocket = getenv('DB_SOCKET') ?: '/tmp/mysql.sock';

$con = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName, $dbPort, $dbSocket);

if (!$con) {
    $con = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName, $dbPort);
}

if ($con) {
    mysqli_set_charset($con, 'utf8mb4');
}
