-- Thomas -- 04/06/2012 --
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`, `historic_view`, `notif_view`, `notif_mail`) VALUES
('2', '5', '_DIMS_LABEL_ADD_COMMENT', NULL, NULL, '0', '0', '0', '0'),
('2', '6', '_SMILE_CREATION_ASSOCIATE', NULL, NULL, '0', '0', '0', '0'),
('2', '7', '_MANAGEMENT_OF_ASSOCIATES', NULL, NULL, '0', '0', '0', '0'),
('2', '8', '_SYSTEM_LABELTAB_ROLEMANAGEMENT', NULL, NULL, '0', '0', '0', '0'),
('2', '9', '_OEUVRE_CREATION_OF_A_DEPARTMENT', NULL, NULL, '0', '0', '0', '0');

-- Thomas -- 05/06/2012 --
DROP TABLE IF EXISTS `dims_mod_wce_lang`;
CREATE TABLE IF NOT EXISTS `dims_mod_wce_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(244) NOT NULL,
  `ref` varchar(3) NOT NULL DEFAULT '',
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `dims_mod_wce_lang` (`id`, `label`, `ref`, `id_module`, `id_workspace`, `id_user`, `is_active`) VALUES
(1, 'Français', 'fr', 333, 93, 80, 1),
(2, 'Anglais', 'en', 333, 93, 80, 1),
(3, 'Allemand', 'de', 333, 93, 80, 1);

-- Pat 06/06/2012

ALTER TABLE `dims_mod_wce_article` DROP `id_lang`;
ALTER TABLE `dims_mod_wce_article` ADD `id_lang` INT( 11 ) NOT NULL DEFAULT '1' AFTER `id`;
ALTER TABLE `dims_mod_wce_article` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` , `id_lang` ) ;
ALTER TABLE `dims_mod_wce_article_block` ADD `id_lang` INT( 11 ) NOT NULL DEFAULT '1' AFTER `id`;
ALTER TABLE `dims_mod_wce_article_block` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` , `id_lang` );
ALTER TABLE `dims_mod_wce_article_block_version` ADD `id_lang` INT( 11 ) NOT NULL DEFAULT '1' AFTER `blockid` ;
#ALTER TABLE `dims_mod_wce_article_block_version` ADD UNIQUE (`blockid` ,`id_lang`);

ALTER TABLE `dims_mod_wce_article`
ADD `content11` LONGTEXT NOT NULL ,
ADD `content12` LONGTEXT NOT NULL ,
ADD `content13` LONGTEXT NOT NULL ,
ADD `content14` LONGTEXT NOT NULL ,
ADD `content15` LONGTEXT NOT NULL ,
ADD `content16` LONGTEXT NOT NULL ,
ADD `content17` LONGTEXT NOT NULL ,
ADD `content18` LONGTEXT NOT NULL ,
ADD `content19` LONGTEXT NOT NULL ,
ADD `draftcontent10` LONGTEXT NOT NULL ,
ADD `draftcontent11` LONGTEXT NOT NULL ,
ADD `draftcontent12` LONGTEXT NOT NULL ,
ADD `draftcontent13` LONGTEXT NOT NULL ,
ADD `draftcontent14` LONGTEXT NOT NULL ,
ADD `draftcontent15` LONGTEXT NOT NULL ,
ADD `draftcontent16` LONGTEXT NOT NULL ,
ADD `draftcontent17` LONGTEXT NOT NULL ,
ADD `draftcontent18` LONGTEXT NOT NULL ,
ADD `draftcontent19` LONGTEXT NOT NULL ;

