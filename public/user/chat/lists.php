<div class="appointment-upcoming d-flex flex-column vh-100">
    <?php
  // chat-list
  include '../header.php';
  include BASE_DIR . 'public/user/inc/top_head.php';
  require_once BASE_DIR . 'lib/user/table.php'; // Include helper functions
  require_once BASE_DIR . 'lib/user/chat_func.php'; // Include helper functions

  $loggedInUserId = $_SESSION['user_id']; // Get the logged-in user ID from session

    // Retrieve the list of users the logged-in user has chatted with
  $chatPartners = getChatPartners($loggedInUserId);

  // Get pinned chats
  $pinnedChats = getPinnedChats($loggedInUserId);

  // Remove pinned chats from chat partners to avoid duplicates
  $chatPartners = array_filter($chatPartners, function($partner) use ($pinnedChats) {
      return !in_array($partner['id'], array_column($pinnedChats, 'id'));
  });

  // Combine pinned chats and regular chat partners
  $allChats = array_merge($pinnedChats, $chatPartners);

  // Check if a partner ID is available (for marking notifications as read)
  if (isset($_GET['id'])) {
      $partnerId = $_GET['id'];
      // Call the function to mark the notifications as read
      markChatAsRead($loggedInUserId, $partnerId);
  }

  // Check for flash messages
  $flash_message = isset($_SESSION['flash_message']) ? $_SESSION['flash_message'] : null;
  $flash_message_type = isset($_SESSION['flash_message_type']) ? $_SESSION['flash_message_type'] : 'info';

  // Clear the flash message
  if ($flash_message) {
      unset($_SESSION['flash_message']);
      unset($_SESSION['flash_message_type']);
  }

  ?>

    <?php if ($flash_message): ?>
    <div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1100;">
        <div class="alert alert-<?= $flash_message_type ?> alert-dismissible fade show" role="alert" id="flashMessage">
            <?= htmlspecialchars($flash_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <script>
        // Auto hide flash message after 1 second
        setTimeout(function() {
            const flashMessage = document.getElementById('flashMessage');
            if (flashMessage) {
                const bsAlert = new bootstrap.Alert(flashMessage);
                bsAlert.close();
            }
        }, 1000);
    </script>
    <?php endif; ?>

    <div class="search-container px-3 py-2">
        <div class="position-relative">
            <input type="text" id="chatSearch" class="form-control search-input" placeholder="Search Colleagues..."
                style="padding-left: 35px; border-radius: 20px; border: 1px solid #e0e0e0;">
            <i class="fas fa-search position-absolute"
                style="left: 12px; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
        </div>
    </div>
    <!-- Top static section -->
    <div class="bg-white shadow-sm mb-2">
        <div class="px-3" style="margin-top: 8px; margin-bottom: -10px;">
            <!-- Non-scrollable icon -->
            <i class="fas fa-thumbtack pin-l pin-icon"></i>
        </div>

        <!-- Scrollable chat section -->
        <div class="chat-scroll px-3 pb-3 overflow-auto">
            <div class="d-flex align-items-center justify-content-between">
                <?php
                // Get current user ID (you should have this from your session)
                $current_user_id = $_SESSION['user_id'] ?? 0;

                $pdo = getDBConnection();
                $stmt = $pdo->prepare("
                    SELECT pc.*, u.f_name, u.l_name, u.image 
                    FROM pinned_chats pc 
                    JOIN users u ON pc.partner_id = u.id 
                    WHERE pc.user_id = ? 
                    ORDER BY pc.pinned_at DESC
                ");
                $stmt->execute([$current_user_id]);
                $pinned_partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Format pinned partners similar to other chat partner lists
                $formattedPinnedPartners = [];
                foreach ($pinned_partners as $partner) {
                    // Check if image is not null and has content
                    $profileImage = BASE_URL . 'assets/user/img/noimage.png';
                    if (!empty($partner['image']) && $partner['image'] !== null) {
                        // Ensure the image is a valid base64 string
                        $base64Image = base64_encode($partner['image']);
                        if ($base64Image) {
                            $profileImage = 'data:image/jpeg;base64,' . $base64Image;
                        }
                    }

                    $formattedPinnedPartners[] = [
                        'id' => $partner['partner_id'],
                        'fullName' => htmlspecialchars($partner['f_name'] . ' ' . $partner['l_name']),
                        'profileImage' => $profileImage
                    ];
                }
                ?>
                <?php foreach ($formattedPinnedPartners as $partner): ?>
                <a href="<?php echo BASE_URL . 'user/chat/inbox.php?id=' . $partner['id']; ?>"
                    class="link-dark text-center">
                    <img src="<?= $partner['profileImage'] ?>" alt="<?= $partner['fullName'] ?>"
                        class="img-fluid rounded-pill message-profile">
                    <p class="pt-1 m-0 small text-dark-50"><?= explode(' ', $partner['fullName'])[0] ?></p>
                </a>
                <?php endforeach; ?>
                <?php if (empty($formattedPinnedPartners)): ?>
                <p class="text-center text-muted d-flex justify-content-center align-items-center w-100">No pinned chats
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="vh-100 my-auto overflow-auto body-fix-osahan-footer">
        <div class="rounded-4 shadow overflow-hidden bg-white m-3">
            <?php if (empty($allChats)): ?>
            <div class="no-posts-message" style="text-align: center; margin-top: 20px; color: #888;">
                <i class="fas fa-comments" style="font-size: 48px; color: #d3d3d3; margin-bottom: 10px;"></i>
                <h4>No Chats to Show Yet</h4>
                <p>Start a conversation with your colleagues to initiate communication!</p>
                <p>To begin, navigate to your <strong><a href="">Profile</a></strong> page, then visit the <strong><a
                            href="">Contacts</a></strong> section. <br> Once there, click on the chat icon to start a
                    conversation with your colleagues.</p>
            </div>
            <?php else: ?>
            <!-- Loop through the chat partners and display them -->
            <?php foreach ($allChats as $partner): ?>
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
            <div class="bg-white shadow-sm d-flex align-items-center gap-3 p-3 border-bottom position-relative">
                <div class="position-absolute end-0 top-0 p-2">
                    <button class="btn btn-link p-0" data-bs-toggle="modal"
                        data-bs-target="#chatOptionsModal<?= $partner['id']; ?>">
                        <i class="fas fa-ellipsis-h" style="font-weight: bold; font-size: 1.5em;"></i>
                    </button>
                </div>

                <a href="<?php echo BASE_URL . 'user/chat/inbox.php?id=' . $partner['id']; ?>"
                    class="d-flex align-items-center flex-grow-1 text-decoration-none">
                    <!-- Display partner's profile picture -->
                    <img src="<?= $partner['profileImage']; ?>" alt=""
                        class="img-fluid rounded-pill message-profile me-3">

                    <!-- Display unread message count if greater than 0 -->
                    <?php if ($unreadCount > 0): ?>
                    <span
                        class="badge bg-danger position-absolute translate-middle badge-message-count"><?= $unreadCount; ?></span>
                    <?php endif; ?>

                    <div class="flex-grow-1">
                        <!-- Partner's full name and latest message -->
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-1"><?= htmlspecialchars($partner['fullName']); ?></h6>
                        </div>
                        <p class="text-muted mb-0 text-truncate">
                            <?= htmlspecialchars($latestMessageContent); ?>
                        </p>
                    </div>
                </a>
            </div>

            <!-- Chat Options Modal -->
            <div class="modal fade" id="chatOptionsModal<?= $partner['id']; ?>" tabindex="-1"
                aria-labelledby="chatOptionsLabel<?= $partner['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="chatOptionsLabel<?= $partner['id']; ?>">Chat Options
                                (<?= htmlspecialchars($partner['fullName']); ?>)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="list-group">
                                <?php if (isset($partner['pinnedAt'])): ?>
                                <a href="" class="list-group-item list-group-item-action"
                                    onclick="unpinChat(<?= $partner['id']; ?>); return false;">
                                    <i class="fas fa-thumbtack pin-icon" style="margin-right: 13px;"></i> Unpin Chat
                                </a>
                                <?php else: ?>
                                <a href="" class="list-group-item list-group-item-action"
                                    onclick="pinChat(<?= $partner['id']; ?>); return false;">
                                    <i class="fas fa-thumbtack pin-icon" style="margin-right: 13px;"></i> Pin Chat
                                </a>
                                <?php endif; ?>

                                <a href="" class="list-group-item list-group-item-action text-danger"
                                    onclick="hideChat(<?= $partner['id']; ?>); return false;">
                                    <i class="fas fa-trash me-2"></i> Delete Chat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="pinChatToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Chat Pinned</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Chat has been successfully pinned.
        </div>
    </div>
    <div id="unpinChatToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Chat Unpinned</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Chat has been successfully unpinned.
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
    right: 59px;
    margin-top: 6px;
    position: fixed !important;
}

.avx-feedback-float-icon {
    display: none;
}

.chat-scroll {}

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
    max-height: calc(100vh - 150px);
    /* Adjust based on the height of the top section */
    overflow-y: auto;
    padding-bottom: 15px;
}

