<?php
// Prevent any output before headers
ob_start();

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable error display
ini_set('log_errors', 1); // Enable error logging

// Function to send JSON response
function sendJsonResponse($status, $message, $data = []) {
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    echo json_encode(array_merge(
        ['status' => $status, 'message' => $message],
        $data
    ));
    exit;
}

// Define base directory
define('BASE_DIR', dirname(dirname(__DIR__)));

// Include required files
try {
    require_once BASE_DIR . '/lib/dhu.php';
    
    // Get database connection
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
} catch (Exception $e) {
    error_log("Initialization error: " . $e->getMessage());
    sendJsonResponse('error', 'System initialization failed');
}

// Create directories if they don't exist
$clinical_dir = BASE_DIR . '/assets/user/img/cases/clinical_file';
$diagnostic_dir = BASE_DIR . '/assets/user/img/cases/diagnostic_file';

try {
    if (!file_exists($clinical_dir)) {
        if (!mkdir($clinical_dir, 0777, true)) {
            throw new Exception('Failed to create clinical documents directory');
        }
    }
    if (!file_exists($diagnostic_dir)) {
        if (!mkdir($diagnostic_dir, 0777, true)) {
            throw new Exception('Failed to create diagnostic images directory');
        }
    }
} catch (Exception $e) {
    error_log("Directory creation error: " . $e->getMessage());
    sendJsonResponse('error', 'Failed to initialize upload directories');
}

// Function to generate random case ID
function generateCaseId($length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $case_id = '';
    for ($i = 0; $i < $length; $i++) {
        $case_id .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $case_id;
}

// Handle POST request for case creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get user ID from session
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            throw new Exception('User not logged in');
        }

        // Start transaction
        $conn->beginTransaction();

        // Prepare case data
        $case_data = [
            'case_id' => generateCaseId(),
            'user_id' => $user_id,
            'pet_name' => $_POST['pet_name'] ?? '',
            'pet_age' => intval($_POST['pet_age'] ?? 0),
            'age_unit' => $_POST['age_unit'] ?? 'year',
            'pet_weight' => floatval($_POST['pet_weight'] ?? 0),
            'pet_breed' => $_POST['pet_breed'] ?? '',
            'pet_species' => $_POST['pet_species'] ?? '',
            'clinical_history' => $_POST['clinical_history'] ?? '',
            'current_treatment' => $_POST['current_treatment'] ?? '',
            'case_notes' => $_POST['case_notes'] ?? '',
            'status' => $_POST['status'] ?? 'pending'
        ];

        // Insert case
        $sql = "INSERT INTO cases (case_id, user_id, pet_name, pet_age, age_unit, pet_weight, 
                pet_breed, pet_species, clinical_history, current_treatment, case_notes, status) 
                VALUES (:case_id, :user_id, :pet_name, :pet_age, :age_unit, :pet_weight, :pet_breed, 
                :pet_species, :clinical_history, :current_treatment, :case_notes, :status)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt->execute($case_data)) {
            throw new Exception('Failed to insert case data');
        }
        $case_id = $case_data['case_id'];

        // Handle file uploads if present
        $clinical_docs = [];
        $diagnostic_images = [];

        // Handle clinical documents
        if (isset($_FILES['clinical_docs']) && !empty($_FILES['clinical_docs']['name'][0])) {
            foreach ($_FILES['clinical_docs']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['clinical_docs']['error'][$key] === UPLOAD_ERR_OK) {
                    $filename = uniqid() . '_' . $_FILES['clinical_docs']['name'][$key];
                    $filepath = $clinical_dir . '/' . $filename;
                    
                    if (!move_uploaded_file($tmp_name, $filepath)) {
                        throw new Exception('Failed to upload clinical document');
                    }
                    $clinical_docs[] = 'clinical_file/' . $filename;
                }
            }
        }

        // Handle diagnostic images
        if (isset($_FILES['diagnostic_images']) && !empty($_FILES['diagnostic_images']['name'][0])) {
            foreach ($_FILES['diagnostic_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['diagnostic_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $filename = uniqid() . '_' . $_FILES['diagnostic_images']['name'][$key];
                    $filepath = $diagnostic_dir . '/' . $filename;
                    
                    if (!move_uploaded_file($tmp_name, $filepath)) {
                        throw new Exception('Failed to upload diagnostic image');
                    }
                    $diagnostic_images[] = 'diagnostic_file/' . $filename;
                }
            }
        }

        // Update case with file information if files were uploaded
        if (!empty($clinical_docs) || !empty($diagnostic_images)) {
            $update_sql = "UPDATE cases SET clinical_docs = :clinical_docs, 
                          diagnostic_images = :diagnostic_images WHERE case_id = :case_id";
            $stmt = $conn->prepare($update_sql);
            if (!$stmt->execute([
                'clinical_docs' => json_encode($clinical_docs),
                'diagnostic_images' => json_encode($diagnostic_images),
                'case_id' => $case_id
            ])) {
                throw new Exception('Failed to update case with file information');
            }
        }

        // Commit transaction
        $conn->commit();

        // Send success response
        sendJsonResponse('success', 'Case created successfully', ['case_id' => $case_id]);

    } catch (Exception $e) {
        // Rollback transaction if active
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
        }
        
        error_log("Case creation error: " . $e->getMessage());
        sendJsonResponse('error', $e->getMessage());
    }
}

// If we reach here, it's an invalid request
sendJsonResponse('error', 'Invalid request method');

/**
 * Helper function to handle file uploads
 */
function handle_file_upload($file, $subfolder) {
    global $upload_dir;
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("File upload error: " . $file['error']);
        return false;
    }
    
    $target_dir = $upload_dir . '/' . $subfolder;
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Sanitize filename
    $filename = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($file['name']));
    $target_path = $target_dir . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return 'assets/user/img/cases/' . $subfolder . '/' . $filename;
    }
    
    error_log("Failed to move uploaded file: " . $file['name']);
    return false;
}

/**
 * Helper function to reorganize $_FILES array for multiple uploads
 */
function reArrayFiles($file_post) {
    if (!is_array($file_post['name'])) {
        return [$file_post];
    }
    
    $file_ary = [];
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }
    return $file_ary;
}

/**
 * Fetch all cases for a user
 * @param int $user_id User ID
 * @param string $status Optional status filter
 * @return array|false Returns array of cases or false on failure
 */
function get_user_cases($user_id, $status = null) {
    global $conn;
    
    try {
        $sql = "SELECT * FROM cases WHERE user_id = :user_id";
        if ($status) {
            $sql .= " AND status = :status";
        }
        $sql .= " ORDER BY created_at DESC";

        $stmt = $conn->prepare($sql);
        $params = ['user_id' => $user_id];
        if ($status) {
            $params['status'] = $status;
        }
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching cases: " . $e->getMessage());
        return false;
    }
}

/**
 * Get a single case by ID
 * @param int $case_id Case ID
 * @param int $user_id User ID for verification
 * @return array|false Returns case data or false if not found
 */
function get_case($case_id, $user_id) {
    global $conn;
    
    try {
        $sql = "SELECT * FROM cases WHERE case_id = :case_id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':case_id' => $case_id,
            ':user_id' => $user_id
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching case: " . $e->getMessage());
        return false;
    }
}