ALTER TABLE `dims_mod_wce_article_block`
ADD `content11` LONGTEXT NOT NULL ,
ADD `content12` LONGTEXT NOT NULL ,
ADD `content13` LONGTEXT NOT NULL ,
ADD `content14` LONGTEXT NOT NULL ,
ADD `content15` LONGTEXT NOT NULL ,
ADD `content16` LONGTEXT NOT NULL ,
ADD `content17` LONGTEXT NOT NULL ,
ADD `content18` LONGTEXT NOT NULL ,
ADD `content19` LONGTEXT NOT NULL ,
ADD `draftcontent11` LONGTEXT NOT NULL ,
ADD `draftcontent12` LONGTEXT NOT NULL ,
ADD `draftcontent13` LONGTEXT NOT NULL ,
ADD `draftcontent14` LONGTEXT NOT NULL ,
ADD `draftcontent15` LONGTEXT NOT NULL ,
ADD `draftcontent16` LONGTEXT NOT NULL ,
ADD `draftcontent17` LONGTEXT NOT NULL ,
ADD `draftcontent18` LONGTEXT NOT NULL ,
ADD `draftcontent19` LONGTEXT NOT NULL ;


ALTER TABLE `dims_mod_wce_article_version`
ADD `content11` LONGTEXT NOT NULL ,
ADD `content12` LONGTEXT NOT NULL ,
ADD `content13` LONGTEXT NOT NULL ,
ADD `content14` LONGTEXT NOT NULL ,
ADD `content15` LONGTEXT NOT NULL ,
ADD `content16` LONGTEXT NOT NULL ,
ADD `content17` LONGTEXT NOT NULL ,
ADD `content18` LONGTEXT NOT NULL ,
ADD `content19` LONGTEXT NOT NULL ;

ALTER TABLE `dims_mod_wce_article_block_version`
ADD `content11` LONGTEXT NOT NULL ,
ADD `content12` LONGTEXT NOT NULL ,
ADD `content13` LONGTEXT NOT NULL ,
ADD `content14` LONGTEXT NOT NULL ,
ADD `content15` LONGTEXT NOT NULL ,
ADD `content16` LONGTEXT NOT NULL ,
ADD `content17` LONGTEXT NOT NULL ,
ADD `content18` LONGTEXT NOT NULL ,
ADD `content19` LONGTEXT NOT NULL ,
ADD `draftcontent11` LONGTEXT NOT NULL ,
ADD `draftcontent12` LONGTEXT NOT NULL ,
ADD `draftcontent13` LONGTEXT NOT NULL ,
ADD `draftcontent14` LONGTEXT NOT NULL ,
ADD `draftcontent15` LONGTEXT NOT NULL ,
ADD `draftcontent16` LONGTEXT NOT NULL ,
ADD `draftcontent17` LONGTEXT NOT NULL ,
ADD `draftcontent18` LONGTEXT NOT NULL ,
ADD `draftcontent19` LONGTEXT NOT NULL ;

ALTER TABLE `dims_mod_wce_article_block` ADD `section` INT( 11 ) NOT NULL DEFAULT '0' AFTER `id` , ADD INDEX ( `section` );


# Pat 09/06/2012
ALTER TABLE `dims_mod_wce_article_block` ADD `level` TINYINT NOT NULL DEFAULT '1' AFTER `section` ;
ALTER TABLE `dims_mod_wce_heading` ADD `type` TINYINT NOT NULL DEFAULT '0' AFTER `id` ,ADD INDEX ( `type` ) ;

# Pat 16/06/2012
ALTER TABLE `dims_mod_wce_article_block` ADD `l1` INT( 11 ) NOT NULL DEFAULT '0' AFTER `level` ,
ADD `l2` INT( 11 ) NOT NULL DEFAULT '0' AFTER `l1` ,
ADD `l3` INT( 11 ) NOT NULL DEFAULT '0' AFTER `l2` ;

ALTER TABLE `dims_mod_wce_article_block` ADD INDEX ( `l1` , `l2` , `l3` ) ;

# Pat 17/06/2012
update `dims_category` set id_module=2 where id_module=333;

