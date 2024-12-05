<?php
session_start();
require 'db_connection.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $_POST['identifier'];
    $user_type = $_POST['user_type'];

    // Check the identifier in the corresponding table based on the user type
    if ($user_type === 'consultant') {
        $stmt = $conn->prepare("SELECT * FROM consultant WHERE email = :identifier OR username = :identifier");
    } elseif ($user_type === 'investor') {
        $stmt = $conn->prepare("SELECT * FROM investor WHERE email = :identifier OR username = :identifier");
    } elseif ($user_type === 'seller') {
        $stmt = $conn->prepare("SELECT * FROM seller WHERE email = :identifier OR username = :identifier");
    }

    $stmt->bindParam(':identifier', $identifier, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['reset_user'] = $user;
        $_SESSION['user_type'] = $user_type;
        header("Location: reset_password.php");
        exit();
    } else {
        $_SESSION['error_message'] = "No account found with that email or username.";
        header("Location: login.php");
        exit();
    }
}
?>
