<?php
session_start();
$noNavbar = true;
include "invester_sidebar.php";
include "db_connection.php"; // Database connection

// Check if the user is logged in as an investor
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'investor') {
    header("Location: login.php");
    exit();
}

$investor_id = $_SESSION['user_id']; // Get the logged-in investor's ID

// Fetch unique properties the investor has invested in and calculate total annual profit
$total_annual_profit = 0;
$investments = [];

try {
    $stmt_investments = $conn->prepare("
        SELECT p.property_ID, p.name, p.city, p.cost_of_property, 
               SUM(ip.amount_paid) AS total_amount_paid, 
               SUM(ip.monthly_return_amount) AS total_monthly_return,
               p.image_path
        FROM Investment_portfolio ip
        JOIN Property p ON ip.property_ID = p.property_ID
        WHERE ip.investor_ID = :investor_id
        GROUP BY p.property_ID, p.name, p.city, p.cost_of_property, p.image_path
    ");
    $stmt_investments->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
    $stmt_investments->execute();
    $investments = $stmt_investments->fetchAll(PDO::FETCH_ASSOC);

    // Calculate the total annual profit across all properties
    foreach ($investments as $investment) {
        $profit = $investment['total_monthly_return'];
        $total_annual_profit += $profit;
    }
} catch (Exception $e) {
    echo "Error fetching investments: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investor Profit Dashboard</title>
    <link rel="stylesheet" href="seller/layout/css/welcome_sller.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #0D1B2A;
            color: #E8E8E8;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Main container for the entire dashboard */
        .main-container {
            max-width: 1200px;
            width: 100%;
            margin: 20px;
            padding: 20px;
            background-color: #1B263B;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            margin-top: 500px;
        }

        .top-bar {
            background-color: #23374D;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #E8E8E8;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
        }

        .top-bar h1 {
            font-size: 32px;
            font-weight: bold;
            color: #4CAF50;
            margin: 0;
        }

        .top-bar .info p {
            margin: 5px 0;
            font-size: 16px;
            color: #A3BAC3;
        }

        .content-section {
            padding: 20px;
            color: #E8E8E8;
        }

        .content-section h2 {
            font-size: 28px;
            text-align: center;
            color: #A3BAC3;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            justify-content: center;
            margin-top: 20px;
        }

        .card {
            background-color: #2B2D42;
            border-radius: 10px;
            padding: 20px;
            width: 280px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border: 2px solid transparent;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.25);
            border-color: #4CAF50;
        }

        .card-header {
            font-size: 22px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 15px;
        }

        .card-body img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .card-body p {
            margin: 8px 0;
            font-size: 16px;
            color: #C0C5CE;
        }

        .profit-info {
            margin-top: 10px;
            font-weight: bold;
            color: #4CAF50;
            font-size: 18px;
        }
    </style>
</head>
<body>
<div class="main-container" >
    <div class="top-bar">
        <h1>Total Monthly Profit: ﷼<?php echo number_format($total_annual_profit, 2); ?></h1>
        <div class="info">
            <p>Total Properties Invested: <?php echo count($investments); ?></p>
        </div>
    </div>

    <div class="content-section">
        <h2>Property Details</h2>

        <div class="card-container">
            <?php
            foreach ($investments as $investment) {
                $property_id = $investment['property_ID'];
                $name = htmlspecialchars($investment['name']);
                $city = htmlspecialchars($investment['city']);
                $cost_of_property = $investment['cost_of_property'];
                $total_amount_paid = $investment['total_amount_paid'];
                $total_monthly_return = $investment['total_monthly_return'];

                // Calculate profit as (total_monthly_return * 12) - total_amount_paid (assuming profit over a year)
                $profit = $total_monthly_return;
                ?>
                <div class="card">
                    <div class="card-header"><?php echo $name; ?></div>
                    <div class="card-body">
                        <img src="<?php echo 'seller/' . str_replace('\\', '/', htmlspecialchars($investment['image_path'])); ?>" alt="<?php echo $name; ?>">
                        <p>City: <?php echo $city; ?></p>
                        <p>Cost: ﷼<?php echo number_format($cost_of_property); ?></p>
                        <p>Amount Paid: ﷼<?php echo number_format($total_amount_paid); ?></p>
                        <p class="profit-info">Annual Profit: ﷼<?php echo number_format($profit); ?></p>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

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
</body>
</html>
