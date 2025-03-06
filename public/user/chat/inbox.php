<?php
// inbox.php
include '../header.php';
require_once BASE_DIR . 'lib/user/chat_func.php'; // Include helper functions
$loggedInUserId = $_SESSION['user_id'];
$partnerUserId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$partnerUserId) {
    echo "Invalid user ID.";
    exit;
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("
    SELECT id, f_name, l_name, image, last_active
    FROM users
    WHERE id IN (:loggedInUserId, :partnerUserId)
");
$stmt->execute([
    'loggedInUserId' => $loggedInUserId,
    'partnerUserId' => $partnerUserId,
]);

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

// In inbox.php, mark the messages as read when the user views the chat
if (isset($_GET['id'])) {
    $partnerId = $_GET['id'];

    // Mark chat as read when the user views the chat
    updateMessageStatusToRead($loggedInUserId, $partnerId); // This marks the delivered messages as read
}


// In inbox.php or wherever the user views the chat
if (isset($_GET['id'])) {
    $partnerId = $_GET['id'];
    // Mark chat as read when the user views the chat
    markChatAsRead($loggedInUserId, $partnerId);

}


$messages = getMessages($loggedInUserId, $partnerUserId);


?>

<div class="appointment-upcoming d-flex flex-column vh-100">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-auto p-3 bg-white shadow-sm osahan-header">
        <a href="<?php echo BASE_URL; ?>user/chat/lists.php" class="text-dark bg-white shadow rounded-circle icon" style="margin-right: 10px;">
            <span class="mdi mdi-arrow-left mdi-18px"></span>
        </a>
        <div class="d-flex align-items-center gap-2 me-auto">
            <a href="<?php echo BASE_URL; ?>user/user_profile/?id=<?php echo $partnerUser['id']; ?>">
                <img src="<?php echo $partnerUser['profileImage']; ?>" alt="<?php echo $partnerUser['fullName']; ?>" style="width: 50px; height: 50px; border-radius: 50%;">
            </a>
            <div>
            <a href="<?php echo BASE_URL; ?>user/user_profile/?id=<?php echo $partnerUser['id']; ?>">
                <p class="mb-0 fw-bold"><small><?php echo $partnerUser['fullName']; ?></small></p>
                </a>
                <span style="margin-top: 5px;" class="<?php echo $partnerUser['isOnline'] ? 'text-success' : 'text-danger'; ?>">
                    <strong><small><?php echo $partnerUser['isOnline'] ? 'online' : 'offline'; ?></small></strong>
                </span>
            </div>
        </div>
        <!--<div class="d-flex align-items-center gap-2">
            <a href="call-doctor.html" class="shadow rounded-circle icon">
                <span class="mdi mdi-phone-outline mdi-18px"></span>
            </a>
            <a href="call-doctor.html" class="shadow rounded-circle icon">
                <span class="mdi mdi-video-outline mdi-18px"></span>
            </a>
            <a class="toggle bg-white shadow rounded-circle icon d-flex align-items-center justify-content-center fs-5" href="#">
                <i class="bi bi-list"></i>
            </a>
        </div>-->
    </div>

    <!-- Chat Messages Section -->
<div class="vh-100 my-auto overflow-auto p-3" id="chat-messages">
    <?php foreach ($messages as $message): ?>
        <?php
        $senderImage = !empty($message['sender_image'])
            ? 'data:image/jpeg;base64,' . base64_encode($message['sender_image'])
            : BASE_URL . 'assets/user/img/noimage.png';
        $messageTime = date('h:i A', strtotime($message['timestamp']));
        $isIncoming = $message['sender_id'] != $loggedInUserId;

        // Define the preview based on the message type
        $previewUrl = '';
        if ($message['message_type'] == 'file') {
            // Check file extension to determine if it's PDF or DOC
            $fileExtension = pathinfo($message['file_name'], PATHINFO_EXTENSION);
            if ($fileExtension == 'pdf') {
                $previewUrl = "https://t4.ftcdn.net/jpg/01/03/75/43/360_F_103754394_xSNhdDOKFusz9Vrb8ZZNLY8SXSwLfaIT.jpg"; // PDF Dummy Image
            } elseif ($fileExtension == 'doc' || $fileExtension == 'docx') {
                $previewUrl = "https://cdn-icons-png.flaticon.com/512/3979/3979305.png"; // DOC Dummy Image
            }
        } elseif ($message['message_type'] == 'image') {
            // For image types, show the image preview
            $previewUrl = BASE_URL . $message['file_path'];
        }

        // Determine if the message is incoming or outgoing
        $isOutgoing = $message['sender_id'] == $loggedInUserId;

        // Set the message status only for outgoing messages
        if ($isOutgoing) {
            if ($message['status'] == 'delivered') { // Sent
                $status = 'Delivered';
            } elseif ($message['status'] == 'read') { // Read
                $status = 'Read';
            } else {
                $status = 'Unknown'; // Default if no status matches
            }
        }
        ?>

        <?php if ($isIncoming): ?>
            <div class="mb-3">
                <div class="d-flex align-items-end gap-2 mb-1">
                    <div class="bg-white chat-rounded-left p-3 shadow-sm">


                        <?php if ($previewUrl): ?>
                            <!-- Show the file preview (PDF, DOC, or Image) -->
                            <div class="mt-2">
                                <img src="<?php echo $previewUrl; ?>" alt="File Preview" class="img-fluid" style="width: 100%; height: 100px;">
                            </div>
                        <?php endif; ?>
                         <div class="m-0"><?php echo htmlspecialchars($message['message_text']); ?></div>
						  <p class="text-muted mb-0 small"><small><i><?php echo $messageTime; ?></i></small></p>
                    </div>
                </div>
               
            </div>
        <?php else: ?>
            <div class="mb-3">
                <div class="d-flex align-items-end gap-2 mb-1">
                    <div class="d-flex align-items-center gap-3 text-white w-100 text-right">
                        <span class="bg-info ms-auto chat-rounded-right p-3 shadow-sm">

                         <?php if ($previewUrl): ?>
                            <!-- Show the file preview (PDF, DOC, or Image) -->
                            <div class="mt-2">
                                <img src="<?php echo $previewUrl; ?>" alt="File Preview" class="img-fid" style="width: 100%; height: 100px;">
                            </div>
                        <?php endif; ?>

                        <?php echo htmlspecialchars($message['message_text']); ?>


						<p class="text-muted mb-0 small text-end" style="color: #e9f2ff!important;"><small><i><?php echo $messageTime; ?></i></small></p>
                        </span>

                    </div>
                </div>
                


            </div>
        <?php endif; ?>

    <?php endforeach; ?>
</div>


   <!-- Message Input Section -->
<div class="footer bg-white mt-auto shadow border-top">
    <div class="input-group pe-3">

        <!-- File Preview Section -->
        <div id="filePreview" class="d-none position-absolute mb-2" style="margin-top: -40px;margin-left: 10px;">
            <!-- Image Preview -->
            <img id="imagePreview" src="" alt="Image Preview" class="img-fluid" style="max-width: 150%; max-height: 150%;" />
            <!-- PDF Preview -->
            <iframe id="pdfPreview" src="" style="max-width: 150%; height: 150%; display: none;" ></iframe>
            <!-- Document Preview (Generic Text Preview) -->
            <div id="docPreview" style="display: none;">
                <p id="docTextPreview">Document preview is not available for this file type.</p>
            </div>

            <!-- Cancel Icon for File Preview -->
            <span id="cancelPreview" class="position-absolute top-0 end-0 p-1 rounded-circle cursor-pointe" onclick="cancelPreview()">
                <i class="bi bi-x-circle-fill text-danger" style="font-size: 20px;margin-top: -55px;position: absolute;margin-left: -10px;"></i>
            </span>
        </div>

        <!-- Textarea for Message -->
        <textarea style="height: 55px;" id="messageContent" class="form-control textarea-control p-3 border-0" placeholder="Type a message" required></textarea>

        <!-- Attach Icon -->
        <span class="input-group-text bg-transparent border-0 p-0" id="attachIcon">
            <a class="lighter-bg-primary-opacity rounded-circle icon text-dark" href="#" onclick="document.getElementById('fileInput').click();">
                <i class="bi bi-paperclip"></i>
            </a>
        </span>

        <!-- File input (hidden) -->
        <input type="file" id="fileInput" style="display:none;" accept="image/*, .pdf, .doc, .docx" onchange="previewFile()" />

        <!-- Voice Icon -->
        <span class="input-group-text bg-transparent mx-1 border-0 p-0" id="voiceIcon">
            <a class="lighter-bg-primary-opacity rounded-circle icon text-dark" href="#"><i class="bi bi-mic-fill"></i></a>
        </span>

        <!-- Send Icon -->
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
.osahan-header {
    display: none;
}

#typing-indicator {
    font-size: 12px;
    margin-left: 10px;
    margin-top: 10px;
}

.img-fl {

    height: 80px !important;
    width: 100px !important;
    margin-top: -45px !important;
    border-radius: 10px !important;;
}

#chat-messages {
    overflow-y: auto;
    height: calc(100vh - 200px); /* Adjust based on your layout */
    scroll-behavior: smooth; /* For smooth scrolling */
}

