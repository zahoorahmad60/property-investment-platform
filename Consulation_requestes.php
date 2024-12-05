<?php
session_start();
ob_start(); // Start output buffering

include 'init.php'; // Database connection
include 'consultant_sidebar.php';

// Check if the user is logged in as a consultant
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'consultant') {
    header("Location: login.php");
    exit();
}

$consultant_id = $_SESSION['user_id'];

// Fetch consultation requests from investors
$stmt = $conn->prepare("
    SELECT c.session_number, i.Fname AS investor_fname, i.Lname AS investor_lname, 
           i.email AS investor_email, i.phone AS investor_phone, c.date, c.rejection_reason,
           c.time, c.description, c.status, i.investor_ID 
    FROM Consultation c 
    INNER JOIN Investor i ON c.investor_ID = i.investor_ID 
    WHERE c.consultant_ID = :consultant_id
    ORDER BY c.date DESC
");
$stmt->bindParam(':consultant_id', $consultant_id, PDO::PARAM_INT);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update request based on form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $session_number = intval($_POST['session_number']);
    if ($_POST['action'] === 'approve') {
        $zoom_link = $_POST['zoom_link'];
        $stmt = $conn->prepare("UPDATE Consultation SET status = 'approved', zoom_link = :zoom_link WHERE session_number = :session_number");
        $stmt->bindParam(':zoom_link', $zoom_link);
        $stmt->bindParam(':session_number', $session_number);
        $stmt->execute();
    } elseif ($_POST['action'] === 'disapprove') {
        $rejection_message = $_POST['rejection_message'];
        $stmt = $conn->prepare("UPDATE Consultation SET status = 'disapproved', rejection_reason = :rejection_message WHERE session_number = :session_number");
        $stmt->bindParam(':rejection_message', $rejection_message);
        $stmt->bindParam(':session_number', $session_number);
        $stmt->execute();
    }
    header("Location: Consulation_requestes.php");
    exit();
}

