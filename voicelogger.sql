-- phpMyAdmin SQL Dump
-- version 3.0.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 20, 2010 at 04:53 PM
-- Server version: 5.0.77
-- PHP Version: 5.1.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `voicelogger`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE IF NOT EXISTS `account` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(30) NOT NULL default '',
  `password` varchar(30) NOT NULL default '',
  `usertype` varchar(20) NOT NULL default '',
  
  UNIQUE KEY `id` (`id`,`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `username`, `password`, `usertype`) VALUES
(1, 'admin', 'admin', 'admin');

-- --------------------------------------------------------
--
-- Table structure for table `voicefiles`
--

CREATE TABLE IF NOT EXISTS `voicefiles` (
  `id` int(7) NOT NULL auto_increment,
  `date` date NOT NULL,
  `time` varchar(10) NOT NULL,
  `sourse` varchar(20) NOT NULL,
  `destination` varchar(20) NOT NULL,
  `duration` varchar(10) NOT NULL,
  `groupid` int(11) NOT NULL default '1',
  `resellerid` int(11) NOT NULL default '1',
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=ucs2 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `voicefiles`
--

