<?php include "connection.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In & Register - NSBM Marketplace</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/animate.min.css">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="css/header-cyber.css">
    <link rel="stylesheet" href="css/login-style.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Toggle behavior for the two form sections.
           Only one of #signin-form / #register-form is visible at a time. */
        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
        }
    </style>
</head>

<body class="bg-eco-soft text-dark">

    <!-- Sign In Form Section -->
    <div id="signin-form" class="form-section active">
        <div class="login-container">
            <div class="login-card">
                <!-- Header -->
                <div class="login-header">
                    <div class="login-logo">
                        <i class="fas fa-leaf"></i>
                        NSBM
                    </div>
                    <h1 class="login-title">Welcome Back</h1>
                    <p class="login-subtitle">Sign in to your marketplace account</p>
                </div>

                <!-- Messages -->
                <div class="success-message" id="successMessage">
                    <i class="fas fa-check-circle me-2"></i>
                    Login successful! Redirecting...
                </div>
                <div class="error-message" id="errorMessage"></div>

                <!-- Login Form -->
                <form id="loginForm" method="POST" >
                    <!-- Email Input -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address or Username</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="you@example.com" required>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="••••••••" required>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberme" name="rememberme">
                            <label class="form-check-label" for="rememberme">
                                Remember me
                            </label>
                        </div>
                        <div class="forgot-password">
                            <a href="forgot-password.html">Forgot password?</a>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <button type="button" class="login-btn" onclick="login()">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                    <!-- Sign Up Link -->
                    <p class="auth-switch">Don't have an account? <a href="#register-form"
                            onclick="toggleForm('register'); return false;">Register</a></p>
                </form>
            </div>
        </div>
    </div>

    <!-- Register Form Section -->
    <div id="register-form" class="form-section">
    <div class="login-container">
        <div class="login-card register-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-leaf"></i>
                    NSBM
                </div>
                <h1 class="login-title">Create Account</h1>
            </div>

            <div class="success-message" id="registerSuccessMessage"></div>
            <div class="error-message" id="registerErrorMessage"></div>

            <form id="registerForm" method="post">
                <div class="form-group">
                    <label for="reg-name" class="form-label">Full Name</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control" id="reg-name" name="name"
                            placeholder="John Doe" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg-email" class="form-label">Email Address</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" class="form-control" id="reg-email" name="email"
                            placeholder="you@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg-phone" class="form-label">Phone Number (Optional)</label>
                    <div class="input-icon">
                        <i class="fas fa-phone"></i>
                        <input type="tel" class="form-control" id="reg-phone" name="phone"
                            placeholder="07XXXXXXXX">
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg-password" class="form-label">Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="reg-password" name="password"
                            placeholder="••••••••" minlength="8" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg-cpassword" class="form-label">Confirm Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="reg-cpassword"
                            name="cpassword" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="button" class="login-btn w-100" onclick="register()">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>
            </form>
            <p class="auth-switch">Already have an account? <a href="#signin-form"
                    onclick="toggleForm('signin'); return false;">Sign In</a></p>
        </div>
    </div>
</div>

    <!-- Scripts -->
    <script>
        // Toggle between Sign In and Register forms
        function toggleForm(formType) {
            const signin = document.getElementById('signin-form');
            const register = document.getElementById('register-form');

            // Hide both, then show the one requested
            signin.classList.remove('active');
            register.classList.remove('active');

            if (formType === 'signin') {
                signin.classList.add('active');
            } else {
                register.classList.add('active');
            }
        }
    </script>
    <?php include 'footer.php'; ?>
    <script src = "js/main.js"></script>
</body>

</html>
