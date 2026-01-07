<?php
require_once '../../config/database-config.php';

function getAllUsers($conn, $searchTerm = null)
{
  $sql = "SELECT user_id, user_firstname, user_lastname, user_email, user_role, user_createdat FROM User";

  if ($searchTerm) {
    $sql .= " WHERE user_firstname LIKE ? OR user_lastname LIKE ? OR user_email LIKE ?";
  }

  $sql .= " ORDER BY user_createdat DESC";

  $stmt = $conn->prepare($sql);

  if ($searchTerm) {
    $likeTerm = "%$searchTerm%";
    $stmt->bind_param("sss", $likeTerm, $likeTerm, $likeTerm);
  }

  $stmt->execute();
  $result = $stmt->get_result();
  $users = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  return $users;
}

$searchTerm = isset($_GET['search']) ? $_GET['search'] : null;
$allUsers = getAllUsers($conn, $searchTerm);
?>

