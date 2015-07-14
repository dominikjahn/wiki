-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2015 at 03:03 PM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wiki`
--

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`group_id`, `status`, `name`) VALUES
(1, 100, 'public');

--
-- Dumping data for table `groupmember`
--

INSERT INTO `groupmember` (`groupmember_id`, `status`, `group_id`, `user_id`) VALUES
(1, 100, 1, 1);

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`log_id`, `status`, `object_table`, `object_id`, `user_id`, `type`, `timestamp`) VALUES
(1, 100, 'user', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(2, 100, 'page', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(3, 100, 'version', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(4, 100, 'group', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(5, 100, 'groupmember', 1, 1, 'CREATE', '2015-07-14 12:00:00');

--
-- Dumping data for table `page`
--

INSERT INTO `page` (`page_id`, `status`, `name`, `title`, `content`, `user_owner_id`, `group_owner_id`, `visibility`, `manipulation`) VALUES
(1, 100, 'Homepage', 'Homepage', 'Welcome to your new Wiki!To start click on "Edit" in the top right corner and add your own content.', 1, 1, 'PUBLIC', 'EVERYONE');

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `status`, `loginname`, `password`) VALUES
(1, 100, 'admin', '21232f297a57a5a743894a0e4a801fc3');

--
-- Dumping data for table `version`
--

INSERT INTO `version` (`version_id`, `status`, `page_id`, `title`, `content`, `summary`, `minor_edit`) VALUES
(1, 100, 1, 'Homepage', 'Welcome to your new Wiki!\r\n\r\nTo start click on "Edit" in the top right corner and add your own content.', 'Initialized page', b'0');
