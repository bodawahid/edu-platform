<?php
/**
 * Users API - CRUD Operations with Root Admin Protection
 * المطور: محمد وحيد - هندسة شبرا
 */

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$user = getCurrentUser();
// حماية أمنية: يمنع دخول أي شخص غير الأدمن لإدارة المستخدمين
if (!$user || $user['role_name'] !== 'admin') {
    jsonResponse(['success' => false, 'message' => 'Access denied. Unauthorized.'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 403);
}

$action = $_POST['action'] ?? '';
$db = Database::getInstance();

try {
    switch ($action) {
        case 'create':
            if (empty($_POST['full_name']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role_id'])) {
                jsonResponse(['success' => false, 'message' => 'Please fill in all required fields (*)'], 400);
            }

            $username = sanitizeInput($_POST['username'], 'string');
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            $fullName = sanitizeInput($_POST['full_name'], 'string');
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $roleId = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
            $department = sanitizeInput($_POST['department'] ?? '', 'string');

            if (!$email) {
                jsonResponse(['success' => false, 'message' => 'Invalid email address format.'], 400);
            }

            // فحص تكرار اليوزر أو الإيميل
            $existing = $db->query("SELECT id FROM users WHERE username = ? OR email = ?", [$username, $email])->fetch();
            if ($existing) {
                jsonResponse(['success' => false, 'message' => 'Username or Email already exists in the system.'], 409);
            }

            // 🚨 الاستعلام مطابق للداتا بيز بالظبط (password_hash و is_active)
            $db->query(
                "INSERT INTO users (username, email, password_hash, full_name, role_id, department, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)",
                [$username, $email, $password, $fullName, $roleId, $department]
            );

            logActivity('user_created', 'user', $db->lastInsertId(), "Created user: $username");
            jsonResponse(['success' => true, 'message' => 'User created successfully!']);
            break;

        case 'get_user':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid User ID.'], 400);

            // جلب البيانات مع مطابقة اسم الحقل
            $targetUser = $db->query("SELECT id, username, email, full_name, role_id, department, is_active FROM users WHERE id = ?", [$id])->fetch();
            if (!$targetUser) jsonResponse(['success' => false, 'message' => 'User not found.'], 404);

            jsonResponse(['success' => true, 'user' => $targetUser]);
            break;

        case 'update':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid User ID.'], 400);

            // 🚨 حماية الأدمن رقم 1 (محمد وحيد)
            if ($id == 1 && $user['id'] != 1) {
                jsonResponse(['success' => false, 'message' => 'Security Error: You do not have permissions to modify the Root Administrator account.'], 403);
            }

            $fullName = sanitizeInput($_POST['full_name'] ?? '', 'string');
            $roleId = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
            $department = sanitizeInput($_POST['department'] ?? '', 'string');
            $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

            if ($id == 1) {
                $roleId = 1; 
                $isActive = 1;
            }

            $db->query(
                "UPDATE users SET full_name = ?, role_id = ?, department = ?, is_active = ? WHERE id = ?",
                [$fullName, $roleId, $department, $isActive, $id]
            );

            // تعديل الباسورد لو اتكتب في الحقل الاختياري مع الحقل الصحيح password_hash
            if (!empty($_POST['password'])) {
                $newPass = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $db->query("UPDATE users SET password_hash = ? WHERE id = ?", [$newPass, $id]);
            }

            logActivity('user_updated', 'user', $id, "Updated user ID: $id");
            jsonResponse(['success' => true, 'message' => 'User updated successfully!']);
            break;

        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid User ID.'], 400);

            if ($id == 1) {
                jsonResponse(['success' => false, 'message' => 'Critical Protection: Root Administrator account cannot be deactivated.'], 403);
            }

            $db->query("UPDATE users SET is_active = 0 WHERE id = ?", [$id]);
            logActivity('user_suspended', 'user', $id, "Deactivated user ID: $id");
            jsonResponse(['success' => true, 'message' => 'User account deactivated successfully.']);
            break;

        default:
            jsonResponse(['success' => false, 'message' => 'Unknown action.'], 400);
    }
} catch (Exception $e) {
    error_log("Users API error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'An error occurred during execution: ' . $e->getMessage()], 500);
}