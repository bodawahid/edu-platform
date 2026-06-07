<?php
/**
 * Quizzes API - CRUD Operations
 */

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$user = getCurrentUser();
if (!$user) {
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

function quizApiResponse(bool $success, string $message, array $extra = [], int $statusCode = 200): void {
    setFlashMessage($success ? 'success' : 'error', $message);
    jsonResponse(array_merge(['success' => $success, 'message' => $message], $extra), $statusCode);
}

function canManageQuiz(array $user): bool {
    return in_array($user['role_name'], ['admin', 'doctor'], true);
}

function assertQuizOwnership(Database $db, int $quizId, array $user): array {
    $quiz = $db->query('SELECT * FROM quizzes WHERE id = ?', [$quizId])->fetch();
    if (!$quiz) {
        quizApiResponse(false, 'Quiz not found.', [], 404);
    }

    if ($user['role_name'] === 'doctor' && (int)$quiz['created_by'] !== (int)$user['id']) {
        quizApiResponse(false, 'You can only manage your own quizzes.', [], 403);
    }

    return $quiz;
}

function assertStudentCanAccessQuiz(Database $db, int $quizId, int $studentId): array {
    $quiz = $db->query(
        "SELECT q.*
         FROM quizzes q
         JOIN course_enrollments ce ON ce.course_id = q.course_id
         WHERE q.id = ? AND ce.student_id = ? AND ce.status = 'active'",
        [$quizId, $studentId]
    )->fetch();

    if (!$quiz) {
        quizApiResponse(false, 'Quiz is not available for you.', [], 403);
    }

    if (!(int)$quiz['is_published']) {
        quizApiResponse(false, 'Quiz is still in draft mode.', [], 403);
    }

    $now = time();
    if (strtotime((string)$quiz['start_time']) > $now || strtotime((string)$quiz['end_time']) <= $now) {
        quizApiResponse(false, 'Quiz is not active at the moment.', [], 403);
    }

    return $quiz;
}

try {
    switch ($action) {
        case 'create':
            if (!canManageQuiz($user)) {
                quizApiResponse(false, 'Only doctors and admins can create quizzes.', [], 403);
            }

            $courseId = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
            $title = sanitizeInput($_POST['title'] ?? '', 'string');
            $description = sanitizeInput($_POST['description'] ?? '', 'string');
            $quizType = sanitizeInput($_POST['quiz_type'] ?? 'mixed', 'string');
            $duration = filter_input(INPUT_POST, 'duration_minutes', FILTER_VALIDATE_INT) ?: 30;
            $totalMarks = filter_input(INPUT_POST, 'total_marks', FILTER_VALIDATE_FLOAT) ?: 100;
            $passingMarks = filter_input(INPUT_POST, 'passing_marks', FILTER_VALIDATE_FLOAT) ?: 50;
            $startTime = $_POST['start_time'] ?? '';
            $endTime = $_POST['end_time'] ?? '';

            if (!$courseId || empty($title) || empty($startTime) || empty($endTime)) {
                quizApiResponse(false, 'Course, title, start time, and end time are required.', [], 400);
            }

            if (strtotime($startTime) >= strtotime($endTime)) {
                quizApiResponse(false, 'End time must be after start time.', [], 400);
            }

            $db->query(
                'INSERT INTO quizzes (course_id, title, description, quiz_type, duration_minutes, total_marks, passing_marks, start_time, end_time, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$courseId, $title, $description, $quizType, $duration, $totalMarks, $passingMarks, $startTime, $endTime, $user['id']]
            );

            $newId = $db->lastInsertId();
            logActivity('quiz_created', 'quiz', $newId, "Created quiz: $title");
            quizApiResponse(true, 'Quiz created successfully.', ['id' => $newId]);
            break;

        case 'add_question':
            if (!canManageQuiz($user)) {
                quizApiResponse(false, 'Only doctors and admins can add questions.', [], 403);
            }

            $quizId = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
            $questionText = sanitizeInput($_POST['question_text'] ?? '', 'string');
            $questionType = sanitizeInput($_POST['question_type'] ?? 'mcq', 'string');
            $marks = filter_input(INPUT_POST, 'marks', FILTER_VALIDATE_FLOAT) ?: 1;
            $options = $_POST['options'] ?? [];
            $correctOption = filter_input(INPUT_POST, 'correct_option', FILTER_VALIDATE_INT);
            $questionOrder = filter_input(INPUT_POST, 'question_order', FILTER_VALIDATE_INT);

            if (!$quizId || empty($questionText)) {
                quizApiResponse(false, 'Quiz ID and question text are required.', [], 400);
            }

            assertQuizOwnership($db, $quizId, $user);

            if ($correctOption === false || $correctOption === null) {
                $correctOption = 0;
            }

            $correctAnswer = '';
            $cleanOptions = [];

            if ($questionType === 'true_false') {
                $correctOption = $correctOption === 1 ? 1 : 0;
                $correctAnswer = $correctOption === 0 ? 'True' : 'False';
                $cleanOptions = ['True', 'False'];
            } else {
                foreach ((array)$options as $opt) {
                    $cleanOptions[] = sanitizeInput((string)$opt, 'string');
                }
                if (!isset($cleanOptions[$correctOption]) || trim((string)$cleanOptions[$correctOption]) === '') {
                    quizApiResponse(false, 'A valid correct option is required.', [], 400);
                }
                $correctAnswer = $cleanOptions[$correctOption];
            }

            if ($questionOrder === false || $questionOrder === null) {
                $lastOrder = $db->query('SELECT COALESCE(MAX(question_order), 0) as max_order FROM questions WHERE quiz_id = ?', [$quizId])->fetch();
                $questionOrder = ((int)($lastOrder['max_order'] ?? 0)) + 1;
            }

            $db->beginTransaction();
            try {
                $db->query(
                    'INSERT INTO questions (quiz_id, question_text, question_type, marks, correct_answer, question_order)
                     VALUES (?, ?, ?, ?, ?, ?)',
                    [$quizId, $questionText, $questionType, $marks, $correctAnswer, $questionOrder]
                );

                $questionId = (int)$db->lastInsertId();
                foreach ($cleanOptions as $idx => $optText) {
                    if (trim((string)$optText) === '') {
                        continue;
                    }
                    $db->query(
                        'INSERT INTO question_options (question_id, option_text, is_correct, option_order)
                         VALUES (?, ?, ?, ?)',
                        [$questionId, $optText, $idx === $correctOption ? 1 : 0, $idx]
                    );
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }

            logActivity('question_added', 'question', $questionId, "Added question to quiz $quizId");
            quizApiResponse(true, 'Question added successfully.', ['question_id' => $questionId]);
            break;

        case 'update_question':
            if (!canManageQuiz($user)) {
                quizApiResponse(false, 'Only doctors and admins can edit questions.', [], 403);
            }

            $questionId = filter_input(INPUT_POST, 'question_id', FILTER_VALIDATE_INT);
            $questionText = sanitizeInput($_POST['question_text'] ?? '', 'string');
            $questionType = sanitizeInput($_POST['question_type'] ?? 'mcq', 'string');
            $marks = filter_input(INPUT_POST, 'marks', FILTER_VALIDATE_FLOAT) ?: 1;
            $options = $_POST['options'] ?? [];
            $correctOption = filter_input(INPUT_POST, 'correct_option', FILTER_VALIDATE_INT);

            if (!$questionId || empty($questionText)) {
                quizApiResponse(false, 'Question ID and question text are required.', [], 400);
            }

            $question = $db->query('SELECT id, quiz_id FROM questions WHERE id = ?', [$questionId])->fetch();
            if (!$question) {
                quizApiResponse(false, 'Question not found.', [], 404);
            }

            $quizId = (int)$question['quiz_id'];
            assertQuizOwnership($db, $quizId, $user);

            if ($correctOption === false || $correctOption === null) {
                $correctOption = 0;
            }

            $correctAnswer = '';
            $cleanOptions = [];

            if ($questionType === 'true_false') {
                $correctOption = $correctOption === 1 ? 1 : 0;
                $correctAnswer = $correctOption === 0 ? 'True' : 'False';
                $cleanOptions = ['True', 'False'];
            } else {
                foreach ((array)$options as $opt) {
                    $cleanOptions[] = sanitizeInput((string)$opt, 'string');
                }
                if (!isset($cleanOptions[$correctOption]) || trim((string)$cleanOptions[$correctOption]) === '') {
                    quizApiResponse(false, 'A valid correct option is required.', [], 400);
                }
                $correctAnswer = $cleanOptions[$correctOption];
            }

            $db->beginTransaction();
            try {
                $db->query(
                    'UPDATE questions SET question_text = ?, question_type = ?, marks = ?, correct_answer = ? WHERE id = ?',
                    [$questionText, $questionType, $marks, $correctAnswer, $questionId]
                );

                $db->query('DELETE FROM question_options WHERE question_id = ?', [$questionId]);
                foreach ($cleanOptions as $idx => $optText) {
                    if (trim((string)$optText) === '') {
                        continue;
                    }
                    $db->query(
                        'INSERT INTO question_options (question_id, option_text, is_correct, option_order)
                         VALUES (?, ?, ?, ?)',
                        [$questionId, $optText, $idx === $correctOption ? 1 : 0, $idx]
                    );
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }

            logActivity('question_updated', 'question', $questionId, "Updated question $questionId in quiz $quizId");
            quizApiResponse(true, 'Question updated successfully.', ['question_id' => $questionId]);
            break;

        case 'publish':
            if (!canManageQuiz($user)) {
                quizApiResponse(false, 'Only doctors and admins can publish quizzes.', [], 403);
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $publishState = filter_input(INPUT_POST, 'is_published', FILTER_VALIDATE_INT);
            if (!$id) {
                quizApiResponse(false, 'Invalid quiz ID.', [], 400);
            }

            $quiz = assertQuizOwnership($db, $id, $user);
            $isPublished = $publishState === 0 ? 0 : 1;

            if ($isPublished === 1) {
                $questionCount = (int)$db->query('SELECT COUNT(*) as c FROM questions WHERE quiz_id = ?', [$id])->fetch()['c'];
                if ($questionCount === 0) {
                    quizApiResponse(false, 'Add at least one question before publishing.', [], 400);
                }
                if (strtotime((string)$quiz['start_time']) >= strtotime((string)$quiz['end_time'])) {
                    quizApiResponse(false, 'Quiz timing is invalid. Update start/end time before publishing.', [], 400);
                }
            }

            $db->query('UPDATE quizzes SET is_published = ? WHERE id = ?', [$isPublished, $id]);
            logActivity($isPublished ? 'quiz_published' : 'quiz_unpublished', 'quiz', $id, ($isPublished ? 'Published' : 'Unpublished') . " quiz ID: $id");
            quizApiResponse(true, $isPublished ? 'Quiz published successfully.' : 'Quiz moved back to draft.');
            break;

        case 'update':
            if (!canManageQuiz($user)) {
                quizApiResponse(false, 'Only doctors and admins can update quizzes.', [], 403);
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                quizApiResponse(false, 'Invalid quiz ID.', [], 400);
            }

            assertQuizOwnership($db, $id, $user);

            $fields = [];
            $params = [];

            if (isset($_POST['title']) && $_POST['title'] !== '') {
                $fields[] = 'title = ?';
                $params[] = sanitizeInput($_POST['title'], 'string');
            }
            if (isset($_POST['description'])) {
                $fields[] = 'description = ?';
                $params[] = sanitizeInput($_POST['description'], 'string');
            }
            if (isset($_POST['duration_minutes'])) {
                $fields[] = 'duration_minutes = ?';
                $params[] = filter_input(INPUT_POST, 'duration_minutes', FILTER_VALIDATE_INT);
            }
            if (isset($_POST['start_time']) && $_POST['start_time'] !== '') {
                $fields[] = 'start_time = ?';
                $params[] = $_POST['start_time'];
            }
            if (isset($_POST['end_time']) && $_POST['end_time'] !== '') {
                $fields[] = 'end_time = ?';
                $params[] = $_POST['end_time'];
            }
            if (isset($_POST['is_published'])) {
                $fields[] = 'is_published = ?';
                $params[] = filter_input(INPUT_POST, 'is_published', FILTER_VALIDATE_INT) ? 1 : 0;
            }

            if (empty($fields)) {
                quizApiResponse(false, 'No fields to update.', [], 400);
            }

            $params[] = $id;
            $db->query('UPDATE quizzes SET ' . implode(', ', $fields) . ' WHERE id = ?', $params);
            logActivity('quiz_updated', 'quiz', $id, "Updated quiz ID: $id");
            quizApiResponse(true, 'Quiz updated.');
            break;

        case 'delete':
            if (!canManageQuiz($user)) {
                quizApiResponse(false, 'Only doctors and admins can delete quizzes.', [], 403);
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                quizApiResponse(false, 'Invalid quiz ID.', [], 400);
            }

            assertQuizOwnership($db, $id, $user);
            $db->query('DELETE FROM quizzes WHERE id = ?', [$id]);
            logActivity('quiz_deleted', 'quiz', $id, "Deleted quiz ID: $id");
            quizApiResponse(true, 'Quiz deleted.');
            break;

        case 'submit_attempt':
            if ($user['role_name'] !== 'student') {
                quizApiResponse(false, 'Only students can submit quiz attempts.', [], 403);
            }

            $quizId = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
            $answers = $_POST['answers'] ?? [];
            $timeRemaining = filter_input(INPUT_POST, 'time_remaining', FILTER_VALIDATE_INT);

            if (!$quizId) {
                quizApiResponse(false, 'Quiz ID required.', [], 400);
            }

            $quiz = assertStudentCanAccessQuiz($db, $quizId, (int)$user['id']);

            $attempt = $db->query(
                "SELECT id FROM quiz_attempts WHERE quiz_id = ? AND student_id = ? AND status = 'in_progress'",
                [$quizId, $user['id']]
            )->fetch();

            if ($attempt) {
                $attemptId = (int)$attempt['id'];
            } else {
                $existingSubmitted = $db->query(
                    "SELECT id FROM quiz_attempts WHERE quiz_id = ? AND student_id = ? AND status IN ('submitted', 'graded', 'auto_submitted')",
                    [$quizId, $user['id']]
                )->fetch();
                if ($existingSubmitted) {
                    quizApiResponse(false, 'You have already submitted this quiz.', [], 409);
                }

                $db->query(
                    'INSERT INTO quiz_attempts (quiz_id, student_id, total_marks, ip_address, status) VALUES (?, ?, ?, ?, ?)',
                    [$quizId, $user['id'], $quiz['total_marks'], $_SERVER['REMOTE_ADDR'] ?? '', 'in_progress']
                );
                $attemptId = (int)$db->lastInsertId();
            }

            $totalScore = 0;
            foreach ((array)$answers as $questionId => $selectedAnswer) {
                $question = $db->query('SELECT correct_answer, marks FROM questions WHERE id = ? AND quiz_id = ?', [(int)$questionId, $quizId])->fetch();
                if (!$question) {
                    continue;
                }

                $cleanAnswer = sanitizeInput((string)$selectedAnswer, 'string');
                $isCorrect = strtolower(trim($cleanAnswer)) === strtolower(trim((string)$question['correct_answer']));
                $marksObtained = $isCorrect ? (float)$question['marks'] : 0;
                $totalScore += $marksObtained;

                $db->query(
                    'INSERT INTO quiz_answers (attempt_id, question_id, selected_answer, is_correct, marks_obtained)
                     VALUES (?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE selected_answer = ?, is_correct = ?, marks_obtained = ?',
                    [$attemptId, (int)$questionId, $cleanAnswer, $isCorrect ? 1 : 0, $marksObtained, $cleanAnswer, $isCorrect ? 1 : 0, $marksObtained]
                );
            }

            $percentage = $quiz['total_marks'] > 0 ? ($totalScore / (float)$quiz['total_marks']) * 100 : 0;
            $status = $timeRemaining !== null && $timeRemaining <= 0 ? 'auto_submitted' : 'submitted';
            $db->query(
                'UPDATE quiz_attempts SET score = ?, percentage = ?, status = ?, submitted_at = NOW(), time_remaining_seconds = ? WHERE id = ?',
                [$totalScore, $percentage, $status, $timeRemaining, $attemptId]
            );

            logActivity('quiz_submitted', 'quiz_attempt', $attemptId, "Submitted quiz $quizId with score $totalScore");
            quizApiResponse(true, 'Quiz submitted!', ['score' => $totalScore, 'percentage' => round($percentage, 2)]);
            break;

        case 'start_attempt':
            if ($user['role_name'] !== 'student') {
                quizApiResponse(false, 'Only students can start quiz attempts.', [], 403);
            }

            $quizId = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
            if (!$quizId) {
                quizApiResponse(false, 'Quiz ID required.', [], 400);
            }

            $quiz = assertStudentCanAccessQuiz($db, $quizId, (int)$user['id']);

            $existingSubmitted = $db->query(
                "SELECT id FROM quiz_attempts WHERE quiz_id = ? AND student_id = ? AND status IN ('submitted', 'graded', 'auto_submitted')",
                [$quizId, $user['id']]
            )->fetch();
            if ($existingSubmitted) {
                quizApiResponse(false, 'You already completed this quiz.', [], 409);
            }

            $existing = $db->query(
                "SELECT id FROM quiz_attempts WHERE quiz_id = ? AND student_id = ? AND status = 'in_progress'",
                [$quizId, $user['id']]
            )->fetch();
            if ($existing) {
                quizApiResponse(true, 'Continuing existing attempt.', ['attempt_id' => (int)$existing['id']]);
            }

            $db->query(
                "INSERT INTO quiz_attempts (quiz_id, student_id, total_marks, ip_address, status)
                 VALUES (?, ?, ?, ?, 'in_progress')",
                [$quizId, $user['id'], $quiz['total_marks'], $_SERVER['REMOTE_ADDR'] ?? '']
            );

            $attemptId = (int)$db->lastInsertId();
            quizApiResponse(true, 'Quiz started!', ['attempt_id' => $attemptId]);
            break;

        default:
            quizApiResponse(false, 'Unknown action.', [], 400);
    }
} catch (Exception $e) {
    error_log('Quiz API error: ' . $e->getMessage());
    quizApiResponse(false, 'An error occurred.', [], 500);
}
