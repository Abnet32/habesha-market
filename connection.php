<?php
$con = mysqli_connect('127.0.0.1', 'root', '', 'habesha_market', 3306, '/tmp/mysql.sock');

if (!$con) {
    $con = mysqli_connect('127.0.0.1', 'root', '', 'habesha_market', 3306);
}

if ($con) {
    mysqli_set_charset($con, 'utf8mb4');
}
