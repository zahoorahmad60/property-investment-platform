<?php
// Start the session and output buffering to handle headers
session_start();
ob_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Include initialization and database connection
$noNavbar = true;
include 'init.php';
include 'admin_sidebar.php';

$pageTitle = "Investments Made";

// Initialize the investments arrays
$inProgressInvestments = [];
$completedInvestments = [];

try {
    // Total number of distinct investments (unique properties with investments)
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT property_ID) AS total_investments FROM investment_portfolio");
    $stmt->execute();
    $totalInvestments = $stmt->fetchColumn();

    // In-progress investments: where the investment percentage is less than 100%
    $stmt = $conn->prepare("
        SELECT p.property_ID, p.name AS property_name, p.city, p.cost_of_property, 
               SUM(ip.amount_paid) AS total_invested_amount, 
               SUM(ip.investment_percentage) AS total_investment_percentage, 
               COUNT(ip.investor_ID) AS investor_count,
               MAX(p.monthly_return_percentage) AS monthly_return_percentage
        FROM property p
        JOIN investment_portfolio ip ON p.property_ID = ip.property_ID
        GROUP BY p.property_ID
        HAVING total_investment_percentage < 100
    ");
    $stmt->execute();
    $inProgressInvestments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Completed investments: where the investment percentage is exactly 100%
    $stmt = $conn->prepare("
        SELECT p.property_ID, p.name AS property_name, p.city, p.cost_of_property, 
               SUM(ip.amount_paid) AS total_invested_amount, 
               SUM(ip.investment_percentage) AS total_investment_percentage, 
               COUNT(ip.investor_ID) AS investor_count,
               MAX(p.monthly_return_percentage) AS monthly_return_percentage
        FROM property p
        JOIN investment_portfolio ip ON p.property_ID = ip.property_ID
        GROUP BY p.property_ID
        HAVING total_investment_percentage = 100
    ");
    $stmt->execute();
    $completedInvestments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $debug_message = "Database Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary-color: #3DA5D9;
            --secondary-color: #2A3B4C;
            --background-color: #1D2939;
            --text-color: #E8E8E8;
            --card-bg-color: #1E2D3D;
            --button-color: #3A7EBA;
            --hover-color: #5A96D8;
            --table-border-color: #2C3E50;
            --success-color: #2ECC71;
            --danger-color: #E74C3C;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            padding: 0 40px 0 300px;
            width: calc(100% - 30px);
            box-sizing: border-box;
            margin-top: 200px;
        }

        h1 {
            color: var(--primary-color);
            margin: 20px 0;
            text-align: center;
            font-size: 2em;
            width: 100%;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }

        .button-group .btn {
            width: 150px;
            padding: 12px;
            font-size: 1em;
            text-align: center;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .button-group .btn:hover {
            transform: translateY(-2px);
        }

        .btn-warning {
            background-color: #f39c12;
        }

        .btn-warning:hover {
            background-color: #e67e22;
        }

        .btn-success {
            background-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: #27ae60;
        }

        .btn-danger {
            background-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .pending-section {
            background-color: var(--card-bg-color);
            width: 100%;
            border-radius: 10px;
            margin: 15px 0;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            box-sizing: border-box;
        }

        .pending-section:hover {
            transform: translateY(-5px);
        }

        .pending-section h2 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.8em;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid var(--table-border-color);
        }

        th, td {
            padding: 15px;
            text-align: center;
            color: var(--text-color);
            font-size: 1em;
        }

        th {
            background-color: var(--secondary-color);
            font-size: 1.1em;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #2A3B4C;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 1.5em;
            }

            .pending-section {
                padding: 15px;
            }

            th, td {
                font-size: 0.9em;
                padding: 10px;
            }
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--card-bg-color);
            padding: 20px;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            color: var(--text-color);
            position: relative;
        }

        .modal-content h3 {
            margin-top: 0;
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: var(--text-color);
            font-size: 1.5em;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <h1>Investments Made</h1>
    <?php if (isset($debug_message)) : ?>
        <div style="color: red; font-weight: bold;"><?php echo $debug_message; ?></div>
    <?php endif; ?>
    
    <div class="button-group">
        <button class="btn btn-warning" onclick="toggleSection('in-progress')">In Progress</button>
        <button class="btn btn-success" onclick="toggleSection('completed')">Completed</button>
    </div>

    <div id="in-progress" class="pending-section">
        <h2>In Progress Investments</h2>
        <?php if (count($inProgressInvestments) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Property Name</th>
                        <th>City</th>
                        <th>Property Cost</th>
                        <th>Total Invested Amount</th>
                        <th>Total Investment Percentage</th>
                        <th>Annual Return</th>
                        <th>Investors</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($inProgressInvestments as $property): ?>
                    <tr>
                        <td>
                            <a href="admin_property_details.php?property_id=<?php echo htmlspecialchars($property['property_ID']); ?>">
                                <?php echo htmlspecialchars($property['property_name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($property['city']); ?></td>
                        <td><?php echo number_format(htmlspecialchars($property['cost_of_property']), 2); ?></td>
                        <td><?php echo number_format(htmlspecialchars($property['total_invested_amount']), 2); ?></td>
                        <td><?php echo htmlspecialchars($property['total_investment_percentage']); ?>%</td>
                        <td><?php echo htmlspecialchars($property['monthly_return_percentage']); ?>%</td>
                        <td><?php echo htmlspecialchars($property['investor_count']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No properties are currently in progress.</p>
        <?php endif; ?>
    </div>

    <div id="completed" class="pending-section" style="display:none;">
        <h2>Completed Investments</h2>
        <?php if (count($completedInvestments) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Property Name</th>
                        <th>City</th>
                        <th>Property Cost</th>
                        <th>Total Invested Amount</th>
                        <th>Total Investment Percentage</th>
                        <th>Annual Return</th>
                        <th>Investors</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($completedInvestments as $property): ?>
                        <tr>
                            <td>
                                <a href="admin_property_details.php?property_id=<?php echo htmlspecialchars($property['property_ID']); ?>">
                                    <?php echo htmlspecialchars($property['property_name']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($property['city']); ?></td>
                            <td><?php echo number_format(htmlspecialchars($property['cost_of_property']), 2); ?></td>
                            <td><?php echo number_format(htmlspecialchars($property['total_invested_amount']), 2); ?></td>
                            <td><?php echo htmlspecialchars($property['total_investment_percentage']); ?>%</td>
                            <td><?php echo htmlspecialchars($property['monthly_return_percentage']); ?>%</td>
                            <td><?php echo htmlspecialchars($property['investor_count']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No completed investments.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleSection(sectionId) {
        document.querySelectorAll('.pending-section').forEach(section => {
            section.style.display = 'none';
        });
        document.getElementById(sectionId).style.display = 'block';
    }
</script>
</body>
</html>
