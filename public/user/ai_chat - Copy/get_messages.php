<?php
// Disable error display
ini_set('display_errors', 0);
error_reporting(0);

session_start();

// Define base directory and include required files
define('BASE_DIR', dirname(dirname(dirname(__DIR__))) . '/');
require_once BASE_DIR . 'lib/dhu.php';
require_once BASE_DIR . 'lib/user/ai_db.php';

// Set JSON header
header('Content-Type: application/json');

try {
   

    // Get chat_id from URL parameter
    $chatId = isset($_GET['chat_id']) ? $_GET['chat_id'] : null;

    if (!$chatId) {
        throw new Exception('No chat ID provided');
    }

    // Check database connection
    if (!isset($pdo)) {
        throw new Exception('Database connection not available');
    }

    // Fetch messages for this chat
    $messages = fetchChatById($chatId);
    error_log("SQL Query result for chat_id {$chatId}: " . print_r($messages, true));

    if ($messages && count($messages) > 0) {
        echo json_encode([
            'success' => true,
            'messages' => $messages,
            'debug' => [
                'chat_id' => $chatId,
                'message_count' => count($messages)
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No messages found for this chat',
            'debug' => [
                'chat_id' => $chatId
            ]
        ]);
    }

} catch (Exception $e) {
    error_log("Error in get_messages.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString()
        ]
    ]);
}
