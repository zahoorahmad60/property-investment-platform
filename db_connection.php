<?php

// Database connection parameters
$host = 'localhost';  // Host name
$dbname = 'realestatedb1';  // Database name
$user = 'root';  // Username
$pass = '123456';  // Password

// Create a Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

// Options for the PDO connection
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Set default fetch mode to associative array
    PDO::ATTR_PERSISTENT => true, // Enable persistent connections
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4' // Ensure proper UTF-8 encoding
];

try {
    // Establish the connection
    $conn = new PDO($dsn, $user, $pass, $options);
   // echo "Connected successfully!";  // Optionally output success message
} 
catch (PDOException $e) {
    // Handle connection error
    echo "Failed to connect: " . $e->getMessage();
    exit();  // Stop script execution in case of failure
}
?>
