<?php

session_start();
$noNavbar = true;
$pageTitle = "Consultant Profiles";
include "invester_sidebar.php";

// Check if the user is logged in and if they are an "investor"
if (!isset($_SESSION['username']) || !in_array($_SESSION['user_type'], ['investor'])) {
    // Display an alert message and redirect unauthorized users
    echo "<script>alert('Access Denied: Only investors can consult with experts.'); window.location.href = 'login.php';</script>";
    exit();
}

// Include the database connection file
include "init.php"; // Ensure this file sets up your $conn (database connection)

// Fetch approved consultant data from the database
try {
    $stmt = $conn->prepare("
        SELECT consultant_ID, Fname, Lname, rating, company, experience, fee 
        FROM Consultant 
        WHERE approve = 1
    "); // Include only approved consultants
    $stmt->execute();
    $consultants = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all consultants
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultant Profiles</title>
    <style>
        /* General Styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0D1B2A;
            color: white;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: white;
            font-size: 36px;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* Consultant Container */
        .consultant-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        /* Consultant Card */
        .consultant-card {
            background-color: #2B2D42;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            padding: 20px;
            transition: transform 0.3s;
            color: white;
        }

        .consultant-card:hover {
            transform: translateY(-10px);
        }

        /* Consultant Info */
        .consultant-info {
            margin-bottom: 20px;
        }

        .consultant-info h2 {
            margin: 0;
            font-size: 20px;
            color: white;
        }

        .consultant-rating {
            margin: 10px 0;
            font-size: 18px;
            color: #f4c542;
        }

        .consultant-company {
            font-size: 14px;
            font-weight: bold;
            color: #cccccc;
        }

        /* Consultant Price and Button */
        .consultant-price {
            text-align: center;
        }

        .consultant-price .price {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        .consultant-price button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }

        .consultant-price button:hover {
            background-color: #0056b3;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .consultant-card {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="consultant-container">
        <?php foreach ($consultants as $consultant): ?>
        <div class="consultant-card">
            <div class="consultant-info">
                <h2><?php echo htmlspecialchars($consultant['Fname'] . ' ' . $consultant['Lname']); ?></h2>
                <div class="consultant-rating">
                    <?php
                    // Display star rating based on the rating value
                    $rating = round($consultant['rating']);
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $rating ? "★" : "☆";  // Filled star for rating, empty star otherwise
                    }
                    ?>
                </div>
                <p>Experience: <?php echo htmlspecialchars($consultant['experience']); ?> years</p>
                <p class="consultant-company">Company: <?php echo htmlspecialchars($consultant['company']); ?></p>
            </div>
            <div class="consultant-price">
                <span class="price">SAR<?php echo htmlspecialchars(number_format($consultant['fee'], 2)); ?></span>
                <p>Consultation Fee</p>
                <!-- Book Consultation Button -->
                <form action="invester_book_consultation.php" method="GET">
                    <input type="hidden" name="consultant_id" value="<?php echo $consultant['consultant_ID']; ?>"> 
                    <button type="submit">Book Consultation</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <script>
    let sidebarOpen = true;

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

    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.querySelector('.side-nav');
        sidebar.style.left = '0px';
    });
</script>

</body>
</html>
