<?php

// Include files using absolute paths
include '../header.php';
require_once BASE_DIR . 'lib/user/info_upd.php';

// Check if there is a session message to display
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']); // Clear message after displaying
?>

<div id="info_MessageContainer" class="message" style="text-align: center;">
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
</div>

<div class="appointment-upcoming d-flex flex-column vh-100">
    <!-- Header with back button -->
    <div class="d-flex align-items-center justify-content-between mb-auto p-3 bg-white shadow-sm osahan-header">
        <a href="list.php" onclick="slideBack();" class="text-dark bg-white shadow rounded-circle icon">
            <span class="mdi mdi-arrow-left mdi-18px"></span>
        </a>
        <h6 class="mb-0 ms-3 me-auto fw-bold">Update Information</h6>
        <div class="d-flex align-items-center gap-3">
        </div>
    </div>

    <div class="change-profile d-flex flex-column vh-100">
        <div class="vh-100 my-auto overflow-auto p-3">
            <form id="infoForm" method="POST">
                <!-- Profession Field -->
                <div class="mb-3">
                    <label for="profession" class="form-label mb-1">Profession</label>
                    <div class="input-group border bg-white rounded-3 py-1">
                        <input style="padding-left: 15px !important;" type="text" name="profession" class="form-control bg-transparent rounded-0 border-0 px-0" placeholder="Enter your profession" value="<?= htmlspecialchars($user['profession']) ?>" required>
                    </div>
                </div>

                <!-- About Field -->
                <div class="mb-3">
                    <label for="about" class="form-label mb-1">About</label>
                    <div class="input-group border bg-white rounded-3 py-1">
                        <textarea style="padding-left: 15px !important;" name="about" class="form-control bg-transparent rounded-0 border-0 px-0" placeholder="Tell us about yourself" required><?= htmlspecialchars($user['about'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" onclick="updateUserInfo(); return false;" class="btn btn-info rounded-4" style="position: absolute; margin-left: 20px; width: 80%;">Save Changes</button>

            </form>
        </div>
    </div>
</div>






<?php include '../footer.php'; ?>



<style>
    .fix-osahan-footer {

        display: none;
    }


    .fix-osahan-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 3px;
        top: 570px !important;
    }

    .vh-100 {
        height: 100vh !unset;
    }

    .slide-out {
        transform: translateX(100%);
        /* Slide out to the right */
    }


    /* Slide-in animation from the right */
    .slide-in {
        transform: translateX(0%);
        transition: transform 0.5s ease;
    }

    /* Slide-out animation to the right */
    .slide-out {
        transform: translateX(100%);
        transition: transform 0.5s ease;
    }



    /* Wrapper for positioning edit icon above the profile image */
    .profile-wrapper {
        position: relative;
        width: 120px;
        margin: 0 auto;
        /* Centers the entire component horizontally */
    }

    /* Edit icon styling */
    .edit-icon {
        position: absolute;
        top: -10px;
        right: 10px;
        width: 30px;
        height: 30px;
        background-color: #00aaff;
        /* Background color of the edit icon */
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
    }

    /* Profile image container */
    .profile-image-wrapper {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Profile image styling */
    .profile-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .form-select {
        padding-left: 15px !important;
        /* Adjust value as needed */
    }
</style>