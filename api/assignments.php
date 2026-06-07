<?php
/**
 * Assignments API - CRUD Operations
 */

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$user = getCurrentUser();
if (!$user || !in_array($user['role_name'], ['admin', 'doctor', 'ta'])) {
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
            if ($user['role_name'] !== 'doctor' && $user['role_name'] !== 'admin') {
                jsonResponse(['success' => false, 'message' => 'Only doctors can create assignments.'], 403);
            }

            $courseId = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
            $title = sanitizeInput($_POST['title'] ?? '', 'string');
            $description = sanitizeInput($_POST['description'] ?? '', 'string');
            $instructions = sanitizeInput($_POST['instructions'] ?? '', 'string');
            $deadline = $_POST['deadline'] ?? '';
            $maxMarks = filter_input(INPUT_POST, 'max_marks', FILTER_VALIDATE_FLOAT) ?: 100;
            $allowedTypes = sanitizeInput($_POST['allowed_types'] ?? 'pdf,zip,doc,docx', 'string');
            $maxFileSize = filter_input(INPUT_POST, 'max_file_size', FILTER_VALIDATE_INT) ?: 10;

            if (!$courseId || empty($title) || empty($deadline)) {
                jsonResponse(['success' => false, 'message' => 'Course, title, and deadline are required.'], 400);
            }

            $db->query(
                "INSERT INTO assignments (course_id, title, description, instructions, deadline, max_marks, allowed_file_types, max_file_size_mb, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$courseId, $title, $description, $instructions, $deadline, $maxMarks, $allowedTypes, $maxFileSize, $user['id']]
            );

            $newId = $db->lastInsertId();
            logActivity('assignment_created', 'assignment', $newId, "Created assignment: $title");
            jsonResponse(['success' => true, 'message' => 'Assignment created successfully.', 'id' => $newId]);
            break;

        case 'submit':
            if ($user['role_name'] !== 'student') {
                jsonResponse(['success' => false, 'message' => 'Only students can submit assignments.'], 403);
            }

            $assignmentId = filter_input(INPUT_POST, 'assignment_id', FILTER_VALIDATE_INT);
            $submissionText = sanitizeInput($_POST['submission_text'] ?? '', 'string');

            if (!$assignmentId) {
                jsonResponse(['success' => false, 'message' => 'Assignment ID is required.'], 400);
            }

            // Handle file upload
            $fileData = ['file_name' => null, 'file_path' => null, 'file_size' => null, 'file_type' => null];
            if (isset($_FILES['submission_file']) && $_FILES['submission_file']['tmp_name']) {
                $result = uploadFile($_FILES['submission_file'], 'assignments');
                if (!$result['success']) {
                    jsonResponse(['success' => false, 'message' => $result['error']], 400);
                }
                $fileData = [
                    'file_name' => $result['filename'],
                    'file_path' => $result['path'],
                    'file_size' => $result['size'],
                    'file_type' => $result['type']
                ];
            }

            // Check if late
            $assignment = $db->query("SELECT deadline, late_submission_allowed FROM assignments WHERE id = ?", [$assignmentId])->fetch();
            $isLate = false;
            if ($assignment) {
                $isLate = strtotime('now') > strtotime($assignment['deadline']);
                if ($isLate && !$assignment['late_submission_allowed']) {
                    jsonResponse(['success' => false, 'message' => 'Late submissions are not allowed for this assignment.'], 400);
                }
            }

            // Check if already submitted
            $existing = $db->query("SELECT id FROM assignment_submissions WHERE assignment_id = ? AND student_id = ?", [$assignmentId, $user['id']])->fetch();
            if ($existing) {
                // Update existing submission
                $db->query(
                    "UPDATE assignment_submissions SET submission_text = ?, file_name = ?, file_path = ?, file_size = ?, file_type = ?, is_late = ?, submitted_at = NOW() WHERE id = ?",
                    [$submissionText, $fileData['file_name'], $fileData['file_path'], $fileData['file_size'], $fileData['file_type'], $isLate ? 1 : 0, $existing['id']]
                );
                $subId = $existing['id'];
            } else {
                $db->query(
                    "INSERT INTO assignment_submissions (assignment_id, student_id, file_name, file_path, file_size, file_type, submission_text, is_late)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    [$assignmentId, $user['id'], $fileData['file_name'], $fileData['file_path'], $fileData['file_size'], $fileData['file_type'], $submissionText, $isLate ? 1 : 0]
                );
                $subId = $db->lastInsertId();
            }

            logActivity('assignment_submitted', 'assignment_submission', $subId, "Submitted assignment $assignmentId");
            jsonResponse(['success' => true, 'message' => 'Assignment submitted successfully!' . ($isLate ? ' (Late submission)' : '')]);
            break;

        case 'grade':
            if ($user['role_name'] !== 'doctor' && $user['role_name'] !== 'ta') {
                jsonResponse(['success' => false, 'message' => 'Only doctors and TAs can grade.'], 403);
            }

            $submissionId = filter_input(INPUT_POST, 'submission_id', FILTER_VALIDATE_INT);
            $marks = filter_input(INPUT_POST, 'marks', FILTER_VALIDATE_FLOAT);
            $feedback = sanitizeInput($_POST['feedback'] ?? '', 'string');

            if (!$submissionId || $marks === null || $marks === false) {
                jsonResponse(['success' => false, 'message' => 'Submission ID and marks are required.'], 400);
            }

            $db->query(
                "UPDATE assignment_submissions SET marks_obtained = ?, feedback = ?, graded_by = ?, graded_at = NOW(), status = 'graded' WHERE id = ?",
                [$marks, $feedback, $user['id'], $submissionId]
            );

            logActivity('assignment_graded', 'assignment_submission', $submissionId, "Graded submission $submissionId with $marks marks");
            jsonResponse(['success' => true, 'message' => 'Grade submitted successfully.']);
            break;

        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid assignment ID.'], 400);

            $db->query("DELETE FROM assignments WHERE id = ?", [$id]);
            logActivity('assignment_deleted', 'assignment', $id, "Deleted assignment ID: $id");
            jsonResponse(['success' => true, 'message' => 'Assignment deleted.']);
            break;

        default:
            jsonResponse(['success' => false, 'message' => 'Unknown action.'], 400);
    }
} catch (Exception $e) {
    error_log("Assignment API error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'An error occurred.'], 500);
}
