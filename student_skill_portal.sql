-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2026 at 05:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_skill_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `total_marks` int(11) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `status` enum('Passed','Needs Improvement') NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`id`, `student_id`, `category_id`, `score`, `total_marks`, `percentage`, `status`, `attempted_at`) VALUES
(1, 2, 1, 3, 5, 60.00, 'Passed', '2026-08-26 14:08:44'),
(2, 2, 2, 5, 5, 100.00, 'Passed', '2026-08-26 14:22:41'),
(3, 2, 4, 4, 5, 80.00, 'Passed', '2026-08-26 14:31:34'),
(4, 2, 3, 4, 5, 80.00, 'Passed', '2026-08-26 14:54:30');

-- --------------------------------------------------------

--
-- Table structure for table `assessment_answers`
--

CREATE TABLE `assessment_answers` (
  `id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_option` char(1) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessment_answers`
--

INSERT INTO `assessment_answers` (`id`, `assessment_id`, `question_id`, `selected_option`, `is_correct`) VALUES
(1, 1, 1, 'C', 1),
(2, 1, 2, 'B', 1),
(3, 1, 3, 'C', 0),
(4, 1, 4, 'C', 0),
(5, 1, 5, 'C', 1),
(6, 2, 6, 'B', 1),
(7, 2, 7, 'A', 1),
(8, 2, 8, 'B', 1),
(9, 2, 9, 'A', 1),
(10, 2, 10, 'C', 1),
(11, 3, 16, 'B', 1),
(12, 3, 17, 'B', 1),
(13, 3, 18, 'B', 1),
(14, 3, 19, 'B', 1),
(15, 3, 20, 'A', 0),
(16, 4, 11, 'B', 1),
(17, 4, 12, 'B', 1),
(18, 4, 13, 'C', 1),
(19, 4, 14, 'C', 1),
(20, 4, 15, 'B', 0);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'HTML & CSS', 'Web structure, semantic HTML, CSS fundamentals and responsive design', '2026-08-26 14:04:24'),
(2, 'JavaScript', 'Core JavaScript, DOM, events and modern syntax', '2026-08-26 14:04:24'),
(3, 'PHP & MySQL', 'Server-side PHP, forms, sessions and database concepts', '2026-08-26 14:04:24'),
(4, 'Computer Fundamentals', 'Programming, networking, DBMS and software fundamentals', '2026-08-26 14:04:24');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` char(1) NOT NULL,
  `marks` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `category_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `marks`) VALUES
(1, 1, 'Which HTML element is used for the largest heading?', '<h6>', '<heading>', '<h1>', '<head>', 'C', 1),
(2, 1, 'Which CSS property changes text color?', 'font-style', 'color', 'text-size', 'background', 'B', 1),
(3, 1, 'Which Bootstrap class creates a responsive container?', '.box', '.container', '.responsive', '.row-fluid', 'B', 1),
(4, 1, 'Which HTML tag creates a hyperlink?', '<link>', '<a>', '<href>', '<url>', 'B', 1),
(5, 1, 'Which CSS unit is relative to the root font size?', 'px', 'em', 'rem', 'vh', 'C', 1),
(6, 2, 'Which keyword declares a block-scoped variable?', 'var', 'let', 'define', 'dim', 'B', 1),
(7, 2, 'Which method selects an element by ID?', 'getElementById()', 'queryId()', 'selectId()', 'getElement()', 'A', 1),
(8, 2, 'Which symbol is used for strict equality?', '==', '===', '=', '!=', 'B', 1),
(9, 2, 'Which function converts JSON text to an object?', 'JSON.parse()', 'JSON.object()', 'JSON.decode()', 'parse.JSON()', 'A', 1),
(10, 2, 'Which event commonly fires when a button is clicked?', 'submit', 'hover', 'click', 'press', 'C', 1),
(11, 3, 'What does PHP stand for?', 'Personal Home Page', 'PHP: Hypertext Preprocessor', 'Private Hypertext Processor', 'Public Hosting Program', 'B', 1),
(12, 3, 'Which superglobal contains POST data?', '$_GET', '$_POST', '$_DATA', '$_FORM', 'B', 1),
(13, 3, 'Which PHP function starts a session?', 'session_begin()', 'start_session()', 'session_start()', 'begin_session()', 'C', 1),
(14, 3, 'Which SQL command retrieves records?', 'GET', 'FETCH', 'SELECT', 'READ', 'C', 1),
(15, 3, 'Which SQL clause filters rows?', 'ORDER BY', 'GROUP BY', 'WHERE', 'LIMIT', 'C', 1),
(16, 4, 'Which device forwards packets between networks?', 'Switch', 'Router', 'Hub', 'Repeater', 'B', 1),
(17, 4, 'What is the smallest unit of data?', 'Byte', 'Bit', 'Nibble', 'Word', 'B', 1),
(18, 4, 'Which key uniquely identifies a row?', 'Foreign key', 'Primary key', 'Candidate value', 'Index only', 'B', 1),
(19, 4, 'What does DBMS stand for?', 'Data Backup Management System', 'Database Management System', 'Digital Base Management Service', 'Database Memory System', 'B', 1),
(20, 4, 'Which language is mainly used to style web pages?', 'HTML', 'SQL', 'CSS', 'PHP', 'C', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin@skillportal.test', '0192023a7bbd73250516f069df18b500', 'admin', '2026-08-26 14:04:24'),
(2, 'Demo Student', 'student@skillportal.test', 'ad6a280417a0f533d8b670c61667e1a0', 'student', '2026-08-26 14:04:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `assessment_answers`
--
ALTER TABLE `assessment_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assessment_id` (`assessment_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `assessment_answers`
--
ALTER TABLE `assessment_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessments`
--
ALTER TABLE `assessments`
  ADD CONSTRAINT `assessments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assessments_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assessment_answers`
--
ALTER TABLE `assessment_answers`
  ADD CONSTRAINT `assessment_answers_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assessment_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
