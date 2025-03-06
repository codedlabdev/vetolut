<?php
//FetchMessages.php

require_once BASE_DIR . 'lib/dhu.php'; // Include database connection
require_once BASE_DIR . 'lib/user/table.php'; // Include helper functions
require_once BASE_DIR . 'lib/user/chat_func.php'; // Include helper functions
$userId = $_SESSION['user_id']; // Assume session contains the current user's ID

// Fetch messages involving the current user
$lastMessageId = isset($_GET['lastMessageId']) ? (int) $_GET['lastMessageId'] : 0;

$query = $db->prepare("
    SELECT * FROM chat 
    WHERE (sender_id = :userId OR receiver_id = :userId) 
    AND id > :lastMessageId 
    ORDER BY timestamp ASC
");
$query->execute(['userId' => $userId, 'lastMessageId' => $lastMessageId]);
$messages = $query->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($messages);




?>
