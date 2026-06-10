<?php
/**
 * Doctor Dashboard
 */

require_once __DIR__ . '/../includes/functions.php';
requireRole('doctor');

$db = Database::getInstance();
$section = $_GET['section'] ?? 'dashboard';
$user = getCurrentUser();

// Fetch doctor's courses
$myCourses = $db->query(
    "SELECT c.* FROM courses c
     JOIN course_doctors cd ON c.id = cd.course_id
     WHERE cd.doctor_id = ? AND c.is_active = 1
     ORDER BY c.course_code",
    [$user['id']]
)->fetchAll();

// Fetch all TAs
$allTas = $db->query("SELECT u.* FROM users u WHERE u.role_id = 3 AND u.is_active = 1 ORDER BY u.full_name")->fetchAll();

// Fetch my quizzes
$myQuizzes = $db->query(
    "SELECT q.*, c.course_code, c.course_name,
     (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id) as attempt_count
     FROM quizzes q
     JOIN courses c ON q.course_id = c.id
     WHERE q.created_by = ? ORDER BY q.created_at DESC",
    [$user['id']]
)->fetchAll();

// Fetch my assignments
$myAssignments = $db->query(
    "SELECT a.*, c.course_code, c.course_name,
     (SELECT COUNT(*) FROM assignment_submissions WHERE assignment_id = a.id) as submission_count
     FROM assignments a
     JOIN courses c ON a.course_id = c.id
     WHERE a.created_by = ? ORDER BY a.created_at DESC",
    [$user['id']]
)->fetchAll();

// Fetch students for gradebook
$gradebookData = [];
if ($section === 'gradebook') {
    foreach ($myCourses as $course) {
        $students = $db->query(
            "SELECT u.id, u.full_name, u.username,
             (SELECT AVG(qa.percentage) FROM quiz_attempts qa
              JOIN quizzes q ON qa.quiz_id = q.id WHERE q.course_id = ? AND qa.student_id = u.id AND qa.status IN ('submitted', 'graded')) as quiz_avg,
             (SELECT AVG(asub.marks_obtained) FROM assignment_submissions asub
              JOIN assignments a ON asub.assignment_id = a.id WHERE a.course_id = ? AND asub.student_id = u.id AND asub.status = 'graded') as assignment_avg
             FROM users u
             JOIN course_enrollments ce ON u.id = ce.student_id
             WHERE ce.course_id = ? AND ce.status = 'active'",
            [$course['id'], $course['id'], $course['id']]
        )->fetchAll();
        $gradebookData[$course['id']] = ['course' => $course, 'students' => $students];
    }
}

// Stats
$stats = [
    'courses' => count($myCourses),
    'quizzes' => count($myQuizzes),
    'assignments' => count($myAssignments),
    'total_students' => $db->query(
        "SELECT COUNT(DISTINCT ce.student_id) as c FROM course_enrollments ce
         JOIN course_doctors cd ON ce.course_id = cd.course_id WHERE cd.doctor_id = ?",
        [$user['id']]
    )->fetch()['c'],
];

