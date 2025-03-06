<?php

// submit_comment.php

session_start();
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
require_once BASE_DIR . 'lib/dhu.php'; // Include db & helper functions

// Get the user ID from the session
$userId = $_SESSION['user_id'];
$commentText = $_POST['comment'];
$postId = $_POST['post_id'] ?? 0;

if (empty($commentText) || $postId == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid comment or post ID']);
    exit;
}

// Insert the comment into the database
$sql = "INSERT INTO network_comment (post_id, user_id, comment) VALUES (:post_id, :user_id, :comment)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':post_id', $postId);
$stmt->bindParam(':user_id', $userId);
$stmt->bindParam(':comment', $commentText);
$stmt->execute();

// Get the inserted comment ID
$commentId = $pdo->lastInsertId();

// Fetch user details for the comment
$query = "SELECT f_name, l_name, image FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

 // Check if there is a profile image and convert to base64 if it exists
	$profileImage = !empty($post['profile_image']) 
		? 'data:image/jpeg;base64,' . base64_encode($post['profile_image']) 
		: BASE_URL . 'assets/user/img/noimage.png'; // Placeholder if no image

// Fetch the post owner
$postOwnerQuery = "SELECT user_id FROM network_post WHERE id = ?";
$postOwnerStmt = $pdo->prepare($postOwnerQuery);
$postOwnerStmt->execute([$postId]);
$postOwner = $postOwnerStmt->fetch(PDO::FETCH_ASSOC);

// Add a notification if the commenter is not the post owner
if ($postOwner && $postOwner['user_id'] != $userId) {
    $notificationSql = "INSERT INTO notifications (user_id, sender_id, post_id, comment_id, type, is_read, created_at) 
                        VALUES (:user_id, :sender_id, :post_id, :comment_id, :type, :is_read, NOW())";
    $notificationStmt = $pdo->prepare($notificationSql);
    $notificationStmt->execute([
        ':user_id' => $postOwner['user_id'],
        ':sender_id' => $userId,
        ':post_id' => $postId,
        ':comment_id' => $commentId,
        ':type' => 'comment',
        ':is_read' => 0
    ]);
}

// Return success response with comment details
echo json_encode([
    'success' => true,
    'comment_id' => $commentId,
    'full_name' => $user['f_name'] . ' ' . $user['l_name'],
    'profile_image' => $profileImage,
    'comment' => nl2br(htmlspecialchars($commentText))
]);
?>
