<?php
// forget_process.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');

// Require necessary files
require_once BASE_DIR . 'lib/db.php';
require_once BASE_DIR . 'lib/auth/function.php';
require_once BASE_DIR . 'lib/helpers.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate email input
    $email = filter_var(trim($_POST['f_pass']), FILTER_VALIDATE_EMAIL);
    
    if (!$email) {
        $_SESSION['p_error'] = "Invalid email address.";
        header("Location: " . getBaseUrl() . "auth/user_forget_pass.php");
        exit();
    }

    // Check if email exists in the database
    if (!registerUserCheckEmail($email)) {
        $_SESSION['p_error'] = "Email does not exist. Please confirm email.";
        header("Location: " . getBaseUrl() . "auth/user_forget_pass.php");
        exit();
    }

    // Generate a random 6-digit verification code
    $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Update the verification code in the database
    if (updateUserVerificationCode($email, $verificationCode)) {
        // Store email in session for the next step
        $_SESSION['user_email'] = $email;

        // Redirect to verification page, where the email will be sent
        header("Location: " . getBaseUrl() . "auth/forget_verify.php");
        exit();
    } else {
        $_SESSION['p_error'] = "Failed to update verification code. Please try again.";
    }

    // Redirect back if there's an error
    header("Location: " . getBaseUrl() . "auth/user_forget_pass.php");
    exit();
}

// Function to check if email exists
function registerUserCheckEmail($email) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch() !== false;
}

// Function to update verification code in the database
function updateUserVerificationCode($email, $code) {
    $db = getDBConnection();
    $stmt = $db->prepare("UPDATE users SET email_vecode = ? WHERE email = ?");
    return $stmt->execute([$code, $email]);
}
?>
