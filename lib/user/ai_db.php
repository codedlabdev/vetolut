<?php

// lib/user/ai_db.php

// Insert new chat record
function saveChatMessage($chatId, $userId, $prompt, $response, $filePath = null) {
    global $pdo; // Database connection
    
    try {
        // First check if a title already exists for this chat_id
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ai_chat WHERE chat_id = :chat_id AND title IS NOT NULL");
        $stmt->bindParam(':chat_id', $chatId);
        $stmt->execute();
        $hasTitleAlready = $stmt->fetchColumn() > 0;
        
        if (!$hasTitleAlready) {
            // No title exists yet, create one from the first message
            $title = strlen($prompt) > 50 ? substr($prompt, 0, 47) . '...' : $prompt;
            
            $query = "INSERT INTO ai_chat (chat_id, user_id, title, prompt, response, file_path, created_at) 
                     VALUES (:chat_id, :user_id, :title, :prompt, :response, :file_path, NOW())";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':title', $title);
        } else {
            // Title already exists, just insert the new message without a title
            $query = "INSERT INTO ai_chat (chat_id, user_id, prompt, response, file_path, created_at) 
                     VALUES (:chat_id, :user_id, :prompt, :response, :file_path, NOW())";
            $stmt = $pdo->prepare($query);
        }
        
        $stmt->bindParam(':chat_id', $chatId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':prompt', $prompt);
        $stmt->bindParam(':response', $response);
        $stmt->bindParam(':file_path', $filePath);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error saving chat message: " . $e->getMessage());
        return false;
    }
}

// Fetch previous chats for a user
function fetchUserChats($userId) {
    global $pdo; // Database connection
    try {
        // Get the first message (with title) for each chat_id
        $query = "SELECT DISTINCT a.*
                 FROM ai_chat a
                 INNER JOIN (
                     SELECT chat_id, MIN(created_at) as first_message
                     FROM ai_chat
                     GROUP BY chat_id
                 ) b ON a.chat_id = b.chat_id AND a.created_at = b.first_message
                 WHERE a.user_id = :user_id
                 ORDER BY a.created_at DESC";
                 
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching user chats: " . $e->getMessage());
        return [];
    }
}

// Fetch chat messages by chat_id
function fetchChatById($chatId) {
    global $pdo; // Database connection
    
    try {
        // Debug log the input
        error_log("fetchChatById called with chat_id: " . $chatId);
        
        $query = "SELECT * FROM ai_chat WHERE chat_id = :chat_id ORDER BY created_at ASC";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':chat_id', $chatId);
        
        // Execute and check for success
        if (!$stmt->execute()) {
            error_log("SQL Error: " . print_r($stmt->errorInfo(), true));
            return [];
        }
        
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug log the results
        error_log("fetchChatById found " . count($messages) . " messages");
        error_log("Messages: " . print_r($messages, true));
        
        return $messages;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

// Delete all chat messages by chat_id
function deleteChatById($chatId, $userId) {
    global $pdo;
    try {
        $query = "DELETE FROM ai_chat WHERE chat_id = :chat_id AND user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':chat_id', $chatId);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error deleting chat: " . $e->getMessage());
        return false;
    }
}

?>
