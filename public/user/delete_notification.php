<?php
// Include necessary files for database connection and session handling
session_start();
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
require_once BASE_DIR . 'lib/dhu.php'; // Include helper functions
require_once BASE_DIR . 'lib/user/table.php'; // Include helper functions

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$loggedInUserId = $_SESSION['user_id']; // Get the logged-in user's ID

// Get the notification ID from the request
$data = json_decode(file_get_contents('php://input'), true);
$notificationId = $data['notificationId'] ?? null;

if (!$notificationId) {
    echo json_encode(['success' => false, 'error' => 'Notification ID is required']);
    exit;
}

// Function to delete the notification from the database
function deleteNotificationById($notificationId, $userId) {
    $pdo = getDBConnection();
    // Check if the notification exists and belongs to the logged-in user
    $sql = "SELECT * FROM notifications WHERE id = :notification_id AND recipient_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['notification_id' => $notificationId, 'user_id' => $userId]);
    
    if ($stmt->rowCount() == 0) {
        // Notification does not exist or doesn't belong to the user
        return false;
    }
    
    // Delete the notification
    $deleteSql = "DELETE FROM notifications WHERE id = :notification_id";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->execute(['notification_id' => $notificationId]);
    
    return $deleteStmt->rowCount() > 0; // Return true if deletion was successful
}

// Attempt to delete the notification
if (deleteNotificationById($notificationId, $loggedInUserId)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to delete notification or notification not found']);
}
?>