</style>

<script>
const BASE_URL = "<?php echo BASE_URL; ?>";
let lastMessageId = <?php echo !empty($messages) ? $messages[count($messages) - 1]['id'] : 0; ?>;

// Function to handle file selection and preview
function previewFile() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    const filePreview = document.getElementById('filePreview');

    if (!file) return;

    // Allowed file extensions
    const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    const fileExtension = file.name.split('.').pop().toLowerCase();

    if (!allowedExtensions.includes(fileExtension)) {
        alert('Invalid file type. Please select an image, PDF, or document.');
        fileInput.value = ''; // Reset the input
        filePreview.classList.add('d-none'); // Hide preview
        return;
    }

    // Show file preview container
    filePreview.classList.remove('d-none');

    // Clear previous preview
    filePreview.innerHTML = `
        <span id="cancelPreview" class="position-absolute top-0 end-0 p-1 rounded-circle cursor-pointer" onclick="cancelPreview()">
            <i class="bi bi-x-circle-fill text-danger" style="font-size: 20px;margin-top: -55px;position: absolute;margin-left: -10px;"></i>
        </span>
    `;

    if (file) {
        const fileReader = new FileReader();
        const fileName = file.name.toLowerCase();

        if (fileName.endsWith('.jpg') || fileName.endsWith('.jpeg') || fileName.endsWith('.png') || fileName.endsWith('.gif')) {
            // Image Preview
            const imagePreview = document.createElement('img');
            imagePreview.src = URL.createObjectURL(file);
            imagePreview.classList.add('img-fl');

            filePreview.appendChild(imagePreview);
        } else if (fileName.endsWith('.pdf')) {
            // PDF Preview - Display a dummy image instead of the actual file
            const pdfPreview = document.createElement('img');
            pdfPreview.src = 'https://t4.ftcdn.net/jpg/01/03/75/43/360_F_103754394_xSNhdDOKFusz9Vrb8ZZNLY8SXSwLfaIT.jpg'; // Replace this with your dummy image path
            pdfPreview.style.maxWidth = '100px';
            pdfPreview.style.maxHeight = '80px';
            pdfPreview.style.marginTop = '-45px';
            filePreview.appendChild(pdfPreview);

            filePreview.appendChild(pdfPreview);
        } else if (fileName.endsWith('.doc') || fileName.endsWith('.docx')) {
            // Document Preview - Display a dummy image instead of the actual file
            const docPreview = document.createElement('img');
            docPreview.src = 'https://cdn-icons-png.flaticon.com/512/3979/3979305.png'; // Replace this with your dummy image path
            docPreview.style.maxWidth = '100px';
            docPreview.style.maxHeight = '80px';
            docPreview.style.marginTop = '-45px';
            filePreview.appendChild(docPreview);
        }
    }
}

