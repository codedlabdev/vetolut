<div class="appointment-upcoming d-flex flex-column">
<?php 
include 'inc/p_others.php';

require_once BASE_DIR . 'lib/dhu.php'; // Include db & helper functions
require_once BASE_DIR . 'lib/user/table.php'; // Include helper functions

$contactList = getAllContacts($loggedInUserId); // Fetch all contacts for the contact list

// Get the post ID from the URL
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($post_id > 0) {
    // Fetch the post data along with user details based on the ID
    $query = "SELECT p.*, u.f_name, u.l_name, u.username, u.image AS profile_image 
              FROM network_post p 
              JOIN users u ON p.user_id = u.id 
              WHERE p.id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo "Post not found!";
        exit;
    }

	  // Check if there is a profile image and convert to base64 if it exists
	$profileImage = !empty($post['profile_image']) 
		? 'data:image/jpeg;base64,' . base64_encode($post['profile_image']) 
		: BASE_URL . 'assets/user/img/noimage.png'; // Placeholder if no image

} else {
    echo "Invalid post ID!";
    exit;
}

// Get the current like count for this post
$likeCount = getLikeCount($post_id);

// Check if the logged-in user has already liked this post
$isLiked = isLikedByUser($post_id, $loggedInUserId) ? 'liked' : ''; // Apply the "liked" class if already liked

// Query to get the number of comments for this post
$query = "SELECT COUNT(*) FROM network_comment WHERE post_id = :post_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT); // Consistently use $post_id
$stmt->execute();
$commentCount = $stmt->fetchColumn();

// Fetch comments if they exist
if ($commentCount > 0) {
    // Query to get all comments for this post
    $query = "SELECT c.*, u.f_name, u.l_name, u.username, u.image AS profile_image, c.created_at 
              FROM network_comment c 
              JOIN users u ON c.user_id = u.id 
              WHERE c.post_id = :post_id 
              ORDER BY c.created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT); // Consistently use $post_id
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $comments = [];
}

// Get the login user image data as a base64 string
$imageData = getUserProfileImage($loggedInUserId);
?>


<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/user/css/feed.css">
 <!-- Begin emoji-picker Stylesheets -->
    <link href="<?php echo BASE_URL; ?>vendors/emoji/lib/css/emoji.css" rel="stylesheet">
    <!-- End emoji-picker Stylesheets -->
 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">


        <div class="container">
         <!-- Dashboard -->




 <div class="container">
        <div class="posts-container">
            <div class="post">
                <!-- Post Header -->
                <div class="post-header">
                    <!-- Profile image of the post owner -->
                    <a href="user_profile.php?id=<?php echo $post['user_id']; ?>" class="name-link">   
                        <img src="<?php echo $profileImage; ?>" alt="User Profile" class="profile-image">
                    </a>
                    <div>
                        <a href="user_profile.php?id=<?php echo $post['user_id']; ?>" class="name-link">
                            <!-- Display full name or username -->
                            <div class="post-user"><?php echo htmlspecialchars($post['f_name'] . ' ' . $post['l_name']); ?></div>
                        </a>
                        <div class="post-time"><?php echo date('F j, Y, g:i a', strtotime($post['date'])); ?></div>
                    </div>
                    <!-- Post actions for the owner -->
                    <div class="post-actions">
                        <?php if ($post['user_id'] == $loggedInUserId): ?>
                            <a href="<?php echo BASE_URL; ?>user/network.php?id=<?php echo $post['id']; ?>"
                            class="edit-icon" title="Edit Post">
                            <i class="fas fa-edit"></i>
							</a>
                            <a href="javascript:void(0);" class="delete-icon" title="Delete Post" onclick="confirmDelete(<?php echo $post['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

               <!-- Post Content -->
				<div class="post-content">
					<p><?php echo nl2br(htmlspecialchars($post['text'])); ?></p>
					<?php if ($post['photo']): ?>
						<img src="<?php echo BASE_URL . htmlspecialchars($post['photo']); ?>" alt="Post Image" class="post-img">
					<?php endif; ?>
				</div>

               <!-- Post Footer with Like, Comment, and Share buttons -->
				<div class="post-footer">
					<div>
						<button id="like-button-<?php echo $post['id']; ?>" class="like-button <?php echo $isLiked; ?>" onclick="toggleLike(<?php echo $post['id']; ?>)">
							<i class="fas fa-thumbs-up"></i>
							<span id="like-count-<?php echo $post['id']; ?>"><?php echo $likeCount; ?></span>&nbsp; Likes
						</button>
					</div>
					<div>						 
							<i class="fas fa-comment-alt"></i>
							 <span><?php echo getCommentCount($post['id']); ?> Comments</span>
					</div>
					<div class="action-btn">
						<i class="fas fa-share"></i>
						<span>Share</span>
					</div>
				</div>
            </div>
        </div>

 
