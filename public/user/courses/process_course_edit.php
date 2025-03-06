<?php
include '../inc/p_top.php';
require_once BASE_DIR . 'lib/user/courses_func.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get course ID
    $courseId = $_POST['course_id'] ?? 0;
    if (!$courseId) {
        $_SESSION['error_message'] = 'Invalid course ID';
        header('Location: ' . BASE_URL . 'user/user_profile/index.php');
        exit;
    }

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

    // Handle file uploads if present
    if (isset($_FILES['previewImage']) && $_FILES['previewImage']['error'] === UPLOAD_ERR_OK) {
        $courseData['preview_image'] = $_FILES['previewImage'];
    }

    if (isset($_FILES['previewVideo']) && $_FILES['previewVideo']['error'] === UPLOAD_ERR_OK) {
        $courseData['preview_video'] = $_FILES['previewVideo'];
    }

    // Update course
    $result = updateCourse($courseId, $courseData, $_SESSION['user_id']);

    if ($result['success']) {
        $_SESSION['success_message'] = 'Course updated successfully!';
        header('Location: edit.php?id=' . $courseId);
        exit;
    } else {
        $_SESSION['error_message'] = $result['message'] ?? 'Error updating course. Please try again.';
        header('Location: edit.php?id=' . $courseId);
        exit;
    }
} else {
    header('Location: ' . BASE_URL . 'user/user_profile/index.php');
    exit;
}
