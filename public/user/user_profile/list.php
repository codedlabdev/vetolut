<?php
//list.php

?>

<div class="appointment-upcoming d-flex flex-column vh-100">
    <?php include '../inc/p_top.php'; ?>

    <div class="change-profile d-flex flex-column vh-100">
        <div class="rounded-4 shadow overflow-hidden">
            <a href="edit_profile.php" class="link-dark">
                <div class="bg-white d-flex align-items-center justify-content-between p-3 border-bottom">
                    <h6 class="m-0">Profile Information</h6>
                    <span class="mdi mdi-chevron-right mdi-24px icon shadow rounded-pill"></span>
                </div>
            </a>

            <a href="verify.php" class="link-dark">
                <div class="bg-white d-flex align-items-center justify-content-between p-3 border-bottom">
                    <h6 class="m-0">Verifications</h6>
                    <span class="mdi mdi-chevron-right mdi-24px icon shadow rounded-pill"></span>
                </div>
            </a>

            <a href="passw.php" class="link-dark">
                <div class="bg-white d-flex align-items-center justify-content-between p-3 border-bottom">
                    <h6 class="m-0">Update Password</h6>
                    <span class="mdi mdi-chevron-right mdi-24px icon shadow rounded-pill"></span>
                </div>
            </a>

            <a href="info.php" class="link-dark">
                <div class="bg-white d-flex align-items-center justify-content-between p-3 border-bottom">
                    <h6 class="m-0">Update Information</h6>
                    <span class="mdi mdi-chevron-right mdi-24px icon shadow rounded-pill"></span>
                </div>
            </a>
        </div>

        <!-- Container for loading new content with slide animation -->
        <div id="content-container" class="slide-container"></div>
    </div>

    <?php include '../inc/float_nav.php'; ?>
    <?php include '../inc/side_menu.php'; ?>
    <?php include '../footer.php'; ?>

    <style>
        .slide-container {
            position: fixed;
            top: 0;
            left: 100%;
            width: 100%;
            height: 100%;
            background-color: white;
            transition: transform 0.5s ease;
            z-index: 10;
            overflow-y: auto;
        }

        .slide-in {
            transform: translateX(-100%);
        }
    </style>

    <script>
        function loadContent(url) {
            const contentContainer = document.getElementById('content-container');

            contentContainer.classList.remove('slide-in');

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    contentContainer.innerHTML = html;

                    // Restart the CSS animation
                    void contentContainer.offsetWidth;
                    contentContainer.classList.add('slide-in');
                })
                .catch(error => {
                    console.error('Error loading content:', error);
                });
        }



        function displaySelectedImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profile-image');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function updateProfile() {
            const formData = new FormData(document.getElementById('profileForm'));

            fetch('edit_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    const messageContainer = document.getElementById('profileMessageContainer');

                    // Show success or error message
                    if (data.success) {
                        messageContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                        if (data.image_path) {
                            document.getElementById('profile-image').src = data.image_path;
                        }
                    } else {
                        messageContainer.innerHTML = `<div class="alert alert-danger">Error: ${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const messageContainer = document.getElementById('profileMessageContainer');
                    messageContainer.innerHTML = `<div class="alert alert-danger">An unexpected error occurred: ${error.message}. Please try again later.</div>`;
                });
        }
    </script>

    <script>
        function updateEmail() {
            const formData = new FormData(document.getElementById('emailForm'));

            fetch('verify.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const messageContainer = document.getElementById('messageContainer'); // Get the message container

                    if (data.success) {
                        messageContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`; // Display success message
                    } else {
                        messageContainer.innerHTML = `<div class="alert alert-danger">Error: ${data.message}</div>`; // Display error message
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const messageContainer = document.getElementById('messageContainer');
                    messageContainer.innerHTML = `<div class="alert alert-danger">An unexpected error occurred. Please try again later.</div>`; // Display generic error message
                });
        }
    </script>


    <script>
        function updatePass() {
            const formData = new FormData(document.getElementById('emailForm'));

            fetch('passw.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const messageContainer = document.getElementById('P_MessageContainer'); // Get the message container

                    if (data.success) {
                        messageContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`; // Display success message
                    } else {
                        messageContainer.innerHTML = `<div class="alert alert-danger">Error: ${data.message}</div>`; // Display error message
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const messageContainer = document.getElementById('P_MessageContainer');
                    messageContainer.innerHTML = `<div class="alert alert-danger">An unexpected error occurred. Please try again later.</div>`; // Display generic error message
                });
        }
    </script>


    <script>
        function updateUserInfo() {
            const formData = new FormData(document.getElementById('infoForm'));

            fetch('info.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const messageContainer = document.getElementById('info_MessageContainer');

                    if (data.success) {
                        messageContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`; // Display success message
                    } else {
                        messageContainer.innerHTML = `<div class="alert alert-danger">Error: ${data.message}</div>`; // Display error message
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const messageContainer = document.getElementById('info_MessageContainer');
                    messageContainer.innerHTML = `<div class="alert alert-danger">An unexpected error occurred. Please try again later.</div>`;
                });
        }
    </script>



    <script>
        // JavaScript to remove the success message after 5 seconds
        setTimeout(function() {
            var messages = document.getElementsByClassName("message"); // Use class name instead of id
            if (messages.length > 0) {
                var message = messages[0]; // Access the first element with that class
                message.style.display = "none"; // Hide the message
            }
        }, 9000); // 5000 milliseconds = 5 seconds
    </script>