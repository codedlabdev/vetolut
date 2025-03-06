<div class="appointment-upcoming d-flex flex-column vh-100">
  <?php include '../inc/p_top.php';
  require_once BASE_DIR . 'lib/user/courses_func.php'; // Include courses functions
$followerCount = getFollowerCount($userId);
$followingCount = getFollowingCount($userId);
  ?>

  <div class="vh-100 my-auto overflow-auto body-fix-osahan-footer">
    <div class="p-3 bg-white shadow-sm">
     <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
     <img src="<?php echo !empty($imageData) ? $imageData : BASE_URL . 'assets/user/img/noimage.png'; ?>" alt="" class="img-fluid rounded-4 voice-img">
    <div>
        <h6 class="mb-1"><?php echo htmlspecialchars($user['f_name']); ?> <?php echo htmlspecialchars($user['l_name']); ?></h6>
        <p class="text-muted mb-2"><?php echo htmlspecialchars($user['profession']); ?></p>
    </div>
    <div class="ms-auto">
        <div class="d-flex justify-content-end">
		 <?php if ($userId == $loggedInUserId):?>
            <div class="bg-info-subtle rounded-circle icon mb-3">
				<a href="list.php" style="cursor: pointer;">
                    <span class="mdi mdi-square-edit-outline mdi-18px text-info"></span>
                </a>
			</div>
			<?php endif; ?>
        </div>
		
		<!--<//?php if ($userId != $loggedInUserId): // Check if the logged-in user is NOT the owner ?> -->
		
		 <?php if ($userId == $loggedInUserId): // Check if the logged-in user is the owner ?>
		 <span class="badge bg-success-subtle text-success fw-normal rounded-pill px-2">ONLINE</span>
       <?php else: ?>
		<?php 
		// Check if the user is online (last active within the past 1 minutes)
		$isOnline = strtotime($user['last_active']) > (time() - 60);
			?>
		<span style="margin-top: -10; margin-bottom: 10px;" class="badge <?php echo $isOnline ? 'bg-success-subtle text-success' : 'bg-danger text-white'; ?> fw-normal rounded-pill px-2">
            <?php echo $isOnline ? 'ONLINE' : 'OFFLINE'; ?>
        </span>
		 
		<span style="display: flex;">
		<?php if (isFollowing($loggedInUserId, $user['id'])): ?>
					<a style="margin-right: 10px;" onclick="window.location.href='<?php echo BASE_URL . 'user/chat/inbox.php?id=' . $user['id']; ?>';" class="message-btn"><i class="mdi mdi-message-text-outline"></i></a>
					<a id="follow-btn-<?php echo $user['id']; ?>" onclick="toggleFollow(<?php echo $user['id']; ?>)" class="unfollow-btn"><i class="mdi mdi-account-remove"></i></a>

				<?php else: ?>
				  <a id="follow-btn-<?php echo $user['id']; ?>" onclick="toggleFollow(<?php echo $user['id']; ?>)" class="follow-btn"><i class="mdi mdi-account-plus"></i></a>
				<?php endif; ?>
		 <?php endif; ?>
        </span>
    </div>
</div>
      <div class="d-flex align-items-center justify-content-between">
       

	   <div class="d-flex align-items-center gap-3 col">
          
          <div>  
			<p class="mb-0 small text-muted">Followers</p>
            <center><p class="text-primary m-0 fw-bold"><?php echo $followerCount; ?></p></center>
          </div>
        </div>
		
	   <div class="d-flex align-items-center gap-3 col">
          
          <div>    
			<p class="mb-0 small text-muted">Following</p>
            <center><p class="text-primary m-0 fw-bold"><?php echo $followingCount; ?></p></center>
          </div>
        </div>
		
		
		
		<div class="d-flex align-items-center gap-3 col">
          
          <div>
            <p class="mb-0 small text-muted">Course</p>
            <center><p class="text-primary m-0 fw-bold">20</p></center>
          </div>
        </div>
        <div class="d-flex align-items-center gap-3 col">
          
          <div>
            <p class="mb-0 small text-muted">Review</p>
            <center><p class="text-primary m-0 fw-bold">5.0</p></center>
          </div>
        </div>
       
      </div>
    </div>
	
	 


<div class="doctor-profile d-flex flex-column vh-100">
 

<div class="vh-100 my-auto overflow-auto">

 
 

<div class="bg-white shadow-sm border-top">
<ul class="nav doctor-profile-tabs gap-1 p-0" id="pills-tab" role="tablist">

