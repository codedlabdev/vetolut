<?php
// mark_as_read.php


require_once BASE_DIR . 'lib/user/chat_func.php';  // Include the chat functions

if (isset($_GET['id'])) {
    $loggedInUserId = $_SESSION['user_id'];
    $partnerId = $_GET['id'];

    // Mark chat as read
    markChatAsRead($loggedInUserId, $partnerId);
}

header('Location: ' . $_SERVER['HTTP_REFERER']);  // Redirect back to the previous page
exit();
?>
