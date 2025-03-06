<?php
// Prevent any output before headers
ob_start();

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable error display
ini_set('log_errors', 1); // Enable error logging

// Define base directory
define('BASE_DIR', dirname(dirname(__DIR__)));

require_once BASE_DIR . '/lib/dhu.php';
require_once BASE_DIR . '/lib/db.php';
require_once BASE_DIR . '/lib/helpers.php';

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

function handle_file_upload($file, $subfolder) {
    $target_dir = BASE_DIR . '/assets/user/img/cases/' . $subfolder . '/';
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $subfolder . '/' . $new_filename;
    }
    return false;
}

function reArrayFiles($file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }
    return $file_ary;
}

function update_case($case_id, $user_id, $data) {
    try {
        error_log("Starting update_case for case_id: $case_id, user_id: $user_id");
        
        // Get database connection
        $conn = getDBConnection();
        if (!$conn) {
            throw new Exception('Database connection failed');
        }
        
        // Start transaction
        $conn->beginTransaction();
        error_log("Transaction started");
        
        // First verify the case exists and belongs to the user
        $check_sql = "SELECT case_id FROM cases WHERE case_id = :case_id AND user_id = :user_id";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([':case_id' => $case_id, ':user_id' => $user_id]);
        
        if (!$check_stmt->fetch()) {
            throw new Exception("Case not found or access denied");
        }
        
        error_log("Case verification passed");
        
        // Update main case data
        $sql = "UPDATE cases SET 
                pet_name = :pet_name,
                pet_age = :pet_age,
                age_unit = :age_unit,
                pet_weight = :pet_weight,
                pet_breed = :pet_breed,
                pet_species = :pet_species,
                clinical_history = :clinical_history,
                current_treatment = :current_treatment,
                case_notes = :case_notes,
                updated_at = NOW()
                WHERE case_id = :case_id AND user_id = :user_id";
                
        error_log("Prepared update SQL: " . $sql);
        error_log("Update data: " . print_r($data, true));
        
        $stmt = $conn->prepare($sql);
        $update_data = [
            ':pet_name' => $data['pet_name'],
            ':pet_age' => $data['pet_age'],
            ':age_unit' => $data['age_unit'],
            ':pet_weight' => $data['pet_weight'],
            ':pet_breed' => $data['pet_breed'],
            ':pet_species' => $data['pet_species'],
            ':clinical_history' => $data['clinical_history'] ?? '',
            ':current_treatment' => $data['current_treatment'] ?? '',
            ':case_notes' => $data['case_notes'] ?? '',
            ':case_id' => $case_id,
            ':user_id' => $user_id
        ];
        
        error_log("Executing update with data: " . print_r($update_data, true));
        $stmt->execute($update_data);
        error_log("Main case data updated");

        // Handle file uploads and update file arrays
        $clinical_docs = [];
        $diagnostic_images = [];

        // Get existing files from database
        if (!empty($data['existing_clinical_docs'])) {
            $clinical_docs = json_decode($data['existing_clinical_docs'], true) ?? [];
        }
        if (!empty($data['existing_diagnostic_imgs'])) {
            $diagnostic_images = json_decode($data['existing_diagnostic_imgs'], true) ?? [];
        }

        // Handle new file uploads if present
        if (!empty($_FILES['clinical_docs'])) {
            error_log("Processing clinical docs");
            $clinical_files = reArrayFiles($_FILES['clinical_docs']);
            foreach ($clinical_files as $file) {
                if ($file['error'] === 0) {
                    $file_path = handle_file_upload($file, 'clinical_file');
                    if ($file_path) {
                        $clinical_docs[] = $file_path;
                        error_log("Clinical file uploaded: " . $file_path);
                    }
                }
            }
        }

        if (!empty($_FILES['diagnostic_imgs'])) {
            error_log("Processing diagnostic images");
            $diagnostic_files = reArrayFiles($_FILES['diagnostic_imgs']);
            foreach ($diagnostic_files as $file) {
                if ($file['error'] === 0) {
                    $file_path = handle_file_upload($file, 'diagnostic_file');
                    if ($file_path) {
                        $diagnostic_images[] = $file_path;
                        error_log("Diagnostic file uploaded: " . $file_path);
                    }
                }
            }
        }

        // Update file arrays in database
        if (!empty($clinical_docs) || !empty($diagnostic_images)) {
            $update_files_sql = "UPDATE cases SET 
                               clinical_docs = :clinical_docs,
                               diagnostic_images = :diagnostic_images 
                               WHERE case_id = :case_id AND user_id = :user_id";
            $stmt = $conn->prepare($update_files_sql);
            $stmt->execute([
                ':clinical_docs' => json_encode($clinical_docs),
                ':diagnostic_images' => json_encode($diagnostic_images),
                ':case_id' => $case_id,
                ':user_id' => $user_id
            ]);
            error_log("File arrays updated in database");
        }

        // Commit transaction
        $conn->commit();
        error_log("Transaction committed successfully");
        return true;

    } catch (Exception $e) {
        error_log("Error in update_case: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
            error_log("Transaction rolled back");
        }
        throw $e;
    }
}

