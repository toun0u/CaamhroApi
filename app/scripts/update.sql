-- Cyril / 03/08/2012 - [GESCOM / Desktop_v2]
DROP TABLE dims_todo;
CREATE TABLE `dims_todo` (	 `id` int(11) NOT NULL AUTO_INCREMENT,	 `type` tinyint(4) NOT NULL DEFAULT '0',   `considered_as` tinyint(1) DEFAULT '0',	 `priority` tinyint(4) NOT NULL DEFAULT '0',   `date` datetime NOT NULL,   `date_validation` datetime DEFAULT NULL,   `is_validator` tinyint(1) DEFAULT '0',   `content` text,	 `user_from` int(11) DEFAULT NULL COMMENT 'expediteur',   `user_to` int(11) DEFAULT NULL COMMENT 'destinataire',   `user_by` int(11) DEFAULT NULL COMMENT 'validé par',   `state` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0',	 `id_parent` int(11) DEFAULT NULL COMMENT 'tache associée',   `timestp_create` bigint(14) DEFAULT '0',	 `timestp_modify` bigint(14) DEFAULT '0',	`id_user` int(11) NOT NULL,   `id_module` int(11) NOT NULL DEFAULT '0',   `id_workspace` int(11) NOT NULL DEFAULT '0',	 `id_globalobject_ref` int(11) DEFAULT '0',   `id_globalobject` int(11) DEFAULT '0',   PRIMARY KEY (`id`),	 KEY `type` (`user_from`),	 KEY `date` (`date`) ) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
DROP TABLE dims_todo_dest;
CREATE TABLE `dims_todo_dest` (   `id` int(11) NOT NULL AUTO_INCREMENT,   `id_todo` int(11) NOT NULL,	`id_user` int(11) NOT NULL,   `flag` tinyint(1) DEFAULT '0',   `type` tinyint(3) DEFAULT '0',	PRIMARY KEY (`id`) ) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Todos', `dims_constant`.`phpvalue` = '_TODOS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Todos', `dims_constant`.`phpvalue` = '_TODOS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Intervenir', `dims_constant`.`phpvalue` = 'COMMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Comment', `dims_constant`.`phpvalue` = 'COMMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Nouvelle intervention', `dims_constant`.`phpvalue` = 'NEW_INTERVENTION';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'New comment', `dims_constant`.`phpvalue` = 'NEW_INTERVENTION';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Créer un todo', `dims_constant`.`phpvalue` = 'NEW_TODO';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Create a todo', `dims_constant`.`phpvalue` = 'NEW_TODO';
UPDATE dims_constant SET value='Créer un todo pour', phpvalue='NEW_TODO' WHERE id_lang=1 AND phpvalue='NEW_TODO';
UPDATE dims_constant SET value='Create a todo for', phpvalue='NEW_TODO' WHERE id_lang=2 AND phpvalue='NEW_TODO';
UPDATE dims_constant SET value='à', phpvalue='_AT' WHERE id_lang=1 AND phpvalue='_AT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'pour', `dims_constant`.`phpvalue` = 'POUR';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'for', `dims_constant`.`phpvalue` = 'POUR';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Aucun commentaire pour l\'instant', `dims_constant`.`phpvalue` = 'NO_COMMENT_FOR_THE_MOMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'No comment for the moment', `dims_constant`.`phpvalue` = 'NO_COMMENT_FOR_THE_MOMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Répondre', `dims_constant`.`phpvalue` = 'ANSWER';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Answer', `dims_constant`.`phpvalue` = 'ANSWER';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Réponse', `dims_constant`.`phpvalue` = 'ANSWER';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Answer', `dims_constant`.`phpvalue` = 'ANSWER';
UPDATE dims_constant SET value='Répondre', phpvalue='ANSWER_ACTION' WHERE id_lang=1 AND phpvalue='ANSWER';
UPDATE dims_constant SET value='Answer', phpvalue='ANSWER_ACTION' WHERE id_lang=2 AND phpvalue='ANSWER';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Tâche à réaliser', `dims_constant`.`phpvalue` = 'TASK_TO_REALIZE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Todo', `dims_constant`.`phpvalue` = 'TASK_TO_REALIZE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Tâche traitée', `dims_constant`.`phpvalue` = 'TODO_VALIDATED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Task done', `dims_constant`.`phpvalue` = 'TODO_VALIDATED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Commentaire de validation', `dims_constant`.`phpvalue` = 'VALIDATION_COMMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Validation comment', `dims_constant`.`phpvalue` = 'VALIDATION_COMMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Aucune tâche pour l\'instant', `dims_constant`.`phpvalue` = 'NO_TASK_FOR_THE_MOMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'No task for the moment', `dims_constant`.`phpvalue` = 'NO_TASK_FOR_THE_MOMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Voir le todo', `dims_constant`.`phpvalue` = 'SEE_THE_TODO';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'See the todo', `dims_constant`.`phpvalue` = 'SEE_THE_TODO';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Voir le message', `dims_constant`.`phpvalue` = 'SEE_THE_MESSAGE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'See the message', `dims_constant`.`phpvalue` = 'SEE_THE_MESSAGE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Aucun message concernant l\'un de vos articles', `dims_constant`.`phpvalue` = 'NO_MESSAGE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'No message for one of your articles', `dims_constant`.`phpvalue` = 'NO_MESSAGE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'smile', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'vous-même', `dims_constant`.`phpvalue` = '_DIMS_LABEL_ASSUR_TASK_YOURSELF';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'smile', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'yourself', `dims_constant`.`phpvalue` = '_DIMS_LABEL_ASSUR_TASK_YOURSELF';

-- Cyril / 06/08/2012 - [GESCOM / Desktop v2]
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Réponse', `dims_constant`.`phpvalue` = 'ANSWER';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Answer', `dims_constant`.`phpvalue` = 'ANSWER';
UPDATE dims_constant SET value='Répondre', phpvalue='ANSWER_ACTION' WHERE id_lang=1 AND phpvalue='ANSWER';
UPDATE dims_constant SET value='Answer', phpvalue='ANSWER_ACTION' WHERE id_lang=2 AND phpvalue='ANSWER';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Edition', `dims_constant`.`phpvalue` = 'SHORT_EDITION';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Edition', `dims_constant`.`phpvalue` = 'SHORT_EDITION';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Ce champ est obligatoire', `dims_constant`.`phpvalue` = 'THIS_FIELD_IS_MANDATORY';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'This field is mandatory', `dims_constant`.`phpvalue` = 'THIS_FIELD_IS_MANDATORY';INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Le format de l\'adresse email est incorrect', `dims_constant`.`phpvalue` = 'WRONG_EMAIL_FORMAT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Wrong email format', `dims_constant`.`phpvalue` = 'WRONG_EMAIL_FORMAT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Veuillez contrôler les valeurs saisies', `dims_constant`.`phpvalue` = 'PLEASE_VERIFY_FIELDS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Please verify fields', `dims_constant`.`phpvalue` = 'PLEASE_VERIFY_FIELDS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Login déjà existant', `dims_constant`.`phpvalue` = 'LOGIN_ALREADY_USED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Login already used', `dims_constant`.`phpvalue` = 'LOGIN_ALREADY_USED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'de la part de', `dims_constant`.`phpvalue` = 'FROM_THE_PART_OF';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'from', `dims_constant`.`phpvalue` = 'FROM_THE_PART_OF';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'vous a assigné une tâche', `dims_constant`.`phpvalue` = 'ASSIGNED_A_TASK_TO_YOU';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'assigned a task to you', `dims_constant`.`phpvalue` = 'ASSIGNED_A_TASK_TO_YOU';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Vous pouvez y accéder en cliquant sur le lien suivant', `dims_constant`.`phpvalue` = 'YOU_CAN_ACCESS_TO_IT_BY_CLICKING_ON';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'You can access this todo by clicking on this link', `dims_constant`.`phpvalue` = 'YOU_CAN_ACCESS_TO_IT_BY_CLICKING_ON';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Dossier', `dims_constant`.`phpvalue` = 'DIMS_CASE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Case', `dims_constant`.`phpvalue` = 'DIMS_CASE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Document commercial', `dims_constant`.`phpvalue` = 'COMMERCIAL_DOCUMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Commercial document', `dims_constant`.`phpvalue` = 'COMMERCIAL_DOCUMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'sur la', `dims_constant`.`phpvalue` = 'ON_THE_FEMININ';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'on the', `dims_constant`.`phpvalue` = 'ON_THE_FEMININ';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'sur la fiche de l\'événement', `dims_constant`.`phpvalue` = 'ON_THE_EVENT_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'on the event record', `dims_constant`.`phpvalue` = 'ON_THE_EVENT_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'sur la fiche de l\'activité', `dims_constant`.`phpvalue` = 'ON_THE_ACTIVITY_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'on the activity record', `dims_constant`.`phpvalue` = 'ON_THE_ACTIVITY_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'sur la fiche de l\'opportunité', `dims_constant`.`phpvalue` = 'ON_THE_OPPORTUNITY_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'on the opportunity record', `dims_constant`.`phpvalue` = 'ON_THE_OPPORTUNITY_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'sur la fiche du contact', `dims_constant`.`phpvalue` = 'ON_THE_CONTACT_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'on the contact record', `dims_constant`.`phpvalue` = 'ON_THE_CONTACT_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'sur la fiche de l\'entreprise', `dims_constant`.`phpvalue` = 'ON_THE_COMPANY_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'on the company record', `dims_constant`.`phpvalue` = 'ON_THE_COMPANY_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'sur la fiche du document', `dims_constant`.`phpvalue` = 'ON_THE_DOCUMENT_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'on the document record', `dims_constant`.`phpvalue` = 'ON_THE_DOCUMENT_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'sur la fiche du dossier', `dims_constant`.`phpvalue` = 'ON_THE_CASE_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'on the case record', `dims_constant`.`phpvalue` = 'ON_THE_CASE_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'sur la fiche du document commercial', `dims_constant`.`phpvalue` = 'ON_THE_COMMERCIAL_DOCUMENT_RECORD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'on the commercial document record', `dims_constant`.`phpvalue` = 'ON_THE_COMMERCIAL_DOCUMENT_RECORD';



-- BEN - 06/08/2012 - desktopv2 / system

ALTER TABLE `dims_tag`
ADD `shared` tinyint(4) NOT NULL DEFAULT '0' AFTER `group`,
COMMENT=''
REMOVE PARTITIONING;

-- BEN / 07/08/2012 - [GESCOM / Desktop_v2]
ALTER TABLE `dims_mod_business_action`
ADD `opportunity_partner_id` int(11) NOT NULL AFTER `opportunity_type_id`,
COMMENT=''
REMOVE PARTITIONING;

ALTER TABLE `dims_mod_business_action`
ADD `opportunity_product_reference` varchar(16) COLLATE 'utf8_general_ci' NOT NULL AFTER `opportunity_partner_id`,
ADD `opportunity_budget` decimal(8,2) unsigned NOT NULL AFTER `opportunity_product_reference`,
COMMENT=''
REMOVE PARTITIONING;

ALTER TABLE `dims_mod_business_produit`
DROP INDEX `reference`,
DROP INDEX `reference_2`;

ALTER TABLE `dims_mod_business_produit`
ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT UNIQUE FIRST,
COMMENT=''
REMOVE PARTITIONING;

ALTER TABLE `dims_mod_business_produit`
ADD PRIMARY KEY `id` (`id`),
ADD UNIQUE `reference_id_workspace` (`reference`, `id_workspace`),
DROP INDEX `PRIMARY`,
DROP INDEX `id`;

INSERT INTO `dims_mod_business_produit` (`id`, `reference`, `libelle`, `libelle_search`, `description`, `prix_achat`, `prix_vente`, `stock`, `id_user`, `id_workspace`, `id_module`) VALUES
('',	'PFD',	'Forfait déplacement',	'',	'',	0,	50,	0,	71,	64,	12),
('',	'DEV',	'Développement',	'',	'',	0,	500,	1,	2,	64,	12),
('',	'DIMSCMS',	'Pack Gestion de Contenu',	'',	'DIMS : plateforme de Gestion de Contenu en ligne développée par NETLOR CONCEPT et incluant les fonctionnalités suivantes :\r\n\r\n- Gestion multi-niveau des rubriques/menus\r\n- Editeur de contenu (pour les pages) avec possibilité d\'inclure du texte enrichi, des photos, des documents, des liens etc...\r\n- Gestion d\'un espace documentaire sur le serveur pour déposer les fichiers à mettre en ligne\r\n- Gestion d\'actualités (intégrable dans le contenu)\r\n- Gestion de formulaires (intégrable dans le contenu) avec envoi possible par email et suivi en ligne avec une base de données\r\n- Gestion de Flux RSS (Co-marquage) permettant l\'intégration automatisée de données d\'un site distant.\r\n\r\nUn template (Interface de site) personnalisé reprenant un maximum d\'éléments graphiques fournis par le client (logos, couleurs) \r\n\r\nLa documentation utilisateur permettant une utilisation optimales des outils fournis',	5000,	5000,	0,	2,	64,	12),
('',	'HEBPRO',	'Hébergement PRO',	'',	'- Serveurs haut de gamme (Bi-Xéon 2,8Ghz - 2Go RAM ECC - RAID SCSI)\r\n- Haute disponibilité : Temps de réponse moyen inférieur à 50ms (30 requêtes / pages)\r\n- Bande passante : 20Mbps garantie / Burst jusqu\'à 42Mbps (réserve de bande passante\r\ndisponible)\r\n- Trafic mensuel illimité (stats consultables en ligne ex : http://www.netlorconcept.com/stats/ login\r\n: stats / mdp : stats)\r\n- Espace disque 500 Mo (ajustable)\r\n- Serveur Miroir temps réel\r\n- Sauvegardes quotidiennes (différentielles) à distance\r\n- Statistiques de visites\r\n- Assistance technique (configuration emails, problème d\'accès, etc...) 5j/7 de 8h30 à 12h30 et de\r\n14h30 à 18h30',	0,	290,	0,	2,	64,	12),
('',	'FORMATION',	'Journée de Formation',	'',	'',	500,	500,	0,	2,	64,	12);

ALTER TABLE `dims_mod_business_action`
CHANGE `opportunity_product_reference` `opportunity_product_id` int(11) unsigned NOT NULL AFTER `opportunity_partner_id`,
COMMENT=''
REMOVE PARTITIONING;

-- CYRIL - 07/08/2012 - desktopv2 / todos

ALTER TABLE `dims_todo_dest` CHANGE `flag` `flag` TINYINT(1)  NULL	DEFAULT '0'  COMMENT 'permet de le mettre de côté';
ALTER TABLE `dims_todo_dest` ADD `validated` TINYINT(1)  NULL  DEFAULT '0'	AFTER `flag`;
ALTER TABLE `dims_todo_dest` CHANGE `validated` `validated` TINYINT(1)	NULL  DEFAULT '0'  COMMENT 'indique si le destinataire a accusé la réception du todo';
ALTER TABLE `dims_todo_dest` ADD `date_validation` DATETIME  NULL  AFTER `validated`;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Requiert la validation de chaque destinataire', `dims_constant`.`phpvalue` = 'VALIDATION_OF_EVERY_ONE_REQUIRED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Require the validation of every recipient', `dims_constant`.`phpvalue` = 'VALIDATION_OF_EVERY_ONE_REQUIRED';

-- BEN - 08/08/2012 - desktopv2 / leads

ALTER TABLE `dims_mod_business_action`
ADD `status` tinyint(1) unsigned NULL DEFAULT '0' AFTER `description`,
COMMENT=''
REMOVE PARTITIONING;

-- CYRIL - 08/08/2012
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Vous n\'avez aucune tâche en cours', `dims_constant`.`phpvalue` = 'NO_TASK_TO_DO';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'You don\'t have any task to do', `dims_constant`.`phpvalue` = 'NO_TASK_TO_DO';

-- Simon - 16/08/2012 - gescatalogue system param
ALTER TABLE `dims_param_default`
ADD `id_domain` int(10) unsigned NOT NULL AFTER `id_module_type`,
COMMENT=''
REMOVE PARTITIONING;

-- BEN - 07/09/2012 - desktopv2 / leads
ALTER TABLE `dims_alerts`
ADD `protocol` varchar(10) COLLATE 'utf8_general_ci' NOT NULL AFTER `nb_period`,
ADD `domain` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `protocol`,
COMMENT=''
REMOVE PARTITIONING;

-- CYRIL - 10/09/2012
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Enregistrer l\'entreprise', `dims_constant`.`phpvalue` = 'SAVE_COMPANY';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Save company', `dims_constant`.`phpvalue` = 'SAVE_COMPANY';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'et continuer', `dims_constant`.`phpvalue` = 'AND_CONTINUE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'and continue', `dims_constant`.`phpvalue` = 'AND_CONTINUE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Ajoutez la', `dims_constant`.`phpvalue` = 'ADD_IT_LA';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Add it', `dims_constant`.`phpvalue` = 'ADD_IT_LA';

-- -- CYRIL - 11/09/2012
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'L\'entreprise a été créée avec succès', `dims_constant`.`phpvalue` = 'COMPANY_CREATED_WITH_SUCCESS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'The company has been created with success', `dims_constant`.`phpvalue` = 'COMPANY_CREATED_WITH_SUCCESS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Cet intitulé est déjà utilisé pour l\'entreprise', `dims_constant`.`phpvalue` = 'COMPANY_ALREADY_IN_THE_DATABASE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'This title is already used by the company', `dims_constant`.`phpvalue` = 'COMPANY_ALREADY_IN_THE_DATABASE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Cette entreprise est peut-être déjà dans la base', `dims_constant`.`phpvalue` = 'MAYBE_THIS_COMPANY_EXISTS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Perhaps this company is already in the database', `dims_constant`.`phpvalue` = 'MAYBE_THIS_COMPANY_EXISTS';


-- BEN _ 11/09/2102
INSERT INTO `dims_module_workspace` (`id_module`, `id_workspace`, `position`, `blockposition`, `visible`, `autoconnect`)
VALUES ('1', '0', '1', NULL, '1', '0');

INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '43', 'Créer une activité', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '44', 'Voir ses activités', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '45', 'Voir les activités des autres utilisateurs', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '46', 'Modifier ses activités', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '47', 'Modifier les activités des autres utilisateurs', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '48', 'Annuler ses activités', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '49', 'Annuler les activités des autres utilisateurs', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '50', 'Supprimer ses activités', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '51', 'Supprimer les activités des autres utilisateurs', NULL, '0', '0');

INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '52', 'Créer une opportunité', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '53', 'Voir ses opportunités', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '54', 'Voir les opportunités des autres utilisateurs', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '55', 'Modifier ses opportunités', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '56', 'Modifier les opportunités des autres utilisateurs', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '57', 'Annuler ses opportunités', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '58', 'Annuler les opportunités des autres utilisateurs', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '59', 'Supprimer ses opportunités', NULL, '0', '0');
INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`)
VALUES ('1', '60', 'Supprimer les opportunités des autres utilisateurs', NULL, '0', '0');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Modifier l\'affectation des rôles', '_MODIFY_ROLE_ASSIGNMENT'),
('', 'system', 2, 'Modify role assignment', '_MODIFY_ROLE_ASSIGNMENT');

-- Simon - 11/09/2012 - intranet / sarenov - (from ben)
DROP TABLE IF EXISTS `dims_alerts`;
CREATE TABLE `dims_alerts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mode` tinyint(1) unsigned NOT NULL,
  `timestp_alert` bigint(14) unsigned NOT NULL,
  `period` char(1) NOT NULL DEFAULT '',
  `nb_period` int(11) unsigned NOT NULL DEFAULT '0',
  `protocol` varchar(10) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `timestp_create` bigint(14) unsigned NOT NULL,
  `timestp_modify` bigint(14) unsigned NOT NULL,
  `id_user` int(11) unsigned NOT NULL,
  `id_module` int(11) unsigned NOT NULL,
  `id_workspace` int(11) unsigned NOT NULL,
  `id_globalobject` int(11) unsigned NOT NULL DEFAULT '0',
  `id_go_origin` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `timestp_alert` (`timestp_alert`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `dims_mod_business_params` ADD `id_workspace` int unsigned NULL AFTER `value` ,
COMMENT = ''REMOVE PARTITIONING;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Types d''activité', `dims_constant`.`phpvalue` = 'ACTIVITY_TYPE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Activity types', `dims_constant`.`phpvalue` = 'ACTIVITY_TYPE';

-- Thomas -- 12/09/2012 --
UPDATE `dims_mb_field` SET `id_module_type`=1 WHERE `tablename` like 'dims_mod_business_action';
UPDATE	`dims_mb_table` SET  `id_module_type` =  '1' WHERE	`dims_mb_table`.`name` =  'dims_mod_business_action' AND  `dims_mb_table`.`id_module_type` =18;
UPDATE	`dims_mb_object` SET  `script` = NULL , `id_module_type` =	'1' WHERE  `dims_mb_object`.`id` =10 AND  `dims_mb_object`.`id_module_type` =18 LIMIT 1;

-- Simon - 12/09/2012 - intranet / sarenov
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL,	'system',	1,	'Nouveau type d''activité',	'NEW_ACTIVITY_TYPE'),
(NULL,	'system',	2,	'New activity type',	'NEW_ACTIVITY_TYPE');

-- BEN - 13/09/2012 - intranet
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL,	'system',	1,	'Créer une nouvelle entreprise',	'CREATE_NEW_COMPANY'),
(NULL,	'system',	2,	'Create a new company',	'CREATE_NEW_COMPANY');

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Choisissez une entreprise', `dims_constant`.`phpvalue` = '_DIMS_SELECT_A_COMPANY';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Select a company', `dims_constant`.`phpvalue` = '_DIMS_SELECT_A_COMPANY';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Enregistrer le contact', `dims_constant`.`phpvalue` = '_DIMS_LABEL_SAVE_CONTACT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Save contact', `dims_constant`.`phpvalue` = '_DIMS_LABEL_SAVE_CONTACT';

-- BEN - 14/09/2102 - GESCOM
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Ce contact est peut-être déjà dans la base',	'MAYBE_THIS_CONTACT_EXISTS'),
('',	'system',	2,	'Perhaps this contact is already in the database',	'MAYBE_THIS_CONTACT_EXISTS');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Créer un nouveau contact',	'CREATE_NEW_CONTACT'),
('',	'system',	2,	'Create a new contact',	'CREATE_NEW_CONTACT');

-- Thomas -- 14/09/2012 --
ALTER TABLE dims_mod_business_suivi_detail DROP INDEX id;
ALTER TABLE dims_mod_business_suivi_detail DROP INDEX id_2;
ALTER TABLE  `dims_mod_business_suivi_detail` ADD  `id_globalobject` INT( 11 ) NOT NULL DEFAULT  '0';
UPDATE `dims_mod_business_suivi_detail` SET `id_module`=1 WHERE id_module = 0;
UPDATE	`dims_mb_table` SET  `id_module_type` =  '1' WHERE	`dims_mb_table`.`name` =  'dims_mod_business_suivi' AND  `dims_mb_table`.`id_module_type` =14;
UPDATE	`dims_mb_table` SET  `id_module_type` =  '1' WHERE	`dims_mb_table`.`name` =  'dims_mod_business_suivi_detail' AND	`dims_mb_table`.`id_module_type` =14;
INSERT INTO  `dims_mb_object` (`id` ,`label` ,`script` ,`id_module_type`) VALUES
('31',	'Suivi', NULL ,  '1'),
('34',	'Suivi detail', NULL ,	'1');
INSERT INTO  `dims_mb_field` (`id` ,`tablename` ,`name` ,`label` ,`type` ,`visible` ,`id_module_type` ,`id_object` ,`indexed` ,`protected` ,`level`)VALUES
(NULL ,  'dims_mod_business_suivi',  'id_globalobject',  'id_globalobject',  'int(10) unsigned',  '1',	'1',  '0',	'0',  '1',	'1'),
(NULL ,  'dims_mod_business_suivi_detail',	'id_globalobject',	'id_globalobject',	'int(10) unsigned',  '1',  '1',  '0',  '0',  '1',  '1'),
(NULL ,  'dims_mod_business_suivi',  'id_workspace',  'id_workspace',  'int(10) unsigned',	'1',  '1',	'0',  '0',	'1',  '1'),
(NULL ,  'dims_mod_business_suivi_detail',	'id_user',	'id_user',	'int(10) unsigned',  '1',  '1',  '0',  '0',  '1',  '1'),
(NULL ,  'dims_mod_business_suivi_detail',	'id_module',  'id_module',	'int(10) unsigned',  '1',  '1',  '0',  '0',  '1',  '1'),
(NULL ,  'dims_mod_business_suivi_detail',	'id_workspace',  'id_workspace',  'int(10) unsigned',  '1',  '1',  '0',  '0',  '1',  '1');
UPDATE	`dims_mb_table` SET  `label` =	'dims_mod_business_suivi' WHERE  `dims_mb_table`.`name` =  'dims_mod_business_suivi' AND  `dims_mb_table`.`id_module_type` =1;
UPDATE	`dims_mb_table` SET  `label` =	'dims_mod_business_suivi_detail' WHERE	`dims_mb_table`.`name` =  'dims_mod_business_suivi_detail' AND	`dims_mb_table`.`id_module_type` =1;
DELETE FROM `dims_mb_field` WHERE `dims_mb_field`.`id` = 592;
UPDATE `dims_mb_field` SET `id_object`=31 WHERE `tablename` LIKE 'dims_mod_business_suivi';
UPDATE `dims_mb_field` SET `id_object`=34 WHERE `tablename` LIKE 'dims_mod_business_suivi_detail';
UPDATE	`dims_mb_field` SET  `label` =	'_TYPE' WHERE  `dims_mb_field`.`id` =578;
UPDATE	`dims_mb_field` SET  `label` =	'_DUTY' WHERE  `dims_mb_field`.`id` =579;
UPDATE	`dims_mb_field` SET  `label` =	'_DIMS_LABEL_LABEL' WHERE  `dims_mb_field`.`id` =580;
UPDATE	`dims_mb_field` SET  `label` =	'_DIMS_LABEL_DESCRIPTION' WHERE  `dims_mb_field`.`id` =581;
UPDATE	`dims_mb_field` SET  `label` =	'_TYPE' WHERE  `dims_mb_field`.`id` =596;
UPDATE	`dims_mb_field` SET  `label` =	'_DUTY' WHERE  `dims_mb_field`.`id` =597;
UPDATE	`dims_mb_field` SET  `label` =	'_DIMS_LABEL_GROUP_CODE' WHERE	`dims_mb_field`.`id` =598;
UPDATE	`dims_mb_field` SET  `label` =	'_DIMS_LABEL_LABEL' WHERE  `dims_mb_field`.`id` =599;
UPDATE	`dims_mb_field` SET  `label` =	'_DIMS_LABEL_DESCRIPTION' WHERE  `dims_mb_field`.`id` =600;


--- CYRIL - 19/09/2012
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Pour les éléments sélectionnés', `dims_constant`.`phpvalue` = '_FOR_SELECTED_ITEM';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'For selected items', `dims_constant`.`phpvalue` = '_FOR_SELECTED_ITEM';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Envoyer les contacts par email', `dims_constant`.`phpvalue` = '_INET_SEND_CONTACTS_EMAIL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Send the contacts by email', `dims_constant`.`phpvalue` = '_INET_SEND_CONTACTS_EMAIL';

/* 14:16:05  root@127.0.0.1 */ ALTER TABLE `dims_mod_business_contact` ADD `account_id` INT(11)  NULL  DEFAULT '0'	AFTER `id_country`;

-- Thomas -- 27/09/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Import Excel', `dims_constant`.`phpvalue` = '_EXCEL_FILE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Excel import', `dims_constant`.`phpvalue` = '_EXCEL_FILE';
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'assurance', '1', 'Gestion des imports et des synchronisations', '_DIMS_LABEL_IMPORT_MANAGEMENT'), (NULL, 'assurance', '2', 'Import management', '_DIMS_LABEL_IMPORT_MANAGEMENT');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'Gestion des mod&egrave;les', '_DIMS_LABEL_MANAGE_MODEL'), (NULL, 'system', '2', 'Templates management', '_DIMS_LABEL_MANAGE_MODEL');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'enregistrements par page', '_DIMS_LABEL_RECORD_BY_PAGE'), (NULL, 'system', '2', 'records per page', '_DIMS_LABEL_RECORD_BY_PAGE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'enregistrement(s)', '_DIMS_LABEL_RECORDS'), (NULL, 'system', '2', 'record(s)', '_DIMS_LABEL_RECORDS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'sur', '_DIMS_LABEL_OF'), (NULL, 'system', '2', 'of', '_DIMS_LABEL_OF');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'enregistrements', '_DIMS_LABEL_RECORDS'), (NULL, 'system', '2', 'records', '_DIMS_LABEL_RECORDS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'total', '_DIMS_LABEL_TOTAL'), (NULL, 'system', '2', 'total', '_DIMS_LABEL_TOTAL');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'filtre de', '_DIMS_LABEL_FILTERED_FROM'), (NULL, 'system', '2', 'filtered from', '_DIMS_LABEL_FILTERED_FROM');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'Premier', '_FIRST'), (NULL, 'system', '2', 'First', '_FIRST');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'Pr&eacute;c&eacute;dent', '_PREVIOUS'), (NULL, 'system', '2', 'Previous', '_PREVIOUS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'Suivant', '_NEXT'), (NULL, 'system', '2', 'Next', '_NEXT');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'Dernier', '_LAST'), (NULL, 'system', '2', 'Last', '_LAST');
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Liste des contacts', `dims_constant`.`phpvalue` = '_LIST_OF_CONTACTS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'List of the contacts', `dims_constant`.`phpvalue` = '_LIST_OF_CONTACTS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Nb. éléments', `dims_constant`.`phpvalue` = '_NB_ELEMENTS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Nb. elements', `dims_constant`.`phpvalue` = '_NB_ELEMENTS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Nb. importés', `dims_constant`.`phpvalue` = '_NB_IMPORTED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Nb. imported', `dims_constant`.`phpvalue` = '_NB_IMPORTED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Nb. erreurs', `dims_constant`.`phpvalue` = '_NB_ERRORS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Nb. errors', `dims_constant`.`phpvalue` = '_NB_ERRORS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Erreur', `dims_constant`.`phpvalue` = '_ERROR';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Error', `dims_constant`.`phpvalue` = '_ERROR';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Garder les données d\'origine', `dims_constant`.`phpvalue` = '_KEEP_DATA_ORIGIN';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Keep the data of origin', `dims_constant`.`phpvalue` = '_KEEP_DATA_ORIGIN';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Utiliser les données de l\'import', `dims_constant`.`phpvalue` = '_USE_DATA_OF_IMPORT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Use the data of the import', `dims_constant`.`phpvalue` = '_USE_DATA_OF_IMPORT';
DROP TABLE IF EXISTS `dims_import_check_fields`;
CREATE TABLE IF NOT EXISTS `dims_import_check_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_fields` varchar(255) NOT NULL,
  `id_mt_field` int(11) NOT NULL,
  `type_obj` int(11) NOT NULL,
  `nb_used` int(11) NOT NULL DEFAULT '1',
  `id_user` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_fields` (`name_fields`,`id_mt_field`,`type_obj`)
) ENGINE=MyISAM DEFAULT CHARSET=ucs2 AUTO_INCREMENT=1 ;

