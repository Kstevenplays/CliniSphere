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
                <li><a href="booking.php">Book Appointment</a></li>
                <li id="user-menu" style="display: none;">
                    <a href="#" onclick="toggleUserMenu()">My Account</a>
                    <ul class="dropdown">
                        <li><a href="my_appointments.php" class="active">My Appointments</a></li>
                        <li><a href="#" onclick="logout()">Logout</a></li>
                    </ul>
                </li>
                <li id="auth-menu">
                    <a href="login.php">Login</a> | <a href="register.php">Register</a>
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
</body>
</html>
