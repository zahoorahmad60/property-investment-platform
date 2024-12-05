<?php
// Start the session
session_start();

// Check if the user is logged in and is an investor
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'investor') {
    // Redirect to login page if the user is not a investor
    exit();
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
    let sidebarOpen = true; // Set to true initially so the sidebar is open by default

    function toggleSidebar() {
        const sidebar = document.querySelector('.side-nav');
        if (sidebarOpen) {
            sidebar.style.left = '-250px';
            sidebarOpen = false;
        } else {
            sidebar.style.left = '0px';
            sidebarOpen = true;
        }
    }

    // Set the sidebar open on page load
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.querySelector('.side-nav');
        sidebar.style.left = '0px'; // Ensure sidebar is visible on load
    });
</script>
<style>
    .container {
    margin-left: 500px; /* Increased left margin for more space */
    padding: 40px;
    width: calc(100% - 300px); /* Adjust width based on new left margin */
    text-align: center;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    margin-top: 200px; /* Increased top margin for more space */
}
</style>
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
        <p>As an investor you can invest in different properties, and book a session with our real estate consultants.</p>
    </div>
    <?php include 'invester_sidebar.php'; ?>
</body>
</html>
