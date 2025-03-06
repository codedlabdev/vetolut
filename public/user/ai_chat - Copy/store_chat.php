<?php
include '../header.php'; // Ensure session and DB connection are set up

// store_chat.php

// Include necessary files
require_once BASE_DIR . 'lib/user/ai_db.php';


// Check if necessary data is provided
if (isset($_POST['chat_id'], $_POST['user_id'], $_POST['prompt'], $_POST['response'])) {
    $chatId = $_POST['chat_id'];
    $userId = $_POST['user_id'];
    $prompt = $_POST['prompt'];
    $response = $_POST['response'];

    // Handle file upload if any
    $filePath = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['file']['name']);
        $filePath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['file']['tmp_name'], $filePath); // Move the uploaded file to the server
    }

    // Call the function to save the message to the database
    $success = saveChatMessage($chatId, $userId, $prompt, $response, $filePath);

    // Respond with success or failure
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to save message']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
}
?>
