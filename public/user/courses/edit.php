<?php
include '../inc/p_top.php';
require_once BASE_DIR . 'lib/user/courses_func.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

// Get course ID from URL
$courseId = $_GET['id'] ?? 0;
if (!$courseId) {
    header('Location: ' . BASE_URL . 'user/user_profile/index.php');
    exit;
}

// Get course details
$course = getCourse($courseId, $_SESSION['user_id']);
if (!$course) {
    $_SESSION['error_message'] = 'Course not found or you don\'t have permission to edit it.';
    header('Location: ' . BASE_URL . 'user/user_profile/index.php');
    exit;
}

// Get categories for dropdown
$categories = getCourseCategories();
?>

<div class="container-fluid py-4">
    <form id="courseEditForm" method="POST" action="process_course_edit.php" enctype="multipart/form-data">
        <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($courseId); ?>">
        
        <!-- Basic Section -->
        <div class="section">
            <div class="card">
                <div class="card-body">
                    <!-- Course Title -->
                    <div class="mb-4">
                        <label for="courseTitle" class="form-label">Course Title</label>
                        <input type="text" class="form-control" id="courseTitle" name="courseTitle" maxlength="50" required 
                               value="<?php echo htmlspecialchars($course['title']); ?>">
                        <small class="text-muted">Maximum 50 characters</small>
                    </div>

                    <!-- Course Description -->
                    <div class="mb-4">
                        <label for="courseDescription" class="form-label">Course Description</label>
                        <textarea class="form-control" id="courseDescription" name="courseDescription" rows="4" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                    </div>

                    <!-- Course Category -->
                    <div class="mb-4">
                        <label for="courseCategory" class="form-label">Course Category</label>
                        <select class="form-select styled-select" id="courseCategory" name="courseCategory" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>" 
                                        <?php echo ($category['id'] == $course['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Section -->
        <div class="section">
            <div class="card">
                <div class="card-body">
                    <!-- Preview Image -->
                    <div class="mb-4">
                        <label for="previewImage" class="form-label">Course Preview Image</label>
                        <?php if (!empty($course['banner_image'])): ?>
                            <div class="current-image mb-2">
                                <img src="<?php echo BASE_URL . $course['banner_image']; ?>" 
                                     alt="Current preview image" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="previewImage" name="previewImage" accept="image/*">
                        <small class="text-muted">Recommended size: 1280x720 pixels. Leave empty to keep current image.</small>
                        <div id="imagePreview" class="mt-2 preview-container"></div>
                    </div>

                    <!-- Preview Video -->
                    <div class="mb-4">
                        <label for="previewVideo" class="form-label">Course Preview Video</label>
                        <?php if (!empty($course['intro_video'])): ?>
                            <div class="current-video mb-2">
                                <video controls class="img-thumbnail" style="max-width: 100%; max-height: 300px;">
                                    <source src="<?php echo BASE_URL . $course['intro_video']; ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="previewVideo" name="previewVideo" accept="video/*">
                        <small class="text-muted">Maximum video size: 100MB. Leave empty to keep current video.</small>
                        <div id="videoPreview" class="mt-2 preview-container"></div>
                    </div>

                    <!-- Duration -->
                    <div class="mb-4">
                        <label for="duration" class="form-label">Duration</label>
                        <div class="row">
                            <div class="col-6">
                                <select class="form-select styled-select" id="durationHours" name="durationHours" required>
                                    <?php for($i = 0; $i < 24; $i++): ?>
                                        <option value="<?= $i ?>" <?php echo ($course['duration_hr'] == $i) ? 'selected' : ''; ?>>
                                            <?= $i ?> Hours
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <select class="form-select styled-select" id="durationMinutes" name="durationMinutes" required>
                                    <option value="" disabled selected>Select Minutes</option>
                                    <?php for($i = 1; $i < 60; $i++): ?>
                                        <option value="<?= $i ?>" <?php echo ($course['duration_min'] == $i) ? 'selected' : ''; ?>>
                                            <?= $i ?> Minutes
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Price Section -->
        <div class="section">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <label for="coursePriceOption" class="form-label">Course Price Option</label>
                        <select id="coursePriceOption" name="coursePriceOption" class="form-select styled-select" 
                                onchange="togglePriceInput(this.value === 'free')" required>
                            <option value="free" <?php echo ($course['price'] == 0) ? 'selected' : ''; ?>>Free</option>
                            <option value="paid" <?php echo ($course['price'] > 0) ? 'selected' : ''; ?>>Paid</option>
                        </select>
                    </div>
                    <div id="price-input" class="mb-4" <?php echo ($course['price'] == 0) ? 'style="display: none;"' : ''; ?>>
                        <label for="coursePrice" class="form-label">Course Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="coursePrice" name="coursePrice" 
                                   min="0" step="0.01" value="<?php echo htmlspecialchars($course['price']); ?>" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="section">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <label for="publishStatus" class="form-label">Status</label>
                        <select id="publishStatus" name="publishStatus" class="form-select" required>
                            <option value="draft" <?php echo ($course['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                            <option value="publish" <?php echo ($course['status'] == 'published') ? 'selected' : ''; ?>>Publish</option>
                        </select>
                    </div>
                    <div class="form-container" style="text-align: center;">
                        <div class="mb-4">
                            <button type="submit" id="submitButton" class="btn btn-success btn-block">
                                <span class="button-text">Update Course</span>
                                <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                            </button>
                            <button type="button" id="deleteButton" class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <span class="button-text">Delete Course</span>
                            </button>
                            <a href="update_section.php?course_id=<?php echo $courseId; ?>" class="btn btn-info btn-block" id="editSectionButton">
                                <span class="button-text">Edit Section</span>
                                <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this course? This action cannot be undone. All associated files (images and videos) will be permanently deleted.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <span class="button-text">Delete Course</span>
                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    
    document.getElementById('confirmDelete').addEventListener('click', function() {
        const button = this;
        const buttonText = button.querySelector('.button-text');
        const spinner = button.querySelector('.spinner-border');
        
        // Create and submit a form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'process_course_delete.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'course_id';
        input.value = '<?php echo $courseId; ?>';
        
        form.appendChild(input);
        document.body.appendChild(form);
        
        // Show loading state
        button.disabled = true;
        buttonText.textContent = 'Deleting...';
        spinner.classList.remove('d-none');
        
        // Submit the form
        form.submit();
    });
});
</script>

<style>
    .section {
        margin-bottom: 30px!important;
    }
    .form-container {
        text-align: center;
    }
    button:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }
    .preview-container {
        max-width: 100%;
        overflow: hidden;
    }
    .preview-container img,
    .preview-container video {
        max-width: 100%;
        max-height: 300px;
        object-fit: contain;
    }
    .logout {
        display: none!important;
    }
</style>

<script>
document.getElementById('courseEditForm').addEventListener('submit', function(e) {
    const submitButton = document.getElementById('submitButton');
    const buttonText = submitButton.querySelector('.button-text');
    const spinner = submitButton.querySelector('.spinner-border');
    
    // Disable button and show spinner
    submitButton.disabled = true;
    spinner.classList.remove('d-none');
    buttonText.textContent = 'Updating Course...';
});

function togglePriceInput(isFree) {
    const priceInput = document.getElementById('price-input');
    priceInput.style.display = isFree ? 'none' : 'block';
    document.getElementById('coursePrice').required = !isFree;
}

// Preview image before upload
document.getElementById('previewImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" alt="Preview">`;
        }
        reader.readAsDataURL(file);
    }
});

// Preview video before upload
document.getElementById('previewVideo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Check file size
        const maxSize = 100 * 1024 * 1024; // 100MB
        if (file.size > maxSize) {
            alert('Video file size must be less than 100MB');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('videoPreview');
            preview.innerHTML = `
                <video controls class="img-thumbnail">
                    <source src="${e.target.result}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>`;
        }
        reader.readAsDataURL(file);
    }
});
</script>
