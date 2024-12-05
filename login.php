<?php
// Enable error reporting for debugging (remove or set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "Login";
include "init.php"; // Ensure this file correctly sets up the database connection

$error_message = '';

// Check if there's a message in the session to display as a JavaScript alert
if (isset($_SESSION['message'])) {
    // Escape the message to prevent JavaScript injection
    $message = addslashes($_SESSION['message']);
    echo "<script type='text/javascript'>
            window.onload = function() {
                alert('" . $message . "');
            };
          </script>";
    // Unset the message after displaying it
    unset($_SESSION['message']);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $user_type = trim($_POST['user_type']);
    
    // Validate user type
    $allowed_types = ['investor', 'consultant', 'seller'];
    if (!in_array($user_type, $allowed_types)) {
        $error_message = "Invalid user type selected.";
    } else {
        // Determine the table and ID field based on user type
        $user_map = [
            'investor' => ['table' => 'Investor', 'id_column' => 'investor_ID'],
            'consultant' => ['table' => 'Consultant', 'id_column' => 'consultant_ID'],
            'seller' => ['table' => 'Seller', 'id_column' => 'seller_ID'],
        ];
        
        $table = $user_map[$user_type]['table'];
        $id_column = $user_map[$user_type]['id_column'];
        
        try {
            // Prepare the SQL statement to fetch user data
            $stmt = $conn->prepare("SELECT $id_column, Fname, email, password, status, approve, rejection_reason FROM $table WHERE username = :username LIMIT 1");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Check the user's account status
                if ($user['approve'] == 1) { // Approved
                    // Verify the password
                    if (password_verify($password, $user['password'])) {
                        // Password is correct; set session variables
                        $_SESSION['username'] = $username;
                        $_SESSION['user_type'] = $user_type;
                        $_SESSION['user_id'] = $user[$id_column];
                        
                        // Redirect based on user type
                        switch ($user_type) {
                            case 'investor':
                                header("Location: investor_welcome.php");
                                break;
                            case 'consultant':
                                header("Location: consultant_welcome.php");
                                break;
                            case 'seller':
                                header("Location: seller/seller_welcome.php");
                                break;
                            default:
                                header("Location: user_dashboard.php"); // Fallback
                        }
                        exit(); // Ensure no further code is executed
                    } else {
                        // Incorrect password
                        $error_message = "Incorrect password.";
                    }
                } elseif ($user['approve'] == 0) { // Pending approval
                    $error_message = "Your account is pending admin approval.";
                } elseif ($user['approve'] == -1) { // Rejected
                    // Get the rejection reason if it exists
                    $rejection_reason = isset($user['rejection_reason']) ? $user['rejection_reason'] : 'No reason provided.';
                    $error_message = "Your account has been rejected. Reason: " . htmlspecialchars($rejection_reason);
                } else {
                    // Undefined status
                    $error_message = "Unknown account status. Please contact support.";
                }
            } else {
                // User not found
                $error_message = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            // Log the error in a real application
            $error_message = "An error occurred while processing your request.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
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
     <style>
        /* Styles for the modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;

            padding-top: 60px;
        }
        .modal-content {
            background-color: #1B263B;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Login</h2>
        
        <!-- Display error message if any -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <form action="login.php" method="post">
            <label for="user_type">User Type:</label>
            <select id="user_type" name="user_type" required>
                <option value="" disabled selected>Select your role</option>
                <option value="investor">Investor</option>
                <option value="consultant">Consultant</option>
                <option value="seller">Seller</option>
            </select>
            
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required autofocus>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            
            <input type="submit" value="Login">
        </form>
        
        <div class="links">
        <p>New here? <a href="register.php">Register now</a>.</p>
        <p><a href="#" id="forgot-password-link">Forgot Password?</a></p>
    </div>
    </div>
    
    <!-- Forgot Password Modal -->
    <div id="forgot-password-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Forgot Password</h2>
            <form id="forgot-password-form" action="forgot_password.php" method="post">
                <label for="user_type_modal">User Type:</label>
                <select id="user_type_modal" name="user_type" required>
                    <option value="" disabled selected>Select your role</option>
                    <option value="investor">Investor</option>
                    <option value="consultant">Consultant</option>
                    <option value="seller">Seller</option>
                </select>

                <label for="identifier">Email/Username:</label>
                <input type="text" id="identifier" name="identifier" placeholder="Enter your email or username" required>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById("forgot-password-modal");
        var btn = document.getElementById("forgot-password-link");
        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
