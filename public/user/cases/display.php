<?php
include '../header.php';
include BASE_DIR . 'public/user/inc/top_head.php';
require_once BASE_DIR . 'lib/user/case_lists.php';

// Check user authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get case ID from URL
$case_id = $_GET['id'] ?? null;
if (!$case_id) {
    header('Location: lists.php');
    exit();
}

// Fetch case details
try {
    $user_id = $_SESSION['user_id'];
    $case = get_case($case_id, $user_id);
    
    if (!$case) {
        throw new Exception('Case not found or access denied');
    }
} catch (Exception $e) {
    error_log("Case details error: " . $e->getMessage());
    header('Location: lists.php');
    exit();
}

// Decode JSON file arrays
$clinical_docs = !empty($case['clinical_docs']) ? json_decode($case['clinical_docs'], true) : [];
$diagnostic_images = !empty($case['diagnostic_images']) ? json_decode($case['diagnostic_images'], true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Details | VetoCare</title>
    
    
    
    <style>
        .case-details-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .case-details-section h4 {
            color: #007bff;
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .case-details-section p {
            color: #495057;
        }
        .case-images-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .case-images-grid img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }
        .case-images-grid img:hover {
            transform: scale(1.05);
        }
        @media (max-width: 768px) {
            .case-images-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .case-action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        .case-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .case-header h2 {
            margin: 0;
            color: #007bff;
        }
    </style>
</head>
<body>
    <?php include '../inc/float_nav.php'; ?>

    <div class="container mt-4">
        <!-- Case Header -->
        <div class="case-header">
            <h2>Case #<?php echo htmlspecialchars($case['case_id']); ?></h2>
            <div>
                <a href="edit_case.php?id=<?php echo htmlspecialchars($case['case_id']); ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>

        <!-- Patient Information -->
        <div class="case-details-section">
            <h4><i class="fas fa-dog"></i> Patient Details</h4>
            <div class="row">
                <div class="col-6">
                    <strong>Name:</strong> 
                    <?php echo htmlspecialchars($case['patient_name']); ?>
                </div>
                <div class="col-6">
                    <strong>Species:</strong> 
                    <?php echo htmlspecialchars($case['species']); ?>
                </div>
                <div class="col-6 mt-2">
                    <strong>Breed:</strong> 
                    <?php echo htmlspecialchars($case['breed']); ?>
                </div>
                <div class="col-6 mt-2">
                    <strong>Age:</strong> 
                    <?php echo htmlspecialchars($case['age']); ?> years
                </div>
            </div>
        </div>

        <!-- Clinical History -->
        <div class="case-details-section">
            <h4><i class="fas fa-notes-medical"></i> Clinical History</h4>
            <p><?php echo !empty($case['clinical_history']) ? htmlspecialchars($case['clinical_history']) : 'No clinical history available.'; ?></p>
        </div>

        <!-- Current Treatment -->
        <div class="case-details-section">
            <h4><i class="fas fa-briefcase-medical"></i> Current Treatment</h4>
            <p><?php echo !empty($case['current_treatment']) ? htmlspecialchars($case['current_treatment']) : 'No current treatment plan.'; ?></p>
        </div>

        <!-- Case Notes -->
        <div class="case-details-section">
            <h4><i class="fas fa-sticky-note"></i> Case Notes</h4>
            <p><?php echo !empty($case['case_notes']) ? htmlspecialchars($case['case_notes']) : 'No additional notes.'; ?></p>
        </div>

        <!-- Clinical Documents -->
        <?php if (!empty($clinical_docs)): ?>
        <div class="case-details-section">
            <h4><i class="fas fa-file-medical"></i> Clinical Documents</h4>
            <div class="case-images-grid">
                <?php foreach ($clinical_docs as $doc): ?>
                    <img src="<?php echo BASE_URL . 'assets/user/img/cases/' . htmlspecialchars($doc); ?>" 
                         alt="Clinical Document" 
                         onclick="openImageModal('<?php echo BASE_URL . 'assets/user/img/cases/' . htmlspecialchars($doc); ?>')">
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Diagnostic Images -->
        <?php if (!empty($diagnostic_images)): ?>
        <div class="case-details-section">
            <h4><i class="fas fa-x-ray"></i> Diagnostic Images</h4>
            <div class="case-images-grid">
                <?php foreach ($diagnostic_images as $img): ?>
                    <img src="<?php echo BASE_URL . 'assets/user/img/cases/' . htmlspecialchars($img); ?>" 
                         alt="Diagnostic Image" 
                         onclick="openImageModal('<?php echo BASE_URL . 'assets/user/img/cases/' . htmlspecialchars($img); ?>')">
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="case-action-buttons">
            <a href="lists.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Cases
            </a>
            <button class="btn btn-danger delete-case" data-case-id="<?php echo htmlspecialchars($case['case_id']); ?>">
                <i class="fas fa-trash-alt"></i> Delete Case
            </button>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Full Image">
                </div>
            </div>
        </div>
    </div>

    <?php include '../footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete case functionality
        const deleteButton = document.querySelector('.delete-case');
        if (deleteButton) {
            deleteButton.addEventListener('click', function() {
                const caseId = this.getAttribute('data-case-id');
                
                Swal.fire({
                    title: 'Delete Case',
                    text: 'Are you sure you want to delete this case? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('<?php echo BASE_URL; ?>lib/user/edit_case.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `action=delete&case_id=${encodeURIComponent(caseId)}`
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.status === 'success') {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'The case has been successfully deleted.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = 'lists.php';
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: result.message || 'Failed to delete the case.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Delete error:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'An unexpected error occurred.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                    }
                });
            });
        }

        // Image modal functionality
        window.openImageModal = function(imageSrc) {
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageSrc;
            modal.show();
        };
    });
    </script>
</body>
</html>
