<?php
/**
 * TA Dashboard (المعيد)
 */

require_once __DIR__ . '/../includes/functions.php';
requireRole('ta');

$db = Database::getInstance();
$section = $_GET['section'] ?? 'dashboard';
$user = getCurrentUser();

// Fetch TA's assigned courses
$assignedCourses = $db->query(
    "SELECT c.*, u.full_name as doctor_name FROM course_tas ct
     JOIN courses c ON ct.course_id = c.id
     JOIN course_doctors cd ON c.id = cd.course_id
     JOIN users u ON cd.doctor_id = u.id
     WHERE ct.ta_id = ? AND c.is_active = 1
     ORDER BY c.course_code",
    [$user['id']]
)->fetchAll();

// Fetch grading queue (submissions for assignments in TA's courses)
$gradingQueue = [];
if (!empty($assignedCourses)) {
    $courseIds = array_column($assignedCourses, 'id');
    $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
    $gradingQueue = $db->query(
        "SELECT asub.*, a.title as assignment_title, a.max_marks, a.deadline, u.full_name as student_name, u.username, c.course_code, c.course_name
         FROM assignment_submissions asub
         JOIN assignments a ON asub.assignment_id = a.id
         JOIN users u ON asub.student_id = u.id
         JOIN courses c ON a.course_id = c.id
         WHERE a.course_id IN ($placeholders) AND asub.status = 'submitted'
         ORDER BY asub.submitted_at DESC",
        $courseIds
    )->fetchAll();
}

// Fetch all submissions (graded + pending)
$allSubmissions = [];
if (!empty($assignedCourses)) {
    $courseIds = array_column($assignedCourses, 'id');
    $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
    $allSubmissions = $db->query(
        "SELECT asub.*, a.title as assignment_title, a.max_marks, u.full_name as student_name, u.username, c.course_code,
         grader.full_name as graded_by_name
         FROM assignment_submissions asub
         JOIN assignments a ON asub.assignment_id = a.id
         JOIN users u ON asub.student_id = u.id
         JOIN courses c ON a.course_id = c.id
         LEFT JOIN users grader ON asub.graded_by = grader.id
         WHERE a.course_id IN ($placeholders)
         ORDER BY asub.submitted_at DESC LIMIT 50",
        $courseIds
    )->fetchAll();
}

// Fetch students per course
$studentsPerCourse = [];
if ($section === 'students') {
    foreach ($assignedCourses as $course) {
        $students = $db->query(
            "SELECT u.id, u.full_name, u.username, u.email, u.department, ce.enrolled_at, ce.status
             FROM users u
             JOIN course_enrollments ce ON u.id = ce.student_id
             WHERE ce.course_id = ?
             ORDER BY u.full_name",
            [$course['id']]
        )->fetchAll();
        $studentsPerCourse[$course['id']] = ['course' => $course, 'students' => $students];
    }
}

