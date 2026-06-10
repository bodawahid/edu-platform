<?php
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$user = getCurrentUser();
$role = $user['role_name'];
$userId = $user['id'];

$db = Database::getInstance();

// جلب الإشعارات الغير مقروءة الموجهة للشخص ده أو للـ Role بتاعه بالكامل
$notifications = $db->query(
    "SELECT * FROM notifications 
     WHERE is_read = 0 AND (user_id = ? OR role_target = ?) 
     ORDER BY created_at DESC LIMIT 5",
    [$userId, $role]
)->fetchAll();

jsonResponse([
    'count' => count($notifications),
    'list' => $notifications
]);