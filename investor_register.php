<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "init.php";
$pageTitle = "Investor Register";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $fname = $_POST['Fname'];
    $lname = $_POST['Lname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone_number = $_POST['number'];

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username already exists (since it must be unique)
    $stmt_check = $conn->prepare("SELECT username FROM Investor WHERE username = :zUsername");
    $stmt_check->execute(array("zUsername" => $username));

    if ($stmt_check->rowCount() > 0) {
        // Username is already taken
        echo "<script>alert('This username is already taken. Please choose another one.');</script>";
    } else {
        // Username is available, proceed with the insertion
        $stmt_investor = $conn->prepare("INSERT INTO Investor (Fname, Lname, email, username, password, phone) 
                                         VALUES (:zFname, :zLname, :zEmail, :zUsername, :zPassword, :zPhone)");
        $stmt_investor->execute(array(
            "zFname"    => $fname,
            "zLname"    => $lname,
            "zEmail"    => $email,
            "zUsername" => $username,
            "zPassword" => $hashed_password,
            "zPhone"    => $phone_number
        ));

        // Redirect to login page after successful registration
        header("Location: login.php");
        exit();
    }
}
?>

<link rel="stylesheet" href="layout/css/reg.css">
<script>
    function validateForm() {
        const phoneNumber = document.getElementById('PhoneNumber').value;
        const password = document.getElementById('Password').value;
        const passwordRegex = /^(?=.*[A-Z])(?=.*[0-9]).{8,}$/;

        // Validate phone number (ensure it's numeric)
        if (isNaN(phoneNumber)) {
            alert('Phone number must be numeric.');
            return false;
        }

        // Validate password policy
        if (!passwordRegex.test(password)) {
            alert('Password must be at least 8 characters long, contain at least one uppercase letter, and one number.');
            return false;
        }

        return true;
    }
</script>
<!-- Investor Registration Form -->
<form action="investor_register.php" method="post" onsubmit="return validateForm()" class="register mt-5 py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="Fname">First Name:</label>
                    <input type="text" id="Fname" name="Fname" placeholder="Enter your First name" required>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="Lname">Last Name:</label>
                    <input type="text" id="Lname" name="Lname" placeholder="Enter your Last name" required>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="Username">Username:</label>
                    <input type="text" id="Username" name="username" placeholder="Enter your username" required>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="Email">Email:</label>
                    <input type="email" id="Email" name="email" placeholder="Enter your email" required>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="Password">Password:</label>
                    <input type="password" id="Password" name="password" placeholder="Enter your password" required>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="PhoneNumber">Phone Number:</label>
                    <input type="text" id="PhoneNumber" name="number" placeholder="Enter your phone number" required>
                </div>
            </div>
        </div>
        <input type="submit" value="Register as Investor" class="btn btn-primary">
        <p>Already have an account? <a href="login.php" class="link-color">Login here</a>.</p>
        <p>New here? <a href="register.php">Register now</a>.</p>
    </div>
</form>
