<?php
//lib/user/p_func

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add cache control headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_DIR', BASE_DIR . 'public/uploads/users/profile_img/');

// Create upload directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = null;
    $updateImage = false;

    // Check for file upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $updateImage = true;
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

        if (in_array($_FILES['profile_image']['type'], $allowedTypes)) {
            if ($_FILES['profile_image']['size'] <= MAX_FILE_SIZE) {
                // Get current user's image
                $stmt = $pdo->prepare("SELECT image FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $currentImage = $stmt->fetchColumn();

                // Generate filename using user ID
                $fileExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $fileName = 'profile_' . $userId . '.' . $fileExtension;
                $targetPath = UPLOAD_DIR . $fileName;
                $dbImagePath = 'uploads/users/profile_img/' . $fileName;

                // Delete old image if exists
                if ($currentImage && file_exists(BASE_DIR . 'public/' . $currentImage)) {
                    unlink(BASE_DIR . 'public/' . $currentImage);
                }

                // Upload new image
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                    chmod($targetPath, 0644);
                    // Add timestamp to image path to prevent caching
                    $imagePath = $dbImagePath . '?v=' . time();
                } else {
                    $_SESSION['error'] = 'Failed to upload file.';
                    header('Location: profile.php');
                    exit;
                }
            } else {
                $_SESSION['error'] = 'File size exceeds the maximum limit of 5MB.';
                header('Location: profile.php');
                exit;
            }
        } else {
            $_SESSION['error'] = 'Invalid file type. Only JPG and PNG are allowed.';
            header('Location: profile.php');
            exit;
        }
    }

    // Collect form data
    $f_name = $_POST['f_name'] ?? '';
    $l_name = $_POST['l_name'] ?? '';
    $country = $_POST['country'] ?? '';
    $gender = $_POST['gender'] ?? '';

    // Build SQL query based on whether image is being updated
    if ($updateImage) {
        $sql = "UPDATE users SET 
                f_name = :f_name, 
                l_name = :l_name, 
                country = :country, 
                gender = :gender,
                image = :image,
                updated_at = NOW()
                WHERE id = :user_id";
        $params = [
            ':f_name' => $f_name,
            ':l_name' => $l_name,
            ':country' => $country,
            ':gender' => $gender,
            ':image' => $imagePath,
            ':user_id' => $userId
        ];
    } else {
        $sql = "UPDATE users SET 
                f_name = :f_name, 
                l_name = :l_name, 
                country = :country, 
                gender = :gender,
                updated_at = NOW()
                WHERE id = :user_id";
        $params = [
            ':f_name' => $f_name,
            ':l_name' => $l_name,
            ':country' => $country,
            ':gender' => $gender,
            ':user_id' => $userId
        ];
    }

    try {
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            $_SESSION['success'] = true;
            $_SESSION['message'] = 'Profile updated successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            throw new Exception('Database update failed');
        }
    } catch (Exception $e) {
        $_SESSION['success'] = false;
        $_SESSION['message'] = 'Failed to update profile: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }

    // Redirect
    if (!headers_sent()) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}
