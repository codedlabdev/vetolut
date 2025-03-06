<div class="appointment-upcoming d-flex flex-column vh-100">
    <?php 
    include '../inc/p_top.php';
    require_once BASE_DIR . 'lib/user/courses_func.php';

    // Get user ID from session
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Fetch categories
    $categories = getCourseCategories();

    // If no categories found, show error
    if (empty($categories)) {
        echo '<div class="alert alert-danger">Error: No course categories available. Please try again later.</div>';
        exit;
    }
    ?>

    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <!-- Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>public/user/courses/style.css" rel="stylesheet">



   

<div class="container py-5">
    <h2 class="mb-4">Create New Course</h2>
    
    <form id="courseForm" enctype="multipart/form-data" method="POST" action="process_course.php">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" id="courseTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="details-tab" data-bs-toggle="tab" href="#details" role="tab">
                    <i class="fas fa-info-circle"></i> Details
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="content-tab" data-bs-toggle="tab" href="#content" role="tab">
                    <i class="fas fa-list"></i> Content
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="settings-tab" data-bs-toggle="tab" href="#settings" role="tab">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Course Details Tab -->
            <div class="tab-pane fade show active" id="details" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="collapsible-section">
                            <div class="collapsible-header">
                                <i class="fas fa-chevron-up me-2"></i> Basic Information
                            </div>
                            <div class="collapsible-content">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Course Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <div id="description"></div>
                                    <input type="hidden" name="description">
                                </div>
                            </div>
                        </div>

                        <div class="collapsible-section">
                            <div class="collapsible-header">
                                <i class="fas fa-chevron-up me-2"></i> Media
                            </div>
                            <div class="collapsible-content" style="display: block;">
                                <div class="mb-4">
                                    <label class="form-label">Banner Image</label>
                                    <div class="upload-zone" id="bannerUploadZone">
                                        <input type="file" class="file-input" id="bannerImage" name="banner_image" accept="image/*">
                                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        <h4 class="upload-text">Drag & Drop your banner image here</h4>
                                        <p class="upload-info">or click to browse (Recommended: 1920x400px)</p>
                                        <div id="bannerPreview" class="preview-container d-none">
                                            <img src="" alt="Banner preview">
                                            <button type="button" class="remove-preview" data-target="bannerImage">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="upload-progress d-none">
                                            <div class="progress-bar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Introductory Video</label>
                                    <div class="upload-zone" id="videoUploadZone">
                                        <input type="file" class="file-input" id="introVideo" name="intro_video" accept="video/*">
                                        <i class="fas fa-film upload-icon"></i>
                                        <h4 class="upload-text">Drag & Drop your video here</h4>
                                        <p class="upload-info">or click to browse (Max size: 100MB)</p>
                                        <div id="videoPreview" class="preview-container d-none">
                                            <video controls>
                                                <source src="" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                            <button type="button" class="remove-preview" data-target="introVideo">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="upload-progress d-none">
                                            <div class="progress-bar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="collapsible-section">
                            <div class="collapsible-header">
                                <i class="fas fa-chevron-up me-2"></i> Course Information
                            </div>
                            <div class="collapsible-content" style="display: block;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">Duration (minutes)</label>
                                            <input type="number" class="form-control" id="duration" name="duration" min="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="button" class="btn btn-primary btn-lg px-5" onclick="$('#content-tab').tab('show')">Next <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- Course Content Tab -->
            <div class="tab-pane fade" id="content" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div id="topicsContainer">
                            <!-- Topics will be added here dynamically -->
                        </div>
                        <button type="button" class="btn btn-outline-primary" id="addTopic">
                            <i class="fas fa-plus"></i> Add New Topic
                        </button>
                    </div>
                    <div class="card-footer text-end">
                        <button type="button" class="btn btn-primary btn-lg px-5" onclick="$('#settings-tab').tab('show')">Next <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div class="tab-pane fade" id="settings" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                         
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enableComments" name="enable_comments">
                                <label class="form-check-label" for="enableComments">Enable Comments</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enableReviews" name="enable_reviews">
                                <label class="form-check-label" for="enableReviews">Enable Reviews</label>
                            </div>
                        </div>
                        
                        <!-- Create Course Button -->
                        <div class="mt-4">
                            <button type="submit" name="action" value="publish" class="btn btn-primary btn-lg w-100">Create Course</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
 

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Include necessary JavaScript libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
<!-- Include script.js-->
<script src="<?php echo BASE_URL; ?>public/user/courses/script.js"></script>
</body>
</html>