<?php
/**
 * Quizzes API - CRUD Operations
 */

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$user = getCurrentUser();
if (!$user || !in_array($user['role_name'], ['admin', 'doctor'])) {
    // استثناء حالة الـ submit_attempt والـ start_attempt لأن الطالب هو اللي بينادي عليهم
    $action = $_POST['action'] ?? '';
    if (!in_array($action, ['submit_attempt', 'start_attempt'], true)) {
        jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
    }
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

            $db->query(
                "INSERT INTO quizzes (course_id, title, description, quiz_type, duration_minutes, total_marks, passing_marks, start_time, end_time, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$courseId, $title, $description, $quizType, $duration, $totalMarks, $passingMarks, $startTime, $endTime, $user['id']]
            );

            $newId = $db->lastInsertId();
            logActivity('quiz_created', 'quiz', $newId, "Created quiz: $title");
            
            // 🚨 1️⃣ إشعار للطلبة: الدكتور نزل كويز جديد
            addNotification(null, 'student', 'quiz', '📝 New Quiz Added', "Dr. posted a new quiz: \"{$title}\". Check your schedule.");

            jsonResponse(['success' => true, 'message' => 'Quiz created successfully.', 'id' => $newId]);
            break;

        case 'add_question':
            $quizId = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
            $questionText = sanitizeInput($_POST['question_text'] ?? '', 'string');
            $questionType = sanitizeInput($_POST['question_type'] ?? 'mcq', 'string');
            $marks = filter_input(INPUT_POST, 'marks', FILTER_VALIDATE_FLOAT) ?: 1;
            $options = $_POST['options'] ?? [];
            $correctOption = filter_input(INPUT_POST, 'correct_option', FILTER_VALIDATE_INT) ?: 0;

            if (!$quizId || empty($questionText)) {
                jsonResponse(['success' => false, 'message' => 'Quiz ID and question text are required.'], 400);
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
            jsonResponse(['success' => true, 'message' => 'Question added successfully.']);
            break;

                case 'update':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid quiz ID.'], 400);

            // Verify ownership
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
                if ($val !== false) { $fields[] = "duration_minutes = ?"; $params[] = $val; }
            }
            if (isset($_POST['total_marks'])) { 
                $val = filter_input(INPUT_POST, 'total_marks', FILTER_VALIDATE_FLOAT);
                if ($val !== false) { $fields[] = "total_marks = ?"; $params[] = $val; }
            }
            if (isset($_POST['passing_marks'])) { 
                $val = filter_input(INPUT_POST, 'passing_marks', FILTER_VALIDATE_FLOAT);
                if ($val !== false) { $fields[] = "passing_marks = ?"; $params[] = $val; }
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
            if ($user['role_name'] !== 'doctor' && $user['role_name'] !== 'admin') {
                jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $isPublished = filter_input(INPUT_POST, 'is_published', FILTER_VALIDATE_INT);

            if (!$id || $isPublished === null) {
                jsonResponse(['success' => false, 'message' => 'Quiz ID and status are required.'], 400);
            }

            // Verify ownership
            $existing = $db->query("SELECT created_by, title, is_published FROM quizzes WHERE id = ?", [$id])->fetch();
            if (!$existing || ($existing['created_by'] != $user['id'] && $user['role_name'] !== 'admin')) {
                jsonResponse(['success' => false, 'message' => 'Permission denied.'], 403);
            }

            $db->query("UPDATE quizzes SET is_published = ? WHERE id = ?", [$isPublished ? 1 : 0, $id]);
            
            $statusText = $isPublished ? 'published' : 'set to draft';
            logActivity('quiz_publish_toggled', 'quiz', $id, "Quiz $id $statusText");

            // Notify students if newly published
            if ($isPublished && !$existing['is_published']) {
                addNotification(null, 'student', 'quiz', '📝 Quiz Published', "Quiz \"{$existing['title']}\" is now published and ready to take.");
            }

            jsonResponse(['success' => true, 'message' => 'Quiz ' . $statusText . ' successfully.']);
            break;

        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid quiz ID.'], 400);

            $db->query("DELETE FROM quizzes WHERE id = ?", [$id]);
            logActivity('quiz_deleted', 'quiz', $id, "Deleted quiz ID: $id");
            jsonResponse(['success' => true, 'message' => 'Quiz deleted.']);
            break;

        case 'submit_attempt':
            $quizId = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
            $answers = $_POST['answers'] ?? [];
            $timeRemaining = filter_input(INPUT_POST, 'time_remaining', FILTER_VALIDATE_INT);

            if (!$quizId) jsonResponse(['success' => false, 'message' => 'Quiz ID required.'], 400);

            $attempt = $db->query("SELECT id FROM quiz_attempts WHERE quiz_id = ? AND student_id = ? AND status = 'in_progress'", [$quizId, $user['id']])->fetch();

            if ($attempt) {
                $attemptId = $attempt['id'];
            } else {
                $db->query(
                    "INSERT INTO quiz_attempts (quiz_id, student_id, total_marks, ip_address) VALUES (?, ?, (SELECT total_marks FROM quizzes WHERE id = ?), ?)",
                    [$quizId, $user['id'], $quizId, $_SERVER['REMOTE_ADDR'] ?? '']
                );
                $attemptId = $db->lastInsertId();
            }

            $totalScore = 0;
            foreach ($answers as $questionId => $selectedAnswer) {
                $question = $db->query("SELECT correct_answer, marks FROM questions WHERE id = ?", [$questionId])->fetch();
                if ($question) {
                    $isCorrect = strtolower(trim($selectedAnswer)) === strtolower(trim($question['correct_answer']));
                    $marksObtained = $isCorrect ? $question['marks'] : 0;
                    $totalScore += $marksObtained;

                    $db->query(
                        "INSERT INTO quiz_answers (attempt_id, question_id, selected_answer, is_correct, marks_obtained)
                         VALUES (?, ?, ?, ?, ?)
                         ON DUPLICATE KEY UPDATE selected_answer = ?, is_correct = ?, marks_obtained = ?",
                        [$attemptId, $questionId, $selectedAnswer, $isCorrect ? 1 : 0, $marksObtained, $selectedAnswer, $isCorrect ? 1 : 0, $marksObtained]
                    );
                }
            }

            $quiz = $db->query("SELECT title, created_by, total_marks FROM quizzes WHERE id = ?", [$quizId])->fetch();
            $percentage = $quiz['total_marks'] > 0 ? ($totalScore / $quiz['total_marks']) * 100 : 0;

            $status = $timeRemaining !== null && $timeRemaining <= 0 ? 'auto_submitted' : 'submitted';
            $db->query(
                "UPDATE quiz_attempts SET score = ?, percentage = ?, status = ?, submitted_at = NOW(), time_remaining_seconds = ? WHERE id = ?",
                [$totalScore, $percentage, $status, $timeRemaining, $attemptId]
            );

            logActivity('quiz_submitted', 'quiz_attempt', $attemptId, "Submitted quiz $quizId with score $totalScore");
            
            // 🚨 2️⃣ إشعار للدكتور: فيه طالب سلم الكويز بتاعه
            $studentName = htmlspecialchars($user['username'] ?? 'A student');
            $quizTitle = htmlspecialchars($quiz['title'] ?? 'Quiz');
            $instructorId = $quiz['created_by'] ?? null;
            
            // بنبعته لـ ID الدكتور المعين اللي عمل الكويز، والـ Target بتاعه 'doctor'
            addNotification($instructorId, 'doctor', 'quiz_submission', '🎓 Quiz Attempt Submitted', "Student ({$studentName}) submitted \"{$quizTitle}\". Score: {$totalScore}");

            jsonResponse(['success' => true, 'message' => 'Quiz submitted!', 'score' => $totalScore, 'percentage' => round($percentage, 2)]);
            break;

        case 'start_attempt':
            $quizId = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
            if (!$quizId) jsonResponse(['success' => false, 'message' => 'Quiz ID required.'], 400);

            $existing = $db->query("SELECT id FROM quiz_attempts WHERE quiz_id = ? AND student_id = ? AND status = 'in_progress'", [$quizId, $user['id']])->fetch();
            if ($existing) {
                jsonResponse(['success' => true, 'attempt_id' => $existing['id'], 'message' => 'Continuing existing attempt.']);
            }

            $db->query(
                "INSERT INTO quiz_attempts (quiz_id, student_id, total_marks, ip_address, status)
                 VALUES (?, ?, (SELECT total_marks FROM quizzes WHERE id = ?), ?, 'in_progress')",
                [$quizId, $user['id'], $quizId, $_SERVER['REMOTE_ADDR'] ?? '']
            );

            $attemptId = $db->lastInsertId();
            jsonResponse(['success' => true, 'attempt_id' => $attemptId, 'message' => 'Quiz started!']);
            break;

        default:
            jsonResponse(['success' => false, 'message' => 'Unknown action.'], 400);
    }
} catch (Exception $e) {
    error_log("Quiz API error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'An error occurred.'], 500);
}