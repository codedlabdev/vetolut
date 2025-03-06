<?php
// login_process_sso.php

session_start(); // Start the session
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');

// Require necessary files
require_once BASE_DIR . 'lib/db.php'; // Include database connection
require_once BASE_DIR . 'lib/url.php'; // Include database connection
require_once BASE_DIR . 'lib/auth/function.php'; // Adjust path as necessary
require_once BASE_DIR . 'lib/helpers.php'; // Include the helper for dynamic URL generation
require BASE_DIR . '/vendors/sso_auth/vendor/autoload.php';

$client = new Google\Client;
$client->setHttpClient(new GuzzleHttp\Client(['verify' => false])); // Disable SSL verification

$client->setClientId("1027379866529-bp21notc4ua98lu391pkn17e1jd0mlsg.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri(BASE_URL . "public/auth/login_process_gmail.php");

if (!isset($_GET["code"])) {
    exit("Login failed");
}

$token = $client->fetchAccessTokenWithAuthCode($_GET["code"]);
$client->setAccessToken($token["access_token"]);

$oauth = new Google\Service\Oauth2($client);
$userinfo = $oauth->userinfo->get();

// Extract user data from Google
$email = $userinfo->email;
$firstName = $userinfo->givenName;
$lastName = $userinfo->familyName;

// Database connection
$db = getDBConnection();

// Check if the email exists in the database
$stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // User exists: Set session and redirect to the dashboard
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['is_logged_in'] = true; // Add this line
} else {

     // User does not exist: Insert the user into the database
$stmt = $db->prepare("
    INSERT INTO users (f_name, l_name, email, password, last_active, verify)
    VALUES (:f_name, :l_name, :email, :password, :last_active, :verify)
");
$stmt->bindParam(':f_name', $firstName);
$stmt->bindParam(':l_name', $lastName);
$stmt->bindParam(':email', $email);

$lastActive = date('Y-m-d H:i:s');  // Assign current timestamp to a variable
$stmt->bindValue(':last_active', $lastActive);  // Use bindValue instead of bindParam

$stmt->bindValue(':verify', 1);  // Use bindValue instead of bindParam
$stmt->bindValue(':password', password_hash('google_user', PASSWORD_DEFAULT)); // Placeholder password

if ($stmt->execute()) {
    // Get the last inserted user ID
    $newUserId = $db->lastInsertId();
    $_SESSION['user_id'] = $newUserId;
    $_SESSION['is_logged_in'] = true; // Add this line
} else {
    exit("Error: Unable to create user");
}



}

// Redirect to the dashboard after either login or registration
header("Location: " . BASE_URL . "public/user/dashboard.php");
exit();

