<?php
require_once '../../../config/database-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['product_id'];

  // First, get the image path from the database
  $sql = "SELECT product_image FROM product WHERE product_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($image_path);
  $stmt->fetch();
  $stmt->close();

  // Now, delete the product from the database
  $sql = "DELETE FROM product WHERE product_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    // If the product was deleted successfully, delete the image file
    if ($image_path && file_exists("../../../" . $image_path)) {
      unlink("../../../" . $image_path);
    }
    $_SESSION['success_message'] = "Product deleted successfully.";
    header("Location: ../../../pages/admin/product-management.php");
  } else {
    $_SESSION['error_message'] = "Error: " . $stmt->error;
    header("Location: ../../../pages/admin/product-management.php");
  }

  $stmt->close();
  $conn->close();
}
?>

