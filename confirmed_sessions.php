<?php
session_start();
include 'init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'investor') {
    header("Location: login.php");
    exit();
}

$investor_id = $_SESSION['user_id'];
$stmt_completed = $conn->prepare("SELECT c.*, co.Fname, co.Lname, co.fee FROM consultation c JOIN consultant co ON c.consultant_ID = co.consultant_ID WHERE c.investor_ID = :investor_id AND c.status = 'approved' AND c.paid = 1 AND c.rating IS NOT NULL AND c.feedback IS NOT NULL");
$stmt_completed->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_completed->execute();
$completed_ratings = $stmt_completed->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirmed Sessions and Ratings</title>
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
    margin: 120px auto 30px auto; /* Adjusted top margin for extra space */
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
        .rating {
    display: inline-block;
    font-size: 1.5em;
    color: #ffd700; /* Gold color for stars */
}
.rating .star {
    color: #ccc; /* Grey color for empty stars */
}
.rating .filled {
    color: #ffd700; /* Gold color for filled stars */
}

    </style>
</head>
<body>
<div class="container">
    <h2>Completed Ratings</h2>
    <?php if ($completed_ratings): ?>
        <table>
            <thead>
            <tr>
                <th>Consultation ID</th>
                <th>Date</th>
                <th>Time</th>
                <th>Description</th>
                <th>Zoom Link</th>
                <th>Rating</th>
                <th>Feedback</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($completed_ratings as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['session_number']); ?></td>
                    <td><?php echo htmlspecialchars($request['date']); ?></td>
                    <td><?php echo htmlspecialchars($request['time']); ?></td>
                    <td><?php echo htmlspecialchars($request['description']); ?></td>
                    <td><a href="<?php echo htmlspecialchars($request['zoom_link']); ?>" target="_blank">Join Zoom</a></td>
                    <td>
    <div class="rating">
        <?php 
        $rating = (int)$request['rating']; // Assuming 'rating' is a number from 1 to 5
        for ($i = 1; $i <= 5; $i++): 
            $starClass = $i <= $rating ? 'filled' : 'star';
        ?>
            <span class="<?php echo $starClass; ?>">★</span>
        <?php endfor; ?>
    </div>
</td>

                    <td><?php echo htmlspecialchars($request['feedback']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No completed ratings found.</p>
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
