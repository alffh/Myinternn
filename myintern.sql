-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2026 at 06:59 PM
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
-- Database: `myintern`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `attendance_status` enum('present','absent') DEFAULT 'present',
  `status` enum('ON TIME','LATE','ABSENT') DEFAULT NULL,
  `absent_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `student_id`, `attendance_date`, `check_in`, `check_out`, `attendance_status`, `status`, `absent_reason`) VALUES
(5, 7, '2026-01-22', '09:06:45', '09:06:47', 'present', NULL, NULL),
(9, 11, '2026-02-09', '01:28:16', '01:28:34', 'present', 'ON TIME', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `company_address` text DEFAULT NULL,
  `company_email` varchar(100) DEFAULT NULL,
  `company_phone` varchar(20) DEFAULT NULL,
  `industry_type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `user_id`, `company_name`, `company_address`, `company_email`, `company_phone`, `industry_type`) VALUES
(1, 3, 'intel.com', NULL, NULL, NULL, 'finance'),
(7, 1, 'ABC Tech', NULL, 'contact@abctech.com', NULL, NULL),
(14, 24, 'Petronas', 'Petronas 11790 Gelugor', 'Petronas@gmail.com', '012913171', 'Multimedia'),
(18, 28, 'Jabatan Pendidikan Negeri', NULL, NULL, NULL, 'Education'),
(19, 29, 'Cyberlink Solutions', NULL, NULL, NULL, 'it'),
(20, 40, 'Empire Sushi', NULL, 'empiresushi@gmail.com', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `evaluation_id` int(11) NOT NULL,
  `evaluator_type` enum('company','lecturer') NOT NULL DEFAULT 'company',
  `student_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `marks` int(11) DEFAULT NULL CHECK (`marks` between 0 and 100),
  `comments` text DEFAULT NULL,
  `evaluated_at` datetime DEFAULT current_timestamp(),
  `attendance_score` int(11) DEFAULT 0,
  `skill_score` int(11) DEFAULT 0,
  `discipline_score` int(11) DEFAULT 0,
  `final_score` decimal(5,2) DEFAULT 0.00,
  `feedback` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`evaluation_id`, `evaluator_type`, `student_id`, `company_id`, `marks`, `comments`, `evaluated_at`, `attendance_score`, `skill_score`, `discipline_score`, `final_score`, `feedback`, `submitted_at`) VALUES
(1, 'company', 7, 18, NULL, NULL, '2026-01-20 08:50:21', 100, 80, 90, 90.00, 'Showed good problem-solving skills and the ability to adapt to new tools and technologies.', '2026-01-20 00:50:21'),
(2, 'company', 10, 20, NULL, NULL, '2026-01-22 12:33:08', 100, 88, 76, 88.00, 'Need a few improvement\r\n', '2026-01-22 08:07:28'),
(3, 'company', 11, 20, NULL, NULL, '2026-02-09 01:45:57', 100, 99, 98, 99.00, 'The student has demonstrated excellent commitment and professionalism throughout the internship period. Tasks were completed on time with high accuracy, and the student showed strong initiative in learning new skills. Communication with supervisors and colleagues was effective, and the student adapted well to the working environment. Overall, the student performed exceptionally well and met all internship objectives.', '2026-02-08 17:45:57');

-- --------------------------------------------------------

--
-- Table structure for table `internship_ads`
--

CREATE TABLE `internship_ads` (
  `ad_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `posted_at` datetime DEFAULT current_timestamp(),
  `ad_status` enum('active','closed') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `internship_ads`
--

INSERT INTO `internship_ads` (`ad_id`, `company_id`, `title`, `description`, `requirements`, `start_date`, `end_date`, `posted_at`, `ad_status`) VALUES
(1, 1, 'Software Development Intern', 'Assist in developing and maintaining web applications.', 'Basic knowledge of PHP, HTML, CSS, MySQL.', '2026-03-01', '2026-08-31', '2026-01-20 04:33:42', 'active'),
(5, 1, 'Web Development Internship', 'Work on web projects using PHP, JS, HTML/CSS', 'Knowledge of PHP, HTML, JS', '2026-02-01', '2026-05-01', '2026-01-20 04:47:55', 'active'),
(29, 18, 'Education Data Fields', 'Location: Pulau Pinang\n\nDetails: We are looking for a Data Analyst Intern to help manage and interpret our student education data fields. You will be responsible for cleaning academic datasets, generating reports on student CGPA trends, and ensuring the accuracy of university records in our management system.', NULL, NULL, NULL, '2026-01-20 07:07:14', 'active'),
(30, 20, 'Business Administration Intern', 'Location: Pulau Pinang\n\nDetails: Assist in daily operational activities\r\n\r\nSupport administrative and documentation tasks\r\n\r\nHelp with marketing content and promotions (online & offline)\r\n\r\nHandle basic data entry, reporting, and records\r\n\r\nWork closely with supervisors and team members\r\n\r\nLearn real-world business operations in the F&B industry', NULL, NULL, NULL, '2026-01-22 09:17:12', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `internship_applications`
--

CREATE TABLE `internship_applications` (
  `application_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `apply_date` datetime DEFAULT current_timestamp(),
  `application_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `phone` varchar(20) DEFAULT NULL,
  `university` varchar(255) DEFAULT NULL,
  `programme` varchar(50) DEFAULT NULL,
  `cgpa` decimal(3,2) DEFAULT NULL,
  `additional_info` text DEFAULT NULL,
  `form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`form_data`)),
  `applied_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `internship_applications`
--

INSERT INTO `internship_applications` (`application_id`, `student_id`, `ad_id`, `apply_date`, `application_status`, `phone`, `university`, `programme`, `cgpa`, `additional_info`, `form_data`, `applied_at`) VALUES
(3, 9, 1, '2026-01-20 04:42:12', 'pending', '0199904573', 'Uitm Machang', 'cs234', 4.00, 'hi', NULL, '2026-01-20 07:12:41'),
(4, 7, 29, '2026-01-20 07:08:53', 'approved', '0123495635', 'Uitm Machang', 'CS456', 4.00, 'I am a high-achieving student with a current CGPA of 4.00. My academic background has provided me with a strong foundation in research and data integrity. I am particularly interested in how university data can be used to improve student internship placement rates', NULL, '2026-01-20 07:12:41'),
(12, 10, 29, '2026-01-22 08:08:14', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-22 08:08:14'),
(13, 10, 1, '2026-01-22 08:08:29', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-22 08:08:29'),
(15, 10, 30, '2026-01-22 09:18:09', 'approved', '0123194139', 'Uitm Machang', 'CS240', 3.55, '', NULL, '2026-01-22 09:18:09'),
(16, 13, 30, '2026-01-22 15:31:37', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-22 15:31:37'),
(17, 11, 30, '2026-01-26 19:05:53', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-26 19:05:53'),
(18, 14, 30, '2026-02-09 01:34:52', 'rejected', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-09 01:34:52');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `lecturer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lecturer_name` varchar(100) NOT NULL,
  `programme_code` varchar(10) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `profile_pix` varchar(255) DEFAULT 'default_avatar.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_name`, `programme_code`, `password`, `email`, `profile_pix`) VALUES
(14, 101, 'Dr. Siti Khadijah binti Hassan', 'CS240', 'sv123', 'Siti@uitm.edu.my', 'lecturer_101_1769070118.png'),
(15, 102, 'Ts. Norliana binti Ahmad', 'IM262', 'sv123', 'Norliana@uitm.edu.my', 'lecturer_102_1770573182.png'),
(16, 103, 'Ms. Aisyah Sofea binti Azman', 'AC220', 'sv123', 'aisyah@uitm.edu.my', 'lecturer_103_1770573247.png'),
(17, 104, 'Ms. Farah Nabila binti Rahman', 'BA232', 'sv123', 'Farah@uitm.edu.my', 'lecturer_104_1770573276.png'),
(18, 105, 'Dr. Amirah Izzati binti Salleh', 'CS241', 'sv123', 'amirah@uitm.edu.my', 'lecturer_105_1770573312.png'),
(19, 106, 'Dr. Amin bin Aman', 'BA242', 'sv123', 'amin@uitm.edu.my', 'lecturer_106_1770573338.png'),
(20, 107, 'Dr. Hasreeq bin Omar', 'IC210', 'sv123', 'Hasreeq@uitm.edu.my', 'lecturer_107_1770573374.png');

-- --------------------------------------------------------

--
-- Table structure for table `logbook`
--

CREATE TABLE `logbook` (
  `logbook_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `activities` text DEFAULT NULL,
  `hours_spent` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `supervisor_comments` text DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logbook`
--

INSERT INTO `logbook` (`logbook_id`, `student_id`, `log_date`, `activities`, `hours_spent`, `status`, `supervisor_comments`, `approved_at`, `remarks`, `submitted_at`) VALUES
(2, 7, '2026-01-20', 'Attended onboarding session and introduction to company workflow.', 8, 'approved', 'same things', NULL, '', '2026-01-20 08:04:25'),
(3, 7, '2026-01-20', 'Attended onboarding session and introduction to company workflow.', 8, 'approved', 'same things', NULL, '', '2026-01-20 08:36:25'),
(4, 10, '2026-01-22', 'Assisted in organizing daily administrative documents and filing records. Updated customer and supplier information in the company database and ensured all records were properly maintained.', 8, 'approved', '', NULL, NULL, '2026-01-22 10:10:23'),
(5, 11, '2026-01-26', 'Introduce with the team and everyone', 8, 'pending', '', NULL, NULL, '2026-01-26 19:19:12');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `student_number` varchar(20) NOT NULL,
  `programme` varchar(100) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `university` varchar(255) DEFAULT NULL,
  `cgpa` decimal(3,2) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'default.png',
  `faculty` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `internship_status` enum('applied','approved','completed') DEFAULT 'applied',
  `status` varchar(20) DEFAULT 'Applied'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `student_name`, `student_number`, `programme`, `company_name`, `university`, `cgpa`, `profile_pic`, `faculty`, `phone`, `internship_status`, `status`) VALUES
(4, 8, 'Amna batrisya', '21832919', 'cs234', NULL, NULL, NULL, 'default.png', NULL, NULL, NULL, 'Not Applied'),
(5, 9, 'Amalin Qistina', '1235839', 'CS456', NULL, NULL, NULL, 'default.png', NULL, NULL, NULL, 'Not Applied'),
(7, 11, 'Nur Batrisya binti Wawi', '234322', 'CS332', NULL, 'Uitm Machang', 0.00, 'profile_7_1768976461.png', NULL, '', '', 'Applied'),
(8, 12, 'zaireeq emani', '20231139', 'Cs433', NULL, NULL, NULL, 'default.png', NULL, NULL, NULL, 'Not Applied'),
(9, 13, 'hazreeq', '20192921', 'CS321', NULL, NULL, NULL, 'default.png', NULL, NULL, NULL, 'Not Applied'),
(10, 39, 'zulaika', '2025643786', 'BA242', 'Empire Sushi', '', 3.45, 'default.png', NULL, '', '', 'Applied'),
(11, 112, 'Nur Syafieqa binti Zaimin', '202543232', 'IM262', NULL, 'UNIVERSITI TEKNOLOGI MARA CAWANGAN MACHANG', 4.00, 'user_112_1769067400.png', NULL, '0199904432', 'applied', 'Applied'),
(13, 114, 'Melissa Aini binti Zamrud', '2025098765', 'IC210', NULL, 'Uitm Machang', 3.55, 'default.png', NULL, '014-3432532', 'applied', 'Applied'),
(14, 115, 'Amina binti Zahid', '2025003002', 'AC220', NULL, 'Uitm Machang', 4.00, 'default.png', NULL, '015-2568965', 'applied', 'Applied'),
(15, 116, 'Aina binti Zakaria', '2025001002', 'CS241', NULL, NULL, NULL, 'default.png', NULL, '015632589', 'applied', 'Applied');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','applicant','company','lecturer') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'aliffah', 'ikramienaa@gmail.com', '$2y$10$7LZOyYZq4euFIC.3K9x3JeCb.DlwHAa2sVvMeyhgb7OtjqsDKOOpG', 'applicant', 'active', '2026-01-19 22:28:27'),
(3, 'intel', 'intel@gmail.com', '$2y$10$lUPo2IwD/pz/.IgGQBeqMOavBMpX73SjsaInrGfRQwSWqdf9Drmea', 'company', 'active', '2026-01-19 22:33:40'),
(6, 'nina', 'ninaikramiena@gmail.com', '$2y$10$Ni22Gw2912sgOM2ZyU5QCeBv/ge8QnpNVWecG1/ig/9tPYRWiS8I6', 'applicant', 'active', '2026-01-19 22:45:22'),
(7, 'Fatin', 'ftin@gmail.com', '$2y$10$BbXVAxIYhtTQEl5JDaCZrOREibRCPcvEITZT38xw4z8gTzSInWb.2', 'applicant', 'active', '2026-01-20 00:55:12'),
(8, 'amna', 'amna@gmail.com', '$2y$10$AyKiGtVPApo2DGck/BSKGu7feWkp3QkHoXq8PwUuo5l994jGyIYY2', 'applicant', 'active', '2026-01-20 01:00:14'),
(9, 'Amalin', 'amalin@gmail.com', '$2y$10$xcIviGp1n177PWGGWIk0ReS31kHWBqL95FusDJHhnpRkt8/IrzhSC', 'applicant', 'active', '2026-01-20 01:02:22'),
(10, 'ana', 'ana@gmail.com', '$2y$10$qmTy29NZ855.9o/Hm.GZJ.LP6METWeFv9jqcxlYavbkHIG7dJNg6S', 'applicant', 'active', '2026-01-20 01:03:25'),
(11, 'ama', 'ama@gmail.com', '$2y$10$9HBmWgoMqGWOrVTWQuhJS.xBPRGP0LxaZ4uRSM76bvyqX9hKRbxqu', 'applicant', 'active', '2026-01-20 01:04:06'),
(12, 'zaireeq', 'zaireeq@gmail.com', '$2y$10$t/y0MEHpGm851yyEjaowAe0IHArp90ldz2pnVcXIOVa30wPr7wYfW', 'applicant', 'active', '2026-01-20 03:07:17'),
(13, 'hazreeq', 'hzreeq@gmail.com', '$2y$10$KHSrwN41BeI/ErDD4/ZR0.V38TOdIGpAUQDwrYhCdYaYjoOecD.sG', 'applicant', 'active', '2026-01-20 03:21:38'),
(14, 'company1', 'contact@abc.com', 'password123', 'company', 'active', '2026-01-20 04:50:37'),
(15, 'company2', 'hr@xyz.com', 'password123', 'company', 'active', '2026-01-20 04:50:37'),
(24, 'Petronas', 'petronas@gmail.com', '$2y$10$hf4TqNmv4g2EFnmmWiT6UOhgc.m4bBY5yCyKcEb0hcLcH9QQP53Qq', 'company', 'active', '2026-01-20 05:10:10'),
(25, 'Shell', 'shell@gmail.com', '$2y$10$.TtCbpE7tfmGp4MgWu9GYetHQ4mBSjNSnLtJWHwWqKmBjhxLES3Da', 'company', 'active', '2026-01-20 05:17:13'),
(26, 'software', 'aliffah@gmail.com', '$2y$10$dmsYw0hGQpzo7MAss1yQKOVeyuLugvq6c9Mwayk1.1/sQgoU7AUUu', 'company', 'active', '2026-01-20 06:10:48'),
(27, 'media', 'tdgas@gmail.com', '$2y$10$V9Q3qF/JSjlQlXXCEwpUSesN6ijuDhMUMy.GfHUxTW009M30LWlGu', 'company', 'active', '2026-01-20 06:11:26'),
(28, 'JPN', 'jpnppp@gmail.com', '$2y$10$2ADMYMlamg3nFn8NO3pI3OsfJ/wG8tKsuCZbLFTgdsvy1TfVN01mK', 'company', 'active', '2026-01-20 06:56:34'),
(29, 'Cyberlink Solutions', 'Cyber@solution.gmail.com', '$2y$10$Umn9wYqwxDWM4pomQnaWieidj8cXfLWZqys2owuZB7/UMTBScqAli', 'company', 'active', '2026-01-21 14:59:52'),
(39, 'ika', 'zulaika@gmail.com', '$2y$10$GbMlnGeOPTqqypk08Z7CbuUemoUUA92rDwCQB27/TMjyCpChPq2ru', 'applicant', 'active', '2026-01-22 06:41:00'),
(40, 'Empire Sushi', 'empiresushi@gmail.com', '$2y$10$Ew50dRvV5uoU/g3eCmkHd.sT/GgBMMfcrKpygJ3TAOEyjBckzbFWi', 'company', 'active', '2026-01-22 09:08:29'),
(101, 'siti_khadijah', 'sv1@uitm.edu.my', 'no_password', 'lecturer', 'active', '2026-01-22 10:45:47'),
(102, 'norliana', 'sv2@uitm.edu.my', 'no_password', 'lecturer', 'active', '2026-01-22 10:45:47'),
(103, 'aisyah_sofea', 'sv3@uitm.edu.my', 'no_password', 'lecturer', 'active', '2026-01-22 10:45:47'),
(104, 'farah_nabila', 'sv4@uitm.edu.my', 'no_password', 'lecturer', 'active', '2026-01-22 10:45:47'),
(105, 'amirah_izzati', 'sv5@uitm.edu.my', 'no_password', 'lecturer', 'active', '2026-01-22 10:45:47'),
(106, 'amin_aman', 'sv6@uitm.edu.my', 'no_password', 'lecturer', 'active', '2026-01-22 10:45:47'),
(107, 'hasreeq_omar', 'sv7@uitm.edu.my', 'no_password', 'lecturer', 'active', '2026-01-22 10:45:47'),
(112, 'Nur Syafieqa ', 'weikaa@gmail.com', '$2y$10$oC1/gyzGexqybuq12SfeBO7sHbDH04z2KC6UEq5Hbmnhlw0a2uLve', 'applicant', 'active', '2026-01-22 11:30:58'),
(114, 'Melissa', 'melissa@gmail.com', '$2y$10$TFVSXWDbB3MJ2dDIrewvNup0fx9.WXrLlpa5d8cxp9CY5TAWmK4tG', 'applicant', 'active', '2026-01-22 15:24:20'),
(115, 'amina', 'amina@gmail.com', '$2y$10$QooLY.QDwFvUf/QYJqqFjeJxG3kHdbsyZJWkzqXPbNWp2WrpGaPE6', 'applicant', 'active', '2026-02-09 01:33:52'),
(116, 'Aina ', 'aina@gmail.com', '$2y$10$EaXiw/T2UnsMSBCYmw//7eEv3AhGqMFzHLOtRmMsnQWSW2JzrW.xS', 'applicant', 'active', '2026-02-09 01:43:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `fk_attendance_student` (`student_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `fk_companies_user` (`user_id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`evaluation_id`),
  ADD KEY `fk_eval_student` (`student_id`),
  ADD KEY `fk_eval_company` (`company_id`);

--
-- Indexes for table `internship_ads`
--
ALTER TABLE `internship_ads`
  ADD PRIMARY KEY (`ad_id`),
  ADD KEY `fk_ads_company` (`company_id`);

--
-- Indexes for table `internship_applications`
--
ALTER TABLE `internship_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `fk_app_student` (`student_id`),
  ADD KEY `fk_app_ad` (`ad_id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`lecturer_id`),
  ADD KEY `fk_lecturer_user` (`user_id`);

--
-- Indexes for table `logbook`
--
ALTER TABLE `logbook`
  ADD PRIMARY KEY (`logbook_id`),
  ADD KEY `fk_logbook_student` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD KEY `fk_students_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `evaluation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `internship_ads`
--
ALTER TABLE `internship_ads`
  MODIFY `ad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `internship_applications`
--
ALTER TABLE `internship_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `lecturer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `logbook`
--
ALTER TABLE `logbook`
  MODIFY `logbook_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_attendance_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `fk_companies_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `fk_eval_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eval_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `internship_ads`
--
ALTER TABLE `internship_ads`
  ADD CONSTRAINT `fk_ads_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `internship_applications`
--
ALTER TABLE `internship_applications`
  ADD CONSTRAINT `fk_app_ad` FOREIGN KEY (`ad_id`) REFERENCES `internship_ads` (`ad_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_app_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD CONSTRAINT `fk_lecturer_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `logbook`
--
ALTER TABLE `logbook`
  ADD CONSTRAINT `fk_logbook_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
