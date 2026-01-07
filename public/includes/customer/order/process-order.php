<?php
header('Content-Type: application/json');
require_once '../../../config/database-config.php';

// Guard
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$cart = $data['cart'] ?? [];
$checkout = $data['checkout'] ?? [];
$totals = $data['totals'] ?? [];
$user_id = $_SESSION['user_id'];

if (empty($cart) || empty($checkout) || empty($totals)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Invalid order data.']);
  exit();
}

// Start transaction
$conn->begin_transaction();

try {
  // 1. Insert into 'orders' table
  $order_type = strtolower($checkout['method']);
  $order_total = $totals['finalTotal'];
  $order_payment_method = strtolower($checkout['payment']);
  $order_delivery_address = $checkout['address'];
  // Set payment status based on payment method
  $order_payment_status = 'unpaid'; // Default to unpaid
  if ($order_payment_method === 'card') {
    $order_payment_status = 'paid';
  }

  $stmt_order = $conn->prepare(
    "INSERT INTO orders (user_id, order_type, order_total, order_payment_method, order_payment_status, order_delivery_address) VALUES (?, ?, ?, ?, ?, ?)"
  );
  $stmt_order->bind_param("isdsss", $user_id, $order_type, $order_total, $order_payment_method, $order_payment_status, $order_delivery_address);
  $stmt_order->execute();

  // 2. Get the new order_id
  $order_id = $conn->insert_id;
  if ($order_id == 0) {
    throw new Exception("Failed to create order.");
  }

  // 3. Insert into 'order_item' table
  $stmt_item = $conn->prepare(
    "INSERT INTO order_item (order_id, product_id, orderitem_quantity, orderitem_price, orderitem_subtotal) VALUES (?, ?, ?, ?, ?)"
  );

  foreach ($cart as $item) {
    $product_id = $item['id'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    $subtotal = $quantity * $price;

    $stmt_item->bind_param("iiidd", $order_id, $product_id, $quantity, $price, $subtotal);
    $stmt_item->execute();
  }

  // If all went well, commit the transaction
  $conn->commit();

  echo json_encode(['success' => true, 'message' => 'Order placed successfully!']);

} catch (Exception $e) {
  // Something went wrong, roll back
  $conn->rollback();
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Failed to place order: ' . $e->getMessage()]);
}

$stmt_order->close();
$stmt_item->close();
$conn->close();
?>

