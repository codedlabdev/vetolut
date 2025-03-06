<?php
//toggle_like.php

session_start();
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
require_once BASE_DIR . 'lib/dhu.php'; // Include helper functions
require_once BASE_DIR . 'lib/user/table.php'; // Include helper functions
 

$userId = $_SESSION['user_id'];
$postId = $_POST['post_id'];


if (isLikedByUser($postId, $userId)) {
    removeLike($postId, $userId);
    $isLiked = false;
} else {
    addLike($postId, $userId);
    $isLiked = true;

    // Send a like notification only when the like is added
    $postOwnerId = getPostOwnerId($postId);  // Get post owner ID
    if ($postOwnerId && $postOwnerId != $userId) { // Ensure the post owner is different from the liker
       $message = "You have a new like on your post";
        addNotification($postOwnerId, $userId, 'like', $postId, $message); // Add notification
    }
}

// Return updated like status and count
echo json_encode([
    'success' => true,
    'isLiked' => $isLiked,
    'likeCount' => getLikeCount($postId)
]);

