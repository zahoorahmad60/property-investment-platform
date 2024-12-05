<?php
session_start();
include "init.php"; // Include your database connection
include "invester_sidebar.php";

// Get session number from the URL
if (isset($_GET['session_number'])) {
    $session_number = intval($_GET['session_number']);
} else {
    echo "Invalid session number.";
    exit();
}

// Fetch session details and consultant data
$stmt = $conn->prepare("SELECT Fname, Lname, fee FROM Consultant WHERE consultant_ID = :consultant_id");
$stmt->bindParam(':consultant_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$consultant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consultant) {
    echo "Consultant not found.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            background-color: #0D1B2A;
            color: white;
            padding: 20px;
        }
        .container {
            max-width: 1800px; /* Increased width */
            width: 60%; /* Make it fluid */
            margin: 0 auto; /* Center alignment */
            padding: 20px;
            background-color: #1B263B;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
        }
        h1 {
            text-align: center;
            color: white;
        }
        .details {
            margin: 10px 0;
            font-size: 16px;
        }
        .confirm-btn {
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
        .confirm-btn:hover {
            background-color: #0056b3;
        }
             /* Receipt Popup Styling */
             .receipt-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #1B263B;
            padding: 20px;
            border-radius: 8px;
            color: white;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #888;
            z-index: 1000; /* Ensure it's above the blurred background */
        }

        .receipt-popup button {
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .receipt-popup button:hover {
            background-color: #1B263B;
        }

        /* Background Blur when Popup is shown */
        .blur-background {
            filter: blur(5px);
            pointer-events: none; /* Disable interactions with blurred elements */
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
<div class="container" style="margin-top:100px;">
   <h1>Confirm Payment</h1>
    <p>Consultation Fee: SAR <?php echo htmlspecialchars($consultant['fee']); ?></p>

        <form id="payment-form">
        <div class="form-group">
            <label for="card_name">Cardholder Name:</label>
            <input type="text" id="card_name" name="card_name" placeholder="Name on card" required>
        </div>
        <div class="form-group">

            <label for="card-number">Card number</label><br>
            <input type="text" id="card-number" name="card-number" placeholder="Card number" maxlength="16" pattern="\d{15,16}" required title="Card number should be 15 or 16 digits"><br>
            </div>
        <div class="form-group">

            <label for="expiry-date">Expire date (MM/YY)</label><br>
            <input type="month" id="expiry-date" name="expiry-date" placeholder="MM/YY" maxlength="5" pattern="\d{2}/\d{2}" required title="Expiration date should be in MM/YY format"><br>
            </div>
        <div class="form-group">

            <label for="cvv">CVV</label><br>
            <input type="password" id="cvv" name="cvv" placeholder="CVV" maxlength="3" pattern="\d{3}" required title="CVV should be 3 digits"><br>
            </div>
       
            <div class="submit-btn" >
                <button class="confirm-btn" type="submit">Confirm Payment</button>
            </div>
        </form>
    </div>
</div>

<!-- Receipt Pop-up -->
<div class="receipt-popup" id="receipt-popup">
    <h3>Receipt</h3>
    <p>Payment Successful!</p>
    <p>Thank you for your payment. Your session with <?php echo htmlspecialchars($consultant['Fname'] . ' ' . $consultant['Lname']); ?> has been confirmed.</p>
    <p><strong>Amount Paid:</strong> SAR <?php echo htmlspecialchars($consultant['fee']); ?></p>
    <button onclick="closeReceiptPopup()">Close</button>
</div>

<script>
    // Submit payment form
    document.getElementById("payment-form").addEventListener("submit", function(event) {
        event.preventDefault();

        // Apply background blur effect to the main content
        document.querySelector('.container').classList.add('blur-background');
        
        // Show receipt popup
        document.getElementById("receipt-popup").style.display = "block";
    });
// Close receipt popup and remove background blur, then redirect
function closeReceiptPopup() {
    // Hide the receipt popup
    document.getElementById("receipt-popup").style.display = "none";

    // Remove the blur effect from the main content
    document.querySelector('.container').classList.remove('blur-background');
    
    // Redirect to the invester_h.php page
    window.location.href = 'invester_confirmed_sessions.php';  // Redirects the user
}

    
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
