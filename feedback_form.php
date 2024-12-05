<?php
session_start();
include 'init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'investor') {
    header("Location: login.php");
    exit();
}

$investor_id = $_SESSION['user_id'];
$stmt_pending = $conn->prepare("SELECT c.*, co.Fname, co.Lname, co.fee FROM consultation c JOIN consultant co ON c.consultant_ID = co.consultant_ID WHERE c.investor_ID = :investor_id AND c.status = 'approved' AND c.paid = 1 AND (c.rating IS NULL OR c.feedback IS NULL)");
$stmt_pending->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_pending->execute();
$pending_ratings = $stmt_pending->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Ratings</title>
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
            margin: 80px 50px 30px 250px;
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
        .review-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .review-btn:hover {
            background-color: #218838;
        }
        .side-nav {
            position: fixed;
            left: -250px;
            width: 250px;
            height: 100%;
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
    <h2>Feedback form</h2>
    <?php if ($pending_ratings): ?>
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Description</th>
                <th>Zoom Link</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($pending_ratings as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['date']); ?></td>
                    <td><?php echo htmlspecialchars($request['time']); ?></td>
                    <td><?php echo htmlspecialchars($request['description']); ?></td>
                    <td><a href="<?php echo htmlspecialchars($request['zoom_link']); ?>" target="_blank">Join Zoom</a></td>
                    <td>
                        <form action="leave_review.php" method="POST">
                            <input type="hidden" name="session_number" value="<?php echo $request['session_number']; ?>">
                            <label>Rating (1-5):</label>
                            <select name="rating" required>
                                <option value="">Select Rating</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                            <label>Feedback:</label>
                            <textarea name="feedback" rows="4" required></textarea>
                            <button type="submit" class="review-btn">Leave Review</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending ratings found.</p>
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
