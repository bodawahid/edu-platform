<?php
/**
 * Quizzes API - CRUD Operations
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

            // Determine correct answer
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

            // Add options for MCQ
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
                // Add True/False options
                $db->query("INSERT INTO question_options (question_id, option_text, is_correct, option_order) VALUES (?, 'True', ?, 0)", [$questionId, $correctOption == 0 ? 1 : 0]);
                $db->query("INSERT INTO question_options (question_id, option_text, is_correct, option_order) VALUES (?, 'False', ?, 1)", [$questionId, $correctOption == 1 ? 1 : 0]);
            }

            logActivity('question_added', 'question', $questionId, "Added question to quiz $quizId");
            jsonResponse(['success' => true, 'message' => 'Question added successfully.']);
            break;

        case 'update':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid quiz ID.'], 400);

            $fields = [];
            $params = [];

            if (!empty($_POST['title'])) { $fields[] = "title = ?"; $params[] = sanitizeInput($_POST['title'], 'string'); }
            if (!empty($_POST['description'])) { $fields[] = "description = ?"; $params[] = sanitizeInput($_POST['description'], 'string'); }
            if (isset($_POST['duration_minutes'])) { $fields[] = "duration_minutes = ?"; $params[] = filter_input(INPUT_POST, 'duration_minutes', FILTER_VALIDATE_INT); }
            if (isset($_POST['is_published'])) { $fields[] = "is_published = ?"; $params[] = filter_input(INPUT_POST, 'is_published', FILTER_VALIDATE_INT); }

            if (empty($fields)) jsonResponse(['success' => false, 'message' => 'No fields to update.'], 400);

            $params[] = $id;
            $db->query("UPDATE quizzes SET " . implode(', ', $fields) . " WHERE id = ?", $params);
            logActivity('quiz_updated', 'quiz', $id, "Updated quiz ID: $id");
            jsonResponse(['success' => true, 'message' => 'Quiz updated.']);
            break;

        case 'delete':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid quiz ID.'], 400);

            $db->query("DELETE FROM quizzes WHERE id = ?", [$id]);
            logActivity('quiz_deleted', 'quiz', $id, "Deleted quiz ID: $id");
            jsonResponse(['success' => true, 'message' => 'Quiz deleted.']);
            break;

        case 'submit_attempt':
            // Student submitting quiz answers
            $quizId = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
            $answers = $_POST['answers'] ?? [];
            $timeRemaining = filter_input(INPUT_POST, 'time_remaining', FILTER_VALIDATE_INT);

            if (!$quizId) jsonResponse(['success' => false, 'message' => 'Quiz ID required.'], 400);

            // Create or update attempt
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

            // Save answers
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

            // Calculate percentage
            $quiz = $db->query("SELECT total_marks FROM quizzes WHERE id = ?", [$quizId])->fetch();
            $percentage = $quiz['total_marks'] > 0 ? ($totalScore / $quiz['total_marks']) * 100 : 0;

            // Update attempt
            $status = $timeRemaining !== null && $timeRemaining <= 0 ? 'auto_submitted' : 'submitted';
            $db->query(
                "UPDATE quiz_attempts SET score = ?, percentage = ?, status = ?, submitted_at = NOW(), time_remaining_seconds = ? WHERE id = ?",
                [$totalScore, $percentage, $status, $timeRemaining, $attemptId]
            );

            logActivity('quiz_submitted', 'quiz_attempt', $attemptId, "Submitted quiz $quizId with score $totalScore");
            jsonResponse(['success' => true, 'message' => 'Quiz submitted!', 'score' => $totalScore, 'percentage' => round($percentage, 2)]);
            break;

        case 'start_attempt':
            $quizId = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
            if (!$quizId) jsonResponse(['success' => false, 'message' => 'Quiz ID required.'], 400);

            // Check if already has in-progress attempt
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
