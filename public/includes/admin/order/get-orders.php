<?php
require_once '../../config/database-config.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'order_id DESC';

// Base query to select orders and join with the user table to get customer names
$sql = "SELECT o.*, u.user_firstname, u.user_lastname
        FROM orders o
        JOIN user u ON o.user_id = u.user_id";

$whereClauses = [];
$params = [];
$types = '';

if (in_array($sort, ['pending', 'completed', 'cancelled', 'ready'])) {
    $whereClauses[] = "o.order_status = ?";
    $params[] = $sort;
    $types .= 's';
    $orderBy = 'o.order_id DESC';
} else {
    // Basic validation for sort to prevent SQL injection
    $allowed_sorts = ['order_id DESC', 'order_id ASC', 'order_total DESC', 'order_total ASC'];
    if (in_array($sort, $allowed_sorts)) {
        $orderBy = $sort;
    } else {
        $orderBy = 'o.order_id DESC';
    }
}

if (!empty($search)) {
  $whereClauses[] = "(o.order_id LIKE ? OR CONCAT(u.user_firstname, ' ', u.user_lastname) LIKE ? OR o.order_status LIKE ?)";
  $searchTerm = "%" . $search . "%";
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $types .= 'sss';
}

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(' AND ', $whereClauses);
}

$sql .= " ORDER BY " . $orderBy;

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$allOrders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>