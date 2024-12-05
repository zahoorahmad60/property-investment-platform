<?php
// Start output buffering
ob_start();

// Start the session and check if the user is logged in and is an admin
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$noNavbar = true;
include 'init.php';
include 'admin_sidebar.php';
$pageTitle = "Admin Profile";

// Assuming a single admin with ID 1 for simplicity; replace with dynamic admin ID if needed
$admin_id = 1;

// Fetch admin profile information
$stmt = $conn->prepare("SELECT * FROM admin WHERE admin_ID = :admin_id");
$stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Process the profile update if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the new password
    $stmt = $conn->prepare("UPDATE admin SET username = :username, password = :password WHERE admin_ID = :admin_id");
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':password', $password);
    $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();

    // Display success message
    $_SESSION['message'] = "Profile updated successfully!";
    header("Refresh:0"); // Refresh to load updated information
    exit();
}

// Calculate admin earnings from consultations and properties
$consultation_earnings_stmt = $conn->prepare("
    SELECT consultation.session_number, consultant.fee AS consultation_fees, 
           (consultant.fee * 0.05) AS admin_earning, consultation.date
    FROM consultation
    JOIN consultant ON consultation.consultant_ID = consultant.consultant_ID
    WHERE consultation.paid = 1 AND consultant.fee > 0
");
$consultation_earnings_stmt->execute();
$consultation_earnings = $consultation_earnings_stmt->fetchAll(PDO::FETCH_ASSOC);


$property_earnings_stmt = $conn->prepare("
    SELECT property_ID, 
           cost_of_property, 
           monthly_return_percentage, 
           (cost_of_property * monthly_return_percentage) AS monthly_rental_returns, 
           (cost_of_property * monthly_return_percentage * 0.02) AS admin_earning
    FROM Property
    WHERE monthly_return_percentage > 0
");
$property_earnings_stmt->execute();
$property_earnings = $property_earnings_stmt->fetchAll(PDO::FETCH_ASSOC);





// Calculate total earnings
$total_consultation_earnings = array_sum(array_column($consultation_earnings, 'admin_earning'));
$total_property_earnings = array_sum(array_column($property_earnings, 'admin_earning'));
$total_earnings = $total_consultation_earnings + $total_property_earnings;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
        --success-color: #2ECC71;
        --danger-color: #E74C3C;
    }

    body {
    display:block !important;
    font-family: 'Roboto', sans-serif;
    background-color: var(--background-color);
    color: var(--text-color);
    margin: 0;
}

.wrapper {
    display: flex;
    flex-direction: column;
    padding: 0 40px 0 300px; /* Adds left and right padding for sidebar and right margin */
    width: calc(100% - 30px); /* Adjusted width to account for padding */
    box-sizing: border-box;
    margin-top: 80px;
}

    h1 {
        color: var(--primary-color);
        text-align: center;
        font-size: 2em;
        margin: 20px 0;
    }
    .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }

        .button-group .btn {
            padding: 12px;
            font-size: 1em;
            text-align: center;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .button-group .btn:hover {
            transform: translateY(-2px);
        }
    .button-group {
        text-align: center;
        margin: 20px 0;
    }
    .btn-settings {
        background-color: var(--primary-color);
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1em;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .btn-settings:hover {
        background-color: var(--hover-color);
        transform: translateY(-3px);
    }
    .table-wrapper, .dashboard-card {
        margin-top: 20px;
        padding: 20px;
        background-color: var(--card-bg-color);
        border-radius: 8px;
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
    }
    .dashboard-card {
        display: flex;
        justify-content: space-between;
        padding: 15px;
        color: var(--text-color);
    }
    .dashboard-card h2 {
        color: var(--primary-color);
        font-size: 1.5em;
    }
    .dashboard-card .card-content {
        font-size: 1.2em;
    }
    .earnings-info {
        font-size: 1.5em;
        text-align: center;
        margin-top: 20px;
        background-color: var(--secondary-color);
        color: var(--success-color);
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    .earnings-info1 {
    font-size: 1.5em;
    text-align: center;
    margin-top: 20px;
    background-color: #28a745; /* Softer, rich green */
    color: #e8f5e9; /* Light, complementary success color */
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
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
        font-weight: 600;
    }
    tr:nth-child(even) {
        background-color: #2A3B4C;
    }
</style>

</head>
<body>
<div class="wrapper">
    <h1>Admin Profile</h1>

    <!-- Total Earnings Display -->
    <div class="earnings-info1">
        <p><strong>Total Earnings/Profit: </strong> SAR <?php echo number_format($total_earnings, 2); ?></p>
    </div>

    <!-- Settings Button -->
    <div class="button-group">
        <button class="btn-settings" onclick="toggleSettings()">Settings</button>
    </div>

    <!-- Settings Modal (hidden by default) -->
    <div id="settings-modal" style="display: none;">
        <h2>Update Profile</h2>
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
            
            <label>New Password:</label>
            <input type="password" name="password" placeholder="New password" required>
            
            <input type="submit" value="Update Profile" class="btn-settings">
        </form>
    </div>

    <!-- Dashboard Cards for Quick Stats -->
    <div class="dashboard-card">
        <h2>Admin Overview</h2>
        <div class="card-content">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($admin['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
        </div>
    </div>
    <div class="button-group">
        <button class="btn btn-warning" onclick="toggleSection('Consultation')">Consultation Earnings</button>
        <button class="btn btn-success" onclick="toggleSection('property')">Property Earnings</button>
    </div>
    <!-- Admin Earnings Information -->
    <div id="Consultation" class="table-wrapper earnings">
        <h2>Consultation Earnings (5% of Fees)</h2>
        <table>
            <thead>
                <tr>
                    <th>Session Number</th>
                    <th>Consultation Fee</th>
                    <th>Admin Earning</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultation_earnings as $earning): ?>
                    <tr>
                        <td><?php echo $earning['session_number']; ?></td>
                        <td><?php echo $earning['consultation_fees']; ?></td>
                        <td><?php echo $earning['admin_earning']; ?></td>
                        <td><?php echo $earning['date']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="property" class="table-wrapper earnings" style="display:none;">
        <h2>Property Earnings (2% of Annual Returns)</h2>
        <table>
            <thead>
                <tr>
                    <th>Property ID</th>
                    <th>Annual Rental Returns</th>
                    <th>Admin Earning</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($property_earnings as $earning): ?>
                    <tr>
                        <td><?php echo $earning['property_ID']; ?></td>
                        <td><?php echo $earning['monthly_rental_returns']; ?></td>
                        <td><?php echo $earning['admin_earning']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleSettings() {
        const settingsModal = document.getElementById('settings-modal');
        settingsModal.style.display = settingsModal.style.display === 'none' ? 'block' : 'none';
    }
</script>
<script>
    function toggleSection(sectionId) {
        document.querySelectorAll('.earnings').forEach(section => {
            section.style.display = 'none';
        });
        document.getElementById(sectionId).style.display = 'block';
    }
</script>
</body>
</html>

<?php
// End output buffering
ob_end_flush();
?>
