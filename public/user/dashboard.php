<?php
//public/user/dashbaod.php

include 'header.php';
// Fetch 4 random AI chats
$randomChats = fetchRandomAIChats(4);
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/user/css/feed.css">

<div class="home d-flex flex-column vh-100">
    <?php
    include 'inc/top_head.php';
    ?>

    <div class="vh-100 my-auto overflow-auto body-fix-osahan-footer">

        <!-- Booking -->
        <!-- Booking -->
        <!-- Doctors -->
        <!-- Doctors -->

        <div class="mb-3">
            <!-- Top Doctors -->
            <!-- Top Doctors -->
        </div>

        <!-- Available Doctors -->
        <!-- Available Doctors -->


        <div class="container">
            <div class="post">
                <div class="ai-interaction">
                    <h4>AI-Interaction</h4>
                    <div class="input-container">
                        <i class="far fa-comment-alt"></i>
                        <input type="text" placeholder="Ask your veterinary question here..." id="ai-input" readonly
                            onclick="window.location.href='<?php echo BASE_URL; ?>public/user/ai_chat/';">
                        <i class="fas fa-headphones"></i>
                        <i class="fas fa-microphone"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">


            <!-- AI Tabs Section -->
            <div class="tabs" id="ai-tabs">
                <div class="tab active" data-target="#ai-interactions">Recent AI Interactions</div>
                <div class="tab" data-target="#specialist-opinions">Recent Specialist Opinions</div>
            </div>

            <div class="tab-content active" id="ai-interactions">
                <div class="recent-updates">
                    <!-- AI Interaction Cards -->


                    <?php
                    // Display the AI chat cards
                    foreach ($randomChats as $chat):
                    ?>
                        <div class="card">
                            <div class="card-content">

                                <div class="post-header" style="padding: 10px;">
                                    <a href="user_profile?id=<?php echo urlencode($chat['user_id']); ?>" class="name-link">
                                        <img src="<?php echo getUserProfileImage($chat['user_id']); ?>"
                                            alt="<?php echo $chat['username'] ? htmlspecialchars($chat['username']) : 'User Profile'; ?>"
                                            style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                                    </a>
                                    <div>
                                        <a href="user_profile?id=<?php echo urlencode($chat['user_id']); ?>" class="name-link">
                                            <div class="post-user" style="font-size: small;"><?php echo getUserFullName($chat); ?></div>
                                        </a>
                                        <div class="post-time"><?php echo timeAgo($chat['created_at']); ?></div>
                                    </div>

                                </div>
                                <h3><?php echo htmlspecialchars(substr($chat['title'], 0, 40)) . '...'; ?></h3>
                                <p><?php echo htmlspecialchars(substr($chat['content'], 0, 100)) . '...'; ?></p>


                            </div>
                            <div class="card-buttons">
                                <button class="follow-btn" onclick="window.location.href='view_chat.php?id=<?php echo $chat['chat_id']; ?>'">View</button>
                            </div>
                        </div>
                    <?php endforeach; ?>


                </div>
            </div>

            <div class="tab-content" id="specialist-opinions">
                <div class="recent-updates">
                    <!-- Specialist Opinion Cards -->
                    <div class="card">
                        <img src="https://placedog.net/350/200?random=<?php echo time() . rand(1001, 2000); ?>" alt="Veterinary Image" style="width: 350px; height: 200px; object-fit: cover;">
                        <div class="card-content">
                            <h3>Specialist Opinion on Healthcare Innovations</h3>
                            <p>Analysis of the latest healthcare technologies shaping the future.</p>
                            <p class="date" style=" text-align: right; margin-top: 20px;"><i>Date: 2023-11-0</i></p>
                        </div>
                        <div class="card-buttons">
                            <button class="follow-btn">View</button>
                        </div>
                    </div>


                    <div class="card">
                        <img src="https://placedog.net/350/200?random=<?php echo time() . rand(2001, 3000); ?>" alt="Veterinary Image" style="width: 350px; height: 200px; object-fit: cover;">
                        <div class="card-content">
                            <h3>Specialist Opinion on Healthcare Innovations</h3>
                            <p>Analysis of the latest healthcare technologies shaping the future.</p>
                            <p class="date" style=" text-align: right; margin-top: 20px;"><i>Date: 2023-11-0</i></p>
                        </div>
                        <div class="card-buttons">
                            <button class="follow-btn">View</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <?php

        // Dashboard

        $limit = 10;
        $offset = 0; // Starting point for the first set of posts
        $filter = 'likes'; // Set the filter to either 'likes' or 'comments'
        $posts = fetchFilteredPosts($limit, $offset, $filter); // Get the first 10 posts with filter
        ?>


        <div class="container network-section">

            <!-- Network Tabs Section -->
            <div class="network-tabs">
                <div class="network-tab active" data-target="#network-updates">Network Updates</div>
                <div class="network-tab" data-target="#job-postings"><i class="fas fa-briefcase"></i>
                    Job</div>
                <div class="network-tab" data-target="#professional-networks"> <i class="fas fa-users"></i>
                    Network</div>
            </div>




            <div class="posts-container">
                <div class="network-tab-content active" id="network-updates">

                    <?php if (empty($posts)): ?>
                        <div class="no-posts-message" style="text-align: center; margin-top: 20px; color: #888;">
                            <i class="fas fa-camera-retro" style="font-size: 48px; color: #d3d3d3; margin-bottom: 10px;"></i>
                            <h4>No Most Popular or Recent Post from Colleagues to show yet</h4>

                        </div>
                    <?php else: ?>


                        <div class="recent-updates">

                            <?php foreach ($posts as $post):
                                $isLiked = isLikedByUser($post['id'], $_SESSION['user_id']) ? 'liked' : ''; // Check if the user has liked the post
                            ?>

                                <!-- Network Update Cards -->
                                <div class="card">
                                    <div class="post-header" style="padding: 10px;">
                                        <a href="user_profile?id=<?php echo urlencode($post['user_id']); ?>" class="name-link">
                                            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="User Image"
                                                class="img-fluid" style="border-radius: 50%;">
                                        </a>
                                        <div>
                                            <a href="user_profile?id=<?php echo urlencode($post['user_id']); ?>" class="name-link">
                                                <div class="post-user" style="font-size: small;"><?php echo getUserFullName($post); ?></div>
                                            </a>
                                            <div class="post-time"><?php echo timeAgo($post['date']); ?></div>
                                        </div>

                                    </div>
                                    <a href="<?php echo BASE_URL; ?>user/post_list.php?id=<?php echo $post['id']; ?>">
                                        <img src="<?php echo BASE_URL . $post['photo']; ?>" alt="Network Update">
                                    </a>
                                    <div class="card-content" style="margin-bottom: 40px;">
                                        <!--<h3>Network Expansion in Tech Industry</h3>-->
                                        <p><?php echo truncateText(htmlspecialchars($post['text'])); ?></p>

                                    </div>


                                    <div class="card-buttons" style="bottom: 0;position: absolute;width: 100%;">
                                        <a id="like-button-<?php echo $post['id']; ?>" style="padding: unset;"
                                            class="like-button <?php echo $isLiked; ?>"
                                            onclick="toggleLike(<?php echo $post['id']; ?>)">
                                            <i class="fas fa-thumbs-up"></i>
                                            <span
                                                id="like-count-<?php echo $post['id']; ?>"><?php echo getLikeCount($post['id']); ?></span>&nbsp;

                                        </a>
                                        <a href="<?php echo BASE_URL; ?>user/post_list.php?id=<?php echo $post['id']; ?>">
                                            <button><i class="fas fa-comment"></i><?php echo getCommentCount($post['id']); ?></button>
                                        </a>
                                        <button><i class="fas fa-share"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
                <?php endif; ?>


                <div class="suggestions-card">
                    <div class="suggestions-header">
                        <p class="suggestions-header-title">Colleague Suggestions</p>
                    </div>

                    <?php

                    // Fetch suggested users for the dashboard
                    $suggestedUsers = getSuggestedUsers($loggedInUserId);
                    foreach ($suggestedUsers as $user):
                        $imageData = !empty($user['image'])
                            ? (strpos($user['image'], '/') !== false 
                                ? BASE_URL . $user['image'] 
                                : BASE_URL . 'assets/user/img/noimage.png')
                            : BASE_URL . 'assets/user/img/noimage.png';
                    ?>
                        <div class="suggestion">
                            <img src="<?php echo htmlspecialchars($imageData); ?>"
                                alt="<?php echo htmlspecialchars($user['f_name'] . ' ' . $user['l_name']); ?>"
                                class="profile-pic">
                            <div class="suggestion-info">
                                <!-- Link to user profile page -->
                                <a href="user_profile?id=<?php echo urlencode($user['id']); ?>" class="name-link">
                                    <p class="name"><?php echo htmlspecialchars($user['f_name'] . ' ' . $user['l_name']); ?></p>
                                </a>
                                <p class="role"><?php echo htmlspecialchars($user['profession']); ?></p>
                            </div>
                            <?php if (isFollowing($loggedInUserId, $user['id'])): ?>
                            <button id="follow-btn-<?php echo $user['id']; ?>"
                                onclick="toggleFollow(<?php echo $user['id']; ?>)" class="follow-btn">Unfollow</button>
                            <?php else: ?>
                            <button id="follow-btn-<?php echo $user['id']; ?>"
                                onclick="toggleFollow(<?php echo $user['id']; ?>)" class="follow-btn">Follow</button>
                            <?php endif; ?>
                        </div>

                    <?php endforeach; ?>
                    <p></p>
                    <button class="post" style="width: 100%;"
                        onclick="window.location.href='<?php echo BASE_URL; ?>user/contact_list.php'">
                        List More
                    </button>

                </div>


            </div>

            <?php
            include 'inc/float_nav.php';
            ?>
        </div>

        <?php
        include 'inc/side_menu.php';
        ?>
    </div>

    <?php
    include 'footer.php';
    ?>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        const BASE_URL = "<?php echo BASE_URL; ?>";

        let offset = 10; // Start from the next set after the first 10 posts

        async function loadMorePosts() {
            const loadMoreButton = document.getElementById("load-more");
            const postContainer = document.querySelector('.posts-container');

            // Disable the button temporarily to prevent multiple clicks
            loadMoreButton.disabled = true;
            loadMoreButton.innerText = "Loading...";

            // Create and append the skeleton loader
            const skeletonLoader = document.createElement('div');
            skeletonLoader.classList.add('skeleton-loader');
            skeletonLoader.innerHTML = `<div class="post-skeleton"></div>`;
            postContainer.appendChild(skeletonLoader);

            try {
                // Fetch the new posts
                const response = await fetch(`${BASE_URL}user/fetch_posts.php?limit=10&offset=${offset}`);
                const newPosts = await response.text();

                if (newPosts.trim()) {
                    // Remove the skeleton loader
                    postContainer.removeChild(skeletonLoader);

                    // Append new posts
                    postContainer.innerHTML += newPosts;

                    // Increase the offset for the next batch
                    offset += 10;

                    // Check if fewer than 10 posts were returned
                    const postCount = (newPosts.match(/class="post"/g) || []).length;
                    if (postCount < 10) {
                        loadMoreButton.innerText = "That's all for now";
                        loadMoreButton.disabled = true;
                    } else {
                        loadMoreButton.disabled = false;
                        loadMoreButton.innerText = "Load More";
                    }
                } else {
                    loadMoreButton.innerText = "No posts";
                    loadMoreButton.disabled = true;
                }
            } catch (error) {
                console.error("Error loading posts:", error);
                loadMoreButton.innerText = "Error loading posts";
                loadMoreButton.disabled = false;
            } finally {
                // Ensure the skeleton loader is removed
                if (postContainer.contains(skeletonLoader)) {
                    postContainer.removeChild(skeletonLoader);
                }
            }
        }
    </script>

    <script>
        function toggleLike(postId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", BASE_URL + "user/toggle_like.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
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
    </script>