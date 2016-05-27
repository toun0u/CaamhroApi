##############triggers pour l'enregistrement des suppression##############
DELIMITER //
CREATE TRIGGER histo_api_delete_article BEFORE DELETE ON dims_mod_cata_article
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_delete set id_ligne=old.id, timestp_delete = now()+0, deleted_from = 'articles';
END; //

DELIMITER //
CREATE TRIGGER histo_api_delete_famille_article BEFORE DELETE ON dims_mod_cata_article_famille
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_delete set id_ligne=old.id, timestp_delete = now()+0, deleted_from = 'articles_famille';
END; //

DELIMITER //
CREATE TRIGGER histo_api_delete_cde BEFORE DELETE ON dims_mod_cata_cde
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_delete set id_ligne=old.id_cde, timestp_delete = now()+0, deleted_from = 'commandes';
END; //

DELIMITER //
CREATE TRIGGER histo_api_delete_cde_content BEFORE DELETE ON dims_mod_cata_cde_lignes
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_delete set id_ligne=old.id_cde_ligne, timestp_delete = now()+0, deleted_from = 'commandes_content';
END; //

DELIMITER //
CREATE TRIGGER histo_api_delete_client BEFORE DELETE ON dims_mod_cata_client
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_delete set id_ligne=old.id_client, timestp_delete = now()+0, deleted_from = 'clients';
END; //

DELIMITER //
CREATE TRIGGER histo_api_delete_facture BEFORE DELETE ON dims_mod_cata_facture
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_delete set id_ligne=old.id, timestp_delete = now()+0, deleted_from = 'factures';
END; //

DELIMITER //
CREATE TRIGGER histo_api_delete_facture_det BEFORE DELETE ON dims_mod_cata_facture_det
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_delete set id_ligne=old.id, timestp_delete = now()+0, deleted_from = 'factures_det';
END; //

DELIMITER //
CREATE TRIGGER histo_api_delete_famille BEFORE DELETE ON dims_mod_cata_famille
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_delete set id_ligne=old.id, timestp_delete = now()+0, deleted_from = 'familles';
END; //

DELIMITER //
CREATE TRIGGER histo_api_delete_prixnets BEFORE DELETE ON dims_mod_cata_prix_nets FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_delete set id_tarifs = concat_ws('/', old.type, old.code_cm, old.reference), timestp_delete = now()+0, deleted_from = 'prix_nets';
END; //

DELIMITER //
CREATE TRIGGER histo_api_delete_tarqte BEFORE DELETE ON dims_mod_cata_tarqte FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_delete set id_tarifs = concat_ws('/', old.type, old.code_cm, old.reference), timestp_delete = now()+0, deleted_from = 'tarifs_qte';
END; //

##############fin desTRIGGERs##############

##############create table historique des delete##############

SET NAMES utf8;
SET time_zone = '+00:00';

DROP TABLE IF EXISTS `dims_mod_cata_api_delete`;
CREATE TABLE `dims_mod_cata_api_delete` (
  `id_ligne` int(10) unsigned DEFAULT NULL COMMENT 'id de la ligne supprimée',
  `id_tarifs` varchar(40) DEFAULT NULL COMMENT 'concat de type,code_cm,reference pour les tarifs',
  `timestp_delete` bigint(14) unsigned NOT NULL COMMENT 'timestamp du delete',
  `deleted_from` varchar(20) NOT NULL COMMENT 'table faisant l''objet du delete'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

