<?php
session_start();
include 'init.php'; // Include your database connection

// Check if the user is logged in as an investor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'investor') {
    header("Location: login.php"); // Redirect to login if not logged in as investor
    exit();
}

// Fetch approved consultation requests for the logged-in investor
$investor_id = $_SESSION['user_id'];
$stmt_approved = $conn->prepare("SELECT c.*, co.Fname, co.Lname, co.fee FROM consultation c JOIN consultant co ON c.consultant_ID = co.consultant_ID WHERE c.investor_ID = :investor_id AND c.status = 'approved' AND c.paid = 0");
$stmt_approved->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_approved->execute();
$approved_requests = $stmt_approved->fetchAll(PDO::FETCH_ASSOC);

// Fetch disapproved consultation requests for the logged-in investor
$stmt_disapproved = $conn->prepare("SELECT * FROM consultation WHERE investor_ID = :investor_id AND status = 'disapproved'");
$stmt_disapproved->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_disapproved->execute();
$disapproved_requests = $stmt_disapproved->fetchAll(PDO::FETCH_ASSOC);

// Fetch approved and paid consultation requests for the logged-in investor
$stmt_paid = $conn->prepare("SELECT c.*, co.Fname, co.Lname, co.fee FROM consultation c JOIN consultant co ON c.consultant_ID = co.consultant_ID WHERE c.investor_ID = :investor_id AND c.status = 'approved' AND c.paid = 1");
$stmt_paid->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_paid->execute();
$paid_requests = $stmt_paid->fetchAll(PDO::FETCH_ASSOC);
// Handle payment process when an investor clicks 'Pay' on a consultation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['session_number'])) {
    $session_number = $_POST['session_number'];
    
    // Update the consultation record to mark it as paid
    $stmt_update = $conn->prepare("UPDATE consultation SET paid = 1 WHERE session_number = :session_number");
    $stmt_update->bindParam(':session_number', $session_number, PDO::PARAM_INT);
    
    if ($stmt_update->execute()) {
        // Redirect to the payment page
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
            padding: 20px;
        }
        .container {
    max-width: 1100px;
    margin: 1000px 50px 30px 250px; /* Increased top margin from 60px to 80px */
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
        .pay-btn, .review-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .pay-btn:hover, .review-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Approved Consultation Requests</h2>
    <?php if ($approved_requests): ?>
        <table>
            <thead>
                <tr>
                    <th>Consultant Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($approved_requests as $request): ?>
                    <tr>
                        <td>
                            <a href="consultant_profile.php?consultant_id=<?php echo $request['consultant_ID']; ?>" target="_blank">
                                <?php echo htmlspecialchars($request['Fname'] . ' ' . $request['Lname']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($request['date']); ?></td>
                        <td><?php echo htmlspecialchars($request['time']); ?></td>
                        <td><?php echo htmlspecialchars($request['description']); ?></td>
                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="session_number" value="<?php echo $request['session_number']; ?>">
                                <button type="submit" class="pay-btn">Pay</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-requests">No approved consultation requests found.</p>
    <?php endif; ?>

    <h2>Disapproved Consultation Requests</h2>
    <?php if ($disapproved_requests): ?>
        <table>
            <thead>
                <tr>
                    <th>Consultant Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Reason for Disapproval</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($disapproved_requests as $request): ?>
                    <tr>
                        <td>
                            <a href="consultant_profile.php?consultant_id=<?php echo $request['consultant_ID']; ?>" target="_blank">
                                <?php echo htmlspecialchars($request['Fname'] . ' ' . $request['Lname']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($request['date']); ?></td>
                        <td><?php echo htmlspecialchars($request['time']); ?></td>
                        <td><?php echo htmlspecialchars($request['description']); ?></td>
                        <td><?php echo htmlspecialchars($request['rejection_reason']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-requests">No disapproved consultation requests found.</p>
    <?php endif; ?>

    <h2>Paid Consultation Requests</h2>
    <?php if ($paid_requests): ?>
        <table>
            <thead>
                <tr>
                    <th>Consultant Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Zoom Link</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paid_requests as $request): ?>
                    <tr>
                        <td>
                            <a href="consultant_profile.php?consultant_id=<?php echo $request['consultant_ID']; ?>" target="_blank">
                                <?php echo htmlspecialchars($request['Fname'] . ' ' . $request['Lname']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($request['date']); ?></td>
                        <td><?php echo htmlspecialchars($request['time']); ?></td>
                        <td><?php echo htmlspecialchars($request['description']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($request['zoom_link']); ?>" target="_blank">Join Zoom</a></td>
                        <td>
                            <?php if (!is_null($request['rating']) && !is_null($request['feedback'])): ?>
                                <p><strong>Rating:</strong> <?php echo htmlspecialchars($request['rating']); ?></p>
                                <p><strong>Feedback:</strong> <?php echo htmlspecialchars($request['feedback']); ?></p>
                            <?php else: ?>
                                <form action="leave_review.php" method="POST">
                                    <input type="hidden" name="session_number" value="<?php echo $request['session_number']; ?>">
                                    <div class="form-group">
                                        <label for="rating">Rating (1-5):</label>
                                        <select name="rating" required>
                                            <option value="">Select Rating</option>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="feedback">Feedback:</label>
                                        <textarea name="feedback" rows="4" required></textarea>
                                    </div>
                                    <button type="submit" class="review-btn">Leave Review</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-requests">No paid consultation requests found.</p>
    <?php endif; ?>
</div>
<?php include 'invester_sidebar.php'; ?>
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
