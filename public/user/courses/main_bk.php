<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../header.php';
include BASE_DIR . 'public/user/inc/top_head.php';
require_once BASE_DIR . 'lib/user/case_lists.php';
require_once BASE_DIR . 'lib/user/courses_func.php';
?>

<div class="courses-container">
    <!-- Featured Banner -->
    <div class="featured-banner mb-4">
        <div class="banner-content p-4 text-white rounded-4" style="background: linear-gradient(45deg, #003366, #0066b2);">
            <div class="row align-items-center">
                <div class="col-7">
                    <h2 class="h3 fw-bold mb-2">VETERINARY COURSES</h2>
                    <p class="display-5 fw-bold mb-3">20% OFF</p>
                    
                </div>
                <div class="col-5 d-flex justify-content-end" style="min-height: 180px;">
                    <img src="<?php echo BASE_URL; ?>/assets/img/courses/banner.png" 
                         class="banner-image"
                         alt="Veterinary Professional">
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Pills -->
    <div class="categories-section mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h6 mb-0">Filter By :</h3>
        </div>
        <div class="filter-pills mb-4">
            <div class="d-flex gap-2 overflow-auto pb-2 hide-scrollbar">
                <span class="badge rounded-pill border border-primary text-primary px-3 py-2 flex-shrink-0">Free</span>
                <span class="badge rounded-pill border border-primary text-primary px-3 py-2 flex-shrink-0">Paid</span>
                <span class="badge rounded-pill border border-primary text-primary px-3 py-2 flex-shrink-0">Popular</span>
                <span class="badge rounded-pill border border-primary text-primary px-3 py-2 flex-shrink-0">New</span>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="categories-section mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h6 mb-0">Categories By :</h3>
        </div>

 <!-- Filter Pills -->
 <div class="filter-pills mb-4">
       <!-- Filter Pills -->
<div class="filter-pills mb-4">
    <div class="d-flex gap-2 overflow-auto pb-2 hide-scrollbar">
        <?php
        $categories = getCourseCategories();
        foreach ($categories as $category) {
            echo '<span class="badge rounded-pill border border-primary text-primary px-3 py-2 flex-shrink-0">' . htmlspecialchars($category['name']) . '</span>';
        }
        ?>
    </div>
