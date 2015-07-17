-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2015 at 08:05 AM
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

INSERT INTO `%PREFIX%group` (`group_id`, `status`, `name`) VALUES
(1, 100, 'public'),
(2, 100, 'admin');

--
-- Dumping data for table `groupmember`
--

INSERT INTO `%PREFIX%groupmember` (`groupmember_id`, `status`, `group_id`, `user_id`) VALUES
(1, 100, 1, 1),
(2, 100, 1, 2),
(3, 100, 2, 2);

--
-- Dumping data for table `log`
--

INSERT INTO `%PREFIX%log` (`log_id`, `status`, `object_table`, `object_id`, `user_id`, `type`, `timestamp`) VALUES
(1, 100, 'user', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(2, 100, 'user', 2, 1, 'CREATE', '2015-07-14 12:00:00'),
(3, 100, 'page', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(4, 100, 'version', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(5, 100, 'group', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(6, 100, 'group', 1, 2, 'CREATE', '2015-07-14 12:00:00'),
(7, 100, 'groupmember', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(8, 100, 'userpermission', 1, 1, 'CREATE', '2015-07-14 12:00:00');

--
-- Dumping data for table `page`
--

INSERT INTO `%PREFIX%page` (`page_id`, `status`, `name`, `title`, `content`, `user_owner_id`, `group_owner_id`, `visibility`, `manipulation`) VALUES
(1, 100, 'Homepage', 'Homepage', 'Welcome to your new Wiki!To start click on "Edit" in the top right corner and add your own content.', 1, 1, 'PUBLIC', 'EVERYONE');

--
-- Dumping data for table `user`
--

INSERT INTO `%PREFIX%user` (`user_id`, `status`, `loginname`, `password`) VALUES
(1, 100, 'guest', '638339665e853852005321d244f7a266'),
(2, 100, 'admin', '21232f297a57a5a743894a0e4a801fc3');

--
-- Dumping data for table `userpermission`
--

INSERT INTO `%PREFIX%userpermission` (`userpermission_id`, `status`, `user_id`, `permission`) VALUES
(1, 100, 2, 'SCRIPTING');

--
-- Dumping data for table `version`
--

INSERT INTO `%PREFIX%version` (`version_id`, `status`, `page_id`, `title`, `content`, `summary`, `minor_edit`) VALUES
(1, 100, 1, 'Homepage', 'Welcome to your new Wiki!\r\n\r\nTo start click on "Edit" in the top right corner and add your own content.', 'Initialized page', b'0');
