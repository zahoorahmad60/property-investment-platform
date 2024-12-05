<?php
// Start the session
session_start();

// Check if the user is logged in and is an investor
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'investor') {
    // Redirect to login page if the user is not an investor
    header("Location: login.php");
    exit();
}

// Database connection
require_once 'db_connection.php'; // Adjust this to your connection file

// Debugging: Print the username to check if it is set correctly
echo "Debug: Username in session: " . $_SESSION['username'] . "<br>";

$investor_username = $_SESSION['username'];

// Make the query case-insensitive with utf8mb4 collation and trim the username to remove any extra spaces
$investor_id_query = "SELECT InvestorID FROM Investor WHERE TRIM(LOWER(Username)) = TRIM(LOWER(:username))";
$statement = $conn->prepare($investor_id_query);
$statement->execute([':username' => $investor_username]);

// Check if any row was returned
if ($statement->rowCount() > 0) {
    $investor_id_row = $statement->fetch(PDO::FETCH_ASSOC);
    $investor_id = $investor_id_row['InvestorID'];
    echo "Debug: InvestorID found: " . $investor_id . "<br>"; // Debugging: Print the InvestorID
} else {
    echo "Investor not found.";
    exit();
}

// Query to fetch previous and current investments
$previous_investments_query = "
    SELECT p.Name AS property_name, p.City, p.cost_of_property, i.Amount, 'completed' AS status 
    FROM Invests i
    JOIN Property p ON i.PropertyID = p.PropertyID
    WHERE i.InvestorID = :investor_id AND i.Amount = p.cost_of_property";

$current_investments_query = "
    SELECT p.Name AS property_name, p.City, p.cost_of_property, i.Amount, 'active' AS status 
    FROM Invests i
    JOIN Property p ON i.PropertyID = p.PropertyID
    WHERE i.InvestorID = :investor_id AND i.Amount < p.cost_of_property";

$previous_investments_statement = $conn->prepare($previous_investments_query);
$previous_investments_statement->execute([':investor_id' => $investor_id]);

$current_investments_statement = $conn->prepare($current_investments_query);
$current_investments_statement->execute([':investor_id' => $investor_id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investor Dashboard</title>
    <link rel="stylesheet" href="investor_welcome.css">
    <script>
        function toggleSidebar() {
            document.querySelector('.side-nav').classList.toggle('side-nav-active');
        }
    </script>
</head>

<body>
<?php
$profile_link = 'login.php'; // Default to login page

if (isset($_SESSION['username']) && isset($_SESSION['user_type'])) {
    switch ($_SESSION['user_type']) {
        case 'admin':
            $profile_link = 'admin_dashboard.php';
            break;
        case 'investor':
            $profile_link = 'investor_dashboard.php';
            break;
        case 'consultant':
            $profile_link = 'consultant_welcome.php';
            break;
        case 'seller':
            $profile_link = 'seller_welcome.php';
            break;
    }
}
?>

    <!-- Button to open the sidebar -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="logo">
            <a href="h.php">
                <img src="layout/image/hisstakpic.png" alt="Company Logo">
            </a>
        </div>
        <ul class="top-nav-links">
            <li><a href="h.php">Invest</a></li>
            <li><a href="consultant.php">Ask a Consultant</a></li>
            <li><a href="aboutus.php">About</a></li>
            <li><a href="contactus.php">Contact Us</a></li>
        </ul>
        <div class="profile-icon">
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <h2>Previous Investments</h2>
        <table class="investment-table">
            <thead>
                <tr>
                    <th>Property Name</th>
                    <th>City</th>
                    <th>Cost</th>
                    <th>Investment Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $previous_investments_statement->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['property_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['City']); ?></td>
                        <td><?php echo htmlspecialchars($row['cost_of_property']); ?></td>
                        <td><?php echo htmlspecialchars($row['Amount']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h2>Current Investments</h2>
        <table class="investment-table">
            <thead>
                <tr>
                    <th>Property Name</th>
                    <th>City</th>
                    <th>Cost</th>
                    <th>Investment Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $current_investments_statement->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['property_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['City']); ?></td>
                        <td><?php echo htmlspecialchars($row['cost_of_property']); ?></td>
                        <td><?php echo htmlspecialchars($row['Amount']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php include 'invester_sidebar.php'; ?>
</body>
</html>
