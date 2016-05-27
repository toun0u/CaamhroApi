-- phpMyAdmin SQL Dump
-- version 2.11.2
-- http://www.phpmyadmin.net
--
-- Host: mysql1.netlorconcept.com
-- Generation Time: Apr 17, 2008 at 06:40 AM
-- Server version: 4.0.27
-- PHP Version: 5.2.0-8+etch10

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_doc_ext`
--

DROP TABLE IF EXISTS `dims_mod_doc_ext`;
CREATE TABLE IF NOT EXISTS `dims_mod_doc_ext` (
  `ext` varchar(10) default NULL,
  `filetype` varchar(16) default NULL,
  KEY `ext` (`ext`),
  KEY `filetype` (`filetype`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_doc_file`
--

DROP TABLE IF EXISTS `dims_mod_doc_file`;
CREATE TABLE IF NOT EXISTS `dims_mod_doc_file` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `md5id` varchar(32) NOT NULL default '',
  `name` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `timestp_create` bigint(14) default NULL,
  `timestp_modify` bigint(14) default NULL,
  `size` int(10) unsigned default '0',
  `extension` varchar(20) default NULL,
  `parents` varchar(255) default NULL,
  `content` longtext NOT NULL,
  `nbclick` int(10) unsigned default '0',
  `version` int(10) NOT NULL default '1',
  `id_folder` int(10) unsigned default '0',
  `id_user_modify` int(10) unsigned default '0',
  `id_user` int(10) unsigned default '0',
  `id_workspace` int(10) unsigned default '0',
  `id_module` int(10) unsigned default '0',
  PRIMARY KEY  (`id`),
  KEY `id_group` (`id_workspace`),
  KEY `id_module` (`id_module`),
  KEY `name` (`name`),
  KEY `id_folder` (`id_folder`),
  KEY `extension` (`extension`),
  KEY `md5id` (`md5id`),
  KEY `key_search` (`id_module`,`id_workspace`,`id_user`)
) TYPE=MyISAM AUTO_INCREMENT=302 ;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_doc_file_draft`
--

DROP TABLE IF EXISTS `dims_mod_doc_file_draft`;
CREATE TABLE IF NOT EXISTS `dims_mod_doc_file_draft` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `md5id` varchar(32) NOT NULL default '',
  `id_docfile` int(10) unsigned default '0',
  `name` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `timestp_create` bigint(14) default NULL,
  `size` int(10) unsigned default '0',
  `extension` varchar(20) NOT NULL default '',
  `parents` varchar(255) default NULL,
  `id_folder` int(10) unsigned default '0',
  `id_user_modify` int(10) unsigned NOT NULL default '0',
  `id_user` int(10) unsigned NOT NULL default '0',
  `id_workspace` int(10) unsigned NOT NULL default '0',
  `id_module` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `md5id` (`md5id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_doc_file_history`
--

DROP TABLE IF EXISTS `dims_mod_doc_file_history`;
CREATE TABLE IF NOT EXISTS `dims_mod_doc_file_history` (
  `id_docfile` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `timestp_create` bigint(14) default NULL,
  `timestp_modify` bigint(14) default NULL,
  `size` int(10) unsigned default '0',
  `version` int(10) NOT NULL default '1',
  `extension` varchar(20) NOT NULL default '',
  `id_user_modify` int(10) unsigned NOT NULL default '0',
  `id_module` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_docfile`,`version`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_doc_folder`
--

DROP TABLE IF EXISTS `dims_mod_doc_folder`;
CREATE TABLE IF NOT EXISTS `dims_mod_doc_folder` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `foldertype` varchar(16) NOT NULL default 'private',
  `readonly` tinyint(1) unsigned NOT NULL default '0',
  `readonly_content` tinyint(1) unsigned NOT NULL default '0',
  `parents` varchar(255) default '0',
  `timestp_create` bigint(14) default NULL,
  `timestp_modify` bigint(14) default NULL,
  `nbelements` int(10) unsigned NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '1',
  `id_folder` int(10) unsigned default '0',
  `id_user_modify` int(10) unsigned default '0',
  `id_user` int(10) unsigned default '0',
  `id_workspace` int(10) unsigned default '0',
  `id_module` int(10) unsigned default '0',
  PRIMARY KEY  (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_group` (`id_workspace`),
  KEY `id_module` (`id_module`),
  KEY `id_folder` (`id_folder`)
) TYPE=MyISAM AUTO_INCREMENT=1533 ;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_doc_param`
--

DROP TABLE IF EXISTS `dims_mod_doc_param`;
CREATE TABLE IF NOT EXISTS `dims_mod_doc_param` (
  `id` int(10) NOT NULL auto_increment,
  `libelle` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `ext` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=9 ;

