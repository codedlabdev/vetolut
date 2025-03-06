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
    <link href="<?php echo BASE_URL; ?>public/user/courses/js/style/create.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <div class="container-fluid py-4">
        <!-- Sections Section -->
        <div id="sections-section" class="section">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Course Sections</h3>
                    <div id="course-sections" class="sections-container">
                        <!-- Sections will be added here dynamically -->
                    </div>
                    <button type="button" class="btn btn-primary mt-4 d-flex align-items-center gap-2" id="addSectionBtn">
                        <i class="fas fa-plus"></i> Add New Section
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Form Modal -->
    <div class="modal fade" id="sectionModal" tabindex="-1" aria-labelledby="sectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sectionModalLabel">Add Course Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sectionForm">
                        <input type="hidden" id="sectionId" name="id">
                        <input type="hidden" id="courseId" name="course_id" value="<?php echo htmlspecialchars($_GET['course_id'] ?? ''); ?>">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Section Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="video_url" class="form-label">Video URL</label>
                            <input type="url" class="form-control" id="video_url" name="video_url" 
                                   placeholder="Enter video URL (YouTube, Vimeo, etc.)">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveSectionBtn">Save Section</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .sections-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .section-card {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .section-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .section-title {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .section-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .section-description {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .video-url {
            color: #007bff;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .section-card {
                padding: 1rem;
            }
            
            .section-header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .section-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseId = document.getElementById('courseId').value;
            const sectionsContainer = document.getElementById('course-sections');
            const addSectionBtn = document.getElementById('addSectionBtn');
            const sectionModal = new bootstrap.Modal(document.getElementById('sectionModal'));
            const sectionForm = document.getElementById('sectionForm');
            const saveSectionBtn = document.getElementById('saveSectionBtn');

            // Load existing sections
            loadSections();

            // Add new section button click
            addSectionBtn.addEventListener('click', () => {
                sectionForm.reset();
                document.getElementById('sectionId').value = '';
                document.getElementById('sectionModalLabel').textContent = 'Add Course Section';
                sectionModal.show();
            });

            // Save section
            saveSectionBtn.addEventListener('click', () => {
                const formData = new FormData(sectionForm);
                const sectionId = formData.get('id');
                const url = sectionId ? 'update_section.php' : 'add_section.php';

                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        sectionModal.hide();
                        loadSections();
                        showAlert('success', data.message);
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while saving the section');
                });
            });

            function loadSections() {
                fetch(`get_sections.php?course_id=${courseId}`)
                    .then(response => response.json())
                    .then(sections => {
                        sectionsContainer.innerHTML = '';
                        sections.forEach(section => {
                            const sectionElement = createSectionElement(section);
                            sectionsContainer.appendChild(sectionElement);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading sections:', error);
                        showAlert('danger', 'Error loading sections');
                    });
            }

            function createSectionElement(section) {
                const div = document.createElement('div');
                div.className = 'section-card animate__animated animate__fadeIn';
                div.innerHTML = `
                    <div class="section-header">
                        <h4 class="section-title">${escapeHtml(section.title)}</h4>
                        <div class="section-actions">
                            <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${section.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${section.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="section-description">${escapeHtml(section.description || '')}</div>
                    ${section.video_url ? `<div class="video-url"><i class="fas fa-video"></i> ${escapeHtml(section.video_url)}</div>` : ''}
                `;

                // Add event listeners
                div.querySelector('.edit-btn').addEventListener('click', () => editSection(section));
                div.querySelector('.delete-btn').addEventListener('click', () => deleteSection(section.id));

                return div;
            }

            function editSection(section) {
                document.getElementById('sectionId').value = section.id;
                document.getElementById('title').value = section.title;
                document.getElementById('description').value = section.description || '';
                document.getElementById('video_url').value = section.video_url || '';
                document.getElementById('sectionModalLabel').textContent = 'Edit Course Section';
                sectionModal.show();
            }

            function deleteSection(sectionId) {
                if (confirm('Are you sure you want to delete this section?')) {
                    fetch('delete_section.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: sectionId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadSections();
                            showAlert('success', data.message);
                        } else {
                            showAlert('danger', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('danger', 'An error occurred while deleting the section');
                    });
                }
            }

            function showAlert(type, message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} animate__animated animate__fadeIn`;
                alertDiv.textContent = message;
                document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.section'));
                setTimeout(() => alertDiv.remove(), 3000);
            }

            function escapeHtml(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        });
    </script>
</div>
