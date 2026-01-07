<?php
require_once '../../../config/database-config.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'barista') {
  $_SESSION['error_message'] = "You are not authorized to perform this action.";
  header("Location: ../../../pages/auth/login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $orderId = $_POST['order_id'] ?? null;
  $newStatus = $_POST['order_status'] ?? null;
  $paymentStatus = $_POST['payment_status'] ?? null;
  $amountPaid = $_POST['amount_paid'] ?? null;

  if ($orderId && $newStatus) {
    // If payment status is being updated to 'paid'
    if ($paymentStatus === 'paid' && !empty($amountPaid)) {
      // You might want to add validation here to ensure amount paid is sufficient
      $stmt = $conn->prepare("UPDATE orders SET order_status = ?, order_payment_status = ? WHERE order_id = ?");
      $stmt->bind_param("ssi", $newStatus, $paymentStatus, $orderId);

      if ($stmt->execute()) {
        $_SESSION['success_message'] = "Order and payment status updated successfully.";
      } else {
        $_SESSION['error_message'] = "Failed to update order and payment status.";
      }
      $stmt->close();

    } else { // Only update order status
      $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
      $stmt->bind_param("si", $newStatus, $orderId);

      if ($stmt->execute()) {
        $_SESSION['success_message'] = "Order status updated successfully.";
      } else {
        $_SESSION['error_message'] = "Failed to update order status.";
      }
      $stmt->close();
    }
  } else {
    $_SESSION['error_message'] = "Invalid input.";
  }
} else {
  $_SESSION['error_message'] = "Invalid request method.";
}

header("Location: ../../../pages/barista/order-management.php");
exit();
?>
