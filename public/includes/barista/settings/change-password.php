<?php
require_once '../../../config/database-config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'barista') {
    header('Location: ../../../pages/barista/setting-management.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if ($new_password !== $confirm_password) {
    $_SESSION['error_message'] = "New passwords do not match.";
    header('Location: ../../../pages/barista/setting-management.php');
    exit();
}

$stmt = $conn->prepare("SELECT user_password FROM user WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!password_verify($current_password, $user['user_password'])) {
    $_SESSION['error_message'] = "Incorrect current password.";
    header('Location: ../../../pages/barista/setting-management.php');
    exit();
}

$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE user SET user_password = ? WHERE user_id = ?");
$stmt->bind_param("si", $hashed_password, $user_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Password changed successfully!";
} else {
    $_SESSION['error_message'] = "Error changing password.";
}

$stmt->close();
header('Location: ../../../pages/barista/setting-management.php');
exit();
