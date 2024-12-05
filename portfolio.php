<?php
ob_start();
session_start();
$noNavbar = true;
include "init.php"; // Database connection


// Check if the user is logged in as an investor
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'investor') {
    header("Location: login.php");
    exit();
}

// Get the investor ID from the session
$investor_id = $_SESSION['user_id'];

// Fetch total investments made by the investor
$stmt_total_investment = $conn->prepare("
    SELECT SUM(amount_paid) AS total_invested
    FROM Investment_portfolio
    WHERE investor_ID = :investor_id
");
$stmt_total_investment->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_total_investment->execute();
$total_investment = $stmt_total_investment->fetch(PDO::FETCH_ASSOC)['total_invested'] ?? 0;

// Fetch consolidated property details where the investor has invested
$stmt_properties = $conn->prepare("
    SELECT p.property_ID, p.name, p.city, p.type, p.cost_of_property, 
           SUM(ip.amount_paid) AS investor_invested_amount,
           (SELECT SUM(amount_paid) FROM Investment_portfolio WHERE property_ID = p.property_ID) AS total_invested_amount
    FROM Property p
    JOIN Investment_portfolio ip ON p.property_ID = ip.property_ID
    WHERE ip.investor_ID = :investor_id
    GROUP BY p.property_ID
");
$stmt_properties->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_properties->execute();
$properties = $stmt_properties->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investor Dashboard</title>
    <link rel="stylesheet" href="investor_welcome.css">

    <style>
        body {
            display:block;
            font-family: 'Poppins', sans-serif;
            background-color: #0D1B2A;
            margin: 0;
            padding: 0;
            color: white;
        }
        .content-wrapper {
            margin-top:80px;
            display: flex;
            margin-left: 250px; /* Adjusted to leave space for sidebar */
            padding: 20px;
        }
        .container {
            flex: 1;
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: #1B263B;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            color: white;
        }
        h1, h2 {
            text-align: center;
            color: white;
        }
        .stat-box {
            background-color: #415A77;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .stat-box h3 {
            margin: 0;
            font-size: 24px;
        }
        .stat-box p {
            font-size: 20px;
        }
        .property-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .property-table th, .property-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .property-table th {
            background-color: #415A77;
            color: white;
        }
        .property-table tr:nth-child(even) {
            background-color: #f9f9f9;
            color: black;
        }
        .property-table tr:nth-child(odd) {
            background-color: #415A77;
            color: white;
        }
        .progress-bar {
            background-color: #4CAF50;
            height: 20px;
            border-radius: 5px;
        }
        .progress-bar-container {
            width: 100%;
            background-color: #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="content-wrapper">
    <div class="container">
        <h1>Investor Dashboard</h1>
        
        <!-- Total Investment Stat -->
        <div class="stat-box">
            <h3>Total Investments</h3>
            <p> ﷼<?php echo number_format($total_investment, 2); ?></p>
        </div>

        <!-- Properties Invested -->
        <h2>Properties You've Invested In</h2>
        <?php if (count($properties) > 0): ?>
            <table class="property-table">
                <thead>
                    <tr>
                        <th>Property Name</th>
                        <th>City</th>
                        <th>Type</th>
                        <th>Total Property Cost</th>
                        <th>Amount You Invested</th>
                        <th>Investment Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $property): 
                        $total_value = $property['cost_of_property'];
                        $amount_invested = $property['investor_invested_amount'];
                        $total_invested = $property['total_invested_amount'];
                        $progress_percent = ($total_invested / $total_value) * 100;
                    ?>
                        <tr>
                            <td>
                                <a href="invester_property_details.php?property_id=<?php echo htmlspecialchars($property['property_ID']); ?>">
                                    <?php echo htmlspecialchars($property['name']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($property['city']); ?></td>
                            <td><?php echo htmlspecialchars($property['type']); ?></td>
                            <td> ﷼<?php echo number_format($total_value, 2); ?></td>
                            <td> ﷼<?php echo number_format($amount_invested, 2); ?></td>
                            <td>
                                <div class="progress-bar-container">
                                    <div class="progress-bar" style="width: <?php echo $progress_percent; ?>%;"></div>
                                </div>
                                <?php echo number_format($progress_percent, 2); ?>%
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-properties">You haven't invested in any properties yet.</p>
        <?php endif; ?>
    </div>
</div>
<?php include 'invester_sidebar.php'; ?>
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
<?php ob_end_flush();
