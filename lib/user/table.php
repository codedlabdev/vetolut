<?php
// lib/user/table

$userId = $_SESSION['user_id'];

// Get the logged-in user ID from the session
$loggedInUserId = $_SESSION['user_id'] ?? null;

// Check if an ID is provided in the URL
$userId = isset($_GET['id']) ? $_GET['id'] : $loggedInUserId;

// Get the user's data including last_active
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Update the last_active timestamp for the logged-in user only if a user is logged in
if ($loggedInUserId) {
    $updateStmt = $pdo->prepare("UPDATE users SET last_active = NOW() WHERE id = :id");
    $updateStmt->execute(['id' => $loggedInUserId]);
}

// Check if the user is online (active within the past 1 minute)
$onlineStatus = false;
if ($user && isset($user['last_active'])) {
    if (strtotime($user['last_active']) > (time() - 60)) { // Change to 60 seconds
        $onlineStatus = true;
    }
}

// Convert the user's image BLOB data to a base64 string
// Handle user image
$imageData = '';
if (!empty($user['image'])) {
    if (strpos($user['image'], '/') !== false) {
        // Image upload Available
        $imageData = BASE_URL . $user['image'];
    } else {
        // No image Upload, use default placeholder
        $imageData = BASE_URL . 'assets/user/img/noimage.png';
    }
}

