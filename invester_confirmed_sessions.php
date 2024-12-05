<?php

// Start the session if it is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'init.php'; // Database connection
include 'invester_sidebar.php';

// Replace with the actual logged-in investor ID
$investor_id = $_SESSION['user_id']; // Use session variable or fallback for testing

// Fetch confirmed consultation sessions (approved, paid, pending feedback)
$stmt_confirmed = $conn->prepare("
    SELECT c.*, co.Fname, co.Lname, co.consultant_ID, co.fee 
    FROM consultation c 
    JOIN consultant co ON c.consultant_ID = co.consultant_ID 
    WHERE c.investor_ID = :investor_id AND c.status = 'approved' AND c.paid = 1 AND (c.rating IS NULL OR c.feedback IS NULL)
");
$stmt_confirmed->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_confirmed->execute();
$confirmed_sessions = $stmt_confirmed->fetchAll(PDO::FETCH_ASSOC);

// Fetch completed consultation sessions (approved, paid, feedback provided)
$stmt_completed = $conn->prepare("
    SELECT c.*, co.Fname, co.Lname, co.consultant_ID, co.fee 
    FROM consultation c 
    JOIN consultant co ON c.consultant_ID = co.consultant_ID 
    WHERE c.investor_ID = :investor_id AND c.status = 'approved' AND c.paid = 1 AND c.rating IS NOT NULL AND c.feedback IS NOT NULL
");
$stmt_completed->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt_completed->execute();
$completed_sessions = $stmt_completed->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Consultation Sessions</title>
    <link rel="stylesheet" href="layout/css/reg.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTTXrXjoP4UA6X6dtgX6IRY/t1kE8pZZXYjOn57AWtJsk2EXd/ZgpLT8NMMx1iN/pg1ERxP8Ng==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
    <h2>Confirmed Sessions</h2>
    <?php if ($confirmed_sessions): ?>
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Consultant</th>
                <th>Time</th>
                <th>Description</th>
                <th>Zoom Link</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($confirmed_sessions as $session): ?>
                <tr>
                    <td><?php echo htmlspecialchars($session['date']); ?></td>
                    <td>
                        <a href="invester_consultant_profile.php?id=<?php echo htmlspecialchars($session['consultant_ID']); ?>">
                            <?php echo htmlspecialchars($session['Fname'] . " " . $session['Lname']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($session['time']); ?></td>
                    <td><?php echo htmlspecialchars($session['description']); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($session['zoom_link']); ?>" target="_blank">Join Zoom</a>
                    </td>
                    <td>
                    <?php if (!is_null($session['rating']) && !is_null($session['feedback'])): ?>
                        <p><strong>Rating:</strong> <?php echo htmlspecialchars($session['rating']); ?></p>
                        <p><strong>Feedback:</strong> <?php echo htmlspecialchars($session['feedback']); ?></p>
                    <?php else: ?>
                        <form action="leave_review.php" method="POST">
                            <input type="hidden" name="session_number" value="<?php echo $session['session_number']; ?>">
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
        <p>No confirmed sessions found.</p>
    <?php endif; ?>

    <h2>Completed Sessions</h2>
    <?php if ($completed_sessions): ?>
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Consultant</th>
                <th>Time</th>
                <th>Description</th>
                <th>Zoom Link</th>
                <th>Rating</th>
                <th>Feedback</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($completed_sessions as $session): ?>
                <tr>
                    <td><?php echo htmlspecialchars($session['date']); ?></td>
                    <td>
                        <a href="invester_consultant_profile.php?id=<?php echo htmlspecialchars($session['consultant_ID']); ?>">
                            <?php echo htmlspecialchars($session['Fname'] . " " . $session['Lname']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($session['time']); ?></td>
                    <td><?php echo htmlspecialchars($session['description']); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($session['zoom_link']); ?>" target="_blank">Join Zoom</a>
                    </td>
                    <td>
                    <div class="rating">
        <?php 
        $rating = (int)$session['rating']; // Assuming 'rating' is a number from 1 to 5
        for ($i = 1; $i <= 5; $i++): 
            $starClass = $i <= $rating ? 'filled' : 'star';
        ?>
            <span class="<?php echo $starClass; ?>">★</span>
        <?php endfor; ?>
    </div>
                    </td>
                    <td><?php echo htmlspecialchars($session['feedback']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No completed sessions found.</p>
    <?php endif; ?>
</div>
<script>
    let sidebarOpen = true;

    function toggleSidebar() {
        const sidebar = document.querySelector('.side-nav');
        sidebar.style.left = sidebarOpen ? '-250px' : '0px';
        sidebarOpen = !sidebarOpen;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.querySelector('.side-nav');
        sidebar.style.left = '0px'; // Ensure sidebar is visible on load
    });
</script>
</body>
</html>
