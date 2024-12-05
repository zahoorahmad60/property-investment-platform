<?php
session_start();
$noNavbar = true;
include 'init.php'; // Database connection
include "invester_sidebar.php";

// Check if the user is logged in as an investor
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'investor') {
    // Redirect to login page with a message
    $_SESSION['message'] = "Please register as an investor to make investments.";
    header("Location: login.php");
    exit();
}

$investor_id = $_SESSION['user_id']; // Get the logged-in investor's ID

// Check if the property ID is provided in the URL, use 'property_id'
if (isset($_GET['property_id']) && !empty($_GET['property_id'])) {
    $property_id = intval($_GET['property_id']); // Get the property ID from the URL
} else {
    // If property_id is not found in the URL, show an error message
    echo "Invalid request. Property ID is missing.";
    exit();
}

// Fetch the property details from the database
$stmt_property = $conn->prepare("SELECT * FROM Property WHERE property_ID = :property_id");
$stmt_property->bindParam(':property_id', $property_id, PDO::PARAM_INT);
$stmt_property->execute();
$property = $stmt_property->fetch(PDO::FETCH_ASSOC);

// If the property doesn't exist, show an error
if (!$property) {
    echo "Invalid Property ID.";
    exit();
}

// Fetch the total investment for the property
$stmt_investment = $conn->prepare("SELECT SUM(amount_paid) AS total_invested FROM Investment_Portfolio WHERE property_ID = :property_id");
$stmt_investment->bindParam(':property_id', $property_id, PDO::PARAM_INT);
$stmt_investment->execute();
$investment = $stmt_investment->fetch(PDO::FETCH_ASSOC);

$total_invested = $investment['total_invested'] ?: 0; // Default to 0 if no investments yet
$amount_left_to_invest = $property['cost_of_property'] - $total_invested;
// Handle form submission for investment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount_paid = floatval($_POST['amount_paid']);
   
    // Calculate the monthly return amount based on the property's monthly rental return and investment percentage
    $monthly_return_amount = ($amount_paid / 100) * $property['monthly_return_percentage'];
    
    // Insert the investment into the Investment_portfolio table
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

    // Redirect to the investments dashboard after successful investment
    echo "<script>alert('Investment made successfully!');</script>";
    header("Location: investor_investments.php");
    exit();
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
            display:block;
            font-family: Arial, sans-serif;
            background-color: #0D1B2A;
            margin: 0;
            padding: 20px;
            color: white;
        }
        .container {
            max-width: 900px;
            padding: 20px;
            background-color: #415A77;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top:80px;

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
        .alert {
            color: red;
        }
        .percentage-display {
            font-size: 16px;
            margin-top: -10px;
            margin-bottom: 20px;
            color: #FFD700;
        }
    </style>
  <script>
function calculatePercentageAndReturn() {
    var amountPaid = parseFloat(document.getElementById('amount_paid').value);
    var costOfProperty = <?php echo json_encode($property['cost_of_property']); ?>;
    var monthlyReturnPercentage = <?php echo json_encode($property['monthly_return_percentage']); ?>;
    var amountLeftToInvest = <?php echo json_encode($amount_left_to_invest); ?>;

    if (!isNaN(amountPaid) && amountPaid > 0 && amountPaid <= amountLeftToInvest) {
        var investmentPercentage = (amountPaid / costOfProperty) * 100;
        var monthlyReturnAmount = (amountPaid * monthlyReturnPercentage) / 100;

        document.getElementById('percentage_display').innerText = 'Investment Percentage: ' + investmentPercentage.toFixed(6) + '%';
        document.getElementById('annual_return_display').innerText = 'Monthly Return Amount: ﷼' + monthlyReturnAmount.toFixed(3);
    } else {
        document.getElementById('percentage_display').innerText = '';
        document.getElementById('annual_return_display').innerText = '';
    }
}

function validateInvestmentAmount() {
    var amountPaid = parseFloat(document.getElementById('amount_paid').value);
    var amountLeftToInvest = <?php echo json_encode($amount_left_to_invest); ?>;

    if (isNaN(amountPaid) || amountPaid <= 0 || amountPaid > amountLeftToInvest) {
        alert('Please enter a valid investment amount. You cannot invest more than the remaining amount.');
        return false;
    }
    return true;
}
</script>

</head>
<body>

<div class="container">
    <h1>Invest in Property: <?php echo htmlspecialchars($property['name']); ?></h1>
    <p>Property Location: <?php echo htmlspecialchars($property['city'] . ', ' . $property['street'] . ', ' . $property['zip_code']); ?></p>
    <p>Cost of Property: ﷼<?php echo htmlspecialchars($property['cost_of_property']); ?></p>
    <p>Annual Return: <?php echo htmlspecialchars($property['monthly_return_percentage']); ?>%</p>
    <p>Total Invested: ﷼<?php echo htmlspecialchars($total_invested); ?></p>
    <p>Amount Left to Invest: ﷼<?php echo htmlspecialchars($amount_left_to_invest); ?></p>

    <?php if ($amount_left_to_invest > 0): ?>
    <form action="payment_page.php?property_id=<?php echo $property_id; ?>" method="post" onsubmit="return validateInvestmentAmount();">
        <div class="form-group">
            <label for="amount_paid">Amount Paid (SAR):</label>
            <input type="number" id="amount_paid" name="amount_paid" step="0.01" placeholder="Enter amount you wish to invest" oninput="calculatePercentageAndReturn()" required>
            <p id="percentage_display" class="percentage-display"></p>
            <p id="annual_return_display" class="annual-return-display"></p>
        </div>
        <input type="submit" value="Proceed to Payment" class="submit-btn">
    </form>
    <?php else: ?>
    <p>This property is fully funded and no further investments are allowed.</p>
    <?php endif; ?>
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
