<div class="appointment-upcoming d-flex flex-column vh-100">
    <?php include '../inc/p_top.php';
// network.php
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
 ?>
    <div class="vh-100 my-auto overflow-auto body-fix-osahan-footer">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <div class="container">
            <div class="post-input-box">
                <form id="caseForm" action="<?php echo BASE_URL; ?>lib/user/case_func.php" method="POST" enctype="multipart/form-data">
                    <!-- Animal Details Section -->
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Animal Details</h5>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="petName" name="pet_name" placeholder="Pet Name" required>
                                <label for="petName">Pet Name</label>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="petAge" name="pet_age" placeholder="Age" required>
                                            <label for="petAge">Age</label>
                                        </div>
                                        <div class="form-floating">
                                            <select class="form-select" id="ageUnit" name="age_unit" required>
                                                <option value="week">Weeks</option>
                                                <option value="month">Months</option>
                                                <option value="year" selected>Years</option>
                                            </select>
                                            <label for="ageUnit">Unit</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-floating">
                                        <input type="number" step="0.1" class="form-control" id="petWeight" name="pet_weight" placeholder="Weight (kg)" required>
                                        <label for="petWeight">Weight (kg)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="petBreed" name="pet_breed" placeholder="Breed" required>
                                <label for="petBreed">Breed</label>
                            </div>
                            <div class="form-floating mb-3">
                                <select class="form-select" id="petSpecies" name="pet_species" required>
                                    <option value="">Select Species</option>
                                    <option value="dog">Dog</option>
                                    <option value="cat">Cat</option>
                                    <option value="bird">Bird</option>
                                    <option value="other">Other</option>
                                </select>
                                <label for="petSpecies">Species</label>
                            </div>
                        </div>
                    </div>

                    <!-- Clinical History Section -->
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Clinical History</h5>
                            <div class="mb-3">
                                <textarea class="form-control" id="clinicalHistory" name="clinical_history" rows="4" placeholder="Enter clinical history"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Clinical Documents</label>
                                <div class="upload-container">
                                    <div class="upload-area">
                                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        <p class="upload-text">Click to browse</p>
                                        <p class="upload-subtext">Maximum 3 files (PDF, DOC, DOCX)</p>
                                        <input type="file" name="clinical_docs[]" accept=".pdf,.doc,.docx" multiple class="file-input">
                                    </div>
                                </div>
                                <div id="clinicalDocsPreview" class="file-preview-list"></div>
                                <div class="file-error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnostics Section -->
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Diagnostics</h5>
                            <div class="mb-3">
                                <label class="form-label">Diagnostic Images</label>
                                <div class="upload-container">
                                    <div class="upload-area">
                                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        <p class="upload-text">Click to browse</p>
                                        <p class="upload-subtext">Maximum 3 files (JPG, PNG, GIF)</p>
                                        <input type="file" name="diagnostic_images[]" accept="image/*" multiple class="file-input">
                                    </div>
                                </div>
                                <div id="diagnosticImagesPreview" class="file-preview-grid"></div>
                                <div class="file-error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Treatment Section -->
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Current Treatment</h5>
                            <div class="mb-3">
                                <textarea class="form-control" id="currentTreatment" name="current_treatment" rows="3" placeholder="List current medications and procedures"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Case Notes Section -->
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Case Notes</h5>
                            <div class="mb-3">
                                <textarea class="form-control" id="caseNotes" name="case_notes" rows="4" placeholder="Additional notes about the case"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="submitButton">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="btn-text">Create Case</span>
                            <span class="btn-loading-text">Creating Case...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="toastTitle">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>
<?php
 include '../inc/float_nav.php';
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



.post-button:hover {
    background-color: #357abd;
}



