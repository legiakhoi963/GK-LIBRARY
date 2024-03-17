-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2023 at 04:42 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `DISPLAY_BOOKS` ()   SELECT * FROM book
ORDER BY book_id DESC$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `DISPLAY_USER_NAME` (`search_id` INT) RETURNS VARCHAR(50) CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN
DECLARE result VARCHAR(50);
SELECT user_name INTO result FROM user WHERE user_id = search_id;
RETURN result;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(50) NOT NULL,
  `admin_address` varchar(200) NOT NULL,
  `admin_contact` varchar(20) NOT NULL,
  `admin_email` varchar(50) NOT NULL,
  `admin_password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `admin_address`, `admin_contact`, `admin_email`, `admin_password`) VALUES
(1, 'Tran Van Admin', 'Thư Viện, Ninh Kiều, Cần Thơ', '0968123456', 'admin@gmail.com', 'admin'),
(2, 'Nguyen Van Admin1', 'Thư Viện, Ninh Kiều, Cần Thơ', '0968234567', 'admin1@gmail.com', 'admin1');

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

CREATE TABLE `author` (
  `author_id` int(11) NOT NULL,
  `author_name` varchar(50) NOT NULL,
  `author_status` enum('Enable','Disable') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `author`
--

INSERT INTO `author` (`author_id`, `author_name`, `author_status`) VALUES
(1, 'Bernard Marr', 'Enable'),
(2, 'Carl Gustav Jung', 'Enable'),
(3, 'Jared Diamond', 'Enable'),
(4, 'Nassim Nicholas Taleb', 'Enable'),
(5, 'Nguyễn Văn Tuấn', 'Enable'),
(6, 'Lê Xuân Mậu', 'Enable'),
(7, 'Bill Bryson', 'Enable'),
(8, 'Terrence J. Sejnowski', 'Enable'),
(9, 'Nguyễn Duy Cần', 'Enable'),
(10, 'Tim Marshall', 'Enable');

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `book_id` int(11) NOT NULL,
  `book_code` varchar(20) NOT NULL,
  `book_name` varchar(200) NOT NULL,
  `book_location` varchar(20) NOT NULL,
  `book_copies` int(11) NOT NULL,
  `book_publisher` int(11) NOT NULL,
  `book_status` enum('Enable','Disable') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`book_id`, `book_code`, `book_name`, `book_location`, `book_copies`, `book_publisher`, `book_status`) VALUES
(1, 'B000001', 'Dữ Liệu Lớn', 'A01B01', 1, 1, 'Enable'),
(2, 'B000002', 'Phân Tích Dữ Liệu Với R', 'A01B01', 1, 3, 'Enable'),
(3, 'B000003', 'Con người và Biểu tượng', 'A01B01', 2, 2, 'Enable'),
(4, 'B000004', 'Súng, Vi Trùng Và Thép', 'A01B01', 2, 2, 'Enable'),
(5, 'B000005', 'Thiên Nga Đen', 'A01B01', 2, 2, 'Enable'),
(6, 'B000006', 'Vẻ Đẹp Ngôn Ngữ - Vẻ Đẹp Văn Chương', 'A01B01', 2, 4, 'Enable'),
(7, 'B000007', 'Lược Sử Vạn Vật', 'A01B01', 2, 5, 'Enable'),
(8, 'B000008', 'Deep Learning - Cuộc Cách Mạng Học Sâu', 'A01B01', 2, 1, 'Enable'),
(9, 'B000009', 'Một Nghệ Thuật Sống', 'A01B01', 2, 4, 'Enable'),
(10, 'B000010', 'Những Tù Nhân Của Địa Lý', 'A01B01', 1, 6, 'Enable');

-- --------------------------------------------------------

--
-- Table structure for table `book_author`
--

CREATE TABLE `book_author` (
  `ba_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_author`
--

INSERT INTO `book_author` (`ba_id`, `book_id`, `author_id`) VALUES
(1, 1, 1),
(2, 2, 5),
(3, 3, 2),
(4, 4, 3),
(5, 5, 4),
(6, 6, 6),
(7, 7, 7),
(8, 8, 8),
(9, 9, 9),
(10, 10, 10);

-- --------------------------------------------------------

--
-- Table structure for table `book_category`
--

CREATE TABLE `book_category` (
  `bc_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_category`
--

INSERT INTO `book_category` (`bc_id`, `book_id`, `category_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 2),
(4, 4, 4),
(5, 5, 4),
(6, 6, 5),
(7, 7, 6),
(8, 9, 4),
(9, 10, 10),
(10, 8, 7);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_status` enum('Enable','Disable') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `category_status`) VALUES
(1, '000 - Khoa học máy tính, thông tin và tác phẩm tổng quát', 'Enable'),
(2, '100 - Triết học và Tâm lý học', 'Enable'),
(3, '200 - Tôn giáo học', 'Enable'),
(4, '300 - Khoa học xã hội', 'Enable'),
(5, '400 - Ngôn ngữ học', 'Enable'),
(6, '500 - Khoa học tự nhiên', 'Enable'),
(7, '600 - Công nghệ và khoa học ứng dụng', 'Enable'),
(8, '700 - Nghệ thuật và giải trí', 'Enable'),
(9, '800 - Văn học', 'Enable'),
(10, '900 - Địa lý và lịch sử', 'Enable');

-- --------------------------------------------------------

--
-- Table structure for table `issue_book`
--

CREATE TABLE `issue_book` (
  `issue_book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `issue_book_issue_date` varchar(30) NOT NULL,
  `issue_book_return_date` varchar(30) NOT NULL,
  `issue_book_real_return_date` varchar(30) NOT NULL,
  `issue_book_fines` varchar(20) NOT NULL,
  `issue_book_status` enum('Pending','Issue','Return','Not Return','Decline') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issue_book`
--

INSERT INTO `issue_book` (`issue_book_id`, `user_id`, `book_id`, `admin_id`, `issue_book_issue_date`, `issue_book_return_date`, `issue_book_real_return_date`, `issue_book_fines`, `issue_book_status`) VALUES
(1, 1, 1, 1, '01-04-2023 10:46:30', '04-04-2023 10:46:30', '10-04-2023 11:46:30', '60000', 'Return'),
(2, 1, 1, 1, '11-04-2023 10:46:36', '14-04-2023 10:46:36', '15-05-2023 06:52:14', '0', 'Return'),
(3, 2, 1, 1, '23-04-2023 10:51:06', '26-04-2023 10:51:06', '', '180000', 'Not Return'),
(4, 2, 1, 1, '23-04-2023 10:50:31', '', '', '0', 'Pending'),
(5, 1, 2, 1, '23-04-2023 10:51:34', '26-04-2023 10:51:34', '', '180000', 'Not Return'),
(6, 1, 2, 1, '23-04-2023 10:51:41', '26-04-2023 10:51:41', '15-05-2023 09:32:10', '180000', 'Return'),
(7, 1, 3, 1, '23-04-2023 10:52:21', '', '', '0', 'Pending'),
(8, 3, 3, 1, '23-04-2023 10:55:40', '', '', '0', 'Decline'),
(9, 3, 3, 1, '23-04-2023 10:55:42', '', '', '0', 'Pending'),
(11, 1, 3, 1, '23-04-2023 18:54:20', '', '', '0', 'Pending'),
(12, 1, 3, 1, '23-04-2023 18:54:23', '', '', '0', 'Pending'),
(13, 4, 10, 1, '30-04-2023 22:45:37', '03-05-2023 22:45:37', '', '110000', 'Not Return');

-- --------------------------------------------------------

--
-- Table structure for table `publisher`
--

CREATE TABLE `publisher` (
  `publisher_id` int(11) NOT NULL,
  `publisher_name` varchar(50) NOT NULL,
  `publisher_address` varchar(200) NOT NULL,
  `publisher_website` varchar(50) NOT NULL,
  `publisher_status` enum('Enable','Disable') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publisher`
