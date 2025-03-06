<?php

// lib/url.php

// Fetch the base URL from the database
$stmt = $pdo->prepare("SELECT value FROM app_config WHERE key_name = :key_name");
$stmt->execute(['key_name' => 'base_url']);
$base_url = $stmt->fetchColumn();

// Define BASE_URL constant here, before using it
define('BASE_URL', rtrim($base_url, '/') . '/');


?>
