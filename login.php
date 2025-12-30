<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CliniSphere</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <h2>CliniSphere Login</h2>
            
            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" required>
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
                    msgDiv.textContent = 'Login successful! Redirecting...';
                    msgDiv.style.display = 'block';
                    setTimeout(() => window.location.href = 'index.php', 1500);
                } else {
                    msgDiv.className = 'message error';
                    msgDiv.textContent = data.error || 'Login failed';
                    msgDiv.style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>
