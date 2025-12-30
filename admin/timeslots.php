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
    <title>Manage Time Slots - CliniSphere Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>CliniSphere - Admin</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="doctors.php">Doctors</a></li>
                <li><a href="timeslots.php" class="active">Time Slots</a></li>
                <li><a href="#" onclick="logout()">👤 <?php echo htmlspecialchars($adminName); ?> (Logout)</a></li>
            </ul>
        </div>
    </nav>

    <div class="container admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Manage Time Slots</h2>
            <button class="btn btn-primary" onclick="openAddSlotForm()">Add Time Slot</button>
        </div>
        
        <div style="background: white; padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
            <h3>Quick Add Slots</h3>
            <div class="form-group">
                <label for="slot-doctor">Select Doctor:</label>
                <select id="slot-doctor" required>
                    <option value="">-- Choose a Doctor --</option>
                </select>
            </div>
            <div class="form-group">
                <label for="slot-date">Date:</label>
                <input type="date" id="slot-date" required>
            </div>
            <div class="form-group">
                <label for="slot-time">Time:</label>
                <input type="time" id="slot-time" required>
            </div>
            <button class="btn btn-primary" onclick="addTimeSlot()">Add Slot</button>
        </div>

        <h3>Existing Time Slots</h3>
        <div id="slots-list" style="display: grid; gap: 1rem;">
            <p>Loading time slots...</p>
        </div>
    </div>

    <script src="../js/timeslots-admin.js"></script>
</body>
</html>