.pin-btn {
    position: relative;
    overflow: hidden;
    display: inline-block;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    color: #6c757d;
    border: none;
    background-color: transparent;
    padding: 0.5rem;
    border-radius: 50%;
    border-radius: 50%;
    border: 2px solid #00336696;
    box-sizing: border-box;
    transition: border-color 0.3s ease, transform 0.5s ease;
    /* Makes the button circular */
}

.pin-btn:hover {
    color: #0d6efd;
    box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
}

.pin-btn.pinned {
    transform: rotate(-45deg);
    color: #0d6efd;
    box-shadow: 0 0 10px rgba(13, 110, 253, 0.7);
}

.pin-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 2px solid transparent;
    box-sizing: border-box;
    transition: border-color 0.3s ease, transform 0.5s ease;
}

.pin-btn:hover::before {
    border-color: #0d6efd;
    transform: scale(1.2);
    /* Expands the border */
}

.pin-btn.pinned::before {
    border-color: #0d6efd;
    transform: scale(1.5);
}

.pin-icon {
    font-size: 1rem;
    transition: color 0.3s ease;
}

.pin-icon {
    transform: rotate(45deg);
    transition: transform 0.2s ease;
    /* Smooth transition over 0.2 seconds */
}


.notification-count {
    position: fixed !important;
}

