-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 05, 2024 at 06:34 PM
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
-- Database: `printing_exam`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`user_id`) VALUES
(1),
(10);

-- --------------------------------------------------------

--
-- Table structure for table `exam`
--

CREATE TABLE `exam` (
  `exam_id` int(11) NOT NULL,
  `sub_id` int(11) DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `exam_start` time DEFAULT NULL,
  `exam_end` time DEFAULT NULL,
  `exam_status` varchar(50) DEFAULT NULL,
  `exam_room` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam`
--

INSERT INTO `exam` (`exam_id`, `sub_id`, `exam_date`, `exam_start`, `exam_end`, `exam_status`, `exam_room`) VALUES
(1, 1, '2024-12-01', '09:00:00', '12:00:00', 'Scheduled', 'Room 101'),
(2, 2, '2024-12-02', '13:00:00', '16:00:00', 'Scheduled', 'Room 102'),
(3, 3, '2024-12-03', '09:00:00', '12:00:00', 'Scheduled', 'Room 103'),
(4, 4, '2024-12-04', '13:00:00', '16:00:00', 'Scheduled', 'Room 104'),
(5, 5, '2024-12-05', '09:00:00', '12:00:00', 'Scheduled', 'Room 105'),
(6, 6, '2024-12-06', '13:00:00', '16:00:00', 'Scheduled', 'Room 106'),
(7, 7, '2024-12-07', '09:00:00', '12:00:00', 'Scheduled', 'Room 107'),
(8, 8, '2024-12-08', '13:00:00', '16:00:00', 'Scheduled', 'Room 108'),
(9, 9, '2024-12-09', '09:00:00', '12:00:00', 'Scheduled', 'Room 109'),
(10, 10, '2024-12-10', '13:00:00', '16:00:00', 'Scheduled', 'Room 110');

-- --------------------------------------------------------

--
-- Table structure for table `examtech`
--

CREATE TABLE `examtech` (
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `examtech`
--

INSERT INTO `examtech` (`user_id`) VALUES
(3),
(7);

-- --------------------------------------------------------

--
-- Table structure for table `manage`
--

CREATE TABLE `manage` (
  `user_id` int(11) NOT NULL,
  `sub_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manage`
--

INSERT INTO `manage` (`user_id`, `sub_id`) VALUES
(1, 1),
(2, 2),
(2, 3),
(2, 8),
(5, 4),
(5, 5),
(5, 9),
(6, 6),
(6, 10),
(9, 7);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `sub_id` int(11) NOT NULL,
  `teach_id` int(11) DEFAULT NULL,
  `sub_semester` int(11) DEFAULT NULL,
  `sub_nameEN` varchar(100) DEFAULT NULL,
  `sub_nameTH` varchar(100) DEFAULT NULL,
  `sub_section` varchar(50) DEFAULT NULL,
  `sub_department` varchar(100) DEFAULT NULL,
  `sub_detail` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`sub_id`, `teach_id`, `sub_semester`, `sub_nameEN`, `sub_nameTH`, `sub_section`, `sub_department`, `sub_detail`) VALUES
(1, 2, 1, 'Mathematics', 'คณิตศาสตร์', 'A', 'Science', 'Basic Mathematics course.'),
(2, 5, 2, 'Physics', 'ฟิสิกส์', 'B', 'Science', 'Physics course with lab sessions.'),
(3, 6, 1, 'Chemistry', 'เคมี', 'A', 'Science', 'Introductory Chemistry.'),
(4, 9, 2, 'Biology', 'ชีววิทยา', 'B', 'Science', 'Biology course with lab sessions.'),
(5, 2, 1, 'English', 'ภาษาอังกฤษ', 'A', 'Arts', 'Basic English course.'),
(6, 5, 1, 'History', 'ประวัติศาสตร์', 'B', 'Arts', 'Introduction to World History.'),
(7, 6, 2, 'Geography', 'ภูมิศาสตร์', 'A', 'Arts', 'Physical and Human Geography.'),
(8, 9, 1, 'Computer Science', 'วิทยาการคอมพิวเตอร์', 'A', 'Technology', 'Introduction to Computer Science.'),
(9, 2, 2, 'Economics', 'เศรษฐศาสตร์', 'B', 'Commerce', 'Principles of Economics.'),
(10, 5, 1, 'Political Science', 'รัฐศาสตร์', 'A', 'Social Science', 'Introduction to Political Science.');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`user_id`) VALUES
(2),
(5),
(6),
(9);

-- --------------------------------------------------------

--
-- Table structure for table `technology`
--

CREATE TABLE `technology` (
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `technology`
--

INSERT INTO `technology` (`user_id`) VALUES
(4),
(8);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_firstname` varchar(50) DEFAULT NULL,
  `user_lastname` varchar(50) DEFAULT NULL,
  `user_role` varchar(50) DEFAULT NULL,
  `user_tel` varchar(20) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_firstname`, `user_lastname`, `user_role`, `user_tel`, `user_email`, `user_password`) VALUES
(1, 'John', 'Doe', 'Admin', '1234567890', 'john.doe@example.com', 'a8abda66046ac419baf24a48781a1f21'),
(2, 'Jane', 'Smith', 'Teacher', '0987654321', 'jane.smith@example.com', '9c195e0b911a5a0b3acf7b3641e2a3f2'),
(3, 'Tom', 'Brown', 'ExamTech', '1112223333', 'tom.brown@example.com', '110f4de0bde8ee28525f378dd515074f'),
(4, 'Lucy', 'Johnson', 'Technology', '4445556666', 'lucy.johnson@example.com', '2403c2f364a37ee03ea7cd03a2661c0a'),
(5, 'Emma', 'Davis', 'Teacher', '7778889999', 'emma.davis@example.com', '4073f806d35fe93a1c93d64e878bcd53'),
(6, 'Mike', 'Wilson', 'Teacher', '1114447777', 'mike.wilson@example.com', 'c68978af81d46d878774f6dd9cc4e901'),
(7, 'Laura', 'Moore', 'ExamTech', '2225558888', 'laura.moore@example.com', 'aceaff1bcd8e0060d8845a44d17bb148'),
(8, 'Anna', 'Taylor', 'Technology', '3336669999', 'anna.taylor@example.com', '742d66f6f05955ec444719dc1a1ac076'),
(9, 'David', 'White', 'Teacher', '4447770000', 'david.white@example.com', 'ed3e110d7b6b67e00f499b9024a227a7'),
(10, 'Chris', 'Martin', 'Admin', '5558881111', 'chris.martin@example.com', '4c1b9166b3c2398bb8e59250c93ee045');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `exam`
--
ALTER TABLE `exam`
  ADD PRIMARY KEY (`exam_id`),
  ADD KEY `sub_id` (`sub_id`);

--
-- Indexes for table `examtech`
--
ALTER TABLE `examtech`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `manage`
--
ALTER TABLE `manage`
  ADD PRIMARY KEY (`user_id`,`sub_id`),
  ADD KEY `sub_id` (`sub_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`sub_id`),
  ADD KEY `teach_id` (`teach_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `technology`
--
ALTER TABLE `technology`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exam`
--
ALTER TABLE `exam`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `sub_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `exam`
--
ALTER TABLE `exam`
  ADD CONSTRAINT `exam_ibfk_1` FOREIGN KEY (`sub_id`) REFERENCES `subject` (`sub_id`);

--
-- Constraints for table `examtech`
--
ALTER TABLE `examtech`
  ADD CONSTRAINT `examtech_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `manage`
--
ALTER TABLE `manage`
  ADD CONSTRAINT `manage_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `manage_ibfk_2` FOREIGN KEY (`sub_id`) REFERENCES `subject` (`sub_id`);

--
-- Constraints for table `subject`
--
ALTER TABLE `subject`
  ADD CONSTRAINT `subject_ibfk_1` FOREIGN KEY (`teach_id`) REFERENCES `teacher` (`user_id`);

--
-- Constraints for table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `technology`
--
ALTER TABLE `technology`
  ADD CONSTRAINT `technology_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
