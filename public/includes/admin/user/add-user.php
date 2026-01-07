<?php
require_once '../../../config/database-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = $_POST['role'];

  // Check if email already exists
  $sql = "SELECT user_id FROM User WHERE user_email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $_SESSION['error_message'] = "Email already exists.";
    header("Location: ../../../pages/admin/user-management.php");
    exit();
  } else {
    $sql = "INSERT INTO User (user_firstname, user_lastname, user_email, user_password, user_role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $firstname, $lastname, $email, $password, $role);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = "User added successfully.";
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

