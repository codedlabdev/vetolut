<?php
// process_course.php
// Disable output buffering
ob_end_clean();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in");
}

$userId = $_SESSION['user_id'];

// Set content type to plain text for debugging
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Error: Invalid request method");
}

// Debug information
echo "Debug Info:\n";
echo "Request Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "Content Type: " . $_SERVER['CONTENT_TYPE'] . "\n";
echo "POST Data:\n";
print_r($_POST);
echo "\n";

// Get and validate course title
if (!isset($_POST['courseTitle'])) {
    die("Error: Course title field is missing");
}

$title = trim($_POST['courseTitle']);
if (empty($title)) {
    die("Error: Course title cannot be empty");
}

// Get and validate course category
if (!isset($_POST['courseCategory'])) {
    die("Error: Course category field is missing");
}

$category = trim($_POST['courseCategory']);
if (empty($category)) {
    die("Error: Course category cannot be empty");
}

try {
    // Include database functions after validation
    require_once '../inc/p_top.php';
    require_once BASE_DIR . 'lib/user/courses_func.php';
    
    // Increase memory limit
    ini_set('memory_limit', '256M');
    
    // Prepare course data
    $courseData = [
        'title' => $title,
        'category_id' => $category,
        'status' => 'draft'
    ];

    // Create course
    $result = createCourse($courseData, $userId);

    if ($result['success']) {
        echo "Success: " . $result['message'];
    } else {
        echo "Error: " . $result['message'];
    }

    // Free up memory
    unset($courseData, $result);
} catch (Exception $e) {
    error_log("Exception in process_course.php: " . $e->getMessage());
    die("Error: An unexpected error occurred");
}
?>
