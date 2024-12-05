<?php
// Start the session and include necessary files
session_start();
$noNavbar = true;
include 'init.php'; // Database connection
include 'admin_sidebar.php'; // Sidebar navigation

// Check if the user is logged in as an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$pageTitle = "Consultation Sessions";

// Determine session status (default: pending)
$status = isset($_GET['status']) && in_array($_GET['status'], ['pending', 'approved', 'disapproved']) 
          ? $_GET['status'] 
          : 'pending';

// Fetch consultation sessions based on the status
try {
    $stmt = $conn->prepare("
        SELECT c.session_number, c.date, c.time, c.description, c.rating, c.feedback, c.status, c.zoom_link, c.paid, c.rejection_reason,
               inv.investor_ID, CONCAT(inv.Fname, ' ', inv.Lname) AS investor_name,
               con.consultant_ID, CONCAT(con.Fname, ' ', con.Lname) AS consultant_name
        FROM consultation c
        JOIN investor inv ON c.investor_ID = inv.investor_ID
        JOIN consultant con ON c.consultant_ID = con.consultant_ID
        WHERE c.status = :status
    ");
    $stmt->bindParam(':status', $status);
    $stmt->execute();
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $sessions = [];
    $debug_message = "Error fetching sessions: " . $e->getMessage();
}
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
    position: fixed; /* Keeps the .wrapper fixed at the top */
    top: 80px; /* Adjusted to account for the height of the navbar */
    left: 300px; /* Adjust for any sidebar or layout requirements */
    padding: 0 100px;
    width: calc(100% - 340px); /* Account for left padding + sidebar */
    box-sizing: border-box;
    background-color: #1D2939; /* Optional: Add a background color if content scrolls underneath */
    z-index: 1000; /* Ensure it stays above other content */
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
            color: white;
            text-decoration: none;
        }

        .button-group .btn-warning {
            background-color: #f39c12;
        }

        .button-group .btn-warning:hover {
            background-color: #e67e22;
        }

        .button-group .btn-success {
            background-color: var(--success-color);
        }

        .button-group .btn-success:hover {
            background-color: #27ae60;
        }

        .button-group .btn-danger {
            background-color: var(--danger-color);
        }

        .button-group .btn-danger:hover {
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

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--card-bg-color);
            padding: 20px;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            color: var(--text-color);
            position: relative;
        }

        .modal-content h3 {
            margin-top: 0;
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: var(--text-color);
            font-size: 1.5em;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <h1>Consultation Sessions</h1>
    <?php if (isset($debug_message)): ?>
        <div style="color: red; font-weight: bold;"><?php echo htmlspecialchars($debug_message); ?></div>
    <?php endif; ?>

    <!-- Status navigation buttons -->
    <div class="button-group">
        <a href="?status=pending" class="btn btn-warning">Pending</a>
        <a href="?status=approved" class="btn btn-success">Approved</a>
        <a href="?status=disapproved" class="btn btn-danger">Disapproved</a>
    </div>

    <!-- Display consultation sessions -->
    <div class="pending-section">
        <h2><?php echo ucfirst(htmlspecialchars($status)); ?> Sessions</h2>
        <?php if (!empty($sessions)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Session #</th>
                        <th>Consultant</th>
                        <th>Investor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Description</th>
                        <?php if ($status == 'approved'): ?>
                            <th>Paid</th>
                            <th>Rating</th>
                            <th>Feedback</th>
                        <?php elseif ($status == 'disapproved'): ?>
                            <th>Rejection Reason</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $session): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($session['session_number']); ?></td>
                            <!-- Consultant link -->
                            <td>
                                <a href="admin_consultant_profile.php?id=<?php echo htmlspecialchars($session['consultant_ID']); ?>">
                                    <?php echo htmlspecialchars($session['consultant_name']); ?>
                                </a>
                            </td>
                            <!-- Investor link -->
                            <td>
                                <a href="admin_investor_profile.php?id=<?php echo htmlspecialchars($session['investor_ID']); ?>">
                                    <?php echo htmlspecialchars($session['investor_name']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($session['date']); ?></td>
                            <td><?php echo htmlspecialchars($session['time']); ?></td>
                            <td><?php echo htmlspecialchars($session['description']); ?></td>
                            <?php if ($status == 'approved'): ?>
                                <td><?php echo $session['paid'] ? 'Yes' : 'No'; ?></td>
                                <td><?php echo htmlspecialchars($session['rating']); ?></td>
                                <td><?php echo htmlspecialchars($session['feedback']); ?></td>
                            <?php elseif ($status == 'disapproved'): ?>
                                <td><?php echo htmlspecialchars($session['rejection_reason']); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No <?php echo htmlspecialchars($status); ?> sessions available.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
