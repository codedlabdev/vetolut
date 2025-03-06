<?php

// Include files using absolute paths
include '../header.php';
require_once BASE_DIR . '/lib/user/email_verify.php';


// Check if there is a session message to display
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']); // Clear message after displaying
?>




<div id="messageContainer" class="message" style="text-align: center;">
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
        <h6 class="mb-0 ms-3 me-auto fw-bold">Update Email</h6>
        <div class="d-flex align-items-center gap-3">

        </div>
    </div>




    <div class="change-profile d-flex flex-column vh-100">
        <div class="vh-100 my-auto overflow-auto p-3">

            <form id="emailForm" enctype="multipart/form-data" method="POST">
                <input type="hidden" name="form_type" value="email_update"> <!-- Hidden input to identify the form -->

                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label mb-1">Email</label>
                    <div class="input-group border bg-white rounded-3 py-1">
                        <input type="text" name="email" class="form-control bg-transparent rounded-0 border-0 px-0" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                </div>
                <?php
                if ($verifyStatus == '1') {
                ?>
                    <p class="text-muted text-center">Your email is already verified.</p>'
                <?php } ?>

                <!-- Submit Button -->
                <button type="submit" onclick="updateEmail(); return false;" class="btn btn-info rounded-4" style="position: absolute; margin-left: 20px; width: 80%;">Update Email</button>
            </form>




            <div class="verify p-4" style="margin-top: 80px;">
                <?php
                if ($verifyStatus == '0') {
                ?>
                    <div class="align-items-start justify-content-between mb-4">
                        <center>
                            <h2 class="my-3 fw-bold">Verification Code</h2>
                            <p class="text-muted mb-0">Hello,<b> <?php echo htmlspecialchars($user['f_name']); ?>!</b> Please enter the verification code sent to your email: <b><?php echo htmlspecialchars($user['email']); ?></b> to complete the verification process.</p>
                        </center>
                    </div>



                    <form id="verificationForm" method="POST">
                        <div class="d-flex gap-1 mb-2">
                            <div class="col">
                                <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel" maxlength="1" required>
                            </div>
                            <div class="col">
                                <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel" maxlength="1" required>
                            </div>
                            <div class="col">
                                <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel" maxlength="1" required>
                            </div>
                            <b style="margin-top: 20px;"> - </b>
                            <div class="col">
                                <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel" maxlength="1" required>
                            </div>
                            <div class="col">
                                <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel" maxlength="1" required>
                            </div>
                            <div class="col">
                                <input type="tel" name="verification_code[]" class="form-control form-control-lg text-center py-3 tel" maxlength="1" required>
                            </div>
                        </div>

                        <div id="countdown" class="alert alert-info" style="display: none;">
                            Please wait <span id="time">05:00</span> before resending the code.
                        </div>

                        <div id="resendButton" style="display: none;">
                            <p class="text-muted text-center mt-4">Didn't receive it? <a href="#" class="ml-2 text-primary">Resend Code</a></p>
                        </div>

                        <button type="submit" class="btn btn-info rounded-4" style="bottom: 10px; position: absolute; margin-left: 20px; width: 80%;">Verify Code</button>
                    </form>
                <?php }  ?>





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

        .form-control {
            padding-left: 15px !important;
            /* Adjust value as needed */
        }
    </style>