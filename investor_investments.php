<?php
session_start();
$noNavbar = true;
include 'init.php'; // Database connection



// Check if the user is logged in as an investor
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'investor') {
    header("Location: login.php");
    exit();
}

$investor_id = $_SESSION['user_id']; // Get the logged-in investor's ID

// Initialize $current_investments as an empty array
$current_investments = [];

// Fetch current investments made by the investor
try {
    $stmt_investments = $conn->prepare("
        SELECT p.property_ID, p.name, p.city, p.cost_of_property, p.monthly_return_percentage, ip.investment_percentage, ip.amount_paid, ip.monthly_return_amount, p.image_path
        FROM Investment_portfolio ip
        JOIN Property p ON ip.property_ID = p.property_ID
        WHERE ip.investor_ID = :investor_id
    ");
    $stmt_investments->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
    $stmt_investments->execute();
    $current_investments = $stmt_investments->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error fetching investments: " . $e->getMessage();
}

// Fetch available properties for new investments
$available_properties = [];
try {
    $stmt_properties = $conn->prepare("SELECT * FROM Property");
    $stmt_properties->execute();
    $available_properties = $stmt_properties->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error fetching available properties: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Investments</title>
    <link rel="stylesheet" href="seller/layout/css/welcome_sller.css">
    
    <style>
        body {
            display:block;
            font-family: Arial, sans-serif;
            background-color: #0D1B2A;
            color: #E8E8E8;
            margin: 0;
            padding: 0;
        }

        /* Main content adjustments */
        .main-content {
            padding: 20px 40px; /* Added left and right padding */
            margin-top: 80px; /* Distance from top */
            margin-left: 250px; /* Adjust if you have a sidebar */
            box-sizing: border-box;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 30px;
            background-color: #1B263B;
            border-radius: 12px;
            color: #E8E8E8;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        h1 {
            font-size: 36px;
            margin-bottom: 25px;
            text-align: center;
            text-transform: uppercase;
            color: #FFFFFF;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 25px;
            text-align: center;
            color: #A3BAC3;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            justify-content: flex-start; /* Align cards to the left */
            margin-bottom: 40px;
        }

        .card {
            background-color: #2B2D42;
            border-radius: 10px;
            padding: 20px;
            flex: 1 1 calc(33.333% - 30px); /* Three cards per row with spacing */
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .investment{
    display:flex;
}
        .total-invested {
    color: #2E8B57 !important; /* Dark green */
    background-color: #F0FFF0 !important; /* Light green background */
    padding: 8px !important;
    border-radius: 4px !important;
    margin: 10px !important;
}

.funding-percentage {
    color: #4682B4 !important; /* Steel blue */
    background-color: #F0F8FF !important; /* Alice blue background */
    padding: 8px!important;
    border-radius: 4px !important;
    margin:10px !important;
}
.funded-stamp {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: red;
    color: white;
    padding: 5px;
    font-size: 14px;
    font-weight: bold;
}
        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .card {
                flex: 1 1 calc(50% - 30px); /* Two cards per row */
            }
        }

        @media (max-width: 768px) {
            .card {
                flex: 1 1 100%; /* One card per row */
            }

            .main-content {
                margin-left: 0; /* Remove left margin on small screens */
            }
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.25);
        }

        .card-header {
            font-size: 22px;
            font-weight: bold;
            color: #E8E8E8;
            margin-bottom: 15px;
        }

        .card-body img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .card-body p {
            margin: 8px 0;
            font-size: 16px;
            color: #C0C5CE;
        }

        .btn {
            padding: 10px 20px;
            background-color: #3DA5D9;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #3298c6;
        }

        .no-properties {
            text-align: center;
            font-size: 18px;
            color: #D1D5DB;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <?php include 'invester_sidebar.php'; ?>
    <div class="main-content">
        <div class="container">
            <h1>Manage My Investments</h1>
<!-- Current Investments -->
<h2>Current Investments</h2>
<?php
if (count($current_investments) > 0) {
    $displayedProperties = []; // Track displayed properties
    echo '<div class="card-container">';
    foreach ($current_investments as $investment) {
        if (!in_array($investment['property_ID'], $displayedProperties)) {
            $displayedProperties[] = $investment['property_ID']; // Mark property as displayed
            
            // Fetch the number of investors for this property
            $stmt_investors = $conn->prepare("SELECT COUNT(DISTINCT investor_ID) as num_investors FROM Investment_Portfolio WHERE property_ID = :property_ID");
            $stmt_investors->bindParam(':property_ID', $investment['property_ID'], PDO::PARAM_INT);
            $stmt_investors->execute();
            $investors = $stmt_investors->fetch(PDO::FETCH_ASSOC);
            $num_investors = $investors['num_investors'] ?? 0;

            // Fetch total amount invested in this property
            $stmt_investment = $conn->prepare("SELECT SUM(amount_paid) as total_invested FROM Investment_Portfolio WHERE property_ID = :property_ID");
            $stmt_investment->bindParam(':property_ID', $investment['property_ID'], PDO::PARAM_INT);
            $stmt_investment->execute();
            $investmentDetails = $stmt_investment->fetch(PDO::FETCH_ASSOC);
            $total_invested = $investmentDetails['total_invested'] ?? 0;

            // Calculate the percentage funded
            $percentage_funded = ($total_invested / $investment['cost_of_property']) * 100;
            ?>
            <div class="card" style="position: relative;">
                <div class="card-header"> <a href="invester_property_details.php?property_id=<?php echo htmlspecialchars($property['property_ID']); ?>" class="btn">
                                    <?php echo htmlspecialchars($property['name']); ?>
                                </a> ?></div>
                <div class="card-body">
                    <img src="<?php echo 'seller/' . str_replace('\\', '/', htmlspecialchars($investment['image_path'])); ?>" alt="<?php echo htmlspecialchars($investment['name']); ?>">
                    
                    <div class="investment">
                    <p class="total-invested"><strong>Investors:</strong> <?php echo htmlspecialchars($num_investors); ?></p>
                    <p class="funding-percentage"><strong>Funded:</strong> <?php echo number_format($percentage_funded, 2); ?>%</p>
        </div>
                    <p>City: <?php echo htmlspecialchars($investment['city']); ?></p>
                    <p>Cost: ﷼<?php echo number_format($investment['cost_of_property']); ?></p>
                    <p>Investment %: <?php echo htmlspecialchars($investment['investment_percentage']); ?>%</p>
                    <p>Paid: ﷼<?php echo number_format($investment['amount_paid']); ?></p>
                    <p>Annual Return: <?php echo number_format($investment['monthly_return_percentage']); ?>%</p>
                   <?php if ($percentage_funded >= 100): ?>
                        <div class="funded-stamp">Fully Funded</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    }
    echo '</div>';
} else {
    echo '<p class="no-properties">No investments made yet.</p>';
}
?>


            <!-- Available Properties Heading -->
            <h2 class="heading-available">Properties Available for Investment</h2>

            <?php if (count($available_properties) > 0): ?>
    <div class="card-container">
        <?php foreach ($available_properties as $property): ?>
            <?php
            // Fetch the number of investors for this property
            $stmt_investors = $conn->prepare("SELECT COUNT(DISTINCT investor_ID) as num_investors FROM Investment_Portfolio WHERE property_ID = :property_ID");
            $stmt_investors->bindParam(':property_ID', $property['property_ID'], PDO::PARAM_INT);
            $stmt_investors->execute();
            $investors = $stmt_investors->fetch(PDO::FETCH_ASSOC);
            $num_investors = $investors['num_investors'] ?? 0;

            // Fetch total amount invested in this property
            $stmt_investment = $conn->prepare("SELECT SUM(amount_paid) as total_invested FROM Investment_Portfolio WHERE property_ID = :property_ID");
            $stmt_investment->bindParam(':property_ID', $property['property_ID'], PDO::PARAM_INT);
            $stmt_investment->execute();
            $investment = $stmt_investment->fetch(PDO::FETCH_ASSOC);
            $total_invested = $investment['total_invested'] ?? 0;

            // Calculate the percentage funded
            $percentage_funded = ($total_invested / $property['cost_of_property']) * 100;
            ?>

            <div class="card" style="position: relative;">
                <div class="card-header"> <a href="invester_property_details.php?property_id=<?php echo htmlspecialchars($property['property_ID']); ?>" class="btn">
                                    <?php echo htmlspecialchars($property['name']); ?>
                                </a></div>
                <div class="card-body">
                    <img src="<?php echo 'seller/' . str_replace('\\', '/', htmlspecialchars($property['image_path'])); ?>" alt="<?php echo htmlspecialchars($property['name']); ?>">
                    
                    <div class="investment">
                    <p class="total-invested"><strong>Investors:</strong> <?php echo htmlspecialchars($num_investors); ?></p>
                    <p class="funding-percentage"><strong>Funded:</strong> <?php echo number_format($percentage_funded, 2); ?>%</p>
        </div>
                    <p>City: <?php echo htmlspecialchars($property['city']); ?></p>
                    <p>Cost: ﷼<?php echo number_format($property['cost_of_property']); ?></p>
                    <p>Annual Return: <?php echo number_format($property['monthly_return_percentage']); ?>%</p>
                   <?php if ($percentage_funded < 100): ?>
                        <a href="invester_invest_property.php?property_id=<?php echo $property['property_ID']; ?>" class="btn">Invest</a>
                    <?php else: ?>
                        <div class="funded-stamp">Fully Funded</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="no-properties">No available properties for investment.</p>
<?php endif; ?>

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
