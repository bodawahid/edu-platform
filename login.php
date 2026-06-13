<?php
/**
 * Faculty of Engineering at Shubra
 * Benha University - Login Page (Enhanced UI/UX)
 */

require_once __DIR__ . '/includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectByRole();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Faculty of Engineering at Shubra</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        /* ============================================
           LOGIN PAGE - ENHANCED UI/UX
           Matches Landing Page (index.php) Style
           ============================================ */

        :root {
            --primary: #1e3a5f;
            --primary-light: #2c5282;
            --primary-dark: #15294a;
            --secondary: #c9a227;
            --secondary-light: #e2bc3a;
            --accent: #e74c3c;
            --success: #27ae60;
            --warning: #f39c12;
            --info: #3498db;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --dark: #2c3e50;
            --gray: #6c757d;
            --gray-light: #e9ecef;
            --white: #ffffff;
            --border-radius: 12px;
            --shadow-sm: 0 2px 8px rgba(30, 58, 95, 0.08);
            --shadow-md: 0 8px 24px rgba(30, 58, 95, 0.12);
            --shadow-lg: 0 16px 48px rgba(30, 58, 95, 0.16);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ============================================
           BACKGROUND = صورة الكلية (الخلفية الزرقاء كلها)
           ============================================ */
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* الخلفية الزرقاء كلها = صورة الكلية */
            background: url('/assets/images/benha.jpg') center/cover no-repeat fixed;
            background-color: #002d72;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Overlay داكن على الخلفية كلها */
        /* .login-page::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                135deg, 
                rgba(21, 41, 74, 0.92) 0%, 
                rgba(30, 58, 95, 0.88) 50%,
                rgba(44, 82, 130, 0.85) 100%
            );
            z-index: 0;
        } */

        /* Container */
        .login-container {
            display: flex;
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            max-width: 950px;
            width: 100%;
            animation: slideUp 0.6s ease;
            position: relative;
            z-index: 1;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Left Side - Form */
        .login-left {
            flex: 1;
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: fadeIn 0.8s ease 0.2s both;
        }

        .login-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            color: var(--gray);
            margin-bottom: 32px;
            font-size: 1rem;
            line-height: 1.5;
        }

        /* Form Inputs */
        .login-form .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--dark);
        }

        .login-input-wrapper {
            position: relative;
            transition: var(--transition);
        }

        .login-input-wrapper:focus-within {
            transform: translateY(-2px);
        }

        .login-input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .login-input-wrapper:focus-within .login-input-icon {
            color: var(--primary);
        }

        .login-input-wrapper .form-control {
            width: 100%;
            padding: 14px 16px 14px 48px;
            height: 52px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition);
            background: #fafbfc;
            font-family: inherit;
            box-sizing: border-box;
        }

        .login-input-wrapper .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.08);
            background: var(--white);
        }

        .login-input-wrapper .form-control:hover {
            border-color: #cbd5e1;
        }

        /* Button */
        .login-btn {
            width: 100%;
            padding: 16px;
            font-size: 1.05rem;
            font-weight: 700;
            margin-top: 16px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--white);
            border: none;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 16px rgba(30, 58, 95, 0.3);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .login-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }

        .login-btn:active::after {
            width: 300px;
            height: 300px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(30, 58, 95, 0.4);
        }

        .login-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Alerts */
        #loginAlert {
            margin-bottom: 20px;
        }

        .alert {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 12px;
            font-size: 0.95rem;
            animation: slideDown 0.4s ease;
            border: 1px solid transparent;
        }

        .alert-success { 
            background: #f0fdf4; 
            color: #166534; 
            border-color: #bbf7d0;
        }
        .alert-error { 
            background: #fef2f2; 
            color: #991b1b; 
            border-color: #fecaca;
        }

        .alert-icon { 
            font-weight: bold; 
            font-size: 1.2rem;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(0,0,0,0.05);
            flex-shrink: 0;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-10px); }
            40% { transform: translateX(10px); }
            60% { transform: translateX(-5px); }
            80% { transform: translateX(5px); }
        }

        /* Credentials Box */
        .credentials-info {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px;
            margin-top: 24px;
            font-size: 0.85rem;
            transition: var(--transition);
        }

        .credentials-info:hover {
            border-color: var(--primary-light);
            box-shadow: var(--shadow-sm);
        }

        .credentials-info h4 {
            font-size: 0.9rem;
            color: var(--primary);
            margin-bottom: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cred-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            color: var(--gray);
            border-bottom: 1px dashed #e2e8f0;
        }

        .cred-row:last-child {
            border-bottom: none;
        }

        .cred-row span:first-child {
            font-weight: 500;
        }

        .cred-row span:last-child {
            font-family: 'Courier New', monospace;
            background: var(--white);
            padding: 4px 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            font-weight: 600;
            color: var(--primary);
            font-size: 0.8rem;
        }

        /* ============================================
           RIGHT SIDE - صورة الكلية + لوجو مربع
           ============================================ */

        .login-right {
            flex: 1;
            /* صورة الكلية كخلفية للـ right side */
            background: url('/assets/images/benha.jpg') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--white);
            padding: 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
            min-height: 500px;
        }

        /* Overlay داكن على الصورة */
        .login-right::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                135deg, 
                rgba(21, 41, 74, 0.85) 0%, 
                rgba(30, 58, 95, 0.75) 50%,
                rgba(21, 41, 74, 0.85) 100%
            );
            z-index: 1;
        }

        /* Pattern overlay شفاف */
        .login-right::after {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 2;
        }

        /* ============================================
           اللوجو - مربع (مش دائرة) علشان يشمل كل الصورة
           ============================================ */
        .login-logo {
            width: 160px;          /* كبرت شوية */
            height: 160px;         /* مربع */
            background: var(--white);
            border-radius: 16px;   /* زوايا مدورة بس مش دائرة */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            margin-bottom: 24px;
            box-shadow: 
                0 8px 32px rgba(0,0,0,0.3), 
                0 0 0 4px rgba(201, 162, 39, 0.4),
                0 0 0 8px rgba(201, 162, 39, 0.1);
            position: relative;
            z-index: 3;
            animation: floatLogo 4s ease-in-out infinite;
            overflow: hidden;
        }

        .login-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;   /* يشمل كل الصورة */
            border-radius: 8px;
        }

        @keyframes floatLogo {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* الكلام فوق الصورة */
        .login-university {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 3;
            color: var(--white);
            text-shadow: 0 2px 8px rgba(0,0,0,0.5), 0 0 20px rgba(0,0,0,0.3);
        }

        .login-faculty {
            font-size: 1.1rem;
            margin-bottom: 24px;
            position: relative;
            z-index: 3;
            font-weight: 500;
            color: var(--white);
            text-shadow: 0 2px 8px rgba(0,0,0,0.5), 0 0 20px rgba(0,0,0,0.3);
        }

        .login-right p {
            font-size: 0.95rem;
            max-width: 280px;
            line-height: 1.7;
            position: relative;
            z-index: 3;
            color: rgba(255,255,255,0.9);
            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }

        /* خط فاصل ذهبي */
        .login-divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--secondary), var(--secondary-light));
            border-radius: 2px;
            margin: 16px auto;
            position: relative;
            z-index: 3;
        }

        .login-footer {
            text-align: center;
            margin-top: auto;
            padding-top: 24px;
            color: var(--gray);
            font-size: 0.85rem;
        }

        /* Spinner */
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: var(--white);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: inline-block;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 100%;
            }

            .login-right {
                display: none;
            }

            .login-left {
                padding: 40px 24px;
            }

            .login-title {
                font-size: 1.5rem;
            }
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

                    <button type="submit" class="btn login-btn" id="loginBtn">
                        Sign In
                    </button>
                </form>

                <p class="login-footer">&copy; <?= date('Y') ?> Faculty of Engineering at Shubra, Benha University</p>
            </div>

            <div class="login-right">
                <!-- اللوجو مربع 160×160 علشان يشمل كل الصورة -->
                <div class="login-logo">
                    <img src="/assets/images/logo_benha.png" alt="Faculty of Engineering Logo">
                </div>

                <div class="login-university">Benha University</div>
                <div class="login-divider"></div>
                <div class="login-faculty">Faculty of Engineering at Shubra</div>
                <p>
                    Empowering the next generation of engineers through innovative education and cutting-edge research.
                </p>
            </div>
        </div>
    </div>

    <script>
    async function handleLogin(e) {
        e.preventDefault();
        const btn = document.getElementById('loginBtn');
        const alert = document.getElementById('loginAlert');
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        const csrf = document.querySelector('input[name="csrf_token"]').value;
        const form = document.getElementById('loginForm');

        if (!username || !password) {
            showAlert('error', 'Please fill in all fields.');
            shakeElement(form);
            return false;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Signing in...';
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

            // WAF Block (Status 403)
            if (response.status === 403) {
                const htmlResult = await response.text();
                document.body.innerHTML = htmlResult;
                return false;
            }

            const data = await response.json();

            if (data.success) {
                showAlert('success', 'Login successful! Redirecting...');
                btn.innerHTML = '✓ Redirecting...';
                btn.style.background = 'var(--success)';

                setTimeout(() => { 
                    window.location.href = data.redirect; 
                }, 800);
            } else {
                showAlert('error', data.message || 'Invalid username or password.');
                resetButton(btn);
                shakeElement(form);
            }
        } catch (error) {
            showAlert('error', 'Connection error. Please try again.');
            resetButton(btn);
        }

        return false;
    }

    function showAlert(type, message) {
        const alert = document.getElementById('loginAlert');
        const icon = type === 'success' ? '✓' : '✗';
        alert.innerHTML = '<div class="alert alert-' + type + '"><span class="alert-icon">' + icon + '</span> <span>' + message + '</span></div>';
    }

    function resetButton(btn) {
        btn.disabled = false;
        btn.innerHTML = 'Sign In';
        btn.style.background = '';
    }

    function shakeElement(element) {
        element.style.animation = 'none';
        element.offsetHeight;
        element.style.animation = 'shake 0.5s ease';
        setTimeout(() => { element.style.animation = ''; }, 500);
    }
    </script>
</body>
</html>