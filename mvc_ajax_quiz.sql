-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 07, 2025 at 08:21 PM
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
-- Database: `mvc_ajax_quiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_09_07_171744_create_quizzes_table', 1),
(6, '2025_09_07_171745_create_questions_table', 1),
(7, '2025_09_07_171746_create_options_table', 1),
(8, '2025_09_07_171747_create_quiz_sessions_table', 1),
(9, '2025_09_07_171748_create_user_answers_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Personal Home Page', 0, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(2, 1, 'PHP: Hypertext Preprocessor', 1, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(3, 1, 'Private Home Page', 0, 3, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(4, 1, 'Professional Hypertext Processor', 0, 4, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(5, 2, '<?php', 1, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(6, 2, '<php>', 0, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(7, 2, '<?', 0, 3, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(8, 2, '<script language=\"php\">', 0, 4, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(9, 3, 'var myVariable;', 0, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(10, 3, '$myVariable;', 1, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(11, 3, 'declare myVariable;', 0, 3, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(12, 3, 'variable myVariable;', 0, 4, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(13, 4, 'print()', 0, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(14, 4, 'write()', 0, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(15, 4, 'echo', 1, 3, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(16, 4, 'display()', 0, 4, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(17, 5, '$array = array(1, 2, 3);', 1, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(18, 5, '$array = {1, 2, 3};', 0, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(19, 5, '$array = (1, 2, 3);', 0, 3, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(20, 5, '$array = list(1, 2, 3);', 0, 4, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(21, 6, '$_GET', 0, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(22, 6, '$_POST', 1, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(23, 6, '$_REQUEST', 0, 3, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(24, 6, '$_FORM', 0, 4, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(25, 7, 'No difference', 0, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(26, 7, '== checks value only, === checks value and type', 1, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(27, 7, '=== is faster than ==', 0, 3, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(28, 7, '== checks type only, === checks value', 0, 4, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(29, 8, 'mysql_connect()', 0, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(30, 8, 'mysqli_connect()', 1, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(31, 8, 'db_connect()', 0, 3, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(32, 8, 'connect_mysql()', 0, 4, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(33, 9, '<!-- This is a comment -->', 0, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(34, 9, '// This is a comment', 1, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(35, 9, '# This is a comment', 0, 3, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(36, 9, '/* This is a comment', 0, 4, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(37, 10, 'try-catch', 1, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(38, 10, 'if-else', 0, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(39, 10, 'switch-case', 0, 3, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(40, 10, 'do-while', 0, 4, '2025-09-07 12:34:19', '2025-09-07 12:34:19');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `question_text` text NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `display_order`, `points`, `created_at`, `updated_at`) VALUES
(1, 1, 'What does PHP stand for?', 1, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(2, 1, 'Which of the following is the correct way to start a PHP block?', 2, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(3, 1, 'How do you declare a variable in PHP?', 3, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(4, 1, 'Which function is used to output text in PHP?', 4, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(5, 1, 'What is the correct way to create an array in PHP?', 5, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(6, 1, 'Which superglobal variable is used to collect form data sent with POST method?', 6, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(7, 1, 'What is the difference between == and === in PHP?', 7, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(8, 1, 'Which PHP function is used to connect to a MySQL database?', 8, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(9, 1, 'What is the correct way to add a comment in PHP?', 9, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(10, 1, 'Which of the following is used to handle errors in PHP?', 10, 2, '2025-09-07 12:34:19', '2025-09-07 12:34:19');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `time_limit` int(11) DEFAULT NULL COMMENT 'Time limit in seconds',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `description`, `time_limit`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'PHP Programming Quiz', 'Test your knowledge of PHP programming language, syntax, and best practices.', 1200, 1, '2025-09-07 12:34:19', '2025-09-07 12:34:19');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_sessions`
--

CREATE TABLE `quiz_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `current_question_index` int(11) NOT NULL DEFAULT 0,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `status` enum('in_progress','completed','abandoned') NOT NULL DEFAULT 'in_progress',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_sessions`
--

