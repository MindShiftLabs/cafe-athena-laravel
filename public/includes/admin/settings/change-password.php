<?php
require_once '../../../config/database-config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to change your password.";
    header("Location: ../../../pages/auth/login.php");
    exit();
  }

  $user_id = $_SESSION['user_id'];
  $current_password = $_POST['current_password'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];

  if ($new_password !== $confirm_password) {
    $_SESSION['error_message'] = "New password and confirm password do not match.";
    header("Location: ../../../pages/admin/admin-setting.php");
    exit();
  }

  $stmt = $conn->prepare("SELECT user_password FROM user WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();
  $stmt->close();

  if ($user && password_verify($current_password, $user['user_password'])) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
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

  header("Location: ../../../pages/admin/settings.php");
  exit();
}
?>

