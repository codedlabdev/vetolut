<?php
// websocket_server.php

// Define BASE_DIR if not already defined
if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
}

// Include the Ratchet autoloader and your project dependencies
require_once BASE_DIR . 'vendor/autoload.php';
require_once BASE_DIR . 'lib/dhu.php'; // Include helper functions for database or other needs

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\App;

class ChatServer implements MessageComponentInterface
{
    protected $clients;  // All connected clients
    protected $userConnections; // Map to store user ID and their corresponding connection

    public function __construct() {
        $this->clients = new \SplObjectStorage();
        $this->userConnections = [];
        echo "WebSocket Server initialized...\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        // When a new connection opens, no immediate user ID is available,
        // we need to associate the connection with the user ID once it's received
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        echo "Received message: " . $msg . "\n";  // Log the incoming message

        if (isset($data['type'])) {
            if ($data['type'] === 'chat') {
                $this->handleChatMessage($data, $from);
            } elseif ($data['type'] === 'typing') {
                $this->handleTypingNotification($data, $from);
            } elseif ($data['type'] === 'newMessage') {
                // Handle new message for notifications
                $this->handleNewMessage($data);
            } else {
                echo "Unknown message type received.\n";
            }
        } else {
            echo "Invalid message structure received.\n";
        }
    }

    // Handle chat message (sender -> receiver)
   private function handleChatMessage($data, $from) {
    if (isset($data['sender_id'], $data['receiver_id'], $data['message'], $data['timestamp'])) {
        $senderId = $data['sender_id'];
        $receiverId = $data['receiver_id'];
        $message = $data['message'];
        $timestamp = $data['timestamp'];

        // Ensure sender and receiver are different
        if ($senderId === $receiverId) {
            echo "Sender and receiver are the same. Message not sent.\n";
            return; // Do not send the message to oneself
        }

        // Store connection by userId (sender)
        $this->userConnections[$senderId] = $from;
        echo "Sender {$senderId} is connected.\n";

        // Check if receiver is connected
        if (isset($this->userConnections[$receiverId])) {
            echo "Receiver {$receiverId} is connected. Sending message...\n";
            $this->userConnections[$receiverId]->send(json_encode([
                'type' => 'chat',
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'message' => $message,
                'timestamp' => $timestamp
            ]));
        } else {
            echo "Receiver {$receiverId} is not connected.\n";
            // Optionally store the message for later or handle accordingly
            $this->queueMessage($senderId, $receiverId, $message, $timestamp);
        }

        // Optionally save the message in the database
        $this->saveMessage($senderId, $receiverId, $message, $timestamp);  // Call the function to save the message
    } else {
        echo "Invalid chat message structure received.\n";
    }
}
    
    // Handle typing message (sender -> receiver)
    private function handleTypingNotification($data, $from) {
    if (isset($data['sender_id'], $data['receiver_id'], $data['is_typing'])) {
        $senderId = $data['sender_id'];
        $receiverId = $data['receiver_id'];
        $isTyping = $data['is_typing'];  // true or false

        if ($senderId === $receiverId) {
            echo "Sender and receiver are the same. Typing notification not sent.\n";
            return; // Do not send the typing notification to oneself
        }

        // Send the typing notification to the receiver if they are connected
        if (isset($this->userConnections[$receiverId])) {
            $this->userConnections[$receiverId]->send(json_encode([
                'type' => 'typing',
                'sender_id' => $senderId,
                'is_typing' => $isTyping
            ]));
        } else {
            echo "Receiver {$receiverId} is not connected. Typing notification not sent.\n";
        }
    } else {
        echo "Invalid typing notification structure received.\n";
    }
}

	
	

    // Handle newMessage type (for notifications or other purposes)
    private function handleNewMessage($data) {
        // Handle new message type here, for example, for notifications
        if (isset($data['senderId'], $data['receiverId'])) {
            echo "New message from " . $data['senderId'] . " to " . $data['receiverId'] . "\n";
            // Example: save notifications to the database or broadcast the message.
        } else {
            echo "Invalid newMessage structure.\n";
        }
    }

    // Save the message to the database 
   private function saveMessage($senderId, $receiverId, $message, $timestamp) {
    try {
        // Use the existing getDBConnection function to get the database connection
        $pdo = getDBConnection(); // This fetches the connection set up in lib/db.php
        
        $stmt = $pdo->prepare("
            INSERT INTO chat (sender_id, receiver_id, message_text, timestamp) 
            VALUES (:sender_id, :receiver_id, :message_text, :timestamp)
        ");
        $stmt->execute([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message_text' => $message,
            'timestamp' => $timestamp
        ]);
        echo "Message saved to database.\n";
    } catch (PDOException $e) {
        echo "Error saving message: " . $e->getMessage() . "\n";
    }
}


    // Queue the message for later delivery (optional)
    private function queueMessage($senderId, $receiverId, $message, $timestamp) {
        // Optionally queue the message for later delivery when the receiver connects
        echo "Message queued for receiver {$receiverId}.\n";
        // Example: Save to a queue or database to be sent later when receiver is online
    }

    public function onClose(ConnectionInterface $conn) {
        // Remove the client when they disconnect
        foreach ($this->userConnections as $userId => $userConn) {
            if ($userConn === $conn) {
                unset($this->userConnections[$userId]);
                break;
            }
        }
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Start the WebSocket server
$port = 8080; // Use your desired port
$server = new App('localhost', $port); // Correct way to instantiate the App

$server->route('/chat', new ChatServer, ['*']); // Define the WebSocket route for the chat

echo "WebSocket Server running on ws://localhost:$port/chat\n";

$server->run();
