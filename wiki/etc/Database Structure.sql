-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2015 at 08:03 AM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wiki`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `%PREFIX%category` (
`category_id` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `checksum` char(32) CHARACTER SET ascii NOT NULL,
  `name` char(140) CHARACTER SET ascii NOT NULL,
  `title` varchar(560) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categorypage`
--

CREATE TABLE IF NOT EXISTS `%PREFIX%categorypage` (
`categorypage_id` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `checksum` char(32) CHARACTER SET ascii NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `alias` varchar(560) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `%PREFIX%group`;
CREATE TABLE `%PREFIX%group` (
`group_id` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `checksum` char(32) CHARACTER SET ascii NOT NULL,
  `name` varchar(20) CHARACTER SET ascii NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groupmember`
--

DROP TABLE IF EXISTS `%PREFIX%groupmember`;
CREATE TABLE `%PREFIX%groupmember` (
`groupmember_id` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `checksum` char(32) CHARACTER SET ascii NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `%PREFIX%log`;
CREATE TABLE `%PREFIX%log` (
`log_id` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `checksum` char(32) CHARACTER SET ascii NOT NULL,
  `object_table` varchar(20) CHARACTER SET ascii NOT NULL,
  `object_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `type` enum('CREATE','MODIFY','DELETE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

DROP TABLE IF EXISTS `%PREFIX%page`;
CREATE TABLE `%PREFIX%page` (
`page_id` int(10) unsigned NOT NULL,
  `status` tinyint(4) unsigned NOT NULL,
  `checksum` char(32) CHARACTER SET ascii NOT NULL,
  `name` varchar(140) CHARACTER SET ascii NOT NULL,
  `title` varchar(560) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_owner_id` int(10) unsigned NOT NULL,
  `group_owner_id` int(10) unsigned NOT NULL,
  `visibility` enum('PUBLIC','PROTECTED','PRIVATE','GROUPPRIVATE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PUBLIC',
  `manipulation` enum('EVERYONE','REGISTERED','OWNER','GROUP') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'REGISTERED'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `%PREFIX%pagelink` (
`pagelink_id` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `checksum` char(32) CHARACTER SET ascii NOT NULL,
  `page_from_id` int(10) unsigned NOT NULL,
  `page_to_id` int(10) unsigned NOT NULL,
  `text` varchar(560) COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `%PREFIX%user`;
CREATE TABLE `%PREFIX%user` (
`user_id` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `checksum` char(32) CHARACTER SET ascii NOT NULL,
  `loginname` char(20) CHARACTER SET ascii NOT NULL,
  `password` char(255) CHARACTER SET ascii NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userpermission`
--

DROP TABLE IF EXISTS `%PREFIX%userpermission`;
CREATE TABLE `%PREFIX%userpermission` (
`userpermission_id` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `checksum` char(32) CHARACTER SET ascii NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `permission` varchar(50) CHARACTER SET ascii NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `version`
--

DROP TABLE IF EXISTS `%PREFIX%version`;
CREATE TABLE `%PREFIX%version` (
`version_id` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `checksum` char(32) CHARACTER SET ascii NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `title` varchar(560) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `minor_edit` tinyint(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `%PREFIX%category`
 ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `categorypage`
--
ALTER TABLE `%PREFIX%categorypage`
 ADD PRIMARY KEY (`categorypage_id`);

--
-- Indexes for table `group`
--
ALTER TABLE `%PREFIX%group`
 ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `groupmember`
--
ALTER TABLE `%PREFIX%groupmember`
 ADD PRIMARY KEY (`groupmember_id`);

--
-- Indexes for table `log`
--
ALTER TABLE `%PREFIX%log`
 ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `page`
--
ALTER TABLE `%PREFIX%page`
 ADD PRIMARY KEY (`page_id`), ADD KEY `name` (`name`);
 
 --
-- Indexes for table `pagelink`
--
ALTER TABLE `%PREFIX%pagelink`
 ADD PRIMARY KEY (`pagelink_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `%PREFIX%user`
 ADD PRIMARY KEY (`user_id`), ADD KEY `loginname_lookup` (`status`,`loginname`);

--
-- Indexes for table `userpermission`
--
ALTER TABLE `%PREFIX%userpermission`
 ADD PRIMARY KEY (`userpermission_id`);

--
-- Indexes for table `version`
--
ALTER TABLE `%PREFIX%version`
 ADD PRIMARY KEY (`version_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `%PREFIX%category`
MODIFY `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `categorypage`
--
ALTER TABLE `%PREFIX%categorypage`
MODIFY `categorypage_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `group`
--
ALTER TABLE `%PREFIX%group`
MODIFY `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `groupmember`
--
ALTER TABLE `%PREFIX%groupmember`
MODIFY `groupmember_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `%PREFIX%log`
MODIFY `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `page`
--
ALTER TABLE `%PREFIX%page`
MODIFY `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `pagelink`
--
ALTER TABLE `%PREFIX%pagelink`
MODIFY `pagelink_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `%PREFIX%user`
MODIFY `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `userpermission`
--
ALTER TABLE `%PREFIX%userpermission`
MODIFY `userpermission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `version`
--
ALTER TABLE `%PREFIX%version`
MODIFY `version_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
