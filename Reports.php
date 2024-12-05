<?php
// Start session
session_start();

// Check if the user is logged in as a consultant
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'consultant') {
    header("Location: login.php");
    exit();
}

// Include necessary files
$noNavbar = true;
$pageTitle = "Consultation Reports and Dashboard";
include "init.php"; // Database connection
include "consultant_sidebar.php";

// Check if ConsultantID is stored in session
if (!isset($_SESSION['user_id'])) {
    die("Consultant ID not found in session.");
}

$consultant_id = $_SESSION['user_id']; // Consultant ID

// Fetch statistics for the dashboard

// Total number of consultation requests
$stmt_total_requests = $conn->prepare("SELECT COUNT(*) FROM Consultation WHERE consultant_ID = :consultant_id");
$stmt_total_requests->execute(['consultant_id' => $consultant_id]);
$total_requests = $stmt_total_requests->fetchColumn();

// Total number of consultations handled
$stmt_handled = $conn->prepare("SELECT COUNT(*) FROM Consultation WHERE consultant_ID = :consultant_id AND feedback IS NOT NULL");
$stmt_handled->execute(['consultant_id' => $consultant_id]);
$total_handled = $stmt_handled->fetchColumn();

// Average rating
$stmt_avg_rating = $conn->prepare("SELECT AVG(rating) FROM Consultation WHERE consultant_ID = :consultant_id AND rating IS NOT NULL");
$stmt_avg_rating->execute(['consultant_id' => $consultant_id]);
$avg_rating = $stmt_avg_rating->fetchColumn() ?? 0;

// Total number of investors handled
$stmt_investors = $conn->prepare("SELECT COUNT(DISTINCT investor_ID) FROM Consultation WHERE consultant_ID = :consultant_id AND investor_ID IS NOT NULL");
$stmt_investors->execute(['consultant_id' => $consultant_id]);
$total_investors = $stmt_investors->fetchColumn();

// Fetch reports for the logged-in consultant
$stmt_investor_reports = $conn->prepare("
    SELECT 
        c.session_number, 
        i.FName AS investor_fname, 
        i.LName AS investor_lname, 
        c.date,
        c.time,
        c.description,
        c.feedback,
        c.rating
    FROM 
        Consultation c 
    INNER JOIN 
        Investor i ON c.investor_ID = i.investor_ID 
    WHERE 
        c.consultant_ID = :consultant_id AND c.investor_ID IS NOT NULL
    ORDER BY 
        c.date DESC
");
$stmt_investor_reports->execute(['consultant_id' => $consultant_id]);
$investor_reports = $stmt_investor_reports->fetchAll(PDO::FETCH_ASSOC);
?>
        
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation Dashboard</title>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTTXrXjoP4UA6X6dtgX6IRY/t1kE8pZZXYjOn57AWtJsk2EXd/ZgpLT8NMMx1iN/pg1ERxP8Ng==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Google Fonts for Modern Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

    <style>
        /* Root Variables for Easy Customization */
        :root {
            --primary-color: #3DA5D9;
            --secondary-color: #0D1B2A;
            --accent-color: #007bff;
            --background-color: #1B263B;
            --text-color: #FFFFFF;
            --hover-color: #0056b3;
            --box-shadow: rgba(0, 0, 0, 0.1);
            --card-background: #263238;
            --font-family: 'Roboto', sans-serif;
            --card-padding: 15px;
            --card-margin-bottom: 15px;
            --stat-icon-size: 1.5em;
            --transition-speed: 0.3s;
            --sidebar-width: 200px;
        }

        /* Basic Reset and Typography */
        body {
            display:block;
            font-family: Arial, sans-serif;
            background-color: #0D1B2A;
            margin: 0;
            padding: 0;
            color: white;
            text-align: center;
        }

        /* Main Content Styling */
        .main-content {
            flex: 1;
            padding: 20px 150px 20px 150px;
            margin-top: 80px; /* Adjust for top gap */
            box-sizing: border-box;
            background-color: #1B263B; /* Slightly different to distinguish from sidebar */
        }

        /* Heading Styles */
        .main-content h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
            color: var(--primary-color);
        }

        .main-content h2 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .main-content h3 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        /* Statistics Section */
        .statistics {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: space-between;
    margin-bottom: 40px;
    margin-top: 80px; /* Adds space from the top */
}

        .stat-card {
            background-color: var(--card-background);
            padding: var(--card-padding);
            border-radius: 5px;
            text-align: center;
            flex: 1 1 calc(50% - 20px);
            box-shadow: 0 0 5px var(--box-shadow);
            transition: background-color var(--transition-speed), transform var(--transition-speed);
        }

        .stat-card i {
            font-size: var(--stat-icon-size);
            margin-bottom: 10px;
            color: var(--accent-color);
        }

        .stat-card h3 {
            margin: 0;
            font-size: 1.8rem;
        }

        .stat-card p {
            margin: 5px 0 0;
            font-size: 1rem;
        }

        .stat-card:hover {
            background-color: var(--hover-color);
            transform: translateY(-3px);
        }

        /* Consultation Reports Section */
        .reports {
            margin-top: 20px;
        }

        .report-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
ul{
    align-items: flex-start;
}
        .report-item {
            background-color: var(--card-background);
            padding: var(--card-padding);
            border-radius: 5px;
            margin-bottom: 15px;
            box-shadow: 0 0 5px var(--box-shadow);
            transition: background-color var(--transition-speed), transform var(--transition-speed);
        }

        .report-item:hover {
            background-color: var(--hover-color);
            transform: translateY(-3px);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .report-header h4 {
            margin: 0;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .report-date {
            font-size: 0.9rem;
            color: #ccc;
        }

        .report-description {
            font-size: 1rem;
            margin-bottom: 10px;
            color: var(--text-color);
        }

        .report-feedback {
            font-size: 0.9rem;
            color: #bbb;
        }

        /* Rating Stars Styling */
        .rating {
            display: flex;
            align-items: center;
            font-size: 1rem;
            color: #ffdd00;
            margin-bottom: 5px;
        }
        .rating .fa-star {
        color: #ccc; /* Unfilled star color */
    }
    .rating .fa-star.filled {
        color: #FFD700; /* Filled star color (gold) */
    }

        .rating i {
            margin-right: 2px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-content">
            <h1>Consultation Dashboard</h1>

            <div class="statistics">
                <div class="stat-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Total Requests</h3>
                    <p><?php echo $total_requests; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-check-double"></i>
                    <h3>Handled Requests</h3>
                    <p><?php echo $total_handled; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-star"></i>
                    <h3>Average Rating</h3>
                    <p><?php echo number_format($avg_rating, 2); ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-user"></i>
                    <h3>Total Investors</h3>
                    <p><?php echo $total_investors; ?></p>
                </div>
            </div>

            <h2>Consultation Reports</h2>
            <div class="reports">
                <ul class="report-list">
                    <?php foreach ($investor_reports as $report): ?>
                        <li class="report-item">
                            <div class="report-header">
                                <h4>Session #<?php echo htmlspecialchars($report['session_number']); ?> - <?php echo htmlspecialchars($report['investor_fname'] . ' ' . $report['investor_lname']); ?></h4>
                                <span class="report-date"><?php echo htmlspecialchars($report['date']) . ' at ' . htmlspecialchars($report['time']); ?></span>
                            </div>
                            <div class="rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa fa-star <?php echo $i <= $report['rating'] ? 'filled' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="report-description"><?php echo htmlspecialchars($report['description']); ?></p>
                            <p class="report-feedback"><strong>Feedback:</strong> <?php echo htmlspecialchars($report['feedback']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
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
