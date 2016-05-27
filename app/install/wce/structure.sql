-- phpMyAdmin SQL Dump
-- version 2.11.2
-- http://www.phpmyadmin.net
--
-- Host: mysql1.netlorconcept.com
-- Generation Time: Apr 17, 2008 at 06:42 AM
-- Server version: 4.0.27
-- PHP Version: 5.2.0-8+etch10

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_wce_article`
--

DROP TABLE IF EXISTS `dims_mod_wce_article`;
CREATE TABLE IF NOT EXISTS `dims_mod_wce_article` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `reference` varchar(255) NOT NULL default '',
  `title` varchar(255) default NULL,
  `content1` longtext,
  `author` longtext,
  `version` varchar(16) NOT NULL default '',
  `visible` tinyint(1) unsigned NOT NULL default '0',
  `timestp` bigint(14) unsigned NOT NULL default '0',
  `timestp_published` bigint(14) unsigned NOT NULL default '0',
  `timestp_unpublished` bigint(14) unsigned NOT NULL default '0',
  `id_heading` int(10) unsigned default '0',
  `id_module` int(10) unsigned default '0',
  `id_user` int(10) unsigned default '0',
  `id_workspace` int(10) unsigned default '0',
  `position` int(10) unsigned NOT NULL default '0',
  `status` varchar(16) NOT NULL default '',
  `meter` bigint(14) NOT NULL default '0',
  `template_id` int(10) NOT NULL default '0',
  `content2` longtext NOT NULL,
  `content3` longtext NOT NULL,
  `content4` longtext NOT NULL,
  `content5` longtext NOT NULL,
  `content6` longtext NOT NULL,
  `content7` longtext NOT NULL,
  `content8` longtext NOT NULL,
  `content9` longtext NOT NULL,
  `content10` longtext NOT NULL,
  `meta_description` longtext NOT NULL,
  `meta_keywords` longtext NOT NULL,
  `model` varchar(255) NOT NULL default '',
  `lastupdate_timestp` bigint(14) NOT NULL default '0',
  `lastupdate_id_user` bigint(14) NOT NULL default '0',
  `timestp_modify` bigint(14) NOT NULL default '0',
  `urlrewrite` varchar(255) NOT NULL default '',
  `draftcontent1` longtext NOT NULL,
  `draftcontent2` longtext NOT NULL,
  `draftcontent3` longtext NOT NULL,
  `draftcontent4` longtext NOT NULL,
  `draftcontent5` longtext NOT NULL,
  `draftcontent6` longtext NOT NULL,
  `draftcontent7` longtext NOT NULL,
  `draftcontent8` longtext NOT NULL,
  `draftcontent9` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=76 ;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_wce_article_draft`
--

DROP TABLE IF EXISTS `dims_mod_wce_article_draft`;
CREATE TABLE IF NOT EXISTS `dims_mod_wce_article_draft` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `reference` varchar(255) NOT NULL default '',
  `title` varchar(255) default NULL,
  `content1` longtext,
  `author` longtext,
  `version` varchar(16) NOT NULL default '',
  `visible` tinyint(1) unsigned NOT NULL default '0',
  `timestp` bigint(14) unsigned NOT NULL default '0',
  `timestp_published` bigint(14) unsigned NOT NULL default '0',
  `timestp_unpublished` bigint(14) unsigned NOT NULL default '0',
  `id_heading` int(10) unsigned default '0',
  `id_module` int(10) unsigned default '0',
  `id_user` int(10) unsigned default '0',
  `id_workspace` int(10) unsigned default '0',
  `position` int(10) unsigned NOT NULL default '0',
  `status` varchar(16) NOT NULL default '',
  `meter` bigint(14) NOT NULL default '0',
  `template_id` int(10) NOT NULL default '0',
  `content2` longtext NOT NULL,
  `content3` longtext NOT NULL,
  `content4` longtext NOT NULL,
  `content5` longtext NOT NULL,
  `content6` longtext NOT NULL,
  `content7` longtext NOT NULL,
  `content8` longtext NOT NULL,
  `content9` longtext NOT NULL,
  `content10` longtext NOT NULL,
  `meta_description` longtext NOT NULL,
  `meta_keywords` longtext NOT NULL,
  `model` varchar(255) NOT NULL default '',
  `lastupdate_timestp` int(10) NOT NULL default '0',
  `lastupdate_id_user` int(10) NOT NULL default '0',
  `urlrewrite` varchar(255) NOT NULL default '',
  `timestp_modify` bigint(14) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=70 ;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_wce_article_meter`
--

DROP TABLE IF EXISTS `dims_mod_wce_article_meter`;
CREATE TABLE IF NOT EXISTS `dims_mod_wce_article_meter` (
  `id_article` int(10) NOT NULL default '0',
  `timestp` varchar(14) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `meter` bigint(14) NOT NULL default '0',
  PRIMARY KEY  (`id_article`,`timestp`,`email`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_wce_article_version`
--

DROP TABLE IF EXISTS `dims_mod_wce_article_version`;
CREATE TABLE IF NOT EXISTS `dims_mod_wce_article_version` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `articleid` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `version` int(11) NOT NULL default '0',
  `draftversion` int(10) NOT NULL default '0',
  `title` varchar(255) default NULL,
  `author` longtext,
  `id_module` int(10) unsigned default '0',
  `id_user` int(10) unsigned default '0',
  `id_workspace` int(10) unsigned default '0',
  `content1` longtext,
  `content2` longtext NOT NULL,
  `content3` longtext NOT NULL,
  `content4` longtext NOT NULL,
  `content5` longtext NOT NULL,
  `content6` longtext NOT NULL,
  `content7` longtext NOT NULL,
  `content8` longtext NOT NULL,
  `content9` longtext NOT NULL,
  `content10` longtext NOT NULL,
  `meta_description` longtext NOT NULL,
  `meta_keywords` longtext NOT NULL,
  `timestp_modify` bigint(14) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=171 ;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_wce_heading`
--

DROP TABLE IF EXISTS `dims_mod_wce_heading`;
CREATE TABLE IF NOT EXISTS `dims_mod_wce_heading` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `template` varchar(255) NOT NULL default '',
  `id_heading` int(10) unsigned NOT NULL default '0',
  `parents` varchar(255) NOT NULL default '',
  `depth` int(10) unsigned NOT NULL default '0',
  `position` int(10) unsigned NOT NULL default '0',
  `color` varchar(32) NOT NULL default '',
  `posx` int(10) unsigned NOT NULL default '0',
  `posy` int(10) unsigned NOT NULL default '0',
  `visible` tinyint(1) unsigned NOT NULL default '0',
  `linkedpage` int(10) unsigned NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `url_window` tinyint(1) unsigned NOT NULL default '0',
  `free1` varchar(255) NOT NULL default '',
  `free2` varchar(255) NOT NULL default '',
  `id_module` tinyint(10) unsigned default '0',
  `id_user` int(10) unsigned default '0',
  `id_workspace` int(10) unsigned default '0',
  `timestp_modify` bigint(14) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=91 ;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_wce_template`
--

DROP TABLE IF EXISTS `dims_mod_wce_template`;
CREATE TABLE IF NOT EXISTS `dims_mod_wce_template` (
  `id` int(10) NOT NULL auto_increment,
  `label` varchar(255) NOT NULL default '',
  `id_workspace` int(10) NOT NULL default '0',
  `id_module` int(10) NOT NULL default '0',
  `id_user` int(10) NOT NULL default '0',
  `share` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

