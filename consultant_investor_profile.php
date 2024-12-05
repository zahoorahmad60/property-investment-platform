<?php
session_start();
$noNavbar = true;

include "db_connection.php"; // Database connection
include 'consultant_sidebar.php';


$investor_id = $_SESSION['user_id']; // Get the logged-in investor's ID

// Fetch investor details
try {
    $stmt_investor = $conn->prepare("SELECT * FROM investor WHERE investor_ID = :investor_id");
    $stmt_investor->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
    $stmt_investor->execute();
    $investor = $stmt_investor->fetch(PDO::FETCH_ASSOC);

    if (!$investor) {
        echo "Investor not found.";
        exit();
    }

    // Fetch unique properties the investor has invested in and calculate total annual profit
    $total_annual_profit = 0;
    $investments = [];

    $stmt_investments = $conn->prepare("
        SELECT p.property_ID, p.name AS property_name, p.city, p.cost_of_property, 
               SUM(ip.amount_paid) AS total_amount_paid, 
               SUM(ip.monthly_return_amount) AS total_monthly_return
        FROM Investment_portfolio ip
        JOIN Property p ON ip.property_ID = p.property_ID
        WHERE ip.investor_ID = :investor_id
        GROUP BY p.property_ID, p.name, p.city, p.cost_of_property
    ");
    $stmt_investments->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
    $stmt_investments->execute();
    $investments = $stmt_investments->fetchAll(PDO::FETCH_ASSOC);

    foreach ($investments as $investment) {
        $annual_profit = $investment['total_monthly_return'] * 12; // Assuming profit for a year
        $total_annual_profit += $annual_profit;
    }
} catch (Exception $e) {
    echo "Error fetching data: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investor Profile</title>
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
    align-items: center; /* Centers the content horizontally */
    justify-content: flex-start; /* Ensures content stays at the top */
    padding: 20px;
    box-sizing: border-box;
    margin-top: 100px;
}

.profile-section, .investment-section {
    width: 100%; /* Makes it span full width of the container */
    max-width: 800px; /* Ensures it doesn't get too wide */
    background-color: var(--card-bg-color);
    border-radius: 10px;
    padding: 20px;
    margin: 15px 0;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    box-sizing: border-box;
}

h1, h2 {
    color: var(--primary-color);
    text-align: center;
}


        .profile-section, .investment-section {
            background-color: var(--card-bg-color);
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
        }

        .profile-info p {
            font-size: 1.1em;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

            th, td {
                font-size: 0.9em;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <h1>Investor Profile</h1>
    
    <div class="profile-section">
        <h2>Investor Information</h2>
        <div class="profile-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($investor['Fname'] . ' ' . $investor['Lname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($investor['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($investor['phone']); ?></p>
            <p><strong>Status:</strong> <?php echo $investor['status'] ? "Active" : "Inactive"; ?></p>
            <?php if (!empty($investor['rejection_reason'])): ?>
                <p><strong>Rejection Reason:</strong> <?php echo htmlspecialchars($investor['rejection_reason']); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="investment-section">
        <h2>Investment Portfolio</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Property Name</th>
                    <th>City</th>
                    <th>Cost</th>
                    <th>Amount Paid</th>
                    <th>Annual Return</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($investments as $investment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($investment['property_name']); ?></td>
                        <td><?php echo htmlspecialchars($investment['city']); ?></td>
                        <td>﷼<?php echo number_format($investment['cost_of_property']); ?></td>
                        <td>﷼<?php echo number_format($investment['total_amount_paid']); ?></td>
                        <td>﷼<?php echo number_format($investment['total_monthly_return']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.side-nav');
        sidebar.style.left = sidebar.style.left === '0px' ? '-250px' : '0px';
    }
</script>
</body>
</html>