function delete_case_file($file_id, $user_id) {
    try {
        // Get database connection
        $conn = getDBConnection();
        if (!$conn) {
            throw new Exception('Database connection failed');
        }
        
        // First verify the file belongs to the user's case
        $sql = "SELECT cf.file_path 
                FROM case_files cf 
                JOIN cases c ON cf.case_id = c.case_id 
                WHERE cf.id = :file_id AND c.user_id = :user_id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':file_id' => $file_id, ':user_id' => $user_id]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$file) {
            return false;
        }
        
        // Delete the physical file
        $full_path = BASE_DIR . '/assets/user/img/cases/' . $file['file_path'];
        if (file_exists($full_path)) {
            unlink($full_path);
        }
        
        // Delete the database record
        $sql = "DELETE FROM case_files WHERE id = :file_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':file_id' => $file_id]);
        
        return true;
    } catch (Exception $e) {
        error_log("Error deleting case file: " . $e->getMessage());
        return false;
    }
}

function delete_case($case_id, $user_id) {
    try {
        error_log("Attempting to delete case: $case_id for user: $user_id");
        
        // Get database connection
        $conn = getDBConnection();
        if (!$conn) {
            throw new Exception('Database connection failed');
        }
        
        // Start transaction
        $conn->beginTransaction();
        
        // First verify the case exists and belongs to the user
        $check_sql = "SELECT case_id, clinical_docs, diagnostic_images FROM cases WHERE case_id = :case_id AND user_id = :user_id";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([':case_id' => $case_id, ':user_id' => $user_id]);
        $case = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$case) {
            throw new Exception("Case not found or access denied");
        }
        
        // Delete physical files if they exist
        $base_upload_dir = BASE_DIR . '/assets/user/img/cases/';
        
        // Delete clinical documents
        if (!empty($case['clinical_docs'])) {
            $clinical_docs = json_decode($case['clinical_docs'], true);
            foreach ($clinical_docs as $doc) {
                $file_path = $base_upload_dir . $doc;
                if (file_exists($file_path)) {
                    unlink($file_path);
                    error_log("Deleted clinical doc: $doc");
                }
            }
        }
        
        // Delete diagnostic images
        if (!empty($case['diagnostic_images'])) {
            $diagnostic_images = json_decode($case['diagnostic_images'], true);
            foreach ($diagnostic_images as $img) {
                $file_path = $base_upload_dir . $img;
                if (file_exists($file_path)) {
                    unlink($file_path);
                    error_log("Deleted diagnostic image: $img");
                }
            }
        }
        
        // Delete the case from the database
        $delete_sql = "DELETE FROM cases WHERE case_id = :case_id AND user_id = :user_id";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->execute([':case_id' => $case_id, ':user_id' => $user_id]);
        
        // Commit transaction
        $conn->commit();
        error_log("Case $case_id deleted successfully");
        
        return true;
    } catch (Exception $e) {
        error_log("Error deleting case: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
            error_log("Transaction rolled back");
        }
        
        throw $e;
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        error_log("POST request received: " . print_r($_POST, true));
        error_log("FILES received: " . print_r($_FILES, true));
        
        $user_id = $_SESSION['user_id'] ?? null;
        $case_id = $_POST['case_id'] ?? null;
        
        if (!$user_id || !$case_id) {
            sendJsonResponse('error', 'User not logged in or case ID not provided');
        }

        if ($_POST['action'] === 'update') {
            if (update_case($case_id, $user_id, $_POST)) {
                sendJsonResponse('success', 'Case updated successfully');
            } else {
                sendJsonResponse('error', 'Failed to update case');
            }
        } elseif ($_POST['action'] === 'delete') {
            if (delete_case($case_id, $user_id)) {
                sendJsonResponse('success', 'Case deleted successfully');
            } else {
                sendJsonResponse('error', 'Failed to delete case');
            }
        } elseif ($_POST['action'] === 'delete_file') {
            $file_id = $_POST['file_id'] ?? null;
            if (!$file_id) {
                sendJsonResponse('error', 'File ID not provided');
            }
            
            if (delete_case_file($file_id, $user_id)) {
                sendJsonResponse('success', 'File deleted successfully');
            } else {
                sendJsonResponse('error', 'Failed to delete file');
            }
        } else {
            sendJsonResponse('error', 'Invalid action');
        }
    } catch (Exception $e) {
        error_log("Error in edit_case.php: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        sendJsonResponse('error', $e->getMessage());
    }
} else {
    sendJsonResponse('error', 'Invalid request method');
}
