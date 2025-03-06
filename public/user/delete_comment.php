<?php
session_start();
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
require_once BASE_DIR . 'lib/dhu.php'; // Include db & helper functions

// Get user ID from session and comment ID from POST
$userId = $_SESSION['user_id'];
$commentId = intval($_POST['comment_id'] ?? 0);

// Check if comment ID is valid
if ($commentId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid comment ID']);
    exit;
}

// Query to check if the logged-in user owns the comment
$query = "SELECT user_id FROM network_comment WHERE id = :comment_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
$stmt->execute();
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($comment && $comment['user_id'] === $userId) {
    // If the user owns the comment, delete it
    $deleteQuery = "DELETE FROM network_comment WHERE id = :comment_id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $deleteStmt->execute();

    echo json_encode(['success' => true, 'message' => 'Comment deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Unauthorized action']);
}
?>
