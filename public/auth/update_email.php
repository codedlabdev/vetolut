<?php
// update_email.php

session_start();
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
require_once BASE_DIR . 'lib/db.php';  // Include your database connection file
require_once BASE_DIR . 'lib/helpers.php'; // Load the new helper file

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You are not logged in.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION['user_id']; // Get the user ID from session
    $newEmail = $_POST['email']; // Get the new email from the form

    // Validate email format
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['email_update_error'] = "Invalid email format.";
        header("Location: " . getBaseUrl() . "auth/verify.php"); // Redirect back to verify
        exit();
    }

    try {
        // Create a database connection
        $db = getDBConnection(); // Ensure you have this function to get your PDO connection

        // Check if the email already exists with verify = 1
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND verify = 1");
        $checkStmt->execute(['email' => $newEmail]);
        $emailExists = $checkStmt->fetchColumn();

        if ($emailExists > 0) {
            $_SESSION['email_update_error'] = "This email is already associated with another account that is verified.";
            header("Location: " . getBaseUrl() . "auth/verify.php"); // Redirect back to verify
            exit();
        }

        // Prepare the SQL statement to update the email
        $sql = "UPDATE users SET email = :email WHERE id = :id";
        $stmt = $db->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':email', $newEmail);
        $stmt->bindParam(':id', $userId);
        
        // Execute the statement
        $stmt->execute(); // Execute the statement

        // Update the session variable if the email was updated successfully
        $_SESSION['user_email'] = $newEmail;

        // Set a success message in the session
        $_SESSION['success_message'] = "Email updated successfully.";

        // Redirect back to the verification page
        header("Location: " . getBaseUrl() . "auth/verify.php");
        exit();

    } catch (PDOException $e) {
        // Check for duplicate entry error
        if ($e->getCode() == 23000) {
            $_SESSION['email_update_error'] = "This email is already associated with another account.";
        } else {
            $_SESSION['email_update_error'] = "Error: " . $e->getMessage();
        }
        header("Location: " . getBaseUrl() . "auth/verify.php"); // Redirect back to verify
        exit();
    }
}
