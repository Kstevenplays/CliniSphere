<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['name'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'patient';

// Redirect admins to admin panel
if ($isLoggedIn && $userRole === 'admin') {
    header('Location: admin/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - CliniSphere</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>CliniSphere</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                <?php endif; ?>
                <li><a href="booking.php" class="active">Book Appointment</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="my_appointments.php">My Appointments</a></li>
                    <?php if ($userRole === 'admin'): ?>
                        <li><a href="admin/index.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li>
                        <a href="#" onclick="toggleUserMenu()">👤 <?php echo htmlspecialchars($userName); ?></a>
                        <ul class="dropdown">
                            <li><a href="profile.php">My Profile</a></li>
                            <li><a href="#" onclick="logout()">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="login.php">Login</a> | <a href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="booking-container">
            <h2>Schedule Your Appointment</h2>
            
            <div class="booking-form">
                <div class="form-group">
                    <label for="doctor">Select Doctor:</label>
                    <select id="doctor" required>
                        <option value="">-- Choose a Doctor --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="appointment_date">Appointment Date:</label>
                    <input type="date" id="appointment_date" required>
                </div>

                <div class="form-group">
                    <label for="appointment_time">Appointment Time:</label>
                    <select id="appointment_time" required>
                        <option value="">-- Select Time Slot --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reason">Reason for Visit:</label>
                    <textarea id="reason" rows="4" placeholder="Please describe your symptoms or reason for visit"></textarea>
                </div>

                <button class="btn btn-primary" onclick="bookAppointment()">Book Appointment</button>
            </div>

            <div id="message" class="message" style="display: none;"></div>
        </div>
    </div>

    <script src="js/booking.js"></script>
    <script>
        function toggleUserMenu() {
            const dropdown = document.querySelector('.dropdown');
            if (dropdown) {
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            }
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                fetch('api/logout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        window.location.href = 'index.php';
                    })
                    .catch(err => {
                        console.error('Logout error:', err);
                        window.location.href = 'index.php';
                    });
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.dropdown');
            const navMenu = document.querySelector('.nav-menu');
            if (dropdown && !navMenu.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
</body>
</html>
