<?php
require_once '../../../config/database-config.php';

header('Content-Type: application/json');

if (isset($_GET['order_id'])) {
  $order_id = $_GET['order_id'];

  // Fetch order details
  $query = "SELECT o.order_id, CONCAT(u.user_firstname, ' ', u.user_lastname) AS customer_name, o.order_createdat, o.order_payment_method, o.order_total FROM `orders` o JOIN `user` u ON o.user_id = u.user_id WHERE o.order_id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $order_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $orderDetails = $result->fetch_assoc();

    // Fetch order items
    $items_query = "SELECT p.product_name, oi.orderitem_quantity, oi.orderitem_price, oi.orderitem_subtotal FROM `order_item` oi JOIN `product` p ON oi.product_id = p.product_id WHERE oi.order_id = ?";
    $items_stmt = $conn->prepare($items_query);
    $items_stmt->bind_param("i", $order_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();

    $items = [];
    while ($item_row = $items_result->fetch_assoc()) {
      $items[] = $item_row;
    }

    $orderDetails['items'] = $items;

    echo json_encode($orderDetails);
  } else {
    echo json_encode(['error' => 'Order not found']);
  }
} else {
  echo json_encode(['error' => 'No order ID provided']);
}
?>

