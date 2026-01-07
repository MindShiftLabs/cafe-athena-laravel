<?php
header('Content-Type: application/json');
require_once '../../../config/database-config.php';

// Guard
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT
            order_id,
            order_status,
            order_type,
            order_total,
            order_createdat
        FROM orders
        WHERE user_id = ?
        ORDER BY order_createdat DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
  }
}

echo json_encode($orders);

$conn->close();
?>