div:where(.swal2-icon).swal2-warning {
    border-color: #000f1f!important;
    color: #041c34!important;
}
</style>

<script>
// Function to handle follow/unfollow actions
function toggleFollow(userId) {
    fetch('<?php echo BASE_URL; ?>user/follow_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                followingId: userId
            })
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

$(document).on('click', '.pin-btn', function(e) {
    e.preventDefault(); // Prevent default behavior
    e.stopPropagation(); // Stop event from bubbling up

    const chatId = $(this).data('chat-id');
    const isPinned = $(this).data('pinned') === 1;
    const $icon = $(this).find('.pin-icon');

    $.ajax({
        url: '/api/chat/toggle-pin',
        method: 'POST',
        data: {
            chat_id: chatId,
            pinned: !isPinned
        },
        success: function(response) {
            if (response.success) {
                if (isPinned) {
                    $icon.removeClass('text-primary');
                    $(this).data('pinned', 0);
                } else {
                    $icon.addClass('text-primary');
                    $(this).data('pinned', 1);
                }
                // Optionally refresh the chat list to update order
                // location.reload();
            }
        }.bind(this) // Bind this to maintain context
    });
});

function showToast(toastId) {
    var toastEl = document.getElementById(toastId);
    var toast = new bootstrap.Toast(toastEl);
    toast.show();
}

function pinChat(partnerId) {
    $.ajax({
        url: 'ajax_actions.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'pin_chat',
            partner_id: partnerId
        },
        success: function(response) {
            if (response.success) {
                showToast('pinChatToast');
                setTimeout(function() {
                    location.reload();
                }, 1500); // Delay reload to allow toast to be seen
            }
        },
        error: function() {
            location.reload();
        }
    });
}

function unpinChat(partnerId) {
    $.ajax({
        url: 'ajax_actions.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'unpin_chat',
            partner_id: partnerId
        },
        success: function(response) {
            if (response.success) {
                showToast('unpinChatToast');
                setTimeout(function() {
                    location.reload();
                }, 1500); // Delay reload to allow toast to be seen
            }
        },
        error: function() {
            location.reload();
        }
    });
}

function hideChat(partnerId) {
    Swal.fire({
        title: 'Confirm Deletion of Chat',
        html: `
            <p>Are you sure you want to delete this chat?</p>
            <div class="text-left mt-3">
                <p class="font-weight-bold mb-2">Important:</p>
                <ul class="text-left" style="list-style-type: disc; padding-left: 20px;">
                    <li>This action is irreversible.</li>
                    <li>All previous messages, files, and chat history will be permanently lost.</li>
                    <li>You will not be able to recover this chat once deleted.</li>
                </ul>
                <p class="mt-3">Please confirm if you wish to proceed with deleting this chat.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
        customClass: {
            popup: 'swal-wide',
            content: 'text-left'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax_actions.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'hide_chat',
                    partner_id: partnerId
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Chat has been deleted successfully.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Failed to delete chat. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'deleted successfully.',
                        icon: 'success',
                        timer: 1000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
}

// Add custom styles for the wide alert
const style = document.createElement('style');
style.textContent = `
    .swal-wide {
        width: 600px !important;
        max-width: 90% !important;
    }
    .text-left {
        text-align: left !important;
    }
    .swal2-html-container ul {
        margin-bottom: 0;
    }
    .swal2-html-container li {
        margin: 5px 0;
    }
`;
document.head.appendChild(style);

</script>
<?php include '../footer.php'; ?>