<li class="nav-item col" role="presentation">
<button class="nav-link w-100 active" activeid="pills-info-tab" data-bs-toggle="pill" data-bs-target="#pills-info" type="button" role="tab" aria-controls="pills-info" aria-selected="true" tabindex="-1">About</button>
</li>

 <?php if ($userId == $loggedInUserId):?>
<li class="nav-item col" role="presentation">
<button class="nav-link w-100" id="pills-contact_list-tab" data-bs-toggle="pill" data-bs-target="#pills-contact_list" type="button" role="tab" aria-controls="pills-contact_list" aria-selected="false">Contacts</button>
</li>
 <?php endif; ?>

<li class="nav-item col" role="presentation">
<button class="nav-link w-100" id="pills-experience-tab" data-bs-toggle="pill" data-bs-target="#pills-experience" type="button" role="tab" aria-controls="pills-experience" aria-selected="false">Badges</button>
</li>

<li class="nav-item col" role="presentation">
<button class="nav-link w-100 " id="pills-course-tab" data-bs-toggle="pill" data-bs-target="#pills-course" type="button" role="tab" aria-controls="pills-course" aria-selected="false">Course</button>
</li>

<li class="nav-item col" role="presentation">
<button class="nav-link w-100 " id="pills-review-tab" data-bs-toggle="pill" data-bs-target="#pills-review" type="button" role="tab" aria-controls="pills-review" aria-selected="false">Review</button>
</li>

</ul>
</div>
<div class="mb-3">
<div class="tab-content" id="pills-tabContent">

<div class="tab-pane fade active show p-3" id="pills-info" role="tabpanel" aria-labelledby="pills-info-tab" tabindex="0">
<h6 class="pb-2 mb-0"> </h6>
<p class="text-muted"><?= htmlspecialchars($user['about'] ?? '') ?></p>

</p>
</div>




<div class="tab-pane fade p-3" id="pills-experience" role="tabpanel" aria-labelledby="pills-experience-tab" tabindex="0">



<div class="bg-white rounded-4 p-3 mb-3 shadow-sm">
<h6 class="mb-3">Amercan Medical</h6>

<span class="text-dark"><img src="https://public-assets.envato-static.com/assets/badges/was_weekly_top_seller-s-450a372ef504c226ad5c50b332c51741364a5014267839e0484804ae2fafe398.svg" width="50" ></span>
</p>
 
</div>


 


</div>

<div class="tab-pane fade" id="pills-contact_list" role="tabpanel" aria-labelledby="pills-contact_list-tab" tabindex="0">



<div class="container" style="margin-top: 40px;">
		<div class="post-input-box">
            <div class="suggestions-card">
                <div class="suggestions-header">
                    <p class="suggestions-header-title">Following</p>
                </div>

                 <?php
				 require_once BASE_DIR . 'lib/user/table.php'; // Include helper functions
				 
// Assuming you have already fetched the logged-in user's ID in $loggedInUserId
$followedUsers = getFollowedUsers($loggedInUserId);

// Display the following users
echo '<h3></h3>';
foreach ($followedUsers['following'] as $user):
    $imageData = !empty($user['image'])
        ? 'data:image/jpeg;base64,' . base64_encode($user['image'])
        : BASE_URL . 'assets/user/img/noimage.png';
?>
    <div class="suggestion">
        <img src="<?php echo htmlspecialchars($imageData); ?>"
             alt="<?php echo htmlspecialchars($user['f_name'] . ' ' . $user['l_name']); ?>"
             class="profile-pic">
        <div class="suggestion-info">
            <a href="<?php echo BASE_URL; ?>user/user_profile?id=<?php echo urlencode($user['id']); ?>" class="name-link">
                <p class="name"><?php echo htmlspecialchars($user['f_name'] . ' ' . $user['l_name']); ?></p>
            </a>
            <p class="role"><?php echo htmlspecialchars($user['profession']); ?></p>
        </div>
		
		<?php if (isFollowing($loggedInUserId, $user['id'])): ?>
					<a style="margin-right: 10px;" onclick="window.location.href='<?php echo BASE_URL . 'user/chat/inbox.php?id=' . $user['id']; ?>';" class="message-btn"><i class="mdi mdi-message-text-outline"></i></a>
					<a id="follow-btn-<?php echo $user['id']; ?>" onclick="toggleFollow(<?php echo $user['id']; ?>)" class="unfollow-btn"><i class="mdi mdi-account-remove"></i></a>

				<?php else: ?>
					<button id="follow-btn-<?php echo $user['id']; ?>" onclick="toggleFollow(<?php echo $user['id']; ?>)" class="follow-btn">Follow</button>
				<?php endif; ?>
    </div>
