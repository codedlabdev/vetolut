<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../header.php';
include BASE_DIR . 'public/user/inc/top_head.php';
require_once BASE_DIR . 'lib/user/case_lists.php';


// Get cases based on filter
$status_filter = $_GET['status'] ?? null;
$cases = getUserCases($status_filter);

// Get case counts for each status
$all_cases = getUserCases();
$urgent_count = 0;
$pending_count = 0;
$completed_count = 0;

foreach ($all_cases as $case) {
    switch (strtolower($case['status'])) {
        case 'awaiting':
            $urgent_count++;
            break;
        case 'pending':
            $pending_count++;
            break;
        case 'finalized':
            $completed_count++;
            break;
    }
}
?>

<div class="app-container">
    <!-- Quick Filters -->
    <div class="quick-filters animate__animated animate__fadeIn">
        <a href="?status=" class="filter-pill <?php echo !$status_filter ? 'active' : ''; ?>">All</a>
        <a href="?status=pending" class="filter-pill <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
        <a href="?status=finalized" class="filter-pill <?php echo $status_filter === 'finalized' ? 'active' : ''; ?>">Finalized</a>
        <a href="?status=awaiting" class="filter-pill <?php echo $status_filter === 'awaiting' ? 'active' : ''; ?>">Awaiting</a>
    </div>

    <!-- Stories Section -->
    <div class="stories-container animate__animated animate__fadeIn">
        <div class="stories-wrapper">
            <div class="story-card urgent">
                <div class="story-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="story-info">
                    <h3><?php echo $urgent_count; ?></h3>
                    <p>Urgent Cases</p>
                </div>
            </div>
            <div class="story-card pending">
                <div class="story-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="story-info">
                    <h3><?php echo $pending_count; ?></h3>
                    <p>Pending</p>
                </div>
            </div>
            <div class="story-card completed">
                <div class="story-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="story-info">
                    <h3><?php echo $completed_count; ?></h3>
                    <p>Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cases List -->
    <div class="cases-container">
        <?php if (empty($cases)): ?>
            <div class="empty-state animate__animated animate__fadeIn">
                <div class="empty-state-icon animate__animated animate__pulse animate__infinite">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3 class="empty-state-title animate__animated animate__fadeInUp animate__delay-1s">
                    No Cases Found
                </h3>
                <p class="empty-state-description animate__animated animate__fadeInUp animate__delay-2s">
                    Start by creating your first case
                </p>
                <a href="create.php" class="empty-state-button animate__animated animate__fadeInUp animate__delay-3s">
                    <i class="fas fa-plus"></i> Create New Case
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($cases as $case): ?>
                <div class="case-card animate__animated animate__fadeInUp">
                    <div class="case-header">
                        <div class="case-title">
                            <h4>#<?php echo htmlspecialchars($case['case_id']); ?></h4>
                            <span class="badge <?php echo getStatusClass($case['status']); ?>">
                                <?php echo ucfirst(htmlspecialchars($case['status'])); ?>
                            </span>
                        </div>
                        <div class="case-actions">
                            <button class="action-btn" onclick="toggleActionMenu(this)">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="action-menu">
                                <a href="edit_case.php?case_id=<?php echo htmlspecialchars($case['case_id']); ?>" class="action-item">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <a href="#" 
                                   class="action-item text-danger delete-case"
                                   data-case-id="<?php echo htmlspecialchars($case['case_id']); ?>">
                                    <i class="fas fa-trash-alt"></i>
                                    <span>Delete</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="case-content">
                        <div class="case-info">
                            <span class="info-tag">
                                <i class="fas <?php echo $case['pet_species'] === 'Cat' ? 'fa-cat' : 'fa-dog'; ?>"></i> 
                                <?php echo htmlspecialchars($case['pet_species']); ?>
                            </span>
                            <span class="info-tag">
                                <i class="fas fa-paw"></i> 
                                <?php echo htmlspecialchars($case['pet_breed']); ?>
                            </span>
                        </div>
                        <p class="case-description">
                            <?php 
                            $description = $case['clinical_history'] ?? 'No clinical history available';
                            echo htmlspecialchars(substr($description, 0, 100)) . (strlen($description) > 100 ? '...' : '');
                            ?>
                        </p>
                        <div class="case-footer">
                            <span class="timestamp">
                                <i class="far fa-clock"></i> 
                                <?php echo formatDate($case['created_at']); ?>
                            </span>
                            <a href="details.php?id=<?php echo htmlspecialchars($case['case_id']); ?>" class="view-btn">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Floating Action Button -->
    <a href="create.php" class="fab-button">
        <i class="fas fa-plus"></i>
    </a>
</div>

