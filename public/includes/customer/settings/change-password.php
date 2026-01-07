<?php
require_once '../../../config/database-config.php';

// The session is already started in database-config.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
  // Redirect to login if not a post request, not logged in, or not a customer
  header('Location: ../../../pages/auth/login.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if ($new_password !== $confirm_password) {
  $_SESSION['error_message'] = "New password and confirm password do not match.";
  header('Location: ../../../pages/customer/customer-setting.php');
  exit();
}

// Fetch the current password from the DB
$stmt = $conn->prepare("SELECT user_password FROM user WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user && password_verify($current_password, $user['user_password'])) {
  // Current password is correct, hash the new password
  $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

  // Update the password in the DB
  $update_stmt = $conn->prepare("UPDATE user SET user_password = ? WHERE user_id = ?");
  $update_stmt->bind_param("si", $hashed_password, $user_id);

  if ($update_stmt->execute()) {
    $_SESSION['success_message'] = "Password changed successfully.";
  } else {
    $_SESSION['error_message'] = "Error changing password: " . $update_stmt->error;
  }
  $update_stmt->close();
} else {
  $_SESSION['error_message'] = "Incorrect current password.";
}

$conn->close();

header('Location: ../../../pages/customer/customer-setting.php');
exit();
?>
