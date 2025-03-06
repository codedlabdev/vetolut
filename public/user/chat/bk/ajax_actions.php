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
            $_SESSION['flash_message'] = 'Chat successfully pinned!';
            $_SESSION['flash_message_type'] = 'success';
            
            $response = [
                'success' => true, 
                'message' => 'Chat pinned successfully',
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
            $_SESSION['flash_message'] = 'Chat successfully unpinned!';
            $_SESSION['flash_message_type'] = 'success';
            
            $response = [
                'success' => true, 
                'message' => 'Chat unpinned successfully'
            ];
        }
    }
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
