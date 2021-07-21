-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Host: db.fantasy-sim.com
-- Generation Time: Jul 21, 2021 at 09:16 PM
-- Server version: 10.2.21-MariaDB-log
-- PHP Version: 7.3.9-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `md233414db449505`
--
CREATE DATABASE IF NOT EXISTS `md233414db449505` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `md233414db449505`;

-- --------------------------------------------------------

--
-- Table structure for table `ban`
--

CREATE TABLE `ban` (
  `id` int(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  `user` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `banmod` text DEFAULT NULL,
  `game` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

CREATE TABLE `characters` (
  `id` int(11) NOT NULL,
  `alive` int(11) NOT NULL DEFAULT 1,
  `lastonline` datetime DEFAULT NULL,
  `familyid` int(11) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `type` text DEFAULT NULL,
  `age` int(11) NOT NULL DEFAULT 0,
  `sex` text DEFAULT NULL,
  `race` text DEFAULT NULL,
  `user` text DEFAULT NULL,
  `liege` int(11) DEFAULT NULL,
  `father` int(11) DEFAULT NULL,
  `mother` int(11) DEFAULT NULL,
  `married` int(11) DEFAULT 0,
  `matrilineal` int(11) DEFAULT 0,
  `maintitle` int(11) DEFAULT NULL,
  `fertile` int(11) NOT NULL DEFAULT 0,
  `birthplace` text DEFAULT NULL,
  `wayoflifeskill` int(11) NOT NULL DEFAULT 0,
  `wayoflifeaction` int(11) NOT NULL DEFAULT 0,
  `location` text DEFAULT NULL,
  `location2` text DEFAULT NULL,
  `nationality` text DEFAULT NULL,
  `imprisoned` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `claim`
--

CREATE TABLE `claim` (
  `id` int(11) NOT NULL,
  `type` text DEFAULT NULL,
  `inheritable` int(11) NOT NULL DEFAULT 0,
  `charowner` int(11) DEFAULT NULL,
  `title` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `countryowner` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `owner` text NOT NULL,
  `countryco` text NOT NULL,
  `region` text NOT NULL,
  `type` text NOT NULL,
  `companyname` text NOT NULL,
  `position1` text NOT NULL,
  `position2` text NOT NULL,
  `position3` text NOT NULL,
  `position4` text NOT NULL,
  `position5` text NOT NULL,
  `joboffer` double DEFAULT NULL,
  `rooms` int(11) NOT NULL DEFAULT 0,
  `price` double DEFAULT NULL,
  `crop` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `congress`
--

CREATE TABLE `congress` (
  `id` int(11) NOT NULL,
  `type` text DEFAULT NULL,
  `country` text DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `user` text DEFAULT NULL,
  `votedfor` int(11) DEFAULT NULL,
  `extratext` text DEFAULT NULL,
  `extraint` double DEFAULT NULL,
  `votesfor` int(11) NOT NULL DEFAULT 0,
  `votesagainst` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `countryinfo`
--

CREATE TABLE `countryinfo` (
  `id` int(11) NOT NULL,
  `country` varchar(50) NOT NULL,
  `countrypresident` varchar(50) NOT NULL,
  `characterowner` int(11) DEFAULT NULL,
  `currency` text NOT NULL,
  `gold` double NOT NULL DEFAULT 0,
  `money` double NOT NULL DEFAULT 0,
  `treasurygold` double NOT NULL DEFAULT 0,
  `treasurymoney` double NOT NULL DEFAULT 0,
  `vat` int(11) NOT NULL DEFAULT 5,
  `worktax` int(11) NOT NULL DEFAULT 5,
  `immigrationtax` double NOT NULL DEFAULT 0,
  `nodecisions` int(11) NOT NULL DEFAULT 0,
  `moneycreation` double NOT NULL DEFAULT 0,
  `government` int(11) NOT NULL DEFAULT 1,
  `changedgov` int(11) NOT NULL DEFAULT 0,
  `statereligion` text NOT NULL,
  `changedrel` int(11) NOT NULL DEFAULT 0,
  `finance` text NOT NULL,
  `foreignaffairs` text NOT NULL,
  `immigration` text NOT NULL,
  `hospital` int(11) NOT NULL DEFAULT 0,
  `citizens` int(11) NOT NULL DEFAULT 0,
  `totalcongressel` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `crops`
--

CREATE TABLE `crops` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `temperatureneed` text DEFAULT NULL,
  `waterneed` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE `currency` (
  `id` int(11) NOT NULL,
  `usercur` varchar(50) NOT NULL,
  `gold` double NOT NULL DEFAULT 0,
  `doge` double NOT NULL DEFAULT 100,
  `enmus` double NOT NULL DEFAULT 100,
  `gez` double NOT NULL DEFAULT 100,
  `hebrea` double NOT NULL DEFAULT 100,
  `geadh` double NOT NULL DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `currencymarket`
--

CREATE TABLE `currencymarket` (
  `id` int(11) NOT NULL,
  `offerid` text NOT NULL,
  `sellcur` text NOT NULL,
  `sellamount` double NOT NULL,
  `forcur` text NOT NULL,
  `foramount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `diplomacy`
--

CREATE TABLE `diplomacy` (
  `id` int(11) NOT NULL,
  `type` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `country1` text DEFAULT NULL,
  `country2` text DEFAULT NULL,
  `acceptnap` int(11) DEFAULT NULL,
  `peace` int(11) DEFAULT NULL,
  `attackcountry1` text DEFAULT NULL,
  `attackcountry1start` datetime NOT NULL DEFAULT '2099-01-01 00:00:00',
  `country11damage` int(11) DEFAULT NULL,
  `country12damage` int(11) DEFAULT NULL,
  `paygold1` double NOT NULL DEFAULT 0,
  `goldperdamage1` double NOT NULL DEFAULT 0,
  `hospital1` int(11) NOT NULL DEFAULT 0,
  `attackcountry2` text DEFAULT NULL,
  `attackcountry2start` datetime NOT NULL DEFAULT '2099-01-01 00:00:00',
  `country21damage` int(11) DEFAULT NULL,
  `country22damage` int(11) DEFAULT NULL,
  `paygold2` double NOT NULL DEFAULT 0,
  `goldperdamage2` double NOT NULL DEFAULT 0,
  `hospital2` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `elections`
--

CREATE TABLE `elections` (
  `id` int(11) NOT NULL,
  `type` text DEFAULT NULL,
  `candidate` text DEFAULT NULL,
  `countryel` text DEFAULT NULL,
  `votes` int(11) NOT NULL DEFAULT 0,
  `message` tinytext DEFAULT NULL,
  `party` int(11) DEFAULT NULL,
  `electorder` int(11) NOT NULL DEFAULT 99
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `family`
--

CREATE TABLE `family` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `heritagelaw` int(11) NOT NULL DEFAULT 0,
  `dynast` int(11) DEFAULT NULL,
  `heir` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `immigration`
--

CREATE TABLE `immigration` (
  `id` int(11) NOT NULL,
  `immigrant` text DEFAULT NULL,
  `tocountry` text DEFAULT NULL,
  `message` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `userinv` text NOT NULL,
  `rawfood` int(11) NOT NULL DEFAULT 0,
  `rawweapon` int(11) NOT NULL DEFAULT 0,
  `rawhouse` int(11) NOT NULL DEFAULT 0,
  `paper` int(11) NOT NULL DEFAULT 0,
  `weaponq1` int(11) NOT NULL DEFAULT 0,
  `weaponq2` int(11) NOT NULL DEFAULT 0,
  `weaponq3` int(11) NOT NULL DEFAULT 0,
  `weaponq4` int(11) NOT NULL DEFAULT 0,
  `weaponq5` int(11) NOT NULL DEFAULT 0,
  `foodq1` int(11) NOT NULL DEFAULT 0,
  `foodq2` int(11) NOT NULL DEFAULT 0,
  `foodq3` int(11) NOT NULL DEFAULT 0,
  `foodq4` int(11) NOT NULL DEFAULT 0,
  `foodq5` int(11) NOT NULL DEFAULT 0,
  `house` int(11) NOT NULL DEFAULT 1,
  `book` int(11) NOT NULL DEFAULT 0,
  `rawhospital` int(11) NOT NULL DEFAULT 0,
  `hospital` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `marketplace`
--

CREATE TABLE `marketplace` (
  `id` int(11) NOT NULL,
  `owner` text DEFAULT NULL,
  `country` text DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `type` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `marriageproposal`
--

CREATE TABLE `marriageproposal` (
  `id` int(11) NOT NULL,
  `candidate1` int(11) DEFAULT NULL,
  `candidate1liege` int(11) DEFAULT NULL,
  `candidate1accept` int(11) DEFAULT 0,
  `candidate2` int(11) DEFAULT NULL,
  `candidate2liege` int(11) DEFAULT NULL,
  `candidate2accept` int(11) DEFAULT 0,
  `matrilineal` int(11) NOT NULL DEFAULT 0,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `militaryunit`
--

CREATE TABLE `militaryunit` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `owner` text DEFAULT NULL,
  `country` text DEFAULT NULL,
  `gold` double NOT NULL DEFAULT 0,
  `percentowner` int(11) NOT NULL DEFAULT 25,
  `percentunit` int(11) NOT NULL DEFAULT 50,
  `percentuser` int(11) NOT NULL DEFAULT 25,
  `message` text DEFAULT NULL,
  `camp` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `newspaper`
--

CREATE TABLE `newspaper` (
  `id` int(11) NOT NULL,
  `owner` text DEFAULT NULL,
  `name` text DEFAULT NULL,
  `region` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `politicalparty`
--

CREATE TABLE `politicalparty` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `owner` text DEFAULT NULL,
  `partypresident` text DEFAULT NULL,
  `structure` int(11) NOT NULL DEFAULT 0,
  `country` text DEFAULT NULL,
  `gold` double NOT NULL DEFAULT 0,
  `message` tinytext DEFAULT NULL,
  `runcongress` int(11) NOT NULL DEFAULT 0,
  `congressvotes` int(11) NOT NULL DEFAULT 0,
  `partypresidentel` int(11) NOT NULL DEFAULT 1,
  `ad` int(11) NOT NULL DEFAULT 0,
  `adtext` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `region`
--

CREATE TABLE `region` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `curowner` text NOT NULL,
  `originalowner` text NOT NULL,
  `characterowner` int(11) DEFAULT NULL,
  `1` int(11) NOT NULL DEFAULT 0,
  `2` int(11) NOT NULL DEFAULT 0,
  `3` int(11) NOT NULL DEFAULT 0,
  `biggestrel` text NOT NULL,
  `resource` text NOT NULL,
  `epidemic` int(11) NOT NULL DEFAULT 0,
  `epidemiccured` int(11) NOT NULL DEFAULT 0,
  `epidemicup` int(11) NOT NULL DEFAULT 0,
  `taxtoday` double NOT NULL DEFAULT 0,
  `taxminoneday` double NOT NULL DEFAULT 0,
  `climate` text DEFAULT NULL,
  `currtemp` double DEFAULT NULL,
  `currweather` text DEFAULT NULL,
  `weatherstreak` int(11) DEFAULT 0,
  `weatherevent` int(11) DEFAULT NULL,
  `archprelate` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `relics`
--

CREATE TABLE `relics` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `owner` text NOT NULL,
  `location` text NOT NULL,
  `spreadpower` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `religion`
--

CREATE TABLE `religion` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `type` text DEFAULT NULL,
  `religionid` int(11) DEFAULT NULL,
  `leader` text DEFAULT NULL,
  `nominee` text DEFAULT NULL,
  `owner` text DEFAULT NULL,
  `deathdate` datetime NOT NULL DEFAULT '2099-01-01 00:00:00',
  `gold` double NOT NULL DEFAULT 0,
  `donatedgold` double NOT NULL DEFAULT 0,
  `religiontax` int(11) NOT NULL DEFAULT 0,
  `changedtax` int(11) NOT NULL DEFAULT 0,
  `message` tinytext DEFAULT NULL,
  `crusade` text DEFAULT NULL,
  `crusadeup` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shop`
--

CREATE TABLE `shop` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `trial` datetime NOT NULL,
  `game` int(11) NOT NULL DEFAULT 0,
  `userinfo` int(11) NOT NULL DEFAULT 0,
  `avatar` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `titles`
--

CREATE TABLE `titles` (
  `id` int(11) NOT NULL,
  `holdingtype` text DEFAULT NULL,
  `holdingid` int(11) DEFAULT NULL,
  `holderid` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `traits`
--

CREATE TABLE `traits` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `type` text DEFAULT NULL,
  `amount` int(11) NOT NULL DEFAULT 0,
  `removechance` int(11) NOT NULL DEFAULT 0,
  `birthchance` int(11) NOT NULL DEFAULT 0,
  `inheritable` int(11) NOT NULL DEFAULT 0,
  `antitrait` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `traitscharacters`
--

CREATE TABLE `traitscharacters` (
  `id` int(11) NOT NULL,
  `characterid` int(11) DEFAULT NULL,
  `traitid` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `extrainfo` text DEFAULT NULL,
  `extrainfo2` text DEFAULT NULL,
  `invissible` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `rememberme` text DEFAULT NULL,
  `accountcreated` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `inactive` int(11) NOT NULL DEFAULT 0,
  `moderator` int(11) NOT NULL DEFAULT 0,
  `nationality` varchar(50) NOT NULL,
  `race` text NOT NULL,
  `location` text NOT NULL,
  `location2` text NOT NULL,
  `about` text DEFAULT NULL,
  `strength` int(11) DEFAULT 0,
  `trained` int(11) NOT NULL DEFAULT 0,
  `trainbonus` int(11) NOT NULL DEFAULT 0,
  `energy` int(11) NOT NULL DEFAULT 100,
  `state` text DEFAULT NULL,
  `sleephours` int(11) DEFAULT NULL,
  `statetime` datetime DEFAULT NULL,
  `voted` int(11) NOT NULL DEFAULT 0,
  `salary` double DEFAULT NULL,
  `workid` int(11) DEFAULT NULL,
  `workstart` datetime NOT NULL DEFAULT '2099-01-01 00:00:00',
  `worked` int(11) NOT NULL DEFAULT 0,
  `workedlastday` int(11) NOT NULL DEFAULT 0,
  `dominance` int(11) DEFAULT 0,
  `dueled` int(11) NOT NULL DEFAULT 0,
  `lottery` int(11) NOT NULL DEFAULT 0,
  `age` int(11) NOT NULL DEFAULT 0,
  `ageup` int(11) DEFAULT 0,
  `militaryunit` int(11) DEFAULT 0,
  `militaryunitrank` int(11) NOT NULL DEFAULT 0,
  `politicalparty` int(11) NOT NULL DEFAULT 0,
  `userreligion` text DEFAULT NULL,
  `religionorder` int(11) DEFAULT NULL,
  `orderrank` int(11) NOT NULL DEFAULT 0,
  `removedreligion` int(11) DEFAULT 0,
  `spreadbonus` int(11) NOT NULL DEFAULT 0,
  `spread` int(11) NOT NULL DEFAULT 0,
  `expedition` int(11) NOT NULL DEFAULT 0,
  `lastonline` datetime NOT NULL DEFAULT '2099-01-01 00:00:00',
  `housepos` text DEFAULT NULL,
  `housebuilt` int(11) NOT NULL DEFAULT 0,
  `tavern` int(11) NOT NULL DEFAULT 0,
  `tavernup` int(11) NOT NULL DEFAULT 1,
  `locationup` int(11) NOT NULL DEFAULT 0,
  `totalspread` int(11) NOT NULL DEFAULT 0,
  `totaldamage` int(11) NOT NULL DEFAULT 0,
  `congressmember` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ban`
--
ALTER TABLE `ban`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `characters`
--
ALTER TABLE `characters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `claim`
--
ALTER TABLE `claim`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `congress`
--
ALTER TABLE `congress`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countryinfo`
--
ALTER TABLE `countryinfo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crops`
--
ALTER TABLE `crops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currencymarket`
--
ALTER TABLE `currencymarket`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `diplomacy`
--
ALTER TABLE `diplomacy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `elections`
--
ALTER TABLE `elections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `family`
--
ALTER TABLE `family`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `immigration`
--
ALTER TABLE `immigration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marketplace`
--
ALTER TABLE `marketplace`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marriageproposal`
--
ALTER TABLE `marriageproposal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `militaryunit`
--
ALTER TABLE `militaryunit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newspaper`
--
ALTER TABLE `newspaper`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `politicalparty`
--
ALTER TABLE `politicalparty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `region`
--
ALTER TABLE `region`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `relics`
--
ALTER TABLE `relics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `religion`
--
ALTER TABLE `religion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop`
--
ALTER TABLE `shop`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `titles`
--
ALTER TABLE `titles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `traits`
--
ALTER TABLE `traits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `traitscharacters`
--
ALTER TABLE `traitscharacters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ban`
--
ALTER TABLE `ban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `characters`
--
ALTER TABLE `characters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `claim`
--
ALTER TABLE `claim`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `congress`
--
ALTER TABLE `congress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `countryinfo`
--
ALTER TABLE `countryinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `crops`
--
ALTER TABLE `crops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `currencymarket`
--
ALTER TABLE `currencymarket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `diplomacy`
--
ALTER TABLE `diplomacy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `elections`
--
ALTER TABLE `elections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `family`
--
ALTER TABLE `family`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `immigration`
--
ALTER TABLE `immigration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `marketplace`
--
ALTER TABLE `marketplace`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `marriageproposal`
--
ALTER TABLE `marriageproposal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `militaryunit`
--
ALTER TABLE `militaryunit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `newspaper`
--
ALTER TABLE `newspaper`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `politicalparty`
--
ALTER TABLE `politicalparty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `region`
--
ALTER TABLE `region`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `relics`
--
ALTER TABLE `relics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `religion`
--
ALTER TABLE `religion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `shop`
--
ALTER TABLE `shop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `titles`
--
ALTER TABLE `titles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `traits`
--
ALTER TABLE `traits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `traitscharacters`
--
ALTER TABLE `traitscharacters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
