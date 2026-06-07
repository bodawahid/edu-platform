-- ============================================
-- Faculty of Engineering at Shubra
-- Benha University - E-Learning Platform
-- Complete Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS benha_engineering DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE benha_engineering;

-- ============================================
-- 1. USERS & ROLES
-- ============================================

CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(20) NOT NULL UNIQUE,
    display_name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role_id INT NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    department VARCHAR(100) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

-- ============================================
-- 2. COURSES
-- ============================================

CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(20) NOT NULL UNIQUE,
    course_name VARCHAR(200) NOT NULL,
    description TEXT,
    department VARCHAR(100) NOT NULL,
    semester VARCHAR(20) NOT NULL,
    year INT NOT NULL,
    credit_hours INT DEFAULT 3,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- 3. COURSE ASSIGNMENTS (Doctor-TA-Student)
-- ============================================

CREATE TABLE course_doctors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    doctor_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_course_doctor (course_id, doctor_id),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE course_tas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    ta_id INT NOT NULL,
    assigned_by INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_course_ta (course_id, ta_id),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (ta_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE course_enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    student_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'dropped', 'completed') DEFAULT 'active',
    UNIQUE KEY unique_enrollment (course_id, student_id),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- 4. QUIZZES & QUESTIONS
-- ============================================

CREATE TABLE quizzes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    quiz_type ENUM('mcq', 'true_false', 'mixed') DEFAULT 'mixed',
    duration_minutes INT NOT NULL DEFAULT 30,
    total_marks DECIMAL(10,2) NOT NULL DEFAULT 100,
    passing_marks DECIMAL(10,2) DEFAULT 50,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    shuffle_questions TINYINT(1) DEFAULT 0,
    show_results_immediately TINYINT(1) DEFAULT 1,
    is_published TINYINT(1) DEFAULT 0,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('mcq', 'true_false') NOT NULL,
    marks DECIMAL(5,2) NOT NULL DEFAULT 1,
    correct_answer TEXT NOT NULL,
    explanation TEXT,
    question_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

