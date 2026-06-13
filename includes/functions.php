<?php
date_default_timezone_set('Africa/Cairo');
/**
 * Faculty of Engineering - AI WAF Middleware + Functions
 * FIXED: Session handling (start ONCE), CSRF, Timer logic, Timezone sync
 */

// ═══════════════════════════════════════════════════════════════
// 1️⃣ SESSION HANDLING — Start ONCE at the very top, before ANY output
// ═══════════════════════════════════════════════════════════════
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 0,
        'cookie_path' => '/',
        'cookie_secure' => false,      // Set to true if using HTTPS
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

// 2️⃣ Database + Functions
require_once __DIR__ . '/db.php';

// ========== AI THREAT DETECTION MIDDLEWARE ==========
class AIThreatDetectionMiddleware
{
    private const SERVICE_URL = 'http://127.0.0.1:5005/predict';
    private const CONNECT_TIMEOUT_MS = 500;
    private const TIMEOUT_MS = 2000;
    private static bool $hasRun = false;

    public static function handleGlobalRequest(): void
    {
        // Skip for API endpoints to avoid blocking quiz submissions
        $uri = strtolower($_SERVER['REQUEST_URI'] ?? '');
        if (str_contains($uri, 'api/quizzes.php') || str_contains($uri, 'api/assignments.php')) {
            return;
        }

        if (self::$hasRun || PHP_SAPI === 'cli') return;
        self::$hasRun = true;

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (!in_array($method, ['GET', 'POST'], true)) return;

        // Skip static assets
        if (preg_match('/\.(png|jpg|jpeg|gif|css|js|ico|svg|woff|woff2|ttf|eot)$/', $uri)) {
            return;
        }

        $getData  = $_GET  ?? [];
        $postData = $_POST ?? [];

        $rawBody = file_get_contents('php://input');
        $bodyData = [];

        if (!empty($rawBody)) {
            $contentType = strtolower($_SERVER['CONTENT_TYPE'] ?? '');
            if (str_contains($contentType, 'application/json')) {
                $decoded = json_decode($rawBody, true);
                if (is_array($decoded)) $bodyData = $decoded;
            } elseif (str_contains($contentType, 'application/x-www-form-urlencoded')) {
                parse_str($rawBody, $parsedData);
                if (is_array($parsedData)) $bodyData = $parsedData;
            }
        }

        $finalPost = array_merge($postData, $bodyData);

        $payload = [
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'query_string' => $_SERVER['QUERY_STRING'] ?? '',
            'request_data' => [
                'get'  => $getData,
                'post' => $finalPost
            ]
        ];

        $prediction = self::analyzeWithPythonService($payload);
        if ($prediction === null) return;

        if ($prediction['is_attack'] ?? false) {
            $requestFingerprint = md5($_SERVER['REQUEST_URI'] . json_encode($finalPost));

            if (isset($_SESSION['last_attack_fingerprint']) &&
                $_SESSION['last_attack_fingerprint'] === $requestFingerprint &&
                (time() - ($_SESSION['last_attack_time'] ?? 0)) <= 3) {
                self::blockRequest($prediction);
            }

            $_SESSION['last_attack_fingerprint'] = $requestFingerprint;
            $_SESSION['last_attack_time'] = time();

            self::logAttackToDatabase($prediction, $payload);
            self::blockRequest($prediction);
        }
    }

    private static function analyzeWithPythonService(array $payload): ?array
    {
        if (!function_exists('curl_init')) return null;

        $ch = curl_init(self::SERVICE_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT_MS => self::CONNECT_TIMEOUT_MS,
            CURLOPT_TIMEOUT_MS => self::TIMEOUT_MS,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) return null;

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }

    private static function logAttackToDatabase(array $prediction, array $payload): void
    {
        try {
            $db = Database::getInstance();

            $attackType = $prediction['attack_type'] ?? 'Unknown Attack';
            $confidence = $prediction['confidence'] ?? 0;
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '::1';
            $requestUrl = $_SERVER['REQUEST_URI'] ?? '';
            $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'POST';

            $usernameAttempt = $payload['request_data']['post']['username'] ??
                               $payload['request_data']['post']['username_or_email'] ?? NULL;

            $description = "AI Shield Blocked a " . $attackType . " attempt. Payload: " . json_encode($payload['request_data'], JSON_UNESCAPED_UNICODE);

            $db->query(
                "INSERT INTO security_logs (ip_address, user_id, username_attempt, attack_type, description, request_url, request_method, request_data, severity, action_taken, confidence, created_at)
                 VALUES (?, NULL, ?, ?, ?, ?, ?, ?, 'critical', 'blocked', ?, NOW())",
                [
                    $ipAddress,
                    $usernameAttempt,
                    $attackType,
                    $description,
                    $requestUrl,
                    $requestMethod,
                    json_encode($payload['request_data'], JSON_UNESCAPED_UNICODE),
                    $confidence
                ]
            );
            addNotification(null, 'admin', 'security', '🚨 New Attack Blocked', "Blocked a {$attackType} attempt from IP {$ipAddress}");
        } catch (Exception $e) {
            error_log("WAF DB Logging Failed: " . $e->getMessage());
        }
    }