-- BEN - 26/09/2012 - scribus
INSERT INTO `dims_module_type` (`label`, `system`, `publicparam`, `description`, `version`, `author`, `date`, `contenttype`)
VALUES ('Scribus', '0', '1', 'Scribus catalog generator', '2.0', 'Netlor', '20120926175500', 'scribus');

-- BEN - 27/09/2012 - scribus
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'scribus', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Gestion des catalogues', `dims_constant`.`phpvalue` = '_SCRIBUS_CATALOGS_MANAGEMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'scribus', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Catalogs management', `dims_constant`.`phpvalue` = '_SCRIBUS_CATALOGS_MANAGEMENT';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'scribus', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Gestion des modèles', `dims_constant`.`phpvalue` = '_SCRIBUS_MODELS_MANAGEMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'scribus', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Models management', `dims_constant`.`phpvalue` = '_SCRIBUS_MODELS_MANAGEMENT';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'scribus', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Paramètres', `dims_constant`.`phpvalue` = '_SCRIBUS_PARAMETERS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'scribus', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Parameters', `dims_constant`.`phpvalue` = '_SCRIBUS_PARAMETERS';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'scribus', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Scribus / DIMS', `dims_constant`.`phpvalue` = '_SCRIBUS_DIMS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'scribus', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Scribus / DIMS', `dims_constant`.`phpvalue` = '_SCRIBUS_DIMS';

-- BEN - 03/10/2012 - gescom - proposition de rendez-vous
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Proposer un rendez vous', `dims_constant`.`phpvalue` = '_DIMS_OFFER_APPOINTMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Offer an appointment', `dims_constant`.`phpvalue` = '_DIMS_OFFER_APPOINTMENT';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Gestion des propositions de rendez-vous', `dims_constant`.`phpvalue` = '_SYSTEM_MANAGE_APPOINTMENT_OFFER';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Appointment offers management', `dims_constant`.`phpvalue` = '_SYSTEM_MANAGE_APPOINTMENT_OFFER';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Nouvelle proposition de rendez-vous', `dims_constant`.`phpvalue` = 'NEW_APPOINTMENT_OFFER';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'New appointment offer', `dims_constant`.`phpvalue` = 'NEW_APPOINTMENT_OFFER';

ALTER TABLE `dims_matrix`
CHANGE `id_activity` `id_activity` int(11) NOT NULL DEFAULT '0' AFTER `id_opportunity`,
ADD `id_appointment_offer` int(11) NOT NULL DEFAULT '0' AFTER `id_activity`,
COMMENT='';

-- Thomas -- 04/10/2012 --
ALTER TABLE  `dims_mod_business_action` ADD  `ref` VARCHAR( 255 ) NOT NULL DEFAULT	'' AFTER  `id`;

-- Simon - 04/10/2012 - gescom(sharefile)
DROP TABLE IF EXISTS `dims_mod_wce_article_doc`;
CREATE TABLE IF NOT EXISTS `dims_mod_wce_article_doc` (
  `id_article` int(11) NOT NULL,
  `id_doc` int(11) NOT NULL,
  PRIMARY KEY (`id_article`,`id_doc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Êtes vous sûr de vouloir supprimer cet élément ?', `dims_constant`.`phpvalue` = 'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Are you sure you want to delete this element ?', `dims_constant`.`phpvalue` = 'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?';

-- Thomas -- 05/10/2012 --
DROP TABLE IF EXISTS `dims_mod_business_action_reponse`;
CREATE TABLE IF NOT EXISTS `dims_mod_business_action_reponse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `go_appointment` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `timestp` bigint(14) NOT NULL,
  `id_contact` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
DROP TABLE IF EXISTS `dims_mod_business_action_reponse_value`;
CREATE TABLE IF NOT EXISTS `dims_mod_business_action_reponse_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_appointment` int(11) NOT NULL,
  `presence` tinyint(1) NOT NULL DEFAULT '0',
  `id_reponse` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
ALTER TABLE  `dims_mod_business_contact` ADD  `ref` VARCHAR( 255 ) NOT NULL DEFAULT  '' AFTER  `id`;
UPDATE `dims_mod_business_contact` SET `ref`= md5(concat(id, UNIX_TIMESTAMP())) WHERE 1;

-- Thomas -- 09/10/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Date retenue', `dims_constant`.`phpvalue` = '_RESTAINT_DATE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Restaint date', `dims_constant`.`phpvalue` = '_RESTAINT_DATE';

-- Thomas -- 10/10/2012 -- Demandes Thierry WCE --
ALTER TABLE  `dims_mod_wce_slideshow_element` ADD  `color` VARCHAR( 10 ) NOT NULL DEFAULT  '';
ALTER TABLE  `dims_mod_wce_article` ADD  `color` VARCHAR( 10 ) NOT NULL DEFAULT  '';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Proposition de rendez-vous envoyée aux participants !', `dims_constant`.`phpvalue` = '_APPOINTMENT_SAVE_OK';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Proposal of appointment sent to participants !', `dims_constant`.`phpvalue` = '_APPOINTMENT_SAVE_OK';
ALTER TABLE  `dims_mod_business_action` ADD  `private` BOOLEAN NOT NULL DEFAULT  '0';

-- Thomas -- 11/10/2012 --
ALTER TABLE  `dims_workspace` ADD  `email_appointment` VARCHAR( 255 ) NOT NULL DEFAULT	'',
ADD  `title_appointment` TEXT NOT NULL DEFAULT	'',
ADD  `content_appointment` LONGTEXT NOT NULL DEFAULT  '';
ALTER TABLE  `dims_workspace` ADD  `validation_appointment` LONGTEXT NOT NULL DEFAULT  '';
ALTER TABLE  `dims_workspace` ADD  `title_appointment_val` TEXT NOT NULL AFTER	`content_appointment`;

-- Simon - gescom (sharefile) - 08/10/2012
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Vous n''êtes pas ou plus connnecté.',	'YOU_ARE_NOT_IDENTIFIED'),
('',	'system',	2,	'You are not identified.',	'YOU_ARE_NOT_IDENTIFIED');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Merci de vous identifier.',	'PLEASE_LOG_IN'),
('',	'system',	2,	'Please log in.',	'PLEASE_LOG_IN');


-- Simon - gescom (sharefile) - 09/10/2012
ALTER TABLE `dims_mod_business_contact`
ADD `private` tinyint unsigned NOT NULL DEFAULT '0' AFTER `id`,
COMMENT=''
REMOVE PARTITIONING;


-- Simon - gescom (sharefile) - 15/10/012
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Erreur d''accès.',	'ACCESS_ERROR'),
('',	'system',	2,	'Access error.',	'ACCESS_ERROR');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'La date de publication a été dépassé.',	'ERROR_SHARE_MAXDATE'),
('',	'system',	2,	'The publication date has been exceeded.',	'ERROR_SHARE_MAXDATE');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Ce partage a été supprimé.',	'ERROR_SHARE_DELETED'),
('',	'system',	2,	'this share has been deleted.',	'ERROR_SHARE_DELETED');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Nombre maximum de téléchargement atteint.',	'ERROR_SHARE_MAXDOWNLOAD'),
('',	'system',	2,	'Maximum download reached.',	'ERROR_SHARE_MAXDOWNLOAD');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Erreur de code.',	'CODE_CHECK_ERROR'),
('',	'system',	2,	'Code error.',	'CODE_CHECK_ERROR');

-- Ben - 16/10/2012 - gescom (scribus)
ALTER TABLE `dims_mod_scribus_catalogue`
ADD `type` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `nbpages`;

ALTER TABLE `dims_mod_scribus_catalogue`
ADD `racine` int(11) unsigned NOT NULL DEFAULT '0' AFTER `type`;

ALTER TABLE `dims_mod_scribus_catalogue`
CHANGE `label` `label` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `id`;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'scribus', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Normal', `dims_constant`.`phpvalue` = 'SCRIBUS_CATA_TYPE_NORMAL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'scribus', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Normal', `dims_constant`.`phpvalue` = 'SCRIBUS_CATA_TYPE_NORMAL';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'scribus', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Famille', `dims_constant`.`phpvalue` = 'SCRIBUS_CATA_TYPE_FAMILLE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'scribus', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Family', `dims_constant`.`phpvalue` = 'SCRIBUS_CATA_TYPE_FAMILLE';

-- Trex - 18/10/2012 - Sharefile

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Mes contacts ajoutés', `dims_constant`.`phpvalue` = 'MES_CONTACTS_AJOUTES';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'My contacts added', `dims_constant`.`phpvalue` = 'MES_CONTACTS_AJOUTES';

-- Simon - gescom (sharefile) - 17/10/2012
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Nouveau partage.',	'NEW_SHARE'),
('',	'system',	2,	'New sharing.',	'NEW_SHARE');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Gestion des contacts.',	'CONTACTS_MANAGEMENT'),
('',	'system',	2,	'Contacts management.',	'CONTACTS_MANAGEMENT');

-- Ben - 17/10/2012 - gescom (scribus)

ALTER TABLE `dims_mod_scribus_model`
ADD `type` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `filename`,
ADD `nb_elements` int(11) unsigned NOT NULL DEFAULT '0' AFTER `type`;

DROP TABLE IF EXISTS `dims_mod_scribus_famille`;
CREATE TABLE `dims_mod_scribus_famille` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) CHARACTER SET utf8 NOT NULL,
  `mode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `nb_elements` int(11) NOT NULL DEFAULT '0',
  `nb_pages` int(11) NOT NULL DEFAULT '0',
  `page_debut` int(11) NOT NULL DEFAULT '0',
  `timestp_create` bigint(14) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `dims_mod_scribus_famille`
DROP `nb_elements`,
CHANGE `page_debut` `start_page` int(11) NOT NULL DEFAULT '0' AFTER `nb_pages`,
ADD `active` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `start_page`,
ADD `locked` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `active`;

ALTER TABLE `dims_mod_scribus_famille`
ADD `id_modele` int(11) unsigned NOT NULL DEFAULT '0' AFTER `locked`;

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Partager un document.',	'SHARE_DOCUMENT'),
('',	'system',	2,	'Share a document.',	'SHARE_DOCUMENT');

-- Simon - gescom (sharefile) - 18/10/2012
ALTER TABLE `dims_mod_business_contact`
ADD `id_tiers` int(10) unsigned NOT NULL AFTER `id`,
COMMENT=''
REMOVE PARTITIONING;


-- Simon - gescom (sharefile) - 19/10/2012
ALTER TABLE `dims_mod_sharefile_share`
CHANGE `id` `id` int(11) unsigned NOT NULL AUTO_INCREMENT FIRST,
ADD `id_entity` int(11) unsigned NOT NULL DEFAULT '0' AFTER `id`,
COMMENT=''
REMOVE PARTITIONING;

ALTER TABLE `dims_matrix`
ADD `id_share` int(11) unsigned NOT NULL DEFAULT '0' AFTER `id_wce_article2`,
COMMENT=''
REMOVE PARTITIONING;

ALTER TABLE `dims_mod_sharefile_share`
ADD `id_globalobject` int(10) unsigned NOT NULL DEFAULT '0' AFTER `nbdoc`,
COMMENT=''
REMOVE PARTITIONING;

-- Ben - 23/10/2012 - gescom
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Ajoutez-le', `dims_constant`.`phpvalue` = 'ADD_IT_LE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Add it', `dims_constant`.`phpvalue` = 'ADD_IT_LE';

UPDATE `dims_constant` SET `value` = 'Planification des rendez-vous' WHERE `phpvalue` = '_SYSTEM_MANAGE_APPOINTMENT_OFFER' AND `id_lang` = 1 LIMIT 1;
UPDATE `dims_constant` SET `value` = 'Nouveau compte' WHERE `phpvalue` = '_IMPORT_TAB_NEW_COMPANY' AND `id_lang` = 1 LIMIT 1;
UPDATE `dims_constant` SET `value` = 'Enregistrer le compte' WHERE `phpvalue` = 'SAVE_COMPANY' AND `id_lang` = 1 LIMIT 1;
UPDATE `dims_constant` SET `value` = 'Compte' WHERE `phpvalue` = '_DIMS_LABEL_COMPANY' AND `id_lang` = 1 LIMIT 1;
UPDATE `dims_constant` SET `value` = 'Choisissez un compte' WHERE `phpvalue` = '_DIMS_SELECT_A_COMPANY' AND `id_lang` = 1 LIMIT 1;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Compte parent', `dims_constant`.`phpvalue` = '_DIMS_LABEL_PARENT_COMPANY';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Parent company', `dims_constant`.`phpvalue` = '_DIMS_LABEL_PARENT_COMPANY';

-- Ben - 23/10/2012 - DOODLE
DROP TABLE IF EXISTS `dims_mod_business_action_notification`;
CREATE TABLE `dims_mod_business_action_notification` (
  `id_action` int(10) unsigned NOT NULL,
  `id_contact` int(10) unsigned NOT NULL,
  UNIQUE KEY `id_action_id_contact` (`id_action`,`id_contact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Ben - 25/10/2012 - DOODLE
UPDATE `dims_constant` SET `value` = 'Proposer un rendez-vous' WHERE `phpvalue` = '_DIMS_OFFER_APPOINTMENT' AND `id_lang` = 1 LIMIT 1;

-- Thomas -- 29/10/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Aucune date ne me convient', `dims_constant`.`phpvalue` = '_NO_DATE_FOR_ME';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'No date for me', `dims_constant`.`phpvalue` = '_NO_DATE_FOR_ME';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Un participant a répondu', `dims_constant`.`phpvalue` = '_ONE_PARTICIPANT_RESPONDED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'One participant responded', `dims_constant`.`phpvalue` = '_ONE_PARTICIPANT_RESPONDED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Aucune date ne convient', `dims_constant`.`phpvalue` = '_NO_DATE_SHOULD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'No date should', `dims_constant`.`phpvalue` = '_NO_DATE_SHOULD';
ALTER TABLE  `dims_workspace` ADD  `title_appointment_rep` TEXT NOT NULL DEFAULT  '',
ADD  `content_appointment_rep` LONGTEXT NOT NULL DEFAULT  '';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Je participe au RDV', `dims_constant`.`phpvalue` = '_I_PARTICIPE_APPOINTMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'I participate in the appointment', `dims_constant`.`phpvalue` = '_I_PARTICIPE_APPOINTMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Toutes la journée', `dims_constant`.`phpvalue` = '_ALL_DAY';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'All day', `dims_constant`.`phpvalue` = '_ALL_DAY';


-- Ben - 29/10/2102 - DOODLE
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Envoyer un rappel', `dims_constant`.`phpvalue` = '_DIMS_APPOINTMENT_OFFER_SEND_REMINDER';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Send a reminder', `dims_constant`.`phpvalue` = '_DIMS_APPOINTMENT_OFFER_SEND_REMINDER';

ALTER TABLE `dims_workspace`
ADD `title_appointment_remind` text COLLATE 'utf8_general_ci' NOT NULL,
ADD `content_appointment_remind` longtext COLLATE 'utf8_general_ci' NOT NULL AFTER `title_appointment_remind`;

-- Cyril - 29/10/2012 - SYSTEM
-- Cyril 29/06 / Gestion de l'accès back / front d'un dimsuser

-- Ben - 30/10/2012 - DOODLE
UPDATE `dims_constant` SET `value` = 'Toute la journée' WHERE `phpvalue` = '_ALL_DAY' AND `id_lang` = 1 LIMIT 1;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Vous devez renseigner votre nom', `dims_constant`.`phpvalue` = '_APP_OFFER_NAME_OBLIGATORY';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'You must enter your name', `dims_constant`.`phpvalue` = '_APP_OFFER_NAME_OBLIGATORY';



-- Pat - 31/10/2012 - Inégration des events avec inscription
UPDATE dims_constant SET value='Evénements avec inscription', phpvalue='_DIMS_PLANNING_FAIR_STEPS' WHERE id_lang=1 AND phpvalue='_DIMS_PLANNING_FAIR_STEPS';
UPDATE dims_constant SET value='Events with subscriptions', phpvalue='_DIMS_PLANNING_FAIR_STEPS' WHERE id_lang=2 AND phpvalue='_DIMS_PLANNING_FAIR_STEPS';


-- Pat 05/11/2012 -- constante
UPDATE dims_constant SET value='Liste des emails rattaches', phpvalue='_DIMS_LABEL_MAILING_EMAIL_LIST' WHERE id_lang=1 AND phpvalue='_DIMS_LABEL_MAILING_EMAIL_LIST';

