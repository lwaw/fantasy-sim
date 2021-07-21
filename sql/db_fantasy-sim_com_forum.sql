-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Host: db.fantasy-sim.com
-- Generation Time: Jul 21, 2021 at 09:18 PM
-- Server version: 10.2.21-MariaDB-log
-- PHP Version: 7.3.9-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `md233414db436835`
--
CREATE DATABASE IF NOT EXISTS `md233414db436835` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `md233414db436835`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `protected` int(11) NOT NULL DEFAULT 0,
  `catorder` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `type` text DEFAULT NULL,
  `typeid` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `user` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `report` int(11) NOT NULL DEFAULT 0,
  `reporter` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `content` text NOT NULL,
  `extrainfo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender` text DEFAULT NULL,
  `recipient` text DEFAULT NULL,
  `read` int(11) NOT NULL DEFAULT 0,
  `date` datetime DEFAULT NULL,
  `subject` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `replyto` int(11) NOT NULL DEFAULT 0,
  `inviteid` int(11) DEFAULT NULL,
  `invitetype` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `newsarticle`
--

CREATE TABLE `newsarticle` (
  `id` int(11) NOT NULL,
  `newspaperid` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `country` text DEFAULT NULL,
  `price` int(11) NOT NULL DEFAULT 0,
  `title` text DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `buyers` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `newsextra`
--

CREATE TABLE `newsextra` (
  `id` int(11) NOT NULL,
  `newsarticleid` int(11) DEFAULT NULL,
  `type` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `user` text DEFAULT NULL,
  `content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `topic` int(11) DEFAULT NULL,
  `creator` text DEFAULT NULL,
  `replyto` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  `topic` int(11) DEFAULT NULL,
  `post` int(11) DEFAULT NULL,
  `user` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `page` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `statistics`
--

CREATE TABLE `statistics` (
  `id` int(11) NOT NULL,
  `type` text DEFAULT NULL,
  `datestat` date DEFAULT NULL,
  `waarde` float DEFAULT NULL,
  `name` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `storycategories`
--

CREATE TABLE `storycategories` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `tablename` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `storytopics`
--

CREATE TABLE `storytopics` (
  `id` int(11) NOT NULL,
  `category` int(11) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `gameid` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `version1id` int(11) NOT NULL DEFAULT 0,
  `versiondate` datetime DEFAULT NULL,
  `verified` int(11) DEFAULT 0,
  `creator` text DEFAULT NULL,
  `content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `subject` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `creator` text DEFAULT NULL,
  `archived` int(11) NOT NULL DEFAULT 0,
  `lastreply` datetime DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsarticle`
--
ALTER TABLE `newsarticle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsextra`
--
ALTER TABLE `newsextra`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statistics`
--
ALTER TABLE `statistics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `storycategories`
--
ALTER TABLE `storycategories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `storytopics`
--
ALTER TABLE `storytopics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `newsarticle`
--
ALTER TABLE `newsarticle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `newsextra`
--
ALTER TABLE `newsextra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `statistics`
--
ALTER TABLE `statistics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `storycategories`
--
ALTER TABLE `storycategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `storytopics`
--
ALTER TABLE `storytopics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
