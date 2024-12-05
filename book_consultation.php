<?php
session_start();
include "init.php"; // Include your database connection

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

// Check if the user is logged in as an investor or seller
if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] !== 'investor' && $_SESSION['user_type'] !== 'seller')) {
    header("Location: login.php"); // Redirect to login if not logged in as investor or seller
    exit();
}

// Handle consultation booking form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_consultation'])) {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $description = $_POST['description'];
    
    // Assign investor_ID or seller_ID based on the session's user type
    $investor_id = null;
    $seller_id = null;
    
    if ($_SESSION['user_type'] === 'investor') {
        $investor_id = $_SESSION['user_id'];
    } elseif ($_SESSION['user_type'] === 'seller') {
        $seller_id = $_SESSION['user_id'];
    }

    // Insert the consultation data into the Consultation table
    $stmt_insert = $conn->prepare("INSERT INTO Consultation (investor_ID, consultant_ID, date, time, description, seller_ID, status) 
                                   VALUES (:investor_id, :consultant_id, :date, :time, :description, :seller_id, 'pending')");
    $stmt_insert->bindParam(':investor_id', $investor_id); // Nullable if the user is a seller
    $stmt_insert->bindParam(':consultant_id', $consultant_id);
    $stmt_insert->bindParam(':date', $date);
    $stmt_insert->bindParam(':time', $time);
    $stmt_insert->bindParam(':description', $description);
    $stmt_insert->bindParam(':seller_id', $seller_id); // Nullable if the user is an investor
    
    try {
        $stmt_insert->execute();
        echo "Consultation request sent successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    exit();
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
            font-family: Arial, sans-serif;
            background-color: #0D1B2A;
            padding: 20px;
            color: white;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #1B263B;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: white;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin: 10px 0 5px;
        }
        input, textarea {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Book Consultation with <?php echo htmlspecialchars($consultant['Fname'] . ' ' . $consultant['Lname']); ?></h1>

    <form action="book_consultation.php?consultant_id=<?php echo $consultant_id; ?>" method="POST">
        <label for="date">Select Date:</label>
        <input type="date" id="date" name="date" required>

        <label for="time">Select Time:</label>
        <input type="time" id="time" name="time" required>

        <label for="description">Description (optional):</label>
        <textarea id="description" name="description" placeholder="Provide a short description..." rows="4"></textarea>

        <button type="submit" name="book_consultation">Book Consultation</button>
    </form>
</div>

</body>
</html>