-- Pat 06/11/2012 -- alter table pour ajout du lien avec la gestion de contenu
ALTER TABLE `dims_mod_newsletter_content` ADD `id_article` INT NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_wce_article` ADD `template` VARCHAR( 255 ) NOT NULL DEFAULT '';


-- Pat 12/11/2012
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Newsletters attachées à', `dims_constant`.`phpvalue` = 'NEWSLETTERS_ATTACH_TO';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Newsletters attached to', `dims_constant`.`phpvalue` = 'NEWSLETTERS_ATTACH_TO';

-- Cyril 05/11/2012 - ADMIN DES MODELES DE DOC POUR LES SUIVIS
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Modèles d\'impression', `dims_constant`.`phpvalue` = 'PRINTING_MODELS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Printing models', `dims_constant`.`phpvalue` = 'PRINTING_MODELS';

CREATE TABLE `dims_mod_business_suivi_type` (id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT) DEFAULT CHARACTER SET `utf8` ENGINE = `MyISAM`;
ALTER TABLE `dims_mod_business_suivi_type` ADD `label` VARCHAR(255)  NOT NULL  DEFAULT ''  AFTER `id`;
ALTER TABLE `dims_mod_business_suivi_type` ADD `public` TINYINT(1)	NOT NULL  DEFAULT '0'  AFTER `label`;
ALTER TABLE `dims_mod_business_suivi_type` ADD `status` TINYINT(1)	NOT NULL  DEFAULT '0'  AFTER `public`;
ALTER TABLE `dims_mod_business_suivi_type` ADD `id_user` INT(11)  NULL	DEFAULT '0'  AFTER `status`;
ALTER TABLE `dims_mod_business_suivi_type` ADD `id_module` INT(11)	NULL  DEFAULT '0'  AFTER `id_user`;
ALTER TABLE `dims_mod_business_suivi_type` ADD `id_workspace` INT(11)  NULL  DEFAULT NULL  AFTER `id_module`;
ALTER TABLE `dims_mod_business_suivi_type` ADD `timestp_create` BIGINT(14)	NULL  DEFAULT '0'  AFTER `status`;
ALTER TABLE `dims_mod_business_suivi_type` ADD `timestp_modify` INT(11)  NULL  DEFAULT '0'	AFTER `id_workspace`;
ALTER TABLE `dims_mod_business_suivi_type` CHANGE `id_workspace` `id_workspace` INT(11)  NULL  DEFAULT '0';
ALTER TABLE `dims_mod_business_suivi_type` MODIFY COLUMN `timestp_modify` INT(11) DEFAULT '0' AFTER `timestp_create`;
ALTER TABLE `dims_mod_business_suivi_type` CHANGE `timestp_modify` `timestp_modify` BIGINT(14)	NULL  DEFAULT '0';


CREATE TABLE `dims_mod_business_print_model` (id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT) DEFAULT CHARACTER SET `utf8` ENGINE = `MyISAM`;
ALTER TABLE `dims_mod_business_print_model` ADD `label` VARCHAR(255)  NULL	DEFAULT NULL  AFTER `id`;
ALTER TABLE `dims_mod_business_print_model` ADD `description` TEXT	NULL  AFTER `label`;
ALTER TABLE `dims_mod_business_print_model` ADD `id_type` INT(11)  NOT NULL  DEFAULT '0'  AFTER `description`;
ALTER TABLE `dims_mod_business_print_model` ADD `id_doc` INT(11)  NOT NULL	DEFAULT '0'  AFTER `id_type`;
ALTER TABLE `dims_mod_business_print_model` MODIFY COLUMN `id_type` INT(11) NOT NULL DEFAULT '0' AFTER `id`;
ALTER TABLE `dims_mod_business_print_model` MODIFY COLUMN `id_doc` INT(11) NOT NULL DEFAULT '0' AFTER `id_type`;
ALTER TABLE `dims_mod_business_print_model` ADD `timestp_create` BIGINT(14)  NULL  DEFAULT '0'	AFTER `description`;
ALTER TABLE `dims_mod_business_print_model` ADD `timestp_modify` BIGINT(14)  NULL  DEFAULT '0'	AFTER `timestp_create`;
ALTER TABLE `dims_mod_business_print_model` ADD `id_user` INT(11)  NULL  DEFAULT '0'  AFTER `timestp_modify`;
ALTER TABLE `dims_mod_business_print_model` ADD `id_module` INT(11)  NULL  DEFAULT '0'	AFTER `id_user`;
ALTER TABLE `dims_mod_business_print_model` ADD `id_workspace` INT(11)	NULL  DEFAULT '0'  AFTER `id_module`;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Nouveau modèle', `dims_constant`.`phpvalue` = 'NEW_MODEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'New model', `dims_constant`.`phpvalue` = 'NEW_MODEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Aucun modèle n\'a été défini pour ce module', `dims_constant`.`phpvalue` = 'NO_MODEL_FOR_THIS_WORKSPACE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'No model for this workspace', `dims_constant`.`phpvalue` = 'NO_MODEL_FOR_THIS_WORKSPACE';
UPDATE dims_constant SET value='Télécharger', phpvalue='_DIMS_DOWNLOAD' WHERE id_lang=1 AND phpvalue='_DIMS_DOWNLOAD';
UPDATE dims_constant SET value='Aucun modèle n\'a été défini pour cet espace de travail', phpvalue='NO_MODEL_FOR_THIS_WORKSPACE' WHERE id_lang=1 AND phpvalue='NO_MODEL_FOR_THIS_WORKSPACE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Liste de vos modèles d\'impression', `dims_constant`.`phpvalue` = 'PRINTING_MODELS_LIST';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'List of your printing models', `dims_constant`.`phpvalue` = 'PRINTING_MODELS_LIST';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Edition du modèle', `dims_constant`.`phpvalue` = 'MODEL_EDITION';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Editing the model', `dims_constant`.`phpvalue` = 'MODEL_EDITION';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Enregistrer ce modèle', `dims_constant`.`phpvalue` = 'SAVE_THIS_MODEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Save this model', `dims_constant`.`phpvalue` = 'SAVE_THIS_MODEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Veuillez sélectionner un type', `dims_constant`.`phpvalue` = 'PLEASE_SELECT_A_TYPE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Please select a type', `dims_constant`.`phpvalue` = 'PLEASE_SELECT_A_TYPE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Modèle', `dims_constant`.`phpvalue` = 'MODEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Model', `dims_constant`.`phpvalue` = 'MODEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Une erreur est survenue. Veuillez recommencer s\'il vous plaît.', `dims_constant`.`phpvalue` = 'ERROR_THROWN';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'An error has been thrown. Please try again.', `dims_constant`.`phpvalue` = 'ERROR_THROWN';

-- Ben - 06/11/2012 - gescom (fiche tiers)
INSERT INTO `dims_mb_field` (`id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES ('74', 'mel', 'email', 'varchar', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES ('74', 'adresse2', 'adresse2', 'varchar', '1', '1', '1', '1');
INSERT INTO `dims_mb_field` (`id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES ('74', 'adresse3', 'adresse3', 'varchar', '1', '1', '1', '1');
UPDATE `dims_mod_business_meta_field` SET `id_mbfield` = '988' WHERE `id` = '120' COLLATE utf8_bin AND `id_object` = '6' LIMIT 1;
UPDATE `dims_mod_business_meta_field` SET `id_mbfield` = '989' WHERE `id` = '121' COLLATE utf8_bin AND `id_object` = '6' LIMIT 1;
UPDATE `dims_mod_business_meta_field` SET `id_mbfield` = '990' WHERE `id` = '122' COLLATE utf8_bin AND `id_object` = '6' LIMIT 1;

-- Ben - 13/11/2012 - gescom (fiche tiers)
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Ajouter des contacts et entreprises', `dims_constant`.`phpvalue` = '_ADDING_CONTACT_COMPANIES';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Adding contacts and companies', `dims_constant`.`phpvalue` = '_ADDING_CONTACT_COMPANIES';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Rechercher avant de créer', `dims_constant`.`phpvalue` = '_SEARCH_BEFORE_CREATING';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Search before creating', `dims_constant`.`phpvalue` = '_SEARCH_BEFORE_CREATING';

-- Ben - 14/11/2012 - gescom (fiche tiers)
ALTER TABLE `dims_mod_business_tiers` ADD `share_suivi` tinyint(1) unsigned NULL;
INSERT INTO `dims_mb_field` (`id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`)
VALUES ('74', 'share_suivi', 'Partage des suivis', 'tinyint', '1', '0', '1', '1');
INSERT INTO `dims_mod_business_meta_field` (`id_object`, `name`, `fieldname`, `separator`, `separator_level`, `separator_fontsize`, `type`, `format`, `values`, `description`, `position`, `maxlength`, `cols`, `option_needed`, `option_search`, `option_arrayview`, `option_exportview`, `option_cmsgroupby`, `option_cmsorderby`, `option_cmsdisplaylabel`, `option_cmsshowfilter`, `defaultvalue`, `interline`, `id_metacateg`, `used`, `id_mbfield`, `enum`, `mode`)
VALUES ('6', NULL, '0', '0', '0', '0', 'radio', 'string', NULL, NULL, '4', '0', '0', '0', '0', '1', '1', '0', '0', '0', '0', NULL, '0', '2', '1', '991', NULL, '');
ALTER TABLE `dims_mod_business_tiers`
CHANGE `share_suivi` `share_suivi` char(2) COLLATE 'utf8_general_ci' NULL AFTER `field1`;

-- Cyril - 17/12/2012 --
ALTER TABLE `dims_mb_object_relation` ADD `extended_indexation` TINYINT(1)	NULL  DEFAULT '0'  AFTER `data_type`;

-- Thomas -- 19/12/2012 --
ALTER TABLE `dims_tag` ADD `id_module` INT( 11 ) NOT NULL DEFAULT '0' AFTER `id_user` ;
ALTER TABLE `dims_tag` ADD `timestp_modify` BIGINT( 20 ) NOT NULL DEFAULT '0' AFTER `id_workspace` ,
ADD `timestp_create` BIGINT( 20 ) NOT NULL DEFAULT '0' AFTER `timestp_modify` ,
ADD `id_globalobject` INT( 11 ) NOT NULL DEFAULT '0' AFTER `timestp_create` ;
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(null, 'wce', 1, 'Nouvel article', '_NEW_ARTICLE'),
(null, 'wce', 2, 'New article', '_NEW_ARTICLE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(null, 'wce', 1, 'Edition d''un article', '_EDIT_ARTICLE'),
(null, 'wce', 2, 'Edit article', '_EDIT_ARTICLE');


-- Pat 10/01/2013 --
ALTER TABLE `dims_user` CHANGE `password` `password` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `dims_user` ADD `salt` CHAR( 32 ) NOT NULL DEFAULT '' AFTER `login`;
;

-- Pat 13/01/2013 --
ALTER TABLE `dims_mod_wce_article_block` ADD `class` VARCHAR( 150 ) NOT NULL DEFAULT '' AFTER `position_style` ;

-- Pat 24/01/2013 --
ALTER TABLE `dims_mod_business_contact` ADD `id_folder` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_business_tiers` ADD `id_folder` INT( 11 ) NOT NULL DEFAULT '0';

INSERT INTO `dims_workspace_group` (`id_group`, `id_workspace`, `id_profile`, `adminlevel`, `activesearch`, `activeticket`, `activeprofil`, `activeannot`, `activecontact`, `activeproject`, `activeplanning`, `activenewsletter`, `activeevent`, `activeeventstep`, `activeeventemail`) VALUES
(3, 97, 6, 10, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0),
(4, 97, 8, 10, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0),
(5, 97, 7, 10, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0),
(6, 97, 9, 10, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0),
(3, 97, 0, 99, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0);


INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Ajouter un dossier', `dims_constant`.`phpvalue` = '_ADD_FOLDER';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Add folder', `dims_constant`.`phpvalue` = '_ADD_FOLDER';


#06/02/2013
ALTER TABLE `dims_mod_forms` ADD `accesscode` VARCHAR( 100 ) NOT NULL DEFAULT '';
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(null, 'forms', 1, 'Code d''accès', 'ACCESS_CODE'),
(null, 'forms', 2, 'Access code', 'ACCESS_CODE');

-- Cyril 15/03/2013
UPDATE `dims_mb_object` SET `id` = '340' WHERE `id` = '2' AND `id_module_type` = '22';

-- Simon - 03/04/2012 - applicam
ALTER TABLE `dims_mod_applicam_abonnement`			RENAME TO `dims_mod_reservation_abonnement` ;
ALTER TABLE `dims_mod_applicam_activite`			RENAME TO `dims_mod_reservation_activite` ;
ALTER TABLE `dims_mod_applicam_article`				RENAME TO `dims_mod_reservation_article` ;
ALTER TABLE `dims_mod_applicam_article_activite`	RENAME TO `dims_mod_reservation_article_activite` ;
ALTER TABLE `dims_mod_applicam_compte`				RENAME TO `dims_mod_reservation_compte` ;
ALTER TABLE `dims_mod_applicam_creneau`				RENAME TO `dims_mod_reservation_creneau` ;
ALTER TABLE `dims_mod_applicam_days`				RENAME TO `dims_mod_reservation_days` ;
ALTER TABLE `dims_mod_applicam_fmi`					RENAME TO `dims_mod_reservation_fmi` ;
ALTER TABLE `dims_mod_applicam_import`				RENAME TO `dims_mod_reservation_import` ;
ALTER TABLE `dims_mod_applicam_import_error`		RENAME TO `dims_mod_reservation_import_error` ;
ALTER TABLE `dims_mod_applicam_inscription`			RENAME TO `dims_mod_reservation_inscription` ;
ALTER TABLE `dims_mod_applicam_message`				RENAME TO `dims_mod_reservation_message` ;
ALTER TABLE `dims_mod_applicam_params`				RENAME TO `dims_mod_reservation_params` ;
ALTER TABLE `dims_mod_applicam_repeat`				RENAME TO `dims_mod_reservation_repeat` ;
ALTER TABLE `dims_mod_applicam_repeat_line`			RENAME TO `dims_mod_reservation_repeat_line` ;
ALTER TABLE `dims_mod_applicam_reservation`			RENAME TO `dims_mod_reservation_reservation` ;
ALTER TABLE `dims_mod_applicam_session`				RENAME TO `dims_mod_reservation_session` ;
ALTER TABLE `dims_mod_applicam_trainer`				RENAME TO `dims_mod_reservation_trainer` ;
ALTER TABLE `dims_mod_applicam_type`				RENAME TO `dims_mod_reservation_type` ;
ALTER TABLE `dims_mod_applicam_type_week`			RENAME TO `dims_mod_reservation_type_week` ;
ALTER TABLE `dims_mod_applicam_users`				RENAME TO `dims_mod_reservation_users` ;
ALTER TABLE `dims_mod_applicam_weeks`				RENAME TO `dims_mod_reservation_weeks` ;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'PLANNING', `dims_constant`.`value` = 'Planning';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'PLANNING', `dims_constant`.`value` = 'Planning';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'ADMINISTRATION', `dims_constant`.`value` = 'Administration';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'ADMINISTRATION', `dims_constant`.`value` = 'Administration';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'STATISTICS', `dims_constant`.`value` = 'Statistiques';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'STATISTICS', `dims_constant`.`value` = 'Statistics';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'SYNCHRONIZATION', `dims_constant`.`value` = 'Synchronisation';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'SYNCHRONIZATION', `dims_constant`.`value` = 'Synchronization';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'ACTIVITY_TYPES', `dims_constant`.`value` = 'Types d''activité';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'ACTIVITY_TYPES', `dims_constant`.`value` = 'Activity types';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'WEEK_TYPES', `dims_constant`.`value` = 'Types de semaine';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'WEEK_TYPES', `dims_constant`.`value` = 'Week types';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'WEEKS_PLANNING', `dims_constant`.`value` = 'Planning des semaines';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'WEEKS_PLANNING', `dims_constant`.`value` = 'Weeks planning';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'USERS', `dims_constant`.`value` = 'Utilisateurs';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'USERS', `dims_constant`.`value` = 'Users';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'SUBSCRIBERS_ACCOUNT', `dims_constant`.`value` = 'Comptes abonnés';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'SUBSCRIBERS_ACCOUNT', `dims_constant`.`value` = 'Subscribers account';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'ARTICLES_AND_ACTIVITIES', `dims_constant`.`value` = 'Articles & activités';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'ARTICLES_AND_ACTIVITIES', `dims_constant`.`value` = 'Articles & activities';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'GENERALS_SETTINGS', `dims_constant`.`value` = 'Paramètre généraux';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'GENERALS_SETTINGS', `dims_constant`.`value` = 'Generals settings';

-- Simon - Reservation (Applicam) - 12/04/2013
CREATE TABLE IF NOT EXISTS `dims_mod_reservation_abonnement` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `compte_id` int(11) unsigned NOT NULL,
  `article_id` int(11) NOT NULL,
  `type_article` tinyint(1) unsigned NOT NULL,
  `date_debut_abo` bigint(14) NOT NULL DEFAULT '0',
  `date_limite_abo` bigint(14) unsigned DEFAULT '0',
  `quantite` int(11) unsigned DEFAULT '0',
  `timestp_create` bigint(14) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  `id_user` int(11) unsigned NOT NULL DEFAULT '0',
  `id_module` int(11) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(11) unsigned NOT NULL DEFAULT '0',
  `need_synchro` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_activite` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `label` text,
  `description` text,
  `state` tinyint(1) NOT NULL,
  `color` varchar(8) DEFAULT NULL,
  `isgroup` tinyint(4) NOT NULL DEFAULT '0',
  `is_certif_required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cout_default` int(11) unsigned DEFAULT '0',
  `trainer_id_default` int(11) unsigned DEFAULT NULL,
  `place_default` int(11) unsigned DEFAULT '0',
  `place_web_default` int(11) unsigned DEFAULT '0',
  `id_doc` int(11) DEFAULT NULL,
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_globalobject` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) DEFAULT '0' COMMENT 'permet de faire la jointure avec les articles de la caisse',
  `label` varchar(250) DEFAULT NULL,
  `isgroup` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `prix` float(8,2) DEFAULT NULL,
  `duree` int(11) unsigned DEFAULT NULL,
  `type_duree` tinyint(1) NOT NULL COMMENT 'jour / mois / année',
  `debut_validite` bigint(14) NOT NULL DEFAULT '0',
  `type_debut_validite` tinyint(1) NOT NULL DEFAULT '0' COMMENT '- jour de la vente\n- date fixe (dans ce cas on prend la valeur du champ debut_validite)\n- 1ère utilisation\n- date à saisir (mais nous on gère pas, c''est pour la caissière)',
  `quantite` int(11) unsigned DEFAULT NULL,
  `state` tinyint(1) DEFAULT '0',
  `multiple` tinyint(1) DEFAULT '0',
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_user` int(11) unsigned NOT NULL DEFAULT '0',
  `id_module` int(11) unsigned DEFAULT '0',
  `id_workspace` int(11) unsigned NOT NULL DEFAULT '0',
  `need_synchro` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_UNIQUE` (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_article_activite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `activite_id` int(11) unsigned NOT NULL,
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_user` int(11) unsigned NOT NULL DEFAULT '0',
  `id_module` int(11) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `a1` (`article_id`),
  KEY `a2` (`activite_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_compte` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_external` int(11) unsigned NOT NULL DEFAULT '0',
  `inscription_id` int(11) unsigned DEFAULT NULL,
  `id_user_link` int(11) DEFAULT NULL,
  `login` varchar(32) DEFAULT NULL,
  `password` varchar(48) DEFAULT NULL,
  `card_number` varchar(20) DEFAULT NULL,
  `credit` int(11) unsigned NOT NULL DEFAULT '0',
  `date_limite_certif` bigint(14) unsigned DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `civilite` varchar(10) DEFAULT NULL,
  `sexe` varchar(10) DEFAULT NULL,
  `nationalite` varchar(25) DEFAULT NULL,
  `address` text,
  `postalcode` varchar(16) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `mobile` varchar(32) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `birthdate` bigint(14) DEFAULT '0',
  `isgroup` tinyint(1) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `password_code` varchar(15) DEFAULT NULL,
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_globalobject` int(10) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `need_synchro` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `inscription1` (`inscription_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_creneau` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trainer_id` int(10) unsigned DEFAULT NULL,
  `activite_id` int(11) unsigned NOT NULL,
  `repeat_id` int(11) unsigned NOT NULL DEFAULT '0',
  `timestp_deb` bigint(14) NOT NULL DEFAULT '0',
  `timestp_fin` bigint(14) NOT NULL DEFAULT '0',
  `id_action` int(10) unsigned NOT NULL DEFAULT '0',
  `label` text,
  `description` text,
  `placemax` int(10) unsigned NOT NULL DEFAULT '0',
  `place_web` int(10) unsigned NOT NULL DEFAULT '0',
  `prix` int(10) unsigned NOT NULL DEFAULT '1',
  `state` int(10) NOT NULL DEFAULT '0',
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `nb_reservation` int(11) NOT NULL DEFAULT '0',
  `nb_reservation_web` int(11) NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_globalobject` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `trainer1` (`trainer_id`),
  KEY `activite1` (`activite_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_days` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_week` int(11) unsigned DEFAULT NULL,
  `id_type_week` int(11) unsigned DEFAULT NULL,
  `timestp_day_deb` int(11) unsigned DEFAULT NULL,
  `timestp_day_end` int(11) unsigned DEFAULT NULL,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `INDEX` (`id_week`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_fmi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fmi_piscine` float(6,2) NOT NULL DEFAULT '0.00',
  `fmi_balneo` float(6,2) NOT NULL DEFAULT '0.00',
  `fmi_activites` float(6,2) NOT NULL DEFAULT '0.00',
  `timestp_create` bigint(14) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_import` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_deb` bigint(14) DEFAULT NULL,
  `date_fin` bigint(14) DEFAULT NULL,
  `state` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_import_error` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `import_id` int(11) NOT NULL,
  `table` varchar(250) NOT NULL,
  `id_elem` int(11) NOT NULL,
  `type` varchar(1) DEFAULT NULL,
  `code` tinyint(2) DEFAULT NULL,
  `state` tinyint(1) DEFAULT '1',
  `timestp_create` bigint(14) DEFAULT NULL,
  `timestp_modify` bigint(14) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_module` int(11) DEFAULT NULL,
  `id_workspace` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_inscription` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lastname` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `civilite` varchar(10) DEFAULT NULL,
  `sexe` varchar(1) DEFAULT NULL,
  `birthdate` bigint(14) NOT NULL DEFAULT '0',
  `nationalite` varchar(25) DEFAULT NULL,
  `address` text,
  `postalcode` varchar(16) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `mobile` varchar(32) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `integre` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'indique s''il a été intégré par la base elisath',
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_user` int(11) unsigned NOT NULL DEFAULT '0',
  `id_module` int(11) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(11) unsigned NOT NULL DEFAULT '0',
  `need_synchro` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_compte` int(10) unsigned DEFAULT NULL,
  `subject` text,
  `content` text,
  `read` int(10) unsigned DEFAULT NULL,
  `timestp_modify` int(10) unsigned NOT NULL DEFAULT '0',
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_globalobject` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_params` (
  `param` varchar(50) NOT NULL DEFAULT '',
  `value` varchar(5) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_globalobject` int(11) NOT NULL,
  PRIMARY KEY (`param`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_repeat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activite_id` int(10) unsigned NOT NULL DEFAULT '0',
  `trainer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `placemax` int(10) unsigned NOT NULL DEFAULT '0',
  `place_web` int(10) unsigned NOT NULL DEFAULT '0',
  `prix` int(10) unsigned NOT NULL DEFAULT '0',
  `nb_repeat` int(10) unsigned NOT NULL DEFAULT '0',
  `type_repeat` int(10) unsigned NOT NULL DEFAULT '0',
  `date_deb_repeat` int(10) unsigned NOT NULL DEFAULT '0',
  `date_end_repeat` int(10) unsigned NOT NULL DEFAULT '0',
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_user` int(11) unsigned NOT NULL DEFAULT '0',
  `id_module` int(11) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_repeat_line` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_repeat` int(10) unsigned NOT NULL DEFAULT '0',
  `week_day` int(10) unsigned NOT NULL DEFAULT '0',
  `heure_deb` tinyint(2) NOT NULL DEFAULT '0',
  `minute_deb` tinyint(2) NOT NULL DEFAULT '0',
  `heure_end` tinyint(2) NOT NULL DEFAULT '0',
  `minute_end` tinyint(2) NOT NULL DEFAULT '0',
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_user` int(11) unsigned NOT NULL DEFAULT '0',
  `id_module` int(11) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_reservation` (
  `source_id` int(11) unsigned NOT NULL,
  `ref` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `compte_id` int(11) unsigned NOT NULL,
  `creneau_id` int(11) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `state` int(10) unsigned DEFAULT NULL,
  `article_id_used` int(11) unsigned DEFAULT NULL,
  `abonnement_id_used` int(11) unsigned DEFAULT NULL,
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_globalobject` int(10) unsigned NOT NULL DEFAULT '0',
  `timestp_zone1` bigint(14) DEFAULT '0',
  `timestp_zone2` bigint(14) DEFAULT '0',
  `need_synchro` tinyint(4) DEFAULT '0',
  `resa_imme` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`source_id`,`ref`),
  KEY `compte1` (`compte_id`),
  KEY `session1` (`creneau_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='dims_mod_applicam_reservation';


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_session` (
  `site_id` int(11) unsigned NOT NULL,
  `ref` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_site_id` int(11) unsigned NOT NULL,
  `type_ref` int(11) unsigned NOT NULL DEFAULT '0',
  `id_action` int(10) unsigned NOT NULL DEFAULT '0',
  `trainer_site_id` int(11) unsigned NOT NULL DEFAULT '0',
  `trainer_ref` int(11) unsigned NOT NULL,
  `label` text,
  `description` text,
  `placemax` int(10) unsigned NOT NULL DEFAULT '0',
  `place_web` int(10) unsigned NOT NULL DEFAULT '0',
  `prix` int(10) unsigned NOT NULL DEFAULT '1',
  `state` int(10) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_globalobject` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`site_id`,`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_trainer` (
  `site_id` int(11) unsigned NOT NULL,
  `ref` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user_link` int(10) unsigned NOT NULL DEFAULT '0',
  `id_user` int(11) unsigned NOT NULL,
  `id_module` int(11) unsigned NOT NULL,
  `id_workspace` int(11) unsigned NOT NULL,
  PRIMARY KEY (`site_id`,`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_type` (
  `site_id` int(11) unsigned NOT NULL,
  `ref` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `label` text,
  `status` tinyint(1) NOT NULL,
  `color` varchar(8) DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_globalobject` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`site_id`,`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_type_week` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` text,
  `initiale` text,
  `color` varchar(8) DEFAULT NULL,
  `is_default` int(1) unsigned DEFAULT '0',
  `state` int(1) DEFAULT NULL,
  `blocking` int(1) unsigned DEFAULT '0',
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_user` int(11) unsigned NOT NULL,
  `id_module` int(11) unsigned NOT NULL,
  `id_workspace` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user_link` int(10) unsigned NOT NULL DEFAULT '0',
  `initiale` text,
  `state` tinyint(1) NOT NULL,
  `password_temp` varchar(48) DEFAULT NULL,
  `timestp_create` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_user` int(11) unsigned NOT NULL,
  `id_module` int(11) unsigned NOT NULL,
  `id_workspace` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_weeks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_type_week` int(10) unsigned DEFAULT NULL,
  `timestp_week_deb` int(11) unsigned DEFAULT NULL,
  `timestp_week_end` int(11) unsigned DEFAULT NULL,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `week` (`id_type_week`),
  CONSTRAINT `fk_dims_mod_applicam_type_weektype_week1` FOREIGN KEY (`id_type_week`) REFERENCES `dims_mod_reservation_type_week` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dims_mod_reservation_etablissement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` tinyint(1) unsigned DEFAULT '0',
  `label` varchar(34) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `address_cpl` varchar(50) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dims_mod_reservation_etablissement_workspace` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_etablissement` int(10) unsigned ,
  `id_workspacelinked` int(10) unsigned ,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `dims_mod_reservation_creneau`
ADD `etablissement_id` int(11) unsigned NOT NULL AFTER `repeat_id`,
COMMENT='';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'ESTABLISHMENT', `dims_constant`.`value` = 'Établissement';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'ESTABLISHMENT', `dims_constant`.`value` = 'Establishment';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'SELECT_ESTABLISHMENT', `dims_constant`.`value` = 'Selectionnez un établissement';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'SELECT_ESTABLISHMENT', `dims_constant`.`value` = 'Select an establishment';

CREATE TABLE IF NOT EXISTS `dims_mod_reservation_etablissement_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_etablissement` int(10) unsigned ,
  `id_article` int(10) unsigned ,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'EXERCICES', `dims_constant`.`value` = 'Exercices';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'EXERCICES', `dims_constant`.`value` = 'Exercices';

CREATE TABLE IF NOT EXISTS `dims_mod_reservation_exercice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(25) ,
  `timestamp_start` bigint(14) unsigned DEFAULT NULL,
  `timestamp_end` bigint(14) unsigned DEFAULT NULL,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'DATE_START_IS_MANDATORY', `dims_constant`.`value` = 'La date de début est obligatoire.';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'DATE_START_IS_MANDATORY', `dims_constant`.`value` = 'Date start is mandatory.';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'DATE_END_IS_MANDATORY', `dims_constant`.`value` = 'La date de fin est obligatoire.';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'DATE_END_IS_MANDATORY', `dims_constant`.`value` = 'Date end is mandatory.';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'EXERCICES', `dims_constant`.`value` = 'Exercices';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'EXERCICES', `dims_constant`.`value` = 'Exercices';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'BACK_TO_THE_LIST', `dims_constant`.`value` = 'Retour à la liste';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'BACK_TO_THE_LIST', `dims_constant`.`value` = 'Back to the list';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'EXERCICE', `dims_constant`.`value` = 'Exercice';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'EXERCICE', `dims_constant`.`value` = 'Exercice';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'ADD_NEW_EXERCICE', `dims_constant`.`value` = 'Ajouter un nouvel exercice';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'ADD_NEW_EXERCICE', `dims_constant`.`value` = 'Add a new exercice';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'LABEL', `dims_constant`.`value` = 'Libellé';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'LABEL', `dims_constant`.`value` = 'Label';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'DATE_START', `dims_constant`.`value` = 'Date de début';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'DATE_START', `dims_constant`.`value` = 'Date start';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'DATE_END', `dims_constant`.`value` = 'Date de fin';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'DATE_END', `dims_constant`.`value` = 'Date end';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'CREATION_DATE', `dims_constant`.`value` = 'Date de création';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'CREATION_DATE', `dims_constant`.`value` = 'Creation date';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'OVERLAP_OTHER_EXERCICE', `dims_constant`.`value` = 'Conflit avec un autre exercice.';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'OVERLAP_OTHER_EXERCICE', `dims_constant`.`value` = 'Overlap other exercice.';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'NO_EXERCICE', `dims_constant`.`value` = 'Aucun exercice.';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'NO_EXERCICE', `dims_constant`.`value` = 'No exercice';

ALTER TABLE `dims_mod_reservation_creneau`
ADD `exercice_id` int(11) unsigned NOT NULL AFTER `etablissement_id`,
COMMENT='';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'N/A', `dims_constant`.`value` = 'n/a';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'N/A', `dims_constant`.`value` = 'n/a';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'ATTACH_TO_EXERCICE', `dims_constant`.`value` = 'Attacher à l''exercice';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'ATTACH_TO_EXERCICE', `dims_constant`.`value` = 'Attach to exercice';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'ATTACH_TO_EXERCICE_IF_ANY', `dims_constant`.`value` = 'Attacher à l''exercice éventuel';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'ATTACH_TO_EXERCICE_IF_ANY', `dims_constant`.`value` = 'Attach to exercice if any';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'EXERCICES', `dims_constant`.`value` = 'Exercices';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'EXERCICES', `dims_constant`.`value` = 'Exercices';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'ADD_NEW_EXERCICE', `dims_constant`.`value` = 'Ajouter un nouvel exercice';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'ADD_NEW_EXERCICE', `dims_constant`.`value` = 'Add new exercice';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'ACTIONS', `dims_constant`.`value` = 'Actions';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'ACTIONS', `dims_constant`.`value` = 'Actions';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'EXERCICE', `dims_constant`.`value` = 'Exercice';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'EXERCICE', `dims_constant`.`value` = 'Exercice';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'COPY_SESSIONS_FROM_EXERCICE', `dims_constant`.`value` = 'Copier les sessions de l''exercice';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'COPY_SESSIONS_FROM_EXERCICE', `dims_constant`.`value` = 'Copy sessions from exercice';

ALTER TABLE `dims_mod_reservation_etablissement`
ADD `numetab` int(10) unsigned NULL COMMENT 'external id' AFTER `id`,
ADD `streetnum` varchar(25) COLLATE 'utf8_general_ci' NULL AFTER `description`,
ADD `btq` varchar(25) COLLATE 'utf8_general_ci' NULL AFTER `streetnum`,
ADD `city` varchar(50) COLLATE 'utf8_general_ci' NULL AFTER `zipcode`,
ADD `country` varchar(50) COLLATE 'utf8_general_ci' NULL AFTER `city`,
ADD `type` varchar(50) COLLATE 'utf8_general_ci' NULL AFTER `phone`,
COMMENT='';

CREATE TABLE IF NOT EXISTS `dims_mod_reservation_payment_method` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `numpayment` int(10) unsigned NULL COMMENT 'external id',
  `label` varchar(25) ,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Thomas -- 10/06/2013 --
DROP TABLE IF EXISTS `dims_mod_business_address_type`;
CREATE TABLE IF NOT EXISTS `dims_mod_business_address_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  `id_globalobject` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `dims_mod_business_address_type` ADD  `blocked` BOOLEAN NOT NULL DEFAULT  '1' AFTER  `label`;
INSERT INTO `dims_mod_business_address_type` (`id`, `label`, `blocked`, `id_user`, `id_module`, `id_workspace`, `id_globalobject`) VALUES
('', '_PERSO', 1, 65, 1, 64, 0),
('', '_PRO', 1, 65, 1, 64, 0);
ALTER TABLE  `dims_mod_business_address_type` ADD  `is_active` BOOLEAN NOT NULL DEFAULT  '1' AFTER	`blocked`;
ALTER TABLE  `dims_mod_business_address` ADD  `id_city` INT( 11 ) NOT NULL DEFAULT	'0' AFTER  `city`;

-- Thomas -- 13/06/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Nouvelle entreprise', '_NEW_COMPANY'),
('', 'system', 2, 'New company', '_NEW_COMPANY');

-- Thomas -- 17/06/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Non classée', '_UNCATEGORIZED_FEM'),
('', 'system', 2, 'Uncategorized', '_UNCATEGORIZED_FEM');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Non classé', '_UNCATEGORIZED'),
('', 'system', 2, 'Uncategorized', '_UNCATEGORIZED');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Ajouter un service', '_ADD_SERVICE'),
('', 'system', 2, 'Add a service', '_ADD_SERVICE');

-- Thomas -- 24/06/2013 --
DROP TABLE IF EXISTS `dims_tag_category_object`;
CREATE TABLE IF NOT EXISTS `dims_tag_category_object` (
  `id_tag` int(11) NOT NULL,
  `id_object` int(11) NOT NULL,
  UNIQUE KEY `id_tag` (`id_tag`,`id_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `dims_mod_reservation_article`
ADD `numarticle` int(10) unsigned NULL COMMENT 'external id' AFTER `id`,
ADD `code` int(10) unsigned NULL AFTER `numarticle`,
ADD `longlabel` varchar(250) COLLATE 'utf8_general_ci' NULL AFTER `label`,
ADD `labeltva` varchar(250) COLLATE 'utf8_general_ci' NULL AFTER `longlabel`,
ADD `tva` float unsigned COLLATE 'utf8_general_ci' NULL AFTER `labeltva`,
ADD `sellminqty` int(10) unsigned NULL AFTER `multiple`,
ADD `sellmaxqty` int(10) unsigned NULL AFTER `sellminqty`,
COMMENT='';

CREATE TABLE IF NOT EXISTS `dims_mod_reservation_pricing_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `etab_id` int(10) unsigned NOT NULL,
  `numpricingcode` int(10) unsigned NULL COMMENT 'external id',
  `code` int(10) unsigned NULL,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'CREATE_SESSIONS_FOR_AN_EXERCICE', `dims_constant`.`value` = 'Créer une session pour un exercice.';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'CREATE_SESSIONS_FOR_AN_EXERCICE', `dims_constant`.`value` = 'Create session for an exercice.';

CREATE TABLE IF NOT EXISTS `dims_mod_reservation_article_pricing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_article` int(10) unsigned NOT NULL,
  `numarticle` int(10) unsigned NULL COMMENT 'external id',
  `id_pricingcode` int(10) unsigned NOT NULL,
  `numpricingcode` int(10) unsigned NULL COMMENT 'external id',
  `price` int(10) unsigned NULL,
  `effective_date` bigint(10) unsigned NULL,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dims_mod_reservation_compte_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `numcomptetype` int(10) unsigned NULL COMMENT 'external id',
  `label` varchar(50) COLLATE 'utf8_general_ci' NULL,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `dims_mod_reservation_compte`
ADD `id_type_compte` int(10) unsigned NULL AFTER `inscription_id`,
ADD `accepts_sms` tinyint(1) unsigned NULL AFTER `state`,
ADD `accepts_email` tinyint(1) unsigned NULL AFTER `accepts_sms`,
ADD `streetnum` varchar(25) COLLATE 'utf8_general_ci' NULL AFTER `nationalite`,
ADD `btq` varchar(25) COLLATE 'utf8_general_ci' NULL AFTER `streetnum`,
ADD `address_cpl` varchar(50) DEFAULT NULL AFTER `address`,
ADD `country` varchar(50) COLLATE 'utf8_general_ci' NULL AFTER `city`,
COMMENT='';

DROP TABLE IF EXISTS `dims_mod_reservation_message` ;
CREATE TABLE IF NOT EXISTS `dims_mod_reservation_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nummessage` int(10) unsigned NULL COMMENT 'external id',
  `label` varchar(50) COLLATE 'utf8_general_ci' NULL,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `dims_mod_reservation_abonnement`
ADD `numabonnement` int(10) unsigned NULL COMMENT 'external id' AFTER `id`,
ADD `cardnum` int(10) unsigned NULL AFTER `type_article`,
ADD `date_attribution_card` bigint(10) unsigned NULL AFTER `quantite`,
ADD `date_last_update_card` bigint(10) unsigned NULL AFTER `date_attribution_card`,
ADD `cardzone` int(10) unsigned NULL AFTER `date_last_update_card`,
COMMENT='';

INSERT INTO `dims_mb_action` (`id_module_type`, `id_action`, `label`, `description`, `id_workspace`, `id_object`, `historic_view`, `notif_view`, `notif_mail`) VALUES
(23,	13,	'Gérer les comptes abonnés',	NULL,	NULL,	0,	0,	0,	0);

INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES
(null,	'applicam - Site - Articles',	23,	'?op=\'site\'&action=\'articles\'',	NULL,	NULL,	NULL,	NULL);

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'NO_ESTABLISHMENT', `dims_constant`.`value` = 'Aucun établissement.';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'NO_ESTABLISHMENT', `dims_constant`.`value` = 'No establishment';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'NO_ARTICLE', `dims_constant`.`value` = 'Aucun article.';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'NO_ARTICLE', `dims_constant`.`value` = 'No article';

INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES
(null,	'applicam - Site multi etablissements',	23,	'?op=\'site_multietab\'',	NULL,	NULL,	NULL,	NULL);

ALTER TABLE `dims_mod_reservation_etablissement`
ADD `coord_x` int(10) unsigned NULL AFTER `type`,
ADD `coord_y` int(10) unsigned NULL AFTER `coord_x`,
COMMENT='';

ALTER TABLE `dims_mod_reservation_params`
CHANGE `value` `value` varchar(50) COLLATE 'utf8_general_ci' NULL AFTER `param`,
COMMENT='';

INSERT INTO `dims_mod_reservation_params` (`param`, `value`, `id_user`, `id_module`, `id_workspace`, `id_globalobject`) VALUES
('sitenum',	'85',	9468,	354,	97,	0),
('termnum',	'29',	9468,	354,	97,	0),
('wsdlfile',	'http://wsapplisys.zecarte.fr/wsCEsys.asmx?WSDL',	9468,	354,	97,	0);

ALTER TABLE `dims_mod_reservation_params`
CHANGE `value` `value` text COLLATE 'utf8_general_ci' NULL AFTER `param`,
COMMENT='';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'NOTIFICATIONS_EMAILS', `dims_constant`.`value` = 'Emails de notifications';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'NOTIFICATIONS_EMAILS', `dims_constant`.`value` = 'E-mails notifications ';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'PAYMENT_REJECTED_EMAIL', `dims_constant`.`value` = 'E-mail de paiement refusé';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'PAYMENT_REJECTED_EMAIL', `dims_constant`.`value` = 'Payment rejected e-mail';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'PAYMENT_ACCEPTED_EMAIL', `dims_constant`.`value` = 'E-mail de paiement accepté';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'PAYMENT_ACCEPTED_EMAIL', `dims_constant`.`value` = 'Payment accepted e-mail';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'VALID_ORDER_EMAIL', `dims_constant`.`value` = 'E-mail de validation de commande';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'VALID_ORDER_EMAIL', `dims_constant`.`value` = 'Valid order e-mail';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'RESERVATION_CANCELED_EMAIL', `dims_constant`.`value` = 'E-mail d''annulation de reservation';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'RESERVATION_CANCELED_EMAIL', `dims_constant`.`value` = 'Reservation canceled e-mail';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'RESERVATION_VALIDATED_EMAIL', `dims_constant`.`value` = 'E-mail validation de reservation';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'RESERVATION_VALIDATED_EMAIL', `dims_constant`.`value` = 'Reservation validated e-mail';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'BLOCKED_ACCOUNT_EMAIL', `dims_constant`.`value` = 'E-mail de désactivation de compte';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'BLOCKED_ACCOUNT_EMAIL', `dims_constant`.`value` = 'Blocked account e-mail';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'CREATED_ACCOUNT_EMAIL', `dims_constant`.`value` = 'E-mail de création de compte';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'CREATED_ACCOUNT_EMAIL', `dims_constant`.`value` = 'Created account e-mail';

-- Simon - reservation (applicam) - 28/05/2013
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'PAYMENT_REJECTED_EMAIL_SUBJECT', `dims_constant`.`value` = 'Sujet de l''e-mail de paiement refusé';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'PAYMENT_REJECTED_EMAIL_SUBJECT', `dims_constant`.`value` = 'Payment rejected e-mail subject';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'PAYMENT_ACCEPTED_EMAIL_SUBJECT', `dims_constant`.`value` = 'Sujet de l''e-mail de paiement accepté';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'PAYMENT_ACCEPTED_EMAIL_SUBJECT', `dims_constant`.`value` = 'Payment accepted e-mail subject';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'VALID_ORDER_EMAIL_SUBJECT', `dims_constant`.`value` = 'Sujet de l''e-mail de validation de commande';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'VALID_ORDER_EMAIL_SUBJECT', `dims_constant`.`value` = 'Valid order e-mail subject';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'RESERVATION_CANCELED_EMAIL_SUBJECT', `dims_constant`.`value` = 'Sujet de l''e-mail d''annulation de reservation';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'RESERVATION_CANCELED_EMAIL_SUBJECT', `dims_constant`.`value` = 'Reservation canceled e-mail subject';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'RESERVATION_VALIDATED_EMAIL_SUBJECT', `dims_constant`.`value` = 'Sujet de l''e-mail validation de reservation';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'RESERVATION_VALIDATED_EMAIL_SUBJECT', `dims_constant`.`value` = 'Reservation validated e-mail subject';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'BLOCKED_ACCOUNT_EMAIL_SUBJECT', `dims_constant`.`value` = 'Sujet de l''e-mail de désactivation de compte';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'BLOCKED_ACCOUNT_EMAIL_SUBJECT', `dims_constant`.`value` = 'Blocked account e-mail subject';

INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`phpvalue` = 'CREATED_ACCOUNT_EMAIL_SUBJECT', `dims_constant`.`value` = 'Sujet de l''e-mail de création de compte';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = NULL, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`phpvalue` = 'CREATED_ACCOUNT_EMAIL_SUBJECT', `dims_constant`.`value` = 'Created account e-mail subject';

-- Simon 29/05/2013
ALTER TABLE `dims_mod_reservation_article_pricing`
CHANGE `price` `price` float unsigned NULL AFTER `numpricingcode`,
COMMENT='';

-- Kevin 29/05/2013 -- applicam
ALTER TABLE `dims_mod_reservation_etablissement`
ADD `image` varchar(255);

DROP TABLE IF EXISTS `dims_mod_reservation_order` ;
CREATE TABLE IF NOT EXISTS `dims_mod_reservation_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_account` int(10) unsigned NOT NULL ,
  `state` int(10) unsigned NOT NULL ,
  `label` varchar(50) COLLATE 'utf8_general_ci' NULL,
  `comment` text,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dims_mod_reservation_order_line` ;
CREATE TABLE IF NOT EXISTS `dims_mod_reservation_order_line` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_order` int(10) unsigned NOT NULL ,
  `id_account` int(10) unsigned NOT NULL ,
  `id_article` int(10) unsigned NOT NULL ,
  `id_etab` int(10) unsigned NOT NULL ,
  `label` varchar(50) COLLATE 'utf8_general_ci' NULL,
  `longlabel` varchar(50) COLLATE 'utf8_general_ci' NULL,
  `labeltva` varchar(50) COLLATE 'utf8_general_ci' NULL,
  `tva` int(10) unsigned NOT NULL ,
  `isgroup` int(10) unsigned NOT NULL ,
  `type` int(10) unsigned NOT NULL ,
  `prix` int(10) unsigned NOT NULL ,
  `duree` int(10) unsigned NOT NULL ,
  `type_duree` int(10) unsigned NOT NULL ,
  `debut_validite` bigint(14) unsigned NOT NULL ,
  `type_debut_validite` int(10) unsigned NOT NULL ,
  `quantity` int(10) unsigned NOT NULL ,
  `multiple` int(10) unsigned NOT NULL ,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Simon - 30/05/2013 - Reservation (applicam)
ALTER TABLE `dims_mod_reservation_order_line`
ADD `totalprix` int(10) unsigned NOT NULL AFTER `prix`,
COMMENT='';

ALTER TABLE `dims_mod_reservation_order_line`
CHANGE `prix` `prix` float unsigned NOT NULL AFTER `type`,
CHANGE `totalprix` `totalprix` float unsigned NOT NULL AFTER `prix`,
COMMENT='';

ALTER TABLE `dims_mod_reservation_order_line`
CHANGE `tva` `tva` float unsigned NOT NULL AFTER `labeltva`,
COMMENT='';

ALTER TABLE `dims_mod_reservation_order`
ADD `totalprix` int(10) unsigned NOT NULL AFTER `comment`,
COMMENT='';

ALTER TABLE `dims_mod_reservation_pricing_code`
ADD `label` varchar(50) NOT NULL AFTER `code`,
COMMENT='';

-- Simon - 31/05/2013 - Reservation (applicam)
ALTER TABLE `dims_mod_reservation_abonnement`
ADD `etab_id` int(11) NOT NULL AFTER `article_id`,
COMMENT='';

-- Kévin - 04/06/2013 - Planning (applicam)
CREATE TABLE IF NOT EXISTS `dims_mod_reservation_etablissement_activite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_activite` int(10) unsigned NOT NULL ,
  `id_etab` int(10) unsigned NOT NULL ,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --Maj WCE applicam - site - planning :
INSERT INTO `dims_mb_wce_object` (`label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES
('applicam - Site - Planning',	23,	'?op=\'planning\'',	NULL,	NULL,	NULL,	NULL);

ALTER TABLE `dims_mod_reservation_activite`
ADD `image` varchar(255);

-- Cyril 05/06/2013 --
ALTER TABLE `dims_mod_reservation_etablissement` CHANGE `image` `id_picto` INT(11)  NULL  DEFAULT '0';
ALTER TABLE `dims_mod_reservation_etablissement` CHANGE `latitude` `latitude` VARCHAR(25)  NULL  DEFAULT NULL;
ALTER TABLE `dims_mod_reservation_etablissement` CHANGE `longitude` `longitude` VARCHAR(25)  NULL  DEFAULT NULL;

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'reservation', 2, 'You have no establishment for now', 'NO_ETAB_NOW'),
	('', 'reservation', 1, 'Aucun établissement pour l\'instant', 'NO_ETAB_NOW');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'reservation', 2, 'Editing establishment', 'EDITION_OF_ETAB'),
	('', 'reservation', 1, 'Edition de l\'établissement', 'EDITION_OF_ETAB');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'reservation', 2, 'Longitude', 'LONGITUDE'),
	('', 'reservation', 1, 'Longitude', 'LONGITUDE'),
	('', 'reservation', 2, 'Latitude', 'LATITUDE'),
	('', 'reservation', 1, 'Latitude', 'LATITUDE');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'reservation', 2, 'The establishment has been successfuly saved', 'ETAB_SAVED'),
	('', 'reservation', 1, 'L\'établissement a été enregistré avec succès', 'ETAB_SAVED'),
	('', 'reservation', 2, 'Help on Google Maps :', 'HELP_ON_GMAP'),
	('', 'reservation', 1, 'De l\'aide sur Google Maps :', 'HELP_ON_GMAP');

-- Maj WCE applicam - site - planning :
UPDATE dims_mb_wce_object SET script = `?op='planning'` WHERE id = `83`;

-- Simon - 05/06/2013 - Reservation (applicam)
ALTER TABLE `dims_mod_reservation_order`
ADD `card_number` varchar(20) NULL AFTER `id_account`,
ADD `lastname` varchar(100) NULL AFTER `card_number`,
ADD `firstname` varchar(100) NULL AFTER `lastname`,
ADD `civilite` varchar(10) NULL AFTER `firstname`,
ADD `sexe` varchar(10) NULL AFTER `civilite`,
ADD `nationalite` varchar(25) NULL AFTER `sexe`,
ADD `streetnum` varchar(25) NULL AFTER `nationalite`,
ADD `btq` varchar(25) NULL AFTER `streetnum`,
ADD `address` text NULL AFTER `btq`,
ADD `address_cpl` text NULL AFTER `address`,
ADD `postalcode` varchar(16) NULL AFTER `address_cpl`,
ADD `city` varchar(100) NULL AFTER `postalcode`,
ADD `country` varchar(50) NULL AFTER `city`,
ADD `phone` varchar(32) NULL AFTER `country`,
ADD `birthdate` bigint unsigned NULL AFTER `phone`,
ADD `mobile` varchar(32) NULL AFTER `birthdate`,
ADD `email` varchar(120) NULL AFTER `mobile`,
CHANGE `state` `state` int(10) unsigned NULL AFTER `email`,
COMMENT='';

-- Simon - 06/06/2013 - Reservation (applicam)
ALTER TABLE `dims_mod_reservation_article`
ADD `quantitedebit` int(11) unsigned NULL AFTER `quantite`,
COMMENT='';

ALTER TABLE `dims_mod_reservation_article`
ADD `fin_validite` bigint(14) NOT NULL AFTER `debut_validite`,
COMMENT='';

-- Cyril 06/06/2013 --
DELETE FROM dims_mb_wce_object WHERE id_module_type=23;
INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`)
VALUES
	('', 'applicam - Borne', 23, '?op=\'borne\'', NULL, NULL, NULL, NULL),
	('', 'applicam - FMI', 23, '?meta=\'fmi\'', NULL, NULL, NULL, NULL),
	('', 'applicam - Site - Articles', 23, '?op=\'site\'&action=\'articles\'', NULL, NULL, NULL, NULL),
	('', 'applicam - Site - Mon compte', 23, '?op=\'site_multietab\'', NULL, NULL, NULL, NULL),
	('', 'applicam - Site - Planning', 23, '?op=\'planning\'', NULL, NULL, NULL, NULL);

INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES (NULL, 'applicam - Site - Prestations', '23', '?op=\'prestations\'', NULL, NULL, NULL, NULL);
DELETE FROM dims_mb_wce_object WHERE label='applicam - Site - Articles';

DELETE FROM dims_mb_wce_object WHERE id_module_type=23;
INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`)
VALUES
	('', 'reservation - Borne', 23, '?op=\'borne\'', NULL, NULL, NULL, NULL),
	('', 'reservation - Site - Prestations', 23, '?op=\'prestations\'', NULL, NULL, NULL, NULL),
	('', 'reservation - FMI', 23, '?meta=\'fmi\'', NULL, NULL, NULL, NULL),
	('', 'reservation - Site - Mon compte', 23, '?op=\'site_multietab\'', NULL, NULL, NULL, NULL),
	('', 'reservation - Site - Planning', 23, '?op=\'planning\'', NULL, NULL, NULL, NULL);

UPDATE `dims_module` SET `label` = 'Reservation' WHERE `id_module_type` = 23;

UPDATE `dims_mb_wce_object` SET `script` = '?op=\'mon_compte\'' WHERE `label` = 'reservation - Site - Mon compte';

-- Simon - 06/06/2013 - Reservation (applicam)
ALTER TABLE `dims_mod_reservation_order_line`
ADD `quantitedebit` int(11) unsigned NULL AFTER `type_debut_validite`,
COMMENT='';

ALTER TABLE `dims_mod_reservation_order_line`
ADD `fin_validite` bigint(14) NOT NULL AFTER `debut_validite`,
COMMENT='';

ALTER TABLE `dims_mod_reservation_abonnement`
ADD `article_num` int(11) NOT NULL AFTER `article_id`,
COMMENT='';

ALTER TABLE `dims_mod_reservation_params`
CHANGE `value` `value` text COLLATE 'utf8_general_ci' NULL AFTER `param`,
COMMENT='';

-- Simon - 07/06/2013 - Reservation (applicam)
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `phpvalue`, `value`)
VALUES
	('', 'reservation', 2, 'RENEW_PASSWORD_EMAIL_SUBJECT', 'Renew password email subject'),
	('', 'reservation', 1, 'RENEW_PASSWORD_EMAIL_SUBJECT', 'Suject de l''e-mail de renouvellement de mot de passe');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `phpvalue`, `value`)
VALUES
	('', 'reservation', 2, 'RENEW_PASSWORD_EMAIL', 'Renew password email'),
	('', 'reservation', 1, 'RENEW_PASSWORD_EMAIL', 'E-mail de renouvellement de mot de passe');


-- Cyril - 12/06/2013 -
ALTER TABLE `dims_mod_reservation_abonnement` DROP `etab_id`;
-- Cyril - 12/06/2013 -
ALTER TABLE `dims_mod_reservation_activite` DROP `image`;

-- Cyril - 13/06/2013 -
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'reservation', 2, 'Supported activities', 'SUPPORTED_ACTIVITIES'),
	('', 'reservation', 1, 'Activités supportées', 'SUPPORTED_ACTIVITIES');

-- Cyril - 13/06/2013 -
ALTER TABLE `dims_mod_reservation_type_week` ADD `id_etab` INT(11)  NULL  DEFAULT '0'  AFTER `id`;
ALTER TABLE `dims_mod_reservation_type_week` ADD INDEX (`id_etab`);
update dims_mod_reservation_type_week set id_etab=1;

-- Thomas -- 27/06/2013 --
DROP TABLE IF EXISTS `dims_mod_business_address`;
CREATE TABLE IF NOT EXISTS `dims_mod_business_address` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL DEFAULT '',
  `address2` varchar(255) DEFAULT '',
  `address3` varchar(255) DEFAULT '',
  `postalcode` varchar(10) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL DEFAULT '',
  `id_city` int(11) NOT NULL DEFAULT '0',
  `country` varchar(100) NOT NULL,
  `id_country` int(11) NOT NULL DEFAULT '0',
  `id_globalobject` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_globalobject` (`id_globalobject`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `dims_mod_business_address_link`;
CREATE TABLE IF NOT EXISTS `dims_mod_business_address_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_goaddress` int(11) NOT NULL DEFAULT '0',
  `id_goobject` int(11) NOT NULL DEFAULT '0',
  `id_type` int(11) NOT NULL DEFAULT '0',
  `default` tinyint(4) NOT NULL DEFAULT '0',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(120) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_goobject` (`id_goobject`),
  KEY `id_goaddress` (`id_goaddress`,`id_goobject`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Sociétés', '_COMPANIES_CT'),
('', 'system', 2, 'Companies', '_COMPANIES_CT');

-- Thomas -- 28/06/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Ajouter une structure', '_ADD_A_STRUCTURE'),
('', 'system', 2, 'Add a structure', '_ADD_A_STRUCTURE'),
('', 'system', 1, 'Aucune structure pour l''instant', '_NO_STRUCTURE_AT_THE_MOMENT'),
('', 'system', 2, 'No structure at the moment', '_NO_STRUCTURE_AT_THE_MOMENT'),
('', 'system', 1, 'Adresses', '_ADDRESSES'),
('', 'system', 2, 'Addresses', '_ADDRESSES'),
('', 'system', 1, 'Aucune adresse pour l''instant', '_NO_ADDRESS_FOR_THE_MOMENT'),
('', 'system', 2, 'No address for the moment', '_NO_ADDRESS_FOR_THE_MOMENT'),
('', 'system', 1, 'créée par', '_CREEEE_PAR'),
('', 'system', 2, 'created by', '_CREEEE_PAR');

ALTER TABLE  `dims_mod_business_address` ADD  `user_create` INT( 11 ) NOT NULL DEFAULT  '0',
ADD  `timestp_create` BIGINT( 14 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `dims_mod_business_address` ADD  `bp` VARCHAR( 25 ) NOT NULL DEFAULT  '' AFTER  `country`;

-- Thomas -- 01/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Le système a détecté que cette adresse existe peut-être déjà dans la base de données :', '_SYSTEM_DETECT_SIMILAR_ADDRESS'),
('', 'system', 2, 'The system has detected that this address may already exist in the database :', '_SYSTEM_DETECT_SIMILAR_ADDRESS'),
('', 'system', 1, 'Cette adresse est associée à :', '_THIS_ADDRESS_ASSOCIATED_WITH'),
('', 'system', 2, 'This address is associated with :', '_THIS_ADDRESS_ASSOCIATED_WITH'),
('', 'system', 1, 'Vous avez le choix de rattacher {DIMS_TEXT} à cette adresse ou bien d''en créer une nouvelle', '_CHOICE_LINK_TEXT_TO_ADDRESS_OR_NEW'),
('', 'system', 2, 'You have the choice to link {DIMS_TEXT} at this address or even create a new', '_CHOICE_LINK_TEXT_TO_ADDRESS_OR_NEW'),
('', 'system', 1, 'Rattacher à l''adresse existante', '_ATTACH_TO_EXISTING_ADDRESS'),
('', 'system', 2, 'Attach to the existing address', '_ATTACH_TO_EXISTING_ADDRESS'),
('', 'system', 1, 'Créer la nouvelle adresse', '_CREATE_NEW_ADDRESS'),
('', 'system', 2, 'Create the new address', '_CREATE_NEW_ADDRESS');

-- Thomas --02/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Entité', '_ENTITY'),
('', 'system', 2, 'Entity', '_ENTITY'),
('', 'system', 1, 'Entités', '_ENTITIES'),
('', 'system', 2, 'Entities', '_ENTITIES'),
('', 'system', 1, 'Aucune entreprise correspondant à {DIMS_TEXT} n''a été trouvée.', '_NO_COMPANY_MATCHING_WAS_FOUND'),
('', 'system', 2, 'No company matching {DIMS_TEXT} was found.', '_NO_COMPANY_MATCHING_WAS_FOUND'),
('', 'system', 1, 'Créer une nouvelle entreprise', '_CREATE_NEW_COMPANY'),
('', 'system', 2, 'Create a new company', '_CREATE_NEW_COMPANY'),
('', 'system', 1, 'Nouvelle structure', '_NEW_STRUCTURE'),
('', 'system', 2, 'New structure', '_NEW_STRUCTURE'),
('', 'system', 1, 'Société', '_COMPANY_CT'),
('', 'system', 2, 'Company', '_COMPANY_CT');

-- Thomas -- 03/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Sous-service de', '_UNDER_SERVICE_OF'),
('', 'system', 2, 'Under service of', '_UNDER_SERVICE_OF'),
('', 'system', 1, 'Fonction de {DIMS_TEXT} au sein de cette structure', '_FUNCTION_WITHIN_THIS_STRUCTURE'),
('', 'system', 2, '{DIMS_TEXT}function within this structure', '_FUNCTION_WITHIN_THIS_STRUCTURE'),
('', 'system', 1, 'Adresse de la structure', '_ADDRESS_OF_STRUCTURE'),
('', 'system', 2, 'Address of the structure', '_ADDRESS_OF_STRUCTURE'),
('', 'system', 1, 'Créer une nouvelle adresse (cette adresse sera automatiquement rattachée à {DIMS_TEXT})', '_CREATE_NEW_ADDRESS_ATTACHED_TO_CT'),
('', 'system', 2, 'Create a new address (this address will be automatically attached to {DIMS_TEXT})', '_CREATE_NEW_ADDRESS_ATTACHED_TO_CT');

-- Thomas -- 04/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Attacher cette structure à {DIMS_TEXT}', '_ATTACH_STRUCTURE_TO_XXX'),
('', 'system', 2, 'Attach this structure to {DIMS_TEXT}', '_ATTACH_STRUCTURE_TO_XXX'),
('', 'system', 1, 'Aucun document pour l''instant', '_NO_DOC_FOR_NOW'),
('', 'system', 2, 'No document for now', '_NO_DOC_FOR_NOW'),
('', 'system', 1, 'Ajouter un todo', '_ADD_TODO'),
('', 'system', 2, 'Add a todo', '_ADD_TODO'),
('', 'system', 1, 'Aucun todo pour l''instant', '_NO_TODO_FOR_NOW'),
('', 'system', 2, 'No todo for now', '_NO_TODO_FOR_NOW'),
('', 'system', 1, 'Aucune activité pour l''instant', '_NO_ACTIVITY_FOR_MOMENT'),
('', 'system', 2, 'No activity for the moment', '_NO_ACTIVITY_FOR_MOMENT'),
('', 'system', 1, 'Discussions', '_DISCUSSIONS'),
('', 'system', 2, 'Discussions', '_DISCUSSIONS'),
('', 'system', 1, 'Ajouter une discussion', '_ADD_DISCUSSION'),
('', 'system', 2, 'Add a discussion', '_ADD_DISCUSSION'),
('', 'system', 1, 'Aucune discussion pour l''instant', '_NO_DISCUSSION_FOR_NOW'),
('', 'system', 2, 'No discussion for now', '_NO_DISCUSSION_FOR_NOW');

-- Thomas -- 11/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Répertoire', '_DIRECTORY'),
('', 'system', 2, 'Directory', '_DIRECTORY'),
('', 'system', 1, 'pas d''échéance', '_NOT_DUE'),
('', 'system', 2, 'not due', '_NOT_DUE'),
('', 'system', 1, 'Entrez votre texte ici ...', '_ENTER_YOUR_TEXT_HERE'),
('', 'system', 2, 'Enter your text here ...', '_ENTER_YOUR_TEXT_HERE');

-- Thomas -- 12/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Tapez un nom ...', '_TYPE_A_NAME'),
('', 'system', 2, 'Type a name ...', '_TYPE_A_NAME');

-- Simon -- 2013-07-11 -- Lorient (elisath)
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Recherche sur', 'SEARCH_ON'),
('', 'system', 2, 'Search on', 'SEARCH_ON');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Dims',	'DIMS'),
('',	'system',	2,	'Dims',	'DIMS');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Activé',	'ENABLED'),
('',	'system',	2,	'Enabled',	'ENABLED');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Demandes',	'REQUESTS'),
('',	'system',	2,	'Requests',	'REQUESTS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Titre du groupe',	'TITLE_OF_THE_GROUP'),
('',	'system',	2,	'Title of the group',	'TITLE_OF_THE_GROUP');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Nb d\'envois',	'NB_SENDINGS'),
('',	'system',	2,	'Nb Sendings',	'NB_SENDINGS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Nb de contact e-mails',	'NB_CONTACT_EMAILS'),
('',	'system',	2,	'Nb contact emails',	'NB_CONTACT_EMAILS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Groupe lié(s) de bulletins d\'information',	'LINKED_GROUP(S)_OF_NEWSLETTERS'),
('',	'system',	2,	'Linked group(s) of newsletters',	'LINKED_GROUP(S)_OF_NEWSLETTERS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Recherche avancée',	'ADVANCED_SEARCH'),
('',	'system',	2,	'Advanced Search',	'ADVANCED_SEARCH');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Gestion newsletters',	'NEWSLETTERS_MANAGEMENT'),
('',	'system',	2,	'newsletters management',	'NEWSLETTERS_MANAGEMENT');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Groupes de Newsletters',	'GROUPS_OF_NEWSLETTERS'),
('',	'system',	2,	'Groups of Newsletters',	'GROUPS_OF_NEWSLETTERS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Revenir à la gestion des bulletins',	'COME_BACK_TO_THE_NEWSLETTERS_MANAGEMENT'),
('',	'system',	2,	'Come back to the newsletters management',	'COME_BACK_TO_THE_NEWSLETTERS_MANAGEMENT');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'DESCRIPTION',	'DESCRIPTION'),
('',	'system',	2,	'DESCRIPTION',	'DESCRIPTION');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Créé par',	'CREATED_BY'),
('',	'system',	2,	'Created by',	'CREATED_BY');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Créé le',	'CREATED_THE'),
('',	'system',	2,	'Created the',	'CREATED_THE');

DROP TABLE IF EXISTS `dims_mod_newsletter_tag`;
CREATE TABLE `dims_mod_newsletter_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_newsletter` int(11) NOT NULL DEFAULT '0',
  `id_tag` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Nb abonnements',	'NB_SUBSCRIPTIONS'),
('',	'system',	2,	'Nb subscriptions',	'NB_SUBSCRIPTIONS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Description / Synopsis',	'DESCRIPTION___SYNOPSIS'),
('',	'system',	2,	'Description / Synopsis',	'DESCRIPTION___SYNOPSIS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'BÉNÉFICIAIRES',	'RECIPIENTS'),
('',	'system',	2,	'RECIPIENTS',	'RECIPIENTS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'ENVOIS',	'SENDINGS'),
('',	'system',	2,	'SENDINGS',	'SENDINGS');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Liste des envois préparés',	'LIST_OF_PREPARED_SENDINGS'),
('',	'system',	2,	'List of prepared sendings',	'LIST_OF_PREPARED_SENDINGS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Liste des bulletins déjà envoyé',	'LIST_OF_NEWSLETTERS_ALREADY_SENT'),
('',	'system',	2,	'List of newsletters already sent',	'LIST_OF_NEWSLETTERS_ALREADY_SENT');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Listes de diffusion attachés',	'MAILING_LISTS_ATTACHED'),
('',	'system',	2,	'Mailing lists attached',	'MAILING_LISTS_ATTACHED');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Nb e-mails',	'NB_EMAILS'),
('',	'system',	2,	'Nb emails',	'NB_EMAILS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Rechercher un contact',	'SEARCH_A_CONTACT'),
('',	'system',	2,	'Search a contact',	'SEARCH_A_CONTACT');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Se désinscrire de cette date',	'UNSUSCRIBE_DATE'),
('',	'system',	2,	'Unsuscribe date',	'UNSUSCRIBE_DATE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'De',	'FROM'),
('',	'system',	2,	'From',	'FROM');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('',	'system',	1,	'Listes de bénéficiaires',	'RECIPIENTS_LISTS'),
('',	'system',	2,	'Recipients lists',	'RECIPIENTS_LISTS');

DROP TABLE IF EXISTS `dims_country`;
CREATE TABLE `dims_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iso` varchar(2) NOT NULL DEFAULT '',
  `name` varchar(80) DEFAULT NULL,
  `printable_name` varchar(80) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  `phoneprefix` int(10) DEFAULT NULL,
  `fr` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `dims_country` (`id`, `iso`, `name`, `printable_name`, `iso3`, `numcode`, `phoneprefix`, `fr`) VALUES
(1,	'AF',	'AFGHANISTAN',	'Afghanistan',	'AFG',	4,	93,	'Afghanistan'),
(2,	'AL',	'ALBANIA',	'Albania',	'ALB',	8,	355,	'Albanie'),
(3,	'DZ',	'ALGERIA',	'Algeria',	'DZA',	12,	213,	'Algérie'),
(4,	'AS',	'AMERICAN SAMOA',	'American Samoa',	'ASM',	16,	1,	'Samoa américaines'),
(5,	'AD',	'ANDORRA',	'Andorra',	'AND',	20,	376,	'Andorre'),
(6,	'AO',	'ANGOLA',	'Angola',	'AGO',	24,	244,	'Angola'),
(7,	'AI',	'ANGUILLA',	'Anguilla',	'AIA',	660,	1,	'Anguilla'),
(8,	'AQ',	'ANTARCTICA',	'Antarctica',	NULL,	NULL,	672,	'Antarctique'),
(9,	'AG',	'ANTIGUA AND BARBUDA',	'Antigua and Barbuda',	'ATG',	28,	1,	''),
(10,	'AR',	'ARGENTINA',	'Argentina',	'ARG',	32,	54,	'Argentine'),
(11,	'AM',	'ARMENIA',	'Armenia',	'ARM',	51,	374,	'Arménie'),
(12,	'AW',	'ARUBA',	'Aruba',	'ABW',	533,	297,	'Aruba'),
(13,	'AU',	'AUSTRALIA',	'Australia',	'AUS',	36,	61,	'Australie'),
(14,	'AT',	'AUSTRIA',	'Austria',	'AUT',	40,	43,	'Autriche'),
(15,	'AZ',	'AZERBAIJAN',	'Azerbaijan',	'AZE',	31,	994,	'Azerbaïdjan'),
(16,	'BS',	'BAHAMAS',	'Bahamas',	'BHS',	44,	1,	'Bahamas'),
(17,	'BH',	'BAHRAIN',	'Bahrain',	'BHR',	48,	973,	'Bahreïn'),
(18,	'BD',	'BANGLADESH',	'Bangladesh',	'BGD',	50,	880,	'Bangladesh'),
(19,	'BB',	'BARBADOS',	'Barbados',	'BRB',	52,	1,	'Barbade'),
(20,	'BY',	'BELARUS',	'Belarus',	'BLR',	112,	375,	'Biélorussie'),
(21,	'BE',	'BELGIUM',	'Belgium',	'BEL',	56,	32,	'Belgique'),
(22,	'BZ',	'BELIZE',	'Belize',	'BLZ',	84,	501,	'Belize'),
(23,	'BJ',	'BENIN',	'Benin',	'BEN',	204,	229,	'Bénin'),
(24,	'BM',	'BERMUDA',	'Bermuda',	'BMU',	60,	1,	'Bermudes'),
(25,	'BT',	'BHUTAN',	'Bhutan',	'BTN',	64,	975,	'Bhoutan'),
(26,	'BO',	'BOLIVIA',	'Bolivia',	'BOL',	68,	591,	'Bolivie'),
(27,	'BA',	'BOSNIA AND HERZEGOVINA',	'Bosnia and Herzegovina',	'BIH',	70,	387,	'Bosnie-Herzégovine'),
(28,	'BW',	'BOTSWANA',	'Botswana',	'BWA',	72,	267,	'Botswana'),
(29,	'BV',	'BOUVET ISLAND',	'Bouvet Island',	NULL,	NULL,	0,	'Ile Bouvet'),
(30,	'BR',	'BRAZIL',	'Brazil',	'BRA',	76,	55,	'Brésil'),
(31,	'IO',	'BRITISH INDIAN OCEAN TERRITORY',	'British Indian Ocean Territory',	NULL,	NULL,	0,	'Territoire britannique de l\'Océan Indien'),
(32,	'BN',	'BRUNEI DARUSSALAM',	'Brunei Darussalam',	'BRN',	96,	673,	'Brunei'),
(33,	'BG',	'BULGARIA',	'Bulgaria',	'BGR',	100,	359,	'Bulgarie'),
(34,	'BF',	'BURKINA FASO',	'Burkina Faso',	'BFA',	854,	226,	'Burkina Faso'),
(35,	'BI',	'BURUNDI',	'Burundi',	'BDI',	108,	257,	'Burundi'),
(36,	'KH',	'CAMBODIA',	'Cambodia',	'KHM',	116,	855,	'Cambodge'),
(37,	'CM',	'CAMEROON',	'Cameroon',	'CMR',	120,	237,	'Cameroun'),
(38,	'CA',	'CANADA',	'Canada',	'CAN',	124,	1,	'Canada'),
(39,	'CV',	'CAPE VERDE',	'Cape Verde',	'CPV',	132,	238,	'Cap-Vert'),
(40,	'KY',	'CAYMAN ISLANDS',	'Cayman Islands',	'CYM',	136,	1,	'Iles Cayman'),
(41,	'CF',	'CENTRAL AFRICAN REPUBLIC',	'Central African Republic',	'CAF',	140,	236,	'République centrafricaine'),
(42,	'TD',	'CHAD',	'Chad',	'TCD',	148,	235,	'Tchad'),
(43,	'CL',	'CHILE',	'Chile',	'CHL',	152,	56,	'Chili'),
(44,	'CN',	'CHINA',	'China',	'CHN',	156,	86,	'Chine'),
(45,	'CX',	'CHRISTMAS ISLAND',	'Christmas Island',	NULL,	NULL,	61,	'Ile Christmas'),
(46,	'CC',	'COCOS (KEELING) ISLANDS',	'Cocos (Keeling) Islands',	NULL,	NULL,	61,	'Iles des Cocos (Keeling)'),
(47,	'CO',	'COLOMBIA',	'Colombia',	'COL',	170,	57,	'Colombie'),
(48,	'KM',	'COMOROS',	'Comoros',	'COM',	174,	269,	'Comores'),
(49,	'CG',	'CONGO',	'Congo',	'COG',	178,	0,	'Congo'),
(50,	'CD',	'CONGO, THE DEMOCRATIC REPUBLIC OF THE',	'Congo, the Democratic Republic of the',	'COD',	180,	0,	'République du Congo'),
(51,	'CK',	'COOK ISLANDS',	'Cook Islands',	'COK',	184,	682,	'Iles Cook'),
(52,	'CR',	'COSTA RICA',	'Costa Rica',	'CRI',	188,	506,	'Costa Rica'),
(53,	'CI',	'COTE D\'IVOIRE',	'Cote D\'Ivoire',	'CIV',	384,	385,	'Cote D\'Ivoire'),
(54,	'HR',	'CROATIA',	'Croatia',	'HRV',	191,	0,	'Croatie'),
(55,	'CU',	'CUBA',	'Cuba',	'CUB',	192,	53,	'Cuba'),
(56,	'CY',	'CYPRUS',	'Cyprus',	'CYP',	196,	357,	'Chypre'),
(57,	'CZ',	'CZECH REPUBLIC',	'Czech Republic',	'CZE',	203,	420,	'République tchèque'),
(58,	'DK',	'DENMARK',	'Denmark',	'DNK',	208,	45,	'Danemark'),
(59,	'DJ',	'DJIBOUTI',	'Djibouti',	'DJI',	262,	253,	'Djibouti'),
(60,	'DM',	'DOMINICA',	'Dominica',	'DMA',	212,	1,	'Dominique'),
(61,	'DO',	'DOMINICAN REPUBLIC',	'Dominican Republic',	'DOM',	214,	1,	'République dominicaine'),
(62,	'EC',	'ECUADOR',	'Ecuador',	'ECU',	218,	593,	'Équateur'),
(63,	'EG',	'EGYPT',	'Egypt',	'EGY',	818,	20,	'Égypte'),
(64,	'SV',	'EL SALVADOR',	'El Salvador',	'SLV',	222,	503,	'Salvador'),
(65,	'GQ',	'EQUATORIAL GUINEA',	'Equatorial Guinea',	'GNQ',	226,	240,	'Guinée équatoriale'),
(66,	'ER',	'ERITREA',	'Eritrea',	'ERI',	232,	291,	'Érythrée'),
(67,	'EE',	'ESTONIA',	'Estonia',	'EST',	233,	372,	'Estonie'),
(68,	'ET',	'ETHIOPIA',	'Ethiopia',	'ETH',	231,	251,	'Éthiopie'),
(69,	'FK',	'FALKLAND ISLANDS (MALVINAS)',	'Falkland Islands (Malvinas)',	'FLK',	238,	500,	'Iles Falkland'),
(70,	'FO',	'FAROE ISLANDS',	'Faroe Islands',	'FRO',	234,	298,	'Iles Féroé'),
(71,	'FJ',	'FIJI',	'Fiji',	'FJI',	242,	679,	'Iles Fidji'),
(72,	'FI',	'FINLAND',	'Finland',	'FIN',	246,	358,	'Finlande'),
(73,	'FR',	'FRANCE',	'France',	'FRA',	250,	33,	'France'),
(74,	'GF',	'FRENCH GUIANA',	'French Guiana',	'GUF',	254,	0,	'Guyane'),
(75,	'PF',	'FRENCH POLYNESIA',	'French Polynesia',	'PYF',	258,	689,	'Polynésie française'),
(76,	'TF',	'FRENCH SOUTHERN TERRITORIES',	'French Southern Territories',	NULL,	NULL,	0,	''),
(77,	'GA',	'GABON',	'Gabon',	'GAB',	266,	241,	'Gabon'),
(78,	'GM',	'GAMBIA',	'Gambia',	'GMB',	270,	220,	'Gambie'),
(79,	'GE',	'GEORGIA',	'Georgia',	'GEO',	268,	995,	'Géorgie'),
(80,	'DE',	'GERMANY',	'Germany',	'DEU',	276,	49,	'Allemagne'),
(81,	'GH',	'GHANA',	'Ghana',	'GHA',	288,	233,	'Ghana'),
(82,	'GI',	'GIBRALTAR',	'Gibraltar',	'GIB',	292,	350,	'Gibraltar'),
(83,	'GR',	'GREECE',	'Greece',	'GRC',	300,	30,	'Grèce'),
(84,	'GL',	'GREENLAND',	'Greenland',	'GRL',	304,	299,	'Groenland'),
(85,	'GD',	'GRENADA',	'Grenada',	'GRD',	308,	1,	'Grenade'),
(86,	'GP',	'GUADELOUPE',	'Guadeloupe',	'GLP',	312,	0,	'Guadeloupe'),
(87,	'GU',	'GUAM',	'Guam',	'GUM',	316,	1671,	'Guam'),
(88,	'GT',	'GUATEMALA',	'Guatemala',	'GTM',	320,	502,	'Guatemala'),
(89,	'GN',	'GUINEA',	'Guinea',	'GIN',	324,	224,	'Guinée'),
(90,	'GW',	'GUINEA-BISSAU',	'Guinea-Bissau',	'GNB',	624,	245,	'Guinée-Bissao'),
(91,	'GY',	'GUYANA',	'Guyana',	'GUY',	328,	592,	'Guyana'),
(92,	'HT',	'HAITI',	'Haiti',	'HTI',	332,	509,	'Haïti'),
(93,	'HM',	'HEARD ISLAND AND MCDONALD ISLANDS',	'Heard Island and Mcdonald Islands',	NULL,	NULL,	0,	''),
(94,	'VA',	'HOLY SEE (VATICAN CITY STATE)',	'Holy See (Vatican City State)',	'VAT',	336,	39,	''),
(95,	'HN',	'HONDURAS',	'Honduras',	'HND',	340,	504,	'Honduras'),
(96,	'HK',	'HONG KONG',	'Hong Kong',	'HKG',	344,	852,	'Hong Kong'),
(97,	'HU',	'HUNGARY',	'Hungary',	'HUN',	348,	36,	'Hongrie'),
(98,	'IS',	'ICELAND',	'Iceland',	'ISL',	352,	354,	'Islande'),
(99,	'IN',	'INDIA',	'India',	'IND',	356,	91,	'Inde'),
(100,	'ID',	'INDONESIA',	'Indonesia',	'IDN',	360,	62,	'Indonésie'),
(101,	'IR',	'IRAN, ISLAMIC REPUBLIC OF',	'Iran, Islamic Republic of',	'IRN',	364,	98,	'Iran'),
(102,	'IQ',	'IRAQ',	'Iraq',	'IRQ',	368,	964,	'Iraq'),
(103,	'IE',	'IRELAND',	'Ireland',	'IRL',	372,	353,	'Irlande'),
(104,	'IL',	'ISRAEL',	'Israel',	'ISR',	376,	972,	'Israël'),
(105,	'IT',	'ITALY',	'Italy',	'ITA',	380,	39,	'Italie'),
(106,	'JM',	'JAMAICA',	'Jamaica',	'JAM',	388,	1876,	'Jamaïque'),
(107,	'JP',	'JAPAN',	'Japan',	'JPN',	392,	81,	'Japon'),
(108,	'JO',	'JORDAN',	'Jordan',	'JOR',	400,	962,	'Jordanie'),
(109,	'KZ',	'KAZAKHSTAN',	'Kazakhstan',	'KAZ',	398,	7,	'Kazakhstan'),
(110,	'KE',	'KENYA',	'Kenya',	'KEN',	404,	254,	'Kenya'),
(111,	'KI',	'KIRIBATI',	'Kiribati',	'KIR',	296,	686,	'Kiribati'),
(112,	'KP',	'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF',	'Korea, Democratic People\'s Republic of',	'PRK',	408,	0,	'Korée'),
(113,	'KR',	'KOREA, REPUBLIC OF',	'Korea, Republic of',	'KOR',	410,	0,	'Korée'),
(114,	'KW',	'KUWAIT',	'Kuwait',	'KWT',	414,	965,	'Koweït'),
(115,	'KG',	'KYRGYZSTAN',	'Kyrgyzstan',	'KGZ',	417,	996,	'Kirghizistan'),
(116,	'LA',	'LAO PEOPLE\'S DEMOCRATIC REPUBLIC',	'Lao People\'s Democratic Republic',	'LAO',	418,	856,	'Laos'),
(117,	'LV',	'LATVIA',	'Latvia',	'LVA',	428,	371,	'Lettonie'),
(118,	'LB',	'LEBANON',	'Lebanon',	'LBN',	422,	961,	'Liban'),
(119,	'LS',	'LESOTHO',	'Lesotho',	'LSO',	426,	266,	'Lesotho'),
(120,	'LR',	'LIBERIA',	'Liberia',	'LBR',	430,	231,	'Liberia'),
(121,	'LY',	'LIBYAN ARAB JAMAHIRIYA',	'Libyan Arab Jamahiriya',	'LBY',	434,	218,	'Libye'),
(122,	'LI',	'LIECHTENSTEIN',	'Liechtenstein',	'LIE',	438,	423,	'Liechtenstein'),
(123,	'LT',	'LITHUANIA',	'Lithuania',	'LTU',	440,	370,	'Lituanie'),
(124,	'LU',	'LUXEMBOURG',	'Luxembourg',	'LUX',	442,	352,	'Luxembourg'),
(125,	'MO',	'MACAO',	'Macao',	'MAC',	446,	853,	'Macao'),
(126,	'MK',	'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',	'Macedonia, the Former Yugoslav Republic of',	'MKD',	807,	389,	'Macédonie'),
(127,	'MG',	'MADAGASCAR',	'Madagascar',	'MDG',	450,	261,	'Madagascar'),
(128,	'MW',	'MALAWI',	'Malawi',	'MWI',	454,	265,	'Malawi'),
(129,	'MY',	'MALAYSIA',	'Malaysia',	'MYS',	458,	60,	'Malaisie'),
(130,	'MV',	'MALDIVES',	'Maldives',	'MDV',	462,	960,	'Maldives'),
(131,	'ML',	'MALI',	'Mali',	'MLI',	466,	223,	'Mali'),
(132,	'MT',	'MALTA',	'Malta',	'MLT',	470,	356,	'Malte'),
(133,	'MH',	'MARSHALL ISLANDS',	'Marshall Islands',	'MHL',	584,	692,	'Iles Marshall'),
(134,	'MQ',	'MARTINIQUE',	'Martinique',	'MTQ',	474,	0,	'Martinique'),
(135,	'MR',	'MAURITANIA',	'Mauritania',	'MRT',	478,	222,	'Mauritanie'),
(136,	'MU',	'MAURITIUS',	'Mauritius',	'MUS',	480,	230,	'Maurice'),
(137,	'YT',	'MAYOTTE',	'Mayotte',	NULL,	NULL,	262,	'Mayotte'),
(138,	'MX',	'MEXICO',	'Mexico',	'MEX',	484,	52,	'Mexique'),
(139,	'FM',	'MICRONESIA, FEDERATED STATES OF',	'Micronesia, Federated States of',	'FSM',	583,	691,	'Micronésie'),
(140,	'MD',	'MOLDOVA, REPUBLIC OF',	'Moldova, Republic of',	'MDA',	498,	373,	'Moldavie'),
(141,	'MC',	'MONACO',	'Monaco',	'MCO',	492,	377,	'Monaco'),
(142,	'MN',	'MONGOLIA',	'Mongolia',	'MNG',	496,	976,	'Mongolie'),
(143,	'MS',	'MONTSERRAT',	'Montserrat',	'MSR',	500,	1664,	'Montserrat'),
(144,	'MA',	'MOROCCO',	'Morocco',	'MAR',	504,	212,	'Maroc'),
(145,	'MZ',	'MOZAMBIQUE',	'Mozambique',	'MOZ',	508,	258,	'Mozambique'),
(146,	'MM',	'MYANMAR',	'Myanmar',	'MMR',	104,	0,	''),
(147,	'NA',	'NAMIBIA',	'Namibia',	'NAM',	516,	264,	'Namibie'),
(148,	'NR',	'NAURU',	'Nauru',	'NRU',	520,	674,	'Nauru'),
(149,	'NP',	'NEPAL',	'Nepal',	'NPL',	524,	977,	'Népal'),
(150,	'NL',	'NETHERLANDS',	'Netherlands',	'NLD',	528,	31,	'Pays-Bas'),
(151,	'AN',	'NETHERLANDS ANTILLES',	'Netherlands Antilles',	'ANT',	530,	599,	'Antilles néerlandaises'),
(152,	'NC',	'NEW CALEDONIA',	'New Caledonia',	'NCL',	540,	687,	'Nouvelle-Calédonie'),
(153,	'NZ',	'NEW ZEALAND',	'New Zealand',	'NZL',	554,	64,	'Nouvelle-Zélande'),
(154,	'NI',	'NICARAGUA',	'Nicaragua',	'NIC',	558,	505,	'Nicaragua'),
(155,	'NE',	'NIGER',	'Niger',	'NER',	562,	227,	'Niger'),
(156,	'NG',	'NIGERIA',	'Nigeria',	'NGA',	566,	234,	'Nigeria'),
(157,	'NU',	'NIUE',	'Niue',	'NIU',	570,	683,	'Nioué'),
(158,	'NF',	'NORFOLK ISLAND',	'Norfolk Island',	'NFK',	574,	672,	'Ile Norfolk'),
(159,	'MP',	'NORTHERN MARIANA ISLANDS',	'Northern Mariana Islands',	'MNP',	580,	1670,	'Mariannes du Nord'),
(160,	'NO',	'NORWAY',	'Norway',	'NOR',	578,	47,	'Norvège'),
(161,	'OM',	'OMAN',	'Oman',	'OMN',	512,	968,	'Oman'),
(162,	'PK',	'PAKISTAN',	'Pakistan',	'PAK',	586,	92,	'Pakistan'),
(163,	'PW',	'PALAU',	'Palau',	'PLW',	585,	680,	'Belau'),
(164,	'PS',	'PALESTINIAN TERRITORY, OCCUPIED',	'Palestinian Territory, Occupied',	NULL,	NULL,	0,	'Palestine'),
(165,	'PA',	'PANAMA',	'Panama',	'PAN',	591,	507,	'Panama'),
(166,	'PG',	'PAPUA NEW GUINEA',	'Papua New Guinea',	'PNG',	598,	675,	'Papouasie-Nouvelle-Guinée'),
(167,	'PY',	'PARAGUAY',	'Paraguay',	'PRY',	600,	595,	'Paraguay'),
(168,	'PE',	'PERU',	'Peru',	'PER',	604,	51,	'Pérou'),
(169,	'PH',	'PHILIPPINES',	'Philippines',	'PHL',	608,	63,	'Philippines'),
(170,	'PN',	'PITCAIRN',	'Pitcairn',	'PCN',	612,	870,	''),
(171,	'PL',	'POLAND',	'Poland',	'POL',	616,	48,	'Pologne'),
(172,	'PT',	'PORTUGAL',	'Portugal',	'PRT',	620,	351,	'Portugal'),
(173,	'PR',	'PUERTO RICO',	'Puerto Rico',	'PRI',	630,	1,	'Porto Rico'),
(174,	'QA',	'QATAR',	'Qatar',	'QAT',	634,	974,	'Qatar'),
(175,	'RE',	'REUNION',	'Reunion',	'REU',	638,	0,	'Réunion'),
(176,	'RO',	'ROMANIA',	'Romania',	'ROM',	642,	40,	'Roumanie'),
(177,	'RU',	'RUSSIAN FEDERATION',	'Russian Federation',	'RUS',	643,	7,	'Russie'),
(178,	'RW',	'RWANDA',	'Rwanda',	'RWA',	646,	250,	'Rwanda'),
(179,	'SH',	'SAINT HELENA',	'Saint Helena',	'SHN',	654,	290,	'Sainte-Hélène'),
(180,	'KN',	'SAINT KITTS AND NEVIS',	'Saint Kitts and Nevis',	'KNA',	659,	1869,	'Saint-Christophe-et-Niévès'),
(181,	'LC',	'SAINT LUCIA',	'Saint Lucia',	'LCA',	662,	1758,	'Sainte-Lucie'),
(182,	'PM',	'SAINT PIERRE AND MIQUELON',	'Saint Pierre and Miquelon',	'SPM',	666,	508,	'Yougoslavie'),
(183,	'VC',	'SAINT VINCENT AND THE GRENADINES',	'Saint Vincent and the Grenadines',	'VCT',	670,	1784,	'Saint-Vincent-et-les-Grenadines'),
(184,	'WS',	'SAMOA',	'Samoa',	'WSM',	882,	685,	'Samoa'),
(185,	'SM',	'SAN MARINO',	'San Marino',	'SMR',	674,	378,	'Saint-Marin'),
(186,	'ST',	'SAO TOME AND PRINCIPE',	'Sao Tome and Principe',	'STP',	678,	239,	'Sao Tomé-et-Principe'),
(187,	'SA',	'SAUDI ARABIA',	'Saudi Arabia',	'SAU',	682,	966,	'Arabie saoudite'),
(188,	'SN',	'SENEGAL',	'Senegal',	'SEN',	686,	221,	'Sénégal'),
(189,	'CS',	'SERBIA AND MONTENEGRO',	'Serbia and Montenegro',	NULL,	NULL,	381,	'Montenegro'),
(190,	'SC',	'SEYCHELLES',	'Seychelles',	'SYC',	690,	248,	'Seychelles'),
(191,	'SL',	'SIERRA LEONE',	'Sierra Leone',	'SLE',	694,	232,	'Sierra Leone'),
(192,	'SG',	'SINGAPORE',	'Singapore',	'SGP',	702,	65,	'Singapour'),
(193,	'SK',	'SLOVAKIA',	'Slovakia',	'SVK',	703,	421,	'Slovaquie'),
(194,	'SI',	'SLOVENIA',	'Slovenia',	'SVN',	705,	386,	'Slovénie'),
(195,	'SB',	'SOLOMON ISLANDS',	'Solomon Islands',	'SLB',	90,	677,	'Iles Salomon'),
(196,	'SO',	'SOMALIA',	'Somalia',	'SOM',	706,	252,	'Somalie'),
(197,	'ZA',	'SOUTH AFRICA',	'South Africa',	'ZAF',	710,	27,	'Afrique du Sud'),
(198,	'GS',	'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',	'South Georgia and the South Sandwich Islands',	NULL,	NULL,	82,	''),
(199,	'ES',	'SPAIN',	'Spain',	'ESP',	724,	34,	'Espagne'),
(200,	'LK',	'SRI LANKA',	'Sri Lanka',	'LKA',	144,	94,	'Sri Lanka'),
(201,	'SD',	'SUDAN',	'Sudan',	'SDN',	736,	249,	'Soudan'),
(202,	'SR',	'SURINAME',	'Suriname',	'SUR',	740,	597,	'Suriname'),
(203,	'SJ',	'SVALBARD AND JAN MAYEN',	'Svalbard and Jan Mayen',	'SJM',	744,	0,	''),
(204,	'SZ',	'SWAZILAND',	'Swaziland',	'SWZ',	748,	268,	'Swaziland'),
(205,	'SE',	'SWEDEN',	'Sweden',	'SWE',	752,	46,	'Suède'),
(206,	'CH',	'SWITZERLAND',	'Switzerland',	'CHE',	756,	41,	'Suisse'),
(207,	'SY',	'SYRIAN ARAB REPUBLIC',	'Syrian Arab Republic',	'SYR',	760,	963,	'Syrie'),
(208,	'TW',	'TAIWAN, PROVINCE OF CHINA',	'Taiwan, Province of China',	'TWN',	158,	886,	'Taiwan'),
(209,	'TJ',	'TAJIKISTAN',	'Tajikistan',	'TJK',	762,	992,	'Tadjikistan'),
(210,	'TZ',	'TANZANIA, UNITED REPUBLIC OF',	'Tanzania, United Republic of',	'TZA',	834,	255,	'Tanzanie'),
(211,	'TH',	'THAILAND',	'Thailand',	'THA',	764,	66,	'Thaïlande'),
(212,	'TL',	'TIMOR-LESTE',	'Timor-Leste',	NULL,	NULL,	670,	''),
(213,	'TG',	'TOGO',	'Togo',	'TGO',	768,	228,	'Togo'),
(214,	'TK',	'TOKELAU',	'Tokelau',	'TKL',	772,	690,	'Tokélaou'),
(215,	'TO',	'TONGA',	'Tonga',	'TON',	776,	676,	'Tonga'),
(216,	'TT',	'TRINIDAD AND TOBAGO',	'Trinidad and Tobago',	'TTO',	780,	1868,	''),
(217,	'TN',	'TUNISIA',	'Tunisia',	'TUN',	788,	216,	'Tunisie'),
(218,	'TR',	'TURKEY',	'Turkey',	'TUR',	792,	90,	'Turquie'),
(219,	'TM',	'TURKMENISTAN',	'Turkmenistan',	'TKM',	795,	993,	'Turkménistan'),
(220,	'TC',	'TURKS AND CAICOS ISLANDS',	'Turks and Caicos Islands',	'TCA',	796,	1649,	'Iles Turks-et-Caicos'),
(221,	'TV',	'TUVALU',	'Tuvalu',	'TUV',	798,	688,	'Tuvalu'),
(222,	'UG',	'UGANDA',	'Uganda',	'UGA',	800,	256,	'Ouganda'),
(223,	'UA',	'UKRAINE',	'Ukraine',	'UKR',	804,	380,	'Ukraine'),
(224,	'AE',	'UNITED ARAB EMIRATES',	'United Arab Emirates',	'ARE',	784,	971,	'Émirats arabes unis'),
(225,	'GB',	'UNITED KINGDOM',	'United Kingdom',	'GBR',	826,	44,	'Angleterre'),
(226,	'US',	'UNITED STATES',	'United States',	'USA',	840,	1,	'États-Unis'),
(227,	'UM',	'UNITED STATES MINOR OUTLYING ISLANDS',	'United States Minor Outlying Islands',	NULL,	NULL,	1340,	''),
(228,	'UY',	'URUGUAY',	'Uruguay',	'URY',	858,	598,	'Uruguay'),
(229,	'UZ',	'UZBEKISTAN',	'Uzbekistan',	'UZB',	860,	998,	'Ouzbékistan'),
(230,	'VU',	'VANUATU',	'Vanuatu',	'VUT',	548,	678,	'Vanuatu'),
(231,	'VE',	'VENEZUELA',	'Venezuela',	'VEN',	862,	58,	'Venezuela'),
(232,	'VN',	'VIET NAM',	'Viet Nam',	'VNM',	704,	84,	'Viêt Nam'),
(233,	'VG',	'VIRGIN ISLANDS, BRITISH',	'Virgin Islands, British',	'VGB',	92,	1340,	'Iles Vierges britanniques'),
(234,	'VI',	'VIRGIN ISLANDS, U.S.',	'Virgin Islands, U.s.',	'VIR',	850,	1340,	'Iles Vierges américaines'),
(235,	'WF',	'WALLIS AND FUTUNA',	'Wallis and Futuna',	'WLF',	876,	681,	'Wallis-et-Futuna'),
(236,	'EH',	'WESTERN SAHARA',	'Western Sahara',	'ESH',	732,	0,	'Sahara occidental'),
(237,	'YE',	'YEMEN',	'Yemen',	'YEM',	887,	967,	'Yémen'),
(238,	'ZM',	'ZAMBIA',	'Zambia',	'ZMB',	894,	260,	'Zambie'),
(239,	'ZW',	'ZIMBABWE',	'Zimbabwe',	'ZWE',	716,	263,	'Zimbabwe');

ALTER TABLE `dims_mod_newsletter_content`
ADD `template` varchar(100) COLLATE 'utf8_general_ci' NULL AFTER `date_envoi`,
COMMENT='';

-- Cyril 26/09/2013 --
ALTER TABLE `dims_mb_field` ADD `from_model` TINYINT(1)  NULL  DEFAULT '0'  AFTER `level`;

-- Thomas -- 18/07/2013 --
-- Penser à adapter l'id_table --
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES
(NULL, '324', 'address', 'address', 'varchar', '1', '1', '1', '1'),
(NULL, '324', 'address2', 'address2', 'varchar', '1', '1', '1', '1'),
(NULL, '324', 'address3', 'address3', 'varchar', '1', '1', '1', '1'),
(NULL, '324', 'postalcode', 'postalcode', 'int', '1', '1', '1', '1'),
(NULL, '324', 'id_workspace', 'id_workspace', 'int', '0', '0', '1', '1'),
(NULL, '324', 'id_user', 'id_user', 'int', '0', '0', '1', '1'),
(NULL, '324', 'id_module', 'id_module', 'type', '0', '0', '1', '1'),
(NULL, '324', 'id_globalobject', 'id_globalobject', '1', '1', '1', '1', '1');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 2, 'Validated the', '_VALIDATED_THE'),
('', 'system', 1, 'Validé le', '_VALIDATED_THE'),
('', 'system', 2, 'Maturity', '_MATURITY'),
('', 'system', 1, 'Echéance', '_MATURITY');

-- Thomas -- 22/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Ajouter des documents', '_ADD_DOCUMENTS'),
('', 'system', 2, 'Add documents', '_ADD_DOCUMENTS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Glisser/déposer un fichier dans cette zone', '_DRAG_DROP_FILE_IN_THIS_BOX'),
('', 'system', 2, 'Drag/drop a file in this box', '_DRAG_DROP_FILE_IN_THIS_BOX'),
('', 'system', 1, 'Vous pouvez en sélectionner plusieurs à la fois', '_YOU_CAN_SELECT_MULTIPLE_AT_ONCE'),
('', 'system', 2, 'You can select multiple at once', '_YOU_CAN_SELECT_MULTIPLE_AT_ONCE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Poids', '_WEIGHT'),
('', 'system', 2, 'Weight', '_WEIGHT'),
('', 'system', 1, 'Enregistrer les documents', '_SAVE_DOCUMENTS'),
('', 'system', 2, 'Save documents', '_SAVE_DOCUMENTS'),
('', 'system', 1, 'Choisissez un ou plusieurs tags', '_CHOOSE_ONE_OR_MORE_TAGS'),
('', 'system', 2, 'Choose one or more tags', '_CHOOSE_ONE_OR_MORE_TAGS'),
('', 'system', 1, 'Choisissez un répertoire', '_CHOOSE_DIRECTORY'),
('', 'system', 2, 'Choose a directory', '_CHOOSE_DIRECTORY');

-- Thomas -- 23/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Non définie', '_NOT_DEFINED_FEM'),
('', 'system', 2, 'Not defined', '_NOT_DEFINED_FEM');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Déposé le', '_FILED_THE'),
('', 'system', 2, 'Filed the', '_FILED_THE'),
('', 'system', 1, 'dans le répertoire', '_IN_THE_DIRECTORY'),
('', 'system', 2, 'in the directory', '_IN_THE_DIRECTORY');

-- Thomas -- 24/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Attaché par', '_ATTACHED_BY'),
('', 'system', 2, 'Attached by', '_ATTACHED_BY');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'En tant que', 'AS_A'),
('', 'system', 2, 'As a', 'AS_A');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Le lien ce termine le', '_LINK_THAT_COMPLETES_THE'),
('', 'system', 2, 'The link that completes the', '_LINK_THAT_COMPLETES_THE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Créer une adresse ultérieurement', '_CREATE_ADDRESS_LATER'),
('', 'system', 2, 'Create an address later', '_CREATE_ADDRESS_LATER');

-- Thomas -- 26/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'attaché à la fiche de', '_ATTACHED_TO_PLUG_FROM'),
('', 'system', 2, 'attached to the plug from', '_ATTACHED_TO_PLUG_FROM'),
('', 'system', 1, 'Aucune fonction définie', '_NO_FUNCTION_DEFINED'),
('', 'system', 2, 'No function defined', '_NO_FUNCTION_DEFINED');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'La collaboration a pris fin le', '_COLLAB_ENDED_THE'),
('', 'system', 2, 'The collaboration ended the', '_COLLAB_ENDED_THE'),
('', 'system', 1, 'Sous-services', '_SUB_SERVICES'),
('', 'system', 2, 'Sub-services', '_SUB_SERVICES'),
('', 'system', 1, 'Ajouter un sous-services', '_ADD_SUB_SERVICES'),
('', 'system', 2, 'Add a sub-services', '_ADD_SUB_SERVICES'),
('', 'system', 1, 'Nouveau sous-service', '_NEW_SUB_SERVICE'),
('', 'system', 2, 'New sub-service', '_NEW_SUB_SERVICE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Attacher ce contact à {DIMS_TEXT}', '_ATTACH_THIS_CONTACT_TO_XXX'),
('', 'system', 2, 'Attach this contact to {DIMS_TEXT}', '_ATTACH_THIS_CONTACT_TO_XXX'),
('', 'system', 1, 'Recherche du contact', '_SEARCH_CONTACT'),
('', 'system', 2, 'Search contact', '_SEARCH_CONTACT'),
('', 'system', 1, 'Aucun contact correspondant à {DIMS_TEXT} n''a été trouvé.', '_NO_CONTACT_MATCHING_WAS_FOUND'),
('', 'system', 2, 'No contact matching {DIMS_TEXT} was found.', '_NO_CONTACT_MATCHING_WAS_FOUND');

-- Thomas -- 29/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Catégories de tag', '_TAG_CATEGORIES'),
('', 'system', 2, 'Tag categories', '_TAG_CATEGORIES');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Fiche du contact', '_THE_MAINS_PLUG'),
('', 'system', 2, 'The mains plug', '_THE_MAINS_PLUG'),
('', 'system', 1, 'Fiche de la structure', '_SHEET_STRUCTURE'),
('', 'system', 2, 'Sheet structure', '_SHEET_STRUCTURE'),
('', 'system', 1, 'Fiche du service', '_SERVICE_SHEET'),
('', 'system', 2, 'Service sheet', '_SERVICE_SHEET');

ALTER TABLE  `dims_mod_business_contact`
ADD  `facebook` VARCHAR( 255 ) NOT NULL DEFAULT  '',
ADD  `twitter` VARCHAR( 255 ) NOT NULL DEFAULT  '',
ADD  `linkedin` VARCHAR( 255 ) NOT NULL DEFAULT  '',
ADD  `google_plus` VARCHAR( 255 ) NOT NULL DEFAULT  '',
ADD  `viadeo` VARCHAR( 255 ) NOT NULL DEFAULT  '';

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Réseaux sociaux', '_SOCIAL_NETWORKS'),
('', 'system', 2, 'Social networks', '_SOCIAL_NETWORKS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Aucune des adresses précédentes ne correspond', '_NONE_PREVIOUS_ADDRESSES_MATCH'),
('', 'system', 2, 'None of the previous addresses match', '_NONE_PREVIOUS_ADDRESSES_MATCH');

-- Thomas -- 30/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Liens contacts', '_LINKS_CONTACTS'),
('', 'system', 2, 'Links contacts', '_LINKS_CONTACTS'),
('', 'system', 1, 'Associer cette adresse à tous les contacts de la structure', '_ASSOCIATE_ADDRESS_TO_ALL_CT_OF_STRUCTURE'),
('', 'system', 2, 'Associate this address to all contacts of the structure', '_ASSOCIATE_ADDRESS_TO_ALL_CT_OF_STRUCTURE'),
('', 'system', 1, 'Des informations sont manquantes sur cette fiche', '_INFORMATIONS_MISSING_ON_THIS_SHEET'),
('', 'system', 2, 'Information is missing on this sheet', '_INFORMATIONS_MISSING_ON_THIS_SHEET'),
('', 'system', 1, 'Aucun numéro de téléphone renseigné', '_NO_NUM_TEL_PROVIDE'),
('', 'system', 2, 'No telephone number provide', '_NO_NUM_TEL_PROVIDE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Recherche avec tags', '_SEARCH_WITH_TAGS'),
('', 'system', 2, 'Search with tags', '_SEARCH_WITH_TAGS');


-- Thomas -- 02/08/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Tags géographiques', '_GEOGRAPHICAL_TAGS'),
('', 'system', 2, 'Geographical tags', '_GEOGRAPHICAL_TAGS'),
('', 'system', 1, 'Tag géographique', '_TAG_GEOGRAPHICAL'),
('', 'system', 2, 'Geographical tag', '_TAG_GEOGRAPHICAL');

-- Thomas -- 06/08/2013 --
ALTER TABLE  `dims_matrix` ADD  `timestp_create` BIGINT( 14 ) NOT NULL DEFAULT  '0',
ADD  `timestp_end` BIGINT( 14 ) NOT NULL DEFAULT  '0',
ADD  `id_city` INT( 11 ) NOT NULL DEFAULT  '0',
ADD  `id_address` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `dims_matrix` ADD INDEX (  `timestp_create` ) ;
ALTER TABLE  `dims_matrix` ADD INDEX (  `timestp_end` ) ;
ALTER TABLE  `dims_matrix` ADD INDEX (  `id_city` ) ;
ALTER TABLE  `dims_matrix` ADD INDEX (  `id_address` ) ;

-- Thomas -- 07/08/2013 --
ALTER TABLE  `dims_matrix` ADD  `id_tag` INT( 11 ) NOT NULL DEFAULT  '0' COMMENT  'id et non id_globalobject';
ALTER TABLE  `dims_matrix` ADD INDEX (  `id_tag` ) ;
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Recherche de communes', '_SEARCH_FOR_CITIES'),
('', 'system', 2, 'Search for cities', '_SEARCH_FOR_CITIES'),
('', 'system', 1, 'Communes liées', '_RELATED_CITIES'),
('', 'system', 2, 'Related cities', '_RELATED_CITIES'),
('', 'system', 1, 'Tags temporels', '_TEMPORAL_TAGS'),
('', 'system', 2, 'Temporal tags', '_TEMPORAL_TAGS'),
('', 'system', 1, 'Sélectionnez un mois et une année', '_SELECT_MONTH_AND_YEAR'),
('', 'system', 2, 'Select a month and a year', '_SELECT_MONTH_AND_YEAR');

-- Thomas -- 04/09/2013 --
ALTER TABLE  `dims_mod_business_action` ADD  `id_city` INT( 11 ) NOT NULL DEFAULT  '0';

-- Thomas -- 30/09/2013 --
ALTER TABLE `dims_tag_category` ADD `type_tag` INT(2) NOT NULL DEFAULT '0';
INSERT INTO `dims_module_type` (`id` ,`label` ,`system` ,`publicparam` ,`description` ,`version` ,`author` ,`date` ,`contenttype`) VALUES
(NULL , 'invitation', '0', '1', 'Gestion des invitations', '0.1', 'Netlor', '20133009000000', 'events');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'invitation', 1, 'Invitations', '_INVITATIONS'),
(NULL, 'invitation', 2, 'Invitations', '_INVITATIONS');

-- Thomas -- 01/10/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'invitation', 1, 'Aucune invitation', '_NO_INVITATION'),
(NULL, 'invitation', 2, 'No invitation', '_NO_INVITATION'),
(NULL, 'invitation', 1, 'Nouvelle invitation', '_NEW_INVITATION'),
(NULL, 'invitation', 2, 'New invitation', '_NEW_INVITATION'),
(NULL, 'invitation', 1, 'Édition invitation', '_EDIT_INVITATION'),
(NULL, 'invitation', 2, 'Edit invitation', '_EDIT_INVITATION');;

--Thomas -- 07/10/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'invitation', 1, 'Invitation', '_INVITATION'),
(NULL, 'invitation', 2, 'Invitation', '_INVITATION'),
(NULL, 'invitation', 1, 'Paramètres invitation', '_INVITATION_SETTINGS'),
(NULL, 'invitation', 2, 'Invitation settings', '_INVITATION_SETTINGS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'invitation', 1, 'Vos paramètres ont été sauvegardés.', '_YOUR_SETTINGS_HAVE_BEEN_SAVED'),
(NULL, 'invitation', 2, 'Your settings have been saved.', '_YOUR_SETTINGS_HAVE_BEEN_SAVED'),
(NULL, 'invitation', 1, 'Les données ont été sauvegardées.', '_DATA_HAVE_BEEN_SAVED'),
(NULL, 'invitation', 2, 'The data have been saved.', '_DATA_HAVE_BEEN_SAVED');

--Thomas -- 08/10/2013 --
DROP TABLE IF EXISTS `dims_mod_business_action_accompany`;
CREATE TABLE IF NOT EXISTS `dims_mod_business_action_accompany` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_action` int(11) NOT NULL,
  `id_reponse` int(11) NOT NULL,
  `id_action_child` INT( 11 ) NOT NULL,
  `name` varchar(255) NOT NULL,
  `age` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- Thomas -- 09/10/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'invitation', 1, 'Réponses', '_ANSWERS'),
(NULL, 'invitation', 2, 'Answers', '_ANSWERS'),
(NULL, 'invitation', 1, 'Date sélectionnée', '_SELECTED_DATE'),
(NULL, 'invitation', 2, 'Selected date', '_SELECTED_DATE'),
(NULL, 'invitation', 1, 'Aucune réponse', '_NO_ANSWER'),
(NULL, 'invitation', 2, 'No answer', '_NO_ANSWER');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'invitation', 1, 'Accompagnants max', '_ACCOMPANYING_PERSONS_MAX'),
(NULL, 'invitation', 2, 'Accompanying persons max', '_ACCOMPANYING_PERSONS_MAX');

-- Thomas -- 11/10/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Région', '_REGION'),
(NULL, 'system', 2, 'Region', '_REGION'),
(NULL, 'system', 1, 'Département', '_DEPARTMENT'),
(NULL, 'system', 2, 'Department', '_DEPARTMENT'),
(NULL, 'system', 1, 'Canton', '_CANTON'),
(NULL, 'system', 2, 'Canton', '_CANTON'),
(NULL, 'system', 1, 'Arrondissement', '_ARRONDISSEMENT'),
(NULL, 'system', 2, 'Arrondissement', '_ARRONDISSEMENT');

-- Thomas -- 16/10/2013 --
ALTER TABLE `dims_mod_business_contact` ADD `num_enregistrement` VARCHAR( 255 ) NOT NULL DEFAULT '';
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES
(NULL, '82', 'facebook', 'facebook', 'varchar', '1', '1', '1', '1'),
(NULL, '82', 'twitter', 'twitter', 'varchar', '1', '1', '1', '1'),
(NULL, '82', 'linkedin', 'linkedin', 'varchar', '1', '1', '1', '1'),
(NULL, '82', 'google_plus', 'google_plus', 'varchar', '1', '1', '1', '1'),
(NULL, '82', 'viadeo', 'viadeo', 'varchar', '1', '1', '1', '1'),
(NULL, '82', 'num_enregistrement', 'num_enregistrement', 'varchar', '1', '1', '1', '1');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'N° d''enregistrement', '_REGISTRATION_NO'),
(NULL, 'system', 2, 'Registration No.', '_REGISTRATION_NO');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Civilité', '_CIVILITY'),
(NULL, 'system', 2, 'Civility', '_CIVILITY');
ALTER TABLE `dims_mod_business_contact` ADD `title` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `civilite`;
ALTER TABLE `dims_mod_business_address_link` ADD `fax` VARCHAR( 32 ) NOT NULL DEFAULT '';
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES
(NULL, '74', 'abrege', '_ACRONYMS', 'varchar', '1', '1', '1', '1');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Acronymes', '_ACRONYMS'),
(NULL, 'system', 2, 'Acronyms', '_ACRONYMS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Catégorie planning', '_CATEGORY_PLANNING'),
(NULL, 'system', 2, 'Category planning', '_CATEGORY_PLANNING'),
(NULL, 'system', 1, 'Organisation des rendez-vous', '_ORGANISE_MEETINGS'),
(NULL, 'system', 2, 'Organise meetings', '_ORGANISE_MEETINGS');

-- Cyril 26/09/2013 --
ALTER TABLE `dims_mb_field` ADD `from_model` TINYINT(1)  NULL  DEFAULT '0'  AFTER `level`;

-- Thomas -- 18/07/2013 --
-- Penser à adapter l'id_table --
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES
(NULL, '324', 'address', 'address', 'varchar', '1', '1', '1', '1'),
(NULL, '324', 'address2', 'address2', 'varchar', '1', '1', '1', '1'),
(NULL, '324', 'address3', 'address3', 'varchar', '1', '1', '1', '1'),
(NULL, '324', 'postalcode', 'postalcode', 'int', '1', '1', '1', '1'),
(NULL, '324', 'id_workspace', 'id_workspace', 'int', '0', '0', '1', '1'),
(NULL, '324', 'id_user', 'id_user', 'int', '0', '0', '1', '1'),
(NULL, '324', 'id_module', 'id_module', 'type', '0', '0', '1', '1'),
(NULL, '324', 'id_globalobject', 'id_globalobject', '1', '1', '1', '1', '1');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 2, 'Validated the', '_VALIDATED_THE'),
('', 'system', 1, 'Validé le', '_VALIDATED_THE'),
('', 'system', 2, 'Maturity', '_MATURITY'),
('', 'system', 1, 'Echéance', '_MATURITY');

-- Thomas -- 22/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Ajouter des documents', '_ADD_DOCUMENTS'),
('', 'system', 2, 'Add documents', '_ADD_DOCUMENTS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Glisser/déposer un fichier dans cette zone', '_DRAG_DROP_FILE_IN_THIS_BOX'),
('', 'system', 2, 'Drag/drop a file in this box', '_DRAG_DROP_FILE_IN_THIS_BOX'),
('', 'system', 1, 'Vous pouvez en sélectionner plusieurs à la fois', '_YOU_CAN_SELECT_MULTIPLE_AT_ONCE'),
('', 'system', 2, 'You can select multiple at once', '_YOU_CAN_SELECT_MULTIPLE_AT_ONCE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Poids', '_WEIGHT'),
('', 'system', 2, 'Weight', '_WEIGHT'),
('', 'system', 1, 'Enregistrer les documents', '_SAVE_DOCUMENTS'),
('', 'system', 2, 'Save documents', '_SAVE_DOCUMENTS'),
('', 'system', 1, 'Choisissez un ou plusieurs tags', '_CHOOSE_ONE_OR_MORE_TAGS'),
('', 'system', 2, 'Choose one or more tags', '_CHOOSE_ONE_OR_MORE_TAGS'),
('', 'system', 1, 'Choisissez un répertoire', '_CHOOSE_DIRECTORY'),
('', 'system', 2, 'Choose a directory', '_CHOOSE_DIRECTORY');

-- Thomas -- 23/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Non définie', '_NOT_DEFINED_FEM'),
('', 'system', 2, 'Not defined', '_NOT_DEFINED_FEM');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Déposé le', '_FILED_THE'),
('', 'system', 2, 'Filed the', '_FILED_THE'),
('', 'system', 1, 'dans le répertoire', '_IN_THE_DIRECTORY'),
('', 'system', 2, 'in the directory', '_IN_THE_DIRECTORY');

-- Thomas -- 24/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Attaché par', '_ATTACHED_BY'),
('', 'system', 2, 'Attached by', '_ATTACHED_BY');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'En tant que', 'AS_A'),
('', 'system', 2, 'As a', 'AS_A');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Le lien ce termine le', '_LINK_THAT_COMPLETES_THE'),
('', 'system', 2, 'The link that completes the', '_LINK_THAT_COMPLETES_THE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Créer une adresse ultérieurement', '_CREATE_ADDRESS_LATER'),
('', 'system', 2, 'Create an address later', '_CREATE_ADDRESS_LATER');

-- Thomas -- 26/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'attaché à la fiche de', '_ATTACHED_TO_PLUG_FROM'),
('', 'system', 2, 'attached to the plug from', '_ATTACHED_TO_PLUG_FROM'),
('', 'system', 1, 'Aucune fonction définie', '_NO_FUNCTION_DEFINED'),
('', 'system', 2, 'No function defined', '_NO_FUNCTION_DEFINED');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'La collaboration a pris fin le', '_COLLAB_ENDED_THE'),
('', 'system', 2, 'The collaboration ended the', '_COLLAB_ENDED_THE'),
('', 'system', 1, 'Sous-services', '_SUB_SERVICES'),
('', 'system', 2, 'Sub-services', '_SUB_SERVICES'),
('', 'system', 1, 'Ajouter un sous-services', '_ADD_SUB_SERVICES'),
('', 'system', 2, 'Add a sub-services', '_ADD_SUB_SERVICES'),
('', 'system', 1, 'Nouveau sous-service', '_NEW_SUB_SERVICE'),
('', 'system', 2, 'New sub-service', '_NEW_SUB_SERVICE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Attacher ce contact à {DIMS_TEXT}', '_ATTACH_THIS_CONTACT_TO_XXX'),
('', 'system', 2, 'Attach this contact to {DIMS_TEXT}', '_ATTACH_THIS_CONTACT_TO_XXX'),
('', 'system', 1, 'Recherche du contact', '_SEARCH_CONTACT'),
('', 'system', 2, 'Search contact', '_SEARCH_CONTACT'),
('', 'system', 1, 'Aucun contact correspondant à {DIMS_TEXT} n''a été trouvé.', '_NO_CONTACT_MATCHING_WAS_FOUND'),
('', 'system', 2, 'No contact matching {DIMS_TEXT} was found.', '_NO_CONTACT_MATCHING_WAS_FOUND');

-- Thomas -- 29/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Catégories de tag', '_TAG_CATEGORIES'),
('', 'system', 2, 'Tag categories', '_TAG_CATEGORIES');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Fiche du contact', '_THE_MAINS_PLUG'),
('', 'system', 2, 'The mains plug', '_THE_MAINS_PLUG'),
('', 'system', 1, 'Fiche de la structure', '_SHEET_STRUCTURE'),
('', 'system', 2, 'Sheet structure', '_SHEET_STRUCTURE'),
('', 'system', 1, 'Fiche du service', '_SERVICE_SHEET'),
('', 'system', 2, 'Service sheet', '_SERVICE_SHEET');

ALTER TABLE  `dims_mod_business_contact`
ADD  `facebook` VARCHAR( 255 ) NOT NULL DEFAULT  '',
ADD  `twitter` VARCHAR( 255 ) NOT NULL DEFAULT  '',
ADD  `linkedin` VARCHAR( 255 ) NOT NULL DEFAULT  '',
ADD  `google_plus` VARCHAR( 255 ) NOT NULL DEFAULT  '',
ADD  `viadeo` VARCHAR( 255 ) NOT NULL DEFAULT  '';

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Réseaux sociaux', '_SOCIAL_NETWORKS'),
('', 'system', 2, 'Social networks', '_SOCIAL_NETWORKS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Aucune des adresses précédentes ne correspond', '_NONE_PREVIOUS_ADDRESSES_MATCH'),
('', 'system', 2, 'None of the previous addresses match', '_NONE_PREVIOUS_ADDRESSES_MATCH');

-- Thomas -- 30/07/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Liens contacts', '_LINKS_CONTACTS'),
('', 'system', 2, 'Links contacts', '_LINKS_CONTACTS'),
('', 'system', 1, 'Associer cette adresse à tous les contacts de la structure', '_ASSOCIATE_ADDRESS_TO_ALL_CT_OF_STRUCTURE'),
('', 'system', 2, 'Associate this address to all contacts of the structure', '_ASSOCIATE_ADDRESS_TO_ALL_CT_OF_STRUCTURE'),
('', 'system', 1, 'Des informations sont manquantes sur cette fiche', '_INFORMATIONS_MISSING_ON_THIS_SHEET'),
('', 'system', 2, 'Information is missing on this sheet', '_INFORMATIONS_MISSING_ON_THIS_SHEET'),
('', 'system', 1, 'Aucun numéro de téléphone renseigné', '_NO_NUM_TEL_PROVIDE'),
('', 'system', 2, 'No telephone number provide', '_NO_NUM_TEL_PROVIDE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Recherche avec tags', '_SEARCH_WITH_TAGS'),
('', 'system', 2, 'Search with tags', '_SEARCH_WITH_TAGS');


-- Thomas -- 02/08/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Tags géographiques', '_GEOGRAPHICAL_TAGS'),
('', 'system', 2, 'Geographical tags', '_GEOGRAPHICAL_TAGS'),
('', 'system', 1, 'Tag géographique', '_TAG_GEOGRAPHICAL'),
('', 'system', 2, 'Geographical tag', '_TAG_GEOGRAPHICAL');

-- Thomas -- 06/08/2013 --
ALTER TABLE  `dims_matrix` ADD  `timestp_create` BIGINT( 14 ) NOT NULL DEFAULT  '0',
ADD  `timestp_end` BIGINT( 14 ) NOT NULL DEFAULT  '0',
ADD  `id_city` INT( 11 ) NOT NULL DEFAULT  '0',
ADD  `id_address` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `dims_matrix` ADD INDEX (  `timestp_create` ) ;
ALTER TABLE  `dims_matrix` ADD INDEX (  `timestp_end` ) ;
ALTER TABLE  `dims_matrix` ADD INDEX (  `id_city` ) ;
ALTER TABLE  `dims_matrix` ADD INDEX (  `id_address` ) ;

-- Thomas -- 07/08/2013 --
ALTER TABLE  `dims_matrix` ADD  `id_tag` INT( 11 ) NOT NULL DEFAULT  '0' COMMENT  'id et non id_globalobject';
ALTER TABLE  `dims_matrix` ADD INDEX (  `id_tag` ) ;
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
('', 'system', 1, 'Recherche de communes', '_SEARCH_FOR_CITIES'),
('', 'system', 2, 'Search for cities', '_SEARCH_FOR_CITIES'),
('', 'system', 1, 'Communes liées', '_RELATED_CITIES'),
('', 'system', 2, 'Related cities', '_RELATED_CITIES'),
('', 'system', 1, 'Tags temporels', '_TEMPORAL_TAGS'),
('', 'system', 2, 'Temporal tags', '_TEMPORAL_TAGS'),
('', 'system', 1, 'Sélectionnez un mois et une année', '_SELECT_MONTH_AND_YEAR'),
('', 'system', 2, 'Select a month and a year', '_SELECT_MONTH_AND_YEAR');

-- Thomas -- 04/09/2013 --
ALTER TABLE  `dims_mod_business_action` ADD  `id_city` INT( 11 ) NOT NULL DEFAULT  '0';

-- Thomas -- 30/09/2013 --
ALTER TABLE `dims_tag_category` ADD `type_tag` INT(2) NOT NULL DEFAULT '0';
INSERT INTO `dims_module_type` (`id` ,`label` ,`system` ,`publicparam` ,`description` ,`version` ,`author` ,`date` ,`contenttype`) VALUES
(NULL , 'invitation', '0', '1', 'Gestion des invitations', '0.1', 'Netlor', '20133009000000', 'events');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'invitation', 1, 'Invitations', '_INVITATIONS'),
(NULL, 'invitation', 2, 'Invitations', '_INVITATIONS');

-- Thomas -- 01/10/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'invitation', 1, 'Aucune invitation', '_NO_INVITATION'),
(NULL, 'invitation', 2, 'No invitation', '_NO_INVITATION'),
(NULL, 'invitation', 1, 'Nouvelle invitation', '_NEW_INVITATION'),
(NULL, 'invitation', 2, 'New invitation', '_NEW_INVITATION'),
(NULL, 'invitation', 1, 'Édition invitation', '_EDIT_INVITATION'),
(NULL, 'invitation', 2, 'Edit invitation', '_EDIT_INVITATION');;

