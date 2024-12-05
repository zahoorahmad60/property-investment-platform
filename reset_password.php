<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['reset_user']) || !isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $user = $_SESSION['reset_user'];
    $user_type = $_SESSION['user_type'];

    // Update the password in the correct table
    if ($user_type === 'consultant') {
        $stmt = $conn->prepare("UPDATE consultant SET password = :new_password WHERE consultant_ID = :id");
        $stmt->bindParam(':id', $user['consultant_ID'], PDO::PARAM_INT);
    } elseif ($user_type === 'investor') {
        $stmt = $conn->prepare("UPDATE investor SET password = :new_password WHERE investor_ID = :id");
        $stmt->bindParam(':id', $user['investor_ID'], PDO::PARAM_INT);
    } elseif ($user_type === 'seller') {
        $stmt = $conn->prepare("UPDATE seller SET password = :new_password WHERE seller_ID = :id");
        $stmt->bindParam(':id', $user['seller_ID'], PDO::PARAM_INT);
    }
    $stmt->bindParam(':new_password', $new_password, PDO::PARAM_STR);
    $stmt->execute();

    unset($_SESSION['reset_user']);
    unset($_SESSION['user_type']);
    $_SESSION['message'] = "Your password has been reset successfully.";
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="log.css">

      <style>
        /* Optional: Inline styles for demonstration; preferably use an external CSS file */
        body {
            background-color: #0D1B2A;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: #1B263B;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            color: #FFFFFF;
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #fffff;
        }
        label {
            display: block;
            margin-bottom: 5px;
            margin-top: 15px;
            color: #ddd;
        }
        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #blue;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        input[type="submit"]:hover {
            background-color: #blue;
        }
        .error-message {
            background-color: #dc3545;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            color: #17a2b8;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form action="reset_password.php" method="post">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <input type="submit" value="Reset Password">
        </form>
    </div>
</body>
</html>
