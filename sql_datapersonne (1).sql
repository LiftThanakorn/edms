-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 03, 2025 at 05:20 PM
-- Server version: 8.0.35
-- PHP Version: 8.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sql_datapersonne`
--

-- --------------------------------------------------------

--
-- Table structure for table `edms_certificate_requests`
--

CREATE TABLE `edms_certificate_requests` (
  `request_id` int NOT NULL COMMENT 'รหัสคำขอ',
  `document_number` int NOT NULL COMMENT 'เลขที่หนังสือ',
  `document_year` year NOT NULL COMMENT 'ปีของเอกสาร',
  `receiver` varchar(255) DEFAULT NULL COMMENT 'ผู้รับหนังสือ',
  `date_created` date NOT NULL COMMENT 'วันที่สร้างหนังสือ',
  `attachment_path` varchar(255) DEFAULT NULL COMMENT 'ที่อยู่ไฟล์เอกสารแนบ',
  `note` text COMMENT 'หมายเหตุ',
  `created_by` int DEFAULT NULL COMMENT 'ผู้ที่สร้างคำขอ',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'เวลาที่คำขอถูกสร้าง'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `edms_certificate_requests`
--

INSERT INTO `edms_certificate_requests` (`request_id`, `document_number`, `document_year`, `receiver`, `date_created`, `attachment_path`, `note`, `created_by`, `created_at`) VALUES
(1, 1, '2025', 'นางสาวเจษฏสิตา', '2025-01-02', NULL, 'หนังสือรับรองเงินเดือนและสถานภาพโสด', 2, '2025-01-02 06:30:33');

-- --------------------------------------------------------

--
-- Table structure for table `edms_circular_documents`
--

CREATE TABLE `edms_circular_documents` (
  `document_id` int NOT NULL,
  `document_number` int NOT NULL,
  `document_year` year NOT NULL,
  `title` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `date_sent` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `note` text,
  `category_id` int DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `edms_command_documents`
--

CREATE TABLE `edms_command_documents` (
  `document_id` int NOT NULL,
  `document_number` int NOT NULL,
  `document_year` year NOT NULL,
  `title` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `date_sent` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `note` text,
  `category_id` int DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `edms_external_in_documents`
--

CREATE TABLE `edms_external_in_documents` (
  `document_id` int NOT NULL,
  `document_number` int NOT NULL COMMENT 'เลขที่หนังสือ',
  `document_year` year NOT NULL COMMENT 'ปีของเอกสาร',
  `title` varchar(255) NOT NULL COMMENT 'ชื่อเรื่อง',
  `sender` varchar(255) NOT NULL COMMENT 'ผู้ส่ง',
  `receiver` varchar(255) DEFAULT NULL COMMENT 'ผู้รับ',
  `date_received` date NOT NULL COMMENT 'วันที่รับ',
  `attachment_path` varchar(255) DEFAULT NULL COMMENT 'ไฟล์แนบ',
  `category_id` int DEFAULT NULL COMMENT 'หมวดหมู่งาน',
  `created_by` int DEFAULT NULL COMMENT 'ผู้สร้าง',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'เวลาที่สร้างเอกสาร',
  `note` text COMMENT 'หมายเหตุ',
  `document_reference_number` varchar(100) DEFAULT NULL COMMENT 'เลขที่หนังสืออ้างอิง',
  `date_signed` date DEFAULT NULL COMMENT 'วันที่ลงในหนังสือ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `edms_external_out_documents`
--

CREATE TABLE `edms_external_out_documents` (
  `document_id` int NOT NULL,
  `document_number` int NOT NULL COMMENT 'เลขที่หนังสือ',
  `document_year` year NOT NULL COMMENT 'ปีของเอกสาร',
  `title` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `date_created` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `note` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `edms_id_card_requests`
--

CREATE TABLE `edms_id_card_requests` (
  `request_id` int NOT NULL COMMENT 'รหัสคำขอ',
  `document_number` int NOT NULL COMMENT 'เลขที่คำขอ',
  `document_year` year NOT NULL COMMENT 'ปีของเอกสาร',
  `applicant_name` varchar(255) NOT NULL COMMENT 'ชื่อผู้ยื่นคำขอ',
  `date_submitted` date NOT NULL COMMENT 'วันที่ยื่นคำขอ',
  `attachment_path` varchar(255) DEFAULT NULL COMMENT 'ที่อยู่ไฟล์เอกสารแนบ',
  `note` text COMMENT 'หมายเหตุ',
  `created_by` int DEFAULT NULL COMMENT 'ผู้ที่สร้างคำขอ',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'เวลาที่คำขอถูกสร้าง'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `edms_internal_in_documents`
--

CREATE TABLE `edms_internal_in_documents` (
  `document_id` int NOT NULL,
  `document_number` varchar(20) NOT NULL,
  `document_year` year NOT NULL,
  `document_reference_number` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `date_received` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `note` text,
  `category_id` int DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_signed` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `edms_internal_in_documents`
--

INSERT INTO `edms_internal_in_documents` (`document_id`, `document_number`, `document_year`, `document_reference_number`, `title`, `sender`, `receiver`, `date_received`, `attachment_path`, `note`, `category_id`, `created_by`, `created_at`, `date_signed`) VALUES
(1, '3', '2024', 'อว 0647.07/864', 'ขอผลการประเมินตำแหน่งทางวิชาการ(ดำเนินการแล้ว)', 'อาจารย์สุนันวดี พละศักดิ์', 'admin', '2024-12-20', 'document_6764f90c2174c.pdf', 'ขอผลการประเมินตำแหน่งทางวิชาการเพื่อเป็นหลักฐานประกอบการพิจารณาแต่งตั้งให้ดำรงตำแหน่งทางวิชาการ\r\n', 1, 1, '2024-12-20 04:56:44', '2024-12-18');

-- --------------------------------------------------------

--
-- Table structure for table `edms_internal_out_documents`
--

