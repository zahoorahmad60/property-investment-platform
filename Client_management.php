<?php
// Start session and include initialization file
session_start();
include 'init.php'; // This should contain your database connection

// Check if the user is logged in as a consultant
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'consultant') {
    header("Location: login.php"); // Redirect to login if not logged in as a consultant
    exit();
}

// Get the consultant ID from the session
$consultant_id = $_SESSION['user_id']; // Assuming 'user_id' stores the consultant's ID in the session

// Fetch the consultation requests from investors
$stmt_investors = $conn->prepare("
    SELECT 
        i.investor_ID, i.Fname AS investor_fname, i.Lname AS investor_lname, 
        i.email AS investor_email, i.phone AS investor_phone, 
        c.date, c.time, c.description
    FROM 
        Consultation c
    INNER JOIN 
        Investor i ON c.investor_ID = i.investor_ID
    WHERE 
        c.consultant_ID = :consultant_id
");
$stmt_investors->bindParam(':consultant_id', $consultant_id, PDO::PARAM_INT);
$stmt_investors->execute();
$investor_requests = $stmt_investors->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0D1B2A;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #415A77;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            margin-bottom: 10px;
        }
        .sidebar a:hover {
            background-color: #007bff;
        }
        .main-content {
            margin-left: 270px;
            padding: 20px;
            margin-top: 40px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #0D1B2A;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            color: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #415A77;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
            color: black;
        }
        tr:nth-child(odd) {
            background-color: #415A77;
            color: white;
        }
        h1 {
            text-align: center;
            color: white;
        }
        .btn {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
            display: inline-block;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .no-requests {
            text-align: center;
            font-size: 18px;
            color: #f9f9f9;
        }
    </style>
</head>
<body>

<!-- Sidebar (Optional: You can remove if not needed) -->
<div class="sidebar">
    <a href="dashboard.php">Dashboard</a>
    <a href="client_management.php">Client Management</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <h1>Client Management</h1>

        <h2>Investors Consultation Requests</h2>
        <?php if (count($investor_requests) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Investor Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($investor_requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['investor_fname'] . ' ' . $request['investor_lname']); ?></td>
                            <td><?php echo htmlspecialchars($request['investor_email']); ?></td>
                            <td><?php echo htmlspecialchars($request['investor_phone']); ?></td>
                            <td><?php echo htmlspecialchars($request['date']); ?></td>
                            <td><?php echo htmlspecialchars($request['time']); ?></td>
                            <td><?php echo htmlspecialchars($request['description']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-requests">No consultation requests from investors found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
