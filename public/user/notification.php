<?php
// Notifications.php
?>

<div class="appointment-upcoming d-flex flex-column vh-100">
    <?php include 'inc/p_others.php'; 
    require_once BASE_DIR . 'lib/dhu.php'; // Include helper functions
    require_once BASE_DIR . 'lib/user/table.php'; // Include user-related functions

    $loggedInUserId = $_SESSION['user_id']; // Assuming user_id is stored in session

    // Mark notifications as read when the user views the page
    markNotificationsAsRead($loggedInUserId);

    // Fetch all notifications for the logged-in user
    $notifications = getNotifications($loggedInUserId, false); // Fetch all notifications, not only unread
    ?>

    <div class="vh-100 my-auto overflow-auto body-fix-osahan-footer">
    <div class="container" style="margin-top: 40px;">
        <div class="post-input-box">
            <div class="suggestions-card">


 <div class="vh-100 my-auto overflow-auto body-fix-osahan-footer">
    <ul class="nav doctor-profile-tabs mb-2 shadow-sm" id="pills-tab" role="tablist">
        <li class="nav-item col" role="presentation">
            <button class="nav-link w-100 active" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all" aria-selected="false" tabindex="-1">
                All
            </button>
        </li>

        <li class="nav-item col" role="presentation">
            <button class="nav-link w-100" id="pills-folio-tab" data-bs-toggle="pill" data-bs-target="#pills-folio" type="button" role="tab" aria-controls="pills-folio" aria-selected="false" tabindex="-1">
                Folio
            </button>
        </li>
	
	<!--
        <li class="nav-item col" role="presentation">
            <button class="nav-link w-100" id="pills-chat-tab" data-bs-toggle="pill" data-bs-target="#pills-chat" type="button" role="tab" aria-controls="pills-chat" aria-selected="false" tabindex="-1">
                Chat
            </button>
        </li>
	-->
        <li class="nav-item col" role="presentation">
            <button class="nav-link w-100" id="pills-network-tab" data-bs-toggle="pill" data-bs-target="#pills-network" type="button" role="tab" aria-controls="pills-network" aria-selected="false" tabindex="-1">
                Network
            </button>
        </li>

        <li class="nav-item col" role="presentation">
            <button class="nav-link w-100" id="pills-learn-tab" data-bs-toggle="pill" data-bs-target="#pills-learn" type="button" role="tab" aria-controls="pills-learn" aria-selected="true">
                Learn
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        <!-- All Tab Content -->
        <div class="tab-pane fade active show" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab" tabindex="0">
            <!-- Content for "All" tab -->

              <?php if (empty($notifications)): ?>
    <p>No notifications at the moment.</p>
<?php else: ?>
    <?php foreach ($notifications as $notification): ?>
        <?php
            // Conditionally display the image and name if the notification type is "follow", "comment", "like", or "post"
            if (in_array($notification['type'], ['follow', 'comment', 'like', 'post'])) {
                $imageData = !empty($notification['sender_image'])
                    ? 'data:image/jpeg;base64,' . base64_encode($notification['sender_image'])
                    : BASE_URL . 'assets/user/img/noimage.png';
                $senderName = htmlspecialchars($notification['sender_f_name'] . ' ' . $notification['sender_l_name']);
            }
        ?>

        <div class="suggestion <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
            <?php if (in_array($notification['type'], ['follow', 'comment', 'like', 'post'])): ?>
                <img src="<?php echo htmlspecialchars($imageData); ?>"
                     alt="<?php echo $senderName; ?>"
                     class="profile-pic">
                <div class="suggestion-info">
                    <a href="user_profile?id=<?php echo urlencode($notification['sender_id']); ?>" class="name-link">
                        <p class="name"><?php echo $senderName; ?></p>
                    </a>
                    <p class="role"><?php echo htmlspecialchars($notification['message']); ?></p>
                    <span class="notification-time" style="margin-right: 30px;">
               <small> <?php echo date("F j, Y, g:i a", strtotime($notification['created_at'])); ?> </small>
            </span><br>

                    <?php if ($notification['type'] == 'like'): ?>
                        <!-- Display link for "like" type notifications -->
                        <a href="<?php echo BASE_URL . 'post_list.php?id=' . urlencode($notification['reference_id']); ?>" class="post-link">View Post</a>

                    <?php elseif ($notification['type'] == 'post' && !empty($notification['post_link'])): ?>
                        <!-- For "post" type notifications, use the existing post link if available -->
                        <a href="<?php echo htmlspecialchars($notification['post_link']); ?>" class="post-link">View Post</a>

                    <?php elseif ($notification['type'] == 'comment'): ?>
                        <!-- For "comment" type notifications, use the existing comment link if available -->
                     <a href="<?php echo BASE_URL . 'post_list.php?id=' . urlencode($notification['reference_id']); ?>" class="post-link">View Comment</a>

                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="suggestion-info">
                    <p class="role"><?php echo htmlspecialchars($notification['message']); ?></p>

                </div>
            <?php endif; ?>



            <button onclick="deleteNotification(<?php echo $notification['id']; ?>)" class="btn btn-danger small"><i class="fas fa-trash"></i></button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>


        </div>

        <!-- Folio Tab Content -->
        <div class="tab-pane fade" id="pills-folio" role="tabpanel" aria-labelledby="pills-folio-tab" tabindex="0">
            <!-- Content for "Folio" tab -->
             <p>No notifications at the moment.</p>
        </div>

        <!-- Chat Tab Content -->
        <div class="tab-pane fade" id="pills-chat" role="tabpanel" aria-labelledby="pills-chat-tab" tabindex="0">
            <!-- Content for "Chat" tab -->
             <p>No notifications at the moment.</p>
        </div>

        <!-- Network Tab Content -->
        <div class="tab-pane fade" id="pills-network" role="tabpanel" aria-labelledby="pills-network-tab" tabindex="0">
            <!-- Content for "Network" tab -->
             <p>No notifications at the moment.</p>
        </div>

        <!-- Learn Tab Content -->
        <div class="tab-pane fade" id="pills-learn" role="tabpanel" aria-labelledby="pills-learn-tab" tabindex="0">
            <!-- Content for "Learn" tab -->
             <p>No notifications at the moment.</p>    

           <!-- <a href="#" class="link-dark">
                <div class="bg-white d-flex align-items-center gap-3 p-3 mb-1 shadow-sm">
                    <img src="img/video/available-doctor-5.jpg" alt="" class="img-fluid rounded-4 voice-img">
                    <div>
                        <h6 class="mb-1">Dr. Morgan</h6>
                        <p class="text-muted mb-2">Dentist</p>
                        <p class="text-muted m-0"><span class="mdi mdi-calendar-month text-primary me-1"></span>18 Nov 2023</p>
                         <span class="badge bg-success-subtle text-success fw-normal rounded-pill px-2">View Post</span>
                    </div>
                    <div class="ms-auto">
                        <div class="d-flex justify-content-end">
                            <div class="bg-inf-subtle rounded-circle icon mb-3">
                                <span class="mdi mdi-delete-outline mdi-18px text-danger"></span>

                            </div>
                        </div>

                    </div>
                </div>
            </a>
            -->


        </div>
    </div>
</div>




            </div>
        </div>
    </div>
</div>

</div>

<script>
// Function to delete notification (AJAX request to delete notification by ID)
function deleteNotification(notificationId) {
    fetch('<?php echo BASE_URL; ?>user/delete_notification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ notificationId: notificationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload or update the notifications list
            location.reload(); // You can replace this with dynamic removal if you prefer
        } else {
            console.error('Failed to delete notification:', data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<?php include 'footer.php'; ?>
