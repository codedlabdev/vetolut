<?php
include '../inc/p_top.php';
require_once BASE_DIR . 'lib/user/courses_func.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $courseData = [
        'title' => $_POST['courseTitle'] ?? '',
        'description' => $_POST['courseDescription'] ?? '',
        'category_id' => $_POST['courseCategory'] ?? '',
        'status' => ($_POST['publishStatus'] === 'publish') ? 'published' : 'draft',
        'price' => ($_POST['coursePriceOption'] === 'paid' && isset($_POST['coursePrice'])) ? floatval($_POST['coursePrice']) : 0,
        'duration_hr' => intval($_POST['durationHours'] ?? 0),
        'duration_min' => intval($_POST['durationMinutes'] ?? 0)
    ];

    // Debugging: Log form data
    error_log('Form Data: ' . print_r($courseData, true));

    // Handle file uploads
    if (isset($_FILES['previewImage']) && $_FILES['previewImage']['error'] === UPLOAD_ERR_OK) {
        $courseData['preview_image'] = $_FILES['previewImage'];
    }

    if (isset($_FILES['previewVideo']) && $_FILES['previewVideo']['error'] === UPLOAD_ERR_OK) {
        $courseData['preview_video'] = $_FILES['previewVideo'];
    }

    // Create course using courses_func
    $result = createCourse($courseData, $_SESSION['user_id']);

    if ($result['success']) {
        $_SESSION['success_message'] = 'Course created successfully!';
        // Redirect to sections.php with the new course ID
        header('Location: update_sections.php?course_id=' . $result['course_id']);
        exit;
    } else {
        $_SESSION['error_message'] = $result['message'] ?? 'Error creating course. Please try again.';
        header('Location: create.php');
        exit;
    }
} else {
    // If not POST request, redirect to create form
    header('Location: create.php');
    exit;
}
