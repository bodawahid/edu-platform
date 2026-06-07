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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty of Engineering at Shubra - Benha University</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
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
            font-size: 1.6rem;
            margin: 0 auto 16px;
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
    </style>
</head>
<body>
    <div class="landing-page">
        <!-- Navbar -->
        <nav class="landing-navbar">
            <div class="landing-nav-container">
                <div class="landing-logo">
                    <div class="landing-logo-icon">FE</div>
                    <div class="landing-logo-text">
                        Faculty of Engineering
                        <span>Benha University - Shubra</span>
                    </div>
                </div>
                <div class="landing-nav-links">
                    <a href="#about" class="landing-nav-link">About</a>
                    <a href="#features" class="landing-nav-link">Features</a>
                    <a href="#news" class="landing-nav-link">News</a>
                    <a href="/login.php" class="btn btn-primary btn-sm">Sign In</a>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="landing-hero" id="about">
            <div class="landing-hero-container">
                <h1>E-Learning & Assessment Platform</h1>
                <p>
                    The official digital gateway for academic assessment, quiz management, and dynamic collaboration
                    at the Faculty of Engineering at Shubra, Benha University. Designed to empower faculty and students
                    with modern tools for academic excellence.
                </p>
                <div class="landing-hero-buttons">
                    <a href="/login.php" class="btn btn-lg" style="background: var(--secondary); color: var(--primary-dark); font-weight: 600;">
                        Access Your Dashboard
                    </a>
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
                <div class="feature-card">
                    <div class="feature-icon" style="background: #e3f2fd;">&#128218;</div>
                    <h3 class="feature-title">Course Management</h3>
                    <p class="feature-desc">Create and manage engineering courses with intuitive tools for content organization and student enrollment.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #e8f5e9;">&#128221;</div>
                    <h3 class="feature-title">Interactive Quizzes</h3>
                    <p class="feature-desc">Build MCQ and True/False quizzes with countdown timers, auto-grading, and detailed analytics.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #fff3e0;">&#128193;</div>
                    <h3 class="feature-title">Assignment Submission</h3>
                    <p class="feature-desc">Students can submit assignments in PDF/ZIP formats with deadline tracking and late submission handling.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #fce4ec;">&#128200;</div>
                    <h3 class="feature-title">Gradebook & Analytics</h3>
                    <p class="feature-desc">Comprehensive grade tracking with visual analytics, performance charts, and progress monitoring.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #f3e5f5;">&#128272;</div>
                    <h3 class="feature-title">AI Security Shield</h3>
                    <p class="feature-desc">Advanced threat detection powered by AI to protect against SQL injection, XSS, and path traversal attacks.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: #e0f2f1;">&#128101;</div>
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
                ?>
                <div class="news-card">
                    <div class="news-image">
                        <?php
                        $icons = ['announcement' => '&#128226;', 'academic' => '&#127891;', 'event' => '&#127881;', 'campus' => '&#127979;', 'opportunity' => '&#127942;', 'general' => '&#128240;'];
                        echo $icons[$item['category']] ?? '&#128240;';
                        ?>
                    </div>
                    <div class="news-content">
                        <span class="news-category" style="background: <?= $catStyle['bg'] ?>; color: <?= $catStyle['color'] ?>;">
                            <?= ucfirst($item['category']) ?>
                        </span>
                        <h3 class="news-title"><?= htmlspecialchars($item['title']) ?></h3>
                        <p class="news-excerpt"><?= htmlspecialchars(substr($item['content'], 0, 120)) ?>...</p>
                        <div class="news-date"><?= formatDate($item['published_at'], 'F j, Y') ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="cta-container">
                <h2 class="cta-title">Ready to Get Started?</h2>
                <p class="cta-text">Sign in to your account to access courses, take quizzes, submit assignments, and track your academic progress.</p>
                <div class="cta-buttons">
                    <a href="/login.php" class="btn btn-lg btn-white">Sign In Now</a>
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
</body>
</html>
