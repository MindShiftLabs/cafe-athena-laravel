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
$order_id = $data['order_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$order_id) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Invalid Order ID.']);
  exit();
}

// Update the order status to 'completed' and set the completed timestamp
// Crucially, we also check that the order belongs to the logged-in user
$sql = "UPDATE orders SET order_status = 'completed', order_completedat = NOW() WHERE order_id = ? AND user_id = ? AND order_status = 'ready'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);

if ($stmt->execute()) {
  if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Order marked as completed!']);
  } else {
    // This can happen if the order wasn't in the 'ready' state or didn't belong to the user
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order could not be updated. It may not be ready or does not belong to you.']);
  }
} else {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error.']);
}

$stmt->close();
$conn->close();
?>

