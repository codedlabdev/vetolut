<?php
define('BASE_DIR', dirname(dirname(__DIR__)) . '/');
require_once BASE_DIR . 'lib/dhu.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['post_id'])) {
    $postId = $_GET['post_id'];
    $pdo = getDBConnection();
    
    // Fetch the current post to get the images
    $stmt = $pdo->prepare("SELECT photo FROM network_post WHERE id = :post_id");
    $stmt->execute(['post_id' => $postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post && $post['photo']) {
        $photos = json_decode($post['photo'], true);
        if ($photos) {
            // Delete all images from filesystem
            foreach ($photos as $photo) {
                $photoPath = BASE_DIR . $photo;
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }
        }

        // Update the database to remove photo references
        $stmt = $pdo->prepare("UPDATE network_post SET photo = NULL WHERE id = :post_id");
        $stmt->execute(['post_id' => $postId]);

        echo json_encode(['success' => true]);
        exit();
    }
}

echo json_encode(['success' => false]);
