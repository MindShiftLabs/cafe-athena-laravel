<?php
require_once '../../config/database-config.php';

function getAllProducts($conn, $searchTerm = null)
{
  $sql = "SELECT product_id, product_name, product_description, product_price, product_image, product_status, product_has_stock, product_category ,product_createdat FROM product";

  if ($searchTerm) {
    $sql .= " WHERE product_name LIKE ? OR product_description LIKE ?";
  }

  $sql .= " ORDER BY product_id DESC";

  $stmt = $conn->prepare($sql);

  if ($searchTerm) {
    $likeTerm = "%$searchTerm%";
    $stmt->bind_param("ss", $likeTerm, $likeTerm);
  }

  $stmt->execute();
  $result = $stmt->get_result();
  $products = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  return $products;
}

?>

