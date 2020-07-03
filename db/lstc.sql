-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2020 at 02:13 PM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lstc`
--
CREATE DATABASE IF NOT EXISTS `lstc` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `lstc`;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `description` varchar(30) NOT NULL,
  `day` varchar(60) NOT NULL,
  `year_id` int(11) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `school_year` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `exam_id` int(11) NOT NULL,
  `exam_title` varchar(30) NOT NULL,
  `score` varchar(30) NOT NULL,
  `term` varchar(30) NOT NULL,
  `exam_date` varchar(40) NOT NULL,
  `year_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `school_year` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL,
  `term` varchar(20) NOT NULL,
  `grade` varchar(20) NOT NULL,
  `grade_date` varchar(30) NOT NULL,
  `year_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `school_year` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `permissions` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`group_id`, `name`, `permissions`) VALUES
(1, 'Standard user', ''),
(2, 'Instructor', '{\"instructor\": 1}'),
(3, 'Administrator', '{\"admin\": 1}');

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `instructor_id` int(11) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `firstname` varchar(20) NOT NULL,
  `middlename` varchar(20) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `instructors`
--

INSERT INTO `instructors` (`instructor_id`, `lastname`, `firstname`, `middlename`, `gender`, `user_id`) VALUES
(1, 'Garcia', 'Allan', '', 'Male', 3);

-- --------------------------------------------------------

--
-- Table structure for table `instructor_subjects`
--

CREATE TABLE `instructor_subjects` (
  `instructor_subject_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `school_year` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int(11) NOT NULL,
  `quiz_title` varchar(30) NOT NULL,
  `score` varchar(30) NOT NULL,
  `term` varchar(30) NOT NULL,
  `quiz_date` varchar(40) NOT NULL,
  `year_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `school_year` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recitations`
--