CREATE TABLE `edms_internal_out_documents` (
  `document_id` int NOT NULL,
  `document_number` int NOT NULL,
  `document_year` year NOT NULL,
  `title` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `date_created` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `note` text,
  `category_id` int DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `edms_internal_out_documents`
--

INSERT INTO `edms_internal_out_documents` (`document_id`, `document_number`, `document_year`, `title`, `sender`, `receiver`, `date_created`, `attachment_path`, `note`, `category_id`, `created_by`, `created_at`) VALUES
(1, 368, '2024', 'การสละสิทธิ์รายงานตัว', 'admin', 'อธิการบดี', '2024-12-18', 'document_6763885fb7751.pdf', '', 3, 1, '2024-12-19 02:22:07'),
(2, 369, '2024', 'แจ้งผลการประเมินผลงานทางวิชาการเพื่อขอกำหนดตำแหน่งทางผู้ช่วยศาสตราจารย์', 'admin', 'อาจารย์ ดร.สุจิตตา ฤทธิ์มนตรี', '2024-12-20', 'document_6765034e66b0d.pdf', '', 1, 1, '2024-12-20 03:20:46'),
(3, 370, '2024', 'แจ้งผลการประเมินผลงานทางวิชาการเพื่อขอกำหนดตำแหน่งทางผู้ช่วยศาสตราจารย์', 'Thanakorn', 'ผศ.ศตวรรษ อุดรศาสตร์', '2024-12-23', 'document_67692ca42fbae.pdf', 'ส่งเอกสารเมื่อวันที่ 23 ธค 67', 1, 4, '2024-12-23 03:17:51'),
(4, 371, '2024', 'แจ้งผลการประเมินผลงานทางวิชาการเพื่อขอกำหนดตำแหน่งทางผู้ช่วยศาสตราจารย์', 'Thanakorn', 'ผศ. ดร. อุณนดาทร มูลเพ็ญ', '2024-12-23', 'document_67692c9401de1.pdf', 'ส่งเอกสารเมื่อวันที่ 23 ธค 67', 1, 4, '2024-12-23 06:48:28'),
(5, 372, '2024', 'แจ้งผลการประเมินตำแหน่งทางวิชาการเพื่อเป็นหลักฐานประกอบการพิจารณาแต่งตั้งให้ดำรงตำแหน่งวิชาการ', 'Thanakorn', 'อาจารย์ ดร.สุนันวดี พละศักดิ์', '2024-12-24', 'document_676b6a381e300.pdf', '', 1, 4, '2024-12-24 04:30:41'),
(8, 1, '2025', 'ยกเลิกประกาศผู้ได้รับการคัดเลือก', 'Piyapun', 'ปิยพันธุ์', '2025-01-03', NULL, 'ยกเลิกประกาศ', 6, 2, '2025-01-03 08:55:20'),
(9, 2, '2025', 'ขออนุมัติงบประมาณ', 'Thanakorn', 'อธิการบดี', '2025-01-03', NULL, 'ค่าตอบแทนประชุมประเมินการสอน อ.ชญตว์ อินทร์ชา', 1, 4, '2025-01-03 09:04:15');

-- --------------------------------------------------------

--
-- Table structure for table `edms_job_assignment_documents`
--

CREATE TABLE `edms_job_assignment_documents` (
  `assignment_id` int NOT NULL,
  `document_number` int NOT NULL COMMENT 'เลขที่หนังสือ',
  `document_reference_number` varchar(50) DEFAULT NULL COMMENT 'เลขที่อ้างอิงหนังสือ',
  `document_year` year NOT NULL COMMENT 'ปีของเอกสาร',
  `title` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `document_type` enum('รับ','ส่ง','เวียน','สั่งการ') NOT NULL COMMENT 'ประเภทเอกสาร (รับ, ส่ง, เวียน,สั่งการ)',
  `position_type` enum('สายวิชาการ','สายสนับสนุน') NOT NULL COMMENT 'ประเภทตำแหน่ง',
  `date_created` date NOT NULL,
  `reference_date` date DEFAULT NULL COMMENT 'วันที่อ้างอิงหนังสือ',
  `attachment_path` varchar(255) DEFAULT NULL,
  `note` text,
  `category_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `edms_job_assignment_documents`
--

INSERT INTO `edms_job_assignment_documents` (`assignment_id`, `document_number`, `document_reference_number`, `document_year`, `title`, `sender`, `receiver`, `document_type`, `position_type`, `date_created`, `reference_date`, `attachment_path`, `note`, `category_id`, `created_by`, `created_at`) VALUES
(1, 1, 'อว 0647.03/2124', '2025', 'เสนอเอกสารที่ใช้ประเมินผลการสอนเพื่อประเมินการสอน', 'Thanakorn', 'ผศ.ดร.กันตภณ พรหมนิกร', 'รับ', 'สายวิชาการ', '2025-01-02', '2024-12-28', NULL, '', 1, 4, '2025-01-02 06:21:09'),
(2, 1, NULL, '2025', 'นำส่งเอกสารหลักฐานที่ใช้ในการประเมินผลการสอน (ฉบับแก้ไข)', 'Thanakorn', 'คณะอนุกรรมการประเมินการสอน อาจารย์ ดร.ธีรพล สืบชมภู', 'เวียน', 'สายวิชาการ', '2025-01-03', NULL, NULL, 'ผศ.เชี่ยวชาญ , ผศ.ดร.นิทิศ , ผศ.ดร.เกรียงศักดิ์', 1, 4, '2025-01-03 02:13:05');

-- --------------------------------------------------------

--
-- Table structure for table `edms_users`
--

CREATE TABLE `edms_users` (
  `user_id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `edms_users`
--

INSERT INTO `edms_users` (`user_id`, `username`, `password`, `first_name`, `last_name`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$argon2id$v=19$m=65536,t=4,p=1$i+A5ktA8P92vYdEcPM20JA$fYdmuBG3cB5s9/7ud6gyi4wJYPra5p/sNZHXTMjfW/k', 'Admin', 'Admin', 'admin@example.com', 'admin', '2024-12-19 02:14:22'),
(2, 'Piyapun', '$argon2id$v=19$m=65536,t=4,p=1$p/bjReAS61rF9Vvt1I9wow$AoF5BMIuCq7WcfUce/9JJXPrWn7kieVXcccUfbH9Nbs', 'ปิยพันธุ์', 'ท้าวบุตร', NULL, 'user', '2024-12-19 02:16:36'),
(3, 'Meawnoi', '$argon2id$v=19$m=65536,t=4,p=1$VNFOrPnJ9vVR7znree52dw$Sj3SWJ5iSYlIJNy8t7Pfwx3kfCpMJnJyBYdXJ+FLUIU', 'กัลยาวดี', 'ศรีบุญจันทร์', NULL, 'user', '2024-12-19 09:57:30'),
(4, 'Thanakorn', '$argon2id$v=19$m=65536,t=4,p=1$gSzsJtKUmkrKXTqc4wEnkg$cemsV3jiwGNNtL4iz808C8UBqTCPZ2vd9vXXR/GI/u8', 'ธนากร', 'อินทพันธ์', NULL, 'admin', '2024-12-20 09:01:19'),
(5, 'Punjarut', '$argon2id$v=19$m=65536,t=4,p=1$idB3o3titZKjCi9k9xRNjw$wki30u032e9yehnULsS7L4v9wr2uVG7NcZqymFuzEwI', 'ปัญจรัตน์', 'ชินคำ', NULL, 'user', '2024-12-20 09:21:38');

-- --------------------------------------------------------

--
-- Table structure for table `edms_work_categories`
--

CREATE TABLE `edms_work_categories` (
  `category_id` int NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `edms_work_categories`
--

INSERT INTO `edms_work_categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(1, 'งานกำหนดตำแหน่งทางวิชาการ', 'กพว', '2024-12-19 02:21:23'),
(2, 'งานกำหนดตำแหน่งที่สูงขึ้น', 'กพส', '2024-12-19 02:21:31'),
(3, 'งานสรรหาและบรรจุแต่งตั้ง', '', '2024-12-19 02:21:40'),
(4, 'งานการเงินและพัสดุ', '', '2024-12-20 03:22:08'),
(5, 'งานธุรการ สารบรรณ', '', '2024-12-20 03:22:34'),
(6, 'งานอื่นๆ', '', '2024-12-20 03:20:20');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '#3788d8',
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `start_date`, `end_date`, `color`, `created_by`, `created_at`) VALUES
(1, 'ประชุมประเมินการสอน', 'ประชุมประเมินการสอน อ.ชญตว์', '2025-01-08 03:00:00', '2025-01-08 05:00:00', '#ff00ae', 4, '2025-01-03 06:52:22');

-- --------------------------------------------------------

--
-- Table structure for table `staff_journal`
--

CREATE TABLE `staff_journal` (
  `STAFF_JOURNAL_ID` int NOT NULL,
  `ACADEMIC_YEAR` varchar(4) COLLATE utf8mb4_general_ci NOT NULL,
  `SEMESTER` varchar(1) COLLATE utf8mb4_general_ci NOT NULL,
  `MONTH_UPLOAD` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `UNIV_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `CAMPUS_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `CITIZEN_ID` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `YEAR_OF_PUBLISH` varchar(4) COLLATE utf8mb4_general_ci NOT NULL,
  `MONTH_OF_PUBLISH` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `FAC_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `PROGRAM_NAME_TH` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ARTICLE_ID` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `ARTICLE_NAME` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `RESEARCH_GROUP` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `RESEARCH_SCAT_TH` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `RESEARCH_RESULT` text COLLATE utf8mb4_general_ci NOT NULL,
  `RESEARCH_PERIOD` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `KEYWORD` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `JOURNAL_NAME` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `YEAR_NUMBER` varchar(4) COLLATE utf8mb4_general_ci NOT NULL,
  `PRINTED_MONTH` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `INTELLECTUAL_PROPERTY_TYPE_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_lecture`
--

CREATE TABLE `staff_lecture` (
  `STAFF_LECTURE_ID` int NOT NULL,
  `ACADEMIC_YEAR` varchar(4) COLLATE utf8mb4_general_ci NOT NULL,
  `SEMESTER` varchar(1) COLLATE utf8mb4_general_ci NOT NULL,
  `UNIV_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `CAMPUS_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `CITIZEN_ID` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `LECTURE_TOPIC` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ORGANIZER_NAME` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `PLACE_NAME` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_proceeding`
--

CREATE TABLE `staff_proceeding` (
  `STAFF_PROCEEDING_ID` int NOT NULL,
  `ACADEMIC_YEAR` varchar(4) COLLATE utf8mb4_general_ci NOT NULL,
  `SEMESTER` varchar(1) COLLATE utf8mb4_general_ci NOT NULL,
  `MONTH_UPLOAD` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `UNIV_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `CAMPUS_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `CITIZEN_ID` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `ACADEMIC_MEETING_DATE` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `FAC_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `PROGRAM_NAME_TH` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ARTICLE_ID` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `ARTICLE_NAME` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `RESEARCH_GROUP` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `RESEARCH_SCAT_TH` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `RESEARCH_RESULT` text COLLATE utf8mb4_general_ci NOT NULL,
  `RESEARCH_PERIOD` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `KEYWORD` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `MEETING_NAME` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ACADEMIC_MEETING_ORGANIZER` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ACADEMIC_MEETING_LOCATION` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ACADEMIC_MEETING_COUNTRY` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ACADEMIC_MEETING_LEVEL` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `INTELLECTUAL_PROPERTY_TYPE_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_training`
--

CREATE TABLE `staff_training` (
  `STAFF_TRAINING_ID` int NOT NULL,
  `ACADEMIC_YEAR` varchar(4) COLLATE utf8mb4_general_ci NOT NULL,
  `SEMESTER` varchar(1) COLLATE utf8mb4_general_ci NOT NULL,
  `UNIV_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `CAMPUS_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `CITIZEN_ID` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `TRAINING_DATE` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `COURSETRAINING_NAME` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `COURSE_ORGANIZER` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `TRAINING_LOCATION` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_work`
--

CREATE TABLE `staff_work` (
  `STAFF_WORK_ID` int NOT NULL,
  `ACADEMIC_YEAR` varchar(4) COLLATE utf8mb4_general_ci NOT NULL,
  `SEMESTER` varchar(1) COLLATE utf8mb4_general_ci NOT NULL,
  `UNIV_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `CAMPUS_CODE` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `CITIZEN_ID` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `WORK_STARTDATE` date NOT NULL,
  `WORK_UNIV_CODE` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `WORK_FAC_CODE` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ACADEMIC_STANDING_CODE` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `EXECUTIVE_POSITION_CODE` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `st_academic_positions`
--

CREATE TABLE `st_academic_positions` (
  `id` int NOT NULL,
  `academic_position_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `st_academic_positions`
--

INSERT INTO `st_academic_positions` (`id`, `academic_position_name`) VALUES
(1, 'ไม่มี'),
(3, 'ศาสตราจารย์'),
(4, 'รองศาสตราจารย์'),
(5, 'ผู้ช่วยศาสตราจารย์');

-- --------------------------------------------------------

--
-- Table structure for table `st_budget_types`
--

CREATE TABLE `st_budget_types` (
  `id` int NOT NULL,
  `budget_type_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `st_budget_types`
--

INSERT INTO `st_budget_types` (`id`, `budget_type_name`) VALUES
(1, 'งบประมาณแผ่นดิน'),
(2, 'งบประมาณรายได้');

-- --------------------------------------------------------

--
-- Table structure for table `st_departments`
--

CREATE TABLE `st_departments` (
  `id` int NOT NULL,
  `department_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `st_departments`
--

INSERT INTO `st_departments` (`id`, `department_name`) VALUES
(1, 'กองกลาง'),
(2, 'กองนโยบายและแผน'),
(3, 'สำนักงานส่งเสริมวิชาการและจัดการเรียนรู้ตลอดชีวิต (สสร.)'),
(4, 'คณะศิลปศาสตร์และวิทยาศาสตร์'),
(5, 'คณะครุศาสตร์และการพัฒนามนุษย์'),
(6, 'คณะบริหารธุรกิจและการบัญชี'),
(7, 'คณะนิติรัฐศาสตร์'),
(8, 'คณะเทคโนโลยีสารสนเทศ'),
(9, 'คณะพยาบาลศาสตร์'),
(10, 'บัณฑิตวิทยาลัย'),
(11, 'ศูนย์บริการวิทยาศาสตร์สุขภาพ'),
(12, 'โรงเรียนสาธิตมหาวิทยาลัยราชภัฏร้อยเอ็ด');

-- --------------------------------------------------------

--
-- Table structure for table `st_employee_types`
--

CREATE TABLE `st_employee_types` (
  `id` int NOT NULL,
  `type_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `st_employee_types`
--

INSERT INTO `st_employee_types` (`id`, `type_name`) VALUES
(1, 'พนักงานมหาลัย'),
(2, 'พนักงานประจำตามสัญญา'),
(3, 'ข้าราชการ'),
(4, 'พนักงานราชการ'),
(5, 'พนักงานมหาลัย(เฉพาะกิจ)');

-- --------------------------------------------------------

--
-- Table structure for table `st_images`
--

CREATE TABLE `st_images` (
  `id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `st_images`
--

INSERT INTO `st_images` (`id`, `staff_id`, `image_path`, `uploaded_at`) VALUES
(1, 1, 'uploads/1_1723087971.png', '2024-08-08 03:05:48'),
(2, 2, 'uploads/2_1723087566.png', '2024-08-08 03:26:06');

-- --------------------------------------------------------

--
-- Table structure for table `st_management_positions`
--

CREATE TABLE `st_management_positions` (
  `id` int NOT NULL,
  `management_position_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `st_management_positions`
--

INSERT INTO `st_management_positions` (`id`, `management_position_name`) VALUES
(1, 'ไม่มี'),
(2, 'อธิการบดี'),
(3, 'รองอธิการบดี'),
(4, 'คณบดี'),
(5, 'หัวหน้าหน่วยงาน'),
(6, 'รองคณบดี'),
(7, 'ผู้อำนวยการกอง'),
(8, 'ตำแหน่งอื่นๆ'),
(9, 'รองผู้อำนวยการกอง');

-- --------------------------------------------------------

--
-- Table structure for table `st_prefixes`
--

CREATE TABLE `st_prefixes` (
  `id` int NOT NULL,
  `prefix_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `st_prefixes`
--

INSERT INTO `st_prefixes` (`id`, `prefix_name`) VALUES
(1, 'นาย'),
(2, 'นางสาว'),
(3, 'นาง'),
(4, 'ว่าที่ ร.ต. หญิง'),
(5, 'ว่าที่ ร.ต. ชาย');

-- --------------------------------------------------------

--
-- Table structure for table `st_qualifications`
--

CREATE TABLE `st_qualifications` (
  `id` int NOT NULL,
  `qualification_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `st_qualifications`
--

INSERT INTO `st_qualifications` (`id`, `qualification_name`) VALUES
(1, 'ต่ำกว่าปริญญาตรี'),
(2, 'ปริญญาตรี'),
(3, 'ปริญญาโท'),
(4, 'ปริญญาเอก');

-- --------------------------------------------------------

--
-- Table structure for table `st_staff`
--

CREATE TABLE `st_staff` (
  `id` int NOT NULL,
  `prefix_id` int DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `job_position` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `employee_type_id` int DEFAULT NULL,
  `management_position_id` int DEFAULT NULL,
  `academic_position_id` int DEFAULT NULL,
  `support_position_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `unit_id` int DEFAULT NULL,
  `budget_type_id` int DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_general_ci DEFAULT 'user',
  `staff_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `st_type_id` int DEFAULT NULL,
  `qualification_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `st_staff`
--

INSERT INTO `st_staff` (`id`, `prefix_id`, `first_name`, `last_name`, `job_position`, `employee_type_id`, `management_position_id`, `academic_position_id`, `support_position_id`, `department_id`, `unit_id`, `budget_type_id`, `password`, `role`, `staff_name`, `created_at`, `updated_at`, `st_type_id`, `qualification_id`) VALUES
(1, 1, 'ทินภัทร', 'โพธิ์ชัย', 'นักวิชาการศึกษา', 1, 7, 1, 5, 1, 26, 1, '$argon2i$v=19$m=65536,t=4,p=1$dk9aRzEvOUNGNmEuaEFKYw$YVSZPnbkzCGwbe8+YSjPtSDxrINoDyZu3q8UgG4yPGg', 'admin', 'ทินภัทร โพธิ์ชัย', '2024-08-08 10:05:48', '2024-08-31 09:37:51', 2, 3),
(2, 1, 'อภิชิต', 'สุทธิประภา', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 9, 1, 5, 1, 1, 1, '$2y$10$LCDkv7GWpkriJQRZmuFVceFQIs3aPxV2uKrzafbujWIbHFRCz1Sp2', 'user', 'อภิชิต สุทธิประภา', '2024-08-08 10:26:06', '2024-08-09 15:04:17', 2, NULL),
(3, 1, 'เอกชัย', 'จันทะคัด', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 8, 1, 6, 1, 1, 2, '', 'user', 'เอกชัย จันทะคัด', '2024-08-08 10:44:32', '2024-08-09 15:04:17', 2, NULL),
(4, 2, 'รุ่งเพชร', 'โพธิ์ศรี', 'ผู้ปฏิบัติงานบริหาร', 5, 1, 1, 7, 1, 1, 2, '', 'user', 'รุ่งเพชร โพธิ์ศรี', '2024-08-08 10:49:40', '2024-08-09 15:04:17', 2, NULL),
(5, 2, 'กัลยาวดี', 'ศรีบุญจันทร์', 'บุคลากร', 1, 9, 1, 5, 1, 2, 1, '', 'user', 'กัลยาวดี ศรีบุญจันทร์', '2024-08-09 11:36:05', '2024-08-09 11:36:05', 2, NULL),
(6, 2, 'ปิยพันธุ์', 'ท้าวบุตร', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 1, 2, 2, '', 'admin', 'ปิยพันธุ์ ท้าวบุตร', '2024-08-09 15:50:34', '2024-11-15 03:06:34', 2, NULL),
(7, 1, 'ธนากร', 'อินทพันธ์', 'บุคลากร', 2, 1, 1, 1, 1, 2, 2, '$argon2i$v=19$m=65536,t=4,p=1$elBtZEFlb1k0eW94cVZsQw$HMHX87HVin97n3Y4I4cNXngsCDDjLWiE0g/bZYgjhQI', 'admin', 'thanakorn', '2024-08-09 16:01:51', '2024-08-14 11:53:11', 2, NULL),
(8, 2, 'เจษฎ์สิตา', 'เลิศธนบวรพงษ์', 'นักวิชาการเงินและบัญชี', 1, 5, 1, 5, 1, 3, 1, '', 'user', 'เจษฎ์สิตา เลิศธนบวรพงษ์', '2024-08-09 16:02:43', '2024-08-09 16:07:29', 2, NULL),
(9, 2, 'ภทวรัศมิ์', 'ดงใจ', 'นักวิชาการเงินและบัญชี', 1, 8, 1, 5, 1, 3, 1, '', 'user', 'ภทวรัศมิ์ ดงใจ', '2024-08-09 16:21:54', '2024-08-09 16:21:54', 2, NULL),
(10, 2, 'เบญจพร', 'วงค์ไชยชาญ', 'นักวิชาการเงินและบัญชี', 1, 1, 1, 5, 1, 3, 1, '', 'user', 'เบญจพร วงค์ไชยชาญ', '2024-08-09 16:22:58', '2024-08-09 16:22:58', 2, NULL),
(11, 2, 'สุขฤทัย', 'บุตรชาลี', 'นักวิชาการเงินและบัญชี', 1, 1, 1, 5, 1, 3, 1, '', 'user', 'สุขฤทัย บุตรชาลี', '2024-08-14 15:07:07', '2024-08-14 15:07:07', 2, NULL),
(12, 2, 'ประภาพร', 'ขำคมเขตต์', 'นักวิชาการเงินและบัญชี', 1, 1, 1, 5, 1, 3, 1, '', 'user', 'ประภาพร ขำคมเขตต์', '2024-08-14 15:13:44', '2024-08-14 15:13:44', 2, NULL),
(13, 2, 'วรรณนิภา', 'สุทธิประภา', 'นักวิชาการเงินและบัญชี', 4, 1, 1, 1, 1, 3, 1, '', 'user', 'วรรณนิภา สุทธิประภา', '2024-08-14 15:14:21', '2024-08-14 15:14:21', 2, NULL),
(14, 2, 'แพรวพรรณ', 'กุดราศรี', 'นักวิชาการเงินและบัญชี', 4, 1, 1, 1, 1, 3, 1, '', 'user', 'แพรวพรรณ กุดราศรี', '2024-08-15 11:19:17', '2024-08-15 11:19:17', 2, NULL),
(15, 2, 'อังศุมา', 'ถัดเกษม', 'นักวิชาการเงินและบัญชี', 4, 1, 1, 1, 1, 3, 1, '', 'user', 'อังศุมา ถัดเกษม', '2024-08-15 11:26:25', '2024-08-15 11:26:25', 2, NULL),
(16, 2, 'พรพิมล', 'พลเยี่ยม', 'นักวิชาการเงินและบัญชี', 2, 1, 1, 1, 1, 3, 2, '', 'user', 'พรพิมล พลเยี่ยม', '2024-08-15 11:26:53', '2024-08-15 11:26:53', 2, NULL),
(17, 2, 'สญาดา', 'สาลีวรรณ', 'นักวิชาการศึกษา', 1, 5, 1, 5, 1, 4, 2, '', 'user', 'สญาดา สาลีวรรณ', '2024-08-15 11:34:22', '2024-08-15 11:34:36', 2, NULL),
(18, 3, 'ดารณี', 'เนตรถาวร', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 5, 1, 4, 1, '', 'user', 'ดารณี เนตรถาวร', '2024-08-15 11:43:55', '2024-08-15 11:43:55', 2, NULL),
(19, 2, 'สุจิตรา', 'แก้วใส', 'นิติกร', 1, 1, 1, 6, 1, 5, 2, '', 'user', 'สุจิตรา แก้วใส', '2024-08-15 11:45:29', '2024-08-15 11:45:29', 2, NULL),
(20, 1, 'อภิเชษฐ์', 'จีบแก้ว', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 1, 6, 2, '', 'user', 'อภิเชษฐ์ จีบแก้ว', '2024-08-15 11:51:37', '2024-08-15 11:51:37', 2, NULL),
(21, 1, 'ธีระ', 'วงค์ไชยชาญ', 'นักประชาสัมพันธ์', 1, 5, 1, 5, 1, 7, 1, '', 'user', 'ธีระ วงค์ไชยชาญ', '2024-08-15 11:52:16', '2024-08-15 11:52:16', 2, NULL),
(22, 1, 'กันตภณ', 'คชพันธ์', 'นักประชาสัมพันธ์', 2, 1, 1, 1, 1, 7, 2, '', 'user', 'กันตภณ คชพันธ์', '2024-08-15 11:52:43', '2024-08-15 11:52:43', 2, NULL),
(23, 2, 'นิศรา', 'วงสุเพ็ง', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 5, 1, 6, 1, 8, 1, '', 'user', 'นิศรา วงสุเพ็ง', '2024-08-15 11:53:29', '2024-08-15 11:53:29', 2, NULL),
(24, 1, 'ศุภรักษ์', 'ภักดีหาร', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 1, 8, 2, '', 'user', 'ศุภรักษ์ ภักดีหาร', '2024-08-15 11:53:57', '2024-08-15 11:53:57', 2, NULL),
(25, 2, 'กรรณิการ์', 'สุภรัมย์', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 1, 8, 2, '', 'user', 'กรรณิการ์ สุภรัมย์', '2024-08-15 11:54:28', '2024-08-15 11:54:28', 2, NULL),
(26, 2, 'มาลินี', 'สุราช', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 5, 1, 5, 1, 9, 1, '', 'user', 'มาลินี สุราช', '2024-08-15 13:13:58', '2024-08-15 13:13:58', 2, NULL),
(27, 1, 'เอกลักษณ์', 'เปลรินทร์', 'นิติกร', 1, 1, 1, 6, 1, 9, 2, '', 'user', 'เอกลักษณ์ เปลรินทร์', '2024-08-15 13:15:21', '2024-08-15 13:15:21', 2, NULL),
(28, 1, 'วีรชัย', 'ภาชนะวรรณ', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 1, 9, 2, '', 'user', 'วีรชัย ภาชนะวรรณ', '2024-08-15 13:16:28', '2024-08-15 13:16:28', 2, NULL),
(29, 2, 'อมรรัตน์', 'อธิจันทร์', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 5, 1, 6, 1, 10, 1, '', 'user', 'อมรรัตน์ อธิจันทร์', '2024-08-15 13:17:26', '2024-08-15 13:17:26', 2, NULL),
(30, 2, 'ฐิติมา', 'แพงวงษ์', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 1, 11, 2, '', 'user', 'ฐิติมา แพงวงษ์', '2024-08-15 13:17:52', '2024-08-15 13:17:52', 2, NULL),
(31, 1, 'บดินทร์', 'ศิริเกษ', 'นักวิชาการโสตทัศนศึกษา', 4, 5, 1, 1, 1, 12, 1, '', 'user', 'บดินทร์ ศิริเกษ', '2024-08-15 13:18:47', '2024-08-15 13:18:47', 2, NULL),
(32, 1, 'ศรัญญู', 'ด้วงไผ่', 'นักวิชาการโสตทัศนศึกษา', 4, 1, 1, 1, 1, 12, 1, '', 'user', 'ศรัญญู ด้วงไผ่', '2024-08-15 13:19:29', '2024-08-15 13:19:29', 2, NULL),
(33, 1, 'มรรควิน', 'แสนนาใต้', 'นักวิชาการโสตทัศนศึกษา', 2, 1, 1, 1, 1, 12, 2, '', 'user', 'มรรควิน แสนนาใต้', '2024-08-15 13:19:58', '2024-08-15 13:19:58', 2, NULL),
(34, 1, 'สุรินทร์', 'จันทร์ปุ่ม', 'นักวิชาการคอมพิวเตอร์', 1, 5, 1, 5, 1, 13, 1, '', 'user', 'สุรินทร์ จันทร์ปุ่ม', '2024-08-15 13:24:35', '2024-08-15 13:24:35', 2, NULL),
(35, 1, 'สมยศ', 'ศรีหานนท์', 'นักวิชาการคอมพิวเตอร์', 1, 1, 1, 5, 1, 13, 2, '', 'user', 'สมยศ ศรีหานนท์', '2024-08-15 13:25:01', '2024-08-15 13:25:01', 2, NULL),
(36, 1, 'วรสิทธิ์', 'นุกุลการ', 'นักวิชาการคอมพิวเตอร์', 2, 1, 1, 1, 1, 13, 2, '', 'user', 'วรสิทธิ์ นุกุลการ', '2024-08-15 13:25:27', '2024-08-15 13:25:27', 2, NULL),
(37, 3, 'วราภรณ์', 'จันทไชย', 'บรรณารักษ์', 1, 8, 1, 6, 1, 14, 1, '', 'user', 'วราภรณ์ จันทไชย', '2024-08-15 13:38:05', '2024-08-15 13:38:05', 2, NULL),
(38, 2, 'จิรัชยา', 'วิชิตมงคล', 'บรรณารักษ์', 1, 1, 1, 6, 1, 14, 2, '', 'user', 'จิรัชยา วิชิตมงคล', '2024-08-15 13:38:38', '2024-08-15 13:38:38', 2, NULL),
(39, 2, 'พัชรินทร์', 'เตชพละ', 'บรรณารักษ์', 4, 1, 1, 1, 1, 14, 1, '', 'user', 'พัชรินทร์ เตชพละ', '2024-08-15 13:39:35', '2024-08-15 13:39:35', 2, NULL),
(40, 2, 'ปุณิกา', 'โสภาพล', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 5, 1, 14, 1, '', 'user', 'ปุณิกา โสภาพล', '2024-08-15 13:41:08', '2024-08-15 13:41:08', 2, NULL),
(41, 3, 'กิตติมา', 'ยางศิลา', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 5, 1, 14, 1, '', 'user', 'กิตติมา ยางศิลา', '2024-08-15 13:50:12', '2024-08-15 13:50:12', 2, NULL),
(42, 1, 'สว่าง', 'จันทไชย', 'ผู้ปฏิบัติงานห้องสมุด', 5, 1, 1, 7, 1, 14, 2, '', 'user', 'สว่าง จันทไชย', '2024-08-15 13:50:43', '2024-08-15 13:50:43', 2, NULL),
(43, 3, 'ฉวีวรรณ', 'รัตนวรรณ', 'เจ้าหน้าที่ซ่อมบำรุง', 2, 1, 1, 1, 1, 14, 2, '', 'user', 'ฉวีวรรณ รัตนวรรณ', '2024-08-15 13:51:09', '2024-08-15 13:51:09', 2, NULL),
(44, 1, 'ปิยภูมินทร์', 'มุทาไร', 'นักวิชาการคอมพิวเตอร์', 2, 1, 1, 1, 1, 14, 2, '', 'user', 'ปิยภูมินทร์ มุทาไร', '2024-08-15 13:56:01', '2024-08-15 13:56:01', 2, NULL),
(45, 2, 'สมจิตร', 'ประเสริฐสังข์', 'เจ้าหน้าที่จัดชั้น', 2, 1, 1, 1, 1, 14, 2, '', 'user', 'สมจิตร ประเสริฐสังข์', '2024-08-15 13:56:40', '2024-08-15 13:56:40', 2, NULL),
(46, 2, 'ศุภสร', 'โพธาราม', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 1, 14, 2, '', 'user', 'ศุภสร โพธาราม', '2024-08-15 13:57:05', '2024-08-15 13:57:05', 2, NULL),
(47, 1, 'อนันต์', 'เม็กแสงนีน', 'นักวิชาการพัสดุ', 1, 7, 1, 5, 2, 15, 1, '', 'user', 'อนันต์ เม็กแสงนีน', '2024-08-15 13:57:58', '2024-08-15 13:57:58', 2, NULL),
(48, 2, 'ศุภลักษณ์', 'บุญยืน', 'นักวิชาการพัสดุ', 1, 1, 1, 5, 2, 15, 1, '', 'user', 'ศุภลักษณ์ บุญยืน', '2024-08-15 14:03:34', '2024-08-15 14:03:34', 2, NULL),
(49, 2, 'อรพรรณ', 'เปลรินทร์', 'นักวิชาการพัสดุ', 1, 1, 1, 6, 2, 15, 2, '', 'user', 'อรพรรณ เปลรินทร์', '2024-08-15 14:04:12', '2024-08-15 14:04:12', 2, NULL),
(50, 2, 'อรวรรณ', 'หัตชัย', 'นักวิชาการพัสดุ', 2, 1, 1, 1, 2, 15, 2, '', 'user', 'อรวรรณ หัตชัย', '2024-08-15 14:04:37', '2024-08-15 14:04:37', 2, NULL),
(51, 1, 'พงษ์พิจักษณ์', 'แก้วโสม', 'นักวิชาการพัสดุ', 2, 1, 1, 1, 2, 15, 2, '', 'user', 'พงษ์พิจักษณ์ แก้วโสม', '2024-08-15 14:04:59', '2024-08-15 14:04:59', 2, NULL),
(52, 2, 'ลลิตา', 'เพ็งพารา', 'นักวิเคราะห์นโยบายและแผน', 1, 5, 1, 5, 2, 16, 2, '', 'user', 'ลลิตา เพ็งพารา', '2024-08-15 14:07:11', '2024-08-15 14:07:11', 2, NULL),
(53, 2, 'บุษรา', 'จันทร์โตพฤกษ์', 'นักวิเคราะห์นโยบายและแผน', 1, 1, 1, 6, 2, 16, 1, '', 'user', 'บุษรา จันทร์โตพฤกษ์', '2024-08-15 14:07:42', '2024-08-15 14:07:42', 2, NULL),
(54, 1, 'สราวุธ', 'เนตรศิริ', 'นักวิเคราะห์นโยบายและแผน', 4, 1, 1, 1, 2, 16, 1, '', 'user', 'สราวุธ เนตรศิริ', '2024-08-15 14:08:16', '2024-08-15 14:08:16', 2, NULL),
(55, 3, 'มนัสนันท์', 'พรทวีกุล', 'นักวิเคราะห์นโยบายและแผน', 2, 1, 1, 1, 2, 16, 2, '', 'user', 'มนัสนันท์ พรทวีกุล', '2024-08-15 14:11:10', '2024-08-15 14:11:10', 2, NULL),
(56, 2, 'ณัชชา', 'รัตนนิคม', 'นักวิเคราะห์นโยบายและแผน', 2, 1, 1, 1, 2, 16, 2, '', 'user', 'ณัชชา รัตนนิคม', '2024-08-15 14:11:31', '2024-08-15 14:11:31', 2, NULL),
(57, 1, 'สราวุธ', 'ไสยรส', 'วิศวกร', 1, 1, 1, 6, 2, 17, 1, '', 'user', 'สราวุธ ไสยรส', '2024-08-15 14:13:45', '2024-08-15 14:13:45', 2, NULL),
(58, 1, 'พรชัย', 'ท้าวบุตร', 'เจ้าหน้าที่บริหารงานทั่วไป', 4, 1, 1, 1, 2, 17, 1, '', 'user', 'พรชัย ท้าวบุตร', '2024-08-15 14:14:06', '2024-08-15 14:14:06', 2, NULL),
(59, 3, 'จังกร', 'พรรณแสง', 'ผู้ปฏิบัติงานบริหาร', 5, 1, 1, 7, 2, 17, 2, '', 'user', 'จังกร พรรณแสง', '2024-08-15 14:14:31', '2024-08-15 14:14:31', 2, NULL),
(60, 1, 'อุทัย', 'สิงธวัช', 'ผู้ปฏิบัติงานบริหาร', 5, 1, 1, 7, 2, 17, 2, '', 'user', 'อุทัย สิงธวัช', '2024-08-15 14:14:51', '2024-08-15 14:14:51', 2, NULL),
(61, 3, 'สมสมัย', 'ฤทธิยงค์', 'ผู้ปฏิบัติงานบริหาร', 5, 1, 1, 7, 2, 17, 2, '', 'user', 'สมสมัย ฤทธิยงค์', '2024-08-15 14:15:13', '2024-08-15 14:15:13', 2, NULL),
(62, 1, 'ประมวล', 'บุญแสน', 'ผู้ปฏิบัติงานบริหาร', 5, 1, 1, 7, 2, 17, 2, '', 'user', 'ประมวล บุญแสน', '2024-08-15 14:15:31', '2024-08-15 14:15:31', 2, NULL),
(63, 1, 'ปริญญา', 'ดาแก้ว', 'วิศวกร', 1, 5, 1, 6, 2, 21, 1, '', 'user', 'ปริญญา ดาแก้ว', '2024-08-15 14:20:15', '2024-08-15 14:20:15', 2, NULL),
(64, 3, 'กรรณิการ์', 'ดาแก้ว', 'วิศวกร', 2, 1, 1, 1, 2, 21, 2, '', 'user', 'กรรณิการ์ ดาแก้ว', '2024-08-15 14:27:25', '2024-08-15 14:27:25', 2, NULL),
(65, 1, 'ศุภรานนท์', 'ลาศรีทัศน์', 'วิศวกร', 1, 5, 1, 6, 2, 22, 1, '', 'user', 'ศุภรานนท์ ลาศรีทัศน์', '2024-08-15 14:28:23', '2024-08-15 14:28:23', 2, NULL),
(66, 1, 'ทินรัตน์', 'ยะนะโชติ', 'วิศวกร', 1, 1, 1, 6, 2, 22, 2, '', 'user', 'ทินรัตน์ ยะนะโชติ', '2024-08-15 14:29:09', '2024-08-15 14:29:09', 2, NULL),
(67, 1, 'อภิชัย', 'จันทร์ราช', 'ช่างเทคนิค', 2, 1, 1, 1, 2, 22, 2, '', 'user', 'อภิชัย จันทร์ราช', '2024-08-15 14:29:31', '2024-08-15 14:29:31', 2, NULL),
(68, 1, 'นายสุริยันท์', 'วรภูมิ', 'เจ้าหน้าที่ธุรการ', 4, 1, 1, 1, 2, 23, 1, '', 'user', 'นายสุริยันท์ วรภูมิ', '2024-08-15 14:30:01', '2024-08-15 14:30:01', 2, NULL),
(69, 1, 'ศักดิ์ดา', 'รัตนวรรณ', 'ช่างเครื่องยนต์', 5, 1, 1, 7, 2, 23, 2, '', 'user', 'ศักดิ์ดา รัตนวรรณ', '2024-08-15 14:30:44', '2024-08-15 14:30:44', 2, NULL),
(70, 1, 'วุฒิเดช', 'นิยม', 'ช่างเครื่องยนต์', 5, 1, 1, 7, 2, 23, 2, '', 'user', 'วุฒิเดช นิยม', '2024-08-15 14:33:26', '2024-08-15 14:33:26', 2, NULL),
(71, 1, 'พิทักษ์', 'สิทธิ์ศักดิ์', 'ช่างเครื่องยนต์', 5, 1, 1, 7, 2, 23, 2, '', 'user', 'พิทักษ์ สิทธิ์ศักดิ์', '2024-08-15 14:33:57', '2024-08-15 14:33:57', 2, NULL),
(72, 1, 'สุจิตร', 'ไชยฤทธิ์', 'พนักงานขับรถ', 2, 1, 1, 1, 2, 23, 2, '', 'user', 'สุจิตร ไชยฤทธิ์', '2024-08-15 14:34:25', '2024-08-15 14:34:25', 2, NULL),
(73, 1, 'ผดุงศักดิ์', 'ประเสริฐสังข์', 'พนักงานขับรถ', 2, 1, 1, 1, 2, 23, 2, '', 'user', 'ผดุงศักดิ์ ประเสริฐสังข์', '2024-08-15 14:36:07', '2024-08-15 14:36:07', 2, NULL),
(74, 1, 'สุรชัย', 'สุภาพสุนทร', 'พนักงานขับรถ', 2, 1, 1, 1, 2, 23, 2, '', 'user', 'สุรชัย สุภาพสุนทร', '2024-08-15 14:36:59', '2024-08-15 14:36:59', 2, NULL),
(75, 1, 'เดชา', 'วรราช', 'พนักงานขับรถ', 2, 1, 1, 1, 2, 23, 2, '', 'user', 'เดชา วรราช', '2024-08-15 14:37:32', '2024-08-15 14:37:32', 2, NULL),
(76, 1, 'ประเสริฐ', 'ฤทธิยงค์', 'พนักงานขับรถ', 2, 1, 1, 1, 2, 23, 2, '', 'user', 'ประเสริฐ ฤทธิยงค์', '2024-08-15 14:38:07', '2024-08-15 14:38:07', 2, NULL),
(77, 1, 'วัชรธร', 'ภูมิศรี', 'พนักงานขับรถ', 2, 1, 1, 1, 2, 23, 2, '', 'user', 'วัชรธร ภูมิศรี', '2024-08-15 14:43:42', '2024-08-15 14:43:42', 2, NULL),
(78, 1, 'นวกาย', 'บุษบา', 'พนักงานขับรถ', 2, 1, 1, 1, 2, 23, 2, '', 'user', 'นวกาย บุษบา', '2024-08-15 14:44:51', '2024-08-15 14:44:51', 2, NULL),
(79, 1, 'สมนึก', 'พลเยี่ยม', 'เจ้าหน้าที่บริหารงานทั่วไป', 4, 1, 1, 1, 2, 24, 1, '', 'user', 'สมนึก พลเยี่ยม', '2024-08-15 14:57:57', '2024-08-15 14:57:57', 2, NULL),
(80, 2, 'ภิญญาพัชญ์ ', 'วังภูงา', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 5, 4, 25, 1, '', 'user', 'ภิญญาพัชญ์  วังภูงา', '2024-08-15 15:01:47', '2024-08-15 15:01:47', 2, NULL),
(81, 1, 'ชวาลย์', 'เวือมประโคน', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 6, 4, 25, 2, '', 'user', 'ชวาลย์ เวือมประโคน', '2024-08-15 15:15:42', '2024-08-15 15:15:42', 2, NULL),
(82, 3, 'รติรส', 'เสี่ยงสาย', 'นักวิทยาศาสตร์', 1, 1, 1, 6, 4, 25, 2, '', 'user', 'รติรส เสี่ยงสาย', '2024-08-15 15:16:09', '2024-08-15 15:16:09', 2, NULL),
(83, 2, 'กนกวรรณ', 'ศรีดาฮด', 'นักวิทยาศาสตร์', 1, 1, 1, 6, 4, 25, 2, '', 'user', 'กนกวรรณ ศรีดาฮด', '2024-08-15 15:16:52', '2024-08-15 15:16:52', 2, NULL),
(84, 2, 'ไพลิน', 'ทวยหาญ', 'เจ้าหน้าที่บริหารงานทั่วไป', 4, 1, 1, 1, 4, 25, 1, '', 'user', 'ไพลิน ทวยหาญ', '2024-08-15 15:22:15', '2024-08-15 15:22:15', 2, NULL),
(85, 2, 'รุ่งนภา', 'ครองชื่น', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 4, 25, 2, '', 'user', 'รุ่งนภา ครองชื่น', '2024-08-15 15:22:39', '2024-08-15 15:22:39', 2, NULL),
(86, 1, 'พิเชษฐ', 'ศรีนนท์', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 4, 25, 2, '', 'user', 'พิเชษฐ ศรีนนท์', '2024-08-15 15:23:09', '2024-08-15 15:23:09', 2, NULL),
(87, 1, 'กอบศักดิ์', 'วงศ์วิลาศ', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 4, 25, 2, '', 'user', 'กอบศักดิ์ วงศ์วิลาศ', '2024-08-15 15:23:32', '2024-08-15 15:23:32', 2, NULL),
(88, 2, 'ศรัญญา', 'สุทธิประภา', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 4, 25, 2, '', 'user', 'ศรัญญา สุทธิประภา', '2024-08-15 15:27:20', '2024-08-15 15:30:44', 2, NULL),
(89, 1, 'บวรลักษณ์', 'บุญจำเนียร', 'นักวิชาการศึกษา', 1, 1, 1, 5, 5, 25, 1, '', 'user', 'บวรลักษณ์ บุญจำเนียร', '2024-08-15 15:32:49', '2024-08-15 15:32:49', 2, NULL),
(90, 1, 'โอภาส', 'ศิริโท', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 6, 5, 25, 1, '', 'user', 'โอภาส ศิริโท', '2024-08-15 15:33:50', '2024-08-15 15:33:50', 2, NULL),
(91, 1, 'สำเริง', 'คำมีวงษ์', 'นักวิชาการคอมพิวเตอร์', 1, 1, 1, 6, 5, 25, 1, '', 'user', 'สำเริง คำมีวงษ์', '2024-08-15 15:34:14', '2024-08-15 15:34:14', 2, NULL),
(92, 1, 'ภูเบตร', 'ภักดีวุฒิ', 'เจ้าหน้าที่ธุรการ', 4, 1, 1, 1, 5, 25, 1, '', 'user', 'ภูเบตร ภักดีวุฒิ', '2024-08-15 15:34:44', '2024-08-15 15:34:44', 2, NULL),
(93, 2, 'ณิชยา', 'เนืองพันธ์', 'นักทรัพยากรบุคคล', 4, 1, 1, 1, 5, 25, 1, '', 'user', 'ณิชยา เนืองพันธ์', '2024-08-15 15:35:18', '2024-08-15 15:35:18', 2, NULL),
(94, 1, 'ฐนกร', 'บุญสุข', 'นักวิชาการศึกษา', 4, 1, 1, 1, 5, 25, 1, '', 'user', 'ฐนกร บุญสุข', '2024-08-15 15:35:53', '2024-08-15 15:35:53', 2, NULL),
(95, 2, 'ปิยพรวดี', 'ทองดี', 'นักวิชาการศึกษา', 2, 1, 1, 1, 5, 25, 2, '', 'user', 'ปิยพรวดี ทองดี', '2024-08-15 15:36:22', '2024-08-15 15:36:22', 2, NULL),
(96, 1, 'คมเพชร', 'ทิพอาจ', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 5, 25, 2, '', 'user', 'คมเพชร ทิพอาจ', '2024-08-15 15:36:51', '2024-08-15 15:36:51', 2, NULL),
(97, 2, 'มีนราศรี', 'ประเสริฐสังข์', 'นักวิชาการศึกษา', 2, 1, 1, 1, 5, 25, 2, '', 'user', 'มีนราศรี ประเสริฐสังข์', '2024-08-15 15:37:18', '2024-08-15 15:37:18', 2, NULL),
(98, 1, 'โชคชัย', 'ศรีชัย', 'นักวิชาการศึกษา', 1, 8, 1, 5, 6, 25, 1, '', 'user', 'โชคชัย ศรีชัย', '2024-08-15 15:41:36', '2024-08-15 15:41:36', 2, NULL),
(99, 1, 'ธนทรัพย์', 'อนารัตน์', 'นักวิชาการโสตทัศนศึกษา', 1, 1, 1, 5, 6, 25, 1, '', 'user', 'ธนทรัพย์ อนารัตน์', '2024-08-15 15:51:51', '2024-08-15 15:51:51', 2, NULL),
(100, 1, 'ปวีกร', 'ท้าวบุตร', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 6, 6, 25, 2, '', 'user', 'ปวีกร ท้าวบุตร', '2024-08-15 15:52:21', '2024-08-15 15:52:21', 2, NULL),
(101, 2, 'สมปอง', 'ละออ', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 6, 6, 25, 2, '', 'user', 'สมปอง ละออ', '2024-08-15 15:52:47', '2024-08-15 15:52:47', 2, NULL),
(102, 2, 'เพียงเพ็ญ', 'สุทธิประภา', 'เจ้าหน้าที่บริหารงานทั่วไป', 4, 1, 1, 1, 6, 25, 1, '', 'user', 'เพียงเพ็ญ สุทธิประภา', '2024-08-15 15:54:07', '2024-08-15 15:54:07', 2, NULL),
(103, 1, 'อานนท์', 'จันจิตร', 'นักวิชาการศึกษา', 2, 1, 1, 1, 6, 25, 2, '', 'user', 'อานนท์ จันจิตร', '2024-08-15 15:54:51', '2024-08-15 15:54:51', 2, NULL),
(104, 2, 'ชนิตชญา', 'นาสิงห์ทอง', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 6, 25, 2, '', 'user', 'ชนิตชญา นาสิงห์ทอง', '2024-08-15 15:55:26', '2024-08-15 15:55:26', 2, NULL),
(105, 2, 'วาสนา', 'เลิศมะเลา', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 8, 1, 5, 7, 25, 2, '', 'user', 'วาสนา เลิศมะเลา', '2024-08-15 15:56:01', '2024-08-15 15:56:39', 2, NULL),
(106, 2, 'วาสนา', 'บุตรพรม', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 5, 7, 25, 2, '', 'user', 'วาสนา บุตรพรม', '2024-08-15 15:56:32', '2024-08-15 15:56:32', 2, NULL),
(107, 2, 'มนนิภา', 'ศรีรักษา', 'เจ้าหน้าที่ธุรการ', 4, 1, 1, 1, 7, 25, 1, '', 'user', 'มนนิภา ศรีรักษา', '2024-08-15 15:57:14', '2024-08-15 15:57:14', 2, NULL),
(108, 1, 'สุรชัย', 'สีดา', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 7, 25, 2, '', 'user', 'สุรชัย สีดา', '2024-08-15 15:57:34', '2024-08-15 15:57:34', 2, NULL),
(109, 1, 'ศักดิ์ดา', 'สุทธิประภา', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 5, 8, 25, 1, '', 'user', 'ศักดิ์ดา สุทธิประภา', '2024-08-15 15:58:15', '2024-08-15 15:58:15', 2, NULL),
(110, 2, 'รัฐพร', 'ภูชัย', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 8, 25, 2, '', 'user', 'รัฐพร ภูชัย', '2024-08-15 15:58:39', '2024-08-15 15:58:39', 2, NULL),
(111, 2, 'นิชาภา', 'ศรีพนม', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 5, 9, 25, 2, '', 'user', 'นิชาภา ศรีพนม', '2024-08-15 15:59:11', '2024-08-15 15:59:11', 2, NULL),
(112, 2, 'แสงจันทร์', 'จันทร์โท', 'บรรณารักษ์', 1, 1, 1, 5, 9, 25, 2, '', 'user', 'แสงจันทร์ จันทร์โท', '2024-08-15 16:01:52', '2024-08-15 16:01:52', 2, NULL),
(113, 1, 'เชาวฤทธิ์', 'โสภาพล', 'นักวิชาการโสตทัศนศึกษา', 2, 1, 1, 1, 9, 25, 2, '', 'user', 'เชาวฤทธิ์ โสภาพล', '2024-08-15 16:02:27', '2024-08-15 16:02:27', 2, NULL),
(114, 4, 'ศุภชญา', 'โลตุริต', 'เจ้าหน้าที่การเงินและบัญชี', 2, 1, 1, 1, 9, 25, 2, '', 'user', 'ศุภชญา โลตุริต', '2024-08-15 16:03:55', '2024-08-15 16:03:55', 2, NULL),
(115, 2, 'ปนัดดา', 'ชัยปัญญา', 'เจ้าหน้าที่ห้องปฏิบัติการพยาบาล', 2, 1, 1, 1, 9, 25, 2, '', 'user', 'ปนัดดา ชัยปัญญา', '2024-08-15 16:04:48', '2024-08-15 16:04:48', 2, NULL),
(116, 1, 'พิมหันต์', 'กันเสนา', 'นักวิชาการศึกษา', 1, 1, 1, 6, 9, 25, 2, '', 'user', 'พิมหันต์ กันเสนา', '2024-08-15 16:05:06', '2024-08-15 16:05:35', 2, NULL),
(117, 2, 'สุนิสา', 'แมดคำ', 'นักวิชาการศึกษา', 1, 1, 1, 6, 9, 25, 2, '', 'user', 'สุนิสา แมดคำ', '2024-08-15 16:06:02', '2024-08-15 16:06:02', 2, NULL),
(118, 2, 'อ้อมขวัญ', 'กฤษณะกาฬ', 'นักวิชาการศึกษา', 1, 1, 1, 6, 10, 25, 1, '', 'user', 'อ้อมขวัญ กฤษณะกาฬ', '2024-08-15 16:08:02', '2024-08-15 16:08:02', 2, NULL),
(119, 2, 'พัฒนา', 'สัจจาวาท', 'นักวิชาการศึกษา', 1, 1, 1, 6, 10, 25, 1, '', 'user', 'พัฒนา สัจจาวาท', '2024-08-15 16:08:24', '2024-08-15 16:08:24', 2, NULL),
(120, 2, 'ชีวรัตน์', 'ศีลพันธุ์', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 6, 10, 25, 2, '', 'user', 'ชีวรัตน์ ศีลพันธุ์', '2024-08-15 16:08:53', '2024-08-15 16:08:53', 2, NULL),
(121, 2, 'แพรวนภา', 'เกี้ยงเก่า', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 6, 11, 25, 2, '', 'user', 'แพรวนภา เกี้ยงเก่า', '2024-08-15 16:09:17', '2024-08-15 16:09:17', 2, NULL),
(122, 2, 'กรธิดา', 'เขื่อนคำ', 'นักวิชาการสาธารณสุข', 2, 1, 1, 1, 11, 25, 2, '', 'user', 'กรธิดา เขื่อนคำ', '2024-08-15 16:09:57', '2024-09-26 09:36:04', 2, NULL),
(123, 1, 'คุณากร', 'พลเยี่ยม', 'นักวิชาการสาธารณสุข', 2, 1, 1, 1, 11, 25, 2, '', 'user', 'คุณากร พลเยี่ยม', '2024-08-15 16:11:11', '2024-08-15 16:11:11', 2, NULL),
(124, 2, 'นวลละออง', 'กันแก้ว', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 5, 3, 28, 1, '', 'user', 'นวลละออง กันแก้ว', '2024-08-16 09:29:05', '2024-08-16 09:29:05', 2, NULL),
(125, 3, 'สุภาวดี', 'ศรีลัย', 'นักวิชาการศึกษา', 1, 1, 1, 5, 3, 28, 1, '', 'user', 'สุภาวดี ศรีลัย', '2024-08-16 09:29:39', '2024-08-16 09:29:39', 2, NULL),
(126, 3, 'นันท์นภัส', 'ประเสริฐสังข์', 'นักวิชาการศึกษา', 1, 1, 1, 5, 3, 28, 1, '', 'user', 'นันท์นภัส ประเสริฐสังข์', '2024-08-16 09:30:07', '2024-08-16 09:30:07', 2, NULL),
(127, 2, 'สุกัญญา', 'แพงโพนทอง', 'นักวิชาการศึกษา', 1, 1, 1, 6, 3, 28, 2, '', 'user', 'สุกัญญา แพงโพนทอง', '2024-08-16 09:31:03', '2024-08-16 09:31:03', 2, NULL),
(128, 1, 'ปรัชญา', 'สุทธิประภา', 'นักวิชาการคอมพิวเตอร์', 1, 1, 1, 6, 3, 28, 2, '', 'user', 'ปรัชญา สุทธิประภา', '2024-08-16 09:31:57', '2024-08-16 09:31:57', 2, NULL),
(129, 3, 'เนาวะพา', 'อนารัตน์', 'นักวิชาการศึกษา', 1, 1, 1, 5, 3, 28, 2, '', 'user', 'เนาวะพา อนารัตน์', '2024-08-16 09:32:25', '2024-08-16 09:32:25', 2, NULL),
(130, 2, 'จินตนา', 'โพธิ์ชัย', 'นักวิชาการศึกษา', 1, 1, 1, 6, 3, 28, 2, '', 'user', 'จินตนา โพธิ์ชัย', '2024-08-16 09:32:49', '2024-08-16 09:32:49', 2, NULL),
(131, 1, 'อนุวัตร', 'ไชยรินทร์', 'นักวิชาการศึกษา', 1, 1, 1, 6, 3, 28, 2, '', 'user', 'อนุวัตร ไชยรินทร์', '2024-08-16 09:33:12', '2024-08-16 09:33:12', 2, NULL),
(132, 2, 'วิลาวรรณ', 'กมลสินธิ์', 'นักวิชาการศึกษา', 1, 1, 1, 6, 3, 28, 2, '', 'user', 'วิลาวรรณ กมลสินธิ์', '2024-08-16 09:37:18', '2024-08-16 09:37:18', 2, NULL),
(133, 2, 'นิภาพร', 'ประเสริฐสังข์', 'นักวิชาการศึกษา', 4, 1, 1, 1, 3, 28, 1, '', 'user', 'นิภาพร ประเสริฐสังข์', '2024-08-16 09:37:48', '2024-08-16 09:37:48', 2, NULL),
(134, 1, 'เกียรติกมล', 'เกลี้ยงเกลา', 'นักวิชาการคอมพิวเตอร์', 2, 1, 1, 1, 3, 28, 2, '', 'user', 'เกียรติกมล เกลี้ยงเกลา', '2024-08-16 09:38:17', '2024-08-16 09:38:17', 2, NULL),
(135, 2, 'ถิรญา', 'พิมพ์พรม', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 8, 1, 5, 3, 29, 1, '', 'user', 'ถิรญา พิมพ์พรม', '2024-08-16 09:41:50', '2024-08-16 09:41:50', 2, NULL),
(136, 2, 'สิรินันท์', 'จันทร์หยวก', 'นักวิชาการศึกษา', 1, 1, 1, 6, 3, 29, 1, '', 'user', 'สิรินันท์ จันทร์หยวก', '2024-08-16 09:42:26', '2024-08-16 09:42:26', 2, NULL),
(137, 3, 'บังอร', 'อุดมศักดิ์', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, NULL, 1, 5, 3, 29, 1, '', 'user', 'บังอร อุดมศักดิ์', '2024-08-16 09:42:49', '2024-08-16 09:42:49', 2, NULL),
(138, 2, 'สุวิมล', 'แทเรือง', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 5, 3, 29, 1, '', 'user', 'สุวิมล แทเรือง', '2024-08-16 09:43:24', '2024-08-16 09:43:24', 2, NULL),
(139, 2, 'อรัญญา', 'น้อยบัวทิพย์', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 5, 3, 29, 1, '', 'user', 'อรัญญา น้อยบัวทิพย์', '2024-08-16 09:43:57', '2024-08-16 09:43:57', 2, NULL),
(140, 2, 'โชติกา', 'เอกพันธุ์', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 5, 3, 29, 2, '', 'user', 'โชติกา เอกพันธุ์', '2024-08-16 09:45:15', '2024-08-16 09:45:15', 2, NULL),
(141, 3, 'วราพร', 'เตียนพลกรัง', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 6, 3, 29, 2, '', 'user', 'วราพร เตียนพลกรัง', '2024-08-16 09:45:43', '2024-08-16 09:45:43', 2, NULL),
(142, 1, 'ปกครอง', 'เปรินทร์', 'เจ้าหน้าที่ธุรการ', 4, 1, 1, 1, 3, 29, 1, '', 'user', 'ปกครอง เปรินทร์', '2024-08-16 09:46:15', '2024-08-16 09:46:15', 2, NULL),
(143, 1, 'เกียรติดำรุงวุฒิ นาสิงทอง', 'เจริญรัชตานันท์', 'เจ้าหน้าที่บริหารงานทั่วไป', 4, 1, 1, 1, 3, 29, 1, '', 'user', 'เกียรติดำรุงวุฒิ นาสิงทอง เจริญรัชตานันท์', '2024-08-16 09:47:05', '2024-08-16 09:47:05', 2, NULL),
(144, 2, 'นวลจันทร์', 'แวงวรรณ', 'นักวิชาการศึกษา', 2, 1, 1, 1, 3, 29, 2, '', 'user', 'นวลจันทร์ แวงวรรณ', '2024-08-16 10:05:17', '2024-08-16 10:05:17', 2, NULL),
(145, 1, 'พิสุทธิ์', 'คชาชัด', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 3, 29, 2, '', 'user', 'พิสุทธิ์ คชาชัด', '2024-08-16 10:05:39', '2024-08-16 10:05:39', 2, NULL),
(146, 2, 'สุภรัตน์', 'มะเริงสิทธิ์', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 3, 29, 2, '', 'user', 'สุภรัตน์ มะเริงสิทธิ์', '2024-08-16 10:06:00', '2024-08-16 10:06:00', 2, NULL),
(147, 1, 'ธีระวุฒิ', 'แฟสันเทียะ', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 3, 29, 2, '', 'user', 'ธีระวุฒิ แฟสันเทียะ', '2024-08-16 10:06:33', '2024-08-16 10:06:33', 2, NULL),
(148, 1, 'วัชรกร', 'เนตรถาวร', 'นักวิชาการศึกษา', 1, 1, 1, 5, 3, 30, 1, '', 'user', 'วัชรกร เนตรถาวร', '2024-08-16 10:22:34', '2024-08-16 10:22:34', 2, NULL),
(149, 1, 'ประจิตร', 'ประเสริฐสังข์', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 6, 3, 30, 1, '', 'user', 'ประจิตร ประเสริฐสังข์', '2024-08-16 10:23:02', '2024-08-16 10:23:02', 2, NULL),
(150, 2, 'ณิชากมล', 'ศรีอาราม', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 5, 3, 30, 2, '', 'user', 'ณิชากมล ศรีอาราม', '2024-08-16 10:23:31', '2024-08-16 10:23:31', 2, NULL),
(151, 3, 'ยุพเรศน์', 'ประทีป ณ ถลาง', 'เจ้าหน้าที่บริหารงานทั่วไป', 1, 1, 1, 6, 3, 30, 2, '', 'user', 'ยุพเรศน์ ประทีป ณ ถลาง', '2024-08-16 10:24:19', '2024-08-16 10:24:19', 2, NULL),
(152, 2, 'พรทิวา', 'สารจันทร์', 'เจ้าหน้าที่บริหารงานทั่วไป', 4, 1, 1, 1, 3, 30, 1, '', 'user', 'พรทิวา สารจันทร์', '2024-08-16 10:24:44', '2024-08-16 10:24:44', 2, NULL),
(153, 1, 'ณัฐดนัย', 'สอนวงษ์แก้ว', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 3, 30, 2, '', 'user', 'ณัฐดนัย สอนวงษ์แก้ว', '2024-08-16 10:26:10', '2024-08-16 10:26:10', 2, NULL),
(154, 2, 'ปวันรัตน์', 'ประเสริฐสังข์', 'เจ้าหน้าที่บริหารงานทั่วไป', 2, 1, 1, 1, 12, 31, 2, '', 'user', 'ปวันรัตน์ ประเสริฐสังข์', '2024-08-16 10:26:35', '2024-08-16 10:26:35', 2, NULL),
(155, 1, 'วัชรากร', 'วงศ์คำจันทร์', 'รองอธิการบดีฝ่ายบริหารและนโยบาย', 3, 3, 5, 1, 4, 26, 1, '', 'user', 'วัชรากร วงศ์คำจันทร์', '2024-08-16 14:35:02', '2024-08-16 14:35:02', 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `st_support_positions`
--

CREATE TABLE `st_support_positions` (
  `id` int NOT NULL,
  `support_position_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `st_support_positions`
--

INSERT INTO `st_support_positions` (`id`, `support_position_name`) VALUES
(1, 'ไม่มี'),
(3, 'เชี่ยวชาญ'),
(4, 'ชำนาญการพิเศษ'),
(5, 'ชำนาญการ'),
(6, 'ปฏิบัติการ'),
(7, 'ปฏิบัติงาน');

-- --------------------------------------------------------

--
-- Table structure for table `st_type`
--

CREATE TABLE `st_type` (
  `id` int NOT NULL,
  `type_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `st_type`
--

INSERT INTO `st_type` (`id`, `type_name`) VALUES
(1, 'สายวิชาการ'),
(2, 'สายสนับสนุนวิชาการ');

-- --------------------------------------------------------

--
-- Table structure for table `st_units`
--

CREATE TABLE `st_units` (
  `id` int NOT NULL,
  `unit_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `st_units`
--

INSERT INTO `st_units` (`id`, `unit_name`) VALUES
(1, 'ฝ่ายธุรการ'),
(2, 'ฝ่ายการเจ้าหน้าที่'),
(3, 'ฝ่ายการเงิน'),
(4, 'ฝ่ายประกันคุณภาพ'),
(5, 'ฝ่ายนิติการ'),
(6, 'ฝ่ายกิจการพิเศษ'),
(7, 'ฝ่ายประชาสัมพันธ์'),
(8, 'ฝ่ายเลขานุการ'),
(9, 'สำนักงานเลขานุการสภามหาวิทยาลัย '),
(10, 'ฝ่ายวิเทศสัมพันธ์และการศึกษานานาชาติ'),
(11, 'ศูนย์ภาษา'),
(12, 'ศูนย์นวัตกรรมและสื่อ'),
(13, 'ศูนย์คอมพิวเตอร์'),
(14, 'ศูนย์วิทยบริการ'),
(15, 'ฝ่ายพัสดุ '),
(16, 'ฝ่ายแผนงานและงบประมาณ'),
(17, 'ฝ่ายอาคารและสถานที่ '),
(21, 'ฝ่ายก่อสร้างและภูมิทัศน์'),
(22, 'ฝ่ายสาธารณูปโภค'),
(23, 'ฝ่ายยานพาหนะ'),
(24, 'ฝ่ายสวัสดิการ'),
(25, 'สำนักงานคณบดี'),
(26, 'สำนักงานอธิการบดี'),
(28, 'สำนักวิชาการและประมวลผล'),
(29, 'สำนักกิจการนักศึกษา'),
(30, 'สถาบันวิจัยและพัฒนา'),
(31, 'โรงเรียนสาธิตมหาวิทยาลัยราชภัฏร้อยเอ็ด');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'piyapun', '$2y$10$uZ1jb0ALhJpp2SoW3ppbhuRZ9S1qOyJz0r4KpJi/AvJxyO4zK0Ho2', 'admin', '2024-06-25 01:49:24', '2024-06-25 01:49:24'),
(2, 'thanakorn', '$2y$10$3kjRr9dMHf5jFHj0qpRqZeIANinvEhJWiyvml5yPPdGca7djEIlc.', 'admin', '2024-06-25 01:49:39', '2024-06-25 01:49:39'),
(3, 'thanakornlift', '$2y$10$THuJY9oGNC2hYj1L/MhDY.JxVWjsdPJIKu458qfpaTSTh0uL8za5q', 'admin', '2024-06-25 08:28:17', '2024-06-25 08:28:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `edms_certificate_requests`
--
ALTER TABLE `edms_certificate_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `created_by` (`created_by`);

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
-- Indexes for table `edms_id_card_requests`
--
ALTER TABLE `edms_id_card_requests`
  ADD PRIMARY KEY (`request_id`),
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
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `staff_journal`
--
ALTER TABLE `staff_journal`
  ADD PRIMARY KEY (`STAFF_JOURNAL_ID`);

--
-- Indexes for table `staff_lecture`
--
ALTER TABLE `staff_lecture`
  ADD PRIMARY KEY (`STAFF_LECTURE_ID`);

--
-- Indexes for table `staff_proceeding`
--
ALTER TABLE `staff_proceeding`
  ADD PRIMARY KEY (`STAFF_PROCEEDING_ID`);

--
-- Indexes for table `staff_training`
--
ALTER TABLE `staff_training`
  ADD PRIMARY KEY (`STAFF_TRAINING_ID`);

--
-- Indexes for table `staff_work`
--
ALTER TABLE `staff_work`
  ADD PRIMARY KEY (`STAFF_WORK_ID`);

--
-- Indexes for table `st_academic_positions`
--
ALTER TABLE `st_academic_positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `st_budget_types`
--
ALTER TABLE `st_budget_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `st_departments`
--
ALTER TABLE `st_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `st_employee_types`
--
ALTER TABLE `st_employee_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `st_images`
--
ALTER TABLE `st_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `st_management_positions`
--
ALTER TABLE `st_management_positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `st_prefixes`
--
ALTER TABLE `st_prefixes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `st_qualifications`
--
ALTER TABLE `st_qualifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `st_staff`
--
ALTER TABLE `st_staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_st_type` (`st_type_id`),
  ADD KEY `fk_qualification_id` (`qualification_id`),
  ADD KEY `fk_prefix_id` (`prefix_id`),
  ADD KEY `fk_employee_type_id` (`employee_type_id`),
  ADD KEY `fk_management_position_id` (`management_position_id`),
  ADD KEY `fk_academic_position_id` (`academic_position_id`),
  ADD KEY `fk_budget_type_id` (`budget_type_id`),
  ADD KEY `fk_department_id` (`department_id`);

--
-- Indexes for table `st_support_positions`
--
ALTER TABLE `st_support_positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `st_type`
--
ALTER TABLE `st_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `st_units`
--
ALTER TABLE `st_units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `edms_certificate_requests`
--
ALTER TABLE `edms_certificate_requests`
  MODIFY `request_id` int NOT NULL AUTO_INCREMENT COMMENT 'รหัสคำขอ', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `edms_circular_documents`
--
ALTER TABLE `edms_circular_documents`
  MODIFY `document_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edms_command_documents`
--
ALTER TABLE `edms_command_documents`
  MODIFY `document_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edms_external_in_documents`
--
ALTER TABLE `edms_external_in_documents`
  MODIFY `document_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edms_external_out_documents`
--
ALTER TABLE `edms_external_out_documents`
  MODIFY `document_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edms_id_card_requests`
--
ALTER TABLE `edms_id_card_requests`
  MODIFY `request_id` int NOT NULL AUTO_INCREMENT COMMENT 'รหัสคำขอ';

--
-- AUTO_INCREMENT for table `edms_internal_in_documents`
--
ALTER TABLE `edms_internal_in_documents`
  MODIFY `document_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `edms_internal_out_documents`
--
ALTER TABLE `edms_internal_out_documents`
  MODIFY `document_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `edms_job_assignment_documents`
--
ALTER TABLE `edms_job_assignment_documents`
  MODIFY `assignment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `edms_users`
--
ALTER TABLE `edms_users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `edms_work_categories`
--
ALTER TABLE `edms_work_categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff_journal`
--
ALTER TABLE `staff_journal`
  MODIFY `STAFF_JOURNAL_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_lecture`
--
ALTER TABLE `staff_lecture`
  MODIFY `STAFF_LECTURE_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_proceeding`
--
ALTER TABLE `staff_proceeding`
  MODIFY `STAFF_PROCEEDING_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_training`
--
ALTER TABLE `staff_training`
  MODIFY `STAFF_TRAINING_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_work`
--
ALTER TABLE `staff_work`
  MODIFY `STAFF_WORK_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `st_academic_positions`
--
ALTER TABLE `st_academic_positions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `st_budget_types`
--
ALTER TABLE `st_budget_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `st_departments`
--
ALTER TABLE `st_departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `st_employee_types`
--
ALTER TABLE `st_employee_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `st_images`
--
ALTER TABLE `st_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `st_management_positions`
--
ALTER TABLE `st_management_positions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `st_prefixes`
--
ALTER TABLE `st_prefixes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `st_qualifications`
--
ALTER TABLE `st_qualifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `st_staff`
--
ALTER TABLE `st_staff`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `st_support_positions`
--
ALTER TABLE `st_support_positions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `st_type`
--
ALTER TABLE `st_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `st_units`
--
ALTER TABLE `st_units`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `edms_certificate_requests`
--
ALTER TABLE `edms_certificate_requests`
  ADD CONSTRAINT `edms_certificate_requests_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`);

--
-- Constraints for table `edms_circular_documents`
--
ALTER TABLE `edms_circular_documents`
  ADD CONSTRAINT `edms_circular_documents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `edms_work_categories` (`category_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `edms_circular_documents_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `edms_command_documents`
--
ALTER TABLE `edms_command_documents`
  ADD CONSTRAINT `edms_command_documents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `edms_work_categories` (`category_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `edms_command_documents_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`) ON DELETE CASCADE;

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
-- Constraints for table `edms_id_card_requests`
--
ALTER TABLE `edms_id_card_requests`
  ADD CONSTRAINT `edms_id_card_requests_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`);

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
  ADD CONSTRAINT `edms_internal_out_documents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `edms_work_categories` (`category_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `edms_internal_out_documents_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `edms_job_assignment_documents`
--
ALTER TABLE `edms_job_assignment_documents`
  ADD CONSTRAINT `edms_job_assignment_documents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `edms_work_categories` (`category_id`),
  ADD CONSTRAINT `edms_job_assignment_documents_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `edms_users` (`user_id`);

--
-- Constraints for table `st_images`
--
ALTER TABLE `st_images`
  ADD CONSTRAINT `st_images_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `st_staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `st_staff`
--
ALTER TABLE `st_staff`
  ADD CONSTRAINT `fk_academic_position_id` FOREIGN KEY (`academic_position_id`) REFERENCES `st_academic_positions` (`id`),
  ADD CONSTRAINT `fk_budget_type_id` FOREIGN KEY (`budget_type_id`) REFERENCES `st_budget_types` (`id`),
  ADD CONSTRAINT `fk_department_id` FOREIGN KEY (`department_id`) REFERENCES `st_departments` (`id`),
  ADD CONSTRAINT `fk_employee_type_id` FOREIGN KEY (`employee_type_id`) REFERENCES `st_employee_types` (`id`),
  ADD CONSTRAINT `fk_management_position_id` FOREIGN KEY (`management_position_id`) REFERENCES `st_management_positions` (`id`),
  ADD CONSTRAINT `fk_prefix_id` FOREIGN KEY (`prefix_id`) REFERENCES `st_prefixes` (`id`),
  ADD CONSTRAINT `fk_qualification_id` FOREIGN KEY (`qualification_id`) REFERENCES `st_qualifications` (`id`),
  ADD CONSTRAINT `fk_st_type` FOREIGN KEY (`st_type_id`) REFERENCES `st_type` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
