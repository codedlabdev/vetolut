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
        echo '<div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                ' . htmlspecialchars($_SESSION['success_message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                ' . htmlspecialchars($_SESSION['error_message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['error_message']);
    }
    ?>
<link href="<?php echo BASE_URL; ?>public/user/courses/section.css" rel="stylesheet">


<div class="container-fluid py-4">
    <!-- Sections Section -->
    <div id="section" class="section">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="section">
                    <div class="section-header">
                        <h2>Course Section</h2>
                    </div>

                    <!-- Template for new sections -->

                        <?php
                        $sections = getCourseSections($course_id);
                        if ($sections) {
                            foreach ($sections as $section) {
                        ?>
                            <div class="section-container" data-section-id="<?php echo $section['id']; ?>">
                                <input type="hidden" class="course-id" value="<?php echo $course_id; ?>">
                                <input type="hidden" class="section-id" value="<?php echo $section['id']; ?>">
                                <div class="section-title">
                                    <h3>Content</h3>
                                </div>
                                <input type="text" class="input-field" placeholder="Title here"
                                       value="<?php echo htmlspecialchars($section['title']); ?>" required>
                                <textarea class="textarea-field" placeholder="Description..."><?php echo htmlspecialchars($section['description']); ?></textarea>

                                <?php if (!empty($section['video_url'])): ?>
                                <div class="video-preview">
                                    <?php 
                                    $videoPath = BASE_URL . 'public/'. $section['video_url'];
                                    ?>
                                    <video controls preload="metadata" width="100%" style="max-width: 600px; margin: 10px 0;">
                                        <source src="<?php echo htmlspecialchars($videoPath); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                <?php endif; ?>

                                <div class="video-upload">
                                    <label>Upload Video:</label>
                                    <input type="file" class="input-field video-upload-input" accept="video/*">
                                    <!--
                                    </?php if (!empty($section['video_url'])): ?>
                                    <small>Current video: </?php echo basename($section['video_url']); ?></small>
                                    </?php endif; ?>
                                    -->
                                </div>

                                <div class="action-buttons">
                                <button class="save-section btn btn-succes" style="background-color:rgba(6, 151, 37, 0.6); margin-right: 10px;">Save Changes</button>
                                <button class="delete-section btn btn-danger" style="background-color: #dc3545;">Delete</button>
                                </div>

                            </div>
                        <?php
                            }
                        } else {
                            echo '<p>No sections found for this course.</p>';
                        }
                        ?>
                  </div>
            </div>
        </div>
    </div>


    <!-- Sections Section -->
    <div id="sections-section" class="section">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="section">
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
    .video-preview {
        width: 100%;
        margin: 15px 0;
    }
    .video-preview video {
        background: #000;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 4px;
    }
    .delete-section.btn-danger {
        background-color: #dc3545;
    }

    .alert-success {
     text-align: center;
    margin: 50px;
}
 
</style>

<div class="loader-overlay" id="overlay"></div>
<div class="loader" id="loader">
    <div class="spinner"></div>
</div>

<script>
// Auto-close alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.add('animate__fadeOut');
                setTimeout(() => {
                    alert.remove();
                }, 1000);
            }, 5000);
        });

        // Enable Bootstrap close buttons
        const closeButtons = document.querySelectorAll('.btn-close');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const alert = this.closest('.alert');
                alert.classList.add('animate__fadeOut');
                setTimeout(() => {
                    alert.remove();
                }, 1000);
            });
        });
    });
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

            // Reload the current page immediately
            window.location.reload();

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

    // Handle section deletion
    document.querySelectorAll('.close').forEach(button => {
        button.addEventListener('click', async function(e) {
            const section = e.target.closest('.section');
            const sectionId = section.dataset.sectionId;

            if (!sectionId) {
                // If no section ID, this is a new unsaved section
                section.remove();
                return;
            }

            // Show confirmation dialog
            if (!confirm('Are you sure you want to delete this section? This action cannot be undone.')) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('section_id', sectionId);

                const response = await fetch('process_sections.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    // Remove the section from the DOM
                    section.remove();

                    // Show success message
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'alert alert-success animate__animated animate__fadeIn';
                    messageDiv.textContent = result.message;
                    document.querySelector('.container-fluid').insertAdjacentElement('afterbegin', messageDiv);

                    // Remove the message after 3 seconds
                    setTimeout(() => {
                        messageDiv.classList.replace('animate__fadeIn', 'animate__fadeOut');
                        setTimeout(() => messageDiv.remove(), 1000);
                    }, 3000);
                } else {
                    throw new Error(result.error || 'Failed to delete section');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });
    });
});