CREATE TABLE `recitations` (
  `recitation_id` int(11) NOT NULL,
  `recitation_title` varchar(30) NOT NULL,
  `score` varchar(30) NOT NULL,
  `term` varchar(30) NOT NULL,
  `recitation_date` varchar(40) NOT NULL,
  `year_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `school_year` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `school_year`
--

CREATE TABLE `school_year` (
  `sy_id` int(11) NOT NULL,
  `schoolyear` varchar(30) NOT NULL,
  `semester` varchar(40) NOT NULL,
  `isCurrent` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL,
  `section_name` varchar(20) NOT NULL,
  `program` varchar(64) NOT NULL,
  `year` varchar(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `student_no` varchar(30) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `firstname` varchar(20) NOT NULL,
  `middlename` varchar(20) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `birthday` varchar(30) NOT NULL,
  `address` text NOT NULL,
  `program` varchar(64) NOT NULL,
  `year_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_no`, `lastname`, `firstname`, `middlename`, `gender`, `birthday`, `address`, `program`, `year_id`, `user_id`) VALUES
(1, '0001', 'Sacchi', 'Sacchiko', '', 'Male', '2019-01-23', '', 'Bachelor of Science in Computer Science', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `student_behavior`
--

CREATE TABLE `student_behavior` (
  `behavior_id` int(11) NOT NULL,
  `behavior` varchar(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `year_id` int(11) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `school_year` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student_section`
--

CREATE TABLE `student_section` (
  `student_section_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `school_year` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student_subject`
--

CREATE TABLE `student_subject` (
  `student_subject_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `year_id` int(11) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `school_year` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student_year`
--

CREATE TABLE `student_year` (
  `student_year_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `year_id` int(11) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `school_year` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(50) NOT NULL,
  `class_days` varchar(100) NOT NULL,
  `class_starts` varchar(20) NOT NULL,
  `class_ends` varchar(20) NOT NULL,
  `section_id` int(11) NOT NULL,
  `units` varchar(11) NOT NULL,
  `program` varchar(100) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `school_year` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `firstname` varchar(20) NOT NULL,
  `middlename` varchar(20) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(64) NOT NULL,
  `salt` varchar(32) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `lastname`, `firstname`, `middlename`, `username`, `password`, `salt`, `group_id`) VALUES
(1, 'Arcangel', 'John', '', 'arc.john', 'd666750e8063599b008357452214a1c659858899704fa2068bf2793b89f7323e', 'i&B^kX[=;5+*fr=6Govi<N4u2ez^lD#n', 3),
(2, 'Sacchi', 'Sacchiko', '', 'S0001', '481caac239b4a2171cb6e29bcfb92d7e2c512afa75bebd5e96b6140d3fa06449', 'wGhO{V+%S$@~9=I2v_zBsOInh`j4QM$v', 1),
(3, 'Garcia', 'Allan', '', 'garcia.allan', '9819ae307c56d38ee7b0715399ac1e8de0348d85dec09f49b1ea8d2cd9ccfb9a', '`J:i^!5lC.f4(qimDXK/@Fta(a8ptY0~', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users_session`
--

CREATE TABLE `users_session` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hash` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_session`
--

INSERT INTO `users_session` (`session_id`, `user_id`, `hash`) VALUES
(30, 1, 'f5a4d2e49d9a6c487925cf077c050b9c56e6ffe3e32791b6d050df793c906c3d');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_current_school_year`
-- (See below for the actual view)
--
CREATE TABLE `view_current_school_year` (
`sy_id` int(11)
,`schoolyear` varchar(30)
,`semester` varchar(40)
,`isCurrent` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_current_student_section_list`
-- (See below for the actual view)
--
CREATE TABLE `view_current_student_section_list` (
`student_name` varchar(63)
,`student_section_id` int(11)
,`section_id` int(11)
,`student_id` int(11)
,`semester` varchar(20)
,`school_year` varchar(20)
,`section_name` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_get_students_subject_name`
-- (See below for the actual view)
--
CREATE TABLE `view_get_students_subject_name` (
`lastname` varchar(20)
,`subject_code` varchar(20)
,`section_name` varchar(20)
,`units` varchar(11)
,`student_id` int(11)
,`year_id` int(11)
,`semester` varchar(20)
,`school_year` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_grades`
-- (See below for the actual view)
--
CREATE TABLE `view_grades` (
`grade_id` int(11)
,`student_id` int(11)
,`fullName` varchar(42)
,`subject_code` varchar(20)
,`subject_name` varchar(50)
,`units` varchar(11)
,`grade` varchar(20)
,`term` varchar(20)
,`year_id` int(11)
,`year` varchar(20)
,`subject_id` int(11)
,`instructor_id` int(11)
,`lastname` varchar(20)
,`firstname` varchar(20)
,`gender` varchar(20)
,`semester` varchar(30)
,`school_year` varchar(30)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_instructors`
-- (See below for the actual view)
--
CREATE TABLE `view_instructors` (
`instructor_id` int(11)
,`lastname` varchar(20)
,`firstname` varchar(20)
,`middlename` varchar(20)
,`gender` varchar(20)
,`user_id` int(11)
,`username` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_instructor_schedule`
-- (See below for the actual view)
--
CREATE TABLE `view_instructor_schedule` (
`subject_id` int(11)
,`instructor_id` int(11)
,`subject_code` varchar(20)
,`subject_name` varchar(50)
,`class_days` varchar(100)
,`clsTime` varchar(43)
,`section_name` varchar(20)
,`units` varchar(11)
,`semester` varchar(30)
,`school_year` varchar(30)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_instructor_subjects`
-- (See below for the actual view)
--
CREATE TABLE `view_instructor_subjects` (
`instructor_id` int(11)
,`instructor_subject_id` int(11)
,`subject_id` int(11)
,`subject_code` varchar(20)
,`subject_name` varchar(50)
,`sched` varchar(146)
,`units` varchar(11)
,`section_name` varchar(20)
,`semester` varchar(30)
,`school_year` varchar(30)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_instructor_subjects_all`
-- (See below for the actual view)
--
CREATE TABLE `view_instructor_subjects_all` (
`instructor_subject_id` int(11)
,`instructor_id` int(11)
,`subject_id` int(11)
,`semester` varchar(30)
,`school_year` varchar(30)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_school_year`
-- (See below for the actual view)
--
CREATE TABLE `view_school_year` (
`sy_id` int(11)
,`schoolyear` varchar(30)
,`semester` varchar(40)
,`isCurrent` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_sections`
-- (See below for the actual view)
--
CREATE TABLE `view_sections` (
`section_id` int(11)
,`section_name` varchar(20)
,`program` varchar(64)
,`year` varchar(18)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_semestral_instructors`
-- (See below for the actual view)
--
CREATE TABLE `view_semestral_instructors` (
`instructor_id` int(11)
,`semester` varchar(30)
,`school_year` varchar(30)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_semestral_students`
-- (See below for the actual view)
--
CREATE TABLE `view_semestral_students` (
`student_id` int(11)
,`semester` varchar(20)
,`school_year` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_students`
-- (See below for the actual view)
--
CREATE TABLE `view_students` (
`student_id` int(11)
,`student_no` varchar(30)
,`lastname` varchar(20)
,`firstname` varchar(20)
,`middlename` varchar(20)
,`gender` varchar(10)
,`birthday` varchar(30)
,`address` text
,`program` varchar(64)
,`year_id` int(11)
,`user_id` int(11)
,`year` varchar(20)
,`username` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_students_by_subjects`
-- (See below for the actual view)
--
CREATE TABLE `view_students_by_subjects` (
`student_id` int(11)
,`student_no` varchar(30)
,`lastname` varchar(20)
,`firstname` varchar(20)
,`middlename` varchar(20)
,`year_id` int(11)
,`year` varchar(20)
,`semester` varchar(20)
,`school_year` varchar(20)
,`subject_id` int(11)
,`instructor_id` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_student_attendance`
-- (See below for the actual view)
--
CREATE TABLE `view_student_attendance` (
`student_id` int(11)
,`fulLName` varchar(63)
,`subject_id` int(11)
,`subjectName` varchar(72)
,`instructor_id` int(11)
,`description` varchar(30)
,`day` varchar(60)
,`school_year` varchar(30)
,`semester` varchar(30)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_student_grades`
-- (See below for the actual view)
--
CREATE TABLE `view_student_grades` (
`student_id` int(11)
,`student_name` varchar(42)
,`subject_code` varchar(20)
,`subject_name` varchar(50)
,`section_name` varchar(20)
,`grade` varchar(20)
,`term` varchar(20)
,`semester` varchar(30)
,`school_year` varchar(30)
,`year_id` int(11)
,`year` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_student_schedule`
-- (See below for the actual view)
--
CREATE TABLE `view_student_schedule` (
`subject_id` int(11)
,`student_id` int(11)
,`subject_code` varchar(20)
,`subject_name` varchar(50)
,`class_days` varchar(100)
,`clsTime` varchar(43)
,`section_name` varchar(20)
,`units` varchar(11)
,`semester` varchar(20)
,`school_year` varchar(20)
,`year_id` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_student_subject`
-- (See below for the actual view)
--
CREATE TABLE `view_student_subject` (
`student_subject_id` int(11)
,`subject_id` int(11)
,`student_id` int(11)
,`semester` varchar(20)
,`school_year` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_student_year`
-- (See below for the actual view)
--
CREATE TABLE `view_student_year` (
`student_year_id` int(11)
,`student_id` int(11)
,`year_id` int(11)
,`year` varchar(20)
,`semester` varchar(30)
,`school_year` varchar(30)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_subjects`
-- (See below for the actual view)
--
CREATE TABLE `view_subjects` (
`subject_id` int(11)
,`subject_code` varchar(20)
,`subject_name` varchar(50)
,`class_days` varchar(100)
,`class_starts` varchar(20)
,`class_ends` varchar(20)
,`section_id` int(11)
,`units` varchar(11)
,`program` varchar(100)
,`semester` varchar(30)
,`school_year` varchar(30)
,`section_name` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_subjects_by_student`
-- (See below for the actual view)
--
CREATE TABLE `view_subjects_by_student` (
`student_subject_id` int(11)
,`subject_code` varchar(20)
,`subject_name` varchar(50)
,`class_days` varchar(100)
,`sched` varchar(43)
,`units` varchar(11)
,`section_id` int(11)
,`section_name` varchar(20)
,`student_name` varchar(42)
,`student_id` int(11)
,`year_id` int(11)
,`semester` varchar(20)
,`school_year` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_total_students_by_subject`
-- (See below for the actual view)
--
CREATE TABLE `view_total_students_by_subject` (
`student_id` int(11)
,`section_id` int(11)
,`subject_id` int(11)
,`instructor_id` int(11)
,`school_year` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `year`
--

CREATE TABLE `year` (
  `year_id` int(11) NOT NULL,
  `year` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `year`
--

INSERT INTO `year` (`year_id`, `year`) VALUES
(1, '1st Year'),
(2, '2nd Year'),
(3, '3rd Year'),
(4, '4th Year');

-- --------------------------------------------------------

--
-- Structure for view `view_current_school_year`
--
DROP TABLE IF EXISTS `view_current_school_year`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_current_school_year`  AS  select `school_year`.`sy_id` AS `sy_id`,`school_year`.`schoolyear` AS `schoolyear`,`school_year`.`semester` AS `semester`,`school_year`.`isCurrent` AS `isCurrent` from `school_year` where `school_year`.`isCurrent` = 1 ;

-- --------------------------------------------------------

--
-- Structure for view `view_current_student_section_list`
--
DROP TABLE IF EXISTS `view_current_student_section_list`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_current_student_section_list`  AS  select concat(`st`.`lastname`,', ',`st`.`firstname`,' ',`st`.`middlename`) AS `student_name`,`stc`.`student_section_id` AS `student_section_id`,`stc`.`section_id` AS `section_id`,`stc`.`student_id` AS `student_id`,`stc`.`semester` AS `semester`,`stc`.`school_year` AS `school_year`,`sc`.`section_name` AS `section_name` from ((`student_section` `stc` join `students` `st` on(`stc`.`student_id` = `st`.`student_id`)) join `sections` `sc` on(`stc`.`section_id` = `sc`.`section_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_get_students_subject_name`
--
DROP TABLE IF EXISTS `view_get_students_subject_name`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_get_students_subject_name`  AS  select `st`.`lastname` AS `lastname`,`sub`.`subject_code` AS `subject_code`,`sc`.`section_name` AS `section_name`,`sub`.`units` AS `units`,`sts`.`student_id` AS `student_id`,`sts`.`year_id` AS `year_id`,`sts`.`semester` AS `semester`,`sts`.`school_year` AS `school_year` from (((`student_subject` `sts` join `students` `st` on(`st`.`student_id` = `sts`.`student_id`)) join `subjects` `sub` on(`sts`.`subject_id` = `sub`.`subject_id`)) join `sections` `sc` on(`sub`.`section_id` = `sc`.`section_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_grades`
--
DROP TABLE IF EXISTS `view_grades`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_grades`  AS  select `gr`.`grade_id` AS `grade_id`,`gr`.`student_id` AS `student_id`,concat(`st`.`lastname`,', ',`st`.`firstname`) AS `fullName`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,`sub`.`units` AS `units`,`gr`.`grade` AS `grade`,`gr`.`term` AS `term`,`gr`.`year_id` AS `year_id`,`yr`.`year` AS `year`,`gr`.`subject_id` AS `subject_id`,`gr`.`instructor_id` AS `instructor_id`,`ins`.`lastname` AS `lastname`,`ins`.`firstname` AS `firstname`,`ins`.`gender` AS `gender`,`gr`.`semester` AS `semester`,`gr`.`school_year` AS `school_year` from ((((`grades` `gr` join `students` `st` on(`st`.`student_id` = `gr`.`student_id`)) join `subjects` `sub` on(`sub`.`subject_id` = `gr`.`subject_id`)) join `instructors` `ins` on(`ins`.`instructor_id` = `gr`.`instructor_id`)) join `year` `yr` on(`yr`.`year_id` = `gr`.`year_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_instructors`
--
DROP TABLE IF EXISTS `view_instructors`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_instructors`  AS  select `ins`.`instructor_id` AS `instructor_id`,`ins`.`lastname` AS `lastname`,`ins`.`firstname` AS `firstname`,`ins`.`middlename` AS `middlename`,`ins`.`gender` AS `gender`,`ins`.`user_id` AS `user_id`,`usr`.`username` AS `username` from (`instructors` `ins` join `users` `usr` on(`usr`.`user_id` = `ins`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_instructor_schedule`
--
DROP TABLE IF EXISTS `view_instructor_schedule`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_instructor_schedule`  AS  select `inss`.`subject_id` AS `subject_id`,`inss`.`instructor_id` AS `instructor_id`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,`sub`.`class_days` AS `class_days`,concat(`sub`.`class_starts`,' - ',`sub`.`class_ends`) AS `clsTime`,`sc`.`section_name` AS `section_name`,`sub`.`units` AS `units`,`inss`.`semester` AS `semester`,`inss`.`school_year` AS `school_year` from (((`subjects` `sub` join `instructor_subjects` `inss` on(`inss`.`subject_id` = `sub`.`subject_id`)) join `instructors` `ins` on(`inss`.`instructor_id` = `ins`.`instructor_id`)) join `sections` `sc` on(`sc`.`section_id` = `sub`.`section_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_instructor_subjects`
--
DROP TABLE IF EXISTS `view_instructor_subjects`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_instructor_subjects`  AS  select `inss`.`instructor_id` AS `instructor_id`,`inss`.`instructor_subject_id` AS `instructor_subject_id`,`inss`.`subject_id` AS `subject_id`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,concat(`sub`.`class_days`,' | ',`sub`.`class_starts`,' - ',`sub`.`class_ends`) AS `sched`,`sub`.`units` AS `units`,`sc`.`section_name` AS `section_name`,`inss`.`semester` AS `semester`,`inss`.`school_year` AS `school_year` from (((`instructor_subjects` `inss` join `subjects` `sub` on(`sub`.`subject_id` = `inss`.`subject_id`)) join `instructors` `ins` on(`ins`.`instructor_id` = `inss`.`instructor_id`)) join `sections` `sc` on(`sc`.`section_id` = `sub`.`section_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_instructor_subjects_all`
--
DROP TABLE IF EXISTS `view_instructor_subjects_all`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_instructor_subjects_all`  AS  select `instructor_subjects`.`instructor_subject_id` AS `instructor_subject_id`,`instructor_subjects`.`instructor_id` AS `instructor_id`,`instructor_subjects`.`subject_id` AS `subject_id`,`instructor_subjects`.`semester` AS `semester`,`instructor_subjects`.`school_year` AS `school_year` from `instructor_subjects` ;

-- --------------------------------------------------------

--
-- Structure for view `view_school_year`
--
DROP TABLE IF EXISTS `view_school_year`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_school_year`  AS  select `school_year`.`sy_id` AS `sy_id`,`school_year`.`schoolyear` AS `schoolyear`,`school_year`.`semester` AS `semester`,`school_year`.`isCurrent` AS `isCurrent` from `school_year` ;

-- --------------------------------------------------------

--
-- Structure for view `view_sections`
--
DROP TABLE IF EXISTS `view_sections`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_sections`  AS  select `sections`.`section_id` AS `section_id`,`sections`.`section_name` AS `section_name`,`sections`.`program` AS `program`,`sections`.`year` AS `year` from `sections` ;

-- --------------------------------------------------------

--
-- Structure for view `view_semestral_instructors`
--
DROP TABLE IF EXISTS `view_semestral_instructors`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_semestral_instructors`  AS  select distinct `instructor_subjects`.`instructor_id` AS `instructor_id`,`instructor_subjects`.`semester` AS `semester`,`instructor_subjects`.`school_year` AS `school_year` from `instructor_subjects` ;

-- --------------------------------------------------------

--
-- Structure for view `view_semestral_students`
--
DROP TABLE IF EXISTS `view_semestral_students`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_semestral_students`  AS  select distinct `student_subject`.`student_id` AS `student_id`,`student_subject`.`semester` AS `semester`,`student_subject`.`school_year` AS `school_year` from `student_subject` ;

-- --------------------------------------------------------

--
-- Structure for view `view_students`
--
DROP TABLE IF EXISTS `view_students`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_students`  AS  select `st`.`student_id` AS `student_id`,`st`.`student_no` AS `student_no`,`st`.`lastname` AS `lastname`,`st`.`firstname` AS `firstname`,`st`.`middlename` AS `middlename`,`st`.`gender` AS `gender`,`st`.`birthday` AS `birthday`,`st`.`address` AS `address`,`st`.`program` AS `program`,`st`.`year_id` AS `year_id`,`st`.`user_id` AS `user_id`,`yr`.`year` AS `year`,`usr`.`username` AS `username` from ((`students` `st` join `users` `usr` on(`usr`.`user_id` = `st`.`user_id`)) join `year` `yr` on(`yr`.`year_id` = `st`.`year_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_students_by_subjects`
--
DROP TABLE IF EXISTS `view_students_by_subjects`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_students_by_subjects`  AS  select `st`.`student_id` AS `student_id`,`st`.`student_no` AS `student_no`,`st`.`lastname` AS `lastname`,`st`.`firstname` AS `firstname`,`st`.`middlename` AS `middlename`,`st`.`year_id` AS `year_id`,`yr`.`year` AS `year`,`sts`.`semester` AS `semester`,`sts`.`school_year` AS `school_year`,`sub`.`subject_id` AS `subject_id`,`ins`.`instructor_id` AS `instructor_id` from (((((`students` `st` join `year` `yr` on(`st`.`year_id` = `yr`.`year_id`)) join `student_subject` `sts` on(`sts`.`student_id` = `st`.`student_id`)) join `subjects` `sub` on(`sts`.`subject_id` = `sub`.`subject_id`)) join `instructor_subjects` `inss` on(`inss`.`subject_id` = `sub`.`subject_id`)) join `instructors` `ins` on(`ins`.`instructor_id` = `inss`.`instructor_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_student_attendance`
--
DROP TABLE IF EXISTS `view_student_attendance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_attendance`  AS  select `st`.`student_id` AS `student_id`,concat(`st`.`lastname`,', ',`st`.`firstname`,' ',`st`.`middlename`) AS `fulLName`,`sub`.`subject_id` AS `subject_id`,concat(`sub`.`subject_code`,': ',`sub`.`subject_name`) AS `subjectName`,`ins`.`instructor_id` AS `instructor_id`,`att`.`description` AS `description`,`att`.`day` AS `day`,`att`.`school_year` AS `school_year`,`att`.`semester` AS `semester` from (((`attendance` `att` join `students` `st` on(`st`.`student_id` = `att`.`student_id`)) join `subjects` `sub` on(`sub`.`subject_id` = `att`.`subject_id`)) join `instructors` `ins` on(`ins`.`instructor_id` = `att`.`instructor_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_student_grades`
--
DROP TABLE IF EXISTS `view_student_grades`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_grades`  AS  select `st`.`student_id` AS `student_id`,concat(`st`.`lastname`,', ',`st`.`firstname`) AS `student_name`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,`sc`.`section_name` AS `section_name`,`gr`.`grade` AS `grade`,`gr`.`term` AS `term`,`gr`.`semester` AS `semester`,`gr`.`school_year` AS `school_year`,`st`.`year_id` AS `year_id`,`yr`.`year` AS `year` from ((((`students` `st` join `grades` `gr` on(`st`.`student_id` = `gr`.`student_id`)) join `subjects` `sub` on(`gr`.`subject_id` = `sub`.`subject_id`)) join `sections` `sc` on(`sc`.`section_id` = `sub`.`section_id`)) join `year` `yr` on(`gr`.`year_id` = `yr`.`year_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_student_schedule`
--
DROP TABLE IF EXISTS `view_student_schedule`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_schedule`  AS  select `sts`.`subject_id` AS `subject_id`,`sts`.`student_id` AS `student_id`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,`sub`.`class_days` AS `class_days`,concat(`sub`.`class_starts`,' - ',`sub`.`class_ends`) AS `clsTime`,`sc`.`section_name` AS `section_name`,`sub`.`units` AS `units`,`sts`.`semester` AS `semester`,`sts`.`school_year` AS `school_year`,`sts`.`year_id` AS `year_id` from (((`student_subject` `sts` join `subjects` `sub` on(`sub`.`subject_id` = `sts`.`subject_id`)) join `students` `st` on(`st`.`student_id` = `sts`.`student_id`)) join `sections` `sc` on(`sc`.`section_id` = `sub`.`section_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_student_subject`
--
DROP TABLE IF EXISTS `view_student_subject`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_subject`  AS  select `student_subject`.`student_subject_id` AS `student_subject_id`,`student_subject`.`subject_id` AS `subject_id`,`student_subject`.`student_id` AS `student_id`,`student_subject`.`semester` AS `semester`,`student_subject`.`school_year` AS `school_year` from `student_subject` ;

-- --------------------------------------------------------

--
-- Structure for view `view_student_year`
--
DROP TABLE IF EXISTS `view_student_year`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_year`  AS  select `sty`.`student_year_id` AS `student_year_id`,`sty`.`student_id` AS `student_id`,`sty`.`year_id` AS `year_id`,`yr`.`year` AS `year`,`sty`.`semester` AS `semester`,`sty`.`school_year` AS `school_year` from ((`student_year` `sty` join `students` `st` on(`st`.`student_id` = `sty`.`student_id`)) join `year` `yr` on(`yr`.`year_id` = `sty`.`year_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_subjects`
--
DROP TABLE IF EXISTS `view_subjects`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_subjects`  AS  select `sub`.`subject_id` AS `subject_id`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,`sub`.`class_days` AS `class_days`,`sub`.`class_starts` AS `class_starts`,`sub`.`class_ends` AS `class_ends`,`sub`.`section_id` AS `section_id`,`sub`.`units` AS `units`,`sub`.`program` AS `program`,`sub`.`semester` AS `semester`,`sub`.`school_year` AS `school_year`,`sc`.`section_name` AS `section_name` from (`subjects` `sub` join `sections` `sc` on(`sc`.`section_id` = `sub`.`section_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_subjects_by_student`
--
DROP TABLE IF EXISTS `view_subjects_by_student`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_subjects_by_student`  AS  select `sts`.`student_subject_id` AS `student_subject_id`,`sub`.`subject_code` AS `subject_code`,`sub`.`subject_name` AS `subject_name`,`sub`.`class_days` AS `class_days`,concat(`sub`.`class_starts`,' - ',`sub`.`class_ends`) AS `sched`,`sub`.`units` AS `units`,`sc`.`section_id` AS `section_id`,`sc`.`section_name` AS `section_name`,concat(`st`.`lastname`,', ',`st`.`firstname`) AS `student_name`,`st`.`student_id` AS `student_id`,`sts`.`year_id` AS `year_id`,`sts`.`semester` AS `semester`,`sts`.`school_year` AS `school_year` from (((`student_subject` `sts` join `students` `st` on(`st`.`student_id` = `sts`.`student_id`)) join `subjects` `sub` on(`sts`.`subject_id` = `sub`.`subject_id`)) join `sections` `sc` on(`sc`.`section_id` = `sub`.`section_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_total_students_by_subject`
--
DROP TABLE IF EXISTS `view_total_students_by_subject`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_total_students_by_subject`  AS  select `st`.`student_id` AS `student_id`,`sc`.`section_id` AS `section_id`,`sub`.`subject_id` AS `subject_id`,`ins`.`instructor_id` AS `instructor_id`,`sts`.`school_year` AS `school_year` from ((((((`students` `st` join `student_subject` `sts` on(`sts`.`student_id` = `st`.`student_id`)) join `subjects` `sub` on(`sts`.`subject_id` = `sub`.`subject_id`)) join `student_section` `stc` on(`stc`.`student_id` = `st`.`student_id`)) join `sections` `sc` on(`sc`.`section_id` = `stc`.`section_id`)) join `instructor_subjects` `inss` on(`inss`.`subject_id` = `sub`.`subject_id`)) join `instructors` `ins` on(`ins`.`instructor_id` = `inss`.`instructor_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`instructor_id`);

--
-- Indexes for table `instructor_subjects`
--
ALTER TABLE `instructor_subjects`
  ADD PRIMARY KEY (`instructor_subject_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`);

--
-- Indexes for table `recitations`
--
ALTER TABLE `recitations`
  ADD PRIMARY KEY (`recitation_id`);

--
-- Indexes for table `school_year`
--
ALTER TABLE `school_year`
  ADD PRIMARY KEY (`sy_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`section_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_no` (`student_no`);

--
-- Indexes for table `student_behavior`
--
ALTER TABLE `student_behavior`
  ADD PRIMARY KEY (`behavior_id`);

--
-- Indexes for table `student_section`
--
ALTER TABLE `student_section`
  ADD PRIMARY KEY (`student_section_id`);

--
-- Indexes for table `student_subject`
--
ALTER TABLE `student_subject`
  ADD PRIMARY KEY (`student_subject_id`);

--
-- Indexes for table `student_year`
--
ALTER TABLE `student_year`
  ADD PRIMARY KEY (`student_year_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `users_session`
--
ALTER TABLE `users_session`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `year`
--
ALTER TABLE `year`
  ADD PRIMARY KEY (`year_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `instructor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `instructor_subjects`
--
ALTER TABLE `instructor_subjects`
  MODIFY `instructor_subject_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recitations`
--
ALTER TABLE `recitations`
  MODIFY `recitation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `school_year`
--
ALTER TABLE `school_year`
  MODIFY `sy_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `student_behavior`
--
ALTER TABLE `student_behavior`
  MODIFY `behavior_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_section`
--
ALTER TABLE `student_section`
  MODIFY `student_section_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_subject`
--
ALTER TABLE `student_subject`
  MODIFY `student_subject_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_year`
--
ALTER TABLE `student_year`
  MODIFY `student_year_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users_session`
--
ALTER TABLE `users_session`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `year`
--
ALTER TABLE `year`
  MODIFY `year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
