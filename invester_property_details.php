<?php
// Start the session at the top and include necessary files
session_start();
include 'init.php'; // This should contain your database connection

// Check if the user is logged in as an investor
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'investor') {
    header("Location: login.php");
    exit();
}

// Include sidebar only after session and permission checks
include "invester_sidebar.php";

// Validate and retrieve the property ID from the URL
if (isset($_GET['property_id']) && is_numeric($_GET['property_id'])) {
    $property_id = $_GET['property_id'];

    // Fetch the property details (no seller_ID check for admin)
    $stmt = $conn->prepare("SELECT * FROM Property WHERE property_ID = :property_id");
    $stmt->bindParam(':property_id', $property_id, PDO::PARAM_INT);
    $stmt->execute();
    $property = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if property exists
    if (!$property) {
        echo "<script>alert('Property not found or you do not have permission to view it.'); window.location.href='admin_investments_made.php';</script>";
        exit();
    }
} else {
    // If property ID is invalid, redirect with an alert
    echo "<script>alert('Invalid property ID.'); window.location.href='admin_investments_made.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details - <?php echo htmlspecialchars($property['name']); ?></title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #0D1B2A;
        margin: 0;
        padding: 0;
        color: white;
        display: flex;
        flex-direction: column;
        height: 100vh;
        width: 100vw;
    }
    .container {
        width: calc(100% - 60px); /* Adds more distance from the sides */
        margin: 600px auto 0 280px; /* Adds 50px distance from the top and left */
        padding: 20px;
        background-color: #1B263B;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Added subtle shadow for better focus */
        color: white;
        max-width: 1200px; /* Restricts the maximum width */
    }
    h1 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 2.5em;
        color: #00BFFF; /* Highlighted color for heading */
    }
    .property-image img {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .property-details {
        display: flex;
        flex-wrap: wrap;
        gap: 20px; /* Adds spacing between items */
        margin-top: 20px;
    }
    .detail-item {
        flex: 1 1 30%;
        background-color: #415A77;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .detail-item strong {
        display: block;
        margin-bottom: 8px;
        color: #00BFFF;
        font-size: 1.1em;
    }
    .description {
        width: 100%;
        background-color: #415A77;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        line-height: 1.6;
    }
    .back-button {
        display: inline-block;
        margin-top: 30px;
        padding: 12px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.2s;
        font-size: 1em;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2); /* Button shadow for prominence */
    }
    .back-button:hover {
        background-color: #0056b3;
        transform: translateY(-2px); /* Subtle upward movement on hover */
    }
    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            width: calc(100% - 40px); /* Adjust spacing for smaller screens */
            margin: 20px auto; /* Reduce top and left margins */
        }
        .property-details {
            flex-direction: column;
        }
        .detail-item {
            flex: 1 1 100%;
        }
        .description {
            padding: 15px;
        }
        .property-image img {
            max-height: 300px;
        }
    }
</style>

</head>
<body>
<div class="container">
    <h1><?php echo htmlspecialchars($property['name']); ?></h1>
    <div class="property-image">
        <?php if (!empty($property['image_path']) && file_exists('seller/' . $property['image_path'])): ?>
            <img src="seller/<?php echo htmlspecialchars($property['image_path']); ?>" 
                 alt="<?php echo htmlspecialchars($property['name']); ?>">
        <?php else: ?>
            <div style="background-color: #283E4A; display: flex; align-items: center; justify-content: center; height: 300px; border-radius: 10px;">
                <span style="color: #FFFFFF; font-size: 1.5em;">No Image Available</span>
            </div>
        <?php endif; ?>
    </div>
    <div class="property-details">
        <div class="detail-item">
            <strong>Size</strong>
            <span><?php echo htmlspecialchars($property['size']); ?> sq ft</span>
        </div>
        <div class="detail-item">
            <strong>Type</strong>
            <span><?php echo htmlspecialchars($property['type']); ?></span>
        </div>
        <div class="detail-item">
            <strong>Cost</strong>
            <span>﷼<?php echo number_format($property['cost_of_property'], 2); ?></span>
        </div>
        <div class="detail-item">
            <strong>City</strong>
            <span><?php echo htmlspecialchars($property['city']); ?></span>
        </div>
        <div class="detail-item">
            <strong>Street</strong>
            <span><?php echo htmlspecialchars($property['street']); ?></span>
        </div>
        <div class="detail-item">
            <strong>Zip Code</strong>
            <span><?php echo htmlspecialchars($property['zip_code']); ?></span>
        </div>
        <div class="detail-item">
            <strong>Annual Return Percentage</strong>
            <span><?php echo number_format($property['monthly_return_percentage'], 2); ?>%</span>
        </div>
    </div>
    <div class="description">
        <strong>Description:</strong>
        <p><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
<?php include "invester_sidebar.php";?>
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
