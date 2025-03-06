<?php
// FetchMessages.php

require_once BASE_DIR . 'lib/dhu.php';
require_once BASE_DIR . 'lib/user/table.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loggedInUserId = $_POST['logged_in_user_id'];
    $partnerUserId = $_POST['partner_user_id'];
    $lastMessageId = $_POST['last_message_id'] ?? 0;

    try {
        $pdo = getDBConnection();

        // Execute a single query to fetch new messages
        $stmt = $pdo->prepare("
            SELECT *
            FROM chat
            WHERE ((sender_id = :logged_in_user_id AND receiver_id = :partner_user_id)
                OR (sender_id = :partner_user_id AND receiver_id = :logged_in_user_id))
                AND id > :last_message_id
            ORDER BY timestamp ASC
        ");
        $stmt->execute([
            'logged_in_user_id' => $loggedInUserId,
            'partner_user_id' => $partnerUserId,
            'last_message_id' => $lastMessageId,
        ]);

        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return messages if available
        if (!empty($messages)) {
            echo json_encode(['status' => 'success', 'messages' => $messages]);
            exit;
        } else {
            // For no new messages
            echo json_encode(['status' => 'no_new_messages', 'messages' => []]);
            exit;
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch messages']);
        exit;
    }
}
