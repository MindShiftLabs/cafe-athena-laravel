<?php
require_once '../../config/database-config.php';

function getBaristaProducts($conn, $search = '', $filter = 'all')
{
    $query = "SELECT product_id, product_name, product_description, product_price, product_image, product_status, product_has_stock, product_category, product_createdat FROM product";
    $where = [];
    if ($search !== '') {
        $searchTerm = $conn->real_escape_string($search);
        $where[] = "product_name LIKE '%$searchTerm%'";
    }
    if ($filter === 'available') {
        $where[] = "product_status = 'available'";
    } elseif ($filter === 'unavailable') {
        $where[] = "product_status = 'unavailable'";
    }
    if (!empty($where)) {
        $query .= " WHERE " . implode(' AND ', $where);
    }
    $query .= " ORDER BY product_id DESC";

    $result = $conn->query($query);

    $products = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}
?>