</div>
    </div>

        <div style="display:none" class="row g-3">
            <div class="col-6">
                <a href="#" class="category-item d-block text-decoration-none text-center p-3 rounded-4">
                    <div class="icon-wrapper mb-2">
                        <i class="mdi mdi-book-open-outline mdi-24px"></i>
                    </div>
                    <span class="d-block small text-dark">courses</span>
                </a>
            </div>
            <div class="col-6">
                <a href="#" class="category-item d-block text-decoration-none text-center p-3 rounded-4">
                    <div class="icon-wrapper mb-2">
                        <i class="mdi mdi-video-outline mdi-24px"></i>
                    </div>
                    <span class="d-block small text-dark">webinars</span>
                </a>
            </div>
            <div style="display:none" class="col-4">
                <a href="#" class="category-item d-block text-decoration-none text-center p-3 rounded-4">
                    <div class="icon-wrapper mb-2">
                        <i class="mdi mdi-file-document-outline mdi-24px"></i>
                    </div>
                    <span class="d-block small text-dark">studies</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Trending Webinars and Courses -->
    <div class="trending-section mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h6 mb-0">Trending Webinars and Courses</h3>
           
        </div>
        <div class="trending-slider">
            <div class="slider-container">
                <button class="slider-btn prev-btn">
                    <i class="mdi mdi-chevron-left"></i>
                </button>
                <button class="slider-btn next-btn">
                    <i class="mdi mdi-chevron-right"></i>
                </button>
                <div class="trending-wrapper">
                    <div class="trending-card">
                        <div class="card border-0 shadow-sm h-100 video-card">
                            <div class="img-wrapper position-relative">
                                <img src="https://img.youtube.com/vi/PkZNo7MFNFg/maxresdefault.jpg" class="rounded-top w-100" alt="Christian Hayes">
                                <span class="badge bg-primary position-absolute top-0 end-0 m-2">Featured</span>
                                <div class="duration-badge">45:30</div>
                            </div>
                            <div class="card-body p-3">
                                <h5 class="video-title">Advanced Veterinary Surgery Techniques</h5>
                                <div class="creator-info d-flex align-items-center mb-2">
                                    <img src="https://randomuser.me/api/portraits/men/32.jpg" class="creator-avatar" alt="Creator">
                                    <div class="creator-details">
                                        <p class="creator-name">Dr. Christian Hayes</p>
                                        <p class="location-text">London Veterinary Institute</p>
                                    </div>
                                </div>
                                <div class="video-stats d-flex align-items-center gap-2">
                                    <span class="rating"><i class="mdi mdi-star text-warning"></i> 4.5</span>
                                    <span class="views">(1,233 views)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="trending-card">
                        <div class="card border-0 shadow-sm h-100 video-card">
                            <div class="img-wrapper position-relative">
                                <img src="https://img.youtube.com/vi/W6NZfCO5SIk/maxresdefault.jpg" class="rounded-top w-100" alt="Dennis Sweeney">
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">LIVE</span>
                                <div class="duration-badge">1:30:00</div>
                            </div>
                            <div class="card-body p-3">
                                <h5 class="video-title">Veterinary Emergency Care and Stabilization</h5>
                                <div class="creator-info d-flex align-items-center mb-2">
                                    <img src="https://randomuser.me/api/portraits/men/33.jpg" class="creator-avatar" alt="Creator">
                                    <div class="creator-details">
                                        <p class="creator-name">Dr. Dennis Sweeney</p>
                                        <p class="location-text">New York Veterinary Hospital</p>
                                    </div>
                                </div>
                                <div class="video-stats d-flex align-items-center gap-2">
                                    <span class="rating"><i class="mdi mdi-star text-warning"></i> 4.5</span>
                                    <span class="views">(views)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="trending-card">
                        <div class="card border-0 shadow-sm h-100 video-card">
                            <div class="img-wrapper position-relative">
                                <img src="https://img.youtube.com/vi/yXY3f9jw7fg/maxresdefault.jpg" class="rounded-top w-100" alt="Michael Brown">
                                <span class="badge bg-primary position-absolute top-0 end-0 m-2">New</span>
                                <div class="duration-badge">30:00</div>
                            </div>
                            <div class="card-body p-3">
                                <h5 class="video-title">Veterinary Diagnostic Imaging and Radiology</h5>
                                <div class="creator-info d-flex align-items-center mb-2">
                                    <img src="https://randomuser.me/api/portraits/men/34.jpg" class="creator-avatar" alt="Creator">
                                    <div class="creator-details">
                                        <p class="creator-name">Dr. Michael Brown</p>
                                        <p class="location-text">Chicago Veterinary Clinic</p>
                                    </div>
                                </div>
                                <div class="video-stats d-flex align-items-center gap-2">
                                    <span class="rating"><i class="mdi mdi-star text-warning"></i> 4.8</span>
                                    <span class="views">(892 views)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-controls d-flex justify-content-center gap-2 mt-2">
                <div class="d-flex align-items-center gap-3">
                    
                    <div class="dots d-flex gap-2">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular courses -->
    <div class="popular-courses mb-4">
        <div class="section-header d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="section-icon">
                    <i class="mdi mdi-star-circle text-warning"></i>
                </div>
                <h3 class="section-title mb-0">Popular Courses</h3>
            </div>
            <a href="#" class="view-more-link">View more <i class="mdi mdi-arrow-right"></i></a>
        </div>
        <div class="courses-slider">
            <div class="courses-wrapper">
                <div class="course-card">
                    <div class="position-relative mb-2">
                        <span class="badge bg-gradient position-absolute top-0 start-0 m-2">Course</span>
                        <div class="course-image-wrapper">
                            <img src="https://img.youtube.com/vi/OK_JCtrrv-c/maxresdefault.jpg" class="rounded-4 w-100" alt="PHP Course">
                        </div>
                    </div>
                    <h5 class="card-title fw-semibold mb-1">PHP in One Click</h5>
                    <p class="instructor-name mb-2"><i class="mdi mdi-account-circle"></i> Ramondo Wulschner</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price-tag">$59</span>
                        <div class="course-meta d-flex align-items-center gap-2">
                            <div class="rating-badge">
                                <i class="mdi mdi-star text-warning"></i>
                                <span>4.5</span>
                            </div>
                            <span class="lessons-count"><i class="mdi mdi-book-open-page-variant"></i> 18 lessons</span>
                        </div>
                    </div>
                </div>
                <div class="course-card">
                    <div class="position-relative mb-2">
                        <span class="badge bg-gradient position-absolute top-0 start-0 m-2">Webinar</span>
                        <div class="course-image-wrapper">
                            <img src="https://img.youtube.com/vi/rfscVS0vtbw/maxresdefault.jpg" class="rounded-4 w-100" alt="Python Course">
                        </div>
                    </div>
                    <h5 class="card-title fw-semibold mb-1">Python Introduction</h5>
                    <p class="instructor-name mb-2"><i class="mdi mdi-account-circle"></i> Ramondo Wulschner</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price-tag">$39</span>
                        <div class="course-meta d-flex align-items-center gap-2">
                            <span class="live-badge"><i class="mdi mdi-access-point"></i> LIVE ON</span>
                            <span class="views-count"><i class="mdi mdi-eye"></i> 12 views</span>
                        </div>
                    </div>
                </div>
                <!-- Add more course cards for scrolling -->
                <div class="course-card">
                    <div class="position-relative mb-2">
                        <span class="badge bg-gradient position-absolute top-0 start-0 m-2">Course</span>
                        <div class="course-image-wrapper">
                            <img src="https://img.youtube.com/vi/PkZNo7MFNFg/maxresdefault.jpg" class="rounded-4 w-100" alt="JavaScript Course">
                        </div>
                    </div>
                    <h5 class="card-title fw-semibold mb-1">JavaScript Essentials</h5>
                    <p class="instructor-name mb-2"><i class="mdi mdi-account-circle"></i> Alex Johnson</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price-tag">$49</span>
                        <div class="course-meta d-flex align-items-center gap-2">
                            <div class="rating-badge">
                                <i class="mdi mdi-star text-warning"></i>
                                <span>4.7</span>
                            </div>
                            <span class="lessons-count"><i class="mdi mdi-book-open-page-variant"></i> 22 lessons</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Popular Webinars -->
    <div class="popular-courses mb-4">
        <div class="section-header d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="section-icon">
                    <i class="mdi mdi-star-circle text-danger"></i>
                </div>
                <h3 class="section-title mb-0">Popular Webinars</h3>
            </div>
            <a href="#" class="view-more-link">View more <i class="mdi mdi-arrow-right"></i></a>
        </div>
        <div class="courses-slider">
            <div class="courses-wrapper">
                <div class="course-card">
                    <div class="position-relative mb-2">
                        <span class="badge bg-gradient position-absolute top-0 start-0 m-2">Course</span>
                        <div class="course-image-wrapper">
                            <img src="https://img.youtube.com/vi/OK_JCtrrv-c/maxresdefault.jpg" class="rounded-4 w-100" alt="PHP Course">
                        </div>
                    </div>
                    <h5 class="card-title fw-semibold mb-1">PHP in One Click</h5>
                    <p class="instructor-name mb-2"><i class="mdi mdi-account-circle"></i> Ramondo Wulschner</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price-tag">$59</span>
                        <div class="course-meta d-flex align-items-center gap-2">
                            <div class="rating-badge">
                                <i class="mdi mdi-star text-warning"></i>
                                <span>4.5</span>
                            </div>
                            <span class="lessons-count"><i class="mdi mdi-book-open-page-variant"></i> 18 lessons</span>
                        </div>
                    </div>
                </div>
                <div class="course-card">
                    <div class="position-relative mb-2">
                        <span class="badge bg-gradient position-absolute top-0 start-0 m-2">Webinar</span>
                        <div class="course-image-wrapper">
                            <img src="https://img.youtube.com/vi/rfscVS0vtbw/maxresdefault.jpg" class="rounded-4 w-100" alt="Python Course">
                        </div>
                    </div>
                    <h5 class="card-title fw-semibold mb-1">Python Introduction</h5>
                    <p class="instructor-name mb-2"><i class="mdi mdi-account-circle"></i> Ramondo Wulschner</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price-tag">$39</span>
                        <div class="course-meta d-flex align-items-center gap-2">
                            <span class="live-badge"><i class="mdi mdi-access-point"></i> LIVE ON</span>
                            <span class="views-count"><i class="mdi mdi-eye"></i> 12 views</span>
                        </div>
                    </div>
                </div>
                <!-- Add more course cards for scrolling -->
                <div class="course-card">
                    <div class="position-relative mb-2">
                        <span class="badge bg-gradient position-absolute top-0 start-0 m-2">Course</span>
                        <div class="course-image-wrapper">
                            <img src="https://img.youtube.com/vi/PkZNo7MFNFg/maxresdefault.jpg" class="rounded-4 w-100" alt="JavaScript Course">
                        </div>
                    </div>
                    <h5 class="card-title fw-semibold mb-1">JavaScript Essentials</h5>
                    <p class="instructor-name mb-2"><i class="mdi mdi-account-circle"></i> Alex Johnson</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price-tag">$49</span>
                        <div class="course-meta d-flex align-items-center gap-2">
                            <div class="rating-badge">
                                <i class="mdi mdi-star text-warning"></i>
                                <span>4.7</span>
                            </div>
                            <span class="lessons-count"><i class="mdi mdi-book-open-page-variant"></i> 22 lessons</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Latest Courses -->
   <?php
    // Pagination settings
    $items_per_page = 5;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $current_page = max(1, $current_page); // Ensure page is at least 1

    // Assuming you have a function to get total number of courses
    $total_courses = 15; // Replace with actual count from database
    $total_pages = ceil($total_courses / $items_per_page);
    $current_page = min($current_page, $total_pages); // Ensure page doesn't exceed max

    $offset = ($current_page - 1) * $items_per_page;
   ?>
   <div class="latest-courses mb-4">
        <div class="section-header d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="section-icon">
                    <i class="mdi mdi-clock-outline text-success"></i>
                </div>
                <h3 class="h6 mb-0">Latest Publish</h3>
            </div>
        </div>
        <div class="latest-courses-list">
            <?php
            // Your course fetching logic here with LIMIT and OFFSET
            // Example: SELECT * FROM courses ORDER BY created_at DESC LIMIT $items_per_page OFFSET $offset
            ?>
            <div class="course-list-item">
                <div class="row align-items-center">
                    <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
                        <div class="position-relative">
                            <span class="badge bg-gradient position-absolute top-0 start-0 m-2">Webinar</span>
                            <div class="course-image-wrapper">
                                <img src="https://img.youtube.com/vi/yXY3f9jw7fg/maxresdefault.jpg" class="rounded-4 w-100" alt="Latest Course">
                                <div class="course-overlay">
                                    <button class="btn btn-light btn-sm"><i class="mdi mdi-play"></i> Watch Preview</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-9">
                        <div class="course-details">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title fw-semibold mb-1">Advanced Web Development</h5>
                                
                                <span class="price-tag">$49</span>
                            </div>
                            <p class="instructor-name mb-2"><i class="mdi mdi-account-circle"></i> John Smith</p>
                            <div class="course-meta d-flex flex-wrap gap-3 mb-2">
                                <span class="duration"><i class="mdi mdi-clock-outline"></i> 2h 30m</span>
                                <span class="level"><i class="mdi mdi-signal"></i> Intermediate</span>
                                <span class="students"><i class="mdi mdi-account-group"></i> 234 students</span>
                                <span class="last-updated"><i class="mdi mdi-calendar"></i> Updated 2 days ago</span>
                            </div>
                            <p class="course-description text-muted mb-0">Learn advanced web development techniques and best practices for building modern, scalable web applications.</p>
                           
                        </div>
                    </div>
                </div>
            </div>

            <div class="course-list-item">
                <div class="row align-items-center">
                    <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
                        <div class="position-relative">
                            <span class="badge bg-gradient position-absolute top-0 start-0 m-2">Course</span>
                            <div class="course-image-wrapper">
                                <img src="https://img.youtube.com/vi/qz0aGYrrlhU/maxresdefault.jpg" class="rounded-4 w-100" alt="Latest Course">
                                <div class="course-overlay">
                                    <button class="btn btn-light btn-sm"><i class="mdi mdi-play"></i> Watch Preview</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-9">
                        <div class="course-details">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title fw-semibold mb-1">UI/UX Design Fundamentals</h5>
                                <span class="price-tag">$49</span>
                            </div>
                            <p class="instructor-name mb-2"><i class="mdi mdi-account-circle"></i> Sarah Johnson</p>
                            <div class="course-meta d-flex flex-wrap gap-3 mb-2">
                                <span class="duration"><i class="mdi mdi-clock-outline"></i> 4h 15m</span>
                                <span class="level"><i class="mdi mdi-signal"></i> Beginner</span>
                                <span class="students"><i class="mdi mdi-account-group"></i> 156 students</span>
                                <span class="last-updated"><i class="mdi mdi-calendar"></i> Updated 3 days ago</span>
                            </div>
                            <p class="course-description text-muted mb-0">Master the fundamentals of UI/UX design and create beautiful, user-friendly interfaces that engage and delight users.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="course-list-item">
                <div class="row align-items-center">
                    <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
                        <div class="position-relative">
                            <span class="badge bg-gradient position-absolute top-0 start-0 m-2">Course</span>
                            <div class="course-image-wrapper">
                                <img src="https://img.youtube.com/vi/W6NZfCO5SIk/maxresdefault.jpg" class="rounded-4 w-100" alt="Latest Course">
                                <div class="course-overlay">
                                    <button class="btn btn-light btn-sm"><i class="mdi mdi-play"></i> Watch Preview</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-9">
                        <div class="course-details">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title fw-semibold mb-1">Mobile App Development</h5>
                                <span class="badge bg-success-subtle text-primary">Free</span>
                            </div>
                            <p class="instructor-name mb-2"><i class="mdi mdi-account-circle"></i> Mike Wilson</p>
                            <div class="course-meta d-flex flex-wrap gap-3 mb-2">
                                <span class="duration"><i class="mdi mdi-clock-outline"></i> 5h 45m</span>
                                <span class="level"><i class="mdi mdi-signal"></i> Advanced</span>
                                <span class="students"><i class="mdi mdi-account-group"></i> 89 students</span>
                                <span class="last-updated"><i class="mdi mdi-calendar"></i> Updated 5 days ago</span>
                            </div>
                            <p class="course-description text-muted mb-0">Learn to build professional mobile applications for iOS and Android platforms using modern frameworks and tools.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination Controls -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination-container mt-4">
            <nav aria-label="Course pagination">
                <ul class="pagination justify-content-center">
                    <!-- Previous button -->
                    <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" <?php echo $current_page <= 1 ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                            <i class="mdi mdi-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php
                    // Calculate range of pages to show
                    $range = 2;
                    $start_page = max(1, $current_page - $range);
                    $end_page = min($total_pages, $current_page + $range);

                    // Show first page if not in range
                    if ($start_page > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                        if ($start_page > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }

                    // Show page numbers
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        echo '<li class="page-item ' . ($current_page == $i ? 'active' : '') . '">';
                        echo '<a class="page-link" href="?page=' . $i . '">' . $i . '</a>';
                        echo '</li>';
                    }

                    // Show last page if not in range
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                    }
                    ?>

                    <!-- Next button -->
                    <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" <?php echo $current_page >= $total_pages ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                            <i class="mdi mdi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
 
