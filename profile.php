<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['username'])){
    header("Location: login.php"); 
    exit();
}
$pageTitle = "profile";
include "init.php";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $user_type = $_POST['user_type'];
    $fname = $_POST['Fname'];
    $lname = $_POST['Lname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = empty($_POST['password']) ? $_POST['oldpassword'] : password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dob = $_POST['date'];
    $phone_number = $_POST['number'];
    $id = $_POST['id'];

    // Hash the password before storing it

    // Prepare the SQL statement for inserting data
    $stmt = $conn->prepare("UPDATE `users_info` SET `UserType` = ?, `Fname` = ?, `Lname` = ?, `Username` = ?, `Email` = ?, `Password` = ?, `DateOfBirth` =?, `PhoneNumber`= ? WHERE ID = ?");

    $stmt->execute(array($user_type, $fname, $lname, $username, $email, $password, $dob, $phone_number, $id));

    // Redirect to login page or show a success message
    header("Location: profile.php"); // Redirect to login page
    exit();
}

// Close the database connection
// echo $_SESSION['ID'];

$stmt = $conn->prepare("SELECT * FROM `users_info` WHERE `Username` = ?");
$stmt->execute(array(htmlspecialchars($_SESSION['username'])));
// $stmt->bind_result(htmlspecialchars($_SESSION['username']));
$row = $stmt->fetch();
// $count = $stmt->rowCount();
?>
<link rel="stylesheet" href="layout/css/reg.css">
<script>
    // Additional client-side validation
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
        if (password != '' && !passwordRegex.test(password)) {
            alert('Password must be at least 8 characters long, contain at least one uppercase letter, and one number.');
            return false;
        }

        return true;
    }
</script>
<form action="profile.php" method="post" onsubmit="return validateForm()" class="register">

    <!-- Register Form -->
    <div class="container">
        <div class="form-group">
            <input type="hidden" id="id" name="id"  value="<?= $row['ID']?>" required>
            <label for="registerUserType">User Type:</label>
            <select id="registerUserType" name="user_type">
                <option value="investor" <?php echo $row['UserType'] == 'investor' ? 'selected' : '' ?>>Investor</option>
                <option value="consultant" <?php echo $row['UserType'] == 'consultant' ? 'selected' : '' ?>>Consultant</option>
                <option value="seller" <?php echo $row['UserType'] == 'seller' ? 'selected' : '' ?>>Seller</option>
                <option value="seller" <?php echo $row['UserType'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label for="Fname">First Name:</label>
            <input type="text" id="Fname" name="Fname" placeholder="Enter your First name" value="<?= $row['Fname']?>" required>
        </div>

        <div class="form-group">
            <label for="Lname">Last Name:</label>
            <input type="text" id="Lname" name="Lname" placeholder="Enter your Last name" value="<?= $row['Lname']?>" required>
        </div>

        <div class="form-group">
            <label for="Username">Username:</label>
            <input type="text" id="Username" name="username" placeholder="Enter your username" value="<?= $row['Username']?>" required>
        </div>

        <div class="form-group">
            <label for="Email">Email:</label>
            <input type="email" id="Email" name="email" placeholder="Enter your email" value="<?= $row['Email']?>" required>
        </div>

        <div class="form-group">
            <label for="Password">Password:</label>
            <input type="hidden" name="oldpassword" value="<?php echo $row['Password']; ?>"> 
            <input type="password" id="Password" name="password" placeholder="Enter your password">
        </div>

        <div class="form-group">
            <label for="Date">Date of Birth:</label>
            <input type="date" id="Date" name="date" value="<?= $row['DateOfBirth']?>" required>
        </div>

        <div class="form-group">
            <label for="PhoneNumber">Phone Number:</label>
            <input type="text" id="PhoneNumber" name="number" placeholder="Enter your phone number" value="<?= $row['PhoneNumber']?>" required>
        </div>
        <form action="profile.php" method="post" onsubmit="return validateForm()">
            <input type="submit" value="Update" name="submit">
        <p class="" style="text-align:left"><a href="user_information.php" style="color:antiquewhite">More your information</a></p>
    </div>

    </body>

    </html>