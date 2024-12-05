<?php
session_start();
$noNavbar = true;
include "init.php"; // Include your database connection
include "invester_sidebar.php";

// Check if the consultant_id is provided
if (isset($_GET['consultant_id'])) {
    $consultant_id = intval($_GET['consultant_id']);
} else {
    echo "No consultant selected.";
    exit();
}

// Fetch consultant details for display
$stmt = $conn->prepare("SELECT Fname, Lname FROM Consultant WHERE consultant_ID = :consultant_id");
$stmt->bindParam(':consultant_id', $consultant_id, PDO::PARAM_INT);
$stmt->execute();
$consultant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consultant) {
    echo "Consultant not found.";
    exit();
}

// Check if the user is logged in as an investor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'investor') {
    header("Location: login.php"); // Redirect to login if not logged in as investor
    exit();
}

// Handle consultation booking form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['invester_book_consultation'])) {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $description = $_POST['description'];
    $investor_id = $_SESSION['user_id'];
    
    // Server-side validation for date and time
    $appointment_datetime = strtotime("$date $time");
    $current_datetime = time();
    
    if ($appointment_datetime <= $current_datetime) {
        echo "<script>alert('You cannot book an appointment in the past.');</script>";
    } else {
        // Insert the consultation data into the Consultation table
        $stmt_insert = $conn->prepare("INSERT INTO Consultation (investor_ID, consultant_ID, date, time, description, status) 
                                       VALUES (:investor_id, :consultant_id, :date, :time, :description, 'pending')");
        $stmt_insert->bindParam(':investor_id', $investor_id);
        $stmt_insert->bindParam(':consultant_id', $consultant_id);
        $stmt_insert->bindParam(':date', $date);
        $stmt_insert->bindParam(':time', $time);
        $stmt_insert->bindParam(':description', $description);

        try {
            $stmt_insert->execute();
            echo "<script>
                    alert('Your request for consultation has been sent. Please wait until approval.');
                    window.location.href = 'invester_h.php';
                  </script>";
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Consultation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #0D1B2A;
            color: white;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(45deg, #1B263B, #0D1B2A);
        }

        .container {
            width: 70%; /* Wider form */
            max-width: 900px;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.1); /* Transparent background */
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
        }

        h1 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            font-size: 2em;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px; /* Space between form elements */
        }

        label {
            font-size: 18px;
            font-weight: bold;
        }

        input, textarea {
            padding: 15px;
            font-size: 16px;
            border-radius: 10px;
            border: 2px solid #ccc;
            margin-top: 5px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="date"], input[type="time"] {
            font-size: 16px;
        }

        button {
            padding: 15px;
            font-size: 18px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        small {
            color: #ccc;
            font-size: 14px;
            margin-top: -10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Book Consultation with <?php echo htmlspecialchars($consultant['Fname'] . ' ' . $consultant['Lname']); ?></h1>

    <form action="invester_book_consultation.php?consultant_id=<?php echo $consultant_id; ?>" method="POST">
        <label for="date">Select Date:</label>
        <input type="date" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>">

        <label for="time">Select Time:</label>
        <input type="time" id="time" name="time" required min="<?php echo date('H:i'); ?>" max="23:50" step="600" />

        <small>Session is one hour</small> <!-- Note about session duration -->

        <label for="description">Description (optional):</label>
        <textarea id="description" name="description" placeholder="Provide a short description..." rows="5"></textarea>

        <button type="submit" name="invester_book_consultation">Book Consultation</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('date');
        const timeInput = document.getElementById('time');

        dateInput.addEventListener('change', function() {
            const selectedDate = new Date(dateInput.value);
            const today = new Date();
            
            // Check if selected date is today and adjust the min time
            if (selectedDate.toDateString() === today.toDateString()) {
                const currentHours = today.getHours();
                const currentMinutes = Math.ceil(today.getMinutes() / 10) * 10; // Round up to nearest 10
                timeInput.min = `${String(currentHours).padStart(2, '0')}:${String(currentMinutes).padStart(2, '0')}`;
            } else {
                timeInput.min = "00:00"; // Reset min time for future dates
            }
        });
    });
</script>
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
