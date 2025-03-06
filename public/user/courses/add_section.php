<?php
require_once '../../config.php';
require_once BASE_DIR . 'lib/user/courses_func.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

// Validate input
if (!isset($_POST['course_id']) || !isset($_POST['title'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$course_id = intval($_POST['course_id']);
$title = trim($_POST['title']);
$description = isset($_POST['description']) ? trim($_POST['description']) : null;
$video_url = isset($_POST['video_url']) ? trim($_POST['video_url']) : null;

// Validate course ownership
$stmt = $db->prepare("SELECT user_id FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course || $course['user_id'] != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Not authorized to modify this course']);
    exit;
}

try {
    $stmt = $db->prepare("INSERT INTO course_sections (course_id, title, description, video_url) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$course_id, $title, $description, $video_url]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Section added successfully',
            'id' => $db->lastInsertId()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add section']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
