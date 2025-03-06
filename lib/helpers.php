<?php

// lib/helpers.php

require_once 'db.php'; // Include the database connection file


function getBaseUrl() {
    $db = getDBConnection(); // Assuming you have a function to get the DB connection

    $sql = "SELECT value FROM app_config WHERE key_name = 'base_url'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    // Fetch the value
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['value'] : ''; // Return the base URL or an empty string if not found
}


?>
