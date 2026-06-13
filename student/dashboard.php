<?php

/**
 * Student Dashboard - Enhanced UI/UX Production Version
 * Faculty of Engineering at Shubra - Benha University
 * Changes: Toast notifications, merged Gradebook+Grades, removed To-Do,
 *          fixed undefined vars, professional button labels, visual identity
 */

require_once __DIR__ . '/../includes/functions.php';
requireRole('student');

$db = Database::getInstance();
$section = $_GET['section'] ?? 'dashboard';
$user = getCurrentUser();

// ── Enrolled courses ────────────────────────────────────────────────────────
$enrolledCourses = $db->query(
    "SELECT c.* FROM courses c
     JOIN course_enrollments ce ON c.id = ce.course_id
     WHERE ce.student_id = ? AND ce.status = 'active' AND c.is_active = 1
     ORDER BY c.course_code",
    [$user['id']]
)->fetchAll();

$courseIds      = !empty($enrolledCourses) ? array_column($enrolledCourses, 'id') : [];
$placeholders   = !empty($courseIds) ? implode(',', array_fill(0, count($courseIds), '?')) : '0';

// ── Upcoming / available quizzes ────────────────────────────────────────────
$upcomingQuizzes = [];
if (!empty($courseIds)) {
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

$availableQuizzes = [];
if (!empty($courseIds)) {
    $availableQuizzes = $db->query(
        "SELECT q.*, c.course_code, c.course_name,
         (SELECT COUNT(*) FROM quiz_attempts
          WHERE quiz_id = q.id AND student_id = ?
          AND status IN ('submitted','graded','auto_submitted')) as has_attempted
         FROM quizzes q
         JOIN courses c ON q.course_id = c.id
         WHERE q.course_id IN ($placeholders) AND q.is_published = 1
           AND q.start_time <= NOW() AND q.end_time > NOW()
         ORDER BY q.end_time ASC",
        array_merge([$user['id']], $courseIds)
    )->fetchAll();
}

// ── Assignments ─────────────────────────────────────────────────────────────
$pendingAssignments = [];
if (!empty($courseIds)) {
    $pendingAssignments = $db->query(
        "SELECT a.*, c.course_code, c.course_name,
         (SELECT COUNT(*) FROM assignment_submissions
          WHERE assignment_id = a.id AND student_id = ?) as has_submitted
         FROM assignments a
         JOIN courses c ON a.course_id = c.id
         WHERE a.course_id IN ($placeholders) AND a.is_published = 1
           AND (a.deadline > NOW() OR a.late_submission_allowed = 1)
         ORDER BY a.deadline ASC",
        array_merge([$user['id']], $courseIds)
    )->fetchAll();
}

// ── Quiz results ─────────────────────────────────────────────────────────────
$quizResults = $db->query(
    "SELECT qa.*, q.title as quiz_title, q.total_marks, c.course_code
     FROM quiz_attempts qa
     JOIN quizzes q ON qa.quiz_id = q.id
     JOIN courses c ON q.course_id = c.id
     WHERE qa.student_id = ? AND qa.status IN ('submitted','graded','auto_submitted')
     ORDER BY qa.submitted_at DESC",
    [$user['id']]
)->fetchAll();

// ── Assignment grades ─────────────────────────────────────────────────────────
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

// ── Dashboard stats ──────────────────────────────────────────────────────────
$stats = [
    'courses'             => count($enrolledCourses),
    'upcoming_quizzes'    => count($upcomingQuizzes),
    'pending_assignments' => count(array_filter($pendingAssignments, fn($a) => !$a['has_submitted'])),
    'avg_score'           => $db->query(
        "SELECT AVG(percentage) as avg FROM quiz_attempts
         WHERE student_id = ? AND status IN ('submitted','graded')",
        [$user['id']]
    )->fetch()['avg'] ?? 0,
];

// ── Quiz execution ───────────────────────────────────────────────────────────
$quizDetail    = null;
$quizQuestions = [];
if ($section === 'take_quiz' && isset($_GET['id'])) {
    $quizId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($quizId) {
        $quizDetail = $db->query(
            "SELECT q.*, c.course_name FROM quizzes q
             JOIN courses c ON q.course_id = c.id
             WHERE q.id = ? AND q.is_published = 1",
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
            unset($q);
        }
    }
}

// ── Gradebook (merged) ───────────────────────────────────────────────────────
$gradebookData = [];
if ($section === 'grades') {
    foreach ($enrolledCourses as $course) {
        $cQuizzes = $db->query(
            "SELECT q.title, qa.score, qa.percentage, qa.submitted_at, q.total_marks
             FROM quizzes q
             LEFT JOIN quiz_attempts qa ON q.id = qa.quiz_id AND qa.student_id = ?
             WHERE q.course_id = ? AND q.is_published = 1
               AND qa.status IN ('submitted','graded','auto_submitted')",
            [$user['id'], $course['id']]
        )->fetchAll();
        $cAssignments = $db->query(
            "SELECT a.title, asub.marks_obtained, asub.status, a.max_marks, asub.feedback
             FROM assignments a
             LEFT JOIN assignment_submissions asub
               ON a.id = asub.assignment_id AND asub.student_id = ?
             WHERE a.course_id = ? AND a.is_published = 1",
            [$user['id'], $course['id']]
        )->fetchAll();
        $gradebookData[] = ['course' => $course, 'quizzes' => $cQuizzes, 'assignments' => $cAssignments];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal — Faculty of Engineering · Benha University</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- Google Font: Inter for clean academic feel -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ── Global font override ── */
        body,
        .form-control,
        .btn,
        .card-title,
        .page-title {
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        /* ══════════════════════════════════════════
           TOAST NOTIFICATION SYSTEM
        ══════════════════════════════════════════ */
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 360px;
            width: 100%;
        }

        .toast {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: #ffffff;
            border-radius: 10px;
            padding: 14px 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12), 0 1px 4px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #3b82f6;
            animation: toastIn .3s cubic-bezier(.22, 1, .36, 1) both;
            pointer-events: all;
        }

        .toast.success {
            border-left-color: #22c55e;
        }

        .toast.error {
            border-left-color: #ef4444;
        }

        .toast.warning {
            border-left-color: #f59e0b;
        }

        .toast.info {
            border-left-color: #3b82f6;
        }

        .toast-icon {
            font-size: 1.2rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .toast-body {
            flex: 1;
            min-width: 0;
        }

        .toast-title {
            font-weight: 600;
            font-size: .88rem;
            color: #111827;
            margin-bottom: 2px;
        }

        .toast-msg {
            font-size: .82rem;
            color: #6b7280;
            line-height: 1.45;
        }

        .toast-close {
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            font-size: 1rem;
            padding: 0;
            line-height: 1;
            flex-shrink: 0;
            align-self: flex-start;
        }

        .toast-close:hover {
            color: #374151;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(20px) scale(.96);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes toastOut {
            to {
                opacity: 0;
                transform: translateX(20px) scale(.96);
            }
        }

        .toast.removing {
            animation: toastOut .25s ease forwards;
        }

        /* ══════════════════════════════════════════
           SIDEBAR — University branding
        ══════════════════════════════════════════ */
        .sidebar-brand {
            padding: 14px 20px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }

        /* .sidebar-brand-logo {
            display: block;
            width: 100%;
            max-height: 52px;
            object-fit: contain;
            object-position: left center;
            filter: brightness(0) invert(1);
            opacity: .92;
        } */

        /* ══════════════════════════════════════════
           TOPBAR — header strip with university crest
        ══════════════════════════════════════════ */
        .topbar-univ-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .78rem;
            color: var(--gray);
            font-weight: 500;
        }

        .topbar-univ-badge img {
            height: 28px;
            opacity: .75;
        }

        /* ══════════════════════════════════════════
           STAT CARDS — lighter elevation
        ══════════════════════════════════════════ */
        .stat-card {
            border: 1px solid #f0f0f0;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
            cursor: default;
        }

        .stat-card a {
            text-decoration: none;
            color: inherit;
            display: contents;
        }

        /* ══════════════════════════════════════════
           HERO WELCOME BANNER
        ══════════════════════════════════════════ */
        .student-hero {
            background: linear-gradient(120deg, #1e3a5f 0%, #2c5282 60%, #1a4a7a 100%);
            border-radius: 14px;
            padding: 28px 32px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
            overflow: hidden;
            position: relative;
        }

        .student-hero::before {
            content: '';
            position: absolute;
            right: -40px;
            top: -40px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, .04);
            border-radius: 50%;
        }

        .student-hero-text h2 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .student-hero-text p {
            font-size: .88rem;
            opacity: .82;
            margin: 0;
        }

        .student-hero-univ {
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: .85;
            font-size: .78rem;
            font-weight: 500;
            flex-shrink: 0;
        }

        .student-hero-univ img {
            height: 38px;
            filter: brightness(0) invert(1);
        }

        /* ══════════════════════════════════════════
           SECTION HEADERS inside content
        ══════════════════════════════════════════ */
        .section-eyebrow {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--primary);
            opacity: .7;
            margin-bottom: 4px;
        }

        /* ══════════════════════════════════════════
           ASSIGNMENT ROW — status pill refinement
        ══════════════════════════════════════════ */
        .pill-submitted {
            background: #dcfce7;
            color: #166534;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: .77rem;
            font-weight: 600;
        }

        .pill-pending {
            background: #fef3c7;
            color: #92400e;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: .77rem;
            font-weight: 600;
        }

        .pill-missed {
            background: #fee2e2;
            color: #991b1b;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: .77rem;
            font-weight: 600;
        }

        .pill-graded {
            background: #dbeafe;
            color: #1e40af;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: .77rem;
            font-weight: 600;
        }

        /* ══════════════════════════════════════════
           GRADES TABLE — score bar
        ══════════════════════════════════════════ */
        .score-bar-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .score-bar {
            flex: 1;
            height: 6px;
            background: #f0f0f0;
            border-radius: 3px;
            overflow: hidden;
        }

        .score-bar-fill {
            height: 100%;
            border-radius: 3px;
            transition: width .4s ease;
        }

        .fill-excellent {
            background: #22c55e;
        }

        .fill-good {
            background: #3b82f6;
        }

        .fill-average {
            background: #f59e0b;
        }

        .fill-poor {
            background: #ef4444;
        }

        /* ══════════════════════════════════════════
           QUIZ INTERFACE — cleaner card
        ══════════════════════════════════════════ */
        .quiz-question-card {
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 22px 24px;
            margin-bottom: 18px;
        }

        .quiz-question-card:focus-within {
            border-color: var(--primary);
        }

        /* ══════════════════════════════════════════
           COURSE CARD
        ══════════════════════════════════════════ */
        .course-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        /* ══════════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════════ */
        @media (max-width: 768px) {
            .student-hero {
                flex-direction: column;
                text-align: center;
            }

            .student-hero-univ {
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <!-- ── Toast Container ──────────────────────────────────────────────────── -->
    <div id="toast-container" aria-live="polite" aria-atomic="false"></div>

    <div class="dashboard-wrapper">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <div class="main-content">
            <!-- ── Topbar ──────────────────────────────────────────────────── -->
            <div class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" onclick="toggleSidebar()">&#9776;</button>
                    <h2 class="topbar-title">Student Portal</h2>
                </div>
                <div class="topbar-right" style="display:flex;align-items:center;gap:16px;">
                    <div class="topbar-univ-badge">
                        <img src="/assets/images/logo_benha.png" alt="Benha University" onerror="this.style.display='none'">
                        <span style="display:none" class="d-md-block">Benha University</span>
                    </div>
                    <span style="color:var(--gray);font-size:.88rem;font-weight:500;">
                        <?= htmlspecialchars($user['full_name']) ?>
                    </span>
                    <!-- Notification bell -->
                    <div class="notification-wrapper" style="position:relative;display:inline-block;">
                        <button class="topbar-icon-btn" id="notificationBtn" title="Notifications">
                            🔔 <span class="notification-badge" id="notificationCount" style="display:none;">0</span>
                        </button>
                        <div id="notificationDropdown" style="display:none;position:absolute;right:0;top:44px;width:310px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:1000;color:#333;text-align:left;">
                            <div style="padding:12px 16px;font-weight:600;border-bottom:1px solid #f0f0f0;font-size:.88rem;background:#f8f9fa;border-radius:10px 10px 0 0;">
                                Notifications
                            </div>
                            <div id="notificationList" style="max-height:280px;overflow-y:auto;font-size:.84rem;">
                                <div style="padding:18px;text-align:center;color:#9ca3af;">No new notifications</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ── End Topbar ──────────────────────────────────────────────── -->

            <div class="content-wrapper">
                <?= showFlashMessage() ?>

                <!-- ════════════════════════════════════════════════════════════
                 NOTIFICATIONS ARCHIVE
            ════════════════════════════════════════════════════════════ -->
                <?php if ($section === 'notifications'): ?>
                    <div class="page-header">
                        <div class="section-eyebrow">Archive</div>
                        <h1 class="page-title">All Notifications</h1>
                        <p class="page-subtitle">Your academic and system alerts in one place</p>
                        <a href="?section=dashboard" class="btn btn-sm btn-outline" style="margin-top:12px;">← Back to Dashboard</a>
                    </div>

                    <div class="card">
                        <div class="card-header"><span class="card-title">Notification History</span></div>
                        <?php
                        $allNotifs = $db->query(
                            "SELECT * FROM notifications WHERE user_id = ? OR role_target = ?
                             ORDER BY created_at DESC LIMIT 100",
                            [$user['id'], $user['role_name']]
                        )->fetchAll();
                        $db->query(
                            "UPDATE notifications SET is_read = 1 WHERE user_id = ? OR role_target = ?",
                            [$user['id'], $user['role_name']]
                        );
                        ?>
                        <div class="card-body" style="padding:0;">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Type</th>
                                            <th>Title</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allNotifs as $n):
                                            $tb = $n['type'] === 'security' ? 'badge-danger' : 'badge-primary';
                                            $st = $n['is_read'] == 0
                                                ? '<span style="color:#2563eb;">● New</span>'
                                                : '<span style="color:#9ca3af;">○ Read</span>';
                                        ?>
                                            <tr>
                                                <td><?= $st ?></td>
                                                <td><span class="badge <?= $tb ?>"><?= htmlspecialchars(ucfirst($n['type'])) ?></span></td>
                                                <td><strong><?= htmlspecialchars($n['title']) ?></strong></td>
                                                <td><?= htmlspecialchars($n['message']) ?></td>
                                                <td><?= htmlspecialchars($n['created_at']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($allNotifs)): ?>
                                            <tr>
                                                <td colspan="5" style="text-align:center;color:var(--gray);padding:40px;">Your notification archive is empty.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ════════════════════════════════════════════════════════════
                 DASHBOARD OVERVIEW
            ════════════════════════════════════════════════════════════ -->
                <?php if ($section === 'dashboard'): ?>
                    <!-- Welcome Hero -->
                    <div class="student-hero">
                        <div class="student-hero-text">
                            <h2>Welcome back, <?= htmlspecialchars(explode(' ', $user['full_name'])[0]) ?> 👋</h2>
                            <p>Here's your academic overview for today — <?= date('l, F j, Y') ?></p>
                        </div>
                        <div class="student-hero-univ">
                            <img src="/assets/images/bu_logo.png" alt="Benha University" onerror="this.style.display='none'">
                            <div>
                                <div style="font-weight:700;font-size:.85rem;">Benha University</div>
                                <div style="font-size:.72rem;opacity:.75;">Faculty of Engineering · Shubra</div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon blue">📚</div>
                            <div class="stat-info">
                                <h3><?= $stats['courses'] ?></h3>
                                <p>Enrolled Courses</p>
                            </div>
                        </div>
                        <div class="stat-card" style="cursor:pointer;" onclick="location.href='?section=quizzes'">
                            <div class="stat-icon orange">📝</div>
                            <div class="stat-info">
                                <h3><?= $stats['upcoming_quizzes'] ?></h3>
                                <p>Active Quizzes</p>
                            </div>
                        </div>
                        <div class="stat-card" style="cursor:pointer;" onclick="location.href='?section=assignments'">
                            <div class="stat-icon red">📂</div>
                            <div class="stat-info">
                                <h3><?= $stats['pending_assignments'] ?></h3>
                                <p>Assignments Due</p>
                            </div>
                        </div>
                        <div class="stat-card" style="cursor:pointer;" onclick="location.href='?section=grades'">
                            <div class="stat-icon green">🎓</div>
                            <div class="stat-info">
                                <h3><?= number_format($stats['avg_score'], 1) ?>%</h3>
                                <p>Overall Average</p>
                            </div>
                        </div>
                    </div>

                    <!-- Two-column widgets -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <!-- Upcoming Quizzes widget -->
                        <div class="card">
                            <div class="card-header">
                                <span class="card-title">📝 Upcoming Quizzes</span>
                                <a href="?section=quizzes" class="btn btn-sm btn-outline">View All</a>
                            </div>
                            <div class="card-body" style="padding:0;">
                                <ul class="todo-list">
                                    <?php foreach (array_slice($upcomingQuizzes, 0, 5) as $q):
                                        $dl = getDeadlineStatus($q['end_time']);
                                    ?>
                                        <li class="todo-item">
                                            <div class="todo-icon" style="background:#fff7ed;color:#c2410c;">📝</div>
                                            <div class="todo-info">
                                                <div class="todo-title"><?= htmlspecialchars($q['title']) ?></div>
                                                <div class="todo-meta"><?= htmlspecialchars($q['course_code']) ?> · <?= $q['duration_minutes'] ?> min</div>
                                            </div>
                                            <span class="deadline-badge deadline-<?= $dl['class'] ?>"><?= $dl['text'] ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                    <?php if (empty($upcomingQuizzes)): ?>
                                        <li style="padding:28px;text-align:center;color:var(--gray);">🎉 No upcoming quizzes</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <!-- Assignments Due widget -->
                        <div class="card">
                            <div class="card-header">
                                <span class="card-title">📂 Assignments Due</span>
                                <a href="?section=assignments" class="btn btn-sm btn-outline">View All</a>
                            </div>
                            <div class="card-body" style="padding:0;">
                                <ul class="todo-list">
                                    <?php
                                    $pendingOnly = array_filter($pendingAssignments, fn($a) => !$a['has_submitted']);
                                    foreach (array_slice($pendingOnly, 0, 5) as $a):
                                        $dl = getDeadlineStatus($a['deadline']);
                                    ?>
                                        <li class="todo-item">
                                            <div class="todo-icon" style="background:#fef2f2;color:#b91c1c;">📂</div>
                                            <div class="todo-info">
                                                <div class="todo-title"><?= htmlspecialchars($a['title']) ?></div>
                                                <div class="todo-meta"><?= htmlspecialchars($a['course_code']) ?></div>
                                            </div>
                                            <span class="deadline-badge deadline-<?= $dl['class'] ?>"><?= $dl['text'] ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                    <?php if (empty($pendingOnly)): ?>
                                        <li style="padding:28px;text-align:center;color:var(--gray);">✅ All caught up!</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Recent grades snapshot -->
                    <div class="card" style="margin-top:20px;">
                        <div class="card-header">
                            <span class="card-title">🏆 Recent Quiz Results</span>
                            <a href="?section=grades" class="btn btn-sm btn-outline">Full Gradebook</a>
                        </div>
                        <div class="card-body" style="padding:0;">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Quiz</th>
                                            <th>Course</th>
                                            <th>Score</th>
                                            <th>Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($quizResults, 0, 5) as $r):
                                            $pct = (float)($r['percentage'] ?? 0);
                                            $gc  = $pct >= 85 ? 'grade-excellent' : ($pct >= 70 ? 'grade-good' : ($pct >= 60 ? 'grade-average' : 'grade-poor'));
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($r['quiz_title']) ?></td>
                                                <td><?= htmlspecialchars($r['course_code']) ?></td>
                                                <td><?= $r['score'] !== null ? $r['score'] . '/' . $r['total_marks'] : '—' ?></td>
                                                <td class="<?= $gc ?>"><?= $pct ? number_format($pct, 1) . '%' : '—' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($quizResults)): ?>
                                            <tr>
                                                <td colspan="4" style="text-align:center;color:var(--gray);padding:24px;">No results yet — take your first quiz!</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ════════════════════════════════════════════════════════════
                 MY COURSES
            ════════════════════════════════════════════════════════════ -->
                <?php if ($section === 'courses'): ?>
                    <div class="page-header">
                        <div class="section-eyebrow">This Semester</div>
                        <h1 class="page-title">My Courses</h1>
                        <p class="page-subtitle"><?= count($enrolledCourses) ?> course<?= count($enrolledCourses) !== 1 ? 's' : '' ?> enrolled</p>
                    </div>

                    <div class="course-card-grid">
                        <?php foreach ($enrolledCourses as $c):
                            $doctor = $db->query(
                                "SELECT u.full_name FROM course_doctors cd JOIN users u ON cd.doctor_id = u.id WHERE cd.course_id = ?",
                                [$c['id']]
                            )->fetch();
                        ?>
                            <div class="card" style="overflow:hidden;">
                                <div style="background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff;padding:22px 24px;">
                                    <div style="font-size:.78rem;opacity:.75;font-weight:600;letter-spacing:.05em;text-transform:uppercase;">
                                        <?= htmlspecialchars($c['course_code']) ?>
                                    </div>
                                    <div style="font-size:1.15rem;font-weight:700;margin-top:6px;line-height:1.3;">
                                        <?= htmlspecialchars($c['course_name']) ?>
                                    </div>
                                </div>
                                <div style="padding:18px 20px;">
                                    <p style="color:var(--gray);font-size:.87rem;margin-bottom:14px;line-height:1.55;">
                                        <?= htmlspecialchars(substr($c['description'] ?? 'No description available.', 0, 110)) ?>…
                                    </p>
                                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:.84rem;">
                                        <span style="color:var(--gray);">Dr. <?= htmlspecialchars($doctor['full_name'] ?? 'N/A') ?></span>
                                        <span class="badge badge-primary"><?= $c['credit_hours'] ?> Credits</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($enrolledCourses)): ?>
                            <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--gray);">No courses found for your account.</div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- ════════════════════════════════════════════════════════════
                 QUIZZES
            ════════════════════════════════════════════════════════════ -->
                <?php if ($section === 'quizzes'): ?>
                    <div class="page-header">
                        <div class="section-eyebrow">Assessments</div>
                        <h1 class="page-title">Quizzes</h1>
                        <p class="page-subtitle">Available and upcoming exams for your enrolled courses</p>
                    </div>

                    <div class="card">
                        <div class="card-header"><span class="card-title">📝 Quiz Schedule</span></div>
                        <div class="card-body" style="padding:0;">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Quiz</th>
                                            <th>Course</th>
                                            <th>Duration</th>
                                            <th>Closes</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($upcomingQuizzes as $q):
                                            $dl         = getDeadlineStatus($q['end_time']);
                                            $hoursLeft  = max(0, round((strtotime($q['end_time']) - time()) / 3600, 1));
                                            $isUrgent   = $hoursLeft <= 24 && $hoursLeft > 0;
                                            $isLive     = strtotime($q['start_time']) <= time();
                                        ?>
                                            <tr style="<?= $isUrgent ? 'background:#fffbeb;' : '' ?>">
                                                <td>
                                                    <strong><?= htmlspecialchars($q['title']) ?></strong>
                                                    <?php if ($isUrgent): ?>
                                                        <span class="badge badge-danger" style="margin-left:6px;">⚡ <?= round($hoursLeft) ?>h left</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($q['course_code']) ?></td>
                                                <td><?= $q['duration_minutes'] ?> min</td>
                                                <td><span class="deadline-badge deadline-<?= $dl['class'] ?>"><?= $dl['text'] ?></span></td>
                                                <td>
                                                    <?php if ($q['has_attempted']): ?>
                                                        <span class="pill-submitted">✓ Completed</span>
                                                    <?php elseif (!$isLive): ?>
                                                        <span class="pill-pending">⏳ Not started yet</span>
                                                    <?php else: ?>
                                                        <span class="pill-pending">⏳ Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!$q['has_attempted'] && $isLive): ?>
                                                        <a href="?section=take_quiz&id=<?= $q['id'] ?>"
                                                            class="btn btn-sm btn-primary"
                                                            style="<?= $isUrgent ? 'background:var(--danger);' : '' ?>">
                                                            Start Quiz
                                                        </a>
                                                    <?php elseif ($q['has_attempted']): ?>
                                                        <span style="color:var(--gray);font-size:.84rem;">✓ Submitted</span>
                                                    <?php else: ?>
                                                        <span style="color:var(--gray);font-size:.84rem;">Opens <?= date('M j, g:i A', strtotime($q['start_time'])) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($upcomingQuizzes)): ?>
                                            <tr>
                                                <td colspan="6" style="text-align:center;color:var(--gray);padding:36px;">No quizzes available right now.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ════════════════════════════════════════════════════════════
                 ASSIGNMENTS LIST
            ════════════════════════════════════════════════════════════ -->
                <?php if ($section === 'assignments'): ?>
                    <div class="page-header">
                        <div class="section-eyebrow">Submissions</div>
                        <h1 class="page-title">Assignments</h1>
                        <p class="page-subtitle">Submit your work before the deadline</p>
                    </div>

                    <div class="card">
                        <div class="card-header"><span class="card-title">📂 Assignment Queue</span></div>
                        <div class="card-body" style="padding:0;">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Assignment</th>
                                            <th>Course</th>
                                            <th>Deadline</th>
                                            <th>Time Left</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingAssignments as $a):
                                            $dl        = getDeadlineStatus($a['deadline']);
                                            $hoursLeft = max(0, round((strtotime($a['deadline']) - time()) / 3600, 1));
                                            $isUrgent  = $hoursLeft <= 24 && $hoursLeft > 0;
                                            $isExpired = $hoursLeft <= 0 && !($a['late_submission_allowed'] ?? 0);
                                        ?>
                                            <tr style="<?= $isUrgent ? 'background:#fffbeb;' : ($isExpired ? 'background:#fef2f2;' : '') ?>">
                                                <td><strong><?= htmlspecialchars($a['title']) ?></strong></td>
                                                <td><?= htmlspecialchars($a['course_code']) ?></td>
                                                <td><span class="deadline-badge deadline-<?= $dl['class'] ?>"><?= $dl['text'] ?></span></td>
                                                <td>
                                                    <?php if ($isExpired): ?>
                                                        <span style="color:var(--danger);font-weight:600;">Closed</span>
                                                    <?php elseif ($hoursLeft <= 24): ?>
                                                        <span style="color:var(--danger);font-weight:600;">⏳ <?= round($hoursLeft) ?>h</span>
                                                    <?php else: ?>
                                                        <span style="color:var(--gray);">📅 <?= round($hoursLeft / 24, 1) ?> days</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($a['has_submitted']): ?>
                                                        <span class="pill-submitted">✓ Submitted</span>
                                                    <?php elseif ($isExpired): ?>
                                                        <span class="pill-missed">✗ Missed</span>
                                                    <?php else: ?>
                                                        <span class="pill-pending">⏳ Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!$a['has_submitted'] && !$isExpired): ?>
                                                        <a href="?section=submit_assignment_form&id=<?= $a['id'] ?>"
                                                            class="btn btn-sm btn-primary">
                                                            Submit Work
                                                        </a>
                                                    <?php elseif ($a['has_submitted']): ?>
                                                        <a href="?section=submit_assignment_form&id=<?= $a['id'] ?>"
                                                            class="btn btn-sm btn-outline">
                                                            View / Resubmit
                                                        </a>
                                                    <?php else: ?>
                                                        <span style="color:var(--gray);font-size:.84rem;">🔒 Closed</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($pendingAssignments)): ?>
                                            <tr>
                                                <td colspan="6" style="text-align:center;color:var(--gray);padding:36px;">🎉 No assignments due right now.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ════════════════════════════════════════════════════════════
                 TAKE QUIZ
            ════════════════════════════════════════════════════════════ -->
                <?php if ($section === 'take_quiz' && $quizDetail): ?>
                    <?php
                    $hasCompleted = $db->query(
                        "SELECT COUNT(*) as c FROM quiz_attempts
                     WHERE quiz_id = ? AND student_id = ?
                     AND status IN ('submitted','graded','auto_submitted')",
                        [$quizDetail['id'], $user['id']]
                    )->fetch()['c'];
                    ?>
                    <?php if ($hasCompleted > 0): ?>
                        <div class="card">
                            <div class="card-body" style="text-align:center;padding:60px;">
                                <div style="font-size:3rem;margin-bottom:16px;">✅</div>
                                <h3 style="color:var(--primary);margin-bottom:8px;">Quiz Already Submitted</h3>
                                <p style="color:var(--gray);margin-bottom:24px;">You have already completed this quiz. Your results are recorded.</p>
                                <a href="?section=grades" class="btn btn-primary">View My Grades</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="quiz-container" id="quizInterface">
                            <div class="quiz-header">
                                <div class="quiz-title"><?= htmlspecialchars($quizDetail['title']) ?></div>
                                <div style="font-size:.88rem;opacity:.8;margin:4px 0 12px;">
                                    <?= htmlspecialchars($quizDetail['course_name']) ?> · <?= $quizDetail['duration_minutes'] ?> minutes
                                </div>
                                <div class="quiz-timer" id="quizTimer">--:--</div>
                            </div>
                            <div class="quiz-body">
                                <form id="quizForm">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="quiz_id" value="<?= $quizDetail['id'] ?>">
                                    <?php foreach ($quizQuestions as $idx => $q): ?>
                                        <div class="quiz-question-card" data-question="<?= $q['id'] ?>">
                                            <div class="question-number">Question <?= $idx + 1 ?> of <?= count($quizQuestions) ?> · <?= $q['marks'] ?> mark<?= $q['marks'] != 1 ? 's' : '' ?></div>
                                            <div class="question-text"><?= htmlspecialchars($q['question_text']) ?></div>
                                            <div class="options-list">
                                                <?php foreach ($q['options'] as $opt): ?>
                                                    <label class="option-item" onclick="selectOption(this)">
                                                        <input type="radio" name="answers[<?= $q['id'] ?>]"
                                                            value="<?= htmlspecialchars($opt['option_text']) ?>" required>
                                                        <span><?= htmlspecialchars($opt['option_text']) ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitQuizBtn"
                                        style="width:100%;margin-top:8px;">
                                        Submit Quiz
                                    </button>
                                </form>
                            </div>
                        </div>
                   
