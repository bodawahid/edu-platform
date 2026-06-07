<?php
/**
 * Admin Dashboard
 */

require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');

$db = Database::getInstance();
$section = $_GET['section'] ?? 'dashboard';
$user = getCurrentUser();

// Statistics
$stats = [
    'total_users' => $db->query("SELECT COUNT(*) as c FROM users")->fetch()['c'],
    'doctors' => $db->query("SELECT COUNT(*) as c FROM users WHERE role_id = 2")->fetch()['c'],
    'tas' => $db->query("SELECT COUNT(*) as c FROM users WHERE role_id = 3")->fetch()['c'],
    'students' => $db->query("SELECT COUNT(*) as c FROM users WHERE role_id = 4")->fetch()['c'],
    'courses' => $db->query("SELECT COUNT(*) as c FROM courses")->fetch()['c'],
    'quizzes' => $db->query("SELECT COUNT(*) as c FROM quizzes")->fetch()['c'],
];

// Security stats for AI Shield
$securityStats = [
    'total_attacks' => $db->query("SELECT COUNT(*) as c FROM security_logs")->fetch()['c'],
    'blocked_today' => $db->query("SELECT COUNT(*) as c FROM security_logs WHERE DATE(created_at) = CURDATE()")->fetch()['c'],
    'critical' => $db->query("SELECT COUNT(*) as c FROM security_logs WHERE severity = 'critical'")->fetch()['c'],
    'sqli' => $db->query("SELECT COUNT(*) as c FROM security_logs WHERE attack_type = 'SQL Injection'")->fetch()['c'],
    'xss' => $db->query("SELECT COUNT(*) as c FROM security_logs WHERE attack_type = 'XSS'")->fetch()['c'],
];

// Fetch lists
$users = $db->query("SELECT u.*, r.role_name, r.display_name as role_display FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id")->fetchAll();
$courses = $db->query("SELECT c.*, u.full_name as creator_name FROM courses c JOIN users u ON c.created_by = u.id ORDER BY c.id")->fetchAll();
$roles = $db->query("SELECT * FROM roles ORDER BY id")->fetchAll();

// Security logs
$securityLogs = $db->query("SELECT * FROM security_logs ORDER BY created_at DESC LIMIT 50")->fetchAll();

// Attack type distribution for chart
$attackDist = $db->query("SELECT attack_type, COUNT(*) as count FROM security_logs GROUP BY attack_type ORDER BY count DESC")->fetchAll();

