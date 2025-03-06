<?php
session_start();
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
require_once BASE_DIR . 'lib/dhu.php';
require_once BASE_DIR . 'lib/user/table.php'; // Include the function from table.php

header('Content-Type: application/json');

// Get data from POST request
$data = json_decode(file_get_contents("php://input"), true);
$followingId = $data['followingId'];
$userId = $_SESSION['user_id']; // Logged-in user ID

// Verify valid IDs
if (!$userId || !$followingId) {
    echo json_encode(['success' => false, 'error' => 'Invalid user IDs']);
    exit;
}

$pdo = getDBConnection();

// Check if the user is already following the target user
if (isFollowing($userId, $followingId)) {
    // Unfollow if already following
    unfollowUser($userId, $followingId);
    $isFollowing = false; // Status after action
} else {
    // Follow if not already following
    followUser($userId, $followingId);
    $isFollowing = true; // Status after action

    // Add notification for follow action
    $message = "started following you.";
    addNotification($followingId, $userId, 'follow', $userId, $message);
}

// Respond with updated follow status
echo json_encode([
    'success' => true,
    'isFollowing' => $isFollowing
]);
