<!-- Button to open the sidebar -->
<button class="sidebar-toggle" onclick="toggleSidebar()" style="position: fixed; top: 20px; left: 0px; background-color: #1B263B; color: #E0E0E0; border: none; font-size: 24px; cursor: pointer; padding: 10px 15px; z-index: 1100;">☰</button>

<!-- Top Navigation -->
<div class="top-nav" style="position: fixed; top: 0; left: 0; right: 0; background-color: #1B263B; color: #E0E0E0; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5); z-index: 1000;">
  <div class="logo" style="margin-left: 30px;">
    <a href="invester_h.php">
      <img src="layout/image/hisstakpic.png" alt="Company Logo" style="height: 45px;">
    </a>
  </div>
  <ul class="top-nav-links" style="list-style: none; display: flex; margin: 0; padding: 0;">
    <li style="margin: 0 15px;"><a href="invester_h.php" style="color: #E0E0E0; text-decoration: none; font-weight: 500; padding: 5px 10px; transition: background-color 0.3s, color 0.3s;">Invest</a></li>
    <li style="margin: 0 15px;"><a href="invester_consultant.php" style="color: #E0E0E0; text-decoration: none; font-weight: 500; padding: 5px 10px; transition: background-color 0.3s, color 0.3s;">Ask a Consultant</a></li>
    <li style="margin: 0 15px;"><a href="invester_aboutus.php" style="color: #E0E0E0; text-decoration: none; font-weight: 500; padding: 5px 10px; transition: background-color 0.3s, color 0.3s;">About</a></li>
    <li style="margin: 0 15px;"><a href="invester_contactus.php" style="color: #E0E0E0; text-decoration: none; font-weight: 500; padding: 5px 10px; transition: background-color 0.3s, color 0.3s;">Contact Us</a></li>
  </ul>
  <div class="profile-icon"></div>
</div>

<!-- Sidebar Navigation -->
<div class="side-nav side-nav-active" style="position: fixed; top: 0; left: -250px; width: auto; height: 100%; background-color: #1f2c46; color: #E0E0E0; padding: 80px 20px 20px 20px; box-shadow: 4px 0 10px rgba(0, 0, 0, 0.5); transition: left 0.3s ease; z-index: 999; margin-top: 20px;">
  <div class="user-info" style="margin-bottom: 20px; text-align: center;">
    <img src="..\Grad Project\layout\image\Profile.png" alt="Profile Picture" style="border-radius: 50%; height: 80px; width: 80px;">
    <h2 style="margin: 0; font-size: 18px;"><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
  </div>
  <ul style="list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column;">
    <!--<li><a href="seller.php">Add Property</a></li>-->
    <li style="margin-bottom: 10px;"><a href="investor_investments.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Investments</a></li>
    <li style="margin-bottom: 10px;"><a href="invester_consultant.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Ask a Consultant</a></li>

    <!-- Consultation Requests Dropdown -->
    <li style="margin-bottom: 10px; position: relative;">
      <a href="#" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s; background-color: #1B263B; border-radius: 5px;">
        Consultation Requests <span style="font-size: 14px;">▼</span>
      </a>
      <ul style="list-style: none; margin: 0; padding: 0; display: none; background-color: #1f2c46; width: 100%; z-index: 100;">
        <li style="margin-bottom: 10px;">
          <a href="investers_pending_session.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Pending Sessions</a>
        </li>
        <li style="margin-bottom: 10px;">
          <a href="investers_app_disapp_sessions.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Approved & Disapproved Sessions</a>
        </li>
        <li style="margin-bottom: 10px;">
          <a href="invester_confirmed_sessions.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Confirmed Sessions</a>
        </li>
      </ul>
    </li>
    <li style="margin-bottom: 10px;"><a href="invester_profit.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Profit Return</a></li>
    <li style="margin-bottom: 10px;"><a href="portfolio.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Portfolio</a></li>
    <li style="margin-bottom: 10px;"><a href="logout.php" style="color: #E0E0E0; text-decoration: none; padding: 10px; display: block; transition: background-color 0.3s, color 0.3s;">Logout</a></li>
  </ul>
</div>

<script>
  // JavaScript for handling the dropdown menu
  document.querySelectorAll('.side-nav > ul > li > a').forEach(function(item) {
    item.addEventListener('click', function(e) {
      let subMenu = this.nextElementSibling;
      if (subMenu && subMenu.tagName === 'UL') {
        e.preventDefault();
        subMenu.style.display = subMenu.style.display === 'block' ? 'none' : 'block';
        // Toggle the arrow icon
        let arrow = this.querySelector('span');
        arrow.textContent = subMenu.style.display === 'block' ? '▲' : '▼';
      }
    });
  });
</script>
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