<style>
    /* Modern Mobile-First Styles */
    :root {
        --primary-color: #4a90e2;
        --secondary-color: #f5f6fa;
        --success-color: #2ecc71;
        --warning-color: #f1c40f;
        --danger-color: #e74c3c;
        --text-primary: #2c3e50;
        --text-secondary: #7f8c8d;
        --border-radius: 12px;
        --card-shadow: 0 2px 20px rgba(0,0,0,0.1);
    }

    .app-container {
        max-width: 100%;
        padding: 16px;
        background: #f8f9fa;
        min-height: auto;
    }

    /* Search Styles */
    .search-container {
        margin-bottom: 20px;
    }

    .search-box {
        background: white;
        border-radius: var(--border-radius);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        box-shadow: var(--card-shadow);
    }

    .search-input {
        border: none;
        width: 100%;
        padding: 8px;
        margin-left: 10px;
        font-size: 16px;
    }

    /* Quick Filters */
    .quick-filters {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding: 10px 0;
        margin-bottom: 20px;
    }

    .filter-pill {
        background: white;
        border: none;
        padding: 8px 16px;
        border-radius: 20px;
        white-space: nowrap;
        color: var(--text-secondary);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .filter-pill.active {
        background: var(--primary-color);
        color: white;
    }

    /* Stories Section */
    .stories-container {
        margin-bottom: 24px;
    }

    .stories-wrapper {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding: 10px 0;
    }

    .story-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 16px;
        min-width: 140px;
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease;
    }

    .story-card:hover {
        transform: translateY(-5px);
    }

    .story-icon {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .story-info h3 {
        margin: 0;
        font-size: 24px;
        font-weight: bold;
    }

    .story-info p {
        margin: 5px 0 0;
        color: var(--text-secondary);
        font-size: 14px;
    }

    /* Case Cards */
    .case-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 16px;
        margin-bottom: 16px;
        box-shadow: var(--card-shadow);
    }

    .case-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .case-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .case-title h4 {
        margin: 0;
        font-size: 18px;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge.pending {
        background: #fff3cd;
        color: #856404;
    }

    .case-info {
        display: flex;
        gap: 10px;
        margin-bottom: 12px;
    }

    .info-tag {
        background: var(--secondary-color);
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 13px;
        color: var(--text-secondary);
    }

    .case-description {
        color: var(--text-secondary);
        margin-bottom: 12px;
        font-size: 14px;
    }

    .case-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
    }

    .timestamp {
        color: var(--text-secondary);
        font-size: 13px;
    }

    .view-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .view-btn:hover {
        background: #357abd;
    }

    /* Action Menu Styles */
    .case-actions {
        position: relative;
    }

    .action-btn {
        background: transparent;
        border: none;
        padding: 8px;
        border-radius: 50%;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .action-btn:hover {
        background-color: var(--secondary-color);
    }

    .action-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        min-width: 150px;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s ease;
    }

    .action-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .action-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        color: var(--text-primary);
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .action-item:first-child {
        border-radius: 8px 8px 0 0;
    }

    .action-item:last-child {
        border-radius: 0 0 8px 8px;
    }

    .action-item:hover {
        background-color: var(--secondary-color);
    }

    .action-item i {
        font-size: 16px;
    }

    .text-danger {
        color: var(--danger-color) !important;
    }

    .text-danger:hover {
        background-color: #ffebee !important;
    }
    .fix-osahan-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    }

    /* Empty State Styles */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        margin: 20px auto;
        max-width: 400px;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: var(--secondary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        color: var(--primary-color);
    }

    .empty-state-icon i {
        font-size: 36px;
    }

    .empty-state-title {
        color: var(--text-primary);
        font-size: 24px;
        margin-bottom: 12px;
        font-weight: 600;
    }

    .empty-state-description {
        color: var(--text-secondary);
        font-size: 16px;
        margin-bottom: 24px;
        line-height: 1.5;
    }

    .empty-state-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--primary-color);
        color: white;
        padding: 12px 24px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
    }

    .empty-state-button:hover {
        background: #357abd;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(74, 144, 226, 0.4);
        color: white;
        text-decoration: none;
    }

    .empty-state-button i {
        font-size: 18px;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (min-width: 768px) {
        .cases-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .case-card {
            margin-bottom: 0;
        }
    }
</style>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
    // Add smooth animations when filtering
    document.querySelectorAll('.filter-pill').forEach(pill => {
        pill.addEventListener('click', () => {
            // Remove active class from all pills
            document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
            // Add active class to clicked pill
            pill.classList.add('active');
            
            // Animate cards
            document.querySelectorAll('.case-card').forEach(card => {
                card.style.animation = 'none';
                card.offsetHeight; // Trigger reflow
                card.style.animation = 'fadeInUp 0.5s ease forwards';
            });
        });
    });

    // Action menu toggle
    function toggleActionMenu(button) {
        // Close all other open menus first
        document.querySelectorAll('.action-menu.show').forEach(menu => {
            if (menu !== button.nextElementSibling) {
                menu.classList.remove('show');
            }
        });
        
        const menu = button.nextElementSibling;
        menu.classList.toggle('show');
        
        // Close menu when clicking outside
        document.addEventListener('click', function closeMenu(e) {
            if (!button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('show');
                document.removeEventListener('click', closeMenu);
            }
        });
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener to delete case links
    const deleteLinks = document.querySelectorAll('.delete-case');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
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
                            // Success animation and reload
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'The case has been successfully deleted.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                didClose: () => {
                                    window.location.reload();
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
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php 
include '../inc/float_nav.php';
include '../footer.php';
?>