--Thomas -- 07/10/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'invitation', 1, 'Invitation', '_INVITATION'),
(NULL, 'invitation', 2, 'Invitation', '_INVITATION'),
(NULL, 'invitation', 1, 'Paramètres invitation', '_INVITATION_SETTINGS'),
(NULL, 'invitation', 2, 'Invitation settings', '_INVITATION_SETTINGS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'invitation', 1, 'Vos paramètres ont été sauvegardés.', '_YOUR_SETTINGS_HAVE_BEEN_SAVED'),
(NULL, 'invitation', 2, 'Your settings have been saved.', '_YOUR_SETTINGS_HAVE_BEEN_SAVED'),
(NULL, 'invitation', 1, 'Les données ont été sauvegardées.', '_DATA_HAVE_BEEN_SAVED'),
(NULL, 'invitation', 2, 'The data have been saved.', '_DATA_HAVE_BEEN_SAVED');

--Thomas -- 08/10/2013 --
DROP TABLE IF EXISTS `dims_mod_business_action_accompany`;
CREATE TABLE IF NOT EXISTS `dims_mod_business_action_accompany` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_action` int(11) NOT NULL,
  `id_reponse` int(11) NOT NULL,
  `id_action_child` INT( 11 ) NOT NULL,
  `name` varchar(255) NOT NULL,
  `age` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- Thomas -- 09/10/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'invitation', 1, 'Réponses', '_ANSWERS'),
(NULL, 'invitation', 2, 'Answers', '_ANSWERS'),
(NULL, 'invitation', 1, 'Date sélectionnée', '_SELECTED_DATE'),
(NULL, 'invitation', 2, 'Selected date', '_SELECTED_DATE'),
(NULL, 'invitation', 1, 'Aucune réponse', '_NO_ANSWER'),
(NULL, 'invitation', 2, 'No answer', '_NO_ANSWER');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'invitation', 1, 'Accompagnants max', '_ACCOMPANYING_PERSONS_MAX'),
(NULL, 'invitation', 2, 'Accompanying persons max', '_ACCOMPANYING_PERSONS_MAX');

-- Thomas -- 11/10/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Région', '_REGION'),
(NULL, 'system', 2, 'Region', '_REGION'),
(NULL, 'system', 1, 'Département', '_DEPARTMENT'),
(NULL, 'system', 2, 'Department', '_DEPARTMENT'),
(NULL, 'system', 1, 'Canton', '_CANTON'),
(NULL, 'system', 2, 'Canton', '_CANTON'),
(NULL, 'system', 1, 'Arrondissement', '_ARRONDISSEMENT'),
(NULL, 'system', 2, 'Arrondissement', '_ARRONDISSEMENT');

-- Thomas -- 16/10/2013 --
ALTER TABLE `dims_mod_business_contact` ADD `num_enregistrement` VARCHAR( 255 ) NOT NULL DEFAULT '';
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES
(NULL, '82', 'facebook', 'facebook', 'varchar', '1', '1', '1', '1'),
(NULL, '82', 'twitter', 'twitter', 'varchar', '1', '1', '1', '1'),
(NULL, '82', 'linkedin', 'linkedin', 'varchar', '1', '1', '1', '1'),
(NULL, '82', 'google_plus', 'google_plus', 'varchar', '1', '1', '1', '1'),
(NULL, '82', 'viadeo', 'viadeo', 'varchar', '1', '1', '1', '1'),
(NULL, '82', 'num_enregistrement', 'num_enregistrement', 'varchar', '1', '1', '1', '1');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'N° d''enregistrement', '_REGISTRATION_NO'),
(NULL, 'system', 2, 'Registration No.', '_REGISTRATION_NO');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Civilité', '_CIVILITY'),
(NULL, 'system', 2, 'Civility', '_CIVILITY');
ALTER TABLE `dims_mod_business_contact` ADD `title` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `civilite`;
ALTER TABLE `dims_mod_business_address_link` ADD `fax` VARCHAR( 32 ) NOT NULL DEFAULT '';
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`) VALUES
(NULL, '74', 'abrege', '_ACRONYMS', 'varchar', '1', '1', '1', '1');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Acronymes', '_ACRONYMS'),
(NULL, 'system', 2, 'Acronyms', '_ACRONYMS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Catégorie planning', '_CATEGORY_PLANNING'),
(NULL, 'system', 2, 'Category planning', '_CATEGORY_PLANNING'),
(NULL, 'system', 1, 'Organisation des rendez-vous', '_ORGANISE_MEETINGS'),
(NULL, 'system', 2, 'Organise meetings', '_ORGANISE_MEETINGS');