// Quiz attempts distribution for analytics
$quizAnalytics = $db->query(
    "SELECT q.title, COUNT(qa.id) as attempts, AVG(qa.percentage) as avg_score
     FROM quizzes q
     LEFT JOIN quiz_attempts qa ON q.id = qa.quiz_id
     WHERE q.created_by = ?
     GROUP BY q.id ORDER BY q.created_at DESC LIMIT 10",
    [$user['id']]
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - Faculty of Engineering</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-wrapper">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" onclick="toggleSidebar()">&#9776;</button>
                    <h2 class="topbar-title">Doctor Dashboard</h2>
                </div>
                <div class="topbar-right" style="display: flex; align-items: center; gap: 15px;">
                    <span style="color: var(--gray); font-size: 0.9rem;">Dr. <?= htmlspecialchars($user['full_name']) ?></span>
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
                            // جلب الأرشيف بالكامل (آخر 100 إشعار)
                            $allNotifs = $db->query(
                                "SELECT * FROM notifications 
                                 WHERE user_id = ? OR role_target = ? 
                                 ORDER BY created_at DESC LIMIT 100",
                                [$user['id'], $user['role_name']]
                            )->fetchAll();
                            
                            // تحديث حالة الإشعارات لـ مقروءة فور دخول الصفحة عشان الجرس يصفر علطول
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
                <?php endif; ?>

                <?php if ($section === 'dashboard'): ?>
                <div class="page-header">
                    <h1 class="page-title">Dashboard Overview</h1>
                    <p class="page-subtitle">Welcome, Dr. <?= htmlspecialchars($user['full_name']) ?></p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">&#128218;</div>
                        <div class="stat-info"><h3><?= $stats['courses'] ?></h3><p>My Courses</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">&#127891;</div>
                        <div class="stat-info"><h3><?= $stats['total_students'] ?></h3><p>Enrolled Students</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">&#128221;</div>
                        <div class="stat-info"><h3><?= $stats['quizzes'] ?></h3><p>Quizzes Created</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">&#128193;</div>
                        <div class="stat-info"><h3><?= $stats['assignments'] ?></h3><p>Assignments</p></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Recent Quiz Performance</span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($quizAnalytics)): ?>
                            <canvas id="quizPerformanceChart" height="200"></canvas>
                            <?php else: ?>
                            <p style="color: var(--gray); text-align: center; padding: 40px;">No quiz data available yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Quick Actions</span>
                        </div>
                        <div class="card-body" style="display: flex; flex-direction: column; gap: 10px;">
                            <a href="?section=quizzes" class="btn btn-primary" style="justify-content: flex-start;">&#10133; Create Quiz</a>
                            <a href="?section=assignments" class="btn btn-secondary" style="justify-content: flex-start;">&#10133; New Assignment</a>
                            <a href="?section=tas" class="btn btn-outline" style="justify-content: flex-start;">&#128101; Manage TAs</a>
                            <a href="?section=gradebook" class="btn btn-outline" style="justify-content: flex-start;">&#128200; View Gradebook</a>
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-top: 20px;">
                    <div class="card-header">
                        <span class="card-title">My Courses</span>
                        <a href="?section=courses" class="btn btn-sm btn-outline">View All</a>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Code</th><th>Name</th><th>Department</th><th>Students</th><th>Quizzes</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($myCourses, 0, 5) as $c):
                                        $studentCount = $db->query("SELECT COUNT(*) as c FROM course_enrollments WHERE course_id = ? AND status = 'active'", [$c['id']])->fetch()['c'];
                                        $quizCount = $db->query("SELECT COUNT(*) as c FROM quizzes WHERE course_id = ?", [$c['id']])->fetch()['c'];
                                    ?>
                                    <tr>
                                        $courseGrowthTemp = (int)$c['id'];
                                        <td><strong><?= htmlspecialchars($c['course_code']) ?></strong></td>
                                        <td><?= htmlspecialchars($c['course_name']) ?></td>
                                        <td><?= htmlspecialchars($c['department']) ?></td>
                                        <td><?= $studentCount ?></td>
                                        <td><?= $quizCount ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($section === 'courses'): ?>
                <div class="page-header">
                    <h1 class="page-title">My Courses</h1>
                    <p class="page-subtitle">Manage your engineering courses</p>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Code</th><th>Course Name</th><th>Dept</th><th>Semester</th><th>Year</th><th>Credits</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($myCourses as $c): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($c['course_code']) ?></strong></td>
                                        <td><?= htmlspecialchars($c['course_name']) ?></td>
                                        <td><?= htmlspecialchars($c['department']) ?></td>
                                        <td><?= $c['semester'] ?></td>
                                        <td><?= $c['year'] ?></td>
                                        <td><?= $c['credit_hours'] ?></td>
                                        <td><?= $c['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($section === 'quizzes'): ?>
                <div class="page-header">
                    <h1 class="page-title">Quiz Builder</h1>
                    <p class="page-subtitle">Create and manage quizzes</p>
                    <button class="btn btn-primary" onclick="openModal('createQuizModal')" style="margin-top: 12px;">
                        <span>&#10133;</span> Create New Quiz
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Title</th><th>Course</th><th>Type</th><th>Duration</th><th>Total Marks</th>
                                        <th>Attempts</th><th>Status</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($myQuizzes as $q): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($q['title']) ?></td>
                                        <td><?= htmlspecialchars($q['course_code']) ?></td>
                                        <td><?= strtoupper($q['quiz_type']) ?></td>
                                        <td><?= $q['duration_minutes'] ?> min</td>
                                        <td><?= $q['total_marks'] ?></td>
                                        <td><?= $q['attempt_count'] ?></td>
                                        <td><?= $q['is_published'] ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-warning">Draft</span>' ?></td>
                                        <td>
                                            <a href="?section=quiz_detail&id=<?= $q['id'] ?>" class="btn btn-sm btn-info">&#128269;</a>
                                            <button class="btn btn-sm btn-danger" onclick="deleteQuiz(<?= $q['id'] ?>)">&#128465;</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($section === 'quiz_detail' && isset($_GET['id'])):
                    $quizId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                    $quiz = $db->query("SELECT q.*, c.course_name FROM quizzes q JOIN courses c ON q.course_id = c.id WHERE q.id = ? AND q.created_by = ?", [$quizId, $user['id']])->fetch();
                    $questions = $db->query("SELECT * FROM questions WHERE quiz_id = ? ORDER BY question_order", [$quizId])->fetchAll();
                ?>
                <div class="page-header">
                    <h1 class="page-title"><?= htmlspecialchars($quiz['title'] ?? 'Quiz') ?></h1>
                    <p class="page-subtitle"><?= htmlspecialchars($quiz['course_name'] ?? '') ?></p>
                    <a href="?section=quizzes" class="btn btn-outline btn-sm" style="margin-top: 12px;">&#8592; Back to Quizzes</a>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Questions (<?= count($questions) ?>)</span>
                            <button class="btn btn-sm btn-primary" onclick="openModal('addQuestionModal')">&#10133; Add Question</button>
                        </div>
                        <div class="card-body">
                            <?php foreach ($questions as $idx => $q):
                                $options = $db->query("SELECT * FROM question_options WHERE question_id = ? ORDER BY option_order", [$q['id']])->fetchAll();
                            ?>
                            <div class="question-card">
                                <div class="question-number">Question <?= $idx + 1 ?> &middot; <?= $q['marks'] ?> marks</div>
                                <div class="question-text"><?= htmlspecialchars($q['question_text']) ?></div>
                                <div class="options-list">
                                    <?php foreach ($options as $opt): ?>
                                    <div class="option-item <?= $opt['is_correct'] ? 'selected' : '' ?>" style="background: <?= $opt['is_correct'] ? '#e8f5e9' : '' ?>;">
                                        <input type="radio" <?= $opt['is_correct'] ? 'checked' : '' ?> disabled>
                                        <span><?= htmlspecialchars($opt['option_text']) ?> <?= $opt['is_correct'] ? '<strong>(Correct)</strong>' : '' ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($questions)): ?>
                            <p style="color: var(--gray); text-align: center; padding: 40px;">No questions yet. Add your first question!</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <div class="card" style="margin-bottom: 20px;">
                            <div class="card-header"><span class="card-title">Quiz Settings</span></div>
                            <div class="card-body">
                                <p><strong>Type:</strong> <?= strtoupper($quiz['quiz_type'] ?? '') ?></p>
                                <p><strong>Duration:</strong> <?= $quiz['duration_minutes'] ?? '' ?> minutes</p>
                                <p><strong>Total Marks:</strong> <?= $quiz['total_marks'] ?? '' ?></p>
                                <p><strong>Passing:</strong> <?= $quiz['passing_marks'] ?? '' ?></p>
                                <p><strong>Shuffle:</strong> <?= ($quiz['shuffle_questions'] ?? 0) ? 'Yes' : 'No' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($section === 'assignments'): ?>
                <div class="page-header">
                    <h1 class="page-title">Assignments</h1>
                    <p class="page-subtitle">Create and manage assignments</p>
                    <button class="btn btn-primary" onclick="openModal('createAssignmentModal')" style="margin-top: 12px;">
                        <span>&#10133;</span> New Assignment
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Title</th><th>Course</th><th>Deadline</th><th>Submissions</th>
                                        <th>Max Marks</th><th>Status</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($myAssignments as $a):
                                        $deadline = getDeadlineStatus($a['deadline']);
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($a['title']) ?></td>
                                        <td><?= htmlspecialchars($a['course_code']) ?></td>
                                        <td><span class="deadline-badge deadline-<?= $deadline['class'] ?>"><?= $deadline['text'] ?></span></td>
                                        <td><?= $a['submission_count'] ?></td>
                                        <td><?= $a['max_marks'] ?></td>
                                        <td><?= $a['is_published'] ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-warning">Draft</span>' ?></td>
                                        <td>
                                            <a href="?section=assignment_submissions&id=<?= $a['id'] ?>" class="btn btn-sm btn-info" title="View Submissions">&#128269;</a>
                                            <button class="btn btn-sm btn-danger" onclick="deleteAssignment(<?= $a['id'] ?>)">&#128465;</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($section === 'assignment_submissions' && isset($_GET['id'])):
                    $assignmentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                    $assignment = $db->query("SELECT a.*, c.course_name FROM assignments a JOIN courses c ON a.course_id = c.id WHERE a.id = ? AND a.created_by = ?", [$assignmentId, $user['id']])->fetch();
                    $submissions = $db->query(
                        "SELECT asub.*, u.full_name, u.username FROM assignment_submissions asub
                         JOIN users u ON asub.student_id = u.id WHERE asub.assignment_id = ? ORDER BY asub.submitted_at DESC",
                        [$assignmentId]
                    )->fetchAll();
                ?>
                <div class="page-header">
                    <h1 class="page-title">Submissions: <?= htmlspecialchars($assignment['title'] ?? '') ?></h1>
                    <p class="page-subtitle"><?= count($submissions) ?> submissions received</p>
                    <a href="?section=assignments" class="btn btn-outline btn-sm" style="margin-top: 12px;">&#8592; Back</a>
                </div>

                <div class="card">
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Student</th><th>Submitted</th><th>File</th><th>Status</th><th>Marks</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($submissions as $sub): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($sub['full_name']) ?> <small style="color:var(--gray)">(<?= $sub['username'] ?>)</small></td>
                                        <td><?= timeAgo($sub['submitted_at']) ?> <?= $sub['is_late'] ? '<span class="badge badge-warning">Late</span>' : '' ?></td>
                                        <td>
                                            <?php if ($sub['file_path']): ?>
                                            <a href="/<?= $sub['file_path'] ?>" target="_blank" class="btn btn-sm btn-outline">&#128190; Download</a>
                                            <?php else: ?>-<?php endif; ?>
                                        </td>
                                        <td><span class="badge badge-<?= $sub['status'] === 'graded' ? 'success' : 'warning' ?>"><?= ucfirst($sub['status']) ?></span></td>
                                        <td><?= $sub['marks_obtained'] !== null ? $sub['marks_obtained'] . '/' . $assignment['max_marks'] : '-' ?></td>
                                        <td>
                                            <?php if ($sub['status'] !== 'graded'): ?>
                                            <button class="btn btn-sm btn-primary" onclick="gradeSubmission(<?= $sub['id'] ?>, <?= $assignment['max_marks'] ?>)">&#9998; Grade</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($submissions)): ?>
                                    <tr><td colspan="6" style="text-align:center;color:var(--gray);padding:40px;">No submissions yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($section === 'tas'): ?>
                <div class="page-header">
                    <h1 class="page-title">Teaching Assistants</h1>
                    <p class="page-subtitle">Assign TAs to your courses</p>
                </div>

                <?php foreach ($myCourses as $course):
                    $courseTas = $db->query(
                        "SELECT u.id, u.full_name, u.email, u.department FROM course_tas ct
                         JOIN users u ON ct.ta_id = u.id WHERE ct.course_id = ?",
                        [$course['id']]
                    )->fetchAll();
                ?>
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <span class="card-title"><?= htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']) ?></span>
                        <button class="btn btn-sm btn-primary" onclick="assignTa(<?= $course['id'] ?>)">&#10133; Assign TA</button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($courseTas)): ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead><tr><th>Name</th><th>Email</th><th>Department</th><th>Actions</th></tr></thead>
                                <tbody>
                                    <?php foreach ($courseTas as $ta): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($ta['full_name']) ?></td>
                                        <td><?= htmlspecialchars($ta['email']) ?></td>
                                        <td><?= htmlspecialchars($ta['department']) ?></td>
                                        <td><button class="btn btn-sm btn-danger" onclick="removeTa(<?= $course['id'] ?>, <?= $ta['id'] ?>)">Remove</button></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p style="color: var(--gray); text-align: center; padding: 20px;">No TAs assigned to this course yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($section === 'gradebook'): ?>
                <div class="page-header">
                    <h1 class="page-title">Gradebook</h1>
                    <p class="page-subtitle">Student performance overview</p>
                </div>

                <?php foreach ($gradebookData as $data): ?>
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <span class="card-title"><?= htmlspecialchars($data['course']['course_code'] . ' - ' . $data['course']['course_name']) ?></span>
                        <span class="badge badge-primary"><?= count($data['students']) ?> Students</span>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Student</th><th>Quiz Avg</th><th>Assignment Avg</th><th>Overall</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['students'] as $s):
                                        $quizAvg = $s['quiz_avg'] ? round($s['quiz_avg'], 1) : 0;
                                        $assignAvg = $s['assignment_avg'] ? round($s['assignment_avg'], 1) : 0;
                                        $overall = ($quizAvg + $assignAvg) / 2;
                                        $gradeClass = $overall >= 85 ? 'grade-excellent' : ($overall >= 70 ? 'grade-good' : ($overall >= 60 ? 'grade-average' : 'grade-poor'));
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($s['full_name']) ?></td>
                                        <td><?= $quizAvg ? $quizAvg . '%' : 'N/A' ?></td>
                                        <td><?= $assignAvg ? $assignAvg . '%' : 'N/A' ?></td>
                                        <td class="<?= $gradeClass ?>"><?= $overall ? number_format($overall, 1) . '%' : 'N/A' ?></td>
                                        <td>
                                            <?php if ($overall >= 85): ?><span class="badge badge-success">Excellent</span>
                                            <?php elseif ($overall >= 70): ?><span class="badge badge-info">Good</span>
                                            <?php elseif ($overall >= 60): ?><span class="badge badge-warning">Average</span>
                                            <?php elseif ($overall > 0): ?><span class="badge badge-danger">At Risk</span>
                                            <?php else: ?><span class="badge badge-secondary">No Data</span><?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="createQuizModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Create New Quiz</h3>
                <button class="modal-close" onclick="closeModal('createQuizModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="createQuizForm">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label class="form-label">Course *</label>
                        <select name="course_id" class="form-control" required>
                            <?php foreach ($myCourses as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quiz Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Quiz Type</label>
                            <select name="quiz_type" class="form-control">
                                <option value="mixed">Mixed (MCQ + T/F)</option>
                                <option value="mcq">Multiple Choice Only</option>
                                <option value="true_false">True/False Only</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Duration (minutes) *</label>
                            <input type="number" name="duration_minutes" class="form-control" value="30" min="5" required>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Total Marks *</label>
                            <input type="number" name="total_marks" class="form-control" value="100" min="1" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Passing Marks</label>
                            <input type="number" name="passing_marks" class="form-control" value="50" min="1">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Start Time *</label>
                            <input type="datetime-local" name="start_time" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">End Time *</label>
                            <input type="datetime-local" name="end_time" class="form-control" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('createQuizModal')">Cancel</button>
                <button class="btn btn-primary" onclick="submitQuiz()">Create Quiz</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="addQuestionModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add Question</h3>
                <button class="modal-close" onclick="closeModal('addQuestionModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addQuestionForm">
                    <?= csrfField() ?>
                    <input type="hidden" name="quiz_id" value="<?= $quizId ?? '' ?>">
                    <div class="form-group">
                        <label class="form-label">Question Text *</label>
                        <textarea name="question_text" class="form-control" rows="3" required></textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select name="question_type" class="form-control" id="qType">
                                <option value="mcq">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Marks</label>
                            <input type="number" name="marks" class="form-control" value="1" min="0.5" step="0.5">
                        </div>
                    </div>
                    <div id="mcqOptions">
                        <label class="form-label">Options (select correct one)</label>
                        <div class="form-group" style="display: flex; gap: 8px; align-items: center;">
                            <input type="radio" name="correct_option" value="0" checked>
                            <input type="text" name="options[]" class="form-control" placeholder="Option 1">
                        </div>
                        <div class="form-group" style="display: flex; gap: 8px; align-items: center;">
                            <input type="radio" name="correct_option" value="1">
                            <input type="text" name="options[]" class="form-control" placeholder="Option 2">
                        </div>
                        <div class="form-group" style="display: flex; gap: 8px; align-items: center;">
                            <input type="radio" name="correct_option" value="2">
                            <input type="text" name="options[]" class="form-control" placeholder="Option 3">
                        </div>
                        <div class="form-group" style="display: flex; gap: 8px; align-items: center;">
                            <input type="radio" name="correct_option" value="3">
                            <input type="text" name="options[]" class="form-control" placeholder="Option 4">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('addQuestionModal')">Cancel</button>
                <button class="btn btn-primary" onclick="submitQuestion()">Add Question</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="createAssignmentModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Create New Assignment</h3>
                <button class="modal-close" onclick="closeModal('createAssignmentModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="createAssignmentForm">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label class="form-label">Course *</label>
                        <select name="course_id" class="form-control" required>
                            <?php foreach ($myCourses as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Instructions</label>
                        <textarea name="instructions" class="form-control" rows="3" placeholder="Detailed instructions for students..."></textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Deadline *</label>
                            <input type="datetime-local" name="deadline" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Max Marks</label>
                            <input type="number" name="max_marks" class="form-control" value="100" min="1">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Allowed File Types</label>
                            <input type="text" name="allowed_types" class="form-control" value="pdf,zip,doc,docx">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Max File Size (MB)</label>
                            <input type="number" name="max_file_size" class="form-control" value="10" min="1">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('createAssignmentModal')">Cancel</button>
                <button class="btn btn-primary" onclick="submitAssignment()">Create Assignment</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="gradeModal">
        <div class="modal" style="max-width: 400px;">
            <div class="modal-header">
                <h3 class="modal-title">Grade Submission</h3>
                <button class="modal-close" onclick="closeModal('gradeModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="gradeForm">
                    <?= csrfField() ?>
                    <input type="hidden" name="submission_id" id="gradeSubmissionId">
                    <div class="form-group">
                        <label class="form-label">Marks Obtained</label>
                        <input type="number" name="marks" id="gradeMarks" class="form-control" min="0" step="0.5" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Feedback</label>
                        <textarea name="feedback" class="form-control" rows="3" placeholder="Provide feedback to the student..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('gradeModal')">Cancel</button>
                <button class="btn btn-primary" onclick="submitGrade()">Submit Grade</button>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js"></script>
    <script>
    // Quiz Performance Chart
    <?php if ($section === 'dashboard' && !empty($quizAnalytics)): ?>
    new Chart(document.getElementById('quizPerformanceChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(fn($q) => substr($q['title'], 0, 20), $quizAnalytics)) ?>,
            datasets: [
                {
                    label: 'Attempts',
                    data: <?= json_encode(array_column($quizAnalytics, 'attempts')) ?>,
                    backgroundColor: '#3498db'
                },
                {
                    label: 'Avg Score (%)',
                    data: <?= json_encode(array_map(fn($q) => round($q['avg_score'] ?? 0, 1), $quizAnalytics)) ?>,
                    backgroundColor: '#27ae60'
                }
            ]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
    <?php endif; ?>

    // Quiz CRUD
    async function submitQuiz() {
        const form = document.getElementById('createQuizForm');
        const formData = new FormData(form);
        formData.append('action', 'create');

        try {
            const res = await fetch('/api/quizzes.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                showNotification('Quiz created! Now add questions.', 'success');
                closeModal('createQuizModal');
                setTimeout(() => window.location.href = '?section=quiz_detail&id=' + data.id, 500);
            } else {
                showNotification(data.message, 'error');
            }
        } catch (e) { showNotification('Error creating quiz', 'error'); }
    }

    async function submitQuestion() {
        const form = document.getElementById('addQuestionForm');
        const formData = new FormData(form);
        formData.append('action', 'add_question');

        try {
            const res = await fetch('/api/quizzes.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                showNotification('Question added!', 'success');
                closeModal('addQuestionModal');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification(data.message, 'error');
            }
        } catch (e) { showNotification('Error adding question', 'error'); }
    }

    function deleteQuiz(id) {
        confirmDelete('Delete this quiz?', async () => {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);
            formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

            try {
                const res = await fetch('/api/quizzes.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) { showNotification('Quiz deleted!', 'success'); setTimeout(() => location.reload(), 500); }
                else showNotification(data.message, 'error');
            } catch (e) { showNotification('Error', 'error'); }
        });
    }

    // Assignment CRUD
    async function submitAssignment() {
        const form = document.getElementById('createAssignmentForm');
        const formData = new FormData(form);
        formData.append('action', 'create');

        try {
            const res = await fetch('/api/assignments.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                showNotification('Assignment created!', 'success');
                closeModal('createAssignmentModal');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification(data.message, 'error');
            }
        } catch (e) { showNotification('Error creating assignment', 'error'); }
    }

    function deleteAssignment(id) {
        confirmDelete('Delete this assignment?', async () => {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);
            formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

            try {
                const res = await fetch('/api/assignments.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) { showNotification('Deleted!', 'success'); setTimeout(() => location.reload(), 500); }
                else showNotification(data.message, 'error');
            } catch (e) { showNotification('Error', 'error'); }
        });
    }

    // Grading
    function gradeSubmission(id, maxMarks) {
        document.getElementById('gradeSubmissionId').value = id;
        document.getElementById('gradeMarks').max = maxMarks;
        document.getElementById('gradeMarks').placeholder = 'Max: ' + maxMarks;
        openModal('gradeModal');
    }

    async function submitGrade() {
        const form = document.getElementById('gradeForm');
        const formData = new FormData(form);
        formData.append('action', 'grade');

        try {
            const res = await fetch('/api/assignments.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                showNotification('Grade submitted!', 'success');
                closeModal('gradeModal');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification(data.message, 'error');
            }
        } catch (e) { showNotification('Error grading', 'error'); }
    }

    // TA Management
    async function assignTa(courseId) {
        const taId = prompt('Enter TA User ID to assign:');
        if (!taId) return;

        const formData = new FormData();
        formData.append('action', 'assign_ta');
        formData.append('course_id', courseId);
        formData.append('ta_id', taId);
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

        try {
            const res = await fetch('/api/courses.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) { showNotification('TA assigned!', 'success'); setTimeout(() => location.reload(), 500); }
            else showNotification(data.message, 'error');
        } catch (e) { showNotification('Error', 'error'); }
    }

    async function removeTa(courseId, taId) {
        confirmDelete('Remove this TA from the course?', async () => {
            const formData = new FormData();
            formData.append('action', 'remove_ta');
            formData.append('course_id', courseId);
            formData.append('ta_id', taId);
            formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

            try {
                const res = await fetch('/api/courses.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) { showNotification('TA removed!', 'success'); setTimeout(() => location.reload(), 500); }
                else showNotification(data.message, 'error');
            } catch (e) { showNotification('Error', 'error'); }
        });
    }
    </script>
</body>
</html>