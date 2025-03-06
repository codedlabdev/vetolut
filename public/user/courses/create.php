<?php
include '../inc/p_top.php';
require_once BASE_DIR . 'lib/user/courses_func.php';

// Get user ID from session
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$userId) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

// Fetch categories
$categories = getCourseCategories();

// If no categories found, show error
if (empty($categories)) {
    echo '<div class="alert alert-danger">Error: No course categories available. Please try again later.</div>';
    exit;
}

?>
<div class="container-fluid py-4">
    <!-- Course Creation Form -->
    <form id="courseBasicForm" method="POST" action="process_course.php" enctype="multipart/form-data">
        <!-- Section Container -->
        <div id="sections-container">
            <!-- Basic Section -->
            <div id="basic-section" class="section">
                <div class="card">
                    <div class="card-body">
                        <!-- Course Title -->
                        <div class="mb-4">
                            <label for="courseTitle" class="form-label">Course Title</label>
                            <input type="text" class="form-control" id="courseTitle" name="courseTitle" maxlength="50" required>
                            <div class="invalid-feedback">Please enter a course title</div>
                            <small class="text-muted">Maximum 50 characters</small>
                        </div>

                        <!-- Course Description -->
                        <div class="mb-4">
                            <label for="courseDescription" class="form-label">Course Description</label>
                            <textarea class="form-control" id="courseDescription" name="courseDescription" rows="4" required></textarea>
                            <div class="invalid-feedback">Please enter a course description</div>
                        </div>

                        <!-- Course Category -->
                        <div class="mb-4">
                            <label for="courseCategory" class="form-label">Course Category</label>
                            <select class="form-select styled-select" id="courseCategory" name="courseCategory" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a category</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media Section -->
            <div id="basic-section" class="section" style="">
                <div class="card">
                    <div class="card-body">
                        <!-- Preview Image -->
                        <div class="mb-4">
                            <label for="previewImage" class="form-label">Course Preview Image</label>
                            <input type="file" class="form-control" id="previewImage" name="previewImage" accept="image/*" required>
                            <small class="text-muted">Recommended size: 1280x720 pixels</small>
                            <div id="imagePreview" class="mt-2 preview-container"></div>
                        </div>

                        <!-- Preview Video -->
                        <div class="mb-4">
                            <label for="previewVideo" class="form-label">Course Preview Video</label>
                            <input type="file" class="form-control" id="previewVideo" name="previewVideo" accept="video/*" required>
                            <small class="text-muted">Maximum video size: 100MB</small>
                            <div id="videoPreview" class="mt-2 preview-container"></div>
                        </div>

                        <!-- Duration -->
                        <div class="mb-4">
                            <label for="duration" class="form-label">Duration</label>
                            <div class="row">
                                <div class="col-6">
                                    <select class="form-select styled-select" id="durationHours" name="durationHours" required>
                                        <?php for($i = 0; $i < 24; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?> Hours</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select class="form-select styled-select" id="durationMinutes" name="durationMinutes" required>
                                        <option value="" disabled selected>Select Minutes</option>
                                        <?php for($i = 1; $i < 60; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?> Minutes</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <small class="text-muted">Select hours and minutes</small>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Price Section -->
            <div id="basic-section" class="section">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="coursePriceOption" class="form-label">Course Price Option</label>
                            <select id="coursePriceOption" name="coursePriceOption" class="form-select styled-select" onchange="togglePriceInput(this.value === 'free')" required>
                                <option value="free" selected>Free</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                        <div id="price-input" class="mb-4" style="display: none;">
                            <label for="coursePrice" class="form-label">Course Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="coursePrice" name="coursePrice" min="0" step="0.01" value="0" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Publish Section -->
            <div id="basic-section" class="section">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Review and Publish</h5>
                        <div class="alert alert-info">
                            Please review your course details before publishing.
                        </div>
                        <div id="course-summary">
                            <!-- Course summary will be displayed here -->
                        </div>
                        <div class="mb-3">
                            <label for="publishStatus" class="form-label">Status</label>
                            <select id="publishStatus" name="publishStatus" class="form-select" required>
                                <option value="draft">Draft</option>
                                <option value="publish">Publish</option>
                            </select>
                        </div>
                        <div class="form-container">
                            <div class="mb-4">
                                <button type="submit" id="submitButton" class="btn btn-primary btn-block">
                                    <span class="button-text">Create Course</span>
                                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Navigation Buttons -->

    </form>
</div>
<style>
    .section {
        margin-bottom: 30px!important;
    }
    .form-container {
        text-align: right;
    }
    /* Add loading state styles */
    button:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }
    .logout {
        display: none!important;
    }
</style>

<script>
document.getElementById('courseBasicForm').addEventListener('submit', function(e) {
    const submitButton = document.getElementById('submitButton');
    const buttonText = submitButton.querySelector('.button-text');
    const spinner = submitButton.querySelector('.spinner-border');
    
    // Disable button and show spinner
    submitButton.disabled = true;
    spinner.classList.remove('d-none');
    buttonText.textContent = 'Creating Course...';
});

// Add client-side video size validation
document.getElementById('previewVideo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const maxSize = 100 * 1024 * 1024; // 100MB in bytes
    
    if (file && file.size > maxSize) {
        alert('Video file size must be less than 100MB');
        this.value = ''; // Clear the file input
    }
});
</script>
<script>
    function togglePriceInput(isFree) {
        const priceInput = document.getElementById('price-input');
        const coursePrice = document.getElementById('coursePrice');
        if (isFree) {
            priceInput.style.display = 'none';
            coursePrice.value = '0';
            coursePrice.readOnly = true;
        } else {
            priceInput.style.display = 'block';
            coursePrice.readOnly = false;
        }
    }
</script>