-- Cyril 17/09/2013 --
ALTER TABLE `dims_object_historic_action` CHANGE `tablename` `id_table` INT(11)  NULL  DEFAULT '0';
ALTER TABLE `dims_object_historic_action` ADD INDEX (`id_table`);
ALTER TABLE `dims_object_historic_action` CHANGE `id_table` `ref_globalobject` INT(11)  NULL  DEFAULT '0';
DROP TABLE `dims_object_historic_ids`;
ALTER TABLE `dims_object_historic_action` DROP INDEX `id_table`;
ALTER TABLE `dims_object_historic_action` ADD INDEX (`ref_globalobject`);
ALTER TABLE `dims_object_historic_action` ADD `go_reference` INT(11)  NULL  DEFAULT 0 AFTER `ref_globalobject`;
ALTER TABLE `dims_object_historic_action` ADD `type` ENUM('C', 'D', 'U')  NULL  DEFAULT NULL AFTER `go_reference`;
ALTER TABLE `dims_object_historic_action` ADD `code` CHAR(15)  NULL  DEFAULT NULL  AFTER `type`;
ALTER TABLE `dims_object_historic_action` ADD INDEX (`type`);
ALTER TABLE `dims_object_historic_action` ADD INDEX (`code`);

ALTER TABLE `dims_object_historic_action` ADD `reference_to` INT(11)  NULL  DEFAULT '0'  AFTER `ref_globalobject`;
ALTER TABLE `dims_object_historic_action` CHANGE `reference_to` `go_reference` INT(11)  NULL  DEFAULT '0';
ALTER TABLE `dims_object_historic_action` ADD INDEX (`go_reference`);

