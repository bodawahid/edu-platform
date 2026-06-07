<?php
/**
 * Faculty of Engineering at Shubra
 * Benha University - Login Page
 */

require_once __DIR__ . '/includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectByRole();
}

// Check for attack patterns on login page access
// checkAndBlockAttacks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Faculty of Engineering at Shubra</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .credentials-info {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 14px;
            margin-top: 16px;
            font-size: 0.8rem;
        }
        .credentials-info h4 {
            font-size: 0.85rem;
            color: var(--primary);
            margin-bottom: 8px;
        }
        .cred-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            color: var(--gray);
        }
        .cred-row span:last-child {
            font-family: monospace;
            background: #e9ecef;
            padding: 1px 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-left">
                <h2 class="login-title">Welcome Back!</h2>
                <p class="login-subtitle">Sign in to access your academic dashboard.</p>

                <div id="loginAlert"></div>

                <form class="login-form" id="loginForm" onsubmit="return handleLogin(event)">
                    <?= csrfField() ?>

                    <div class="form-group">
                        <label class="form-label">Username or Email</label>
                        <div class="login-input-wrapper">
                            <span class="login-input-icon">&#128100;</span>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required autocomplete="username">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="login-input-wrapper">
                            <span class="login-input-icon">&#128274;</span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary login-btn" id="loginBtn">
                        Sign In
                    </button>
                </form>

                <div class="credentials-info">
                    <h4>Demo Credentials</h4>
                    <div class="cred-row"><span>Admin</span> <span>admin / admin</span></div>
                    <div class="cred-row"><span>Doctor</span> <span>doctor / doctor</span></div>
                    <div class="cred-row"><span>TA</span> <span>ta / ta</span></div>
                    <div class="cred-row"><span>Student</span> <span>student / student</span></div>
                </div>

                <p class="login-footer">&copy; <?= date('Y') ?> Faculty of Engineering at Shubra, Benha University</p>
            </div>

            <div class="login-right">
                <div class="login-logo">FE</div>
                <div class="login-university">Benha University</div>
                <div class="login-faculty">Faculty of Engineering at Shubra</div>
                <p style="opacity: 0.8; font-size: 0.9rem; max-width: 280px;">
                    Empowering the next generation of engineers through innovative education and cutting-edge research.
                </p>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js"></script>
    <script>
    async function handleLogin(e) {
        e.preventDefault();
        const btn = document.getElementById('loginBtn');
        const alert = document.getElementById('loginAlert');
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        const csrf = document.querySelector('input[name="csrf_token"]').value;

        if (!username || !password) {
            alert.innerHTML = '<div class="alert alert-error"><span class="alert-icon">✗</span> Please fill in all fields.</div>';
            return false;
        }

        btn.disabled = true;
        btn.innerHTML = '<div class="spinner"></div> Signing in...';
        alert.innerHTML = '';

        try {
            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('username', username);
            formData.append('password', password);
            formData.append('csrf_token', csrf);

            const response = await fetch('/api/auth.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                alert.innerHTML = '<div class="alert alert-success"><span class="alert-icon">✓</span> Login successful! Redirecting...</div>';
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 500);
            } else {
                alert.innerHTML = '<div class="alert alert-error"><span class="alert-icon">✗</span> ' + (data.message || 'Login failed.') + '</div>';
                btn.disabled = false;
                btn.innerHTML = 'Sign In';
            }
        } catch (error) {
            alert.innerHTML = '<div class="alert alert-error"><span class="alert-icon">✗</span> An error occurred. Please try again.</div>';
            btn.disabled = false;
            btn.innerHTML = 'Sign In';
        }

        return false;
    }
    </script>
</body>
</html>
