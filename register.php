<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CliniSphere</title>
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
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php" class="active">Register</a></li>
            </ul>
        </div>
    </nav>

    <div class="auth-page auth-register">
        <div class="auth-container">
            <div class="auth-box">
                <div class="auth-header">
                    <div class="auth-logo">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <h1>Join CliniSphere</h1>
                    <p class="auth-subtitle">Create an account to book appointments with trusted doctors</p>
                </div>
                
                <form id="registerForm" class="auth-form">
                    <div class="form-row">
                        <div class="form-group floating-label">
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name"
                                placeholder="" 
                                required
                                aria-label="First Name"
                                autocomplete="given-name">
                            <label for="first_name">First Name</label>
                        </div>
                        <div class="form-group floating-label">
                            <input 
                                type="text" 
                                id="last_name" 
                                name="last_name"
                                placeholder="" 
                                required
                                aria-label="Last Name"
                                autocomplete="family-name">
                            <label for="last_name">Last Name</label>
                        </div>
                    </div>

                    <div class="form-group floating-label">
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            placeholder="" 
                            required
                            aria-label="Email Address"
                            autocomplete="email">
                        <label for="email">Email Address</label>
                    </div>

                    <div class="form-group floating-label">
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone"
                            placeholder=""
                            aria-label="Phone Number"
                            autocomplete="tel">
                        <label for="phone">Phone Number <span class="optional-label">(Optional)</span></label>
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
                                autocomplete="new-password"
                                minlength="8">
                            <label for="password">Password</label>
                            <button 
                                type="button" 
                                class="password-toggle" 
                                id="togglePassword" 
                                aria-label="Toggle password visibility">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <small class="form-hint">Minimum 8 characters required</small>
                    </div>

                    <div class="form-group floating-label">
                        <div class="password-input-wrapper">
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password"
                                placeholder="" 
                                required
                                aria-label="Confirm Password"
                                autocomplete="new-password"
                                minlength="8">
                            <label for="confirm_password">Confirm Password</label>
                            <button 
                                type="button" 
                                class="password-toggle" 
                                id="toggleConfirmPassword" 
                                aria-label="Toggle password visibility">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-agree">
                        <label class="checkbox-label">
                            <input type="checkbox" id="terms" required>
                            <span class="checkbox-text">
                                I agree to the <a href="#" class="inline-link">Terms of Service</a> and <a href="#" class="inline-link">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-register">
                        Create Account
                    </button>

                    <div id="message" class="message" role="alert" aria-live="polite" style="display: none;"></div>
                </form>

                <div class="auth-footer">
                    <p>Already have an account? 
                        <a href="login.php" class="signup-link">Sign in here</a>
                    </p>
                </div>
            </div>
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
                    msgDiv.textContent = 'Registration successful!';
                    msgDiv.style.display = 'block';
                    setTimeout(() => window.location.href = 'dashboard.php', 800);
                } else {
                    msgDiv.className = 'message error';
                    msgDiv.textContent = data.error || 'Registration failed';
                    msgDiv.style.display = 'block';
                }
            })
            .catch(error => {
                msgDiv.className = 'message error';
                msgDiv.textContent = 'Network error: ' + error.message;
                msgDiv.style.display = 'block';
                console.error('Error:', error);
            });
        });

        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirm_password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
            confirmPasswordInput.type = type;
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
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
