<?php
date_default_timezone_set('Africa/Cairo');
/**
 * Quizzes API - CRUD Operations
 * FIXED: Session handling, CSRF validation, timer persistence, timezone, FOR UPDATE removed
 */

// ═══════════════════════════════════════════════════════════════
// 1️⃣ Start session BEFORE any output — critical for CSRF
// ═══════════════════════════════════════════════════════════════
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 0,
        'cookie_path' => '/',
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$user = getCurrentUser();

// Determine allowed actions based on role
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$isStudentAction = in_array($action, ['submit_attempt', 'start_attempt'], true);

if (!$user) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

// Role-based access control
if (!in_array($user['role_name'], ['admin', 'doctor', 'student'])) {
    jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
}

// Students can only do start_attempt and submit_attempt
if ($user['role_name'] === 'student' && !$isStudentAction) {
    jsonResponse(['success' => false, 'message' => 'Students can only take quizzes.'], 403);
}

// Doctors/Admins can do everything except student-only actions
if (in_array($user['role_name'], ['admin', 'doctor']) && $isStudentAction) {
    jsonResponse(['success' => false, 'message' => 'Invalid action for this role.'], 403);
}

// Method validation
$allowedMethods = ['POST'];
if ($action === 'get_question') {
    $allowedMethods[] = 'GET';
}

if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods, true)) {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

// ═══════════════════════════════════════════════════════════════
// 2️⃣ CSRF VALIDATION — Check token exists in session first
// ═══════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    
    if (empty($token)) {
        error_log("CSRF Token missing. Session ID: " . session_id() . " | Session csrf_token exists: " . (isset($_SESSION['csrf_token']) ? 'yes' : 'no'));
        jsonResponse(['success' => false, 'message' => 'CSRF token missing. Please refresh the page.'], 403);
    }
    
    if (!validateCSRFToken($token)) {
        error_log("CSRF Token invalid. Provided: " . substr($token, 0, 8) . "... | Session: " . substr($_SESSION['csrf_token'] ?? 'EMPTY', 0, 8) . "...");
        jsonResponse(['success' => false, 'message' => 'Invalid CSRF token. Please refresh the page.'], 403);
    }
}

$db = Database::getInstance();

