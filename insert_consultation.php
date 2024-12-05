<?php
session_start();
include "init.php"; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $investor_id = $_POST['investor_id'];
    $consultant_id = $_POST['consultant_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $description = $_POST['description'];
    
    // Insert the consultation data into the Consultation table
    $stmt_insert = $conn->prepare("INSERT INTO Consultation (investor_ID, consultant_ID, date, time, description, status) 
                                   VALUES (:investor_id, :consultant_id, :date, :time, :description, 'pending')");
    $stmt_insert->bindParam(':investor_id', $investor_id);
    $stmt_insert->bindParam(':consultant_id', $consultant_id);
    $stmt_insert->bindParam(':date', $date);
    $stmt_insert->bindParam(':time', $time);
    $stmt_insert->bindParam(':description', $description);

    try {
        $stmt_insert->execute();
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
