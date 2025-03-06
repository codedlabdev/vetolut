<?php
include '../header.php'; // Ensure session and DB connection are set up

$response = ['status' => 'error', 'data' => [], 'message' => 'No chats found'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_SESSION['user_id'];

    if (!empty($userId)) {
        $stmt = $pdo->prepare("SELECT * FROM ai_chat WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($chats) {
            $response = ['status' => 'success', 'data' => $chats];
        } else {
            $response['message'] = 'No chats found';
        }
    } else {
        $response['message'] = 'Invalid user ID';
    }
}

echo json_encode($response);
