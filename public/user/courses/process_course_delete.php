<?php
include '../inc/p_top.php';
require_once BASE_DIR . 'lib/user/courses_func.php';

// Ensure user is logged in

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please log in to continue']);
    exit;
}

$userId = $_SESSION['user_id'];

// Get course ID from POST data
$courseId = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

if (!$courseId) {
    echo json_encode(['success' => false, 'message' => 'Invalid course ID']);
    exit;
}

// Get course details to verify ownership and get file paths
$course = getCourse($courseId, $userId);

if (!$course) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Course not found or you do not have permission to delete it']);
    exit;
}

// Delete the course
if (deleteCourse($courseId, $userId)) {
    header('Location: ' . BASE_URL . 'public/user/user_profile/index.php?msg=deleted');
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to delete the course']);
    exit;
}
