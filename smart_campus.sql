-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2026 at 11:15 AM
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
-- Database: `smart_campus`
--

-- --------------------------------------------------------

--
-- Table structure for table `faculty_classes`
--

CREATE TABLE `faculty_classes` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `section` varchar(100) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `schedule` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `day_of_week` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_classes`
--

INSERT INTO `faculty_classes` (`id`, `faculty_id`, `section`, `class_name`, `subject`, `schedule`, `start_time`, `end_time`, `room`, `created_at`, `day_of_week`) VALUES
(5, 1, 'BSIT 3A', '', 'Web Systems and Technologies', '', '08:00:00', '10:00:00', 'Comlab 1', '2026-10-13 09:14:53', 1),
(6, 1, 'BSIT 3B', '', 'Web Systems and Technologies', '', '08:00:00', '10:00:00', 'Comlab 1', '2026-10-13 09:14:53', 3),
(7, 1, 'BSIT 4A', '', 'Capstone Project 1', '', '13:00:00', '16:00:00', 'Comlab 2', '2026-10-13 09:14:53', 5),
(8, 2, 'BSIT 2A', '', 'Data Structures and Algorithms', '', '10:00:00', '12:00:00', 'ICT Lab 3', '2026-10-13 09:14:53', 1),
(9, 2, 'BSIT 2B', '', 'Data Structures and Algorithms', '', '13:00:00', '15:00:00', 'ICT Lab 3', '2026-10-13 09:14:53', 2),
(10, 2, 'BSIT 3A', '', 'Object-Oriented Programming', '', '08:00:00', '10:00:00', 'ICT Lab 1', '2026-10-13 09:14:53', 4),
(11, 3, 'BSCS 2A', '', 'Discrete Mathematics', '', '08:00:00', '10:00:00', 'Room 305', '2026-10-13 09:14:53', 2),
(12, 3, 'BSCS 3A', '', 'Theory of Computation', '', '10:00:00', '12:00:00', 'Room 305', '2026-10-13 09:14:53', 4),
(13, 3, 'BSCS 4A', '', 'Machine Learning', '', '09:00:00', '12:00:00', 'Lab A', '2026-10-13 09:14:53', 5),
(14, 4, 'BSCS 4A', '', 'Advanced Algorithms', '', '14:00:00', '16:00:00', 'Room 401', '2026-10-13 09:14:53', 1),
(15, 4, 'BSCS 4B', '', 'Advanced Algorithms', '', '14:00:00', '16:00:00', 'Room 401', '2026-10-13 09:14:53', 3),
(16, 4, 'MSIT 1A', '', 'Research Methods', '', '15:00:00', '18:00:00', 'Grad Room', '2026-10-13 09:14:53', 5),
(17, 5, 'BSBA 1A', '', 'Principles of Management', '', '09:00:00', '11:00:00', 'Room 105', '2026-10-13 09:14:53', 1),
(18, 5, 'BSBA 1B', '', 'Principles of Management', '', '09:00:00', '11:00:00', 'Room 105', '2026-10-13 09:14:53', 3),
(19, 5, 'BSBA 2A', '', 'Marketing Management', '', '13:00:00', '15:00:00', 'Room 106', '2026-10-13 09:14:53', 4),
(20, 6, 'BSBA 3A', '', 'Financial Management', '', '10:00:00', '12:00:00', 'Room 201', '2026-10-13 09:14:53', 2),
(21, 6, 'BSBA 3B', '', 'Financial Management', '', '10:00:00', '12:00:00', 'Room 201', '2026-10-13 09:14:53', 4),
(22, 6, 'BSBA 4A', '', 'Strategic Management', '', '08:00:00', '10:00:00', 'Room 202', '2026-10-13 09:14:53', 5),
(23, 7, 'BSEE 2A', '', 'Circuit Analysis', '', '08:00:00', '11:00:00', 'EE Lab 1', '2026-10-13 09:14:53', 1),
(24, 7, 'BSEE 3A', '', 'Signals and Systems', '', '08:00:00', '11:00:00', 'EE Lab 1', '2026-10-13 09:14:53', 3),
(25, 8, 'BSCE 3A', '', 'Structural Analysis', '', '13:00:00', '16:00:00', 'CE Lab 2', '2026-10-13 09:14:53', 2),
(26, 8, 'BSCE 4A', '', 'Structural Design', '', '13:00:00', '16:00:00', 'CE Lab 2', '2026-10-13 09:14:53', 4);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_staff`
--

