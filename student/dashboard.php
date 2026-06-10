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

// Fetch assignment files (doctor's uploaded instructions)
$assignmentFiles = [];

// Course-wise gradebook data
$courseGradebook = [];

// Stats

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
                <div class="topbar-right" style="display: flex; align-items: center; gap: 15px;">
                    <span style="color: var(--gray); font-size: 0.9rem;"><?= htmlspecialchars($user['full_name']) ?></span>
                    <div class="notification-wrapper" style="position: relative; display: inline-block;">
                        <button class="topbar-icon-btn" id="notificationBtn">
                            🔔 <span class="notification-badge" id="notificationCount" style="display:none;">0</span>
                        </button>
                        <div id="notificationDropdown" style="display: none; position: absolute; right: 0; top: 40px; width: 300px; background: white; border: 1px solid #e9ecef; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; color: #333; text-align: left;">
                            <div style="padding: 12px; font-weight: bold; border-bottom: 1px solid #e9ecef; font-size: 0.9rem; background: #f8f9fa; border-radius: 8px 8px 0 0;">Notifications</div>
                            <div id="notificationList" style="max-height: 280px; overflow-y: auto; font-size: 0.85rem;">
                                <div style="padding: 15px; text-align: center; color: #888;">No new notifications</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <?= showFlashMessage() ?>

                <?php if ($section === 'notifications'): ?>
                    <div class="page-header">
                        <h1 class="page-title">Notification Archive</h1>
                        <p class="page-subtitle">All your academic and security alerts in one place</p>
                        <a href="?section=dashboard" class="btn btn-sm btn-outline" style="margin-top: 12px;">&#8592; Back to Dashboard</a>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">All Notifications History</span>
                            <?php 
                            $allNotifs = $db->query(
                                "SELECT * FROM notifications 
                                 WHERE user_id = ? OR role_target = ? 
                                 ORDER BY created_at DESC LIMIT 100",
                                [$user['id'], $user['role_name']]
                            )->fetchAll();
                            
                            $db->query("UPDATE notifications SET is_read = 1 WHERE user_id = ? OR role_target = ?", [$user['id'], $user['role_name']]);
                            ?>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Type</th>
                                            <th>Title</th>
                                            <th>Message</th>
                                            <th>Date & Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allNotifs as $n): 
                                            $typeBadge = $n['type'] === 'security' ? 'badge-danger' : 'badge-primary';
                                            $statusText = $n['is_read'] == 0 ? '<span style="color:#2563eb;">● New</span>' : '<span style="color:#94a3b8;">○ Read</span>';
                                        ?>
                                            <tr>
                                                <td><strong><?= $statusText ?></strong></td>
                                                <td><span class="badge <?= $typeBadge ?>"><?= htmlspecialchars(ucfirst($n['type'])) ?></span></td>
                                                <td><strong><?= htmlspecialchars($n['title']) ?></strong></td>
                                                <td><?= htmlspecialchars($n['message']) ?></td>
                                                <td><?= htmlspecialchars($n['created_at']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($allNotifs)): ?>
                                            <tr><td colspan="5" style="text-align:center; color:var(--gray); padding:40px;">Your notification archive is empty.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php 
                    echo "</div></div></div><script src='/assets/js/main.js'></script></body></html>";
                    exit; 
                endif; 
                ?>

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

                <?php if ($section === 'todo'): ?>
                <div class="page-header">
                    <h1 class="page-title">Academic To-Do List</h1>
                    <p class="page-subtitle">Upcoming quizzes and pending assignments</p>
                </div>

                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header"><span class="card-title">&#128221; Upcoming Quizzes</span></div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Quiz</th><th>Course</th><th>Duration</th><th>Available Until</th><th>Status</th><th>Action</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingQuizzes as $q):
                                        $deadline = getDeadlineStatus($q['end_time']);
                                        $hoursLeft = max(0, round((strtotime($q['end_time']) - time()) / 3600, 1));
                                        $isUrgent = $hoursLeft <= 24 && $hoursLeft > 0;
                                    ?>
                                    <tr style="<?= $isUrgent ? 'background: #fff3e0;' : '' ?>">
                                        <td>
                                            <strong><?= htmlspecialchars($q['title']) ?></strong>
                                            <?php if ($isUrgent): ?>
                                            <span class="badge badge-danger" style="margin-left: 6px;">&#9888; <?= round($hoursLeft) ?>h left</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($q['course_code']) ?></td>
                                        <td><?= $q['duration_minutes'] ?> min</td>
                                        <td><span class="deadline-badge deadline-<?= $deadline['class'] ?>"><?= $deadline['text'] ?></span></td>
                                        <td>
                                            <?php if ($q['has_attempted']): ?>
                                            <span class="badge badge-success">&#10003; Completed</span>
                                            <?php else: ?>
                                            <span class="badge badge-warning">&#9203; Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!$q['has_attempted'] && strtotime($q['start_time']) <= time()): ?>
                                            <a href="?section=take_quiz&id=<?= $q['id'] ?>" class="btn btn-sm btn-primary <?= $isUrgent ? 'btn-danger' : '' ?>">Take Quiz</a>
                                            <?php elseif ($q['has_attempted']): ?>
                                            <span style="color: var(--gray); font-size: 0.85rem;">&#10003; Taken</span>
                                            <?php else: ?>
                                            <span style="color: var(--gray); font-size: 0.85rem;">&#9200; Not started</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($upcomingQuizzes)): ?>
                                    <tr><td colspan="6" style="text-align:center;color:var(--gray);padding:30px;">&#127881; No upcoming quizzes! Enjoy your free time.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><span class="card-title">&#128193; Assignments</span></div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Assignment</th><th>Course</th><th>Deadline</th><th>Time Left</th><th>Status</th><th>Action</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingAssignments as $a):
                                        $deadline = getDeadlineStatus($a['deadline']);
                                        $hoursLeft = max(0, round((strtotime($a['deadline']) - time()) / 3600, 1));
                                        $isUrgent = $hoursLeft <= 24 && $hoursLeft > 0;
                                        $isExpired = $hoursLeft <= 0 && !($a['late_submission_allowed'] ?? 0);
                                    ?>
                                    <tr style="<?= $isUrgent ? 'background: #fff3e0;' : ($isExpired ? 'background: #ffebee;' : '') ?>">
                                        <td>
                                            <strong><?= htmlspecialchars($a['title']) ?></strong>
                                            <?php if ($isUrgent && !$a['has_submitted']): ?>
                                            <span class="badge badge-danger" style="margin-left: 6px;">&#9888; Due Soon!</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($a['course_code']) ?></td>
                                        <td><span class="deadline-badge deadline-<?= $deadline['class'] ?>"><?= $deadline['text'] ?></span></td>
                                        <td>
                                            <?php if ($isExpired): ?>
                                                <span style="color: var(--danger); font-weight: 600;">&#10060; Expired</span>
                                            <?php elseif ($hoursLeft <= 24): ?>
                                                <span style="color: var(--danger); font-weight: 600;">&#9200; <?= round($hoursLeft) ?> hours</span>
                                            <?php else: ?>
                                                <span style="color: var(--gray);">&#128197; <?= round($hoursLeft / 24, 1) ?> days</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($a['has_submitted']): ?>
                                            <span class="badge badge-success">&#10003; Submitted</span>
                                            <?php elseif ($isExpired): ?>
                                            <span class="badge badge-danger">&#128683; Missed</span>
                                            <?php else: ?>
                                            <span class="badge badge-warning">&#9203; Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!$a['has_submitted'] && !$isExpired): ?>
                                            <a href="?section=assignments&id=<?= $a['id'] ?>" class="btn btn-sm btn-primary <?= $isUrgent ? 'btn-danger' : '' ?>">Submit Now</a>
                                            <?php elseif ($a['has_submitted']): ?>
                                            <a href="?section=assignments&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline">&#128260; Resubmit</a>
                                            <?php else: ?>
                                            <span style="color: var(--gray); font-size: 0.85rem;">&#128274; Closed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($pendingAssignments)): ?>
                                    <tr><td colspan="6" style="text-align:center;color:var(--gray);padding:30px;">&#127881; All assignments submitted! Great job!</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($section === 'take_quiz' && $quizDetail): ?>
                <?php
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
                        submitQuizAnswers(true);
                    });

                    quizTimer.start();

                    fetch('/api/quizzes.php', {
                        method: 'POST',
                        body: 'action=start_attempt&quiz_id=<?= $quizDetail['id'] ?>&csrf_token=' + document.querySelector('input[name="csrf_token"]').value
                    });

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
                    el.parentElement.querySelectorAll('.option-item').forEach(opt => opt.classList.remove('selected'));
                    el.classList.add('selected');
                }
                </script>
                <?php endif; ?>
                <?php endif; ?>

                <?php if ($section === 'assignments' && isset($_GET['id'])): ?>
                <?php
                    $assignmentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                    $assignment = $db->query(
                        "SELECT a.*, c.course_code, c.course_name FROM assignments a
                         JOIN courses c ON a.course_id = c.id WHERE a.id = ? AND a.is_published = 1",
                        [$assignmentId]
                    )->fetch();

                    if (!$assignment):
                ?>
                <div class="page-header">
                    <h1 class="page-title">Assignment Not Found</h1>
                    <a href="?section=todo" class="btn btn-outline btn-sm" style="margin-top: 12px;">&#8592; Back</a>
                </div>
                <div class="card">
                    <div class="card-body" style="text-align: center; padding: 60px;">
                        <div style="font-size: 4rem; margin-bottom: 16px;">&#128683;</div>
                        <h3 style="color: var(--danger);">Assignment Not Available</h3>
                        <p style="color: var(--gray);">This assignment may not exist, is not published yet, or you don't have access.</p>
                    </div>
                </div>
                <?php else:
                    // Check deadline status
                    $deadlineTimestamp = strtotime($assignment['deadline']);
                    $now = time();
                    $diff = $deadlineTimestamp - $now;
                    $isExpired = $diff <= 0;
                    $isNearDeadline = $diff > 0 && $diff <= 86400; // Within 24 hours
                    $canSubmit = !$isExpired || ($assignment['late_submission_allowed'] ?? 0);
                    
                    $existingSub = $db->query(
                        "SELECT * FROM assignment_submissions WHERE assignment_id = ? AND student_id = ?",
                        [$assignmentId, $user['id']]
                    )->fetch();
                    
                    // Fetch doctor's uploaded files
                    $assignmentFiles = $db->query(
                        "SELECT * FROM assignment_files WHERE assignment_id = ? ORDER BY uploaded_at DESC",
                        [$assignmentId]
                    )->fetchAll();
                ?>
                <div class="page-header">
                    <h1 class="page-title">Submit Assignment</h1>
                    <p class="page-subtitle"><?= htmlspecialchars($assignment['title']) ?></p>
                    <a href="?section=todo" class="btn btn-outline btn-sm" style="margin-top: 12px;">&#8592; Back to To-Do</a>
                </div>

                <?php if ($isNearDeadline && !$isExpired): ?>
                <div class="alert alert-warning" style="margin-bottom: 20px;">
                    <span class="alert-icon">&#9888;</span>
                    <strong>Deadline Approaching!</strong> Less than 24 hours remaining. Submit now to avoid late penalties.
                </div>
                <?php endif; ?>

                <?php if ($isExpired && !($assignment['late_submission_allowed'] ?? 0)): ?>
                <div class="alert alert-error" style="margin-bottom: 20px;">
                    <span class="alert-icon">&#10060;</span>
                    <strong>Deadline Passed!</strong> This assignment is no longer accepting submissions.
                </div>
                <?php endif; ?>

                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                    <div class="card">
                        <div class="card-header"><span class="card-title">Assignment Details</span></div>
                        <div class="card-body">
                            <p><strong>Course:</strong> <?= htmlspecialchars($assignment['course_code'] ?? '') ?></p>
                            <p><strong>Deadline:</strong> 
                                <?php 
                                $deadlineStatus = getDeadlineStatus($assignment['deadline']);
                                ?>
                                <span class="deadline-badge deadline-<?= $deadlineStatus['class'] ?>"><?= $deadlineStatus['text'] ?></span>
                            </p>
                            <p><strong>Max Marks:</strong> <?= $assignment['max_marks'] ?? '' ?></p>
                            <p><strong>File Types:</strong> <?= htmlspecialchars($assignment['allowed_file_types'] ?? '') ?></p>
                            <p><strong>Max Size:</strong> <?= $assignment['max_file_size_mb'] ?? 10 ?> MB</p>
                            
                            <?php if (!empty($assignment['instructions'])): ?>
                            <div style="margin-top: 16px; padding: 12px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary);">
                                <strong style="color: var(--primary);">Instructions:</strong><br>
                                <span style="font-size: 0.9rem; color: var(--dark);"><?= nl2br(htmlspecialchars($assignment['instructions'])) ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($assignmentFiles)): ?>
                            <div style="margin-top: 16px;">
                                <strong style="color: var(--primary);">&#128196; Attached Files:</strong>
                                <div style="margin-top: 8px; display: flex; flex-direction: column; gap: 6px;">
                                    <?php foreach ($assignmentFiles as $file): ?>
                                    <a href="/<?= htmlspecialchars($file['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline" style="justify-content: flex-start; text-align: left;">
                                        &#128190; <?= htmlspecialchars($file['file_name']) ?> 
                                        <span style="color: var(--gray); font-size: 0.75rem; margin-left: auto;"><?= round($file['file_size'] / 1024, 1) ?> KB</span>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($existingSub): ?>
                            <div class="alert alert-info" style="margin-top: 16px;">
                                <span class="alert-icon">&#9432;</span> 
                                Submitted on <?= formatDate($existingSub['submitted_at']) ?>
                                <?php if ($existingSub['is_late']): ?>
                                <span class="badge badge-warning">Late</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">
                                <?php if ($existingSub): ?>
                                    &#128260; Resubmit Assignment
                                <?php else: ?>
                                    &#128228; Submit Assignment
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <?php if (!$canSubmit): ?>
                            <div style="text-align: center; padding: 40px;">
                                <div style="font-size: 3rem; margin-bottom: 12px;">&#9200;</div>
                                <h3 style="color: var(--danger);">Submission Closed</h3>
                                <p style="color: var(--gray);">The deadline has passed and late submissions are not allowed.</p>
                            </div>
                            <?php else: ?>
                            <form id="submitAssignmentForm" enctype="multipart/form-data">
                                <?= csrfField() ?>
                                <input type="hidden" name="assignment_id" value="<?= $assignmentId ?>">

                                <div class="form-group">
                                    <label class="form-label">Submission Text (optional)</label>
                                    <textarea name="submission_text" class="form-control" rows="6" placeholder="Enter your answer or notes here..."><?= $existingSub ? htmlspecialchars($existingSub['submission_text'] ?? '') : '' ?></textarea>
                                </div>

                                <?php if ($existingSub && $existingSub['file_path']): ?>
                                <div class="form-group">
                                    <label class="form-label">Current Submission File</label>
                                    <div style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #e8f5e9; border-radius: 8px; border: 1px solid #a5d6a7;">
                                        <span style="font-size: 1.5rem;">&#128196;</span>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 500;"><?= htmlspecialchars($existingSub['file_name']) ?></div>
                                            <div style="font-size: 0.8rem; color: var(--gray);">Submitted <?= timeAgo($existingSub['submitted_at']) ?></div>
                                        </div>
                                        <a href="/<?= htmlspecialchars($existingSub['file_path']) ?>" target="_blank" class="btn btn-sm btn-primary" title="Download my submission">&#128190; Download</a>
                                    </div>
                                    <p style="font-size: 0.85rem; color: var(--gray); margin-top: 6px;">Upload a new file below to replace this submission.</p>
                                </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <label class="form-label">
                                        <?= $existingSub ? 'Replace File (optional)' : 'Attach File' ?>
                                    </label>
                                    <div class="upload-zone" id="uploadZone" onclick="document.getElementById('submissionFile').click()">
                                        <div class="upload-zone-icon" id="uploadIcon">&#128194;</div>
                                        <div class="upload-zone-text" id="uploadText">Click to upload or drag and drop</div>
                                        <div class="upload-zone-hint" id="uploadHint">Allowed: <?= htmlspecialchars($assignment['allowed_file_types'] ?? '') ?> (Max <?= $assignment['max_file_size_mb'] ?? 10 ?>MB)</div>
                                        <input type="file" name="submission_file" id="submissionFile" style="display: none;" accept=".pdf,.zip,.doc,.docx" onchange="handleFileSelect(this, <?= ($assignment['max_file_size_mb'] ?? 10) * 1024 * 1024 ?>)">
                                    </div>
                                    <div class="file-list" id="fileList" style="margin-top: 10px;"></div>
                                    <div id="uploadStatus" style="margin-top: 8px; font-size: 0.85rem; display: none;"></div>
                                </div>

                                <div id="submitArea">
                                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;" id="submitBtn">
                                        <?= $existingSub ? '&#128260; Resubmit Assignment' : '&#128228; Submit Assignment' ?>
                                    </button>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <script>
                // File upload handling with validation
                function handleFileSelect(input, maxSize) {
                    const file = input.files[0];
                    const statusDiv = document.getElementById('uploadStatus');
                    const fileList = document.getElementById('fileList');
                    
                    if (!file) {
                        fileList.innerHTML = '';
                        statusDiv.style.display = 'none';
                        return;
                    }
                    
                    // Validate file size
                    if (file.size > maxSize) {
                        showNotification('File size exceeds ' + (maxSize / 1024 / 1024) + 'MB limit.', 'error');
                        input.value = '';
                        fileList.innerHTML = '';
                        return;
                    }
                    
                    // Validate file type
                    const allowedTypes = ['pdf', 'zip', 'doc', 'docx'];
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (!allowedTypes.includes(ext)) {
                        showNotification('Invalid file type. Allowed: ' + allowedTypes.join(', '), 'error');
                        input.value = '';
                        fileList.innerHTML = '';
                        return;
                    }
                    
                    // Show file info
                    fileList.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #e3f2fd; border-radius: 8px; border: 1px solid #90caf9;">
                            <span style="font-size: 1.2rem;">&#128196;</span>
                            <div style="flex: 1;">
                                <div style="font-weight: 500; font-size: 0.9rem;">${file.name}</div>
                                <div style="font-size: 0.8rem; color: var(--gray);">${(file.size / 1024).toFixed(1)} KB</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger" onclick="clearFile()">&#10005; Remove</button>
                        </div>
                    `;
                    
                    statusDiv.innerHTML = '<span style="color: var(--success);">&#10003; File ready for upload</span>';
                    statusDiv.style.display = 'block';
                }
                
                function clearFile() {
                    document.getElementById('submissionFile').value = '';
                    document.getElementById('fileList').innerHTML = '';
                    document.getElementById('uploadStatus').style.display = 'none';
                }

                // Form submission
                document.getElementById('submitAssignmentForm').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const btn = document.getElementById('submitBtn');
                    const originalText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner"></span> Uploading...';
                    
                    const statusDiv = document.getElementById('uploadStatus');
                    statusDiv.innerHTML = '<span style="color: var(--primary);">&#128259; Uploading file... Please wait</span>';
                    statusDiv.style.display = 'block';

                    const formData = new FormData(this);
                    formData.append('action', 'submit');

                    try {
                        const res = await fetch('/api/assignments.php', { method: 'POST', body: formData });
                        const data = await res.json();
                        
                        if (data.success) {
                            statusDiv.innerHTML = '<span style="color: var(--success);">&#10003; ' + data.message + '</span>';
                            showNotification(data.message, 'success');
                            setTimeout(() => window.location.href = '?section=todo', 1500);
                        } else {
                            statusDiv.innerHTML = '<span style="color: var(--danger);">&#10007; ' + (data.message || 'Error') + '</span>';
                            showNotification(data.message || 'Error submitting assignment', 'error');
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    } catch (err) {
                        statusDiv.innerHTML = '<span style="color: var(--danger);">&#10007; Network error. Please try again.</span>';
                        showNotification('Error submitting assignment', 'error');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                });
                </script>
                <?php endif; ?>
                <?php endif; ?>
                                <?php if ($section === 'gradebook'): ?>
                <div class="page-header">
                    <h1 class="page-title">My Gradebook</h1>
                    <p class="page-subtitle">View all your grades and feedback organized by course</p>
                </div>

                <?php if (empty($enrolledCourses)): ?>
                <div class="card">
                    <div class="card-body" style="text-align: center; padding: 60px;">
                        <div style="font-size: 3rem; margin-bottom: 12px;">&#128218;</div>
                        <h3 style="color: var(--gray);">No Enrolled Courses</h3>
                        <p style="color: var(--gray);">You are not enrolled in any courses yet.</p>
                    </div>
                </div>
                <?php endif; ?>

                <?php foreach ($enrolledCourses as $course): 
                    $courseQuizzes = $db->query(
                        "SELECT q.title, qa.score, qa.percentage, qa.status, qa.submitted_at, q.total_marks
                         FROM quizzes q
                         LEFT JOIN quiz_attempts qa ON q.id = qa.quiz_id AND qa.student_id = ?
                         WHERE q.course_id = ? AND q.is_published = 1 AND qa.status IN ('submitted', 'graded', 'auto_submitted')
                         ORDER BY q.created_at DESC",
                        [$user['id'], $course['id']]
                    )->fetchAll();
                    
                    $courseAssignments = $db->query(
                        "SELECT a.title, asub.marks_obtained, asub.feedback, asub.status, asub.submitted_at, a.max_marks, asub.graded_at, asub.is_late
                         FROM assignments a
                         LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id AND asub.student_id = ?
                         WHERE a.course_id = ? AND a.is_published = 1
                         ORDER BY a.created_at DESC",
                        [$user['id'], $course['id']]
                    )->fetchAll();
                    
                    $hasAnyGrades = !empty($courseQuizzes) || !empty(array_filter($courseAssignments, fn($a) => $a['status'] === 'graded'));
                ?>
                <div class="card" style="margin-bottom: 24px;">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="card-title">&#128218; <?= htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']) ?></span>
                        <?php if ($hasAnyGrades): ?>
                        <span class="badge badge-success">Grades Available</span>
                        <?php else: ?>
                        <span class="badge badge-secondary">No Grades Yet</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body" style="padding: 0;">
                        <?php if (!empty($courseQuizzes)): ?>
                        <div style="padding: 16px 20px; border-bottom: 1px solid #e9ecef;">
                            <h4 style="margin: 0 0 12px 0; color: var(--primary); font-size: 1rem;">&#128221; Quizzes</h4>
                            <div class="table-container">
                                <table class="data-table" style="margin: 0;">
                                    <thead>
                                        <tr><th>Quiz</th><th>Score</th><th>Percentage</th><th>Grade</th><th>Submitted</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($courseQuizzes as $quiz):
                                            $pct = $quiz['percentage'] ?? 0;
                                            $gradeClass = $pct >= 85 ? 'grade-excellent' : ($pct >= 70 ? 'grade-good' : ($pct >= 60 ? 'grade-average' : 'grade-poor'));
                                            $letterGrade = $pct >= 85 ? 'A' : ($pct >= 70 ? 'B' : ($pct >= 60 ? 'C' : ($pct >= 50 ? 'D' : 'F')));
                                        ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($quiz['title']) ?></strong></td>
                                            <td><?= $quiz['score'] !== null ? $quiz['score'] . '/' . $quiz['total_marks'] : 'N/A' ?></td>
                                            <td class="<?= $gradeClass ?>"><?= $quiz['percentage'] !== null ? number_format($quiz['percentage'], 1) . '%' : 'N/A' ?></td>
                                            <td><span class="badge badge-<?= $pct >= 60 ? 'success' : 'danger' ?>"><?= $letterGrade ?></span></td>
                                            <td><?= timeAgo($quiz['submitted_at']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($courseAssignments)): ?>
                        <div style="padding: 16px 20px;">
                            <h4 style="margin: 0 0 12px 0; color: var(--primary); font-size: 1rem;">&#128193; Assignments</h4>
                            <div class="table-container">
                                <table class="data-table" style="margin: 0;">
                                    <thead>
                                        <tr><th>Assignment</th><th>Status</th><th>Marks</th><th>Feedback</th><th>Submitted</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($courseAssignments as $assign):
                                            $isGraded = $assign['status'] === 'graded';
                                            $pct = $isGraded && $assign['max_marks'] > 0 ? ($assign['marks_obtained'] / $assign['max_marks']) * 100 : 0;
                                        ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($assign['title']) ?></strong></td>
                                            <td>
                                                <?php if ($isGraded): ?>
                                                <span class="badge badge-success">Graded</span>
                                                <?php elseif ($assign['status'] === 'submitted'): ?>
                                                <span class="badge badge-warning">Pending Grade</span>
                                                <?php else: ?>
                                                <span class="badge badge-secondary">Not Submitted</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($isGraded): ?>
                                                    <span style="font-weight: 600; color: <?= $pct >= 60 ? 'var(--success)' : 'var(--danger)' ?>;">
                                                        <?= $assign['marks_obtained'] ?>/<?= $assign['max_marks'] ?>
                                                        (<?= number_format($pct, 1) ?>%)
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color: var(--gray);">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="max-width: 300px;">
                                                <?php if ($isGraded && $assign['feedback']): ?>
                                                    <div style="background: #e3f2fd; padding: 8px 12px; border-radius: 6px; font-size: 0.85rem; color: #1565c0; border-left: 3px solid var(--primary);">
                                                        <strong>Dr. Comment:</strong> <?= nl2br(htmlspecialchars($assign['feedback'])) ?>
                                                    </div>
                                                <?php elseif ($isGraded): ?>
                                                    <span style="color: var(--gray); font-size: 0.85rem;">No feedback provided</span>
                                                <?php else: ?>
                                                    <span style="color: var(--gray);">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($assign['submitted_at']): ?>
                                                    <?= timeAgo($assign['submitted_at']) ?>
                                                    <?php if ($assign['is_late']): ?>
                                                    <span class="badge badge-warning">Late</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span style="color: var(--gray);">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (empty($courseQuizzes) && empty(array_filter($courseAssignments, fn($a) => $a['status'] === 'graded'))): ?>
                        <div style="padding: 40px; text-align: center; color: var(--gray);">
                            <div style="font-size: 2rem; margin-bottom: 8px;">&#128221;</div>
                            <p>No graded assessments available for this course yet.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js"></script>
</body>
</html>