-- Cyril 26/09/2013 --
ALTER TABLE `dims_mb_field` ADD `from_model` TINYINT(1)  NULL  DEFAULT '0'  AFTER `level`;

-- Thomas -- 24/10/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Ajouter dans la liste ', '_ADD_IT_TO_THE_LIST'),
(NULL, 'system', 2, 'Add it to the list', '_ADD_IT_TO_THE_LIST'),
(NULL, 'system', 1, 'Modifier et ajouter dans la liste', '_MODIFY_AND_ADD_TO_LIST'),
(NULL, 'system', 2, 'Modify an add it to the list', '_MODIFY_AND_ADD_TO_LIST');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Aucun mail renseigné', '_NO_MAIL_FILLED'),
(NULL, 'system', 2, 'No mail filled', '_NO_MAIL_FILLED');

-- Thomas -- 25/10/2013 --
ALTER TABLE `dims_mod_rssrequest` DROP INDEX `id`;
ALTER TABLE `dims_mod_rssrequest` DROP INDEX `id_2`;
TRUNCATE `dims_mod_rsspref`;
TRUNCATE `dims_mod_rsscat`;
TRUNCATE `dims_mod_rssfeed`;
TRUNCATE `dims_mod_rsscache`;
TRUNCATE `dims_mod_rssrequest`;
ALTER TABLE `dims_mod_rssfeed` ADD `id_object` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_rssfeed` ADD `ico` VARCHAR( 255 ) NOT NULL DEFAULT '';
ALTER TABLE `dims_mod_rsscache` ADD `guid` VARCHAR( 255 ) NOT NULL AFTER `id_rssfeed`;
ALTER TABLE `dims_mod_wce_object_corresp` ADD `id_rss` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_rsscache` ADD `id_object` INT( 11 ) NOT NULL DEFAULT '0';