</div>

<style>

/* Latest Courses List Styles */
.latest-courses-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.course-list-item {
    background: #ffffff;
    border-radius: 1rem;
    padding: 1.25rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.course-list-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.course-details {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.course-description {
    font-size: 0.875rem;
    line-height: 1.5;
}

.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1);
}

.course-meta {
    font-size: 0.813rem;
    color: #6c757d;
}

.course-meta span {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

/* Responsive adjustments for list view */
@media (max-width: 768px) {
    .course-list-item {
        padding: 1rem;
    }
    
    .course-meta {
        gap: 0.75rem;
    }
}




.courses-container {
    padding: 1rem;
    margin-bottom: 90px;
}
.smaller {
    font-size: 0.75rem;
}
.badge {
    font-weight: normal;
}
.rounded-4 {
    border-radius: 0.75rem;
}
img {
    object-fit: cover;
    aspect-ratio: 16/9;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
}
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.filter-pills .badge {
    white-space: nowrap;
}
.category-item {
    background: linear-gradient(145deg, #ffffff, #f0f0f0);
    box-shadow: 5px 5px 10px #d9d9d9, -5px -5px 10px #ffffff;
    transition: all 0.3s ease;
}
.category-item:hover {
    transform: translateY(-5px);
    box-shadow: 7px 7px 15px #d9d9d9, -7px -7px 15px #ffffff;
}
.icon-wrapper {
    background: linear-gradient(145deg, #007bff, #0056b3);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
.icon-wrapper i {
    color: white;
}
.trending-slider {
    position: relative;
    padding: 0.5rem 0;
    margin: 0 -8px;
}
.slider-container {
    overflow: hidden;
    margin: 0 8px;
    position: relative;
}
.trending-wrapper {
    display: flex;
    transition: transform 0.6s ease;
    gap: 16px;
}
.trending-card {
    flex: 0 0 auto;
    width: 200px;
}
.img-wrapper {
    height: 120px;
    overflow: hidden;
}
.img-wrapper img {
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.card:hover .img-wrapper img {
    transform: scale(1.05);
}
.card {
    background: #ffffff;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}
.badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
}
.slider-controls .dot {
    width: 8px;
    height: 8px;
    background-color: #ddd;
    border-radius: 50%;
    display: inline-block;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.slider-controls .dot.active {
    background-color: #007bff;
    width: 24px;
    border-radius: 4px;
}
.slider-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: all 0.3s ease;
}
.slider-btn:hover {
    background: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    transform: translateY(-50%) scale(1.1);
}

.next-btn {
    right: 1px;
}
.play-pause-btn {
    background: none;
    border: none;
    color: #007bff;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
}
.play-pause-btn i {
    font-size: 20px;
}
.fix-osahan-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 3px;
}

.featured-banner .banner-content {
    overflow: hidden;
    position: relative;
    background-size: cover;
}

.featured-banner .display-5 {
    font-size: 2.5rem;
    line-height: 1.2;
}

.featured-banner .btn-success {
    background-color: #00ab55;
    border-color: #00ab55;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.featured-banner .btn-success:hover {
    background-color: #008f47;
    border-color: #008f47;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.featured-banner .banner-image {
    height: 190px;
    object-fit: contain;
    object-position: right bottom;
    margin-right: -1rem;
    margin-bottom: -1rem;
    filter: drop-shadow(2px 4px 6px rgba(0, 0, 0, 0.1));
}

.video-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.video-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.video-title {
    font-size: 1.1rem;
    font-weight: 700;
    line-height: 1.4;
    margin-bottom: 0.75rem;
    color: #2c3e50;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.creator-info {
    gap: 10px;
}

.creator-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.creator-details {
    flex: 1;
}

.creator-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #444;
    margin-bottom: 0;
    line-height: 1.2;
}

.location-text {
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 0;
    line-height: 1.2;
}

.video-stats {
    font-size: 0.85rem;
    color: #666;
}

.rating {
    display: flex;
    align-items: center;
    gap: 4px;
    font-weight: 600;
}

.views {
    color: #888;
}

.duration-badge {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.75);
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
}

.img-wrapper {
    position: relative;
    height: 160px;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
}

.img-wrapper img {
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.video-card:hover .img-wrapper img {
    transform: scale(1.05);
}

.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.section-container {
    padding: 1rem 0;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    position: relative;
    padding-left: 1rem;
}

.section-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 24px;
    background: #007bff;
    border-radius: 2px;
}

.view-more {
    color: #007bff;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: color 0.3s ease;
    display: flex;
    align-items: center;
    gap: 4px;
}

.view-more:hover {
    color: #0056b3;
}

.view-more:hover i {
    transform: translateX(3px);
}

.view-more i {
    transition: transform 0.3s ease;
}

.price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #28a745;
}

.course-stats {
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid #eee;
}

.video-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.video-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.video-title {
    font-size: 1.1rem;
    font-weight: 700;
    line-height: 1.4;
    margin-bottom: 0.75rem;
    color: #2c3e50;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge.bg-success {
    background-color: #28a745 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.row.g-3 {
    margin-right: -12px;
    margin-left: -12px;
}

.row.g-3 > [class*="col-"] {
    padding-right: 12px;
    padding-left: 12px;
}

/* Section Headers */
.section-header {
    position: relative;
    padding-bottom: 0.5rem;
}

.section-icon {
    font-size: 1.5rem;
    display: flex;
    align-items: center;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
    margin-left: 0.5rem;
}

.view-more-link {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem;
    transition: color 0.3s ease;
}

.view-more-link:hover {
    color: #0056b3;
}

/* Course Cards */
.course-card {
    background: #ffffff;
    border-radius: 1rem;
    padding: 0.75rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.course-image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 0.75rem;
}

.course-image-wrapper img {
    transition: transform 0.5s ease;
}

.course-card:hover .course-image-wrapper img {
    transform: scale(1.05);
}

.course-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.course-card:hover .course-overlay {
    opacity: 1;
}

.bg-gradient {
    background: linear-gradient(45deg, #007bff, #00bcd4);
    border: none;
    padding: 0.35rem 0.75rem;
}

.card-title {
    font-size: 1rem;
    color: #2c3e50;
    line-height: 1.4;
}

.instructor-name {
    color: #6c757d;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.price-tag {
    font-size: 1.125rem;
    font-weight: 600;
    color: #007bff;
}

.course-meta {
    font-size: 0.75rem;
    color: #6c757d;
}

.rating-badge {
    background: rgba(255, 193, 7, 0.1);
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.live-badge {
    color: #dc3545;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.views-count, .lessons-count, .duration, .level {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Horizontal Scroll Styles */
.courses-slider {
    position: relative;
    overflow: hidden;
    margin: 0 -1rem;
    padding: 0.5rem 1rem;
}

.courses-wrapper {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
    -webkit-overflow-scrolling: touch;
    padding: 0.5rem 0;
}

.courses-wrapper::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

.course-card {
    flex: 0 0 300px; /* Fixed width for each card */
    scroll-snap-align: start;
    background: #ffffff;
    border-radius: 1rem;
    padding: 0.75rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

/* Update existing course card styles */
.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.course-image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 0.75rem;
    aspect-ratio: 16/9;
}

.course-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

/* Add smooth scroll behavior */
.courses-wrapper {
    scroll-behavior: smooth;
}

/* Add scroll indicators */
.courses-slider::before,
.courses-slider::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 60px;
    pointer-events: none;
    z-index: 1;
}

.courses-slider::before {
    left: 0;
    background: linear-gradient(to right, rgba(255,255,255,0.9), rgba(255,255,255,0));
}

.courses-slider::after {
    right: 0;
    background: linear-gradient(to left, rgba(255,255,255,0.9), rgba(255,255,255,0));
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .course-card {
        flex: 0 0 260px; /* Slightly smaller cards on mobile */
    }
}

/* Pagination Styles */
.pagination {
    gap: 5px;
}

.pagination .page-link {
    border-radius: 8px;
    padding: 8px 16px;
    color: #666;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.pagination .page-item.active .page-link {
    background-color: #0066b2;
    border-color: #0066b2;
    color: white;
}

.pagination .page-link:hover:not(.disabled) {
    background-color: #e9ecef;
    border-color: #dee2e6;
    color: #0066b2;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sliderContainer = document.querySelector('.slider-container');
    const wrapper = document.querySelector('.trending-wrapper');
    const slides = document.querySelectorAll('.trending-card');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const playPauseBtn = document.querySelector('.play-pause-btn');
    
    const slideCount = slides.length;
    let currentSlide = 0;
    let isPlaying = true;
    let slideInterval;
    let isTransitioning = false;

    function updateSlidePosition(instant = false) {
        const slideWidth = slides[0].offsetWidth + 16; // Width + gap
        if (instant) {
            wrapper.style.transition = 'none';
        }
        wrapper.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
        if (instant) {
            wrapper.offsetHeight; // Force reflow
            wrapper.style.transition = 'transform 0.6s ease';
        }
    }

    function updateDots() {
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide % 3);
        });
    }

    function nextSlide() {
        if (isTransitioning) return;
        isTransitioning = true;
        currentSlide++;
        
        if (currentSlide >= slideCount - 1) {
            currentSlide = 0;
        }
        
        updateSlidePosition();
        updateDots();
        
        setTimeout(() => {
            isTransitioning = false;
        }, 600);
    }

    function prevSlide() {
        if (isTransitioning) return;
        isTransitioning = true;
        currentSlide--;
        
        if (currentSlide < 0) {
            currentSlide = slideCount - 2;
        }
        
        updateSlidePosition();
        updateDots();
        
        setTimeout(() => {
            isTransitioning = false;
        }, 600);
    }

    function startAutoplay() {
        if (slideInterval) clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, 4000); // 4 seconds interval
    }

    function stopAutoplay() {
        if (slideInterval) {
            clearInterval(slideInterval);
            slideInterval = null;
        }
    }

    // Event Listeners
    prevBtn.addEventListener('click', () => {
        prevSlide();
        if (isPlaying) {
            stopAutoplay();
            startAutoplay();
        }
    });

    nextBtn.addEventListener('click', () => {
        nextSlide();
        if (isPlaying) {
            stopAutoplay();
            startAutoplay();
        }
    });

    playPauseBtn.addEventListener('click', () => {
        isPlaying = !isPlaying;
        const icon = playPauseBtn.querySelector('i');
        icon.className = isPlaying ? 'mdi mdi-pause' : 'mdi mdi-play';
        if (isPlaying) {
            startAutoplay();
        } else {
            stopAutoplay();
        }
    });

    // Touch events
    let touchStartX = 0;
    let touchEndX = 0;

    wrapper.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        stopAutoplay();
    });

    wrapper.addEventListener('touchmove', (e) => {
        if (isTransitioning) return;
        touchEndX = e.touches[0].clientX;
        const diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 5) {
            e.preventDefault();
            const slideWidth = slides[0].offsetWidth + 16;
            wrapper.style.transform = `translateX(-${(currentSlide * slideWidth) + diff}px)`;
        }
    });

    wrapper.addEventListener('touchend', () => {
        const diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        } else {
            updateSlidePosition();
        }
        if (isPlaying) startAutoplay();
    });

    // Mouse hover events
    wrapper.addEventListener('mouseenter', () => {
        if (isPlaying) stopAutoplay();
    });

    wrapper.addEventListener('mouseleave', () => {
        if (isPlaying) startAutoplay();
    });

    // Initialize
    updateSlidePosition();
    if (isPlaying) startAutoplay();
});
</script>

<?php 
include '../inc/float_nav.php';
include '../footer.php';
?>