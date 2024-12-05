<?php
// Start the session and include necessary files
session_start();
include 'init.php'; // Include your database connection

// Check if the seller ID is provided in the URL
if (!isset($_GET['seller_id'])) {
    echo "Seller ID not provided.";
    exit();
}

$seller_id = intval($_GET['seller_id']); // Get the seller_id from the URL

// Fetch seller details
$stmt_seller = $conn->prepare("SELECT * FROM Seller WHERE seller_ID = :seller_id");
$stmt_seller->bindParam(':seller_id', $seller_id, PDO::PARAM_INT);
$stmt_seller->execute();
$seller = $stmt_seller->fetch(PDO::FETCH_ASSOC);

if (!$seller) {
    echo "Seller not found.";
    exit();
}

// Fetch properties of the seller
$stmt_properties = $conn->prepare("SELECT * FROM Property WHERE seller_ID = :seller_id");
$stmt_properties->bindParam(':seller_id', $seller_id, PDO::PARAM_INT);
$stmt_properties->execute();
$properties = $stmt_properties->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0D1B2A;
            margin: 0;
            padding: 0;
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #1B263B;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #FFF;
        }

        .profile-section {
            margin-bottom: 40px;
        }

        .profile-details, .property-list {
            background-color: #0D1B2A;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-details p, .property-list p {
            margin: 10px 0;
            font-size: 16px;
        }

        .property-list table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: white;
        }

        .property-list th, .property-list td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        .property-list th {
            background-color: #415A77;
            color: white;
        }

        .property-list tr:nth-child(even) {
            background-color: #f9f9f9;
            color: black;
        }

        .property-list tr:nth-child(odd) {
            background-color: #415A77;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Seller Profile</h1>

        <div class="profile-section profile-details">
            <h2>Seller Information</h2>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($seller['Fname']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($seller['Lname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($seller['username']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($seller['phone']); ?></p>
        </div>

        <div class="profile-section property-list">
            <h2>Properties Listed by <?php echo htmlspecialchars($seller['Fname'] . ' ' . $seller['Lname']); ?></h2>

            <?php if (count($properties) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Property Name</th>
                            <th>City</th>
                            <th>Street</th>
                            <th>Zip Code</th>
                            <th>Type</th>
                            <th>Size (sq ft)</th>
                            <th>Cost (USD)</th>
                            <th>Monthly Rental Returns (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($properties as $property): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($property['name']); ?></td>
                                <td><?php echo htmlspecialchars($property['city']); ?></td>
                                <td><?php echo htmlspecialchars($property['street']); ?></td>
                                <td><?php echo htmlspecialchars($property['zip_code']); ?></td>
                                <td><?php echo htmlspecialchars($property['type']); ?></td>
                                <td><?php echo htmlspecialchars($property['size']); ?></td>
                                <td>$<?php echo number_format($property['cost_of_property'], 2); ?></td>
                                <td>$<?php echo number_format($property['monthly_rental_returns'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No properties listed by this seller.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
