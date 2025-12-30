<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Service.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Redirect admins to admin dashboard
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: admin/index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? 'patient';
$userName = $_SESSION['name'] ?? 'User';

// Get user details
$userService = new UserService($conn);
$user = $userService->getUserById($userId);

// Get appointments based on role
$appointmentService = new AppointmentService($conn);
if ($userRole === 'patient') {
    $appointments = $appointmentService->getPatientAppointments($userId);
} else {
    $appointments = [];
}

// Get statistics
$stats = [];
if ($userRole === 'patient') {
    $result = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE patient_id = $userId");
    $stats['total_appointments'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as pending FROM appointments WHERE patient_id = $userId AND status = 'pending'");
    $stats['pending_appointments'] = $result->fetch_assoc()['pending'];
    
    $result = $conn->query("SELECT COUNT(*) as approved FROM appointments WHERE patient_id = $userId AND status = 'approved'");
    $stats['approved_appointments'] = $result->fetch_assoc()['approved'];
} elseif ($userRole === 'doctor') {
    $result = $conn->query("SELECT id FROM doctors WHERE user_id = $userId");
    $doctor = $result->fetch_assoc();
    if ($doctor) {
        $doctorId = $doctor['id'];
        
        $result = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE doctor_id = $doctorId");
        $stats['total_appointments'] = $result->fetch_assoc()['total'];
        
        $result = $conn->query("SELECT COUNT(*) as pending FROM appointments WHERE doctor_id = $doctorId AND status = 'pending'");
        $stats['pending_appointments'] = $result->fetch_assoc()['pending'];
        
        $result = $conn->query("SELECT COUNT(*) as approved FROM appointments WHERE doctor_id = $doctorId AND status = 'approved'");
        $stats['approved_appointments'] = $result->fetch_assoc()['approved'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CliniSphere</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .dashboard-header h1 {
            margin: 0;
            font-size: 2.5rem;
        }

        .dashboard-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            animation: slideUp 0.6s ease-out;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .stat-card .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin: 1rem 0;
        }

        .stat-card .stat-label {
            color: #666;
            font-size: 0.95rem;
        }

        .stat-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .appointments-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .appointments-section h2 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 2px solid #667eea;
            padding-bottom: 1rem;
        }

        .appointment-list {
            margin-top: 1.5rem;
        }

        .appointment-item {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .appointment-item:hover {
            background: #f0f2f5;
            transform: translateX(5px);
        }

        .appointment-details h3 {
            margin: 0 0 0.5rem 0;
            color: #2c3e50;
        }

        .appointment-details p {
            margin: 0.25rem 0;
            color: #666;
            font-size: 0.9rem;
        }

        .appointment-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .appointment-status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .appointment-status.approved {
            background: #d4edda;
            color: #155724;
        }

        .appointment-status.completed {
            background: #cfe2ff;
            color: #084298;
        }

        .appointment-status.cancelled {
            background: #f8d7da;
            color: #842029;
        }

        .no-appointments {
            text-align: center;
            padding: 2rem;
            color: #666;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            font-weight: 500;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>CliniSphere</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <?php if ($userRole === 'patient'): ?>
                    <li><a href="booking.php">Book Appointment</a></li>
                    <li><a href="my_appointments.php">My Appointments</a></li>
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

    <div class="dashboard-header">
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($userName); ?>! 👋</h1>
            <p>Here's your dashboard overview</p>
        </div>
    </div>

    <div class="container">
        <!-- Quick Actions -->
        <div class="quick-actions">
            <?php if ($userRole === 'patient'): ?>
                <a href="booking.php" class="action-btn">📅 Book New Appointment</a>
                <a href="my_appointments.php" class="action-btn">📋 View My Appointments</a>
                <a href="profile.php" class="action-btn">👤 Edit Profile</a>
            <?php elseif ($userRole === 'doctor'): ?>
                <a href="dashboard.php?view=appointments" class="action-btn">📅 My Appointments</a>
                <a href="profile.php" class="action-btn">👤 Edit Profile</a>
            <?php endif; ?>
        </div>

        <!-- Statistics -->
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-label">Total Appointments</div>
                <div class="stat-number"><?php echo $stats['total_appointments'] ?? 0; ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-label">Pending</div>
                <div class="stat-number"><?php echo $stats['pending_appointments'] ?? 0; ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-label">Approved</div>
                <div class="stat-number"><?php echo $stats['approved_appointments'] ?? 0; ?></div>
            </div>
        </div>

        <!-- Appointments Section -->
        <?php if ($userRole === 'patient' || $userRole === 'doctor'): ?>
            <div class="appointments-section">
                <h2>📅 Your Appointments</h2>
                
                <?php if (count($appointments) > 0): ?>
                    <div class="appointment-list">
                        <?php foreach (array_slice($appointments, 0, 5) as $appointment): ?>
                            <div class="appointment-item">
                                <div class="appointment-details">
                                    <h3>
                                        <?php 
                                        if ($userRole === 'patient') {
                                            echo htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']);
                                        } else {
                                            echo "Appointment #" . $appointment['id'];
                                        }
                                        ?>
                                    </h3>
                                    <p>
                                        <strong>Date:</strong> 
                                        <?php echo date('F d, Y', strtotime($appointment['appointment_date'])); ?>
                                    </p>
                                    <p>
                                        <strong>Time:</strong> 
                                        <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?>
                                    </p>
                                    <?php if ($appointment['reason_for_visit']): ?>
                                        <p>
                                            <strong>Reason:</strong> 
                                            <?php echo htmlspecialchars(substr($appointment['reason_for_visit'], 0, 50)); ?>...
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <span class="appointment-status <?php echo strtolower($appointment['status']); ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($appointments) > 5): ?>
                        <div style="text-align: center; margin-top: 1.5rem;">
                            <a href="my_appointments.php" class="btn btn-primary">View All Appointments</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="no-appointments">
                        <p>No appointments yet.</p>
                        <?php if ($userRole === 'patient'): ?>
                            <p><a href="booking.php" style="color: #667eea; text-decoration: none; font-weight: 500;">Book your first appointment</a></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 CliniSphere. All rights reserved. | <a href="#" style="color: inherit;">Privacy Policy</a> | <a href="#" style="color: inherit;">Terms of Service</a></p>
        </div>
    </footer>

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
                    .then(response => response.json())
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
