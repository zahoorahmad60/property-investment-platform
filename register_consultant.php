<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "init.php";
$pageTitle = "Consultant Register";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data with default values
    $fname = trim($_POST['Fname'] ?? '');
    $lname = trim($_POST['Lname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone_number = trim($_POST['number'] ?? '');
    $experience = $_POST['experience'] ?? 0;
    $fee = $_POST['fee'] ?? 0;
    $company = trim($_POST['company'] ?? '');
    $rating = $_POST['rating'] !== '' ? $_POST['rating'] : NULL; // Set to NULL if empty

    // Server-side Validation

    // Initialize an array to collect error messages
    $errors = [];

    // Validate required fields
    if (empty($fname) || empty($lname) || empty($username) || empty($email) || empty($password) || empty($phone_number)) {
        $errors[] = "All required fields must be filled.";
    }

    // Validate phone number: numeric and at least 10 digits
    if (!ctype_digit($phone_number)) {
        $errors[] = "Phone number must be numeric.";
    } elseif (strlen($phone_number) < 10) {
        $errors[] = "Phone number must be at least 10 digits.";
    }

    // Validate username: 5-20 characters, alphanumeric and underscores only
    if (!preg_match('/^[a-zA-Z0-9_]{5,20}$/', $username)) {
        $errors[] = "Username must be 5-20 characters long and can contain only letters, numbers, and underscores.";
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate password strength
    $passwordRegex = '/^(?=.*[A-Z])(?=.*[0-9]).{8,}$/';
    if (!preg_match($passwordRegex, $password)) {
        $errors[] = "Password must be at least 8 characters long, contain at least one uppercase letter, and one number.";
    }

    // If there are validation errors, display them and exit
    if (!empty($errors)) {
        // Display all error messages
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
        exit();
    }

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if username already exists
    $stmt_check_username = $conn->prepare("SELECT COUNT(*) FROM Consultant WHERE username = :username");
    $stmt_check_username->bindParam(':username', $username);
    $stmt_check_username->execute();
    $username_exists = $stmt_check_username->fetchColumn();

    if ($username_exists > 0) {
        echo "<script>alert('The username \"$username\" is already taken. Please choose a different one.');</script>";
        exit();
    }

    // Insert into Consultant table
    $stmt_consultant = $conn->prepare("INSERT INTO Consultant (Username, Password, Email, Phone, Fname, Lname, experience, fee, company, rating) 
                                      VALUES (:zUsername, :zPassword, :zEmail, :zPhone, :zFname, :zLname, :zExperience, :zFee, :zCompany, :zRating)");
    $stmt_consultant->execute(array(
        "zUsername"     => $username,
        "zPassword"     => $hashed_password,
        "zEmail"        => $email,
        "zPhone"        => $phone_number,
        "zFname"        => $fname,
        "zLname"        => $lname,
        "zExperience"   => $experience,
        "zFee"          => $fee,
        "zCompany"      => $company,
        "zRating"       => $rating  // Pass NULL or the actual rating value
    ));

    // Redirect to login page after successful registration
    header("Location: login.php");
    exit();
}
?>

<link rel="stylesheet" href="layout/css/reg.css">
<script>
    function validateForm() {
        const phoneNumber = document.getElementById('PhoneNumber').value.trim();
        const username = document.getElementById('Username').value.trim();
        const password = document.getElementById('Password').value;
        const passwordRegex = /^(?=.*[A-Z])(?=.*[0-9]).{8,}$/;
        const usernameRegex = /^[a-zA-Z0-9_]{5,20}$/;

        // Validate phone number (ensure it's numeric)
        if (isNaN(phoneNumber)) {
            alert('Phone number must be numeric.');
            return false;
        }

        // Validate phone number length
        if (phoneNumber.length < 10) {
            alert('Phone number must be at least 10 digits.');
            return false;
        }

        // Validate username length and allowed characters
        if (!usernameRegex.test(username)) {
            alert('Username must be 5-20 characters long and can contain only letters, numbers, and underscores.');
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

<!-- Consultant Registration Form -->
<form action="register_consultant.php" method="post" onsubmit="return validateForm()" class="register mt-5 py-5">
    <div class="container">
        <div class="row">
            <!-- First Name -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="Fname">First Name:</label>
                    <input type="text" id="Fname" name="Fname" placeholder="Enter your first name" required>
                </div>
            </div>

            <!-- Last Name -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="Lname">Last Name:</label>
                    <input type="text" id="Lname" name="Lname" placeholder="Enter your last name" required>
                </div>
            </div>

            <!-- Username -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="Username">Username:</label>
                    <input type="text" id="Username" name="username" placeholder="Enter your username" required
                        pattern="^[a-zA-Z0-9_]{5,20}$" title="Username must be 5-20 characters long and can contain only letters, numbers, and underscores.">
                </div>
            </div>

            <!-- Email -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="Email">Email:</label>
                    <input type="email" id="Email" name="email" placeholder="Enter your email" required>
                </div>
            </div>

            <!-- Password -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="Password">Password:</label>
                    <input type="password" id="Password" name="password" placeholder="Enter your password" required
                        pattern="^(?=.*[A-Z])(?=.*\d).{8,}$"
                        title="Password must be at least 8 characters long, contain at least one uppercase letter, and one number.">
                </div>
            </div>

            <!-- Phone Number -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="PhoneNumber">Phone Number:</label>
                    <input type="tel" id="PhoneNumber" name="number" placeholder="Enter your phone number" required
                        pattern="\d{10,}" title="Phone number must be at least 10 digits.">
                </div>
            </div>

            <!-- Experience -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="experience">Experience (years):</label>
                    <input type="number" id="experience" name="experience" placeholder="Enter your experience" min="0" required>
                </div>
            </div>

            <!-- Fee -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="fee">Consultation Fee/Per session (SAR):</label>
                    <input type="number" id="fee" name="fee" placeholder="Enter your fee" min="0" step="0.01" required>
                </div>
            </div>

            <!-- Company -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="company">Company Name:</label>
                    <input type="text" id="company" name="company" placeholder="Enter your company name" required>
                </div>
            </div>
        </div>
        <input type="submit" value="Register as Consultant" class="btn btn-primary">
        <p>Already have an account? <a href="login.php" class="link-color">Login here</a>.</p>
        <p>New here? <a href="register.php">Register now</a>.</p>
    </div>
</form>
