<?php
//lib/user/table


$userId = $_SESSION['user_id'];

// Get user data from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle user image
$imageData = '';
if (!empty($user['image'])) {
    if (strpos($user['image'], '/') !== false) {
        // It's a file path
        $imageData = BASE_URL . $user['image'];
    } else {
        // It's a BLOB, use default image
        $imageData = BASE_URL . 'assets/user/img/noimage.png';
    }
} else {
    // If there's no image, use default placeholder
    $imageData = BASE_URL . 'assets/user/img/noimage.png';
}

// Also update the image handling in the foreach loop
$data = [];
foreach ($users as $user) {
    if (!empty($user['image'])) {
        if (strpos($user['image'], '/') !== false) {
            $imageData = BASE_URL . $user['image'];
        } else {
            $imageData = BASE_URL . 'assets/user/img/noimage.png';
        }
    } else {
        $imageData = BASE_URL . 'assets/user/img/noimage.png';
    }
    
    $data[] = [
        'f_name' => $user['f_name'],
        'l_name' => $user['l_name'],
        'profession' => $user['profession'],
        'image' => $imageData,
    ];
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);


?>
