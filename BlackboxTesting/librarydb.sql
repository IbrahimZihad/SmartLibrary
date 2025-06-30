-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2025 at 06:01 AM
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
-- Database: `librarydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookimage`
--

CREATE TABLE `bookimage` (
  `book_id` int(11) NOT NULL,
  `cover_img` varchar(255) DEFAULT NULL,
  `side_img` varchar(255) DEFAULT NULL,
  `back_img` varchar(255) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookimage`
--

INSERT INTO `bookimage` (`book_id`, `cover_img`, `side_img`, `back_img`, `pdf_path`) VALUES
(3, 'Book Images/book_B1/cover.jpg', 'Book Images/book_B1/side.jpg', 'Book Images/book_B1/back.jpg', '../Book Images/book_107/book.pdf'),
(5, 'Book Images/book_B2/cover.jpg', 'Book Images/book_B2/side.jpg', 'Book Images/book_B2/back.jpg', '../Book Images/book_107/book.pdf'),
(6, 'Book Images/book_B3/cover.jpg', 'Book Images/book_B3/side.jpg', 'Book Images/book_B3/back.jpg', '../Book Images/book_107/book.pdf'),
(7, 'Book Images/book_B4/cover.jpg', 'Book Images/book_B4/side.jpg', 'Book Images/book_B4/back.jpg', '../Book Images/book_107/book.pdf'),
(8, 'Book Images/book_B5/cover.jpg', 'Book Images/book_B5/side.jpg', 'Book Images/book_B5/back.jpg', '../Book Images/book_107/book.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `booklist`
--

CREATE TABLE `booklist` (
  `book_id` int(11) NOT NULL,
  `book_name` varchar(255) DEFAULT NULL,
  `total_copies` int(11) DEFAULT NULL,
  `available_copies` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booklist`
--

INSERT INTO `booklist` (`book_id`, `book_name`, `total_copies`, `available_copies`) VALUES
(3, 'B1', 10, 10),
(5, 'B2', 4, 4),
(6, 'B3', 3, 3),
(7, 'B4', 2, 2),
(8, 'B5', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `borrowhistory`
--

CREATE TABLE `borrowhistory` (
  `student_id` varchar(10) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `borrowed_copies` int(11) DEFAULT NULL,
  `penalty` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowhistory`
--

INSERT INTO `borrowhistory` (`student_id`, `book_id`, `borrow_date`, `due_date`, `return_date`, `borrowed_copies`, `penalty`) VALUES
('011221257', 3, '2025-06-01', '2025-06-07', '2024-06-10', 1, 0.00),
('011221257', 8, '2025-06-30', '2025-07-07', NULL, 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `penalty`
--

CREATE TABLE `penalty` (
  `student_id` varchar(10) NOT NULL,
  `total_penalty` decimal(10,2) DEFAULT NULL,
  `status` enum('paid','not paid') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penalty`
--

INSERT INTO `penalty` (`student_id`, `total_penalty`, `status`) VALUES
('011221257', 0.00, 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `studentimage`
--

CREATE TABLE `studentimage` (
  `student_id` varchar(10) NOT NULL,
  `front_img` varchar(255) DEFAULT NULL,
  `left_img` varchar(255) DEFAULT NULL,
  `right_img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentimage`
--

INSERT INTO `studentimage` (`student_id`, `front_img`, `left_img`, `right_img`) VALUES
('011221257', 'StudentImage/011221257/front_front_image.jpg', 'StudentImage/011221257/left_left_image.jpg', 'StudentImage/011221257/right_right_image.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(10) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `phone`, `email`, `department`) VALUES
('011221257', 'Md. Ibrahim Zihad', '01885434861', 'mzihad221257@bscse.uiu.ac.bd', 'BSCSE');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `bookimage`
--
ALTER TABLE `bookimage`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `booklist`
--
ALTER TABLE `booklist`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `borrowhistory`
--
ALTER TABLE `borrowhistory`
  ADD PRIMARY KEY (`student_id`,`book_id`,`borrow_date`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `penalty`
--
ALTER TABLE `penalty`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `studentimage`
--
ALTER TABLE `studentimage`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booklist`
--
ALTER TABLE `booklist`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookimage`
--
ALTER TABLE `bookimage`
  ADD CONSTRAINT `bookimage_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `booklist` (`book_id`) ON DELETE CASCADE;

--
-- Constraints for table `borrowhistory`
--
ALTER TABLE `borrowhistory`
  ADD CONSTRAINT `borrowhistory_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `borrowhistory_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `booklist` (`book_id`) ON DELETE CASCADE;

--
-- Constraints for table `penalty`
--
ALTER TABLE `penalty`
  ADD CONSTRAINT `penalty_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `studentimage`
--
ALTER TABLE `studentimage`
  ADD CONSTRAINT `studentimage_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