// Fetch up to 5 random users excluding the logged-in user and users already followed
function getSuggestedUsers($loggedInUserId, $limit = 5)
{
    $pdo = getDBConnection();
    $sql = "SELECT u.id, u.f_name, u.l_name, u.profession, u.image 
            FROM users u
            LEFT JOIN network_followers nf ON u.id = nf.following_id AND nf.follower_id = :user_id
            WHERE u.id != :user_id
            AND nf.following_id IS NULL  -- Exclude users that the logged-in user is following
            ORDER BY RAND()
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $loggedInUserId, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Fetch all users excluding the logged-in user and users already followed
function getAllContacts($loggedInUserId)
{
    $pdo = getDBConnection();
    $sql = "SELECT u.id, u.f_name, u.l_name, u.profession, u.image 
            FROM users u
            LEFT JOIN network_followers nf ON u.id = nf.following_id AND nf.follower_id = :user_id
            WHERE u.id != :user_id
            AND nf.following_id IS NULL  -- Exclude users that the logged-in user is following
            ORDER BY RAND()";  // Random order for contact list

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $loggedInUserId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


//user/table.php
// Fetch posts with pagination

// Update fetchPosts function image handling
function fetchPosts($limit = 10, $offset = 0)
{
    try {
        $pdo = getDBConnection();
        $sql = "SELECT p.*, COUNT(pi.image) AS image_count, u.username, u.f_name, u.l_name, u.image 
                FROM network_post p
                LEFT JOIN network_post_imgs pi ON p.id = pi.network_post_id
                JOIN users u ON p.user_id = u.id 
                GROUP BY p.id
                ORDER BY p.date DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Loop through posts and handle image data
        foreach ($posts as &$post) {
            if (!empty($post['image'])) {
                if (strpos($post['image'], '/') !== false) {
                    // It's a file path
                    $post['image'] = BASE_URL . $post['image'];
                } else {
                    // It's a BLOB, use default image
                    $post['image'] = BASE_URL . 'assets/user/img/noimage.png';
                }
            } else {
                // If there's no image, use default placeholder
                $post['image'] = BASE_URL . 'assets/user/img/noimage.png';
            }
        }

        return $posts;
    } catch (PDOException $e) {
        // Handle errors and exceptions (e.g., connection issues)
        error_log("Database error: " . $e->getMessage());
        return []; // Return an empty array on error
    }
}



function fetchFilteredPosts($limit = 10, $offset = 0)
{
    try {
        $pdo = getDBConnection();

        // SQL query: select posts that have 5 or more likes or 5 or more comments, randomly ordered
        $sql = "
            SELECT p.*, u.username, u.f_name, u.l_name, u.image, 
                COUNT(l.id) AS like_count, 
                COUNT(c.id) AS comment_count
            FROM network_post p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN network_likes l ON p.id = l.post_id
            LEFT JOIN network_comment c ON p.id = c.post_id
            GROUP BY p.id
            HAVING (like_count >= 5 OR comment_count >= 5)
            ORDER BY RAND()
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Loop through posts and handle image data
        foreach ($posts as &$post) {
            if (!empty($post['image'])) {
                if (strpos($post['image'], '/') !== false) {
                    // It's a file path
                    $post['image'] = BASE_URL . $post['image'];
                } else {
                    // It's a BLOB, use default image
                    $post['image'] = BASE_URL . 'assets/user/img/noimage.png';
                }
            } else {
                // If there's no image, use default placeholder
                $post['image'] = BASE_URL . 'assets/user/img/noimage.png';
            }
        }

        return $posts;
    } catch (PDOException $e) {
        // Handle errors and exceptions (e.g., connection issues)
        error_log("Database error: " . $e->getMessage());
        return []; // Return an empty array on error
    }
}



/// Function to get the total comment count for a post
function getCommentCount($postId)
{
    $pdo = getDBConnection(); // Assuming you have a function to get DB connection
    $query = "SELECT COUNT(*) FROM network_comment WHERE post_id = :post_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn(); // Return the comment count
}


function getUserFullName($user)
{
    // Check if username is set and not empty
    if (!empty($user['username'])) {
        return $user['username'];
    } else {
        // If username is not set, return the full name
        return htmlspecialchars($user['f_name'] . ' ' . $user['l_name']);
    }
}


function truncateText($text, $wordLimit = 10)
{
    $words = explode(' ', $text);
    if (count($words) > $wordLimit) {
        $truncated = implode(' ', array_slice($words, 0, $wordLimit)) . '&nbsp;...';
        return $truncated;
    }
    return $text;
}


function timeAgo($datetime)
{
    $timestamp = strtotime($datetime);
    $current_time = time();
    $time_difference = $current_time - $timestamp;

    $seconds = $time_difference;
    $minutes = round($seconds / 60);           // value 60 is seconds
    $hours   = round($seconds / 3600);         // value 3600 is 60 minutes * 60 sec
    $days    = round($seconds / 86400);        // value 86400 is 24 hours * 60 minutes * 60 sec
    $weeks   = round($seconds / 604800);       // value 604800 is 7 days * 24 hours * 60 minutes * 60 sec
    $months  = round($seconds / 2629440);      // value 2629440 is ((365+365+365+365+366)/5/12) days * 24 hours * 60 minutes * 60 sec
    $years   = round($seconds / 31553280);     // value 31553280 is ((365+365+365+365+366)/5) days * 24 hours * 60 minutes * 60 sec

    if ($seconds <= 60) {
        return "Just now";
    } else if ($minutes <= 60) {
        return ($minutes == 1) ? "1 minute ago" : "$minutes minutes ago";
    } else if ($hours <= 24) {
        return ($hours == 1) ? "1 hour ago" : "$hours hours ago";
    } else if ($days <= 7) {
        return ($days == 1) ? "1 day ago" : "$days days ago";
    } else if ($weeks <= 4.3) {
        return ($weeks == 1) ? "1 week ago" : "$weeks weeks ago";
    } else if ($months <= 12) {
        return ($months == 1) ? "1 month ago" : "$months months ago";
    } else {
        return ($years == 1) ? "1 year ago" : "$years years ago";
    }
}

// Add a like to a post
function addLike($postId, $userId)
{
    $pdo = getDBConnection();
    $sql = "INSERT INTO network_likes (post_id, user_id) VALUES (:post_id, :user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    return $stmt->execute();
}

// Remove a like from a post
function removeLike($postId, $userId)
{
    $pdo = getDBConnection();
    $sql = "DELETE FROM network_likes WHERE post_id = :post_id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    return $stmt->execute();
}

// Get the like count for a post
function getLikeCount($postId)
{
    $pdo = getDBConnection();
    $sql = "SELECT COUNT(*) FROM network_likes WHERE post_id = :post_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Check if a user has liked a specific post
function isLikedByUser($postId, $userId)
{
    $pdo = getDBConnection();
    $sql = "SELECT COUNT(*) FROM network_likes WHERE post_id = :post_id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

//table.php

function followUser($followerId, $followingId)
{
    $pdo = getDBConnection();
    $sql = "INSERT IGNORE INTO network_followers (follower_id, following_id) VALUES (:follower_id, :following_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'follower_id' => $followerId,
        'following_id' => $followingId
    ]);
    return $stmt->rowCount() > 0; // Returns true if a new follow relationship was created
}


function unfollowUser($followerId, $followingId)
{
    $pdo = getDBConnection();
    $sql = "DELETE FROM network_followers WHERE follower_id = :follower_id AND following_id = :following_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'follower_id' => $followerId,
        'following_id' => $followingId
    ]);
    return $stmt->rowCount() > 0; // Returns true if the relationship was removed
}

function isFollowing($followerId, $followingId)
{
    $pdo = getDBConnection();
    $sql = "SELECT COUNT(*) FROM network_followers WHERE follower_id = :follower_id AND following_id = :following_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'follower_id' => $followerId,
        'following_id' => $followingId
    ]);
    return $stmt->fetchColumn() > 0;
}


function getFollowerCount($userId)
{
    $pdo = getDBConnection();
    $sql = "SELECT COUNT(*) FROM network_followers WHERE following_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    return (int) $stmt->fetchColumn();
}

