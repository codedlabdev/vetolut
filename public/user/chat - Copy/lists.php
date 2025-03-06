<div class="appointment-upcoming d-flex flex-column vh-100">
  <?php
  // chat-list

  include '../inc/p_top.php';

  require_once BASE_DIR . 'lib/user/chat_func.php'; // Include helper functions

  $loggedInUserId = $_SESSION['user_id']; // Get the logged-in user ID from session

  // Retrieve the list of users the logged-in user has chatted with
  $chatPartners = getChatPartners($loggedInUserId);
  ?>

  <hr/>
  
 <!-- Top static section -->
<div class="bg-white shadow-sm mb-2">
    <div class="px-3">
        <!-- Non-scrollable icon -->
        <span class="mdi mdi-pin mdi-10px"></span>
    </div>

    <!-- Scrollable chat section -->
    <div class="chat-scroll px-3 pb-3 overflow-auto">
        <div class="d-flex align-items-center justify-content-between">
            <a href="chat.html" class="link-dark text-center">
                <!-- Placeholder image -->
                <img src="<?= BASE_URL . 'assets/user/img/noimage.png' ?>" alt="" class="img-fluid rounded-pill message-profile">
                <p class="pt-1 m-0 small text-dark-50">Rumpa</p>
            </a>
            <a href="chat.html" class="link-dark text-center">
                <!-- Placeholder image -->
                <img src="<?= BASE_URL . 'assets/user/img/noimage.png' ?>" alt="" class="img-fluid rounded-pill message-profile">
                <p class="pt-1 m-0 small text-dark-50">Nipa</p>
            </a>
            <a href="chat.html" class="link-dark text-center">
                <!-- Placeholder image -->
                <img src="<?= BASE_URL . 'assets/user/img/noimage.png' ?>" alt="" class="img-fluid rounded-pill message-profile">
                <p class="pt-1 m-0 small text-dark-50">Riya</p>
            </a>
            <a href="chat.html" class="link-dark text-center">
                <!-- Placeholder image -->
                <img src="<?= BASE_URL . 'assets/user/img/noimage.png' ?>" alt="" class="img-fluid rounded-pill message-profile">
                <p class="pt-1 m-0 small text-dark-50">John</p>
            </a>
            <a href="chat.html" class="link-dark text-center">
                <!-- Placeholder image -->
                <img src="<?= BASE_URL . 'assets/user/img/noimage.png' ?>" alt="" class="img-fluid rounded-pill message-profile">
                <p class="pt-1 m-0 small text-dark-50">Sully</p>
            </a>
        </div>
    </div>
</div>


<div class="vh-100 my-auto overflow-auto body-fix-osahan-footer">
  <div class="rounded-4 shadow overflow-hidden bg-white m-3">
    <?php if (empty($chatPartners)): ?>
      <div class="no-posts-message" style="text-align: center; margin-top: 20px; color: #888;">
        <i class="fas fa-comments" style="font-size: 48px; color: #d3d3d3; margin-bottom: 10px;"></i>
       <h4>No Chats to Show Yet</h4>
        <p>Start a conversation with your colleagues to initiate communication!</p>
		<p>To begin, navigate to your <strong><a href="">Profile</a></strong> page, then visit the <strong><a href="">Contacts</a></strong> section. <br> Once there, click on the chat icon to start a conversation with your colleagues.</p>
      </div>
    <?php else: ?>
      <!-- Loop through the chat partners and display them -->
      <?php foreach ($chatPartners as $partner): ?>
        <?php
        // Get the latest message between the logged-in user and the partner
        $latestMessage = getLatestMessage($loggedInUserId, $partner['id']);
        // Get unread message count for the logged-in user and the partner
        $unreadCount = getUnreadMessageCount($loggedInUserId, $partner['id']);
        // Truncate the latest message content to 40 characters
        $latestMessageContent = substr($latestMessage['content'], 0, 40);
        // Optionally, add ellipsis (...) if the message is truncated
        if (strlen($latestMessage['content']) > 40) {
            $latestMessageContent .= '...';
        }
        ?>
        <a href="<?php echo BASE_URL . 'user/chat/inbox.php?id=' .$partner['id']; ?>" class="link-dark position-relative">
          <div class="bg-white shadow-sm d-flex align-items-center gap-3 p-3 border-bottom">
            <!-- Display partner's profile picture -->
            <img src="<?= $partner['profileImage']; ?>" alt="" class="img-fluid rounded-pill message-profile">

            <!-- Display unread message count if greater than 0 -->
            <?php if ($unreadCount > 0): ?>
              <span class="badge bg-danger position-absolute translate-middle badge-message-count"><?= $unreadCount; ?></span>
            <?php endif; ?>

            <div>
              <!-- Partner's full name -->
              <h6 class="mb-1 fs-14"><?= htmlspecialchars($partner['fullName']); ?></h6>
              <!-- Latest message preview (truncated to 150 characters) -->
              <p class="text-muted m-0 small"><?= htmlspecialchars($latestMessageContent); ?></p>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

</div>

 


  <?php include '../inc/float_nav.php'; ?>
</div>



<?php include '../inc/side_menu.php'; ?>

<style>

/* Badge styles for message count */
.badge-message-count {
    padding: 0.25em 0.6em;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 30%;
    color: white;
}

/* Example for positioning on each chat item profile */
.position-relative .badge-message-count {
    margin-right: 8px;
    right: 12px;
    margin-top: 6px;
    position: fixed!important;
}

.avx-feedback-float-icon{
	display:none;
}

.chat-scroll{
	 
}

.text-center {
    
    margin-top: 20px;
}


/* Styles for the non-scrollable icon */
.icon {
    position: sticky;
    top: 0;
    z-index: 1;
    padding: 10px;
    border-radius: 50%;
}

/* Scrollable chat area */
.chat-scroll {
    max-height: calc(100vh - 150px); /* Adjust based on the height of the top section */
    overflow-y: auto;
    padding-bottom: 15px;
}

</style>

<script>


// Function to handle follow/unfollow actions
function toggleFollow(userId) {
    fetch('<?php echo BASE_URL; ?>user/follow_user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ followingId: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the button text and functionality based on the follow status
            const button = document.getElementById('follow-btn-' + userId);
            if (data.isFollowing) {
                button.innerText = 'Unfollow';
                button.setAttribute('onclick', 'toggleFollow(' + userId + ')'); // Set to unfollow action
            } else {
                button.innerText = 'Follow';
                button.setAttribute('onclick', 'toggleFollow(' + userId + ')'); // Set to follow action
            }
        }
    })
    .catch(error => console.error('Error:', error)); // Handle errors
}

</script>
<?php include '../footer.php'; ?>
