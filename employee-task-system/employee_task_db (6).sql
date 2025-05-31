-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 07:11 PM
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
-- Database: `employee_task_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` enum('assigned','in_progress','pending_review','accepted','rejected','completed') DEFAULT 'assigned',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `refusal_comment` text DEFAULT NULL,
  `employee_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `assigned_to`, `created_by`, `status`, `due_date`, `created_at`, `refusal_comment`, `employee_status`) VALUES
(1, 'project', 'new', 7, 9, 'in_progress', '2025-06-01', '2025-05-19 07:13:15', 'hh', NULL),
(2, 'project1', 'aaa', 8, 9, 'assigned', '2025-06-01', '2025-05-19 08:06:21', NULL, NULL),
(3, 'project1', 'aaa', 8, 9, 'assigned', '2025-06-01', '2025-05-19 08:44:54', NULL, NULL),
(4, 'css', 'نسق', 10, 9, 'completed', '2025-05-08', '2025-05-19 08:58:03', 'hh', NULL),
(5, 'html', 'ssss', 10, 11, 'completed', '2025-05-19', '2025-05-19 14:10:55', 'hh', NULL),
(6, 'php', 'ffff', 10, 11, 'completed', '2025-05-27', '2025-05-19 15:52:54', 'hh', NULL),
(7, 'Java Script', 'ggg', 10, 11, 'completed', '2025-05-09', '2025-05-22 07:32:38', 'modify it broo', NULL),
(8, 'Java', 'a', 10, 11, 'rejected', '2025-05-30', '2025-05-22 07:36:46', 'w', NULL),
(9, 'back-end', 'www', 7, 11, 'completed', '2025-05-09', '2025-05-22 07:43:42', 'g', NULL),
(10, 'navbrar', 'gggg', 7, 11, 'pending_review', '2025-05-22', '2025-05-22 10:17:44', 'j', NULL),
(11, 'navbrara', 'ddd', 10, 11, 'completed', '2025-05-06', '2025-05-22 13:52:22', 'hh', NULL),
(12, 'شششش', '', 8, 11, 'assigned', '0000-00-00', '2025-05-22 17:11:10', NULL, NULL),
(13, 'شششش', '', 8, 11, 'assigned', '0000-00-00', '2025-05-22 17:13:57', NULL, NULL),
(15, 'شششش', '', 8, 11, 'assigned', '0000-00-00', '2025-05-22 17:14:09', NULL, NULL),
(16, 'fff', 'ggg', 7, 11, '', '2025-05-09', '2025-05-24 16:09:48', NULL, NULL),
(17, 'sleep', 'bed', 7, 11, 'assigned', '2025-05-03', '2025-05-24 20:27:40', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `task_updates`
--

CREATE TABLE `task_updates` (
  `id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `update_text` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_updates`
--

INSERT INTO `task_updates` (`id`, `task_id`, `update_text`, `updated_by`, `updated_at`) VALUES
(1, 4, 'jjjj', 11, '2025-05-19 15:54:49'),
(2, 5, 'jjjj', 11, '2025-05-19 15:55:07'),
(3, 4, 'ff', 11, '2025-05-19 16:37:19'),
(4, 4, 'hh', 11, '2025-05-20 02:00:18'),
(5, 5, 'hh', 11, '2025-05-20 02:00:23'),
(6, 6, 'hh', 11, '2025-05-20 02:00:28'),
(7, 6, 'hh', 11, '2025-05-21 04:11:56'),
(8, 7, 'modify it broo', 11, '2025-05-22 07:33:55'),
(9, 8, 'aa', 11, '2025-05-22 07:37:22'),
(10, 9, 'gg', 11, '2025-05-22 07:45:03'),
(11, 9, 'g', 11, '2025-05-22 07:56:57'),
(12, 1, 'hh', 11, '2025-05-22 07:58:49'),
(13, 11, 'hh', 11, '2025-05-22 14:05:11'),
(14, 10, 'j', 11, '2025-05-22 16:35:42'),
(15, 8, 'd', 11, '2025-05-24 19:42:27'),
(16, 8, 'w', 11, '2025-05-24 19:46:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee','user') DEFAULT 'employee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `gender` enum('male','female') DEFAULT 'male',
  `remember_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `phone`, `gender`, `remember_token`) VALUES
(7, 'amer', 'amer2@gmail.com', '$2y$10$MGU80bBzoTQH7UMEzWAL7O3YXIdGlDwWyZKwC.B9N4Rt6C5MlfZLO', 'employee', '2025-05-18 04:18:34', '1', 'male', '29539804e58c31aacd5be90335817bf3'),
(8, 'iutii', 'amer3@gmail.com', '$2y$10$/NZaMBdxVVhymxE1M7INvObsZ9ZG7LfWLUT9.vWmCpueLqMEut96i', 'employee', '2025-05-19 02:16:45', '0734083215', 'male', NULL),
(9, 'iutii', 'amer01@gmail.com', '$2y$10$YiL73f0/PMYfRZjw6rxf4e5tSSBleDpSpuuJrn3uPJUAcn0di6uNG', 'admin', '2025-05-19 03:26:42', '0734083215', 'male', NULL),
(10, 'mohaned', 'mohaned@gmail.com', '$2y$10$ldJnmg/OAPhPGQBqCvkIeuX61paow7pQLNecIwCySl8MGt0W5rYfC', 'employee', '2025-05-19 08:57:09', '777', 'male', 'd9267bf2871981f174bc48d62aad562d05ea88b5240d377a2b9944f4684487ba'),
(11, 'nebras', 'nebras@gmail.com', '$2y$10$P76dsBznfe1AEZYepJ.GvOem7B8n5q0dCtO2ZakyGNgOhZRZ3GWZC', 'admin', '2025-05-19 14:09:00', '654+151', 'male', 'e6a97d92d6164e9b0597f9d08a47bbe9b7d3259ddca34e77394386461a7de6ba'),
(14, 'mohanad', 'mo@gmail.com', '$2y$10$sVHPwaHTTKRa6BA0OMLme.qjNLyQSOH/9QU0TOGQ4Dth2iBiXnsY6', 'employee', '2025-05-26 11:39:33', '777', 'male', NULL),
(18, 'عامر طارق عبدالواسع', 'am@gmail.com', '$2y$10$QKbs2vgZi8sdQNZh4Snbj.Q9DCC.x0Vi99pY.AqDrCLGSTeXfl05q', 'user', '2025-05-26 14:29:44', '0734083215', 'male', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `task_updates`
--
ALTER TABLE `task_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `updated_by` (`updated_by`);

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
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `task_updates`
--
ALTER TABLE `task_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_updates`
--
ALTER TABLE `task_updates`
  ADD CONSTRAINT `task_updates_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_updates_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
