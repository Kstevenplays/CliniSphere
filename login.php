<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CliniSphere</title>
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
                <li><a href="login.php" class="active">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </div>
    </nav>

    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-box">
                <h2>CliniSphere Login</h2>
                
                <form id="loginForm">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary full-width">Login</button>
                </form>

                <p class="auth-link">Don't have an account? <a href="register.php">Register here</a></p>
                <div id="message" class="message" style="display: none;"></div>
            </div>
        </div>
    </div>

    <script src="js/auth.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            fetch('api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({email, password})
            })
            .then(res => res.json())
            .then(data => {
                const msgDiv = document.getElementById('message');
                if (data.success) {
                    msgDiv.className = 'message success';
                    
                    // Determine redirect based on user role
                    let redirectUrl = 'dashboard.php';
                    if (data.user.role === 'doctor') {
                        redirectUrl = 'doctors.php';
                    } else if (data.user.role === 'admin') {
                        redirectUrl = 'admin/index.php';
                    }
                    
                    msgDiv.textContent = 'Login successful!';
                    msgDiv.style.display = 'block';
                    setTimeout(() => window.location.href = redirectUrl, 800);
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
