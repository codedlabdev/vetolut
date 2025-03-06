<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../header.php';

// Check if user is logged in and has a valid user_id
if (!isset($_SESSION['user_id'])) {
    header('Location: lists.php');
    exit();
}

$userId = $_SESSION['user_id'];
$caseId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$caseId) {
    header('Location: lists.php');
    exit();
}

// Get case details
try {
    $conn = getDBConnection();
    $sql = "SELECT * FROM cases WHERE case_id = :case_id AND user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':case_id' => $caseId,
        ':user_id' => $userId
    ]);
    $case = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get user data for profile image
    $userStmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $userStmt->execute(['id' => $userId]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    // Convert the user's image BLOB data to a base64 string
    $imageData = '';
    if (!empty($user['image'])) {
        $imageData = 'data:image/jpeg;base64,' . base64_encode($user['image']);
    }

} catch (Exception $e) {
   
    exit;
}

require_once BASE_DIR . 'lib/user/case_lists.php';
include BASE_DIR . 'public/user/inc/top_head.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Details</title>


    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #34495e;
        --background-color: #f4f6f7;
        --text-color: #2c3e50;
        --muted-color: #7f8c8d;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .mobile-container {
        //max-width: 480px;
        margin: 0 auto;
        padding: 15px;
        background-color: white;
        min-height: 100vh;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .page-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--primary-color);
    }

    .case-badge {
        background-color: var(--secondary-color);
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    .section {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 15px;
        overflow: hidden;
    }

    .section-header {
        display: flex;
        align-items: center;
        background-color: var(--background-color);
        padding: 12px 15px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .section-header i {
        margin-right: 10px;
        color: var(--primary-color);
        font-size: 1.1rem;
    }

    .section-header h3 {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--secondary-color);
        margin: 0;
    }

    .section-content {
        padding: 15px;
    }

    .info-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-item-label {
        color: var(--muted-color);
        font-size: 0.8rem;
        font-weight: 500;
    }

    .info-item-value {
        color: var(--text-color);
        font-size: 0.9rem;
        font-weight: 600;
        text-align: right;
    }

    .badge {
        padding: 0.4em 0.8em;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 4px;
        color: white;
    }

    /* Status Badge Colors */
    .bg-finalized {
        background-color: #27ae60 !important; /* Green */
    }

    .bg-awaiting {
        background-color: #e67e22 !important; /* Orange */
    }

    .bg-pending {
        background-color: #3498db !important; /* Blue */
    }

    .bg-cancelled {
        background-color: #e74c3c !important; /* Red */
    }

    .bg-inprogress {
        background-color: #9b59b6 !important; /* Purple */
    }

    .bg-referred {
        background-color: #1abc9c !important; /* Turquoise */
    }

    .bg-default {
        background-color: #95a5a6 !important; /* Gray */
    }

    .document-link, .image-link {
        display: block;
        position: relative;
        overflow: hidden;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s ease-in-out;
    }

    .document-link:hover, .image-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .document-link img, .image-link img {
        width: 100%;
        height: 100px;
        object-fit: cover;
    }

    .clinical-text {
        white-space: pre-line;
        color: var(--text-color);
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .image-gallery {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .image-gallery img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 6px;
        transition: transform 0.2s;
    }

    .image-gallery img:hover {
        transform: scale(1.05);
    }

    .action-buttons {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        padding: 20px;
        margin-top: 30px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px;
        border-radius: 10px;
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: white;
    }

    .action-btn i {
        font-size: 1.1rem;
    }

    .btn-back {
        background: linear-gradient(145deg, #4b5c6b, #364857);
    }

    .btn-edit {
        background: linear-gradient(145deg, #2980b9, #2471a3);
    }

    .btn-delete {
        background: linear-gradient(145deg, #c0392b, #a93226);
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        filter: brightness(110%);
    }

    .action-btn:active {
        transform: translateY(1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 380px) {
        .action-buttons {
            padding: 15px;
            gap: 10px;
        }

        .action-btn {
            padding: 12px 8px;
            font-size: 0.85rem;
        }
    }

    /* For very small screens */
    @media (max-width: 320px) {
        .action-btn span {
            display: none;
        }

        .action-btn i {
            font-size: 1.3rem;
            margin: 0;
        }

        .action-buttons {
            gap: 8px;
        }
    }

    .fix-osahan-footer {
        position: fixed;

    }

    .shadow-sm {
        position: fixed;
        width: 100%;
        margin-top: -70px;
        z-index: 999;
    }
    </style>
</head>

<body>
    <div class="mobile-container" style="margin-top: 70px;margin-bottom: 100px;">
       <!-- <div class="page-header">
            <div class="page-title">Case Details</div>
            <div class="case-badge">Case #<?php echo htmlspecialchars($case['case_id']); ?></div>
        </div>
        -->

        <!-- Patient Information Section -->
        <div class="section">
            <div class="section-header">
                <i class="fas fa-paw"></i>
                <h3>Patient Information</h3>
                <div class="case-badge" style="right: 25px; position: absolute;">Case #<?php echo htmlspecialchars($case['case_id']); ?></div>
            </div>
            <div class="section-content">
                <div class="info-list">
                    <div class="info-item">
                        <div class="info-item-label">Patient Name</div>
                        <div class="info-item-value"><?php echo !empty($case['pet_name']) ? htmlspecialchars($case['pet_name']) : '<span class="text-muted">No data available</span>'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Species</div>
                        <div class="info-item-value"><?php echo !empty($case['pet_species']) ? htmlspecialchars($case['pet_species']) : '<span class="text-muted">No data available</span>'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Breed</div>
                        <div class="info-item-value"><?php echo !empty($case['pet_breed']) ? htmlspecialchars($case['pet_breed']) : '<span class="text-muted">No data available</span>'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Age</div>
                        <div class="info-item-value">
                            <?php 
                            if (!empty($case['pet_age']) && !empty($case['age_unit'])) {
                                echo htmlspecialchars($case['pet_age'] . ' ' . $case['age_unit']);
                            } else {
                                echo '<span class="text-muted">No data available</span>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Weight</div>
                        <div class="info-item-value">
                            <?php 
                            if (!empty($case['pet_weight'])) {
                                echo htmlspecialchars($case['pet_weight']) . ' kg';
                            } else {
                                echo '<span class="text-muted">No data available</span>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Status</div>
                        <div class="info-item-value">
                            <?php if (!empty($case['status'])): ?>
                                <span class="badge bg-<?php echo strtolower($case['status']) === 'finalized' ? 'finalized' : (strtolower($case['status']) === 'awaiting' ? 'awaiting' : (strtolower($case['status']) === 'pending' ? 'pending' : (strtolower($case['status']) === 'cancelled' ? 'cancelled' : (strtolower($case['status']) === 'inprogress' ? 'inprogress' : (strtolower($case['status']) === 'referred' ? 'referred' : 'default'))))); ?>">
                                    <?php echo htmlspecialchars(ucfirst($case['status'])); ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">No data available</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clinical History Section -->
        <div class="section">
            <div class="section-header">
                <i class="fas fa-notes-medical"></i>
                <h3>Clinical History</h3>
            </div>
            <div class="section-content">
                <?php if (!empty($case['clinical_history'])): ?>
                    <p class="clinical-text">
                        <?php echo nl2br(htmlspecialchars($case['clinical_history'])); ?>
                    </p>
                <?php else: ?>
                    <div class="empty-state text-center py-4">
                        <i class="fas fa-notes-medical fa-3x mb-3 text-muted animate__animated animate__pulse animate__infinite"></i>
                        <p class="text-muted mb-0">No Clinical History Available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Current Treatment Section -->
        <div class="section">
            <div class="section-header">
                <i class="fas fa-briefcase-medical"></i>
                <h3>Current Treatment</h3>
            </div>
            <div class="section-content">
                <?php if (!empty($case['current_treatment'])): ?>
                    <p class="clinical-text">
                        <?php echo nl2br(htmlspecialchars($case['current_treatment'])); ?>
                    </p>
                <?php else: ?>
                    <div class="empty-state text-center py-4">
                        <i class="fas fa-briefcase-medical fa-3x mb-3 text-muted animate__animated animate__pulse animate__infinite"></i>
                        <p class="text-muted mb-0">No Treatment Information Available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Case Notes Section -->
        <div class="section">
            <div class="section-header">
                <i class="fas fa-sticky-note"></i>
                <h3>Case Notes</h3>
            </div>
            <div class="section-content">
                <?php if (!empty($case['case_notes'])): ?>
                    <p class="clinical-text">
                        <?php echo nl2br(htmlspecialchars($case['case_notes'])); ?>
                    </p>
                <?php else: ?>
                    <div class="empty-state text-center py-4">
                        <i class="fas fa-sticky-note fa-3x mb-3 text-muted animate__animated animate__pulse animate__infinite"></i>
                        <p class="text-muted mb-0">No Case Notes Available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Clinical Documents Section -->
        <div class="section">
            <div class="section-header">
                <i class="fas fa-file-medical"></i>
                <h3>Clinical Documents</h3>
            </div>
            <div class="section-content">
                <?php
                $clinicalDocuments = !empty($case['clinical_docs']) ? json_decode($case['clinical_docs'], true) : [];
                
                if (!empty($clinicalDocuments)): ?>
                    <div class="image-gallery">
                        <?php foreach ($clinicalDocuments as $document): ?>
                            <a href="<?php echo htmlspecialchars(BASE_URL . $document); ?>" target="_blank" class="document-link">
                                <img src="<?php echo htmlspecialchars(BASE_URL . $document); ?>" 
                                     alt="Clinical Document"
                                     onerror="this.src='<?php echo BASE_URL; ?>assets/user/img/document-icon.png';">
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state text-center py-4">
                        <i class="fas fa-folder-open fa-3x mb-3 text-muted animate__animated animate__pulse animate__infinite"></i>
                        <p class="text-muted mb-0">No Clinical Documents Available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Diagnostic Images Section -->
        <div class="section">
            <div class="section-header">
                <i class="fas fa-x-ray"></i>
                <h3>Diagnostic Images</h3>
            </div>
            <div class="section-content">
                <?php
                $diagnosticImages = !empty($case['diagnostic_images']) ? json_decode($case['diagnostic_images'], true) : [];
                
                if (!empty($diagnosticImages)): ?>
                    <div class="image-gallery">
                        <?php foreach ($diagnosticImages as $image): ?>
                            <a href="<?php echo htmlspecialchars(BASE_URL . $image); ?>" target="_blank" class="image-link">
                                <img src="<?php echo htmlspecialchars(BASE_URL . $image); ?>" 
                                     alt="Diagnostic Image">
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state text-center py-4">
                        <i class="fas fa-x-ray fa-3x mb-3 text-muted animate__animated animate__pulse animate__infinite"></i>
                        <p class="text-muted mb-0">No Diagnostic Images Available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="lists.php" class="action-btn btn-back">
                <i class="fas fa-list-ul"></i>
                <span>Case List</span>
            </a>
            <a href="edit_case.php?case_id=<?php echo htmlspecialchars($case['case_id']); ?>" class="action-btn btn-edit">
                <i class="fas fa-pencil-alt"></i>
                <span>Edit Case</span>
            </a>
            <button data-case-id="<?php echo htmlspecialchars($case['case_id']); ?>" class="action-btn btn-delete delete-case">
                <i class="fas fa-trash-alt"></i>
                <span>Delete</span>
            </button>
        </div>

        <!-- Add SweetAlert2 library -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listener to delete button
            const deleteBtn = document.querySelector('.delete-case');
            deleteBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const caseId = this.getAttribute('data-case-id');
                
                // Use SweetAlert2 for modern confirmation
                Swal.fire({
                    title: 'Delete Case',
                    text: 'Are you sure you want to delete this case? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    customClass: {
                        popup: 'animated fadeIn',
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary mr-2'
                    }
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            // Show loading state
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait while the case is being deleted.',
                                icon: 'info',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const response = await fetch('<?php echo BASE_URL; ?>lib/user/edit_case.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `action=delete&case_id=${encodeURIComponent(caseId)}`
                            });
                            
                            const result = await response.json();
                            
                            if (result.status === 'success') {
                                // Success animation and redirect to list page
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'The case has been successfully deleted.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    didClose: () => {
                                        window.location.href = 'lists.php';
                                    }
                                });
                            } else {
                                // Error handling
                                Swal.fire({
                                    title: 'Error',
                                    text: result.message || 'Failed to delete the case.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            }
                        } catch (error) {
                            console.error('Delete case error:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'An unexpected error occurred while deleting the case.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        }
                    }
                });
            });
        });
        </script>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php 
include '../inc/float_nav.php';
include '../footer.php';
?>
</body>

</html>