CREATE TABLE question_options (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    option_text TEXT NOT NULL,
    is_correct TINYINT(1) DEFAULT 0,
    option_order INT DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- ============================================
-- 5. QUIZ ATTEMPTS & ANSWERS
-- ============================================

CREATE TABLE quiz_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    student_id INT NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    submitted_at DATETIME DEFAULT NULL,
    score DECIMAL(10,2) DEFAULT NULL,
    total_marks DECIMAL(10,2) NOT NULL,
    percentage DECIMAL(5,2) DEFAULT NULL,
    status ENUM('in_progress', 'submitted', 'auto_submitted', 'graded') DEFAULT 'in_progress',
    time_remaining_seconds INT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE quiz_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    attempt_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_answer TEXT,
    is_correct TINYINT(1) DEFAULT NULL,
    marks_obtained DECIMAL(5,2) DEFAULT 0,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- ============================================
-- 6. ASSIGNMENTS
-- ============================================

CREATE TABLE assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    instructions TEXT,
    max_file_size_mb INT DEFAULT 10,
    allowed_file_types VARCHAR(255) DEFAULT 'pdf,zip,doc,docx',
    max_marks DECIMAL(10,2) NOT NULL DEFAULT 100,
    deadline DATETIME NOT NULL,
    late_submission_allowed TINYINT(1) DEFAULT 0,
    late_penalty_percent DECIMAL(5,2) DEFAULT 0,
    is_published TINYINT(1) DEFAULT 0,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE assignment_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE assignment_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    file_name VARCHAR(255),
    file_path VARCHAR(500),
    file_size INT,
    file_type VARCHAR(50),
    submission_text TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_late TINYINT(1) DEFAULT 0,
    marks_obtained DECIMAL(10,2) DEFAULT NULL,
    feedback TEXT,
    graded_by INT DEFAULT NULL,
    graded_at DATETIME DEFAULT NULL,
    status ENUM('submitted', 'graded', 'returned') DEFAULT 'submitted',
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (graded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- 7. NEWS & ANNOUNCEMENTS
-- ============================================

CREATE TABLE news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    category VARCHAR(50) DEFAULT 'general',
    is_published TINYINT(1) DEFAULT 1,
    published_by INT NOT NULL,
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (published_by) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- 8. SECURITY & AI SHIELD
-- ============================================

CREATE TABLE security_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    user_id INT DEFAULT NULL,
    username_attempt VARCHAR(100) DEFAULT NULL,
    attack_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    request_url VARCHAR(500),
    request_method VARCHAR(10),
    request_data TEXT,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    action_taken VARCHAR(100) DEFAULT 'blocked',
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE csrf_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL
);

-- ============================================
-- 9. ACTIVITY LOGS
-- ============================================

CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- SEED DATA
-- ============================================

INSERT INTO roles (id, role_name, display_name, description) VALUES
(1, 'admin', 'Administrator', 'System administrator with full access'),
(2, 'doctor', 'Doctor', 'Faculty doctor who creates courses and quizzes'),
(3, 'ta', 'Teaching Assistant', 'Teaching assistant who helps grade assignments'),
(4, 'student', 'Student', 'Student who takes courses and quizzes');

-- Password: admin (hashed with bcrypt)
INSERT INTO users (id, username, email, password_hash, full_name, role_id, department, is_active) VALUES
(1, 'admin', 'admin@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 1, 'IT Department', 1),
(2, 'doctor', 'doctor@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Ahmed Hassan', 2, 'Computer Engineering', 1),
(3, 'ta', 'ta@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eng. Mohamed Ali', 3, 'Computer Engineering', 1),
(4, 'student', 'student@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mostafa Sayed', 4, 'Computer Engineering', 1);

-- Sample Courses
INSERT INTO courses (id, course_code, course_name, description, department, semester, year, credit_hours, created_by) VALUES
(1, 'CSE101', 'Introduction to Computer Science', 'Fundamentals of computer science including algorithms, data structures, and programming basics.', 'Computer Engineering', 'First', 1, 3, 2),
(2, 'CSE201', 'Data Structures & Algorithms', 'Advanced data structures and algorithm design techniques.', 'Computer Engineering', 'Second', 2, 3, 2),
(3, 'CSE301', 'Database Systems', 'Relational database design, SQL, and database management systems.', 'Computer Engineering', 'First', 3, 3, 2),
(4, 'CSE302', 'Software Engineering', 'Software development lifecycle, design patterns, and project management.', 'Computer Engineering', 'Second', 3, 3, 2),
(5, 'CSE401', 'Artificial Intelligence', 'Machine learning, neural networks, and intelligent systems.', 'Computer Engineering', 'First', 4, 3, 2),
(6, 'ECE201', 'Digital Logic Design', 'Boolean algebra, combinational and sequential logic circuits.', 'Electrical Engineering', 'First', 2, 3, 2);

-- Course Assignments
INSERT INTO course_doctors (course_id, doctor_id) VALUES
(1, 2), (2, 2), (3, 2), (4, 2), (5, 2), (6, 2);

INSERT INTO course_tas (course_id, ta_id, assigned_by) VALUES
(1, 3, 2), (2, 3, 2), (3, 3, 2);

INSERT INTO course_enrollments (course_id, student_id) VALUES
(1, 4), (2, 4), (3, 4), (4, 4);

-- Sample News
INSERT INTO news (title, content, category, published_by) VALUES
('Welcome to the New E-Learning Platform', 'The Faculty of Engineering at Shubra is proud to launch its new digital learning platform. This system will streamline academic assessments, quiz management, and foster collaboration between faculty and students.', 'announcement', 1),
('Midterm Examination Schedule Released', 'The midterm examination schedule for the Fall 2024 semester has been published. Please check your course dashboards for specific dates and times.', 'academic', 1),
('Research Symposium 2024', 'Join us for the annual Research Symposium showcasing innovative projects from our engineering students. Prizes will be awarded to the top three presentations.', 'event', 1),
('New Computer Lab Opening', 'A state-of-the-art computer laboratory has been inaugurated in Building C, featuring the latest hardware and software for engineering simulations.', 'campus', 1),
('Summer Training Program', 'Applications are now open for the summer training program with industry partners. Gain real-world experience in your field of study.', 'opportunity', 1),
('Library Extended Hours', 'The faculty library will now operate until midnight during exam periods to accommodate student study schedules.', 'announcement', 1);

-- Sample Quiz
INSERT INTO quizzes (id, course_id, title, description, quiz_type, duration_minutes, total_marks, start_time, end_time, is_published, created_by) VALUES
(1, 1, 'Week 1-3 Assessment', 'Assessment covering introductory concepts, algorithms, and basic programming.', 'mixed', 30, 20, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 7 DAY), 1, 2),
(2, 2, 'Data Structures Quiz', 'Test your knowledge on arrays, linked lists, stacks, and queues.', 'mcq', 45, 30, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 5 DAY), 1, 2);

