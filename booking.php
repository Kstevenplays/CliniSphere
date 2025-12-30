<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CliniSphere - Book Your Appointment</title>
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
                <li><a href="booking.php" class="active">Book Appointment</a></li>
                <li id="user-menu" style="display: none;">
                    <a href="#" onclick="toggleUserMenu()">My Account</a>
                    <ul class="dropdown">
                        <li><a href="my_appointments.php">My Appointments</a></li>
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
</body>
</html>
