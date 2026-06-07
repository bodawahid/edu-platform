<?php
/**
 * Student Dashboard
 */

require_once __DIR__ . '/../includes/functions.php';
requireRole('student');

$db = Database::getInstance();
$section = $_GET['section'] ?? 'dashboard';
$user = getCurrentUser();

// Fetch enrolled courses
$enrolledCourses = $db->query(
    "SELECT c.* FROM courses c
     JOIN course_enrollments ce ON c.id = ce.course_id
     WHERE ce.student_id = ? AND ce.status = 'active' AND c.is_active = 1
     ORDER BY c.course_code",
    [$user['id']]
)->fetchAll();

// Fetch upcoming quizzes with countdown
$upcomingQuizzes = [];
if (!empty($enrolledCourses)) {
    $courseIds = array_column($enrolledCourses, 'id');
    $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
    $upcomingQuizzes = $db->query(
        "SELECT q.*, c.course_code, c.course_name,
         (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id AND student_id = ? AND status IN ('submitted', 'graded', 'auto_submitted')) as has_attempted
         FROM quizzes q
         JOIN courses c ON q.course_id = c.id
         WHERE q.course_id IN ($placeholders) AND q.is_published = 1
         AND q.end_time > NOW()
         ORDER BY q.start_time ASC",
        array_merge([$user['id']], $courseIds)
    )->fetchAll();
}

// Fetch pending assignments
$pendingAssignments = [];
if (!empty($enrolledCourses)) {
    $courseIds = array_column($enrolledCourses, 'id');
    $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
    $pendingAssignments = $db->query(
        "SELECT a.*, c.course_code, c.course_name,
         (SELECT COUNT(*) FROM assignment_submissions WHERE assignment_id = a.id AND student_id = ?) as has_submitted
         FROM assignments a
         JOIN courses c ON a.course_id = c.id
         WHERE a.course_id IN ($placeholders) AND a.is_published = 1
         AND (a.deadline > NOW() OR a.late_submission_allowed = 1)
         ORDER BY a.deadline ASC",
        array_merge([$user['id']], $courseIds)
    )->fetchAll();
}

// Fetch available quizzes to take
$availableQuizzes = [];
if (!empty($enrolledCourses)) {
    $courseIds = array_column($enrolledCourses, 'id');
    $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
    $availableQuizzes = $db->query(
        "SELECT q.*, c.course_code, c.course_name,
         (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id AND student_id = ? AND status IN ('submitted', 'graded', 'auto_submitted')) as has_attempted
         FROM quizzes q
         JOIN courses c ON q.course_id = c.id
         WHERE q.course_id IN ($placeholders) AND q.is_published = 1
         AND q.start_time <= NOW() AND q.end_time > NOW()
         ORDER BY q.end_time ASC",
        array_merge([$user['id']], $courseIds)
    )->fetchAll();
}

// Fetch quiz results
$quizResults = $db->query(
    "SELECT qa.*, q.title as quiz_title, q.total_marks, c.course_code
     FROM quiz_attempts qa
     JOIN quizzes q ON qa.quiz_id = q.id
     JOIN courses c ON q.course_id = c.id
     WHERE qa.student_id = ? AND qa.status IN ('submitted', 'graded', 'auto_submitted')
     ORDER BY qa.submitted_at DESC",
    [$user['id']]
)->fetchAll();

// Fetch assignment grades
$assignmentGrades = $db->query(
    "SELECT asub.*, a.title as assignment_title, a.max_marks, c.course_code,
     grader.full_name as graded_by_name
     FROM assignment_submissions asub
     JOIN assignments a ON asub.assignment_id = a.id
     JOIN courses c ON a.course_id = c.id
     LEFT JOIN users grader ON asub.graded_by = grader.id
     WHERE asub.student_id = ?
     ORDER BY asub.submitted_at DESC",
    [$user['id']]
)->fetchAll();

// Stats
$stats = [
    'courses' => count($enrolledCourses),
    'upcoming_quizzes' => count($upcomingQuizzes),
    'pending_assignments' => count(array_filter($pendingAssignments, fn($a) => !$a['has_submitted'])),
    'avg_score' => $db->query("SELECT AVG(percentage) as avg FROM quiz_attempts WHERE student_id = ? AND status IN ('submitted', 'graded')", [$user['id']])->fetch()['avg'] ?? 0,
];