// Function to handle canceling the preview
function cancelPreview() {
    const filePreview = document.getElementById('filePreview');
    const fileInput = document.getElementById('fileInput');

    // Hide file preview
    filePreview.classList.add('d-none');

    // Clear the file input value
    fileInput.value = '';
}

// Function to scroll to the bottom of the chat
function scrollToBottom() {
    const chatBox = document.getElementById('chat-messages');
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Call scrollToBottom when the page loads
document.addEventListener('DOMContentLoaded', () => {
    scrollToBottom();
});

function appendMessage(message) {
    const chatBox = document.getElementById('chat-messages');
    const messageTime = new Date(message.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const isIncoming = message.sender_id !== <?php echo $loggedInUserId; ?>;

    let previewHtml = '';
    if (message.file_path) {
        if (message.message_type === 'image') {
            // Use the full server path for images, not blob URLs
            const imageSrc = message.file_path.startsWith('blob:') ? message.file_path : `${BASE_URL}${message.file_path}`;
            previewHtml = `<img src="${imageSrc}" alt="Attached Image" class="" style="width: 100%; height: 100px;">`;
        } else if (message.message_type === 'file') {
            const fileExtension = message.file_name.split('.').pop().toLowerCase();
            if (fileExtension === 'pdf') {
                previewHtml = `<img src="https://t4.ftcdn.net/jpg/01/03/75/43/360_F_103754394_xSNhdDOKFusz9Vrb8ZZNLY8SXSwLfaIT.jpg" alt="PDF File" class="img-fluid" style="max-width: 100px; max-height: 100px;">`;
            } else if (fileExtension === 'doc' || fileExtension === 'docx') {
                previewHtml = `<img src="https://cdn-icons-png.flaticon.com/512/3979/3979305.png" alt="Document File" class="img-fluid" style="max-width: 100px; max-height: 100px;">`;
            }
        }
    }

    const messageHtml = `
        <div id="msg-${message.id}" class="mb-3">
            <div class="d-flex align-items-end gap-2 mb-1">
                <div class="${isIncoming ? 'bg-white chat-rounded-left' : 'bg-info ms-auto chat-rounded-right text-white'} p-3 shadow-sm">
                    ${previewHtml}
                    <div class="m-0">${message.message_text}</div>
                </div>
            </div>
            <p class="text-muted mb-0 small ${isIncoming ? '' : 'text-end'}"><small><i>${messageTime}</i></small></p>
        </div>
    `;

    chatBox.insertAdjacentHTML('beforeend', messageHtml);
    scrollToBottom();
}

function sendMessage() {
    const messageContent = document.getElementById('messageContent').value.trim();
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];

    if (!messageContent && !file) return;

    const formData = new FormData();
    formData.append('sender_id', <?php echo $loggedInUserId; ?>);
    formData.append('receiver_id', <?php echo $partnerUserId; ?>);
    formData.append('message_text', messageContent);
    if (file) formData.append('file', file);

    const tempId = 'temp-' + Date.now();
    const tempMessage = {
        id: tempId,
        sender_id: <?php echo $loggedInUserId; ?>,
        message_text: messageContent,
        timestamp: new Date().toISOString(),
        file_path: file ? URL.createObjectURL(file) : null,
        message_type: file ? (file.type.startsWith('image/') ? 'image' : 'file') : 'text',
        file_name: file ? file.name : null
    };

    appendMessage(tempMessage);
    document.getElementById('messageContent').value = '';
    fileInput.value = '';
    cancelPreview();

    $.ajax({
        url: `${BASE_URL}user/chat/sendMessage.php`,
        method: 'POST',
        processData: false,
        contentType: false,
        data: formData,
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    updateMessage(tempId, result.data);
                } else {
                    markMessageAsFailed(tempId);
                    console.error('Failed to send message:', result.message);
                }
            } catch (e) {
                markMessageAsFailed(tempId);
                console.error('Failed to parse server response:', e);
            }
        },
        error: function(xhr, status, error) {
            markMessageAsFailed(tempId);
            console.error('Ajax request failed:', status, error);
        }
    });
}

