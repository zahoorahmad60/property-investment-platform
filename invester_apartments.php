<?php
session_start();
$noNavbar = true;
include "init.php"; // Database connection
include "invester_sidebar.php";
$pageTitle = "Invest in Apartments";

// Fetch apartments from the database
$stmt = $conn->prepare("SELECT * FROM Property WHERE type = 'Apartments'");
$stmt->execute();
$apartments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="layout/css/H.css">
    <style>
        .funded-stamp {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: red;
    color: white;
    padding: 5px;
    font-size: 14px;
    font-weight: bold;
}
        /* Main Content */
        main {
            padding: 20px;
            text-align: center;
        }

        main h1 {
            margin-bottom: 30px;
            color: #333;
        }

        /* Property Container */
        .property-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 20px;
        }

        /* Property Card */
        .property-card {
            background-color: #415A77;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 300px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }

        .property-card:hover {
            transform: scale(1.02);
        }

        .property-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .property-info {
            padding: 15px;
            flex-grow: 1;
            text-align: left;
        }

        .property-info h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: white;
        }

        .property-info p {
            margin: 5px 0;
            color: white;
        }

        /* Button Group */
        .button-group {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-top: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .button-group button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        .button-group .invest-button {
            background-color: #28a745;
            color: #fff;
        }

        .button-group .invest-button:hover {
            background-color: #218838;
        }

        .button-group .consultant-button {
            background-color: #007bff;
            color: #fff;
        }

        .button-group .consultant-button:hover {
            background-color: #415A77;
        }

        /* Slideshow container */
        .slideshow-container {
            position: relative;
            max-width: 100%;
            margin: auto;
        }

        /* Hide the images by default */
        .slide {
            display: none;
        }

        /* Fading animation */
        .fade {
            -webkit-animation-name: fade;
            -webkit-animation-duration: 1.5s;
            animation-name: fade;
            animation-duration: 1.5s;
        }

        @-webkit-keyframes fade {
            from {opacity: .4} 
            to {opacity: 1}
        }

        @keyframes fade {
            from {opacity: .4} 
            to {opacity: 1}
        }
.investment{
    display:flex;
}
        .total-invested {
    color: #2E8B57; /* Dark green */
    background-color: #F0FFF0; /* Light green background */
    padding: 8px;
    border-radius: 4px;
    margin: 10px;
}

.funding-percentage {
    color: #4682B4; /* Steel blue */
    background-color: #F0F8FF; /* Alice blue background */
    padding: 8px;
    border-radius: 4px;
    margin:10px;
}

        /* Responsive Design */
        @media (max-width: 768px) {
            .property-container {
                flex-direction: column;
                align-items: center;
            }

            .property-card {
                width: 90%;
            }

            nav ul {
                flex-direction: column;
            }

            nav ul li ul {
                position: static;
            }
        }
    </style>
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
                <h1><span class="highlight">Hissatk</span> makes investment accessible to everyone <br>and is willing to help you.</h1>
                <a href="register.php" class="cta-button">Get Started Today</a>
            </div>
        </div>
    </header>
    <main>
    <h1>Apartments Available for Investment</h1>
    <div class="property-container">
        <?php if (count($apartments) > 0): ?>
            <?php foreach ($apartments as $apartment): ?>
                <?php
                // Fetch the number of investors for this apartment
                $stmt_investors = $conn->prepare("SELECT COUNT(DISTINCT investor_ID) as num_investors FROM Investment_Portfolio WHERE property_ID = :property_ID");
                $stmt_investors->bindParam(':property_ID', $apartment['property_ID'], PDO::PARAM_INT);
                $stmt_investors->execute();
                $investors = $stmt_investors->fetch(PDO::FETCH_ASSOC);
                $num_investors = $investors['num_investors'] ?? 0;

                // Fetch total amount invested in this apartment
                $stmt_investment = $conn->prepare("SELECT SUM(amount_paid) as total_invested FROM Investment_Portfolio WHERE property_ID = :property_ID");
                $stmt_investment->bindParam(':property_ID', $apartment['property_ID'], PDO::PARAM_INT);
                $stmt_investment->execute();
                $investment = $stmt_investment->fetch(PDO::FETCH_ASSOC);
                $total_invested = $investment['total_invested'] ?? 0;

                // Calculate the percentage funded
                $percentage_funded = ($total_invested / $apartment['cost_of_property']) * 100;
                ?>

                <div class="property-card" style="position: relative;">
                    <!-- Display the property image with the 'seller/' prefix -->
                    <img src="<?php echo 'seller/' . str_replace('\\', '/', htmlspecialchars($apartment['image_path'])); ?>" alt="<?php echo htmlspecialchars($apartment['name']); ?>">
                    <div class="investment">
                        <p class="total-invested"><strong>Investors:</strong> <?php echo htmlspecialchars($num_investors); ?></p>
                        <p class="funding-percentage"><strong>Funded:</strong> <?php echo number_format($percentage_funded, 2); ?>%</p>
            </div>
                    <div class="property-info">
                        <h2> <a href="invester_property_details.php?property_id=<?php echo htmlspecialchars($apartment['property_ID']); ?>" class="btn">
                                    <?php echo htmlspecialchars($apartment['name']); ?>
                                </a></h2>
                        <p><strong>Price:</strong> $<?php echo number_format($apartment['cost_of_property'], 2); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($apartment['city']); ?></p>
                        <p><?php echo htmlspecialchars($apartment['description']); ?></p>
                       
                    </div>
                    <div class="button-group">
                        <?php if ($percentage_funded < 100): ?>
                            <?php if (isset($_SESSION['username']) && $_SESSION['user_type'] === 'investor'): ?>
                                <!-- Redirect to investment page if logged in as investor -->
                                
                                <button class="invest-button" onclick="location.href='invester_invest_property.php?property_id=<?php echo $apartment['property_ID']; ?>'">Invest Now</button>
                                <button class="consultant-button" onclick="location.href='invester_consultant.php'">Ask a Consultant</button>

                                <?php else: ?>
                                <!-- Redirect to login page with a message if not logged in or not an investor -->
                                <button class="invest-button" onclick="showLoginMessage()">Invest Now</button>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="funded-stamp">Fully Funded</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No apartments available for investment at the moment.</p>
        <?php endif; ?>
    </div>
</main>


    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Hissatk. All rights reserved.</p>
    </footer>

    <script>
        function showLoginMessage() {
            alert("Please register as an investor to make investments.");
            window.location.href = 'login.php?message=Please register as an investor to make investments.';
        }

        // JavaScript for slideshow
        let slideIndex = 0;
        showSlides();

        function showSlides() {
            let slides = document.getElementsByClassName("slide");
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slideIndex++;
            if (slideIndex > slides.length) {slideIndex = 1}
            slides[slideIndex - 1].style.display = "block";
            setTimeout(showSlides, 3000); // Change image every 3 seconds
        }
    </script>
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
