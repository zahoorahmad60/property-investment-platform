<?php
session_start();
include 'init.php'; // Include your database connection

// Check if the user is logged in as an investor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'investor') {
    header("Location: login.php");
    exit();
}

$investor_id = $_SESSION['user_id'];

// Fetch approved consultation requests for the logged-in investor
$stmt_approved = $conn->prepare("
    SELECT c.session_number, c.date, c.time, c.description, c.zoom_link, c.paid,
           co.Fname AS consultant_fname, co.Lname AS consultant_lname, co.fee 
    FROM consultation c
    JOIN consultant co ON c.consultant_ID = co.consultant_ID
    WHERE c.investor_ID = :investor_id AND c.status = 'approved' AND c.paid = 0
");
$stmt_approved->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_approved->execute();
$approved_sessions = $stmt_approved->fetchAll(PDO::FETCH_ASSOC);

// Fetch disapproved consultation requests for the logged-in investor
$stmt_disapproved = $conn->prepare("
    SELECT c.session_number, c.date, c.time, c.description, c.rejection_reason
    FROM consultation c
    WHERE c.investor_ID = :investor_id AND c.status = 'disapproved'
");
$stmt_disapproved->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_disapproved->execute();
$disapproved_sessions = $stmt_disapproved->fetchAll(PDO::FETCH_ASSOC);

// Handle payment process when an investor clicks 'Pay' on a consultation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['session_number'])) {
    $session_number = $_POST['session_number'];

    // Update the consultation record to mark it as paid
    $stmt_update = $conn->prepare("UPDATE consultation SET paid = 1 WHERE session_number = :session_number");
    $stmt_update->bindParam(':session_number', $session_number, PDO::PARAM_INT);

    if ($stmt_update->execute()) {
        echo "<script>alert('Payment successful for session number $session_number');</script>";
        header("Location: investor_session_approved_disapproved.php");
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
    <title>My Consultation Sessions</title>
    <link rel="stylesheet" href="layout/css/reg.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0D1B2A;
            color: white;
            padding: 20px;
        }
        .container {
            max-width: 1100px;
            margin: 100px auto;
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
        .pay-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .pay-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Approved Sessions (Pending Payment)</h2>
    <?php if ($approved_sessions): ?>
        <table>
            <thead>
            <tr>
                <th>Session #</th>
                <th>Date</th>
                <th>Time</th>
                <th>Description</th>
                <th>Consultant</th>
                <th>Fee</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($approved_sessions as $session): ?>
                <tr>
                    <td><?php echo htmlspecialchars($session['session_number']); ?></td>
                    <td><?php echo htmlspecialchars($session['date']); ?></td>
                    <td><?php echo htmlspecialchars($session['time']); ?></td>
                    <td><?php echo htmlspecialchars($session['description']); ?></td>
                    <td><?php echo htmlspecialchars($session['consultant_fname'] . ' ' . $session['consultant_lname']); ?></td>
                    <td>﷼<?php echo htmlspecialchars(number_format($session['fee'], 2)); ?></td>
                    <td>
                        <?php if ($session['paid'] == 0): ?>
                            <form action="" method="POST">
                                <input type="hidden" name="session_number" value="<?php echo $session['session_number']; ?>">
                                <button type="submit" class="pay-btn">Pay</button>
                            </form>
                        <?php else: ?>
                            <span>Paid</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No approved sessions awaiting payment.</p>
    <?php endif; ?>

    <h2>Disapproved Sessions</h2>
    <?php if ($disapproved_sessions): ?>
        <table>
            <thead>
            <tr>
                <th>Session #</th>
                <th>Date</th>
                <th>Time</th>
                <th>Description</th>
                <th>Reason for Disapproval</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($disapproved_sessions as $session): ?>
                <tr>
                    <td><?php echo htmlspecialchars($session['session_number']); ?></td>
                    <td><?php echo htmlspecialchars($session['date']); ?></td>
                    <td><?php echo htmlspecialchars($session['time']); ?></td>
                    <td><?php echo htmlspecialchars($session['description']); ?></td>
                    <td><?php echo htmlspecialchars($session['rejection_reason']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No disapproved sessions available.</p>
    <?php endif; ?>
</div>

<?php include 'invester_sidebar.php'; ?>
</body>
</html>
