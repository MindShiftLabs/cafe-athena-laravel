<?php
// Include the database configuration
require_once '../../config/database-config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Get form data and sanitize it
  $firstname = htmlspecialchars(trim($_POST['firstname']));
  $lastname = htmlspecialchars(trim($_POST['lastname']));
  $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirm_password'];

  // Validate form data
  if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirmPassword)) {
    $_SESSION['error_message'] = "All fields are required.";
    header("Location: ../../pages/auth/register.php");
    exit();
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = "Invalid email format.";
    header("Location: ../../pages/auth/register.php");
    exit();
  }

  if ($password !== $confirmPassword) {
    $_SESSION['error_message'] = "Passwords do not match.";
    header("Location: ../../pages/auth/register.php");
    exit();
  }

  if (strlen($password) < 6) {
    $_SESSION['error_message'] = "Password must be at least 6 characters long.";
    header("Location: ../../pages/auth/register.php");
    exit();
  }

  // Hash the password for security
  $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

  if (strlen($hashedPassword) !== 60) {
    $_SESSION['error_message'] = "Password hashing failed. Please contact support.";
    header("Location: ../../pages/auth/register.php");
    exit();
  }

  // Check if the email already exists
  $sql = "SELECT user_id FROM user WHERE user_email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $_SESSION['error_message'] = "Email already exists. Please use a different email.";
    header("Location: ../../pages/auth/register.php");
    exit();
  }

  $stmt->close();

  // Insert the new user into the database
  $sql = "INSERT INTO user (user_firstname, user_lastname, user_email, user_password, user_role) VALUES (?, ?, ?, ?, 'customer')";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssss", $firstname, $lastname, $email, $hashedPassword);

  if ($stmt->execute()) {
    // Registration successful
    $_SESSION['success_message'] = "Registration successful! You can now log in.";
    header("Location: ../../pages/auth/login.php");
    exit();
  } else {
    // Registration failed
    $_SESSION['error_message'] = "Registration failed. Please try again.";
    header("Location: ../../pages/auth/register.php");
    exit();
  }
} else {
  // If the form is not submitted, redirect to the registration page
  header("Location: ../../pages/auth/register.php");
  exit();
}
?>
