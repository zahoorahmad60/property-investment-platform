<?php
// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session (only if not started yet)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "Admin Login";
include "init.php"; // Ensure this file correctly sets up the database connection

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
                header("Location: user_management.php");
                exit(); // Ensure the script stops after the redirect
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
    <title> Invester Dashboard</title>
    <link rel="stylesheet" href="investor_welcome.css">
    <script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.side-nav');
        if (sidebar.style.left === '0px') {
            sidebar.style.left = '-250px';
        } else {
            sidebar.style.left = '0px';
        }
    }
    </script>
</head>

<body>
<?php
// Ensure the session is started and check for login status
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$profile_link = 'login.php'; // Default to login page

if (isset($_SESSION['username']) && isset($_SESSION['user_type'])) {
    // Redirect based on user type
    switch ($_SESSION['user_type']) {
        case 'admin':
            $profile_link = 'admin_dashboard.php';
            break;
        case 'investor':
            $profile_link = 'investor_welcome.php';
            break;
        case 'consultant':
            $profile_link = 'consultant_welcome.php';
            break;
        case 'seller':
            $profile_link = 'seller_welcome.php';
            break;
    }
}
?>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>As an Admin, you can manage different users 1. Sellers  2. Investos   3. Consultants .</p>
    </div>
    <?php include 'admin_subsidebar_user.php'; ?>
    <script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.side-nav');
        if (sidebar.style.left === '0px') {
            sidebar.style.left = '-250px';
        } else {
            sidebar.style.left = '0px';
        }
    }
    </script>
</body>
</html>
