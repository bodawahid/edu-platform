<?php
/**
 * Courses API - CRUD Operations
 */

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$user = getCurrentUser();
if (!$user || !in_array($user['role_name'], ['admin', 'doctor'])) {
    jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
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
            $courseCode = strtoupper(sanitizeInput($_POST['course_code'] ?? '', 'string'));
            $courseName = sanitizeInput($_POST['course_name'] ?? '', 'string');
            $description = sanitizeInput($_POST['description'] ?? '', 'string');
            $department = sanitizeInput($_POST['department'] ?? '', 'string');
            $semester = sanitizeInput($_POST['semester'] ?? '', 'string');
            $year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);
            $creditHours = filter_input(INPUT_POST, 'credit_hours', FILTER_VALIDATE_INT) ?: 3;

            $errors = validateRequired(['course_code', 'course_name', 'department', 'semester'], $_POST);
            if (!empty($errors)) {
                jsonResponse(['success' => false, 'message' => implode(' ', $errors)], 400);
            }

            // Check for duplicate course code
            $existing = $db->query("SELECT id FROM courses WHERE course_code = ?", [$courseCode])->fetch();
            if ($existing) {
                jsonResponse(['success' => false, 'message' => 'Course code already exists.'], 409);
            }

            $db->query(
                "INSERT INTO courses (course_code, course_name, description, department, semester, year, credit_hours, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$courseCode, $courseName, $description, $department, $semester, $year, $creditHours, $user['id']]
            );

            $newId = $db->lastInsertId();

            // Auto-assign doctor
            if ($user['role_name'] === 'doctor') {
                $db->query("INSERT INTO course_doctors (course_id, doctor_id) VALUES (?, ?)", [$newId, $user['id']]);
            }

            logActivity('course_created', 'course', $newId, "Created course: $courseCode");
            jsonResponse(['success' => true, 'message' => 'Course created successfully.', 'id' => $newId]);
            break;

        case 'update':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid course ID.'], 400);
            }

            $fields = [];
            $params = [];

            if (!empty($_POST['course_code'])) {
                $fields[] = "course_code = ?";
                $params[] = strtoupper(sanitizeInput($_POST['course_code'], 'string'));
            }
            if (!empty($_POST['course_name'])) {
                $fields[] = "course_name = ?";
                $params[] = sanitizeInput($_POST['course_name'], 'string');
            }
            if (!empty($_POST['description'])) {
                $fields[] = "description = ?";
                $params[] = sanitizeInput($_POST['description'], 'string');
            }
            if (!empty($_POST['department'])) {
                $fields[] = "department = ?";
                $params[] = sanitizeInput($_POST['department'], 'string');
            }
            if (!empty($_POST['semester'])) {
                $fields[] = "semester = ?";
                $params[] = sanitizeInput($_POST['semester'], 'string');
            }
            if (isset($_POST['year'])) {
                $fields[] = "year = ?";
                $params[] = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);
            }
            if (isset($_POST['is_active'])) {
                $fields[] = "is_active = ?";
                $params[] = filter_input(INPUT_POST, 'is_active', FILTER_VALIDATE_INT) ? 1 : 0;
            }

            if (empty($fields)) {
                jsonResponse(['success' => false, 'message' => 'No fields to update.'], 400);
            }

            $params[] = $id;
            $db->query("UPDATE courses SET " . implode(', ', $fields) . " WHERE id = ?", $params);

            logActivity('course_updated', 'course', $id, "Updated course ID: $id");
            jsonResponse(['success' => true, 'message' => 'Course updated successfully.']);
            break;

        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid course ID.'], 400);
            }

            $db->query("UPDATE courses SET is_active = 0 WHERE id = ?", [$id]);
            logActivity('course_deleted', 'course', $id, "Deactivated course ID: $id");

            jsonResponse(['success' => true, 'message' => 'Course deactivated successfully.']);
            break;

        case 'assign_ta':
            $courseId = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
            $taId = filter_input(INPUT_POST, 'ta_id', FILTER_VALIDATE_INT);

            if (!$courseId || !$taId) {
                jsonResponse(['success' => false, 'message' => 'Course ID and TA ID are required.'], 400);
            }

            // Check if already assigned
            $existing = $db->query("SELECT id FROM course_tas WHERE course_id = ? AND ta_id = ?", [$courseId, $taId])->fetch();
            if ($existing) {
                jsonResponse(['success' => false, 'message' => 'TA already assigned to this course.'], 409);
            }

            $db->query("INSERT INTO course_tas (course_id, ta_id, assigned_by) VALUES (?, ?, ?)", [$courseId, $taId, $user['id']]);
            logActivity('ta_assigned', 'course', $courseId, "Assigned TA $taId to course $courseId");

            jsonResponse(['success' => true, 'message' => 'TA assigned successfully.']);
            break;

        case 'remove_ta':
            $courseId = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
            $taId = filter_input(INPUT_POST, 'ta_id', FILTER_VALIDATE_INT);

            if (!$courseId || !$taId) {
                jsonResponse(['success' => false, 'message' => 'Course ID and TA ID are required.'], 400);
            }

            $db->query("DELETE FROM course_tas WHERE course_id = ? AND ta_id = ?", [$courseId, $taId]);
            logActivity('ta_removed', 'course', $courseId, "Removed TA $taId from course $courseId");

            jsonResponse(['success' => true, 'message' => 'TA removed successfully.']);
            break;

        default:
            jsonResponse(['success' => false, 'message' => 'Unknown action.'], 400);
    }
} catch (Exception $e) {
    error_log("Course API error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'An error occurred.'], 500);
}