<?php endforeach; ?>


<div class="suggestions-header">
                    <p class="suggestions-header-title">Followers</p>
                </div>
<!-- Display the followers users -->
<?php
echo '<h3></h3>';
foreach ($followedUsers['followers'] as $user):
    $imageData = !empty($user['image'])
        ? 'data:image/jpeg;base64,' . base64_encode($user['image'])
        : BASE_URL . 'assets/user/img/noimage.png';
?>
    <div class="suggestion">
        <img src="<?php echo htmlspecialchars($imageData); ?>"
             alt="<?php echo htmlspecialchars($user['f_name'] . ' ' . $user['l_name']); ?>"
             class="profile-pic">
        <div class="suggestion-info">
           <a href="<?php echo BASE_URL; ?>user/user_profile?id=<?php echo urlencode($user['id']); ?>" class="name-link">
				<p class="name"><?php echo htmlspecialchars($user['f_name'] . ' ' . $user['l_name']); ?></p>
			</a>

            <p class="role"><?php echo htmlspecialchars($user['profession']); ?></p>
        </div>
		
		<a style="margin-right: 10px;" onclick="window.location.href='<?php echo BASE_URL . 'user/chat/inbox.php?id=' . $user['id']; ?>';" class="message-btn"><i class="mdi mdi-message-text-outline"></i></a>
    </div>
<?php endforeach; ?>

				<p></p>
				
            </div>
        </div>
    </div>
</div>

