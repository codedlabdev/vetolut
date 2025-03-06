<?php
// lib/user/email_verify.php

// Fetch the verification status from the database
$sql = "SELECT verify FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$verifyStatus = $stmt->fetchColumn();

// Check if the form is submitted for updating email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'email_update') {
    error_log('Form submitted for email update.');

    // Validate and sanitize the email input
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    // Check if the user ID is set and valid
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User ID is not set.']);
        exit;
    }
    $userId = $_SESSION['user_id'];

    // Retrieve the current email from the database
    $sql = "SELECT email FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $currentEmail = $stmt->fetchColumn();

    if (!$currentEmail) {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }

    // Check if the new email is different from the current email
    if ($currentEmail !== $email) {
        // Update email and set verify to 0
        $updateSql = "UPDATE users SET email = :email, verify = 0, updated_at = NOW() WHERE id = :user_id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':email', $email);
        $updateStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            $_SESSION['user_email'] = $email; // Update the session variable
            echo json_encode(['success' => true, 'message' => 'Email updated successfully.', 'new_email' => $email]);
        } else {
            $errorInfo = $updateStmt->errorInfo();
            echo json_encode(['success' => false, 'message' => 'Failed to update email: ' . $errorInfo[2]]);
        }
    } else {
        // Email is the same, no need to update
        echo json_encode(['success' => true, 'message' => 'Email is already up-to-date.', 'new_email' => $email]);
    }
    exit;
}
?>
