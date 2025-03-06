<?php
// Prevent any output before JSON response
ob_start();
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../inc/p_top.php';
require_once BASE_DIR . 'lib/user/courses_func.php';

// Ensure we're receiving a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    header('Content-Type: application/json');
    die(json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]));
}

// Handle update action
if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['section_id'])) {
    try {
        error_log('Starting section update process');
        
        $sectionId = intval($_POST['section_id']);
        $courseId = intval($_POST['course_id']);
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        
        if (empty($title)) {
            throw new Exception('Title cannot be empty');
        }

        // Get existing section data
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT video_url FROM course_sections WHERE id = ?");
        $stmt->execute([$sectionId]);
        $existingSection = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $sectionData = [
            'title' => $title,
            'description' => $description
        ];

        // Handle video upload if present
        if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['video'];
            
            // Validate file type
            $allowedTypes = ['video/mp4', 'video/webm', 'video/ogg'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Invalid video format. Allowed formats: MP4, WebM, OGG');
            }

            // Validate file size (100MB max)
            if ($file['size'] > 100 * 1024 * 1024) {
                throw new Exception('Video file size must be less than 100MB');
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('course_video_') . '.' . $extension;
            $uploadDir = BASE_DIR . 'public/uploads/courses/videos/';
            $newFilePath = $uploadDir . $fileName;
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new Exception('Failed to create upload directory');
                }
            }

            // Delete existing video if it exists
            if ($existingSection && !empty($existingSection['video_url'])) {
                $oldFilePath = BASE_DIR . 'public/' . $existingSection['video_url'];
                if (file_exists($oldFilePath)) {
                    try {
                        unlink($oldFilePath);
                    } catch (Exception $e) {
                        error_log('Failed to delete old video: ' . $e->getMessage());
                        // Continue with upload even if delete fails
                    }
                }
            }

            // Upload new video
            if (!move_uploaded_file($file['tmp_name'], $newFilePath)) {
                throw new Exception('Failed to upload video file');
            }

            $sectionData['video_url'] = 'uploads/courses/videos/' . $fileName;
            error_log('New video uploaded successfully: ' . $fileName);
        }

        $result = updateCourseSection($sectionId, $sectionData);
        
        ob_clean();
        header('Content-Type: application/json');
        
        if ($result) {
            die(json_encode([
                'success' => true,
                'message' => 'Section updated successfully',
                'data' => $sectionData
            ]));
        } else {
            throw new Exception('Failed to update section in database');
        }
    } catch (Exception $e) {
        error_log('Section update error: ' . $e->getMessage());
        ob_clean();
        header('Content-Type: application/json');
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]));
    }
}

// Handle section creation
if (isset($_POST['sections'])) {
    try {
        $sections = $_POST['sections'];
        $results = [];

        foreach ($sections as $index => $section) {
            $videoUrl = null;
            if (isset($_FILES['sections']['name'][$index]['video'])) {
                $file = $_FILES['sections']['name'][$index]['video'];
                $fileName = time() . '_' . basename($file);
                $uploadDir = BASE_DIR . 'public/uploads/courses/videos/';
                
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                if (move_uploaded_file($_FILES['sections']['tmp_name'][$index]['video'], $uploadDir . $fileName)) {
                    $videoUrl = 'uploads/courses/videos/' . $fileName;
                }
            }

            $sectionData = [
                'title' => $section['title'],
                'description' => $section['description'],
                'video_url' => $videoUrl
            ];

            $result = addCourseSection($section['course_id'], $sectionData);
            $results[] = $result;
        }

        ob_clean(); // Clear any previous output
        header('Content-Type: application/json');
        echo json_encode([
            'success' => !in_array(false, $results),
            'message' => in_array(false, $results) ? 'Some sections failed to save' : 'All sections saved successfully'
        ]);
    } catch (Exception $e) {
        ob_clean(); // Clear any previous output
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error processing sections: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Handle delete action
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['section_id'])) {
    try {
        $sectionId = intval($_POST['section_id']);
        
        // Delete the section
        $result = deleteCourseSection($sectionId);
        
        ob_clean();
        header('Content-Type: application/json');
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Section deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete section'
            ]);
        }
        exit;
    } catch (Exception $e) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
        exit;
    }
}

// If no valid action is found
ob_clean(); // Clear any previous output
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'Invalid request'
]);
exit;