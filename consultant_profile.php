<?php
session_start();
$noNavbar = true;
include "init.php"; // Database connection
include "consultant_sidebar.php";

// Get the investor ID from the query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid investor ID.";
    exit();
}

$investor_id = intval($_GET['id']); // Validate and sanitize the ID

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

    // Fetch properties associated with the investor
    $stmt_properties = $conn->prepare("
        SELECT p.property_ID, p.name AS property_name, p.city, p.street, p.zip_code, p.type, 
               p.size, p.cost_of_property, SUM(ip.amount_paid) AS total_investment, 
               SUM(ip.monthly_return_amount) AS total_monthly_return
        FROM investment_portfolio ip
        JOIN property p ON ip.property_ID = p.property_ID
        WHERE ip.investor_ID = :investor_id
        GROUP BY p.property_ID
    ");
    $stmt_properties->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
    $stmt_properties->execute();
    $properties = $stmt_properties->fetchAll(PDO::FETCH_ASSOC);

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
            --table-border-color: #2C3E50;
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
            padding: 0 200px 0 200px;
            width: calc(100% - 20px);
            box-sizing: border-box;
            margin-top: 80px;
        }

        h1, h2 {
            color: var(--primary-color);
            text-align: center;
            margin-top: 20px;
        }

        .profile-section, .property-section {
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
            text-align:center;
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
            <?php if (!empty($investor['rejection_reason'])): ?>
                <p><strong>Rejection Reason:</strong> <?php echo htmlspecialchars($investor['rejection_reason']); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