// Filter requests based on status
$pendingRequests = array_filter($requests, fn($req) => $req['status'] === 'pending');
$approvedRequests = array_filter($requests, fn($req) => $req['status'] === 'approved');
$rejectedRequests = array_filter($requests, fn($req) => $req['status'] === 'disapproved');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Consultation Requests</title>
    <style>
        :root {
            --primary-color: #3DA5D9;
            --secondary-color: #2A3B4C;
            --background-color: #1D2939;
            --text-color: #E8E8E8;
            --card-bg-color: #1E2D3D;
            --button-color: #3A7EBA;
            --hover-color: #5A96D8;
            --table-border-color: #2C3E50;
            --success-color: #2ECC71;
            --danger-color: #E74C3C;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center; /* Centers child elements horizontally */
            padding: 0 40px;
            max-width: 1200px; /* Optional: Set a max-width for better centering control */
            margin: 0 auto; /* Centers the wrapper itself horizontally */
            width: calc(100% - 30px);
            box-sizing: border-box;
        }

        h1 {
            color: var(--primary-color);
            margin: 20px 0;
            text-align: center;
            font-size: 2em;
            width: 100%;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }

        .button-group .btn {
            width: 150px;
            padding: 12px;
            font-size: 1em;
            text-align: center;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.3s;
            color: white;
            text-decoration: none;
        }

        .button-group .btn-warning {
            background-color: #f39c12;
        }

        .button-group .btn-warning:hover {
            background-color: #e67e22;
        }

        .button-group .btn-success {
            background-color: var(--success-color);
        }

        .button-group .btn-success:hover {
            background-color: #27ae60;
        }

        .button-group .btn-danger {
            background-color: var(--danger-color);
        }

        .button-group .btn-danger:hover {
            background-color: #c0392b;
        }

        .section {
            background-color: var(--card-bg-color);
            width: 100%;
            border-radius: 10px;
            margin: 15px 0;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            box-sizing: border-box;
            display: none; /* Hide all sections by default */
        }

        .section.active {
            display: block; /* Show the active section */
        }

        .section:hover {
            transform: translateY(-5px);
        }

        .section h2 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.8em;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid var(--table-border-color);
        }

        th, td {
            padding: 15px;
            text-align: center;
            color: var(--text-color);
            font-size: 1em;
        }

        th {
            background-color: var(--secondary-color);
            font-size: 1.1em;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #2A3B4C;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 1.5em;
            }

            .section {
                padding: 15px;
            }

            th, td {
                font-size: 0.9em;
                padding: 10px;
            }
        }

        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.5); /* Black w/ opacity */
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--card-bg-color);
            padding: 20px;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            color: var(--text-color);
            position: relative;
        }

        .modal-content h3 {
            margin-top: 0;
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: var(--text-color);
            font-size: 1.5em;
            cursor: pointer;
        }

        /* Button Styling */
        .btn-approve, .btn-disapprove, .btn-submit {
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            border: none;
            margin: 5px;
        }

        .btn-approve {
            background-color: #28a745;
        }

        .btn-approve:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-disapprove {
            background-color: #dc3545;
        }

        .btn-disapprove:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .btn-submit {
            background-color: #007bff;
        }

        .btn-submit:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Input field styling */
        .modal-content input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #2A3B4C;
            color: #E8E8E8;
        }

        .modal-content label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <h1>Consultation Requests</h1>

    <!-- Button group for sections -->
    <div class="button-group">
        <button class="btn btn-warning" onclick="showSection('pending-section')">Pending</button>
        <button class="btn btn-success" onclick="showSection('approved-section')">Approved</button>
        <button class="btn btn-danger" onclick="showSection('rejected-section')">Rejected</button>
    </div>

    <!-- Pending Requests -->
    <div id="pending-section" class="section active">
        <h2>Pending Requests</h2>
        <?php if (count($pendingRequests) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Investor Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingRequests as $request): ?>
                        <tr>
                            <td>
                                <a href="consultant_profile.php?id=<?php echo htmlspecialchars($request['investor_ID']); ?>">
                                    <?php echo htmlspecialchars($request['investor_fname'] . ' ' . $request['investor_lname']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($request['investor_email']); ?></td>
                            <td><?php echo htmlspecialchars($request['investor_phone']); ?></td>
                            <td><?php echo htmlspecialchars($request['date']); ?></td>
                            <td><?php echo htmlspecialchars($request['time']); ?></td>
                            <td><?php echo htmlspecialchars($request['description']); ?></td>
                            <td>
                                <!-- Approval Buttons -->
                                <button type="button" class="btn btn-approve" onclick="openModal('approve', <?php echo $request['session_number']; ?>)">Approve</button>
                                <button type="button" class="btn btn-disapprove" onclick="openModal('disapprove', <?php echo $request['session_number']; ?>)">Disapprove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No pending requests found.</p>
        <?php endif; ?>
    </div>

    <!-- Approved Requests -->
    <div id="approved-section" class="section">
        <h2>Approved Requests</h2>
        <?php if (count($approvedRequests) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Investor Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Description</th>
                        <th>Zoom Link</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($approvedRequests as $request): ?>
                        <tr>
                            <td>
                                <a href="consultant_profile.php?id=<?php echo htmlspecialchars($request['investor_ID']); ?>">
                                    <?php echo htmlspecialchars($request['investor_fname'] . ' ' . $request['investor_lname']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($request['investor_email']); ?></td>
                            <td><?php echo htmlspecialchars($request['investor_phone']); ?></td>
                            <td><?php echo htmlspecialchars($request['date']); ?></td>
                            <td><?php echo htmlspecialchars($request['time']); ?></td>
                            <td><?php echo htmlspecialchars($request['description']); ?></td>
                            <td><?php echo htmlspecialchars($request['zoom_link'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No approved requests found.</p>
        <?php endif; ?>
    </div>

    <!-- Rejected Requests -->
    <div id="rejected-section" class="section">
        <h2>Rejected Requests</h2>
        <?php if (count($rejectedRequests) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Investor Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Description</th>
                        <th>Rejection Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rejectedRequests as $request): ?>
                        <tr>
                            <td>
                                <a href="consultant_profile.php?id=<?php echo htmlspecialchars($request['investor_ID']); ?>">
                                    <?php echo htmlspecialchars($request['investor_fname'] . ' ' . $request['investor_lname']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($request['investor_email']); ?></td>
                            <td><?php echo htmlspecialchars($request['investor_phone']); ?></td>
                            <td><?php echo htmlspecialchars($request['date']); ?></td>
                            <td><?php echo htmlspecialchars($request['time']); ?></td>
                            <td><?php echo htmlspecialchars($request['description']); ?></td>
                            <td><?php echo htmlspecialchars($request['rejection_reason']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No rejected requests found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Action Modal -->
<div id="actionModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <h3 id="modalTitle">Title</h3>
        <form method="POST">
            <input type="hidden" name="session_number" id="modalSessionNumber">
            <input type="hidden" name="action" id="modalAction">
            <div id="modalBody">
                <!-- Input fields will be inserted here -->
            </div>
            <button type="submit" class="btn btn-submit">Submit</button>
        </form>
    </div>
</div>

<script>
    function showSection(sectionId) {
        document.querySelectorAll('.section').forEach(section => section.classList.remove('active'));
        document.getElementById(sectionId).classList.add('active');
    }

    function openModal(action, sessionNumber) {
        const modal = document.getElementById('actionModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        const modalSessionNumber = document.getElementById('modalSessionNumber');
        const modalAction = document.getElementById('modalAction');

        modalSessionNumber.value = sessionNumber;
        modalAction.value = action;

        if (action === 'approve') {
            modalTitle.textContent = 'Approve Consultation';
            modalBody.innerHTML = `
                <label for="zoom_link">Zoom Link:</label>
                <input type="text" name="zoom_link" id="zoom_link" required>
            `;
        } else if (action === 'disapprove') {
            modalTitle.textContent = 'Disapprove Consultation';
            modalBody.innerHTML = `
                <label for="rejection_message">Rejection Message:</label>
                <input type="text" name="rejection_message" id="rejection_message" required>
            `;
        }

        modal.style.display = 'flex';
    }

    function closeModal() {
        const modal = document.getElementById('actionModal');
        modal.style.display = 'none';
    }

    // Close modal when clicking outside of modal content
    window.onclick = function(event) {
        const modal = document.getElementById('actionModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Initialize by showing the pending section
    document.addEventListener('DOMContentLoaded', () => showSection('pending-section'));
</script>
<?php
ob_end_flush(); // Flush the output buffer and send the output
?>

</body>
</html>
