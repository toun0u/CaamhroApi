-- Adminer 3.3.4 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `dims_mod_sharefile_contact`;
CREATE TABLE `dims_mod_sharefile_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`,`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `dims_mod_sharefile_file`;
CREATE TABLE `dims_mod_sharefile_file` (
  `id_share` int(11) NOT NULL,
  `id_doc` int(11) NOT NULL,
  `download` int(11) NOT NULL,
  PRIMARY KEY (`id_share`,`id_doc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `dims_mod_sharefile_history`;
CREATE TABLE `dims_mod_sharefile_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_share` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `action` tinyint(4) NOT NULL DEFAULT '1',
  `timestp_create` bigint(14) NOT NULL,
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_share` (`id_share`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `dims_mod_sharefile_param`;
CREATE TABLE `dims_mod_sharefile_param` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `uniquecode` tinyint(4) NOT NULL,
  `nbcar` int(11) NOT NULL,
  `message` longtext NOT NULL,
  `title_message` char(255) NOT NULL DEFAULT '',
  `send_message` text NOT NULL,
  `nbdays` int(11) NOT NULL DEFAULT '15',
  `nbdownload` int(11) NOT NULL DEFAULT '0',
  `email_title` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `dims_mod_sharefile_share`;
CREATE TABLE `dims_mod_sharefile_share` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_entity` int(11) unsigned NOT NULL,
  `label` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `timestp_modify` bigint(14) NOT NULL,
  `mustbeconnected` tinyint(1) NOT NULL,
  `code` varchar(100) NOT NULL,
  `timestp_create` bigint(14) NOT NULL,
  `timestp_finished` bigint(14) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `docsize` int(10) NOT NULL DEFAULT '0',
  `nbdoc` int(10) NOT NULL DEFAULT '0',
  `id_globalobject` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `dims_mod_sharefile_user`;
CREATE TABLE `dims_mod_sharefile_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_share` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `id_group` int(11) NOT NULL DEFAULT '0',
  `view` int(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `code` varchar(12) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_share` (`id_share`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- 2012-10-22 11:42:47
