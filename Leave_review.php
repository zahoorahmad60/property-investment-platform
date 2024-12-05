<?php
session_start();
include 'init.php'; // Include your database connection

// Check if the user is logged in as an investor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'investor') {
    header("Location: login.php"); // Redirect to login if not logged in as investor
    exit();
}

// Handle the review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $session_number = $_POST['session_number'];
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];

    try {
        $conn->beginTransaction();

        // Update the consultation record with the rating and feedback
        $stmt_update = $conn->prepare("UPDATE consultation SET rating = :rating, feedback = :feedback WHERE session_number = :session_number");
        $stmt_update->bindParam(':session_number', $session_number, PDO::PARAM_INT);
        $stmt_update->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt_update->bindParam(':feedback', $feedback, PDO::PARAM_STR);

        if (!$stmt_update->execute()) {
            throw new Exception('Failed to update consultation record.');
        }

        // Fetch the consultant ID
        $stmt_consultant = $conn->prepare("SELECT consultant_ID FROM consultation WHERE session_number = :session_number");
        $stmt_consultant->bindParam(':session_number', $session_number, PDO::PARAM_INT);
        $stmt_consultant->execute();
        $consultant = $stmt_consultant->fetch(PDO::FETCH_ASSOC);

        if (!$consultant) {
            throw new Exception('Consultant not found.');
        }

        $consultant_ID = $consultant['consultant_ID'];

        // Fetch the current rating of the consultant
        $stmt_current_rating = $conn->prepare("SELECT rating FROM consultant WHERE consultant_ID = :consultant_ID");
        $stmt_current_rating->bindParam(':consultant_ID', $consultant_ID, PDO::PARAM_INT);
        $stmt_current_rating->execute();
        $current_rating = $stmt_current_rating->fetch(PDO::FETCH_COLUMN);

        // Fetch all new ratings for the consultant
        $stmt_new_ratings = $conn->prepare("SELECT rating FROM consultation WHERE consultant_ID = :consultant_ID AND rating IS NOT NULL");
        $stmt_new_ratings->bindParam(':consultant_ID', $consultant_ID, PDO::PARAM_INT);
        $stmt_new_ratings->execute();
        $new_ratings = $stmt_new_ratings->fetchAll(PDO::FETCH_COLUMN);

        if (count($new_ratings) === 0) {
            throw new Exception('No new ratings found for consultant.');
        }

        // Calculate the new average rating
        if (is_null($current_rating)) {
            $average_rating = array_sum($new_ratings) / count($new_ratings);
        } else {
            $total_ratings = array_merge([$current_rating], $new_ratings);
            $average_rating = array_sum($total_ratings) / count($total_ratings);
        }

        // Update the consultant's rating
        $stmt_update_rating = $conn->prepare("UPDATE consultant SET rating = :rating WHERE consultant_ID = :consultant_ID");
        $stmt_update_rating->bindParam(':rating', $average_rating, PDO::PARAM_STR);
        $stmt_update_rating->bindParam(':consultant_ID', $consultant_ID, PDO::PARAM_INT);

        if (!$stmt_update_rating->execute()) {
            throw new Exception('Failed to update consultant rating.');
        }

        $conn->commit();

        echo "<script>alert('Thank you for your review!'); window.location.href = 'invester_confirmed_sessions.php';</script>";
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Failed to submit your review. Please try again.'); window.location.href = 'investor_consultations.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Review</title>
</head>
<body>
    <h2>Leave a Review</h2>
    <form action="leave_review.php" method="POST">
        <input type="hidden" name="session_number" value="<?php echo $_GET['session_number']; ?>">
        <div class="form-group">
            <label for="rating">Rating (1-5):</label>
            <select name="rating" required>
                <option value="">Select Rating</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="feedback">Feedback:</label>
            <textarea name="feedback" rows="4" required></textarea>
        </div>
        <button type="submit">Submit Review</button>
    </form>
</body>
</html>
