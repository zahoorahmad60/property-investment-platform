<?php
session_start();
include "init.php"; // Database connection
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
            background-color: #0069d9;
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
        // Function to handle Invest Now button click
        function handleInvestClick(propertyId) {
            // Check if user is logged in and is an investor
            <?php if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'investor'): ?>
                // Redirect to login page with a message
                alert('Please register as an investor to make investments.');
                window.location.href = 'login.php';
            <?php else: ?>
                // Redirect to invest property page
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
                    <div class="property-card">
                        <!-- Display the property image. Ensure backslashes are replaced with forward slashes -->
                        <img src="<?php echo str_replace('\\', '/', 'seller/' . htmlspecialchars($skyscraper['image_path'])); ?>" alt="<?php echo htmlspecialchars($skyscraper['name']); ?>">

                        <div class="property-info">
                            <h2><?php echo htmlspecialchars($skyscraper['name']); ?></h2>
                            <p><strong>Price:</strong> $<?php echo number_format($skyscraper['cost_of_property'], 2); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($skyscraper['city']); ?></p>
                            <p><?php echo htmlspecialchars($skyscraper['description']); ?></p>
                        </div>
                        <div class="button-group">
                            <button class="invest-button" onclick="handleInvestClick('<?php echo $skyscraper['property_ID']; ?>')">Invest Now</button>
                            <button class="consultant-button" onclick="location.href='consultant.php'">Ask a Consultant</button>
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
</body>
</html>