    private static function blockRequest(array $prediction): void
    {
        if (ob_get_level()) ob_clean();

        http_response_code(403);

        $attackType = htmlspecialchars($prediction['attack_type'] ?? 'Unknown');
        $confidence = htmlspecialchars(($prediction['confidence'] ?? 0) . '%');
        $timestamp = date('Y-m-d H:i:s');

        $jsonPayload = json_encode([
            'error' => 'Security violation detected. Request blocked by AI WAF.',
            'attack_type' => $attackType,
            'confidence' => $confidence,
            'shield' => 'AI-Powered Security Shield',
            'timestamp' => $timestamp
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>Security Violation - AI Shield</title>
            <style>
                html, body { margin: 0 !important; padding: 0 !important; background: #0d1117 !important; width: 100%; height: 100%; }
            </style>
        </head>
        <body>
            <div style='background:#0d1117; color:#00d26a; padding:30px; font-family:monospace; font-size:16px; min-height:100vh; box-sizing:border-box; overflow:auto; display:flex; flex-direction:column; justify-content:center; align-items:center;'>
                <div style='max-width: 800px; width: 100%; background: #161b22; padding: 30px; border-radius: 8px; border: 1px solid #30363d; box-shadow: 0 10px 30px rgba(0,0,0,0.5);'>
                    <h2 style='color:#ff4a4a; border-bottom:1px solid #30363d; padding-bottom:15px; margin-top:0; font-size: 24px;'>
                        🚨 [AI SHIELD] SECURITY VIOLATION DETECTED
                    </h2>
                    <pre style='background:#0d1117; padding:20px; border-radius:6px; border:1px solid #21262d; color:#00d26a; line-height:1.6; font-size:15px; overflow-x:auto;'>{$jsonPayload}</pre>
                    <p style='color:#8b949e; font-size:14px; margin-top:20px; text-align:center; border-top: 1px solid #30363d; padding-top: 15px;'>
                        Your IP address has been logged and reported to the system administrator.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";

        exit;
    }
}

// ✅ Run WAF (but NOT for API endpoints — they handle their own security)
$currentUri = strtolower($_SERVER['REQUEST_URI'] ?? '');
if (!str_contains($currentUri, 'api/quizzes.php') && !str_contains($currentUri, 'api/assignments.php')) {
    AIThreatDetectionMiddleware::handleGlobalRequest();
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
        case 'email': return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
        case 'int': return filter_var($input, FILTER_VALIDATE_INT);
        case 'float': return filter_var($input, FILTER_VALIDATE_FLOAT);
        case 'url': return filter_var(trim($input), FILTER_SANITIZE_URL);
        case 'bool': return filter_var($input, FILTER_VALIDATE_BOOLEAN);
        default: return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
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
    $user = getCurrentUser();
    if (!$user) {
        header('Location: /login.php');
        exit;
    }

    switch ($user['role_name']) {
        case 'admin':
            header('Location: /admin/dashboard.php');
            break;
        case 'doctor':
            header('Location: /doctor/dashboard.php');
            break;
        case 'ta':
            header('Location: /ta/dashboard.php');
            break;
        case 'student':
            header('Location: /student/dashboard.php');
            break;
        default:
            header('Location: /index.php');
            break;
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

function addNotification(?int $userId, ?string $roleTarget, string $type, string $title, string $message): bool {
    try {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO notifications (user_id, role_target, type, title, message) VALUES (?, ?, ?, ?, ?)",
            [$userId, $roleTarget, $type, $title, $message]
        );
        return true;
    } catch (Exception $e) {
        error_log("Notification Insertion Failed: " . $e->getMessage());
        return false;
    }
}

/**
 * دالة حساب حالة الموعد النهائي وتلوين الـ Badges
 */
function getDeadlineStatus(string $deadlineTime): array
{
    $deadlineTimestamp = strtotime($deadlineTime);
    $currentTimestamp = time();
    $diff = $deadlineTimestamp - $currentTimestamp;

    if ($diff <= 0) {
        return [
            'class' => 'danger',
            'text' => 'Ended (' . date('M j, g:i A', $deadlineTimestamp) . ')'
        ];
    }

    if ($diff <= 86400) {
        $hours = round($diff / 3600);
        return [
            'class' => 'warning',
            'text' => 'Due in ' . $hours . ' hours'
        ];
    }

    return [
        'class' => 'success',
        'text' => date('M j, g:i A', $deadlineTimestamp)
    ];
}

/**
 * دالة التحقق من الحقول الإجبارية
 */
function validateRequired(array $fields, array $data): array {
    $errors = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $errors[] = "Field '" . htmlspecialchars($field) . "' is required.";
        }
    }
    return $errors;
}

/**
 * دالة جلب قائمة الأقسام الرسمية لهندسة شبرا
 */
function getShubraDepartments(): array {
    return [
        'Computer Engineering',
        'Electrical Engineering',
        'Mechanical Engineering',
        'Civil Engineering',
        'Architectural Engineering',
        'Basic Sciences',
        'IT Department',
    ];
}