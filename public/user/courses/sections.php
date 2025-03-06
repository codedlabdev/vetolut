<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<div class="appointment-upcoming d-flex flex-column vh-100">
    <?php
    include '../inc/p_top.php';
    require_once BASE_DIR . 'lib/user/courses_func.php';

    // Get user ID from session
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if (!$userId) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }

    // Retrieve course_id from the URL if set
    $course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : null;

    if (!$course_id) {
        // Handle the case where course_id is not provided
        die('Course ID is required.');
    }

    // Display success/error messages if they exist
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        unset($_SESSION['error_message']);
    }
    ?>
<link href="<?php echo BASE_URL; ?>public/user/courses/section.css" rel="stylesheet">
<div class="container-fluid py-4">
    <!-- Sections Section -->
    <div id="sections-section" class="section">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="section">
                    <div class="section-header">
                        <h2>Course Section</h2>
                        <button style="display: none;" class="new-section-button">New Section</button>
                    </div>

                    <!-- Template for new sections -->
                    <template id="sectionTemplate">
                        <div class="section-content">
                            <input type="hidden" class="course-id" value="<?php echo $course_id; ?>">
                            <div class="section-title">
                                <h3>Content</h3>
                            </div>
                            <input type="text" class="input-field" placeholder="Title here" required id="titleField">
                            <textarea class="textarea-field" placeholder="Description..."></textarea>
                            <div class="video-upload">
                                <label>Upload Video:</label>
                                <input type="file" class="input-field video-upload-input" required accept="video/*" id="videoField">
                            </div>
                            <div class="action-buttons">
                                <button class="close">Delete</button>
                            </div>
                        </div>
                    </template>

                    <div id="sectionsContainer">
                        <!-- Dynamically added sections will appear here -->
                    </div>

                    <div class="action-buttons main-buttons" style="justify-content: center;">
                        <button type="button" class="btn btn-primary" id="addSectionBtn">
                            <i class="fas fa-plus"></i> Add New Section
                        </button>
                        <button type="button" class="btn btn-success" id="saveSectionBtn" disabled>
                            <i class="fas fa-save"></i> Save Section
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .loader {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
    }
    .loader-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9998;
    }
    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-radius: 50%;
        border-top: 5px solid #3498db;
        -webkit-animation: spin 1s linear infinite;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="loader-overlay" id="overlay"></div>
<div class="loader" id="loader">
    <div class="spinner"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const template = document.getElementById('sectionTemplate');
    const sectionsContainer = document.getElementById('sectionsContainer');
    const addSectionBtn = document.getElementById('addSectionBtn');
    const saveSectionBtn = document.getElementById('saveSectionBtn');
    const loader = document.getElementById('loader');
    const overlay = document.getElementById('overlay');
    let sectionCounter = 0;

    function showLoader() {
        loader.style.display = 'block';
        overlay.style.display = 'block';
        saveSectionBtn.disabled = true;
    }

    function hideLoader() {
        loader.style.display = 'none';
        overlay.style.display = 'none';
        saveSectionBtn.disabled = false;
    }

    async function saveSection(e) {
        e.preventDefault();
        showLoader();

        const sections = sectionsContainer.querySelectorAll('.section-content');
        const formData = new FormData();

        sections.forEach((section, index) => {
            const courseId = section.querySelector('.course-id').value;
            const title = section.querySelector('.input-field').value;
            const description = section.querySelector('.textarea-field').value;
            const videoFile = section.querySelector('.video-upload-input').files[0];

            formData.append(`sections[${index}][course_id]`, courseId);
            formData.append(`sections[${index}][title]`, title);
            formData.append(`sections[${index}][description]`, description);
            if (videoFile) {
                formData.append(`sections[${index}][video]`, videoFile);
            }
        });

        try {
            await fetch('process_sections.php', {
                method: 'POST',
                body: formData
            });
            
            // Redirect immediately after form submission
            window.location.href = '<?php echo BASE_URL; ?>public/user/user_profile/index.php';
        } catch (error) {
            hideLoader();
            console.error('Error:', error);
        }
    }

    // Add event listener for save button
    saveSectionBtn.addEventListener('click', saveSection);

    function checkSaveButtonVisibility() {
        const sections = sectionsContainer.querySelectorAll('.section-content');
        let canSave = true;

        sections.forEach(section => {
            const title = section.querySelector('.input-field').value.trim();
            const videoUpload = section.querySelector('.video-upload-input').files.length;

            if (!title || videoUpload === 0) {
                canSave = false;
            }
        });

        saveSectionBtn.disabled = !canSave;
    }

    sectionsContainer.addEventListener('input', checkSaveButtonVisibility);
    sectionsContainer.addEventListener('change', checkSaveButtonVisibility);

    addSectionBtn.addEventListener('click', function() {
        sectionCounter++;
        
        // Clone the template content
        const newSection = template.content.cloneNode(true);
        const sectionContent = newSection.querySelector('.section-content');
        
        // Update unique IDs for the new section
        const videoUpload = sectionContent.querySelector('.video-upload-input');
        videoUpload.id = `video-upload-${sectionCounter}`;
        const label = videoUpload.previousElementSibling;
        label.setAttribute('for', `video-upload-${sectionCounter}`);

        // Add to container
        sectionsContainer.appendChild(sectionContent);

        // Trigger reflow to ensure animation works
        sectionContent.offsetHeight;

        // Show with animation
        requestAnimationFrame(() => {
            sectionContent.classList.add('show');
        });

        // Immediately disable the Save Section button
        saveSectionBtn.disabled = true;

        // Add event listeners to inputs to check for content
        const inputs = sectionContent.querySelectorAll('.input-field, .textarea-field');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                checkSaveButtonVisibility();
            });
        });

        // Maximum file size in bytes (100 MB)
        const MAX_FILE_SIZE = 100 * 1024 * 1024;

        // Add event listener for video upload input
        videoUpload.addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.size > MAX_FILE_SIZE) {
                alert('File size exceeds 100 MB. Please upload a smaller file.');
                this.value = ''; // Clear the input
                checkSaveButtonVisibility(); // Update button state
            }
        });

        // Add delete functionality
        const deleteBtn = sectionContent.querySelector('.close');
        deleteBtn.addEventListener('click', function() {
            sectionContent.classList.remove('show');
            setTimeout(() => {
                sectionContent.remove();
                checkSaveButtonVisibility(); // Check visibility after deletion
            }, 300);
        });
    });
});
</script>