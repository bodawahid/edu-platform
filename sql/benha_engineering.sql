-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2026 at 02:42 PM
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
(1, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-06 22:12:55'),
(2, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-06 22:13:50'),
(3, 3, 'login', 'user', 3, 'User ta logged in.', '::1', '2026-06-06 22:13:55'),
(4, 3, 'logout', 'user', 3, 'User ta logged out.', '::1', '2026-06-06 22:14:50'),
(5, 4, 'login', 'user', 4, 'User student logged in.', '::1', '2026-06-06 22:14:58'),
(6, 4, 'logout', 'user', 4, 'User student logged out.', '::1', '2026-06-07 00:09:47'),
(7, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-07 00:10:02'),
(8, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-07 01:06:39'),
(9, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 01:06:42'),
(10, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 01:07:34'),
(11, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 01:30:25'),
(12, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 01:38:14'),
(13, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 01:38:17'),
(14, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 11:06:19'),
(15, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 11:13:42'),
(16, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 11:14:16'),
(17, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 11:17:36'),
(18, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 11:17:50'),
(19, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 11:24:12'),
(20, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 11:24:30'),
(21, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 11:30:35'),
(22, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 11:39:18'),
(23, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 11:39:24'),
(24, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 12:47:21'),
(25, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 12:49:05'),
(26, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-07 12:49:08'),
(27, 2, 'quiz_created', 'quiz', 3, 'Created quiz: سسسسسسسس', '::1', '2026-06-07 12:49:24'),
(28, 2, 'question_added', 'question', 6, 'Added question to quiz 3', '::1', '2026-06-07 12:49:37'),
(29, 2, 'question_added', 'question', 7, 'Added question to quiz 3', '::1', '2026-06-07 12:50:00'),
(30, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-07 13:36:17'),
(31, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 13:48:45'),
(32, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 13:53:17'),
(33, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 13:53:54'),
(34, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 14:01:24'),
(35, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 17:15:05'),
(36, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 17:18:24'),
(37, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 17:18:41'),
(38, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 17:22:03'),
(39, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 18:37:54'),
(40, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 20:16:09'),
(41, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 21:04:03'),
(42, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 21:06:49'),
(43, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 21:32:59'),
(44, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 21:43:08'),
(45, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 21:47:31'),
(46, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-07 22:03:44'),
(47, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-07 22:04:01'),
(48, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-09 20:20:16'),
(49, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-09 21:00:05'),
(50, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-09 21:18:32'),
(51, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-09 21:19:14'),
(52, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-09 21:19:19'),
(53, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-09 21:20:07'),
(54, 3, 'login', 'user', 3, 'User ta logged in.', '::1', '2026-06-09 21:20:11'),
(55, 3, 'logout', 'user', 3, 'User ta logged out.', '::1', '2026-06-09 21:20:33'),
(56, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-09 21:20:39'),
(57, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-09 22:08:08'),
(58, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-09 22:08:12'),
(59, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-09 22:11:46'),
(60, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-09 22:11:49'),
(61, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-09 22:25:59'),
(62, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-09 22:26:02'),
(63, 2, 'quiz_created', 'quiz', 4, 'Created quiz: sssssss', '::1', '2026-06-09 22:26:22'),
(64, 2, 'question_added', 'question', 8, 'Added question to quiz 4', '::1', '2026-06-09 22:26:41'),
(65, 2, 'question_added', 'question', 9, 'Added question to quiz 4', '::1', '2026-06-09 22:27:17'),
(66, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-09 22:27:30'),
(67, 3, 'login', 'user', 3, 'User ta logged in.', '::1', '2026-06-09 22:27:33'),
(68, 3, 'logout', 'user', 3, 'User ta logged out.', '::1', '2026-06-09 22:28:05'),
(69, 4, 'login', 'user', 4, 'User student logged in.', '::1', '2026-06-09 22:28:09'),
(70, 4, 'logout', 'user', 4, 'User student logged out.', '::1', '2026-06-09 23:26:50'),
(71, 4, 'login', 'user', 4, 'User student logged in.', '::1', '2026-06-09 23:38:43'),
(72, 4, 'logout', 'user', 4, 'User student logged out.', '::1', '2026-06-09 23:39:01'),
(73, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-09 23:39:07'),
(74, 1, 'user_created', 'user', 5, 'Created user: test', '::1', '2026-06-10 00:13:19'),
(75, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-10 00:13:50'),
(76, 5, 'login', 'user', 5, 'User test logged in.', '::1', '2026-06-10 00:13:56'),
(77, 5, 'user_updated', 'user', 5, 'Updated user ID: 5', '::1', '2026-06-10 00:28:30'),
(78, 5, 'logout', 'user', 5, 'User test logged out.', '::1', '2026-06-10 00:32:17'),
(79, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-10 00:32:20'),
(80, 1, 'user_suspended', 'user', 5, 'Deactivated user ID: 5', '::1', '2026-06-10 00:32:29'),
(81, 1, 'user_suspended', 'user', 5, 'Deactivated user ID: 5', '::1', '2026-06-10 00:32:46'),
(82, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-10 00:32:51'),
(83, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-10 00:33:24'),
(84, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-10 00:33:56'),
(85, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-10 00:36:40'),
(86, 1, 'user_updated', 'user', 5, 'Updated user ID: 5', '::1', '2026-06-10 00:36:51'),
(87, 1, 'course_created', 'course', 7, 'Created course: CSE222', '::1', '2026-06-10 00:49:34'),
(88, 1, 'course_deleted', 'course', 7, 'Deactivated course ID: 7', '::1', '2026-06-10 00:49:45'),
(89, 1, 'course_deleted', 'course', 7, 'Deactivated course ID: 7', '::1', '2026-06-10 00:50:31'),
(90, 1, 'course_updated', 'course', 7, 'Updated course ID: 7', '::1', '2026-06-10 01:09:24'),
(91, 1, 'course_updated', 'course', 7, 'Updated course ID: 7', '::1', '2026-06-10 01:09:55'),
(92, 1, 'course_updated', 'course', 7, 'Updated course ID: 7', '::1', '2026-06-10 01:14:11'),
(93, 1, 'course_updated', 'course', 7, 'Updated course ID: 7', '::1', '2026-06-10 01:14:42'),
(94, 1, 'course_updated', 'course', 7, 'Updated course ID: 7', '::1', '2026-06-10 01:17:38'),
(95, 1, 'course_updated', 'course', 7, 'Updated course ID: 7', '::1', '2026-06-10 01:18:17'),
(96, 1, 'course_updated', 'course', 7, 'Updated course ID: 7', '::1', '2026-06-10 01:18:41'),
(97, 1, 'course_updated', 'course', 6, 'Updated course ID: 6', '::1', '2026-06-10 01:28:14'),
(98, 1, 'course_created', 'course', 8, 'Created course: CSE222', '::1', '2026-06-10 01:43:30'),
(99, 1, 'course_updated', 'course', 8, 'Updated course ID: 8', '::1', '2026-06-10 01:43:42'),
(100, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-10 01:50:08'),
(101, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-10 01:50:47'),
(102, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-10 01:51:02'),
(103, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-10 02:32:59'),
(104, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-10 02:35:06'),
(105, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-10 03:10:31'),
(106, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-10 03:10:40'),
(107, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-10 03:32:58'),
(108, 1, 'login', 'user', 1, 'User admin logged in.', '::1', '2026-06-10 17:10:04'),
(109, 1, 'logout', 'user', 1, 'User admin logged out.', '::1', '2026-06-10 17:11:25'),
(110, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-10 17:11:31'),
(111, 2, 'quiz_created', 'quiz', 5, 'Created quiz: sdxsc', '::1', '2026-06-10 17:24:20'),
(112, 2, 'question_added', 'question', 10, 'Added question to quiz 5', '::1', '2026-06-10 17:24:43'),
(113, 2, 'question_added', 'question', 11, 'Added question to quiz 5', '::1', '2026-06-10 17:24:59'),
(114, 2, 'quiz_deleted', 'quiz', 5, 'Deleted quiz ID: 5', '::1', '2026-06-10 17:27:44'),
(115, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-10 18:35:24'),
(116, 2, 'quiz_publish_toggled', 'quiz', 4, 'Quiz 4 published', '::1', '2026-06-10 18:35:44'),
(117, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-10 18:36:33'),
(118, 2, 'login', 'user', 2, 'User doctor logged in.', '::1', '2026-06-10 18:36:46'),
(119, 2, 'logout', 'user', 2, 'User doctor logged out.', '::1', '2026-06-10 18:37:40'),
(120, 4, 'login', 'user', 4, 'User student logged in.', '::1', '2026-06-10 18:37:48'),
(121, 4, 'assignment_submitted', 'assignment_submission', 1, 'Submitted assignment 1', '::1', '2026-06-10 19:12:17'),
(122, 4, 'assignment_submitted', 'assignment_submission', 2, 'Submitted assignment 3', '::1', '2026-06-10 19:45:05');

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
(1, 1, 'Programming Assignment #1', 'Write a C program to implement basic sorting algorithms.', 'Submit a ZIP file containing your source code and a brief report.', 10, 'pdf,zip,doc,docx', 100.00, '2026-06-12 00:56:22', 0, 0.00, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22'),
(2, 2, 'Data Structures Project', 'Implement a linked list with insertion, deletion, and traversal operations.', 'Submit your code as a PDF or ZIP file. Include comments and documentation.', 10, 'pdf,zip,doc,docx', 100.00, '2026-06-10 00:56:22', 0, 0.00, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22'),
(3, 3, 'Database Design', 'Design an ER diagram for a library management system.', 'Submit a PDF with the ER diagram and schema design.', 10, 'pdf,zip,doc,docx', 50.00, '2026-06-14 00:56:22', 0, 0.00, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22');

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
(1, 1, 4, NULL, NULL, NULL, NULL, 'ggfgf', '2026-06-10 19:12:17', 0, NULL, NULL, NULL, NULL, 'submitted'),
(2, 3, 4, NULL, NULL, NULL, NULL, 'يئر شسؤ سش', '2026-06-10 19:45:05', 0, NULL, NULL, NULL, NULL, 'submitted');

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
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 1, 4, '2026-06-06 21:56:22', 'active'),
(2, 2, 4, '2026-06-06 21:56:22', 'active'),
(3, 3, 4, '2026-06-06 21:56:22', 'active'),
(4, 4, 4, '2026-06-06 21:56:22', 'active');

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
(1, 1, 3, 2, '2026-06-06 21:56:22'),
(2, 2, 3, 2, '2026-06-06 21:56:22'),
(3, 3, 3, 2, '2026-06-06 21:56:22');

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
(1, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-09 22:12:52'),
(2, NULL, 'student', 'quiz', '📝 New Quiz Added', 'Dr. posted a new quiz: \"sssssss\". Check your schedule.', 1, '2026-06-09 22:26:22'),
(3, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 00:36:10'),
(4, NULL, 'student', 'course_announcement', '📘 New Course Available', 'Course (CSE222 - java script) has been added to the SSS department.', 0, '2026-06-10 00:49:34'),
(5, NULL, 'student', 'course_announcement', '📘 New Course Available', 'Course (CSE222 - java script) has been added to the Computer Engineering department.', 0, '2026-06-10 01:43:30'),
(6, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 01:51:20'),
(7, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 01:51:50'),
(8, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 1, '2026-06-10 01:52:10'),
(9, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 1, '2026-06-10 01:52:25'),
(10, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 1, '2026-06-10 01:52:39'),
(11, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 1, '2026-06-10 01:52:46'),
(12, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-10 01:53:12'),
(13, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 01:54:12'),
(14, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:05:25'),
(15, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:09:06'),
(16, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:09:16'),
(17, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:10:10'),
(18, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:10:37'),
(19, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-10 02:11:55'),
(20, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:12:02'),
(21, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:14:21'),
(22, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-10 02:18:28'),
(23, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-10 02:20:46'),
(24, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-10 02:25:54'),
(25, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:25:54'),
(26, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-10 02:25:54'),
(27, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:25:54'),
(28, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:27:59'),
(29, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:28:14'),
(30, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:29:01'),
(31, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:29:11'),
(32, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:29:44'),
(33, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-10 02:35:09'),
(34, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-10 02:41:51'),
(35, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-10 02:43:29'),
(36, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:46:09'),
(37, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:46:58'),
(38, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:47:15'),
(39, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:50:47'),
(40, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:50:58'),
(41, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:51:30'),
(42, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:51:49'),
(43, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:52:03'),
(44, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:54:06'),
(45, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-10 02:58:27'),
(46, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:58:44'),
(47, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 02:59:26'),
(48, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-10 02:59:37'),
(49, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 1, '2026-06-10 03:01:27'),
(50, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 1, '2026-06-10 03:01:40'),
(51, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 03:06:49'),
(52, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a Path Traversal attempt from IP ::1', 1, '2026-06-10 03:07:41'),
(53, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 1, '2026-06-10 03:07:56'),
(54, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 1, '2026-06-10 03:08:01'),
(55, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a XSS attempt from IP ::1', 1, '2026-06-10 03:08:16'),
(56, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 03:09:03'),
(57, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 03:10:26'),
(58, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 03:10:44'),
(59, NULL, 'admin', 'security', '🚨 New Attack Blocked', 'Blocked a SQL Injection attempt from IP ::1', 1, '2026-06-10 17:08:46'),
(60, NULL, 'student', 'quiz', '📝 New Quiz Added', 'Dr. posted a new quiz: \"sdxsc\". Check your schedule.', 0, '2026-06-10 17:24:20'),
(61, NULL, 'student', 'quiz', '📝 Quiz Published', 'Quiz \"sssssss\" is now published and ready to take.', 0, '2026-06-10 18:35:44'),
(62, 2, 'doctor', 'assignment_submission', '📤 Assignment Submitted', 'Student (student) uploaded a solution for \"Programming Assignment #1\".', 0, '2026-06-10 19:12:17'),
(63, 2, 'doctor', 'assignment_submission', '📤 Assignment Submitted', 'Student (student) uploaded a solution for \"Database Design\".', 0, '2026-06-10 19:45:05');

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
(1, 1, 'What does CPU stand for?', 'mcq', 2.00, 'Central Processing Unit', NULL, 1, '2026-06-06 21:56:22'),
(2, 1, 'Which of the following is a programming language?', 'mcq', 2.00, 'Python', NULL, 2, '2026-06-06 21:56:22'),
(3, 1, 'The binary system uses base-2 notation.', 'true_false', 2.00, 'True', NULL, 3, '2026-06-06 21:56:22'),
(4, 1, 'What is the time complexity of binary search?', 'mcq', 2.00, 'O(log n)', NULL, 4, '2026-06-06 21:56:22'),
(5, 1, 'RAM is a type of volatile memory.', 'true_false', 2.00, 'True', NULL, 5, '2026-06-06 21:56:22'),
(6, 3, 'ششششششششش', 'mcq', 1.00, 'سسس', NULL, 0, '2026-06-07 12:49:37'),
(7, 3, 'سسسبسب', 'mcq', 1.00, 'يررض', NULL, 0, '2026-06-07 12:50:00'),
(8, 4, 'cxxcxc', 'mcq', 1.00, 'sdxc', NULL, 0, '2026-06-09 22:26:41'),
(9, 4, 'scxx a', 'mcq', 1.00, 'knxkmc l', NULL, 0, '2026-06-09 22:27:17');

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
(17, 6, 'سسس', 1, 0),
(18, 6, 'بسب', 0, 1),
(19, 6, 'بؤسؤ', 0, 2),
(20, 6, 'بسرسر', 0, 3),
(21, 7, 'يررض', 1, 0),
(22, 7, 'ريرير', 0, 1),
(23, 7, 'بؤسرس', 0, 2),
(24, 7, ' ؤرش ', 0, 3),
(25, 8, 'sdxc', 1, 0),
(26, 8, 'cxxc', 0, 1),
(27, 8, 'xvxc', 0, 2),
(28, 8, 'cscsc', 0, 3),
(29, 9, 'csc xznj', 0, 0),
(30, 9, 'knxkmc l', 1, 1),
(31, 9, 'mlxml', 0, 2),
(32, 9, 'ml', 0, 3);

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
(1, 1, 'Week 1-3 Assessment', 'Assessment covering introductory concepts, algorithms, and basic programming.', 'mixed', 30, 20.00, 50.00, '2026-06-06 00:56:22', '2026-06-14 00:56:22', 0, 1, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22'),
(2, 2, 'Data Structures Quiz', 'Test your knowledge on arrays, linked lists, stacks, and queues.', 'mcq', 45, 30.00, 50.00, '2026-06-05 00:56:22', '2026-06-12 00:56:22', 0, 1, 1, 2, '2026-06-06 21:56:22', '2026-06-06 21:56:22'),
(3, 1, 'سسسسسسسس', 'سسسسسسسس', 'mixed', 30, 100.00, 50.00, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 0, 2, '2026-06-07 12:49:24', '2026-06-07 12:49:24'),
(4, 1, 'sssssss', 'axax', 'mixed', 30, 100.00, 50.00, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 1, 2, '2026-06-09 22:26:22', '2026-06-10 18:35:44');

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
(1, 'admin', 'Administrator', 'System administrator with full access', '2026-06-06 21:56:22'),
(2, 'doctor', 'Doctor', 'Faculty doctor who creates courses and quizzes', '2026-06-06 21:56:22'),
(3, 'ta', 'Teaching Assistant', 'Teaching assistant who helps grade assignments', '2026-06-06 21:56:22'),
(4, 'student', 'Student', 'Student who takes courses and quizzes', '2026-06-06 21:56:22');

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
(1, '192.168.1.100', NULL, NULL, 'SQL Injection', 'Attempted UNION-based SQL injection in login form with \' OR \'1\'=\'1', '/api/login.php', NULL, NULL, 'high', 'blocked', 0.00, NULL, '2026-06-06 21:56:22'),
(2, '10.0.0.50', NULL, NULL, 'XSS', '<script>alert(document.cookie)</script> payload in search field', '/search.php', NULL, NULL, 'medium', 'blocked', 0.00, NULL, '2026-06-06 21:56:22'),
(3, '172.16.0.20', NULL, NULL, 'Path Traversal', 'Attempted directory traversal with ../../../etc/passwd', '/download.php', NULL, NULL, 'high', 'blocked', 0.00, NULL, '2026-06-06 21:56:22'),
(4, '192.168.1.105', NULL, NULL, 'SQL Injection', 'Time-based blind SQL injection attempt with \' AND SLEEP(5)--', '/api/quiz.php', NULL, NULL, 'critical', 'blocked', 0.00, NULL, '2026-06-06 21:56:22'),
(5, '10.0.0.75', NULL, NULL, 'XSS', 'JavaScript event handler injection: onerror=alert(1)', '/profile.php', NULL, NULL, 'medium', 'blocked', 0.00, NULL, '2026-06-06 21:56:22'),
(6, '192.168.1.200', NULL, NULL, 'Brute Force', 'Multiple failed login attempts (15 attempts in 2 minutes)', '/login.php', NULL, NULL, 'high', 'rate_limited', 0.00, NULL, '2026-06-06 21:56:22'),
(7, '::1', NULL, NULL, 'XSS', 'Detected XSS attack in parameter: wp-settings-1', '/login.php', 'GET', '[]', 'high', 'blocked', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-06 21:59:32'),
(8, '::1', NULL, NULL, 'XSS', 'Detected XSS attack in parameter: wp-settings-1', '/login.php', 'GET', '[]', 'high', 'blocked', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-06 21:59:32'),
(9, '::1', NULL, NULL, 'XSS', 'Detected XSS attack in parameter: wp-settings-1', '/login.php', 'GET', '[]', 'high', 'blocked', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-06 22:01:58'),
(10, '::1', NULL, NULL, 'XSS', 'Detected XSS attack in parameter: wp-settings-1', '/login.php', 'GET', '[]', 'high', 'blocked', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-06 22:01:58'),
(11, '::1', NULL, NULL, 'XSS', 'Detected XSS attack in parameter: wp-settings-1', '/login.php', 'GET', '[]', 'high', 'blocked', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-06 22:02:06'),
(12, '::1', NULL, NULL, 'XSS', 'Detected XSS attack in parameter: wp-settings-1', '/login.php', 'GET', '[]', 'high', 'blocked', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-06 22:02:06'),
(13, '::1', NULL, 'admin', 'Failed Login', 'Invalid password for user: admin', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\",\"password\":\"\\\" خق 1=1\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:07:55'),
(14, '::1', NULL, 'admin\" OR 1==1 --', 'Failed Login', 'Invalid username attempt: admin&quot; OR 1==1 --', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\\\" OR 1==1 --\",\"password\":\"sss\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:08:10'),
(15, '::1', NULL, 'admin\" OR 1==1 --', 'Failed Login', 'Invalid username attempt: admin&quot; OR 1==1 --', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\\\" OR 1==1 --\",\"password\":\"sss\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:08:11'),
(16, '::1', NULL, 'admin\" OR 1==1 --', 'Failed Login', 'Invalid username attempt: admin&quot; OR 1==1 --', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\\\" OR 1==1 --\",\"password\":\"sss\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:08:11'),
(17, '::1', NULL, 'admin\" OR 1==1 --', 'Failed Login', 'Invalid username attempt: admin&quot; OR 1==1 --', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\\\" OR 1==1 --\",\"password\":\"sss\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:08:12'),
(18, '::1', NULL, 'admin\" OR 1==1 --', 'Failed Login', 'Invalid username attempt: admin&quot; OR 1==1 --', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\\\" OR 1==1 --\",\"password\":\"sss\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:08:13'),
(19, '::1', NULL, 'admin\" OR 1==1 --', 'Failed Login', 'Invalid username attempt: admin&quot; OR 1==1 --', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\\\" OR 1==1 --\",\"password\":\"ssssss\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:08:14'),
(20, '::1', NULL, 'admin\" OR 1==1 --', 'Failed Login', 'Invalid username attempt: admin&quot; OR 1==1 --', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\\\" OR 1==1 --\",\"password\":\"ssssss\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:08:15'),
(21, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:17:02'),
(22, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:18:45'),
(23, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:22:59'),
(24, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:27:32'),
(25, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"ca5a6b694971ffcf47aba3ef0196f1eb0f7ca3ba0dbda737eebcc76d41ffe12f\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 01:27:51'),
(26, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"bf0e0d087e1a4ab67dc501fc3ce6eeb9bbc722355e7934ef2d8dbaeff27bbb42\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 11:14:01'),
(27, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"sss\",\"csrf_token\":\"bf0e0d087e1a4ab67dc501fc3ce6eeb9bbc722355e7934ef2d8dbaeff27bbb42\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 11:14:06'),
(28, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"2b9acbd7ec183856cc6f94cb186a3ea07b4822bbc193866c79fa3b8d2f07ee69\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 11:17:42'),
(29, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"4a35599e37155a76ede208349c4cf91e409385f4f7fd404ce30fd77e0567d84e\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 11:24:22'),
(30, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"67884bfba2bd0e121aecae1194904655c638e484d8b908389b94242d19edbe11\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 11:30:43'),
(31, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"e51ce9a9a386351bc76de51a3811e107984cf7eb7bd85c779d01feebd48e49ed\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 11:39:40'),
(32, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"e51ce9a9a386351bc76de51a3811e107984cf7eb7bd85c779d01feebd48e49ed\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 12:47:12'),
(33, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"doctor\",\"csrf_token\":\"3ff773ea8a3f0a848109c53e3e661a676452e1a0ea9dc9500b7c314ce2ff160a\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 13:36:29'),
(34, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"doctor\",\"csrf_token\":\"3ff773ea8a3f0a848109c53e3e661a676452e1a0ea9dc9500b7c314ce2ff160a\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 13:46:39'),
(35, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"doctor\",\"csrf_token\":\"3ff773ea8a3f0a848109c53e3e661a676452e1a0ea9dc9500b7c314ce2ff160a\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 13:48:03'),
(36, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"d20d139ffca5c9ec2c59004f24607e76bc29419f2864c48ff8a9e9f750424c22\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 13:53:24'),
(37, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"d20d139ffca5c9ec2c59004f24607e76bc29419f2864c48ff8a9e9f750424c22\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 13:53:33'),
(38, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"d20d139ffca5c9ec2c59004f24607e76bc29419f2864c48ff8a9e9f750424c22\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 13:53:38'),
(39, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"50a3a49d60a37a391f95cd403d41c62b089d388133692b9c9e966f67169248af\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 14:01:34'),
(40, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"50a3a49d60a37a391f95cd403d41c62b089d388133692b9c9e966f67169248af\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 14:06:31'),
(41, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"50a3a49d60a37a391f95cd403d41c62b089d388133692b9c9e966f67169248af\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 14:06:54'),
(42, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"50a3a49d60a37a391f95cd403d41c62b089d388133692b9c9e966f67169248af\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 14:11:01'),
(43, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin&#039; OR &#039;1&#039;=&#039;1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"50a3a49d60a37a391f95cd403d41c62b089d388133692b9c9e966f67169248af\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 14:11:38'),
(44, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"50a3a49d60a37a391f95cd403d41c62b089d388133692b9c9e966f67169248af\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 17:14:45'),
(45, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"50a3a49d60a37a391f95cd403d41c62b089d388133692b9c9e966f67169248af\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 17:14:51'),
(46, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"b5476a3921f0bc635daa6ddda9cc1eac3885ef0780377803bdbd852b7367a11c\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 17:18:29'),
(47, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"b5476a3921f0bc635daa6ddda9cc1eac3885ef0780377803bdbd852b7367a11c\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 17:18:33'),
(48, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"4b0395ec0d31b2f71f45d56987e1583f925a5341efb2a01734ccd0322b8b91d1\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 17:22:11'),
(49, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"4b0395ec0d31b2f71f45d56987e1583f925a5341efb2a01734ccd0322b8b91d1\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 17:22:16'),
(50, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"4b0395ec0d31b2f71f45d56987e1583f925a5341efb2a01734ccd0322b8b91d1\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 17:23:46'),
(51, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"4b0395ec0d31b2f71f45d56987e1583f925a5341efb2a01734ccd0322b8b91d1\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 17:23:48'),
(52, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"4b0395ec0d31b2f71f45d56987e1583f925a5341efb2a01734ccd0322b8b91d1\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 17:23:49'),
(53, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"4b0395ec0d31b2f71f45d56987e1583f925a5341efb2a01734ccd0322b8b91d1\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 17:24:09'),
(54, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"4b0395ec0d31b2f71f45d56987e1583f925a5341efb2a01734ccd0322b8b91d1\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 17:29:51'),
(55, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"4b0395ec0d31b2f71f45d56987e1583f925a5341efb2a01734ccd0322b8b91d1\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 17:29:57'),
(56, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"4b0395ec0d31b2f71f45d56987e1583f925a5341efb2a01734ccd0322b8b91d1\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07 17:38:28'),
(57, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"4b0395ec0d31b2f71f45d56987e1583f925a5341efb2a01734ccd0322b8b91d1\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 17:38:45'),
(58, '::1', NULL, 'admin\' OR \'1\'=\'1', 'Failed Login', 'Invalid username attempt: admin\' OR \'1\'=\'1', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"4b0395ec0d31b2f71f45d56987e1583f925a5341efb2a01734ccd0322b8b91d1\"}}', 'medium', 'logged', 0.00, 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 17:53:47'),
(59, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"f8d4b2a126e88c24b920c35ef86f2de5482081acfda408db317ddda0d0f9d615\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"f8d4b2a126e88c24b920c35ef86f2de5482081acfda408db317ddda0d0f9d615\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 21:43:13'),
(60, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"f8d4b2a126e88c24b920c35ef86f2de5482081acfda408db317ddda0d0f9d615\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"f8d4b2a126e88c24b920c35ef86f2de5482081acfda408db317ddda0d0f9d615\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 21:43:25'),
(61, '::1', NULL, '<script>alert(\'XSS\')</script>', 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"<script>alert(\'XSS\')<\\/script>\",\"password\":\"hgk\",\"csrf_token\":\"dcbbe8b8047ca4740b2ac15eb256aeb11458fe682d807812a036fecacf967d69\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"<script>alert(\'XSS\')<\\/script>\",\"password\":\"hgk\",\"csrf_token\":\"dcbbe8b8047ca4740b2ac15eb256aeb11458fe682d807812a036fecacf967d69\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 21:48:47'),
(62, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":{\"<script>alert(\'XSS\')<\\/script>\":\"\"},\"post\":[]}', '/admin/dashboard.php?%3Cscript%3Ealert(%27XSS%27)%3C/script%3E', 'GET', '{\"get\":{\"<script>alert(\'XSS\')<\\/script>\":\"\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 21:49:15'),
(63, '::1', NULL, NULL, 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":{\"<script>alert(\'XSS\')<\\/script>\":\"\"},\"post\":[]}', '/admin/dashboard.php?%3Cscript%3Ealert(%27XSS%27)%3C/script%3E', 'GET', '{\"get\":{\"<script>alert(\'XSS\')<\\/script>\":\"\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 21:49:16'),
(64, '::1', NULL, 'admin....//....//....//etc/passwd', 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\",\"password\":\"hgk\",\"csrf_token\":\"dcbbe8b8047ca4740b2ac15eb256aeb11458fe682d807812a036fecacf967d69\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\",\"password\":\"hgk\",\"csrf_token\":\"dcbbe8b8047ca4740b2ac15eb256aeb11458fe682d807812a036fecacf967d69\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 21:51:00'),
(65, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', '/admin/dashboard.php?page=....//....//....//etc/passwd', 'GET', '{\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 21:51:57'),
(66, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', '/admin/dashboard.php?page=....//....//....//etc/passwd', 'GET', '{\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 21:51:58'),
(67, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"hgk\",\"csrf_token\":\"dcbbe8b8047ca4740b2ac15eb256aeb11458fe682d807812a036fecacf967d69\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"hgk\",\"csrf_token\":\"dcbbe8b8047ca4740b2ac15eb256aeb11458fe682d807812a036fecacf967d69\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 22:03:15'),
(68, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', '/admin/dashboard.php?page=....//....//....//etc/passwd', 'GET', '{\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 22:03:24'),
(69, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', '/admin/dashboard.php?page=....//....//....//etc/passwd', 'GET', '{\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 22:03:24'),
(70, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"2c066ff305883deb4cb199f021f09f8b21ca35aab276d10429ce1787066e2a16\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"2c066ff305883deb4cb199f021f09f8b21ca35aab276d10429ce1787066e2a16\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 22:03:50'),
(71, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', '/admin/dashboard.php?page=....//....//....//etc/passwd', 'GET', '{\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 22:04:32'),
(72, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', '/admin/dashboard.php?page=....//....//....//etc/passwd', 'GET', '{\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 22:04:32'),
(73, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', '/admin/dashboard.php?page=....//....//....//etc/passwd', 'GET', '{\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 22:04:40'),
(74, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', '/admin/dashboard.php?page=....//....//....//etc/passwd', 'GET', '{\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 22:04:41'),
(75, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', '/admin/dashboard.php?page=....//....//....//etc/passwd', 'GET', '{\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 22:11:48'),
(76, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', '/admin/dashboard.php?page=....//....//....//etc/passwd', 'GET', '{\"get\":{\"page\":\"....\\/\\/....\\/\\/....\\/\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-07 22:11:48'),
(77, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"ca68b9cf35bd9f4f858548d2a686762570766b948dbdcf6f181fd6093a3d3659\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"ca68b9cf35bd9f4f858548d2a686762570766b948dbdcf6f181fd6093a3d3659\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 20:20:10'),
(78, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', '/admin/dashboard.php?file=../../includes/db.php', 'GET', '{\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 20:45:30'),
(79, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', '/admin/dashboard.php?file=../../includes/db.php', 'GET', '{\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 20:45:31'),
(80, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', '/admin/dashboard.php?file=../../includes/db.php', 'GET', '{\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 20:50:21'),
(81, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"d52e8772a4daf890164aa2aaa03dbd96b9c4a3a35fe4a9e7ce7b33857566718d\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"d52e8772a4daf890164aa2aaa03dbd96b9c4a3a35fe4a9e7ce7b33857566718d\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 21:00:09'),
(82, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', '/admin/dashboard.php?file=../../includes/db.php', 'GET', '{\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 21:00:57'),
(83, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"d52e8772a4daf890164aa2aaa03dbd96b9c4a3a35fe4a9e7ce7b33857566718d\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"d52e8772a4daf890164aa2aaa03dbd96b9c4a3a35fe4a9e7ce7b33857566718d\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 21:01:02'),
(84, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', '/admin/dashboard.php?file=../../includes/db.php', 'GET', '{\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 21:06:31'),
(85, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"d52e8772a4daf890164aa2aaa03dbd96b9c4a3a35fe4a9e7ce7b33857566718d\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"d52e8772a4daf890164aa2aaa03dbd96b9c4a3a35fe4a9e7ce7b33857566718d\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 21:06:59'),
(86, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', '/admin/dashboard.php?file=../../includes/db.php', 'GET', '{\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 21:07:07'),
(87, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', '/admin/dashboard.php?file=../../includes/db.php', 'GET', '{\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 21:09:04'),
(88, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"d52e8772a4daf890164aa2aaa03dbd96b9c4a3a35fe4a9e7ce7b33857566718d\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"d52e8772a4daf890164aa2aaa03dbd96b9c4a3a35fe4a9e7ce7b33857566718d\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 21:09:14'),
(89, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', '/admin/dashboard.php?file=../../includes/db.php', 'GET', '{\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-09 22:12:52'),
(90, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"7e7d928bec14137ac9ab53be67326d80b815cbf7ced8c84e166f23e507dfe60a\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"7e7d928bec14137ac9ab53be67326d80b815cbf7ced8c84e166f23e507dfe60a\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 00:36:10'),
(91, '::1', NULL, 'admin\'; WAITFOR DELAY \'0:0:5\'--', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'; WAITFOR DELAY \'0:0:5\'--\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'; WAITFOR DELAY \'0:0:5\'--\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 01:51:20'),
(92, '::1', NULL, 'adminUNION%20SELECT%20null,null,null--', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"adminUNION%20SELECT%20null,null,null--\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"adminUNION%20SELECT%20null,null,null--\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 99.75, NULL, '2026-06-10 01:51:50'),
(93, '::1', NULL, '<script>alert(\'XSS\')</script>', 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"<script>alert(\'XSS\')<\\/script>\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"<script>alert(\'XSS\')<\\/script>\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 01:52:10'),
(94, '::1', NULL, '<svg onload=alert(1)', 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"<svg onload=alert(1)\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"<svg onload=alert(1)\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 01:52:25'),
(95, '::1', NULL, '<a href=\"javascript:alert(1)\">Click</a', 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"<a href=\\\"javascript:alert(1)\\\">Click<\\/a\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"<a href=\\\"javascript:alert(1)\\\">Click<\\/a\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 01:52:39'),
(96, '::1', NULL, 'يسؤس', 'XSS', 'AI Shield Blocked a XSS attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"يسؤس\",\"password\":\"<a href=\\\"javascript:alert(1)\\\">Click<\\/a\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"يسؤس\",\"password\":\"<a href=\\\"javascript:alert(1)\\\">Click<\\/a\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 01:52:46'),
(97, '::1', NULL, '%2e%2e%2f%2e%2e%2f%2e%2e%2fetc/passwd', 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"%2e%2e%2f%2e%2e%2f%2e%2e%2fetc\\/passwd\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"%2e%2e%2f%2e%2e%2f%2e%2e%2fetc\\/passwd\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 01:53:12'),
(98, '::1', NULL, 'admin\' AND (SELECT 1)= (SELECT 1) --', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' AND (SELECT 1)= (SELECT 1) --\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' AND (SELECT 1)= (SELECT 1) --\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 01:54:12'),
(99, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"username\":\"\' OR \'1\'=\'1\"},\"post\":[]}', '/login.php?username=%27%20OR%20%271%27=%271', 'GET', '{\"get\":{\"username\":\"\' OR \'1\'=\'1\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:05:25'),
(100, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"username\":\"admin\' \\/*!50000union*\\/ select 1,2,3--\"},\"post\":[]}', '/login.php?username=admin%27%20/*!50000union*/%20select%201,2,3--', 'GET', '{\"get\":{\"username\":\"admin\' \\/*!50000union*\\/ select 1,2,3--\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:09:06'),
(101, '::1', NULL, 'admin\' OR \'1\'=\'1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\' OR \'1\'=\'1\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:09:16'),
(102, '::1', NULL, '0x61646d696e', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"0x61646d696e\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"0x61646d696e\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 70.00, NULL, '2026-06-10 02:10:10'),
(103, '::1', NULL, 'admin\'//or//1=1', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'\\/\\/or\\/\\/1=1\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'\\/\\/or\\/\\/1=1\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:10:37'),
(104, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', '/admin/dashboard.php?file=../../includes/db.php', 'GET', '{\"get\":{\"file\":\"..\\/..\\/includes\\/db.php\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:11:55'),
(105, '::1', NULL, '/*!50000union*/', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"\\/*!50000union*\\/\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"\\/*!50000union*\\/\",\"password\":\"admin\",\"csrf_token\":\"47df1717f17fe30616f2ad24e0fe12f7120bbb18aadef54ad47fefcbeebfd95f\"}}', 'critical', 'blocked', 70.00, NULL, '2026-06-10 02:12:02'),
(106, '::1', NULL, NULL, 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":{\"username\":\"admin\"},\"post\":[]}', '/login.php?username=admin', 'GET', '{\"get\":{\"username\":\"admin\"},\"post\":[]}', 'critical', 'blocked', 70.00, NULL, '2026-06-10 02:14:21'),
(107, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/..\\/..\\/etc\\/passwd\"},\"post\":[]}', '/login.php?file=../../../../etc/passwd', 'GET', '{\"get\":{\"file\":\"..\\/..\\/..\\/..\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:18:28'),
(108, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/..\\/..\\/etc\\/passwd\"},\"post\":[]}', '/login.php?file=../../../../etc/passwd', 'GET', '{\"get\":{\"file\":\"..\\/..\\/..\\/..\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:20:46'),
(109, '::1', NULL, NULL, 'Path Traversal', 'AI Shield Blocked a Path Traversal attempt. Payload: {\"get\":{\"file\":\"..\\/..\\/..\\/..\\/etc\\/passwd\"},\"post\":[]}', '/login.php?file=../../../../etc/passwd', 'GET', '{\"get\":{\"file\":\"..\\/..\\/..\\/..\\/etc\\/passwd\"},\"post\":[]}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 02:25:54');
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
(144, '::1', NULL, 'admin\'', 'SQL Injection', 'AI Shield Blocked a SQL Injection attempt. Payload: {\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'\",\"password\":\"admin\",\"csrf_token\":\"7210af5a9b0e738d0bbfcec287d2f80580d9a8f55db3480e34d99e14692baeee\"}}', '/api/auth.php', 'POST', '{\"get\":[],\"post\":{\"action\":\"login\",\"username\":\"admin\'\",\"password\":\"admin\",\"csrf_token\":\"7210af5a9b0e738d0bbfcec287d2f80580d9a8f55db3480e34d99e14692baeee\"}}', 'critical', 'blocked', 100.00, NULL, '2026-06-10 17:08:46');

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
(1, 'admin', 'admin@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 1, NULL, NULL, 'IT Department', 1, '2026-06-10 20:10:04', '2026-06-06 21:56:22', '2026-06-10 17:10:04'),
(2, 'doctor', 'doctor@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Ahmed Hassan', 2, NULL, NULL, 'Computer Engineering', 1, '2026-06-10 21:36:46', '2026-06-06 21:56:22', '2026-06-10 18:36:46'),
(3, 'ta', 'ta@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eng. Mohamed Ali', 3, NULL, NULL, 'Computer Engineering', 1, '2026-06-10 01:27:33', '2026-06-06 21:56:22', '2026-06-09 22:27:33'),
(4, 'student', 'student@benha.edu.eg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mostafa Sayed', 4, NULL, NULL, 'Computer Engineering', 1, '2026-06-10 21:37:48', '2026-06-06 21:56:22', '2026-06-10 18:37:48'),
(5, 'test', 'test@benha.edu.eg', '$2y$10$L395v7kwB9OJep9DgUSTf.Vfolx5wo9Bcajziot0sZbWHyiuTX2yG', 'test admin', 1, NULL, NULL, 'IT Department', 1, '2026-06-10 03:13:56', '2026-06-10 00:13:19', '2026-06-10 00:36:51');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `assignment_files`
--
ALTER TABLE `assignment_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `course_doctors`
--
ALTER TABLE `course_doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `course_tas`
--
ALTER TABLE `course_tas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `csrf_tokens`
--
ALTER TABLE `csrf_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