function updateMessage(tempId, confirmedMessage) {
    const messageElement = document.getElementById(`msg-${tempId}`);
    if (messageElement) {
        messageElement.id = `msg-${confirmedMessage.id}`;
        const timeElement = messageElement.querySelector('p.text-muted');
        if (timeElement) {
            timeElement.textContent = new Date(confirmedMessage.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
        if (confirmedMessage.file_path) {
            const imgElement = messageElement.querySelector('img');
            if (imgElement) {
                // Update the image source with the server path
                imgElement.src = `${BASE_URL}${confirmedMessage.file_path}`;
            }
        }
    }
    lastMessageId = Math.max(lastMessageId, confirmedMessage.id);
}



// Function to handle file selection and preview
function previewFile() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    const filePreview = document.getElementById('filePreview');

    if (!file) return;

    const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    const fileExtension = file.name.split('.').pop().toLowerCase();

    if (!allowedExtensions.includes(fileExtension)) {
        alert('Invalid file type. Please select an image, PDF, or document.');
        fileInput.value = '';
        filePreview.classList.add('d-none');
        return;
    }

    filePreview.classList.remove('d-none');
    filePreview.innerHTML = `
        <span id="cancelPreview" class="position-absolute top-0 end-0 p-1 rounded-circle cursor-pointer" onclick="cancelPreview()">
            <i class="bi bi-x-circle-fill text-danger" style="font-size: 20px;margin-top: -55px;position: absolute;margin-left: -10px;"></i>
        </span>
    `;

    if (file.type.startsWith('image/')) {
        const imagePreview = document.createElement('img');
        imagePreview.src = URL.createObjectURL(file);
        imagePreview.classList.add('img-fl');
        filePreview.appendChild(imagePreview);
    } else if (fileExtension === 'pdf') {
        const pdfPreview = document.createElement('img');
        pdfPreview.src = 'https://t4.ftcdn.net/jpg/01/03/75/43/360_F_103754394_xSNhdDOKFusz9Vrb8ZZNLY8SXSwLfaIT.jpg';
        pdfPreview.style.maxWidth = '100px';
        pdfPreview.style.maxHeight = '80px';
        pdfPreview.style.marginTop = '-45px';
        filePreview.appendChild(pdfPreview);
    } else if (fileExtension === 'doc' || fileExtension === 'docx') {
        const docPreview = document.createElement('img');
        docPreview.src = 'https://cdn-icons-png.flaticon.com/512/3979/3979305.png';
        docPreview.style.maxWidth = '100px';
        docPreview.style.maxHeight = '80px';
        docPreview.style.marginTop = '-45px';
        filePreview.appendChild(docPreview);
    }
}

function cancelPreview() {
    const filePreview = document.getElementById('filePreview');
    const fileInput = document.getElementById('fileInput');
    filePreview.classList.add('d-none');
    fileInput.value = '';
}


document.getElementById('fileInput').addEventListener('change', previewFile);



function markMessageAsFailed(id) {
    const messageElement = document.getElementById(`msg-${id}`);
    if (messageElement) {
        const contentElement = messageElement.querySelector('.message-content');
        if (contentElement) {
            contentElement.classList.add('bg-danger');
            contentElement.insertAdjacentHTML('beforeend', '<span class="text-white ms-2"></span>');
        }
    }
}

function fetchMessages() {
    $.ajax({
        url: 'path/to/FetchMessages.php',
        method: 'POST',
        data: {
            logged_in_user_id: loggedInUserId, // Replace with actual ID
            partner_user_id: partnerUserId,   // Replace with actual ID
            last_message_id: lastMessageId,  // Replace with the last seen message ID
        },
        success: function(response) {
            if (response.status === 'success') {
                // Process new messages
                response.messages.forEach((message) => {
                    appendMessage(message); // Implement appendMessage to update UI
                });
            }
        },
        error: function() {
            console.error('Failed to fetch messages.');
        },
    });

    // Poll every 3 seconds
    setTimeout(fetchMessages, 3000);
}

// Start polling
fetchMessages();


// Start fetching new messages
fetchNewMessages();


// Add event listener for sending messages
document.getElementById('sendIcon').addEventListener('click', sendMessage);
document.getElementById('messageContent').addEventListener('keypress', function(e) {
    if (e.key === '|' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});



</script>













<?php include '../footer.php'; ?>