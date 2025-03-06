<?php
// search.php
?>

<div class="appointment-upcoming d-flex flex-column vh-100">
    <?php include 'inc/p_others.php'; 
    require_once BASE_DIR . 'lib/dhu.php';
    require_once BASE_DIR . 'lib/user/table.php';
    require_once BASE_DIR . 'lib/user/search_func.php';

    $loggedInUserId = $_SESSION['user_id'];
    
    // Get search parameters
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 10;
    ?>

    <div class="vh-100 my-auto overflow-auto body-fix-osahan-footer">
        <div class="container" style="margin-top: 20px;">
            <!-- Search Form -->
            <div class="row mb-3">
                <div class="col-12">
                    <form method="GET" action="" class="search-form">
                        <div class="search-wrapper bg-white rounded-4 shadow-sm p-3">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-transparent border-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="q" class="form-control border-0 shadow-none" 
                                    placeholder="AI queries, courses, colleagues...." 
                                    value="<?php echo htmlspecialchars($query); ?>">
                                <button class="btn btn-primary rounded-3 px-4" type="submit">Search</button>
                            </div>
                            
                            <!-- Filters Section -->
                            <div class="d-flex flex-wrap gap-2 align-items-center pt-2">
                                <div class="filter-group me-3">
                                    <select name="category" class="form-select form-select-sm border-0 bg-light rounded-3" onchange="this.form.submit()">
                                        <option value="" <?php echo $category === '' ? 'selected' : ''; ?>>All Categories</option>
                                        <option value="colleagues" <?php echo $category === 'colleagues' ? 'selected' : ''; ?>>Colleagues</option>
                                        <option value="courses" <?php echo $category === 'courses' ? 'selected' : ''; ?>>Courses</option>
                                        <option value="cases" <?php echo $category === 'cases' ? 'selected' : ''; ?>>Cases</option>
                                        <option value="ai_queries" <?php echo $category === 'ai_queries' ? 'selected' : ''; ?>>AI queries</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Section -->
            <div class="results-section">
                <?php
                $search_results = [];
                
                if (!empty($query)) {
                    if ($category === 'colleagues' || empty($category)) {
                        $users = search_users($query);
                        foreach ($users as $user) {
                            $search_results[] = [
                                'type' => 'colleague',
                                'id' => $user['id'],
                                'name' => $user['f_name'] . ' ' . $user['l_name'],
                                'profession' => $user['profession'],
                                'location' => $user['country'],
                                'profile_image' => $user['image'] ?? null,
                                'email' => $user['email'],
                                'phone' => $user['phone']
                            ];
                        }
                    }
                    
                    if ($category === 'ai_queries' || empty($category)) {
                        $ai_queries = search_ai_queries($query);
                        foreach ($ai_queries as $query_item) {
                            $search_results[] = [
                                'type' => 'ai_query',
                                'id' => $query_item['id'],
                                'chat_id' => $query_item['chat_id'],
                                'title' => $query_item['title'],
                                'prompt' => mb_substr($query_item['prompt'], 0, 150) . '...',
                                'created_at' => date('M d, Y', strtotime($query_item['created_at'])),
                                'author' => $query_item['f_name'] . ' ' . $query_item['l_name']
                            ];
                        }
                    }
                }
                ?>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-muted">Search Results</h6>
                    <div class="d-flex align-items-center gap-2">
                        <?php if($query || $category): ?>
                            <a href="search.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-redo-alt"></i> Reset
                            </a>
                        <?php endif; ?>
                        <?php if($query): ?>
                            <small class="text-muted">Showing results for "<?php echo htmlspecialchars($query); ?>"</small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="results-container mb-4">
                    <?php if (!empty($search_results)): ?>
                        <div class="row g-3">
                            <?php foreach ($search_results as $result): ?>
                                <div class="col-12">
                                    <?php if ($result['type'] === 'colleague'): ?>
                                        <!-- Colleague Card -->
                                        <div class="card border-0 shadow-sm hover-card">
                                            <div class="card-body">
                                                <div class="d-flex flex-wrap align-items-start">
                                                    <div class="flex-shrink-0 me-3 mb-3 mb-md-0">
                                                        <?php if (!empty($result['profile_image'])): ?>
                                                            <img src="<?php echo htmlspecialchars($result['profile_image']); ?>" 
                                                                 alt="Profile" 
                                                                 class="rounded-circle colleague-avatar"
                                                                 style="width: 60px; height: 60px; object-fit: cover;"
                                                                 onerror="this.src='assets/img/default-avatar.jpg';">
                                                        <?php else: ?>
                                                            <img src="assets/img/default-avatar.jpg" 
                                                                 alt="Profile" 
                                                                 class="rounded-circle colleague-avatar"
                                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            <a href="profile.php?id=<?php echo $result['id']; ?>" 
                                                               class="text-decoration-none text-dark">
                                                                <?php echo htmlspecialchars($result['name']); ?>
                                                            </a>
                                                        </h6>
                                                        <p class="mb-2 text-muted small">
                                                            <?php echo htmlspecialchars($result['profession'] ?? ''); ?>
                                                        </p>
                                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                                            <?php if (!empty($result['location'])): ?>
                                                            <span class="badge bg-light text-dark d-flex align-items-center">
                                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                                <span class="text-truncate" style="max-width: 150px;">
                                                                    <?php echo htmlspecialchars($result['location']); ?>
                                                                </span>
                                                            </span>
                                                            <?php endif; ?>
                                                            
                                                            <?php if (!empty($result['email'])): ?>
                                                            <span class="badge bg-light text-dark d-flex align-items-center">
                                                                <i class="fas fa-envelope me-1"></i>
                                                                <span class="text-truncate" style="max-width: 200px;">
                                                                    <?php echo htmlspecialchars($result['email']); ?>
                                                                </span>
                                                            </span>
                                                            <?php endif; ?>
                                                            
                                                            <?php if (!empty($result['phone'])): ?>
                                                            <span class="badge bg-light text-dark d-flex align-items-center">
                                                                <i class="fas fa-phone me-1"></i>
                                                                <span class="text-truncate" style="max-width: 150px;">
                                                                    <?php echo htmlspecialchars($result['phone']); ?>
                                                                </span>
                                                            </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php elseif ($result['type'] === 'ai_query'): ?>
                                        <!-- AI Query Card -->
                                        <div class="card border-0 shadow-sm hover-card">
                                            <div class="card-body">
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="card-title mb-0">
                                                            <a href="ai_chat.php?id=<?php echo $result['chat_id']; ?>" 
                                                               class="text-decoration-none text-dark">
                                                                <i class="fas fa-robot me-2 text-primary"></i>
                                                                <?php echo htmlspecialchars($result['title']); ?>
                                                            </a>
                                                        </h6>
                                                    </div>
                                                    <p class="card-text text-muted small mb-2">
                                                        <?php echo htmlspecialchars($result['prompt']); ?>
                                                    </p>
                                                    <div class="d-flex flex-wrap gap-2 align-items-center mt-2">
                                                        <span class="badge bg-light text-dark d-flex align-items-center">
                                                            <i class="fas fa-user me-1"></i>
                                                            <span class="text-truncate" style="max-width: 150px;">
                                                                <?php echo htmlspecialchars($result['author']); ?>
                                                            </span>
                                                        </span>
                                                        <span class="badge bg-light text-dark d-flex align-items-center">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            <?php echo $result['created_at']; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($search_results) > $per_page): ?>
                            <div class="d-flex justify-content-center mt-4">
                                <nav aria-label="Search results pagination">
                                    <ul class="pagination pagination-sm">
                                        <?php
                                        $total_pages = ceil(count($search_results) / $per_page);
                                        $max_visible_pages = 5;
                                        
                                        // Previous button
                                        if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo ($page - 1); ?>&q=<?php echo urlencode($query); ?>&category=<?php echo urlencode($category); ?>">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php endif;

                                        // Calculate visible page range
                                        $start_page = max(1, min($page - floor($max_visible_pages / 2), $total_pages - $max_visible_pages + 1));
                                        $end_page = min($start_page + $max_visible_pages - 1, $total_pages);

                                        // Page numbers
                                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                                            <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>&q=<?php echo urlencode($query); ?>&category=<?php echo urlencode($category); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor;

                                        // Next button
                                        if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo ($page + 1); ?>&q=<?php echo urlencode($query); ?>&category=<?php echo urlencode($category); ?>">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-search fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No results found</h5>
                            <p class="text-muted small">Try adjusting your search terms or filters</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
        include 'inc/float_nav.php';
        ?>
