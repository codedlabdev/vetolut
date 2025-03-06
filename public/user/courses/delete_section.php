<?php
require_once '../../config.php';
require_once BASE_DIR . 'lib/user/courses_func.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

// Get and validate input
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['id'])) {
    echo json_encode(['success' => false, 'message' => 'Section ID is required']);
    exit;
}

$section_id = intval($input['id']);

// Validate section ownership
$stmt = $db->prepare("
    SELECT cs.*, c.user_id 
    FROM course_sections cs 
    JOIN courses c ON cs.course_id = c.id 
    WHERE cs.id = ?
");
$stmt->execute([$section_id]);
$section = $stmt->fetch();

if (!$section || $section['user_id'] != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Not authorized to delete this section']);
    exit;
}

try {
    $stmt = $db->prepare("DELETE FROM course_sections WHERE id = ?");
    $result = $stmt->execute([$section_id]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Section deleted successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete section']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
