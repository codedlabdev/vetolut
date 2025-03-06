<style>
    hide {
        display: none !important;
    }

    /* Profile image styling */
    .profile-img {
        border-radius: 50%;
        /* Makes the image circular */
        width: 40px;
        height: 40px;
        object-fit: cover;
        /* Ensures image covers the area without stretching or zooming */
    }

    /* Notification count styling */
    .notification-count {
        position: absolute;
        top: 8px;
        right: 8px;
        background-color: red;
        color: white;
        font-size: 12px;
        border-radius: 50%;
        padding: 2px 6px;
        font-weight: bold;
    }

    /* Profile image styling */
    .profile-image {
        width: 50px;
        /* Set the desired width */
        height: 50px;
        /* Set the desired height */
        border-radius: 50%;
        /* Makes the image circular */
        object-fit: cover;
        /* Keeps the image within bounds without stretching */
        border: 2px solid #f3f3f3;
        /* Optional: adds a subtle border */
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        /* Optional: subtle shadow effect */
    }



    .footer-bottom-nav.active {
        color: #003366;
    }


    .footer-bottom-nav.active:after {

        background: #003263 !important;

    }

    .shadow {
        box-shadow: unset;
    }


    :root {
        --primary-color: #003366;
        /* This is a shade of blue */
    }

    .bg-white {
        --bs-bg-opacity: 1;

    }

    body {
        font-family: karla, sans-serif;
        font-size: 18px !important;
    }

    .mb-1 {
        margin-bottom: .25rem !important;
        font-size: large;
        font-weight: 800;
    }

    .img-fluid {
        max-width: 100%;
        height: auto;
        height: 40px !important;
        width: 45px !important;
    }



    .mdi-18px.mdi-set,
    .mdi-18px.mdi:before {
        font-size: 30px !important;
    }


    .hc-offcanvas-nav li:not(.custom-content) a {
        padding: 14px 17px;
        font-size: 16px !important;

    }


    a {
        color: #4f4f4f;
        text-decoration: none !important;
    }

    a:hover {
        text-decoration: none !important;
    }


    a:active {
        text-decoration: none;
    }


    /*

follow user
*/

    .suggestions-card {

        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        position: relative;
    }

    .suggestions-header {
        font-weight: bold;
        color: #003366;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }

    .suggestions-header-title {
        margin: 0;
    }

    .suggestions-header::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 90px;
        transform: translateX(-50%);
        width: 185px;
        height: 2px;
        background-color: #003366b;
    }

    .spinner {
        font-size: 1em;
        color: #003366b;
        cursor: pointer;
        transition: transform 0.2s;
    }

    /* Spinning animation */
    .spinner.active {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .suggestion {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e0e0e0;
    }

    .suggestion:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .suggestion-info {
        flex-grow: 1;
    }

    .suggestion-info p {
        margin: 0;
    }

    .name {
        font-weight: bold;
    }

    .role {
        font-size: 0.9em;
        color: #757575;
    }

    .follow-btn {
        background-color: #003366;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        cursor: pointer;
    }

    .unfollow-btn {
        background-color: #e33e31;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        cursor: pointer;
    }

    .message-btn {
        background-color: #18528c;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        cursor: pointer;
    }



    .follow-btn:hover {
        background-color: #00574b;
    }

    .spinner.loading {
        animation: spin 1s infinite linear;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }


    .read-more {
        color: #007bff;
        text-decoration: none;
        cursor: pointer;
    }

    .read-more:hover {
        text-decoration: underline;
    }

    .post-footer {
        display: flex;
        justify-content: space-between;
        /* Distributes space between items */
        align-items: center;
        /* Aligns items vertically centered */
        margin-top: 10px;
        /* Adds some spacing above the footer */
    }

    .post-actions {
        margin-left: auto;
        /* Pushes the actions to the far right */
    }

    .edit-icon {
        color: #808080;
        /* Gray color for edit icon */
        margin-left: 10px;
        /* Space between icons */
        text-decoration: none;
        /* Remove underline */
    }

    .delete-icon {
        color: #c95353;
        /* Red color for delete icon */
        margin-left: 10px;
        /* Space between icons */
        text-decoration: none;
        /* Remove underline */
    }

    .edit-icon:hover {
        color: #666666;
        /* Darker gray on hover for edit */
    }

    .delete-icon:hover {
        color: #a43d3d;
        /* Darker red on hover for delete */
    }

    /* Default styling for the like button */
    .like-button {
        background-color: transparent;
        border: none;
        color: #888;
        font-size: 16px;
        cursor: pointer;
        padding: 5px 10px;
        display: flex;
        align-items: center;
    }

    /* Add a default styling when it's in loading state */
    .like-button.loading {
        background-color: #cccccc;
        /* Light gray background while loading */
        cursor: not-allowed;
        /* Disable cursor when loading */
    }

    /* Styling for liked state */
    .like-button.liked {
        color: #003366;
        /* Active like color */
    }

    /* Icon adjustment for better visuals */
    .like-button i {
        margin-right: 5px;
    }


    /* Style for the notification count */
    .notification-count {
        position: absolute;
        top: 5px;
        right: 65px;
        background-color: red;
        color: white;
        font-size: 17px;
        font-weight: bold;
        padding: 1px 8px;
        border-radius: 35%;
    }


    /* Comment Section */
    .comments-section {
        margin-top: 20px;
        padding-top: 10px;
        border-top: 1px solid #e0e0e0;
        max-height: 300px;
        overflow-y: auto;
    }

    .comment {
        display: flex;
        align-items: flex-start;
        margin-bottom: 15px;
        position: relative;
        /* For positioning the delete button */
    }

    .comment img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .comment-content {
        background-color: #f0f2f5;
        padding: 8px 12px;
        border-radius: 15px;
        font-size: 14px;
        max-width: 80%;
        width: 100%;
        position: relative;
        /* Make sure the delete button is positioned relative to the content */
    }

    .comment-time {
        color: #888;
        font-size: 12px;
        margin-top: 5px;
    }

    /* Delete Button */
    .delete-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        padding: 5px;
        color: #ff4d4d;
        border-radius: 50%;
        cursor: pointer;
        font-size: 15px;
        transition: background-color 0.3s ease;
    }

    .delete-btn:hover {
        background-color: #ff1a1a;
        color: #fff;
    }

    /* Sticky Comment Input Box */
    .comment-input-box {
        display: flex;
        align-items: center;
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 800px;
        background-color: white;
        padding: 10px;
        border-top: 1px solid #ddd;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        z-index: 10;
    }

    .comment-input-box img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .comment-input-box textarea {
        flex: 1;
        max-height: 100px;
        resize: none;
        overflow-y: auto;
        padding: 10px;
        font-size: 14px;
        border-radius: 20px;
        border: 1px solid #ddd;
        outline: none;
    }

    .emoji-btn,
    .mic-btn,
    .send-btn {
        background: none;
        border: none;
        cursor: pointer;
        margin-left: 5px;
        font-size: 20px;
        color: #555;
    }

    .emoji-btn:hover,
    .mic-btn:hover,
    .send-btn:hover {
        color: #1877f2;
    }

    /* Comment Section */
    .comments-section {
        margin-top: 20px;
        padding-top: 10px;
        border-top: 1px solid #e0e0e0;
        max-height: 300px;
        /* Set a fixed height for the comments section */
        overflow-y: scroll;
        /* Make comments scrollable */
        scrollbar-width: none;
        /* Firefox */
    }

    .comments-section::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari, and Opera */
    }


    .emoji-wysiwyg-editor {
        margin-right: 30px !important;
        height: 55px !important;
    }


    .emoji-picker-icon {

        right: 88px !important;
        top: 20px !important;

    }


    .container {

        margin-bottom: 40px;
    }



    /* Skeleton Loader Styles */
    .post-skeleton {
        display: block;
    }

    .post-header-skeleton,
    .skeleton-footer {
        display: flex;
        align-items: center;
    }

    .skeleton-img {
        width: 100%;
        height: 50px;
        border-radius: 5%;
        background-color: #e0e0e0;
        margin-right: 10px;
    }

    .skeleton-text {
        flex: 1;
    }

    .skeleton-line {
        height: 12px;
        background-color: #e0e0e0;
        margin: 6px 0;
    }

    .skeleton-line.short {
        width: 50%;
    }

    .skeleton-textarea {
        width: 100%;
        height: 20px;
        background-color: #e0e0e0;
        margin: 10px 0;
    }

    .post-img-skeleton {
        width: 100%;
        height: 200px;
        background-color: #e0e0e0;
        margin: 10px 0;
    }

    /* Hide the skeleton when the page is fully loaded */
    .post-content {
        display: none;
    }

    .post.loaded .post-content {
        display: block;
    }

    .post.loaded .post-skeleton {
        display: none;
    }
