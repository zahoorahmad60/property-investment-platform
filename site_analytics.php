<?php
// site_analytics.php
session_start();
if ($_SESSION['user_type'] != 'admin') {
    header('Location: login.php');
    exit();
}

require 'db_connection.php';

// Query to get analytics data
$total_users_query = "SELECT COUNT(*) as total_users FROM `users info`";
$total_properties_query = "SELECT COUNT(*) as total_properties FROM properties";

$total_users_result = $conn->query($total_users_query)->fetch_assoc();
$total_properties_result = $conn->query($total_properties_query)->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Analytics</title>
    <link rel="stylesheet" href="admin_content.css">
</head>

<body>

   <!-- Button to open the sidebar -->
   <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

   <!-- Top Navigation -->
   <nav class="top-nav">
       <div class="logo">
           <a href="home.php">
               <img src="hisstakpic.png" alt="Company Logo">
           </a>
       </div>
       <ul class="top-nav-links">
           <li><a href="invest.php">Invest</a></li>
           <li><a href="consultant.php">Ask a Consultant</a></li>
           <li><a href="aboutus.php">About</a></li>
           <li><a href="contactus.php">Contact Us</a></li>
       </ul>
       <div class="profile-icon">
           <a href="admin_dashboard.php">
               <img src="Profile.png" alt="Profile Icon">
           </a>
       </div>
   </nav>

   <!-- Left Sidebar -->
   <nav class="side-nav" id="sidebar">
       <div class="user-info">
           <img src="admin_profile.png" alt="Admin Profile Picture"> <!-- Replace with actual profile image path -->
           <h2><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
           <p>Administrator</p>
       </div>
       <ul>
           <li><a href="manage_users.php">User Management</a></li>
           <li><a href="manage_properties.php">Property Management</a></li>
           <li><a href="site_analytics.php">Site Analytics</a></li>
           <li><a href="logout.php">Logout</a></li>
       </ul>
   </nav>

   <!-- Main Content -->
   <div class="container">
       <h1>Site Analytics</h1>
       <p>Total Users: <?= $total_users_result['total_users']; ?></p>
       <p>Total Properties Listed: <?= $total_properties_result['total_properties']; ?></p>
       <!-- You can add charts here using libraries like Chart.js -->
   </div>

   <!-- Sidebar Toggle Script -->
   <script>
       function toggleSidebar() {
           var sidebar = document.getElementById('sidebar');
           var container = document.querySelector('.container');
           
           // Toggle the active class on the sidebar
           sidebar.classList.toggle('side-nav-active');
           
           // Adjust the main container when the sidebar is toggled
           if (container) {
               container.classList.toggle('side-nav-active');
           }
       }
   </script>

</body>
</html>
