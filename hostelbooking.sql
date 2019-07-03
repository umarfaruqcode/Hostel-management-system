-- phpMyAdmin SQL Dump
-- version 4.7.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 25, 2018 at 02:25 PM
-- Server version: 10.1.29-MariaDB
-- PHP Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+01:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hostelbooking`
--
CREATE DATABASE IF NOT EXISTS `hostelbooking` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `hostelbooking`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminid` int(3) NOT NULL,
  `displayName` varchar(50) NOT NULL DEFAULT 'Administrator',
  `username` varchar(50) NOT NULL,
  `admin_email` varchar(80) NOT NULL,
  `password` varchar(128) NOT NULL,
  `current_session` varchar(9) NOT NULL DEFAULT '2016/2017',
  `current_semester` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminid`, `displayName`, `username`, `admin_email`, `password`, `current_session`, `current_semester`) VALUES
(1, 'Administrator', 'admin', 'admin@unilorin.edu.ng', '21232f297a57a5a743894a0e4a801fc3', '2017/2018', 2);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `bookid` int(11) NOT NULL,
  `book_hostel_id` int(6) NOT NULL,
  `book_stdtid` int(11) NOT NULL,
  `room_no` int(6) NOT NULL,
  `session` varchar(9) NOT NULL,
  `date_booked` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_approved` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`bookid`, `book_hostel_id`, `book_stdtid`, `room_no`, `session`, `date_booked`, `date_approved`) VALUES
(3, 6, 1, 6, '2017/2018', '2018-06-25 12:52:20', '2018-06-25 00:00:00'),
(5, 7, 3, 43, '2017/2018', '2018-06-25 13:21:31', '2018-06-25 14:24:49');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `deptid` int(4) NOT NULL,
  `dept_acron` varchar(10) DEFAULT NULL,
  `dept_name` varchar(100) NOT NULL,
  `facultyid` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`deptid`, `dept_acron`, `dept_name`, `facultyid`) VALUES
(1, 'CSC', 'Computer Science', 1),
(2, 'MAC', 'Mass Communication', 1),
(3, 'LIS', 'Library and Information Science', 1),
(4, 'ICS', 'Information and Communication Science', 1),
(5, 'TLS', 'Telecommunication Science', 1);

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `facid` int(3) NOT NULL,
  `fac_acron` varchar(10) NOT NULL,
  `fac_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`facid`, `fac_acron`, `fac_name`) VALUES
(1, 'CIS', 'Communication and Information Science');

-- --------------------------------------------------------

--
-- Table structure for table `hostels`
--

CREATE TABLE `hostels` (
  `hostel_id` int(11) NOT NULL,
  `hostel_name` varchar(100) NOT NULL,
  `no_of_rooms` int(11) NOT NULL DEFAULT '0',
  `max_stdt_per_room` int(2) NOT NULL DEFAULT '4'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `hostels`
--

INSERT INTO `hostels` (`hostel_id`, `hostel_name`, `no_of_rooms`, `max_stdt_per_room`) VALUES
(6, 'Saraki Hostel', 40, 4),
(7, 'Alimi Hostel', 50, 4),
(8, 'Alanamu VIP Hostel', 40, 2);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `stdtid` int(11) NOT NULL,
  `matric_no` varchar(20) NOT NULL,
  `level` enum('100','200','300','400','500') NOT NULL,
  `password` varchar(128) NOT NULL,
  `surname` varchar(20) NOT NULL,
  `other_names` varchar(40) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `email` varchar(80) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `stdt_deptid` int(4) NOT NULL,
  `reg_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`stdtid`, `matric_no`, `level`, `password`, `surname`, `other_names`, `gender`, `email`, `phone`, `stdt_deptid`, `reg_date`) VALUES
(1, 'UIL/02/16372', '300', 'e10adc3949ba59abbe56e057f20f883e', 'Adedokun', 'Simeon Femi', 'Male', 'femsimade@gmail.com', '08060530063', 1, '2017-06-27 22:55:35'),
(3, 'UIL/02/16378', '200', 'e10adc3949ba59abbe56e057f20f883e', 'Gbola', 'Boluwatife', 'Female', 'gbolajoko@yahooo.com', '08137337636', 2, '2017-06-28 09:51:30'),
(4, 'UIL/02/16374', '100', 'e10adc3949ba59abbe56e057f20f883e', 'Ibrahim', 'Ridawan', 'Male', 'ibrorid@gmail.com', '872893789948', 4, '2017-06-28 09:53:13'),
(5, 'UIL/12/212343', '300', 'e10adc3949ba59abbe56e057f20f883e', 'Simeon', 'Paul Dave', 'Male', 'sundave@gmail.com', '080123098647', 3, '2018-05-25 13:07:28'),
(6, '53274480WA', '200', 'e10adc3949ba59abbe56e057f20f883e', 'Babasola', 'Ajibade', 'Male', 'babasolajide@gmail.com', '09028271617', 2, '2018-05-25 13:21:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `admin_email` (`admin_email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`bookid`),
  ADD KEY `book_hostel_id` (`book_hostel_id`),
  ADD KEY `book_stdtid` (`book_stdtid`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`deptid`),
  ADD UNIQUE KEY `dept_name` (`dept_name`),
  ADD UNIQUE KEY `dept_acron` (`dept_acron`),
  ADD KEY `facultyid` (`facultyid`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`facid`),
  ADD UNIQUE KEY `fac_name` (`fac_name`),
  ADD UNIQUE KEY `fac_acron` (`fac_acron`);

--
-- Indexes for table `hostels`
--
ALTER TABLE `hostels`
  ADD PRIMARY KEY (`hostel_id`),
  ADD UNIQUE KEY `hall_name` (`hostel_name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`stdtid`),
  ADD UNIQUE KEY `matric_no` (`matric_no`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `deptid` (`stdt_deptid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminid` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `bookid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `deptid` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `facid` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hostels`
--
ALTER TABLE `hostels`
  MODIFY `hostel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `stdtid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`book_hostel_id`) REFERENCES `hostels` (`hostel_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`book_stdtid`) REFERENCES `students` (`stdtid`) ON UPDATE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `dept_fac` FOREIGN KEY (`facultyid`) REFERENCES `faculties` (`facid`) ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_stdt_dept` FOREIGN KEY (`stdt_deptid`) REFERENCES `departments` (`deptid`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
