<?php
session_start();
$noNavbar = true;
include "init.php"; // Database connection
include "admin_sidebar.php";


$investor_id = $_SESSION['user_id']; // Get the logged-in investor's ID

// Fetch investor details
try {
    $stmt_investor = $conn->prepare("SELECT * FROM investor WHERE investor_ID = :investor_id");
    $stmt_investor->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
    $stmt_investor->execute();
    $investor = $stmt_investor->fetch(PDO::FETCH_ASSOC);

    if (!$investor) {
        echo "Investor not found.";
        exit();
    }

    // Fetch unique properties the investor has invested in and calculate total annual profit
    $total_annual_profit = 0;
    $investments = [];

    $stmt_investments = $conn->prepare("
        SELECT p.property_ID, p.name AS property_name, p.city, p.cost_of_property, 
               SUM(ip.amount_paid) AS total_amount_paid, 
               SUM(ip.monthly_return_amount) AS total_monthly_return
        FROM Investment_portfolio ip
        JOIN Property p ON ip.property_ID = p.property_ID
        WHERE ip.investor_ID = :investor_id
        GROUP BY p.property_ID, p.name, p.city, p.cost_of_property
    ");
    $stmt_investments->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
    $stmt_investments->execute();
    $investments = $stmt_investments->fetchAll(PDO::FETCH_ASSOC);

    foreach ($investments as $investment) {
        $annual_profit = $investment['total_monthly_return'] * 12; // Assuming profit for a year
        $total_annual_profit += $annual_profit;
    }
} catch (Exception $e) {
    echo "Error fetching data: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investor Profile</title>
   
</head>
<body>
<div class="wrapper">
    <h1>Investor Profile</h1>
    
    <div class="profile-section">
        <h2>Investor Information</h2>
        <div class="profile-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($investor['Fname'] . ' ' . $investor['Lname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($investor['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($investor['phone']); ?></p>
            <p><strong>Status:</strong> <?php echo $investor['status'] ? "Active" : "Inactive"; ?></p>
            <?php if (!empty($investor['rejection_reason'])): ?>
                <p><strong>Rejection Reason:</strong> <?php echo htmlspecialchars($investor['rejection_reason']); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="investment-section">
        <h2>Investment Portfolio</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Property Name</th>
                    <th>City</th>
                    <th>Cost</th>
                    <th>Amount Paid</th>
                    <th>Annual Return</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($investments as $investment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($investment['property_name']); ?></td>
                        <td><?php echo htmlspecialchars($investment['city']); ?></td>
                        <td>﷼<?php echo number_format($investment['cost_of_property']); ?></td>
                        <td>﷼<?php echo number_format($investment['total_amount_paid']); ?></td>
                        <td>﷼<?php echo number_format($investment['total_monthly_return']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
