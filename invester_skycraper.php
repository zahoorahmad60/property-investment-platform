<?php
session_start();
$noNavbar = true;
include "init.php"; // Database connection
include "invester_sidebar.php";
$pageTitle = "Invest in Skyscrapers";

// Fetch skyscrapers from the database
try {
    $stmt = $conn->prepare("SELECT * FROM Property WHERE type = 'Skyscrapers'");
    $stmt->execute();
    $skyscrapers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="layout/css/H.css">
    <style>
        main {
            padding: 20px;
            text-align: center;
        }

        main h1 {
            margin-bottom: 30px;
            color: #333;
        }

        .property-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 20px;
        }

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
            background-color: #0069d9;
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
    <script>
        function handleInvestClick(propertyId) {
            <?php if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'investor'): ?>
                alert('Please register as an investor to make investments.');
                window.location.href = 'login.php';
            <?php else: ?>
                window.location.href = 'invest_property.php?type=skyscraper&id=' + propertyId;
            <?php endif; ?>
        }
    </script>
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
    <h1>Skyscrapers Available for Investment</h1>
    <div class="property-container">
        <?php if (!empty($skyscrapers)): ?>
            <?php foreach ($skyscrapers as $skyscraper): ?>
                <?php
                // Fetch the number of investors for this skyscraper
                $stmt_investors = $conn->prepare("SELECT COUNT(DISTINCT investor_ID) as num_investors FROM Investment_Portfolio WHERE property_ID = :property_ID");
                $stmt_investors->bindParam(':property_ID', $skyscraper['property_ID'], PDO::PARAM_INT);
                $stmt_investors->execute();
                $investors = $stmt_investors->fetch(PDO::FETCH_ASSOC);
                $num_investors = $investors['num_investors'] ?? 0;

                // Fetch total amount invested in this skyscraper
                $stmt_investment = $conn->prepare("SELECT SUM(amount_paid) as total_invested FROM Investment_Portfolio WHERE property_ID = :property_ID");
                $stmt_investment->bindParam(':property_ID', $skyscraper['property_ID'], PDO::PARAM_INT);
                $stmt_investment->execute();
                $investment = $stmt_investment->fetch(PDO::FETCH_ASSOC);
                $total_invested = $investment['total_invested'] ?? 0;

                // Calculate the percentage funded
                $percentage_funded = ($total_invested / $skyscraper['cost_of_property']) * 100;
                ?>

                <div class="property-card" style="position: relative;">
                    <img src="<?php echo str_replace('\\', '/', 'seller/' . htmlspecialchars($skyscraper['image_path'])); ?>" alt="<?php echo htmlspecialchars($skyscraper['name']); ?>">
                    <div class="investment">
                        <p class="total-invested"><strong>Investors:</strong> <?php echo htmlspecialchars($num_investors); ?></p>
                        <p class="funding-percentage"><strong>Funded:</strong> <?php echo number_format($percentage_funded, 2); ?>%</p>
            </div>
                    <div class="property-info">
                        <h2> <a href="invester_property_details.php?property_id=<?php echo htmlspecialchars($skyscraper['property_ID']); ?>" class="btn">
                                    <?php echo htmlspecialchars($skyscraper['name']); ?>
                                </a></h2>
                        <p><strong>Price:</strong> $<?php echo number_format($skyscraper['cost_of_property'], 2); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($skyscraper['city']); ?></p>
                        <p><?php echo htmlspecialchars($skyscraper['description']); ?></p>
                         </div>

                    <div class="button-group">
                        <?php if ($percentage_funded < 100): ?>
                            <?php if (isset($_SESSION['username']) && $_SESSION['user_type'] === 'investor'): ?>
                                <button class="invest-button" onclick="location.href='invester_invest_property.php?property_id=<?php echo $skyscraper['property_ID']; ?>'">Invest Now</button>
                                <button class="consultant-button" onclick="location.href='invester_consultant.php'">Ask a Consultant</button>
                                <?php else: ?>
                                <button class="invest-button" onclick="showLoginMessage()">Invest Now</button>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="funded-stamp">Fully Funded</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No skyscrapers available for investment at the moment.</p>
        <?php endif; ?>
    </div>
</main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Hissatk. All rights reserved.</p>
    </footer>

    <script src="layout/js/slideshow.js"></script>
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
