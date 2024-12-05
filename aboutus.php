<?php
session_start();
$pageTitle = "About Us - Hissatk";
include "init.php";
?>

<link rel="stylesheet" href="layout/css/H.css">

<!-- Main Content -->
<main>
    <div class="about-container">
        <h1 class="highlight">Know us more</h1>
        <div class="about-section">
<h2> Frequently asked questions</h2>
        </div>
        <!-- Cards Section -->
        <div class="about-cards">
            <div class="about-card">
                <h3>Who are we?</h3>
                <p> <br> Hissatk is a platform for real estate investors and sellers. Where you can invest in portions of properties and earn proportional profits, making real estate investments accessible for everyone. We also  offer consultation session to guide you on your journey.</p>
            </div>
            <div class="about-card">
                <h3>How does it work?</h3>
                <p><br>Property is listed on Hissatk. <br> <br> Investors completed the fund.
               <br> <br> SPV is handled by the seller.
              <br> <br> Rental distirbution begins after 1 Year from completing property funding. <div class="
            "></div></p>
            </div>
            <div class="about-card">
                <h3>Expert Guidance</h3>
                <p><br> We offer consultation sessions from top industry experts to help you make tailored investment decisions to meet your budget and needs.</p>
            </div>
            <div class="about-card">
                <h3>Who can list properties on the website?
                
                </h3>
                <p><br>Only registered sellers can list properties after completing their registration recieving approval from the platform.</p>
            </div>
            <div class="about-card">
                <h3>How do I schedule a consultation?

                
                </h3>
                <p><br>Investors can click on the "Request a Consultation" button, select a consultant, choose a time, and provide details about their query</p>
            </div>

            <div class="about-card">
                <h3>Do I need to register to use Hissatk?
                </h3>
                <p><br>Yes, registration is required to access features like property investment, consultations, and property listing.
                </p>
            </div>
            <div class="about-card">
                <h3>Can I invest in properties without owning them fully?
                </h3>
                <p><br>Yes, the website allows fractional ownership by letting you invest in property shares.</p>
</Div>
<div class="about-card">
                <h3>How can I contact support if I face issues
                </h3>
                <p><br>You can reach out to the support team via email: support@hissatk.com.
                </p>
</Div>
<div class="about-card">
                <h3>Can I withdraw my investmet?
                </h3>
                <p><br>No, as investments in property go towards building the property and setting it up for leasing to later on help you get a return on your investment.
                </p>
</Div>
<div class="about-card">
                <h3>More questions?
                </h3>
<p><br>For further questions or assistance, email us at support@hissatk.com.</p>      
</Div>













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
        padding-top: 80px; /* Reserve space for fixed navbar */
        display: block;
    }

    /* About Section Styling */
    .about-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 60px 20px;
        background-color: #2E4057;
        border-radius: 10px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.5);
        max-width: 900px;
        margin: 50px auto;
    }

    .about-container h1 {
        font-size: 3em;
        color: #3DA5D9;
        margin-bottom: 20px;
    }

    .about-section h2, .about-section h3 {
        margin-bottom: 15px;
        color: #E0E0E0;
        text-align: center;
    }

    .about-section h3 {
        font-size: 1.3em;
        margin-top: 30px;
        color: #3DA5D9;
        line-height: 1.8;
    }

    .highlight {
        color: #3DA5D9;
        font-weight: bold;
    }

    .attention-grabber {
        color: #FF6F61;
        font-weight: bold;
    }

    /* Cards Section */
    .about-cards {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px; /* Space between cards */
        margin-top: 30px;
        width: 100%;
    }

    .about-card {
        background-color: #415A77;
        border-radius: 10px;
        padding: 20px;
        flex: 1;
        min-width: 250px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s;
    }

    .about-card h3 {
        color: #3DA5D9;
        margin-bottom: 10px;
        font-size: 1.5em;
    }

    .about-card p {
        color: #E0E0E0;
        font-size: 1.1em;
    }

    .about-card:hover {
        transform: translateY(-5px); /* Lift effect on hover */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .about-cards {
            flex-direction: column;
            align-items: center;
        }

        .about-card {
            width: 100%;
            max-width: 500px;
        }
    }
</style>

</body>
</html>
