<?php
// Start the session
session_start();

// Include your initialization file (e.g., database connection)
include 'init.php';

// Get the user ID from the URL and determine the user type
$type = '';
$id = 0;

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = intval($_GET['id']);
} else {
    echo "Invalid request! User type and ID are required.";
    exit();
}

// Validate user type
$allowed_types = ['seller', 'investor', 'consultant'];
if (!in_array($type, $allowed_types)) {
    echo "Invalid user type!";
    exit();
}

// Map user type to table and ID column
$user_map = [
    'seller' => ['table' => 'Seller', 'id_column' => 'seller_ID'],
    'investor' => ['table' => 'Investor', 'id_column' => 'investor_ID'],
    'consultant' => ['table' => 'Consultant', 'id_column' => 'consultant_ID'],
];

$table = $user_map[$type]['table'];
$id_column = $user_map[$type]['id_column'];

// Fetch user details based on the user type
$stmt = $conn->prepare("SELECT * FROM $table WHERE $id_column = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If user not found, display a message
if (!$user) {
    echo "User not found!";
    exit();
}

// Fetch extra details based on user type
$extra_details = [];
if ($type === 'seller') {
    // Fetch the properties listed by the seller
    $properties_stmt = $conn->prepare("SELECT name, city, size, cost_of_property FROM Property WHERE seller_ID = ?");
    $properties_stmt->execute([$id]);
    $extra_details = $properties_stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type === 'investor') {
    // Fetch investments made by the investor
    $investments_stmt = $conn->prepare("
        SELECT p.name, i.investment_percentage, i.amount_paid 
        FROM Investment_portfolio i
        JOIN Property p ON i.property_ID = p.property_ID
        WHERE i.investor_ID = ?");
    $investments_stmt->execute([$id]);
    $extra_details = $investments_stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type === 'consultant') {
    // Fetch consultation sessions
    $sessions_stmt = $conn->prepare("SELECT session_number, date, time FROM Consultation WHERE consultant_ID = ?");
    $sessions_stmt->execute([$id]);
    $extra_details = $sessions_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Map status codes to human-readable text
$status_map = [
    0 => 'Pending',
    1 => 'Approved',
    2 => 'Rejected'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo ucfirst($type); ?> Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0D1B2A;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #FFFFFF;
        }
        .container {
            max-width: 800px;
            background-color: #1B263B;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 100%;
        }
        h1 {
            margin-bottom: 20px;
            color: #28a745; /* Green color for the heading */
        }
        p {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .status {
            font-weight: bold;
            color: #ffc107; /* Amber color for status */
        }
        .extra-details {
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
            color: #FFFFFF;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff; /* Blue background for headers */
        }
        tr:nth-child(even) {
            background-color: #3F4E59;
        }
        .back-link {
            display: inline-block;
            margin-top: 30px;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #28a745; /* Green background */
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .back-link:hover {
            background-color: #218838; /* Darker green on hover */
        }
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            table, th, td {
                font-size: 14px;
            }
            .back-link {
                padding: 8px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1><?php echo ucfirst($type); ?> Details</h1>
    <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['Fname']); ?></p>
    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['Lname']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Phone No:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>

    <p><strong>Status:</strong> <span class="status"><?php echo htmlspecialchars($status_map[$user['status']]); ?></span></p>

    <!-- Display extra details based on user type -->
    <?php if ($type === 'seller' && count($extra_details) > 0): ?>
        <div class="extra-details">
            <h2>Listed Properties</h2>
            <table>
                <thead>
                    <tr>
                        <th>Property Name</th>
                        <th>City</th>
                        <th>Size (sq ft)</th>
                        <th>Cost (USD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($extra_details as $property): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($property['name']); ?></td>
                            <td><?php echo htmlspecialchars($property['city']); ?></td>
                            <td><?php echo htmlspecialchars($property['size']); ?></td>
                            <td><?php echo htmlspecialchars($property['cost_of_property']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($type === 'investor' && count($extra_details) > 0): ?>
        <div class="extra-details">
            <h2>Investments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Property Name</th>
                        <th>Investment Percentage (%)</th>
                        <th>Amount Paid (USD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($extra_details as $investment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($investment['name']); ?></td>
                            <td><?php echo htmlspecialchars($investment['investment_percentage']); ?></td>
                            <td><?php echo htmlspecialchars($investment['amount_paid']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($type === 'consultant' && count($extra_details) > 0): ?>
        <div class="extra-details">
            <h2>Consultation Sessions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Session Number</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($extra_details as $session): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($session['session_number']); ?></td>
                            <td><?php echo htmlspecialchars($session['date']); ?></td>
                            <td><?php echo htmlspecialchars($session['time']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        
    <?php endif; ?>

    <a href="admin_dashboard.php" class="back-link">Go Back to Dashboard</a>
</div>

</body>
</html>