try {
    switch ($action) {
        // ─────────────────────────────────────────
        // ADMIN/DOCTOR ACTIONS
        // ─────────────────────────────────────────

        case 'create':
            $courseId = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
            $title = sanitizeInput($_POST['title'] ?? '', 'string');
            $description = sanitizeInput($_POST['description'] ?? '', 'string');
            $quizType = sanitizeInput($_POST['quiz_type'] ?? 'mixed', 'string');
            $duration = filter_input(INPUT_POST, 'duration_minutes', FILTER_VALIDATE_INT) ?: 30;
            $totalMarks = filter_input(INPUT_POST, 'total_marks', FILTER_VALIDATE_FLOAT) ?: 100;
            $passingMarks = filter_input(INPUT_POST, 'passing_marks', FILTER_VALIDATE_FLOAT) ?: 50;
            $startTime = $_POST['start_time'] ?? '';
            $endTime = $_POST['end_time'] ?? '';

            if (!$courseId || empty($title)) {
                jsonResponse(['success' => false, 'message' => 'Course and title are required.'], 400);
            }

            if ($totalMarks <= 0) {
                jsonResponse(['success' => false, 'message' => 'Total marks must be greater than 0.'], 400);
            }

            if ($passingMarks > $totalMarks) {
                jsonResponse(['success' => false, 'message' => 'Passing marks cannot exceed total marks.'], 400);
            }

            if (!empty($startTime) && !strtotime($startTime)) {
                jsonResponse(['success' => false, 'message' => 'Invalid start time format.'], 400);
            }
            if (!empty($endTime) && !strtotime($endTime)) {
                jsonResponse(['success' => false, 'message' => 'Invalid end time format.'], 400);
            }
            if (!empty($startTime) && !empty($endTime) && strtotime($endTime) <= strtotime($startTime)) {
                jsonResponse(['success' => false, 'message' => 'End time must be after start time.'], 400);
            }

            $db->query(
                "INSERT INTO quizzes (course_id, title, description, quiz_type, duration_minutes, total_marks, passing_marks, start_time, end_time, created_by, is_published, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())",
                [$courseId, $title, $description, $quizType, $duration, $totalMarks, $passingMarks, $startTime, $endTime, $user['id']]
            );

            $newId = $db->lastInsertId();
            logActivity('quiz_created', 'quiz', $newId, "Created quiz: $title");
            addNotification(null, 'student', 'quiz', '📝 New Quiz Added', "Dr. posted a new quiz: \"{$title}\". Check your schedule.");
            jsonResponse(['success' => true, 'message' => 'Quiz created successfully.', 'id' => $newId]);
            break;

        case 'add_question':
            $quizId = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
            $questionText = sanitizeInput($_POST['question_text'] ?? '', 'string');
            $questionType = sanitizeInput($_POST['question_type'] ?? 'mcq', 'string');
            $marks = filter_input(INPUT_POST, 'marks', FILTER_VALIDATE_FLOAT) ?: 1;
            $options = $_POST['options'] ?? [];
            $correctOption = filter_input(INPUT_POST, 'correct_option', FILTER_VALIDATE_INT);

            if (!$quizId || empty($questionText)) {
                jsonResponse(['success' => false, 'message' => 'Quiz ID and question text are required.'], 400);
            }

            $quiz = $db->query("SELECT created_by FROM quizzes WHERE id = ?", [$quizId])->fetch();
            if (!$quiz) {
                jsonResponse(['success' => false, 'message' => 'Quiz not found.'], 404);
            }
            if ($quiz['created_by'] != $user['id'] && $user['role_name'] !== 'admin') {
                jsonResponse(['success' => false, 'message' => 'Permission denied.'], 403);
            }

            $correctAnswer = '';
            if ($questionType === 'true_false') {
                $correctAnswer = $correctOption === 0 ? 'True' : 'False';
            } else {
                $correctAnswer = $options[$correctOption] ?? '';
            }

            $db->query(
                "INSERT INTO questions (quiz_id, question_text, question_type, marks, correct_answer)
                 VALUES (?, ?, ?, ?, ?)",
                [$quizId, $questionText, $questionType, $marks, $correctAnswer]
            );

            $questionId = $db->lastInsertId();

            if ($questionType === 'mcq') {
                foreach ($options as $idx => $optText) {
                    if (!empty($optText)) {
                        $db->query(
                            "INSERT INTO question_options (question_id, option_text, is_correct, option_order)
                             VALUES (?, ?, ?, ?)",
                            [$questionId, $optText, $idx == $correctOption ? 1 : 0, $idx]
                        );
                    }
                }
            } else {
                $db->query("INSERT INTO question_options (question_id, option_text, is_correct, option_order) VALUES (?, 'True', ?, 0)", [$questionId, $correctOption == 0 ? 1 : 0]);
                $db->query("INSERT INTO question_options (question_id, option_text, is_correct, option_order) VALUES (?, 'False', ?, 1)", [$questionId, $correctOption == 1 ? 1 : 0]);
            }

            logActivity('question_added', 'question', $questionId, "Added question to quiz $quizId");
            jsonResponse(['success' => true, 'message' => 'Question added successfully.', 'id' => $questionId]);
            break;

        case 'get_question':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid question ID.'], 400);
            }

            $question = $db->query("SELECT * FROM questions WHERE id = ?", [$id])->fetch();
            if (!$question) {
                jsonResponse(['success' => false, 'message' => 'Question not found.'], 404);
            }

            jsonResponse(['success' => true, 'question' => $question]);
            break;

        case 'update_question':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $questionText = sanitizeInput($_POST['question_text'] ?? '', 'string');
            $marks = filter_input(INPUT_POST, 'points', FILTER_VALIDATE_FLOAT);

            if (!$id || empty($questionText)) {
                jsonResponse(['success' => false, 'message' => 'Question ID and text are required.'], 400);
            }

            $question = $db->query("SELECT q.*, quiz.created_by FROM questions q JOIN quizzes quiz ON q.quiz_id = quiz.id WHERE q.id = ?", [$id])->fetch();
            if (!$question) {
                jsonResponse(['success' => false, 'message' => 'Question not found.'], 404);
            }
            if ($question['created_by'] != $user['id'] && $user['role_name'] !== 'admin') {
                jsonResponse(['success' => false, 'message' => 'Permission denied.'], 403);
            }

            $db->query(
                "UPDATE questions SET question_text = ?, marks = ? WHERE id = ?",
                [$questionText, $marks, $id]
            );

            logActivity('question_updated', 'question', $id, "Updated question ID: $id");
            jsonResponse(['success' => true, 'message' => 'Question updated successfully.']);
            break;

        case 'delete_question':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid question ID.'], 400);
            }

            $question = $db->query("SELECT q.*, quiz.created_by FROM questions q JOIN quizzes quiz ON q.quiz_id = quiz.id WHERE q.id = ?", [$id])->fetch();
            if (!$question) {
                jsonResponse(['success' => false, 'message' => 'Question not found.'], 404);
            }
            if ($question['created_by'] != $user['id'] && $user['role_name'] !== 'admin') {
                jsonResponse(['success' => false, 'message' => 'Permission denied.'], 403);
            }

            $db->beginTransaction();
            try {
                $db->query("DELETE FROM question_options WHERE question_id = ?", [$id]);
                $db->query("DELETE FROM questions WHERE id = ?", [$id]);
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }

            logActivity('question_deleted', 'question', $id, "Deleted question ID: $id");
            jsonResponse(['success' => true, 'message' => 'Question and its options successfully deleted.']);
            break;

        case 'update':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid quiz ID.'], 400);

            $existing = $db->query("SELECT created_by FROM quizzes WHERE id = ?", [$id])->fetch();
            if (!$existing || ($existing['created_by'] != $user['id'] && $user['role_name'] !== 'admin')) {
                jsonResponse(['success' => false, 'message' => 'Permission denied.'], 403);
            }

            $fields = [];
            $params = [];

            if (!empty($_POST['title'])) { $fields[] = "title = ?"; $params[] = sanitizeInput($_POST['title'], 'string'); }
            if (isset($_POST['description'])) { $fields[] = "description = ?"; $params[] = sanitizeInput($_POST['description'], 'string'); }
            if (isset($_POST['duration_minutes'])) {
                $val = filter_input(INPUT_POST, 'duration_minutes', FILTER_VALIDATE_INT);
                if ($val !== false && $val > 0) { $fields[] = "duration_minutes = ?"; $params[] = $val; }
            }
            if (isset($_POST['total_marks'])) {
                $val = filter_input(INPUT_POST, 'total_marks', FILTER_VALIDATE_FLOAT);
                if ($val !== false && $val > 0) { $fields[] = "total_marks = ?"; $params[] = $val; }
            }
            if (isset($_POST['passing_marks'])) {
                $val = filter_input(INPUT_POST, 'passing_marks', FILTER_VALIDATE_FLOAT);
                if ($val !== false && $val >= 0) { $fields[] = "passing_marks = ?"; $params[] = $val; }
            }
            if (isset($_POST['start_time'])) { $fields[] = "start_time = ?"; $params[] = $_POST['start_time']; }
            if (isset($_POST['end_time'])) { $fields[] = "end_time = ?"; $params[] = $_POST['end_time']; }
            if (isset($_POST['is_published'])) {
                $fields[] = "is_published = ?";
                $params[] = filter_input(INPUT_POST, 'is_published', FILTER_VALIDATE_INT) ? 1 : 0;
            }

            if (empty($fields)) jsonResponse(['success' => false, 'message' => 'No fields to update.'], 400);

            $params[] = $id;
            $db->query("UPDATE quizzes SET " . implode(', ', $fields) . " WHERE id = ?", $params);
            logActivity('quiz_updated', 'quiz', $id, "Updated quiz ID: $id");
            jsonResponse(['success' => true, 'message' => 'Quiz updated.']);
            break;

        case 'toggle_publish':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $isPublished = filter_input(INPUT_POST, 'is_published', FILTER_VALIDATE_INT);

            if (!$id || $isPublished === null) {
                jsonResponse(['success' => false, 'message' => 'Quiz ID and status are required.'], 400);
            }

            $existing = $db->query("SELECT created_by, title, is_published FROM quizzes WHERE id = ?", [$id])->fetch();
            if (!$existing || ($existing['created_by'] != $user['id'] && $user['role_name'] !== 'admin')) {
                jsonResponse(['success' => false, 'message' => 'Permission denied.'], 403);
            }

            $db->query("UPDATE quizzes SET is_published = ? WHERE id = ?", [$isPublished ? 1 : 0, $id]);

            $statusText = $isPublished ? 'published' : 'set to draft';
            logActivity('quiz_publish_toggled', 'quiz', $id, "Quiz $id $statusText");

            if ($isPublished && !$existing['is_published']) {
                addNotification(null, 'student', 'quiz', '📝 Quiz Published', "Quiz \"{$existing['title']}\" is now published and ready to take.");
            }

            jsonResponse(['success' => true, 'message' => 'Quiz ' . $statusText . ' successfully.']);
            break;

        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid quiz ID.'], 400);

            $existing = $db->query("SELECT created_by FROM quizzes WHERE id = ?", [$id])->fetch();
            if (!$existing || ($existing['created_by'] != $user['id'] && $user['role_name'] !== 'admin')) {
                jsonResponse(['success' => false, 'message' => 'Permission denied.'], 403);
            }

            $db->beginTransaction();
            try {
                $db->query("DELETE FROM quiz_answers WHERE attempt_id IN (SELECT id FROM quiz_attempts WHERE quiz_id = ?)", [$id]);
                $db->query("DELETE FROM quiz_attempts WHERE quiz_id = ?", [$id]);
                $db->query("DELETE FROM question_options WHERE question_id IN (SELECT id FROM questions WHERE quiz_id = ?)", [$id]);
                $db->query("DELETE FROM questions WHERE quiz_id = ?", [$id]);
                $db->query("DELETE FROM quizzes WHERE id = ?", [$id]);
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }

            logActivity('quiz_deleted', 'quiz', $id, "Deleted quiz ID: $id");
            jsonResponse(['success' => true, 'message' => 'Quiz deleted.']);
            break;

        // ─────────────────────────────────────────
        // STUDENT ACTIONS
        // ─────────────────────────────────────────

        case 'start_attempt':
            $quizId = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
            if (!$quizId) {
                jsonResponse(['success' => false, 'message' => 'Quiz ID required.'], 400);
            }

            $quiz = $db->query(
                "SELECT q.*, c.course_name FROM quizzes q
                 JOIN courses c ON q.course_id = c.id
                 WHERE q.id = ? AND q.is_published = 1",
                [$quizId]
            )->fetch();

            if (!$quiz) {
                jsonResponse(['success' => false, 'message' => 'Quiz not found or not available.'], 404);
            }

            $enrolled = $db->query(
                "SELECT 1 FROM course_enrollments WHERE course_id = ? AND student_id = ? AND status = 'active'",
                [$quiz['course_id'], $user['id']]
            )->fetch();

            if (!$enrolled) {
                jsonResponse(['success' => false, 'message' => 'You are not enrolled in this course.'], 403);
            }

            $now = time();
            $startTime = strtotime($quiz['start_time']);
            $endTime = strtotime($quiz['end_time']);

            if ($startTime && $now < $startTime) {
                jsonResponse(['success' => false, 'message' => 'Quiz has not started yet.'], 403);
            }
            if ($endTime && $now > $endTime) {
                jsonResponse(['success' => false, 'message' => 'Quiz has ended.'], 403);
            }

            $completed = $db->query(
                "SELECT id FROM quiz_attempts WHERE quiz_id = ? AND student_id = ? AND status IN ('submitted', 'graded', 'auto_submitted')",
                [$quizId, $user['id']]
            )->fetch();

            if ($completed) {
                jsonResponse(['success' => false, 'message' => 'You have already completed this quiz.'], 403);
            }

            $existing = $db->query(
                "SELECT id, started_at FROM quiz_attempts WHERE quiz_id = ? AND student_id = ? AND status = 'started'",
                [$quizId, $user['id']]
            )->fetch();

            if ($existing) {
                $startedAt = strtotime($existing['started_at']);
                $elapsed = $now - $startedAt;
                $durationSeconds = $quiz['duration_minutes'] * 60;
                $remaining = max(0, $durationSeconds - $elapsed);

                jsonResponse([
                    'success' => true,
                    'attempt_id' => $existing['id'],
                    'message' => 'Continuing existing attempt.',
                    'remaining_seconds' => $remaining,
                    'duration_minutes' => $quiz['duration_minutes']
                ]);
            }

            $db->query(
                "INSERT INTO quiz_attempts (quiz_id, student_id, total_marks, ip_address, status, started_at)
                 VALUES (?, ?, ?, ?, 'started', NOW())",
                [$quizId, $user['id'], $quiz['total_marks'], $_SERVER['REMOTE_ADDR'] ?? '']
            );

            $attemptId = $db->lastInsertId();
            jsonResponse([
                'success' => true,
                'attempt_id' => $attemptId,
                'message' => 'Quiz started!',
                'remaining_seconds' => $quiz['duration_minutes'] * 60,
                'duration_minutes' => $quiz['duration_minutes']
            ]);
            break;

        case 'submit_attempt':
            $quizId = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
            $answers = $_POST['answers'] ?? [];
            $timeRemaining = filter_input(INPUT_POST, 'time_remaining', FILTER_VALIDATE_INT);
            $autoSubmitted = ($_POST['auto_submitted'] ?? '0') === '1';

            if (!$quizId) {
                jsonResponse(['success' => false, 'message' => 'Quiz ID required.'], 400);
            }

            if (empty($answers)) {
                jsonResponse(['success' => false, 'message' => 'No answers provided.'], 400);
            }

            $quiz = $db->query(
                "SELECT q.*, c.course_name FROM quizzes q
                 JOIN courses c ON q.course_id = c.id
                 WHERE q.id = ? AND q.is_published = 1",
                [$quizId]
            )->fetch();

            if (!$quiz) {
                jsonResponse(['success' => false, 'message' => 'Quiz not found or not available.'], 404);
            }

            $enrolled = $db->query(
                "SELECT 1 FROM course_enrollments WHERE course_id = ? AND student_id = ? AND status = 'active'",
                [$quiz['course_id'], $user['id']]
            )->fetch();

            if (!$enrolled) {
                jsonResponse(['success' => false, 'message' => 'You are not enrolled in this course.'], 403);
            }

            // FIXED: Removed FOR UPDATE (causes issues on some MySQL configs)
            $attempt = $db->query(
                "SELECT id, status, started_at FROM quiz_attempts
                 WHERE quiz_id = ? AND student_id = ? AND status = 'started'",
                [$quizId, $user['id']]
            )->fetch();

            if (!$attempt) {
                jsonResponse(['success' => false, 'message' => 'No active quiz attempt found. Please start the quiz first.'], 400);
            }

            if ($attempt['status'] !== 'started') {
                jsonResponse(['success' => false, 'message' => 'Quiz already submitted.'], 400);
            }

            $startedAt = strtotime($attempt['started_at']);
            $elapsed = time() - $startedAt;
            $maxDuration = $quiz['duration_minutes'] * 60;

            if ($elapsed > ($maxDuration + 30)) {
                jsonResponse(['success' => false, 'message' => 'Quiz time limit exceeded.'], 403);
            }

            // CRITICAL: Update status FIRST to prevent double-submit race
            $db->query(
                "UPDATE quiz_attempts SET status = 'submitted' WHERE id = ? AND status = 'started'",
                [$attempt['id']]
            );

            if ($db->rowCount() === 0) {
                jsonResponse(['success' => false, 'message' => 'Quiz already submitted by another request.'], 400);
            }

            $totalScore = 0;
            $processedAnswers = 0;

            foreach ($answers as $questionId => $selectedAnswer) {
                $questionId = filter_var($questionId, FILTER_VALIDATE_INT);
                if (!$questionId) continue;

                $question = $db->query(
                    "SELECT correct_answer, marks FROM questions WHERE id = ? AND quiz_id = ?",
                    [$questionId, $quizId]
                )->fetch();

                if ($question) {
                    $isCorrect = strtolower(trim($selectedAnswer)) === strtolower(trim($question['correct_answer']));
                    $marksObtained = $isCorrect ? (float)$question['marks'] : 0;
                    $totalScore += $marksObtained;
                    $processedAnswers++;

                    $db->query(
                        "INSERT INTO quiz_answers (attempt_id, question_id, selected_answer, is_correct, marks_obtained)
                         VALUES (?, ?, ?, ?, ?)
                         ON DUPLICATE KEY UPDATE selected_answer = ?, is_correct = ?, marks_obtained = ?",
                        [$attempt['id'], $questionId, $selectedAnswer, $isCorrect ? 1 : 0, $marksObtained,
                         $selectedAnswer, $isCorrect ? 1 : 0, $marksObtained]
                    );
                }
            }

            $totalMarks = (float)$quiz['total_marks'];
            $percentage = $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0;

            $status = $autoSubmitted ? 'auto_submitted' : 'submitted';

            $db->query(
                "UPDATE quiz_attempts
                 SET score = ?, percentage = ?, status = ?, submitted_at = NOW(), time_remaining_seconds = ?
                 WHERE id = ?",
                [$totalScore, $percentage, $status, $timeRemaining, $attempt['id']]
            );

            logActivity('quiz_submitted', 'quiz_attempt', $attempt['id'],
                "Submitted quiz $quizId with score $totalScore/$totalMarks ($percentage%)");

            $studentName = htmlspecialchars($user['username'] ?? 'A student');
            $quizTitle = htmlspecialchars($quiz['title'] ?? 'Quiz');
            $instructorId = $quiz['created_by'] ?? null;

            if ($instructorId) {
                addNotification($instructorId, 'doctor', 'quiz_submission',
                    '🎓 Quiz Attempt Submitted',
                    "Student ({$studentName}) submitted \"{$quizTitle}\". Score: {$totalScore}/{$totalMarks}");
            }

            jsonResponse([
                'success' => true,
                'message' => 'Quiz submitted successfully!',
                'score' => $totalScore,
                'total_marks' => $totalMarks,
                'percentage' => round($percentage, 2),
                'status' => $status,
                'answers_processed' => $processedAnswers
            ]);
            break;

        default:
            jsonResponse(['success' => false, 'message' => 'Unknown action.'], 400);
    }
} catch (Exception $e) {
    error_log("Quiz API error: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
    jsonResponse(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
}