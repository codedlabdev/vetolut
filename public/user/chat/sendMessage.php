<?php
// sendMessage.php
include '../header.php';
require_once BASE_DIR . 'lib/user/chat_func.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senderId = $_POST['sender_id'];
    $receiverId = $_POST['receiver_id'];
    $messageText = $_POST['message_text'] ?? null;

    // Check if a file is uploaded
    $file = $_FILES['file'] ?? null;

    // Validate parameters
    if (!$senderId || !$receiverId || (!$messageText && !$file)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
        exit;
    }

    $messageType = 'text'; // Default message type
    $filePath = null;
    $fileName = null;
    $fileSize = null;

    try {
        if ($file) {
            // Validate the uploaded file
            $allowedTypes = [
                'image/jpeg' => 'image',
                'image/png' => 'image',
                'image/gif' => 'image',
                'video/mp4' => 'video',
                'application/pdf' => 'file',
                'application/msword' => 'file',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'file',
                'audio/mpeg' => 'audio',
                'audio/wav' => 'audio',
                'audio/ogg' => 'audio'
            ];
            $fileType = $file['type'];
            if (!isset($allowedTypes[$fileType])) {
                echo json_encode(['status' => 'error', 'message' => 'Unsupported file type']);
                exit;
            }

            // Determine the upload directory
            $baseUploadDir = BASE_DIR . 'assets/user/img/chat_files/';
            $subDir = $allowedTypes[$fileType] . '/';
            $uploadDir = $baseUploadDir . $subDir;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
            }

            // Generate a unique file name
            $fileName = time() . '_' . basename($file['name']);
            $filePath = $uploadDir . $fileName;
            $fileSize = $file['size'];

            // Move the uploaded file to the appropriate directory
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload file']);
                exit;
            }

            // Prepare the file path for database storage (relative path)
            $filePath = str_replace(BASE_DIR, '', $filePath);
            $messageType = $allowedTypes[$fileType]; // Set message type based on file
        }

        $pdo = getDBConnection();
        $pdo->beginTransaction();

        // Insert message into the chat table
        $stmt = $pdo->prepare("
            INSERT INTO chat (
                sender_id, receiver_id, message_type, message_text,
                file_path, file_name, file_size, timestamp, status
            ) VALUES (
                :sender_id, :receiver_id, :message_type, :message_text,
                :file_path, :file_name, :file_size, NOW(), 'delivered'
            )
        ");
        $stmt->execute([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message_type' => $messageType,
            'message_text' => $messageText,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
        ]);

        $messageId = $pdo->lastInsertId();

        // Check if there's a deleted relationship in partner_option table for both directions
        $stmt = $pdo->prepare("
            SELECT id 
            FROM partner_option 
            WHERE (user_id = :sender_id AND partner_id = :receiver_id AND is_delete = 1)
            OR (user_id = :receiver_id AND partner_id = :sender_id AND is_delete = 1)
        ");
        $stmt->execute([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId
        ]);

        if ($stmt->fetch()) {
            // Update both records if they exist
            $stmt = $pdo->prepare("
                UPDATE partner_option 
                SET is_delete = 0,
                    chat_id = :chat_id,
                    updated_at = NOW()
                WHERE (user_id = :sender_id AND partner_id = :receiver_id)
                   OR (user_id = :receiver_id AND partner_id = :sender_id)
            ");
            $stmt->execute([
                'chat_id' => $messageId,
                'sender_id' => $senderId,
                'receiver_id' => $receiverId
            ]);
        }

        // Insert a notification into the chat_notice table
        $stmt = $pdo->prepare("
            INSERT INTO chat_notice (receiver_id, sender_id, chat_id, is_read, timestamp)
            VALUES (:receiver_id, :sender_id, :chat_id, 0, NOW())
        ");
        $stmt->execute([
            'receiver_id' => $receiverId,
            'sender_id' => $senderId,
            'chat_id' => $messageId, // Using message ID as chat ID
        ]);

        // Retrieve the newly inserted message
        $stmt = $pdo->prepare("SELECT * FROM chat WHERE id = :id");
        $stmt->execute(['id' => $messageId]);
        $message = $stmt->fetch(PDO::FETCH_ASSOC);

        // Commit the transaction
        $pdo->commit();

        // Return success response
        echo json_encode(['status' => 'success', 'message' => 'Message sent', 'data' => $message]);
    } catch (PDOException $e) {
        // Rollback on error
        $pdo->rollBack();
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
    }
}
?>
