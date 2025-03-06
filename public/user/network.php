<div class="appointment-upcoming d-flex flex-column vh-100">
    <?php include 'inc/p_others.php';
// network.php

require_once BASE_DIR . 'lib/dhu.php'; // Include helper functions
 
 
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch the user's data, including the profile image
$imageData = ''; // Initialize imageData for profile image

if ($userId) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Convert the user's image BLOB data to a base64 string if it exists
    if (!empty($user['image'])) {
        $imageData = 'data:image/jpeg;base64,' . base64_encode($user['image']);
    }
}

// Step 2: Fetch the post details if 'id' is set in the URL
$post = null;
$postId = isset($_GET['id']) ? $_GET['id'] : null;

if ($postId) {
    $stmt = $pdo->prepare("SELECT * FROM network_post WHERE id = :id");
    $stmt->execute(['id' => $postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get the image data for the post, if available
$network_imageData = isset($post['photo']) ? BASE_URL . $post['photo'] : null;
    ?>
    <div class="vh-100 my-auto overflow-auto body-fix-osahan-footer">
        <div class="container">
            <div class="post-input-box">
                <form action="<?php echo BASE_URL; ?>lib/user/network_p.php" method="POST" enctype="multipart/form-data"
                    id="post-form">
                    <div class="input-section">
                        <img src="<?php echo !empty($imageData) ? $imageData : BASE_URL . 'assets/user/img/noimage.png'; ?>"
                            alt="User Image" class="img-fluid rounded-4 voice-img">
                        <textarea name="post_text" id="post_text" placeholder="What's on your mind?" class="input-sections" required><?php echo isset($post['text']) ? htmlspecialchars($post['text']) : ''; ?></textarea>
                    </div>
                    <div class="icon-section">
                        <div>
                            <label for="post_photo"><i class="fas fa-photo-video"></i> <span>Photos (Max 5)</span></label>
                            <input type="file" name="post_photo[]" id="post_photo" multiple
                                accept="image/png, image/jpeg, image/jpg" style="display:none;">
                        </div>

                        <!-- Include the post ID as a hidden input if it exists -->
                        <?php if (isset($post['id'])): ?>
                        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                        <button type="submit" class="post-button">Update Post</button>
                        <?php else: ?>
                        <button type="submit" class="post-button">Post</button>
                        <?php endif; ?>
                    </div>

                    <!-- Multiple Images Preview Container -->
                    <div class="image-gallery-container">
                        <div id="multiple-image-preview" class="multiple-image-preview">
                            <?php if (isset($post['photo']) && !empty($post['photo'])): ?>
                                <div class="preview-item">
                                    <div class="image-wrapper">
                                        <img src="<?php echo $network_imageData; ?>" alt="Existing Image" class="img-preview" />
                                        <div class="image-overlay">
                                            <button type="button" class="remove-image" onclick="deleteImage(<?php echo htmlspecialchars($post['id']); ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Navigation Arrows -->
                        <button type="button" class="gallery-nav prev" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                        <button type="button" class="gallery-nav next" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
 <?php
        include 'inc/float_nav.php';
        ?>
<style>
:root {
    --primary-color: #4a90e2;
    --secondary-color: #f0f2f5;
    --text-color: #333;
    --border-radius: 12px;
    --spacing: 16px;
}

.appointment-upcoming {
    padding: 0;
    background-color: var(--secondary-color);
}

.container {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    padding: var(--spacing);
}

.post-input-box {
    background-color: white;
    border-radius: var(--border-radius);
    padding: var(--spacing);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.input-section {
    display: flex;
    gap: var(--spacing);
    margin-bottom: var(--spacing);
}

.voice-img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
}

.input-sections {
    flex: 1;
    padding: 12px;
    border: 1px solid #e1e4e8;
    border-radius: var(--border-radius);
    background-color: var(--secondary-color);
    font-size: 16px;
    width: 100%;
    height: 120px;
    resize: none;
    transition: border-color 0.3s ease;
}

.input-sections:focus {
    border-color: var(--primary-color);
    outline: none;
}

.icon-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
}

.icon-section div {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 20px;
    transition: background-color 0.3s ease;
}

.icon-section div:hover {
    background-color: var(--secondary-color);
}

.icon-section i {
    color: var(--primary-color);
}

.post-button {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: 10px 24px;
    border-radius: 20px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.post-button:hover {
    background-color: #357abd;
}

/* Image Gallery Styles */
.image-gallery-container {
    position: relative;
    width: 100%;
    margin: 20px 0;
    overflow: hidden;
}

.multiple-image-preview {
    display: flex;
    gap: 15px;
    transition: transform 0.3s ease;
    padding: 10px 5px;
    scroll-behavior: smooth;
    overflow-x: auto;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

.multiple-image-preview::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

.preview-item {
    flex: 0 0 auto;
    width: 200px;
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.preview-item:hover {
    transform: translateY(-5px);
}

.image-wrapper {
    position: relative;
    width: 100%;
    padding-top: 100%; /* 1:1 Aspect Ratio */
}

.img-preview {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 12px;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 12px;
}

.preview-item:hover .image-overlay {
    opacity: 1;
}

.remove-image {
    background: transparent;
    border: 2px solid white;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
    font-size: 16px;
}

.remove-image:hover {
    background: white;
    color: #dc3545;
    transform: scale(1.1);
}

.gallery-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 2;
    transition: all 0.3s ease;
    opacity: 0.7;
}

.gallery-nav:hover {
    opacity: 1;
    background: var(--primary-color);
    color: white;
}

.gallery-nav.prev {
    left: 10px;
}

.gallery-nav.next {
    right: 10px;
}

.gallery-nav.hidden {
    display: none;
}

@media (max-width: 768px) {
    .preview-item {
        width: 150px;
    }
    
    .gallery-nav {
        width: 35px;
        height: 35px;
    }
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .container {
        padding: 12px;
    }
    
    .input-section {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .voice-img {
        margin-bottom: 12px;
    }
    
    .icon-section {
        flex-wrap: wrap;
        gap: 12px;
    }
}

/* Fix for footer */
.fix-osahan-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}

.vh-100 {
    min-height: 100vh;
    height: auto !important;
    padding-bottom: 70px; /* Add space for fixed footer */
}

.avx-feedback-float-icon {
    display: none;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const maxImages = 5;
    const input = document.getElementById('post_photo');
    const previewContainer = document.getElementById('multiple-image-preview');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    // Gallery Navigation
    function updateNavButtons() {
        const isScrollable = previewContainer.scrollWidth > previewContainer.clientWidth;
        const isAtStart = previewContainer.scrollLeft <= 0;
        const isAtEnd = previewContainer.scrollLeft >= previewContainer.scrollWidth - previewContainer.clientWidth;
        
        prevBtn.classList.toggle('hidden', !isScrollable || isAtStart);
        nextBtn.classList.toggle('hidden', !isScrollable || isAtEnd);
    }
    
    prevBtn.addEventListener('click', () => {
        previewContainer.scrollBy({
            left: -200,
            behavior: 'smooth'
        });
    });
    
    nextBtn.addEventListener('click', () => {
        previewContainer.scrollBy({
            left: 200,
            behavior: 'smooth'
        });
    });
    
    previewContainer.addEventListener('scroll', updateNavButtons);
    window.addEventListener('resize', updateNavButtons);
    
    input.addEventListener('change', function() {
        const files = Array.from(this.files);
        
        if (files.length > maxImages) {
            Swal.fire({
                icon: 'error',
                title: 'Too many images',
                text: `Please select a maximum of ${maxImages} images.`
            });
            this.value = '';
            return;
        }
        
        previewContainer.innerHTML = '';
        
        files.forEach(file => {
            if (!file.type.match('image.*')) {
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                
                previewItem.innerHTML = `
                    <div class="image-wrapper">
                        <img src="${e.target.result}" class="img-preview" alt="Image preview">
                        <div class="image-overlay">
                            <button type="button" class="remove-image">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                const removeBtn = previewItem.querySelector('.remove-image');
                removeBtn.addEventListener('click', function() {
                    previewItem.remove();
                    const dt = new DataTransfer();
                    const remainingFiles = Array.from(input.files).filter(f => f !== file);
                    remainingFiles.forEach(f => dt.items.add(f));
                    input.files = dt.files;
                    updateNavButtons();
                });
                
                previewContainer.appendChild(previewItem);
                updateNavButtons();
            };
            
            reader.readAsDataURL(file);
        });
    });
    
    // Initial check for navigation buttons
    updateNavButtons();
    
    // Form validation
    document.getElementById('post-form').addEventListener('submit', function(event) {
        const textarea = document.getElementById('post_text');
        const files = input.files;
        
        if (!textarea.value.trim() && files.length === 0) {
            event.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Empty Post',
                text: 'Please write something or add at least one image.'
            });
        }
    });
});

function deleteImage(postId) {
    Swal.fire({
        title: 'Delete Image?',
        text: "This action cannot be undone",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`${BASE_URL}lib/user/delete_network_image.php?post_id=${postId}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Image has been removed',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to delete the image',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }
    });
}
</script>
<!-- Include SweetAlert CSS and JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>




<?php include 'footer.php';
?>