<script>
/**
 * QuizTimer Class - Manages quiz countdown timer
 * Uses server-synced time to prevent client-side tampering
 */
class QuizTimer {
    constructor(durationMinutes, displayElement, onExpireCallback) {
        this.durationSeconds = durationMinutes * 60;
        this.displayElement = displayElement;
        this.onExpire = onExpireCallback;
        this.remainingSeconds = this.durationSeconds;
        this.intervalId = null;
        this.isRunning = false;
        this.hasExpired = false;
    }

    start() {
        if (this.isRunning) return;
        this.isRunning = true;
        this.render();
        this.intervalId = setInterval(() => this.tick(), 1000);
    }

    tick() {
        this.remainingSeconds--;
        this.render();

        if (this.remainingSeconds <= 0) {
            this.stop();
            this.hasExpired = true;
            if (this.onExpire && !this.onExpire._called) {
                this.onExpire._called = true;
                this.onExpire();
            }
        }
    }

    stop() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
        this.isRunning = false;
    }

    getRemainingSeconds() {
        return Math.max(0, this.remainingSeconds);
    }

    render() {
        const m = Math.floor(this.remainingSeconds / 60);
        const s = this.remainingSeconds % 60;
        const timeStr = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;

        if (this.displayElement) {
            this.displayElement.textContent = timeStr;

            // Visual warnings
            if (this.remainingSeconds <= 60) {
                this.displayElement.style.color = '#ef4444';
                this.displayElement.style.fontWeight = '700';
            } else if (this.remainingSeconds <= 300) {
                this.displayElement.style.color = '#f59e0b';
            }
        }

        // Update page title for visibility
        if (this.remainingSeconds <= 60 && this.remainingSeconds > 0) {
            document.title = `⏰ ${timeStr} - Quiz`;
        }
    }
}

