<?php
require_once '../../../config/database-config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'barista') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = $data['product_id'] ?? null;
    $hasStock = $data['has_stock'] ?? null; // Expecting true (in stock) or false (out of stock)

    if ($productId !== null && is_bool($hasStock)) {
        // If frontend says it HAS stock (true), we set the column to 1 (true).
        // If frontend says it has NO stock (false), we set the column to 0 (false).
        $productHasProductValue = $hasStock ? 1 : 0;

        $stmt = $conn->prepare("UPDATE product SET product_has_stock = ? WHERE product_id = ?");
        $stmt->bind_param("ii", $productHasProductValue, $productId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Stock status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update stock status.']);
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
$conn->close();
?>
