<?php

// Include files using absolute paths
include '../header.php';
require_once BASE_DIR . 'lib/user/p_func.php';
require_once 'countries_list.php'; // Add this line to include the countries array

// Check if there is a session message to display
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']); // Clear message after displaying
?>

<!-- Header with back button -->
<div class="d-flex align-items-center justify-content-between mb-auto p-3 bg-white shadow-sm osahan-header">
    <a href="list.php" onclick="slideBack();" class="text-dark bg-white shadow rounded-circle icon">
        <span class="mdi mdi-arrow-left mdi-18px"></span>
    </a>
    <h6 class="mb-0 ms-3 me-auto fw-bold">Update Profile</h6>
    <div class="d-flex align-items-center gap-3">

    </div>
</div>



<div id="profileMessageContainer" class="message" style="text-align: center;">
    <?php if ($message): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert" id="autoCloseAlert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

<!-- Update the toast container structure -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="messageToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="toastTitle">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body"></div>
    </div>
</div>



<div class="appointment-upcoming d-flex flex-column vh-100">






    <div class="change-profile d-flex flex-column vh-100">
        <div class="vh-100 my-auto overflow-auto p-3">

            <form id="profileForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">


                <div class="profile-wrapper" style="margin-top:50px">
                    <div class="edit-icon" onclick="document.getElementById('file-input').click();">
                        <span class="mdi mdi-camera mdi-18px"></span>
                    </div>
                    <div class="profile-image-wrapper">
                        <img src="<?php echo !empty($user['image']) ? BASE_URL . $user['image'] : BASE_URL . 'assets/user/img/noimage.png'; ?>"
                            alt="Profile Image"
                            style="width: 150px; height: 150px; border-radius: 75px;"
                            class="profile-image"
                            id="profile-image">
                    </div>
                    <input type="file" name="profile_image" id="file-input" style="display: none;" accept="image/png, image/jpeg, image/jpg" onchange="displaySelectedImage(event)">

                </div>


                <!-- First Name Field -->
                <div class="mb-3">
                    <label for="firstName" class="form-label mb-1">First Name</label>
                    <div class="input-group border bg-white rounded-3 py-1">
                        <input type="text" name="f_name" class="form-control bg-transparent rounded-0 border-0 px-0" value="<?php echo htmlspecialchars($user['f_name']); ?>" require>
                    </div>
                </div>

                <!-- Last Name Field -->
                <div class="mb-3">
                    <label for="lastName" class="form-label mb-1">Last Name</label>
                    <div class="input-group border bg-white rounded-3 py-1">
                        <input type="text" name="l_name" class="form-control bg-transparent rounded-0 border-0 px-0" value="<?php echo htmlspecialchars($user['l_name']); ?>" require>
                    </div>
                </div>

                <!-- Gender Field -->
                <div class="mb-3">
                    <label for="exampleFormControlGender" class="form-label mb-1">Gender</label>
                    <div class="input-group border bg-white rounded-3 py-1">
                        <label class="input-group-text bg-transparent rounded-0 border-0" for="inputGroupSelectGender">
                            <span class="mdi mdi-account-group-outline mdi-18px"></span>
                        </label>
                        <select id="gender" name="gender" class="form-control" required>
                            <option value="male" <?php echo ($user['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($user['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo ($user['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>


                <div class="row g-2 mb-3">
                    <div class="col">
                        <div>
                            <label class="form-label mb-1">Select Country</label>
                            <div class="input-group border bg-white rounded-3 py-1">
                                <label class="input-group-text bg-transparent rounded-0 border-0" for="countrySelect">
                                    <span class="mdi mdi-earth mdi-18px"></span>
                                </label>
                                <select class="form-select bg-transparent rounded-0 border-0 px-0" id="countrySelect" name="country" required>
                                    <option selected disabled>Select a country</option>
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?= $country ?>" data-country="<?= $country ?>" <?= ($user['country'] === $country) ? 'selected' : ''; ?>>
                                            <?= $country ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>


                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div>
                            <label class="form-label mb-1">Code</label>
                            <div class="input-group border bg-white rounded-3 py-1">
                                <label class="input-group-text bg-transparent rounded-0 border-0" for="codeDisplay">
                                    <span class="mdi mdi-flag mdi-18px"></span>
                                </label>
                                <input type="text" class="form-control bg-transparent rounded-0 border-0 px-0" value="<?php echo htmlspecialchars($user['phone_code']); ?>">
                            </div>
                        </div>
                    </div>

                </div>


                <div class="mb-3">
                    <div>
                        <label class="form-label mb-1">Phone Number</label>
                        <div class="input-group border bg-white rounded-3 py-1">
                            <label class="input-group-text bg-transparent rounded-0 border-0" for="countryNameDisplay">
                                <span class="mdi mdi-account-circle mdi-18px"></span>
                            </label>
                            <input type="text" class="form-control bg-transparent rounded-0 border-0 px-0" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                    </div>
                </div>


                <!-- Submit Button -->
                <button type="submit" class="btn btn-info rounded-4" id="submitButton" style="position: absolute; margin-left: 20px; width: 80%;">Save Changes</button>

            </form>
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
            top: 90px;
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

        .form-control {
            padding-left: 15px !important;
            /* Adjust value as needed */
        }

        .toast {
            background-color: white;
            border-left: 4px solid #28a745;
        }

        .toast.bg-success {
            border-left-color: #28a745;
        }

        .toast.bg-danger {
            border-left-color: #dc3545;
        }

        .toast-header {
            background-color: transparent;
            border-bottom: none;
        }

        .btn-close {
            opacity: 0.8;
        }

        .alert-dismissible .btn-close {

            font-size: medium;
        }

        .mdi-18px.mdi:before {
            font-size: 18px !important;
        }
    </style>

    <script>
        // Add this function for image preview
        function displaySelectedImage(event) {
            const file = event.target.files[0];
            const imagePreview = document.getElementById('profile-image');

            if (file) {
                // Check file size (5MB = 5 * 1024 * 1024 bytes)
                if (file.size > 5 * 1024 * 1024) {
                    showToast('Image size must be less than 5MB', 'danger');
                    event.target.value = ''; // Clear the file input
                    return;
                }

                // Check file type
                if (!file.type.match('image/jpeg') && !file.type.match('image/png') && !file.type.match('image/jpg')) {
                    showToast('Only JPG and PNG files are allowed', 'danger');
                    event.target.value = ''; // Clear the file input
                    return;
                }

                // Preview the image
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function showToast(message, type) {
            const toast = document.getElementById('messageToast');
            const toastTitle = document.getElementById('toastTitle');

            // Reset classes
            toast.classList.remove('bg-success', 'bg-danger');

            // Set type-specific styles
            if (type === 'success') {
                toastTitle.textContent = 'Success';
                toast.classList.add('bg-success');
            } else {
                toastTitle.textContent = 'Error';
                toast.classList.add('bg-danger');
            }

            // Set message
            const toastBody = toast.querySelector('.toast-body');
            toastBody.textContent = message;

            // Initialize and show toast
            const bsToast = new bootstrap.Toast(toast, {
                animation: true,
                autohide: true,
                delay: 3000
            });

            bsToast.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['message'])): ?>
                showToast('<?php echo addslashes($_SESSION['message']); ?>', '<?php echo $_SESSION['message_type']; ?>');
                <?php if (isset($_SESSION['refresh_needed'])): ?>
                    // Force a hard refresh after successful update
                    window.location.reload(true);
                    <?php unset($_SESSION['refresh_needed']); ?>
                <?php endif; ?>
                <?php
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            <?php endif; ?>

            // Add form submission handler
            const form = document.getElementById('profileForm');
            const submitButton = document.getElementById('submitButton');

            form.addEventListener('submit', function(e) {
                // Basic validation for required fields
                const f_name = form.querySelector('input[name="f_name"]').value.trim();
                const l_name = form.querySelector('input[name="l_name"]').value.trim();
                const country = form.querySelector('select[name="country"]').value;
                const gender = form.querySelector('select[name="gender"]').value;

                if (!f_name || !l_name || !country || !gender) {
                    e.preventDefault();
                    showToast('Please fill in all required fields', 'danger');
                    return;
                }

                // Disable the submit button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = 'Saving...';
            });
        });

        // Auto close alert after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('autoCloseAlert');
            if (alert) {
                setTimeout(function() {
                    bootstrap.Alert.getOrCreateInstance(alert).close();
                }, 3000); // close in 3 seconds
            }
        });
    </script>