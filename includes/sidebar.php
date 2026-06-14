<?php
/**
 * Shared Sidebar Component for Dashboards
 */

$user = getCurrentUser();
if (!$user) return;

$role = $user['role_name'];
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Navigation items per role
$navItems = [
    'admin' => [
        ['icon' => '&#127968;', 'label' => 'Dashboard', 'page' => 'dashboard', 'link' => '/admin/dashboard.php'],
        ['icon' => '&#128101;', 'label' => 'Users', 'page' => 'users', 'link' => '/admin/dashboard.php?section=users'],
        ['icon' => '&#128218;', 'label' => 'Courses', 'page' => 'courses', 'link' => '/admin/dashboard.php?section=courses'],
        ['icon' => '&#128272;', 'label' => 'AI Security Shield', 'page' => 'security', 'link' => '/admin/dashboard.php?section=security'],
        ['icon' => '&#128200;', 'label' => 'Analytics', 'page' => 'analytics', 'link' => '/admin/dashboard.php?section=analytics'],
    ],
    'doctor' => [
        ['icon' => '&#127968;', 'label' => 'Dashboard', 'page' => 'dashboard', 'link' => '/doctor/dashboard.php'],
        ['icon' => '&#128218;', 'label' => 'My Courses', 'page' => 'courses', 'link' => '/doctor/dashboard.php?section=courses'],
        ['icon' => '&#128221;', 'label' => 'Quizzes', 'page' => 'quizzes', 'link' => '/doctor/dashboard.php?section=quizzes'],
        ['icon' => '&#128193;', 'label' => 'Assignments', 'page' => 'assignments', 'link' => '/doctor/dashboard.php?section=assignments'],
        ['icon' => '&#128101;', 'label' => 'TAs', 'page' => 'tas', 'link' => '/doctor/dashboard.php?section=tas'],
        ['icon' => '&#128200;', 'label' => 'Gradebook', 'page' => 'gradebook', 'link' => '/doctor/dashboard.php?section=gradebook'],
    ],
    'ta' => [
        ['icon' => '&#127968;', 'label' => 'Dashboard', 'page' => 'dashboard', 'link' => '/ta/dashboard.php'],
        ['icon' => '&#128218;', 'label' => 'My Courses', 'page' => 'courses', 'link' => '/ta/dashboard.php?section=courses'],
        ['icon' => '&#128221;', 'label' => 'Grading Queue', 'page' => 'grading', 'link' => '/ta/dashboard.php?section=grading'],
        ['icon' => '&#128101;', 'label' => 'Students', 'page' => 'students', 'link' => '/ta/dashboard.php?section=students'],
    ],
    'student' => [
        ['icon' => '&#127968;', 'label' => 'Dashboard', 'page' => 'dashboard', 'link' => '/student/dashboard.php'],
        ['icon' => '&#128218;', 'label' => 'My Courses', 'page' => 'courses', 'link' => '/student/dashboard.php?section=courses'],
        // ['icon' => '&#128203;', 'label' => 'To-Do List', 'page' => 'todo', 'link' => '/student/dashboard.php?section=todo'],
        ['icon' => '&#128221;', 'label' => 'Quizzes', 'page' => 'quizzes', 'link' => '/student/dashboard.php?section=quizzes'],
        ['icon' => '&#128193;', 'label' => 'Assignments', 'page' => 'assignments', 'link' => '/student/dashboard.php?section=assignments'],
        ['icon' => '&#127891;', 'label' => 'My Grades', 'page' => 'grades', 'link' => '/student/dashboard.php?section=grades'],
    ],
];

$items = $navItems[$role] ?? [];
$section = $_GET['section'] ?? 'dashboard';
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
  <img src="/assets/images/BU_EN_LogoN.png" alt="Benha University" class="sidebar-brand-logo">
        <div class="sidebar-brand-text">
            Faculty of Engineering<br>
            <small style="opacity:0.7">Benha University</small>
        </div>
    </div>

    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name"><?= htmlspecialchars($user['full_name']) ?></div>
            <div class="sidebar-user-role"><?= htmlspecialchars($user['role_display'] ?? 'Student') ?></div>
        </div>
    </div>

    <ul class="sidebar-nav">
        <?php foreach ($items as $item):
            $isActive = $section === $item['page'];
        ?>
        <li class="sidebar-nav-item">
            <a href="<?= $item['link'] ?>" class="sidebar-nav-link <?= $isActive ? 'active' : '' ?>">
                <span class="sidebar-nav-icon"><?= $item['icon'] ?></span>
                <?= $item['label'] ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>

    <div class="sidebar-footer">
        <a href="/" class="sidebar-logout" style="margin-bottom: 8px;">
            <span>&#127968;</span> Home
        </a>
        <a href="/logout.php" class="sidebar-logout">
            <span>&#128682;</span> Logout
        </a>
    </div>
</aside>