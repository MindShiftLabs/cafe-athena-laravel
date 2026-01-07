<?php
header('Content-Type: application/json');
require_once '../../../config/database-config.php';

$sql = "SELECT
            product_id AS id,
            product_name AS name,
            product_price AS price,
            product_category AS category,
            product_image AS imageUrl,
            product_has_stock AS hasStock
        FROM product
        WHERE product_status = 'available'
        ORDER BY product_category, product_name";

$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    // Convert numeric types
    $row['price'] = (float) $row['price'];
    $row['hasStock'] = (bool) $row['hasStock'];
    $products[] = $row;
  }
}

echo json_encode($products);

$conn->close();
?>

