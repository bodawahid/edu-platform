<?php
/**
 * Assignments API - CRUD Operations
 */

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$user = getCurrentUser();
if (!$user || !in_array($user['role_name'], ['admin', 'doctor', 'ta', 'student'])) {
    jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
}

// السماح بـ GET فقط في حالة استعراض قائمة التسليمات لربطها بالـ Dashboard، وباقي العمليات التعديلية تتم عبر POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'view_submissions')) {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

// التحقق من الـ CSRF Token في عمليات الـ POST فقط
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !validateCSRFToken($_POST['csrf_token'] ?? '')) {
    jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 403);
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
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
            $isPublished = filter_input(INPUT_POST, 'is_published', FILTER_VALIDATE_INT) ?: 0;

            if (!$courseId || empty($title) || empty($deadline)) {
                jsonResponse(['success' => false, 'message' => 'Course, title, and deadline are required.'], 400);
            }

            $db->query(
                "INSERT INTO assignments (course_id, title, description, instructions, deadline, max_marks, allowed_file_types, max_file_size_mb, is_published, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$courseId, $title, $description, $instructions, $deadline, $maxMarks, $allowedTypes, $maxFileSize, $isPublished, $user['id']]
            );

            $newId = $db->lastInsertId();
            $fileUploaded = false;

            // Handle PDF instructions file upload
            if (isset($_FILES['instructions_file']) && $_FILES['instructions_file']['tmp_name']) {
                $result = uploadFile($_FILES['instructions_file'], 'assignments/instructions', ['pdf'], 10485760);
                if ($result['success']) {
                    $db->query(
                        "INSERT INTO assignment_files (assignment_id, file_name, file_path, file_size, file_type, uploaded_by)
                         VALUES (?, ?, ?, ?, ?, ?)",
                        [$newId, $result['filename'], $result['path'], $result['size'], $result['type'], $user['id']]
                    );
                    $fileUploaded = true;
                }
            }

            logActivity('assignment_created', 'assignment', $newId, "Created assignment: $title");
            
            // Notification for students
            addNotification(null, 'student', 'assignment', '📚 New Assignment Posted', "A new assignment has been uploaded: \"{$title}\". Check the deadline.");

            jsonResponse([
                'success' => true, 
                'message' => 'Assignment created successfully.', 
                'id' => $newId,
                'file_uploaded' => $fileUploaded
            ]);
            break;

        // 🚨 إضافة الأكشن المسؤول عن جلب تسليمات الطلاب للتصحيح ومطابقتها مع واجهة الـ Dashboard
        case 'view_submissions':
            if ($user['role_name'] !== 'doctor' && $user['role_name'] !== 'ta' && $user['role_name'] !== 'admin') {
                jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
            }

            $courseId = filter_input(INPUT_GET, 'course_id', FILTER_VALIDATE_INT);
            if (!$courseId) {
                jsonResponse(['success' => false, 'message' => 'Invalid course ID.'], 400);
            }

            // استعلام جلب بيانات تسليمات الطلاب المربوطة بالتكليفات التابعة للكورس المختار
            $submissions = $db->query(
                "SELECT sub.id, sub.file_name, sub.file_path, sub.submission_text, sub.marks_obtained, sub.submitted_at, u.full_name as student_name
                 FROM assignment_submissions sub
                 JOIN assignments a ON sub.assignment_id = a.id
                 JOIN users u ON sub.student_id = u.id
                 WHERE a.course_id = ?
                 ORDER BY sub.submitted_at DESC",
                [$courseId]
            )->fetchAll();

            jsonResponse(['success' => true, 'submissions' => $submissions]);
            break;

        case 'update':
            if ($user['role_name'] !== 'doctor' && $user['role_name'] !== 'admin') {
                jsonResponse(['success' => false, 'message' => 'Only doctors can update assignments.'], 403);
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid assignment ID.'], 400);

            // Verify ownership
            $existing = $db->query("SELECT created_by FROM assignments WHERE id = ?", [$id])->fetch();
            if (!$existing || ($existing['created_by'] != $user['id'] && $user['role_name'] !== 'admin')) {
                jsonResponse(['success' => false, 'message' => 'You do not have permission to edit this assignment.'], 403);
            }

            $fields = [];
            $params = [];

            if (!empty($_POST['title'])) { $fields[] = "title = ?"; $params[] = sanitizeInput($_POST['title'], 'string'); }
            if (isset($_POST['description'])) { $fields[] = "description = ?"; $params[] = sanitizeInput($_POST['description'], 'string'); }
            if (isset($_POST['instructions'])) { $fields[] = "instructions = ?"; $params[] = sanitizeInput($_POST['instructions'], 'string'); }
            if (!empty($_POST['deadline'])) { $fields[] = "deadline = ?"; $params[] = $_POST['deadline']; }
            if (isset($_POST['max_marks'])) { 
                $val = filter_input(INPUT_POST, 'max_marks', FILTER_VALIDATE_FLOAT);
                if ($val !== false) { $fields[] = "max_marks = ?"; $params[] = $val; }
            }

            if (empty($fields)) {
                jsonResponse(['success' => false, 'message' => 'No fields to update.'], 400);
            }

            $params[] = $id;
            $db->query("UPDATE assignments SET " . implode(', ', $fields) . " WHERE id = ?", $params);

            logActivity('assignment_updated', 'assignment', $id, "Updated assignment ID: $id");
            jsonResponse(['success' => true, 'message' => 'Assignment updated successfully.']);
            break;

        case 'toggle_publish':
            if ($user['role_name'] !== 'doctor' && $user['role_name'] !== 'admin') {
                jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $isPublished = filter_input(INPUT_POST, 'is_published', FILTER_VALIDATE_INT);

            if (!$id || $isPublished === null) {
                jsonResponse(['success' => false, 'message' => 'Assignment ID and status are required.'], 400);
            }

            // Verify ownership
            $existing = $db->query("SELECT created_by, title, is_published FROM assignments WHERE id = ?", [$id])->fetch();
            if (!$existing || ($existing['created_by'] != $user['id'] && $user['role_name'] !== 'admin')) {
                jsonResponse(['success' => false, 'message' => 'Permission denied.'], 403);
            }

            $db->query("UPDATE assignments SET is_published = ? WHERE id = ?", [$isPublished ? 1 : 0, $id]);
            
            $statusText = $isPublished ? 'published' : 'set to draft';
            logActivity('assignment_publish_toggled', 'assignment', $id, "Assignment $id $statusText");

            // Notify students if published
            if ($isPublished && !$existing['is_published']) {
                addNotification(null, 'student', 'assignment', '📢 Assignment Published', "Assignment \"{$existing['title']}\" is now published and available.");
            }

            jsonResponse(['success' => true, 'message' => 'Assignment ' . $statusText . ' successfully.']);
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

            // Check if assignment is published
            $assignment = $db->query("SELECT title, deadline, created_by, late_submission_allowed, is_published FROM assignments WHERE id = ?", [$assignmentId])->fetch();
            if (!$assignment || !$assignment['is_published']) {
                jsonResponse(['success' => false, 'message' => 'This assignment is not available for submission.'], 400);
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
            
            $studentName = htmlspecialchars($user['username'] ?? 'A student');
            $assignTitle = htmlspecialchars($assignment['title'] ?? 'Assignment');
            $instructorId = $assignment['created_by'] ?? null;
            
            addNotification($instructorId, 'doctor', 'assignment_submission', '📤 Assignment Submitted', "Student ({$studentName}) uploaded a solution for \"{$assignTitle}\".");

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
            
            $submissionData = $db->query(
                "SELECT s.student_id, a.title 
                 FROM assignment_submissions s 
                 JOIN assignments a ON s.assignment_id = a.id 
                 WHERE s.id = ?", 
                [$submissionId]
            )->fetch();

            if ($submissionData) {
                $targetStudentId = $submissionData['student_id'];
                $assignTitle = htmlspecialchars($submissionData['title'] ?? 'Assignment');
                addNotification($targetStudentId, 'student', 'assignment_graded', '📊 Assignment Graded', "Your submission for \"{$assignTitle}\" has been graded. Marks: {$marks}");
            }

            jsonResponse(['success' => true, 'message' => 'Grade submitted successfully.']);
            break;

        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid assignment ID.'], 400);

            // Verify ownership before delete
            $existing = $db->query("SELECT created_by FROM assignments WHERE id = ?", [$id])->fetch();
            if (!$existing || ($existing['created_by'] != $user['id'] && $user['role_name'] !== 'admin')) {
                jsonResponse(['success' => false, 'message' => 'Permission denied.'], 403);
            }

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