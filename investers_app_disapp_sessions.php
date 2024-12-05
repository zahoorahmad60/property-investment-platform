<?php
session_start();
include 'init.php'; // Include your database connection

// Check if the user is logged in as an investor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'investor') {
    header("Location: login.php"); // Redirect to login if not logged in as investor
    exit();
}

// Fetch the logged-in investor's ID
$investor_id = $_SESSION['user_id'];

// Fetch approved consultation requests for payment
$stmt_approved = $conn->prepare("
    SELECT c.consultant_ID,c.session_number, c.date, c.time, c.description, c.zoom_link, 
           co.Fname AS consultant_fname, co.Lname AS consultant_lname, co.fee
    FROM consultation c
    JOIN consultant co ON c.consultant_ID = co.consultant_ID
    WHERE c.investor_ID = :investor_id AND c.status = 'approved' AND c.paid = 0
");
$stmt_approved->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_approved->execute();
$approved_sessions = $stmt_approved->fetchAll(PDO::FETCH_ASSOC);

// Fetch disapproved consultation requests
$stmt_disapproved = $conn->prepare("
      SELECT c.consultant_ID,c.session_number, c.date, c.time, c.description,c.rejection_reason ,c.zoom_link,
           co.Fname AS consultant_fname, co.Lname AS consultant_lname, co.fee
    FROM consultation c
    JOIN consultant co ON c.consultant_ID = co.consultant_ID
    WHERE c.investor_ID = :investor_id AND c.status = 'disapproved'
");
$stmt_disapproved->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_disapproved->execute();
$disapproved_sessions = $stmt_disapproved->fetchAll(PDO::FETCH_ASSOC);

// Handle payment process when an investor clicks 'Pay'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['session_number'])) {
    $session_number = $_POST['session_number'];

    // Update the consultation record to mark it as paid
    $stmt_update = $conn->prepare("UPDATE consultation SET paid = 1 WHERE session_number = :session_number");
    $stmt_update->bindParam(':session_number', $session_number, PDO::PARAM_INT);

    if ($stmt_update->execute()) {
        // Redirect to payment page
        header("Location: payment.php?session_number=$session_number");
        exit();
    } else {
        echo "<script>alert('Payment failed. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation Requests</title>
    <link rel="stylesheet" href="layout/css/reg.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0D1B2A;
            color: white;
            margin: 0; /* Remove default body margin */
            padding: 0; /* Remove default body padding */
        }

        .container {
            max-width: calc(100% - 300px); /* Adjust width considering sidebar */
            margin: 100px 50px 30px 300px; /* Distance from top (navbar) and left (sidebar) */
            padding: 20px;
            background-color: #415A77;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #007bff;
        }

        .zoom-link {
            color: #28a745;
            text-decoration: none;
            font-weight: bold;
        }

        .zoom-link:hover {
            text-decoration: underline;
        }

        .side-nav {
            position: fixed;
            top: 100px; /* Distance from top to avoid overlapping navbar */
            left: 0;
            width: 250px;
            height: calc(100% - 100px); /* Full height minus navbar space */
            background-color: #1B263B;
            color: white;
            transition: 0.3s;
            padding-top: 20px;
        }

        .side-nav a {
            padding: 10px 20px;
            display: block;
            color: white;
            text-decoration: none;
        }

        .side-nav a:hover {
            background-color: #0D1B2A;
        }

        .toggle-btn {
            position: fixed;
            left: 10px;
            top: 10px;
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .toggle-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Approved Consultation Requests</h2>
    <?php if ($approved_sessions): ?>
        <table>
            <thead>
            <tr>
                <th>Consultant</th>
                <th>Date</th>
                <th>Time</th>
                <th>Description</th>
                <th>Fee</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($approved_sessions as $session): ?>
                <tr>
                    <td><a href="invester_consultant_profile.php?id=<?php echo htmlspecialchars($session['consultant_ID']); ?>">
                            <?php echo htmlspecialchars($session['consultant_fname'] . " " . $session['consultant_lname']); ?>
                        </a></td>
                    <td><?php echo htmlspecialchars($session['date']); ?></td>
                    <td><?php echo htmlspecialchars($session['time']); ?></td>
                    <td><?php echo htmlspecialchars($session['description']); ?></td>
                    <td>﷼<?php echo number_format($session['fee'], 2); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="session_number" value="<?php echo $session['session_number']; ?>">
                            <button type="submit" class="pay-btn">Pay</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No approved consultation requests found.</p>
    <?php endif; ?>

    <h2>Disapproved Consultation Requests</h2>
    <?php if ($disapproved_sessions): ?>
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Consultant</th>
                <th>Time</th>
                <th>Description</th>
                <th>Reason for Disapproval</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($disapproved_sessions as $session): ?>
                <tr>
                    <td><?php echo htmlspecialchars($session['date']); ?></td>
                    <td><a href="invester_consultant_profile.php?id=<?php echo htmlspecialchars($session['consultant_ID']); ?>">
                            <?php echo htmlspecialchars($session['consultant_fname'] . " " . $session['consultant_lname']); ?>
                        </a></td>
                    <td><?php echo htmlspecialchars($session['time']); ?></td>
                    <td><?php echo htmlspecialchars($session['description']); ?></td>
                    <td><?php echo htmlspecialchars($session['rejection_reason']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No disapproved consultation requests found.</p>
    <?php endif; ?>
</div>
<?php include 'invester_sidebar.php'; ?>
<script>
    let sidebarOpen = true;

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

    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.querySelector('.side-nav');
        sidebar.style.left = '0px';
    });
</script>
</body>
</html>
