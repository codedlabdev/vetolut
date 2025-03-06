<?php
include 'header.php';

// Define all page paths at the beginning
$PostPage = '/public/user/network.php'; // Correct path for Post Network
$contactListPage = '/user/contact_list.php'; // Already working
$notificationsPage = '/user/notification.php'; // Already working
$searchPage = '/user/search.php'; // Already working

// Get the current page URL
$currentPage = $_SERVER['REQUEST_URI'];

?>

<div class="d-flex align-items-center justify-content-between mb-auto p-3 bg-white shadow-sm osahan-header">
    <?php if (strpos($currentPage, $searchPage) === false): ?>
    <a href="javascript:void(0);" onclick="window.history.back();" class="text-dark bg-white shadow rounded-circle icon">
        <span class="mdi mdi-arrow-left mdi-18px"></span>
    </a>
    <?php else: ?>
    <a href="<?php echo BASE_URL; ?>public/user/dashboard.php" class="text-dark bg-white shadow rounded-circle icon">
        <span class="mdi mdi-arrow-left mdi-18px"></span>
    </a>
    <?php endif; ?>
    
    <?php
    // Check current page and display appropriate header
    if (strpos($currentPage, $PostPage) !== false) {
        echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Post Network</h6>';
    } elseif (strpos($currentPage, $contactListPage) !== false) {
        echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Contacts List</h6>';
    } elseif (strpos($currentPage, $notificationsPage) !== false) {
        echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Notifications</h6>';
    }
     elseif (strpos($currentPage, $searchPage) !== false) {
        echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Search</h6>';
    }
    ?>
    
    <?php /* Commented out menu button section
    <div class="d-flex align-items-center gap-3">
        <a class="toggle d-flex align-items-center justify-content-center fs-5 bg-white shadow rounded-circle icon hc-nav-trigger hc-nav-1"
            href="#" role="button" aria-controls="hc-nav-1">
            <i class="bi bi-list"></i>
        </a>
    </div>
    */ ?>
</div>
