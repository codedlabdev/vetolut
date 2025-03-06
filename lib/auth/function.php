<?php


// vetoluz/lib/auth/function.php

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
}

require_once BASE_DIR . 'lib/db.php';
require_once BASE_DIR . 'lib/helpers.php'; // Load the new helper file


function registerUser($f_name, $l_name, $email, $password, $countryCode, $phoneCode, $phone, $language) {
    // Create a database connection
    $db = getDBConnection();
    
    if ($db === null) {
        return false; // Connection failed
    }
	
	
	// Check if the email already exists
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $emailExists = $stmt->fetchColumn();

    // Check if the phone already exists
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE phone = :phone");
    $stmt->bindParam(':phone', $phone);
    $stmt->execute();
    
    $phoneExists = $stmt->fetchColumn();

    // Return appropriate messages if email or phone already exists
    if ($emailExists > 0) {
        return "Email already exists"; // Email already registered
    }
    
    if ($phoneExists > 0) {
        return "Phone already exists"; // Phone number already registered
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate a random 6-digit verification code
    $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); // Ensures it's always 6 digits

    // Prepare the SQL statement
    $sql = "INSERT INTO users (f_name, l_name, email, password, country, phone_code, phone, language, user_type, email_vecode, verify) 
            VALUES (:f_name, :l_name, :email, :password, :country, :phone_code, :phone, :language, 'member', :email_vecode, 0)";
    
    try {
        $stmt = $db->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':f_name', $f_name);
        $stmt->bindParam(':l_name', $l_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':country', $countryCode);
        $stmt->bindParam(':phone_code', $phoneCode);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':language', $language);
        $stmt->bindParam(':email_vecode', $verificationCode); // Bind the verification code
        
        // Execute the statement
        $success = $stmt->execute(); // Returns true on success

        // If registration is successful, start session and store user details
        if ($success) {
            // Start the session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Get the last inserted ID
            $userId = $db->lastInsertId();

            // Store user details in the session
            $_SESSION['user_id'] = $userId; // Store the user ID
            $_SESSION['f_name'] = $f_name; // Store the full name
            $_SESSION['l_name'] = $l_name; // Store the full name
            $_SESSION['user_email'] = $email;   // Store the email

            // Get dynamic base URL and redirect
            $baseUrl = getBaseUrl(); // Fetch the dynamically generated base URL

            // Redirect to the verification page after successful registration
            header("Location: " . $baseUrl . "auth/verify.php");
            exit(); // Ensure no further code is executed after redirect
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false; // Registration failed
    }
}


// This function checks user credentials
function authenticateUser($email, $password) {
    $db = getDBConnection(); // Assuming this function returns your database connection

    // Prepare the SQL statement to find the user by email, including verify status
    $sql = "SELECT id, f_name, email, verify, password FROM users WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    // Fetch the user from the database
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the user exists and the password matches, return user data, else return false
    if ($user && password_verify($password, $user['password'])) {
        return $user; // Return user data including verify status
    }

    return false; // Authentication failed
}