</div>

<?php include 'footer.php'; ?>

<!-- Add custom styles -->
<style>
.search-wrapper {
    transition: all 0.3s ease;
}
.search-wrapper:focus-within {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important;
}
.filter-group select {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.filter-group select:hover {
    background-color: #e9ecef !important;
}
.results-container {
    min-height: 200px;
}
.hover-card {
    transition: all 0.2s ease;
}
.hover-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.08)!important;
}

/* Colleague specific styles */
.colleague-avatar {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,.1);
}

/* Course specific styles */
.course-thumbnail-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 16/9;
    background-color: #000;
    overflow: hidden;
    border-radius: 8px 8px 0 0;
}

.course-video-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.course-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(0deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.play-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.play-icon i {
    color: var(--bs-primary);
    font-size: 24px;
    margin-left: 4px;
}

.hover-card:hover .course-overlay {
    opacity: 1;
}

.hover-card:hover .course-video-thumbnail {
    transform: scale(1.05);
}

.price-badge {
    font-size: 0.9rem;
    padding: 0.5em 1em;
    z-index: 2;
}

.card-title {
    font-size: 1.1rem;
    line-height: 1.4;
}

.card-title a:hover {
    color: var(--bs-primary) !important;
}

/* Pagination styles */
.pagination {
    margin-bottom: 0;
}
.pagination .page-link {
    border-radius: 4px;
    margin: 0 2px;
    border: none;
    color: #6c757d;
    background-color: #f8f9fa;
    padding: 0.375rem 0.75rem;
}
.pagination .page-item.active .page-link {
    background-color: var(--bs-primary);
    color: white;
}
.pagination .page-link:hover {
    background-color: #e9ecef;
    color: var(--bs-primary);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .container {
        padding: 0 15px;
    }
    .search-wrapper {
        padding: 0.75rem !important;
    }
    .filter-group {
        flex: 1 1 auto;
    }
    .colleague-avatar {
        width: 45px;
        height: 45px;
    }
    .play-icon {
        width: 40px;
        height: 40px;
    }
    .play-icon i {
        font-size: 18px;
    }
    .card-title {
        font-size: 1rem;
    }
    .price-badge {
        font-size: 0.8rem;
    }
}
</style>
