<?php
require_once '../../../config/database-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['user_id'];

  $sql = "DELETE FROM User WHERE user_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    header("Location: ../../../pages/admin/user-management.php");
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>

