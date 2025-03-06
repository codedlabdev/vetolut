<?php
session_start(); // Start the session

// Include your database connection
require 'db_connection.php'; // Make sure this points to your DB connection file

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Set last_active to a past time when logging out
    $updateStmt = $pdo->prepare("UPDATE users SET last_active = '1970-01-01 00:00:00' WHERE id = :id");
    $updateStmt->execute(['id' => $userId]);
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect the user to the login page or homepage
header("Location: login.php");
exit();
?>
