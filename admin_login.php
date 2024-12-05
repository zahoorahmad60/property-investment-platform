<?php
// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session (only if not started yet)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "Admin Login";
$noNavbar = true;
include "init.php"; // Ensure this file correctly sets up the database connection
include "admin_sidebar_login.php";

$error_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password']; // Do not use htmlspecialchars here, as it may interfere with password hashing

    // Prepare the SQL statement for admin validation from the Admin table
    $stmt = $conn->prepare("SELECT admin_ID, username, password, approve FROM Admin WHERE username = :username");

    // Bind the parameters and execute the statement
    $stmt->execute(array(":username" => $username));

    $row = $stmt->fetch();
    $count = $stmt->rowCount();

    // Check if the admin exists
    if ($count > 0) {
        // Check if the account is approved by admin
        if ($row['approve'] == 1) {
            // Verify the password against the hash stored in the database
            if (password_verify($password, $row['password'])) {
                // Store admin information in session
                $_SESSION['username'] = $username;
                $_SESSION['admin_id'] = $row['admin_ID']; // Set the admin ID in session
                $_SESSION['is_admin'] = true; // Admin flag

                // Redirect to the admin dashboard
                echo "<script>
                window.location.href = 'admin_dashboard.php';
              </script>";                exit(); // Ensure the script stops after the redirect
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "Your account is pending approval.";
        }
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="log.css">
</head>
<body>
    <!-- Login Form -->
    <div class="container">
        <div class="form-group">
            <!-- Display error message if login fails -->
            <?php if (!empty($error_message)): ?>
                <div class='alert alert-danger'><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form action="admin_login.php" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your admin username" required autofocus><br><br>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required><br><br>

                <input type="submit" value="Login">
            </form>
        </div>
    </div>
</body>
</html>