// Daily attack timeline
$attackTimeline = $db->query("SELECT DATE(created_at) as date, COUNT(*) as count FROM security_logs GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 14")->fetchAll();
$attackTimeline = array_reverse($attackTimeline);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Faculty of Engineering</title>
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
                    <h2 class="topbar-title">Admin Dashboard</h2>
                </div>
                <div class="topbar-right">
                    <button class="topbar-icon-btn">
                        &#128276;
                        <span class="notification-badge"><?= $securityStats['blocked_today'] ?></span>
                    </button>
                </div>
            </div>

            <div class="content-wrapper">
                <?= showFlashMessage() ?>

                <!-- Dashboard Overview -->
                <?php if ($section === 'dashboard'): ?>
                <div class="page-header">
                    <h1 class="page-title">Dashboard Overview</h1>
                    <p class="page-subtitle">Welcome back, <?= htmlspecialchars($user['full_name']) ?></p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">&#128101;</div>
                        <div class="stat-info">
                            <h3><?= $stats['total_users'] ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">&#128104;&#8205;&#9877;&#65039;</div>
                        <div class="stat-info">
                            <h3><?= $stats['doctors'] ?></h3>
                            <p>Doctors</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon teal">&#128105;&#8205;&#127891;</div>
                        <div class="stat-info">
                            <h3><?= $stats['tas'] ?></h3>
                            <p>Teaching Assistants</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">&#127891;</div>
                        <div class="stat-info">
                            <h3><?= $stats['students'] ?></h3>
                            <p>Students</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">&#128218;</div>
                        <div class="stat-info">
                            <h3><?= $stats['courses'] ?></h3>
                            <p>Courses</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red">&#128221;</div>
                        <div class="stat-info">
                            <h3><?= $stats['quizzes'] ?></h3>
                            <p>Quizzes</p>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Recent Security Events</span>
                            <a href="?section=security" class="btn btn-sm btn-outline">View All</a>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr><th>Time</th><th>Type</th><th>Severity</th><th>IP</th><th>Status</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($securityLogs, 0, 8) as $log):
                                            $severityClass = match($log['severity']) {
                                                'critical' => 'badge-danger',
                                                'high' => 'badge-warning',
                                                'medium' => 'badge-info',
                                                default => 'badge-secondary'
                                            };
                                        ?>
                                        <tr>
                                            <td><?= timeAgo($log['created_at']) ?></td>
                                            <td><?= htmlspecialchars($log['attack_type']) ?></td>
                                            <td><span class="badge <?= $severityClass ?>"><?= ucfirst($log['severity']) ?></span></td>
                                            <td><?= htmlspecialchars($log['ip_address']) ?></td>
                                            <td><span class="badge badge-success"><?= ucfirst($log['action_taken']) ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Attack Distribution</span>
                        </div>
                        <div class="card-body">
                            <canvas id="attackChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card" style="margin-top: 20px;">
                    <div class="card-header">
                        <span class="card-title">Quick Actions</span>
                    </div>
                    <div class="card-body" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
                        <a href="?section=users" class="btn btn-outline" style="justify-content: flex-start; gap: 10px;">
                            <span>&#128101;</span> Manage Users
                        </a>
                        <a href="?section=courses" class="btn btn-outline" style="justify-content: flex-start; gap: 10px;">
                            <span>&#128218;</span> Manage Courses
                        </a>
                        <a href="?section=security" class="btn btn-outline" style="justify-content: flex-start; gap: 10px;">
                            <span>&#128272;</span> Security Shield
                        </a>
                        <a href="/" class="btn btn-outline" style="justify-content: flex-start; gap: 10px;">
                            <span>&#127968;</span> View Site
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Users Management -->
                <?php if ($section === 'users'): ?>
                <div class="page-header">
                    <h1 class="page-title">User Management</h1>
                    <p class="page-subtitle">Manage all system users</p>
                    <button class="btn btn-primary" onclick="openModal('addUserModal')" style="margin-top: 12px;">
                        <span>&#10133;</span> Add User
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="form-group" style="max-width: 400px;">
                            <input type="text" id="userSearch" class="form-control" placeholder="Search users...">
                        </div>
                        <div class="table-container" style="margin-top: 16px;">
                            <table class="data-table" id="usersTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $u):
                                        $statusBadge = $u['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
                                    ?>
                                    <tr>
                                        <td><?= $u['id'] ?></td>
                                        <td><?= htmlspecialchars($u['full_name']) ?></td>
                                        <td><?= htmlspecialchars($u['username']) ?></td>
                                        <td><?= htmlspecialchars($u['email']) ?></td>
                                        <td><span class="badge badge-primary"><?= htmlspecialchars($u['role_display']) ?></span></td>
                                        <td><?= htmlspecialchars($u['department'] ?? '-') ?></td>
                                        <td><?= $statusBadge ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="editUser(<?= $u['id'] ?>)" title="Edit">&#9998;</button>
                                            <?php if ($u['id'] != $user['id']): ?>
                                            <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $u['id'] ?>)" title="Delete">&#128465;</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Courses Management -->
                <?php if ($section === 'courses'): ?>
                <div class="page-header">
                    <h1 class="page-title">Course Management</h1>
                    <p class="page-subtitle">Manage courses and assignments</p>
                    <button class="btn btn-primary" onclick="openModal('addCourseModal')" style="margin-top: 12px;">
                        <span>&#10133;</span> Add Course
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Semester</th>
                                        <th>Year</th>
                                        <th>Credits</th>
                                        <th>Created By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $c): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($c['course_code']) ?></strong></td>
                                        <td><?= htmlspecialchars($c['course_name']) ?></td>
                                        <td><?= htmlspecialchars($c['department']) ?></td>
                                        <td><?= htmlspecialchars($c['semester']) ?></td>
                                        <td><?= $c['year'] ?></td>
                                        <td><?= $c['credit_hours'] ?></td>
                                        <td><?= htmlspecialchars($c['creator_name']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="editCourse(<?= $c['id'] ?>)" title="Edit">&#9998;</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteCourse(<?= $c['id'] ?>)" title="Delete">&#128465;</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Course Assignment Matrix -->
                <div class="card" style="margin-top: 20px;">
                    <div class="card-header">
                        <span class="card-title">Course Assignment Matrix</span>
                    </div>
                    <div class="card-body">
                        <div class="tabs">
                            <button class="tab-btn active" data-tab="doctors">Doctors</button>
                            <button class="tab-btn" data-tab="tas">Teaching Assistants</button>
                        </div>

                        <div id="tab-doctors" class="tab-content active">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr><th>Course</th><th>Doctor</th><th>Department</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $courseDocs = $db->query("SELECT c.course_code, c.course_name, u.full_name, c.department FROM course_doctors cd JOIN courses c ON cd.course_id = c.id JOIN users u ON cd.doctor_id = u.id ORDER BY c.course_code")->fetchAll();
                                        foreach ($courseDocs as $cd):
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($cd['course_code'] . ' - ' . $cd['course_name']) ?></td>
                                            <td><?= htmlspecialchars($cd['full_name']) ?></td>
                                            <td><?= htmlspecialchars($cd['department']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="tab-tas" class="tab-content">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr><th>Course</th><th>TA</th><th>Assigned By</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $courseTas = $db->query("SELECT c.course_code, c.course_name, u.full_name as ta_name, d.full_name as assigned_by FROM course_tas ct JOIN courses c ON ct.course_id = c.id JOIN users u ON ct.ta_id = u.id JOIN users d ON ct.assigned_by = d.id ORDER BY c.course_code")->fetchAll();
                                        foreach ($courseTas as $ct):
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($ct['course_code'] . ' - ' . $ct['course_name']) ?></td>
                                            <td><?= htmlspecialchars($ct['ta_name']) ?></td>
                                            <td><?= htmlspecialchars($ct['assigned_by']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- AI Security Shield -->
                <?php if ($section === 'security'): ?>
                <div class="page-header">
                    <h1 class="page-title">AI Security Shield</h1>
                    <p class="page-subtitle">Real-time threat detection and analytics</p>
                </div>

                <!-- Shield Panel -->
                <div class="shield-panel">
                    <div class="shield-header">
                        <div class="shield-icon">&#128272;</div>
                        <div>
                            <div class="shield-title">AI-Powered Security Shield</div>
                            <div class="shield-status">
                                <?= $securityStats['total_attacks'] ?> threats detected &middot;
                                <?= $securityStats['blocked_today'] ?> blocked today &middot;
                                All systems protected
                            </div>
                        </div>
                    </div>
                    <div class="shield-stats">
                        <div class="shield-stat">
                            <div class="shield-stat-value" style="color: #ff6b6b;"><?= $securityStats['total_attacks'] ?></div>
                            <div class="shield-stat-label">Total Threats</div>
                        </div>
                        <div class="shield-stat">
                            <div class="shield-stat-value" style="color: #ffd93d;"><?= $securityStats['sqli'] ?></div>
                            <div class="shield-stat-label">SQL Injection</div>
                        </div>
                        <div class="shield-stat">
                            <div class="shield-stat-value" style="color: #6bcb77;"><?= $securityStats['xss'] ?></div>
                            <div class="shield-stat-label">XSS Attacks</div>
                        </div>
                        <div class="shield-stat">
                            <div class="shield-stat-value" style="color: #ff6b6b;"><?= $securityStats['critical'] ?></div>
                            <div class="shield-stat-label">Critical</div>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Attack Type Distribution</span>
                        </div>
                        <div class="card-body">
                            <canvas id="attackTypeChart" height="200"></canvas>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">14-Day Attack Timeline</span>
                        </div>
                        <div class="card-body">
                            <canvas id="attackTimelineChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Security Logs</span>
                        <button class="btn btn-sm btn-outline" onclick="refreshLogs()">&#128260; Refresh</button>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table" id="securityLogsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Time</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Severity</th>
                                        <th>IP Address</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($securityLogs as $log):
                                        $sevClass = match($log['severity']) {
                                            'critical' => 'badge-danger',
                                            'high' => 'badge-warning',
                                            'medium' => 'badge-info',
                                            default => 'badge-secondary'
                                        };
                                    ?>
                                    <tr>
                                        <td><?= $log['id'] ?></td>
                                        <td><?= formatDate($log['created_at']) ?></td>
                                        <td><strong><?= htmlspecialchars($log['attack_type']) ?></strong></td>
                                        <td><?= htmlspecialchars(substr($log['description'], 0, 80)) ?>...</td>
                                        <td><span class="badge <?= $sevClass ?>"><?= ucfirst($log['severity']) ?></span></td>
                                        <td><?= htmlspecialchars($log['ip_address']) ?></td>
                                        <td><span class="badge badge-success"><?= ucfirst($log['action_taken']) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Analytics -->
                <?php if ($section === 'analytics'): ?>
                <div class="page-header">
                    <h1 class="page-title">Platform Analytics</h1>
                    <p class="page-subtitle">Overview of platform usage and activity</p>
                </div>

                <div class="stats-grid">
                    <?php
                    $userGrowth = $db->query("SELECT COUNT(*) as c FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch()['c'];
                    $quizAttempts = $db->query("SELECT COUNT(*) as c FROM quiz_attempts")->fetch()['c'];
                    $assignmentsSubmitted = $db->query("SELECT COUNT(*) as c FROM assignment_submissions")->fetch()['c'];
                    $avgScore = $db->query("SELECT AVG(percentage) as avg FROM quiz_attempts WHERE status IN ('submitted', 'graded')")->fetch()['avg'] ?? 0;
                    ?>
                    <div class="stat-card">
                        <div class="stat-icon blue">&#128101;</div>
                        <div class="stat-info"><h3>+<?= $userGrowth ?></h3><p>New Users (30d)</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">&#128221;</div>
                        <div class="stat-info"><h3><?= $quizAttempts ?></h3><p>Quiz Attempts</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">&#128193;</div>
                        <div class="stat-info"><h3><?= $assignmentsSubmitted ?></h3><p>Assignments Submitted</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">&#127891;</div>
                        <div class="stat-info"><h3><?= number_format($avgScore, 1) ?>%</h3><p>Avg. Score</p></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <span class="card-title">User Distribution by Role</span>
                    </div>
                    <div class="card-body">
                        <canvas id="roleDistChart" height="100"></canvas>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal-overlay" id="addUserModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New User</h3>
                <button class="modal-close" onclick="closeModal('addUserModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Username *</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Role *</label>
                            <select name="role_id" class="form-control" required>
                                <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['display_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('addUserModal')">Cancel</button>
                <button class="btn btn-primary" onclick="submitAddUser()">Add User</button>
            </div>
        </div>
    </div>

    <!-- Add Course Modal -->
    <div class="modal-overlay" id="addCourseModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Course</h3>
                <button class="modal-close" onclick="closeModal('addCourseModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addCourseForm">
                    <?= csrfField() ?>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Course Code *</label>
                            <input type="text" name="course_code" class="form-control" placeholder="CSE101" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Credit Hours</label>
                            <input type="number" name="credit_hours" class="form-control" value="3" min="1" max="6">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Course Name *</label>
                        <input type="text" name="course_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Department *</label>
                            <input type="text" name="department" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Semester *</label>
                            <select name="semester" class="form-control" required>
                                <option value="First">First</option>
                                <option value="Second">Second</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Year *</label>
                        <select name="year" class="form-control" required>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                            <option value="5">5th Year</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('addCourseModal')">Cancel</button>
                <button class="btn btn-primary" onclick="submitAddCourse()">Add Course</button>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js"></script>
    <script>
    // Search functionality
    searchTable('userSearch', 'usersTable');

    // Charts
    <?php if ($section === 'dashboard' || $section === 'security'): ?>
    <?php if (!empty($attackDist)): ?>
    new Chart(document.getElementById('attackChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($attackDist, 'attack_type')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($attackDist, 'count')) ?>,
                backgroundColor: ['#e74c3c', '#f39c12', '#3498db', '#27ae60', '#9b59b6']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
    <?php endif; ?>
    <?php endif; ?>

    <?php if ($section === 'security'): ?>
    <?php if (!empty($attackDist)): ?>
    new Chart(document.getElementById('attackTypeChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($attackDist, 'attack_type')) ?>,
            datasets: [{
                label: 'Attacks',
                data: <?= json_encode(array_column($attackDist, 'count')) ?>,
                backgroundColor: ['#e74c3c', '#f39c12', '#3498db', '#27ae60']
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
    <?php endif; ?>

    <?php if (!empty($attackTimeline)): ?>
    new Chart(document.getElementById('attackTimelineChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode(array_map(fn($d) => date('M j', strtotime($d['date'])), $attackTimeline)) ?>,
            datasets: [{
                label: 'Daily Attacks',
                data: <?= json_encode(array_column($attackTimeline, 'count')) ?>,
                borderColor: '#e74c3c',
                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
    <?php endif; ?>

    function refreshLogs() {
        location.reload();
    }
    <?php endif; ?>

    <?php if ($section === 'analytics'): ?>
    new Chart(document.getElementById('roleDistChart'), {
        type: 'doughnut',
        data: {
            labels: ['Doctors', 'TAs', 'Students'],
            datasets: [{
                data: [<?= $stats['doctors'] ?>, <?= $stats['tas'] ?>, <?= $stats['students'] ?>],
                backgroundColor: ['#3498db', '#1abc9c', '#27ae60']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
    <?php endif; ?>

    // User CRUD
    async function submitAddUser() {
        const form = document.getElementById('addUserForm');
        const formData = new FormData(form);
        formData.append('action', 'create');

        try {
            const res = await fetch('/api/users.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                showNotification('User created successfully!', 'success');
                closeModal('addUserModal');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification(data.message || 'Failed to create user', 'error');
            }
        } catch (e) {
            showNotification('Error creating user', 'error');
        }
    }

    function editUser(id) {
        showNotification('Edit user ' + id + ' - Feature loading from API...', 'info');
    }

    function deleteUser(id) {
        confirmDelete('Are you sure you want to delete this user?', async () => {
            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

                const res = await fetch('/api/users.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    showNotification('User deleted successfully!', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showNotification(data.message || 'Failed to delete user', 'error');
                }
            } catch (e) {
                showNotification('Error deleting user', 'error');
            }
        });
    }

    // Course CRUD
    async function submitAddCourse() {
        const form = document.getElementById('addCourseForm');
        const formData = new FormData(form);
        formData.append('action', 'create');

        try {
            const res = await fetch('/api/courses.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                showNotification('Course created successfully!', 'success');
                closeModal('addCourseModal');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification(data.message || 'Failed to create course', 'error');
            }
        } catch (e) {
            showNotification('Error creating course', 'error');
        }
    }

    function editCourse(id) {
        showNotification('Edit course ' + id + ' - Feature loading from API...', 'info');
    }

    function deleteCourse(id) {
        confirmDelete('Are you sure you want to delete this course?', async () => {
            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

                const res = await fetch('/api/courses.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    showNotification('Course deleted successfully!', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showNotification(data.message || 'Failed to delete course', 'error');
                }
            } catch (e) {
                showNotification('Error deleting course', 'error');
            }
        });
    }
    </script>
</body>
</html>