--

INSERT INTO `publisher` (`publisher_id`, `publisher_name`, `publisher_address`, `publisher_website`, `publisher_status`) VALUES
(1, 'NXB Công Thương', '655 Phạm Văn Đồng, Bắc Từ Liêm, Hà Nội', 'https://nhaxuatbancongthuong.com.vn', 'Enable'),
(2, 'NXB Thế Giới', '46 Trần Hưng Đạo, Hà Nội', 'http://www.thegioipublishers.vn', 'Enable'),
(3, 'NXB Tổng hợp TP.HCM', '62 Nguyễn Thị Minh Khai, Đa Kao, Quận 1, TPHCM', 'https://nxbhcm.com.vn', 'Enable'),
(4, 'NXB Trẻ', '161B Lý Chính Thắng, Võ Thị Sáu, Quận 3, TP. Hồ Chí Minh', 'https://www.nxbtre.com.vn', 'Enable'),
(5, 'NXB Khoa học Xã Hội', '57 Sương Nguyệt Ánh, Quận 1, TP. Hồ Chí Minh', 'https://ssph.vn', 'Enable'),
(6, 'NXB Hội Nhà Văn', '65 Nguyễn Du, Hai Bà Trưng, Hà Nội', 'https://nxbhoinhavan.vn', 'Enable');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_address` varchar(200) NOT NULL,
  `user_contact` varchar(20) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_password` varchar(20) NOT NULL,
  `user_status` enum('Enable','Disable') NOT NULL,
  `user_date_created` varchar(30) NOT NULL,
  `user_date_updated` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `user_address`, `user_contact`, `user_email`, `user_password`, `user_status`, `user_date_created`, `user_date_updated`) VALUES
