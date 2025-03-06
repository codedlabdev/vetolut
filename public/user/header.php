<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
require_once BASE_DIR . 'lib/dhu.php'; // Include database connection
require_once BASE_DIR . 'lib/user/table.php'; // Include helper functions
require_once BASE_DIR . 'lib/user/chat_func.php'; // Include helper functions




// Check if user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: " . BASE_URL);
    exit();
}


// Retrieve the user's name from the session
//$userName = htmlspecialchars($_SESSION['user_name']); // Escape for safe output

if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    // Get the logged-in user ID from session
    $userId = $_SESSION['user_id'];
    // Set last_active to a past time when logging out
    $updateStmt = $pdo->prepare("UPDATE users SET last_active = '1970-01-01 00:00:00' WHERE id = :id");
    $updateStmt->execute(['id' => $userId]);

    // Destroy the session to log the user out
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session

    // Redirect to the login page (or wherever you want to redirect after logout)
    header("Location: " . BASE_URL); // Use BASE_URL now that it's defined
    exit(); // Exit to prevent further execution
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dactorapp - Doctor Appointment Booking Mobile Template - Home</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/user/vender/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/user/vender/sidebar/demo.css">
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>assets/user/vender/materialdesign/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/user/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/user/css/floating.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body class="bg-light">
    <!-- Add SweetAlert2 JS before the closing body tag -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>