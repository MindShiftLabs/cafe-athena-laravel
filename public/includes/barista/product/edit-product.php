<?php
require_once '../../../config/database-config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'barista') {
  $_SESSION['error_message'] = "You are not authorized to perform this action.";
  header("Location: ../../pages/auth/login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $product_id = $_POST['product_id'];
  $name = $_POST['name'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $status = $_POST['status'];
  $category = $_POST['category'];
  $existing_image = $_POST['existing_image'];
  $image_path = $existing_image;

  if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $image_name = basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["image"]["tmp_name"]);

    if ($check !== false) {
      if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif', 'webp'])) {
        $_SESSION['error_message'] = "Sorry, only JPG, JPEG, PNG, GIF & WEBP files are allowed.";
      } else {
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
          if ($existing_image && $existing_image != $image_path && file_exists('../../../' . $existing_image)) {
            unlink('../../../' . $existing_image);
          }
        } else {
          $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
        }
      }
    } else {
      $_SESSION['error_message'] = "File is not an image.";
    }
  }

  if (!isset($_SESSION['error_message'])) {
    $stmt = $conn->prepare("UPDATE product SET product_name = ?, product_description = ?, product_price = ?, product_image = ?, product_status = ?, product_category = ? WHERE product_id = ?");
    $stmt->bind_param("ssdsssi", $name, $description, $price, $image_path, $status, $category, $product_id);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = "Product updated successfully!";
    } else {
      $_SESSION['error_message'] = "Error updating product: " . $conn->error;
    }
    $stmt->close();
  }
}

$conn->close();
header("Location: ../../../pages/barista/product-management.php");
exit();
?>
