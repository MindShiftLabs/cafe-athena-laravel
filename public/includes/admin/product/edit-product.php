<?php
require_once '../../../config/database-config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['product_id'];
  $name = $_POST['name'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $status = $_POST['status'];
  $category = $_POST['category'];
  $existing_image = $_POST['existing_image'];

  $image_path = $existing_image;

  // Handle image upload
  if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $image_name = basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["image"]["tmp_name"]);

    if ($check !== false) {
      // Allow certain file formats
      if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" && $imageFileType != "webp"
      ) {
        $_SESSION['error_message'] = "Sorry, only JPG, JPEG, PNG, GIF & WEBP files are allowed.";
        header("Location: ../../../pages/admin/product-management.php");
        exit();
      }

      $category_folder = '';
      switch ($category) {
        case 'Hot Brew':
          $category_folder = 'hot-brew';
          break;
        case 'Iced & Cold':
          $category_folder = 'iced-&-cold';
          break;
        case 'Pastry':
          $category_folder = 'pastry';
          break;
        case 'Coffee Beans':
          $category_folder = 'coffee-beans';
          break;
        default:
          $category_folder = 'uncategorized';
          break;
      }

      $target_dir = "../../../assets/uploads/" . $category_folder . "/";
      if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
      }

      $sanitized_image_name = preg_replace("/[^a-zA-Z0-9-_\.]/", "", str_replace(" ", "-", strtolower($image_name)));
      $target_file = $target_dir . $sanitized_image_name;

      if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_path = "assets/uploads/" . $category_folder . "/" . $sanitized_image_name;
        if ($existing_image && $existing_image != $image_path) {
          $old_image_path = "../../../" . $existing_image;
          if (file_exists($old_image_path)) {
            unlink($old_image_path);
          }
        }
      } else {
        $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
        header("Location: ../../../pages/admin/product-management.php");
        exit();
      }
    } else {
      $_SESSION['error_message'] = "File is not an image.";
      header("Location: ../../../pages/admin/product-management.php");
      exit();
    }
  }

  $sql = "UPDATE product SET product_name = ?, product_description = ?, product_price = ?, product_image = ?, product_status = ?, product_category = ? WHERE product_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssdsssi", $name, $description, $price, $image_path, $status, $category, $id);

  if ($stmt->execute()) {
    $_SESSION['success_message'] = "Product updated successfully.";
    header("Location: ../../../pages/admin/product-management.php");
  } else {
    $_SESSION['error_message'] = "Error: " . $stmt->error;
    header("Location: ../../../pages/admin/product-management.php");
  }

  $stmt->close();
  $conn->close();
}
?>

