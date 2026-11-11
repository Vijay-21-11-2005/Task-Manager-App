-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2026 at 11:46 AM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `task_manager`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `due_date` date DEFAULT NULL,
  `priority` enum('High','Medium','Low') NOT NULL DEFAULT 'Medium',
  `is_done` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `workspace_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `due_date`, `priority`, `is_done`, `created_at`, `workspace_id`) VALUES
(4, 1, 'Complete resume and cover letter', '2026-11-12', 'High', 0, '2026-11-10 14:42:45', 1),
(5, 1, 'Submit final year project proposal', '2026-11-14', 'High', 0, '2026-11-10 14:43:16', 1),
(6, 1, 'Fix database connection error in TaskMaster', '2026-11-11', 'High', 0, '2026-11-10 14:43:38', 1),
(7, 1, 'Prepare for the upcoming technical interview', '2026-11-15', 'High', 0, '2026-11-10 14:44:02', 1),
(8, 1, 'Submit college assignment on Data Structures', '2026-11-13', 'High', 0, '2026-11-10 14:44:29', 1),
(9, 1, 'Pay the electricity bill', '2026-11-10', 'High', 0, '2026-11-10 14:44:51', 1),
(10, 1, 'Update GitHub profile with new projects', '2026-11-16', 'High', 0, '2026-11-10 14:45:11', 1),
(11, 1, 'Study PHP Security Best Practices', '2026-11-18', 'Medium', 0, '2026-11-10 14:45:53', 1),
(12, 1, 'Build the EduPortal dashboard frontend', '2026-11-20', 'Medium', 0, '2026-11-10 14:46:33', 1),
(13, 1, 'Write documentation for the TaskMaster API', '2026-11-19', 'Medium', 0, '2026-11-10 14:47:20', 1),
(14, 1, 'Watch a tutorial on advanced CSS Grid', '2026-11-17', 'Medium', 0, '2026-11-10 14:48:12', 1),
(15, 1, 'Buy groceries for the week', '2026-11-15', 'Medium', 0, '2026-11-10 14:49:59', 1),
(16, 1, 'Fix the UI glitch on mobile view', '2026-11-21', 'Medium', 0, '2026-11-10 14:50:28', 1),
(17, 1, 'Read 20 pages of a tech book', '2026-11-16', 'Medium', 0, '2026-11-10 14:51:00', 1),
(18, 1, 'Update LinkedIn profile picture', '2026-11-25', 'Low', 0, '2026-11-10 14:52:15', 1),
(19, 1, 'Organize the downloads folder on laptop', '2026-11-22', 'Low', 0, '2026-11-10 14:52:35', 1),
(20, 1, 'Plan weekend trip with friends', '2026-11-28', 'Low', 0, '2026-11-10 14:52:55', 1),
(21, 1, 'Try out the new dark mode theme', '2026-11-30', 'Low', 0, '2026-11-10 14:53:17', 1),
(22, 1, 'Add animations to the task cards', '2026-12-02', 'Low', 0, '2026-11-10 14:53:43', 1),
(23, 1, 'Backup the database to a safe drive', '2026-12-05', 'Low', 0, '2026-11-10 14:54:16', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `workspace_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `created_at`, `workspace_id`) VALUES
(1, 'testuser', '$2y$12$4TeaMZUHSDT3Hi2g9wUoXOJmXHBH2sdhtSSwF8O1uy2Zd8lOhQxuO', '2026-11-09 10:25:10', 1),
(2, 'teammate', '$2y$12$jidbTL/nTQx86kV9nfDKmOA8ksBdRyuST3CWYbJhAsssnSBDbDfku', '2026-11-10 12:14:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `workspaces`
--

CREATE TABLE `workspaces` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workspaces`
--

INSERT INTO `workspaces` (`id`, `name`, `created_at`) VALUES
(1, 'Solo Workspace', '2026-11-10 11:32:27'),
(2, 'teammate\'s Workspace', '2026-11-10 12:14:21');

-- --------------------------------------------------------

--
-- Table structure for table `workspace_members`
--

CREATE TABLE `workspace_members` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `workspace_id` int(11) NOT NULL,
  `role` varchar(20) DEFAULT 'member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workspace_members`
--

INSERT INTO `workspace_members` (`id`, `user_id`, `workspace_id`, `role`) VALUES
(1, 1, 1, 'admin'),
(2, 2, 2, 'admin'),
(8, 2, 1, 'member');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_task_workspace` (`workspace_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_user_workspace` (`workspace_id`);

--
-- Indexes for table `workspaces`
--
ALTER TABLE `workspaces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workspace_members`
--
ALTER TABLE `workspace_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `workspace_id` (`workspace_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `workspaces`
--
ALTER TABLE `workspaces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `workspace_members`
--
ALTER TABLE `workspace_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_task_workspace` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_workspace` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workspace_members`
--
ALTER TABLE `workspace_members`
  ADD CONSTRAINT `workspace_members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workspace_members_ibfk_2` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
