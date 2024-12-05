<?php
// Start the session (only if not started yet)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: admin_login.php");
exit(); // Ensure the script stops after the redirect
?>
