<!-- Button to open the sidebar-->
<button class="sidebar-toggle" onclick="toggleSidebar()" style="position: fixed; top: 20px; left: 10px; background-color: #1B263B; color: #E0E0E0; border: none; font-size: 24px; cursor: pointer; padding: 10px 15px; z-index: 1100;">☰</button>

<!-- Top Navigation -->
<div class="top-nav" style="position: fixed; top: 0; left: 0; right: 0; background-color: #1B263B; color: #E0E0E0; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5); z-index: 1000;">
  <!-- Logo Section -->
  <div class="logo" style="margin-left: 30px;">
    <a href="h.php">
      <img src="layout/image/hisstakpic.png" alt="Company Logo" style="height: 45px;">
    </a>
  </div>
  
  <!-- Logout Button -->
  <div class="logout-btn" style="margin-right: 30px;">
    <a href="logout.php" style="color: #E0E0E0; text-decoration: none; font-weight: 500; font-size: 18px; padding: 5px 15px; border-radius: 5px; background-color: #415A77; transition: background-color 0.3s, color 0.3s;">
      Logout
    </a>
  </div>
</div>

<!-- Sidebar Navigation -->
<div class="side-nav side-nav-active" style="position: fixed; top: 0; left: -250px; width: 200px; height: 100%; background-color: #1f2c46; color: #E0E0E0; padding: 80px 20px 20px 20px; box-shadow: 4px 0 10px rgba(0, 0, 0, 0.5); transition: left 0.3s ease; z-index: 999; margin-top: 20px;">
  <div class="user-info" style="margin-bottom: 20px; text-align: center;">
    <img src="..\Grad Project\layout\image\Profile.png" alt="Profile Picture" style="border-radius: 50%; height: 80px; width: 80px;">
    <h2 style="margin: 0; font-size: 18px;"><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
  </div>
  <ul style="list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column;">
    <!--<li><a href="seller.php">Add Property</a></li>-->
    <li style="margin-bottom: 10px;"><a href="admin_apartments.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Apartments</a></li>
    <li style="margin-bottom: 10px;"><a href="admin_skyscraper.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Skyscrapers</a></li>
    <li style="margin-bottom: 10px;"><a href="admin_beach_villas.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Beach villas</a></li>
    <li style="margin-bottom: 10px;"><a href="admin_dashboard.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Admin Dashboard</a></li>
    <li style="margin-bottom: 10px;"><a href="logout.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Log out</a></li>
  </ul>
</div>
