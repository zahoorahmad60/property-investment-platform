<?php
// Start the session and output buffering
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Include initialization and database connection
$noNavbar = true;
require_once 'init.php'; // Ensure this includes database connection and required functions
require_once 'admin_sidebar.php';

$pageTitle = "Seller Management";

// Approve or reject seller based on form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sellerID = $_POST['sellerID'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        $stmt = $conn->prepare("UPDATE Seller SET approve = 1 WHERE seller_ID = ?");
        $stmt->execute([$sellerID]);
    } elseif ($action == 'reject') {
        $rejectionReason = $_POST['rejection_reason'] ?? 'No reason provided';
        $stmt = $conn->prepare("UPDATE Seller SET approve = -1, rejection_reason = ? WHERE seller_ID = ?");
        $stmt->execute([$rejectionReason, $sellerID]);
    }

    // Refresh to show updated lists
    header("Location: admin_sellers.php");
    exit();
}

// Fetch sellers by status
function getSellersByStatus($conn, $status) {
    $stmt = $conn->prepare("SELECT seller_ID, Fname, Lname, email, rejection_reason FROM Seller WHERE approve = ?");
    $stmt->execute([$status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch pending, accepted, and rejected sellers
$pendingSellers = getSellersByStatus($conn, 0);
$acceptedSellers = getSellersByStatus($conn, 1);
$rejectedSellers = getSellersByStatus($conn, -1);

// End output buffering
ob_end_flush();
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
    </style>
</head>
<body>
<div class="wrapper">
    <h1>Seller Management</h1>

    <div class="button-group">
        <button class="btn btn-warning" onclick="showSection('pending-section')">Pending</button>
        <button class="btn btn-success" onclick="showSection('accepted-section')">Accepted</button>
        <button class="btn btn-danger" onclick="showSection('rejected-section')">Rejected</button>
    </div>

    <!-- Pending Sellers Section -->
    <div id="pending-section" class="pending-section">
        <h2>Pending Sellers</h2>
        <?php if (!empty($pendingSellers)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingSellers as $seller): ?>
                        <tr>
                            <td>
                                <a href="admin_sellers_profile.php?id=<?php echo htmlspecialchars($seller['seller_ID']); ?>">
                                    <?php echo htmlspecialchars($seller['Fname'] . " " . $seller['Lname']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($seller['email']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="sellerID" value="<?php echo htmlspecialchars($seller['seller_ID']); ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success">Approve</button>
                                </form>
                                <button onclick="promptRejection(<?php echo htmlspecialchars(json_encode($seller)); ?>)" class="btn btn-danger">Reject</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No pending sellers found.</p>
        <?php endif; ?>
    </div>

    <!-- Accepted Sellers Section -->
    <div id="accepted-section" class="pending-section" style="display: none;">
        <h2>Accepted Sellers</h2>
        <?php if (!empty($acceptedSellers)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($acceptedSellers as $seller): ?>
                        <tr>
                            <td>
                                <a href="admin_sellers_profile.php?id=<?php echo htmlspecialchars($seller['seller_ID']); ?>">
                                    <?php echo htmlspecialchars($seller['Fname'] . " " . $seller['Lname']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($seller['email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No accepted sellers found.</p>
        <?php endif; ?>
    </div>

    <!-- Rejected Sellers Section -->
    <div id="rejected-section" class="pending-section" style="display: none;">
        <h2>Rejected Sellers</h2>
        <?php if (!empty($rejectedSellers)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Rejection Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rejectedSellers as $seller): ?>
                        <tr>
                            <td>
                                <a href="admin_sellers_profile.php?id=<?php echo htmlspecialchars($seller['seller_ID']); ?>">
                                    <?php echo htmlspecialchars($seller['Fname'] . " " . $seller['Lname']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($seller['email']); ?></td>
                            <td><?php echo htmlspecialchars($seller['rejection_reason']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No rejected sellers found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function showSection(sectionId) {
        document.querySelectorAll('.pending-section').forEach(section => {
            section.style.display = 'none';
        });
        document.getElementById(sectionId).style.display = 'block';
    }

    function promptRejection(seller) {
        const reason = prompt("Please provide a reason for rejection:", "");
        if (reason !== null) {
            const form = document.createElement("form");
            form.method = "post";
            form.innerHTML = `
                <input type="hidden" name="sellerID" value="${seller.seller_ID}">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="rejection_reason" value="${reason}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
</body>
</html>