function getFollowingCount($userId)
{
    $pdo = getDBConnection();
    $sql = "SELECT COUNT(*) FROM network_followers WHERE follower_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    return (int) $stmt->fetchColumn();
}

// Function to get users that the logged-in user is following or who are following the logged-in user
function getFollowedUsers($loggedInUserId)
{
    $pdo = getDBConnection();

    // Get users the logged-in user is following
    $stmt = $pdo->prepare("
        SELECT users.id, users.f_name, users.l_name, users.profession, users.image
        FROM users
        JOIN network_followers ON users.id = network_followers.following_id
        WHERE network_followers.follower_id = :user_id
    ");
    $stmt->execute(['user_id' => $loggedInUserId]);
    $following = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get users who are following the logged-in user
    $stmt = $pdo->prepare("
        SELECT users.id, users.f_name, users.l_name, users.profession, users.image
        FROM users
        JOIN network_followers ON users.id = network_followers.follower_id
        WHERE network_followers.following_id = :user_id
    ");
    $stmt->execute(['user_id' => $loggedInUserId]);
    $followers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Combine both lists and return
    return ['following' => $following, 'followers' => $followers];
}

function addNotification($recipientId, $senderId, $type, $referenceId, $message = '')
{
    $pdo = getDBConnection();

    $sql = "INSERT INTO notifications (recipient_id, sender_id, type, reference_id, message)
            VALUES (:recipient_id, :sender_id, :type, :reference_id, :message)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'recipient_id' => $recipientId,
        'sender_id' => $senderId,
        'type' => $type,
        'reference_id' => $referenceId,
        'message' => $message
    ]);

    return $pdo->lastInsertId();  // Return the notification ID
}


function getNotifications($userId, $unreadOnly = true)
{
    $pdo = getDBConnection();
    $sql = "SELECT n.*, u.f_name AS sender_f_name, u.l_name AS sender_l_name, u.image AS sender_image
            FROM notifications n
            JOIN users u ON n.sender_id = u.id
            WHERE n.recipient_id = :user_id";

    if ($unreadOnly) {
        $sql .= " AND n.is_read = 0";
    }

    $sql .= " ORDER BY n.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function markNotificationAsRead($notificationId)
{
    $pdo = getDBConnection();
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = :notification_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['notification_id' => $notificationId]);
    return $stmt->rowCount() > 0;
}

// Get the count of unread notifications for a specific user
function getUnreadNotificationCount($userId)
{
    $pdo = getDBConnection();
    $sql = "SELECT COUNT(*) FROM notifications WHERE recipient_id = :user_id AND is_read = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    return (int) $stmt->fetchColumn();
}

// Mark notifications as read for a specific user
function markNotificationsAsRead($userId)
{
    $pdo = getDBConnection();
    $sql = "UPDATE notifications SET is_read = 1 WHERE recipient_id = :user_id AND is_read = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
}

function getPostOwnerId($postId)
{
    $pdo = getDBConnection();
    // Change 'posts' to 'network_post' to match your table name
    $stmt = $pdo->prepare("SELECT user_id FROM network_post WHERE id = :post_id");
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['user_id'] ?? null;  // Return user ID of the post owner or null if not found
}


function getUserProfileImage($userId)
{
    global $pdo; // Access the PDO instance for database operations

    // Query to get user image data from the database
    $query = "SELECT image FROM users WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if image data exists and convert to base64, otherwise return default image
    if (!empty($user['image'])) {
        return 'data:image/jpeg;base64,' . base64_encode($user['image']);
    } else {
        return BASE_URL . 'assets/user/img/noimage.png';
    }
}


// Fetch random AI chats
function fetchRandomAIChats($limit = 4)
{
    global $pdo;
    try {
        $query = "SELECT DISTINCT a1.chat_id, a1.title, 
                    (SELECT response FROM ai_chat a2 
                     WHERE a2.chat_id = a1.chat_id 
                     ORDER BY created_at DESC 
                     LIMIT 1) as content,
                    a1.created_at,
                    u.id as user_id,
                    u.username,
                    u.f_name,
                    u.l_name,
                    u.image as user_image
                 FROM ai_chat a1
                 JOIN users u ON a1.user_id = u.id
                 WHERE a1.title IS NOT NULL 
                   AND a1.title != ''
                   AND TRIM(a1.title) != ''
                 GROUP BY a1.chat_id
                 ORDER BY RAND() 
                 LIMIT :limit";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching random AI chats: " . $e->getMessage());
        return [];
    }
}

function getLoggedInUserName()
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $pdo = getDBConnection();
    $sql = "SELECT f_name, l_name FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        return [
            'f_name' => $user['f_name'],
            'l_name' => $user['l_name']
        ];
    }
    return null;
}
