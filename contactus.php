<?php
session_start();
$pageTitle = "Contact Us";
include "init.php";
?>
<link rel="stylesheet" href="layout/css/contactus.css">

<!-- Main Content -->
<main class="contact-container">
    <h1 class="contact-header">Get in touch with us</h1>
    <div class="contact-cards">
        <div class="contact-card">
            <h3>Email Us</h3>
            <p>For any inquiries, email us:</p>
            <p><a href="mailto:Hissatk@company.com">Hissatk@company.com</a></p>
        </div>
        <div class="contact-card">
            <h3>Call Us</h3>
            <p>Reach us at:</p>
            <p><a href="tel:123456789">+555-233-989</a></p>
        </div>
        <div class="contact-card">
            <h3>Visit Us</h3>
            <p>Our address:</p>
            <p>Al-Salam street,<br> AL-Ahsa city, Saudi Arabia</p>
        </div>
    </div>
</main>

<!-- CSS -->
<style>
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #0D1B2A;
        color: #ffffff;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .contact-container {
        padding: 50px 20px;
        background-color: #1B263B;
        border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        max-width: 1200px;
        margin: 20px auto;
        width: 90%;
    }

    .contact-header {
        font-size: 2.5em;
        margin-bottom: 40px;
        text-align: center;
        color: #3DA5D9;
        font-weight: bold;
    }

    .contact-cards {
        display: flex;
        justify-content: space-between;
        gap: 20px; /* Space between cards */
        flex-wrap: wrap;
    }

    .contact-card {
        background-color: #415A77;
        border-radius: 10px;
        padding: 20px;
        flex: 1;
        min-width: 280px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .contact-card h3 {
        font-size: 1.8em;
        color:  #3DA5D9;
        margin-bottom: 10px;
    }

    .contact-card p {
        font-size: 1.2em;
        margin: 10px 0;
        color: #E0E0E0;
    }

    .contact-card p a {
        color: #3DA5D9;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s;
    }

    .contact-card p a:hover {
        color: #FF6F61;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .contact-cards {
            flex-direction: column;
            align-items: center;
        }

        .contact-card {
            width: 100%;
            max-width: 500px;
        }
    }
</style>
