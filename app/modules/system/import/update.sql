/*
    Crée le 17 aout 2011
    Import de données
*/
CREATE TABLE `dims_import_fichier_modele` (
    `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
    `id_globalobject_concerned` INT( 11 ) NOT NULL DEFAULT '0',
    `libelle` TEXT NOT NULL ,
    `tmstp_created` BIGINT( 14 ) NOT NULL ,
    `tmstp_modified` BIGINT( 14 ) NOT NULL ,
    `extension` VARCHAR( 10 ) NOT NULL ,
    PRIMARY KEY ( `id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `dims_import_type_champs_fichier_modele` (
    `id` INT( 11 ) NOT NULL ,
    `libelle` VARCHAR( 120 ) NOT NULL ,
    `php_value` VARCHAR( 120 ) NOT NULL ,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `dims_import_champs_fichier_modele` (
    `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
    `libelle` VARCHAR( 120 ) NOT NULL ,
    `obligatoire` TINYINT( 1 ) NOT NULL ,
    `help_constant` VARCHAR( 120 ) NOT NULL ,
    `id_type_champs` INT( 11 ) NOT NULL DEFAULT '0',
PRIMARY KEY ( `id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `dims_import_correspondance_colonne_champs` (
    `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
    `id_champs` INT( 11 ) NOT NULL DEFAULT '0',
    `id_fichier_modele` INT( 11 ) NOT NULL DEFAULT '0',
    `libelle_colonne` VARCHAR( 120 ) NOT NULL ,
    PRIMARY KEY ( `id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `dims_import` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`timestp_create` BIGINT( 14 ) NOT NULL DEFAULT '0',
`timestp_modify` BIGINT( 14 ) NOT NULL DEFAULT '0',
`id_globalobject_concerned` INT( 11 ) NOT NULL DEFAULT '0',
`id_fichier_modele` INT( 11 ) NOT NULL DEFAULT '0',
`nbelements` INT( 11 ) NOT NULL DEFAULT '0',
`status` TINYINT NOT NULL DEFAULT '0',
`ref_tmp_table` CHAR( 25 ) NOT NULL DEFAULT '',
`id_user` INT( 11 ) NOT NULL DEFAULT '0',
`id_module` INT( 11 ) NOT NULL DEFAULT '0',
`id_workspace` INT( 11 ) NOT NULL DEFAULT '0', 
`comments` TEXT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `dims_import_linked` (
    `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `id_import` INT( 11 ) NOT NULL DEFAULT '0',
    `id_tuple_temp` INT (11) NOT NULL DEFAULT '0',
    `id_object` INT (11) NOT NULL DEFAULT '0',
    `type_object` INT(11) NOT NULL DEFAULT '0'
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

#Aurélien le 19 aout - Attention changement dans le script de création de 
# dims_import_type_champs_fichier_modele

#27/09/2011 by Aurélien
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) 
VALUES  (NULL, 'system', '1', "Démarrer l\'import", '_DIMS_LABEL_IMPORT_START_IMPORT');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) 
VALUES  (NULL, 'system', '1', "Relancer l\'import", '_DIMS_LABEL_IMPORT_RESTART_IMPORT');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) 
VALUES  (NULL, 'system', '1', "Etes-vous sur de vouloir relancer l\'import ?", '_DIMS_LABEL_IMPORT_RESTART_IMPORT_SURE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) 
VALUES  (NULL, 'system', '1', "Consulter les erreurs d\'imports", '_DIMS_LABEL_IMPORT_ERROR_CONSULT');

#02/10/2011 by Aurélien
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) 
VALUES  (NULL, 'system', '1', "Fichier modèle d\'import de fichier", '_DIMS_LABEL_IMPORT_FICHIER_MODEL_SELECT');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) 
VALUES  (NULL, 'system', '1', "Aucun fichier modèle", '_DIMS_LABEL_IMPORT_NO_FICHIER_MODEL_SELECT');
UPDATE `dims_constant` SET `value` = 'Êtes-vous sur de vouloir relancer l`import ?' WHERE `dims_constant`.`phpvalue` = '_DIMS_LABEL_IMPORT_RESTART_IMPORT_SURE' AND `dims_constant`.`id_lang` = '1';


