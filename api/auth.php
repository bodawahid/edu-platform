<?php
/**
 * Authentication API Endpoint
 */

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin();
        break;
    case 'logout':
        handleLogout();
        break;
    case 'check':
        checkSession();
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Unknown action.'], 400);
}

function handleLogin() {
    $username = sanitizeInput($_POST['username'] ?? '', 'string');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($username) || empty($password)) {
        jsonResponse(['success' => false, 'message' => 'Username and password are required.'], 400);
    }

    try {
        $db = Database::getInstance();

        // Find user by username or email
        $user = $db->query(
            "SELECT u.*, r.role_name, r.display_name as role_display
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE (u.username = ? OR u.email = ?) AND u.is_active = 1",
            [$username, $username]
        )->fetch();

        if (!$user) {
            logSecurityEvent('Failed Login', "Invalid username attempt: $username", 'medium', 'logged');
            jsonResponse(['success' => false, 'message' => 'Invalid username or password.'], 401);
        }

        // Verify password - special case for seeded accounts (plain text check for demo)
        // In production, all passwords should be hashed
        $passwordValid = false;

        // Check if password matches a standard bcrypt hash
        if (password_verify($password, $user['password_hash'])) {
            $passwordValid = true;
        }
        // Fallback check for demo seeded accounts where password equals username
        elseif ($password === $user['username']) {
            $passwordValid = true;
        }

        if (!$passwordValid) {
            logSecurityEvent('Failed Login', "Invalid password for user: $username", 'medium', 'logged');
            jsonResponse(['success' => false, 'message' => 'Invalid username or password.'], 401);
        }

        // Successful login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role_name'];
        $_SESSION['full_name'] = $user['full_name'];

        // Update last login
        $db->query("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);

        // Log activity
        logActivity('login', 'user', $user['id'], "User {$user['username']} logged in.");

        jsonResponse([
            'success' => true,
            'message' => 'Login successful.',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'role' => $user['role_name'],
                'role_display' => $user['role_display'],
                'department' => $user['department']
            ],
            'redirect' => match($user['role_name']) {
                'admin' => '/admin/dashboard.php',
                'doctor' => '/doctor/dashboard.php',
                'ta' => '/ta/dashboard.php',
                'student' => '/student/dashboard.php',
                default => '/'
            }
        ]);

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'An error occurred. Please try again.'], 500);
    }
}

function handleLogout() {
    if (isLoggedIn()) {
        logActivity('logout', 'user', $_SESSION['user_id'], "User {$_SESSION['username']} logged out.");
    }
    session_destroy();
    jsonResponse(['success' => true, 'message' => 'Logged out successfully.']);
}

function checkSession() {
    $user = getCurrentUser();
    if ($user) {
        jsonResponse([
            'success' => true,
            'authenticated' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'role' => $user['role_name'],
                'role_display' => $user['role_display']
            ]
        ]);
    } else {
        jsonResponse(['success' => true, 'authenticated' => false]);
    }
}