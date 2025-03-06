<?php
// fetch_posts.php
session_start();

define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
require_once BASE_DIR . 'lib/dhu.php'; // Include helper functions
require_once BASE_DIR . 'lib/user/table.php'; // Include helper functions

// Get parameters from AJAX
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Fetch posts based on limit and offset
$posts = fetchPosts($limit, $offset);

// Check if there are posts
if (empty($posts)) {
    echo ''; // No more posts to load
} else {
    foreach ($posts as $post):
        $imageData = !empty($post['image']) 
            ? 'data:image/jpeg;base64,' . base64_encode($post['image'])
            : BASE_URL . 'assets/user/img/noimage.png';
        $isLiked = isLikedByUser($post['id'], $_SESSION['user_id']) ? 'liked' : ''; // Check if the user has liked the post
        ?>
        <div class="post loaded"> <!-- 'loaded' class to skip skeleton -->
            <!-- Post Header -->
            <div class="post-header">
                <a href="user_profile?id=<?php echo urlencode($post['user_id']); ?>" class="name-link">   
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="User Image" class="img-fluid" style="border-radius: 50%;">
                </a>
                <div>
                    <a href="user_profile?id=<?php echo urlencode($post['user_id']); ?>" class="name-link">
                        <div class="post-user"><?php echo getUserFullName($post); ?></div>
                    </a>
                    <div class="post-time"><?php echo timeAgo($post['date']); ?></div>
                </div>
                <?php if ($post['user_id'] === $_SESSION['user_id']): ?>
                <div class="post-actions">
                    <a href="<?php echo BASE_URL; ?>user/network.php?id=<?php echo $post['id']; ?>" class="edit-icon" title="Edit Post">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="javascript:void(0);" class="delete-icon" title="Delete Post" onclick="confirmDelete(<?php echo $post['id']; ?>)">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Post Content -->
            <div class="post-content">
                <p><?php echo truncateText(htmlspecialchars($post['text'])); ?></p>
                <?php if (!empty($post['photo'])): ?>
                    <img src="<?php echo BASE_URL . $post['photo']; ?>" alt="Post Image" class="post-img">
                <?php endif; ?>
            </div>

            <!-- Post Footer -->
            <div class="post-footer">
                <div>
                    <a id="like-button-<?php echo $post['id']; ?>" class="like-button <?php echo $isLiked; ?>" onclick="toggleLike(<?php echo $post['id']; ?>)">
                        <i class="fas fa-thumbs-up"></i>
                        <span id="like-count-<?php echo $post['id']; ?>"><?php echo getLikeCount($post['id']); ?></span>&nbsp; Likes
                    </a>
                </div>
                <div>
                   <a href="<?php echo BASE_URL; ?>user/post_list.php?id=<?php echo $post['id']; ?>">
                    <i class="fas fa-comment-alt"></i>
                     <span><?php echo getCommentCount($post['id']); ?> Comments</span>
                </a>
                </div>
                <div class="action-btn">
                    <i class="fas fa-share"></i>
                    <span>Share</span>
                </div>
            </div>
        </div>
    <?php endforeach;
}
?>
