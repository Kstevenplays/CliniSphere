<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CliniSphere</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-hospital"></i>
                <h1>CliniSphere</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php" class="active">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </div>
    </nav>

    <div class="auth-page auth-login">
        <div class="auth-container">
            <div class="auth-box">
                <div class="auth-header">
                    <div class="auth-logo">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <h1>Welcome Back</h1>
                    <p class="auth-subtitle">Sign in to manage your appointments and medical records</p>
                </div>
                
                <form id="loginForm" class="auth-form">
                    <div class="form-group floating-label">
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            placeholder="" 
                            required 
                            autofocus
                            aria-label="Email Address"
                            autocomplete="email">
                        <label for="email">Email Address</label>
                    </div>

                    <div class="form-group floating-label">
                        <div class="password-input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password"
                                placeholder="" 
                                required
                                aria-label="Password"
                                autocomplete="current-password">
                            <label for="password">Password</label>
                            <button 
                                type="button" 
                                class="password-toggle" 
                                id="togglePassword" 
                                aria-label="Toggle password visibility">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="forgot-link" onclick="alert('Password reset functionality coming soon'); return false;">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">
                        Sign In
                    </button>

                    <div id="message" class="message" role="alert" aria-live="polite" style="display: none;"></div>
                </form>

                <div class="auth-footer">
                    <p>Don't have an account? 
                        <a href="register.php" class="signup-link">Create one now</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="js/auth.js"></script>
    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                this.title = 'Hide Password';
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                this.title = 'Show Password';
            }
        });

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

        // Floating label functionality
        function initFloatingLabels() {
            const floatingInputs = document.querySelectorAll('.floating-label input');
            
            floatingInputs.forEach(input => {
                // Check on load if input has value
                if (input.value) {
                    input.classList.add('has-value');
                }
                
                // Add class when input has value
                input.addEventListener('input', function() {
                    if (this.value) {
                        this.classList.add('has-value');
                    } else {
                        this.classList.remove('has-value');
                    }
                });
                
                // Handle autofill
                input.addEventListener('change', function() {
                    if (this.value) {
                        this.classList.add('has-value');
                    }
                });
            });
        }
        
        // Initialize floating labels on page load
        initFloatingLabels();
        
        // Re-check after a short delay for autofill
        setTimeout(initFloatingLabels, 100);
    </script>
</body>
</html>
