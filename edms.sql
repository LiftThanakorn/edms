-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 05:12 PM
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
-- Database: `edms`
--

-- --------------------------------------------------------

--
-- Table structure for table `edms_circular_documents`
--

CREATE TABLE `edms_circular_documents` (
  `document_id` int(11) NOT NULL,
  `document_number` int(11) NOT NULL COMMENT 'เลขที่หนังสือ',
  `document_year` year(4) NOT NULL DEFAULT year(curdate()),
  `title` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `date_sent` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edms_command_documents`
--

CREATE TABLE `edms_command_documents` (
  `document_id` int(11) NOT NULL,
  `document_number` int(11) NOT NULL COMMENT 'เลขที่หนังสือ',
  `document_year` year(4) NOT NULL DEFAULT year(curdate()),
  `title` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `date_sent` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edms_external_in_documents`
--

CREATE TABLE `edms_external_in_documents` (
  `document_id` int(11) NOT NULL,
  `document_number` int(11) NOT NULL COMMENT 'เลขที่หนังสือ',
  `document_year` year(4) NOT NULL DEFAULT year(curdate()),
  `title` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `date_received` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edms_external_out_documents`
--

CREATE TABLE `edms_external_out_documents` (
  `document_id` int(11) NOT NULL,
  `document_number` int(11) NOT NULL COMMENT 'เลขที่หนังสือ',
  `document_year` year(4) NOT NULL DEFAULT year(curdate()),
  `title` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `date_created` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edms_internal_in_documents`
--

CREATE TABLE `edms_internal_in_documents` (
  `document_id` int(11) NOT NULL,
  `document_number` int(11) NOT NULL COMMENT 'เลขทะเบียนรับ',
  `document_year` year(4) NOT NULL COMMENT 'ปีของหนังสือ',
  `document_reference_number` varchar(50) NOT NULL COMMENT 'เลขที่หนังสือ',
  `title` varchar(255) NOT NULL COMMENT 'ชื่อเรื่อง',
  `sender` varchar(255) NOT NULL COMMENT 'ผู้ส่ง',
  `receiver` varchar(255) DEFAULT NULL COMMENT 'ผู้รับ',
  `date_received` date NOT NULL COMMENT 'วันที่รับ',
  `attachment_path` varchar(255) DEFAULT NULL COMMENT 'ไฟล์แนบ',
  `note` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL COMMENT 'หมวดหมู่งาน',
  `created_by` int(11) NOT NULL COMMENT 'ผู้สร้าง',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'เวลาที่สร้างเอกสาร',
  `date_signed` date DEFAULT NULL COMMENT 'ลงวันที่'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edms_internal_out_documents`
--

CREATE TABLE `edms_internal_out_documents` (
  `document_id` int(11) NOT NULL,
  `document_number` int(11) NOT NULL COMMENT 'เลขที่หนังสือ',
  `document_year` year(4) NOT NULL DEFAULT year(curdate()),
  `title` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `date_created` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edms_job_assignment_documents`
--

CREATE TABLE `edms_job_assignment_documents` (
  `assignment_id` int(11) NOT NULL,
  `document_number` int(11) NOT NULL COMMENT 'เลขที่หนังสือ',
  `document_year` year(4) NOT NULL DEFAULT year(curdate()),
  `title` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `document_type` enum('รับ','ส่ง','เวียน','สั่งการ') NOT NULL COMMENT 'ประเภทเอกสาร (รับ, ส่ง, เวียน,สั่งการ)',
  `position_type` enum('สายวิชาการ','สายสนับสนุน') NOT NULL COMMENT 'ประเภทตำแหน่ง',
  `date_created` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edms_users`
--

CREATE TABLE `edms_users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `edms_users`
--

INSERT INTO `edms_users` (`user_id`, `username`, `password`, `first_name`, `last_name`, `email`, `role`, `created_at`) VALUES
(1, 'thanakorn', '$argon2id$v=19$m=65536,t=4,p=1$cG5OYVJGVjB6TWllQVJEYg$PJU6I4QZ8cFlbzDhURxclKjRelsNlOroMJaVz/Bk2pI', 'thanakorn', 'inthaphan', 'test@123.com', 'user', '2024-12-18 14:25:47'),
(2, 'admin', '$argon2id$v=19$m=65536,t=4,p=1$enhnZG1mZzBkdHpOeElTaQ$kSiNB46+o1ydn+JxAE7+qtZEbf3bm6MxcloDfWZeB+M', 'admin', 'admin', 'admin@admin.com', 'admin', '2024-12-18 15:34:31');

-- --------------------------------------------------------

--
-- Table structure for table `edms_work_categories`
--

CREATE TABLE `edms_work_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `edms_work_categories`
--

INSERT INTO `edms_work_categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(1, 'งานกำหนดตำแหน่งทางวิชาการ', '', '2024-12-18 14:27:57'),
(2, 'งานกำหนดตำแหน่งที่สูงขึ้น', 'กพส', '2024-12-19 15:07:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `edms_circular_documents`
--
ALTER TABLE `edms_circular_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `edms_command_documents`
--
ALTER TABLE `edms_command_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `edms_external_in_documents`
--
ALTER TABLE `edms_external_in_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `edms_external_out_documents`
--
ALTER TABLE `edms_external_out_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `edms_internal_in_documents`
--
ALTER TABLE `edms_internal_in_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD UNIQUE KEY `document_reference_number` (`document_reference_number`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `edms_internal_out_documents`
--
ALTER TABLE `edms_internal_out_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `edms_job_assignment_documents`
--
ALTER TABLE `edms_job_assignment_documents`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `edms_users`
--
ALTER TABLE `edms_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `edms_work_categories`
--
ALTER TABLE `edms_work_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `edms_circular_documents`
--
ALTER TABLE `edms_circular_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edms_command_documents`
--
ALTER TABLE `edms_command_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edms_external_in_documents`
--
ALTER TABLE `edms_external_in_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edms_external_out_documents`
--
ALTER TABLE `edms_external_out_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edms_internal_in_documents`
--
ALTER TABLE `edms_internal_in_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edms_internal_out_documents`
--
ALTER TABLE `edms_internal_out_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edms_job_assignment_documents`
--
ALTER TABLE `edms_job_assignment_documents`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edms_users`
--
ALTER TABLE `edms_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `edms_work_categories`
--
ALTER TABLE `edms_work_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `edms_circular_documents`
--
ALTER TABLE `edms_circular_documents`
  ADD CONSTRAINT `edms_circular_documents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `edms_work_categories` (`category_id`),
  ADD CONSTRAINT `edms_circular_documents_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`);

--
-- Constraints for table `edms_command_documents`
--
ALTER TABLE `edms_command_documents`
  ADD CONSTRAINT `edms_command_documents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `edms_work_categories` (`category_id`),
  ADD CONSTRAINT `edms_command_documents_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`);

--
-- Constraints for table `edms_external_in_documents`
--
ALTER TABLE `edms_external_in_documents`
  ADD CONSTRAINT `edms_external_in_documents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `edms_work_categories` (`category_id`),
  ADD CONSTRAINT `edms_external_in_documents_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`);

--
-- Constraints for table `edms_external_out_documents`
--
ALTER TABLE `edms_external_out_documents`
  ADD CONSTRAINT `edms_external_out_documents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `edms_work_categories` (`category_id`),
  ADD CONSTRAINT `edms_external_out_documents_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`);

--
-- Constraints for table `edms_internal_in_documents`
--
ALTER TABLE `edms_internal_in_documents`
  ADD CONSTRAINT `edms_internal_in_documents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `edms_work_categories` (`category_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `edms_internal_in_documents_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `edms_internal_out_documents`
--
ALTER TABLE `edms_internal_out_documents`
  ADD CONSTRAINT `edms_internal_out_documents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `edms_work_categories` (`category_id`),
  ADD CONSTRAINT `edms_internal_out_documents_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`);

--
-- Constraints for table `edms_job_assignment_documents`
--
ALTER TABLE `edms_job_assignment_documents`
  ADD CONSTRAINT `edms_job_assignment_documents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `edms_work_categories` (`category_id`),
  ADD CONSTRAINT `edms_job_assignment_documents_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