// Quiz detail page
$quizDetail = null;
$quizQuestions = [];
if ($section === 'take_quiz' && isset($_GET['id'])) {
    $quizId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $quizDetail = $db->query(
        "SELECT q.*, c.course_name FROM quizzes q JOIN courses c ON q.course_id = c.id WHERE q.id = ? AND q.is_published = 1",
        [$quizId]
    )->fetch();

    if ($quizDetail) {
        $quizQuestions = $db->query(
            "SELECT * FROM questions WHERE quiz_id = ? ORDER BY question_order",
            [$quizId]
        )->fetchAll();

        foreach ($quizQuestions as &$q) {
            $q['options'] = $db->query(
                "SELECT * FROM question_options WHERE question_id = ? ORDER BY option_order",
                [$q['id']]
            )->fetchAll();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Faculty of Engineering</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" onclick="toggleSidebar()">&#9776;</button>
                    <h2 class="topbar-title">Student Dashboard</h2>
                </div>
                <div class="topbar-right">
                    <span style="color: var(--gray); font-size: 0.9rem;"><?= htmlspecialchars($user['full_name']) ?></span>
                </div>
            </div>

            <div class="content-wrapper">
                <?= showFlashMessage() ?>

                <!-- Dashboard Overview -->
                <?php if ($section === 'dashboard'): ?>
                <div class="page-header">
                    <h1 class="page-title">My Dashboard</h1>
                    <p class="page-subtitle">Welcome, <?= htmlspecialchars($user['full_name']) ?></p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">&#128218;</div>
                        <div class="stat-info"><h3><?= $stats['courses'] ?></h3><p>Enrolled Courses</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">&#128221;</div>
                        <div class="stat-info"><h3><?= $stats['upcoming_quizzes'] ?></h3><p>Upcoming Quizzes</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red">&#128203;</div>
                        <div class="stat-info"><h3><?= $stats['pending_assignments'] ?></h3><p>Pending Assignments</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">&#127891;</div>
                        <div class="stat-info"><h3><?= number_format($stats['avg_score'], 1) ?>%</h3><p>Avg. Score</p></div>
                    </div>
                </div>

                <!-- To-Do List -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Upcoming Quizzes</span>
                            <a href="?section=todo" class="btn btn-sm btn-outline">View All</a>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <ul class="todo-list">
                                <?php foreach (array_slice($upcomingQuizzes, 0, 5) as $q):
                                    $deadline = getDeadlineStatus($q['end_time']);
                                ?>
                                <li class="todo-item">
                                    <div class="todo-icon" style="background: #fff3e0; color: #e65100;">&#128221;</div>
                                    <div class="todo-info">
                                        <div class="todo-title"><?= htmlspecialchars($q['title']) ?></div>
                                        <div class="todo-meta"><?= htmlspecialchars($q['course_code']) ?> &middot; <?= $q['duration_minutes'] ?> min</div>
                                    </div>
                                    <span class="deadline-badge deadline-<?= $deadline['class'] ?>"><?= $deadline['text'] ?></span>
                                </li>
                                <?php endforeach; ?>
                                <?php if (empty($upcomingQuizzes)): ?>
                                <li style="padding: 30px; text-align: center; color: var(--gray);">No upcoming quizzes!</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Pending Assignments</span>
                            <a href="?section=todo" class="btn btn-sm btn-outline">View All</a>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <ul class="todo-list">
                                <?php
                                $pendingOnly = array_filter($pendingAssignments, fn($a) => !$a['has_submitted']);
                                foreach (array_slice($pendingOnly, 0, 5) as $a):
                                    $deadline = getDeadlineStatus($a['deadline']);
                                ?>
                                <li class="todo-item">
                                    <div class="todo-icon" style="background: #fce4ec; color: #c62828;">&#128193;</div>
                                    <div class="todo-info">
                                        <div class="todo-title"><?= htmlspecialchars($a['title']) ?></div>
                                        <div class="todo-meta"><?= htmlspecialchars($a['course_code']) ?></div>
                                    </div>
                                    <span class="deadline-badge deadline-<?= $deadline['class'] ?>"><?= $deadline['text'] ?></span>
                                </li>
                                <?php endforeach; ?>
                                <?php if (empty($pendingOnly)): ?>
                                <li style="padding: 30px; text-align: center; color: var(--gray);">All assignments submitted!</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Recent Grades Preview -->
                <div class="card" style="margin-top: 20px;">
                    <div class="card-header">
                        <span class="card-title">Recent Grades</span>
                        <a href="?section=grades" class="btn btn-sm btn-outline">View All</a>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Assessment</th><th>Course</th><th>Score</th><th>Grade</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($quizResults, 0, 5) as $r):
                                        $gradeClass = ($r['percentage'] ?? 0) >= 85 ? 'grade-excellent' : (($r['percentage'] ?? 0) >= 70 ? 'grade-good' : (($r['percentage'] ?? 0) >= 60 ? 'grade-average' : 'grade-poor'));
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['quiz_title']) ?></td>
                                        <td><?= htmlspecialchars($r['course_code']) ?></td>
                                        <td><?= $r['score'] !== null ? $r['score'] . '/' . $r['total_marks'] : 'N/A' ?></td>
                                        <td class="<?= $gradeClass ?>"><?= $r['percentage'] !== null ? number_format($r['percentage'], 1) . '%' : 'N/A' ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($quizResults)): ?>
                                    <tr><td colspan="4" style="text-align:center;color:var(--gray);padding:20px;">No quiz results yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Courses -->
                <?php if ($section === 'courses'): ?>
                <div class="page-header">
                    <h1 class="page-title">My Courses</h1>
                    <p class="page-subtitle">Current semester enrolled courses</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach ($enrolledCourses as $c):
                        $doctor = $db->query(
                            "SELECT u.full_name FROM course_doctors cd JOIN users u ON cd.doctor_id = u.id WHERE cd.course_id = ?",
                            [$c['id']]
                        )->fetch();
                    ?>
                    <div class="card">
                        <div style="background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; padding: 24px;">
                            <div style="font-size: 0.8rem; opacity: 0.8;"><?= htmlspecialchars($c['course_code']) ?></div>
                            <div style="font-size: 1.2rem; font-weight: 600; margin-top: 6px;"><?= htmlspecialchars($c['course_name']) ?></div>
                        </div>
                        <div style="padding: 20px;">
                            <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 12px;"><?= htmlspecialchars(substr($c['description'] ?? '', 0, 100)) ?>...</p>
                            <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                                <span style="color: var(--gray);">Dr. <?= htmlspecialchars($doctor['full_name'] ?? 'N/A') ?></span>
                                <span class="badge badge-primary"><?= $c['credit_hours'] ?> Credits</span>
                            </div>
                            <div style="margin-top: 12px; display: flex; gap: 8px;">
                                <span class="badge badge-secondary"><?= htmlspecialchars($c['semester']) ?> Sem</span>
                                <span class="badge badge-secondary">Year <?= $c['year'] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- To-Do List -->
                <?php if ($section === 'todo'): ?>
                <div class="page-header">
                    <h1 class="page-title">Academic To-Do List</h1>
                    <p class="page-subtitle">Upcoming quizzes and pending assignments</p>
                </div>

                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header"><span class="card-title">Upcoming Quizzes</span></div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Quiz</th><th>Course</th><th>Duration</th><th>Available Until</th><th>Status</th><th>Action</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingQuizzes as $q):
                                        $deadline = getDeadlineStatus($q['end_time']);
                                    ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($q['title']) ?></strong></td>
                                        <td><?= htmlspecialchars($q['course_code']) ?></td>
                                        <td><?= $q['duration_minutes'] ?> min</td>
                                        <td><span class="deadline-badge deadline-<?= $deadline['class'] ?>"><?= $deadline['text'] ?></span></td>
                                        <td>
                                            <?php if ($q['has_attempted']): ?>
                                            <span class="badge badge-success">Completed</span>
                                            <?php else: ?>
                                            <span class="badge badge-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!$q['has_attempted'] && strtotime($q['start_time']) <= time()): ?>
                                            <a href="?section=take_quiz&id=<?= $q['id'] ?>" class="btn btn-sm btn-primary">Take Quiz</a>
                                            <?php elseif ($q['has_attempted']): ?>
                                            <span style="color: var(--gray); font-size: 0.85rem;">Taken</span>
                                            <?php else: ?>
                                            <span style="color: var(--gray); font-size: 0.85rem;">Not started</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($upcomingQuizzes)): ?>
                                    <tr><td colspan="6" style="text-align:center;color:var(--gray);padding:30px;">No upcoming quizzes!</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><span class="card-title">Assignments</span></div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Assignment</th><th>Course</th><th>Deadline</th><th>Status</th><th>Action</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingAssignments as $a):
                                        $deadline = getDeadlineStatus($a['deadline']);
                                    ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($a['title']) ?></strong></td>
                                        <td><?= htmlspecialchars($a['course_code']) ?></td>
                                        <td><span class="deadline-badge deadline-<?= $deadline['class'] ?>"><?= $deadline['text'] ?></span></td>
                                        <td><?= $a['has_submitted'] ? '<span class="badge badge-success">Submitted</span>' : '<span class="badge badge-warning">Pending</span>' ?></td>
                                        <td>
                                            <?php if (!$a['has_submitted']): ?>
                                            <a href="?section=assignments&id=<?= $a['id'] ?>" class="btn btn-sm btn-primary">Submit</a>
                                            <?php else: ?>
                                            <span style="color: var(--gray); font-size: 0.85rem;">Done</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($pendingAssignments)): ?>
                                    <tr><td colspan="5" style="text-align:center;color:var(--gray);padding:30px;">No pending assignments!</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Take Quiz -->
                <?php if ($section === 'take_quiz' && $quizDetail): ?>
                <?php
                // Check if already attempted
                $existingAttempt = $db->query(
                    "SELECT id, status FROM quiz_attempts WHERE quiz_id = ? AND student_id = ? AND status = 'in_progress'",
                    [$quizDetail['id'], $user['id']]
                )->fetch();

                $hasCompleted = $db->query(
                    "SELECT COUNT(*) as c FROM quiz_attempts WHERE quiz_id = ? AND student_id = ? AND status IN ('submitted', 'graded', 'auto_submitted')",
                    [$quizDetail['id'], $user['id']]
                )->fetch()['c'];
                ?>

                <?php if ($hasCompleted > 0): ?>
                <div class="page-header">
                    <h1 class="page-title">Quiz Already Completed</h1>
                    <p class="page-subtitle">You have already taken this quiz.</p>
                    <a href="?section=todo" class="btn btn-outline btn-sm" style="margin-top: 12px;">&#8592; Back to To-Do</a>
                </div>
                <div class="card">
                    <div class="card-body" style="text-align: center; padding: 60px;">
                        <div style="font-size: 4rem; margin-bottom: 16px;">&#9989;</div>
                        <h3 style="color: var(--success); margin-bottom: 8px;">Quiz Already Submitted</h3>
                        <p style="color: var(--gray);">You have already completed this quiz. Check your grades for results.</p>
                        <a href="?section=grades" class="btn btn-primary" style="margin-top: 16px;">View My Grades</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="quiz-container" id="quizInterface">
                    <div class="quiz-header">
                        <div class="quiz-title"><?= htmlspecialchars($quizDetail['title']) ?></div>
                        <div style="font-size: 0.9rem; opacity: 0.8; margin-bottom: 12px;"><?= htmlspecialchars($quizDetail['course_name']) ?></div>
                        <div class="quiz-timer" id="quizTimer">--:--</div>
                    </div>
                    <div class="quiz-body">
                        <form id="quizForm">
                            <?= csrfField() ?>
                            <input type="hidden" name="quiz_id" value="<?= $quizDetail['id'] ?>">

                            <?php foreach ($quizQuestions as $idx => $q): ?>
                            <div class="question-card" data-question="<?= $q['id'] ?>">
                                <div class="question-number">Question <?= $idx + 1 ?> of <?= count($quizQuestions) ?> &middot; <?= $q['marks'] ?> mark<?= $q['marks'] > 1 ? 's' : '' ?></div>
                                <div class="question-text"><?= htmlspecialchars($q['question_text']) ?></div>
                                <div class="options-list">
                                    <?php foreach ($q['options'] as $opt): ?>
                                    <label class="option-item" onclick="selectOption(this)">
                                        <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= htmlspecialchars($opt['option_text']) ?>" required>
                                        <span><?= htmlspecialchars($opt['option_text']) ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <?php if (empty($quizQuestions)): ?>
                            <p style="color: var(--gray); text-align: center; padding: 40px;">No questions available for this quiz.</p>
                            <?php else: ?>
                            <div style="display: flex; gap: 12px; justify-content: center; margin-top: 24px;">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitQuizBtn">Submit Quiz</button>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <script>
                (function() {
                    const durationMinutes = <?= $quizDetail['duration_minutes'] ?>;
                    const timerDisplay = document.getElementById('quizTimer');
                    const quizTimer = new QuizTimer(durationMinutes, timerDisplay, function() {
                        // Auto-submit when time expires
                        submitQuizAnswers(true);
                    });

                    // Start timer immediately
                    quizTimer.start();

                    // Also start attempt on server
                    fetch('/api/quizzes.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'action=start_attempt&quiz_id=<?= $quizDetail['id'] ?>&csrf_token=' + document.querySelector('input[name="csrf_token"]').value
                    });

                    // Handle form submission
                    document.getElementById('quizForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        if (confirm('Are you sure you want to submit? You cannot change your answers after submission.')) {
                            submitQuizAnswers(false);
                        }
                    });

                    async function submitQuizAnswers(autoSubmitted) {
                        quizTimer.stop();
                        const btn = document.getElementById('submitQuizBtn');
                        if (btn) {
                            btn.disabled = true;
                            btn.innerHTML = '<div class="spinner"></div> Submitting...';
                        }

                        const formData = new FormData(document.getElementById('quizForm'));
                        formData.append('action', 'submit_attempt');
                        formData.append('time_remaining', quizTimer.getRemainingSeconds());

                        try {
                            const res = await fetch('/api/quizzes.php', { method: 'POST', body: formData });
                            const data = await res.json();

                            if (data.success) {
                                document.getElementById('quizInterface').innerHTML = `
                                    <div style="text-align: center; padding: 60px 20px;">
                                        <div style="font-size: 4rem; margin-bottom: 16px;">&#127881;</div>
                                        <h2 style="color: var(--success); margin-bottom: 8px;">Quiz Submitted!</h2>
                                        <p style="color: var(--gray); font-size: 1.1rem;">
                                            ${autoSubmitted ? 'Time ran out! Your quiz was auto-submitted.' : 'Your answers have been recorded.'}
                                        </p>
                                        <div style="background: #f8f9fa; border-radius: 12px; padding: 24px; margin: 24px auto; max-width: 300px;">
                                            <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary);">${data.score}</div>
                                            <div style="color: var(--gray);">Score: ${data.percentage}%</div>
                                        </div>
                                        <a href="?section=grades" class="btn btn-primary">View My Grades</a>
                                    </div>
                                `;
                            } else {
                                showNotification(data.message, 'error');
                                if (btn) { btn.disabled = false; btn.innerHTML = 'Submit Quiz'; }
                            }
                        } catch (e) {
                            showNotification('Error submitting quiz', 'error');
                            if (btn) { btn.disabled = false; btn.innerHTML = 'Submit Quiz'; }
                        }
                    }
                })();

                function selectOption(el) {
                    // Remove selected from siblings
                    el.parentElement.querySelectorAll('.option-item').forEach(opt => opt.classList.remove('selected'));
                    el.classList.add('selected');
                }
                </script>
                <?php endif; ?>
                <?php endif; ?>

                <!-- Quizzes List -->
                <?php if ($section === 'quizzes'): ?>
                <div class="page-header">
                    <h1 class="page-title">Available Quizzes</h1>
                    <p class="page-subtitle">Quizzes you can take now</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                    <?php foreach ($availableQuizzes as $q):
                        $deadline = getDeadlineStatus($q['end_time']);
                    ?>
                    <div class="card">
                        <div style="padding: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <span class="badge badge-primary"><?= htmlspecialchars($q['course_code']) ?></span>
                                <?php if ($q['has_attempted']): ?>
                                <span class="badge badge-success">Completed</span>
                                <?php endif; ?>
                            </div>
                            <h3 style="font-size: 1.1rem; margin-bottom: 8px;"><?= htmlspecialchars($q['title']) ?></h3>
                            <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 12px;"><?= $q['duration_minutes'] ?> min &middot; <?= $q['total_marks'] ?> marks</p>
                            <span class="deadline-badge deadline-<?= $deadline['class'] ?>"><?= $deadline['text'] ?></span>
                            <div style="margin-top: 16px;">
                                <?php if (!$q['has_attempted']): ?>
                                <a href="?section=take_quiz&id=<?= $q['id'] ?>" class="btn btn-primary" style="width: 100%;">Take Quiz</a>
                                <?php else: ?>
                                <button class="btn btn-outline" style="width: 100%;" disabled>Already Taken</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($availableQuizzes)): ?>
                    <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 60px;">
                        <div style="font-size: 3rem; margin-bottom: 16px;">&#128218;</div>
                        <h3 style="color: var(--gray);">No quizzes available right now</h3>
                        <p style="color: var(--gray);">Check back later for new quizzes.</p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Assignments -->
                <?php if ($section === 'assignments'): ?>
                <?php
                // Specific assignment submission page
                if (isset($_GET['id'])):
                    $assignmentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                    $assignment = $db->query(
                        "SELECT a.*, c.course_code, c.course_name FROM assignments a
                         JOIN courses c ON a.course_id = c.id WHERE a.id = ? AND a.is_published = 1",
                        [$assignmentId]
                    )->fetch();

                    // Check if already submitted
                    $existingSub = $db->query(
                        "SELECT * FROM assignment_submissions WHERE assignment_id = ? AND student_id = ?",
                        [$assignmentId, $user['id']]
                    )->fetch();
                ?>
                <div class="page-header">
                    <h1 class="page-title">Submit Assignment</h1>
                    <p class="page-subtitle"><?= htmlspecialchars($assignment['title'] ?? '') ?></p>
                    <a href="?section=todo" class="btn btn-outline btn-sm" style="margin-top: 12px;">&#8592; Back</a>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                    <div class="card">
                        <div class="card-header"><span class="card-title">Assignment Details</span></div>
                        <div class="card-body">
                            <p><strong>Course:</strong> <?= htmlspecialchars($assignment['course_code'] ?? '') ?></p>
                            <p><strong>Deadline:</strong> <?= formatDate($assignment['deadline'] ?? '') ?></p>
                            <p><strong>Max Marks:</strong> <?= $assignment['max_marks'] ?? '' ?></p>
                            <p><strong>File Types:</strong> <?= htmlspecialchars($assignment['allowed_file_types'] ?? '') ?></p>
                            <p><strong>Max Size:</strong> <?= $assignment['max_file_size_mb'] ?? '' ?> MB</p>
                            <?php if ($existingSub): ?>
                            <div class="alert alert-info" style="margin-top: 12px;">
                                <span class="alert-icon">ℹ</span> You submitted this on <?= formatDate($existingSub['submitted_at']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><span class="card-title"><?= $existingSub ? 'Resubmit' : 'Submit' ?> Assignment</span></div>
                        <div class="card-body">
                            <form id="submitAssignmentForm" enctype="multipart/form-data">
                                <?= csrfField() ?>
                                <input type="hidden" name="assignment_id" value="<?= $assignmentId ?>">

                                <?php if (!empty($assignment['instructions'])): ?>
                                <div class="alert alert-info" style="margin-bottom: 16px;">
                                    <strong>Instructions:</strong> <?= nl2br(htmlspecialchars($assignment['instructions'])) ?>
                                </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <label class="form-label">Submission Text (optional)</label>
                                    <textarea name="submission_text" class="form-control" rows="6" placeholder="Enter your answer or notes here..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Attach File</label>
                                    <div class="upload-zone" onclick="this.querySelector('input').click()">
                                        <div class="upload-zone-icon">&#128194;</div>
                                        <div class="upload-zone-text">Click to upload or drag and drop</div>
                                        <div class="upload-zone-hint">Allowed: <?= htmlspecialchars($assignment['allowed_file_types'] ?? '') ?> (Max <?= $assignment['max_file_size_mb'] ?? 10 ?>MB)</div>
                                        <input type="file" name="submission_file" style="display: none;" accept=".pdf,.zip,.doc,.docx">
                                    </div>
                                    <div class="file-list" style="margin-top: 10px;"></div>
                                </div>

                                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                                    <?= $existingSub ? 'Resubmit Assignment' : 'Submit Assignment' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                initFileUpload();

                document.getElementById('submitAssignmentForm').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const btn = this.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    btn.innerHTML = '<div class="spinner"></div> Uploading...';

                    const formData = new FormData(this);
                    formData.append('action', 'submit');

                    try {
                        const res = await fetch('/api/assignments.php', { method: 'POST', body: formData });
                        const data = await res.json();
                        if (data.success) {
                            showNotification('Assignment submitted!', 'success');
                            setTimeout(() => window.location.href = '?section=todo', 1000);
                        } else {
                            showNotification(data.message, 'error');
                            btn.disabled = false;
                            btn.innerHTML = 'Submit Assignment';
                        }
                    } catch (err) {
                        showNotification('Error submitting assignment', 'error');
                        btn.disabled = false;
                        btn.innerHTML = 'Submit Assignment';
                    }
                });
                </script>
                <?php endif; ?>
                <?php endif; ?>

                <!-- Grades Portal -->
                <?php if ($section === 'grades'): ?>
                <div class="page-header">
                    <h1 class="page-title">My Grades</h1>
                    <p class="page-subtitle">View all your quiz scores and assignment grades</p>
                </div>

                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header"><span class="card-title">Quiz Results</span></div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Quiz</th><th>Course</th><th>Score</th><th>Percentage</th><th>Grade</th><th>Submitted</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($quizResults as $r):
                                        $pct = $r['percentage'] ?? 0;
                                        $gradeClass = $pct >= 85 ? 'grade-excellent' : ($pct >= 70 ? 'grade-good' : ($pct >= 60 ? 'grade-average' : 'grade-poor'));
                                        $grade = $pct >= 85 ? 'A' : ($pct >= 70 ? 'B' : ($pct >= 60 ? 'C' : ($pct >= 50 ? 'D' : 'F')));
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['quiz_title']) ?></td>
                                        <td><?= htmlspecialchars($r['course_code']) ?></td>
                                        <td><?= $r['score'] !== null ? $r['score'] . '/' . $r['total_marks'] : 'N/A' ?></td>
                                        <td class="<?= $gradeClass ?>"><?= $r['percentage'] !== null ? number_format($r['percentage'], 1) . '%' : 'N/A' ?></td>
                                        <td><span class="badge badge-<?= $pct >= 60 ? 'success' : 'danger' ?>"><?= $grade ?></span></td>
                                        <td><?= timeAgo($r['submitted_at'] ?? $r['started_at']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($quizResults)): ?>
                                    <tr><td colspan="6" style="text-align:center;color:var(--gray);padding:30px;">No quiz results yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><span class="card-title">Assignment Grades</span></div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Assignment</th><th>Course</th><th>Submitted</th><th>Status</th><th>Marks</th><th>Feedback</th><th>Graded By</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignmentGrades as $g): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($g['assignment_title']) ?></td>
                                        <td><?= htmlspecialchars($g['course_code']) ?></td>
                                        <td><?= timeAgo($g['submitted_at']) ?> <?= $g['is_late'] ? '<span class="badge badge-warning">Late</span>' : '' ?></td>
                                        <td><span class="badge badge-<?= $g['status'] === 'graded' ? 'success' : 'warning' ?>"><?= ucfirst($g['status']) ?></span></td>
                                        <td><?= $g['marks_obtained'] !== null ? $g['marks_obtained'] . '/' . $g['max_marks'] : '-' ?></td>
                                        <td><?= $g['feedback'] ? htmlspecialchars(substr($g['feedback'], 0, 50)) . '...' : '-' ?></td>
                                        <td><?= $g['graded_by_name'] ? htmlspecialchars($g['graded_by_name']) : '-' ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($assignmentGrades)): ?>
                                    <tr><td colspan="7" style="text-align:center;color:var(--gray);padding:30px;">No assignment submissions yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js"></script>
</body>
</html>