(1, 'Tran Van User', 'Thư Viện, Ninh Kiều, Cần Thơ', '0968345678', 'user@gmail.com', 'user', 'Enable', '01-04-2023 00:00:00', '23-04-2023 01:15:02'),
(2, 'Nguyen Van User1', 'Thư Viện, Ninh Kiều, Cần Thơ', '0968456789', 'user1@gmail.com', 'user1', 'Enable', '01-04-2023 00:00:00', '23-04-2023 10:38:09'),
(3, 'Le Van User2', 'Thư Viện, Ninh Kiều, Cần Thơ', '0968963852', 'user2@gmail.com', 'user2', 'Disable', '01-04-2023 00:00:00', '23-04-2023 10:44:04'),
(4, 'test', 'test', '123', 'test@gmail.com', 'test', 'Enable', '30-04-2023 22:18:50', '');

--
-- Triggers `user`
--
DELIMITER $$
CREATE TRIGGER `ENABLE_USER` BEFORE INSERT ON `user` FOR EACH ROW IF NEW.user_status IS NULL OR NEW.user_status = '' THEN
SET NEW.user_status = 'Enable';
END IF
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `author`
--
ALTER TABLE `author`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `b_publisher` (`book_publisher`);

--
-- Indexes for table `book_author`
--
ALTER TABLE `book_author`
  ADD PRIMARY KEY (`ba_id`),
  ADD KEY `ba_book` (`book_id`),
  ADD KEY `ba_author` (`author_id`);

--
-- Indexes for table `book_category`
--
ALTER TABLE `book_category`
  ADD PRIMARY KEY (`bc_id`),
  ADD KEY `bc_book` (`book_id`),
  ADD KEY `bc_category` (`category_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `issue_book`
--
ALTER TABLE `issue_book`
  ADD PRIMARY KEY (`issue_book_id`),
  ADD KEY `ib_user` (`user_id`),
  ADD KEY `ib_book` (`book_id`),
  ADD KEY `ib_admin` (`admin_id`);

--
-- Indexes for table `publisher`
--
ALTER TABLE `publisher`
  ADD PRIMARY KEY (`publisher_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `author`
--
ALTER TABLE `author`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `book_author`
--
ALTER TABLE `book_author`
  MODIFY `ba_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `book_category`
--
ALTER TABLE `book_category`
  MODIFY `bc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `issue_book`
--
ALTER TABLE `issue_book`
  MODIFY `issue_book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `publisher`
--
ALTER TABLE `publisher`
  MODIFY `publisher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `b_publisher` FOREIGN KEY (`book_publisher`) REFERENCES `publisher` (`publisher_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `book_author`
--
ALTER TABLE `book_author`
  ADD CONSTRAINT `ba_author` FOREIGN KEY (`author_id`) REFERENCES `author` (`author_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ba_book` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `book_category`
--
ALTER TABLE `book_category`
  ADD CONSTRAINT `bc_book` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bc_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `issue_book`
--
ALTER TABLE `issue_book`
  ADD CONSTRAINT `ib_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ib_book` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ib_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