CREATE TABLE `faculty_staff` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `role_title` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_staff`
--

INSERT INTO `faculty_staff` (`id`, `full_name`, `department`, `role_title`) VALUES
(1, 'Maria Santos', 'Information Technology', 'Assistant Professor'),
(2, 'Juan Dela Cruz', 'Information Technology', 'Instructor'),
(3, 'Ana Reyes', 'Computer Science', 'Associate Professor'),
(4, 'Roberto Mercado', 'Computer Science', 'Professor'),
(5, 'Liza Fernandez', 'Business Administration', 'Instructor'),
(6, 'Carlos Villanueva', 'Business Administration', 'Assistant Professor'),
(7, 'Patricia Aquino', 'Engineering', 'Instructor'),
(8, 'Miguel Torres', 'Engineering', 'Associate Professor'),
(28, 'TestTeacher', 'Unassigned', 'Faculty');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_status`
--

CREATE TABLE `faculty_status` (
  `faculty_id` int(11) NOT NULL,
  `status` enum('available','unavailable','in_class','busy','in_meeting','consultation_hours','out_of_campus') NOT NULL DEFAULT 'out_of_campus',
  `location` varchar(100) DEFAULT NULL,
  `note` varchar(150) DEFAULT NULL,
  `auto_update` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_status`
--

INSERT INTO `faculty_status` (`faculty_id`, `status`, `location`, `note`, `auto_update`, `updated_at`) VALUES
(1, 'consultation_hours', 'Faculty Room 204', 'Open for thesis consultations until 4 PM. Drop by anytime.', 1, '2026-10-13 08:59:53'),
(2, 'in_class', 'ICT Laboratory 3', NULL, 1, '2026-10-13 08:34:53'),
(3, 'available', 'Office 118', 'Free until 2 PM — walk-ins welcome.', 1, '2026-10-13 09:09:53'),
(4, 'in_meeting', 'Conference Room A', 'Department meeting. Back by 3 PM.', 0, '2026-10-13 08:49:53'),
(5, 'busy', 'Office 210', 'Grading finals — please email instead.', 0, '2026-10-13 08:14:53'),
(6, 'available', 'Business Dept Office', NULL, 1, '2026-10-13 09:04:53'),
(7, 'out_of_campus', 'Off-site', 'Attending a seminar. Back tomorrow.', 0, '2026-10-13 06:14:53'),
(8, 'in_class', 'Engineering Lab 2', NULL, 1, '2026-10-13 08:54:53');

-- --------------------------------------------------------

--
-- Table structure for table `status_logs`
--

CREATE TABLE `status_logs` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `logged_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status_logs`
--

INSERT INTO `status_logs` (`id`, `faculty_id`, `status`, `location`, `logged_at`) VALUES
(5, 1, 'available', 'Faculty Room 204', '2026-09-13 05:14:53'),
(6, 1, 'in_class', 'Comlab 1', '2026-09-13 07:14:53'),
(7, 1, 'consultation_hours', 'Faculty Room 204', '2026-09-13 10:14:53'),
(8, 1, 'available', 'Faculty Room 204', '2026-09-14 05:14:53'),
(9, 1, 'in_class', 'Comlab 2', '2026-09-14 07:14:53'),
(10, 1, 'available', 'Faculty Room 204', '2026-09-14 11:14:53'),
(11, 1, 'in_meeting', 'Conference A', '2026-09-15 06:14:53'),
(12, 1, 'consultation_hours', 'Faculty Room 204', '2026-09-15 10:14:53'),
(13, 1, 'available', 'Faculty Room 204', '2026-09-16 05:14:53'),
(14, 1, 'in_class', 'Comlab 1', '2026-09-16 07:14:53'),
(15, 1, 'consultation_hours', 'Faculty Room 204', '2026-09-16 11:14:53'),
(16, 1, 'available', 'Faculty Room 204', '2026-09-18 06:14:53'),
(17, 1, 'in_class', 'Comlab 2', '2026-09-18 08:14:53'),
(18, 1, 'available', 'Faculty Room 204', '2026-09-19 05:14:53'),
(19, 1, 'consultation_hours', 'Faculty Room 204', '2026-09-19 11:14:53'),
(20, 1, 'in_meeting', 'Conference A', '2026-09-21 06:14:53'),
(21, 1, 'available', 'Faculty Room 204', '2026-09-21 08:14:53'),
(22, 1, 'in_class', 'Comlab 1', '2026-09-22 05:14:53'),
(23, 1, 'consultation_hours', 'Faculty Room 204', '2026-09-22 11:14:53'),
(24, 1, 'available', 'Faculty Room 204', '2026-09-25 05:14:53'),
(25, 1, 'in_class', 'Comlab 2', '2026-09-25 07:14:53'),
(26, 1, 'busy', 'Faculty Room 204', '2026-09-25 10:14:53'),
(27, 1, 'available', 'Faculty Room 204', '2026-09-28 05:14:53'),
(28, 1, 'in_class', 'Comlab 1', '2026-09-28 07:14:53'),
(29, 1, 'consultation_hours', 'Faculty Room 204', '2026-09-28 11:14:53'),
(30, 1, 'available', 'Faculty Room 204', '2026-10-01 05:14:53'),
(31, 1, 'in_meeting', 'Conference A', '2026-10-01 07:14:53'),
(32, 1, 'consultation_hours', 'Faculty Room 204', '2026-10-01 10:14:53'),
(33, 1, 'available', 'Faculty Room 204', '2026-10-05 05:14:53'),
(34, 1, 'in_class', 'Comlab 2', '2026-10-05 07:14:53'),
(35, 1, 'consultation_hours', 'Faculty Room 204', '2026-10-05 11:14:53'),
(36, 1, 'available', 'Faculty Room 204', '2026-10-08 05:14:53'),
(37, 1, 'in_class', 'Comlab 1', '2026-10-08 07:14:53'),
(38, 1, 'busy', 'Faculty Room 204', '2026-10-08 10:14:53'),
(39, 1, 'available', 'Faculty Room 204', '2026-10-10 05:14:53'),
(40, 1, 'consultation_hours', 'Faculty Room 204', '2026-10-10 08:14:53'),
(41, 1, 'in_class', 'Comlab 2', '2026-10-10 12:14:53'),
(42, 1, 'available', 'Faculty Room 204', '2026-10-12 05:14:53'),
(43, 1, 'in_meeting', 'Conference A', '2026-10-12 07:14:53'),
(44, 1, 'consultation_hours', 'Faculty Room 204', '2026-10-12 11:14:53'),
(45, 1, 'consultation_hours', 'Faculty Room 204', '2026-10-13 08:59:53'),
(46, 2, 'available', 'ICT Lab 3', '2026-09-13 05:14:53'),
(47, 2, 'in_class', 'ICT Lab 3', '2026-09-13 07:14:53'),
(48, 2, 'available', 'ICT Lab 3', '2026-09-13 11:14:53'),
(49, 2, 'available', 'ICT Lab 3', '2026-09-15 05:14:53'),
(50, 2, 'in_class', 'ICT Lab 1', '2026-09-15 07:14:53'),
(51, 2, 'busy', 'ICT Lab 3', '2026-09-15 11:14:53'),
(52, 2, 'available', 'ICT Lab 3', '2026-09-17 05:14:53'),
(53, 2, 'in_class', 'ICT Lab 3', '2026-09-17 08:14:53'),
(54, 2, 'available', 'ICT Lab 3', '2026-09-17 12:14:53'),
(55, 2, 'in_meeting', 'Conference B', '2026-09-19 06:14:53'),
(56, 2, 'available', 'ICT Lab 3', '2026-09-19 08:14:53'),
(57, 2, 'in_class', 'ICT Lab 1', '2026-09-19 12:14:53'),
(58, 2, 'available', 'ICT Lab 3', '2026-09-23 05:14:53'),
(59, 2, 'in_class', 'ICT Lab 3', '2026-09-23 07:14:53'),
(60, 2, 'available', 'ICT Lab 3', '2026-09-23 11:14:53'),
(61, 2, 'in_class', 'ICT Lab 1', '2026-09-26 06:14:53'),
(62, 2, 'busy', 'ICT Lab 3', '2026-09-26 08:14:53'),
(63, 2, 'available', 'ICT Lab 3', '2026-09-26 12:14:53'),
(64, 2, 'available', 'ICT Lab 3', '2026-09-29 05:14:53'),
(65, 2, 'in_class', 'ICT Lab 3', '2026-09-29 07:14:53'),
(66, 2, 'available', 'ICT Lab 3', '2026-09-29 11:14:53'),
(67, 2, 'in_meeting', 'Conference B', '2026-10-03 06:14:53'),
(68, 2, 'in_class', 'ICT Lab 3', '2026-10-03 08:14:53'),
(69, 2, 'available', 'ICT Lab 3', '2026-10-03 12:14:53'),
(70, 2, 'available', 'ICT Lab 3', '2026-10-07 05:14:53'),
(71, 2, 'in_class', 'ICT Lab 1', '2026-10-07 07:14:53'),
(72, 2, 'available', 'ICT Lab 3', '2026-10-07 11:14:53'),
(73, 2, 'in_class', 'ICT Lab 3', '2026-10-11 06:14:53'),
(74, 2, 'available', 'ICT Lab 3', '2026-10-11 08:14:53'),
(75, 2, 'in_class', 'ICT Lab 3', '2026-10-13 08:34:53'),
(76, 3, 'available', 'Office 118', '2026-09-13 05:14:53'),
(77, 3, 'in_class', 'Room 305', '2026-09-13 07:14:53'),
(78, 3, 'consultation_hours', 'Office 118', '2026-09-13 10:14:53'),
(79, 3, 'available', 'Office 118', '2026-09-16 05:14:53'),
(80, 3, 'in_class', 'Lab A', '2026-09-16 08:14:53'),
(81, 3, 'available', 'Office 118', '2026-09-16 12:14:53'),
(82, 3, 'consultation_hours', 'Office 118', '2026-09-19 06:14:53'),
(83, 3, 'in_meeting', 'Conference A', '2026-09-19 08:14:53'),
(84, 3, 'available', 'Office 118', '2026-09-19 11:14:53'),
(85, 3, 'available', 'Office 118', '2026-09-22 05:14:53'),
(86, 3, 'in_class', 'Room 305', '2026-09-22 07:14:53'),
(87, 3, 'consultation_hours', 'Office 118', '2026-09-22 11:14:53'),
(88, 3, 'in_class', 'Lab A', '2026-09-26 06:14:53'),
(89, 3, 'available', 'Office 118', '2026-09-26 10:14:53'),
(90, 3, 'consultation_hours', 'Office 118', '2026-09-29 05:14:53'),
(91, 3, 'in_meeting', 'Conference A', '2026-09-29 07:14:53'),
(92, 3, 'available', 'Office 118', '2026-09-29 11:14:53'),
(93, 3, 'available', 'Office 118', '2026-10-03 05:14:53'),
(94, 3, 'in_class', 'Room 305', '2026-10-03 07:14:53'),
(95, 3, 'consultation_hours', 'Office 118', '2026-10-03 10:14:53'),
(96, 3, 'available', 'Office 118', '2026-10-06 05:14:53'),
(97, 3, 'consultation_hours', 'Office 118', '2026-10-06 08:14:53'),
(98, 3, 'in_class', 'Lab A', '2026-10-06 12:14:53'),
(99, 3, 'in_meeting', 'Conference A', '2026-10-09 05:14:53'),
(100, 3, 'available', 'Office 118', '2026-10-09 07:14:53'),
(101, 3, 'consultation_hours', 'Office 118', '2026-10-09 11:14:53'),
(102, 3, 'available', 'Office 118', '2026-10-13 09:09:53'),
(103, 4, 'available', 'Room 401', '2026-09-13 05:14:53'),
(104, 4, 'in_class', 'Room 401', '2026-09-13 07:14:53'),
(105, 4, 'in_meeting', 'Conference A', '2026-09-13 11:14:53'),
(106, 4, 'available', 'Room 401', '2026-09-15 05:14:53'),
(107, 4, 'in_class', 'Grad Room', '2026-09-15 08:14:53'),
(108, 4, 'available', 'Room 401', '2026-09-15 12:14:53'),
(109, 4, 'in_class', 'Room 401', '2026-09-19 07:14:53'),
(110, 4, 'in_meeting', 'Conference A', '2026-09-19 10:14:53'),
(111, 4, 'available', 'Room 401', '2026-09-19 13:14:53'),
(112, 4, 'available', 'Room 401', '2026-09-23 05:14:53'),
(113, 4, 'in_class', 'Room 401', '2026-09-23 07:14:53'),
(114, 4, 'busy', 'Room 401', '2026-09-23 11:14:53'),
(115, 4, 'in_class', 'Grad Room', '2026-09-27 06:14:53'),
(116, 4, 'available', 'Room 401', '2026-09-27 10:14:53'),
(117, 4, 'in_meeting', 'Conference A', '2026-10-01 05:14:53'),
(118, 4, 'available', 'Room 401', '2026-10-01 07:14:53'),
(119, 4, 'in_class', 'Room 401', '2026-10-01 11:14:53'),
(120, 4, 'available', 'Room 401', '2026-10-05 05:14:53'),
(121, 4, 'in_class', 'Room 401', '2026-10-05 07:14:53'),
(122, 4, 'in_meeting', 'Conference A', '2026-10-05 11:14:53'),
(123, 4, 'in_class', 'Grad Room', '2026-10-10 06:14:53'),
(124, 4, 'available', 'Room 401', '2026-10-10 10:14:53'),
(125, 4, 'in_meeting', 'Conference A', '2026-10-13 08:49:53'),
(126, 5, 'available', 'Room 105', '2026-09-14 05:14:54'),
(127, 5, 'in_class', 'Room 105', '2026-09-14 07:14:54'),
(128, 5, 'consultation_hours', 'Office 118', '2026-09-14 11:14:54'),
(129, 5, 'available', 'Room 105', '2026-09-17 06:14:54'),
(130, 5, 'in_class', 'Room 106', '2026-09-17 08:14:54'),
(131, 5, 'available', 'Room 105', '2026-09-17 12:14:54'),
(132, 5, 'consultation_hours', 'Office 118', '2026-09-21 06:14:54'),
(133, 5, 'in_class', 'Room 105', '2026-09-21 08:14:54'),
(134, 5, 'available', 'Room 105', '2026-09-21 11:14:54'),
(135, 5, 'available', 'Room 105', '2026-09-25 05:14:54'),
(136, 5, 'in_class', 'Room 106', '2026-09-25 07:14:54'),
(137, 5, 'consultation_hours', 'Office 118', '2026-09-25 10:14:54'),
(138, 5, 'in_class', 'Room 105', '2026-09-29 06:14:54'),
(139, 5, 'available', 'Room 105', '2026-09-29 10:14:54'),
(140, 5, 'busy', 'Room 105', '2026-10-03 05:14:54'),
(141, 5, 'available', 'Room 105', '2026-10-03 07:14:54'),
(142, 5, 'consultation_hours', 'Office 118', '2026-10-03 11:14:54'),
(143, 5, 'in_class', 'Room 106', '2026-10-07 07:14:54'),
(144, 5, 'available', 'Room 105', '2026-10-07 11:14:54'),
(145, 5, 'in_class', 'Room 105', '2026-10-11 06:14:54'),
(146, 5, 'available', 'Room 105', '2026-10-11 10:14:54'),
(147, 5, 'busy', 'Room 105', '2026-10-13 08:14:54'),
(148, 6, 'available', 'Room 201', '2026-09-15 05:14:54'),
(149, 6, 'in_class', 'Room 201', '2026-09-15 07:14:54'),
(150, 6, 'in_meeting', 'Conference B', '2026-09-15 11:14:54'),
(151, 6, 'available', 'Room 201', '2026-09-19 06:14:54'),
(152, 6, 'in_class', 'Room 202', '2026-09-19 08:14:54'),
(153, 6, 'available', 'Room 201', '2026-09-19 12:14:54'),
(154, 6, 'in_class', 'Room 201', '2026-09-23 07:14:54'),
(155, 6, 'available', 'Room 201', '2026-09-23 11:14:54'),
(156, 6, 'available', 'Room 201', '2026-09-27 05:14:54'),
(157, 6, 'in_class', 'Room 201', '2026-09-27 07:14:54'),
(158, 6, 'busy', 'Room 201', '2026-09-27 10:14:54'),
(159, 6, 'in_class', 'Room 202', '2026-10-01 06:14:54'),
(160, 6, 'available', 'Room 201', '2026-10-01 10:14:54'),
(161, 6, 'in_meeting', 'Conference B', '2026-10-05 06:14:54'),
(162, 6, 'available', 'Room 201', '2026-10-05 10:14:54'),
(163, 6, 'in_class', 'Room 201', '2026-10-09 07:14:54'),
(164, 6, 'available', 'Room 201', '2026-10-09 11:14:54'),
(165, 6, 'available', 'Room 201', '2026-10-13 09:04:54'),
(166, 7, 'out_of_campus', 'Off-site', '2026-09-13 03:14:54'),
(167, 7, 'available', 'EE Lab 1', '2026-09-13 05:14:54'),
(168, 7, 'in_class', 'EE Lab 1', '2026-09-13 07:14:54'),
(169, 7, 'available', 'EE Lab 1', '2026-09-13 11:14:54'),
(170, 7, 'in_class', 'EE Lab 1', '2026-09-17 06:14:54'),
(171, 7, 'available', 'EE Lab 1', '2026-09-17 08:14:54'),
(172, 7, 'out_of_campus', 'Seminar', '2026-09-21 04:14:54'),
(173, 7, 'in_class', 'EE Lab 1', '2026-09-25 06:14:54'),
(174, 7, 'available', 'EE Lab 1', '2026-09-25 08:14:54'),
(175, 7, 'in_class', 'EE Lab 1', '2026-10-01 06:14:54'),
(176, 7, 'busy', 'EE Lab 1', '2026-10-01 08:14:54'),
(177, 7, 'available', 'EE Lab 1', '2026-10-01 11:14:54'),
(178, 7, 'in_class', 'EE Lab 1', '2026-10-07 06:14:54'),
(179, 7, 'available', 'EE Lab 1', '2026-10-07 08:14:54'),
(180, 7, 'out_of_campus', 'Off-site', '2026-10-13 06:14:54'),
(181, 8, 'available', 'CE Lab 2', '2026-09-16 05:14:54'),
(182, 8, 'in_class', 'CE Lab 2', '2026-09-16 07:14:54'),
(183, 8, 'in_meeting', 'Conference A', '2026-09-16 11:14:54'),
(184, 8, 'in_class', 'CE Lab 2', '2026-09-21 06:14:54'),
(185, 8, 'available', 'CE Lab 2', '2026-09-21 08:14:54'),
(186, 8, 'available', 'CE Lab 2', '2026-09-25 05:14:54'),
(187, 8, 'in_class', 'CE Lab 2', '2026-09-25 07:14:54'),
(188, 8, 'busy', 'CE Lab 2', '2026-09-25 11:14:54'),
(189, 8, 'in_class', 'CE Lab 2', '2026-09-30 06:14:54'),
(190, 8, 'available', 'CE Lab 2', '2026-09-30 10:14:54'),
(191, 8, 'in_meeting', 'Conference A', '2026-10-04 05:14:54'),
(192, 8, 'in_class', 'CE Lab 2', '2026-10-04 07:14:54'),
(193, 8, 'available', 'CE Lab 2', '2026-10-04 11:14:54'),
(194, 8, 'in_class', 'CE Lab 2', '2026-10-09 06:14:54'),
(195, 8, 'available', 'CE Lab 2', '2026-10-09 10:14:54'),
(196, 8, 'in_class', 'CE Lab 2', '2026-10-13 08:54:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `faculty_staff_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `faculty_staff_id`, `created_at`) VALUES
(1, 'maria.santos@ccc.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2026-10-13 09:14:53'),
(2, 'juan.delacruz@ccc.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, '2026-10-13 09:14:53'),
(3, 'ana.reyes@ccc.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, '2026-10-13 09:14:53'),
(4, 'roberto.mercado@ccc.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, '2026-10-13 09:14:53'),
(5, 'liza.fernandez@ccc.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, '2026-10-13 09:14:53'),
(6, 'carlos.villanueva@ccc.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, '2026-10-13 09:14:53'),
(7, 'patricia.aquino@ccc.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 7, '2026-10-13 09:14:53'),
(8, 'miguel.torres@ccc.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 8, '2026-10-13 09:14:53'),
(11, 'TestTeacher@gmail.com', '$2y$10$pn26coenTaqZFWYlw.eOAOfuIWhtEpOzNuFIIpQHJNg2CiQFkwS6.', 28, '2026-10-12 15:49:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `faculty_classes`
--
ALTER TABLE `faculty_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `faculty_staff`
--
ALTER TABLE `faculty_staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_status`
--
ALTER TABLE `faculty_status`
  ADD PRIMARY KEY (`faculty_id`),
  ADD UNIQUE KEY `uniq_faculty` (`faculty_id`);

--
-- Indexes for table `status_logs`
--
ALTER TABLE `status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_faculty_logged` (`faculty_id`,`logged_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `faculty_staff_id` (`faculty_staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `faculty_classes`
--
ALTER TABLE `faculty_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000;

--
-- AUTO_INCREMENT for table `faculty_staff`
--
ALTER TABLE `faculty_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `status_logs`
--
ALTER TABLE `status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `faculty_classes`
--
ALTER TABLE `faculty_classes`
  ADD CONSTRAINT `faculty_classes_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty_staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faculty_status`
--
ALTER TABLE `faculty_status`
  ADD CONSTRAINT `faculty_status_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty_staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `status_logs`
--
ALTER TABLE `status_logs`
  ADD CONSTRAINT `status_logs_fk` FOREIGN KEY (`faculty_id`) REFERENCES `faculty_staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`faculty_staff_id`) REFERENCES `faculty_staff` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
