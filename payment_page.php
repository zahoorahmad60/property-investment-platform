<?php
session_start();
ob_start(); // Start output buffering to prevent early output

$noNavbar = true;
include 'init.php'; // Include database connection
include "invester_sidebar.php";

// Check if the user is logged in as an investor
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'investor') {
    $_SESSION['message'] = "Please log in as an investor to make payments.";
    header("Location: login.php");
    exit();
}

$investor_id = $_SESSION['user_id'];

// Check if the property ID and investment amount are provided
if (isset($_GET['property_id']) && !empty($_POST['amount_paid'])) {
    $property_id = intval($_GET['property_id']);
    $amount_paid = floatval($_POST['amount_paid']);
    
    // Fetch the property details for confirmation
    $stmt_property = $conn->prepare("SELECT * FROM Property WHERE property_ID = :property_id");
    $stmt_property->bindParam(':property_id', $property_id, PDO::PARAM_INT);
    $stmt_property->execute();
    $property = $stmt_property->fetch(PDO::FETCH_ASSOC);

    if (!$property) {
        echo "Invalid Property ID.";
        exit();
    }

   // Calculate the monthly return amount based on the property's monthly rental return and investment percentage
   $monthly_return_amount = ($amount_paid / 100) * $property['monthly_return_percentage'];
   $investment_percentage=($amount_paid/$property['cost_of_property'])*100 ;
  

} else {
    echo "Invalid request.";
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['card_name'])) {
    // Check if the investment already exists
    $stmt_check = $conn->prepare("
        SELECT * FROM Investment_portfolio
        WHERE property_ID = :property_id AND investor_ID = :investor_id
    ");
    $stmt_check->execute([
        ':property_id' => $property_id,
        ':investor_id' => $investor_id,
    ]);

    if ($stmt_check->rowCount() > 0) {
        // Investment exists, update the existing record
        $stmt_update = $conn->prepare("
            UPDATE Investment_portfolio
            SET investment_percentage = investment_percentage + :investment_percentage,
                amount_paid = amount_paid + :amount_paid,
                monthly_return_amount = monthly_return_amount + :monthly_return_amount,
                date = CURDATE(),
                time = CURTIME()
            WHERE property_ID = :property_id AND investor_ID = :investor_id
        ");
        $stmt_update->execute([
            ':property_id' => $property_id,
            ':investor_id' => $investor_id,
            ':investment_percentage' => $investment_percentage,
            ':amount_paid' => $amount_paid,
            ':monthly_return_amount' => $monthly_return_amount,
        ]);
    } else {
        // Insert a new investment record
        $stmt_investment = $conn->prepare("
            INSERT INTO Investment_portfolio (property_ID, investor_ID, investment_percentage, date, time, amount_paid, monthly_return_amount)
            VALUES (:property_id, :investor_id, :investment_percentage, CURDATE(), CURTIME(), :amount_paid, :monthly_return_amount)
        ");
        $stmt_investment->execute([
            ':property_id' => $property_id,
            ':investor_id' => $investor_id,
            ':investment_percentage' => $investment_percentage,
            ':amount_paid' => $amount_paid,
            ':monthly_return_amount' => $monthly_return_amount,
        ]);
    }

    // Redirect to the investments dashboard with a success message
    echo "<script>alert('Payment confirmed! Your investment has been made successfully.'); window.location.href = 'investor_investments.php';</script>";
    exit();
}

ob_end_flush(); // End output buffering and send the output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
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
            margin: 20px 0;
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
    </style>
</head>
<body>

<div class="container" style="margin-top:100px;">
    <h1>Confirm Payment</h1>
    

    <form id="payment_form" action="payment_page.php?property_id=<?php echo $property_id; ?>" method="post">
        <input  type="hidden" name="amount_paid" value="<?php echo $amount_paid; ?>">

        <!-- Card Information Fields -->
        <div class="form-group">
            <label for="card_name">Cardholder Name:</label>
            <input type="text" id="card_name" name="card_name" placeholder="Name on card" required>
        </div>
        <div class="form-group">
            <label for="card_number">Card Number:</label>
            <input type="text" id="card_number" name="card_number" placeholder="Card number" required>
        </div>
        <div class="form-group">
            <label for="expiry_date">Expiry Date:</label>
            <input type="month" id="expiry_date" name="expiry_date" required>
        </div>
        <div class="form-group">
            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" placeholder="CVV" required>
        </div>
        <p id="error_message" class="error-message"></p>

        <button type="submit" name="confirm_payment" class="confirm-btn" onclick="validateCardInfo(event)">Confirm Payment</button>
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
<script>
        function validateCardInfo(event) {
            event.preventDefault(); // Prevent form submission

            const cardName = document.getElementById('card_name').value.trim();
            const cardNumber = document.getElementById('card_number').value.trim();
            const expiryDate = document.getElementById('expiry_date').value.trim();
            const cvv = document.getElementById('cvv').value.trim();
            const errorMessage = document.getElementById('error_message');

            errorMessage.textContent = '';

            if (!cardName || !cardNumber || !expiryDate || !cvv) {
                errorMessage.textContent = 'Please fill in all card details.';
                return;
            }

            if (!/^\d{16}$/.test(cardNumber)) {
                errorMessage.textContent = 'Invalid card number. Please enter a 16-digit card number.';
                return;
            }

            if (!/^\d{3,4}$/.test(cvv)) {
                errorMessage.textContent = 'Invalid CVV. Please enter a 3 or 4-digit CVV.';
                return;
            }

            const currentDate = new Date();
            const [year, month] = expiryDate.split('-').map(Number);
            const expiry = new Date(year, month - 1);

            if (expiry <= currentDate) {
                errorMessage.textContent = 'Invalid expiry date. Please enter a valid future date.';
                return;
            }

            // If all validations pass, submit the form
            document.getElementById('payment_form').submit();
        }
    </script>
</body>
</html>