#Cyril 20/07/2012
DROP TABLE dims_todo;
CREATE TABLE `dims_todo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `priority` tinyint(4) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `date_validation` datetime DEFAULT NULL,
  `content` text,
  `user_from` int(11) DEFAULT NULL COMMENT 'expediteur',
  `user_to` int(11) DEFAULT NULL COMMENT 'destinataire',
  `user_by` int(11) DEFAULT NULL COMMENT 'validé par',
  `state` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `id_parent` int(11) DEFAULT NULL COMMENT 'tache associée',
  `id_user` int(11) NOT NULL,
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  `id_globalobject_ref` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`user_from`),
  KEY `date` (`date`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

DROP TABLE dims_todo_dest;

CREATE TABLE `dims_todo_dest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_todo` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `flag` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

ALTER TABLE `dims_todo` ADD `id_globalobject` INT(11)  NULL  DEFAULT '0'  AFTER `id_globalobject_ref`;
ALTER TABLE `dims_todo` ADD `timestp_create` BIGINT(14)  NULL  DEFAULT '0'  AFTER `id_parent`;
ALTER TABLE `dims_todo` ADD `timestp_modify` BIGINT(14)  NULL  DEFAULT '0'  AFTER `timestp_create`;

#Cyril 23/007/2012 --
ALTER TABLE `dims_todo_dest` ADD `type` TINYINT(3)  NULL  DEFAULT '0'  AFTER `flag`;
ALTER TABLE `dims_todo` ADD `considered_as` TINYINT(1)  NULL  DEFAULT '0'  AFTER `type`;

#Cyril 24/07/2012
ALTER TABLE `dims_todo` ADD `is_validator` TINYINT(1)  NULL  DEFAULT '0'  AFTER `date_validation`;

#Cyril 27/07/2012
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'content10', 'content10', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'draftcontent1', 'draftcontent1', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'draftcontent2', 'draftcontent2', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'draftcontent3', 'draftcontent3', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'draftcontent4', 'draftcontent4', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'draftcontent5', 'draftcontent5', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'draftcontent6', 'draftcontent6', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'draftcontent7', 'draftcontent6', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'draftcontent8', 'draftcontent8', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'draftcontent9', 'draftcontent9', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'draftcontent10', 'draftcontent10', 'longtext', '1', '1', '1', '1');

ALTER TABLE `dims_mod_wce_article_block` ADD `id_globalobject` INT(11)  NULL  DEFAULT '0'  AFTER `draftcontent19`;

INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'content11', 'content11', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'content12', 'content12', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'content13', 'content13', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'content14', 'content14', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'content15', 'content15', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'content16', 'content16', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'content17', 'content17', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'content18', 'content18', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES (NULL, '83', 'content19', 'content19', 'longtext', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES
('', 83, 'draftcontent11', 'draftcontent11', 'longtext', 1, 1, 1, 1),
('', 83, 'draftcontent12', 'draftcontent12', 'longtext', 1, 1, 1, 1),   ('', 83, 'draftcontent13', 'draftcontent13', 'longtext', 1, 1, 1, 1),
('', 83, 'draftcontent14', 'draftcontent14', 'longtext', 1, 1, 1, 1),   ('', 83, 'draftcontent15', 'draftcontent15', 'longtext', 1, 1, 1, 1),
('', 83, 'draftcontent16', 'draftcontent16', 'longtext', 1, 1, 1, 1),   ('', 83, 'draftcontent17', 'draftcontent17', 'longtext', 1, 1, 1, 1),
('', 83, 'draftcontent18', 'draftcontent18', 'longtext', 1, 1, 1, 1),   ('', 83, 'draftcontent19', 'draftcontent19', 'longtext', 1, 1, 1, 1);

INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES   ('', 46, 'content10', 'content10', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent1', 'draftcontent1', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent2', 'draftcontent2', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent3', 'draftcontent3', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent4', 'draftcontent4', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent5', 'draftcontent5', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent6', 'draftcontent6', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent7', 'draftcontent6', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent8', 'draftcontent8', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent9', 'draftcontent9', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent10', 'draftcontent10', 'longtext', 1, 1, 1, 1);
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES   ('', 46, 'content11', 'content11', 'longtext', 1, 1, 1, 1),
('', 46, 'content12', 'content12', 'longtext', 1, 1, 1, 1),
('', 46, 'content13', 'content13', 'longtext', 1, 1, 1, 1),
('', 46, 'content14', 'content14', 'longtext', 1, 1, 1, 1),
('', 46, 'content15', 'content15', 'longtext', 1, 1, 1, 1),
('', 46, 'content16', 'content16', 'longtext', 1, 1, 1, 1),
('', 46, 'content17', 'content17', 'longtext', 1, 1, 1, 1),
('', 46, 'content18', 'content18', 'longtext', 1, 1, 1, 1),
('', 46, 'content19', 'content19', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent11', 'draftcontent11', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent12', 'draftcontent12', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent13', 'draftcontent13', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent14', 'draftcontent14', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent15', 'draftcontent15', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent16', 'draftcontent16', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent17', 'draftcontent17', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent18', 'draftcontent18', 'longtext', 1, 1, 1, 1),
('', 46, 'draftcontent19', 'draftcontent19', 'longtext', 1, 1, 1, 1);

ALTER TABLE `dims_mod_wce_article` ADD `uptodate` TINYINT(1)  NULL  DEFAULT '1'  AFTER `timestp_unpublished`;
ALTER TABLE `dims_mod_wce_article_block` ADD `uptodate` TINYINT(1)  NULL  DEFAULT '1';

ALTER TABLE `dims_mod_wce_article_block` CHANGE `content11` `content11` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `content12` `content12` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `content13` `content13` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `content14` `content14` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `content15` `content15` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `content16` `content16` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `content17` `content17` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `content18` `content18` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `content19` `content19` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `draftcontent11` `draftcontent11` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `draftcontent12` `draftcontent12` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `draftcontent13` `draftcontent13` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `draftcontent14` `draftcontent14` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `draftcontent15` `draftcontent15` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `draftcontent16` `draftcontent16` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `draftcontent17` `draftcontent17` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `draftcontent18` `draftcontent18` LONGTEXT  NULL;
ALTER TABLE `dims_mod_wce_article_block` CHANGE `draftcontent19` `draftcontent19` LONGTEXT  NULL;

--Cyril  31/07/2012 --

ALTER TABLE `dims_mod_wce_article` CHANGE `uptodate` `uptodate` TINYINT(1)  UNSIGNED  NULL  DEFAULT '0';
ALTER TABLE `dims_mod_wce_article_block` CHANGE `uptodate` `uptodate` TINYINT(1)  UNSIGNED  NULL  DEFAULT '0';

-- Cyril 01/08/2012 --
ALTER TABLE `dims_mod_wce_article` ADD `updated_by` INT(11)  NULL  DEFAULT '0'  AFTER `timestp_modify`;
UPDATE dims_mod_wce_article a, dims_user  u SET a.updated_by = u.id_contact WHERE a.id_user = u.id;

-- Thomas -- 02/08/2012 --
ALTER TABLE `dims_mod_wce_article_block_version` CHANGE `content11` `content11` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `content12` `content12` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `content13` `content13` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `content14` `content14` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `content15` `content15` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `content16` `content16` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `content17` `content17` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `content18` `content18` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `content19` `content19` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `draftcontent11` `draftcontent11` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `draftcontent12` `draftcontent12` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `draftcontent13` `draftcontent13` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `draftcontent14` `draftcontent14` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `draftcontent15` `draftcontent15` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `draftcontent16` `draftcontent16` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `draftcontent17` `draftcontent17` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `draftcontent18` `draftcontent18` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `draftcontent19` `draftcontent19` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;

-- Thomas -- 03/08/2012 --
ALTER TABLE  `dims_mod_doc_file` ADD  `converted` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `id_object`;

-- Cyril 08/08/2012 --
-- CYRIL - 07/08/2012 - desktopv2 / todos --

ALTER TABLE `dims_todo_dest` CHANGE `flag` `flag` TINYINT(1)  NULL  DEFAULT '0'  COMMENT 'permet de le mettre de côté';
ALTER TABLE `dims_todo_dest` ADD `validated` TINYINT(1)  NULL  DEFAULT '0'  AFTER `flag`;
ALTER TABLE `dims_todo_dest` CHANGE `validated` `validated` TINYINT(1)  NULL  DEFAULT '0'  COMMENT 'indique si le destinataire a accusé la réception du todo';
ALTER TABLE `dims_todo_dest` ADD `date_validation` DATETIME  NULL  AFTER `validated`;

-- 22/08/2012
ALTER TABLE  `dims_mod_wce_lang` ADD  `default` TINYINT NOT NULL DEFAULT  '0';
update  `dims_mod_wce_lang` set `default`=1 where ref='fr';
delete from dims_mb_object where id_module_type=2 and id_class=30;

-- 27/08/2012
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `content11`  `content11` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `content12`  `content12` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `content13`  `content13` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `content14`  `content14` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `content15`  `content15` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `content16`  `content16` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `content17`  `content17` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `content18`  `content18` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `content19`  `content19` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `draftcontent11`  `draftcontent11` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `draftcontent12`  `draftcontent12` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `draftcontent13`  `draftcontent13` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `draftcontent14`  `draftcontent14` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `draftcontent15`  `draftcontent15` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `draftcontent16`  `draftcontent16` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `draftcontent17`  `draftcontent17` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `draftcontent18`  `draftcontent18` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `dims_mod_wce_article_block_version` CHANGE  `draftcontent19`  `draftcontent19` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE  `dims_matrix` ADD  `id_wce_article` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `dims_matrix` ADD  `id_wce_article2` INT( 11 ) NOT NULL DEFAULT  '0';

DROP TABLE IF EXISTS `dims_mod_wce_reference`;
CREATE TABLE IF NOT EXISTS `dims_mod_wce_reference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `typelink` tinyint(4) NOT NULL DEFAULT '1',
  `link` varchar(255) NOT NULL DEFAULT '',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_article` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_article` (`id_article`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



-- Cyril 07/09/2012 --
-- Ajout de jointure sur les domaines permettant de rediriger sur un article wce d'accueil ou de post connexion
ALTER TABLE `dims_domain`
ADD `id_home_wce_article` int(11) NOT NULL DEFAULT '0' AFTER `mobile`,
ADD `id_post_connexion_wce_article` int(11) NOT NULL DEFAULT '0' AFTER `id_home_wce_article`,
COMMENT=''
REMOVE PARTITIONING;


ALTER TABLE `dims_mod_wce_reference` ADD `position` TINYINT NOT NULL DEFAULT '1' AFTER `typelink`;

-- Pat 12/09/2012 --
ALTER TABLE `dims_mod_wce_reference` ADD `id_doc_link` INT( 11 ) NOT NULL DEFAULT '0';

-- Thomas -- 08/10/2012 --
ALTER TABLE  `dims_mod_wce_article` CHANGE  `id_lang`  `id_lang` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `dims_mod_wce_reference` ADD  `id_lang` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `id`;


-- Pat 17/10/2012 --
UPDATE dims_constant SET value='Etes-vous sûr de vouloir initialiser cette nouvelle langue ?', phpvalue='_CONFIRM_INITIALIZE_NEW_LANGUAGE' WHERE id_lang=1 AND phpvalue='_CONFIRM_INITIALIZE_NEW_LANGUAGE';

-- Thomas -- 17/10/2012 --
UPDATE `dims_tag` SET `tag_fr`=tag WHERE tag_fr = '';