// Add event listeners for delete buttons
// Replace the delete button event listener with this updated version
document.querySelectorAll('.delete-section').forEach(button => {
    button.addEventListener('click', async function() {
        if (confirm('Are you sure you want to delete this section?')) {
            const sectionContainer = this.closest('.section-container');
            const sectionId = sectionContainer.querySelector('.section-id').value;

            try {
                const response = await fetch('process_sections.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&section_id=${sectionId}`
                });

                const result = await response.json();

                if (result.success) {
                    // Show success message above the Save Changes button
                    const actionButtons = sectionContainer.querySelector('.action-buttons');
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show animate__animated animate__fadeIn';
                    alert.innerHTML = `
                        ${result.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    actionButtons.insertAdjacentElement('beforebegin', alert);

                    // Auto-hide message after 5 seconds
                    setTimeout(() => {
                        alert.classList.add('animate__fadeOut');
                        setTimeout(() => {
                            alert.remove();
                        }, 1000);
                    }, 5000);

                    // Remove section after delay
                    setTimeout(() => {
                        sectionContainer.remove();
                    }, 2000);
                } else {
                    // Show error message above the Save Changes button
                    const actionButtons = sectionContainer.querySelector('.action-buttons');
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn';
                    alert.innerHTML = `
                        Error: ${error.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    actionButtons.insertAdjacentElement('beforebegin', alert);
                }
            } catch (error) {
                // Show error message
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn';
                alert.innerHTML = `
                    Error: ${error.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                sectionContainer.insertAdjacentElement('beforebegin', alert);
            }
        }
    });
});
// Add event listeners for save buttons
document.querySelectorAll('.save-section').forEach(button => {
    button.addEventListener('click', async function() {
        const sectionContainer = this.closest('.section-container');
        const actionButtons = this.closest('.action-buttons');
        const sectionId = sectionContainer.querySelector('.section-id').value;
        const courseId = sectionContainer.querySelector('.course-id').value;
        const title = sectionContainer.querySelector('.input-field').value;
        const description = sectionContainer.querySelector('.textarea-field').value;
        const videoInput = sectionContainer.querySelector('.video-upload-input');

        // Create FormData object
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('section_id', sectionId);
        formData.append('course_id', courseId);
        formData.append('title', title);
        formData.append('description', description);

        // Add video file if selected
        if (videoInput && videoInput.files.length > 0) {
            formData.append('video', videoInput.files[0]);
        }

        try {
            // Show loading state
            button.disabled = true;
            button.innerHTML = 'Saving...';

            const response = await fetch('process_sections.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Show success message above the buttons
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show animate__animated animate__fadeIn';
                alert.innerHTML = `
                    ${result.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                actionButtons.insertAdjacentElement('beforebegin', alert);

                // Refresh the page and scroll to top
                setTimeout(() => {
                    window.location.href = window.location.pathname + window.location.search + '#top';
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error(result.message || 'Failed to save section');
            }
        } catch (error) {
            // Show error message above the buttons
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn';
            alert.innerHTML = `
                Error: ${error.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            actionButtons.insertAdjacentElement('beforebegin', alert);
        } finally {
            // Reset button state
            button.disabled = false;
            button.innerHTML = 'Save Changes';
        }
    });
});
</script>
