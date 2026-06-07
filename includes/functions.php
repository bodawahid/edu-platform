<?php
/**
 * Faculty of Engineering at Shubra - Benha University
 * Utility Functions
 */

require_once __DIR__ . '/db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ========== CSRF PROTECTION ==========

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    // Token expires after 1 hour
    if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time'] > 3600)) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCSRFToken()) . '">';
}

// ========== INPUT VALIDATION & SANITIZATION ==========

function sanitizeInput($input, $type = 'string') {
    if ($input === null) return null;

    switch ($type) {
        case 'email':
            return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
        case 'int':
            return filter_var($input, FILTER_VALIDATE_INT);
        case 'float':
            return filter_var($input, FILTER_VALIDATE_FLOAT);
        case 'url':
            return filter_var(trim($input), FILTER_SANITIZE_URL);
        case 'bool':
            return filter_var($input, FILTER_VALIDATE_BOOLEAN);
        default:
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

function validateRequired($fields, $data) {
    $errors = [];
    foreach ($fields as $field) {
        if (empty($data[$field]) || trim($data[$field]) === '') {
            $errors[] = "The field '$field' is required.";
        }
    }
    return $errors;
}

// ========== SECURITY LOGGING HELPERS ==========
// Kept active so your future AI model middleware can call them to log threats

function logSecurityEvent($attackType, $description, $severity = 'medium', $actionTaken = 'blocked') {
    try {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO security_logs (ip_address, user_id, username_attempt, attack_type, description, request_url, request_method, request_data, severity, action_taken, user_agent)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SESSION['user_id'] ?? null,
                $_SESSION['username'] ?? ($_POST['username'] ?? null),
                $attackType,
                $description,
                $_SERVER['REQUEST_URI'] ?? '',
                $_SERVER['REQUEST_METHOD'] ?? 'GET',
                json_encode($_POST),
                $severity,
                $actionTaken,
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]
        );
    } catch (Exception $e) {
        error_log("Failed to log security event: " . $e->getMessage());
    }
}

// ========== AUTHENTICATION HELPERS ==========

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;

    try {
        $db = Database::getInstance();
        $result = $db->query(
            "SELECT u.*, r.role_name, r.display_name as role_display
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.id = ? AND u.is_active = 1",
            [$_SESSION['user_id']]
        )->fetch();
        return $result ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function requireRole($allowedRoles) {
    requireAuth();
    if (!is_array($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }
    $user = getCurrentUser();
    if (!$user || !in_array($user['role_name'], $allowedRoles)) {
        header('HTTP/1.1 403 Forbidden');
        die('Access denied. Insufficient privileges.');
    }
}

function redirectByRole() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
    $user = getCurrentUser();
    switch ($user['role_name']) {
        case 'admin': header('Location: /admin/dashboard.php'); break;
        case 'doctor': header('Location: /doctor/dashboard.php'); break;
        case 'ta': header('Location: /ta/dashboard.php'); break;
        case 'student': header('Location: /student/dashboard.php'); break;
        default: header('Location: /login.php'); break;
    }
    exit;
}

// ========== NOTIFICATION HELPERS ==========

function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

function showFlashMessage() {
    $msg = getFlashMessage();
    if ($msg) {
        $alertClass = match($msg['type']) {
            'success' => 'alert-success',
            'error' => 'alert-error',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            default => 'alert-info'
        };
        $icon = match($msg['type']) {
            'success' => '✓',
            'error' => '✗',
            'warning' => '⚠',
            'info' => 'ℹ',
            default => 'ℹ'
        };
        echo "<div class='alert {$alertClass}'><span class='alert-icon'>{$icon}</span> {$msg['message']}</div>";
    }
}

// ========== PAGINATION ==========

function paginate($page, $perPage, $total) {
    $page = max(1, (int)$page);
    $perPage = max(1, min(100, (int)$perPage));
    $totalPages = max(1, ceil($total / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;

    return [
        'page' => $page,
        'per_page' => $perPage,
        'total' => $total,
        'total_pages' => $totalPages,
        'offset' => $offset
    ];
}

// ========== DATE/TIME HELPERS ==========

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('M j, Y', $time);
}

function formatDate($datetime, $format = 'M j, Y g:i A') {
    return date($format, strtotime($datetime));
}

function getDeadlineStatus($deadline) {
    $deadlineTime = strtotime($deadline);
    $now = time();
    $diff = $deadlineTime - $now;

    if ($diff < 0) return ['class' => 'overdue', 'text' => 'Overdue', 'urgent' => true];
    if ($diff < 86400) return ['class' => 'urgent', 'text' => floor($diff / 3600) . ' hours left', 'urgent' => true];
    if ($diff < 172800) return ['class' => 'soon', 'text' => '1 day left', 'urgent' => false];
    return ['class' => 'normal', 'text' => floor($diff / 86400) . ' days left', 'urgent' => false];
}

// ========== FILE UPLOAD HELPERS ==========

function uploadFile($file, $directory, $allowedTypes = ['pdf', 'zip', 'doc', 'docx'], $maxSize = 10485760) {
    $result = ['success' => false, 'path' => '', 'filename' => '', 'error' => ''];

    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        $result['error'] = 'No file uploaded.';
        return $result;
    }

    if ($file['size'] > $maxSize) {
        $result['error'] = 'File size exceeds the maximum limit of ' . ($maxSize / 1048576) . ' MB.';
        return $result;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        $result['error'] = 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes);
        return $result;
    }

    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $validMimes = [
        'pdf' => ['application/pdf'],
        'zip' => ['application/zip', 'application/x-zip-compressed'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document']
    ];

    if (isset($validMimes[$ext]) && !in_array($mimeType, $validMimes[$ext])) {
        $result['error'] = 'Invalid file content.';
        return $result;
    }

    $uploadDir = __DIR__ . '/../uploads/' . $directory . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $result['success'] = true;
        $result['path'] = 'uploads/' . $directory . '/' . $filename;
        $result['filename'] = $filename;
        $result['size'] = $file['size'];
        $result['type'] = $ext;
    } else {
        $result['error'] = 'Failed to move uploaded file.';
    }

    return $result;
}

// ========== JSON RESPONSE HELPER ==========

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// ========== ACTIVITY LOGGING ==========

function logActivity($action, $entityType = null, $entityId = null, $description = '') {
    try {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $_SESSION['user_id'] ?? null,
                $action,
                $entityType,
                $entityId,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null
            ]
        );
    } catch (Exception $e) {
        error_log("Activity log failed: " . $e->getMessage());
    }
}