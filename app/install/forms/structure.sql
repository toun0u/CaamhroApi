-- phpMyAdmin SQL Dump
-- version 2.11.2
-- http://www.phpmyadmin.net
--
-- Host: mysql1.netlorconcept.com
-- Generation Time: Apr 17, 2008 at 06:41 AM
-- Server version: 4.0.27
-- PHP Version: 5.2.0-8+etch10

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_forms`
--

DROP TABLE IF EXISTS `dims_mod_forms`;
CREATE TABLE IF NOT EXISTS `dims_mod_forms` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(255) default NULL,
  `tablename` varchar(255) NOT NULL default '',
  `description` longtext,
  `pubdate_start` varchar(14) default NULL,
  `pubdate_end` varchar(14) default NULL,
  `email` varchar(255) default NULL,
  `option_onlyone` tinyint(1) unsigned default '0',
  `option_onlyoneday` tinyint(1) unsigned default '0',
  `width` varchar(5) NOT NULL default '*',
  `nbline` int(10) unsigned default '25',
  `model` varchar(32) default NULL,
  `typeform` varchar(16) default 'app',
  `option_modify` varchar(16) NOT NULL default 'nobody',
  `option_view` varchar(16) NOT NULL default 'global',
  `option_displayuser` tinyint(1) unsigned default '0',
  `option_displaygroup` tinyint(1) unsigned default '0',
  `option_displaydate` tinyint(1) unsigned default '0',
  `option_displayip` tinyint(1) unsigned default '0',
  `viewed` int(10) unsigned default '0',
  `autobackup` int(10) unsigned default '0',
  `cms_response` longtext,
  `cms_link` tinyint(1) unsigned default '0',
  `id_user` int(10) unsigned default '0',
  `id_workspace` int(10) unsigned default '0',
  `id_module` int(10) unsigned default '0',
  `timestp_modify` bigint(14) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_forms_field`
--

DROP TABLE IF EXISTS `dims_mod_forms_field`;
CREATE TABLE IF NOT EXISTS `dims_mod_forms_field` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_forms` int(10) unsigned default '0',
  `name` varchar(255) default NULL,
  `fieldname` varchar(255) NOT NULL default '',
  `separator` tinyint(1) unsigned default '0',
  `separator_level` int(10) unsigned default '0',
  `separator_fontsize` int(10) unsigned default '0',
  `type` varchar(16) default NULL,
  `format` varchar(16) default NULL,
  `values` longtext,
  `description` longtext,
  `position` int(10) unsigned default '0',
  `maxlength` int(10) unsigned default '0',
  `cols` int(10) unsigned default '0',
  `option_needed` tinyint(1) unsigned default '0',
  `option_arrayview` tinyint(1) unsigned default '1',
  `option_exportview` tinyint(1) unsigned default '1',
  `option_cmsgroupby` tinyint(1) unsigned default '0',
  `option_cmsorderby` tinyint(1) unsigned default '0',
  `option_cmsdisplaylabel` tinyint(1) unsigned default '0',
  `option_cmsshowfilter` tinyint(1) unsigned default '0',
  `defaultvalue` varchar(255) default NULL,
  `interline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`,`id_forms`)
) TYPE=MyISAM AUTO_INCREMENT=155 ;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_forms_reply`
--

DROP TABLE IF EXISTS `dims_mod_forms_reply`;
CREATE TABLE IF NOT EXISTS `dims_mod_forms_reply` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_forms` int(10) unsigned default '0',
  `id_user` int(10) unsigned default '0',
  `id_workspace` tinyint(3) unsigned default '0',
  `id_module` int(10) unsigned default NULL,
  `date_validation` varchar(14) default NULL,
  `ip` varchar(15) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=47 ;

-- --------------------------------------------------------

--
-- Table structure for table `dims_mod_forms_reply_field`
--

DROP TABLE IF EXISTS `dims_mod_forms_reply_field`;
CREATE TABLE IF NOT EXISTS `dims_mod_forms_reply_field` (
  `id_reply` int(10) unsigned default '0',
  `id_forms` int(10) unsigned default '0',
  `id_field` int(10) unsigned default '0',
  `value` longtext,
  `id_user` int(10) NOT NULL default '0',
  `id_workspace` int(10) NOT NULL default '0',
  `id_module` int(10) NOT NULL default '0',
  `id` int(10) NOT NULL auto_increment,
  `timestp_modify` bigint(14) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id_reply` (`id_reply`),
  KEY `id_forms` (`id_forms`),
  KEY `id_field` (`id_field`),
  KEY `id_workspace` (`id_workspace`,`id_module`)
) TYPE=MyISAM AUTO_INCREMENT=355 ;

