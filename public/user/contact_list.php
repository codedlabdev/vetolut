<?php 
// network.php


?>

<div class="appointment-upcoming d-flex flex-column vh-100">
    <?php include 'inc/p_others.php';


require_once BASE_DIR . 'lib/dhu.php'; // Include helper functions
require_once BASE_DIR . 'lib/user/table.php'; // Include helper functions
$contactList = getAllContacts($loggedInUserId); // Fetch all contacts for the contact list
?>

    <div class="vh-100 my-auto overflow-auto body-fix-osahan-footer">
        <div class="container" style="margin-top: 40px;">
		<div class="post-input-box">
            <div class="suggestions-card">
                <div class="suggestions-header">
                    <p class="suggestions-header-title">Colleague Suggestions</p>
                </div>

                 <?php foreach ($contactList as $user): ?>
        <?php 
            $imageData = !empty($user['image']) 
                ? 'data:image/jpeg;base64,' . base64_encode($user['image'])
                : BASE_URL . 'assets/user/img/noimage.png';
        ?>
        <div class="suggestion">
            <img src="<?php echo htmlspecialchars($imageData); ?>"
                 alt="<?php echo htmlspecialchars($user['f_name'] . ' ' . $user['l_name']); ?>"
                 class="profile-pic">
            <div class="suggestion-info">
                <a href="user_profile?id=<?php echo urlencode($user['id']); ?>" class="name-link">
                    <p class="name"><?php echo htmlspecialchars($user['f_name'] . ' ' . $user['l_name']); ?></p>
                </a>
                <p class="role"><?php echo htmlspecialchars($user['profession']); ?></p>
            </div>
           <?php if (isFollowing($loggedInUserId, $user['id'])): ?>
					<button id="follow-btn-<?php echo $user['id']; ?>" onclick="toggleFollow(<?php echo $user['id']; ?>)" class="follow-btn">Unfollow</button>
				<?php else: ?>
					<button id="follow-btn-<?php echo $user['id']; ?>" onclick="toggleFollow(<?php echo $user['id']; ?>)" class="follow-btn">Follow</button>
				<?php endif; ?>
        </div>
    <?php endforeach; ?>
				<p></p>
				
            </div>
        </div>
    </div>
</div>
</div>

  
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

<?php include 'footer.php';
?>