<div class="tab-pane fade p-3" id="pills-course" role="tabpanel" aria-labelledby="pills-course-tab" tabindex="0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="mb-0 fw-bold">Course</h6>
        <a href="<?php echo BASE_URL; ?>user/courses/create.php" class="text-decoration-none" style="color: #20c997;margin-right: 20px;">
            <i class="mdi mdi-plus"></i> Create
        </a>
    </div>
    
    <div class="course-list">
        <?php 
        $userCourses = getUserCourses($userId);
        if (!empty($userCourses)): 
            foreach ($userCourses as $course): 
        ?>
            <div class="bg-white rounded-4 shadow-sm mb-3">
            <a href="<?php echo BASE_URL; ?>user/courses/edit.php?id=<?php echo $course['id']; ?>" class="text-decoration-none">
                <div class="d-flex align-items-center">
                    <div class="position-relative" style="width: 120px; height: 80px;">
                        <img src="<?php echo !empty($course['banner_image']) ? BASE_URL . $course['banner_image'] : BASE_URL . 'assets/user/img/course-default.jpg'; ?>" 
                             class="rounded-start-4" 
                             style="width: 120px; height: 80px; object-fit: cover;">
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <i class="mdi mdi-play-circle text-white" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="p-3 flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 text-dark"><?php echo htmlspecialchars($course['title']); ?></h6>
                                <p class="text-muted mb-2" style="font-size: 0.875rem;"><?php echo substr(htmlspecialchars($course['description']), 0, 100) . '...'; ?></p>
                                <div class="d-flex align-items-center text-muted" style="font-size: 0.75rem;">
                                    <span class="d-flex align-items-center me-3">
                                        <i class="mdi mdi-star text-warning me-1"></i> 4.3
                                    </span>
                                    <span class="d-flex align-items-center me-3">
                                        <i class="mdi mdi-signal me-1"></i> <?php echo htmlspecialchars($course['category_name']); ?>
                                    </span>
                                    <!--<span class="d-flex align-items-center">
                                        <i class="mdi mdi-account-multiple-outline me-1"></i> 1.5k Students
                                    </span>-->
                                   
                                </div>
                                <!--<small class=""><i class="mdi mdi-clock-time-four-outline"></i> </?php echo date('M d, Y', strtotime($course['created_at'])); ?></small>-->
                            </div>
                            <div class="text-end d-flex flex-column align-items-end">
                                <span class="text-primary fw-bold mb-1" style="font-size: 0.875rem;">
                                    <?php echo ($course['price'] == 0) ? 'Free' : '$' . number_format($course['price'], 2); ?>
                                </span>
                                
                                <?php if ($course['status'] == 'draft'): ?>
                                    <span class="badge bg-danger-subtle text-black" style="font-size: 0.7rem;">Draft</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-black" style="font-size: 0.7rem;">Published</span>
                                <?php endif; ?>

                                <?php if ($course['admin_status'] == 1): ?>
                                    <span class="badge bg-success-subtle text-black" style="font-size: 0.7rem;">Approved</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>


        <?php 
            endforeach;
        else:
        ?>
            <div class="text-center p-4">
                <p class="text-muted">No courses found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

					<div class="tab-pane fade p-3" id="pills-review" role="tabpanel" aria-labelledby="pills-review-tab" tabindex="0">
						<h6 class="pb-3 px-3 pt-3 mb-0">Review (2350)</h6>
						
						<!-- Review Item 1 -->
						<div class="bg-white shadow-sm d-flex align-items-center gap-2 py-2 px-3 border-bottom">
							<img src="img/review/profile-1.jpg" alt="" class="img-fluid rounded-pill review-profile">
							<div>
								<p class="mb-0 fw-bold">Sadia</p>
								<p class="text-muted small m-0">Oct 31, 2023</p>
							</div>
							<div class="ms-auto text-center">
								<div class="d-flex align-items-center gap-1 small text-warning">
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="badge rounded-pill text-bg-warning ms-1">4.9</span>
								</div>
							</div>
						</div>

						<!-- Review Item 2 -->
						<div class="bg-white shadow-sm d-flex align-items-center gap-2 py-2 px-3 border-bottom">
							<img src="img/review/profile-2.jpg" alt="" class="img-fluid rounded-pill review-profile">
							<div>
								<p class="mb-0 fw-bold">Mahabuba</p>
								<p class="text-muted small m-0">Oct 31, 2023</p>
							</div>
							<div class="ms-auto text-center">
								<div class="d-flex align-items-center gap-1 small text-warning">
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="badge rounded-pill text-bg-warning ms-1">4.9</span>
								</div>
							</div>
						</div>

						<!-- Review Item 3 -->
						<div class="bg-white shadow-sm d-flex align-items-center gap-2 py-2 px-3 border-bottom">
							<img src="img/review/profile-3.jpg" alt="" class="img-fluid rounded-pill review-profile">
							<div>
								<p class="mb-0 fw-bold">Faiza</p>
								<p class="text-muted small m-0">Oct 31, 2023</p>
							</div>
							<div class="ms-auto text-center">
								<div class="d-flex align-items-center gap-1 small text-warning">
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="badge rounded-pill text-bg-warning ms-1">4.9</span>
								</div>
							</div>
						</div>

						<!-- Review Item 4 -->
						<div class="bg-white shadow-sm d-flex align-items-center gap-2 py-2 px-3 border-bottom">
							<img src="img/review/profile-4.jpg" alt="" class="img-fluid rounded-pill review-profile">
							<div>
								<p class="mb-0 fw-bold">Nipa</p>
								<p class="text-muted small m-0">Oct 31, 2023</p>
							</div>
							<div class="ms-auto text-center">
								<div class="d-flex align-items-center gap-1 small text-warning">
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="badge rounded-pill text-bg-warning ms-1">4.9</span>
								</div>
							</div>
						</div>

						<!-- Review Item 5 -->
						<div class="bg-white shadow-sm d-flex align-items-center gap-2 py-2 px-3 border-bottom">
							<img src="img/review/profile-5.jpg" alt="" class="img-fluid rounded-pill review-profile">
							<div>
								<p class="mb-0 fw-bold">Rumpa</p>
								<p class="text-muted small m-0">Oct 31, 2023</p>
							</div>
							<div class="ms-auto text-center">
								<div class="d-flex align-items-center gap-1 small text-warning">
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="mdi mdi-star"></span>
									<span class="badge rounded-pill text-bg-warning ms-1">4.9</span>
								</div>
							</div>
						</div>

						<!-- See All Reviews Link -->
						<div class="text-center mt-3">
							<a href="#" class="text-decoration-underline text-primary fw-bold">Tab here to see all reviews</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
  
 <style>
 
 .vh-100 {
    height: unset !important;
}

.fix-osahan-footer {
    position: fixed!important;
    bottom: 0;
    left: 0;
    right: 3px;
}

.avx-feedback-float-icon{
	display:none;
}

.tab-pane {

	background: #ffffff;
    margin-top: 20px;
}

.badge {
    margin-bottom: 8px;
}
 </style>

  <?php include '../inc/float_nav.php'; ?>
</div>



<?php include '../inc/side_menu.php'; ?>


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
