-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2026 at 02:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `benha_engineering`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-06 22:12:55'),
(2, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-07 00:10:02'),
(3, 3, 'login', 'user', 3, 'User ta logged in.', '::1', '2026-06-06 22:13:55'),
(4, 4, 'login', 'user', 4, 'User student logged in.', '::1', '2026-06-06 22:14:58'),
(5, 2, 'quiz_created', 'quiz', 3, 'Created quiz: سسسسسسسس', '::1', '2026-06-07 12:49:24'),
(6, 2, 'assignment_created', 'assignment', 4, 'Created assignment: ass1', '::1', '2026-06-11 15:56:56'),
(7, 4, 'assignment_submitted', 'assignment_submission', 1, 'Submitted assignment 1', '::1', '2026-06-10 19:12:17'),
(8, 2, 'assignment_graded', 'assignment_submission', 3, 'Graded submission 3 with 80 marks', '::1', '2026-06-11 17:22:32'),
(9, 3, 'assignment_graded', 'assignment_submission', 2, 'Graded submission 2 with 60 marks', '::1', '2026-06-11 18:55:37'),
(10, 4, 'quiz_submitted', 'quiz_attempt', 1, 'Submitted quiz 1 with score 2', '::1', '2026-06-12 16:30:48');
-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `max_file_size_mb` int(11) DEFAULT 10,
  `allowed_file_types` varchar(255) DEFAULT 'pdf,zip,doc,docx',
  `max_marks` decimal(10,2) NOT NULL DEFAULT 100.00,
  `deadline` datetime NOT NULL,
  `late_submission_allowed` tinyint(1) DEFAULT 0,
  `late_penalty_percent` decimal(5,2) DEFAULT 0.00,
  `is_published` tinyint(1) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delegate_to_ta` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = TAs can grade if assigned to course, 0 = Doctor/Admin only',
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `created_by` (`created_by`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `course_id`, `title`, `description`, `instructions`, `max_file_size_mb`, `allowed_file_types`, `max_marks`, `deadline`, `late_submission_allowed`, `late_penalty_percent`, `is_published`, `created_by`, `created_at`, `updated_at`, `delegate_to_ta`) VALUES
(1, 1, 'Programming Assignment #1', 'Write a C program to implement basic sorting algorithms.', 'Submit a ZIP file containing your source code and a brief report.', 10, 'pdf,zip,doc,docx', 100.00, '2026-06-12 00:56:22', 0, 0.00, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22', 0),
(2, 2, 'Data Structures Project', 'Implement a linked list with insertion, deletion, and traversal operations.', 'Submit your code as a PDF or ZIP file. Include comments and documentation.', 10, 'pdf,zip,doc,docx', 100.00, '2026-06-10 00:56:22', 0, 0.00, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22', 0),
(3, 3, 'Database Design', 'Design an ER diagram for a library management system.', 'Submit a PDF with the ER diagram and schema design.', 10, 'pdf,zip,doc,docx', 50.00, '2026-06-14 00:56:22', 0, 0.00, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22', 0),
(4, 1, 'ass1', 'description', 'instructions', 10, 'pdf,zip,doc,docx', 100.00, '2026-06-27 18:56:00', 0, 0.00, 1, 2, '2026-06-11 15:56:56', '2026-06-11 15:56:56', 0),
(6, 1, 'سسسس', 'اااااااااا', 'ررررررررررر', 10, 'pdf,zip,doc,docx', 100.00, '2026-06-27 21:05:00', 0, 0.00, 1, 2, '2026-06-12 18:05:48', '2026-06-12 18:05:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `assignment_files`
--

CREATE TABLE `assignment_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `assignment_id` (`assignment_id`),
  KEY `uploaded_by` (`uploaded_by`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignment_files`
--

INSERT INTO `assignment_files` (`id`, `assignment_id`, `file_name`, `file_path`, `file_size`, `file_type`, `uploaded_by`, `uploaded_at`) VALUES
(2, 6, '6a2c4a7c64108_Comprehensive_OpenMP_Report.pdf', 'uploads/assignments/instructions/6a2c4a7c64108_Comprehensive_OpenMP_Report.pdf', 36893, 'pdf', 2, '2026-06-12 18:05:48');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `submission_text` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_late` tinyint(1) DEFAULT 0,
  `marks_obtained` decimal(10,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_by` int(11) DEFAULT NULL,
  `graded_at` datetime DEFAULT NULL,
  `status` enum('submitted','graded','returned') DEFAULT 'submitted',
  PRIMARY KEY (`id`),
  KEY `assignment_id` (`assignment_id`),
  KEY `student_id` (`student_id`),
  KEY `graded_by` (`graded_by`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignment_submissions`
--

INSERT INTO `assignment_submissions` (`id`, `assignment_id`, `student_id`, `file_name`, `file_path`, `file_size`, `file_type`, `submission_text`, `submitted_at`, `is_late`, `marks_obtained`, `feedback`, `graded_by`, `graded_at`, `status`) VALUES
(1, 1, 4, NULL, NULL, NULL, NULL, 'ggfgf', '2026-06-10 19:12:17', 0, 120.00, '', 3, '2026-06-11 21:55:47', 'graded'),
(2, 3, 4, '6a2c351caa2ac_Comprehensive_OpenMP_Report.pdf', 'uploads/assignments/6a2c351caa2ac_Comprehensive_OpenMP_Report.pdf', 36893, 'pdf', 'يئر شسؤ سش', '2026-06-12 16:34:36', 0, 60.00, '', 3, '2026-06-11 21:55:37', 'graded'),
(3, 4, 4, '6a2c6a00e4fe4_Transformers___1_.pdf', 'uploads/assignments/6a2c6a00e4fe4_Transformers___1_.pdf', 1087343, 'pdf', 'اختيار', '2026-06-12 20:20:16', 0, 80.00, 'ممتاز', 2, '2026-06-11 20:22:32', 'graded'),
(4, 6, 4, '6a2c517142385_________________-________2.pdf', 'uploads/assignments/6a2c517142385_________________-________2.pdf', 78563, 'pdf', 'انسرظ', '2026-06-12 18:35:29', 0, 120.00, 'عاش', 3, '2026-06-12 22:14:12', 'graded');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `year` int(11) NOT NULL,
  `credit_hours` int(11) DEFAULT 3,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_code` (`course_code`),
  KEY `created_by` (`created_by`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `description`, `department`, `semester`, `year`, `credit_hours`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'CSE101', 'Introduction to Computer Science', 'Fundamentals of computer science including algorithms, data structures, and programming basics.', 'Computer Engineering', 'First', 1, 3, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22'),
(2, 'CSE201', 'Data Structures & Algorithms', 'Advanced data structures and algorithm design techniques.', 'Computer Engineering', 'Second', 2, 3, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22'),
(3, 'CSE301', 'Database Systems', 'Relational database design, SQL, and database management systems.', 'Computer Engineering', 'First', 3, 3, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22'),
(4, 'CSE302', 'Software Engineering', 'Software development lifecycle, design patterns, and project management.', 'Computer Engineering', 'Second', 3, 3, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22'),
(5, 'CSE401', 'Artificial Intelligence', 'Machine learning, neural networks, and intelligent systems.', 'Computer Engineering', 'First', 4, 3, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22'),
(6, 'ECE201', 'Digital Logic Design', 'Boolean algebra, combinational and sequential logic circuits.', 'Electrical Engineering', 'First', 2, 4, 1, 2, '2026-06-06 21:56:22', '2026-06-10 01:28:14');

-- --------------------------------------------------------

--
-- Table structure for table `course_doctors`
--

CREATE TABLE `course_doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_course_doctor` (`course_id`,`doctor_id`),
  KEY `doctor_id` (`doctor_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_doctors`
--

INSERT INTO `course_doctors` (`id`, `course_id`, `doctor_id`, `assigned_at`) VALUES
(1, 1, 2, '2026-06-06 21:56:22'),
(2, 2, 2, '2026-06-06 21:56:22'),
(3, 3, 2, '2026-06-06 21:56:22'),
(4, 4, 2, '2026-06-06 21:56:22'),
(5, 5, 2, '2026-06-06 21:56:22'),
(6, 6, 2, '2026-06-06 21:56:22');

-- --------------------------------------------------------

--
-- Table structure for table `course_enrollments`
--

CREATE TABLE `course_enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','dropped','completed') DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_enrollment` (`course_id`,`student_id`),
  KEY `student_id` (`student_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_enrollments`
--

INSERT INTO `course_enrollments` (`id`, `course_id`, `student_id`, `enrolled_at`, `status`) VALUES
(1, 1, 4, '2026-06-06 21:56:22', 'active'),
(2, 2, 4, '2026-06-06 21:56:22', 'active'),
(4, 4, 4, '2026-06-06 21:56:22', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `course_tas`
--

CREATE TABLE `course_tas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `ta_id` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_course_ta` (`course_id`,`ta_id`),
  KEY `ta_id` (`ta_id`),
  KEY `assigned_by` (`assigned_by`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_tas`
--

INSERT INTO `course_tas` (`id`, `course_id`, `ta_id`, `assigned_by`, `assigned_at`) VALUES
(1, 1, 3, 2, '2026-06-06 21:56:22'),
(2, 2, 3, 2, '2026-06-06 21:56:22'),
(3, 3, 3, 2, '2026-06-06 21:56:22'),
(4, 4, 3, 2, '2026-06-12 18:36:34');

-- --------------------------------------------------------

--
-- Table structure for table `csrf_tokens`
--

CREATE TABLE `csrf_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `is_published` tinyint(1) DEFAULT 1,
  `published_by` int(11) NOT NULL,
  `published_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `published_by` (`published_by`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `image_url`, `category`, `is_published`, `published_by`, `published_at`) VALUES
(1, 'Welcome to the New E-Learning Platform', 'The Faculty of Engineering at Shubra is proud to launch its new digital learning platform. This system will streamline academic assessments, quiz management, and foster collaboration between faculty and students.', NULL, 'announcement', 1, 1, '2026-06-06 21:56:22'),
(2, 'Midterm Examination Schedule Released', 'The midterm examination schedule for the Fall 2024 semester has been published. Please check your course dashboards for specific dates and times.', NULL, 'academic', 1, 1, '2026-06-06 21:56:22'),
(3, 'Research Symposium 2024', 'Join us for the annual Research Symposium showcasing innovative projects from our engineering students. Prizes will be awarded to the top three presentations.', NULL, 'event', 1, 1, '2026-06-06 21:56:22'),
(4, 'New Computer Lab Opening', 'A state-of-the-art computer laboratory has been inaugurated in Building C, featuring the latest hardware and software for engineering simulations.', NULL, 'campus', 1, 1, '2026-06-06 21:56:22'),
(5, 'Summer Training Program', 'Applications are now open for the summer training program with industry partners. Gain real-world experience in your field of study.', NULL, 'opportunity', 1, 1, '2026-06-06 21:56:22'),
(6, 'Library Extended Hours', 'The faculty library will now operate until midnight during exam periods to accommodate student study schedules.', NULL, 'announcement', 1, 1, '2026-06-06 21:56:22');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `role_target` varchar(50) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `role_target`, `type`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-09 22:12:52'),
(2, NULL, 'student', 'quiz', '📝 New Quiz Added', 'Dr. posted a new quiz: "sssssss". Check your schedule.', 1, '2026-06-09 22:26:22'),
(3, NULL, 'student', 'course_announcement', '📘 New Course Available', 'Course (CSE222 - java script) has been added to the Computer Engineering department.', 1, '2026-06-10 01:43:30'),
(4, 2, 'doctor', 'assignment_submission', '📤 Assignment Submitted', 'Student (student) uploaded a solution for "Programming Assignment #1".', 1, '2026-06-10 19:12:17'),
(5, NULL, 'student', 'assignment', '📚 New Assignment Posted', 'A new assignment has been uploaded: "ass1". Check the deadline.', 1, '2026-06-11 15:56:56'),
(6, 4, 'student', 'assignment_graded', '📊 Assignment Graded', 'Your submission for "ass1" has been graded. Marks: 80', 1, '2026-06-11 17:22:32'),
(7, 3, 'ta', 'assignment', '💼 New Course Assignment', 'You have been assigned as a TA for course: CSE302.', 1, '2026-06-12 18:36:34');
-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('mcq','true_false') NOT NULL,
  `marks` decimal(5,2) NOT NULL DEFAULT 1.00,
  `correct_answer` text NOT NULL,
  `explanation` text DEFAULT NULL,
  `question_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `marks`, `correct_answer`, `explanation`, `question_order`, `created_at`) VALUES
(1, 1, 'What does CPU stand for?', 'mcq', 2.00, 'Central Processing Unit', NULL, 1, '2026-06-06 21:56:22'),
(2, 1, 'Which of the following is a programming language?', 'mcq', 2.00, 'Python', NULL, 2, '2026-06-06 21:56:22'),
(3, 1, 'The binary system uses base-2 notation.', 'true_false', 2.00, 'True', NULL, 3, '2026-06-06 21:56:22'),
(4, 1, 'What is the time complexity of binary search?', 'mcq', 2.00, 'O(log n)', NULL, 4, '2026-06-06 21:56:22'),
(5, 1, 'RAM is a type of volatile memory.', 'true_false', 2.00, 'True', NULL, 5, '2026-06-06 21:56:22'),
(6, 3, 'ششششششششش', 'mcq', 1.00, 'سسس', NULL, 0, '2026-06-07 12:49:37'),
(7, 3, 'سسسبسب', 'mcq', 1.00, 'يررض', NULL, 0, '2026-06-07 12:50:00'),
(12, 2, 'سشنبسن', 'mcq', 1.00, 'ىبن', NULL, 0, '2026-06-11 18:09:15'),
(13, 2, 'سؤسىؤ', 'true_false', 1.00, 'True', NULL, 0, '2026-06-11 18:09:28'),
(14, 4, 'يبشبش', 'mcq', 1.00, 'رر سر ', NULL, 0, '2026-06-12 16:16:39');

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

CREATE TABLE `question_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `option_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `question_options`
--

INSERT INTO `question_options` (`id`, `question_id`, `option_text`, `is_correct`, `option_order`) VALUES
(1, 1, 'Central Processing Unit', 1, 1),
(2, 1, 'Computer Personal Unit', 0, 2),
(3, 1, 'Central Processor Unit', 0, 3),
(4, 1, 'Central Process Unit', 0, 4),
(5, 2, 'HTML', 0, 1),
(6, 2, 'Python', 1, 2),
(7, 2, 'HTTP', 0, 3),
(8, 2, 'URL', 0, 4),
(9, 3, 'True', 1, 1),
(10, 3, 'False', 0, 2),
(11, 4, 'O(n)', 0, 1),
(12, 4, 'O(log n)', 1, 2),
(13, 4, 'O(n^2)', 0, 3),
(14, 4, 'O(1)', 0, 4),
(15, 5, 'True', 1, 1),
(16, 5, 'False', 0, 2),
(17, 6, 'سسس', 1, 0),
(18, 6, 'بسب', 0, 1),
(19, 6, 'بؤسؤ', 0, 2),
(20, 6, 'بسرسر', 0, 3),
(21, 7, 'يررض', 1, 0),
(22, 7, 'ريرير', 0, 1),
(23, 7, 'بؤسرس', 0, 2),
(24, 7, ' ؤرش ', 0, 3),
(41, 12, 'ىبىنسىبن', 0, 0),
(42, 12, 'ىبن', 1, 1),
(43, 12, 'ىبنب', 0, 2),
(44, 12, 'ىنى', 0, 3),
(45, 13, 'True', 1, 0),
(46, 13, 'False', 0, 1),
(47, 14, 'رر سر ', 1, 0),
(48, 14, 'ؤسؤ', 0, 1),
(49, 14, 'ؤصؤصؤ', 0, 2),
(50, 14, 'ؤصسؤص', 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `quiz_type` enum('mcq','true_false','mixed') DEFAULT 'mixed',
  `duration_minutes` int(11) NOT NULL DEFAULT 30,
  `total_marks` decimal(10,2) NOT NULL DEFAULT 100.00,
  `passing_marks` decimal(10,2) DEFAULT 50.00,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `shuffle_questions` tinyint(1) DEFAULT 0,
  `show_results_immediately` tinyint(1) DEFAULT 1,
  `is_published` tinyint(1) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_editable` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Structural edits allowed, 0 = Locked due to student attempts',
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `created_by` (`created_by`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `course_id`, `title`, `description`, `quiz_type`, `duration_minutes`, `total_marks`, `passing_marks`, `start_time`, `end_time`, `shuffle_questions`, `show_results_immediately`, `is_published`, `created_by`, `created_at`, `updated_at`, `is_editable`) VALUES
(1, 1, 'Week 1-3 Assessment', 'Assessment covering introductory concepts, algorithms, and basic programming.', 'mixed', 30, 20.00, 50.00, '2026-06-06 00:56:22', '2026-06-14 00:56:22', 0, 1, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22', 1),
(2, 2, 'Data Structures Quiz', 'Test your knowledge on arrays, linked lists, stacks, and queues.', 'mcq', 45, 30.00, 50.00, '2026-06-05 00:56:22', '2026-06-12 00:56:22', 0, 1, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22', 1),
(3, 1, 'سسسسسسسس', 'سسسسسسسس', 'mixed', 30, 100.00, 50.00, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 0, 2, '2026-06-07 12:49:24', '2026-06-07 12:49:24', 1),
(4, 1, 'sssssss', 'axax', 'mixed', 30, 100.00, 50.00, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 1, 2, '2026-06-09 22:26:22', '2026-06-12 14:41:04', 1);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_answers`
--

CREATE TABLE `quiz_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attempt_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `marks_obtained` decimal(5,2) DEFAULT 0.00,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `attempt_id` (`attempt_id`),
  KEY `question_id` (`question_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_answers`
--

INSERT INTO `quiz_answers` (`id`, `attempt_id`, `question_id`, `selected_answer`, `is_correct`, `marks_obtained`, `answered_at`) VALUES
(1, 1, 1, 'Computer Personal Unit', 0, 0.00, '2026-06-12 16:30:48'),
(2, 1, 2, 'HTTP', 0, 0.00, '2026-06-12 16:30:48'),
(3, 1, 3, 'True', 1, 2.00, '2026-06-12 16:30:48'),
(4, 1, 4, 'O(n)', 0, 0.00, '2026-06-12 16:30:48');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `submitted_at` datetime DEFAULT NULL,
  `score` decimal(10,2) DEFAULT NULL,
  `total_marks` decimal(10,2) NOT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `status` enum('started','submitted','graded','auto_submitted') NOT NULL DEFAULT 'started',
  `time_remaining_seconds` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`),
  KEY `student_id` (`student_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `quiz_id`, `student_id`, `started_at`, `submitted_at`, `score`, `total_marks`, `percentage`, `status`, `time_remaining_seconds`, `ip_address`) VALUES
(1, 1, 4, '2026-06-12 16:30:48', '2026-06-12 19:30:48', 2.00, 20.00, 10.00, 'submitted', NULL, '::1');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(20) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`role_name`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `display_name`, `description`, `created_at`) VALUES
(1, 'admin', 'Administrator', 'System administrator with full access', '2026-06-06 21:56:22'),
(2, 'doctor', 'Doctor', 'Faculty doctor who creates courses and quizzes', '2026-06-06 21:56:22'),
(3, 'ta', 'Teaching Assistant', 'Teaching assistant who helps grade assignments', '2026-06-06 21:56:22'),
(4, 'student', 'Student', 'Student who takes courses and quizzes', '2026-06-06 21:56:22');

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username_attempt` varchar(100) DEFAULT NULL,
  `attack_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `request_url` varchar(500) DEFAULT NULL,
  `request_method` varchar(10) DEFAULT NULL,
  `request_data` text DEFAULT NULL,
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `action_taken` varchar(100) DEFAULT 'blocked',
  `confidence` decimal(5,2) DEFAULT 0.00,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_security_logs_created_at` (`created_at`),
  KEY `idx_security_logs_attack_type` (`attack_type`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_logs`
--

INSERT INTO `security_logs` (`id`, `ip_address`, `user_id`, `username_attempt`, `attack_type`, `description`, `request_url`, `request_method`, `request_data`, `severity`, `action_taken`, `confidence`, `user_agent`, `created_at`) VALUES
(1, '192.168.1.100', NULL, NULL, 'SQL Injection', 'Attempted UNION-based SQL injection in login form', '/api/login.php', 'POST', NULL, 'high', 'blocked', 100.00, NULL, '2026-06-06 21:56:22'),
(2, '10.0.0.50', NULL, NULL, 'XSS', '<script>alert(document.cookie)</script> payload in search field', '/search.php', 'GET', NULL, 'medium', 'blocked', 100.00, NULL, '2026-06-06 21:56:22'),
(3, '172.16.0.20', NULL, NULL, 'Path Traversal', 'Attempted directory traversal with ../../../etc/passwd', '/download.php', 'GET', NULL, 'high', 'blocked', 100.00, NULL, '2026-06-06 21:56:22'),
(4, '192.168.1.200', NULL, NULL, 'Brute Force', 'Multiple failed login attempts (15 attempts in 2 minutes)', '/login.php', 'POST', NULL, 'high', 'rate_limited', 0.00, NULL, '2026-06-06 21:56:22'),
(5, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt', '/api/auth.php', 'POST', NULL, 'critical', 'blocked', 100.00, NULL, '2026-06-07 21:43:13'),
(6, '::1', NULL, '<script>alert(\'XSS\')</script>', 'XSS', 'AI Shield Blocked a XSS attempt', '/api/auth.php', 'POST', NULL, 'critical', 'blocked', 100.00, NULL, '2026-06-07 21:48:47'),
(7, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt', '/admin/dashboard.php', 'GET', NULL, 'critical', 'blocked', 100.00, NULL, '2026-06-07 21:51:57');
INSERT INTO `security_logs` (`id`, `ip_address`, `user_id`, `username_attempt`, `attack_type`, `description`, `request_url`, `request_method`, `request_data`, `severity`, `action_taken`, `confidence`, `user_agent`, `created_at`) VALUES
(110, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"username\":\"admin\"},\"post\":[]}', '/login.php?username=admin', 'GET', '{\"get\":{\"username\":\"admin\"},\"post\":[]}', 'critical', 'blocked', 70.00, NULL, '2026-06-10 02:25:54'),
(111, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/..\\/..\\/etc\\/passwd\"},\"post\":[]}', '/login.php?file=../../../../etc/passwd', 'GET', '{\"get\":{\"file\":\"..\\/..\\/..\\/..\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:25:54'),
(112, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"username\":\"admin\"},\"post\":[]}', '/login.php?username=admin', 'GET', '{\"get\":{\"username\":\"admin\"},\"post\":[]}', 'critical', 'blocked', 70.00, NULL, '2026-06-10 02:25:54'),
(113, '::1', NULL, 'admin', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 70.00, NULL, '2026-06-10 02:27:59'),
(114, '::1', NULL, 'admin', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 70.00, NULL, '2026-06-10 02:28:14'),
(115, '::1', NULL, 'admin', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 70.00, NULL, '2026-06-10 02:29:01'),
(116, '::1', NULL, 'admin', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 70.00, NULL, '2026-06-10 02:29:11'),
(117, '::1', NULL, 'admin', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 70.00, NULL, '2026-06-10 02:29:44'),
(118, '::1', NULL, '/*!50000union*/', 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"\\/*!50000union*\\/\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"\\/*!50000union*\\/\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 99.96, NULL, '2026-06-10 02:35:09'),
(119, '::1', NULL, '/*!50000union*/', 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"\\/*!50000union*\\/\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"\\/*!50000union*\\/\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 99.96, NULL, '2026-06-10 02:41:51'),
(120, '::1', NULL, '/*!50000union*/', 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"\\/*!50000union*\\/\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"\\/*!50000union*\\/\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 99.96, NULL, '2026-06-10 02:43:29'),
(121, '::1', NULL, 'admin\'; WAITFOR DELAY \'0:0:5\'--', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'; WAITFOR DELAY \'0:0:5\'--\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'; WAITFOR DELAY \'0:0:5\'--\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:46:09'),
(122, '::1', NULL, 'select 1,2,3', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"select 1,2,3\",\"password\":\"kkk\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"select 1,2,3\",\"password\":\"kkk\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:46:58'),
(123, '::1', NULL, 'selection', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"selection\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"selection\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:47:15'),
(124, '::1', NULL, 'admin\'', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:50:47'),
(125, '::1', NULL, 'select', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"select\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"select\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:50:58'),
(126, '::1', NULL, 'admin\' union select 1,2,3--', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' union select 1,2,3--\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' union select 1,2,3--\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:51:30'),
(127, '::1', NULL, 'union', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"union\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"union\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:51:49'),
(128, '::1', NULL, 'sunion', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"sunion\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"sunion\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:52:03'),
(129, '::1', NULL, 'select * from users', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"select * from users\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"select * from users\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:54:06'),
(130, '::1', NULL, '/*!50000union*/', 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"\\/*!50000union*\\/\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"\\/*!50000union*\\/\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 99.96, NULL, '2026-06-10 02:58:27'),
(131, '::1', NULL, 'admin\' union select 1,2,3--', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' union select 1,2,3--\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' union select 1,2,3--\",\"password\":\"admin\",\"csrf_token\":\"879daf9ab9b2ba633191ac25b2780e3df07706cc750105e92f4d62326063345c\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:58:44'),
(132, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"admin\'_AND_\'a\'\":\"\'b\"},\"post\":[]}', '/login.php?admin%27%20AND%20%27a%27=%27b', 'GET', '{\"get\":{\"admin\'_AND_\'a\'\":\"\'b\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:59:26'),
(133, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"union\\/\\/select\":\"\"},\"post\":[]}', '/login.php?union//select', 'GET', '{\"get\":{\"union\\/\\/select\":\"\"},\"post\":[]}', 'critical', 'blocked', 99.97, NULL, '2026-06-10 02:59:37'),
(134, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":{\"<img_src\":\"x onerror = alert(1) >\"},\"post\":[]}', '/login.php?%3Cimg%20src=x%20onerror%20=%20alert(1)%20%3E', 'GET', '{\"get\":{\"<img_src\":\"x onerror = alert(1) >\"},\"post\":[]}', 'critical', 'blocked', 99.98, NULL, '2026-06-10 03:01:27'),
(135, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":{\"<<sCrIpT>alert(1)<\\/sCrIpT>\":\"\"},\"post\":[]}', '/login.php?%3C%3CsCrIpT%3Ealert(1)%3C/sCrIpT%3E', 'GET', '{\"get\":{\"<<sCrIpT>alert(1)<\\/sCrIpT>\":\"\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 03:01:40'),
(136, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"admin\'_\\/*!50000union*\\/_select_1,2,3--\":\"\"},\"post\":[]}', '/login.php?admin%27%20/*!50000union*/%20select%201,2,3--', 'GET', '{\"get\":{\"admin\'_\\/*!50000union*\\/_select_1,2,3--\":\"\"},\"post\":[]}', 'critical', 'blocked', 99.28, NULL, '2026-06-10 03:06:49'),
(137, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"\\/etc\\/passwd\"},\"post\":[]}', '/login.php?file=/etc/passwd', 'GET', '{\"get\":{\"file\":\"\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 03:07:41'),
(138, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":{\"f<sCrIpT>alert(document_cookie)<\\/sCrIpT>\":\"\"},\"post\":[]}', '/login.php?f%3CsCrIpT%3Ealert(document.cookie)%3C/sCrIpT%3E', 'GET', '{\"get\":{\"f<sCrIpT>alert(document_cookie)<\\/sCrIpT>\":\"\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 03:07:56'),
(139, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":{\"<sCrIpT>alert(document_cookie)<\\/sCrIpT>\":\"\"},\"post\":[]}', '/login.php?%3CsCrIpT%3Ealert(document.cookie)%3C/sCrIpT%3E', 'GET', '{\"get\":{\"<sCrIpT>alert(document_cookie)<\\/sCrIpT>\":\"\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 03:08:01'),
(140, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":{\"<_s_c_r_i_p_t_>_alert_(_document___cookie_)_<_\\/_s\":\"\"},\"post\":[]}', '/login.php?%3C%20s%20c%20r%20i%20p%20t%20%3E%20alert%20(%20document%20.%20cookie%20)%20%3C%20/%20s', 'GET', '{\"get\":{\"<_s_c_r_i_p_t_>_alert_(_document___cookie_)_<_\\/_s\":\"\"},\"post\":[]}', 'critical', 'blocked', 99.99, NULL, '2026-06-10 03:08:16'),
(141, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"admin\'_\\/*!50000union*\\/_select_1,2,3--\":\"\"},\"post\":[]}', '/login.php?%20admin%27%20/*!50000union*/%20select%201,2,3--', 'GET', '{\"get\":{\"admin\'_\\/*!50000union*\\/_select_1,2,3--\":\"\"},\"post\":[]}', 'critical', 'blocked', 99.28, NULL, '2026-06-10 03:09:03'),
(142, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"admin\'_\\/*!50000union*\\/_select_1,2,3--\":\"\"},\"post\":[]}', '/login.php?%20admin%27%20/*!50000union*/%20select%201,2,3--', 'GET', '{\"get\":{\"admin\'_\\/*!50000union*\\/_select_1,2,3--\":\"\"},\"post\":[]}', 'critical', 'blocked', 99.28, NULL, '2026-06-10 03:10:26'),
(143, '::1', NULL, 'admin\'', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'\",\"password\":\"admin\",\"csrf_token\":\"d8c1e45c2021c97abff31f5370ea222c8b4004005c8cdfebf6b0b5d5a50f7fc9\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'\",\"password\":\"admin\",\"csrf_token\":\"d8c1e45c2021c97abff31f5370ea222c8b4004005c8cdfebf6b0b5d5a50f7fc9\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 03:10:44'),
(144, '::1', NULL, 'admin\'', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'\",\"password\":\"admin\",\"csrf_token\":\"7210af5a9b0e738d0bbfcec287d2f80580d9a8f55db3480e34d99e14692baeee\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'\",\"password\":\"admin\",\"csrf_token\":\"7210af5a9b0e738d0bbfcec287d2f80580d9a8f55db3480e34d99e14692baeee\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 17:08:46'),
(145, '::1', 4, 'student', 'Failed Login', 'Invalid password for user: student', '/api/auth.php', 'POST', NULL, 'medium', 'logged', 0.00, NULL, '2026-06-11 17:49:34'),
(146, '::1', 2, 'doctor', 'Failed Login', 'Invalid password for user: doctor', '/api/auth.php', 'POST', NULL, 'medium', 'logged', 0.00, NULL, '2026-06-11 17:50:26'),
(147, '::1', 2, 'doctor', 'Failed Login', 'Invalid password for user: doctor', '/api/auth.php', 'POST', NULL, 'medium', 'logged', 0.00, NULL, '2026-06-11 17:50:26'),
(148, '::1', 2, 'doctor', 'Failed Login', 'Invalid password for user: doctor', '/api/auth.php', 'POST', NULL, 'medium', 'logged', 0.00, NULL, '2026-06-11 17:50:28'),
(149, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"39aa8e6f2fa5b314a9f7e2dbfac9bf5c96e7c88f326f987b36c9d1339f4df259\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"39aa8e6f2fa5b314a9f7e2dbfac9bf5c96e7c88f326f987b36c9d1339f4df259\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-13 00:10:18'),
(150, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":{\"test\":\"<script>alert(\'XSS\')<\\/script>\"},\"post\":[]}', '/login.php?test=%3Cscript%3Ealert(%27XSS%27)%3C/script%3E', 'GET', '{\"get\":{\"test\":\"<script>alert(\'XSS\')<\\/script>\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-13 00:10:46'),
(151, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"username\":\"admin\' \\/*!50000union*\\/ select 1,2,3--\"},\"post\":[]}', '/login.php?username=admin%27%20/*!50000union*/%20select%201,2,3--', 'GET', '{\"get\":{\"username\":\"admin\' \\/*!50000union*\\/ select 1,2,3--\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-13 00:11:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role_id` int(11) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role_id`, `avatar`, `phone`, `department`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 1, NULL, NULL, 'IT Department', 1, '2026-06-13 03:11:16', '2026-06-06 21:56:22', '2026-06-13 00:11:16'),
(2, 'doctor', 'doctor@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Ahmed Hassan', 2, NULL, NULL, 'Computer Engineering', 1, '2026-06-13 02:14:24', '2026-06-06 21:56:22', '2026-06-12 23:14:24'),
(3, 'ta', 'ta@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eng. Mohamed Ali', 3, NULL, NULL, 'Computer Engineering', 1, '2026-06-12 22:14:48', '2026-06-06 21:56:22', '2026-06-12 19:14:48'),
(4, 'student', 'student@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mostafa Sayed', 4, NULL, NULL, 'Computer Engineering', 1, '2026-06-13 02:27:39', '2026-06-06 21:56:22', '2026-06-12 23:27:39'),
(5, 'test', 'test@benha.edu.eg', '$2y$10$L395v7kwB9OJep9DgUSTf.Vfolx5wo9Bcajziot0sZbWHyiuTX2yG', 'test admin', 1, NULL, NULL, 'IT Department', 1, '2026-06-10 03:13:56', '2026-06-10 00:13:19', '2026-06-10 00:36:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
--
-- Indexes for table `assignments`
--
--
-- Indexes for table `assignment_files`
--
--
-- Indexes for table `assignment_submissions`
--
--
-- Indexes for table `courses`
--
--
-- Indexes for table `course_doctors`
--
--
-- Indexes for table `course_enrollments`
--
--
-- Indexes for table `course_tas`
--
--
-- Indexes for table `csrf_tokens`
--
--
-- Indexes for table `news`
--
--
-- Indexes for table `notifications`
--
--
-- Indexes for table `questions`
--
--
-- Indexes for table `question_options`
--
--
-- Indexes for table `quizzes`
--
--
-- Indexes for table `quiz_answers`
--
--
-- Indexes for table `quiz_attempts`
--
--
-- Indexes for table `roles`
--
--
-- Indexes for table `security_logs`
--
--
-- Indexes for table `users`
--
--
--

--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_files`
--
ALTER TABLE `assignment_files`
  ADD CONSTRAINT `assignment_files_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_files_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD CONSTRAINT `assignment_submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_ibfk_3` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_doctors`
--
ALTER TABLE `course_doctors`
  ADD CONSTRAINT `course_doctors_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_doctors_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD CONSTRAINT `course_enrollments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_enrollments_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_tas`
--
ALTER TABLE `course_tas`
  ADD CONSTRAINT `course_tas_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_tas_ibfk_2` FOREIGN KEY (`ta_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_tas_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `question_options`
--
ALTER TABLE `question_options`
  ADD CONSTRAINT `question_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD CONSTRAINT `security_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