// ── Quiz Interface Controller ──
(function() {
    const quizForm = document.getElementById('quizForm');
    const submitBtn = document.getElementById('submitQuizBtn');
    const timerDisplay = document.getElementById('quizTimer');

    if (!quizForm || !timerDisplay) return;

    // Configuration from PHP
    const durationMinutes = <?= (int)$quizDetail['duration_minutes'] ?>;
    const quizId = <?= (int)$quizDetail['id'] ?>;
    const totalMarks = <?= (float)$quizDetail['total_marks'] ?>;

    // Submission lock to prevent double-submit
    let isSubmitting = false;

    // Initialize timer
    const quizTimer = new QuizTimer(durationMinutes, timerDisplay, function() {
        if (!isSubmitting) {
            showToast("warning', 'Time's up!', 'Your quiz is being auto-submitted...");
            submitQuizAnswers(true);
        }
    });

    // Start timer immediately
    quizTimer.start();

    // Register attempt start with server
    const startData = new FormData();
    startData.append('action', 'start_attempt');
    startData.append('quiz_id', quizId);
    startData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

    fetch('/api/quizzes.php', {
        method: 'POST',
        body: startData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to start attempt:', data.message);
            showToast('error', 'Quiz Error', data.message || 'Could not start quiz attempt.');
        }
    })
    .catch(err => {
        console.error('Start attempt error:', err);
    });

    // Handle manual submit
    quizForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (isSubmitting) return;

        // Check if all questions answered
        const totalQuestions = quizForm.querySelectorAll('.quiz-question-card').length;
        const answeredQuestions = quizForm.querySelectorAll('input[type="radio"]:checked').length;

        let confirmMsg = 'Are you sure you want to submit?';
        if (answeredQuestions < totalQuestions) {
            confirmMsg = `You have answered ${answeredQuestions} of ${totalQuestions} questions.\n\n${confirmMsg}`;
        }

        if (confirm(confirmMsg)) {
            submitQuizAnswers(false);
        }
    });

    // Auto-submit function
    async function submitQuizAnswers(autoSubmitted) {
        if (isSubmitting) return;
        isSubmitting = true;

        quizTimer.stop();

        // Disable form
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="spinner"></div> Submitting…';
        quizForm.querySelectorAll('input').forEach(el => el.disabled = true);

        const formData = new FormData(quizForm);
        formData.append('action', 'submit_attempt');
        formData.append('time_remaining', quizTimer.getRemainingSeconds());
        formData.append('auto_submitted', autoSubmitted ? '1' : '0');

        try {
            const res = await fetch('/api/quizzes.php', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                const scoreText = data.score !== undefined 
                    ? `Your score: ${data.score} / ${totalMarks} (${data.percentage}%)`
                    : 'Quiz submitted successfully!';

                showToast('success', 'Quiz Submitted!', scoreText);

                // Redirect after delay
                setTimeout(() => {
                    window.location.href = '?section=grades';
                }, 2000);
            } else {
                isSubmitting = false;
                showToast('error', 'Submission Failed', data.message || 'Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Submit Quiz';
                quizForm.querySelectorAll('input').forEach(el => el.disabled = false);

                // Restart timer if not expired
                if (!quizTimer.hasExpired) {
                    quizTimer.start();
                }
            }
        } catch (err) {
            isSubmitting = false;
            console.error('Submit error:', err);
            showToast('error', 'Connection Error', 'Check your internet and try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Quiz';
            quizForm.querySelectorAll('input').forEach(el => el.disabled = false);

            if (!quizTimer.hasExpired) {
                quizTimer.start();
            }
        }
    }

    // Warn before leaving page
    window.addEventListener('beforeunload', function(e) {
        if (!isSubmitting && quizTimer.isRunning && quizTimer.remainingSeconds > 0) {
            e.preventDefault();
            e.returnValue = 'You have an active quiz in progress. Are you sure you want to leave?';
            return e.returnValue;
        }
    });

    // Option selection handler
    window.selectOption = function(el) {
        const card = el.closest('.quiz-question-card');
        if (card) {
            card.querySelectorAll('.option-item').forEach(o => o.classList.remove('selected'));
        }
        el.classList.add('selected');

        // Auto-check the radio
        const radio = el.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    };
})();
</script>
                    <?php endif; ?>

                <?php elseif ($section === 'take_quiz'): ?>
                    <div class="page-header">
                        <h1 class="page-title">Quiz Not Found</h1>
                        <a href="?section=quizzes" class="btn btn-outline btn-sm" style="margin-top:12px;">← Back to Quizzes</a>
                    </div>
                    <div class="card">
                        <div class="card-body" style="text-align:center;padding:40px;color:var(--gray);">
                            This quiz could not be found or is no longer available.
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ════════════════════════════════════════════════════════════
                 ASSIGNMENT SUBMISSION FORM
            ════════════════════════════════════════════════════════════ -->
                <?php if ($section === 'submit_assignment_form' && isset($_GET['id'])): ?>
                    <?php
                    $assignmentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                    $assignment   = $assignmentId ? $db->query(
                        "SELECT a.*, c.course_code, c.course_name FROM assignments a
                     JOIN courses c ON a.course_id = c.id
                     WHERE a.id = ? AND a.is_published = 1",
                        [$assignmentId]
                    )->fetch() : null;

                    if ($assignment):
                        $existingSub      = $db->query(
                            "SELECT * FROM assignment_submissions WHERE assignment_id = ? AND student_id = ?",
                            [$assignmentId, $user['id']]
                        )->fetch();
                        $assignmentFiles  = $db->query(
                            "SELECT * FROM assignment_files WHERE assignment_id = ? ORDER BY uploaded_at DESC",
                            [$assignmentId]
                        )->fetchAll();
                        $hoursLeft        = max(0, round((strtotime($assignment['deadline']) - time()) / 3600, 1));
                        $canSubmit        = ($hoursLeft > 0) || ($assignment['late_submission_allowed'] ?? 0);
                    ?>
                        <div class="page-header">
                            <div class="section-eyebrow">Submission Workspace</div>
                            <h1 class="page-title"><?= htmlspecialchars($assignment['title']) ?></h1>
                            <p class="page-subtitle"><?= htmlspecialchars($assignment['course_name']) ?> · Max <?= $assignment['max_marks'] ?> marks</p>
                            <a href="?section=assignments" class="btn btn-outline btn-sm" style="margin-top:12px;">← Back to Assignments</a>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;">
                            <!-- Left: Details panel -->
                            <div class="card">
                                <div class="card-header"><span class="card-title">📋 Details</span></div>
                                <div class="card-body">
                                    <p><strong>Course:</strong> <?= htmlspecialchars($assignment['course_code']) ?></p>
                                    <p><strong>Max Grade:</strong> <?= $assignment['max_marks'] ?> marks</p>
                                    <p><strong>Max File Size:</strong> <?= $assignment['max_file_size_mb'] ?? 10 ?> MB</p>
                                    <p><strong>Deadline:</strong>
                                        <?= date('M j, Y · g:i A', strtotime($assignment['deadline'])) ?>
                                    </p>

                                    <?php if (!empty($assignment['description'])): ?>
                                        <div style="margin-top:14px;padding:12px;background:#f8f9fa;border-radius:8px;font-size:.88rem;">
                                            <strong>Description</strong><br>
                                            <?= htmlspecialchars($assignment['description']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($assignment['instructions'])): ?>
                                        <div style="margin-top:12px;padding:12px;background:#eff6ff;border-left:4px solid var(--primary);border-radius:6px;font-size:.87rem;">
                                            <strong style="color:var(--primary);">Instructions</strong><br>
                                            <?= nl2br(htmlspecialchars($assignment['instructions'])) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($assignmentFiles)): ?>
                                        <div style="margin-top:18px;">
                                            <strong style="font-size:.85rem;color:var(--primary);display:block;margin-bottom:8px;">📄 Attached Resources</strong>
                                            <?php foreach ($assignmentFiles as $f): ?>
                                                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px;background:#fff5f5;border:1px solid #fecaca;border-radius:8px;margin-bottom:6px;">
                                                    <span style="font-size:.82rem;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:130px;">
                                                        <?= htmlspecialchars($f['file_name']) ?>
                                                    </span>
                                                    <div style="display:flex;gap:4px;flex-shrink:0;">
                                                        <a href="/<?= htmlspecialchars($f['file_path']) ?>" target="_blank"
                                                            class="btn btn-sm btn-outline" style="padding:4px 8px;font-size:.75rem;">View</a>
                                                        <a href="/<?= htmlspecialchars($f['file_path']) ?>" download
                                                            class="btn btn-sm btn-primary" style="padding:4px 8px;font-size:.75rem;">Save</a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Right: Submission form -->
                            <div class="card">
                                <div class="card-header">
                                    <span class="card-title">
                                        <?= $existingSub ? '🔄 Update Submission' : '📤 Submit Your Work' ?>
                                    </span>
                                    <?php if ($existingSub): ?>
                                        <span class="pill-submitted">Submitted</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php if (!$canSubmit): ?>
                                        <div style="text-align:center;padding:40px;color:var(--danger);">
                                            <div style="font-size:2.5rem;margin-bottom:12px;">🔒</div>
                                            <strong>Submissions are closed for this assignment.</strong>
                                        </div>
                                    <?php else: ?>
                                        <form id="submitAssignmentForm" enctype="multipart/form-data">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="assignment_id" value="<?= $assignmentId ?>">

                                            <div class="form-group">
                                                <label class="form-label">Text Response <span style="color:var(--gray);font-weight:400;">(optional)</span></label>
                                                <textarea name="submission_text" class="form-control" rows="5"
                                                    placeholder="Add any notes, comments, or a short written response here…"><?= $existingSub ? htmlspecialchars($existingSub['submission_text'] ?? '') : '' ?></textarea>
                                            </div>

                                            <?php if ($existingSub && $existingSub['file_path']): ?>
                                                <div class="form-group">
                                                    <label class="form-label">Current Attachment</label>
                                                    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;">
                                                        <div>
                                                            <strong style="font-size:.88rem;"><?= htmlspecialchars($existingSub['file_name']) ?></strong>
                                                            <div style="font-size:.75rem;color:var(--gray);">
                                                                Submitted <?= timeAgo($existingSub['submitted_at']) ?>
                                                            </div>
                                                        </div>
                                                        <a href="/<?= htmlspecialchars($existingSub['file_path']) ?>" download
                                                            class="btn btn-sm btn-primary">Download</a>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="form-group">
                                                <label class="form-label">
                                                    Upload File
                                                    <span style="color:var(--gray);font-weight:400;">
                                                        (PDF, ZIP, DOCX · max <?= $assignment['max_file_size_mb'] ?? 10 ?>MB)
                                                    </span>
                                                </label>
                                                <div class="upload-zone" id="uploadZone">
                                                    <div class="upload-zone-icon">📁</div>
                                                    <div class="upload-zone-text">Click or drag a file here</div>
                                                    <div class="upload-zone-hint">Max <?= $assignment['max_file_size_mb'] ?? 10 ?>MB</div>
                                                    <input type="file" name="submission_file" id="submissionFile" style="display:none;"
                                                        onchange="handleFileSelect(this, <?= ($assignment['max_file_size_mb'] ?? 10) * 1024 * 1024 ?>)">
                                                </div>
                                                <div id="fileList" style="margin-top:10px;"></div>
                                            </div>

                                            <button type="submit" class="btn btn-primary" id="submitBtn" style="width:100%;">
                                                <?= $existingSub ? 'Update Submission' : 'Submit Assignment' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <script>
                            function handleFileSelect(input, maxSize) {
                                const file = input.files[0];
                                const fileList = document.getElementById('fileList');
                                if (!file) return;
                                if (file.size > maxSize) {
                                    showToast('error', 'File too large', 'Maximum allowed size is ' + (maxSize / 1048576).toFixed(0) + 'MB.');
                                    input.value = '';
                                    fileList.innerHTML = '';
                                    return;
                                }
                                fileList.innerHTML = `<div style="padding:10px;background:#eff6ff;border-radius:8px;font-size:.85rem;">
                        📎 <strong>${file.name}</strong> (${(file.size/1024).toFixed(1)} KB) — ready to upload
                    </div>`;
                            }

                            document.getElementById('submitAssignmentForm').addEventListener('submit', async function(e) {
                                e.preventDefault();
                                const btn = document.getElementById('submitBtn');
                                btn.disabled = true;
                                btn.innerHTML = '<div class="spinner"></div> Uploading…';

                                const formData = new FormData(this);
                                formData.append('action', 'submit');

                                try {
                                    const res = await fetch('/api/assignments.php', {
                                        method: 'POST',
                                        body: formData
                                    });
                                    const data = await res.json();
                                    if (data.success) {
                                        showToast('success', 'Submitted!', data.message);
                                        setTimeout(() => window.location.reload(), 1400);
                                    } else {
                                        showToast('error', 'Submission failed', data.message);
                                        btn.disabled = false;
                                        btn.innerHTML = '<?= $existingSub ? "Update Submission" : "Submit Assignment" ?>';
                                    }
                                } catch (err) {
                                    showToast('error', 'Connection error', 'Please check your connection and try again.');
                                    btn.disabled = false;
                                    btn.innerHTML = '<?= $existingSub ? "Update Submission" : "Submit Assignment" ?>';
                                }
                            });
                        </script>
                    <?php else: ?>
                        <div class="page-header">
                            <h1 class="page-title">Assignment Not Found</h1>
                            <a href="?section=assignments" class="btn btn-outline btn-sm" style="margin-top:12px;">← Back</a>
                        </div>
                        <div class="card">
                            <div class="card-body" style="text-align:center;padding:40px;color:var(--gray);">
                                This assignment could not be found or is not available.
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- ════════════════════════════════════════════════════════════
                 GRADES (merged Gradebook + My Grades)
            ════════════════════════════════════════════════════════════ -->
                <?php if ($section === 'grades' || $section === 'gradebook'): ?>
                    <div class="page-header">
                        <div class="section-eyebrow">Academic Record</div>
                        <h1 class="page-title">My Grades</h1>
                        <p class="page-subtitle">Full performance record across all enrolled courses</p>
                    </div>

                    <!-- Overall summary row -->
                    <div class="stats-grid" style="margin-bottom:24px;">
                        <?php
                        $totalQuizAttempts = count($quizResults);
                        $gradedAssignments = array_filter($assignmentGrades, fn($g) => $g['status'] === 'graded');
                        $overallAvg        = $stats['avg_score'];
                        $gradeLabel        = $overallAvg >= 85 ? 'Excellent' : ($overallAvg >= 70 ? 'Good' : ($overallAvg >= 60 ? 'Average' : 'Needs Improvement'));
                        ?>
                        <div class="stat-card">
                            <div class="stat-icon blue">📝</div>
                            <div class="stat-info">
                                <h3><?= $totalQuizAttempts ?></h3>
                                <p>Quizzes Taken</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon green">📂</div>
                            <div class="stat-info">
                                <h3><?= count($gradedAssignments) ?></h3>
                                <p>Assignments Graded</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon orange">🏆</div>
                            <div class="stat-info">
                                <h3><?= number_format($overallAvg, 1) ?>%</h3>
                                <p>Quiz Average · <?= $gradeLabel ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Per-course breakdown -->
                    <?php foreach ($gradebookData as $gd): ?>
                        <div class="card" style="margin-bottom:22px;">
                            <div class="card-header">
                                <span class="card-title">
                                    📘 <?= htmlspecialchars($gd['course']['course_code'] . ' — ' . $gd['course']['course_name']) ?>
                                </span>
                            </div>
                            <div class="card-body" style="padding:0;">
                                <!-- Tabs inside card -->
                                <div style="padding:16px 20px 0;">
                                    <div class="tabs" style="border-bottom:2px solid #f0f0f0;">
                                        <button class="tab-btn active" data-tab="q-<?= $gd['course']['id'] ?>">Quizzes (<?= count($gd['quizzes']) ?>)</button>
                                        <button class="tab-btn" data-tab="a-<?= $gd['course']['id'] ?>">Assignments (<?= count($gd['assignments']) ?>)</button>
                                    </div>
                                </div>

                                <div id="tab-q-<?= $gd['course']['id'] ?>" class="tab-content active" style="padding:0 0 4px;">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>Quiz</th>
                                                <th>Score</th>
                                                <th>Grade</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($gd['quizzes'] as $qr):
                                                $pct = (float)($qr['percentage'] ?? 0);
                                                $fc  = $pct >= 85 ? 'fill-excellent' : ($pct >= 70 ? 'fill-good' : ($pct >= 60 ? 'fill-average' : 'fill-poor'));
                                                $gc  = $pct >= 85 ? 'grade-excellent' : ($pct >= 70 ? 'grade-good' : ($pct >= 60 ? 'grade-average' : 'grade-poor'));
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($qr['title']) ?></td>
                                                    <td><?= $qr['score'] !== null ? $qr['score'] . '/' . $qr['total_marks'] : '—' ?></td>
                                                    <td>
                                                        <?php if ($pct): ?>
                                                            <div class="score-bar-wrap">
                                                                <span class="<?= $gc ?>" style="font-weight:600;width:42px;flex-shrink:0;"><?= number_format($pct, 1) ?>%</span>
                                                                <div class="score-bar">
                                                                    <div class="score-bar-fill <?= $fc ?>" style="width:<?= min($pct, 100) ?>%;"></div>
                                                                </div>
                                                            </div>
                                                        <?php else: echo '—';
                                                        endif; ?>
                                                    </td>
                                                    <td style="color:var(--gray);font-size:.83rem;">
                                                        <?= $qr['submitted_at'] ? date('M j, Y', strtotime($qr['submitted_at'])) : '—' ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($gd['quizzes'])): ?>
                                                <tr>
                                                    <td colspan="4" style="text-align:center;color:var(--gray);padding:24px;">No quiz results yet.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div id="tab-a-<?= $gd['course']['id'] ?>" class="tab-content" style="padding:0 0 4px;">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>Assignment</th>
                                                <th>Status</th>
                                                <th>Marks</th>
                                                <th>Feedback</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($gd['assignments'] as $ag): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($ag['title']) ?></td>
                                                    <td>
                                                        <?php if ($ag['status'] === 'graded'): ?>
                                                            <span class="pill-graded">Graded</span>
                                                        <?php elseif ($ag['marks_obtained'] !== null): ?>
                                                            <span class="pill-submitted">Submitted</span>
                                                        <?php else: ?>
                                                            <span class="pill-pending">Pending</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($ag['marks_obtained'] !== null): ?>
                                                            <strong><?= $ag['marks_obtained'] ?></strong> / <?= $ag['max_marks'] ?>
                                                        <?php else: echo '—';
                                                        endif; ?>
                                                    </td>
                                                    <td style="font-size:.84rem;color:var(--gray);">
                                                        <?= $ag['feedback'] ? htmlspecialchars($ag['feedback']) : '<em>No feedback yet</em>' ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($gd['assignments'])): ?>
                                                <tr>
                                                    <td colspan="4" style="text-align:center;color:var(--gray);padding:24px;">No assignments in this course yet.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($gradebookData)): ?>
                        <div class="card">
                            <div class="card-body" style="text-align:center;padding:60px;color:var(--gray);">
                                No grade data available. Enroll in courses and complete assessments to see your record here.
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            </div><!-- .content-wrapper -->
        </div><!-- .main-content -->
    </div><!-- .dashboard-wrapper -->

    <script src="/assets/js/main.js"></script>
    <script>
        /* ══════════════════════════════════════════════════════════════════
   TOAST SYSTEM — replaces all alert() calls
══════════════════════════════════════════════════════════════════ */
        function showToast(type, title, message, duration) {
            duration = duration || 4500;
            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };
            const container = document.getElementById('toast-container');

            const toast = document.createElement('div');
            toast.className = 'toast ' + type;
            toast.innerHTML = `
        <span class="toast-icon">${icons[type] || 'ℹ️'}</span>
        <div class="toast-body">
            <div class="toast-title">${title}</div>
            ${message ? `<div class="toast-msg">${message}</div>` : ''}
        </div>
        <button class="toast-close" onclick="dismissToast(this.parentElement)">×</button>
    `;
            container.appendChild(toast);

            setTimeout(() => dismissToast(toast), duration);
            return toast;
        }

        function dismissToast(el) {
            if (!el || el.classList.contains('removing')) return;
            el.classList.add('removing');
            setTimeout(() => el && el.remove(), 280);
        }

        // Override the global showNotification used by main.js to route through toasts
        window.showNotification = function(message, type) {
            const map = {
                success: 'success',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };
            showToast(map[type] || 'info', type === 'error' ? 'Error' : type === 'success' ? 'Done' : 'Notice', message);
        };
    </script>
</body>

</html>