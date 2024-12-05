<?php
// Start session
session_start();

// Mock session data for testing
$_SESSION['user_id'] = 1; // Example investor ID
$_SESSION['user_type'] = 'investor';

// Include database connection
include 'init.php'; // Make sure this points to your correct DB connection file

// Create mock data for testing
$investor_id = $_SESSION['user_id'];

// Clean up previous test data to avoid conflicts
$conn->exec("DELETE FROM consultation WHERE investor_ID = $investor_id");
$conn->exec("DELETE FROM consultant WHERE consultant_ID > 0");

// Insert mock consultant data
$conn->exec("
    INSERT INTO consultant (consultant_ID, Fname, Lname, fee) 
    VALUES (1, 'John', 'Doe', 150.00), (2, 'Jane', 'Smith', 200.00)
");

// Insert mock consultation data for testing
$conn->exec("
    INSERT INTO consultation (session_number, investor_ID, consultant_ID, date, time, description, status, zoom_link) 
    VALUES 
    (1, $investor_id, 1, '2024-11-20', '10:00:00', 'Session description 1', 'pending', 'https://zoom.link/1'),
    (2, $investor_id, 2, '2024-11-21', '14:00:00', 'Session description 2', 'pending', 'https://zoom.link/2')
");

// Run the main script to verify the output
include 'investers_pending_session.php';
?>
