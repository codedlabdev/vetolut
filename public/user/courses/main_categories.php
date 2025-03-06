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

 

   <!-- Latest Courses -->
    <?php
        
        // Pagination settings
        $items_per_page = 15;
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // Get total courses first
        $result = getPublishedCourses($items_per_page, 0);
        $total_courses = $result['total'];

        // Calculate total pages
        $total_pages = ceil($total_courses / $items_per_page);

        // Ensure current page is within valid range
        $current_page = max(1, min($current_page, $total_pages));

        // Calculate correct offset
        $offset = ($current_page - 1) * $items_per_page;

        // Now get the courses with correct offset
        $result = getPublishedCourses($items_per_page, $offset);
        $courses = $result['courses'];
    ?>
  
   <div class="latest-courses mb-4">
        <div class="section-header d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="section-icon">
                    <i class="mdi mdi-clock-outline text-success"></i>
                </div>
               
                <h3 class="section-title mb-0">Latest Publish</h3>
            </div>
        </div>
        <div class="latest-courses-list">
           <?php
            // Your course fetching logic here with LIMIT and OFFSET
            $result = getPublishedCourses($items_per_page, $offset);
            $courses = $result['courses'];
            $total_courses = $result['total']; // Update total courses count
            $total_pages = ceil($total_courses / $items_per_page);

            if (!empty($courses)) {
                foreach ($courses as $course) {
                    $duration = ($course['duration_hr'] > 0 ? $course['duration_hr'] . 'h ' : '') . 
                            ($course['duration_min'] > 0 ? $course['duration_min'] . 'm' : '');
                    ?>
            <div class="course-list-item">
                <div class="row align-items-center">
                    <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
                        <div class="position-relative">
                            <span class="badge bg-gradient position-absolute top-0 start-0 m-2">Course</span>
                           <div class="course-image-wrapper">
                            <?php if ($course['banner_image']): ?>
                                <img src="<?php echo BASE_URL . $course['banner_image']; ?>" class="rounded-4 w-100" alt="<?php echo htmlspecialchars($course['title']); ?>">
                            <?php else: ?>
                                <img src="<?php echo BASE_URL; ?>assets/img/courses/default.jpg" class="rounded-4 w-100" alt="Default Course Image">
                            <?php endif; ?>
                            <?php if ($course['intro_video']): ?>
                            <div class="course-overlay">
                                <div class="play-button">
                                    <i class="mdi mdi-play"></i>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-9">
                        <div class="course-details">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title fw-semibold mb-1"><?php echo htmlspecialchars($course['title']); ?></h5>
                                <span class="price-tag">
                                    <?php if ($course['price'] == 0.00): ?>
                                        <span class="badge bg-success-subtle text-primary">Free</span>
                                    <?php else: ?>
                                        $<?php echo number_format($course['price'], 2); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <p class="instructor-name mb-2">
                                <i class="mdi mdi-account-circle"></i> 
                                <?php echo htmlspecialchars($course['instructor_name']); ?>
                            </p>
                            <div class="course-meta d-flex flex-wrap gap-3 mb-2">
                                <?php if ($duration): ?>
                                    <span class="duration"><i class="mdi mdi-clock-outline"></i> <?php echo $duration; ?></span>
                                <?php endif; ?>
                                <span class="level"><i class="mdi mdi-signal"></i> <?php echo htmlspecialchars($course['category_name']); ?></span>
                                <!--<span class="students"><i class="mdi mdi-account-group"></i> 156 students</span>-->
                                <span class="last-updated">
                                    <i class="mdi mdi-calendar"></i> 
                                    Updated <?php echo date('M j, Y', strtotime($course['updated_at'])); ?>
                                </span>
                            </div>
                            <p class="course-description text-muted mb-0">
                                <?php echo htmlspecialchars(substr($course['description'], 0, 150)) . '...'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php
            }
        } else {
            echo '<div class="alert alert-info">No courses available at the moment.</div>';
        }
        ?>

             

            
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

<link href="<?php echo BASE_URL; ?>public/user/courses/main.css" rel="stylesheet">
<script src="<?php echo BASE_URL; ?>public/user/courses/main.js"></script>
<?php 
include '../inc/float_nav.php';
include '../footer.php';
?>