<?php
session_start();
$noNavbar = true;
include "init.php"; // Include your database connection
include "invester_sidebar.php";

// Check if the consultant_id is provided
if (isset($_SESSION['user_id'])) {
    $consultant_id = intval($_SESSION['user_id']);
} else {
    echo "No consultant selected.";
    exit();
}
if (isset($_POST['session_number'])) {
    $session_number = intval($_POST['session_number']);
} else {
    echo "No session number selected.";
    exit();
}
// Fetch consultant details for display, including fee
$stmt = $conn->prepare("SELECT Fname, Lname, fee FROM Consultant WHERE consultant_ID = :consultant_id");
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

// Handle payment and consultation data insertion on the payment page
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_consultation'])) {
    $session_number = $_POST['session_number'];

    $stmt_update = $conn->prepare("UPDATE consultation SET paid = 1 WHERE session_number = :session_number");
    $stmt_update->bindParam(':session_number', $session_number);

    if ($stmt_update->execute()) {
        echo "<script>alert('Payment successful. You will receive a Zoom link shortly.'); window.location.href = 'investor_consultations.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to update consultation status. Please try again.');window.location.href = 'investor_consultations.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invest in Property</title>
    <link rel="stylesheet" href="layout/css/reg.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0D1B2A;
            margin: 0;
            padding: 20px;
            color: white;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #415A77;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .submit-btn {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
        .percentage-display {
            font-size: 16px;
            margin-top: -10px;
            margin-bottom: 20px;
            color: #FFD700;
        }
    </style>
</head>
<body>

<div class="container">   
    <h2>Consultation Payment</h2>
    <p>Consultation with <?php echo htmlspecialchars($consultant['Fname'] . ' ' . $consultant['Lname']); ?></p>
    <p>Consultation Fee: SAR <?php echo htmlspecialchars($consultant['fee']); ?></p>
    
    <form action="" method="post">
        <div class="form-group">
            <label for="amount_paid">Amount Paid (SAR):</label>
            <input type="number" id="amount_paid" name="amount_paid" step="0.01" value="<?php echo htmlspecialchars($consultant['fee']); ?>" readonly required>
        </div>
        
        <input type="hidden" name="session_number" value="<?php echo $session_number; ?>">
        <input type="submit" value="Proceed to Payment" class="submit-btn" name="book_consultation">
    </form>
</div>
<script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.side-nav');
        if (sidebar.style.left === '0px') {
            sidebar.style.left = '-250px';
        } else {
            sidebar.style.left = '0px';
        }
    }
</script>
</body>
</html>
