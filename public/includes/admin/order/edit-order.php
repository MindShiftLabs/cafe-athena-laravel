<?php
require_once '../../../config/database-config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $order_id = $_POST['order_id'];
  $order_status = $_POST['order_status'];
  $order_payment_status = $_POST['order_payment_status'];

  $sql = "UPDATE orders SET order_status = ?, order_payment_status = ? WHERE order_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssi", $order_status, $order_payment_status, $order_id);

  if ($stmt->execute()) {
    $_SESSION['success_message'] = "Order updated successfully.";
  } else {
    $_SESSION['error_message'] = "Error updating order: " . $stmt->error;
  }
  header("Location: ../../../pages/admin/order-management.php");
  exit();
}
?>

