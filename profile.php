<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Service.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Redirect admins to admin panel
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: admin/index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? 'patient';
$userName = $_SESSION['name'] ?? 'User';

$userService = new UserService($conn);
$user = $userService->getUserById($userId);

$message = '';
$messageType = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    if ($firstName && $lastName) {
        $sql = "UPDATE users SET first_name = ?, last_name = ?, phone = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $firstName, $lastName, $phone, $userId);
        
        if ($stmt->execute()) {
            $_SESSION['name'] = $firstName . ' ' . $lastName;
            $message = 'Profile updated successfully!';
            $messageType = 'success';
            $user['first_name'] = $firstName;
            $user['last_name'] = $lastName;
            $user['phone'] = $phone;
        } else {
            $message = 'Failed to update profile.';
            $messageType = 'error';
        }
    } else {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - CliniSphere</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            animation: slideUp 0.6s ease-out;
        }

        .profile-form {
            display: grid;
            gap: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .profile-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #ddd;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 500;
            color: #2c3e50;
        }

        .info-value {
            color: #666;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: background 0.3s;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .message {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            animation: slideIn 0.3s ease;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profile.php" class="active">My Profile</a></li>
                <li>
                    <a href="#" onclick="toggleUserMenu()">👤 Account</a>
                    <ul class="dropdown">
                        <li><a href="#" onclick="logout()">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div class="profile-header">
        <div class="container">
            <h1>My Profile</h1>
            <p>Manage your account information</p>
        </div>
    </div>

    <div class="container">
        <div class="profile-container">
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <h2>Account Information</h2>
            <div class="profile-info">
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Role:</span>
                    <span class="info-value"><?php echo ucfirst($userRole); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Member Since:</span>
                    <span class="info-value"><?php echo date('F d, Y', strtotime($user['created_at'] ?? 'now')); ?></span>
                </div>
            </div>

            <h2>Edit Profile</h2>
            <form method="POST" class="profile-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input 
                            type="text" 
                            id="first_name" 
                            name="first_name" 
                            value="<?php echo htmlspecialchars($user['first_name']); ?>" 
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input 
                            type="text" 
                            id="last_name" 
                            name="last_name" 
                            value="<?php echo htmlspecialchars($user['last_name']); ?>" 
                            required
                        >
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="phone">Phone Number</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                    >
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 CliniSphere. All rights reserved.</p>
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

        // Auto-hide message after 5 seconds
        const messageDiv = document.querySelector('.message');
        if (messageDiv) {
            setTimeout(() => {
                messageDiv.style.transition = 'opacity 0.3s';
                messageDiv.style.opacity = '0';
                setTimeout(() => messageDiv.remove(), 300);
            }, 5000);
        }
    </script>
</body>
</html>
