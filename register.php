<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "init.php";

$pageTitle = "Register";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $user_type = $_POST['user_type'];
    $fname = $_POST['Fname'];
    $lname = $_POST['Lname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dob = $_POST['date'];
    $phone_number = $_POST['number'];
    $id = $_POST['ID'];

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Redirect to login page or show a success message
    header("Location: login.php");
    exit();
}
?>

<!-- External CSS file -->
<link rel="stylesheet" href="layout/css/reg.css">

<style>
    /* Styles for the tab navigation */
    .nav-tabs {
        border-bottom: 2px solid #ddd;
        background-color: #f8f9fa;
    }

    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        color: #495057;
        font-size: 18px;
        padding: 15px 20px;
        transition: all 0.3s ease;
        background-color: #e9ecef;
    }

    /* Hover and active effects for the tabs */
    .nav-tabs .nav-link:hover {
        background-color: #dee2e6;
        color: #007bff;
    }

    .nav-tabs .nav-link.active {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff #007bff #fff;
    }

    /* Styles for the registration heading */
    .registration-heading {
        text-align: center;
        font-size: 32px;
        font-weight: bold;
        color: #007bff;
        margin-bottom: 30px;
    }

    /* Style adjustments for the container */
    .container {
        background-color: ##0D1B2A;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
    }

    /* Form group styling */
    .form-group label {
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }

    .form-group input, .form-group select {
        font-size: 16px;
        padding: 10px;
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    /* Button styling */
    .btn-register {
        background-color: #007bff;
        color: #fff;
        font-size: 18px;
        padding: 10px 30px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-register:hover {
        background-color: #0056b3;
    }

    /* Responsive design for small screens */
    @media (max-width: 768px) {
        .nav-tabs .nav-link {
            font-size: 16px;
            padding: 10px;
        }
        .registration-heading {
            font-size: 24px;
        }
    }
</style>

<div class="container mt-5 py-5">
    <!-- Heading for the Registration Page -->
    <h1 class="registration-heading">User Registration</h1>

    <!-- Navigation Tabs for User Types -->
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs justify-content-center">
                <li class="nav-item">
                    <a class="nav-link" id="seller-tab" href="register_seller.php">Seller</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="consultant-tab" href="register_consultant.php">Consultant</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="investor-tab" href="register_investor.php">Investor</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Content for the Registration Forms (will load based on the tab selected) -->
    <div class="tab-content mt-4">
        <div class="tab-pane active" id="registerSeller">
            <!-- Content for the form pages will be dynamically loaded here -->
        </div>
    </div>
</div>
