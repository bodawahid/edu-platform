<?php
/**
 * Users API - CRUD Operations
 */

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

// Check admin access
$user = getCurrentUser();
if (!$user || $user['role_name'] !== 'admin') {
    jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

// Validate CSRF
if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 403);
}

$action = $_POST['action'] ?? '';
$db = Database::getInstance();

try {
    switch ($action) {
        case 'create':
            $fullName = sanitizeInput($_POST['full_name'] ?? '', 'string');
            $username = sanitizeInput($_POST['username'] ?? '', 'string');
            $email = sanitizeInput($_POST['email'] ?? '', 'email');
            $password = $_POST['password'] ?? '';
            $roleId = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
            $department = sanitizeInput($_POST['department'] ?? '', 'string');

            $errors = validateRequired(['full_name', 'username', 'email', 'password'], $_POST);
            if (!empty($errors)) {
                jsonResponse(['success' => false, 'message' => implode(' ', $errors)], 400);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                jsonResponse(['success' => false, 'message' => 'Invalid email address.'], 400);
            }

            // Check for existing username/email
            $existing = $db->query("SELECT id FROM users WHERE username = ? OR email = ?", [$username, $email])->fetch();
            if ($existing) {
                jsonResponse(['success' => false, 'message' => 'Username or email already exists.'], 409);
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $db->query(
                "INSERT INTO users (username, email, password_hash, full_name, role_id, department) VALUES (?, ?, ?, ?, ?, ?)",
                [$username, $email, $passwordHash, $fullName, $roleId, $department]
            );

            $newId = $db->lastInsertId();
            logActivity('user_created', 'user', $newId, "Admin created user: $username");

            jsonResponse(['success' => true, 'message' => 'User created successfully.', 'id' => $newId]);
            break;

        case 'update':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid user ID.'], 400);
            }

            $fields = [];
            $params = [];

            if (!empty($_POST['full_name'])) {
                $fields[] = "full_name = ?";
                $params[] = sanitizeInput($_POST['full_name'], 'string');
            }
            if (!empty($_POST['email'])) {
                $fields[] = "email = ?";
                $params[] = sanitizeInput($_POST['email'], 'email');
            }
            if (!empty($_POST['department'])) {
                $fields[] = "department = ?";
                $params[] = sanitizeInput($_POST['department'], 'string');
            }
            if (isset($_POST['role_id'])) {
                $fields[] = "role_id = ?";
                $params[] = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
            }
            if (isset($_POST['is_active'])) {
                $fields[] = "is_active = ?";
                $params[] = filter_input(INPUT_POST, 'is_active', FILTER_VALIDATE_INT) ? 1 : 0;
            }
            if (!empty($_POST['password'])) {
                $fields[] = "password_hash = ?";
                $params[] = password_hash($_POST['password'], PASSWORD_BCRYPT);
            }

            if (empty($fields)) {
                jsonResponse(['success' => false, 'message' => 'No fields to update.'], 400);
            }

            $params[] = $id;
            $db->query("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?", $params);

            logActivity('user_updated', 'user', $id, "Admin updated user ID: $id");
            jsonResponse(['success' => true, 'message' => 'User updated successfully.']);
            break;

        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid user ID.'], 400);
            }

            if ($id == $user['id']) {
                jsonResponse(['success' => false, 'message' => 'Cannot delete yourself.'], 400);
            }

            $db->query("UPDATE users SET is_active = 0 WHERE id = ?", [$id]);
            logActivity('user_deleted', 'user', $id, "Admin deactivated user ID: $id");

            jsonResponse(['success' => true, 'message' => 'User deactivated successfully.']);
            break;

        default:
            jsonResponse(['success' => false, 'message' => 'Unknown action.'], 400);
    }
} catch (Exception $e) {
    error_log("User API error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'An error occurred.'], 500);
}
