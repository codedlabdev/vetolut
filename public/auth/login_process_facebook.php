<?php
session_start(); // Start the session
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');

// Include necessary files
require_once BASE_DIR . 'lib/db.php'; // Include database connection
require_once BASE_DIR . 'lib/url.php'; // Include URL helpers
require_once BASE_DIR . 'lib/auth/function.php'; // Adjust path as necessary
require_once BASE_DIR . 'lib/helpers.php'; // Include helpers for dynamic URL generation
require BASE_DIR . '/vendors/sso_auth/vendor/autoload.php';

use Facebook\Facebook;

$fb = new Facebook([
    'app_id' => '589299403470885', // Replace with your Facebook App ID
    'app_secret' => '367379335a5a0fd35a94171afa835f0e', // Replace with your Facebook App Secret
    'default_graph_version' => 'v2.4',
]);

$helper = $fb->getRedirectLoginHelper();

try {
    // Get access token
    $accessToken = $helper->getAccessToken();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Error from Graph API: ' . htmlspecialchars($e->getMessage());
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Error from Facebook SDK: ' . htmlspecialchars($e->getMessage());
    exit;
}

if (!isset($accessToken)) {
    echo 'Access token not received. Please retry.';
    exit;
}

try {
    // Fetch user data
    $response = $fb->get('/me?fields=name,email', $accessToken);
    $user = $response->getGraphUser();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Error from Graph API: ' . htmlspecialchars($e->getMessage());
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Error from Facebook SDK: ' . htmlspecialchars($e->getMessage());
    exit;
}

// Extract user data
$name = $user['name'];
$email = $user['email'] ?? null; // Email might not be available

if (!$email) {
    exit("Error: Email is required but not provided.");
}

// Database connection
$db = getDBConnection();

// Check if the email exists in the database
$stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userData) {
    // User exists: Set session and redirect to the dashboard
    $_SESSION['user_id'] = $userData['id'];
    $_SESSION['is_logged_in'] = true;
} else {
    // User does not exist: Register the user
    $stmt = $db->prepare("
        INSERT INTO users (f_name, email, last_active, verify, password)
        VALUES (:f_name, :email, :last_active, :verify, :password)
    ");
    $stmt->bindParam(':f_name', $name);
    $stmt->bindParam(':email', $email);

    $lastActive = date('Y-m-d H:i:s'); // Current timestamp
    $stmt->bindValue(':last_active', $lastActive);
    $stmt->bindValue(':verify', 1); // Verify user
    $stmt->bindValue(':password', password_hash('facebook_user', PASSWORD_DEFAULT)); // Placeholder password

    if ($stmt->execute()) {
        // Set session for the new user
        $newUserId = $db->lastInsertId();
        $_SESSION['user_id'] = $newUserId;
        $_SESSION['is_logged_in'] = true;
    } else {
        exit("Error: Unable to register user.");
    }
}

// Redirect to the dashboard after either login or registration
header("Location: " . BASE_URL . "public/user/dashboard.php");
exit();