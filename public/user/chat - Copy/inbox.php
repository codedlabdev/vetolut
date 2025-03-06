<div class="appointment-upcoming d-flex flex-column vh-100">
  <?php 
  // inbox.php

include '../header.php';
//require_once BASE_DIR . 'lib/user/chat_func.php'; // Include helper functions

$loggedInUserId = $_SESSION['user_id']; // Get the logged-in user ID from session
$partnerUserId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$partnerUserId) {
    echo "Invalid user ID.";
    exit;
}

// Retrieve both users' data (logged-in user and partner user)
$pdo = getDBConnection(); // Assuming getDBConnection() is defined to get the PDO instance
$stmt = $pdo->prepare("
    SELECT id, f_name, l_name, image, last_active 
    FROM users 
    WHERE id IN (:loggedInUserId, :partnerUserId)
");
$stmt->execute([
    'loggedInUserId' => $loggedInUserId,
    'partnerUserId' => $partnerUserId,
]);

// Process the user data for both users
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$loggedInUser = null;
$partnerUser = null;

foreach ($users as $user) {
    if ($user['id'] == $loggedInUserId) {
        $loggedInUser = [
            'id' => $user['id'],
            'fullName' => htmlspecialchars($user['f_name'] . ' ' . $user['l_name']),
            'profileImage' => !empty($user['image'])
                ? 'data:image/jpeg;base64,' . base64_encode($user['image'])
                : BASE_URL . 'assets/user/img/noimage.png',
            'isOnline' => strtotime($user['last_active']) > (time() - 60),
        ];
    } elseif ($user['id'] == $partnerUserId) {
        $partnerUser = [
            'id' => $user['id'],
            'fullName' => htmlspecialchars($user['f_name'] . ' ' . $user['l_name']),
            'profileImage' => !empty($user['image'])
                ? 'data:image/jpeg;base64,' . base64_encode($user['image'])
                : BASE_URL . 'assets/user/img/noimage.png',
            'isOnline' => strtotime($user['last_active']) > (time() - 60),
        ];
    }
}

if (!$loggedInUser || !$partnerUser) {
    echo "User data retrieval failed.";
    exit;
}

// Fetch messages
$messages = getMessages($loggedInUserId, $partnerUserId);




?>
  
 

<!-- Header Section -->
<div class="d-flex align-items-center justify-content-between mb-auto p-3 bg-white shadow-sm osahan-header">
    <a href="javascript:void(0);" onclick="window.history.back();" class="text-dark bg-white shadow rounded-circle icon">
        <span class="mdi mdi-arrow-left mdi-18px"></span>
    </a>
	<?php if ($user): ?>
    <div class="d-flex align-items-center gap-2 me-auto">
        <a href="partner-profile.php?id=<?php echo $partnerUser['id']; ?>">
            <img src="<?php echo $partnerUser['profileImage']; ?>" alt="<?php echo $partnerUser['fullName']; ?>" style="width: 50px; height: 50px; border-radius: 50%;">
        </a>
        <div>
            <p class="mb-0 fw-bold"><small><?php echo $partnerUser['fullName']; ?></small></p>
		  <span style="margin-top: 5px;" class="<?php echo $partnerUser['isOnline'] ? 'text-success' : 'text-danger'; ?>">
                <strong><small><?php echo $partnerUser['isOnline'] ? 'online' : 'offline'; ?></small></strong>
            </span>


        </div>
    </div>
	 <?php endif; ?>
    <div class="d-flex align-items-center gap-2">
        <a href="call-doctor.html" class=" shadow rounded-circle icon">
            <span class="mdi mdi-phone-outline mdi-18px"></span>
        </a>
        <a href="call-doctor.html" class=" shadow rounded-circle icon">
            <span class="mdi mdi-video-outline mdi-18px"></span>
        </a>
        <a class="toggle bg-white shadow rounded-circle icon d-flex align-items-center justify-content-center fs-5" href="#">
            <i class="bi bi-list"></i>
        </a>
    </div>
</div>



<!-- Chat Messages Section -->
<div class="vh-100 my-auto overflow-auto p-3" id="chat-messages">
    <?php
    foreach ($messages as $message) {
        // Check if sender has a profile image
        $senderImage = !empty($message['sender_image']) 
                        ? 'data:image/jpeg;base64,' . base64_encode($message['sender_image']) 
                        : BASE_URL . 'assets/user/img/noimage.png';

        // Format the message time
        $messageTime = date('h:i A', strtotime($message['timestamp']));

        // Determine if the message is incoming or outgoing
        $isIncoming = $message['sender_id'] != $loggedInUserId;
    ?>
        <!-- Incoming Message -->
        <?php if ($isIncoming): ?>
		
				<div class="mb-3">
                <div class="d-flex align-items-end gap-2 mb-1">
                    <div class="bg-white chat-rounded-left p-3 shadow-sm">
                        <div class="m-0"><?php echo htmlspecialchars($message['message_text']); ?></div>
                    </div>
                </div>
                <p class="text-muted mb-0 ps-5 small ms-3"><?php echo $messageTime; ?></p>
				<!-- Typing Indicator -->
			 
            </div>
		
        <!-- Outgoing Message -->
        <?php else: ?>
           
            <div class="mb-3">
                <div class="d-flex align-items-end gap-2 mb-1">
                    <div class="d-flex align-items-center gap-3 text-white w-100 text-right">
                        <span class="bg-info ms-auto chat-rounded-right p-3 shadow-sm"><?php echo htmlspecialchars($message['message_text']); ?></span>
                    </div>
                </div>
                <p class="text-muted m-0 pe-5 small me-3 text-end"><?php echo $messageTime; ?></p>
            </div> 
        <?php endif; ?>
    <?php } ?>
	
			<!-- Typing Indicator -->
		<div id="typing-indicator" class="text-muted" style="display: none;">
			Colleagu is typing...
		</div>

</div>

<!-- Message Input Section -->
<div class="footer bg-white mt-auto shadow border-top">
    <div class="input-group pe-3">
        <textarea style="height: 55px;" id="messageContent" class="form-control textarea-control p-3 border-0" placeholder="Type a message" required></textarea>
        <span class="input-group-text bg-transparent border-0 p-0" id="attachIcon">
            <a class="lighter-bg-primary-opacity rounded-circle icon text-dark" href="#">
                <i class="bi bi-paperclip"></i>
            </a>
        </span>
        <span class="input-group-text bg-transparent mx-1 border-0 p-0" id="voiceIcon">
            <a class="lighter-bg-primary-opacity rounded-circle icon text-dark" href="#"><i class="bi bi-mic-fill"></i></a>
        </span>
        <span class="input-group-text bg-transparent border-0 p-0" id="sendIcon">
            <a class="bg-primary rounded-circle icon text-white" href="javascript:void(0);" onclick="sendMessage()">
                <i class="bi bi-send"></i>
            </a>
        </span>
    </div>
</div>




  
</div>

<?php include '../inc/side_menu.php'; ?>

<style>
.osahan-header{
	display:none;
}
 
#typing-indicator {
    font-size: 12px;
    margin-left: 10px;
    margin-top: 10px;
}

