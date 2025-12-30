<?php
session_start();

// Update session activity
$_SESSION['last_activity'] = time();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$adminName = $_SESSION['name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CliniSphere</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>CliniSphere - Admin</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="doctors.php">Doctors</a></li>
                <li><a href="timeslots.php">Time Slots</a></li>
                <li><a href="#" onclick="logout()">👤 <?php echo htmlspecialchars($adminName); ?> (Logout)</a></li>
            </ul>
        </div>
    </nav>

    <div class="container admin-container">
        <h2>Pending Appointments</h2>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Doctor Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Reason</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="appointments-table-body">
                <tr><td colspan="7" style="text-align: center;">Loading...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Approval Modal -->
    <div id="approvalModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 8px; width: 90%; max-width: 500px;">
            <h3>Approve/Reject Appointment</h3>
            <div class="form-group">
                <label for="modal-notes">Notes (optional):</label>
                <textarea id="modal-notes" rows="4"></textarea>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button class="btn btn-success" onclick="approveAppointment()">Approve</button>
                <button class="btn btn-danger" onclick="rejectAppointment()">Reject</button>
                <button class="btn" style="background-color: #95a5a6;" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
</body>
</html>