-- Sample Questions for Quiz 1
INSERT INTO questions (quiz_id, question_text, question_type, marks, correct_answer, question_order) VALUES
(1, 'What does CPU stand for?', 'mcq', 2, 'Central Processing Unit', 1),
(1, 'Which of the following is a programming language?', 'mcq', 2, 'Python', 2),
(1, 'The binary system uses base-2 notation.', 'true_false', 2, 'True', 3),
(1, 'What is the time complexity of binary search?', 'mcq', 2, 'O(log n)', 4),
(1, 'RAM is a type of volatile memory.', 'true_false', 2, 'True', 5);

-- Options for Quiz 1 Questions
INSERT INTO question_options (question_id, option_text, is_correct, option_order) VALUES
(1, 'Central Processing Unit', 1, 1),
(1, 'Computer Personal Unit', 0, 2),
(1, 'Central Processor Unit', 0, 3),
(1, 'Central Process Unit', 0, 4),

(2, 'HTML', 0, 1),
(2, 'Python', 1, 2),
(2, 'HTTP', 0, 3),
(2, 'URL', 0, 4),

(3, 'True', 1, 1),
(3, 'False', 0, 2),

(4, 'O(n)', 0, 1),
(4, 'O(log n)', 1, 2),
(4, 'O(n^2)', 0, 3),
(4, 'O(1)', 0, 4),

(5, 'True', 1, 1),
(5, 'False', 0, 2);

-- Sample Assignment
INSERT INTO assignments (id, course_id, title, description, instructions, max_marks, deadline, is_published, created_by) VALUES
(1, 1, 'Programming Assignment #1', 'Write a C program to implement basic sorting algorithms.', 'Submit a ZIP file containing your source code and a brief report.', 100, DATE_ADD(NOW(), INTERVAL 5 DAY), 1, 2),
(2, 2, 'Data Structures Project', 'Implement a linked list with insertion, deletion, and traversal operations.', 'Submit your code as a PDF or ZIP file. Include comments and documentation.', 100, DATE_ADD(NOW(), INTERVAL 3 DAY), 1, 2),
(3, 3, 'Database Design', 'Design an ER diagram for a library management system.', 'Submit a PDF with the ER diagram and schema design.', 50, DATE_ADD(NOW(), INTERVAL 7 DAY), 1, 2);

-- Sample Security Logs (for AI Shield demo)
INSERT INTO security_logs (ip_address, attack_type, description, request_url, severity, action_taken) VALUES
('192.168.1.100', 'SQL Injection', "Attempted UNION-based SQL injection in login form with ' OR '1'='1", '/api/login.php', 'high', 'blocked'),
('10.0.0.50', 'XSS', '<script>alert(document.cookie)</script> payload in search field', '/search.php', 'medium', 'blocked'),
('172.16.0.20', 'Path Traversal', 'Attempted directory traversal with ../../../etc/passwd', '/download.php', 'high', 'blocked'),
('192.168.1.105', 'SQL Injection', "Time-based blind SQL injection attempt with ' AND SLEEP(5)--", '/api/quiz.php', 'critical', 'blocked'),
('10.0.0.75', 'XSS', 'JavaScript event handler injection: onerror=alert(1)', '/profile.php', 'medium', 'blocked'),
('192.168.1.200', 'Brute Force', 'Multiple failed login attempts (15 attempts in 2 minutes)', '/login.php', 'high', 'rate_limited');
