<?php

include '../header.php';


?>

<div class="d-flex align-items-center justify-content-between mb-auto p-3 bg-white shadow-sm osahan-header">


    <?php
    // Get the current page URL (full URL path)
    $currentPage = $_SERVER['REQUEST_URI'];

    // Define the paths you want to check (relative to the base URL)
    $userProfilePage = '/user/user_profile/';
    $chatListPage = '/user/chat/lists.php';
    $CasePage = '/public/user/cases/create.php';
    $CaseEditPage = '/public/user/cases/edit_case.php';
    $CoursesPage = '/user/courses/create.php';
    $CoursesEditPage = '/user/courses/edit.php';
    $CoursesSectionPage = '/user/courses/sections.php';
    $CoursesupdateSectionPage = '/user/courses/update_section.php';
    $courseDetailsPage = '/user/courses/details.php';

    // Check if the current page is 'user_profile' and display "My Profile" or "Profile"
    if (strpos($currentPage, $userProfilePage) !== false) {
        if ($userId == $loggedInUserId): // Check if the logged-in user is the owner
            echo '<a href="javascript:void(0);" onclick="window.history.back();"
            class="text-dark bg-white shadow rounded-circle icon">
            <span class="mdi mdi-arrow-left mdi-18px"></span>
        </a>';
            echo '<h6 class="mb-0 ms-3 me-auto fw-bold">My Profile</h6>';

        else:
            echo '<a href="javascript:void(0);" onclick="window.history.back();"
            class="text-dark bg-white shadow rounded-circle icon">
            <span class="mdi mdi-arrow-left mdi-18px"></span>
        </a>';
            echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Profile</h6>';

        endif;
    }
    // Check if the current page is 'chat/lists.php' and display "Chat List"
    elseif (strpos($currentPage, $chatListPage) !== false) {
        echo '<a href="' . BASE_URL . 'public/user/dashboard.php"
        class="text-dark bg-white shadow rounded-circle icon">
        <span class="mdi mdi-arrow-left mdi-18px"></span>
    </a>';
        echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Chat List</h6>';
    }

    // Check if the current page is 'case/create.php' and display "create case"
    elseif (strpos($currentPage, $CasePage) !== false) {
        echo '<a href="javascript:void(0);" onclick="window.history.back();"
            class="text-dark bg-white shadow rounded-circle icon">
            <span class="mdi mdi-arrow-left mdi-18px"></span>
        </a>';
        echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Create Case</h6>';
    }

    // Check if the current page is 'case/edit_case.php' and display "Edit Case"
    elseif (strpos($currentPage, $CaseEditPage) !== false) {
        echo '<a href="javascript:void(0);" onclick="window.history.back();"
            class="text-dark bg-white shadow rounded-circle icon">
            <span class="mdi mdi-arrow-left mdi-18px"></span>
        </a>';
        echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Edit Case</h6>';
    }
    // Check if the current page is 'courses/create.php' and display "create case"
    elseif (strpos($currentPage, $CoursesPage) !== false) {
        echo '<a href="javascript:void(0);" onclick="window.history.back();"
            class="text-dark bg-white shadow rounded-circle icon">
            <span class="mdi mdi-arrow-left mdi-18px"></span>
        </a>';
        echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Create Courses</h6>';
    }
    // Check if the current page is 'courses/create.php' and display "create case"
    elseif (strpos($currentPage, $CoursesEditPage) !== false) {
        echo '<a href="' . BASE_URL . 'public/user/user_profile/index.php"
            class="text-dark bg-white shadow rounded-circle icon">
            <span class="mdi mdi-arrow-left mdi-18px"></span>
        </a>';
        echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Edit Courses</h6>';
    }

    // Check if the current page is 'sections.php' and display "create case"
    elseif (strpos($currentPage, $CoursesSectionPage) !== false) {
        echo '<a href="' . BASE_URL . 'public/user/user_profile/index.php"
            class="text-dark bg-white shadow rounded-circle icon">
            <span class="mdi mdi-arrow-left mdi-18px"></span>
        </a>';
        echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Sections</h6>';
    }
    // Check if the current page is 'sections.php' and display "create case"
    elseif (strpos($currentPage, $CoursesupdateSectionPage) !== false) {
        $course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';
        echo '<a href="' . BASE_URL . 'public/user/courses/edit.php?id=' . $course_id . '"
            class="text-dark bg-white shadow rounded-circle icon">
            <span class="mdi mdi-arrow-left mdi-18px"></span>
        </a>';
        echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Add / Update Sections</h6>';
    } elseif (strpos($currentPage, $courseDetailsPage) !== false) {
        // Get the course_id from URL parameter - changed from course_id to id
        $course_id = isset($_GET['id']) ? $_GET['id'] : '';

        // Include required files if not already included
        require_once BASE_DIR . 'lib/user/courses_func.php';

        if ($course_id) {
            $course = getCourseDetails($course_id);
            if ($course) {
                echo '<a href="javascript:void(0);" onclick="window.history.back();"
            class="text-dark bg-white shadow rounded-circle icon">
            <span class="mdi mdi-arrow-left mdi-18px"></span>
        </a>';
                echo '<h6 class="mb-0 ms-3 me-auto fw-bold">' . htmlspecialchars($course['title']) . '</h6>';
            } else {
                echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Course Not Found</h6>';
            }
        } else {
            echo '<h6 class="mb-0 ms-3 me-auto fw-bold">Course Details</h6>';
        }
    }

    ?>

    <?php if ($userId == $loggedInUserId): ?>
        <div class="d-flex align-items-center gap-3 logout">
            <a class=" d-flex align-items-center justify-content-center fs-5 bg-white shadow rounded-circle icon hc-nav-1"
                href="?logout=true" role="button" aria-controls="hc-nav-1">
                <i class="bi bi-box-arrow-right" style="color: red; font-weight: bolder;"></i>
            </a>
        </div>
    <?php endif; ?>
</div>