</style>
 
<!-- End emoji-picker JavaScript -->
	
<!-- JavaScript AJAX Function for Sending Messages -->
<script>
   // Get the logged-in user ID from the URL (example: ?id=54)
const loggedInUserId = new URLSearchParams(window.location.search).get('id');

// Set up WebSocket connection
const socket = new WebSocket("ws://localhost:8080/chat");

socket.onopen = function() {
    console.log("Connected to the WebSocket server");
};

socket.onmessage = function(event) {
    const message = JSON.parse(event.data);
	
    if (message.type === 'chat') {
        displayMessage(message.message, message.sender_id === loggedInUserId ? 'outgoing' : 'incoming');
    } else if (message.type === 'typing') {
        // Handle typing notification
        handleTypingIndicator(data.sender_id, data.is_typing);
    }
};

function sendMessage() {
    const messageContent = document.getElementById("messageContent").value;
    const partnerUserId = <?php echo $userId; ?>; // This should be the intended recipient's ID

    if (messageContent.trim() === "") {
        alert("Please enter a message.");
        return;
    }

    if (socket.readyState === WebSocket.OPEN) {
        const messageData = {
            type: 'chat',
            message: messageContent,
            sender_id: <?php echo $_SESSION['user_id']; ?>, // The logged-in user's ID
            receiver_id: partnerUserId, // The intended recipient's ID
            timestamp: new Date().toISOString()
        };
        socket.send(JSON.stringify(messageData));
        displayMessage(messageContent, 'outgoing');
        document.getElementById("messageContent").value = '';
    } else {
        alert("WebSocket connection is not open.");
    }
}


    // Function to display messages in the chat UI
    function displayMessage(messageText, type) {
        const messageContainer = document.createElement("div");
        messageContainer.classList.add("mb-3");

        if (type === 'outgoing') {
            messageContainer.innerHTML = `
                <div class="d-flex align-items-end gap-2 mb-1">
                    <div class="bg-info ms-auto chat-rounded-right p-3 shadow-sm text-white">
                        <div class="m-0">${messageText}</div>
                    </div>
                </div>
                <p class="text-muted mb-0 ps-5 small ms-3 text-end">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
            `;
        } else if (type === 'incoming') {
            messageContainer.innerHTML = `
                <div class="d-flex align-items-end gap-2 mb-1">
                    <div class=" bg-white chat-rounded-left p-3 shadow-sm">
                        <span>${messageText}</span>
                    </div>
                </div>
                <p class="text-muted m-0 pe-5 small me-3">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
            `;
        }

        const chatContainer = document.querySelector(".vh-100.my-auto.overflow-auto.p-3");
        chatContainer.appendChild(messageContainer);

        // Scroll to the bottom of the chat container
        scrollToBottom();
    }

    // Function to scroll to the bottom of the chat container
    function scrollToBottom() {
        const chatContainer = document.querySelector(".vh-100.my-auto.overflow-auto.p-3");
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
	
 

</script>


<script>

// Typing notification functionality
let typingTimeout;

const messageInput = document.getElementById('messageContent'); // The input where the user types the message
const typingIndicator = document.getElementById('typing-indicator'); // Element to show typing indicator

// Send a "typing" notification when the user starts typing
messageInput.addEventListener('input', () => {
    clearTimeout(typingTimeout);

    // Send typing notification to the server
    sendTypingNotification(true);

    // Stop typing notification after 3 seconds of inactivity
    typingTimeout = setTimeout(() => {
        sendTypingNotification(false);
    }, 3000);
});

// Function to send typing notifications to the WebSocket server
function sendTypingNotification(isTyping) {
    if (socket.readyState === WebSocket.OPEN) {
        const typingData = {
            type: 'typing',
            sender_id: <?php echo $_SESSION['user_id']; ?>,
            receiver_id: <?php echo $partnerUserId; ?>,
            is_typing: isTyping
        };
        socket.send(JSON.stringify(typingData));
    }
}

// Handle typing notification
function handleTypingIndicator(senderId, isTyping) {
    const typingIndicator = document.getElementById('typing-indicator');

    // Ensure it's the partner user's ID
    if (senderId === <?php echo $partnerUserId; ?>) {
        if (isTyping) {
            typingIndicator.style.display = 'block'; // Show typing indicator
            typingIndicator.innerText = 'The other user is typing...';
        } else {
            typingIndicator.style.display = 'none'; // Hide typing indicator
        }
    }
}



 

</script>


<?php include '../footer.php'; ?>