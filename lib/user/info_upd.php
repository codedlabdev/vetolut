<?php
// lib/user/info_upd.php

/// Fetch user data to populate the form (assuming $userId is defined and contains the current user's ID)
$stmt = $pdo->prepare("SELECT profession, about FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $profession = $_POST['profession'];
    $about = $_POST['about'];

    // Update the database
    $sql = "UPDATE users SET profession = :profession, about = :about, updated_at = NOW() WHERE id = :user_id";

    // Prepare and bind parameters
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':profession', $profession);
    $stmt->bindParam(':about', $about);
    $stmt->bindParam(':user_id', $userId);

    // Execute the update
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Information updated successfully.';
        echo json_encode(['success' => true, 'message' => 'Information updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update information.']);
    }
    exit; // Ensure no further output is sent
}

