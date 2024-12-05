<!-- Top Navigation -->
<div class="top-nav">
  <div class="logo">
    <a href="#">
      <img src="layout/image/hisstakpic.png" alt="Company Logo">
    </a>
  </div>
  <div class="logout-btn">
    <a href="admin_profile.php">Profile</a>
  </div>
  <div class="logout-btn">
    <a href="admin_logout.php">Logout</a>
  </div>
</div>
<script>
  // Initialize the sidebar state from local storage
  document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarState = localStorage.getItem("sidebarState");

    if (sidebarState === "open") {
      sidebar.style.left = '0px';
    }
  });

  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.style.left === '0px') {
      sidebar.style.left = '-250px';
      localStorage.setItem("sidebarState", "closed");
    } else {
      sidebar.style.left = '0px';
      localStorage.setItem("sidebarState", "open");
    }
  }

  function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
  }
</script>


<style>
  /* Sidebar Toggle Button */
  .sidebar-toggle {
    position: fixed;
    top: 20px;
    left: 20px;
    background-color: #1B263B;
    color: #E0E0E0;
    border: none;
    font-size: 24px;
    cursor: pointer;
    padding: 10px 15px;
    z-index: 1100;
    transition: background-color 0.3s ease;
    margin-right: 10px;
  }

  .sidebar-toggle:hover {
    background-color: #415A77;
  }

  /* Top Navigation */
  .top-nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background-color: #1B263B;
    color: #E0E0E0;
    padding: 15px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
    z-index: 1000;
    height: 90px; /* Fixed height for the header */

  }

  .top-nav .logo {
    margin-left: 60px;
  }

  .top-nav .logo img {
    height: 45px;
  }

  .top-nav .logout-btn a {
    color: #E0E0E0;
    text-decoration: none;
    font-weight: 500;
    font-size: 18px;
    padding: 5px 15px;
    border-radius: 5px;
    background-color: #415A77;
    transition: background-color 0.3s, color 0.3s;
  }

  .top-nav .logout-btn a:hover {
    background-color: #3DA5D9;
    color: #1B263B;
  }

  /* Sidebar */
  .side-nav {
    position: fixed;
    top: 0;
    left: -250px;
    width: 250px;
    height: 100%;
    background-color: #1f2c46;
    color: #E0E0E0;
    padding: 80px 20px 20px 20px;
    box-shadow: 4px 0 10px rgba(0, 0, 0, 0.5);
    transition: left 0.3s ease;
    z-index: 999;
  }

  .side-nav .user-info {
    text-align: center;
    margin-bottom: 20px;
  }

  .side-nav .user-info img {
    border-radius: 50%;
    height: 80px;
    width: 80px;
  }

  .side-nav .user-info h2 {
    font-size: 18px;
    margin: 10px 0;
  }

  /* Sidebar Links */
  .side-nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .side-nav ul li {
    margin-bottom: 10px;
  }

  .side-nav ul li a {
    color: #E0E0E0;
    text-decoration: none;
    padding: 10px 20px;
    display: block;
    transition: background-color 0.3s, color 0.3s;
    border-radius: 5px;
  }

  .side-nav ul li a:hover {
    background-color: #3A7EBA;
    color: #ffffff;
  }

  /* Dropdown Menu */
  .dropdown {
    display: none;
    padding-left: 15px;
    list-style: none;
    background-color: #25334D;
    border-radius: 5px;
  }

  .dropdown li a {
    padding: 8px 20px;
  }

  .dropdown li a:hover {
    background-color: #415A77;
  }
</style>