</style>



<script src="<?php echo BASE_URL; ?>assets/user/vender/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo BASE_URL; ?>assets/user/vender/jquery/jquery.min.js"></script>

<script src="<?php echo BASE_URL; ?>assets/user/vender/sidebar/hc-offcanvas-nav.js"></script>



<script src="https://unpkg.com/emoji-mart@latest/dist/browser.js"></script>


<script>
    window.addEventListener('load', function() {
        // Once the page is fully loaded, remove the skeleton loader and display content
        const postElements = document.querySelectorAll('.post');
        postElements.forEach(post => {
            post.classList.add('loaded');
        });
    });


    function toggleSpinner(element) {
        element.classList.add('active');
        setTimeout(() => {
            element.classList.remove('active');
        }, 2000); // Spinner will stop spinning after 2 seconds
    }


    // Update user activity every 5 minutes (300000 ms)
    setInterval(function() {
        fetch('update_activity.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("User activity updated successfully");
                }
            })
            .catch(error => console.error("Error updating activity:", error));
    }, 300000); // 300000 ms = 5 minutes


    // Handling AI Tabs Section
    const aiTabs = document.querySelectorAll('#ai-tabs .tab');
    const aiTabContents = document.querySelectorAll('.tab-content'); // Updated class
    aiTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            aiTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const target = tab.getAttribute('data-target');
            aiTabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === target.slice(1)) {
                    content.classList.add('active');
                }
            });
        });
    });

    // Handling Network Tabs Section
    const networkTabs = document.querySelectorAll('.network-tab');
    const networkTabContents = document.querySelectorAll('.network-tab-content');
    networkTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            networkTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const target = tab.getAttribute('data-target');
            networkTabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === target.slice(1)) {
                    content.classList.add('active');
                }
            });
        });
    });
</script>


</body>

</html>