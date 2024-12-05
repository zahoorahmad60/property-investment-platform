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

// Set the default property type for beach villas
$type = 'Beach_villas';
$pageTitle = ucfirst($type) . " Listings";

// Fetch properties and associated seller information based on type
try {
    $stmt = $conn->prepare("
        SELECT 
            p.property_ID, 
            p.name AS property_name, 
            p.city, 
            p.image_path, 
            p.type, 
            p.cost_of_property, 
            (p.cost_of_property * 0.02) AS admin_fee, 
            s.seller_ID, 
            s.Fname, 
            s.Lname 
        FROM property p 
        JOIN seller s ON p.seller_ID = s.seller_ID 
        WHERE p.type = :type
    ");
    $stmt->bindParam(':type', $type);
    $stmt->execute();
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total admin fee
    $total_admin_fee = 0;
    foreach ($properties as $property) {
        $total_admin_fee += $property['admin_fee'];
    }
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

        h1 {
            color: var(--primary-color);
            margin: 20px 0;
            text-align: center;
            font-size: 2em;
            width: 100%;
        }

        .admin-fee-total {
            font-size: 1.2em;
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 20px;
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

        img {
            width: 100px;
            height: auto;
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
    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>

    <!-- Display total admin fee -->
    <div class="admin-fee-total">
        Total Admin Fee: SAR <?php echo number_format($total_admin_fee, 2); ?>
    </div>

    <?php if (isset($debug_message)) : ?>
        <div style="color: red; font-weight: bold;"><?php echo $debug_message; ?></div>
    <?php endif; ?>

    <?php if (count($properties) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Seller Name</th>
                    <th>Property Name</th>
                    <th>City</th>
                    <th>Type</th>
                    <th>Cost of Property (SAR)</th>
                    <th>Admin Fee (SAR)</th>
                    <th>Photo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($properties as $property): ?>
                    <tr>
                        <td>
                            <a href="admin_sellers_profile.php?id=<?php echo htmlspecialchars($property['seller_ID']); ?>">
                                <?php echo htmlspecialchars($property['Fname'] . " " . $property['Lname']); ?>
                            </a>
                        </td>
                        <td>
                            <a href="admin_property_details.php?property_id=<?php echo htmlspecialchars($property['property_ID']); ?>">
                                <?php echo htmlspecialchars($property['property_name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($property['city']); ?></td>
                        <td><?php echo htmlspecialchars($property['type']); ?></td>
                        <td>SAR <?php echo number_format($property['cost_of_property'], 2); ?></td>
                        <td>SAR <?php echo number_format($property['admin_fee'], 2); ?></td>
                        <td><img src="seller/<?php echo htmlspecialchars($property['image_path']); ?>" alt="Property Photo"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No properties found for <?php echo htmlspecialchars($type); ?>.</p>
    <?php endif; ?>
</div>
</body>
</html>
