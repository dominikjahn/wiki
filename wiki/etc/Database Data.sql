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

INSERT INTO `%PREFIX%group` (`group_id`, `status`, `checksum`, `name`) VALUES
(1, 100, '7853d18ca181b6e3cf472792614b61f2', 'public'),
(2, 100, '9188062525100d39e479ba0c4ac52a89', 'admin');

--
-- Dumping data for table `log`
--

INSERT INTO `%PREFIX%log` (`log_id`, `status`, `checksum`, `object_table`, `object_id`, `user_id`, `type`, `timestamp`) VALUES
(1, 100, 'e8f2348d59db52bf04a80b17a87385f6', 'user', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(2, 100, '17b0fd366dd11664d90dade7f48f3e6c', 'user', 2, 1, 'CREATE', '2015-07-14 12:00:00'),
(3, 100, '353565412fe671ffa54eb28d305f44f5', 'page', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(4, 100, '834aa0a223ebee98c4c1a08ab62fb533', 'version', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(5, 100, 'af15a59f9ee4a09e759876fe17bd8ba4', 'group', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(6, 100, '9db8d3ab52fdca9be85a1f19d4aecb24', 'group', 2, 1, 'CREATE', '2015-07-14 12:00:00'),
(7, 100, '03ed805322a9500242ffd0bdcf50f374', 'groupmember', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(8, 100, '2477e3e917b7d1a4bb8afe0dbacc10b6', 'userpermission', 1, 1, 'CREATE', '2015-07-14 12:00:00'),
(9, 100, '99abac74e4020e5529c9faf4aea10500', 'userpermission', 2, 1, 'CREATE', '2015-07-14 12:00:00');

--
-- Dumping data for table `page`
--

INSERT INTO `%PREFIX%page` (`page_id`, `status`, `checksum`, `name`, `title`, `content`, `user_owner_id`, `group_owner_id`, `visibility`, `manipulation`) VALUES
(1, 100, '660d8905a7a29b081bf69a1ebfe9ddf6', 'Homepage', 'Homepage', 'Welcome to your new Wiki!\n\nTo start click on "Edit" in the top right corner and add your own content.', 2, 1, 'PUBLIC', 'EVERYONE');

--
-- Dumping data for table `user`
--

INSERT INTO `%PREFIX%user` (`user_id`, `status`, `checksum`, `loginname`, `password`) VALUES
(1, 100, '090382222d01cd0af7c501e4e7e84d2b', 'guest', '638339665e853852005321d244f7a266'),
(2, 100, '8a7ee52eee59211c62b1f1d1daa4d5ab', 'admin', '21232f297a57a5a743894a0e4a801fc3');

--
-- Dumping data for table `userpermission`
--

INSERT INTO `%PREFIX%userpermission` (`userpermission_id`, `status`, `checksum`, `user_id`, `permission`) VALUES
(1, 100, '6592e633a6f4d941ae29cc17e7937d2b', 1, 'CREATE_USERS'),
(2, 100, '407c18e7f347d9e58eaac3571199092d', 1, 'CREATE_PAGES');

--
-- Dumping data for table `version`
--

INSERT INTO `%PREFIX%version` (`version_id`, `status`, `checksum`, `page_id`, `title`, `content`, `summary`, `minor_edit`) VALUES
(1, 100, '1d879fdacc17532d90e19ea88834be06', 1, 'Homepage', 'Welcome to your new Wiki!\r\n\r\nTo start click on "Edit" in the top right corner and add your own content.', 'Initialized page', '0');
