##########create table de l'historique des modifs###########

SET NAMES utf8;
SET time_zone = '+00:00';

DROP TABLE IF EXISTS `dims_mod_cata_api_historique`;
CREATE TABLE `dims_mod_cata_api_historique` (
  `id_ligne` int(10) unsigned DEFAULT NULL COMMENT 'id ligne modifiée',
  `id_tarifs` varchar(40) DEFAULT NULL COMMENT 'concat de type,code_cm,reference pour les tarifs',
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `nature_modif` varchar(6) DEFAULT NULL COMMENT 'update ou insert',
  `table_modif` varchar(50) DEFAULT NULL COMMENT 'table sujette à la modif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

##########trigger d'enregistrement d'insert et d'update###########

#####articles
DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_insert_article AFTER INSERT ON dims_mod_cata_article
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id, timestp_modify = now()+0, nature_modif = 'INSERT', table_modif = 'dims_mod_cata_article';
END; //

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_update_article AFTER UPDATE ON dims_mod_cata_article
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id, timestp_modify = now()+0, nature_modif = 'UPDATE', table_modif = 'dims_mod_cata_article';
END; //

#####famille d'article

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_insert_famille AFTER INSERT ON dims_mod_cata_famille 
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id, timestp_modify = now()+0, nature_modif = 'INSERT', table_modif = 'dims_mod_cata_famille';
END; //

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_update_famille AFTER UPDATE ON dims_mod_cata_famille
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id, timestp_modify = now()+0, nature_modif = 'UPDATE', table_modif = 'dims_mod_cata_famille';
END; //

#####clients

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_insert_client AFTER INSERT ON dims_mod_cata_client 
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id_client, timestp_modify = now()+0, nature_modif = 'INSERT', table_modif = 'dims_mod_cata_client';
END; //

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_update_client AFTER UPDATE ON dims_mod_cata_client 
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id_client, timestp_modify = now()+0, nature_modif = 'UPDATE', table_modif = 'dims_mod_cata_client';
END; //

#####relation famille-article

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_insert_article_famille AFTER INSERT ON dims_mod_cata_article_famille
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id, timestp_modify = now()+0, nature_modif = 'INSERT', table_modif = 'dims_mod_cata_article_famille';
END; //

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_update_article_famille AFTER UPDATE ON dims_mod_cata_article_famille
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id, timestp_modify = now()+0, nature_modif = 'UPDATE', table_modif = 'dims_mod_cata_article_famille';
END; //

#####prix nets

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_insert_prix_nets AFTER INSERT ON dims_mod_cata_prix_nets
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_historique set id_tarifs = concat_ws('/', new.type, new.code_cm, new.reference), timestp_modify = now()+0, nature_modif = 'INSERT', table_modif='dims_mod_cata_prix_nets';
#INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id_prix_nets, timestp_modify = now()+0, nature_modif = 'INSERT', table_modif = 'dims_mod_cata_prix_nets';
END; //

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_update_prix_nets AFTER UPDATE ON dims_mod_cata_prix_nets
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_historique set id_tarifs = concat_ws('/', new.type, new.code_cm, new.reference), timestp_modify = now()+0, nature_modif = 'UPDATE', table_modif='dims_mod_cata_prix_nets';
#INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id_prix_nets, timestp_modify = now()+0, nature_modif = 'UPDATE', table_modif = 'dims_mod_cata_prix_nets';
END; //

#####tarifs quantités

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_insert_tarqte AFTER INSERT ON dims_mod_cata_tarqte
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_historique set id_tarifs = concat_ws('/', new.type, new.code_cm, new.reference), timestp_modify = now()+0, nature_modif = 'INSERT', table_modif='dims_mod_cata_tarqte';
#INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id_tarqte, timestp_modify = now()+0, nature_modif = 'INSERT', table_modif = 'dims_mod_cata_tarqte';
END; //

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_update_tarqte AFTER UPDATE ON dims_mod_cata_tarqte
FOR EACH ROW
BEGIN
INSERT INTO dims_mod_cata_api_historique set id_tarifs = concat_ws('/', new.type, new.code_cm, new.reference), timestp_modify = now()+0, nature_modif = 'UPDATE', table_modif='dims_mod_cata_tarqte';
#INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id_tarqte, timestp_modify = now()+0, nature_modif = 'UPDATE', table_modif = 'dims_mod_cata_tarqte';
END; //

#####facture

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_insert_facture AFTER INSERT ON dims_mod_cata_facture
FOR EACH ROW
BEGIN
#INSERT INTO dims_mod_cata_api_historique set id_tarifs = concat_ws('/', new.type, new.code_cm, new.reference), timestp_delete = now()+0, nature_modif = 'INSERT', table_modif='dims_mod_cata_facture';
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id, timestp_modify = now()+0, nature_modif = 'INSERT', table_modif = 'dims_mod_cata_article_facture';
END; //

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_update_facture AFTER UPDATE ON dims_mod_cata_facture
FOR EACH ROW
BEGIN
#INSERT INTO dims_mod_cata_api_historique set id_tarifs = concat_ws('/', new.type, new.code_cm, new.reference), timestp_delete = now()+0, nature_modif = 'UPDATE', table_modif='dims_mod_cata_facture';
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id, timestp_modify = now()+0, nature_modif = 'UPDATE', table_modif = 'dims_mod_cata_article_facture';
END; //

#####détail facture

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_insert_facture_det AFTER INSERT ON dims_mod_cata_facture_det
FOR EACH ROW
BEGIN
#INSERT INTO dims_mod_cata_api_historique set id_tarifs = concat_ws('/', new.type, new.code_cm, new.reference), timestp_delete = now()+0, nature_modif = 'INSERT', table_modif='dims_mod_cata_facture_det';
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id, timestp_modify = now()+0, nature_modif = 'INSERT', table_modif = 'dims_mod_cata_article_facture_det';
END; //

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_update_facture_det AFTER UPDATE ON dims_mod_cata_facture_det
FOR EACH ROW
BEGIN
#INSERT INTO dims_mod_cata_api_historique set id_tarifs = concat_ws('/', new.type, new.code_cm, new.reference), timestp_delete = now()+0, nature_modif = 'UPDATE', table_modif='dims_mod_cata_facture_det';
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id, timestp_modify = now()+0, nature_modif = 'UPDATE', table_modif = 'dims_mod_cata_article_facture_det';
END; //

#####commandes

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_insert_cde AFTER INSERT ON dims_mod_cata_cde
FOR EACH ROW
BEGIN
#INSERT INTO dims_mod_cata_api_historique set id_tarifs = concat_ws('/', new.type, new.code_cm, new.reference), timestp_delete = now()+0, nature_modif = 'INSERT', table_modif='dims_mod_cata_cde';
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id_cde, timestp_modify = now()+0, nature_modif = 'INSERT', table_modif = 'dims_mod_cata_cde';
END; //

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_update_cde AFTER UPDATE ON dims_mod_cata_cde
FOR EACH ROW
BEGIN
#INSERT INTO dims_mod_cata_api_historique set id_tarifs = concat_ws('/', new.type, new.code_cm, new.reference), timestp_delete = now()+0, nature_modif = 'UPDATE', table_modif='dims_mod_cata_cde';
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id_cde, timestp_modify = now()+0, nature_modif = 'UPDATE', table_modif = 'dims_mod_cata_cde';
END; //

#####contenu des commandes

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_insert_cde_lignes AFTER INSERT ON dims_mod_cata_cde_lignes
FOR EACH ROW
BEGIN
#INSERT INTO dims_mod_cata_api_historique set id_tarifs = concat_ws('/', new.type, new.code_cm, new.reference), timestp_delete = now()+0, nature_modif = 'INSERT', table_modif='dims_mod_cata_cde_lignes';
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id_cde_ligne, timestp_modify = now()+0, nature_modif = 'INSERT', table_modif = 'dims_mod_cata_cde_lignes';
END; //

DELIMITER //
CREATE DEFINER=`root`@`localhost` TRIGGER histo_api_update_cde_lignes AFTER UPDATE ON dims_mod_cata_cde_lignes
FOR EACH ROW
BEGIN
#INSERT INTO dims_mod_cata_api_historique set id_tarifs = concat_ws('/', new.type, new.code_cm, new.reference), timestp_delete = now()+0, nature_modif = 'UPDATE', table_modif='dims_mod_cata_cde_lignes';
INSERT INTO dims_mod_cata_api_historique set id_ligne = new.id_cde_ligne, timestp_modify = now()+0, nature_modif = 'UPDATE', table_modif = 'dims_mod_cata_cde_lignes';
END; //

