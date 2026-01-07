<?php
require_once '../../../config/database-config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'barista') {
  header('Location: ../../../pages/barista/setting-management.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$firstname = $_POST['user_firstname'];
$lastname = $_POST['user_lastname'];
$email = $_POST['user_email'];
$birthday = $_POST['user_birthday'];
$phone = $_POST['user_phone'];
$address = $_POST['user_address'];

$stmt = $conn->prepare("UPDATE user SET user_firstname = ?, user_lastname = ?, user_email = ?, user_birthday = ?, user_phone = ?, user_address = ? WHERE user_id = ?");
$stmt->bind_param("ssssssi", $firstname, $lastname, $email, $birthday, $phone, $address, $user_id);

if ($stmt->execute()) {
  $_SESSION['success_message'] = "Profile updated successfully!";
  $_SESSION['user_firstname'] = $firstname;
} else {
  $_SESSION['error_message'] = "Error updating profile.";
}

$stmt->close();
header('Location: ../../../pages/barista/barista-setting.php');
exit();
?>
