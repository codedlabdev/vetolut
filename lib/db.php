<?php
// lib/db.php

$host = 'localhost'; // Change if needed
$db   = 'vetolut'; // Change to your database name
$user = 'root'; // Default XAMPP username
$pass = 'root'; // Default XAMPP password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}

// Define the getDBConnection function
function getDBConnection() {
    global $pdo; // Access the global $pdo variable
    return $pdo; // Return the PDO instance
}
