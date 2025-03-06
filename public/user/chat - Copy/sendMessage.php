<?php
// sendMessage.php
include '../header.php';
require_once BASE_DIR . 'lib/user/chat_func.php'; // Include helper functions

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the sender ID from the session
    $senderId = $_SESSION['user_id'];
    $receiverId = $_POST['receiver_id'] ?? null;
    $messageContent = $_POST['message'] ?? '';
    $file = $_FILES['file'] ?? null;

    if (!$receiverId || !$messageContent) {
        echo json_encode(['status' => 'error', 'error' => 'Receiver ID and message content are required.']);
        exit;
    }

    // Process file upload if provided
    $filePath = null;
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        // Handle the file upload (e.g., store in a directory)
        $uploadDir = BASE_DIR . 'uploads/';
        $filePath = $uploadDir . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $filePath);
    }

    // Insert message into the database
    $response = sendMessage($senderId, $receiverId, $messageContent, $filePath);

    if ($response['status'] === 'success') {
        // Get sender and receiver details
        $sender = getUserById($senderId); // Assume this function fetches user details by ID
        $receiver = getUserById($receiverId);

        // Return the message with additional user details
        echo json_encode([
            'status' => 'success',
            'message' => [
                'message_text' => htmlspecialchars($messageContent),
                'sender_name' => $sender['full_name'],
                'sender_image' => !empty($sender['image']) ? BASE_URL . $sender['image'] : BASE_URL . 'assets/user/img/noimage.png',
                'receiver_name' => $receiver['full_name'],
                'timestamp' => date('Y-m-d H:i:s'),
                'sender_image_base64' => $response['sender_image'],  // base64 encoded sender image
                'receiver_image_base64' => $response['receiver_image'], // base64 encoded receiver image
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'error' => 'Failed to send the message.']);
    }
} else {
    echo json_encode(['status' => 'error', 'error' => 'Invalid request method.']);
}
?>
