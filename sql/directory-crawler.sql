-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 25, 2016 at 11:36 AM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `directorycrawlerdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint(20) NOT NULL,
  `directory_id` int(11) NOT NULL,
  `href` varchar(225) NOT NULL,
  `name` varchar(50) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `include` enum('y','n') NOT NULL DEFAULT 'n'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawl`
--

CREATE TABLE IF NOT EXISTS `crawl` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `directory_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawlerlinks`
--

CREATE TABLE IF NOT EXISTS `crawlerlinks` (
  `id` int(11) NOT NULL,
  `directory_id` int(11) NOT NULL,
  `target_link` varchar(225) NOT NULL,
  `href` varchar(225) NOT NULL,
  `crawl_id` int(11) NOT NULL,
  `tag` varchar(100) NOT NULL,
  `text` varchar(100) NOT NULL,
  `outertext` varchar(225) NOT NULL,
  `innertext` varchar(225) NOT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crawl_data`
--

CREATE TABLE IF NOT EXISTS `crawl_data` (
  `id` bigint(20) NOT NULL,
  `crawl_id` int(11) NOT NULL,
  `categories` text NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_phone` varchar(255) NOT NULL DEFAULT '',
  `business_address` varchar(255) NOT NULL DEFAULT '',
  `business_website` varchar(255) NOT NULL DEFAULT '',
  `business_emails` text NOT NULL,
  `record_status` varchar(24) NOT NULL DEFAULT 'incomplete',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `directory`
--

CREATE TABLE IF NOT EXISTS `directory` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `entries`
--

CREATE TABLE IF NOT EXISTS `entries` (
  `id` bigint(20) NOT NULL,
  `directory_id` int(11) NOT NULL,
  `href` varchar(225) NOT NULL,
  `name` varchar(50) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `include` enum('y','n') NOT NULL DEFAULT 'n'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE IF NOT EXISTS `locations` (
  `id` bigint(20) NOT NULL,
  `directory_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `href` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `include` enum('y','n') NOT NULL DEFAULT 'n'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mergequery`
--

CREATE TABLE IF NOT EXISTS `mergequery` (
  `mergecode` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `sqlstatement` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` bigint(20) NOT NULL,
  `href` varchar(255) NOT NULL,
  `expiry` bigint(20) NOT NULL,
  `content` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `id` (`id`),
ADD UNIQUE KEY `href` (`href`);

--
-- Indexes for table `crawl`
--
ALTER TABLE `crawl`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `crawlerlinks`
--
ALTER TABLE `crawlerlinks`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `id` (`id`),
ADD UNIQUE KEY `href` (`href`);

--
-- Indexes for table `crawl_data`
--
ALTER TABLE `crawl_data`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `directory`
--
ALTER TABLE `directory`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `id` (`id`),
ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `entries`
--
ALTER TABLE `entries`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `id` (`id`),
ADD UNIQUE KEY `href` (`href`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `id` (`id`),
ADD UNIQUE KEY `href` (`href`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `crawl`
--
ALTER TABLE `crawl`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `crawlerlinks`
--
ALTER TABLE `crawlerlinks`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `crawl_data`
--
ALTER TABLE `crawl_data`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `directory`
--
ALTER TABLE `directory`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `entries`
--
ALTER TABLE `entries`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
