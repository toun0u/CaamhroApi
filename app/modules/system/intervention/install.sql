CREATE  TABLE IF NOT EXISTS`dims_intervention` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `id_type_intervention` INT(11) NOT NULL DEFAULT 0 ,
  `id_user` INT(11) NOT NULL DEFAULT 0 ,
  `comment` TEXT NOT NULL ,
  `tmstp_realized` BIGINT(14) NOT NULL DEFAULT 0 ,
  `intervention_ground` TEXT NULL ,
  `status` INT(11) NOT NULL DEFAULT 0 ,
  `id_contact` INT(11) NOT NULL DEFAULT 0 ,
  `id_dossier` INT(11) NOT NULL DEFAULT 0 ,
  `id_todo` INT(11) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;



CREATE  TABLE IF NOT EXISTS `dims_intervention_counter` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `id_type_intervention` INT(11) NOT NULL DEFAULT 0 ,
  `id_globalobject` INT(11) NOT NULL DEFAULT 0 ,
  `cmpt` BIGINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;



CREATE  TABLE IF NOT EXISTS `dims_intervention_type` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `php_value` VARCHAR(120) NULL ,
  `libelle` VARCHAR(120) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;



CREATE  TABLE IF NOT EXISTS `dims_intervention_linked` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `id_globalobject` INT(11) NOT NULL DEFAULT 0 ,
  `id_intervention` INT(11) NOT NULL DEFAULT 0 ,
  `id_type_link` INT(11) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;



CREATE  TABLE IF NOT EXISTS `dims_intervention_link_type` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `php_value` VARCHAR(120) NULL ,
  `libelle` VARCHAR(120) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

#18 Aout 2011 by Aurélien
ALTER TABLE `dims_intervention` ADD `id_globalobject` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_intervention` CHANGE `id_dossier` `id_case` INT( 11 ) NOT NULL DEFAULT '0' ;

#18 Aout 2011 Lien au contact tiers par son globalobject
ALTER TABLE `dims_intervention` ADD `id_globalobject_ref` int(11) NOT NULL AFTER `id_globalobject`;
