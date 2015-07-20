-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 02, 2015 at 11:41 AM
-- Server version: 5.1.61
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `blastWeb`
--

-- --------------------------------------------------------

--
-- Table structure for table `blasts`
--

CREATE TABLE IF NOT EXISTS `blasts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `command` varchar(45) NOT NULL,
  `dbtype` varchar(45) NOT NULL,
  `resources` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `blast_jobs`
--

CREATE TABLE IF NOT EXISTS `blast_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blastid` int(10) unsigned NOT NULL,
  `dbid` int(10) unsigned NOT NULL,
  `e` float NOT NULL,
  `m` int(10) unsigned NOT NULL DEFAULT '0',
  `FU` varchar(45) DEFAULT 'T',
  `GU` int(10) NOT NULL DEFAULT '-1',
  `EU` int(10) NOT NULL DEFAULT '-1',
  `XU` int(10) unsigned NOT NULL DEFAULT '0',
  `IU` varchar(45) NOT NULL DEFAULT 'F',
  `q` int(11) NOT NULL DEFAULT '-3',
  `r` int(10) unsigned NOT NULL DEFAULT '1',
  `v` int(10) unsigned NOT NULL DEFAULT '500',
  `b` int(10) unsigned NOT NULL DEFAULT '250',
  `f` int(10) unsigned NOT NULL DEFAULT '0',
  `g` varchar(45) NOT NULL DEFAULT 'T',
  `QU` int(10) unsigned NOT NULL DEFAULT '1',
  `DU` int(10) unsigned NOT NULL DEFAULT '1',
  `a` int(10) unsigned NOT NULL DEFAULT '1',
  `JU` varchar(45) NOT NULL DEFAULT 'F',
  `MU` varchar(45) NOT NULL DEFAULT 'BLOSUM62',
  `WU` int(10) unsigned NOT NULL DEFAULT '0',
  `z` float NOT NULL DEFAULT '0',
  `KU` int(10) unsigned NOT NULL DEFAULT '0',
  `YU` float NOT NULL DEFAULT '0',
  `SU` int(10) unsigned NOT NULL DEFAULT '3',
  `l` varchar(45) NOT NULL,
  `UU` varchar(45) NOT NULL DEFAULT 'F',
  `y` float NOT NULL DEFAULT '0',
  `ZU` int(10) unsigned NOT NULL DEFAULT '0',
  `n` varchar(45) NOT NULL DEFAULT 'F',
  `AU` int(10) unsigned NOT NULL DEFAULT '0',
  `w` int(10) unsigned NOT NULL DEFAULT '0',
  `t` int(10) unsigned NOT NULL DEFAULT '0',
  `CU` varchar(45) DEFAULT NULL,
  `paramsenabled` varchar(45) NOT NULL,
  `BU` int(10) NOT NULL,
  `TU` varchar(45) NOT NULL,
  `RU` varchar(45) NOT NULL,
  `LU` varchar(45) NOT NULL,
  `name` varchar(45) NOT NULL,
  `description` varchar(45) NOT NULL,
  `userid` int(11) NOT NULL,
  `queriesadded` int(11) NOT NULL,
  `queriescompleted` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `submitDate` datetime NOT NULL,
  `submitpid` int(11) NOT NULL,
  `concatpidresult` int(11) NOT NULL,
  `concatpidcsv` int(11) NOT NULL,
  `transferpid` int(11) NOT NULL,
  `deletepid` int(10) unsigned NOT NULL,
  `chunksize` int(11) NOT NULL,
  `completeDate` datetime NOT NULL,
  `querieserrors` int(10) unsigned NOT NULL,
  `priority` int(10) unsigned NOT NULL,
  `token` varchar(45) NOT NULL,
  `numchunks` varchar(45) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 416768 kB; InnoDB free: 411648 kB; InnoDB free:' AUTO_INCREMENT=1377 ;

-- --------------------------------------------------------

--
-- Table structure for table `blast_profiles`
--

CREATE TABLE IF NOT EXISTS `blast_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blastid` int(10) unsigned NOT NULL,
  `dbid` int(10) unsigned NOT NULL,
  `e` float NOT NULL,
  `m` int(10) unsigned NOT NULL DEFAULT '0',
  `FU` varchar(45) DEFAULT 'T',
  `GU` int(10) NOT NULL DEFAULT '-1',
  `EU` int(10) NOT NULL DEFAULT '-1',
  `XU` int(10) unsigned NOT NULL DEFAULT '0',
  `IU` varchar(45) NOT NULL DEFAULT 'F',
  `q` int(11) NOT NULL DEFAULT '-3',
  `r` int(10) unsigned NOT NULL DEFAULT '1',
  `v` int(10) unsigned NOT NULL DEFAULT '500',
  `b` int(10) unsigned NOT NULL DEFAULT '250',
  `f` int(10) unsigned NOT NULL DEFAULT '0',
  `g` varchar(45) NOT NULL DEFAULT 'T',
  `QU` int(10) unsigned NOT NULL DEFAULT '1',
  `DU` int(10) unsigned NOT NULL DEFAULT '1',
  `a` int(10) unsigned NOT NULL DEFAULT '1',
  `JU` varchar(45) NOT NULL DEFAULT 'F',
  `MU` varchar(45) NOT NULL DEFAULT 'BLOSUM62',
  `WU` int(10) unsigned NOT NULL DEFAULT '0',
  `z` float NOT NULL DEFAULT '0',
  `KU` int(10) unsigned NOT NULL DEFAULT '0',
  `YU` float NOT NULL DEFAULT '0',
  `SU` int(10) unsigned NOT NULL DEFAULT '3',
  `l` varchar(45) NOT NULL,
  `UU` varchar(45) NOT NULL DEFAULT 'F',
  `y` float NOT NULL DEFAULT '0',
  `ZU` int(10) unsigned NOT NULL DEFAULT '0',
  `n` varchar(45) NOT NULL DEFAULT 'F',
  `AU` int(10) unsigned NOT NULL DEFAULT '0',
  `w` int(10) unsigned NOT NULL DEFAULT '0',
  `t` int(10) unsigned NOT NULL DEFAULT '0',
  `CU` varchar(45) DEFAULT NULL,
  `paramsenabled` text NOT NULL,
  `BU` int(10) NOT NULL,
  `TU` varchar(45) NOT NULL,
  `RU` varchar(45) NOT NULL,
  `LU` varchar(45) NOT NULL,
  `name` varchar(45) NOT NULL,
  `userid` int(11) NOT NULL,
  `chunksize` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 416768 kB; InnoDB free: 411648 kB; InnoDB free:' AUTO_INCREMENT=166 ;

-- --------------------------------------------------------

--
-- Table structure for table `blast_queries`
--

CREATE TABLE IF NOT EXISTS `blast_queries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jobid` smallint(5) unsigned NOT NULL,
  `statusid` tinyint(3) unsigned NOT NULL,
  `reservenode` smallint(2) unsigned NOT NULL,
  `reservepid` smallint(5) unsigned NOT NULL,
  `starttime` datetime NOT NULL,
  `endtime` datetime NOT NULL,
  `chunksize` smallint(4) unsigned NOT NULL,
  `priority` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `statusid` (`statusid`,`reservenode`,`reservepid`),
  KEY `statusid_2` (`statusid`,`reservenode`,`reservepid`),
  KEY `statusid_3` (`statusid`,`jobid`),
  KEY `statusid_4` (`statusid`,`jobid`,`reservenode`),
  KEY `statusid_5` (`statusid`,`jobid`,`reservenode`),
  KEY `priority_index` (`priority`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2570885 ;

-- --------------------------------------------------------

--
-- Table structure for table `commands`
--

CREATE TABLE IF NOT EXISTS `commands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `command` varchar(45) NOT NULL,
  `description` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slavebuffer` int(11) NOT NULL,
  `fromemail` text NOT NULL,
  `transfercompletemsg` text NOT NULL,
  `jobcompletemsg` text NOT NULL,
  `transfercompletesubj` text NOT NULL,
  `jobcompletesubj` text NOT NULL,
  `maintaindbpid` int(11) NOT NULL,
  `url` varchar(45) NOT NULL,
  `chunksize` int(11) NOT NULL,
  `perlfilespath` text NOT NULL,
  `userdbspath` text NOT NULL,
  `maxjobage` int(10) unsigned NOT NULL,
  `dbvolumesize` int(10) unsigned NOT NULL,
  `taxonomypath` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Table structure for table `dbs`
--

CREATE TABLE IF NOT EXISTS `dbs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dbname` varchar(45) NOT NULL,
  `dbpath` varchar(45) NOT NULL,
  `type` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `sequencenumber` int(10) unsigned NOT NULL,
  `nucleotidesnumber` int(10) unsigned NOT NULL,
  `sequences` int(11) NOT NULL,
  `active` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `last_update` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=410 ;

-- --------------------------------------------------------

--
-- Table structure for table `dbupdates`
--

CREATE TABLE IF NOT EXISTS `dbupdates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `updatestatus` int(11) NOT NULL,
  `updatedate` datetime NOT NULL,
  `discription` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=539 ;

-- --------------------------------------------------------

--
-- Table structure for table `nodes`
--

CREATE TABLE IF NOT EXISTS `nodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` int(10) unsigned NOT NULL,
  `lastactivity` datetime NOT NULL,
  `submitted` tinyint(1) NOT NULL,
  `commandid` int(10) unsigned NOT NULL,
  `processes` int(10) unsigned NOT NULL,
  `hostname` varchar(45) NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=91 ;

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE IF NOT EXISTS `schedule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `nodes` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `nodesmin` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=421 ;

-- --------------------------------------------------------

--
-- Table structure for table `scp_connections`
--

CREATE TABLE IF NOT EXISTS `scp_connections` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `address` varchar(45) NOT NULL,
  `username` varchar(45) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE IF NOT EXISTS `status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy`
--

CREATE TABLE IF NOT EXISTS `taxonomy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file` varchar(45) NOT NULL,
  `description` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `netid` varchar(45) NOT NULL,
  `first` varchar(45) NOT NULL,
  `last` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `dropboxpath` text NOT NULL,
  `auth_token` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=175 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