<!-- Comments Section -->
<div class="comments-section">
    <?php if (count($comments) === 0): ?>
        <!-- Display message if no comments available -->
        <div class="no-posts-message" style="text-align: center; margin-top: 20px; color: #888;">
            <i class="fas fa-camera-retro" style="font-size: 48px; color: #d3d3d3; margin-bottom: 10px;"></i>
            <h4>No comment to show yet</h4>
            <p>Be the first to comment something with others!</p>
        </div>
    <?php else: ?>
        <!-- Loop through comments and display each one -->
        <?php foreach ($comments as $comment): ?>
		 <div class="post">
            <div class="comment">
                <!-- Display user profile image with a placeholder if missing -->
                <img src="<?php echo !empty($comment['profile_image']) 
                            ? 'data:image/jpeg;base64,' . base64_encode($comment['profile_image']) 
                            : 'https://via.placeholder.com/35'; ?>" 
                     alt="user-profile">
                <div class="comment-content">
                    <div class="comment-header">
                        <div class="comment-user"><?php echo htmlspecialchars($comment['f_name']) . ' ' . htmlspecialchars($comment['l_name']); ?></div>
                        <div class="comment-time"><?php echo timeAgo($comment['created_at']); ?></div>
                        
						 <!-- Show delete button only if user is the comment owner -->
                        <?php if ($comment['user_id'] === $loggedInUserId): ?>
                            <span class="delete-btn" onclick="deleteComment(<?php echo $comment['id']; ?>)">
                                <i class="fas fa-trash-alt"></i>
                            </span>
                        <?php endif; ?>
						
						
                    </div>
                   <p><?php echo nl2br(htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8')); ?></p>
                </div>
            </div> </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
		 




<!-- If there are posts -->

  
 <div class="comment-input-box">
    <!-- Sticky Comment Input Box -->
    <img src="<?php echo $imageData; ?>" alt="User Profile" class="profile-image">
    
    <!-- Textarea with 'required' attribute -->
    <textarea id="comment-textarea" class="form-control textarea-control" rows="3" placeholder="Write a comment..." data-emojiable="true" data-emoji-input="unicode" required></textarea>
    
    <div class="icons">
        <!-- Send Button Icon with onclick -->
        <a href="javascript:void(0);" onclick="postComment()">
            <i class="fas fa-paper-plane" style="margin-right: 20px; cursor: pointer;"></i>
        </a>
    </div>
</div>


	
	
</div>

	
		
        </div>

        
   <style>
   
   .emoji-menu {
    position: absolute;
    right: 0;
    z-index: 999;
    width: unset!important;
    overflow: hidden;
    border: 1px #dfdfdf solid;
    border-radius: 3px;
    overflow: hidden;
    box-shadow: 0px 1px 1px rgba(0, 0, 0, .1);
    margin-top: -284px!important;
    height: 195px!important;
}

.emoji-picker-icon {
    cursor: pointer;
    position: absolute;
    right: 80px;
    top: 15px;
    font-size: 20px !important;
    opacity: .7;
    z-index: 100;
    transition: none;
    color: #000;
    -moz-user-select: none;
    -webkit-user-select: none;
    -o-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
   </style>

<?php
include 'footer.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


 <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

    <!-- Begin emoji-picker JavaScript -->
    <script src="<?php echo BASE_URL; ?>vendors/emoji/lib/js/config.min.js"></script>
    <script src="<?php echo BASE_URL; ?>vendors/emoji/lib/js/util.min.js"></script>
    <script src="<?php echo BASE_URL; ?>vendors/emoji/lib/js/jquery.emojiarea.min.js"></script>
    <script src="<?php echo BASE_URL; ?>vendors/emoji/lib/js/emoji-picker.min.js"></script>
    <!-- End emoji-picker JavaScript -->

<script>

 

const BASE_URL = "<?php echo BASE_URL; ?>";

let offset = 10; // Start from the next set after the first 10 posts

function loadMorePosts() {
    const loadMoreButton = document.getElementById("load-more");

    // Disable the button temporarily to prevent multiple clicks
    loadMoreButton.disabled = true;
    loadMoreButton.innerText = "Loading...";

    // Send AJAX request to fetch more posts
    const xhr = new XMLHttpRequest();
    xhr.open("GET", BASE_URL + "user/fetch_posts.php?limit=10&offset=" + offset, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const newPosts = xhr.responseText;

            if (newPosts.trim()) {
                // Append new posts to the post container
                document.querySelector('.posts-container').innerHTML += newPosts;
                
                // Increase the offset for the next batch
                offset += 10;

                // If fewer than 10 posts are returned, display "No more posts" and disable the button
                const postCount = (newPosts.match(/class="post"/g) || []).length;
                if (postCount < 10) {
                    loadMoreButton.innerText = "Thats all for now";
                    loadMoreButton.disabled = true;
                } else {
                    // Re-enable the button for the next click
                    loadMoreButton.disabled = false;
                    loadMoreButton.innerText = "Load More";
                }
            } else {
                // No posts returned, set button text to "No more posts" and disable it
                loadMoreButton.innerText = "No posts";
                loadMoreButton.disabled = true;
            }
        }
    };
    xhr.send();
}


function toggleLike(postId) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", BASE_URL + "user/toggle_like.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            const likeButton = document.querySelector(`#like-button-${postId}`);
            const likeCount = document.querySelector(`#like-count-${postId}`);

            if (response.success) {
                // Toggle liked class based on response
                likeButton.classList.toggle("liked", response.isLiked);
                likeCount.textContent = response.likeCount;
            } else {
                alert("Error toggling like");
            }
        }
    };

    // Send post ID in request body
    xhr.send(`post_id=${postId}`);
}


