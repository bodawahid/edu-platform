<?php
/**
 * Student Dashboard
 */

require_once __DIR__ . '/../includes/functions.php';
requireRole('student');

$db = Database::getInstance();
$section = $_GET['section'] ?? 'dashboard';
$user = getCurrentUser();

// Initialize quizId to prevent undefined variables
$quizId = null;

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

// Stats
$stats = [
    'courses' => count($enrolledCourses),
    'upcoming_quizzes' => count($upcomingQuizzes),
    'pending_assignments' => count(array_filter($pendingAssignments, fn($a) => !$a['has_submitted'])),
    'avg_score' => $db->query("SELECT AVG(percentage) as avg FROM quiz_attempts WHERE student_id = ? AND status IN ('submitted', 'graded')", [$user['id']])->fetch()['avg'] ?? 0,
];

// Quiz detail page logic
$quizDetail = null;
$quizQuestions = [];
if ($section === 'quizzes' && isset($_GET['id'])) {
    $quizId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $quizDetail = $db->query(
        "SELECT q.*, c.course_name FROM quizzes q JOIN courses c ON q.course_id = c.id WHERE q.id = ? AND q.is_published = 1",
        [$quizId]
    )->fetch();

    if ($quizDetail) {
        $quizQuestions = $db->query("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC", [$quizId])->fetchAll();
        foreach ($quizQuestions as &$q) {
            $q['options'] = $db->query("SELECT * FROM question_options WHERE question_id = ? ORDER BY option_order", [$q['id']])->fetchAll();
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
    <style>
        .upload-zone {
            border: 2px dashed #90caf9;
            padding: 30px;
            text-align: center;
            background: #f4fafd;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }
        .upload-zone:hover { background: #e3f2fd; }
        .assignment-file-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: #e8f5e9;
            border-radius: 8px;
            border: 1px solid #a5d6a7;
            margin-bottom: 12px;
        }
    </style>
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
                        <a href="?section=dashboard" class="btn btn-sm btn-outline" style="margin-top: 12px;">&larr; Back to Dashboard</a>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <?php 
                            $allNotifs = $db->query("SELECT * FROM notifications WHERE user_id = ? OR role_target = ? ORDER BY created_at DESC LIMIT 100", [$user['id'], $user['role_name']])->fetchAll();
                            $db->query("UPDATE notifications SET is_read = 1 WHERE user_id = ? OR role_target = ?", [$user['id'], $user['role_name']]);
                            ?>
                            <table class="data-table">
                                <thead><tr><th>Status</th><th>Type</th><th>Title</th><th>Message</th></tr></thead>
                                <tbody>
                                    <?php foreach ($allNotifs as $n): ?>
                                    <tr>
                                        <td><strong><?= $n['is_read'] == 0 ? '● New' : '○ Read' ?></strong></td>
                                        <td><?= htmlspecialchars($n['type']) ?></td>
                                        <td><?= htmlspecialchars($n['title']) ?></td>
                                        <td><?= htmlspecialchars($n['message']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

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

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top:20px;">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Upcoming Quizzes</span>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <ul class="todo-list">
                                <?php foreach (array_slice($upcomingQuizzes, 0, 5) as $q): $deadline = getDeadlineStatus($q['end_time']); ?>
                                <li class="todo-item" style="padding:15px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee;">
                                    <div>
                                        <div style="font-weight:600;"><?= htmlspecialchars($q['title']) ?></div>
                                        <small style="color:gray;"><?= htmlspecialchars($q['course_code']) ?></small>
                                    </div>
                                    <span class="deadline-badge deadline-<?= $deadline['class'] ?>"><?= $deadline['text'] ?></span>
                                </li>
                                <?php endforeach; if(empty($upcomingQuizzes)): ?>
                                    <li style="padding:20px; text-align:center; color:gray;">No upcoming quizzes!</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><span class="card-title">Pending Assignments</span></div>
                        <div class="card-body" style="padding: 0;">
                            <ul class="todo-list">
                                <?php $pOnly = array_filter($pendingAssignments, fn($a) => !$a['has_submitted']);
                                foreach (array_slice($pOnly, 0, 5) as $a): $deadline = getDeadlineStatus($a['deadline']); ?>
                                <li class="todo-item" style="padding:15px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee;">
                                    <div>
                                        <div style="font-weight:600;"><?= htmlspecialchars($a['title']) ?></div>
                                        <small style="color:gray;"><?= htmlspecialchars($a['course_code']) ?></small>
                                    </div>
                                    <span class="deadline-badge deadline-<?= $deadline['class'] ?>"><?= $deadline['text'] ?></span>
                                </li>
                                <?php endforeach; if(empty($pOnly)): ?>
                                    <li style="padding:20px; text-align:center; color:gray;">All assignments submitted!</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($section === 'courses' || $section === 'my_courses'): ?>
                <div class="page-header">
                    <h1 class="page-title">My Courses</h1>
                    <p class="page-subtitle">Current semester enrolled courses</p>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach ($enrolledCourses as $c): 
                        $doctor = $db->query("SELECT u.full_name FROM course_doctors cd JOIN users u ON cd.doctor_id = u.id WHERE cd.course_id = ?", [$c['id']])->fetch();
                    ?>
                    <div class="card">
                        <div style="background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; padding: 20px;">
                            <small><?= htmlspecialchars($c['course_code']) ?></small>
                            <div style="font-size:1.2rem; font-weight:600;"><?= htmlspecialchars($c['course_name']) ?></div>
                        </div>
                        <div style="padding: 15px;">
                            <p style="font-size:0.9rem; color:gray;"><?= htmlspecialchars($c['description'] ?? 'No description.') ?></p>
                            <hr style="margin:10px 0; border:0; border-top:1px solid #eee;">
                            <small>Instructor: Dr. <?= htmlspecialchars($doctor['full_name'] ?? 'N/A') ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($section === 'quizzes' && !$quizDetail): ?>
                <div class="page-header">
                    <h1 class="page-title">Available Quizzes</h1>
                    <p class="page-subtitle">Select a quiz slot to execute attempt</p>
                </div>
                <div class="card">
                    <div class="card-body" style="padding:0;">
                        <table class="data-table">
                            <thead><tr><th>Quiz Title</th><th>Course</th><th>Duration</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach ($upcomingQuizzes as $q): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($q['title']) ?></strong></td>
                                    <td><?= htmlspecialchars($q['course_code']) ?></td>
                                    <td><?= $q['duration_minutes'] ?> min</td>
                                    <td>
                                        <?php if($q['has_attempted']): ?>
                                            <span class="badge badge-success">Submitted</span>
                                        <?php else: ?>
                                            <a href="?section=quizzes&id=<?= $q['id'] ?>" class="btn btn-sm btn-primary">Take Quiz</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; if(empty($upcomingQuizzes)): ?>
                                    <tr><td colspan="4" style="text-align:center; padding:20px; color:gray;">No quizzes bound to your track right now.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($section === 'quizzes' && $quizDetail): ?>
                    <div class="quiz-container" id="quizInterface" style="background:white; padding:25px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                        <div style="border-bottom:2px solid #eee; padding-bottom:15px; margin-bottom:20px;">
                            <h2><?= htmlspecialchars($quizDetail['title']) ?></h2>
                            <small style="color:gray;"><?= htmlspecialchars($quizDetail['course_name']) ?></small>
                        </div>
                        <form id="quizForm">
                            <?= csrfField() ?>
                            <input type="hidden" name="quiz_id" value="<?= $quizDetail['id'] ?>">
                            <?php foreach ($quizQuestions as $idx => $q): ?>
                            <div style="margin-bottom:25px; padding:15px; background:#f9f9f9; border-radius:6px;">
                                <div style="font-weight:600; margin-bottom:12px;"><?= $idx+1 ?>. <?= htmlspecialchars($q['question_text']) ?></div>
                                <?php foreach ($q['options'] as $opt): ?>
                                <label style="display:block; margin-bottom:8px; cursor:pointer;">
                                    <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= htmlspecialchars($opt['option_text']) ?>" required>
                                    <?= htmlspecialchars($opt['option_text']) ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn-primary" id="submitQuizBtn" style="width:100%;">Submit Attempt to Server</button>
                        </form>
                    </div>
                    <script>
                        document.getElementById('quizForm').addEventListener('submit', async function(e) {
                            e.preventDefault();
                            const btn = document.getElementById('submitQuizBtn');
                            btn.disabled = true;
                            const fd = new FormData(this);
                            fd.append('action', 'submit_attempt');

                            const res = await fetch('/api/quizzes.php', { method: 'POST', body: fd });
                            const data = await res.json();
                            if(data.success) {
                                alert('Quiz Submitted Successfully! Percentage Score: ' + data.percentage + '%');
                                window.location.href = '?section=dashboard';
                            } else {
                                alert(data.message);
                                btn.disabled = false;
                            }
                        });
                    </script>
                <?php endif; ?>

                <?php if ($section === 'assignments' && !isset($_GET['id'])): ?>
                <div class="page-header">
                    <h1 class="page-title">My Assignments</h1>
                    <p class="page-subtitle">Track and upload your core assignment sheets</p>
                </div>
                <div class="card">
                    <div class="card-body" style="padding:0;">
                        <table class="data-table">
                            <thead><tr><th>Assignment Title</th><th>Course</th><th>Deadline</th><th>Status</th><th>Control</th></tr></thead>
                            <tbody>
                                <?php foreach ($pendingAssignments as $a): $dl = getDeadlineStatus($a['deadline']); ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($a['title']) ?></strong></td>
                                    <td><?= htmlspecialchars($a['course_code']) ?></td>
                                    <td><span class="deadline-badge deadline-<?= $dl['class'] ?>"><?= $dl['text'] ?></span></td>
                                    <td><?= $a['has_submitted'] ? '<span class="badge badge-success">Submitted</span>' : '<span class="badge badge-warning">Pending</span>' ?></td>
                                    <td><a href="?section=assignments&id=<?= $a['id'] ?>" class="btn btn-sm btn-primary">Open Slot</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($section === 'assignments' && isset($_GET['id'])): ?>
                <?php
                $assignmentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                $assignmentData = $db->query(
                    "SELECT a.*, c.course_code, c.course_name FROM assignments a
                     JOIN courses c ON a.course_id = c.id WHERE a.id = ? AND a.is_published = 1",
                    [$assignmentId]
                )->fetch();

                if ($assignmentData):
                    $existingSub = $db->query("SELECT * FROM assignment_submissions WHERE assignment_id = ? AND student_id = ?", [$assignmentId, $user['id']])->fetch();
                    $assignmentFiles = $db->query("SELECT * FROM assignment_files WHERE assignment_id = ? ORDER BY uploaded_at DESC", [$assignmentId])->fetchAll();
                ?>
                <div class="page-header">
                    <h1 class="page-title">Assignment Slot Control</h1>
                    <p class="page-subtitle"><?= htmlspecialchars($assignmentData['title']) ?></p>
                    <a href="?section=assignments" class="btn btn-sm btn-outline" style="margin-top:10px;">&larr; Back to List</a>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-top:20px;">
                    <div class="card">
                        <div class="card-header"><span class="card-title">Course Guidelines</span></div>
                        <div class="card-body">
                            <p><strong>Code:</strong> <?= htmlspecialchars($assignmentData['course_code']) ?></p>
                            <p><strong>Marks Context:</strong> <?= $assignmentData['max_marks'] ?> Pts</p>
                            
                            <?php if(!empty($assignmentFiles)): ?>
                            <div style="margin-top:15px; padding-top:10px; border-top:1px solid #eee;">
                                <strong style="color:var(--primary); font-size:0.9rem;">📂 Doctor Instructions Files:</strong>
                                <div style="margin-top:8px; display:flex; flex-direction:column; gap:5px;">
                                    <?php foreach ($assignmentFiles as $f): ?>
                                    <a href="/<?= htmlspecialchars($f['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline" style="text-align:left; font-size:0.8rem;">
                                        📄 <?= htmlspecialchars($f['file_name']) ?>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form id="submitAssignmentForm" enctype="multipart/form-data">
                                <?= csrfField() ?>
                                <input type="hidden" name="assignment_id" value="<?= $assignmentId ?>">

                                <div class="form-group">
                                    <label class="form-label">Submission Comments Text</label>
                                    <textarea name="submission_text" class="form-control" rows="4" placeholder="Type text annotations..."><?= $existingSub ? htmlspecialchars($existingSub['submission_text'] ?? '') : '' ?></textarea>
                                </div>

                                <?php if($existingSub && $existingSub['file_path']): ?>
                                <div class="form-group" id="current_file_container">
                                    <label class="form-label">Active Linked Bundle</label>
                                    <div class="assignment-file-badge">
                                        <span>✔</span>
                                        <div style="flex:1;"><strong><?= htmlspecialchars($existingSub['file_name']) ?></strong></div>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="clearExistingDatabaseFile()">&times; Delete & Remove</button>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="form-group" id="upload_zone_wrapper" style="<?= ($existingSub && $existingSub['file_path']) ? 'display:none;' : '' ?>">
                                    <label class="form-label">Upload Solution Target</label>
                                    <div class="upload-zone" id="uploadZone">
                                        <input type="file" name="submission_file" id="submissionFile" onchange="handleFileSelect(this)">
                                    </div>
                                    <div id="uploadStatus" style="margin-top:8px;"></div>
                                </div>

                                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:20px;" id="submitBtn">Commit Solution Bundle</button>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    function handleFileSelect(input) {
                        const status = document.getElementById('uploadStatus');
                        if(input.files[0]) {
                            status.innerHTML = `<span style="color:var(--success);">✔ Asset ready: ${input.files[0].name}</span>`;
                        }
                    }
                    function clearExistingDatabaseFile() {
                        if(confirm('Drop current node execution and replace asset?')) {
                            document.getElementById('current_file_container').style.display = 'none';
                            document.getElementById('upload_zone_wrapper').style.display = 'block';
                            document.getElementById('submissionFile').value = '';
                        }
                    }
                    document.getElementById('submitAssignmentForm').addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const btn = document.getElementById('submitBtn');
                        btn.disabled = true;
                        const fd = new FormData(this);
                        fd.append('action', 'submit');

                        const res = await fetch('/api/assignments.php', { method: 'POST', body: fd });
                        const data = await res.json();
                        if(data.success) {
                            alert('Assignment Synced onto Platform successfully.');
                            window.location.href = '?section=assignments';
                        } else {
                            alert(data.message);
                            btn.disabled = false;
                        }
                    });
                </script>
                <?php endif; ?>
                <?php endif; ?>

                <?php if ($section === 'gradebook' || $section === 'grades' || $section === 'my_grades'): ?>
                <div class="page-header">
                    <h1 class="page-title">My Gradebook</h1>
                    <p class="page-subtitle">View your verified marks index records</p>
                </div>
                <?php foreach ($enrolledCourses as $course): 
                    $courseQuizzes = $db->query("SELECT q.title, qa.score, qa.percentage, q.total_marks FROM quizzes q JOIN quiz_attempts qa ON q.id = qa.quiz_id WHERE q.course_id = ? AND qa.student_id = ?", [$course['id'], $user['id']])->fetchAll();
                ?>
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-header"><strong><?= htmlspecialchars($course['course_code']) ?> - <?= htmlspecialchars($course['course_name']) ?></strong></div>
                    <div class="card-body">
                        <?php if(!empty($courseQuizzes)): ?>
                        <table class="data-table">
                            <thead><tr><th>Quiz</th><th>Score</th><th>Percentage</th></tr></thead>
                            <tbody>
                                <?php foreach ($courseQuizzes as $cq): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cq['title']) ?></td>
                                    <td><?= $cq['score'] ?> / <?= $cq['total_marks'] ?></td>
                                    <td><?= number_format($cq['percentage'], 1) ?>%</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <p style="color:gray; font-size:0.9rem; text-align:center;">No grading data pushed onto your cluster matrix node yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</body>
</html>