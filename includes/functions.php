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
        $requestPayload = [
            'get' => $_GET ?? [],
            'post' => $_POST ?? []
        ];

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
                json_encode($requestPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                $severity,
                $actionTaken,
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]
        );
    } catch (Exception $e) {
        error_log("Failed to log security event: " . $e->getMessage());
    }
}

// ========== AI THREAT DETECTION MIDDLEWARE ==========

class AIThreatDetectionMiddleware {
    private const DEFAULT_SERVICE_URL = 'http://127.0.0.1:5000/predict';
    private const DEFAULT_TIMEOUT_MS = 100;
    private const MAX_PAYLOAD_BYTES = 20000;
    private static bool $hasRun = false;

    public static function handleGlobalRequest(): void {
        if (self::$hasRun || PHP_SAPI === 'cli') {
            return;
        }
        self::$hasRun = true;

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (!in_array($method, ['GET', 'POST'], true)) {
            return;
        }

        $payload = self::buildInputVector($_GET ?? [], $_POST ?? []);
        if (empty($payload['request_data']['get']) && empty($payload['request_data']['post'])) {
            return;
        }

        $prediction = self::analyzeWithModel($payload);
        if (!($prediction['is_attack'] ?? false)) {
            return;
        }

        $severity = strtolower((string)($prediction['severity'] ?? 'medium'));
        if ($severity !== 'critical') {
            return;
        }

        $attackType = (string)($prediction['attack_type'] ?? 'ML Threat Detection');
        $reason = (string)($prediction['reason'] ?? 'AI model flagged this request as critical.');
        logSecurityEvent($attackType, $reason, 'critical', 'blocked');

        self::blockRequest();
    }

    private static function buildInputVector(array $getData, array $postData): array {
        $cleanGet = self::truncatePayload($getData);
        $cleanPost = self::truncatePayload($postData);

        return [
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'request_data' => [
                'get' => $cleanGet,
                'post' => $cleanPost
            ]
        ];
    }

    private static function truncatePayload(array $input): array {
        $encoded = json_encode($input, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            return [];
        }
        if (strlen($encoded) <= self::MAX_PAYLOAD_BYTES) {
            return $input;
        }

        $limited = self::truncateValue($input, 0);
        if (is_array($limited)) {
            $limited['_truncated'] = true;
            $limited['_original_size'] = strlen($encoded);
            return $limited;
        }

        return ['_truncated' => true, '_original_size' => strlen($encoded)];
    }

    private static function truncateValue($value, int $depth) {
        if ($depth >= 5) {
            return '[max-depth]';
        }

        if (is_array($value)) {
            $limitedArray = [];
            $keys = array_keys($value);
            $totalItems = count($keys);
            $headKeys = array_slice($keys, 0, 50);
            $tailKeys = $totalItems > 100 ? array_slice($keys, -50) : [];

            foreach ($headKeys as $key) {
                $limitedArray[$key] = self::truncateValue($value[$key], $depth + 1);
            }

            if ($totalItems > 100) {
                $limitedArray['_items_truncated'] = true;
                $limitedArray['_truncated_count'] = $totalItems - 100;
            }

            foreach ($tailKeys as $key) {
                $limitedArray[$key] = self::truncateValue($value[$key], $depth + 1);
            }
            return $limitedArray;
        }

        if (is_string($value)) {
            if (strlen($value) > 512) {
                return substr($value, 0, 512) . '...[truncated]';
            }
            return $value;
        }

        return $value;
    }

    private static function analyzeWithModel(array $payload): array {
        $serviceResult = self::analyzeWithPythonService($payload);
        if ($serviceResult !== null) {
            return self::normalizePrediction($serviceResult);
        }

        $wrapperResult = self::analyzeWithPythonWrapper($payload);
        if ($wrapperResult !== null) {
            return self::normalizePrediction($wrapperResult);
        }

        if (filter_var(getenv('WAF_ML_FAIL_CLOSED') ?: '0', FILTER_VALIDATE_BOOLEAN)) {
            return [
                'is_attack' => true,
                'severity' => 'critical',
                'attack_type' => 'ML Service Unavailable',
                'reason' => 'Threat detection service unavailable while fail-closed mode is enabled.'
            ];
        }

        return ['is_attack' => false, 'severity' => 'low'];
    }

    private static function analyzeWithPythonService(array $payload): ?array {
        $endpoint = getenv('WAF_ML_ENDPOINT') ?: self::DEFAULT_SERVICE_URL;
        if (!function_exists('curl_init')) {
            return null;
        }
        if (!self::isTrustedModelEndpoint($endpoint)) {
            return null;
        }

        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($body === false) {
            return null;
        }

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT_MS => self::DEFAULT_TIMEOUT_MS,
            CURLOPT_TIMEOUT_MS => self::DEFAULT_TIMEOUT_MS
        ]);

        $response = curl_exec($ch);
        $statusCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $statusCode < 200 || $statusCode >= 300) {
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }

    private static function analyzeWithPythonWrapper(array $payload): ?array {
        // Optional safe skeleton for a local wrapper bridge.
        // Implement with proc_open/IPC in deployment if HTTP microservice is unavailable.
        return null;
    }

    private static function normalizePrediction(array $prediction): array {
        $severity = strtolower((string)($prediction['severity'] ?? 'low'));
        $allowedSeverity = ['low', 'medium', 'high', 'critical'];
        if (!in_array($severity, $allowedSeverity, true)) {
            $severity = 'low';
        }

        return [
            'is_attack' => (bool)($prediction['is_attack'] ?? false),
            'severity' => $severity,
            'attack_type' => $prediction['attack_type'] ?? 'ML Threat Detection',
            'reason' => $prediction['reason'] ?? 'AI threat detection event.'
        ];
    }

    private static function isTrustedModelEndpoint(string $endpoint): bool {
        $parts = parse_url($endpoint);
        if ($parts === false || empty($parts['host'])) {
            return false;
        }

        $host = strtolower((string)$parts['host']);
        $scheme = strtolower((string)($parts['scheme'] ?? 'http'));
        $isLocalhost = in_array($host, ['127.0.0.1', 'localhost', '::1'], true);

        return $isLocalhost && in_array($scheme, ['http', 'https'], true);
    }

    private static function blockRequest(): void {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Security violation detected. Request blocked.']);
        exit;
    }
}

AIThreatDetectionMiddleware::handleGlobalRequest();

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