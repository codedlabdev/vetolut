<div class="shadow-sm" style="background-color: #003366 !important;">
    <div class="d-flex align-items-center justify-content-between mb-auto p-3 osahan-header">
        <div class="d-flex align-items-center gap-2 me-auto">
            <?php
        // Get the current page URL (full URL)
        $currentPage = $_SERVER['REQUEST_URI'];

        // Define the page URLs you want to check (relative to the base URL)
        $dashboardPage = '/public/user/dashboard.php';
        $networkListPage = '/public/user/network_list.php';
        $aichatPage = '/public/user/ai_chat/index.php';
        $aichatPages = '/public/user/ai_chat/';
        $chatListPage = '/user/chat/lists.php';
        $caseListPage = '/user/cases/lists.php';
        $caseDetailstPage = '/user/cases/details.php';
        $courseMainPage = '/user/courses/main.php';
        

        // Check if the current page is 'dashboard.php' or 'network_list.php' and display the appropriate text
        if (strpos($currentPage, $dashboardPage) !== false) {
            echo '<h3 style="color: white;margin-top: 5px;margin-left: 5px;">Home</h3>';
        } elseif (strpos($currentPage, $networkListPage) !== false) {
            echo '<h3 style="color: white;margin-top: 5px;margin-left: 5px;">Network</h3>';
        }  elseif (strpos($currentPage, $aichatPage) !== false) {
            echo '<h3 style="color: white;margin-top: 10px;margin-left: 45px;">Ai Chat</h3>';
        }  elseif (strpos($currentPage, $aichatPages) !== false) {
            echo '<h3 style="color: white;margin-top: 10px;margin-left: 45px;">Ai Chat</h3>';
        } elseif (strpos($currentPage, $chatListPage) !== false) {
           echo '<h3 style="color: white;margin-top: 5px;margin-left: 5px;">Chat List</h3>';
        
        } elseif (strpos($currentPage, $caseDetailstPage) !== false) {
           echo '<h3 style="color: white;margin-top: 5px;margin-left: 5px;">Case Details</h3>';
        
        } elseif (strpos($currentPage, $caseListPage) !== false) {
           echo '<h3 style="color: white;margin-top: 5px;margin-left: 5px;">My Folio</h3>';
        }

        elseif (strpos($currentPage, $courseMainPage) !== false) {
            echo '<h3 style="color: white;margin-top: 5px;margin-left: 5px;">Courses</h3>';
        }
       
        ?>
        </div>

        <div class="d-flex align-items-center gap-2">

            <a href="<?php echo BASE_URL; ?>user/search.php" class="shadow rounded-circle icon">
                <!-- link to a seaching page -->
                <span class="mdi mdi-magnify mdi-18px" style="color: white!important;"></span>
            </a>


            <?php
        $unreadCount = getUnreadNotificationCount($loggedInUserId); // Fetch unread notification count
        ?>
            <a href="<?php echo BASE_URL; ?>user/notification.php" class="shadow rounded-circle icon">
                <?php if ($unreadCount > 0): ?>
                <!-- Display bell icon with count if there are unread notifications -->
                <span class="mdi mdi-bell mdi-18px text-primary" style="color: white!important;"></span>
                <span class="notification-count"><?php echo $unreadCount; ?></span> <!-- Display notification count -->
                <?php else: ?>
                <!-- Display empty bell icon if there are no unread notifications -->
                <span class="mdi mdi-bell-outline mdi-18px text-primary" style="color: white!important;"></span>
                <?php endif; ?>
            </a>

            <a href="<?php echo BASE_URL; ?>user/user_profile/">
                <img src="<?php echo $imageData; ?>" alt="<?php echo $_SESSION['user_id']; ?>" class="img-fluid profile-img"
                    style="border-radius: 50%; width: 40px; height: 40px; object-fit: cover;">
            </a>
        </div>
    </div>


    <div style="display: none;" class="px-3 pb-3">
        <form>
            <div class="input-group rounded-4 shadow py-1 px-3 bg-light">
                <span class="input-group-text bg-transparent text-muted border-0 p-0" id="search">
                    <span class="mdi mdi-magnify mdi-24px" style="color: #9095a1!important;"></span>
                </span>
                <input type="text" class="form-control bg-transparent text-muted rounded-0 border-0 px-3"
                    placeholder="AI queries, cases, courses, or colleagues..." aria-label="Search"
                    aria-describedby="search">
                <a href="#" class="input-group-text bg-transparent text-muted border-0 border-start pe-0" id="search">
                    <span class="mdi mdi-filter-outline mdi-18px"></span>
                </a>
            </div>
        </form>
    </div>
</div>