.logout{
    display:none!important;
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

.post-input-box {
    max-width: 800px;
    margin: 20px auto;
}

.card {
    border-radius: 15px;
    border: none;
    margin-bottom: 1rem;
}

.card-body {
    padding: 1.5rem;
}

.card-title {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.1rem;
}

.form-control, .form-select {
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    border-color: #0d6efd;
}

.form-floating > label {
    padding: 0.75rem;
}

textarea.form-control {
    min-height: 100px;
}

.btn-primary {
    background-color: #0d6efd;
    border: none;
    padding: 1rem;
    border-radius: 10px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 6px rgba(13, 110, 253, 0.1);
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 8px rgba(13, 110, 253, 0.2);
}

.form-text {
    color: #6c757d;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

@media (max-width: 768px) {
    .post-input-box {
        margin: 10px;
    }

    .card-body {
        padding: 1rem;
    }
}

/* File Upload Styles */
.upload-container {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-container:hover {
    border-color: #0d6efd;
    background: #f1f8ff;
}

.upload-icon {
    font-size: 2.5rem;
    color: #6c757d;
    margin-bottom: 10px;
}

.upload-text {
    margin: 0;
    font-size: 1rem;
    color: #495057;
}

.upload-subtext {
    margin: 5px 0 0;
    font-size: 0.875rem;
    color: #6c757d;
}

.file-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.upload-area {
    position: relative;
    min-height: 150px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* List view for clinical documents */
.file-preview-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin: 1rem 0;
}

.file-preview-list .file-preview-item {
    width: 100%;
    height: auto;
    display: flex;
    align-items: center;
    padding: 0.75rem;
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    animation: slideIn 0.3s ease-out forwards;
}

.file-preview-list .file-preview-item .file-icon {
    width: 40px;
    height: 40px;
    min-width: 40px;
    margin-right: 1rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.file-preview-list .file-preview-item .file-name {
    position: static;
    background: none;
    color: #495057;
    padding: 0;
    flex-grow: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-preview-list .file-preview-item .remove-file {
    position: static;
    margin-left: 0.5rem;
}

/* Keep grid view for diagnostic images */
.file-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin: 1rem 0;
}

.file-preview-grid .file-preview-item {
    position: relative;
    width: 100%;
    height: 150px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    animation: slideIn 0.3s ease-out forwards;
}

.file-preview-grid .file-preview-item .file-icon {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.file-preview-grid .file-preview-item .file-name {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 0.5rem;
    background: rgba(0,0,0,0.7);
    color: white;
    font-size: 0.8rem;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-preview-grid .file-preview-item .remove-file {
    position: absolute;
    top: 5px;
    right: 5px;
}

/* File preview animations */
@keyframes slideIn {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.file-preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin: 1rem 0;
}

.file-preview-item {
    position: relative;
    width: 150px;
    height: 150px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    animation: slideIn 0.3s ease-out forwards;
}

.file-preview-item .file-icon {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.file-preview-item .file-icon i {
    font-size: 3rem;
    color: #6c757d;
}

.file-preview-item .file-name {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 0.5rem;
    background: rgba(0,0,0,0.7);
    color: white;
    font-size: 0.8rem;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-preview-item .remove-file {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 24px;
    height: 24px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.file-preview-item:hover .remove-file {
    opacity: 1;
}

.file-error {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Loading spinner styles */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.2em;
    margin-right: 0.5rem;
    display: none;
}

.btn-loading .spinner-border-sm {
    display: inline-block;
}

.btn-loading .btn-text {
    display: none;
}

.btn-loading .btn-loading-text {
    display: inline;
}

.btn-text {
    display: inline;
}

.btn-loading-text {
    display: none;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

const BASE_URL = "<?php echo BASE_URL; ?>";

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pet_name'])) {
    try {
        // Simple case insertion without validation
        $case_id = simple_insert_case($_POST);
        
        if ($case_id) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Case created successfully', 
                'case_id' => $case_id,
                'redirect' => getBaseUrl() . 'public/user/cases/lists.php'
            ]);
        } else {
            throw new Exception('Failed to create case');
        }
    } catch (Exception $e) {
        error_log("Error creating case: " . $e->getMessage());
        http_response_code(400);
        echo json_encode([
            'status' => 'error', 
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
?>

// Function to show toast message
function showToast(message, success = true) {
    const toast = new bootstrap.Toast(document.getElementById('liveToast'));
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    
    // Set toast appearance based on status
    const toastEl = document.getElementById('liveToast');
    toastEl.classList.remove('bg-success', 'bg-danger', 'text-white');
    if (success) {
        toastEl.classList.add('bg-success', 'text-white');
        toastTitle.textContent = 'Success';
    } else {
        toastEl.classList.add('bg-danger', 'text-white');
        toastTitle.textContent = 'Error';
    }
    
    toastMessage.textContent = message;
    toast.show();
}

// Handle form submission
document.getElementById('caseForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Get submit button and disable it
    const submitButton = document.getElementById('submitButton');
    submitButton.disabled = true;
    submitButton.classList.add('btn-loading');
    
    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            showToast(result.message, true);
            // Keep button disabled but update text
            submitButton.textContent = 'Case Created!';
            // Redirect after showing toast
            setTimeout(() => {
                window.location.href = '<?php echo BASE_URL; ?>public/user/cases/lists.php';
            }, 1500);
        } else {
            showToast(result.message || 'Error submitting form', false);
            // Re-enable button and remove loading state
            submitButton.disabled = false;
            submitButton.classList.remove('btn-loading');
        }
    } catch (error) {
        console.error('Form submission error:', error);
        showToast('An error occurred while submitting the form', false);
        // Re-enable button and remove loading state
        submitButton.disabled = false;
        submitButton.classList.remove('btn-loading');
    }
});

// File handling functions
const MAX_FILES = 3;
const ALLOWED_DOCS = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
const ALLOWED_IMAGES = ['image/jpeg', 'image/png', 'image/gif'];

function getFileIcon(type) {
    if (type.startsWith('image/')) return 'fas fa-image';
    if (type.includes('pdf')) return 'fas fa-file-pdf';
    if (type.includes('word')) return 'fas fa-file-word';
    return 'fas fa-file';
}

function createFilePreview(file, container) {
    const div = document.createElement('div');
    div.className = 'file-preview-item';
    div.dataset.fileName = file.name;
    
    const iconDiv = document.createElement('div');
    iconDiv.className = 'file-icon';
    
    if (container.id === 'diagnosticImagesPreview' && file.type.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        iconDiv.appendChild(img);
    } else {
        const icon = document.createElement('i');
        icon.className = getFileIcon(file.type);
        iconDiv.appendChild(icon);
    }
    
    const nameDiv = document.createElement('div');
    nameDiv.className = 'file-name';
    nameDiv.textContent = file.name;
    
    const removeBtn = document.createElement('div');
    removeBtn.className = 'remove-file';
    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
    removeBtn.onclick = () => {
        div.remove();
        updateFileInput(container.id === 'clinicalDocsPreview' ? 'clinical_docs[]' : 'diagnostic_images[]');
    };
    
    div.appendChild(iconDiv);
    div.appendChild(nameDiv);
    div.appendChild(removeBtn);
    container.appendChild(div);
}

function updateFileInput(inputName) {
    const input = document.querySelector(`input[name="${inputName}"]`);
    const container = inputName.includes('clinical') ? clinicalDocsPreview : diagnosticImagesPreview;
    const files = Array.from(input.files);
    
    // Create new FileList with remaining files
    const remainingFiles = files.filter(file => 
        !Array.from(container.children).some(preview => 
            preview.dataset.fileName === file.name
        )
    );
    
    const dataTransfer = new DataTransfer();
    remainingFiles.forEach(file => dataTransfer.items.add(file));
    input.files = dataTransfer.files;
}

function validateFiles(files, type) {
    const errors = [];
    const allowedTypes = type === 'docs' ? ALLOWED_DOCS : ALLOWED_IMAGES;
    
    if (files.length > MAX_FILES) {
        errors.push(`Maximum ${MAX_FILES} files allowed`);
    }
    
    Array.from(files).forEach(file => {
        if (!allowedTypes.includes(file.type)) {
            errors.push(`Invalid file type: ${file.name}`);
        }
        if (file.size > 5 * 1024 * 1024) { // 5MB limit
            errors.push(`File too large: ${file.name}`);
        }
    });
    
    return errors;
}

// Handle file inputs
document.querySelector('input[name="clinical_docs[]"]').addEventListener('change', function(e) {
    const errors = validateFiles(this.files, 'docs');
    const errorDiv = this.parentElement.querySelector('.file-error') || 
                    document.createElement('div');
    errorDiv.className = 'file-error';
    
    if (errors.length) {
        errorDiv.textContent = errors.join(', ');
        this.parentElement.appendChild(errorDiv);
        this.value = '';
        return;
    }
    
    errorDiv.remove();
    clinicalDocsPreview.innerHTML = '';
    Array.from(this.files).forEach(file => createFilePreview(file, clinicalDocsPreview));
});

document.querySelector('input[name="diagnostic_images[]"]').addEventListener('change', function(e) {
    const errors = validateFiles(this.files, 'images');
    const errorDiv = this.parentElement.querySelector('.file-error') || 
                    document.createElement('div');
    errorDiv.className = 'file-error';
    
    if (errors.length) {
        errorDiv.textContent = errors.join(', ');
        this.parentElement.appendChild(errorDiv);
        this.value = '';
        return;
    }
    
    errorDiv.remove();
    diagnosticImagesPreview.innerHTML = '';
    Array.from(this.files).forEach(file => createFilePreview(file, diagnosticImagesPreview));
});

// Add drag and drop functionality
document.querySelectorAll('.upload-container').forEach(container => {
    container.addEventListener('dragover', (e) => {
        e.preventDefault();
        container.style.borderColor = '#0d6efd';
        container.style.background = '#f1f8ff';
    });

    container.addEventListener('dragleave', (e) => {
        e.preventDefault();
        container.style.borderColor = '#ddd';
        container.style.background = '#f8f9fa';
    });

    container.addEventListener('drop', (e) => {
        e.preventDefault();
        container.style.borderColor = '#ddd';
        container.style.background = '#f8f9fa';
        
        const input = container.querySelector('input[type="file"]');
        const files = e.dataTransfer.files;
        
        // Create a new DataTransfer object
        const dataTransfer = new DataTransfer();
        
        // Add dropped files
        Array.from(files).forEach(file => dataTransfer.items.add(file));
        
        // Set the files to the input
        input.files = dataTransfer.files;
        
        // Trigger change event
        input.dispatchEvent(new Event('change'));
    });
});

// Update the preview containers when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const clinicalDocsPreview = document.getElementById('clinicalDocsPreview');
    const diagnosticImagesPreview = document.getElementById('diagnosticImagesPreview');
    
    clinicalDocsPreview.className = 'file-preview-list';
    diagnosticImagesPreview.className = 'file-preview-grid';
});
</script>
<?php include '../footer.php'; ?>
