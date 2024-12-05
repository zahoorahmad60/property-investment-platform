<?php
session_start();
$noNavbar = true;
$pageTitle = "Admin Dashboard";
include 'init.php';
include "admin_sidebar.php";

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Fetch pending investors
$pending_investors_stmt = $conn->prepare("SELECT investor_ID, Fname, email FROM Investor WHERE approve = 0 AND status = 0");
$pending_investors_stmt->execute();
$pending_investors = $pending_investors_stmt->fetchAll();

// Fetch pending sellers
$pending_sellers_stmt = $conn->prepare("SELECT seller_ID, Fname, email FROM Seller WHERE approve = 0 AND status = 0");
$pending_sellers_stmt->execute();
$pending_sellers = $pending_sellers_stmt->fetchAll();

// Fetch pending consultants
$pending_consultants_stmt = $conn->prepare("SELECT consultant_ID, Fname, email FROM Consultant WHERE approve = 0 AND status = 0");
$pending_consultants_stmt->execute();
$pending_consultants = $pending_consultants_stmt->fetchAll();
// Calculate total earnings from consultations (5% deduction)
$total_consultation_earnings_stmt = $conn->prepare("
    SELECT SUM(co.fee * 0.05) AS total_earnings 
    FROM Consultation c
    JOIN Consultant co ON c.consultant_ID = co.consultant_ID
    WHERE c.status = 'approved' AND c.paid = 1
");
$total_consultation_earnings_stmt->execute();
$total_consultation_earnings = $total_consultation_earnings_stmt->fetchColumn() ?? 0;


// Calculate total earnings from properties (2% deduction)
$total_property_earnings_stmt = $conn->prepare("
    SELECT SUM(cost_of_property * 0.02) AS total_earnings 
    FROM Property 
");
$total_property_earnings_stmt->execute();
$total_property_earnings = $total_property_earnings_stmt->fetchColumn() ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTTXrXjoP4UA6X6dtgX6IRY/t1kE8pZZXYjOn57AWtJsk2EXd/ZgpLT8NMMx1iN/pg1ERxP8Ng==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
    margin: 20px 0;
    text-align: center;
    font-size: 2em;
    width: 100%;
}

/* Section styling */
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
    width: 100%; /* Full width for table */
    border-collapse: collapse;
    margin-top: 10px;
}

table, th, td {
    border: 1px solid var(--table-border-color);
}

th, td {
    padding: 15px; /* Increased padding for better readability */
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

/* Button styling */
.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    font-size: 1em;
    color: #fff;
    cursor: pointer;
    transition: background-color 0.2s ease;
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

/* Responsive adjustments */
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

    .btn {
        padding: 8px 12px;
        font-size: 0.9em;
    }
}


    </style>
</head>
<body>
<div class="wrapper">
    <h1>Admin Dashboard</h1>

    <!-- Earnings Section -->
    <div class="pending-section">
        <h2>Total Earnings</h2>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Total Earnings</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Consultation Sessions (5%)</td>
                    <td>SAR <?php echo number_format($total_consultation_earnings, 2); ?></td>
                </tr>
                <tr>
                    <td>Property Sales (2%)</td>
                    <td>SAR <?php echo number_format($total_property_earnings, 2); ?></td>
                </tr>
                <tr>
                    <th>Total Earnings</th>
                    <th>SAR <?php echo number_format($total_consultation_earnings + $total_property_earnings, 2); ?></th>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pending Sellers Section -->
    <div class="pending-section">
        <h2>Pending Sellers</h2>
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pending_sellers) > 0): ?>
                    <?php foreach ($pending_sellers as $seller): ?>
                        <tr>
                        <td>
                                <a href="admin_sellers_profile.php?id=<?php echo htmlspecialchars($seller['seller_ID']); ?>">
                                    <?php echo htmlspecialchars($seller['Fname']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($seller['email']); ?></td>
                            <td>
                                <a href="approve_user.php?type=seller&id=<?php echo urlencode($seller['seller_ID']); ?>" class="btn btn-success">Approve</a>
                                <a href="reject_user.php?type=seller&id=<?php echo urlencode($seller['seller_ID']); ?>" class="btn btn-danger">Reject</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">No pending sellers for approval.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pending Consultants Section -->
    <div class="pending-section">
        <h2>Pending Consultants</h2>
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pending_consultants) > 0): ?>
                    <?php foreach ($pending_consultants as $consultant): ?>
                        <tr>
                        <td>
                                <a href="admin_consultant_profile.php?id=<?php echo htmlspecialchars($consultant['consultant_ID']); ?>">
                                    <?php echo htmlspecialchars($consultant['Fname']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($consultant['email']); ?></td>
                            <td>
                                <a href="approve_user.php?type=consultant&id=<?php echo urlencode($consultant['consultant_ID']); ?>" class="btn btn-success">Approve</a>
                                <a href="reject_user.php?type=consultant&id=<?php echo urlencode($consultant['consultant_ID']); ?>" class="btn btn-danger">Reject</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">No pending consultants for approval.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pending Investors Section -->
    <div class="pending-section">
        <h2>Pending Investors</h2>
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pending_investors) > 0): ?>
                    <?php foreach ($pending_investors as $investor): ?>
                        <tr>

                        <td>
                                <a href="admin_investor_profile.php?id=<?php echo htmlspecialchars($investor['investor_ID']); ?>">
                                    <?php echo htmlspecialchars($investor['Fname'] ); ?>
                                </a>
                            </td>                            <td><?php echo htmlspecialchars($investor['email']); ?></td>
                            <td>
                                <a href="approve_user.php?type=investor&id=<?php echo urlencode($investor['investor_ID']); ?>" class="btn btn-success">Approve</a>
                                <a href="reject_user.php?type=investor&id=<?php echo urlencode($investor['investor_ID']); ?>" class="btn btn-danger">Reject</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">No pending investors for approval.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.side-nav');
        if (sidebar.style.left === '0px') {
            sidebar.style.left = '-250px';
        } else {
            sidebar.style.left = '0px';
        }
    }
</script>
</body>
</html>
