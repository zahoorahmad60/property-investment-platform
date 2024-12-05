<?php
// Start the session
session_start();

// Include your initialization file (e.g., database connection)
include 'init.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Redirect to admin login page if the user is not an admin
    header("Location: admin_login.php");
    exit();
}

// Check if 'type' and 'id' are provided in the URL
if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = intval($_GET['id']);
    
    // Validate user type
    $allowed_types = ['seller', 'investor', 'consultant'];
    if (!in_array($type, $allowed_types)) {
        $_SESSION['error_message'] = "Invalid user type.";
        header("Location: admin_dashboard.php");
        exit();
    }
    
    // Map user type to table and ID column
    $user_map = [
        'seller' => ['table' => 'Seller', 'id_column' => 'seller_ID'],
        'investor' => ['table' => 'Investor', 'id_column' => 'investor_ID'],
        'consultant' => ['table' => 'Consultant', 'id_column' => 'consultant_ID'],
    ];
    
    $table = $user_map[$type]['table'];
    $id_column = $user_map[$type]['id_column'];
    
    // Fetch the user's email and first name for notifications
    $stmt_fetch = $conn->prepare("SELECT Fname, email FROM $table WHERE $id_column = ?");
    $stmt_fetch->execute([$id]);
    $user = $stmt_fetch->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $_SESSION['error_message'] = "User not found.";
        header("Location: admin_dashboard.php");
        exit();
    }
    
    // Update the user's status to approved (1)
    $stmt_update = $conn->prepare("UPDATE $table SET approve = 1 WHERE $id_column = ?");
    $stmt_update->execute([$id]);
    
    // Optionally, send an email notification to the user
    /*
    $to = $user['email'];
    $subject = "Your Account Has Been Approved";
    $message = "Hello " . htmlspecialchars($user['Fname']) . ",\n\nYour account has been approved. You can now log in to the platform.\n\nBest Regards,\nAdmin Team";
    $headers = "From: no-reply@yourdomain.com";
    mail($to, $subject, $message, $headers);
    */
    
    // Optionally, log the approval action
    /*
    $log_stmt = $conn->prepare("INSERT INTO logs (admin_username, action, user_type, user_id) VALUES (?, ?, ?, ?)");
    $log_stmt->execute([$_SESSION['username'], 'approve', ucfirst($type), $id]);
    */
    
    // Set a success message and redirect back to the admin dashboard
    $_SESSION['success_message'] = ucfirst($type) . " approved successfully.";
    header("Location: admin_dashboard.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request. User type and ID are required.";
    header("Location: admin_dashboard.php");
    exit();
}
?>
