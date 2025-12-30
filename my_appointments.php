<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['name'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'patient';

// Redirect if not logged in
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

// Redirect admins to admin panel
if ($userRole === 'admin') {
    header('Location: admin/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - CliniSphere</title>
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="booking.php">Book Appointment</a></li>
                <li><a href="my_appointments.php" class="active">My Appointments</a></li>
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
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2>My Appointments</h2>
        <div id="appointments-list" class="appointments-grid">
            <p>Loading your appointments...</p>
        </div>
    </div>

    <script src="js/appointments.js"></script>
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
