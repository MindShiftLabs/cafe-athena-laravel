<?php
require_once '../../config/database-config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $email = $_POST['email'];
  $password = $_POST['password'];

  // Find the user
  $sql = "SELECT user_id, user_firstname, user_lastname, user_password, user_role, user_phone, user_address FROM user WHERE user_email = ? LIMIT 1";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify Password
    if (password_verify($password, $user['user_password'])) {
      // Password is correct!

      // Create Session
      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['user_firstname'] = $user['user_firstname'];
      $_SESSION['user_lastname'] = $user['user_lastname'];
      $_SESSION['user_role'] = $user['user_role'];
      $_SESSION['user_phone'] = $user['user_phone'];
      $_SESSION['user_address'] = $user['user_address'];
      $_SESSION['success_message'] = "Logged in successfully!";

      $role = strtolower(trim($user['user_role']));
      // We redirect based on the role stored in the database.
      switch ($role) {
        case 'admin':
          header("Location: ../../pages/admin/admin-dashboard.php");
          break;
        case 'barista':
          header("Location: ../../pages/barista/barista-dashboard.php");
          break;
        case 'customer':
          header("Location: ../../pages/customer/customer-dashboard-new.php");
          break;
        default:
          // A fallback in case the role is not set
          header("Location: ../../pages/auth/login.php");
          break;
      }
      exit(); // Stop the script after redirecting

    } else {
      // Invalid password
      $_SESSION['error_message'] = "Invalid email or password.";
      header("Location: ../../pages/auth/login.php");
      exit();
    }
  } else {
    // No user found
    $_SESSION['error_message'] = "Invalid email or password.";
    header("Location: ../../pages/auth/login.php");
    exit();
  }
}
$conn->close();
?>

