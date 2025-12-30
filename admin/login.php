<?php
session_start();

// If already logged in as admin, redirect to dashboard
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CliniSphere</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-login-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        }

        .admin-login-container {
            width: 100%;
            max-width: 400px;
            padding: 0 20px;
        }

        .admin-login-box {
            background: white;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            animation: enlargeForm 0.6s ease-out;
        }

        .admin-login-box h2 {
            text-align: center;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .admin-badge {
            text-align: center;
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .admin-subtitle {
            text-align: center;
            color: #7f8c8d;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-login-page">
        <div class="admin-login-container">
            <div class="admin-login-box">
                <div class="admin-badge">🔐</div>
                <h2>Admin Login</h2>
                <p class="admin-subtitle">CliniSphere Administration Panel</p>
                
                <form id="adminLoginForm">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary full-width">Login as Admin</button>
                </form>

                <div id="message" class="message" style="display: none; margin-top: 1rem;"></div>
                
                <p class="back-link">
                    <a href="../index.php">← Back to Home</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            fetch('../api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({username, password})
            })
            .then(res => res.json())
            .then(data => {
                const msgDiv = document.getElementById('message');
                if (data.success) {
                    // Check if user is admin
                    if (data.user && data.user.role === 'admin') {
                        msgDiv.className = 'message success';
                        msgDiv.textContent = 'Login successful!';
                        msgDiv.style.display = 'block';
                        setTimeout(() => window.location.href = 'index.php', 800);
                    } else {
                        msgDiv.className = 'message error';
                        msgDiv.textContent = 'Access denied. Admin privileges required.';
                        msgDiv.style.display = 'block';
                        // Logout non-admin user
                        fetch('../api/logout.php', { method: 'POST' });
                    }
                } else {
                    msgDiv.className = 'message error';
                    msgDiv.textContent = data.error || 'Login failed';
                    msgDiv.style.display = 'block';
                }
            })
            .catch(error => {
                const msgDiv = document.getElementById('message');
                msgDiv.className = 'message error';
                msgDiv.textContent = 'Network error: ' + error.message;
                msgDiv.style.display = 'block';
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
