<!-- Button to toggle the sidebar -->
<button id="sidebar-toggle" class="sidebar-toggle" onclick="toggleSidebar()" style="position: fixed; top: 20px; left: 10px; background-color: #1B263B; color: #E0E0E0; border: none; font-size: 24px; cursor: pointer; padding: 10px 15px; z-index: 1100;">☰</button>

<!-- Top Navigation -->
<div class="top-nav" style="position: fixed; top: 0; left: 0; right: 0; background-color: #1B263B; color: #E0E0E0; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5); z-index: 1000;">
  <div class="logo" style="margin-left: 30px;">
    <a href="#">
      <img src="layout/image/hisstakpic.png" alt="Company Logo" style="height: 45px;">
    </a>
  </div>
  
  <!-- Centered Navigation Links -->
  <div style="display: flex; flex-grow: 1; justify-content: center;">
    <ul class="top-nav-links" style="list-style: none; display: flex; margin: 0; padding: 0;">
      <li style="margin: 0 15px;"><a href="consultant_aboutus.php" style="color: #E0E0E0; text-decoration: none; font-weight: 500; padding: 5px 10px; transition: background-color 0.3s, color 0.3s;">About</a></li>
      <li style="margin: 0 15px;"><a href="consultant_contactus.php" style="color: #E0E0E0; text-decoration: none; font-weight: 500; padding: 5px 10px; transition: background-color 0.3s, color 0.3s;">Contact Us</a></li>
    </ul>
  </div>

  <div class="profile-icon"></div>
</div>

<!-- Sidebar Navigation -->
<div id="side-nav" class="side-nav" style="position: fixed; top: 0; left: 0; width: auto; height: 100%; background-color: #1f2c46; color: #E0E0E0; padding: 80px 20px 20px 20px; box-shadow: 4px 0 10px rgba(0, 0, 0, 0.5); transition: left 0.3s ease; z-index: 999;">
  <div class="user-info" style="margin-bottom: 20px; text-align: center;">
    <img src="..\Grad Project\layout\image\Profile.png" alt="Profile Picture" style="border-radius: 50%; height: 80px; width: 80px;">
    <h2 style="margin: 0; font-size: 18px;"><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
  </div>
  <ul style="list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column;">
    <li style="margin-bottom: 10px;"><a href="Consulation_requestes.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Consultation Requests</a></li>
    <li style="margin-bottom: 10px;"><a href="Reports.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Reports</a></li>
    <li style="margin-bottom: 10px;"><a href="logout.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Logout</a></li>
  </ul>
</div>

<!-- Content Area -->
<div id="main-content" style="margin-left: 200px; padding: 20px;">
  <!-- Page content goes here -->
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const sideNav = document.getElementById('side-nav');
    const mainContent = document.getElementById('main-content');
    const sidebarOpen = localStorage.getItem('sidebarOpen');

    if (sidebarOpen === 'false') {
      sideNav.style.left = '-200px';
      mainContent.style.marginLeft = '0';
    }
  });

  function toggleSidebar() {
    const sideNav = document.getElementById('side-nav');
    const mainContent = document.getElementById('main-content');
    const sidebarOpen = sideNav.style.left === '0px';

    if (sidebarOpen) {
      sideNav.style.left = '-200px';
      mainContent.style.marginLeft = '0';
      localStorage.setItem('sidebarOpen', 'false');
    } else {
      sideNav.style.left = '0';
      mainContent.style.marginLeft = '200px';
      localStorage.setItem('sidebarOpen', 'true');
    }
  }
</script>
