<?php
session_start();
$noNavbar = true;
include "init.php"; // Database connection
include "admin_sidebar.php";

// Get the seller ID from the query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid seller ID.";
    exit();
}

$seller_id = intval($_GET['id']); // Validate and sanitize the ID

// Fetch seller details
try {
    $stmt_seller = $conn->prepare("SELECT * FROM seller WHERE seller_ID = :seller_id");
    $stmt_seller->bindParam(':seller_id', $seller_id, PDO::PARAM_INT);
    $stmt_seller->execute();
    $seller = $stmt_seller->fetch(PDO::FETCH_ASSOC);

    if (!$seller) {
        echo "Seller not found.";
        exit();
    }

    // Fetch properties associated with the seller
    $stmt_properties = $conn->prepare("
        SELECT property_ID, name AS property_name, city, street, zip_code, type, 
               size, cost_of_property, monthly_return_percentage 
        FROM property 
        WHERE seller_ID = :seller_id
    ");
    $stmt_properties->bindParam(':seller_id', $seller_id, PDO::PARAM_INT);
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
    <title>Seller Profile</title>
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
    <h1>Seller Profile</h1>
    
    <div class="profile-section">
        <h2>Seller Information</h2>
        <div class="profile-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($seller['Fname'] . ' ' . $seller['Lname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($seller['phone']); ?></p>
            <?php if (!empty($seller['rejection_reason'])): ?>
                <p><strong>Rejection Reason:</strong> <?php echo htmlspecialchars($seller['rejection_reason']); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="property-section">
        <h2>Seller Properties</h2>
        <?php if (!empty($properties)): ?>
        <table>
            <thead>
                <tr>
                    <th>Property Name</th>
                    <th>City</th>
                    <th>Street</th>
                    <th>ZIP Code</th>
                    <th>Type</th>
                    <th>Size (sq ft)</th>
                    <th>Cost</th>
                    <th>Annual  Rent Returns %</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($properties as $property): ?>
                    <tr>
                    <td>
                            <a href="admin_property_details.php?property_id=<?php echo htmlspecialchars($property['property_ID']); ?>">
                                <?php echo htmlspecialchars($property['property_name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($property['city']); ?></td>
                        <td><?php echo htmlspecialchars($property['street']); ?></td>
                        <td><?php echo htmlspecialchars($property['zip_code']); ?></td>
                        <td><?php echo htmlspecialchars($property['type']); ?></td>
                        <td><?php echo number_format($property['size'], 2); ?></td>
                        <td>﷼<?php echo number_format($property['cost_of_property'], 2); ?></td>
                        <td>﷼<?php echo number_format($property['monthly_return_percentage'], 2); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No properties found for this seller.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
