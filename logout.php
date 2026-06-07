<?php
/**
 * Logout Handler
 */

require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    logActivity('logout', 'user', $_SESSION['user_id'], "User {$_SESSION['username']} logged out.");
}

// Clear all session data
$_SESSION = array();

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();

header('Location: /login.php');
exit;
