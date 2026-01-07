<?php
require_once '../../../config/database-config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'barista') {
    $_SESSION['error_message'] = "You are not authorized to perform this action.";
    header("Location: ../../../pages/auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    $stmt = $conn->prepare("SELECT product_image FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['product_image'];
        if ($image_path != 'assets/images/cafe-atina-logo-nobg.png' && file_exists('../../' . $image_path)) {
            unlink('../../../' . $image_path);
        }
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Product deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting product: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
header("Location: ../../../pages/barista/product-management.php");
exit();
?>
