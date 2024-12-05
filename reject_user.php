<?php
include 'init.php'; // Ensure this sets up the database connection

// Check if type and id are provided in the URL
if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = intval($_GET['id']);
    
    // Validate user type
    $allowed_types = ['seller', 'investor', 'consultant'];
    if (!in_array($type, $allowed_types)) {
        echo "Invalid user type.";
        exit();
    }
    
    // Map user type to table and ID column
    $user_map = [
        'seller' => ['table' => 'seller', 'id_column' => 'seller_ID'],
        'investor' => ['table' => 'investor', 'id_column' => 'investor_ID'],
        'consultant' => ['table' => 'consultant', 'id_column' => 'consultant_ID'],
    ];
    
    $table = $user_map[$type]['table'];
    $id_column = $user_map[$type]['id_column'];

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rejection_note'])) {
        $rejection_note = $_POST['rejection_note'];

        // Prepare the update statement
        $update_query = "UPDATE `$table` SET `status` = 1, `rejection_reason` = :rejection_reason WHERE `$id_column` = :id";
        
        $stmt = $conn->prepare($update_query);
        $stmt->bindParam(':rejection_reason', $rejection_note);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo "<script>alert('User rejected successfully.'); window.location.href = 'admin_dashboard.php';</script>";
        } else {
            echo "Error updating record: " . $stmt->errorInfo()[2];
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reject User</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #0D1B2A;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #263238;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            color: white;
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #fff;
        }
        p {
            font-size: 16px;
            margin-bottom: 20px;
            color: #ddd;
        }
        .reject-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .reject-btn:hover {
            background-color: #c0392b;
        }
        .hidden-input {
            display: none;
        }
    </style>
   <script>
    function confirmRejection() {
        var rejectionNote = prompt("Please provide a reason for rejecting this user:");
        if (rejectionNote && rejectionNote.trim() !== "") {
            document.getElementById("rejection_note").value = rejectionNote;
            document.getElementById("rejectForm").submit();
        } else {
            alert("Rejection reason is required.");
        }
    }
</script>
</head>
<body>

<div class="container">
    <h1>Reject User</h1>
    <p>You are about to reject this user. Please provide a reason.</p>

    <form id="rejectForm" method="POST">
        <!-- Hidden field to store the rejection note -->
        <input type="hidden" name="rejection_note" id="rejection_note" class="hidden-input">
        <button type="button" class="reject-btn" onclick="confirmRejection()">Reject User</button>
    </form>
</div>
</body>
</html>
