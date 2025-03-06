<?php
// network_p.php
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
require_once BASE_DIR . 'lib/dhu.php'; // Include helper functions

// Define the path to save images
define('UPLOAD_DIR', BASE_DIR . 'assets/user/img/networks/');

// Function to save or update a post with single and multiple images
function savePost($user_id, $text, $photos, $post_id = null) {
    $pdo = getDBConnection();
    
    // Ensure the upload directory exists
    if (!file_exists(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $firstPhotoPathDb = null;

    // Handle multiple photos
    if (isset($photos) && is_array($photos['name'])) {
        // Count the number of uploaded files
        $numFiles = count($photos['name']);
        for ($i = 0; $i < $numFiles; $i++) {
            if ($photos['error'][$i] == UPLOAD_ERR_OK) {
                $photoName = uniqid('post_', true) . '_' . basename($photos['name'][$i]);
                $photoPath = UPLOAD_DIR . $photoName;

                if (move_uploaded_file($photos['tmp_name'][$i], $photoPath)) {
                    $photoPathDb = 'assets/user/img/networks/' . $photoName;
                    
                    // Insert logic for single vs multiple images
                    if ($numFiles === 1) {
                        // If only one image, insert into network_post
                        $firstPhotoPathDb = $photoPathDb;
                    } else if ($i === 0) {
                        // If it's the first image, insert into network_post
                        $firstPhotoPathDb = $photoPathDb;
                    } 
                }
            }
        }
    }

    // Prepare SQL statement for network_post
    if ($post_id) {
        // Update existing post with the first image
        $sql = "UPDATE network_post SET text = :text, photo = :photo WHERE id = :id AND user_id = :user_id";
        $params = [
            ':id' => $post_id,
            ':text' => $text,
            ':photo' => $firstPhotoPathDb,
            ':user_id' => $user_id
        ];
    } else {
        // Create a new post with the first image
        $sql = "INSERT INTO network_post (user_id, text, photo, date) VALUES (:user_id, :text, :photo, NOW())";
        $params = [
            ':user_id' => $user_id,
            ':text' => $text,
            ':photo' => $firstPhotoPathDb
        ];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        // Get the last inserted ID
        $post_id = $pdo->lastInsertId();
    }

    // Now insert the images into network_post_imgs
    if (isset($photos) && is_array($photos['name'])) {
        for ($i = 0; $i < count($photos['name']); $i++) {
            if ($photos['error'][$i] == UPLOAD_ERR_OK) {
                // Only insert remaining images if there are more than one
                if (count($photos['name']) > 1 && $i > 0) {
                    $photoName = uniqid('post_', true) . '_' . basename($photos['name'][$i]);
                    $photoPath = UPLOAD_DIR . $photoName;
                    move_uploaded_file($photos['tmp_name'][$i], $photoPath);
                    $photoPathDb = 'assets/user/img/networks/' . $photoName;
                    // Insert into network_post_imgs
                    $sql = "INSERT INTO network_post_imgs (network_post_id, image, created_at) VALUES (:network_post_id, :image, NOW())";
                    $params = [
                        ':network_post_id' => $post_id,
                        ':image' => $photoPathDb
                    ];
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                }
            }
        }
    }

    // Execute the SQL statement for network_post
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
}

// Function to get current photos
function getCurrentPhotos($post_id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT photo FROM network_post WHERE id = :id");
    $stmt->execute([':id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    return $post['photo'] ?? null;
}

// Function to delete a post and its photos
function deletePost($post_id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT photo FROM network_post WHERE id = :id");
    $stmt->execute([':id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        // Delete the record from the database
        $deleteStmt = $pdo->prepare("DELETE FROM network_post WHERE id = :id");
        $deleteStmt->execute([':id' => $post_id]);

        // If photos exist, delete them from the filesystem
        if ($post['photo']) {
            $photos = json_decode($post['photo'], true);
            if ($photos) {
                foreach ($photos as $photo) {
                    $photoPath = BASE_DIR . $photo;
                    if (file_exists($photoPath)) {
                        unlink($photoPath);
                    }
                }
            }
        }
        return true;
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $user_id = $_SESSION['user_id'];

    // Case handling
    if (isset($_POST['pet_name'])) {
        require_once 'case_func.php';
        
        $case_data = [
            'pet_name' => $_POST['pet_name'],
            'pet_age' => $_POST['pet_age'],
            'age_unit' => $_POST['age_unit'],
            'pet_weight' => $_POST['pet_weight'],
            'pet_breed' => $_POST['pet_breed'],
            'pet_species' => $_POST['pet_species'],
            'clinical_history' => $_POST['clinical_history'],
            'current_treatment' => $_POST['current_treatment'],
            'case_notes' => $_POST['case_notes'],
            'status' => $_POST['status'] ?? 'pending'
        ];
        
        $case_id = insert_case($case_data);
        
        if ($case_id) {
            echo json_encode(['status' => 'success', 'case_id' => $case_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create case']);
        }
        exit;
    }

    // Check for delete request
    if (isset($_POST['delete_post_id'])) {
        $post_id = $_POST['delete_post_id'];
        $success = deletePost($post_id);
        echo json_encode(['success' => $success]);
        exit();
    } 

    // Handle creating or updating a post
    $text = $_POST['post_text'] ?? '';
    $photos = $_FILES['post_photo'] ?? null;
    $post_id = $_POST['post_id'] ?? null;
    
    // Validate number of images
    if (isset($photos) && is_array($photos['name'])) {
        if (count($photos['name']) > 5) {
            $_SESSION['error'] = "Maximum 5 images allowed per post.";
            header("Location: " . BASE_URL . "user/network.php");
            exit();
        }
    }
    
    savePost($user_id, $text, $photos, $post_id);
    
    // Redirect after successful operation
    header("Location: " . BASE_URL . "user/dashboard.php");
    exit();
}