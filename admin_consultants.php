<?php
// Start the session and output buffering
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

$pageTitle = "Consultant Management";

// Approve or reject consultant based on form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $consultantID = $_POST['consultantID'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        $stmt = $conn->prepare("UPDATE Consultant SET approve = 1 WHERE consultant_ID = ?");
        $stmt->execute([$consultantID]);
    } elseif ($action == 'reject') {
        $rejectionReason = $_POST['rejection_reason'] ?? 'No reason provided';
        $stmt = $conn->prepare("UPDATE Consultant SET approve = -1, rejection_reason = ? WHERE consultant_ID = ?");
        $stmt->execute([$rejectionReason, $consultantID]);
    }

    // Refresh to show updated lists
    header("Location: admin_consultants.php");
    exit();
}

// Fetch consultants by status
function getConsultantsByStatus($conn, $status) {
    $stmt = $conn->prepare("SELECT consultant_ID, Fname, Lname, email, rejection_reason FROM Consultant WHERE approve = ?");
    $stmt->execute([$status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pendingConsultants = getConsultantsByStatus($conn, 0);
$acceptedConsultants = getConsultantsByStatus($conn, 1);
$rejectedConsultants = getConsultantsByStatus($conn, -1);

// End output buffering and flush the output
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
    <h1>Consultant Management</h1>

    <div class="button-group">
        <button class="btn btn-warning" onclick="showSection('pending-section')">Pending</button>
        <button class="btn btn-success" onclick="showSection('accepted-section')">Accepted</button>
        <button class="btn btn-danger" onclick="showSection('rejected-section')">Rejected</button>
    </div>

    <!-- Pending Consultants Section -->
    <div id="pending-section" class="pending-section">
        <h2>Pending Consultants</h2>
        <?php if (!empty($pendingConsultants)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingConsultants as $consultant): ?>
                        <tr>
                            <td>
                                <a href="admin_consultant_profile.php?id=<?php echo htmlspecialchars($consultant['consultant_ID']); ?>">
                                    <?php echo htmlspecialchars($consultant['Fname'] . " " . $consultant['Lname']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($consultant['email']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="consultantID" value="<?php echo htmlspecialchars($consultant['consultant_ID']); ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success">Approve</button>
                                </form>
                                <button onclick="promptRejection(<?php echo htmlspecialchars(json_encode($consultant)); ?>)" class="btn btn-danger">Reject</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No pending consultants found.</p>
        <?php endif; ?>
    </div>

    <!-- Accepted Consultants Section -->
    <div id="accepted-section" class="pending-section" style="display: none;">
        <h2>Accepted Consultants</h2>
        <?php if (!empty($acceptedConsultants)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($acceptedConsultants as $consultant): ?>
                        <tr>
                            <td>
                                <a href="admin_consultant_profile.php?id=<?php echo htmlspecialchars($consultant['consultant_ID']); ?>">
                                    <?php echo htmlspecialchars($consultant['Fname'] . " " . $consultant['Lname']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($consultant['email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No accepted consultants found.</p>
        <?php endif; ?>
    </div>

    <!-- Rejected Consultants Section -->
    <div id="rejected-section" class="pending-section" style="display: none;">
        <h2>Rejected Consultants</h2>
        <?php if (!empty($rejectedConsultants)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Rejection Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rejectedConsultants as $consultant): ?>
                        <tr>
                            <td>
                                <a href="admin_consultant_profile.php?id=<?php echo htmlspecialchars($consultant['consultant_ID']); ?>">
                                    <?php echo htmlspecialchars($consultant['Fname'] . " " . $consultant['Lname']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($consultant['email']); ?></td>
                            <td><?php echo htmlspecialchars($consultant['rejection_reason']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No rejected consultants found.</p>
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

    function promptRejection(consultant) {
        const reason = prompt("Please provide a reason for rejection:", "");
        if (reason !== null) {
            const form = document.createElement("form");
            form.method = "post";
            form.innerHTML = `
                <input type="hidden" name="consultantID" value="${consultant.consultant_ID}">
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