<?php
// register_process.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session to store user data
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');

// Require necessary files
require_once BASE_DIR . 'lib/db.php'; // Include database connection
require_once BASE_DIR . 'lib/auth/function.php'; // Adjust path as necessary
require_once BASE_DIR . 'lib/helpers.php'; // Include the helper for dynamic URL generation
 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fName = $_POST['f_name'];
    $lName = $_POST['l_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordConfirmation = $_POST['password_confirmation'];
    $countryCode = $_POST['country'];
    $phoneCode = $_POST['phone_code'];
    $phone = $_POST['phone'];
    $language = $_POST['language'] ?? '';

    // Validate password confirmation
    if ($password !== $passwordConfirmation) {
        $_SESSION['register_error'] = "Passwords do not match.";
    } else {
        // Check if email and phone exist in the database
        $emailCheck = registerUserCheckEmail($email); // Function to check email
        $phoneCheck = registerUserCheckPhone($phone); // Function to check phone 
        
        if ($emailCheck) {
            $_SESSION['register_error'] = "Email already exists. Please Sign In.";
        } elseif ($phoneCheck) {
            $_SESSION['register_error'] = "Phone number already exists. Please Sign In.";
        } else {
            // Generate a random 6-digit verification code
            $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); // Ensure it's always 6 digits

            // Proceed with registration if no errors
            $result = registerUser($fName, $lName, $email, $password, $countryCode, $phoneCode, $phone, $language, $verificationCode);
            if ($result) {
                // Success: Send verification email
                    $_SESSION['user_email'] = $email;
                    header("Location: " . getBaseUrl() . "auth/verify.php");
                    exit();
                
            } else {
                $_SESSION['register_error'] = "Registration failed. Please try again.";
            }
        }
    }

    // Redirect back to the registration form if there's an error
    header("Location: " . getBaseUrl() . "#pills-register");
    exit();
}



// Function to send verification email
function sendVerificationEmail($email, $verificationCode) {
    $to = $email;
    $subject = "Your Verification Code";
    
    // Set HTML content
    $txt = "
    <html>
    <head>
      <title>Your Verification Code</title>
    </head>
    <body>
      <p>Hello,</p>
      <p>Your verification code is: <strong>$verificationCode</strong></p>
      <p>Please enter this code to verify your account.</p>
    </body>
    </html>
    ";

    // Headers for HTML content
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Vetolut <verify@vetolutz.devdigitalz.com>" . "\r\n";

    // Send email
    return mail($to, $subject, $txt, $headers);
}



// Function to check if email exists
function registerUserCheckEmail($email) {
    $db = getDBConnection(); // Create a database connection
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetchColumn() > 0; // Return true if email exists
}

// Function to check if phone exists
function registerUserCheckPhone($phone) {
    $db = getDBConnection(); // Create a database connection
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE phone = :phone");
    $stmt->bindParam(':phone', $phone);
    $stmt->execute();
    return $stmt->fetchColumn() > 0; // Return true if phone exists
}
?>