-- Thomas -- 29/10/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Enquêtes', '_INVESTIGATIONS'),
(NULL, 'system', 2, 'Investigations', '_INVESTIGATIONS');
INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES
(NULL, 'Dernière newsletter', '1', '?op=''last_newsletter''', NULL, NULL, NULL, NULL),
(NULL, 'Historique des newsletters', '1', '?op=''history_newsletter''', NULL, NULL, NULL, NULL);

-- Cyril -- 07/11/2013 --
ALTER TABLE `dims_mb_object_relation` CHANGE `type` `type` ENUM('has_many','belongs_to','has_one')  CHARACTER SET utf8  NULL  DEFAULT 'belongs_to';
ALTER TABLE `dims_mb_object_relation` CHANGE `type` `type` ENUM('has_many','belongs_to','has_one', 'has_many_through')  CHARACTER SET utf8  NULL  DEFAULT 'belongs_to';

-- Ben - 02/12/2013 -- Jeu de données propre repart d'ici !!!!
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'L\'article a été créé avec succès', `dims_constant`.`phpvalue` = 'ARTICLE_HAS_BEEN_CREATED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'The article has been created with success', `dims_constant`.`phpvalue` = 'ARTICLE_HAS_BEEN_CREATED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'L\'article a été modifié avec succès', `dims_constant`.`phpvalue` = 'ARTICLE_HAS_BEEN_UPDATED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'The article has been updated with success', `dims_constant`.`phpvalue` = 'ARTICLE_HAS_BEEN_UPDATED';