// Stats
$stats = [
    'courses' => count($assignedCourses),
    'pending_grades' => count($gradingQueue),
    'total_submissions' => count($allSubmissions),
    'graded' => count(array_filter($allSubmissions, fn($s) => $s['status'] === 'graded')),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TA Dashboard - Faculty of Engineering</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" onclick="toggleSidebar()">&#9776;</button>
                    <h2 class="topbar-title">TA Dashboard (المعيد)</h2>
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
                    <h1 class="page-title">TA Dashboard</h1>
                    <p class="page-subtitle">Welcome, <?= htmlspecialchars($user['full_name']) ?></p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">&#128218;</div>
                        <div class="stat-info"><h3><?= $stats['courses'] ?></h3><p>Assigned Courses</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">&#128221;</div>
                        <div class="stat-info"><h3><?= $stats['pending_grades'] ?></h3><p>Pending Grades</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">&#128193;</div>
                        <div class="stat-info"><h3><?= $stats['total_submissions'] ?></h3><p>Total Submissions</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">&#9989;</div>
                        <div class="stat-info"><h3><?= $stats['graded'] ?></h3><p>Graded</p></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Grading Queue</span>
                            <a href="?section=grading" class="btn btn-sm btn-outline">View All</a>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr><th>Student</th><th>Assignment</th><th>Course</th><th>Submitted</th><th>Action</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($gradingQueue, 0, 5) as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['student_name']) ?></td>
                                            <td><?= htmlspecialchars($item['assignment_title']) ?></td>
                                            <td><?= htmlspecialchars($item['course_code']) ?></td>
                                            <td><?= timeAgo($item['submitted_at']) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="gradeSubmission(<?= $item['id'] ?>, <?= $item['max_marks'] ?>)">&#9998; Grade</button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($gradingQueue)): ?>
                                        <tr><td colspan="5" style="text-align:center;color:var(--gray);padding:30px;">No pending submissions to grade!</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">My Courses</span>
                        </div>
                        <div class="card-body">
                            <?php foreach ($assignedCourses as $course): ?>
                            <div style="padding: 12px; border: 1px solid var(--gray-light); border-radius: 8px; margin-bottom: 10px;">
                                <div style="font-weight: 600; color: var(--primary);"><?= htmlspecialchars($course['course_code']) ?></div>
                                <div style="font-size: 0.9rem;"><?= htmlspecialchars($course['course_name']) ?></div>
                                <div style="font-size: 0.8rem; color: var(--gray); margin-top: 4px;">
                                    Dr. <?= htmlspecialchars($course['doctor_name']) ?> &middot; <?= htmlspecialchars($course['department']) ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($section === 'courses'): ?>
                <div class="page-header">
                    <h1 class="page-title">My Assigned Courses</h1>
                    <p class="page-subtitle">Courses you are assigned to assist with</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
                    <?php foreach ($assignedCourses as $course):
                        $studentCount = $db->query("SELECT COUNT(*) as c FROM course_enrollments WHERE course_id = ? AND status = 'active'", [$course['id']])->fetch()['c'];
                        $assignmentCount = $db->query("SELECT COUNT(*) as c FROM assignments WHERE course_id = ?", [$course['id']])->fetch()['c'];
                    ?>
                    <div class="card">
                        <div style="background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; padding: 20px;">
                            <div style="font-size: 0.85rem; opacity: 0.8; Haus;"><?= htmlspecialchars($course['course_code']) ?></div>
                            <div style="font-size: 1.15rem; font-weight: 600; margin-top: 4px;"><?= htmlspecialchars($course['course_name']) ?></div>
                        </div>
                        <div style="padding: 16px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: var(--gray); font-size: 0.9rem;">Department</span>
                                <span style="font-weight: 500;"><?= htmlspecialchars($course['department']) ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: var(--gray); font-size: 0.9rem;">Students</span>
                                <span style="font-weight: 500;"><?= $studentCount ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: var(--gray); font-size: 0.9rem;">Assignments</span>
                                <span style="font-weight: 500;"><?= $assignmentCount ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--gray); font-size: 0.9rem;">Doctor</span>
                                <span style="font-weight: 500;">Dr. <?= htmlspecialchars($course['doctor_name']) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($section === 'grading'): ?>
                <div class="page-header">
                    <h1 class="page-title">Grading Queue</h1>
                    <p class="page-subtitle">Review and grade student submissions</p>
                </div>

                <div class="card">
                    <div class="card-body" style="padding: 0;">
                        <div class="tabs">
                            <button class="tab-btn active" data-tab="pending">Pending (<?= count($gradingQueue) ?>)</button>
                            <button class="tab-btn" data-tab="all">All Submissions</button>
                        </div>

                        <div id="tab-pending" class="tab-content active">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Student</th><th>Assignment</th><th>Course</th><th>Submitted</th>
                                            <th>File</th><th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($gradingQueue as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['student_name']) ?> <small style="color:var(--gray)">(<?= $item['username'] ?>)</small></td>
                                            <td><?= htmlspecialchars($item['assignment_title']) ?></td>
                                            <td><?= htmlspecialchars($item['course_code']) ?></td>
                                            <td><?= timeAgo($item['submitted_at']) ?> <?= $item['is_late'] ? '<span class="badge badge-warning">Late</span>' : '' ?></td>
                                            <td>
                                                <?php if ($item['file_path']): ?>
                                                <a href="/<?= $item['file_path'] ?>" target="_blank" class="btn btn-sm btn-outline">&#128190; Download</a>
                                                <?php else: ?>Text Only<?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="gradeSubmission(<?= $item['id'] ?>, <?= $item['max_marks'] ?>)">&#9998; Grade</button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($gradingQueue)): ?>
                                        <tr><td colspan="6" style="text-align:center;color:var(--gray);padding:40px;">No pending submissions. Great job!</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="tab-all" class="tab-content">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Student</th><th>Assignment</th><th>Submitted</th><th>Status</th><th>Marks</th><th>Graded By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allSubmissions as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['student_name']) ?></td>
                                            <td><?= htmlspecialchars($item['assignment_title']) ?></td>
                                            <td><?= timeAgo($item['submitted_at']) ?></td>
                                            <td><span class="badge badge-<?= $item['status'] === 'graded' ? 'success' : 'warning' ?>"><?= ucfirst($item['status']) ?></span></td>
                                            <td><?= $item['marks_obtained'] !== null ? $item['marks_obtained'] . '/' . $item['max_marks'] : '-' ?></td>
                                            <td><?= $item['graded_by_name'] ? htmlspecialchars($item['graded_by_name']) : '-' ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($section === 'students'): ?>
                <div class="page-header">
                    <h1 class="page-title">Student Performance</h1>
                    <p class="page-subtitle">Monitor student progress across your courses</p>
                </div>

                <?php foreach ($studentsPerCourse as $data): ?>
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <span class="card-title"><?= htmlspecialchars($data['course']['course_code'] . ' - ' . $data['course']['course_name']) ?></span>
                        <span class="badge badge-primary"><?= count($data['students']) ?> Students</span>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Name</th><th>Username</th><th>Department</th><th>Enrolled</th><th>Quiz Avg</th><th>Assignment Avg</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['students'] as $s):
                                        $quizAvg = $db->query(
                                            "SELECT AVG(qa.percentage) as avg FROM quiz_attempts qa
                                             JOIN quizzes q ON qa.quiz_id = q.id WHERE q.course_id = ? AND qa.student_id = ? AND qa.status IN ('submitted', 'graded')",
                                            [$data['course']['id'], $s['id']]
                                        )->fetch()['avg'];
                                        $assignAvg = $db->query(
                                            "SELECT AVG((asub.marks_obtained / a.max_marks) * 100) as avg FROM assignment_submissions asub
                                             JOIN assignments a ON asub.assignment_id = a.id WHERE a.course_id = ? AND asub.student_id = ? AND asub.status = 'graded'",
                                            [$data['course']['id'], $s['id']]
                                        )->fetch()['avg'];
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($s['full_name']) ?></td>
                                        <td><?= htmlspecialchars($s['username']) ?></td>
                                        <td><?= htmlspecialchars($s['department']) ?></td>
                                        <td><?= timeAgo($s['enrolled_at']) ?></td>
                                        <td><?= $quizAvg ? number_format($quizAvg, 1) . '%' : 'N/A' ?></td>
                                        <td><?= $assignAvg ? number_format($assignAvg, 1) . '%' : 'N/A' ?></td>
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

    <div class="modal-overlay" id="gradeModal">
        <div class="modal" style="max-width: 450px;">
            <div class="modal-header">
                <h3 class="modal-title">Grade Submission</h3>
                <button class="modal-close" onclick="closeModal('gradeModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="gradeForm">
                    <?= csrfField() ?>
                    <input type="hidden" name="submission_id" id="gradeSubmissionId">
                    <div class="form-group">
                        <label class="form-label">Marks Obtained <span id="maxMarksLabel" style="color: var(--gray);"></span></label>
                        <input type="number" name="marks" id="gradeMarks" class="form-control" min="0" step="0.5" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Feedback</label>
                        <textarea name="feedback" class="form-control" rows="4" placeholder="Provide constructive feedback to help the student improve..."></textarea>
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
    function gradeSubmission(id, maxMarks) {
        document.getElementById('gradeSubmissionId').value = id;
        document.getElementById('gradeMarks').max = maxMarks;
        document.getElementById('maxMarksLabel').textContent = '(Max: ' + maxMarks + ')';
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
                showNotification('Grade submitted successfully!', 'success');
                closeModal('gradeModal');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification(data.message, 'error');
            }
        } catch (e) { showNotification('Error submitting grade', 'error'); }
    }
    </script>
</body>
</html>