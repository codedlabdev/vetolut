<?php
// Get the logged-in user ID from session
$loggedInUserId = $_SESSION['user_id'];

// Get the total unread message count for the logged-in user
$unreadCount = getTotalUnreadMessageCount($loggedInUserId);
$currentUrl = $_SERVER['REQUEST_URI'];
?>

<!-- Feedback Icon -->
<div class="avx-feedback-float-icon">
    <button class="fab-button" onclick="toggleFloatMenu()">
            <i class="fas fa-plus"></i>
        </button>

    <div class="float-options">
        <div class="float-option" onclick="handleOptionClick('create_post')" title="Network">
            <div class="float-option-content">
                <i class="fas fa-network-wired"></i>
                <span class="option-title">Network</span>
            </div>
        </div>
        <div class="float-option" onclick="handleOptionClick('create_case')" title="Case">
            <div class="float-option-content">
                <i class="fas fa-briefcase"></i>
                <span class="option-title">Case</span>
            </div>
        </div>
        <div class="float-option" onclick="handleOptionClick('ask_question')" title="AI-Chat">
            <div class="float-option-content">
                <i class="fas fa-robot"></i>
                <span class="option-title">AI-Chat</span>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer mt-auto p-3 fix-osahan-footer">
    <div class="d-flex align-items-center justify-content-between rounded-4 shadow overflow-hidden bottom-nav-main">

        <!-- Home Link -->
        <a href="<?php echo BASE_URL; ?>public/user/dashboard.php" class="col footer-bottom-nav <?php echo (strpos($currentUrl, 'dashboard.php') !== false) ? 'active' : ''; ?>">
            <span class="mdi mdi-home-variant-outline mdi-24px"></span>
            <span>Home</span>
        </a>

        <!-- Folio Link -->
        <a href="<?php echo BASE_URL; ?>public/user/cases/lists.php" class="col footer-bottom-nav <?php echo (strpos($currentUrl, 'cases/lists.php') !== false) ? 'active' : ''; ?>">
            <span class="mdi mdi-folder-outline mdi-24px"></span>
            <span>Folio</span>
        </a>

        <!-- Network Link -->
        <a href="<?php echo BASE_URL; ?>public/user/network_list.php" class="col footer-bottom-nav <?php echo (strpos($currentUrl, 'network_list.php') !== false) ? 'active' : ''; ?>">
            <span class="mdi mdi-account-outline mdi-24px"></span>
            <span>Network</span>
        </a>

        <!-- Chat Link -->
        <a href="<?php echo BASE_URL; ?>public/user/chat/lists.php" class="col footer-bottom-nav <?php echo (strpos($currentUrl, 'chat/lists.php') !== false) ? 'active' : ''; ?>">
            <span class="mdi mdi-message-processing-outline mdi-24px"></span>
            <span>Chat</span>

            <!-- Notification bubble if there are unread messages -->
            <?php if ($unreadCount > 0): ?>
                <span style="
                    position: absolute;
                    font-size: 18px;
                    background: red;
                    color: white;
                    border-radius: 50%;
                    margin-left: 20px;
                    width: 15px;
                    height: 15px;
                    display: inline-block;
                    text-align: center;
                    line-height: 15px;
                "></span>
            <?php endif; ?>
        </a>

        <!-- Learn Link -->
        <a href="<?php echo BASE_URL; ?>public/user/courses/main.php" class="col footer-bottom-nav <?php echo (strpos($currentUrl, 'courses/main.php') !== false) ? 'active' : ''; ?>">
            <span class="mdi mdi-book-open-outline mdi-24px"></span>
            <span>Learn</span>
        </a>

    </div>
</div>

<!-- Floating Menu Container -->
<style>
.avx-feedback-float-icon {
    position: fixed;
    
    right: 20px;
    z-index: 1000;
}

.float-options {
    position: absolute;
    bottom: 45px;
    right: 0;
    display: none;
    flex-direction: column;
    gap: 15px;
}

.float-option {
    width: 65px;
    height: 65px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    cursor: pointer;
    transform: scale(0);
    transition: all 0.3s ease;
    position: relative;
}

.float-option:hover {
    background: #f8f9fa;
    transform: scale(1.05) !important;
}

.float-option-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 2px;
}

.float-option i {
    font-size: 22px;
    color: #007bff;
}

.option-title {
    font-size: 10px;
    color: #666;
    line-height: 1;
    margin-top: 1px;
}

.float-options.show {
    display: flex;
}

.float-options.show .float-option {
    transform: scale(1);
}

 /* Floating Action Button */
 .fab-button {
        position: fixed;
        bottom: 95px;
        right: 24px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .fab-button:hover {
        transform: scale(1.1);
    }

.float-option:nth-child(1) { transition-delay: 0s; }
.float-option:nth-child(2) { transition-delay: 0.05s; }
.float-option:nth-child(3) { transition-delay: 0.1s; }
.float-option:nth-child(4) { transition-delay: 0.15s; }
</style>
<script>
function toggleFloatMenu() {
    const options = document.querySelector('.float-options');
    options.classList.toggle('show');
    
    const icon = document.querySelector('.fab-button');
    icon.textContent = options.classList.contains('show') ? '×' : '+';
}

function handleOptionClick(action) {
    switch(action) {
        case 'create_post':
            window.location.href = '<?php echo BASE_URL; ?>public/user/network.php?action=post';
            break;
        case 'create_case':
            window.location.href = '<?php echo BASE_URL; ?>public/user/cases/create.php';
            break;
        case 'ask_question':
            window.location.href = '<?php echo BASE_URL; ?>public/user/ai_chat';
            break;
        default:
            break;
    }
}
</script>