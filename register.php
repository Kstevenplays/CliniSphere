<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CliniSphere</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <h2>CliniSphere Registration</h2>
            
            <form id="registerForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone">
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary full-width">Register</button>
            </form>

            <p class="auth-link">Already have an account? <a href="login.php">Login here</a></p>
            <div id="message" class="message" style="display: none;"></div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const firstName = document.getElementById('first_name').value;
            const lastName = document.getElementById('last_name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            const msgDiv = document.getElementById('message');
            
            if (password !== confirmPassword) {
                msgDiv.className = 'message error';
                msgDiv.textContent = 'Passwords do not match';
                msgDiv.style.display = 'block';
                return;
            }
            
            fetch('api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({first_name: firstName, last_name: lastName, email, phone, password})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    msgDiv.className = 'message success';
                    msgDiv.textContent = 'Registration successful! Redirecting...';
                    msgDiv.style.display = 'block';
                    setTimeout(() => window.location.href = 'index.php', 1500);
                } else {
                    msgDiv.className = 'message error';
                    msgDiv.textContent = data.error || 'Registration failed';
                    msgDiv.style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>
