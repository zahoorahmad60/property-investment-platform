<?php
// Start the session
session_start();
$noNavbar = true;
include "init.php"; // Database connection
include "invester_sidebar.php";

// Get the consultant ID from the query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid consultant ID.";
    exit();
}

$consultant_id = intval($_GET['id']); // Validate and sanitize the ID

// Fetch consultant details
try {
    $stmt_consultant = $conn->prepare("SELECT * FROM consultant WHERE consultant_ID = :consultant_id");
    $stmt_consultant->bindParam(':consultant_id', $consultant_id, PDO::PARAM_INT);
    $stmt_consultant->execute();
    $consultant = $stmt_consultant->fetch(PDO::FETCH_ASSOC);

    if (!$consultant) {
        echo "Consultant not found.";
        exit();
    }

    // Fetch consultations associated with the consultant
    $stmt_consultations = $conn->prepare("
        SELECT c.session_number, c.date, c.time, c.description, c.status, c.rating, c.feedback, 
               c.zoom_link, c.paid, c.rejection_reason, i.Fname AS investor_fname, i.Lname AS investor_lname 
        FROM consultation c
        LEFT JOIN investor i ON c.investor_ID = i.investor_ID
        WHERE c.consultant_ID = :consultant_id
    ");
    $stmt_consultations->bindParam(':consultant_id', $consultant_id, PDO::PARAM_INT);
    $stmt_consultations->execute();
    $consultations = $stmt_consultations->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "Error fetching data: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultant Profile</title>
    <style>
        :root {
            --primary-color: #3DA5D9;
            --secondary-color: #2A3B4C;
            --background-color: #1D2939;
            --text-color: #E8E8E8;
            --card-bg-color: #1E2D3D;
            --table-border-color: #2C3E50;
        }

        body {
            display:block;
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

        h1, h2 {
            color: var(--primary-color);
            text-align: center;
            margin-top: 20px;
        }

        .profile-section, .consultation-section {
            background-color: var(--card-bg-color);
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
        }

        .profile-info p {
            font-size: 1.1em;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

            th, td {
                font-size: 0.9em;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <h1>Consultant Profile</h1>
    
    <div class="profile-section">
        <h2>Consultant Information</h2>
        <div class="profile-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($consultant['Fname'] . ' ' . $consultant['Lname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($consultant['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($consultant['phone']); ?></p>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($consultant['company']); ?></p>
            <p><strong>Experience (Years):</strong> <?php echo htmlspecialchars($consultant['experience']); ?></p>
            <p><strong>Fee:</strong> ﷼<?php echo number_format($consultant['fee'], 2); ?></p>

            
            <?php if (!empty($consultant['rejection_reason'])): ?>
                <p><strong>Rejection Reason:</strong> <?php echo htmlspecialchars($consultant['rejection_reason']); ?></p>
            <?php endif; ?>
        </div>
    </div>
<script>
    let sidebarOpen = true; // Set to true initially so the sidebar is open by default

    function toggleSidebar() {
        const sidebar = document.querySelector('.side-nav');
        if (sidebarOpen) {
            sidebar.style.left = '-250px';
            sidebarOpen = false;
        } else {
            sidebar.style.left = '0px';
            sidebarOpen = true;
        }
    }

    // Set the sidebar open on page load
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.querySelector('.side-nav');
        sidebar.style.left = '0px'; // Ensure sidebar is visible on load
    });
</script>
</body>
</html>
