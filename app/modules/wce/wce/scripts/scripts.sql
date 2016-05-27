-- Thomas -- 03/09/2012 --
ALTER TABLE  `dims_workspace` ADD  `twitter` VARCHAR( 255 ) NOT NULL DEFAULT  '';
ALTER TABLE  `dims_workspace` ADD  `facebook` VARCHAR( 255 ) NOT NULL DEFAULT  '';

-- Thomas -- 05/09/2012 --
DROP TABLE IF EXISTS `dims_mod_wce_article_visite`;
CREATE TABLE IF NOT EXISTS `dims_mod_wce_article_visite` (
  `sid` varchar(255) NOT NULL,
  `timestp` bigint(14) NOT NULL,
  `meter` int(4) NOT NULL DEFAULT '1',
  `id_module` int(11) NOT NULL,
  UNIQUE KEY `sid` (`sid`,`timestp`,`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Thomas -- 24/09/2012 --
DROP TABLE IF EXISTS `dims_mod_wce_article_tags`;
CREATE TABLE IF NOT EXISTS `dims_mod_wce_article_tags` (
  `id_tag` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `count` int(5) NOT NULL DEFAULT '1',
  UNIQUE KEY `id_tag` (`id_tag`,`id_workspace`,`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
INSERT INTO `dims_mb_wce_object` (`id` ,`label` ,`id_module_type` ,`script` ,`select_id` ,`select_label` ,`select_table` ,`select_params`)
VALUES (NULL ,  'Liste des tags',  '2',  '?op=''display_tags''',  '',  '',  '', NULL);

-- Cyril -- 24/09/2012 --
ALTER TABLE `dims_mod_wce_article` ADD `alert_level` TINYINT(1)  NULL  DEFAULT '1'  AFTER `id_globalobject`;


-- Cyril -- 24/09/2012 --
INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`)
VALUES (NULL, 'Liste de tous les articles', '2', '?op=\'display_all_alerts\'', NULL, NULL, NULL, NULL);
UPDATE `dims_mb_wce_object` SET `select_id` = 'id', `select_label` = 'label', `select_table` = 'dims_mod_wce_object' WHERE `id` = '72';


-- CYRIL -- 25/09/2012 --
UPDATE `dims_mb_wce_object` SET `select_id` = '', `select_label` = '', `select_table` = '' WHERE `id` = '72';
INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES (NULL, 'Liste de toutes les actus', '2', '?op=\'display_all_news\'', NULL, NULL, NULL, NULL);
UPDATE `dims_mb_wce_object` SET `label` = 'Liste de tous les alertes' WHERE `id` = '72';
UPDATE `dims_mb_wce_object` SET `label` = 'Liste d\'items [Objets dynamiques]', `select_id` = 'id', `select_label` = 'label', `select_table` = 'dims_mod_wce_object' WHERE `id` = '72';
DELETE FROM `dims_mb_wce_object` WHERE `id` IN ('73');
UPDATE `dims_mb_wce_object` SET `script` = '?op=\'control_dynobject\'' WHERE `id` = '72';

-- Thomas -- 02/10/2012 --
ALTER TABLE  `dims_mod_wce_slideshow` ADD  `color` VARCHAR( 25 ) NOT NULL DEFAULT  '#121A1D';

-- Thomas -- 03/10/2012 --
INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES
(null, 'Slideshow', 2, '?op=''slideshow''', 'id', 'nom', 'dims_mod_wce_slideshow', NULL);

-- Pat --
ALTER TABLE `dims_mod_wce_reference` ADD `id_lang` INT NOT NULL DEFAULT '0';

-- Pat
ALTER TABLE `dims_mod_wce_lang` DROP PRIMARY KEY ;
ALTER TABLE `dims_mod_wce_lang` ADD INDEX ( `id` , `id_module` )  ;

-- Thomas -- 18/10/2012 --
ALTER TABLE  `dims_mod_wce_heading` DROP PRIMARY KEY ,
ADD PRIMARY KEY (  `id` ,  `id_lang` );
ALTER TABLE  `dims_mod_wce_heading` CHANGE  `id_lang`  `id_lang` INT( 10 ) NOT NULL DEFAULT  '0';
UPDATE `dims_mod_wce_heading` SET `id_lang`=1 WHERE 1;

-- Thomas -- 08/10/2012 --
UPDATE  `dims_lang` SET  `label` =  'French' WHERE  `dims_lang`.`id` =1;
ALTER TABLE  `dims_lang` ADD  `ref` VARCHAR( 2 ) NOT NULL DEFAULT  '';
UPDATE  `dims_lang` SET  `ref` =  'fr' WHERE  `dims_lang`.`id` =1;
UPDATE  `dims_lang` SET  `label` =  'English', `ref` =  'en' WHERE  `dims_lang`.`id` =2;
UPDATE  `dims_lang` SET  `ref` =  'de' WHERE  `dims_lang`.`id` =4;
UPDATE  `dims_lang` SET  `ref` =  'ja' WHERE  `dims_lang`.`id` =5;
INSERT INTO  `dims_lang` (`id` ,`label` ,`code_of_conduct` ,`ref`) VALUES
(NULL ,  'Italian', NULL ,  'it');
ALTER TABLE  `dims_lang` ADD  `isactive` BOOLEAN NOT NULL DEFAULT  '1';
ALTER TABLE  `dims_mod_wce_lang` DROP PRIMARY KEY ,
ADD PRIMARY KEY (  `id` ,  `id_workspace` ,  `id_module` );
ALTER TABLE  `dims_mod_wce_lang` CHANGE  `id`  `id` INT( 11 ) NOT NULL;
UPDATE `dims_mod_wce_lang` SET `ref`='en' WHERE ref like 'um';
UPDATE `dims_mod_wce_lang` wl
inner join dims_lang l
on l.ref = wl.ref
LEFT join dims_mod_wce_article wa
on (wa.id_lang = wl.id
and wa.id_module = wl.id_module
and wa.id_workspace = wl.id_workspace)
LEFT join dims_mod_wce_article_block wab
on (wab.id_lang = wl.id
and wab.id_module = wl.id_module
and wab.id_workspace = wl.id_workspace)
LEFT join dims_mod_wce_heading wh
on (wh.id_lang = wl.id
and wh.id_module = wl.id_module
and wh.id_workspace = wl.id_workspace)
SET wa.id_lang = l.id, wab.id_lang=l.id, wh.id_lang=l.id, wl.id=l.id
WHERE 1=1;
UPDATE `dims_mod_wce_heading` SET `id_lang`=1 WHERE id_lang=0;

ALTER TABLE  `dims_workspace_template` ADD  `is_default` BOOLEAN NOT NULL DEFAULT  '0';

# Pat update title_meta
ALTER TABLE `dims_mod_wce_article` ADD `title_meta` VARCHAR( 255 ) NULL DEFAULT '' AFTER `title`;

-- Thomas -- 21/02/2013 --
ALTER TABLE  `dims_workspace` ADD  `youtube` VARCHAR( 255 ) NOT NULL DEFAULT  '';
ALTER TABLE  `dims_workspace` ADD  `google_plus` VARCHAR( 255 ) NOT NULL DEFAULT  '';
