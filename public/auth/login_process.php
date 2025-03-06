<?php
//login_process


session_start(); // Start the session
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');

// Require necessary files
require_once BASE_DIR . 'lib/db.php'; // Include database connection
require_once BASE_DIR . 'lib/auth/function.php'; // Adjust path as necessary
require_once BASE_DIR . 'lib/helpers.php'; // Include the helper for dynamic URL generation

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $email = trim($_POST['login']);
    $password = trim($_POST['password']);

    // Validate credentials (using a custom authentication function)
    $user = authenticateUser($email, $password);

   if ($user) {
    // Set session variables with user details
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['f_name'] = $user['f_name'];  // Set f_name from the user array
            $_SESSION['l_name'] = $user['l_name'];  // Set l_name from the user array
            $_SESSION['user_email'] = $user['email'];  // Set user_email from the user array
            $_SESSION['is_logged_in'] = true;

		// Check if the user is verified
		if ($user['verify'] == 0) { // Use the 'verify' column
			// User is not verified, redirect to verification page
			$baseUrl = getBaseUrl(); // Get base URL
			header("Location: " . $baseUrl . "auth/verify.php");
			exit();
		} else {
			// User is verified, redirect to the dashboard
			$baseUrl = getBaseUrl(); // Get base URL
			header("Location: " . $baseUrl . "public/user/dashboard.php");
			exit();
		}
	} else {
		// Login failed: Store an error message in the session
		$_SESSION['login_error'] = "Invalid login credentials. Please try again.";

		// Redirect back to login page
		header("Location: " . getBaseUrl());
		exit();
	}


}
