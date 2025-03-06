<?php
// lib/user/chat_func.php



function getMessages($userId, $otherUserId) {
    global $pdo; // Access your DB connection

    try {
        // Modified SQL query to join with users table for sender and receiver images
        $sql = "SELECT
                    chat.*,
                    sender.image AS sender_image,
                    receiver.image AS receiver_image
                FROM chat
                JOIN users AS sender ON chat.sender_id = sender.id
                JOIN users AS receiver ON chat.receiver_id = receiver.id
                WHERE (chat.sender_id = :user_id AND chat.receiver_id = :other_user_id)
                   OR (chat.sender_id = :other_user_id AND chat.receiver_id = :user_id)
                ORDER BY chat.timestamp ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':other_user_id' => $otherUserId
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Get messages error: " . $e->getMessage());
        return [];
    }
}



function getLatestMessage($loggedInUserId, $partnerId) {
    global $pdo; // assuming you are using PDO for database interaction

    // SQL query to get the most recent message between the user and the partner
    $stmt = $pdo->prepare("
        SELECT message_text AS content, timestamp
        FROM chat
        WHERE (sender_id = :user_id AND receiver_id = :partner_id)
           OR (sender_id = :partner_id AND receiver_id = :user_id)
        ORDER BY timestamp DESC
        LIMIT 1
    ");
    $stmt->execute([
        ':user_id' => $loggedInUserId,
        ':partner_id' => $partnerId
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC); // Return the latest message data
}


function getUnreadMessageCount($loggedInUserId, $partnerId) {
    global $pdo;

    // SQL query to count unread messages sent by the partner to the logged-in user
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS unread_count
        FROM chat_notice
        WHERE sender_id = :partner_id
          AND receiver_id = :user_id
          AND is_read = 0
    ");
    $stmt->execute([
        ':user_id' => $loggedInUserId,
        ':partner_id' => $partnerId
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int) $result['unread_count']; // Return the count of unread messages
}


function getChatPartners($userId) {
    global $pdo;

    try {
        // Get distinct users (excluding the logged-in user) who have had a chat with the logged-in user
        // and are not marked as deleted in partner_option
        $sql = "
            WITH chat_partners AS (
                SELECT
                    CASE
                        WHEN sender_id = :user_id THEN receiver_id
                        ELSE sender_id
                    END AS partner_id,
                    MAX(timestamp) AS last_message_time
                FROM chat
                WHERE sender_id = :user_id OR receiver_id = :user_id
                GROUP BY partner_id
            )
            SELECT cp.partner_id, cp.last_message_time
            FROM chat_partners cp
            LEFT JOIN partner_option po ON 
                (po.user_id = :user_id AND po.partner_id = cp.partner_id)
                OR (po.user_id = cp.partner_id AND po.partner_id = :user_id)
            WHERE po.is_delete IS NULL 
                OR (po.is_delete = 0)
                OR (po.user_id IS NULL AND po.partner_id IS NULL)
            ORDER BY cp.last_message_time DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch user details for each partner
        $partnerDetails = [];
        foreach ($partners as $partner) {
            $partnerId = $partner['partner_id'];
            $userStmt = $pdo->prepare("SELECT id, f_name, l_name, image, last_active FROM users WHERE id = :partner_id");
            $userStmt->execute([':partner_id' => $partnerId]);
            $partnerData = $userStmt->fetch(PDO::FETCH_ASSOC);

            if ($partnerData) {
                // Check if image is not null and has content
                $profileImage = BASE_URL . 'assets/user/img/noimage.png';
                if (!empty($partnerData['image']) && $partnerData['image'] !== null) {
                    // Ensure the image is a valid base64 string
                    $base64Image = base64_encode($partnerData['image']);
                    if ($base64Image) {
                        $profileImage = 'data:image/jpeg;base64,' . $base64Image;
                    }
                }

                $partnerDetails[] = [
                    'id' => $partnerData['id'],
                    'fullName' => htmlspecialchars($partnerData['f_name'] . ' ' . $partnerData['l_name']),
                    'profileImage' => $profileImage,
                    'isOnline' => strtotime($partnerData['last_active']) > (time() - 60)
                ];
            }
        }

        return $partnerDetails;
    } catch (PDOException $e) {
        error_log("Get chat partners error: " . $e->getMessage());
        return [];
    }
}

function markChatAsRead($loggedInUserId, $partnerId) {
    global $pdo;

    try {
        // Update notifications to mark them as read (is_read = 1)
        $stmt = $pdo->prepare("
            UPDATE chat_notice
            SET is_read = 1
            WHERE receiver_id = :user_id
                AND sender_id = :partner_id
                AND is_read = 0
        ");
        $stmt->execute([
            ':user_id' => $loggedInUserId,
            ':partner_id' => $partnerId
        ]);
    } catch (PDOException $e) {
        error_log("Error marking notifications as read: " . $e->getMessage());
    }
}

function updateMessageStatusToRead($loggedInUserId, $partnerId) {
    global $pdo;

    try {
        // Update status to 'read' for all delivered messages between the two users
        $stmt = $pdo->prepare("
            UPDATE chat
            SET status = 'read'
            WHERE ((sender_id = :partnerUserId AND receiver_id = :loggedInUserId)
                OR (sender_id = :loggedInUserId AND receiver_id = :partnerUserId))
            AND status = 'delivered'  // Update only delivered messages
        ");
        $stmt->execute([
            ':loggedInUserId' => $loggedInUserId,
            ':partnerUserId' => $partnerId,
        ]);

        // Optional: Log the number of rows updated for debugging
        $affectedRows = $stmt->rowCount();
        error_log("Number of messages marked as read: $affectedRows");

    } catch (Exception $e) {
        // Log the error if something goes wrong
        error_log("Error marking chat messages as read: " . $e->getMessage());
    }
}

// Function to get total unread message count from chat_notice table for the logged-in user
function getTotalUnreadMessageCount($loggedInUserId) {
        global $pdo; // Ensure you're using the global PDO connection

        try {
                // Query to count unread messages for the logged-in user across all partners
                $query = "SELECT COUNT(*)
                                    FROM chat_notice
                                    WHERE receiver_id = :loggedInUserId AND is_read = 0";  // Only count unread messages

                // Prepare and execute the query
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':loggedInUserId', $loggedInUserId, PDO::PARAM_INT);

                // Execute the query
                $stmt->execute();

                // Fetch the result
                $unreadCount = $stmt->fetchColumn();

                return $unreadCount;
        } catch (Exception $e) {
                // Log the error if something goes wrong
                error_log("Error getting total unread message count: " . $e->getMessage());
                return 0;  // Return 0 if an error occurs
        }
}

// Mark a chat as read for a specific user and partner
function markChatAsReadNew($user_id, $partner_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE messages 
        SET is_read = 1 
        WHERE receiver_id = ? AND sender_id = ? AND is_read = 0");
    
    if (!$stmt->execute([$user_id, $partner_id])) {
        throw new Exception("Failed to mark chat as read");
    }
    
    return true;
}

// Mute notifications for a specific chat
function muteChat($user_id, $partner_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO user_chat_settings 
        (user_id, partner_id, is_muted) 
        VALUES (?, ?, 1) 
        ON DUPLICATE KEY UPDATE is_muted = 1");
    
    if (!$stmt->execute([$user_id, $partner_id])) {
        throw new Exception("Failed to mute chat");
    }
    
    return true;
}

// hide a chat history between two users
  
// Pin a chat
function pinChat($userId, $partnerId) {
    global $pdo;

    try {
        // Check if the chat is already pinned
        $checkStmt = $pdo->prepare("SELECT * FROM pinned_chats WHERE user_id = :user_id AND partner_id = :partner_id");
        $checkStmt->execute([
            ':user_id' => $userId,
            ':partner_id' => $partnerId
        ]);

        // If already pinned, return false
        if ($checkStmt->rowCount() > 0) {
            return false;
        }

        // Insert new pinned chat record
        $insertStmt = $pdo->prepare("INSERT INTO pinned_chats (user_id, partner_id, pinned_at) VALUES (:user_id, :partner_id, :pinned_at)");
        $result = $insertStmt->execute([
            ':user_id' => $userId,
            ':partner_id' => $partnerId,
            ':pinned_at' => date('Y-m-d H:i:s')
        ]);

        return $result;
    } catch (PDOException $e) {
        error_log("Pin chat error: " . $e->getMessage());
        return false;
    }
}

// Unpin a chat
function unpinChat($userId, $partnerId) {
    global $pdo;

    try {
        // Delete the pinned chat record
        $stmt = $pdo->prepare("DELETE FROM pinned_chats WHERE user_id = :user_id AND partner_id = :partner_id");
        $result = $stmt->execute([
            ':user_id' => $userId,
            ':partner_id' => $partnerId
        ]);

        return $result;
    } catch (PDOException $e) {
        error_log("Unpin chat error: " . $e->getMessage());
        return false;
    }
}

// Get pinned chats
function getPinnedChats($userId) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            SELECT pc.partner_id, u.f_name, u.l_name, u.image, u.last_active, pc.pinned_at
            FROM pinned_chats pc
            JOIN users u ON pc.partner_id = u.id
            WHERE pc.user_id = :user_id
            ORDER BY pc.pinned_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        
        $pinnedChats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the results similar to getChatPartners
        $formattedPinnedChats = [];
        foreach ($pinnedChats as $chat) {
            // Check if image is not null and has content
            $profileImage = BASE_URL . 'assets/user/img/noimage.png';
            if (!empty($chat['image']) && $chat['image'] !== null) {
                // Ensure the image is a valid base64 string
                $base64Image = base64_encode($chat['image']);
                if ($base64Image) {
                    $profileImage = 'data:image/jpeg;base64,' . $base64Image;
                }
            }

            $formattedPinnedChats[] = [
                'id' => $chat['partner_id'],
                'fullName' => htmlspecialchars($chat['f_name'] . ' ' . $chat['l_name']),
                'profileImage' => $profileImage,
                'isOnline' => strtotime($chat['last_active']) > (time() - 60),
                'pinnedAt' => $chat['pinned_at']
            ];
        }
        
        return $formattedPinnedChats;
    } catch (PDOException $e) {
        error_log("Get pinned chats error: " . $e->getMessage());
        return [];
    }
}
