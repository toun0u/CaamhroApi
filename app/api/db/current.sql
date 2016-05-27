-- Thomas - 02/07/2014
DROP TABLE IF EXISTS `ws_clients`;
CREATE TABLE IF NOT EXISTS `ws_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Thomas - 03/07/2014
ALTER TABLE `dims_user` ADD `token` VARCHAR( 255 ) NOT NULL DEFAULT '',
ADD `token_create` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `dims_user` CHANGE `token` `token` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

-- /!\ Ensure empty string `token` value ('') will not raise duplicate
-- entry errors on next query adding uniqueness to this field. /!\
-- Simon - 2015-11-26
UPDATE `dims_user` SET `token` = NULL WHERE `token` = '';
-- --

ALTER TABLE `dims_user` ADD UNIQUE (`token`);
ALTER TABLE `ws_clients` ADD `id_user` INT( 11 ) NOT NULL DEFAULT '0',
ADD `id_module` INT( 11 ) NOT NULL DEFAULT '0',
ADD `id_workspace` INT( 11 ) NOT NULL DEFAULT '0';

-- Thomas - 23/07/2014
ALTER TABLE `ws_clients` ADD `termnum` INT( 10 ) NULL DEFAULT NULL AFTER `ip` ,
ADD `numhabili` INT( 10 ) NULL DEFAULT NULL AFTER `termnum`;

-- Cyril - 28/10/2015 - pour by pass le contrôle dans la clef de contrôle
ALTER TABLE `ws_clients` ADD `has_ip_fixe` TINYINT(1)  NULL  DEFAULT '1'  AFTER `ip`;


