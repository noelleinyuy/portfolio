-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2026 at 03:38 AM
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
-- Database: `portfoliodb`
--

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_admin`
--

CREATE TABLE `portfolio_admin` (
  `AdminID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portfolio_admin`
--

INSERT INTO `portfolio_admin` (`AdminID`, `Name`, `Email`, `Password`) VALUES
(1, 'Noel', 'admin@noelportfolio.com', '$2y$12$7MT8O.tOlZl4UhyWjt2A1.VY7QsEcuwXTXt.sRfiC5.R8BcqnhXoC');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_messages`
--

CREATE TABLE `portfolio_messages` (
  `MessageID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Phone` varchar(30) DEFAULT NULL,
  `Subject` varchar(200) DEFAULT NULL,
  `Message` text NOT NULL,
  `Status` enum('Unread','Read','Replied') DEFAULT 'Unread',
  `Reply` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `RepliedAt` timestamp NULL DEFAULT NULL,
  `IsRead` tinyint(1) NOT NULL DEFAULT 0,
  `IsReplied` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portfolio_messages`
--

INSERT INTO `portfolio_messages` (`MessageID`, `FullName`, `Email`, `Phone`, `Subject`, `Message`, `Status`, `Reply`, `CreatedAt`, `RepliedAt`, `IsRead`, `IsReplied`) VALUES
(4, 'Noel leinyuy', 'admin@sipms.com', '681730846', 'igkbd', 'ljlfejblcd', 'Read', NULL, '2026-08-29 23:21:02', NULL, 0, 0),
(9, 'Noel leinyuy', 'easytechs29@gmail.com', '681730806', 'easytechs29@gmail.com', 'hkvhvh', 'Unread', NULL, '2026-09-01 13:10:09', NULL, 0, 0),
(10, 'Noel leinyuy', 'easytechs29@gmail.com', '681730806', 'easytechs29@gmail.com', 'hkvhvh', 'Unread', NULL, '2026-09-01 13:10:50', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_projects`
--

CREATE TABLE `portfolio_projects` (
  `ProjectID` int(11) NOT NULL,
  `Title` varchar(150) NOT NULL,
  `Description` text NOT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `ProjectLink` varchar(255) DEFAULT NULL,
  `Status` enum('Visible','Hidden') DEFAULT 'Visible',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_readme`
--

CREATE TABLE `portfolio_readme` (
  `ReadmeID` int(11) NOT NULL,
  `Heading` varchar(200) NOT NULL,
  `Content` text NOT NULL,
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portfolio_readme`
--

INSERT INTO `portfolio_readme` (`ReadmeID`, `Heading`, `Content`, `UpdatedAt`) VALUES
(1, 'About Noel', 'ABOUT ME\r\n\r\nMy name is Noel, and I am a Software Engineering student with a strong interest in technology, software development, and digital innovation.\r\n\r\nI am passionate about understanding how technology works and using it to create practical solutions to real-world problems. My interests include Web Development, Artificial Intelligence, Mobile Application Development, Cybersecurity, and emerging technologies.\r\n\r\nMY JOURNEY\r\n\r\nMy journey into software engineering started with curiosity about how websites, applications, and digital systems are built. That curiosity gradually developed into a desire to understand programming, databases, system design, and the different technologies that work together to create modern software.\r\n\r\nAs I continue my studies and practical training, I am focused on turning what I learn in the classroom into real projects and useful applications.\r\n\r\nWHAT I DO\r\n\r\nI am developing my skills in:\r\n\r\n• Web Development\r\n• Frontend Development\r\n• Backend Development\r\n• Database Management\r\n• Software Engineering\r\n• UI/UX Design\r\n• Artificial Intelligence\r\n• Mobile Application Development\r\n• Cybersecurity\r\n\r\nI enjoy working on projects that allow me to combine creativity, problem-solving, and technology.\r\n\r\nMY APPROACH\r\n\r\nI believe that good software should not only work, but should also be simple, useful, secure, and easy to understand.\r\n\r\nWhen working on a project, I focus on understanding the problem first, designing a practical solution, building it carefully, testing it, and continuously improving it.\r\n\r\nCURRENT FOCUS\r\n\r\nAt the moment, I am focused on strengthening my software engineering foundation through academic learning, practical projects, and internship experience.\r\n\r\nI am particularly interested in developing applications that solve real problems and can be useful to individuals, businesses, schools, and communities.\r\n\r\nMY GOALS\r\n\r\nMy long-term goal is to become a highly skilled software engineer capable of designing and building complete software systems.\r\n\r\nI want to continue exploring Artificial Intelligence, Mobile Development, Web Technologies, Cybersecurity, and other areas of modern computing.\r\n\r\nI also want to use technology to contribute to innovative projects and eventually build products and solutions of my own.\r\n\r\nBEYOND CODING\r\n\r\nFor me, software engineering is not only about writing code. It is about creativity, problem-solving, learning, experimentation, and continuous improvement.\r\n\r\nTechnology changes constantly, so I believe that one of the most important skills for a developer is the ability and willingness to keep learning.\r\n\r\nWHAT I CAN OFFER\r\n\r\nI bring curiosity, determination, creativity, and a willingness to learn to every project I work on.\r\n\r\nWhether it is developing a website, working with databases, designing a software system, or exploring a new technology, I am always interested in improving my skills and finding better ways to solve problems.\r\n\r\nLET\'S CONNECT\r\n\r\nI am always open to learning, collaborating, discussing technology, and working on interesting projects.\r\n\r\nIf you would like to connect, collaborate, or discuss a project, feel free to reach out through the contact section of this portfolio.\r\n\r\nThank you for taking the time to learn more about me.', '2026-09-03 01:33:13');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_services`
--

CREATE TABLE `portfolio_services` (
  `ServiceID` int(11) NOT NULL,
  `Title` varchar(150) NOT NULL,
  `Description` text NOT NULL,
  `Icon` varchar(100) DEFAULT NULL,
  `Status` enum('Visible','Hidden') DEFAULT 'Visible',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portfolio_services`
--

INSERT INTO `portfolio_services` (`ServiceID`, `Title`, `Description`, `Icon`, `Status`, `CreatedAt`) VALUES
(2, 'Web Development', 'Building responsive, high-performance websites using modern technologies like React, Node.js, and MongoDB.', 'bx bx-code', 'Visible', '2026-09-01 14:53:50'),
(3, 'UI/UX Design', 'Creating intuitive, user-centered designs with Figma, Adobe XD, and a focus on accessibility and brand consistency.', 'bx bx-code', 'Visible', '2026-09-01 14:54:53');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_settings`
--

CREATE TABLE `portfolio_settings` (
  `SettingID` int(11) NOT NULL,
  `SettingName` varchar(100) NOT NULL,
  `SettingValue` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portfolio_settings`
--

INSERT INTO `portfolio_settings` (`SettingID`, `SettingName`, `SettingValue`) VALUES
(1, 'SiteName', 'Noel'),
(2, 'HomeIntro', 'Hi, I\'m Noel  a passionate first-year Software Engineering student at the Catholic University of Cameroon (CATUC), Bamenda. I love building things that live on the web and solving problems with code.'),
(3, 'TypedRoles', 'Software Engineering Student, Web Developer, UI/UX Enthusiast, Tech Learner'),
(4, 'AboutHeading', 'First-Year Software Engineering Student at CATUC Bamenda'),
(5, 'AboutText', 'I\'m currently pursuing my HND in Software Engineering at the Catholic University of Cameroon (CATUC), Bamenda. I\'m passionate about technology, web development, and design. I enjoy turning complex problems into simple, beautiful, and functional solutions. I\'m constantly learning and exploring new tools and frameworks to grow as a developer.'),
(6, 'ProfileImage', 'images/uploads/img_6a978885e69788.75501101.jpg'),
(7, 'AboutImage', 'images/uploads/img_6a978885e780c0.24751207.jpg'),
(8, 'CVLink', 'files/Noel_CV.pdf'),
(9, 'SocialLinkedIn', ''),
(10, 'SocialGithub', ''),
(11, 'SocialFacebook', ''),
(12, 'SocialInstagram', '');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_testimonials`
--

CREATE TABLE `portfolio_testimonials` (
  `TestimonialID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Role` varchar(100) DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `Message` text NOT NULL,
  `Rating` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `Status` enum('Visible','Hidden') DEFAULT 'Visible',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portfolio_testimonials`
--

INSERT INTO `portfolio_testimonials` (`TestimonialID`, `Name`, `Email`, `Role`, `Image`, `Message`, `Rating`, `Status`, `CreatedAt`) VALUES
(2, 'amigo', 'amigo@example.com', 'Web Application Project', 'images/02.jpeg', 'Working with Noel on our final web application project was an incredible experience. He built the entire interactive dashboard from scratch using vanilla JavaScript, turning our chaotic design concepts into a highly polished, responsive user interface. His deep understanding of CSS Grid and Flexbox ensured the site looked perfect on any screen size, and his ability to quickly troubleshoot broken event listeners saved our team hours of frustration. Noel is an exceptionally talented frontend developer who consistently delivers high-quality code.', 5, 'Hidden', '2026-08-29 02:06:55'),
(3, 'foncha', 'foncha@example.com', 'Web Development Sprint', 'images/03.jpeg', 'Working with Noel during our 48-hour development sprint was a fantastic experience. He built the complex API integration system and the dynamic search filters that served as the core functionality of our web app prototype. Noel\'s skill in writing optimized JavaScript allowed our platform to handle live data smoothly without lagging, which ultimately won us a top spot in the competition. He is a reliable, fast-working developer who brings immense value to any development team.', 5, 'Hidden', '2026-08-29 02:06:55'),
(11, 'Noel leinyuy', 'easyte29@gmail.com', 'Web Development Sprint', 'images/testimonials/1788267916_NOEL.jpg', 'nice work', 4, 'Visible', '2026-09-01 13:05:16');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_themes`
--

CREATE TABLE `portfolio_themes` (
  `ThemeID` int(11) NOT NULL,
  `ThemeName` varchar(100) NOT NULL,
  `ThemeCode` varchar(50) NOT NULL,
  `Status` enum('Active','Inactive') DEFAULT 'Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portfolio_themes`
--

INSERT INTO `portfolio_themes` (`ThemeID`, `ThemeName`, `ThemeCode`, `Status`) VALUES
(1, 'Theme One', 'theme1', 'Active'),
(2, 'Theme Two', 'theme2', 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_visitor_actions`
--

CREATE TABLE `portfolio_visitor_actions` (
  `ActionID` int(11) NOT NULL,
  `SessionID` varchar(64) NOT NULL,
  `ActionType` varchar(50) NOT NULL,
  `ActionDetail` varchar(255) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portfolio_visitor_actions`
--

INSERT INTO `portfolio_visitor_actions` (`ActionID`, `SessionID`, `ActionType`, `ActionDetail`, `CreatedAt`) VALUES
(1, '0i0gkj9lma94r8emnp821d7f5q', 'testimonial_submit', 'Noel leinyuy', '2026-09-01 09:26:29'),
(2, '0i0gkj9lma94r8emnp821d7f5q', 'testimonial_submit', 'kjgu', '2026-09-01 10:29:21'),
(3, '4mgrsng5jupk6r1r76lvicn840', 'testimonial_submit', 'Easy Techs', '2026-09-01 12:58:31'),
(4, '4mgrsng5jupk6r1r76lvicn840', 'testimonial_submit', 'Easy Techs', '2026-09-01 13:05:16'),
(5, '4mgrsng5jupk6r1r76lvicn840', 'contact_form_submit', 'easytechs29@gmail.com', '2026-09-01 13:10:09'),
(6, '4mgrsng5jupk6r1r76lvicn840', 'contact_form_submit', 'easytechs29@gmail.com', '2026-09-01 13:10:50');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_visits`
--

CREATE TABLE `portfolio_visits` (
  `VisitID` int(11) NOT NULL,
  `PageURL` varchar(255) NOT NULL,
  `IPAddress` varchar(45) NOT NULL,
  `UserAgent` varchar(255) DEFAULT NULL,
  `Referrer` varchar(255) DEFAULT NULL,
  `SessionID` varchar(64) NOT NULL,
  `VisitedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portfolio_visits`
--

INSERT INTO `portfolio_visits` (`VisitID`, `PageURL`, `IPAddress`, `UserAgent`, `Referrer`, `SessionID`, `VisitedAt`) VALUES
(1, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '0i0gkj9lma94r8emnp821d7f5q', '2026-09-01 08:56:26'),
(2, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '0i0gkj9lma94r8emnp821d7f5q', '2026-09-01 08:56:30'),
(3, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 12:32:10'),
(4, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 12:36:10'),
(5, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 12:37:18'),
(6, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 12:37:22'),
(7, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 12:37:22'),
(8, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 12:37:23'),
(9, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 12:37:23'),
(10, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 12:41:06'),
(11, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 12:41:08'),
(12, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 12:41:09'),
(13, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 12:41:09'),
(14, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 12:41:10'),
(15, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:08:31'),
(16, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:08:39'),
(17, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/index.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:10:09'),
(18, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/index.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:10:50'),
(19, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:17:13'),
(20, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:19:37'),
(21, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:21:38'),
(22, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:21:41'),
(23, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:21:43'),
(24, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:23:40'),
(25, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:23:43'),
(26, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:23:44'),
(27, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:23:44'),
(28, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:23:44'),
(29, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:23:44'),
(30, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:23:45'),
(31, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:23:52'),
(32, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/portfolio/admin_login.php', '4mgrsng5jupk6r1r76lvicn840', '2026-09-01 13:23:59'),
(33, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', 'e0g9eal6j2ah06uso0rbq64k6k', '2026-09-02 02:10:58'),
(34, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', 'm52jj5ho6591gfu1buv1c6arf3', '2026-09-02 17:08:19'),
(35, 'Home', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '', 'm7rolq1qsn5oo4vtc6p85a8qbj', '2026-09-03 01:22:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `portfolio_admin`
--
ALTER TABLE `portfolio_admin`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `portfolio_messages`
--
ALTER TABLE `portfolio_messages`
  ADD PRIMARY KEY (`MessageID`);

--
-- Indexes for table `portfolio_projects`
--
ALTER TABLE `portfolio_projects`
  ADD PRIMARY KEY (`ProjectID`);

--
-- Indexes for table `portfolio_readme`
--
ALTER TABLE `portfolio_readme`
  ADD PRIMARY KEY (`ReadmeID`);

--
-- Indexes for table `portfolio_services`
--
ALTER TABLE `portfolio_services`
  ADD PRIMARY KEY (`ServiceID`);

--
-- Indexes for table `portfolio_settings`
--
ALTER TABLE `portfolio_settings`
  ADD PRIMARY KEY (`SettingID`),
  ADD UNIQUE KEY `SettingName` (`SettingName`);

--
-- Indexes for table `portfolio_testimonials`
--
ALTER TABLE `portfolio_testimonials`
  ADD PRIMARY KEY (`TestimonialID`);

--
-- Indexes for table `portfolio_themes`
--
ALTER TABLE `portfolio_themes`
  ADD PRIMARY KEY (`ThemeID`),
  ADD UNIQUE KEY `ThemeCode` (`ThemeCode`);

--
-- Indexes for table `portfolio_visitor_actions`
--
ALTER TABLE `portfolio_visitor_actions`
  ADD PRIMARY KEY (`ActionID`);

--
-- Indexes for table `portfolio_visits`
--
ALTER TABLE `portfolio_visits`
  ADD PRIMARY KEY (`VisitID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `portfolio_admin`
--
ALTER TABLE `portfolio_admin`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `portfolio_messages`
--
ALTER TABLE `portfolio_messages`
  MODIFY `MessageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `portfolio_projects`
--
ALTER TABLE `portfolio_projects`
  MODIFY `ProjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `portfolio_readme`
--
ALTER TABLE `portfolio_readme`
  MODIFY `ReadmeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `portfolio_services`
--
ALTER TABLE `portfolio_services`
  MODIFY `ServiceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `portfolio_settings`
--
ALTER TABLE `portfolio_settings`
  MODIFY `SettingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `portfolio_testimonials`
--
ALTER TABLE `portfolio_testimonials`
  MODIFY `TestimonialID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `portfolio_themes`
--
ALTER TABLE `portfolio_themes`
  MODIFY `ThemeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `portfolio_visitor_actions`
--
ALTER TABLE `portfolio_visitor_actions`
  MODIFY `ActionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `portfolio_visits`
--
ALTER TABLE `portfolio_visits`
  MODIFY `VisitID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