ALTER TABLE `dims_mod_cata_famille` ADD `fields1` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields2` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields3` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields4` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields5` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields6` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields7` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields8` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields9` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields10` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields11` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields12` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields13` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields14` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields15` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields16` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields17` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields18` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields19` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields20` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields21` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields22` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields23` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields24` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields25` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields26` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields27` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields28` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields29` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields30` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields31` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields32` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields33` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields34` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields35` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields36` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields37` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields38` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields39` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields40` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields41` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields42` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields43` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields44` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields45` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields46` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields47` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields48` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields49` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields50` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields51` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields52` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields53` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields54` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields55` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields56` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields57` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields58` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields59` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields60` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields61` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields62` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields63` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields64` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields65` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields66` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields67` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields68` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields69` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields70` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields71` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields72` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields73` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields74` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields75` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields76` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields77` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields78` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields79` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields80` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields81` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields82` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields83` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields84` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields85` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields86` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields87` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields88` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields89` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields90` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields91` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields92` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields93` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields94` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields95` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields96` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields97` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields98` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields99` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields100` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields102` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields103` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields104` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields105` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields106` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields107` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields108` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields109` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields110` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields111` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields112` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields113` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields114` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields115` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields116` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields117` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields118` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields119` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields120` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields121` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields122` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields123` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields124` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields125` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields126` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields127` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields128` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields129` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields130` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields131` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields132` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields133` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields134` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields135` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields136` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields137` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields138` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields139` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields140` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields141` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields142` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields143` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields144` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields145` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields146` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields147` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields148` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields149` VARCHAR(50)  NULL  DEFAULT NULL,
ADD `fields150` VARCHAR(50)  NULL  DEFAULT NULL ;

-- Ben - 10/01/2014
ALTER TABLE `dims_mod_cata_famille` ADD `display_mode` SMALLINT(4)  UNSIGNED  NULL  DEFAULT NULL  AFTER `bg_image`;

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 2, 'Display mode', 'CATA_DISPLAY_MODE'),
	('', 'catalogue', 1, 'Mode d\'affichage', 'CATA_DISPLAY_MODE');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 2, 'Comparator', 'CATA_COMPARATOR'),
	('', 'catalogue', 1, 'Comparateur', 'CATA_COMPARATOR');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 2, 'CMS', 'CATA_CMS'),
	('', 'catalogue', 1, 'CMS', 'CATA_CMS');

-- Ben - 13/01/2014
ALTER TABLE `dims_mod_business_tiers` ADD `id_city` INT(11)  NOT NULL  DEFAULT '0'  AFTER `ville`;
ALTER TABLE `dims_mod_business_tiers` ADD `ent_nic` VARCHAR(5)  NULL  DEFAULT NULL  AFTER `ent_siren`;
ALTER TABLE `dims_mod_business_tiers` ADD `ent_ape` VARCHAR(10)  NULL  DEFAULT NULL  AFTER `ent_nic`;

ALTER TABLE  `dims_mod_business_tiers` ADD  `bank` VARCHAR( 255 ) NOT NULL DEFAULT  '',
ADD  `bank_domici` VARCHAR( 255 ) NOT NULL DEFAULT  '',
ADD  `rib_b` VARCHAR( 5 ) NOT NULL DEFAULT  '',
ADD  `rib_g` VARCHAR( 5 ) NOT NULL DEFAULT  '',
ADD  `rib_c` VARCHAR( 11 ) NOT NULL DEFAULT  '',
ADD  `rib_r` VARCHAR( 2 ) NOT NULL DEFAULT  '',
ADD  `iban` VARCHAR( 50 ) NOT NULL DEFAULT  '',
ADD  `bics` VARCHAR( 40 ) NOT NULL DEFAULT  '';

-- Ben - 14/01/2014
UPDATE `dims_mod_cata_famille` SET `visible` = 1;

-- Ben - 20/01/2014
INSERT INTO `dims_mod_cata_tva` (`id_tva`, `id_pays`, `tx_tva`, `timestp_create`, `timestp_modify`, `id_module`, `id_user`, `id_workspace`)
VALUES
	(1, 73, 5.50, 20140120172143, 20140120172143, 355, 65, 64),
	(2, 73, 20.00, 20140120172149, 20140120172149, 355, 65, 64);
UPDATE `dims_mod_cata_article` SET `ctva` = 2;

-- Ben - 04/02/2014
CREATE TABLE `dims_mod_cata_familles_selections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `famile_id` int(11) unsigned DEFAULT NULL COMMENT 'ID Famille',
  `selection_id` int(11) unsigned DEFAULT NULL COMMENT 'ID Sélection',
  `position` int(11) unsigned DEFAULT NULL COMMENT 'Position de la sélection pour la famille',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `dims_mod_cata_familles_selections_articles` (
  `famille_selection_id` int(11) NOT NULL DEFAULT '0',
  `article_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`famille_selection_id`,`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `dims_mod_cata_selections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT 'Titre de la sélection',
  `template_id` int(11) unsigned DEFAULT NULL COMMENT 'Template de la sélection',
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `dims_mod_cata_selections_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL COMMENT 'Titre du template',
  `doc_id` int(11) unsigned DEFAULT NULL COMMENT 'Document de la GED',
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_lang`, `id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Templates des sélections de familles', 'CATA_FAMILIES_SELECTIONS_TEMPLATES');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Aucun template de sélections de familles', 'CATA_ANY_SELECTION_TEMPLATE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Editer ce template', 'EDIT_THIS_TEMPLATE'),
	('', 'catalogue', 1, 'Supprimer ce template', 'CATA_DELETE_THIS_TEMPLATE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Etes-vous sûr(e) de vouloir supprimer ce template ?', 'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_TEMPLATE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Ajouter un template', 'ADD_TEMPLATE');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Enregistrer le template', 'CATA_SAVE_THE_TEMPLATE'),
	('', 'catalogue', 1, 'Edition d\'un template', 'CATA_TEMPLATE_EDITION'),
	('', 'catalogue', 1, 'Nouveau template', 'CATA_NEW_TEMPLATE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Choisissez la langue pour pouvoir traduire les informations', '_CHOOSE_LANGUAGE_TO_TRANSLATE');

-- Ben - 06/02/2014
DROP TABLE IF EXISTS `dims_mod_cata_selections`;
CREATE TABLE `dims_mod_cata_selections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL COMMENT 'Titre de la sélection',
  `template_id` int(11) unsigned DEFAULT NULL COMMENT 'Template de la sélection',
  `id_user` int(11) unsigned DEFAULT NULL,
  `id_module` int(11) unsigned DEFAULT NULL,
  `id_workspace` int(11) unsigned DEFAULT NULL,
  `timestp_create` bigint(14) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_lang`, `id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Supprimer cette sélection', 'CATA_DELETE_THIS_SELECTION'),
	('', 'catalogue', 1, 'Etes-vous sûr(e) de vouloir supprimer cette sélection ?', 'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_SELECTION'),
	('', 'catalogue', 1, 'Editer cette sélection', 'CATA_EDIT_THIS_SELECTION'),
	('', 'catalogue', 1, 'Enregistrer la sélection', 'CATA_SAVE_THE_SELECTION'),
	('', 'catalogue', 1, 'Edition d\'une sélection', 'CATA_SELECTION_EDITION'),
	('', 'catalogue', 1, 'Nouvelle sélection', 'CATA_NEW_SELECTION'),
	('', 'catalogue', 1, 'Aucune sélection', 'CATA_ANY_SELECTION'),
	('', 'catalogue', 1, 'Ajouter une sélection', 'CATA_ADD_SELECTION'),
	('', 'catalogue', 1, 'Sélections des familles', 'CATA_FAMILIES_SELECTIONS');

ALTER TABLE `dims_mod_cata_familles_selections` CHANGE `famile_id` `family_id` INT(11)  UNSIGNED  NULL  DEFAULT NULL  COMMENT 'ID Famille';

DROP TABLE IF EXISTS `dims_mod_cata_familles_selections_articles`;
CREATE TABLE `dims_mod_cata_familles_selections_articles` (
  `family_id` int(11) unsigned NOT NULL DEFAULT '0',
  `selection_id` int(11) unsigned NOT NULL DEFAULT '0',
  `article_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`family_id`,`selection_id`,`article_id`),
  KEY `family_id` (`family_id`,`selection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Ben - 07/02/2014
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Ajouter une référence', 'CATA_ADD_REFERENCE'),
	('', 'catalogue', 1, 'Sélection', 'CATA_SELECTION'),
	('', 'catalogue', 1, 'Edition des sélections de la famille', 'CATA_FAMILY_SELECTION_EDITION'),
	('', 'catalogue', 1, 'Les sélections d\'articles ne sont disponibles que pour le mode d\'affichage <strong>liste</strong>. Vous pouvez modifier le mode d\'affichage de la famille dans l\'onglet <strong><a href=\"%s\">Propriétés</a></strong>.', 'CATA_ERROR_BAD_DISPLAY_MODE'),
	('', 'catalogue', 1, 'Nb Articles', 'CATA_NB_ARTICLES'),
	('', 'catalogue', 1, 'Sélections', 'CATA_SELECTIONS');

-- Ben - 10/02/2014
UPDATE dims_mod_cata_famille SET display_mode = 1 WHERE ISNULL(display_mode);

-- Ben - 11/02/2014
UPDATE dims_mod_cata_article SET uvente = 1;

-- Ben - 14/02/2014
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Valider ces informations', 'SUBMIT_THESE_INFORMATIONS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Ces informations sont modifiables dans votre espace personnel', 'CATA_INFOS_MODIFIABLES_DANS_ESPACE_PERSO'),
	('', 'catalogue', 1, 'Votre commande', 'CATA_YOUR_ORDER');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Veuillez réessayer', 'CATA_PLEASE_RETRY'),
	('', 'catalogue', 1, 'Votre identifiant et/ou votre mot de passe est incorrect', 'CATA_LOGIN_OR_PASSWORD_INVALID'),
	('', 'catalogue', 1, 'Merci de vous connecter ou de vous inscrire pour pouvoir continuer', 'CATA_TY_CONNECT_OR_SUBSCRIBE_TO_CONTINUE'),
	('', 'catalogue', 1, 'Vous tentez d\'accéder à un espace client réservé', 'CATA_TRY_ACCESS_PRIVATE_SPACE'),
	('', 'catalogue', 1, 'Créer mon compte', 'CATA_CREATE_MY_ACCOUNT'),
	('', 'catalogue', 1, 'Je crée mon compte client', 'CATA_I_CREATE_MY_ACCOUNT'),
	('', 'catalogue', 1, 'Mot de passe perdu', 'CATA_LOST_PASSWORD'),
	('', 'catalogue', 1, 'Connexion', 'CATA_CONNECTION'),
	('', 'catalogue', 1, 'Vous êtes déjà client ? Connectez-vous', 'CATA_ALREADY_CLIENT_CONNECT');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Utiliser les mêmes informations que celles de l\'adresse de facturation', 'CATA_USE_SAME_INFOS_AS_BILLING_ADDRESS'),
	('', 'catalogue', 1, 'Vos informations personnelles', 'CATA_YOUR_PERSONAL_INFORMATIONS'),
	('', 'catalogue', 1, 'Confirmez votre mot de passe', 'CATA_CONFIRM_YOUR_PASSWORD'),
	('', 'catalogue', 1, 'Votre mot de passe', 'CATA_YOUR_PASSWORD'),
	('', 'catalogue', 1, 'Veuillez choisir l\'identifiant et le mot de passe avec lequel vous souhaitez vous connecter sur le site', 'CATA_CHOOSE_LOGIN_AND_PASSWORD_TO_CONNECT'),
	('', 'catalogue', 1, 'Vos identifiants de connexion', 'CATA_YOUR_LOGIN_ID'),
	('', 'catalogue', 1, 'Inscription impossible pour les raisons suivantes', 'CATA_IMPOSSIBLE_INSCRIPTION_FOR_THE_FOLLOWING_REASONS'),
	('', 'catalogue', 1, 'Création de votre compte', 'CATA_CREATION_OF_YOUR_ACCOUNT');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Votre panier est vide', 'CATA_YOUR_CART_IS_EMPTY'),
	('', 'catalogue', 1, 'Valider ma commande', 'CATA_PROCEED_TO_CHECKOUT'),
	('', 'catalogue', 1, 'Recalculer mon panier', 'CATA_RECALCULATE_MY_CART'),
	('', 'catalogue', 1, 'Total TTC', 'CATA_TOTAL_TTC'),
	('', 'catalogue', 1, 'Supprimer la ligne', 'CATA_DROP_THE_LINE'),
	('', 'catalogue', 1, 'Réduire la quantité', 'CATA_REDUCE_QTY'),
	('', 'catalogue', 1, 'Augmenter la quantité', 'CATA_AUGMENT_QTY'),
	('', 'catalogue', 1, 'Somme HT', 'CATA_SUM_HT'),
	('', 'catalogue', 1, 'Prix unitaire HT', 'CATA_UNIT_PRICE_HT'),
	('', 'catalogue', 1, 'Votre panier', 'CATA_YOUR_CART');

-- Ben - 17/02/2014
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Poursuivre mes achats', 'CATA_CONTINUE_SHOPPING');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Votre identifiant', 'CATA_YOUR_LOGIN');

ALTER TABLE `dims_mod_cata_famille` CHANGE `parents` `parents` CHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `dims_mod_cata_famille` ADD INDEX ( `parents` );
ALTER TABLE `dims_mod_cata_article` ADD INDEX ( `status` , `published` , `id` );

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Frais de port HT', 'CATA_FRAIS_PORT_HT'),
	('', 'catalogue', 1, 'Total TVA', 'CATA_TOTAL_TVA'),
	('', 'catalogue', 1, 'Sous-total HT', 'CATA_SS_TOTAL_HT');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Sélectionnez votre mode de paiement', 'SELECT_YOUR_PAYMENT_METHOD');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Vous n\'avez aucun moyen de paiement à disposition. Contactez votre assistance commerciale.', 'YOU_DO_NOT_HAVE_PAYMENT_METHOD_AVAILABLE_CONTACT_YOU_BUSINESS_SUPPORT');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Indiquez-nous ici vos informations spécifiques de livraison (ex. : Cage d’escalier 4A, porte d’entrée située au fond du couloir après les marches…)', 'TELL_US_HERE_YOUR_SPECIFIC_DELIVERY_INFORMATION'),
	('', 'catalogue', 1, '(210 caractères maximum)', '210_CHARACTERS_MAX'),
	('', 'catalogue', 1, 'caractères restants', 'CHARACTERS_LEFT');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'J\'ai lu et j\'accepte les conditions générales de vente du site', 'I_ACCEPT_THE_GENERAL_TERMS_OF_SALE_URL'),
	('', 'catalogue', 1, 'Mettre ma commande en pause', 'PAUSE_MY_ORDER'),
	('', 'catalogue', 1, 'Confirmer cette commande', 'CONFIRM_ORDER');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Vous devez accepter les conditions générales de vente', 'YOU_MUST_ACCEPT_THE_TERMS_AND_CONDITIONS_OF_SALE'),
	('', 'catalogue', 1, 'Vous devez choisir un mode de règlement', 'YOU_MUST_CHOOSE_A_PAYMENT_METHOD');

-- Ben - 18/02/2014
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 1, 'Confirmation de votre commande', 'CATA_YOUR_ORDER_CONFIRMATION');

-- Cyril - 03/03/2014 -
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 2, 'Please control values filled in, including each language', 'PLEASE_VERIFY_EACH_LANG'),
	('', 'catalogue', 1, 'Veuillez contrôler les valeurs saisies, pour chaque langue', 'PLEASE_VERIFY_EACH_LANG');

-- Ben - 03/02/2014
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'catalogue', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'PU TTC', `dims_constant`.`phpvalue` = 'PU_TTC';

ALTER TABLE `dims_mod_cata_famille`
ADD INDEX `id_lang` (`id_lang`);
ALTER TABLE `dims_mod_cata_famille`
ADD INDEX `id` (`id`);

-- Ben - 26/02/2014
ALTER TABLE `dims_mod_cata_famille`
CHANGE `code` `code` char(50) NOT NULL DEFAULT '0' AFTER `description`;

-- Ben - 31/03/2014
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 2, 'Global view', 'CATA_FILTERS_VIEW_GLOBAL'),
	('', 'catalogue', 1, 'Vue globale', 'CATA_FILTERS_VIEW_GLOBAL'),
	('', 'catalogue', 2, 'Filtered view', 'CATA_FILTERS_VIEW_FILTERED'),
	('', 'catalogue', 1, 'Vue filtrée', 'CATA_FILTERS_VIEW_FILTERED');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 2, 'Filters operating mode', 'CATA_FILTERS_OPERATING_MODE'),
	('', 'catalogue', 1, 'Mode de fonctionnement des filtres', 'CATA_FILTERS_OPERATING_MODE');

ALTER TABLE `dims_mod_cata_champ`
ADD `allow_unique_option` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `filtre`;

-- Cyril -- 10/02/2014 --
ALTER TABLE `dims_user` CHANGE `login` `login` VARCHAR(45)  CHARACTER SET utf8  COLLATE utf8_bin  NULL  DEFAULT NULL;

-- Thomas -- 24/10/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Ajouter dans la liste ', '_ADD_IT_TO_THE_LIST'),
(NULL, 'system', 2, 'Add it to the list', '_ADD_IT_TO_THE_LIST'),
(NULL, 'system', 1, 'Modifier et ajouter dans la liste', '_MODIFY_AND_ADD_TO_LIST'),
(NULL, 'system', 2, 'Modify an add it to the list', '_MODIFY_AND_ADD_TO_LIST');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Aucun mail renseigné', '_NO_MAIL_FILLED'),
(NULL, 'system', 2, 'No mail filled', '_NO_MAIL_FILLED');

-- Thomas -- 25/10/2013 --
ALTER TABLE `dims_mod_rssrequest` DROP INDEX `id`;
ALTER TABLE `dims_mod_rssrequest` DROP INDEX `id_2`;
TRUNCATE `dims_mod_rsspref`;
TRUNCATE `dims_mod_rsscat`;
TRUNCATE `dims_mod_rssfeed`;
TRUNCATE `dims_mod_rsscache`;
TRUNCATE `dims_mod_rssrequest`;
ALTER TABLE `dims_mod_rssfeed` ADD `id_object` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_rssfeed` ADD `ico` VARCHAR( 255 ) NOT NULL DEFAULT '';
ALTER TABLE `dims_mod_rsscache` ADD `guid` VARCHAR( 255 ) NOT NULL AFTER `id_rssfeed`;
ALTER TABLE `dims_mod_wce_object_corresp` ADD `id_rss` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_rsscache` ADD `id_object` INT( 11 ) NOT NULL DEFAULT '0';

-- Thomas -- 29/10/2013 --
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL, 'system', 1, 'Enquêtes', '_INVESTIGATIONS'),
(NULL, 'system', 2, 'Investigations', '_INVESTIGATIONS');
INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES
(NULL, 'Dernière newsletter', '1', '?op=''last_newsletter''', NULL, NULL, NULL, NULL),
(NULL, 'Historique des newsletters', '1', '?op=''history_newsletter''', NULL, NULL, NULL, NULL);
-- Simon - 2014-03-06 - upsl
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `phpvalue`, `value`) VALUES
(NULL, 'system', 1, 'ACTIVATE_USER_ACCOUNT', 'Activer le compte utilisateur'),
(NULL, 'system', 2, 'ACTIVATE_USER_ACCOUNT', 'Activate user account');

INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES
(null,    'Account management',   1,  '?op=\'account\'',  NULL,   NULL,   NULL,   NULL);

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `phpvalue`, `value`) VALUES
(NULL, 'system', 1, 'IDENTIFICATION', 'Identification'),
(NULL, 'system', 2, 'IDENTIFICATION', 'Identification');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `phpvalue`, `value`) VALUES
(NULL, 'system', 1, 'THE_MODIFICATIONS_HAVE_BEEN_DONE_SUCCESSFULLY', 'Les modifications ont été effectuées avec succès.'),
(NULL, 'system', 2, 'THE_MODIFICATIONS_HAVE_BEEN_DONE_SUCCESSFULLY', 'The modifications have been done successfully.');

-- Simon - 2014-03-07 - themishome
DROP TABLE IF EXISTS `dims_mod_newsletter_subscribed_options`;
CREATE TABLE IF NOT EXISTS `dims_mod_newsletter_subscribed_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mailinglist` int(11) NOT NULL,
  `id_subscribeduser` int(11) NOT NULL,

  `nomail` TINYINT( 11 ) NOT NULL DEFAULT '0',

  `id_workspace`    int(11) NOT NULL DEFAULT '0',
  `id_module`       int(11) NOT NULL DEFAULT '0',
  `id_user`         int(11) NOT NULL DEFAULT '0',

  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_mailinglist` (`id_mailinglist`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `phpvalue`, `value`) VALUES
(NULL, 'system', 1, 'DEACTIVATE_EMAILS_RECEPTION', 'Désactiver la réception des e-mails.'),
(NULL, 'system', 2, 'DEACTIVATE_EMAILS_RECEPTION', 'Deactivate e-mails reception.');

-- Thomas - 2014-03-20 - add indexation address
INSERT INTO `dims_mb_field` (`id`, `id_table`, `name`, `label`, `type`, `visible`, `indexed`, `protected`, `level`, `from_model`) VALUES
(NULL, '321', 'address', 'address', 'varchar', '1', '1', '1', '1', '0'),
(NULL, '321', 'address2', 'address2', 'varchar', '1', '1', '1', '1', '0'),
(NULL, '321', 'address3', 'address3', 'varchar', '1', '1', '1', '1', '0'),
(NULL, '321', 'postalcode', 'postalcode', 'varchar', '1', '1', '1', '1', '0'),
(NULL, '321', 'country', 'country', 'varchar', '1', '1', '1', '1', '0'),
(NULL, '321', 'bp', 'bp', 'varchar', '1', '1', '1', '1', '0'),
(NULL, '321', 'id', 'id', 'int', '1', '0', '1', '1', '0'),
(NULL, '321', 'id_user', 'id_user', 'int', '1', '0', '1', '1', '0'),
(NULL, '321', 'id_module', 'id_module', 'int', '1', '0', '1', '1', '0'),
(NULL, '321', 'id_workspace', 'id_workspace', 'int', '1', '0', '1', '1', '0'),
(NULL, '321', 'id_globalobject', 'id_globalobject', 'int', '1', '0', '1', '1', '0');

-- Thomas - 2014-03-26 - clear dyn objects
DELETE FROM `dims_mb_wce_object` WHERE `label` like 'Affiche liste' and `id_module_type`=4;
DELETE FROM `dims_mb_wce_object` WHERE label = 'Affichage Articles' and `id_module_type`=2;
DELETE FROM `dims_mb_wce_object` WHERE `id_module_type`=2 and label = 'Liste de toutes les actus';
DELETE FROM `dims_mb_wce_object` WHERE `id_module_type`=2 and label = 'Liste de tous les articles';
DELETE FROM `dims_mb_wce_object` WHERE `id_module_type`=1;
INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES
(112,    'Account management',   1,  '?op=\'account\'',  NULL,   NULL,   NULL,   NULL);

-- Ben - 23/07/2014
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 2, 'Not any article for this link', 'NOT_ANY_ARTICLE_FOR_THIS_LINK'),
	('', 'catalogue', 1, 'Aucun article pour ce lien', 'NOT_ANY_ARTICLE_FOR_THIS_LINK');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 2, 'Add an article for this link', 'ADD_AN_ARTICLE_FOR_THIS_LINK'),
	('', 'catalogue', 1, 'Ajouter un article pour ce lien', 'ADD_AN_ARTICLE_FOR_THIS_LINK');
-- Thomas 30/06/2014
ALTER TABLE `dims_mod_forms` ADD `nb_col` INT( 2 ) NOT NULL DEFAULT '1' AFTER `cms_link`;
ALTER TABLE `dims_mod_forms` ADD `sender` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `pubdate_end`;

-- Thomas 25/07/2014
ALTER TABLE `dims_mod_forms` ADD `nb_fields` INT( 5 ) NOT NULL DEFAULT '0';
UPDATE `dims_mod_forms` f
INNER JOIN (select ff.id_forms, count(ff.id) as nb
FROM dims_mod_forms_field ff
group by ff.id_forms) ff
ON f.id = ff.id_forms
SET f.`nb_fields`= ff.nb

-- Ben - 23/07/2014
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 2, 'Not any article for this link', 'NOT_ANY_ARTICLE_FOR_THIS_LINK'),
	('', 'catalogue', 1, 'Aucun article pour ce lien', 'NOT_ANY_ARTICLE_FOR_THIS_LINK');

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`)
VALUES
	('', 'catalogue', 2, 'Add an article for this link', 'ADD_AN_ARTICLE_FOR_THIS_LINK'),
	('', 'catalogue', 1, 'Ajouter un article pour ce lien', 'ADD_AN_ARTICLE_FOR_THIS_LINK');

-- Ben - 30/07/2014
INSERT INTO `dims_param_type` (`id_module_type`, `name`, `default_value`, `public`, `description`, `label`)
VALUES ('26', 'regles_remises', '1', '0', '', 'Règle de calcul des prix');

UPDATE `dims_param_default` SET `id_module_type` = '26' WHERE (`name` = 'regles_remises' OR `value` = 'regles_remises') AND `id_module` = '355' AND `name` = 'regles_remises' AND `name` = 'regles_remises' COLLATE utf8_bin;

-- Ben - 01/08/2014
ALTER TABLE `dims_mod_cata_client`
ADD `erp_id` varchar(20) COLLATE 'utf8_general_ci' NULL COMMENT 'ID dans l\'ERP' AFTER `code_client`;

ALTER TABLE `dims_mod_business_contact`
ADD `erp_id` varchar(20) COLLATE 'utf8_general_ci' NULL COMMENT 'ID dans l\'ERP',
COMMENT='';

ALTER TABLE `dims_mod_business_contact` ADD INDEX `erp_id` (`erp_id`);

-- Ben - 12/06/2014
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(NULL,	'catalogue',	2,	'Delivery and unloading included (continental France)',	'DELIVERY_AND_UNLOADING_INCLUDED'),
(NULL,	'catalogue',	1,	'Livraison et déchargement compris (toute France continentale)',	'DELIVERY_AND_UNLOADING_INCLUDED'),
(NULL,	'catalogue',	2,	'Delivery included',	'DELIVERY_INCLUDED'),
(NULL,	'catalogue',	1,	'Livraison comprise',	'DELIVERY_INCLUDED');