INSERT INTO `quiz_sessions` (`id`, `user_id`, `quiz_id`, `current_question_index`, `started_at`, `completed_at`, `status`, `created_at`, `updated_at`) VALUES
(3, 5, 1, 9, '2025-09-07 18:09:46', '2025-09-07 13:10:39', 'completed', '2025-09-07 13:09:46', '2025-09-07 13:10:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', NULL, '$2y$12$l12fRH7YRfBGxeZo4Py2HOyN9XqSJJn4slDPHCr6/LcSIwd8R2T/O', NULL, '2025-09-07 12:34:18', '2025-09-07 12:34:18'),
(2, 'John Doe', 'john@example.com', NULL, '$2y$12$gwbTD39KIyJ.O1rbBV9p8uJISOupQA7QnvV4VaCByB/ebZ5HMZSM2', NULL, '2025-09-07 12:34:18', '2025-09-07 12:34:18'),
(3, 'Jane Smith', 'jane@example.com', NULL, '$2y$12$beeVUq4la2/QUmf7tPlxFOPeaRgvb4g5Vc0hH8ZB1ltk/HwDQ9Kp.', NULL, '2025-09-07 12:34:19', '2025-09-07 12:34:19'),
(5, 'hamza', 'hamza@quiz.local', NULL, '$2y$12$6OG7tDTEagLb0VO1tvoRaOxajA4zG5haxv7fITXkLCfef9E17G43K', NULL, '2025-09-07 13:09:42', '2025-09-07 13:09:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_answers`
--

CREATE TABLE `user_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quiz_session_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `points_earned` int(11) NOT NULL DEFAULT 0,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_answers`
--

INSERT INTO `user_answers` (`id`, `quiz_session_id`, `question_id`, `option_id`, `is_correct`, `points_earned`, `answered_at`, `created_at`, `updated_at`) VALUES
(12, 3, 1, 1, 0, 0, '2025-09-07 18:09:50', '2025-09-07 13:09:50', '2025-09-07 13:09:50'),
(13, 3, 2, 6, 0, 0, '2025-09-07 18:09:59', '2025-09-07 13:09:59', '2025-09-07 13:09:59'),
(14, 3, 3, 11, 0, 0, '2025-09-07 18:10:05', '2025-09-07 13:10:05', '2025-09-07 13:10:05'),
(15, 3, 4, 14, 0, 0, '2025-09-07 18:10:09', '2025-09-07 13:10:09', '2025-09-07 13:10:09'),
(16, 3, 5, 18, 0, 0, '2025-09-07 18:10:12', '2025-09-07 13:10:12', '2025-09-07 13:10:12'),
(17, 3, 6, 22, 1, 1, '2025-09-07 18:10:17', '2025-09-07 13:10:17', '2025-09-07 13:10:17'),
(18, 3, 7, 27, 0, 0, '2025-09-07 18:10:22', '2025-09-07 13:10:22', '2025-09-07 13:10:22'),
(19, 3, 8, 30, 1, 2, '2025-09-07 18:10:28', '2025-09-07 13:10:28', '2025-09-07 13:10:28'),
(20, 3, 9, 36, 0, 0, '2025-09-07 18:10:32', '2025-09-07 13:10:32', '2025-09-07 13:10:32'),
(21, 3, 10, 39, 0, 0, '2025-09-07 18:10:38', '2025-09-07 13:10:38', '2025-09-07 13:10:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `options_question_id_display_order_index` (`question_id`,`display_order`),
  ADD KEY `options_question_id_is_correct_index` (`question_id`,`is_correct`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_quiz_id_display_order_index` (`quiz_id`,`display_order`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quizzes_is_active_index` (`is_active`);

--
-- Indexes for table `quiz_sessions`
--
ALTER TABLE `quiz_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_sessions_quiz_id_foreign` (`quiz_id`),
  ADD KEY `quiz_sessions_user_id_quiz_id_index` (`user_id`,`quiz_id`),
  ADD KEY `quiz_sessions_user_id_status_index` (`user_id`,`status`),
  ADD KEY `quiz_sessions_status_index` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_answers`
--
ALTER TABLE `user_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_answers_quiz_session_id_question_id_unique` (`quiz_session_id`,`question_id`),
  ADD KEY `user_answers_question_id_foreign` (`question_id`),
  ADD KEY `user_answers_option_id_foreign` (`option_id`),
  ADD KEY `user_answers_quiz_session_id_index` (`quiz_session_id`),
  ADD KEY `user_answers_quiz_session_id_is_correct_index` (`quiz_session_id`,`is_correct`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quiz_sessions`
--
ALTER TABLE `quiz_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_answers`
--
ALTER TABLE `user_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_sessions`
--
ALTER TABLE `quiz_sessions`
  ADD CONSTRAINT `quiz_sessions_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_answers`
--
ALTER TABLE `user_answers`
  ADD CONSTRAINT `user_answers_option_id_foreign` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_answers_quiz_session_id_foreign` FOREIGN KEY (`quiz_session_id`) REFERENCES `quiz_sessions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
