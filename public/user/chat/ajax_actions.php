<?php
  // Ensure session is started at the very top of the file
  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }

  include '../header.php';
  include BASE_DIR . 'public/user/inc/top_head.php';
  require_once BASE_DIR . 'lib/user/table.php'; // Include helper functions
  require_once BASE_DIR . 'lib/user/chat_func.php'; // Include helper functions

if (isset($_POST['action']) && $_POST['action'] === 'pin_chat') {
    $response = ['success' => false, 'message' => 'Failed to pin chat'];
    
    if (isset($_POST['partner_id']) && is_numeric($_POST['partner_id'])) {
        $partnerId = $_POST['partner_id'];
        $userId = $_SESSION['user_id'];  // Assuming user is logged in
        
        // Call the pinChat function from chat_func.php
        $result = pinChat($userId, $partnerId);
        
        if ($result) {
            // Set a session flash message
            $_SESSION['flash_message'] = ' Pinned!';
            $_SESSION['flash_message_type'] = 'success';
            
            $response = [
                'success' => true, 
                'message' => 'Pinned',
                'pinnedAt' => date('Y-m-d H:i:s')
            ];
        }
    }
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'unpin_chat') {
    $response = ['success' => false, 'message' => 'Failed to unpin chat'];
    
    if (isset($_POST['partner_id']) && is_numeric($_POST['partner_id'])) {
        $partnerId = $_POST['partner_id'];
        $userId = $_SESSION['user_id'];  // Assuming user is logged in
        
        // Call the unpinChat function from chat_func.php
        $result = unpinChat($userId, $partnerId);
        
        if ($result) {
            // Set a session flash message
            $_SESSION['flash_message'] = 'Unpinned!';
            $_SESSION['flash_message_type'] = 'success';
            
            $response = [
                'success' => true, 
                'message' => 'Unpinned'
            ];
        }
    }
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'hide_chat') {
    $response = ['success' => false, 'message' => 'Failed to update chat'];
    
    if (isset($_POST['partner_id']) && is_numeric($_POST['partner_id'])) {
        $partnerId = $_POST['partner_id'];
        $userId = $_SESSION['user_id'];
        
        try {
            // Get the latest chat entry with sender_id and receiver_id
            $stmt = $pdo->prepare("
                SELECT id, sender_id, receiver_id 
                FROM chat 
                WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
                ORDER BY id DESC 
                LIMIT 1
            ");
            $stmt->execute([$userId, $partnerId, $partnerId, $userId]);
            $chatData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($chatData) {
                $chatId = $chatData['id'];
                $senderId = $chatData['sender_id'];
                $receiverId = $chatData['receiver_id'];

                // Check if record exists in partner_option
                $stmt = $pdo->prepare("
                    SELECT id, is_block, is_mute 
                    FROM partner_option 
                    WHERE user_id = ? AND partner_id = ?
                ");
                $stmt->execute([$senderId, $receiverId]);
                $existingOption = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingOption) {
                    // Update existing record maintaining current is_block and is_mute values
                    $stmt = $pdo->prepare("
                        UPDATE partner_option 
                        SET is_delete = 1,
                            chat_id = ?,
                            is_block = ?,
                            is_mute = ?,
                            updated_at = NOW()
                        WHERE user_id = ? AND partner_id = ?
                    ");
                    $result = $stmt->execute([
                        $chatId,
                        $existingOption['is_block'],
                        $existingOption['is_mute'],
                        $senderId,
                        $receiverId
                    ]);
                } else {
                    // Insert new record with default values
                    $stmt = $pdo->prepare("
                        INSERT INTO partner_option 
                        (user_id, partner_id, chat_id, is_delete, is_block, is_mute, created_at, updated_at) 
                        VALUES (?, ?, ?, 1, 0, 0, NOW(), NOW())
                    ");
                    $result = $stmt->execute([$senderId, $receiverId, $chatId]);
                }
                
                if ($result) {
                    $_SESSION['flash_message'] = 'Chat updated successfully!';
                    $_SESSION['flash_message_type'] = 'success';
                    
                    $response = [
                        'success' => true, 
                        'message' => 'Chat updated successfully'
                    ];
                }
            } else {
                $response['message'] = 'No chat found';
            }
        } catch (PDOException $e) {
            error_log("Error in hide_chat: " . $e->getMessage());
            $response['message'] = 'Database error occurred';
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}