<?php
require_once '../../../config/database-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['user_id'];
  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];
  $email = $_POST['email'];
  $role = $_POST['role'];

  // Check if email already exists for another user
  $sql = "SELECT user_id FROM User WHERE user_email = ? AND user_id != ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("si", $email, $id);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $_SESSION['error_message'] = "Email already exists for another user.";
    header("Location: ../../../pages/admin/user-management.php");
    exit();
  } else {
    $sql = "UPDATE User SET user_firstname = ?, user_lastname = ?, user_email = ?, user_role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $firstname, $lastname, $email, $role, $id);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = "User updated successfully.";
      header("Location: ../../../pages/admin/user-management.php");
    } else {
      $_SESSION['error_message'] = "Error: " . $stmt->error;
      header("Location: ../../../pages/admin/user-management.php");
    }
  }

  $stmt->close();
  $conn->close();
}
?>

