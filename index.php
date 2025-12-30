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
    <title>CliniSphere - Online Clinic Booking System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>CliniSphere</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Home</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="booking.php">Book Appointment</a></li>
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
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <h1>Welcome to CliniSphere</h1>
            <p>Your trusted online clinic booking platform</p>
            <a href="<?php echo $isLoggedIn ? 'booking.php' : 'login.php'; ?>" class="btn btn-primary">Book Now</a>
        </div>
    </header>

    <section class="features">
        <div class="container">
            <h2>Why Choose CliniSphere?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📅</div>
                    <h3>Easy Scheduling</h3>
                    <p>Book appointments with our doctors at your convenience with real-time slot availability.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">✓</div>
                    <h3>Instant Confirmation</h3>
                    <p>Receive email confirmations and updates about your appointments immediately.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👨‍⚕️</div>
                    <h3>Expert Doctors</h3>
                    <p>Access qualified healthcare professionals across various specializations.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Secure & Private</h3>
                    <p>Your health information is protected with industry-standard security measures.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="doctors-preview">
        <div class="container">
            <h2>Our Doctors</h2>
            <div id="doctors-preview" class="doctors-grid">
                <p>Loading doctors...</p>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 CliniSphere. All rights reserved. | Privacy Policy | Terms of Service</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
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

        // Load preview of doctors on home page
        fetch('api/doctors.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const doctorsPreview = document.getElementById('doctors-preview');
                    doctorsPreview.innerHTML = '';
                    
                    data.data.slice(0, 3).forEach(doctor => {
                        const doctorCard = document.createElement('div');
                        doctorCard.className = 'doctor-card';
                        const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
                        const bookUrl = isLoggedIn ? 'booking.php' : 'login.php';
                        doctorCard.innerHTML = `
                            <div class="doctor-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                            <h3>${doctor.first_name} ${doctor.last_name}</h3>
                            <p class="specialization">${doctor.specialization || 'General Practitioner'}</p>
                            <a href="${bookUrl}" class="btn btn-small">Book Appointment</a>
                        `;
                        doctorsPreview.appendChild(doctorCard);
                    });
                }
            });
    </script>
</body>
</html>
login