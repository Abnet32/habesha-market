<?php
header('Content-Type: application/json');
require_once '../connection.php';

$q = isset($_GET['q']) ? mysqli_real_escape_string($con, trim($_GET['q'])) : '';
if (strlen($q) < 2) { echo json_encode([]); exit; }

$result = mysqli_query($con, "
    SELECT p.id, p.name, p.price, p.image_url, c.name as category
    FROM products p JOIN categories c ON c.id=p.category_id
    WHERE p.is_active=1 AND (p.name LIKE '%$q%' OR p.description LIKE '%$q%' OR c.name LIKE '%$q%')
    ORDER BY p.rating DESC LIMIT 8
");

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'price' => $row['price'],
        'image' => $row['image_url'],
        'category' => $row['category']
    ];
}
echo json_encode($data);
