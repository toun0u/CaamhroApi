-- Cyril / 03/08/2012 - [GESCOM / Desktop_v2]
DROP TABLE dims_todo;
CREATE TABLE `dims_todo` (   `id` int(11) NOT NULL AUTO_INCREMENT,   `type` tinyint(4) NOT NULL DEFAULT '0',   `considered_as` tinyint(1) DEFAULT '0',   `priority` tinyint(4) NOT NULL DEFAULT '0',   `date` datetime NOT NULL,   `date_validation` datetime DEFAULT NULL,   `is_validator` tinyint(1) DEFAULT '0',   `content` text,   `user_from` int(11) DEFAULT NULL COMMENT 'expediteur',   `user_to` int(11) DEFAULT NULL COMMENT 'destinataire',   `user_by` int(11) DEFAULT NULL COMMENT 'validé par',   `state` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0',   `id_parent` int(11) DEFAULT NULL COMMENT 'tache associée',   `timestp_create` bigint(14) DEFAULT '0',   `timestp_modify` bigint(14) DEFAULT '0',   `id_user` int(11) NOT NULL,   `id_module` int(11) NOT NULL DEFAULT '0',   `id_workspace` int(11) NOT NULL DEFAULT '0',   `id_globalobject_ref` int(11) DEFAULT '0',   `id_globalobject` int(11) DEFAULT '0',   PRIMARY KEY (`id`),   KEY `type` (`user_from`),   KEY `date` (`date`) ) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
DROP TABLE dims_todo_dest;
CREATE TABLE `dims_todo_dest` (   `id` int(11) NOT NULL AUTO_INCREMENT,   `id_todo` int(11) NOT NULL,   `id_user` int(11) NOT NULL,   `flag` tinyint(1) DEFAULT '0',   `type` tinyint(3) DEFAULT '0',   PRIMARY KEY (`id`) ) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

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

-- CYRIL - 07/08/2012 - desktopv2 / todos

ALTER TABLE `dims_todo_dest` CHANGE `flag` `flag` TINYINT(1)  NULL  DEFAULT '0'  COMMENT 'permet de le mettre de côté';
ALTER TABLE `dims_todo_dest` ADD `validated` TINYINT(1)  NULL  DEFAULT '0'  AFTER `flag`;
ALTER TABLE `dims_todo_dest` CHANGE `validated` `validated` TINYINT(1)  NULL  DEFAULT '0'  COMMENT 'indique si le destinataire a accusé la réception du todo';
ALTER TABLE `dims_todo_dest` ADD `date_validation` DATETIME  NULL  AFTER `validated`;

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Requiert la validation de chaque destinataire', `dims_constant`.`phpvalue` = 'VALIDATION_OF_EVERY_ONE_REQUIRED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Require the validation of every recipient', `dims_constant`.`phpvalue` = 'VALIDATION_OF_EVERY_ONE_REQUIRED';

-- CYRIL - 08/08/2012
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Vous n\'avez aucune tâche en cours', `dims_constant`.`phpvalue` = 'NO_TASK_TO_DO';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'You don\'t have any task to do', `dims_constant`.`phpvalue` = 'NO_TASK_TO_DO';

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

INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Types d''activité', `dims_constant`.`phpvalue` = 'ACTIVITY_TYPE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Activity types', `dims_constant`.`phpvalue` = 'ACTIVITY_TYPE';

-- Thomas -- 12/09/2012 --
UPDATE `dims_mb_field` SET `id_module_type`=1 WHERE `tablename` like 'dims_mod_business_action';
UPDATE  `dims_mb_table` SET  `id_module_type` =  '1' WHERE  `dims_mb_table`.`name` =  'dims_mod_business_action' AND  `dims_mb_table`.`id_module_type` =18;
UPDATE  `dims_mb_object` SET  `script` = NULL , `id_module_type` =  '1' WHERE  `dims_mb_object`.`id` =10 AND  `dims_mb_object`.`id_module_type` =18 LIMIT 1;

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
UPDATE  `dims_mb_table` SET  `id_module_type` =  '1' WHERE  `dims_mb_table`.`name` =  'dims_mod_business_suivi' AND  `dims_mb_table`.`id_module_type` =14;
UPDATE  `dims_mb_table` SET  `id_module_type` =  '1' WHERE  `dims_mb_table`.`name` =  'dims_mod_business_suivi_detail' AND  `dims_mb_table`.`id_module_type` =14;
INSERT INTO  `dims_mb_object` (`id` ,`label` ,`script` ,`id_module_type`) VALUES
('31',  'Suivi', NULL ,  '1'),
('34',  'Suivi detail', NULL ,  '1');
INSERT INTO  `dims_mb_field` (`id` ,`tablename` ,`name` ,`label` ,`type` ,`visible` ,`id_module_type` ,`id_object` ,`indexed` ,`protected` ,`level`)VALUES
(NULL ,  'dims_mod_business_suivi',  'id_globalobject',  'id_globalobject',  'int(10) unsigned',  '1',  '1',  '0',  '0',  '1',  '1'),
(NULL ,  'dims_mod_business_suivi_detail',  'id_globalobject',  'id_globalobject',  'int(10) unsigned',  '1',  '1',  '0',  '0',  '1',  '1'),
(NULL ,  'dims_mod_business_suivi',  'id_workspace',  'id_workspace',  'int(10) unsigned',  '1',  '1',  '0',  '0',  '1',  '1'),
(NULL ,  'dims_mod_business_suivi_detail',  'id_user',  'id_user',  'int(10) unsigned',  '1',  '1',  '0',  '0',  '1',  '1'),
(NULL ,  'dims_mod_business_suivi_detail',  'id_module',  'id_module',  'int(10) unsigned',  '1',  '1',  '0',  '0',  '1',  '1'),
(NULL ,  'dims_mod_business_suivi_detail',  'id_workspace',  'id_workspace',  'int(10) unsigned',  '1',  '1',  '0',  '0',  '1',  '1');
UPDATE  `dims_mb_table` SET  `label` =  'dims_mod_business_suivi' WHERE  `dims_mb_table`.`name` =  'dims_mod_business_suivi' AND  `dims_mb_table`.`id_module_type` =1;
UPDATE  `dims_mb_table` SET  `label` =  'dims_mod_business_suivi_detail' WHERE  `dims_mb_table`.`name` =  'dims_mod_business_suivi_detail' AND  `dims_mb_table`.`id_module_type` =1;
DELETE FROM `dims_mb_field` WHERE `dims_mb_field`.`id` = 592;
UPDATE `dims_mb_field` SET `id_object`=31 WHERE `tablename` LIKE 'dims_mod_business_suivi';
UPDATE `dims_mb_field` SET `id_object`=34 WHERE `tablename` LIKE 'dims_mod_business_suivi_detail';
UPDATE  `dims_mb_field` SET  `label` =  '_TYPE' WHERE  `dims_mb_field`.`id` =578;
UPDATE  `dims_mb_field` SET  `label` =  '_DUTY' WHERE  `dims_mb_field`.`id` =579;
UPDATE  `dims_mb_field` SET  `label` =  '_DIMS_LABEL_LABEL' WHERE  `dims_mb_field`.`id` =580;
UPDATE  `dims_mb_field` SET  `label` =  '_DIMS_LABEL_DESCRIPTION' WHERE  `dims_mb_field`.`id` =581;
UPDATE  `dims_mb_field` SET  `label` =  '_TYPE' WHERE  `dims_mb_field`.`id` =596;
UPDATE  `dims_mb_field` SET  `label` =  '_DUTY' WHERE  `dims_mb_field`.`id` =597;
UPDATE  `dims_mb_field` SET  `label` =  '_DIMS_LABEL_GROUP_CODE' WHERE  `dims_mb_field`.`id` =598;
UPDATE  `dims_mb_field` SET  `label` =  '_DIMS_LABEL_LABEL' WHERE  `dims_mb_field`.`id` =599;
UPDATE  `dims_mb_field` SET  `label` =  '_DIMS_LABEL_DESCRIPTION' WHERE  `dims_mb_field`.`id` =600;


-- BEN - 14/09/2012 - sarenov
UPDATE dims_mod_business_action SET typeaction = '_DIMS_EVENT_ACTIVITIES';
UPDATE dims_mod_business_action a, dims_city c SET a.lieu = c.label WHERE c.id = REPLACE(TRIM(a.lieu), 'FRANCE, ', '');
UPDATE dims_mod_business_suivi_detail SET id_workspace = 2;
