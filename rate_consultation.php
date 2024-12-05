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

// Fetch consultant details for display (optional)
$stmt = $conn->prepare("SELECT Fname, Lname FROM Consultant WHERE consultant_ID = :consultant_id");
$stmt->bindParam(':consultant_id', $consultant_id, PDO::PARAM_INT);
$stmt->execute();
$consultant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consultant) {
    echo "Consultant not found.";
    exit();
}

// Handle the rating form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rate_consultant'])) {
    $rating = intval($_POST['rating']);
    $feedback = $_POST['feedback'];

    // Insert the rating into the Consultant_Rating table
    $stmt_insert = $conn->prepare("INSERT INTO Consultant_Rating (consultant_ID, investor_ID, rating, feedback) 
                                   VALUES (:consultant_id, :investor_id, :rating, :feedback)");
    $stmt_insert->bindParam(':consultant_id', $consultant_id);
    $stmt_insert->bindParam(':investor_id', $_SESSION['user_id']); // Assuming the user is logged in
    $stmt_insert->bindParam(':rating', $rating);
    $stmt_insert->bindParam(':feedback', $feedback);
    $stmt_insert->execute();

    echo "Thank you for your feedback!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Consultant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
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
        }

        textarea {
            resize: vertical;
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
    <h1>Rate Consultant <?php echo htmlspecialchars($consultant['Fname'] . ' ' . $consultant['Lname']); ?></h1>

    <form action="rate_consultation.php?consultant_id=<?php echo $consultant_id; ?>" method="POST">
        <label for="rating">Rate (1 to 5):</label>
        <input type="number" id="rating" name="rating" min="1" max="5" required>

        <label for="feedback">Your Feedback (optional):</label>
        <textarea id="feedback" name="feedback" rows="4"></textarea>

        <button type="submit" name="rate_consultant">Submit Rating</button>
    </form>
</div>

</body>
</html>
