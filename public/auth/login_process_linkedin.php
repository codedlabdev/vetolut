<?php
 //login_process_linkdin.php

session_start(); // Start the session
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');

// Include necessary files
require_once BASE_DIR . 'lib/db.php'; // Include database connection
require_once BASE_DIR . 'lib/url.php'; // Include URL helpers
require_once BASE_DIR . 'lib/auth/function.php'; // Adjust path as necessary
require_once BASE_DIR . 'lib/helpers.php'; // Include helpers for dynamic URL generation
require BASE_DIR . '/vendors/sso_auth/vendor/autoload.php';

use GuzzleHttp\Client;

// LinkedIn App Credentials
$linkedinClientId = '789ds4evuk3hcv';
$linkedinClientSecret = 'WPL_AP1.jTaswnP2LyLMjA9b.G0vvGA==';
$linkedinRedirectUri = BASE_URL . 'public/auth/login_process_linkedin.php';

// Check if LinkedIn returned an authorization code
if (!isset($_GET['code'])) {
    $_SESSION['login_error'] = 'Error: LinkedIn login failed.';
    header('Location: ' . BASE_URL);
    exit();
}

$authorizationCode = $_GET['code'];

try {
    // Exchange authorization code for access token
    $client = new Client();
    $response = $client->post('https://www.linkedin.com/oauth/v2/accessToken', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'code' => $authorizationCode,
            'redirect_uri' => $linkedinRedirectUri,
            'client_id' => $linkedinClientId,
            'client_secret' => $linkedinClientSecret,
        ],
    ]);

    $data = json_decode($response->getBody(), true);
    if (!isset($data['access_token'])) {
        throw new Exception('Unable to retrieve access token.');
    }

    $accessToken = $data['access_token'];

    // Fetch LinkedIn user profile (requires r_liteprofile scope)
    $response = $client->get('https://api.linkedin.com/v2/me', [
        'headers' => ['Authorization' => "Bearer $accessToken"],
    ]);
    $userData = json_decode($response->getBody(), true);

    // Fetch LinkedIn user email (requires r_emailaddress scope)
    $response = $client->get('https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))', [
        'headers' => ['Authorization' => "Bearer $accessToken"],
    ]);
    $emailData = json_decode($response->getBody(), true);

    // Extract user details
    $name = $userData['localizedFirstName'] . ' ' . $userData['localizedLastName'];
    $email = $emailData['elements'][0]['handle~']['emailAddress'] ?? null;

    echo '<h3>LinkedIn User Data</h3>';
    echo '<strong>Name:</strong> ' . htmlspecialchars($name) . '<br>';
    echo '<strong>Email:</strong> ' . htmlspecialchars($email ?? 'Not provided') . '<br>';
    echo '<strong>Raw Data:</strong><br><pre>' . htmlspecialchars(json_encode($userData, JSON_PRETTY_PRINT)) . '</pre>';
} catch (Exception $e) {
    error_log($e->getMessage());
    $_SESSION['login_error'] = 'Error: ' . $e->getMessage();
    header('Location: ' . BASE_URL);
    exit();
}