function postComment() {
    const commentText = document.getElementById("comment-textarea").value;
    const postId = "<?php echo $post_id; ?>"; // Pass post_id to JavaScript

    if (commentText === "") {
        Swal.fire({
            title: 'Required!',
            text: "Please write a comment before posting.",
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Okay'
        });
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "<?php echo BASE_URL; ?>user/submit_comment.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
               
                            location.reload(); // Reload the page
            
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: "Error posting comment. Please try again!",
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Okay'
                });
            }
        } else {
            Swal.fire({
                title: 'Error!',
                text: "Something went wrong. Please try again later.",
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Okay'
            });
        }
    };

    xhr.send(`comment=${encodeURIComponent(commentText)}&post_id=${encodeURIComponent(postId)}`);
}






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




function confirmDelete(postId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?php echo BASE_URL; ?>lib/user/network_p.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        delete_post_id: postId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Your post has been deleted.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload(); // Reload the page
                        });
                    } else {
                        Swal.fire('Error!', 'Failed to delete the post.', 'error');
                    }
                });
        }
    });
}

function deleteComment(commentId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "<?php echo BASE_URL; ?>user/delete_comment.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Your comment has been deleted.',
                            icon: 'success',
                            confirmButtonText: 'Okay'
                        }).then(() => {
                            location.reload(); // Reload the page after deletion
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                } else {
                    Swal.fire('Error!', 'Failed to delete comment. Please try again later.', 'error');
                }
            };

            xhr.send(`comment_id=${encodeURIComponent(commentId)}`);
        }
    });
}




$(function() {
        // Initializes and creates emoji set from sprite sheet
        window.emojiPicker = new EmojiPicker({
          emojiable_selector: '[data-emojiable=true]',
          assetsPath: '<?php echo BASE_URL; ?>vendors/emoji/lib/img/',
          popupButtonClasses: 'fa fa-smile-o'
        });
        // Finds all elements with `emojiable_selector` and converts them to rich emoji input fields
        // You may want to delay this step if you have dynamically created input fields that appear later in the loading process
        // It can be called as many times as necessary; previously converted input fields will not be converted again
        window.emojiPicker.discover();
      });

</script>