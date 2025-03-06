<?php
//index ai chat

include '../header.php';
require_once BASE_DIR . 'lib/user/ai_db.php';
// Fetch all previous chats for the user
$previous_chats = fetchUserChats($userId);

// Get chat_id from URL if present
$chatIdParam = isset($_GET['chat_id']) ? $_GET['chat_id'] : null;

$current_chat_messages = [];
if (isset($_GET['chat_id'])) {
    $current_chat_messages = fetchChatById($_GET['chat_id']);
}
 
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/user/css/ai_chat.css">

<div class="home d-flex flex-column vh-100">
    <?php include '../inc/top_head.php'; ?>
    <div class="vh-100 my-auto overflow-auto body-fix-osahan-footer">
        <div class="container">

            <!-- Sidebar Toggle Button -->
            <div class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </div>

            <!-- Sidebar Section -->
			
			
            <div class="sidebar" id="sidebar">
            <button class="button">Start New Chat</button>
			<div class="header">Previous Chat</div>
			
			<?php if ($previous_chats && count($previous_chats) > 0): ?>
			<div class="chat-list">
                <?php foreach ($previous_chats as $chat): 
                    // Get the title from the first message of this chat
                    $title = !empty($chat['title']) ? $chat['title'] : $chat['prompt'];
                    $displayText = strlen($title) > 50 ? substr($title, 0, 47) . '...' : $title;
                ?>
                    <div class="chat-item">
                        <a href="javascript:void(0)" data-chat-id="<?php echo htmlspecialchars($chat['chat_id']); ?>" class="chat-link">
                            <span class="chat-text"><?php echo htmlspecialchars($displayText); ?></span>
                        </a>
                        <div class="actions">
                            <i class="fas fa-edit" title="Edit"></i>
                            <i class="fas fa-trash" title="Delete"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
		<?php else: ?>
			<div class="no-message" style="margin-top: 250px;">
			<center>
				<p>No previous chats</p>
				</center>
			</div>
		<?php endif; ?>

		</div>

           <!-- welcomeMessage Chat Section -->
        <div class="main-chat" id="welcomeMessage">
            <div class="logo-container">
               <img src="<?php echo BASE_URL; ?>assets/img/logo/ai.png" alt="No Chats" style="width: 80px; border-radius: 25px;"/>
                <h1>Vetolut AI Chat</h1>
				<?php echo $userId; ?>

                <p>Connect with our AI for any questions or assistance you need.</p>
            </div>

            <div class="buttons">
                <button class="button">Start New Chat</button>
                <button class="button">View Conversations</button>
            </div>

            <div class="chat-textarea-container">
                <div class="file-preview-container" id="filePreviewContainerWelcome" style="display: none;">
                    <!-- Preview will be added here -->
                </div>
                <i class="fas fa-paperclip" id="fileLabelWelcome" style="cursor: pointer;"></i>
                <input type="file" id="fileInputWelcome" accept="image/*, .pdf, .doc, .docx" style="display: none;">

                <textarea id="messageInputWelcome" placeholder="Type your message..."></textarea>
                <i class="fas fa-headphones"></i>
                <a><i class="fas fa-microphone"></i></a>
                <div class="send-button-container">
                    <button id="sendButtonWelcome">
                        <i class="fas fa-paper-plane" style="color: white;"></i>
                    </button>
                </div>
            </div>

        </div>

        <!-- chatList Chat Section -->
        <div class="main-chat" id="chatList" style="display: none;">
        <div class="chat-messages" id="chatMessages">
    <?php if (!empty($current_chat_messages)): ?>
        <?php foreach ($current_chat_messages as $msg): ?>
            <div class="message-bubble <?php echo empty($msg['response']) ? 'user' : 'ai'; ?>">
                <?php if (!empty($msg['file_path'])): ?>
                    <img src="<?php echo BASE_URL . $msg['file_path']; ?>" alt="Attached Image" style="max-width: 100px;">
                <?php endif; ?>
                <p><?php echo htmlspecialchars(empty($msg['response']) ? $msg['prompt'] : $msg['response']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

          <div class="chat-textarea-container">
            <div class="file-preview-container" id="filePreviewContainerChat" style="display: none;">
                <!-- Preview will be added here -->
            </div>
            <i class="fas fa-paperclip" id="fileLabelChat" style="cursor: pointer;"></i>
            <input type="file" id="fileInputChat" accept="image/*, .pdf, .doc, .docx" style="display: none;">

            <textarea id="messageInputChat" placeholder="Type your message..."></textarea>
            <i class="fas fa-headphones"></i>
            <a><i class="fas fa-microphone"></i></a>
            <div class="send-button-container">
                <button id="sendButtonChat">
                    <i class="fas fa-paper-plane" style="color: white;"></i>
                </button>
            </div>
            <!-- Error message will be displayed here -->
            <div class="error-message" id="errorMessageChat" style="display: none;"></div>
        </div>


        </div>


        </div>

        <?php include '../inc/float_nav.php'; ?>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>

// Initialize chat ID from URL parameter or null for new chat
let chatId = new URLSearchParams(window.location.search).get('chat_id');
let isNewChat = !chatId || chatId === 'undefined';

// Check URL parameters on page load
if (!chatId || chatId === 'undefined') {
    document.getElementById('welcomeMessage').style.display = 'block';
    document.getElementById('chatList').style.display = 'none';
} else {
    document.getElementById('welcomeMessage').style.display = 'none';
    document.getElementById('chatList').style.display = 'block';
    loadChatMessages(chatId);
}

document.addEventListener('DOMContentLoaded', () => {
    // Elements for sidebar toggle and chat sections
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const welcomeMessage = document.getElementById('welcomeMessage');
    const chatList = document.getElementById('chatList');
    const messageInputWelcome = document.getElementById('messageInputWelcome');
    const sendButtonWelcome = document.getElementById('sendButtonWelcome');
    const messageInputChat = document.getElementById('messageInputChat');
    const sendButtonChat = document.getElementById('sendButtonChat');
    const chatMessages = document.getElementById('chatMessages');
    const errorMessageChat = document.getElementById('errorMessageChat'); // For error message

    // File input and preview container
    const fileInputWelcome = document.getElementById('fileInputWelcome');
    const fileLabelWelcome = document.getElementById('fileLabelWelcome');
    const filePreviewContainerWelcome = document.getElementById('filePreviewContainerWelcome');
    const fileInputChat = document.getElementById('fileInputChat');
    const fileLabelChat = document.getElementById('fileLabelChat');
    const filePreviewContainerChat = document.getElementById('filePreviewContainerChat');

    // OpenAI GPT-3 API Key and Endpoint
    //const API_KEY = "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"; // Replace with your OpenAI API key, use server-side code for security!
    //const API_URL = "https://api.openai.com/v1/completions";

     // Google Gemini API and Endpoint
    const API_KEY = 'AIzaSyAWnnEeeIAo2WYkeOgoU5LlZ3omi3fAOJA'; // Replace with your Google Gemini API key
    const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent'; // Replace with Google Gemini API endpoint


    let selectedFile = null; // Variable to store the selected file
    let isFirstMessage = true;

    

    // Function to display error message under the textarea
    function showError(message) {
        errorMessageChat.textContent = message;
        errorMessageChat.style.display = 'block'; // Show the error message
    }

    // Function to clear the error message
    function clearError() {
        errorMessageChat.textContent = '';
        errorMessageChat.style.display = 'none'; // Hide the error message
    }
	
	
	
	
	
	
	const startNewChatButton = document.querySelector('.button'); // Ensure you select the correct button for "Start New Chat"
    const chatIdParam = new URLSearchParams(window.location.search).get('chat_id'); // Check if chat_id exists in the URL

    // Function to generate a unique chat ID
    function generateChatId() {
        return 'chat-' + Math.random().toString(36).substr(2, 9);
    }

    // Update the URL with the chat ID
    function updateURLWithChatId(chatId) {
        const newUrl = `${window.location.pathname}?chat_id=${chatId}`;
        window.history.pushState({ chatId: chatId }, '', newUrl);
    }
	
	
	
	
	
	
	
	
	
	

    // Event Listener for the "Start New Chat" button
    startNewChatButton.addEventListener('click', () => {
        // Clear URL parameters and reload the page
        window.location.href = window.location.pathname;
    });

    // Add click event listeners to chat links
    document.querySelectorAll('.chat-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const selectedChatId = link.getAttribute('data-chat-id');
            if (selectedChatId && selectedChatId !== 'undefined') {
                chatId = selectedChatId;
                isNewChat = false;
                updateURLWithChatId(selectedChatId);
                loadChatMessages(selectedChatId);
            }
        });
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', (event) => {
        const params = new URLSearchParams(window.location.search);
        const newChatId = params.get('chat_id');
        
        if (newChatId) {
            chatId = newChatId;
            isNewChat = false;
            loadChatMessages(newChatId);
        } else {
            // Show welcome message if no chat_id in URL
            document.getElementById('welcomeMessage').style.display = 'block';
            document.getElementById('chatList').style.display = 'none';
            chatId = null;
            isNewChat = true;
        }
    });

    // Function to handle file preview for both sections
    function handleFilePreview(file, previewContainer) {
        previewContainer.innerHTML = ''; // Clear previous preview
        if (file) {
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = file.name;
                img.style.maxWidth = '100px'; // Adjust size as needed
                img.style.marginBottom = '10px';
                previewContainer.appendChild(img);
            } else if (file.type === 'application/pdf') {
                const pdfIcon = document.createElement('img');
                pdfIcon.src = 'path/to/pdf-icon.png'; // Replace with actual PDF icon path
                pdfIcon.alt = 'PDF file';
                previewContainer.appendChild(pdfIcon);
            } else if (file.type === 'application/msword' || file.type ===
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                const wordIcon = document.createElement('img');
                wordIcon.src = 'path/to/word-icon.png'; // Replace with actual Word icon path
                wordIcon.alt = 'Word file';
                previewContainer.appendChild(wordIcon);
            }
            previewContainer.style.display = 'block'; // Show the preview container
        }
    }

    // Trigger the file input click when the paperclip icon is clicked for welcomeMessage
    fileLabelWelcome.addEventListener('click', () => {
        fileInputWelcome.click(); // This will trigger the file input's click event
    });

    // Trigger the file input click when the paperclip icon is clicked for chatList
    fileLabelChat.addEventListener('click', () => {
        fileInputChat.click(); // This will trigger the file input's click event
    });

    // Handle file selection and preview for welcomeMessage
    fileInputWelcome.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            selectedFile = file;
            handleFilePreview(file, filePreviewContainerWelcome);
        }
    });

    // Handle file selection and preview for chatList
    fileInputChat.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            selectedFile = file;
            handleFilePreview(file, filePreviewContainerChat);
        }
    });

    // Sidebar toggle functionality
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        sidebarToggle.classList.toggle('active'); // Toggle the active class on the button
    });

    // Hide the welcome message and show the chat list after the first message
    function hideWelcomeElements() {
        if (isFirstMessage) {
            welcomeMessage.style.display = 'none'; // Hide the welcome message
            chatList.style.display = 'block'; // Show the chat list
            isFirstMessage = false; // Update the flag to prevent future changes
        }
    }

    // Add a message to the chat with an image (if any)
    function addMessage(message, isUser = true, image = null) {
        hideWelcomeElements(); // Hide welcome message and show chat list after first message
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-bubble ${isUser ? 'user' : 'ai'}`;

        // If there's an image, append it to the message
        if (image) {
            const img = document.createElement('img');
            img.src = image;
            img.alt = 'Attached Image';
            img.style.maxWidth = '100px'; // Adjust size as needed
            messageDiv.appendChild(img);
        }

        // Append the text message
        const textNode = document.createElement('p');
        textNode.textContent = message;
        messageDiv.appendChild(textNode);

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to the bottom
    }

    // Fetch response from OpenAI   API
    /*
    async function fetchAIResponse(prompt) {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${API_KEY}`
                },
                body: JSON.stringify({
                    model: "text-davinci-003",
                    prompt: prompt,
                    max_tokens: 150,
                    temperature: 0.7
                })
            });

            if (!response.ok) {
                throw new Error(`API Error: ${response.status}`);
            }

            const data = await response.json();
            if (data && data.choices && data.choices.length > 0) {
                return data.choices[0].text.trim();
            } else {
                throw new Error('Invalid API response format');
            }
        } catch (error) {
            console.error('Error fetching AI response:', error);
            return "I'm sorry, I couldn't process your request.";
        }
    }
    */

    async function fetchAIResponse(prompt) {
    try {
        // Construct the request body
        
        // Determine the context (e.g., welcome vs chat interaction) and update the system message
        const systemMessage = isFirstMessage
            ? "You are a friendly assistant introducing users to Vetolut chat system."
            : "You are a customer support assistant for Vetolut, helping users with their subscription plans, account issues, and general inquiries.";

        // Modify the request to include the appropriate system message
        const requestBody = {
            contents: [
                {
                    parts: [
                        {
                            text: systemMessage
                        },
                        {
                            text: prompt  // User's input
                        }
                    ]
                }
            ]
        };

        // Send the request to Google Gemini API
        const response = await fetch(`${API_URL}?key=${API_KEY}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestBody)
        });

        // Check if the response is successful
        if (!response.ok) {
            const errorData = await response.json();
            console.error('API Error Details:', errorData); // Log detailed API error
            throw new Error(`API Error: ${response.status}`);
        }

        // Parse the response data
        const data = await response.json();
        console.log('API Response:', data); // Log the full API response for debugging

        // Validate the structure of the response and return the AI's response text
        if (data && data.candidates && data.candidates.length > 0 && data.candidates[0].content && data.candidates[0].content.parts && data.candidates[0].content.parts.length > 0) {
            const aiText = data.candidates[0].content.parts[0].text.trim();
            return aiText; // Return the AI response text
        } else {
            throw new Error('Invalid API response format: Missing expected data.');
        }
    } catch (error) {
        console.error('Error fetching AI response:', error);
        return "I'm sorry, I couldn't process your request.";
    }
}

const BASE_URL = "<?php echo BASE_URL; ?>";
let chatId = chatIdParam || null;
    // Handle sending a message with an image
async function handleMessageSend(inputElement, buttonElement, fileInput, previewContainer) {
    const message = inputElement.value.trim();

    if (message || selectedFile) {
        if (selectedFile && !message) {
            showError("Please provide a message with the image.");
            return;
        }

        try {
            // Generate new chat ID only if this is a new chat
            if (isNewChat) {
                chatId = generateChatId();
                updateURLWithChatId(chatId);
                
                // Add new chat to sidebar immediately
                const chatList = document.querySelector('.chat-list');
                const noMessage = document.querySelector('.no-message');
                if (noMessage) {
                    noMessage.style.display = 'none';
                }
                
                if (!chatList) {
                    // Create chat-list if it doesn't exist
                    const sidebar = document.getElementById('sidebar');
                    const newChatList = document.createElement('div');
                    newChatList.className = 'chat-list';
                    sidebar.appendChild(newChatList);
                }

                const newChatItem = document.createElement('div');
                newChatItem.className = 'chat-item';
                newChatItem.innerHTML = `
                    <a href="javascript:void(0)" data-chat-id="${chatId}" class="chat-link">
                        <span class="chat-text">${message.length > 50 ? message.substring(0, 47) + '...' : message}</span>
                    </a>
                    <div class="actions">
                        <i class="fas fa-edit" title="Edit"></i>
                        <i class="fas fa-trash" title="Delete"></i>
                    </div>
                `;

                // Add to beginning of chat list
                const chatListElement = document.querySelector('.chat-list');
                if (chatListElement.firstChild) {
                    chatListElement.insertBefore(newChatItem, chatListElement.firstChild);
                } else {
                    chatListElement.appendChild(newChatItem);
                }

                // Add click event listener to the new chat link
                const chatLink = newChatItem.querySelector('.chat-link');
                chatLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    const selectedChatId = chatLink.getAttribute('data-chat-id');
                    if (selectedChatId && selectedChatId !== 'undefined') {
                        chatId = selectedChatId;
                        isNewChat = false;
                        updateURLWithChatId(selectedChatId);
                        loadChatMessages(selectedChatId);
                    }
                });
            }

            // Display user message instantly
            addMessage(message, true, selectedFile ? URL.createObjectURL(selectedFile) : null);
            inputElement.value = '';
            previewContainer.innerHTML = '';
            previewContainer.style.display = 'none';
            selectedFile = null;

            const aiResponse = await fetchAIResponse(message);
            addMessage(aiResponse, false);
            
            const userId = <?php echo $_SESSION['user_id']; ?>;
            const formData = new FormData();
            formData.append('chat_id', chatId);
            formData.append('user_id', userId);
            formData.append('prompt', message);
            formData.append('response', aiResponse);

            if (selectedFile) {
                formData.append('file', selectedFile);
            }

            const response = await fetch('<?php echo BASE_URL; ?>public/user/ai_chat/store_chat.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            if (!result.success) {
                throw new Error('Failed to store message');
            }
            
            console.log('Message stored successfully:', result);
            
            // If this was the first message (new chat), update sidebar
            if (isNewChat) {
                // Add new chat to sidebar immediately
                const chatList = document.querySelector('.chat-list');
                const noMessage = document.querySelector('.no-message');
                if (noMessage) {
                    noMessage.style.display = 'none';
                }
                
                if (!chatList) {
                    // Create chat-list if it doesn't exist
                    const sidebar = document.getElementById('sidebar');
                    const newChatList = document.createElement('div');
                    newChatList.className = 'chat-list';
                    sidebar.appendChild(newChatList);
                }

                const newChatItem = document.createElement('div');
                newChatItem.className = 'chat-item';
                newChatItem.innerHTML = `
                    <a href="javascript:void(0)" data-chat-id="${chatId}" class="chat-link">
                        <span class="chat-text">${message.length > 50 ? message.substring(0, 47) + '...' : message}</span>
                    </a>
                    <div class="actions">
                        <i class="fas fa-edit" title="Edit"></i>
                        <i class="fas fa-trash" title="Delete"></i>
                    </div>
                `;

                // Add to beginning of chat list
                const chatListElement = document.querySelector('.chat-list');
                if (chatListElement.firstChild) {
                    chatListElement.insertBefore(newChatItem, chatListElement.firstChild);
                } else {
                    chatListElement.appendChild(newChatItem);
                }

                // Add click event listener to the new chat link
                const chatLink = newChatItem.querySelector('.chat-link');
                chatLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    const selectedChatId = chatLink.getAttribute('data-chat-id');
                    if (selectedChatId && selectedChatId !== 'undefined') {
                        chatId = selectedChatId;
                        isNewChat = false;
                        updateURLWithChatId(selectedChatId);
                        loadChatMessages(selectedChatId);
                    }
                });
            }

            isNewChat = false;
            
        } catch (error) {
            console.error('Error:', error);
            //showError("An error occurred while sending your message.");
        }
    } else {
        showError("Please type a message or select an image to send.");
    }
}

// Load chat messages when clicking on a chat link
document.querySelectorAll('.chat-link').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const selectedChatId = link.getAttribute('data-chat-id');
        if (selectedChatId && selectedChatId !== 'undefined') {
            chatId = selectedChatId; // Update current chatId
            isNewChat = false; // Mark as existing chat
            updateURLWithChatId(selectedChatId);
            loadChatMessages(selectedChatId);
        }
    });
});

// Function to add a new chat title to the sidebar with animation
 
function addChatItemToSidebar(chatId, prompt) {
    const sidebar = document.getElementById('sidebar');
    let chatList = sidebar.querySelector('.chat-list');
    const noMessage = sidebar.querySelector('.no-message');

    // If there's a "no message" div and no chat-list, remove it and create chat-list
    if (noMessage) {
        noMessage.remove();
    }
    
    // If chat-list doesn't exist, create it
    if (!chatList) {
        chatList = document.createElement('div');
        chatList.classList.add('chat-list');
        sidebar.appendChild(chatList);
    }

    // Create new chat item
    const chatItem = document.createElement('div');
    chatItem.classList.add('chat-item');

    const chatLink = document.createElement('a');
    chatLink.style.color = 'white';
    chatLink.href = BASE_URL + "public/user/ai_chat/" + chatId;
    
    const chatText = document.createElement('span');
    chatText.classList.add('chat-text');
    chatText.textContent = prompt;
    chatLink.appendChild(chatText);

    const actions = document.createElement('div');
    actions.classList.add('actions');
    actions.innerHTML = '<i class="fas fa-edit" title="Edit"></i> <i class="fas fa-trash" title="Delete"></i>';

    chatItem.appendChild(chatLink);
    chatItem.appendChild(actions);
    
    // Add new chat item to the top
    chatList.insertBefore(chatItem, chatList.firstChild);
}

function loadChatMessages(chatId) {
    console.log('Loading messages for chat ID:', chatId);
    
    // Hide welcome message and show chat list
    document.getElementById('welcomeMessage').style.display = 'none';
    document.getElementById('chatList').style.display = 'block';
    
    // Clear existing messages
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = '';
    
    // Set current chat ID
    window.chatId = chatId;
    
    const url = `${BASE_URL}public/user/ai_chat/get_messages.php?chat_id=${chatId}`;
    console.log('Fetching from URL:', url);
    
    // Fetch messages for this chat
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error parsing JSON:', text);
                    throw new Error('Invalid JSON response from server');
                }
            });
        })
        .then(data => {
            console.log('Received data:', data);
            if (data.success && Array.isArray(data.messages)) {
                data.messages.forEach(msg => {
                    // Display user message (prompt)
                    if (msg.prompt) {
                        const userDiv = document.createElement('div');
                        userDiv.className = 'message-bubble user';
                        const userText = document.createElement('p');
                        userText.textContent = msg.prompt;
                        userDiv.appendChild(userText);
                        chatMessages.appendChild(userDiv);
                    }
                    
                    // Display AI response if it exists
                    if (msg.response) {
                        const aiDiv = document.createElement('div');
                        aiDiv.className = 'message-bubble ai';
                        const aiText = document.createElement('p');
                        aiText.textContent = msg.response;
                        aiDiv.appendChild(aiText);
                        chatMessages.appendChild(aiDiv);
                    }
                    
                    // Add image if exists
                    if (msg.file_path) {
                        const imgDiv = document.createElement('div');
                        imgDiv.className = 'message-bubble user';
                        const img = document.createElement('img');
                        img.src = BASE_URL + msg.file_path;
                        img.alt = 'Attached Image';
                        img.style.maxWidth = '100px'; // Adjust size as needed
                        imgDiv.appendChild(img);
                        chatMessages.appendChild(imgDiv);
                    }
                });
                
                // Scroll to bottom of messages
                chatMessages.scrollTop = chatMessages.scrollHeight;
            } else {
                console.error('Error loading messages:', data.error);
                showError(data.error || "Failed to load chat messages");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(error.message || "An error occurred while loading messages");
        });
}

// Add click event listeners to chat links
document.querySelectorAll('.chat-link').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const selectedChatId = link.getAttribute('data-chat-id');
        if (selectedChatId && selectedChatId !== 'undefined') {
            chatId = selectedChatId; // Update current chatId
            isNewChat = false; // Mark as existing chat
            updateURLWithChatId(selectedChatId);
            
            // Hide welcome message and show chat list before loading messages
            document.getElementById('welcomeMessage').style.display = 'none';
            document.getElementById('chatList').style.display = 'block';
            
            loadChatMessages(selectedChatId);
        }
    });
});

    // Add event listeners for sending messages
    sendButtonWelcome.addEventListener('click', (e) => {
        e.preventDefault(); // Prevent the default button behavior
        handleMessageSend(messageInputWelcome, sendButtonWelcome, fileInputWelcome, filePreviewContainerWelcome);
    });

    sendButtonChat.addEventListener('click', (e) => {
        e.preventDefault(); // Prevent the default button behavior
        handleMessageSend(messageInputChat, sendButtonChat, fileInputChat, filePreviewContainerChat);
    });

    // Allow sending the message when pressing 'Enter'
    messageInputWelcome.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleMessageSend(messageInputWelcome, sendButtonWelcome, fileInputWelcome, filePreviewContainerWelcome);
        }
    });

    messageInputChat.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleMessageSend(messageInputChat, sendButtonChat, fileInputChat, filePreviewContainerChat);
        }
    });

    // Set chat ID in the URL and update the URL after page load
    updateURLWithChatId();
});

</script>