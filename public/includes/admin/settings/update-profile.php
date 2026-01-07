<?php
require_once '../../../config/database-config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to update your profile.";
    header("Location: ../../../pages/auth/login.php");
    exit();
  }

  $user_id = $_SESSION['user_id'];
  $user_birthday = $_POST['user_birthday'];
  $user_phone = $_POST['user_phone'];
  $user_address = $_POST['user_address'];

  $sql = "UPDATE user SET user_birthday = ?, user_phone = ?, user_address = ? WHERE user_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssi", $user_birthday, $user_phone, $user_address, $user_id);

  if ($stmt->execute()) {
    $_SESSION['success_message'] = "Profile updated successfully.";
  } else {
    $_SESSION['error_message'] = "Error updating profile: " . $stmt->error;
  }
  header("Location: ../../../pages/admin/admin-setting.php");
  exit();
}
?>

