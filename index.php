<?php
/**
 * Faculty of Engineering at Shubra
 * Benha University - Landing Page
 */

require_once __DIR__ . '/includes/functions.php';

// Fetch latest news
$db = Database::getInstance();
$news = $db->query("SELECT * FROM news WHERE is_published = 1 ORDER BY published_at DESC LIMIT 6")->fetchAll();

// News category colors
$categoryColors = [
    'announcement' => ['bg' => '#e3f2fd', 'color' => '#1565c0'],
    'academic' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
    'event' => ['bg' => '#fff3e0', 'color' => '#e65100'],
    'campus' => ['bg' => '#f3e5f5', 'color' => '#6a1b9a'],
    'opportunity' => ['bg' => '#e0f2f1', 'color' => '#00695c'],
    'general' => ['bg' => '#f5f5f5', 'color' => '#616161']
];

// Get user info if logged in
$currentUser = null;
$dashboardUrl = '/login.php';
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $currentUser = $db->query("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?", [$userId])->fetch();
    
    if ($currentUser) {
        // Set dashboard URL based on role
        $role = $currentUser['role_name'];
        switch ($role) {
            case 'admin':
                $dashboardUrl = '/admin/dashboard.php';
                break;
            case 'doctor':
                $dashboardUrl = '/doctor/dashboard.php';
                break;
            case 'ta':
                $dashboardUrl = '/ta/dashboard.php';
                break;
            case 'student':
                $dashboardUrl = '/student/dashboard.php';
                break;
            default:
                $dashboardUrl = '/login.php';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty of Engineering at Shubra - Benha University</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        /* ===== LANDING PAGE ONLY STYLES ===== */
        /* دي استايلات خاصة بالـ Landing Page بس ومش موجودة في style.css */
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-top: 30px;
        }
        
        .feature-card {
            background: var(--white);
            border-radius: 12px;
            padding: 28px;
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }
        
        .feature-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-4px);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            overflow: hidden;
        }
        
        .feature-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .feature-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
        }
        
        .feature-desc {
            font-size: 0.9rem;
            color: var(--gray);
            line-height: 1.6;
        }
        
        .cta-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--white);
            padding: 60px 40px;
            text-align: center;
        }
        
        .cta-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .cta-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 12px;
        }
        
        .cta-text {
            opacity: 0.9;
            margin-bottom: 28px;
            font-size: 1rem;
        }
        
        .cta-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-white {
            background: var(--white);
            color: var(--primary);
            font-weight: 600;
        }
        
        .btn-white:hover {
            background: var(--gray-light);
        }
        
        .btn-outline-white {
            background: transparent;
            border: 2px solid var(--white);
            color: var(--white);
        }
        
        .btn-outline-white:hover {
            background: var(--white);
            color: var(--primary);
        }
        
        /* ===== NAVBAR LOGO STYLES ===== */
        .landing-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .landing-logo-img {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: contain;
            background: var(--white);
            padding: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .landing-logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        
        .landing-logo-text strong {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .landing-logo-text span {
            font-size: 0.75rem;
            color: var(--gray);
            font-weight: 500;
        }
        
        /* ===== USER NAV STYLES ===== */
        .nav-user-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .nav-welcome {
            font-size: 0.85rem;
            color: var(--primary);
            font-weight: 600;
            white-space: nowrap;
        }
        
        .nav-welcome span {
            color: var(--primary-dark);
            font-weight: 700;
        }
        
        .btn-logout {
            background: transparent;
            border: 1px solid var(--danger, #dc3545);
            color: var(--danger, #dc3545);
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .btn-logout:hover {
            background: var(--danger, #dc3545);
            color: var(--white);
        }
        
        /* ===== READ MORE BUTTON ===== */
        .btn-readmore {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 12px;
            transition: background 0.2s;
            display: inline-block;
        }
        
        .btn-readmore:hover {
            background: var(--primary-dark);
        }
        
        /* ===== NEWS MODAL ===== */
        .news-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .news-modal.active {
            display: flex;
        }
        
        .news-modal-content {
            background: var(--white);
            border-radius: 16px;
            max-width: 700px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            padding: 0;
        }
        
        .news-modal-close {
            position: absolute;
            top: 16px;
            right: 20px;
            font-size: 2rem;
            color: var(--white);
            cursor: pointer;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            z-index: 10;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.3);
            border-radius: 50%;
        }
        
        .news-modal-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 16px 16px 0 0;
        }
        
        .news-modal-category {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin: 20px 20px 0;
        }
        
        .news-modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 12px 20px 8px;
        }
        
        .news-modal-date {
            font-size: 0.85rem;
            color: var(--gray);
            margin: 0 20px 16px;
        }
        
        .news-modal-body {
            font-size: 1rem;
            line-height: 1.8;
            color: var(--text);
            padding: 0 20px 24px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="landing-page">
        <!-- Navbar -->
        <nav class="landing-navbar">
            <div class="landing-nav-container">
                <a href="/" class="landing-logo">
                    <img src="/assets/images/logo_benha.png" alt="Faculty of Engineering Logo" class="landing-logo-img">
                    <div class="landing-logo-text">
                        <strong>Faculty of Engineering</strong>
                        <span>Benha University - Shubra</span>
                    </div>
                </a>
                <div class="landing-nav-links">
                    <a href="#about" class="landing-nav-link">About</a>
                    <a href="#features" class="landing-nav-link">Features</a>
                    <a href="#news" class="landing-nav-link">News</a>
                    
                    <?php if ($currentUser): ?>
                        <!-- User is logged in -->
                        <div class="nav-user-area">
                            <span class="nav-welcome">Welcome, <span><?= htmlspecialchars($currentUser['full_name']) ?></span></span>
                            <a href="<?= $dashboardUrl ?>" class="btn btn-primary btn-sm">Access Dashboard</a>
                            <a href="/logout.php" class="btn-logout">Logout</a>
                        </div>
                    <?php else: ?>
                        <!-- Guest -->
                        <a href="/login.php" class="btn btn-primary btn-sm">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="landing-hero" id="about">
            <div class="landing-hero-container">
                <h1>E-Learning & Assessment Platform</h1>
                <p>
                    The official digital gateway for academic assessment, quiz management, and dynamic collaboration
                    at the Faculty of Engineering at Shubra, Benha University.
                </p>
                <div class="landing-hero-buttons">
                    <?php if ($currentUser): ?>
                        <a href="<?= $dashboardUrl ?>" class="btn btn-lg" style="background: var(--secondary); color: var(--primary-dark); font-weight: 600; border: none;">
                            Access Your Dashboard
                        </a>
                    <?php else: ?>
                        <a href="/login.php" class="btn btn-lg" style="background: var(--secondary); color: var(--primary-dark); font-weight: 600; border: none;">
                            Access Your Dashboard
                        </a>
                    <?php endif; ?>
                    <a href="#features" class="btn btn-outline btn-lg" style="border-color: var(--white); color: var(--white);">
                        Learn More
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="landing-section" id="features">
            <h2 class="landing-section-title">Platform Features</h2>
            <div class="feature-grid">
                <!-- Course Management -->
                <div class="feature-card">
                    <div class="feature-icon" style="background: #e3f2fd;">
                        <img src="/assets/images/course_management.png" alt="Course Management">
                    </div>
                    <h3 class="feature-title">Course Management</h3>
                    <p class="feature-desc">Create and manage engineering courses with intuitive tools for content organization and student enrollment.</p>
                </div>
                <!-- Interactive Quizzes -->
                <div class="feature-card">
                    <div class="feature-icon" style="background: #e8f5e9;">
                        <img src="/assets/images/interactive_quizes.png" alt="Interactive Quizzes">
                    </div>
                    <h3 class="feature-title">Interactive Quizzes</h3>
                    <p class="feature-desc">Build MCQ and True/False quizzes with countdown timers, auto-grading, and detailed analytics.</p>
                </div>
                <!-- Assignment Submission -->
                <div class="feature-card">
                    <div class="feature-icon" style="background: #fff3e0;">
                        <img src="/assets/images/assignment_submissions.png" alt="Assignment Submission">
                    </div>
                    <h3 class="feature-title">Assignment Submission</h3>
                    <p class="feature-desc">Students can submit assignments in PDF/ZIP formats with deadline tracking and late submission handling.</p>
                </div>
                <!-- Gradebook & Analytics -->
                <div class="feature-card">
                    <div class="feature-icon" style="background: #fce4ec;">
                        <img src="/assets/images/gradebook_analytics.png" alt="Gradebook Analytics">
                    </div>
                    <h3 class="feature-title">Gradebook & Analytics</h3>
                    <p class="feature-desc">Comprehensive grade tracking with visual analytics, performance charts, and progress monitoring.</p>
                </div>
                <!-- AI Security Shield -->
                <div class="feature-card">
                    <div class="feature-icon" style="background: #f3e5f5;">
                        <img src="/assets/images/ai_security.png" alt="AI Security">
                    </div>
                    <h3 class="feature-title">AI Security Shield</h3>
                    <p class="feature-desc">Advanced threat detection powered by AI to protect against SQL injection, XSS, and path traversal attacks.</p>
                </div>
                <!-- Multi-Role Access -->
                <div class="feature-card">
                    <div class="feature-icon" style="background: #e0f2f1;">
                        <img src="/assets/images/multi_role_access.png" alt="Multi-Role Access">
                    </div>
                    <h3 class="feature-title">Multi-Role Access</h3>
                    <p class="feature-desc">Tailored dashboards for Admins, Doctors, TAs, and Students with role-based permissions and workflows.</p>
                </div>
            </div>
        </section>

        <!-- News Section -->
        <section class="landing-section" id="news" style="background: #f8f9fa;">
            <h2 class="landing-section-title">Latest Faculty News</h2>
            <div class="news-grid">
                <?php foreach ($news as $item):
                    $catStyle = $categoryColors[$item['category']] ?? $categoryColors['general'];
                    $catImages = [
                        'announcement' => '/assets/images/1.jpg',
                        'academic'     => '/assets/images/2.jpg',
                        'event'        => '/assets/images/3.jpg',
                        'campus'       => '/assets/images/4.jpg',
                        'opportunity'  => '/assets/images/5.jpg',
                        'library'      => '/assets/images/6.jpg',
                        'general'      => '/assets/images/logo_benha.jpg'
                    ];
                    $imgUrl = $catImages[$item['category']] ?? $catImages['general'];
                    $safeTitle = htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8');
                    $safeContent = htmlspecialchars($item['content'], ENT_QUOTES, 'UTF-8');
                    $safeCategory = ucfirst($item['category']);
                    $safeDate = formatDate($item['published_at'], 'F j, Y');
                ?>
                <div class="news-card" data-news-id="<?= $item['id'] ?>">
                    <div class="news-image">
                        <img src="<?= $imgUrl ?>" alt="<?= $safeCategory ?>" class="news-photo">
                    </div>
                    <div class="news-content">
                        <span class="news-category" style="background: <?= $catStyle['bg'] ?>; color: <?= $catStyle['color'] ?>;">
                            <?= $safeCategory ?>
                        </span>
                        <h3 class="news-title"><?= htmlspecialchars($item['title']) ?></h3>
                        <p class="news-excerpt"><?= htmlspecialchars(substr($item['content'], 0, 120)) ?>...</p>
                        <button class="btn-readmore" onclick='openNewsModal(<?= json_encode($item['id']) ?>, <?= json_encode($safeTitle) ?>, <?= json_encode($safeContent) ?>, <?= json_encode($imgUrl) ?>, <?= json_encode($safeCategory) ?>, <?= json_encode($safeDate) ?>, <?= json_encode($catStyle['bg']) ?>, <?= json_encode($catStyle['color']) ?>)'>
                            Read More
                        </button>
                        <div class="news-date"><?= $safeDate ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- News Modal -->
        <div id="newsModal" class="news-modal">
            <div class="news-modal-content">
                <span class="news-modal-close" onclick="closeNewsModal()">&times;</span>
                <img id="modalImage" src="" alt="" class="news-modal-image">
                <span id="modalCategory" class="news-modal-category"></span>
                <h2 id="modalTitle" class="news-modal-title"></h2>
                <div id="modalDate" class="news-modal-date"></div>
                <div id="modalContent" class="news-modal-body"></div>
            </div>
        </div>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="cta-container">
                <h2 class="cta-title">Ready to Get Started?</h2>
                <p class="cta-text">Sign in to your account to access courses, take quizzes, submit assignments, and track your academic progress.</p>
                <div class="cta-buttons">
                    <?php if ($currentUser): ?>
                        <a href="<?= $dashboardUrl ?>" class="btn btn-lg btn-white">Go to Dashboard</a>
                    <?php else: ?>
                        <a href="/login.php" class="btn btn-lg btn-white">Sign In Now</a>
                    <?php endif; ?>
                    <a href="#about" class="btn btn-lg btn-outline-white">Learn More</a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="landing-footer">
            <p>Faculty of Engineering at Shubra, Benha University &copy; <?= date('Y') ?>. All rights reserved.</p>
            <p style="margin-top: 8px; font-size: 0.8rem;">108 Shubra Street, Shubra, Cairo, Egypt</p>
        </footer>
    </div>

    <script src="/assets/js/main.js"></script>
    <script>
        function openNewsModal(id, title, content, image, category, date, catBg, catColor) {
            document.getElementById('modalImage').src = image;
            document.getElementById('modalImage').alt = category;
            
            const catEl = document.getElementById('modalCategory');
            catEl.textContent = category;
            catEl.style.background = catBg;
            catEl.style.color = catColor;
            
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalDate').textContent = date;
            document.getElementById('modalContent').textContent = content;
            document.getElementById('newsModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeNewsModal() {
            document.getElementById('newsModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close modal on outside click
        document.getElementById('newsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeNewsModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeNewsModal();
            }
        });
    </script>
</body>
</html>