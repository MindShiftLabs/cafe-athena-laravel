<?php
require_once '../../../config/database-config.php';

// The session is already started in database-config.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
  // Redirect to login if not a post request, not logged in, or not a customer
  header('Location: ../../../pages/auth/login.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$firstname = $_POST['user_firstname'];
$lastname = $_POST['user_lastname'];
$email = $_POST['user_email'];
$birthday = $_POST['user_birthday'];
$phone = $_POST['user_phone'];
$address = $_POST['user_address'];

// Optional: Add validation for the inputs

$stmt = $conn->prepare("UPDATE user SET user_firstname = ?, user_lastname = ?, user_email = ?, user_birthday = ?, user_phone = ?, user_address = ? WHERE user_id = ?");
$stmt->bind_param("ssssssi", $firstname, $lastname, $email, $birthday, $phone, $address, $user_id);

if ($stmt->execute()) {
  $_SESSION['success_message'] = "Profile updated successfully!";

  // Update session variables to reflect changes immediately
  $_SESSION['user_firstname'] = $firstname;
  $_SESSION['user_lastname'] = $lastname;
  $_SESSION['user_phone'] = $phone;
  $_SESSION['user_address'] = $address;

} else {
  $_SESSION['error_message'] = "Error updating profile: " . $stmt->error;
}

$stmt->close();
$conn->close();

header('Location: ../../../pages/customer/customer-setting.php');
exit();
?>
