<?php 
session_start();
$noNavbar = true;
include "init.php";
include "invester_sidebar.php";
$pageTitle = "Hissatk";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="layout/css/H.css">
</head>
<body>
    <!-- Header Slideshow -->
    <header class="slideshow-container">
        <div class="slide fade">
            <div class="intro-text">
                <h2 class="attention-grabber">Want to invest but think you don't have enough capital?</h2>
            </div>
        </div>
        
        <div class="slide fade">    
            <div class="intro-text">
                <h2>Not sure where to start your investment journey?</h2>
            </div>
        </div>

        <div class="slide fade">
            <div class="intro-text">
                <h2>Looking for expert advice to guide your investment?</h2>
            </div>
        </div>

        <div class="slide fade">
            <div class="intro-text">
                <h2>Looking for investors for your property?</h2>
            </div>
        </div>

        <div class="slide fade">
            <div class="intro-text">
                <h1><span class="highlight">Hissatk</span> makes investment accessible to everyone <br>and is willing to help you.</h1>
                <a href="register.php" class="cta-button">Get Started Today</a>
            </div>
        </div>
    </header>

    <main>
        <div class="content-card">
            <h2>Apartments</h2>
            <img src="layout/image/A1.jpeg" alt="Apartment in Al-Khobar" class="property-image">
        
            <div class="info">
                <!-- Add any additional information here -->
            </div>
            <div class="button-group">
                <a href="invester_apartments.php"><button>Invest Now</button></a>
                <a href="invester_consultant.php"><button>Ask a Consultant</button></a>
            </div>
        </div>

        <div class="content-card">
            <h2>Skyscrapers</h2>
            <img src="layout/image/S2.jpeg" alt="Skyscraper in Al-Hofouf" class="property-image">
          
            <div class="info">
                <!-- Add any additional information here -->
            </div>
            <div class="button-group">
                <a href="invester_skycraper.php"><button>Invest Now</button></a>
                <a href="invester_consultant.php"><button>Ask a Consultant</button></a>
            </div>
        </div>

        <div class="content-card">
            <h2>Beach Villas</h2>
            <img src="layout/image/S1.jpeg" alt="Beach Villa in Dammam" class="property-image">
          
            <div class="info">
                <!-- Add any additional information here -->
            </div>
            <div class="button-group">
                <a href="invester_beach_villas.php"><button>Invest Now</button></a>
                <a href="invester_consultant.php"><button>Ask a Consultant</button></a>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Hissatk. All rights reserved.</p>
    </footer>

    <script src="layout/js/slideshow.js"></script>
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
