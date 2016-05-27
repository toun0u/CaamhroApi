
DROP TABLE IF EXISTS `dims_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `timestp_modify` bigint(14) NOT NULL,
  `comment` text,
  `additional` text,
  `id_room` int(11) NOT NULL DEFAULT '0',
  `nbcomment` int(11) NOT NULL DEFAULT '0',
  `nblink` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_action`
--

LOCK TABLES `dims_action` WRITE;
/*!40000 ALTER TABLE `dims_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_action_intercom`
--

DROP TABLE IF EXISTS `dims_action_intercom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_action_intercom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_object` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_action_intercom`
--

LOCK TABLES `dims_action_intercom` WRITE;
/*!40000 ALTER TABLE `dims_action_intercom` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_action_intercom` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_action_matrix`
--

DROP TABLE IF EXISTS `dims_action_matrix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_action_matrix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_globalobject` int(11) NOT NULL DEFAULT '0',
  `id_action` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  `id_date` bigint(14) NOT NULL DEFAULT '0',
  `id_tag` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_globalobject` (`id_globalobject`),
  KEY `id_user` (`id_user`),
  KEY `id_workspace` (`id_workspace`),
  KEY `id_tag` (`id_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_action_matrix`
--

LOCK TABLES `dims_action_matrix` WRITE;
/*!40000 ALTER TABLE `dims_action_matrix` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_action_matrix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_annotation`
--

DROP TABLE IF EXISTS `dims_annotation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_annotation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` longtext,
  `object_label` varchar(255) DEFAULT NULL,
  `type_annotation` varchar(16) DEFAULT NULL,
  `date_annotation` varchar(14) DEFAULT NULL,
  `private` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `id_record` varchar(255) DEFAULT NULL,
  `id_object` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) DEFAULT NULL,
  `id_module` int(10) unsigned DEFAULT '0',
  `id_module_type` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_module` (`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_annotation`
--

LOCK TABLES `dims_annotation` WRITE;
/*!40000 ALTER TABLE `dims_annotation` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_annotation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_annotation_tag`
--

DROP TABLE IF EXISTS `dims_annotation_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_annotation_tag` (
  `id_annotation` int(10) unsigned NOT NULL DEFAULT '0',
  `id_tag` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_annotation`,`id_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_annotation_tag`
--

LOCK TABLES `dims_annotation_tag` WRITE;
/*!40000 ALTER TABLE `dims_annotation_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_annotation_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_campaign`
--

DROP TABLE IF EXISTS `dims_campaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_campaign` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `description` text,
  `id_user` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `share` tinyint(4) NOT NULL DEFAULT '0',
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `query` text,
  `temporary` tinyint(4) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_workspace` (`id_workspace`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_campaign`
--

LOCK TABLES `dims_campaign` WRITE;
/*!40000 ALTER TABLE `dims_campaign` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_campaign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_campaign_keyword`
--

DROP TABLE IF EXISTS `dims_campaign_keyword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_campaign_keyword` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(10) NOT NULL DEFAULT '0',
  `position` int(10) NOT NULL DEFAULT '0',
  `(` tinyint(2) NOT NULL DEFAULT '0',
  `)` tinyint(2) NOT NULL DEFAULT '0',
  `op` varchar(3) DEFAULT NULL,
  `word` varchar(50) DEFAULT NULL,
  `key` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_campaign` (`id_campaign`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_campaign_keyword`
--

LOCK TABLES `dims_campaign_keyword` WRITE;
/*!40000 ALTER TABLE `dims_campaign_keyword` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_campaign_keyword` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_chat_msg`
--

DROP TABLE IF EXISTS `dims_chat_msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_chat_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_send` int(11) NOT NULL,
  `timestp` varchar(14) DEFAULT NULL,
  `msg` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_chat_msg`
--

LOCK TABLES `dims_chat_msg` WRITE;
/*!40000 ALTER TABLE `dims_chat_msg` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_chat_msg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_chat_users`
--

DROP TABLE IF EXISTS `dims_chat_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_chat_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_chat` int(11) NOT NULL,
  `id_received` int(11) NOT NULL,
  `isread` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_chat_users`
--

LOCK TABLES `dims_chat_users` WRITE;
/*!40000 ALTER TABLE `dims_chat_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_chat_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_connecteduser`
--

DROP TABLE IF EXISTS `dims_connecteduser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_connecteduser` (
  `sid` varchar(32) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `domain` varchar(128) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT '0',
  `workspace_id` int(10) DEFAULT NULL,
  `module_id` int(10) unsigned DEFAULT '0',
  `timestp` bigint(14) DEFAULT '0',
  KEY `user_id` (`user_id`),
  KEY `sid` (`sid`),
  KEY `timestp` (`timestp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_connecteduser`
--

LOCK TABLES `dims_connecteduser` WRITE;
/*!40000 ALTER TABLE `dims_connecteduser` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_connecteduser` ENABLE KEYS */;
UNLOCK TABLES;


DROP TABLE IF EXISTS `dims_constant`;
CREATE TABLE IF NOT EXISTS `dims_constant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moduletype` varchar(50) DEFAULT NULL,
  `id_lang` int(11) NOT NULL,
  `value` text,
  `phpvalue` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_lang` (`id_lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5232 ;

--
-- Dumping data for table `dims_constant`
--

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(3226, 'system', 2, 'Server', '_SERVER'),
(5, 'website_watcher', 1, 'Identifiant', '_WEBSITE_WATCHER_LABEL_LOGINLABEL'),
(3227, 'system', 2, 'Password', '_DIMS_LABEL_PASSWORD'),
(3401, 'system', 2, 'Version', '_DIMS_LABEL_VERSION'),
(3230, 'system', 2, 'Cryptography method', '_DIMS_LABEL_CRYPTO'),
(3413, 'system', 2, 'Move', '_DIMS_LABEL_MOVE'),
(11, 'website_watcher', 1, 'Nombre de versions stock&eacute;e par page', '_WEBSITE_WATCHER_LABEL_SIZELABEL'),
(12, 'website_watcher', 1, 'p. trouv&eacute;e(s)', '_WEBSITE_WATCHER_LABEL_SRCFOUND'),
(13, 'website_watcher', 1, 'p. trouv&eacute;e', '_WEBSITE_WATCHER_LABEL_SRC_ONEFOUND'),
(3476, 'system', 2, 'Modules used', '_DIMS_LABEL_SYSTEM_USABLE_MODULES'),
(15, 'website_watcher', 1, 'Domaine', '_WEBSITE_WATCHER_LABEL_DOMAIN'),
(16, 'website_watcher', 1, 'Source(s)', '_WEBSITE_WATCHER_LABEL_NBSRC'),
(18, 'website_watcher', 1, 'Liste des pages', '_WEBSITE_WATCHER_LABEL_LIST_PAGE'),
(19, 'website_watcher', 1, 'Historique des versions', '_WEBSITE_WATCHER_LABEL_LIST_VERSION'),
(20, 'faq', 1, 'En attente d''une r&eacute;ponse', '_DIMS_FAQ_WAIT'),
(21, 'faq', 1, 'Publi&eacute;e', '_DIMS_FAQ_PUBLISHED'),
(22, 'faq', 1, 'Non publi&eacute;e', '_DIMS_FAQ_NOTPUBLISHED'),
(23, 'faq', 1, 'Contributeurs', '_DIMS_FAQ_CONTRIB'),
(24, 'faq', 1, 'Confirmez vous l''envoi de cette question par email ?', '_DIMS_FAQ_CONFIRM_SENDMAIL'),
(25, 'faq', 1, 'Message envoyeacute;', '_FAQ_SEND_MESSAGE'),
(26, 'article', 1, 'Gestion du Module «LABEL»', '_DIMS_LABEL_PAGE_TITLE'),
(27, 'article', 1, 'pages trouv&eacute;es', '_ARTICLE_LABEL_PAGESFOUND'),
(28, 'article', 1, 'page trouv&eacute;e', '_ARTICLE_LABEL_PAGEFOUND'),
(3177, 'system', 2, 'Not found', '_NO_PAGEFOUND'),
(30, 'agenda', 1, 'Agenda', '_AGENDA_LABELTAB_LIST'),
(3472, 'system', 2, 'Profiles available in this Group', '_DIMS_LABEL_PROFILES_AVAILABLE'),
(33, 'agenda', 1, 'Organisateurs', '_AGENDA_LABELTAB_ORGANIZERS'),
(34, 'agenda', 1, 'Créer une Catégorie', '_AGENDA_CAT_CREATE'),
(35, 'agenda', 1, 'Modifier une Catégorie', '_AGENDA_CAT_MODIFY'),
(3449, 'system', 2, 'Authorized IP', '_DIMS_LABEL_GROUP_ALLOWEDIP'),
(37, 'agenda', 1, 'Créer un Organisateur', '_AGENDA_ORGANIZER_CREATE'),
(38, 'agenda', 1, 'Modifier un Organisateur', '_AGENDA_ORGANIZER_MODIFY'),
(39, 'agenda', 1, 'Liste des Organisateurs', '_AGENDA_ORGANIZER_LIST'),
(40, 'agenda', 1, 'Libellé', '_AGENDA_LABEL_LABEL'),
(43, 'agenda', 1, 'événement(s) trouvé(s)', '_AGENDA_LABEL_NEWSFOUND'),
(44, 'agenda', 1, 'év&egrave;nement trouvé', '_AGENDA_LABEL_NEWFOUND'),
(45, 'agenda', 1, 'Aucun événement trouvé', '_AGENDA_LABEL_NO_NEWFOUND'),
(3160, 'system', 2, 'General Administration', '_GENERAL_ADMINISTRATION'),
(47, 'system', 1, 'Syst&egrave;me', '_DIMS_LABEL_SYSTEM'),
(48, 'system', 1, 'Installation', '_DIMS_LABEL_INSTALLMODULES'),
(50, 'system', 1, 'Listes de diffusion', '_DIMS_LABEL_MAILINGLIST'),
(51, 'system', 1, 'Param&egrave;tres', '_SYSTEM_LABELICON_PARAMS'),
(52, 'system', 1, 'Domaines', '_SYSTEM_LABELICON_DOMAINS'),
(53, 'system', 1, 'Utilisateurs', '_USERS'),
(3233, 'system', 2, 'Tools', '_DIMS_LABEL_TOOLS'),
(56, 'system', 1, 'Indexation', '_SYSTEM_LABELICON_INDEX'),
(57, 'system', 1, 'Profils', '_SYSTEM_LABELICON_PROFILES'),
(58, 'system', 1, 'R&ocirc;les', '_SYSTEM_LABELICON_ROLES'),
(3199, 'system', 2, 'Workspace', '_WORKSPACE'),
(64, 'system', 1, 'Templates pour l''espace', '_DIMS_LABEL_TEMPLATEWORKSPACE_LIST'),
(65, 'system', 1, 'Retour au planning', '_DIMS_PLANNING_RETURN'),
(66, 'system', 1, 'Personnes concern&eacute;es', '_SYSTEM_USER_PARTICIPATE'),
(67, 'system', 1, 'Activer la gestion de projet', '_DIMS_LABEL_PROJECT'),
(68, 'system', 1, 'Activer les messages', '_DIMS_LABEL_ACTIVE_TICKETS'),
(69, 'system', 1, 'Activer le planning', '_DIMS_LABEL_PLANNING'),
(70, 'system', 1, 'Activer les contacts', '_DIMS_LABEL_ACTIVE_CONTACT'),
(71, 'system', 1, 'Gestion des contacts', '_SYSTEM_MANAGE_CONTACT'),
(72, 'system', 1, 'Entreprises', '_DIMS_LABEL_ENTERPRISES'),
(73, 'system', 1, 'Contacts', '_DIMS_LABEL_CONTACTS'),
(74, 'system', 1, 'Resteindre l''accès en https (SSL)', '_DIMS_LABEL_GROUP_SSL'),
(77, 'system', 1, 'Ajouter un domaine', '_DIMS_LABEL_DOMAIN_ADD'),
(78, 'system', 1, 'Domaines pour l''acc&egrave;s backoffice de', '_DIMS_LABEL_BACKOFFICE_DOMAIN_LIST'),
(79, 'system', 1, 'Domaines pour l''acc&egrave;s frontoffice de', '_DIMS_LABEL_FRONTOFFICE_DOMAIN_LIST'),
(80, 'system', 1, 'Activer l''onglet Recherche', '_DIMS_LABEL_SEARCH'),
(81, 'system', 1, 'Activer l''onglet Tickets', '_DIMS_LABEL_TICKET'),
(82, 'system', 1, 'Activer l''onglet Profil', '_DIMS_LABEL_PROFIL'),
(83, 'system', 1, 'Activer l''onglet Annotations', '_DIMS_LABEL_ANNOT'),
(3308, 'system', 2, 'To', '_DIMS_DEST'),
(89, 'system', 1, 'Liste des domaines actifs', '_SYSTEM_DOMAINSLIST'),
(90, 'system', 1, 'Module selectionn&amp;eacute;', '_SYSTEM_MODULESELECTED'),
(91, 'system', 1, 'Paramètres du module', '_SYSTEM_MODULEPARAM'),
(92, 'system', 1, 'Aucun paramètre pour ce module', '_DIMS_LABEL_NOMODULEPARAM'),
(3412, 'system', 2, 'Attach (already linked by a group)', '_DIMS_LABEL_ALREADYATTACH'),
(94, 'system', 1, 'Nom de domaine', '_DIMS_LABEL_DOMAIN'),
(95, 'system', 1, 'domaine d&eacute;jà existant', '_DIMS_LABEL_DOMAIN_ALREADYEXISTS'),
(96, 'system', 1, 'Restriction SSL', '_DIMS_LABEL_SSLACCESS'),
(97, 'system', 1, 'Accès Frontoffice', '_DIMS_LABEL_WEBDOMAIN'),
(98, 'system', 1, 'Accès Backoffice', '_DIMS_LABEL_ADMINDOMAIN'),
(3236, 'system', 2, 'List', '_DIMS_LIST'),
(3411, 'system', 2, 'Attach', '_DIMS_LABEL_ATTACH'),
(3142, 'system', 2, 'Add', '_DIMS_ADD'),
(103, 'system', 1, 'Rattacher un groupe', '_SYSTEM_LABELTAB_GROUPATTACH'),
(104, 'system', 1, 'Gestion des rôles', '_SYSTEM_LABELTAB_ROLEMANAGEMENT'),
(105, 'system', 1, 'Affectation des rôles', '_SYSTEM_LABELTAB_ROLEASSIGNMENT'),
(106, 'system', 1, 'Affectation des groupes', '_SYSTEM_LABELTAB_MULTIPLEROLEASSIGNMENT'),
(107, 'system', 1, 'Gestion des Profils', '_SYSTEM_LABELTAB_PROFILEMANAGEMENT'),
(108, 'system', 1, 'Ajout d''un Profil', '_SYSTEM_LABELTAB_PROFILEADD'),
(112, 'system', 1, 'Importer', '_SYSTEM_LABELTAB_USERIMPORT'),
(115, 'system', 1, 'Libell&eacute;', '_DIMS_LABEL_LABEL'),
(117, 'system', 1, 'Champ', '_DIMS_LABEL_RULEFIELD'),
(118, 'system', 1, 'Opérateur', '_DIMS_LABEL_RULEOPERATOR'),
(119, 'system', 1, 'Valeur', '_DIMS_LABEL_RULEVALUE'),
(121, 'system', 1, 'Modules installés', '_DIMS_LABEL_INSTALLEDMODULES'),
(122, 'system', 1, 'Nouvelles versions', '_DIMS_LABEL_NEWMODULEVERSIONS'),
(123, 'system', 1, 'Nouveaux Modules', '_DIMS_LABEL_UNINSTALLEDMODULES'),
(124, 'system', 1, 'Ajouter un nouveau module', '_DIMS_LABEL_ADDNEWMODULE'),
(125, 'system', 1, 'Permet d''ajouter un nouveau module ''installable'' en envoyant simplement un fichier .zip', '_DIMS_LABEL_ADDNEWMODULE_DESC'),
(126, 'system', 1, '&lt;b&gt;Erreur !&lt;/b&gt; Le répertoire installé n est pas accessible en écriture pour Apache !&lt;br&gt;Effectuez un &lt;a href=\\&quot;?dims_moduleicon=system_tools&amp;op=diagnostic\\&quot;&gt;diagnostic&lt;/a&gt; pour de plus amples informations', '_DIMS_LABEL_ADDNEWMODULE_WARNING'),
(127, 'system', 1, 'Rapport d''installation', '_DIMS_LABEL_INSTALLREPORT'),
(128, 'system', 1, 'Rapport de mise a jour', '_DIMS_LABEL_UPDATEREPORT'),
(129, 'system', 1, 'Outils', '_DIMS_LABEL_TOOLS'),
(130, 'system', 1, 'Analyse des Logs', '_DIMS_LABEL_LOGS'),
(131, 'system', 1, 'Nombre d''inscrits', '_DIMS_LABEL_NBATTACH'),
(2138, 'system', 1, 'Heure de d&eacute;but et de fin', '_DIMS_LABEL_HEURE_DEB_FIN'),
(134, 'system', 1, 'Auteur', '_AUTHOR'),
(136, 'system', 1, 'Version', '_DIMS_LABEL_VERSION'),
(139, 'system', 1, 'Metabase', '_DIMS_LABEL_METABASE'),
(140, 'system', 1, 'Objets WCE', '_DIMS_LABEL_WCEOBJECTS'),
(141, 'system', 1, 'Nom', '_DIMS_LABEL_NAME'),
(142, 'system', 1, 'Actif', '_DIMS_LABEL_ACTIVE'),
(143, 'system', 1, 'Visible', '_DIMS_LABEL_VISIBLE'),
(144, 'system', 1, 'Public', '_DIMS_LABEL_PUBLIC'),
(145, 'system', 1, 'Restreinte', '_DIMS_LABEL_PROTECTED'),
(146, 'system', 1, 'Connexion Auto', '_DIMS_LABEL_AUTOCONNECT'),
(3447, 'system', 2, 'Group ', '_DIMS_LABEL_GROUP_FATHER'),
(148, 'system', 1, 'Herité', '_DIMS_LABEL_HERITED'),
(149, 'system', 1, 'Administration restreinte', '_DIMS_LABEL_ADMINRESTRICTED'),
(150, 'system', 1, 'Pos.', '_DIMS_LABEL_MODULEPOSITION'),
(3237, 'system', 2, 'Modify', '_MODIFY'),
(3238, 'system', 2, 'Begin', '_BEGIN'),
(153, 'system', 1, 'D&eacute;tacher', '_DIMS_LABEL_DETACH'),
(154, 'system', 1, 'Rattacher', '_DIMS_LABEL_ATTACH'),
(155, 'system', 1, 'Rattacher (deja rattache par un groupe)', '_DIMS_LABEL_ALREADYATTACH'),
(156, 'system', 1, 'Deplacer', '_DIMS_LABEL_MOVE'),
(157, 'system', 1, 'Affecter', '_DIMS_LABEL_ASSIGN'),
(158, 'system', 1, 'Vue', '_DIMS_LABEL_VIEWMODE'),
(159, 'system', 1, 'Role', '_DIMS_LABEL_ROLECHOICE'),
(160, 'system', 1, 'Transversale', '_DIMS_LABEL_TRANSVERSE'),
(161, 'system', 1, 'Appliquer l''heritage aux sous-groupes', '_SYSTEM_APPLYHERITAGE'),
(162, 'system', 1, 'Installer', '_DIMS_LABEL_INSTALL'),
(163, 'system', 1, 'Mettre a Jour', '_DIMS_LABEL_UPDATE'),
(164, 'system', 1, 'Desinstaller', '_DIMS_LABEL_UNINSTALL'),
(165, 'system', 1, 'Ajouter un Role', '_DIMS_LABEL_ROLEADD'),
(170, 'system', 1, 'Adresse', '_DIMS_LABEL_ADDRESS'),
(1498, 'system', 1, 'Tél. Bureau', '_DIMS_LABEL_TEL_WORK'),
(173, 'system', 1, 'N&deg; siret', '_DIMS_LABEL_ENT_SIRET'),
(174, 'system', 1, 'D&eacute;but', '_BEGIN'),
(176, 'system', 1, 'Choisissez', '_DIMS_LABEL_CHOOSE'),
(177, 'system', 1, 'Destinataire', '_DIMS_DEST'),
(178, 'system', 1, 'Emetteur', '_DIMS_LABEL_TICKET_EMETTEUR'),
(179, 'system', 1, 'Post&eacute; le', '_DIMS_LABEL_TICKET_DPOST'),
(180, 'system', 1, 'Rep', '_DIMS_LABEL_TICKET_NBREP'),
(182, 'system', 1, 'Pr&eacute;nom', '_FIRSTNAME'),
(183, 'system', 1, 'Login', '_LOGIN'),
(184, 'system', 1, 'Action', '_LABEL_ACTION'),
(185, 'system', 1, 'Utilisateur', '_DIMS_LABEL_USER'),
(186, 'system', 1, 'Groupe', '_GROUP'),
(187, 'system', 1, 'Mot de Passe', '_DIMS_LABEL_PASSWORD'),
(188, 'system', 1, 'Confirmation du Mot de Passe', '_DIMS_LABEL_PASSWORD_CONFIRM'),
(189, 'system', 1, 'Date d''Expiration', '_DIMS_LABEL_EXPIRATION_DATE'),
(190, 'system', 1, 'Fuseau Horaire', '_DIMS_LABEL_TIMEZONE'),
(191, 'system', 1, 'Origine', '_DIMS_LABEL_ORIGIN'),
(192, 'system', 1, 'Niveau', '_DIMS_LABEL_LEVEL'),
(195, 'system', 1, 'Code Postal', '_DIMS_LABEL_CP'),
(3445, 'system', 2, 'Modules used', '_DIMS_LABEL_USEDMODULES'),
(199, 'system', 1, 'Tél. Portable', '_MOBILE'),
(201, 'system', 1, 'Email', '_DIMS_LABEL_EMAIL'),
(202, 'system', 1, 'Copie des tickets par Mail', '_DIMS_LABEL_TICKETSBYEMAIL'),
(203, 'system', 1, 'Fonction', '_DIMS_LABEL_FUNCTION'),
(204, 'system', 1, 'Service', '_SERVICE'),
(209, 'system', 1, 'Parents', '_DIMS_LABEL_PARENTS'),
(212, 'system', 1, 'Ajout d''un sous-groupe', '_DIMS_LABEL_GROUP_ADD'),
(213, 'system', 1, 'Ajout d''un sous-espace', '_DIMS_LABEL_WORKSPACE_ADD'),
(216, 'system', 1, 'Modifier le Groupe', '_DIMS_LABEL_GROUP_MODIFY'),
(217, 'system', 1, 'Modifier l''Espace', '_DIMS_LABEL_WORKSPACE_MODIFY'),
(218, 'system', 1, 'Filtrage / S&eacute;curit&eacute;', '_DIMS_LABEL_FILTERING'),
(220, 'system', 1, 'Acc&egrave;s', '_DIMS_LABEL_ACCESS'),
(222, 'system', 1, 'META Informations', '_DIMS_LABEL_META'),
(223, 'system', 1, 'Modules utilis&eacute;s', '_DIMS_LABEL_USEDMODULES'),
(224, 'system', 1, 'Code', '_DIMS_LABEL_GROUP_CODE'),
(226, 'system', 1, 'Groupe Pre', '_DIMS_LABEL_GROUP_FATHER'),
(228, 'system', 1, 'Habillage', '_DIMS_LABEL_GROUP_SKIN'),
(229, 'system', 1, 'IP Autorises', '_DIMS_LABEL_GROUP_ALLOWEDIP'),
(230, 'system', 1, 'Adresses MAC Autorises', '_DIMS_LABEL_GROUP_ALLOWEDMAC'),
(232, 'system', 1, 'Role obligatoire pour l''acc&egrave;s cet espace', '_DIMS_LABEL_GROUP_MUSTDEFINERULE'),
(234, 'system', 1, 'Activation du Backoffice', '_DIMS_LABEL_GROUP_ADMIN'),
(235, 'system', 1, 'Activation du Frontoffice', '_DIMS_LABEL_GROUP_WEB'),
(238, 'system', 1, 'Profil par d&eacute;faut', '_DIMS_LABEL_USER_PROFILE'),
(3443, 'system', 2, 'Access', '_DIMS_LABEL_ACCESS'),
(240, 'system', 1, 'Liste des domaines Frontoffice', '_DIMS_LABEL_GROUP_WEBDOMAINLIST'),
(241, 'system', 1, 'Liste des domaines Backoffice', '_DIMS_LABEL_GROUP_ADMINDOMAINLIST'),
(242, 'system', 1, 'Cloner ce groupe <LABEL>', '_DIMS_LABEL_CREATE_CLONE'),
(243, 'system', 1, 'Creer un sous-groupe <LABEL>', '_DIMS_LABEL_CREATE_CHILD'),
(244, 'system', 1, 'Creer un Groupe', '_DIMS_LABEL_CREATE_GROUP'),
(245, 'system', 1, 'Supprimer ce groupe <LABEL>', '_DIMS_LABEL_DELETE_GROUP'),
(246, 'system', 1, 'Cloner cet espace <LABEL>', '_DIMS_LABEL_CREATE_CLONE_WORKSPACE'),
(247, 'system', 1, 'Creer un sous-espace <LABEL>', '_DIMS_LABEL_CREATE_CHILD_WORKSPACE'),
(248, 'system', 1, 'Supprimer cet espace <LABEL>', '_DIMS_LABEL_DELETE_WORKSPACE'),
(251, 'system', 1, 'Modules disponibles dans cet Espace', '_DIMS_LABEL_GROUP_AVAILABLE_MODULES'),
(252, 'system', 1, 'Listes de diffusion disponibles dans cet Espace', '_DIMS_LABEL_AVAILABLE_MAILINGLIST'),
(253, 'system', 1, 'Inscript à la liste de diffusion', '_DIMS_LABEL_AVAILABLE_MAILINGLIST_ATTACH'),
(254, 'system', 1, 'Modules utilisables pour cet Espace', '_DIMS_LABEL_GROUP_USABLE_MODULES'),
(255, 'system', 1, 'Proprietes du Module [ <MODULE> ]', '_DIMS_LABEL_MODULE_PROPERTIES'),
(256, 'system', 1, 'Proprietes de la list de diffusion [ <MAILINGLIST> ]', '_DIMS_LABEL_MAILINGLIST_PROPERTIES'),
(257, 'system', 1, 'Param&egrave;tres du Module [ <MODULE> ]', '_DIMS_LABEL_MODULE_PARAMS'),
(258, 'system', 1, 'Module  <MODULE>', '_DIMS_LABEL_MODULE_ROLES'),
(259, 'system', 1, 'Profils disponibles dans ce Groupe', '_DIMS_LABEL_PROFILES_AVAILABLE'),
(260, 'system', 1, 'Mon Profil', '_DIMS_LABEL_MYPROFILE'),
(261, 'system', 1, 'Mes Informations', '_DIMS_LABEL_MYDATAS'),
(262, 'system', 1, 'Mon Compte', '_DIMS_LABEL_MYACCOUNT'),
(264, 'system', 1, 'Gestionnaire du Module', '_DIMS_LABEL_MODULE_ADMINISTRATOR'),
(265, 'system', 1, 'Modules disponibles', '_DIMS_LABEL_SYSTEM_AVAILABLE_MODULES'),
(266, 'system', 1, 'Modules utilisables', '_DIMS_LABEL_SYSTEM_USABLE_MODULES'),
(267, 'system', 1, 'Profil par defaut', '_DIMS_LABEL_DEFAULT_PROFILE'),
(268, 'system', 1, 'Aucun module defini pour ce groupe', '_DIMS_LABEL_NO_MODULE_DEFINED'),
(269, 'system', 1, 'Aucun utilisateur defini pour ce groupe', '_DIMS_LABEL_NO_USER_DEFINED'),
(270, 'system', 1, 'Aucun role defini pour ce module', '_DIMS_LABEL_NO_ROLE_DEFINED'),
(271, 'system', 1, 'Liste des roles pour cet espace', '_DIMS_LABEL_ROLE_LIST'),
(272, 'system', 1, 'Modifier l''affectation des roles', '_DIMS_LABEL_MODIFY_ROLE_ASSIGNMENT'),
(3469, 'system', 2, 'Mailing list properties', '_DIMS_LABEL_MAILINGLIST_PROPERTIES'),
(3244, 'system', 2, 'Result', '_RESULT'),
(275, 'system', 1, 'Etes-vous certain de vouloir\\nsupprimer ce Groupe ?', '_SYSTEM_MSG_CONFIRMGROUPDELETE'),
(276, 'system', 1, 'Etes-vous certain de vouloir\\ndtacher ce Groupe ?', '_SYSTEM_MSG_CONFIRMGROUPDETACH'),
(277, 'system', 1, 'Etes-vous certain de vouloir\\ndtacher cet Utilisateur ?', '_SYSTEM_MSG_CONFIRMUSERDETACH'),
(278, 'system', 1, 'Etes-vous certain de vouloir\\nsupprimer cet Utilisateur ?', '_SYSTEM_MSG_CONFIRMUSERDELETE'),
(279, 'system', 1, 'Etes-vous certain de vouloir\\nsupprimer ce Rôle ?', '_SYSTEM_MSG_CONFIRMROLEDELETE'),
(280, 'system', 1, 'Etes-vous certain de vouloir\\nsupprimer cette r&egrave;gle ?', '_SYSTEM_MSG_CONFIRMRULEDELETE'),
(281, 'system', 1, 'Etes-vous certain de vouloir\\nsupprimer ce Profil ?', '_SYSTEM_MSG_CONFIRMPROFILEDELETE'),
(282, 'system', 1, 'Etes-vous certain de vouloir\\nsupprimer les Logs ?', '_SYSTEM_MSG_CONFIRMLOGDELETE'),
(283, 'system', 1, 'Erreur lors de la saisie du mot de passe.\\nVous devez saisir deux fois le mot de passe', '_SYSTEM_MSG_PASSWORDERROR'),
(284, 'system', 1, 'Erreur lors de la cration de l''utilisateur.\\nCe login existe d&eacute;jà.', '_SYSTEM_MSG_LOGINERROR'),
(285, 'system', 1, 'Erreur lors de la saisie du mot de passe.<BR>Votre mot de passe a &eacute;t&eacute; rejet&eacute; par le syst&egrave;me', '_SYSTEM_MSG_LOGINPASSWORDERROR'),
(286, 'system', 1, 'Etes-vous certain de vouloir d&eacute;tacher ce Module ?', '_SYSTEM_MSG_CONFIRMMODULEDETACH'),
(287, 'system', 1, 'Etes-vous certain de vouloir supprimer ce Module ?', '_SYSTEM_MSG_CONFIRMMODULEDELETE'),
(288, 'system', 1, 'Etes-vous certain de vouloir supprimer cette liste de diffusion ?', '_SYSTEM_MSG_CONFIRMMAILINGLISTDELETE'),
(289, 'system', 1, 'Etes-vous certain de vouloir supprimer cet &eacute;l&eacute;ment ?', '_SYSTEM_MSG_CONFIRMMAILINGLISTATTACHDELETE'),
(291, 'system', 1, 'Etes-vous certain de vouloir desinstaller ce Module ?', '_SYSTEM_MSG_CONFIRMMODULEUNINSTAL'),
(292, 'system', 1, 'Etes-vous certain de vouloir mettre à jour la metabase de ce module ?\\n(Rechargement du fichier XML)', '_SYSTEM_MSG_CONFIRMMBUPDATE'),
(293, 'system', 1, 'Vous ne pouvez pas supprimer ce groupe car il contient des utilisateurs', '_SYSTEM_MSG_INFODELETE_USERS'),
(294, 'system', 1, 'Vous ne pouvez pas supprimer ce groupe car il contient des sous-groupes', '_SYSTEM_MSG_INFODELETE_GROUPS'),
(295, 'system', 1, 'Vous ne pouvez pas supprimer ce groupe car il est prot&eacute;g&eacute;', '_SYSTEM_MSG_PROTECTED_GROUPS'),
(296, 'system', 1, 'Vous n''avez pas les droits suffisants pour cloner cet espace', '_SYSTEM_MSG_CANTCOPYGROUP'),
(297, 'system', 1, 'L''outil  PhpInfo affiche la configuration PHP du serveur', '_SYSTEM_EXPLAIN_PHPINFO'),
(298, 'system', 1, 'L''outil Diagnostic v&eacute;rifie quelques &eacute;l&eacute;ments sources d''erreurs courantes pouvant entraîner des dysfonctionnements de DIMS', '_SYSTEM_EXPLAIN_DIAGNOSTIC'),
(299, 'system', 1, 'L''outil  Utilisateurs Connects  affiche la liste des utilisateurs connects au site en temps rel', '_SYSTEM_EXPLAIN_CONNECTEDUSERS'),
(300, 'system', 1, 'L''outil SqlDump vous permet de t&eacute;l&eacute;charger les données dans un fichier SQL', '_SYSTEM_EXPLAIN_SQLDUMP'),
(302, 'system', 1, 'L''outil Sauvegarde vous permet de créer une sauvegarde complète du système (données + sources) afin de les restaurer à une date ult&eacute;rieure', '_SYSTEM_EXPLAIN_BACKUP'),
(303, 'system', 1, 'L''outil Nettoyage BD vous permet de nettoyer la base de données en supprimant les données inutilisées', '_SYSTEM_EXPLAIN_CLEANDB'),
(304, 'system', 1, 'L''outil Historique des actions vous permet de consulter les actions effectuées par les utilisateurs', '_SYSTEM_EXPLAIN_ACTIONHISTORY'),
(305, 'system', 1, 'Dims est un produit d&eacute;velopp&eacute; par la soci&eacute;t&eacute; <A TARGET=\\"blank\\" HREF=\\"http://www.netlorconcept.com\\">Netlor Concept</A><br>Le logo ainsi que la marque sont d&eacute;pos&e', '_SYSTEM_EXPLAIN_ABOUT'),
(306, 'system', 1, 'D&eacute;termine si la liste de diffusion accepte les inscriptions directes par les utilisateurs', '_SYSTEM_EXPLAIN_MAILINGLIST_PROTECTED'),
(307, 'system', 1, 'D&eacute;termine si la liste de diffusion est accessible aux utilisateurs non connect&eacute;s', '_SYSTEM_EXPLAIN_MAILINGLIST_PUBLIC'),
(308, 'system', 1, 'Nom que portera la liste de diffusion dans l''interface', '_SYSTEM_EXPLAIN_MAILINGLISTNAME'),
(309, 'system', 1, 'Nom que portera le module dans l''interface', '_SYSTEM_EXPLAIN_MODULENAME'),
(310, 'system', 1, 'D&eacute;termine si le module est activ&eacute; ou non (activ&eacute; = utilisable)', '_SYSTEM_EXPLAIN_ACTIVE'),
(311, 'system', 1, 'D&eacute;termine si le module est public ou non (visible par un utilisateur non connect&eacute;)', '_SYSTEM_EXPLAIN_PUBLIC'),
(312, 'system', 1, 'D&eacute;termine si ce module est affich&eacute; par d&eacute;faut  la connexion de l''utilisateur', '_SYSTEM_EXPLAIN_AUTOCONNECT'),
(313, 'system', 1, 'D&eacute;termine si ce module est partag&eacute; pour les sous-groupes', '_SYSTEM_EXPLAIN_SHARED'),
(314, 'system', 1, 'D&eacute;termine si ce module est automatiquement h&eacute;rit&eacute; aux sous-groupes', '_SYSTEM_EXPLAIN_HERITED'),
(315, 'system', 1, 'D&eacute;termine si le module est visible sur l''espace', '_SYSTEM_EXPLAIN_VISIBLE'),
(316, 'system', 1, 'PhpInfo', '_DIMS_LABEL_PHPINFO'),
(317, 'system', 1, 'Diagnostic', '_DIMS_LABEL_DIAGNOSTIC'),
(318, 'system', 1, 'Utilisateur(s) Connect&eacute;(s)', '_DIMS_LABEL_CONNECTEDUSERS'),
(319, 'system', 1, 'SqlDump', '_DIMS_LABEL_SQLDUMP'),
(1515, 'system', 1, 'Un module de gestion de contenu (WCE) est n&eacute;cessaire', '_SYSTEM_LABEL_ALERT_WCE'),
(321, 'system', 1, 'Sauvegarde', '_DIMS_LABEL_BACKUP'),
(322, 'system', 1, 'Nettoyage BD', '_DIMS_LABEL_CLEANDB'),
(3245, 'system', 2, 'History of actions', '_ACTIONHISTORY'),
(325, 'system', 1, 'Contenu de la Page d''Accueil', '_DIMS_LABEL_HOMEPAGECONTENT'),
(3246, 'system', 2, 'Preview', '_PREVIEW'),
(327, 'system', 1, 'Ajouter une ligne', '_DIMS_LABEL_ADDLINE'),
(329, 'system', 1, 'Nombre de colonnes', '_DIMS_LABEL_NBCOLUMNS'),
(330, 'system', 1, 'Titre', '_DIMS_LABEL_TITLE'),
(331, 'system', 1, 'Module', '_MODULE'),
(333, 'system', 1, 'Non d&eacute;fini', '_DIMS_LABEL_UNDEFINED'),
(3247, 'system', 2, 'Size', '_SIZE'),
(339, 'system', 1, 'Instanciation d''un Module  <LABEL>', '_DIMS_LABEL_MODULEINSTANCIATION'),
(340, 'system', 1, 'Suppresion du Module  <LABEL>', '_DIMS_LABEL_MODULEDELETE'),
(341, 'system', 1, 'D&eacute;installation du Module  <LABEL>', '_DIMS_LABEL_MODULEUNINSTALL'),
(342, 'system', 1, 'Suppression de l''utilisateur  <LABELUSER>  du groupe  <LABELGROUP>', '_DIMS_LABEL_USERDETACH'),
(343, 'system', 1, 'Suppression de l''utilisateur  <LABEL>', '_DIMS_LABEL_USERDELETE'),
(345, 'system', 1, 'Accessibilit&eacute;', '_DIMS_LABEL_ACCES'),
(346, 'system', 1, 'Source d''import', '_DIMS_LABEL_IMPORTSRC'),
(348, 'system', 1, 'Lancement de l''indexation', '_DIMS_LABEL_INDEXRUN'),
(349, 'system', 1, 'Cette indexation reinitialise l''ensemble du thesaurus et correspondances. Cette procdure peut prendre un temps important.', '_SYSTEM_EXPLAIN_INDEXRUN'),
(352, 'system', 1, 'Activit&eacute;s', '_DIMS_LABEL_ACTIVITY'),
(353, 'system', 1, 'Activit&eacute;s mens.', '_DIMS_LABEL_ACTIVITY_WORKSPACE'),
(3158, 'system', 2, 'Action (s) for step', '_DIMS_LABEL_ACTIONS_MILESTONE'),
(355, 'system', 1, 'le mois dernier', '_DIMS_LABEL_LAST_MONTH'),
(356, 'system', 1, 'Logs', '_DIMS_ADMIN_LOGS'),
(357, 'system', 1, 'Mots indexes', '_DIMS_LABEL_WORDS_INDEXED'),
(358, 'system', 1, 'mots cles', '_DIMS_LABEL_KEYWORDS'),
(359, 'system', 1, 'Dictionnaire de', '_DIMS_LABEL_DICO'),
(360, 'system', 1, 'Espace de travail par defaut', '_DIMS_LABEL_DEFAULTWORKSPACE'),
(361, 'system', 1, 'Afficher les modules partag&eacute;s', '_DIMS_SHAREDMOD_DISPLAY'),
(362, 'system', 1, 'Cacher les modules partag&eacute;s', '_DIMS_SHAREDMOD_HIDE'),
(363, 'system', 1, 'Actualit&eacute;s des fichiers', '_DIMS_COLLABORATION'),
(365, 'system', 1, 'Espace utilisateurs', '_DIMS_DESKTOP_USER'),
(367, 'system', 1, '&Agrave; valider', '_DIMS_TOVALID'),
(368, 'system', 1, 'Re&ccedil;us', '_DIMS_TOVIEW'),
(371, 'system', 1, 'En attente', '_DIMS_LABEL_SURVEY'),
(372, 'system', 1, 'Nouveaut&eacute;s', '_DIMS_NEWS'),
(1496, 'system', 1, 'Nationalit&eacute;', '_DIMS_LABEL_NATIONALITY'),
(1484, 'system', 1, 'Personnes concernées', '_DIMS_LABEL_CONCERNED'),
(1492, 'system', 1, 'Masculin', '_DIMS_LABEL_CONT_SEXE_M'),
(376, 'system', 1, 'Actualit&eacute;s des projets', '_DIMS_PROJECT'),
(377, 'system', 1, 'Filtre sur les projets', '_DIMS_PROJECT_FILTER'),
(378, 'system', 1, 'Liste des projets', '_DIMS_PROJECTS'),
(379, 'system', 1, 'Tous les projets', '_DIMS_ALL_PROJECTS'),
(380, 'system', 1, '&Eacute;tapes', '_DIMS_MILESTONE'),
(381, 'system', 1, 'T&acirc;ches', '_DIMS_TASKS'),
(382, 'system', 1, 'T&acirc;che', '_DIMS_TASK'),
(384, 'system', 1, 'Nouveau projet', '_DIMS_ADDPROJECT'),
(3248, 'system', 2, 'Project properties', '_PROPERTIES_PROJECT'),
(387, 'system', 1, 'Etes-vous certain de vouloir\\nsupprimer cette t&acirc;che ?', '_DIMS_MSG_CONFIRMTASKDELETE'),
(389, 'system', 1, 'Nouvelle t&acirc;che', '_DIMS_ADDTASK'),
(390, 'system', 1, 'Gantt', '_DIMS_GANTT'),
(392, 'system', 1, 'Profil', '_PROFIL'),
(393, 'system', 1, 'Int&eacute;rêts', '_DIMS_INTEREST'),
(1495, 'system', 1, 'Intelligence', '_DIMS_LABEL_OPROFIL'),
(395, 'system', 1, 'M&eacute;tiers', '_DIMS_WORK'),
(396, 'system', 1, 'Pr&eacute;sentation', '_DIMS_PRESENTATION'),
(397, 'system', 1, '#B00000', '_BUSINESS_COLOR_TIERS'),
(398, 'system', 1, '#0000B0', '_BUSINESS_COLOR_DOSSIER'),
(400, 'system', 1, '#009000', '_BUSINESS_COLOR_INTERLOC'),
(402, 'system', 1, 'Gestion d''affaires', '_BUSINESS_PAGE_TITLE'),
(3249, 'system', 2, 'Calendar', '_PLANNING'),
(418, 'system', 1, 'Organisations', '_BUSINESS_TIERS'),
(419, 'system', 1, 'Organisation', '_BUSINESS_TIER'),
(420, 'system', 1, 'Aucune organisation', '_BUSINESS_NO_TIER'),
(1514, 'system', 1, 'Champs dynamiques pour la fiche Entreprise', '_DIMS_LABEL_DYNFIELD_ENT'),
(424, 'system', 1, 'Rdv', '_BUSINESS_ACTION'),
(425, 'system', 1, 'Aucun rdv', '_BUSINESS_NO_ACTION'),
(426, 'system', 1, '<font color=\\""._BUSINESS_COLOR_DOSSIER."\\">".''Projets''."</font>', '_BUSINESS_DOSSIER'),
(437, 'system', 1, 'Modifier la Fiche', '_BUSINESS_LEGEND_MODIFY'),
(438, 'system', 1, 'Effacer la Fiche', '_BUSINESS_LEGEND_DELETE'),
(439, 'system', 1, 'Supprimer le Lien', '_BUSINESS_LEGEND_CUT'),
(440, 'system', 1, 'Aller vers la Fiche d&eacute;taill&eacute;e', '_BUSINESS_LEGEND_GOTO'),
(441, 'system', 1, 'Non Permis', '_BUSINESS_LEGEND_NOTALLOWED'),
(442, 'system', 1, 'Dupliquer', '_BUSINESS_LEGEND_RENEW'),
(516, 'system', 1, 'Etes vous sûr de vouloir supprimer ''<VALUE>'' ?', '_BUSINESS_MSG_CONFIRMDELETE'),
(517, 'system', 1, 'Etes vous sûr de vouloir renouveler \\\\''<VALUE>\\\\'' ?', '_BUSINESS_MSG_CONFIRMRENEW'),
(518, 'system', 1, 'Etes vous sûr de vouloir d&eacute;tacher \\\\''<VALUE>\\\\'' ?', '_BUSINESS_MSG_CONFIRMCUT'),
(3250, 'system', 2, 'Back to the list of projects', '_PROJECT_LABEL_BACK'),
(3204, 'system', 2, 'Projects', '_LABEL_PROJECTS'),
(521, 'system', 1, 'Nom du projet', '_PROJECT_LABEL_NAME_PROJECT'),
(523, 'system', 1, 'Ajouter un projet', '_PROJECT_LABEL_ADD_PROJECT'),
(527, 'system', 1, 'Visualisation multi-projets', '_PROJECT_LABEL_VIEW_MULTI_PROJECT'),
(528, 'system', 1, 'Visualisation multi-t&acirc;ches', '_PROJECT_LABEL_VIEW_MULTI_TASK'),
(530, 'system', 1, 'Retour à la liste des projets', '_PROJECT_LABEL_BACK'),
(531, 'system', 1, 'Visualisateur', '_PROJECT_LABEL_VISU'),
(532, 'system', 1, 'Zoom : Semaine', '_PROJECT_LABEL_ZOOM_WEEK'),
(533, 'system', 1, 'Zoom : Mois', '_PROJECT_LABEL_ZOOM_MONTH'),
(536, 'system', 1, 'Suivi de projet', '_PROJECT_LABEL_MANAGE_PROJECT'),
(545, 'system', 1, 'Avancement', '_DIMS_LABEL_PROGRESS'),
(548, 'system', 1, 'Resp.', '_PROJECT_MENU_PERSON_IN_CHARGE'),
(1504, 'system', 1, 'Normal', '_DIMS_LABEL_CONT_VIP_N'),
(558, 'system', 1, 'Êtes-vous certain de vouloir clôturer le projet', '_PROJECT_MSG_2'),
(559, 'system', 1, 'Êtes-vous certain de vouloir d&eacute;clôturer le projet', '_PROJECT_MSG_3'),
(566, 'system', 1, 'S&eacute;lection', '_FORM_SELECTION'),
(569, 'system', 1, 'Cr&eacute;ateur :', '_FORM_PROJECT_CREATOR'),
(571, 'system', 1, 'D&eacute;but :', '_FORM_PROJECT_START_DATE'),
(572, 'system', 1, 'Fin :', '_FORM_PROJECT_END_DATE'),
(575, 'system', 1, 'Annuler', '_DIMS_LABEL_CANCEL'),
(3251, 'system', 2, 'End', '_END'),
(579, 'system', 1, 'A faire', '_FORM_TASK_TIME_TODO'),
(580, 'system', 1, 'Effectu&eacute;', '_FORM_TASK_TIME'),
(581, 'system', 1, 'Pr&eacute;vu', '_FORM_TASK_TIME_EXPECTED'),
(582, 'system', 1, 'Possible', '_FORM_TASK_TIME_ENABLED'),
(584, 'system', 1, 'Dur&eacute;e', '_FORM_TASK_DURATION'),
(586, 'system', 1, 'Priorit&eacute;', '_FORM_TASK_PRIORITY'),
(587, 'system', 1, 'Par d&eacute;faut', '_FORM_TASK_PRIORITY_0'),
(588, 'system', 1, 'Moyenne', '_FORM_TASK_PRIORITY_1'),
(589, 'system', 1, 'Importante', '_FORM_TASK_PRIORITY_2'),
(590, 'system', 1, 'Ajouter cette t&acirc;che au projet', '_FORM_TASK_OK'),
(592, 'system', 1, 'Modifier cette t&acirc;che', '_FORM_TASK_MODIFY'),
(594, 'system', 1, 'Attacher', '_FORM_USER_OK'),
(3408, 'system', 2, 'Restricted Administration', '_DIMS_LABEL_ADMINRESTRICTED'),
(597, 'system', 1, 'Date :', '_FORM_OBJECTIVE_DATE'),
(598, 'system', 1, 'Description :', '_FORM_OBJECTIVE_COMMENT'),
(599, 'system', 1, 'Ajouter cet objectif', '_FORM_OBJECTIVE_OK'),
(601, 'system', 1, 'Informations g&eacute;n&eacute;rales', '_INFOS_LABEL'),
(602, 'system', 1, 'Cr&eacute;ateur', '_INFOS_CREATOR'),
(3194, 'system', 2, 'Manager', '_DIMS_LABEL_RESPONSIBLE'),
(604, 'system', 1, 'Date de d&eacute;but', '_INFOS_START_DATE'),
(605, 'system', 1, 'Date de fin', '_INFOS_END_DATE'),
(606, 'system', 1, '&Eacute;tat', '_INFOS_STATE'),
(608, 'system', 1, 'Retard', '_INFOS_TOLATE'),
(609, 'system', 1, 'Lundi', '_MONDAY'),
(610, 'system', 1, 'Mardi', '_THUESDAY'),
(611, 'system', 1, 'Mecredi', '_WEDNESDAY'),
(612, 'system', 1, 'Jeudi', '_THIRDAY'),
(613, 'system', 1, 'Vendredi', '_FRIDAY'),
(614, 'system', 1, 'Samedi', '_SATURDAY'),
(615, 'system', 1, 'Dimanche', '_SUNDAY'),
(616, 'system', 1, 'Janvier', '_JANUARY'),
(617, 'system', 1, 'F&eacute;vrier', '_FEBRUARY'),
(618, 'system', 1, 'Mars', '_MARCH'),
(619, 'system', 1, 'Avril', '_APRIL'),
(620, 'system', 1, 'Mai', '_MAY'),
(621, 'system', 1, 'Juin', '_JUNE'),
(622, 'system', 1, 'Juillet', '_JULY'),
(623, 'system', 1, 'Août', '_AUGUST'),
(624, 'system', 1, 'Septembre', '_SEPTEMBER'),
(625, 'system', 1, 'Octobre', '_OCTOBER'),
(626, 'system', 1, 'Novembre', '_NOVEMBER'),
(627, 'system', 1, 'D&eacute;cembre', '_DECEMBER'),
(628, 'system', 1, 'L', '_MONDAY_SMALL'),
(629, 'system', 1, 'M', '_THUESDAY_SMALL'),
(630, 'system', 1, 'M', '_WEDNESDAY_SMALL'),
(631, 'system', 1, 'J', '_THIRDAY_SMALL'),
(632, 'system', 1, 'V', '_FRIDAY_SMALL'),
(633, 'system', 1, 'S', '_SATURDAY_SMALL'),
(634, 'system', 1, 'D', '_SUNDAY_SMALL'),
(635, 'system', 1, 'J', '_JANUARY_SMALL'),
(636, 'system', 1, 'F', '_FEBRUARY_SMALL'),
(637, 'system', 1, 'M', '_MARCH_SMALL'),
(638, 'system', 1, 'A', '_APRIL_SMALL'),
(639, 'system', 1, 'M', '_MAY_SMALL'),
(640, 'system', 1, 'J', '_JUNE_SMALL'),
(641, 'system', 1, 'J', '_JULY_SMALL'),
(642, 'system', 1, 'A', '_AUGUST_SMALL'),
(643, 'system', 1, 'S', '_SEPTEMBER_SMALL'),
(644, 'system', 1, 'O', '_OCTOBER_SMALL'),
(645, 'system', 1, 'N', '_NOVEMBER_SMALL'),
(646, 'system', 1, 'D', '_DECEMBER_SMALL'),
(1513, 'system', 1, 'Champs dynamiques pour la fiche Contact', '_DIMS_LABEL_DYNFIELD_CONTACT'),
(1512, 'system', 1, 'Cr&eacute;&eacute;r une fiche entreprise', '_DIMS_LABEL_ENT_CREATE'),
(3162, 'system', 2, 'Added', '_ADD'),
(667, 'system', 1, 'D&eacute;finition des usages pour le champ', '_BUSINESS_USE_DEFINEDFIELD'),
(1503, 'system', 1, 'Importance du contact', '_DIMS_LABEL_CONT_VIP'),
(1502, 'system', 1, 'Secteur d''activit&eacute; entreprise employeur', '_DIMS_LABEL_CONT_SECTACT'),
(1511, 'system', 1, 'Cr&eacute;&eacute;r une fiche personne', '_DIMS_LABEL_CONT_CREATE'),
(3252, 'system', 2, 'General information', '_INFOS_LABEL'),
(1494, 'system', 1, 'Fiche contact', '_DIMS_LABEL_CT_FICHE'),
(1491, 'system', 1, 'Sexe', '_DIMS_LABEL_SEXE'),
(691, 'system', 1, 'Intitulé', '_BUSINESS_FIELD_NAME'),
(3254, 'system', 2, 'Name of field', '_FIELD_FIELDNAME'),
(3407, 'system', 2, 'Herited', '_DIMS_LABEL_HERITED'),
(3406, 'system', 2, 'Auto connect', '_DIMS_LABEL_AUTOCONNECT'),
(3255, 'system', 2, 'Values', '_FIELD_VALUES'),
(3256, 'system', 2, 'Max size', '_FORMS_FIELD_MAXLENGTH'),
(3257, 'system', 2, 'Required', '_FIELD_NEEDED'),
(703, 'system', 1, 'Valeur par Défaut', '_BUSINESS_FIELD_DEFAULTVALUE'),
(3258, 'system', 2, 'Form / field', '_FIELD_FORMFIELD'),
(3161, 'system', 2, 'Display multi-column', '_FIELD_MULTICOLDISPLAY'),
(1490, 'system', 1, 'Créé le', '_SYSTEM_LABEL_FICHCREATED'),
(858, 'forms', 1, 'R&eacute;ponse(s)', '_FORMS_RESPONSE'),
(859, 'forms', 1, 'Nom de la Table', '_FORMS_TABLENAME'),
(862, 'forms', 1, 'Date de d&eacute;but de Publication', '_FORMS_PUBDATESTART'),
(863, 'forms', 1, 'Date de fin de Publication', '_FORMS_PUBDATEEND'),
(864, 'forms', 1, 'Envoyer par email à', '_FORMS_EMAIL'),
(865, 'forms', 1, 'Largeur (*: variable)', '_FORMS_WIDTH'),
(866, 'forms', 1, 'Ajout', '_ADD'),
(867, 'forms', 1, 'Modification', '_FORMS_MODIFICATION'),
(868, 'forms', 1, 'Liste des Champs', '_FORMS_FIELDLIST'),
(869, 'system', 1, 'Ajouter un Champ', '_DIMS_LABEL_ADDFIELD'),
(870, 'forms', 1, 'Ajouter un S&eacute;parateur', '_FORMS_ADDSEPARATOR'),
(871, 'forms', 1, 'Liste des formulaires', '_FORMS_LIST'),
(872, 'forms', 1, 'Utiliser le formulaire', '_FORMS_FILL'),
(873, 'forms', 1, 'Modèle d''Affichage', '_FORMS_MODEL'),
(874, 'forms', 1, 'Type de formulaire', '_FORMS_TYPEFORM'),
(875, 'forms', 1, 'Archivage Automatique (En Jours)', '_FORMS_AUTOBACKUP'),
(876, 'forms', 1, 'Obligatoire', '_FORMS_OBLIGATORY'),
(877, 'forms', 1, 'Modification d''un Champ', '_FIELDMODIFICATION'),
(878, 'forms', 1, 'Modification d''un S&eacute;parateur', '_FORMS_SEPARATORMODIFICATION'),
(1483, 'system', 1, 'Limiter la description aux personnes concern&eacute;es', '_DIMS_LABEL_LIMIT_ACTION'),
(3198, 'system', 2, 'Group', '_GROUP'),
(882, 'forms', 1, 'Adresse IP', '_FORMS_IP'),
(3409, 'system', 2, 'Pos.', '_DIMS_LABEL_MODULEPOSITION'),
(884, 'forms', 1, 'Date de Validation', '_FORMS_DATEVALIDATION'),
(885, 'forms', 1, 'Nb Ligne/Page', '_FORMS_NBLINE'),
(886, 'forms', 1, 'Aperçu', '_FORMS_PREVIEW'),
(887, 'forms', 1, 'Voir les R&eacute;sultats', '_FORMS_VIEWRESULT'),
(889, 'forms', 1, 'Filtre', '_FORMS_FILTER'),
(890, 'forms', 1, 'Export', '_FORMS_EXPORT'),
(892, 'forms', 1, 'Intitul&eacute;', '_FORMS_FIELD_NAME'),
(893, 'system', 1, 'Nom du Champ', '_FIELD_FIELDNAME'),
(3462, 'system', 2, 'Add sub workspace', '_DIMS_LABEL_CREATE_CHILD_WORKSPACE'),
(895, 'forms', 1, 'Interligne', '_FORMS_FIELD_INTERLINE'),
(898, 'forms', 1, 'Format', '_FIELD_FORMAT'),
(899, 'forms', 1, 'Valeurs', '_FIELD_VALUES'),
(900, 'forms', 1, 'Taille Maxi', '_FORMS_FIELD_MAXLENGTH'),
(901, 'forms', 1, 'Requis', '_FIELD_NEEDED'),
(902, 'forms', 1, 'Visible en Export', '_FORMS_FIELD_EXPORTVIEW'),
(903, 'forms', 1, 'Visible en Liste', '_FORMS_FIELD_ARRAYVIEW'),
(904, 'forms', 1, 'Valeur par D&eacute;faut', '_FORMS_FIELD_DEFAULTVALUE'),
(905, 'forms', 1, 'Formulaire / Champ', '_FIELD_FORMFIELD'),
(906, 'forms', 1, 'Niveau du S&eacute;parateur', '_FORMS_FIELD_SEPARATOR_LEVEL'),
(907, 'forms', 1, 'Taille de Police (pix)', '_FORMS_FIELD_SEPARATOR_FONTSIZE'),
(908, 'forms', 1, 'S&eacute;parateur de Niveau <LEVEL>', '_FORMS_FIELD_SEPARATOR_DESC'),
(909, 'forms', 1, 'Affichage multi-colonne', '_FIELD_MULTICOLDISPLAY'),
(910, 'forms', 1, 'Req.', '_FORMS_FIELD_NEEDED_SHORT'),
(911, 'forms', 1, 'Vis. Exp.', '_FORMS_FIELD_EXPORTVIEW_SHORT'),
(912, 'forms', 1, 'Vis. Lst.', '_FORMS_FIELD_ARRAYVIEW_SHORT'),
(913, 'forms', 1, 'Vous avez d&eacute;jà rempli ce formulaire', '_FORMS_ALLREADYFILLED'),
(914, 'forms', 1, 'Une seule saisie par utilisateur (ex: sondage)', '_FORMS_OPTION_ONLYONE'),
(915, 'forms', 1, 'Une seule saisie par jour (ex: relev&eacute;s)', '_FORMS_OPTION_ONLYONEDAY'),
(919, 'forms', 1, 'Droit de Modification', '_FORMS_OPTION_MODIFY'),
(920, 'system', 1, 'Personne', '_OPTION_MODIFY_NOBODY'),
(921, 'forms', 1, 'Le propri&eacute;taire', '_FORMS_OPTION_MODIFY_USER'),
(922, 'forms', 1, 'Le groupe', '_FORMS_OPTION_MODIFY_GROUP'),
(923, 'forms', 1, 'Tout le monde', '_FORMS_OPTION_MODIFY_ALL'),
(924, 'forms', 1, 'Exporter les donn&eacute;es', '_FORMS_DATA_EXPORT'),
(925, 'forms', 1, 'Voir les donn&eacute;es saisies', '_FORMS_DATA_VIEW'),
(926, 'forms', 1, 'Vue sur les donn&eacute;es', '_FORMS_OPTION_VIEW'),
(3260, 'system', 2, 'Global', '_OPTION_VIEW_GLOBAL'),
(3261, 'system', 2, 'Private', '_PRIVATE'),
(3264, 'system', 2, 'Delete user', '_DELETE_USER'),
(3262, 'system', 2, 'descending', '_LABEL_VIEWMODE_DESC'),
(931, 'forms', 1, 'Afficher les infos « Utilisateur »', '_FORMS_OPTION_DISPLAY_USER'),
(932, 'forms', 1, 'Afficher les infos « Groupe »', '_FORMS_OPTION_DISPLAY_GROUP'),
(933, 'forms', 1, 'Afficher la Date de Validation', '_FORMS_OPTION_DISPLAY_DATE'),
(934, 'forms', 1, 'Afficher l''Adresse IP', '_FORMS_OPTION_DISPLAY_IP'),
(935, 'forms', 1, '* champs obligatoire', '_FORMS_FIELDNEEDED'),
(936, 'forms', 1, 'Rempli par', '_FORMS_FILLEDBY'),
(937, 'forms', 1, 'Anonyme', '_FORMS_ANONYMOUS'),
(938, 'forms', 1, 'Vous pouvez saisir plusieurs adresses s&eacute;par&eacute;es par le caractère &laquo; ; &raquo;', '_FORMS_HELP_EMAIL'),
(939, 'forms', 1, 'Certaines options sont sp&eacute;cifiques au type de formulaire que vous cr&eacute;ez', '_FORMS_HELP_TYPEFORM'),
(940, 'directory', 1, 'Mes Contacts', '_DIRECTORY_MYCONTACTS'),
(941, 'directory', 1, 'Mon Espace', '_DIRECTORY_MYGROUP'),
(942, 'directory', 1, 'Contacts Partagés', '_DIRECTORY_COMMON'),
(3232, 'system', 2, 'Users', '_USERS'),
(3263, 'system', 2, 'Search', '_SEARCH'),
(945, 'directory', 1, 'Mes Favoris', '_DIRECTORY_FAVORITES'),
(946, 'directory', 1, 'Ajouter un nouveau contact', '_DIRECTORY_ADDNEWCONTACT'),
(947, 'directory', 1, 'Modifier un contact', '_DIRECTORY_MODIFYCONTACT'),
(948, 'directory', 1, 'Visualiser un contact', '_DIRECTORY_VIEWCONTACT'),
(951, 'system', 1, 'Prénom', '_DIMS_LABEL_FIRSTNAME'),
(953, 'directory', 1, 'Espaces', '_DIRECTORY_GROUPS'),
(3241, 'system', 2, 'Department', '_SERVICE'),
(3200, 'system', 2, 'Share', '_SHARE'),
(3444, 'system', 2, 'Meta informations', '_DIMS_LABEL_META'),
(957, 'directory', 1, 'Téléphone', '_DIRECTORY_PHONE'),
(3240, 'system', 2, 'Mobile', '_MOBILE'),
(960, 'directory', 1, 'Mèl', '_DIRECTORY_EMAIL'),
(3442, 'system', 2, 'Filtering / security', '_DIMS_LABEL_FILTERING'),
(967, 'directory', 1, 'Texte intégral', '_DIRECTORY_FULLTEXT'),
(968, 'directory', 1, 'Légende', '_DIRECTORY_LEGEND'),
(969, 'directory', 1, 'Lignes par Page', '_DIRECTORY_LINESPERPAGE'),
(972, 'directory', 1, 'Êtes vous certain de vouloir supprimer ce contact ?', '_DIRECTORY_CONFIRM_DELETECONTACT'),
(973, 'directory', 1, 'Êtes vous certain de vouloir supprimer ce favoris ?', '_DIRECTORY_CONFIRM_DELETEFAVORITES'),
(3404, 'system', 2, 'Visible', '_DIMS_LABEL_VISIBLE'),
(3405, 'system', 2, 'Restricted', '_DIMS_LABEL_PROTECTED'),
(3276, 'system', 2, 'Intelligence - Company link', '_INTEL_ENTLINK'),
(977, 'directory', 1, 'Effacer', '_DIRECTORY_LEGEND_DELETE'),
(3164, 'system', 2, 'Bookmark', '_ADDTO_FAVORITES'),
(3266, 'system', 2, 'Remove from favorites', '_REMOVEFROM_FAVORITES'),
(980, 'directory', 1, 'Envoyer un Mel', '_DIRECTORY_LEGEND_EMAIL'),
(1487, 'system', 1, 'Organisme attach&eacute;', '_DIMS_LABEL_ATTACH_ORGA'),
(986, 'doc', 1, 'Racine', '_DOC_ROOT'),
(1486, 'system', 1, '&Eacute;v&egrave;nements', '_DIMS_LABEL_EVENTS'),
(988, 'doc', 1, 'Nouveau fichier', '_DOC_NEWFILE'),
(1489, 'system', 1, 'Profil courant', '_DIMS_LABEL_CURPROFIL'),
(3403, 'system', 2, 'WCE objects', '_DIMS_LABEL_WCEOBJECTS'),
(991, 'doc', 1, 'Nouveau dossier', '_DOC_NEWFOLDER'),
(992, 'doc', 1, 'fichiers trouv&eacute;s', '_DOC_LABEL_FILESFOUND'),
(993, 'doc', 1, 'fichier trouv&eacute;', '_DOC_LABEL_FILEFOUND'),
(994, 'doc', 1, 'Aucun fichier trouv&eacute;', '_DOC_LABEL_FILESNOFOUND'),
(3179, 'system', 2, 'No files found', '_DOC_LABEL_FILESNOFOUND'),
(996, 'doc', 1, 'Ajout d''un autre fichier', '_DOC_LABEL_ADD_OTHER_FILE'),
(997, 'doc', 1, 'Envoi en cours', '_DOC_MSG_UPLOAD_FILE'),
(998, 'doc', 1, 'Copie en cours', '_DOC_MSG_COPY_FILE'),
(999, 'doc', 1, 'Attente de connexion', '_DOC_MSG_UPLOAD_WAITING'),
(1000, 'doc', 1, 'Erreur d''envoi de fichier', '_DOC_MSG_UPLOAD_ERROR'),
(1001, 'doc', 1, 'Cette extension n''est pas autoris&eacute;e', '_DOC_MSG_UPLOAD_ERROREXT'),
(3479, 'system', 2, 'No users defined for this workspace', '_DIMS_LABEL_NO_USER_DEFINED'),
(1004, 'doc', 1, 'Op&eacute;ration', '_DOC_LABEL_OPERATION'),
(1005, 'doc', 1, 'D&eacute;placer', '_DOC_LABEL_MOVE'),
(3192, 'system', 2, 'Delete', '_DELETE'),
(1007, 'doc', 1, 'D&eacute;compresser', '_DOC_LABEL_UNCOMPRESS'),
(1013, 'doc', 1, '<b>Erreur ! </b>Enregistrement du fichier impossible - le fichier est vide', '_DOC_LABEL_ERROR_EMPTYFILE'),
(1014, 'doc', 1, '<b>Erreur ! </b>Enregistrement du fichier impossible - le fichier n''est pas accessible en &eacute;criture', '_DOC_LABEL_ERROR_FILENOTWRITABLE'),
(1015, 'doc', 1, '<b>Erreur ! </b>Enregistrement du fichier impossible - le fichier est trop volumineux', '_DOC_LABEL_ERROR_MAXFILESIZE'),
(1016, 'doc', 1, '<b>Erreur ! </b>Impossible de d&eacute;zipper cette archive', '_DOC_LABEL_ERROR_FOLDERZIP'),
(1018, 'doc', 1, 'Vous ne disposez pas des autorisations n&eacute;cessaires pour supprimer ce dossier', '_DOC_LABEL_UNAUTHORIZED_DELETEFOLDER'),
(1019, 'doc', 1, 'Vous ne disposez pas des autorisations n&eacute;cessaires pour supprimer ce fichier', '_DOC_LABEL_UNAUTHORIZED_DELETEFILE'),
(1501, 'system', 1, 'D&eacute;j&agrave; visit&eacute; le Luxembourg', '_DIMS_LABEL_CONT_LUX'),
(1022, 'wce', 1, 'Propri&eacute;t&eacute;s de la page', '_WCE_PAGE_PROPERTIES'),
(1025, 'wce', 1, 'Haut de page', '_WCE_HEADER'),
(1026, 'wce', 1, 'Bas de page', '_WCE_FOOTER'),
(1027, 'wce', 1, 'Contenu de gauche', '_WCE_LEFT_CONTENT'),
(1028, 'wce', 1, 'Contenu de droite', '_WCE_RIGHT_CONTENT'),
(1029, 'wce', 1, 'Mots cl&eacute;s', '_WCE_KEYWORDS_META'),
(1034, 'wce', 1, 'Aucune page trouv&eacute;e', '_NO_PAGEFOUND'),
(1035, 'wce', 1, 'R&eacute;f&eacute;rencement', '_WCE_PAGE_REFER'),
(3173, 'system', 2, 'Tree', '_LABEL_TREE'),
(1037, 'wce', 1, 'Page', '_DIMS_LABEL_PAGE'),
(1040, 'wce', 1, 'R&eacute;&eacute;criture d''url', '_WCE_URLREWRITE'),
(3310, 'system', 2, 'connected users', '_DIMS_CONNECTED_USERS'),
(1043, 'wce', 1, 'Visualiser le site', '_WCE_ARTICLE_SITEPREVIEW'),
(1045, 'wce', 1, 'R&eacute;f&eacute;rence', '_WCE_ARTICLE_REFERENCE'),
(3410, 'system', 2, 'Detach', '_DIMS_LABEL_DETACH'),
(1047, 'wce', 1, 'Publication', '_WCE_ARTICLE_PUBLISH'),
(3473, 'system', 2, 'My profile', '_DIMS_LABEL_MYPROFILE'),
(1049, 'wce', 1, 'Aucun domaine', '_WCE_NO_DOMAIN'),
(1050, 'wce', 1, 'Choix de l''objet DIMS à ins&eacute;rer dans la page :', '_WCE_INSERT_OBJECT_CHOICE'),
(1051, 'wce', 1, 'Choix du module :', '_WCE_INSERT_MODULE_CHOICE'),
(1052, 'wce', 1, 'Cet objet est d&eacute;jà ins&eacute;r&eacute; dans cet article', '_WCE_OBJECT_ALREADY_EXISTS'),
(1485, 'system', 1, 'Veille', '_DIMS_LABEL_VEILLE'),
(1055, 'news', 1, 'news trouvé(s)', '_NEWS_LABEL_NEWSFOUND'),
(1056, 'news', 1, 'Aucune news trouvée', '_NEWS_LABEL_NONEWSFOUND'),
(1058, 'news', 1, 'Rédiger', '_NEWS_LABELTAB_WRITE'),
(1064, 'news', 1, 'Contenu', '_CONTENT'),
(1065, 'system', 1, 'Cat&eacute;gorie', '_DIMS_LABEL_CATEGORY'),
(1066, 'news', 1, '(Aucune Catégorie)', '_LABEL_NOCATEGORY'),
(1068, 'news', 1, 'Date de Publication', '_PUBLISHDATE'),
(1069, 'news', 1, 'Heure de Publication', '_NEWS_LABEL_PUBLISHTIME'),
(1072, 'news', 1, 'Lien', '_DIMS_LABEL_URL'),
(1074, 'news', 1, 'Source', '_NEWS_LABEL_SOURCE'),
(1075, 'news', 1, 'Inconnue', '_NEWS_LABEL_UNKNOWN'),
(1076, 'news', 1, 'A la une', '_NEWS_LABEL_HOT'),
(1078, 'news', 1, 'Lectures', '_NEWS_LABEL_READS'),
(1084, 'news', 1, 'Ajouter une Catégorie', '_CAT_CREATE'),
(3448, 'system', 2, 'Skin', '_DIMS_LABEL_GROUP_SKIN'),
(3267, 'system', 2, 'Modify category ''LABEL''', '_CAT_MODIFY'),
(1087, 'news', 1, 'Rédiger une News', '_NEWS_WRITE'),
(1088, 'news', 1, 'Modifier la news ''LABEL''', '_NEWS_MODIFY'),
(1089, 'news', 1, 'Liste des News', '_NEWS_LIST'),
(1094, 'news', 1, 'Suppression des News<br>Suppression des Catégories<br>Suppression des Paramètres', '_NEWS_LABEL_DELETE_INSTANCE'),
(1095, 'system', 1, 'Suppression des données de l''utilisateur', '_DELETE_USER'),
(1096, 'news', 1, 'Initialisation des données de l''utilisateur', '_NEWS_LABEL_ATTACH_USER'),
(1097, 'news', 1, 'Désinstallation du Module', '_NEWS_LABEL_UNINSTALL'),
(1099, 'rss', 1, 'Catégories', '_CATEGORIES'),
(1100, 'rss', 1, 'Nouveau', '_RSS_LABELTAB_ADD'),
(1101, 'rss', 1, 'Actualiser', '_RSS_LABELTAB_MODIFY'),
(1102, 'rss', 1, 'Explorer', '_RSS_LABELTAB_EXPLORER'),
(3296, 'system', 2, 'Desktop', '_DIMS_LABEL_HOME'),
(1499, 'system', 1, 'Fax Bureau', '_DIMS_LABEL_FAX_WORK'),
(1106, 'rss', 1, ' RSS trouvés', '_RSS_LABEL_NEWSFOUND'),
(1107, 'rss', 1, ' RSS trouvé', '_RSS_LABEL_NEWFOUND'),
(1108, 'rss', 1, 'Aucun RSS trouvé', '_RSS_LABEL_NONEWSFOUND'),
(1112, 'rss', 1, 'Catégorie', '_RSS_LABEL_CATEGORY'),
(1115, 'rss', 1, 'Flux par Défaut', '_RSS_LABEL_DEFAULT'),
(1116, 'rss', 1, 'Renouvellement du Flux', '_RSS_LABEL_FEED_RENEW'),
(1119, 'rss', 1, 'Mettre à jour les Flux d''Information périmés', '_RSS_LABEL_UPDATE_OUTDATED_FEEDS'),
(1120, 'rss', 1, 'Supprimer les Flux d''Information non valides', '_RSS_LABEL_DELETE_UNAVAILABLE_FEEDS'),
(1121, 'rss', 1, 'Mettre à jour tous les Flux d''Information', '_RSS_LABEL_UPDATE_ALL_FEEDS'),
(1122, 'rss', 1, 'Mes Recherches', '_RSS_LABEL_MYSEARCHES'),
(1123, 'rss', 1, 'Requête', '_RSS_LABEL_REQUEST'),
(3168, 'system', 2, 'Add Category', '_CAT_CREATE'),
(1127, 'system', 1, 'Liste des Catégories', '_CAT_LIST'),
(1128, 'rss', 1, 'Modifier la Catégorie ''LABEL''', '_CAT_MODIFY'),
(1129, 'rss', 1, 'Ajouter un Flux RSS', '_RSS_ADD'),
(1130, 'rss', 1, 'Modifier le Flux RSS ''LABEL''', '_RSS_MODIFY'),
(1131, 'rss', 1, 'Liste des Flux RSS', '_RSS_LIST'),
(1132, 'system', 1, 'Historique des actions', '_ACTIONHISTORY'),
(3156, 'system', 2, '(No Category)', '_LABEL_NOCATEGORY'),
(1136, 'rss', 1, 'Êtes-vous certain de vouloir supprimer cette catégorie ?', '_RSSCAT_LABEL_DELETE_CONFIRM'),
(1138, 'system', 1, 'Ajouter', '_DIMS_ADD'),
(1140, 'system', 1, 'Supprimer', '_DELETE'),
(1141, 'system', 1, 'Tout supprimer', '_DIMS_ALLDELETE'),
(1143, 'system', 1, 'Enregistrer', '_DIMS_SAVE'),
(1144, 'system', 1, 'Fermer', '_DIMS_CLOSE'),
(3307, 'system', 2, 'To', '_DIMS_LABEL_DESTS'),
(3269, 'system', 2, 'Check all', '_ALLCHECK'),
(1147, 'system', 1, 'Param&egrave;tres syst&egrave;me', '_DIMS_SYSTEM_ADMIN'),
(1148, 'system', 1, 'Administration des espaces', '_DIMS_SYSTEM_WORKSPSACE'),
(1149, 'system', 1, 'Vos espaces', '_DIMS_ENABLED_WORKSPACE'),
(1150, 'system', 1, 'Espace', '_WORKSPACE'),
(1151, 'system', 1, 'Tous les espaces', '_DIMS_ALL_WORKSPACES'),
(1482, 'system', 1, 'Heure de fin', '_DIMS_LABEL_HEUREFIN'),
(3463, 'system', 2, 'Delete workspace', '_DIMS_LABEL_DELETE_WORKSPACE'),
(1156, 'system', 1, 'Rechercher un planning', '_DIMS_SEARCH_PLANNING'),
(1157, 'system', 1, 'Planning favoris', '_DIMS_FAVORITE_PLANNING'),
(1158, 'system', 1, 'Mode', '_DIMS_MODE'),
(1159, 'system', 1, 'Historique', '_DIMS_HISTORY'),
(3270, 'system', 2, 'Uncheck all', '_ALLUNCHECK'),
(3272, 'system', 2, 'Favorites', '_FAVORITES'),
(1162, 'system', 1, 'Participe', '_DIMS_PARTICIPATES'),
(1163, 'system', 1, 'Pour info', '_DIMS_TOINFO'),
(1164, 'system', 1, 'Participants', '_DIMS_PARTICIP'),
(1165, 'system', 1, 'Pr&eacute;visualiser', '_PREVIEW'),
(1166, 'system', 1, 'R&eacute;initialiser', '_DIMS_RESET'),
(1167, 'system', 1, 'Filtrer', '_DIMS_FILTER'),
(1168, 'system', 1, 'Ex&eacute;cuter', '_DIMS_EXECUTE'),
(1169, 'system', 1, 'Terminer', '_DIMS_COMPLETE'),
(1170, 'system', 1, 'T&eacute;l&eacute;charger', '_DIMS_DOWNLOAD'),
(1171, 'system', 1, 'Envoyer', '_DIMS_SEND'),
(1172, 'system', 1, 'En attente de validation', '_DIMS_CONFIRM_WAIT'),
(1506, 'system', 1, 'Important', '_DIMS_LABEL_CONT_VIP_T'),
(1175, 'system', 1, 'Tout cocher', '_ALLCHECK'),
(1176, 'system', 1, 'Tout d&eacute;cocher', '_ALLUNCHECK'),
(1177, 'system', 1, 'R&eacute;pondre', '_DIMS_REPLY'),
(1178, 'system', 1, 'Continuer', '_DIMS_CONTINUE'),
(1179, 'system', 1, 'Ouvrir', '_DIMS_OPEN'),
(1180, 'system', 1, 'Liste des saisies', '_DIMS_INPUT_LIST'),
(1181, 'system', 1, 'Valider', '_DIMS_VALID'),
(1182, 'system', 1, 'Mettre &agrave; jour', '_DIMS_UPDATE'),
(1183, 'system', 1, 'Mettre en attente', '_DIMS_ADDTO_SURVEY'),
(1184, 'system', 1, 'Ajouter aux favoris', '_ADDTO_FAVORITES'),
(1185, 'system', 1, 'Supprimer de l''attente', '_DIMS_REMOVEFROM_SURVEY'),
(1186, 'system', 1, 'Supprimer des favoris', '_REMOVEFROM_FAVORITES'),
(3206, 'system', 2, 'Back', 'DIMS_BACK'),
(1188, 'system', 1, 'Retour', '_DIMS_BACK'),
(1189, 'system', 1, 'H&eacute;rit&eacute;', '_DIMS_HERITED'),
(1190, 'system', 1, 'Oui', '_DIMS_YES'),
(1191, 'system', 1, 'Non', '_DIMS_NO'),
(1192, 'system', 1, 'Fin', '_END'),
(1193, 'system', 1, 'Taille', '_SIZE'),
(1194, 'system', 1, 'R&eacute;sultat', '_RESULT'),
(3234, 'system', 2, 'Contacts involved', '_SYSTEM_USER_PARTICIPATE'),
(1196, 'system', 1, 'Liste', '_DIMS_LIST'),
(1197, 'system', 1, 'Actions', '_DIMS_ACTIONS'),
(1199, 'system', 1, 'Bloqu&eacute;', '_DIMS_LOCKED'),
(1200, 'system', 1, 'Position', '_POSITION'),
(1201, 'system', 1, 'Couleur', '_DIMS_LABEL_COLOR'),
(1202, 'system', 1, 'Manque', '_DIMS_NEEDED'),
(1203, 'system', 1, 'Retard pr&eacute;visionnel', '_DIMS_TOLATE_PREVIEW'),
(1204, 'system', 1, 'Commentaires', '_DIMS_COMMENTS'),
(1205, 'system', 1, 'R&eacute;ponse', '_DIMS_ANSWER'),
(3181, 'system', 2, 'Author', '_AUTHOR'),
(1208, 'system', 1, 'Date', '_DIMS_DATE'),
(1479, 'system', 1, 'Actualit&eacute;s des contacts', '_DIMS_LABEL_COLLABORATION_PERS'),
(1493, 'system', 1, 'F&eacute;minin', '_DIMS_LABEL_CONT_SEXE_F'),
(1211, 'system', 1, 'Maj le', '_DIMS_DATE_MODIFY'),
(1212, 'system', 1, 'Description', '_DIMS_LABEL_DESCRIPTION'),
(1213, 'system', 1, 'R&eacute;sultat de la recherche', '_DIMS_SEARCH_RESULT'),
(1214, 'system', 1, 'Activer la validation', '_DIMS_ACTIVATE_CHECK'),
(1215, 'system', 1, 'Valider les nouveaut&eacute;s', '_DIMS_VALIDATE_CHECK'),
(1216, 'system', 1, 'Aucun tag d&eacute;fini', '_DIMS_NO_TAGS_SEARCH'),
(1217, 'system', 1, 'Aucun expression d&eacute;finie', '_DIMS_NO_WORDS_SEARCH'),
(1218, 'system', 1, 'Propri&eacute;taire', '_DIMS_OWNER'),
(1219, 'system', 1, 'Propri&eacute;t&eacute;s de l''objet <OBJECT>', '_DIMS_OBJECT_PROPERTIES'),
(1220, 'system', 1, 'Visualiser le contenu', '_DIMS_VIEW_CONTENT'),
(1221, 'system', 1, 'Propri&eacute;t&eacute;s', '_DIMS_PROPERTIES'),
(1222, 'system', 1, 'Afficher le d&eacute;tail', '_DIMS_OBJECT_DISPLAY'),
(1223, 'system', 1, 'Masquer le d&eacute;tail', '_DIMS_OBJECT_HIDE'),
(1224, 'system', 1, 'Afficher la liste', '_DIMS_LIST_DISPLAY'),
(3271, 'system', 2, 'Hide list', '_LIST_HIDE'),
(1226, 'system', 1, 'Propri&eacute;t&eacute;s du projet', '_PROPERTIES_PROJECT'),
(1227, 'system', 1, 'Masquer les propri&eacute;t&eacute;s', '_DIMS_PROPERTIES_PROJECT_HIDE'),
(1228, 'system', 1, 'R&eacute;sum&eacute;', '_DIMS_OBJECT_RESUME'),
(1229, 'system', 1, 'Contenu int&eacute;gral', '_DIMS_OBJECT_COMPLETECONTENT'),
(1230, 'system', 1, 'Etes-vous certain de valider ce r&eacute;sultat de campagne ?', '_DIMS_CONFIRM_UPDATE_CAMPAIGN'),
(1231, 'system', 1, 'Etes-vous certain ?', '_DIMS_CONFIRM'),
(1232, 'system', 1, 'utilisateur connect&eacute;', '_DIMS_CONNECTED_USER'),
(1233, 'system', 1, 'utilisateurs connect&eacute;s', '_DIMS_CONNECTED_USERS'),
(1235, 'system', 1, 'Administration G&eacute;n&eacute;rale', '_GENERAL_ADMINISTRATION'),
(1238, 'system', 1, 'Messages', '_DIMS_LABEL_ADMIN_MESSAGES'),
(3231, 'system', 2, 'System', '_DIMS_LABEL_SYSTEM'),
(1240, 'system', 1, 'Espaces de Travail', '_DIMS_ADMIN_WORKSPACES'),
(1242, 'system', 1, 'Fichier', '_DIMS_LABEL_FILE'),
(1243, 'system', 1, 'Utilisateur inconnu', '_DIMS_UNKNOWNUSER'),
(1244, 'system', 1, 'Administration', '_DIMS_LABEL_ADMIN'),
(1245, 'system', 1, 'Barre de navigation', '_DIMS_LABEL_NAVIGATE'),
(1246, 'system', 1, 'Navigation', '_DIMS_LABEL_TOOLBARNAVIGATE'),
(1247, 'system', 1, 'Recherche', '_SEARCH');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(1248, 'system', 1, 'News', '_DIMS_LABEL_TOOLBARNEWS'),
(1249, 'system', 1, 'Echange', '_DIMS_LABEL_TOOLBARSHARE'),
(3402, 'system', 2, 'Metabase', '_DIMS_LABEL_METABASE'),
(1251, 'system', 1, 'Indexation non effectu&eacute;e', '_DIMS_LABEL_NOINDEX'),
(1252, 'system', 1, 'Vous devez selectionner un element', '_DIMS_MSG_MUSTSELECTEDELEMENT'),
(1255, 'system', 1, 'Modules', '_DIMS_LABEL_MODULES'),
(3242, 'system', 2, 'My Details', '_DIMS_LABEL_MYDATAS'),
(3243, 'system', 2, 'My account', '_DIMS_LABEL_MYACCOUNT'),
(1259, 'system', 1, 'Comment.', '_DIMS_LABEL_ANNOTATION'),
(3468, 'system', 2, 'Module properties', '_DIMS_LABEL_MODULE_PROPERTIES'),
(3446, 'system', 2, 'Code', '_DIMS_LABEL_GROUP_CODE'),
(1263, 'system', 1, 'Gpe', '_DIMS_LABEL_GROUP'),
(1264, 'system', 1, 'Espace connexion', '_DIMS_LABEL_CONNECTWORKSPACE'),
(1267, 'system', 1, 'Bureau', '_DIMS_LABEL_HOME'),
(3464, 'system', 2, 'Modules available for this workspace', '_DIMS_LABEL_GROUP_AVAILABLE_MODULES'),
(1270, 'system', 1, 'Projets', '_LABEL_PROJECTS'),
(1271, 'system', 1, 'Favoris', '_FAVORITES'),
(1273, 'system', 1, 'Pas de message', '_DIMS_LABEL_NO_TICKET'),
(1274, 'system', 1, 'Pas de discussion en cours', '_DIMS_LABEL_NO_DISCUSS'),
(1275, 'system', 1, 'Pas de personne connect&eacute;e &agrave; d''autre Dims', '_DIMS_LABEL_NO_INTERCOM'),
(1276, 'system', 1, 'Pas d''objet s&eacute;lectionn&eacue;', '_DIMS_LABEL_NO_OBJECT'),
(1277, 'system', 1, 'Planning', '_PLANNING'),
(1278, 'system', 1, 'Non utilis&eacute;', '_DIMS_LABEL_NOTUSED'),
(1279, 'system', 1, 'Usage par espace(s) de travail', '_DIMS_WORKSPACE_USE'),
(1280, 'system', 1, 'Usage', '_DIMS_USE_MODE'),
(1281, 'system', 1, '&Eacute;v&egrave;nement', '_DIMS_LABEL_EVENT'),
(3235, 'system', 2, 'Companies', '_DIMS_LABEL_ENTERPRISES'),
(1284, 'system', 1, 'fiches surveill&eacute;es', '_DIMS_LABEL_VEILLE_FICH'),
(1285, 'system', 1, 'Choisissez l''ordre <br/>des blocs d''informations', '_DIMS_LABEL_BLOC_ORDER'),
(3193, 'system', 2, 'company name', '_DIMS_LABEL_ENT_NAME'),
(1289, 'system', 1, 'Dirigeant', '_DIMS_LABEL_ENT_DIR'),
(1290, 'system', 1, 'Pr&eacute;sentation / Activit&eacute;', '_DIMS_LABEL_ENT_PRES'),
(3400, 'system', 2, 'Begin and End hours', '_DIMS_LABEL_HEURE_DEB_FIN'),
(1294, 'system', 1, 'Site web', '_DIMS_LABEL_ENT_WSITE'),
(1295, 'system', 1, 'Secteur d''activit&eacute;', '_DIMS_LABEL_ENT_SECTACT'),
(1296, 'system', 1, 'Effectif', '_DIMS_LABEL_ENT_EFFECTIF'),
(1297, 'system', 1, 'Date de cr&eacute;ation', '_DIMS_LABEL_ENT_DATEC'),
(1298, 'system', 1, 'Capital social', '_DIMS_LABEL_ENT_CAPITAL'),
(1302, 'system', 1, 'Informations m&eacute;tier', '_FORM_CT_INF_MET'),
(1306, 'system', 1, 'Cr&eacute;&eacute; le', '_DIMS_LABEL_CREATE_ON'),
(1308, 'system', 1, 'Modifi&eacute;e le', '_DIMS_LABEL_MODIF_ON_FEM'),
(3239, 'system', 2, 'First name', '_FIRSTNAME'),
(1317, 'system', 1, 'Il n''y a aucun contact', '_DIMS_LABEL_NO_RESP'),
(1324, 'system', 1, 'Il n''y a aucun lien.', '_DIMS_LABEL_NO_LINK'),
(1325, 'system', 1, 'Liens priv&eacute;s', '_DIMS_LABEL_LINK_PERSO'),
(1350, 'system', 1, 'IDENTIT&Eacute;', '_DIMS_PERS_IDENTITY'),
(1351, 'system', 1, 'COORDONN&Eacute;ES', '_DIMS_PERS_COORD'),
(1352, 'system', 1, 'INFORMATIONS', '_DIMS_PERS_INFOS'),
(1356, 'system', 1, 'Raison Sociale', '_DIMS_LABEL_ENT_NAME'),
(1361, 'system', 1, 'Fiches "Personne" en veille', '_DIMS_LABEL_WATCH_CONT'),
(1362, 'system', 1, 'Fiches "Entreprise" en veille', '_DIMS_LABEL_WATCH_ENT'),
(1365, 'system', 1, 'Fiches en cours de surveillance', '_DIMS_LABEL_WATCH_SURVENCOURS'),
(3191, 'system', 2, 'Profile', '_PROFIL'),
(1367, 'system', 1, 'Descriptif m&eacute;tier', '_DIMS_LABEL_CONT_DESCM'),
(1368, 'system', 1, 'Entreprise rattach&eacute;e', '_DIMS_LABEL_CONT_ENTRAT'),
(1371, 'system', 1, 'Recherche de l''entreprise', '_DIMS_LABEL_SEARCH_ENT'),
(3253, 'system', 2, 'Modifying a Field', '_FIELDMODIFICATION'),
(1373, 'system', 1, 'Informations professionnelles', '_DIMS_LABEL_CONT_INFPRO'),
(1374, 'system', 1, 'Informations personnelles', '_DIMS_LABEL_CONT_INFPERS'),
(1375, 'system', 1, 'Date de naissance', '_DIMS_LABEL_CONT_DATE_N'),
(1376, 'system', 1, 'Entreprise actuelle', '_DIMS_LABEL_CONT_ENTACT'),
(3202, 'system', 2, 'Function', '_DIMS_LABEL_FUNCTION'),
(1378, 'system', 1, 'T&eacute;l&eacute;phone', '_DIMS_LABEL_TEL'),
(1381, 'system', 1, 'Fax', '_DIMS_LABEL_FAX'),
(1386, 'system', 1, 'Ville', '_DIMS_LABEL_CITY'),
(1388, 'system', 1, 'Parcours professionnel', '_DIMS_LABEL_PARCPRO'),
(1389, 'system', 1, 'Situation familiale', '_DIMS_LABEL_SITFAM'),
(1390, 'system', 1, 'Nombre d''enfants', '_DIMS_LABEL_NBCHILD'),
(3187, 'system', 2, 'Comments', '_DIMS_COMMENTS'),
(1394, 'system', 1, 'LIENS PERSONNES', '_DIMS_LABEL_LINK_CONT'),
(1396, 'system', 1, 'LIENS ENTREPRISES', '_DIMS_LABEL_LINK_ENT'),
(1397, 'system', 1, 'Liens public', '_DIMS_LABEL_LINK_GEN'),
(1398, 'system', 1, 'Liens espace', '_DIMS_LABEL_LINK_MET'),
(1400, 'system', 1, 'Type de lien', '_DIMS_LABEL_LINK_TYPE'),
(1401, 'system', 1, 'AJOUTER UN LIEN', '_DIMS_LABEL_ADDLINK'),
(1402, 'system', 1, 'Nom ou Pr&eacute;nom', '_DIMS_LABEL_SEARCH_LPERS'),
(1403, 'system', 1, 'Liens vers une Personne', '_DIMS_LABEL_LINK_PSEARCH'),
(1404, 'system', 1, 'Liens vers une Entreprise', '_DIMS_LABEL_LINK_TSEARCH'),
(1411, 'system', 1, 'Lieu', '_LOCATION'),
(1413, 'system', 1, 'Contact', '_DIMS_LABEL_CONTACT'),
(1414, 'system', 1, 'Personnes', '_DIMS_LABEL_PERSONNE'),
(1415, 'system', 1, 'Documents', '_DOCS'),
(1416, 'system', 1, 'Contenus', '_DIMS_LABEL_CONTENT'),
(1417, 'system', 1, 'Annuaire', '_DIMS_LABEL_DIRECTORY'),
(1418, 'system', 1, 'Collecte d''information', '_DIMS_LABEL_WATCH'),
(3305, 'system', 1, 'Exp&eacute;diteur', '_SENDER'),
(1481, 'system', 1, 'Heure de d&eacute;but', '_DIMS_LABEL_HEUREDEB'),
(1426, 'system', 1, '- Tout -', '_DIMS_ALL'),
(1478, 'system', 1, 'Label', '_DIMS_LABEL'),
(1428, 'system', 1, 'Informations comme lues', 'VALIDATE_VIEW_INFO'),
(3203, 'system', 2, 'Not defined', '_DIMS_LABEL_UNDEFINED'),
(1430, 'system', 1, 'Priv&eacute;e', '_DIMS_LABEL_VIEWMODE_PRIVATE'),
(1431, 'system', 1, 'Descendante', '_LABEL_VIEWMODE_DESC'),
(1432, 'system', 1, 'Ascendante', '_LABEL_VIEWMODE_ASC'),
(1433, 'system', 1, 'Globale', '_LABEL_VIEWMODE_GLOBAL'),
(1435, 'system', 1, 'Gestionnaire d''Espace', '_DIMS_LEVEL_GROUPMANAGER'),
(1436, 'system', 1, 'Administrateur d''Espace', '_DIMS_LEVEL_GROUPADMIN'),
(1437, 'system', 1, 'Administrateur Syst&egrave;me', '_DIMS_LEVEL_SYSTEMADMIN'),
(1505, 'system', 1, 'VIP', '_DIMS_LABEL_CONT_VIP_V'),
(1440, 'system', 1, 'Options', '_DIMS_OPTIONS'),
(1441, 'system', 1, 'Partage', '_SHARE'),
(1497, 'system', 1, 'Pays', '_DIMS_LABEL_COUNTRY'),
(1444, 'system', 1, 'Type', '_TYPE'),
(1517, 'system', 1, '<b>Vue Priv&eacute;e</b> : restricion au groupe<br><b>Descendante</b> : groupe et sous-groupes<br><b>Ascendante</b> : restriction au groupe et groupes parents<br><b>Globale</b> : aucune restriction', '_SYSTEM_EXPLAIN_VIEWMODE'),
(1516, 'system', 1, 'En mode restreint, les administrateurs des sous-groupes qui utilisent ce module auront des droits limit&eacute;s', '_SYSTEM_EXPLAIN_ADMINRESTRICTED'),
(1448, 'system', 1, 'Masquer la liste', '_LIST_HIDE'),
(1450, 'system', 1, 'Valider le partage', '_DIMS_SHARE_VALID'),
(1453, 'system', 1, 'Etes vous sur de vouloir supprimer cette campagne ?', '_DIMS_CONFIRM_DELETE_CAMPAIGN'),
(1454, 'system', 1, 'L''adresse m&egrave;l n''est pas valide.\\n Il n''y a pas de caract&egrave;re @ \\n Une adresse m&egrave;l valide est du type adresse@domaine.com', '_DIMS_JS_EMAIL_ERROR_1'),
(1455, 'system', 1, 'L''adresse mèl n''est pas valide.\\n Il ne peut pas y avoir un point (.) juste après @ \\n Une adresse mèl valide est du type adresse@domaine.com', '_DIMS_JS_EMAIL_ERROR_2'),
(1456, 'system', 1, 'L''adresse mèl n''est pas valide.\\n L''adresse mèl ne peut pas finir par un point (.) \\n Une adresse mèl valide est du type adresse@domaine.com', '_DIMS_JS_EMAIL_ERROR_3'),
(1457, 'system', 1, 'L''adresse mèl n''est pas valide. \\n L''adresse mèl ne peut pas contenir 2 points (.) qui se suivent.\\\\nUne adresse mèl valide est du type adresse@domaine.com', '_DIMS_JS_EMAIL_ERROR_4'),
(1458, 'system', 1, 'Le champ ''<FIELD_LABEL>'' ne doit pas &ecirc;tre vide', '_DIMS_JS_STRING_ERROR'),
(1459, 'system', 1, 'Le champ ''<FIELD_LABEL>'' doit &ecirc;tre un nombre entier valide', '_DIMS_JS_INT_ERROR'),
(1460, 'system', 1, 'Le champ ''<FIELD_LABEL>'' doit &ecirc;tre un nombre r&eacute;el valide', '_DIMS_JS_FLOAT_ERROR'),
(1461, 'system', 1, 'Le champ ''<FIELD_LABEL>'' doit &ecirc;tre une date valide', '_DIMS_JS_DATE_ERROR'),
(1462, 'system', 1, 'Le champ ''<FIELD_LABEL>'' doit &ecirc;tre une heure valide', '_DIMS_JS_TIME_ERROR'),
(1463, 'system', 1, 'Vous devez s&eacute;lectionner une valeur pour le champ ''<FIELD_LABEL>''', '_DIMS_JS_CHECK_ERROR'),
(1464, 'system', 1, 'Le champ ''<FIELD_LABEL>'' doit &ecirc;tre une couleur valide (#ffff00 / jaune / yellow)', '_DIMS_JS_COLOR_ERROR'),
(1465, 'system', 1, 'aujourd''hui', '_DIMS_LABEL_DAY'),
(1466, 'system', 1, 'demain', '_DIMS_LABEL_TOMOROW'),
(1468, 'system', 1, 'hier', '_DIMS_LABEL_LASTDAY'),
(1469, 'system', 1, 'dernier acc&egrave;s il y a', '_DIMS_LABEL_THEREIS'),
(1470, 'system', 1, 'jours', '_DIMS_LABEL_DAYS'),
(1471, 'system', 1, 'semaines', '_DIMS_LABEL_WEEKS'),
(1472, 'system', 1, 'Par', '_DIMS_LABEL_FROM'),
(1473, 'system', 1, 'le', '_AT'),
(1474, 'system', 1, 'de', '_FROM'),
(1475, 'system', 1, '&agrave;', '_DIMS_LABEL_A'),
(1476, 'system', 1, 'Filtre s&eacute;curit&eacute; - acc&egrave;s interdit', '_DIMS_SECURITY_ERROR'),
(1477, 'system', 1, 'secondes restantes avant une prochaine tentative de connexion', '_DIMS_REST_SEC'),
(1518, 'system', 1, 'Nouvelles Personnes', '_DIMS_LABEL_NEW_CONT_SINCE'),
(1519, 'system', 1, 'Personnes modifi&eacute;es depuis le', '_DIMS_LABEL_CONT_FICHE_MOD'),
(1520, 'system', 1, 'LIENS &Eacute;V&Egrave;NEMENTS', '_DIMS_LABEL_LINK_EVT'),
(1521, 'system', 1, 'Langue', '_DIMS_LABEL_LANG'),
(1522, 'system', 1, 'Générique', '_DIMS_LABEL_LFB_GEN'),
(1523, 'system', 1, 'Métier', '_DIMS_LABEL_LFB_MET'),
(1524, 'system', 1, 'Personnel', '_PERSO'),
(1525, 'system', 1, 'Modification d''un lien', '_DIMS_LABEL_LFB_MOD_LINK'),
(1526, 'system', 1, 'Niveau du lien', '_DIMS_LABEL_LEVEL_LINK'),
(1527, 'system', 1, 'Fiche Entreprise', '_DIMS_LABEL_ENT_FICHE'),
(1528, 'system', 1, 'Ce lien a bien été supprimé.', '_DIMS_LABEL_DELETED_LINK'),
(1529, 'system', 1, 'poss&egrave;de', '_DIMS_LABEL_LFB_POSSEDE'),
(1530, 'system', 1, 'champ(s)', '_DIMS_LABEL_RULEFIELD_MIN'),
(1531, 'system', 1, 'Envoi d''une demande d''informations', '_DIMS_LABEL_DMD_ENVOI'),
(1532, 'system', 1, 'Sujet', '_SUBJECT'),
(1533, 'system', 1, 'Message', '_DIMS_LABEL_MESSAGE'),
(1534, 'system', 1, 'Type d''information', '_DIMS_LABEL_INFO_TYPE'),
(1535, 'system', 1, 'Demande d''informations concernant la fiche de', '_DIMS_LABEL_DMD_INFO'),
(1536, 'system', 1, 'Cat&eacute;gorie(s) concern&eacute;e(s)', '_DIMS_LABEL_CAT_CONC'),
(1537, 'system', 1, 'Cette fiche est maintenant en cours de surveillance', '_DIMS_LABEL_WATCH_OK'),
(1538, 'system', 1, 'Cette fiche est déjà en cours de surveillance', '_DIMS_LABEL_WATCH_EXIST'),
(1539, 'system', 1, 'Ce lien personnel existe déjà', '_DIMS_LABEL_LINK_EXIST'),
(1540, 'system', 1, 'Ajouter un contact', '_ADD_CT'),
(3275, 'system', 2, 'Intelligence - Contact link', '_INTEL_PLINK'),
(3278, 'system', 2, 'Registration', '_REGISTRATION'),
(1543, 'system', 1, 'Intelligence - Liens entreprises', '_INTEL_ENTLINK'),
(1544, 'system', 1, 'Intelligence - Liens personnes', '_INTEL_PLINK'),
(1545, 'system', 1, 'Etes vous certain ?', '_DIMS_LABEL_CONFIRM_DELETE'),
(1546, 'system', 1, 'Fond d''&eacute;cran', '_DIMS_LABEL_BACKGROUNDIMAGE'),
(1548, 'doc', 1, 'Partag&eacute;', '_DIMS_LABEL_ISSHARED'),
(1549, 'system', 1, 'Téléphone', '_DIMS_LABEL_TEL_DOMICILE'),
(1550, 'system', 1, 'Photo', '_DIMS_LABEL_PHOTO'),
(1551, 'system', 1, 'business', '_DIMS_MOD_LABEL_BUSINESS'),
(1552, 'system', 1, 'r&eacute;seaux', '_DIMS_LABEL_RESEAU'),
(1553, 'system', 1, 'famille / amis', '_DIMS_LABEL_FAMILLE_AMI'),
(1554, 'system', 1, 'autre', '_DIMS_LABEL_OTHER'),
(1555, 'system', 1, 'employeur', '_DIMS_LABEL_EMPLOYEUR'),
(1556, 'system', 1, 'associ&eacute;', '_DIMS_LABEL_ASSOCIE'),
(1557, 'system', 1, 'Conseil d''administration', '_DIMS_LABEL_CONSADMIN'),
(1558, 'system', 1, 'd&eacute;partement', '_DIMS_LABEL_DEPARTEMENT'),
(1559, 'system', 1, 'Relation', '_DIMS_LABEL_RELATION'),
(1560, 'system', 1, 'Contact li&eacute;', '_DIMS_LABEL_CT_LINKED'),
(1561, 'system', 1, 'Commentaire public', '_DIMS_LABEL_COMMENT_GEN'),
(1562, 'system', 1, 'Commentaire espace', '_DIMS_LABEL_COMMENT_MET'),
(1563, 'system', 1, 'Commentaire priv&eacute;', '_DIMS_LABEL_COMMENT_PERS'),
(1564, 'system', 1, 'Historique des commentaires', '_DIMS_LABEL_COMMENT_HIST'),
(1565, 'system', 1, 'Pas de commentaire', '_DIMS_LABEL_NO_COMMENT'),
(1566, 'system', 1, 'Ajout/Modification d''un commentaire', '_DIMS_LABEL_ADD_COMMENT'),
(1567, 'system', 1, 'Niveau du commentaire', '_DIMS_LABEL_COMMENT_LVL'),
(1568, 'system', 1, 'Auteur du commentaire', '_DIMS_LABEL_COMMENT_BY'),
(1569, 'system', 1, 'Il n''y a aucune entreprise', '_DIMS_LABEL_NO_ENTERPRISE'),
(1570, 'system', 1, 'Cette semaine', '_DIMS_LABEL_THIS_WEEK'),
(1571, 'system', 1, 'Deux semaines', '_DIMS_LABEL_15_DAYS'),
(1572, 'system', 1, 'Un mois', '_DIMS_LABEL_THIS_MONTH'),
(1573, 'system', 1, 'Trois derniers mois', '_DIMS_LABEL_3_MONTHS'),
(1574, 'system', 1, 'Import des fiches entreprise', '_LABEL_ADMIN_IMPORT_ENT'),
(1575, 'system', 1, 'La premi&egrave;re ligne du fichier doit &ecirc;tre une ligne de description valide.', '_LABEL_ADMIN_IMPORT_ERROR'),
(1576, 'system', 1, 'Nombre de lignes trait&eacute;es', '_LABEL_ADMIN_NBLINES'),
(1577, 'system', 1, 'Fiches cr&eacute;&eacute;es', '_LABEL_ADMIN_NEW_ENT'),
(1578, 'system', 1, 'Fiches existantes', '_LABEL_ADMIN_DOUBLE'),
(1579, 'system', 1, 'Fiches non conformes', '_LABEL_ADMIN_NB_ERROR'),
(1580, 'system', 1, 'Import', '_LABEL_IMPORT'),
(1581, 'system', 1, 'Ajouter un Rendez-vous', '_DIMS_PLANNING_ADD_RDV'),
(1582, 'system', 1, 'Ajouter un év&egrave;nement', '_DIMS_PLANNING_ADD_EVT'),
(1583, 'system', 1, 'Modification d''un Rendez-vous', '_DIMS_PLANNING_MODIFY_RDV'),
(1584, 'system', 1, 'Modification d''un Ev&eacute;nement', '_DIMS_PLANNING_MODIFY_EVT'),
(1585, 'system', 1, 'Cong&eacute;s', '_DIMS_LABEL_CONGE'),
(1586, 'system', 1, 'Caract&eacute;ristiques Ev&eacute;nement', '_DIMS_PLANNING_LABEL_EVT'),
(3398, 'system', 2, 'Logs report', '_DIMS_LABEL_LOGS'),
(1588, 'system', 1, 'Support de publication', '_DIMS_EVT_RELEASING_SUPPORT'),
(1589, 'system', 1, 'Rubrique newsletter', '_DIMS_EVT_RUB_NEWLETTER'),
(1590, 'system', 1, 'Événement avec inscriptions', '_DIMS_EVT_ALLOW_FO'),
(1591, 'system', 1, 'Cible', '_DIMS_EVT_TARGET'),
(1592, 'system', 1, 'Teaser', '_DIMS_EVT_TEASER'),
(3273, 'system', 2, 'Location', '_LOCATION'),
(1594, 'system', 1, 'Prix', '_DIMS_EVT_PRIX'),
(1595, 'system', 1, 'Conditions de participation', '_DIMS_EVT_CONDITION'),
(1596, 'system', 1, 'Lier un document', '_DIMS_EVT_DOC'),
(1597, 'system', 1, 'Code NACE', '_DIMS_LABEL_ENT_CODE_NACE'),
(1598, 'system', 1, 'Liste des nouvelles entreprises', '_LABEL_LIST_NEW_ENT'),
(1599, 'system', 1, 'Liste des entreprises d&eacute;j&agrave; connues', '_LABEL_LIST_ALREADY_KNOWN'),
(1600, 'system', 1, 'Fiche de l''entreprise correspondante', '_LABEL_ENT_FICHE_CORRESP'),
(1601, 'system', 1, 'Contacts concern&eacute;s', '_DIMS_EVT_CONTACT_PARTICIPATE'),
(3277, 'system', 2, 'Organize', '_ORGANIZE'),
(1603, 'system', 1, 'Dernier import entreprise', '_LABEL_LAST_IMPORT'),
(1604, 'system', 1, 'Informations g&eacute;n&eacute;riques', '_DIMS_LABEL_INFO_G'),
(1605, 'system', 1, 'Partenaire', '_DIMS_LABEL_PARTENAIRE'),
(1606, 'system', 1, 'EFFECTUER UNE RECHERCHE SUR LES PERSONNES', '_DIMS_LABEL_CT_SEARCH_PERS'),
(1607, 'wce', 1, 'Meta descriptions', '_DIMS_LABEL_DESCRIPTION_META'),
(3441, 'system', 2, 'Edit workspace', '_DIMS_LABEL_WORKSPACE_MODIFY'),
(1609, 'system', 1, 'Derni&egrave;res recherches', '_DIMS_LABEL_LAST_SEARCH'),
(1610, 'system', 1, 'trouv&eacute;', '_DIMS_LABEL_FOUND'),
(1611, 'system', 1, 'trouve&eacute;s', '_DIMS_LABEL_FOUNDS'),
(3440, 'system', 2, 'Edit group', '_DIMS_LABEL_GROUP_MODIFY'),
(1613, 'system', 1, 'EFFECTUER UNE RECHERCHE SUR LES ENTREPRISES', '_DIMS_LABEL_CT_SEARCH_ENT'),
(3208, 'system', 2, 'Recent searches', '_DIMS_LABEL_LAST_SEARCH'),
(1615, 'system', 1, 'Contacts inscrits', '_DIMS_EVT_CONTACT_REGISTER'),
(1616, 'system', 1, 'Partenaires de l''&eacute;v&egrave;nement', '_DIMS_EVT_PARTNER'),
(1617, 'system', 1, 'Ajout d''une rencontre', '_DIMS_PLANNING_ADD_RCT'),
(1618, 'system', 1, 'Apr&egrave;s validation de cet &eacute;v&eacute;nement vous pourrez lui associer des documents.', '_DIMS_EVT_NO_DOC'),
(1619, 'system', 1, 'Organise', '_ORGANIZE'),
(1620, 'system', 1, 'Modification d''une rencontre', '_DIMS_PLANNING_MODIFY_RCT'),
(1621, 'system', 1, 'Accompagnant', '_DIMS_ACCOMPANY'),
(1622, 'system', 1, 'Rencontr&eacute;', '_DIMS_MEETED'),
(1623, 'system', 1, 'Veuillez remplir au moins un champ de recherche', '_DIMS_LABEL_ERROR_SCH'),
(1624, 'system', 1, 'Il n''y a aucun &eacute;v&egrave;nement', '_DIMS_LABEL_NO_EVENT'),
(1625, 'system', 1, 'Nouveau tag', '_DIMS_LABEL_NEWTAG'),
(1626, 'system', 1, 'Lignes contenant des erreurs', '_LABEL_ADMIN_IMPORT_LINERROR'),
(1627, 'system', 1, 'Tags', '_DIMS_LABEL_TAGS'),
(1628, 'system', 1, 'S''inscrire &agrave; cet &eacute;v&egrave;nement', '_DIMS_EVT_INSCRIPT'),
(1629, 'system', 1, 'Envoy&eacute;s', '_DIMS_MSG_SENT'),
(1630, 'system', 1, 'Plus de filtres', '_DIMS_LABEL_MORE_FILTERS'),
(1631, 'system', 1, 'Il n''y a aucun &eacute;v&egrave;nement correspondant', '_DIMS_LABEL_NO_EVENT_CORRESP'),
(1632, 'system', 1, 'inscrire plusieurs personnes &agrave; cet &eacute;v&egrave;nement', '_DIMS_EVT_INSCRIPT_MULTI'),
(3207, 'system', 2, 'There is no company', '_DIMS_LABEL_NO_ENTERPRISE'),
(1634, 'system', 1, 'Partenaire &eacute;v&egrave;nement', '_DIMS_LABEL_SPONSOR'),
(1635, 'system', 1, 'Exporter les emails', '_FORMS_EMAIL_EXPORT'),
(1636, 'system', 1, 'Exporter les adresses', '_FORMS_ADR_EXPORT'),
(1637, 'system', 1, 'Filtres utilis&eacute;s', '_DIMS_USED_FILTER'),
(1638, 'system', 1, 'Admin. &eacute;v&eagrave;nements', '_DIMS_EVENT_ADMIN'),
(1639, 'system', 1, 'Cette personne a bien été ajoutée à vos contacts.', '_DIMS_LABEL_LINK_OK'),
(1640, 'system', 1, 'Nouvelles Entreprises', '_DIMS_LABEL_NEW_ENT_SINCE'),
(1641, 'system', 1, 'Entreprises modif&eacute;es', '_DIMS_LABEL_ENT_FICHE_MOD'),
(1642, 'system', 1, 'Acc&egrave;s &agrave; ma fiche personnelle', '_DIMS_LABEL_ACCESS_FICHE_PERS'),
(1643, 'system', 1, 'Tr&egrave;s important', '_DIMS_LABEL_CONT_VIP_L'),
(1644, 'system', 1, 'Afghanistan', '_DIMS_PAYS_AF'),
(1645, 'system', 2, 'Afghanistan', '_DIMS_PAYS_AF'),
(1646, 'system', 1, 'Afrique du Sud', '_DIMS_PAYS_ZA'),
(1647, 'system', 2, 'South Africa', '_DIMS_PAYS_ZA'),
(1648, 'system', 1, 'Albanie', '_DIMS_PAYS_AL'),
(1649, 'system', 2, 'Albania', '_DIMS_PAYS_AL'),
(1650, 'system', 1, 'Alg&eacute;rie', '_DIMS_PAYS_DZ'),
(1651, 'system', 2, 'Algeria', '_DIMS_PAYS_DZ'),
(3172, 'system', 2, 'Overview of listing document', '_DIMS_EVENT_LABEL_ADERVTISE_DOC_PREVIEW'),
(1654, 'system', 1, 'Andorre', '_DIMS_PAYS_AD'),
(1655, 'system', 2, 'Andorra', '_DIMS_PAYS_AD'),
(1656, 'system', 1, 'Angola', '_DIMS_PAYS_AO'),
(1657, 'system', 2, 'Angola', '_DIMS_PAYS_AO'),
(1658, 'system', 1, 'Anguilla', '_DIMS_PAYS_AI'),
(1659, 'system', 2, 'Anguilla', '_DIMS_PAYS_AI'),
(1660, 'system', 1, 'Antarctique', '_DIMS_PAYS_AQ'),
(1661, 'system', 2, 'Antarctica', '_DIMS_PAYS_AQ'),
(1662, 'system', 1, 'Antigua-et-Barbuda', '_DIMS_PAYS_AG'),
(1663, 'system', 2, 'Antigua & Barbuda', '_DIMS_PAYS_AG'),
(1664, 'system', 1, 'Antilles n&eacute;erlandaises', '_DIMS_PAYS_AN'),
(1665, 'system', 2, 'Netherlands Antilles', '_DIMS_PAYS_AN'),
(1666, 'system', 1, 'Arabie saoudite', '_DIMS_PAYS_SA'),
(1667, 'system', 2, 'Saudi Arabia', '_DIMS_PAYS_SA'),
(1668, 'system', 1, 'Argentine', '_DIMS_PAYS_AR'),
(1669, 'system', 2, 'Argentina', '_DIMS_PAYS_AR'),
(1670, 'system', 1, 'Arm&eacute;nie', '_DIMS_PAYS_AM'),
(1671, 'system', 2, 'Armenia', '_DIMS_PAYS_AM'),
(1672, 'system', 1, 'Aruba', '_DIMS_PAYS_AW'),
(1673, 'system', 2, 'Aruba', '_DIMS_PAYS_AW'),
(1674, 'system', 1, 'Australie', '_DIMS_PAYS_AU'),
(1675, 'system', 2, 'Australia', '_DIMS_PAYS_AU'),
(1676, 'system', 1, 'Autriche', '_DIMS_PAYS_AT'),
(1677, 'system', 2, 'Austria', '_DIMS_PAYS_AT'),
(1678, 'system', 1, 'Azerba&iuml;djan', '_DIMS_PAYS_AZ'),
(1679, 'system', 2, 'Azerbaijan', '_DIMS_PAYS_AZ'),
(1680, 'system', 1, 'B&eacute;nin', '_DIMS_PAYS_BJ'),
(1681, 'system', 2, 'Benin', '_DIMS_PAYS_BJ'),
(1682, 'system', 1, 'Bahamas', '_DIMS_PAYS_BS'),
(1683, 'system', 2, 'Bahamas, The', '_DIMS_PAYS_BS'),
(1684, 'system', 1, 'Bahre&iuml;n', '_DIMS_PAYS_BH'),
(1685, 'system', 2, 'Bahrain', '_DIMS_PAYS_BH'),
(1686, 'system', 1, 'Bangladesh', '_DIMS_PAYS_BD'),
(1687, 'system', 2, 'Bangladesh', '_DIMS_PAYS_BD'),
(1688, 'system', 1, 'Barbade', '_DIMS_PAYS_BB'),
(1689, 'system', 2, 'Barbados', '_DIMS_PAYS_BB'),
(1690, 'system', 1, 'Belau', '_DIMS_PAYS_PW'),
(1691, 'system', 2, 'Palau', '_DIMS_PAYS_PW'),
(1692, 'system', 1, 'Belgique', '_DIMS_PAYS_BE'),
(1693, 'system', 2, 'Belgium', '_DIMS_PAYS_BE'),
(1694, 'system', 1, 'Belize', '_DIMS_PAYS_BZ'),
(1695, 'system', 2, 'Belize', '_DIMS_PAYS_BZ'),
(1696, 'system', 1, 'Bermudes', '_DIMS_PAYS_BM'),
(1697, 'system', 2, 'Bermuda', '_DIMS_PAYS_BM'),
(1698, 'system', 1, 'Bhoutan', '_DIMS_PAYS_BT'),
(1699, 'system', 2, 'Bhutan', '_DIMS_PAYS_BT'),
(1700, 'system', 1, 'Bi&eacute;lorussie', '_DIMS_PAYS_BY'),
(1701, 'system', 2, 'Belarus', '_DIMS_PAYS_BY'),
(1702, 'system', 1, 'Birmanie', '_DIMS_PAYS_MM'),
(1703, 'system', 2, 'Myanmar (ex-Burma)', '_DIMS_PAYS_MM'),
(1704, 'system', 1, 'Bolivie', '_DIMS_PAYS_BO'),
(1705, 'system', 2, 'Bolivia', '_DIMS_PAYS_BO'),
(1706, 'system', 1, 'Bosnie-Herz&eacute;govine', '_DIMS_PAYS_BA'),
(1707, 'system', 2, 'Bosnia and Herzegovina', '_DIMS_PAYS_BA'),
(1708, 'system', 1, 'Botswana', '_DIMS_PAYS_BW'),
(1709, 'system', 2, 'Botswana', '_DIMS_PAYS_BW'),
(1710, 'system', 1, 'Br&eacute;sil', '_DIMS_PAYS_BR'),
(1711, 'system', 2, 'Brazil', '_DIMS_PAYS_BR'),
(1712, 'system', 1, 'Brunei', '_DIMS_PAYS_BN'),
(1713, 'system', 2, 'Brunei Darussalam', '_DIMS_PAYS_BN'),
(1714, 'system', 1, 'Bulgarie', '_DIMS_PAYS_BG'),
(1715, 'system', 2, 'Bulgaria', '_DIMS_PAYS_BG'),
(1716, 'system', 1, 'Burkina Faso', '_DIMS_PAYS_BF'),
(1717, 'system', 2, 'Burkina Faso', '_DIMS_PAYS_BF'),
(1718, 'system', 1, 'Burundi', '_DIMS_PAYS_BI'),
(1719, 'system', 2, 'Burundi', '_DIMS_PAYS_BI'),
(1720, 'system', 1, 'C&ocirc;te d''Ivoire', '_DIMS_PAYS_CI'),
(1721, 'system', 2, 'Ivory Coast (see Cote d''Ivoire)', '_DIMS_PAYS_CI'),
(1722, 'system', 1, 'Cambodge', '_DIMS_PAYS_KH'),
(1723, 'system', 2, 'Cambodia', '_DIMS_PAYS_KH'),
(1724, 'system', 1, 'Cameroun', '_DIMS_PAYS_CM'),
(1725, 'system', 2, 'Cameroon', '_DIMS_PAYS_CM'),
(1726, 'system', 1, 'Canada', '_DIMS_PAYS_CA'),
(1727, 'system', 2, 'Canada', '_DIMS_PAYS_CA'),
(1728, 'system', 1, 'Cap-Vert', '_DIMS_PAYS_CV'),
(1729, 'system', 2, 'Cape Verde', '_DIMS_PAYS_CV'),
(1730, 'system', 1, 'Chili', '_DIMS_PAYS_CL'),
(1731, 'system', 2, 'Chile', '_DIMS_PAYS_CL'),
(1732, 'system', 1, 'Chine', '_DIMS_PAYS_CN'),
(1733, 'system', 2, 'China', '_DIMS_PAYS_CN'),
(1734, 'system', 1, 'Chypre', '_DIMS_PAYS_CY'),
(1735, 'system', 2, 'Cyprus', '_DIMS_PAYS_CY'),
(1736, 'system', 1, 'Colombie', '_DIMS_PAYS_CO'),
(1737, 'system', 2, 'Colombia', '_DIMS_PAYS_CO'),
(1738, 'system', 1, 'Comores', '_DIMS_PAYS_KM'),
(1739, 'system', 2, 'Comoros', '_DIMS_PAYS_KM'),
(1740, 'system', 1, 'Congo', '_DIMS_PAYS_CG'),
(1741, 'system', 2, 'Congo', '_DIMS_PAYS_CG'),
(1742, 'system', 1, 'Cor&eacute;e du Nord', '_DIMS_PAYS_KP'),
(1743, 'system', 2, 'Korea, Demo. People''s Rep. of', '_DIMS_PAYS_KP'),
(1744, 'system', 1, 'Cor&eacute;e du Sud', '_DIMS_PAYS_KR'),
(1745, 'system', 2, 'Korea, (South) Republic of', '_DIMS_PAYS_KR'),
(1746, 'system', 1, 'Costa Rica', '_DIMS_PAYS_CR'),
(1747, 'system', 2, 'Costa Rica', '_DIMS_PAYS_CR'),
(1748, 'system', 1, 'Croatie', '_DIMS_PAYS_HR'),
(1749, 'system', 2, 'Croatia', '_DIMS_PAYS_HR'),
(1750, 'system', 1, 'Cuba', '_DIMS_PAYS_CU'),
(1751, 'system', 2, 'Cuba', '_DIMS_PAYS_CU'),
(1752, 'system', 1, 'Danemark', '_DIMS_PAYS_DK'),
(1753, 'system', 2, 'Denmark', '_DIMS_PAYS_DK'),
(1754, 'system', 1, 'Djibouti', '_DIMS_PAYS_DJ'),
(1755, 'system', 2, 'Djibouti', '_DIMS_PAYS_DJ'),
(1756, 'system', 1, 'Dominique', '_DIMS_PAYS_DM'),
(1757, 'system', 2, 'Dominica', '_DIMS_PAYS_DM'),
(1758, 'system', 1, '&Eacute;gypte', '_DIMS_PAYS_EG'),
(1759, 'system', 2, 'Egypt', '_DIMS_PAYS_EG'),
(1760, 'system', 1, '&Eacute;mirats arabes unis', '_DIMS_PAYS_AE'),
(1761, 'system', 2, 'United Arab Emirates', '_DIMS_PAYS_AE'),
(1762, 'system', 1, '&Eacute;quateur', '_DIMS_PAYS_EC'),
(1763, 'system', 2, 'Ecuador', '_DIMS_PAYS_EC'),
(1764, 'system', 1, '&Eacute;rythr&eacute;e', '_DIMS_PAYS_ER'),
(1765, 'system', 2, 'Eritrea', '_DIMS_PAYS_ER'),
(1766, 'system', 1, 'Espagne', '_DIMS_PAYS_ES'),
(1767, 'system', 2, 'Spain', '_DIMS_PAYS_ES'),
(1768, 'system', 1, 'Estonie', '_DIMS_PAYS_EE'),
(1769, 'system', 2, 'Estonia', '_DIMS_PAYS_EE'),
(1770, 'system', 1, '&Eacute;tats-Unis', '_DIMS_PAYS_US'),
(1771, 'system', 2, 'United States', '_DIMS_PAYS_US'),
(1772, 'system', 1, '&Eacute;thiopie', '_DIMS_PAYS_ET'),
(1773, 'system', 2, 'Ethiopia', '_DIMS_PAYS_ET'),
(1774, 'system', 1, 'Finlande', '_DIMS_PAYS_FI'),
(1775, 'system', 2, 'Finland', '_DIMS_PAYS_FI'),
(1776, 'system', 1, 'France', '_DIMS_PAYS_FR'),
(1777, 'system', 2, 'France', '_DIMS_PAYS_FR'),
(1778, 'system', 1, 'G&eacute;orgie', '_DIMS_PAYS_GE'),
(1779, 'system', 2, 'Georgia', '_DIMS_PAYS_GE'),
(1780, 'system', 1, 'Gabon', '_DIMS_PAYS_GA'),
(1781, 'system', 2, 'Gabon', '_DIMS_PAYS_GA'),
(1782, 'system', 1, 'Gambie', '_DIMS_PAYS_GM'),
(1783, 'system', 2, 'Gambia, the', '_DIMS_PAYS_GM'),
(1784, 'system', 1, 'Ghana', '_DIMS_PAYS_GH'),
(1785, 'system', 2, 'Ghana', '_DIMS_PAYS_GH'),
(1786, 'system', 1, 'Gibraltar', '_DIMS_PAYS_GI'),
(1787, 'system', 2, 'Gibraltar', '_DIMS_PAYS_GI'),
(1788, 'system', 1, 'Gr&egrave;ce', '_DIMS_PAYS_GR'),
(1789, 'system', 2, 'Greece', '_DIMS_PAYS_GR'),
(1790, 'system', 1, 'Grenade', '_DIMS_PAYS_GD'),
(1791, 'system', 2, 'Grenada', '_DIMS_PAYS_GD'),
(1792, 'system', 1, 'Groenland', '_DIMS_PAYS_GL'),
(1793, 'system', 2, 'Greenland', '_DIMS_PAYS_GL'),
(1794, 'system', 1, 'Guadeloupe', '_DIMS_PAYS_GP'),
(1795, 'system', 2, 'Guinea, Equatorial', '_DIMS_PAYS_GP'),
(1796, 'system', 1, 'Guam', '_DIMS_PAYS_GU'),
(1797, 'system', 2, 'Guam', '_DIMS_PAYS_GU'),
(1798, 'system', 1, 'Guatemala', '_DIMS_PAYS_GT'),
(1799, 'system', 2, 'Guatemala', '_DIMS_PAYS_GT'),
(1800, 'system', 1, 'Guin&eacute;e', '_DIMS_PAYS_GN'),
(1801, 'system', 2, 'Guinea', '_DIMS_PAYS_GN'),
(1802, 'system', 1, 'Guin&eacute;e &eacute;quatoriale', '_DIMS_PAYS_GQ'),
(1803, 'system', 2, 'Equatorial Guinea', '_DIMS_PAYS_GQ'),
(1804, 'system', 1, 'Guin&eacute;e-Bissao', '_DIMS_PAYS_GW'),
(1805, 'system', 2, 'Guinea-Bissau', '_DIMS_PAYS_GW'),
(1806, 'system', 1, 'Guyana', '_DIMS_PAYS_GY'),
(1807, 'system', 2, 'Guyana', '_DIMS_PAYS_GY'),
(1808, 'system', 1, 'Guyane fran&ccedil;aise', '_DIMS_PAYS_GF'),
(1809, 'system', 2, 'Guiana, French', '_DIMS_PAYS_GF'),
(1810, 'system', 1, 'Ha&iuml;ti', '_DIMS_PAYS_HT'),
(1811, 'system', 2, 'Haiti', '_DIMS_PAYS_HT'),
(1812, 'system', 1, 'Honduras', '_DIMS_PAYS_HN'),
(1813, 'system', 2, 'Honduras', '_DIMS_PAYS_HN'),
(1814, 'system', 1, 'Hong Kong', '_DIMS_PAYS_HK'),
(1815, 'system', 2, 'Hong Kong, (China)', '_DIMS_PAYS_HK'),
(1816, 'system', 1, 'Hongrie', '_DIMS_PAYS_HU'),
(1817, 'system', 2, 'Hungary', '_DIMS_PAYS_HU'),
(1818, 'system', 1, 'Ile Bouvet', '_DIMS_PAYS_BV'),
(1819, 'system', 2, 'Bouvet Island', '_DIMS_PAYS_BV'),
(1820, 'system', 1, 'Ile Christmas', '_DIMS_PAYS_CX'),
(1821, 'system', 2, 'Christmas Island', '_DIMS_PAYS_CX'),
(1822, 'system', 1, 'Ile Norfolk', '_DIMS_PAYS_NF'),
(1823, 'system', 2, 'Norfolk Island', '_DIMS_PAYS_NF'),
(1824, 'system', 1, 'Iles Cayman', '_DIMS_PAYS_KY'),
(1825, 'system', 2, 'Cayman Islands', '_DIMS_PAYS_KY'),
(1826, 'system', 1, 'Iles Cook', '_DIMS_PAYS_CK'),
(1827, 'system', 2, 'Cook Islands', '_DIMS_PAYS_CK'),
(1828, 'system', 1, 'Iles F&eacute;ro&eacute;', '_DIMS_PAYS_FO'),
(1829, 'system', 2, 'Faroe Islands', '_DIMS_PAYS_FO'),
(1830, 'system', 1, 'Iles Falkland', '_DIMS_PAYS_FK'),
(1831, 'system', 2, 'Falkland Islands (Malvinas)', '_DIMS_PAYS_FK'),
(1832, 'system', 1, 'Iles Fidji', '_DIMS_PAYS_FJ'),
(1833, 'system', 2, 'Fiji', '_DIMS_PAYS_FJ'),
(1834, 'system', 1, 'Iles G&eacute;orgie du Sud et Sandwich du Sud', '_DIMS_PAYS_GS'),
(1835, 'system', 2, 'S. Georgia and S. Sandwich Is.', '_DIMS_PAYS_GS'),
(1836, 'system', 1, 'Iles Heard et McDonald', '_DIMS_PAYS_HM'),
(1837, 'system', 2, 'Heard and McDonald Islands', '_DIMS_PAYS_HM'),
(1838, 'system', 1, 'Iles Marshall', '_DIMS_PAYS_MH'),
(1839, 'system', 2, 'Marshall Islands', '_DIMS_PAYS_MH'),
(1840, 'system', 1, 'Iles Pitcairn', '_DIMS_PAYS_PN'),
(1841, 'system', 2, 'Pitcairn Island', '_DIMS_PAYS_PN'),
(1842, 'system', 1, 'Iles Salomon', '_DIMS_PAYS_SB'),
(1843, 'system', 2, 'Solomon Islands', '_DIMS_PAYS_SB'),
(1844, 'system', 1, 'Iles Svalbard et Jan Mayen', '_DIMS_PAYS_SJ'),
(1845, 'system', 2, 'Svalbard and Jan Mayen Islands', '_DIMS_PAYS_SJ'),
(1846, 'system', 1, 'Iles Turks-et-Caicos', '_DIMS_PAYS_TC'),
(1847, 'system', 2, 'Turks and Caicos Islands', '_DIMS_PAYS_TC'),
(1848, 'system', 1, 'Iles Vierges am&eacute;ricaines', '_DIMS_PAYS_VI'),
(1849, 'system', 2, 'Virgin Islands, U.S.', '_DIMS_PAYS_VI'),
(1850, 'system', 1, 'Iles Vierges britanniques', '_DIMS_PAYS_VG'),
(1851, 'system', 2, 'Virgin Islands, British', '_DIMS_PAYS_VG'),
(1852, 'system', 1, 'Iles des Cocos (Keeling)', '_DIMS_PAYS_CC'),
(1853, 'system', 2, 'Cocos (Keeling) Islands', '_DIMS_PAYS_CC'),
(1854, 'system', 1, 'Iles mineures &eacute;loign&eacute;es des &Eacute;tats-Unis', '_DIMS_PAYS_UM'),
(1855, 'system', 2, 'US Minor Outlying Islands', '_DIMS_PAYS_UM'),
(1856, 'system', 1, 'Inde', '_DIMS_PAYS_IN'),
(1857, 'system', 2, 'India', '_DIMS_PAYS_IN'),
(1858, 'system', 1, 'Indon&eacute;sie', '_DIMS_PAYS_ID'),
(1859, 'system', 2, 'Indonesia', '_DIMS_PAYS_ID'),
(1860, 'system', 1, 'Iran', '_DIMS_PAYS_IR'),
(1861, 'system', 2, 'Iran, Islamic Republic of', '_DIMS_PAYS_IR'),
(1862, 'system', 1, 'Iraq', '_DIMS_PAYS_IQ'),
(1863, 'system', 2, 'Iraq', '_DIMS_PAYS_IQ'),
(1864, 'system', 1, 'Irlande', '_DIMS_PAYS_IE'),
(1865, 'system', 2, 'Ireland', '_DIMS_PAYS_IE'),
(1866, 'system', 1, 'Islande', '_DIMS_PAYS_IS'),
(1867, 'system', 2, 'Iceland', '_DIMS_PAYS_IS'),
(1868, 'system', 1, 'Isra&euml;l', '_DIMS_PAYS_IL'),
(1869, 'system', 2, 'Israel', '_DIMS_PAYS_IL'),
(1870, 'system', 1, 'Italie', '_DIMS_PAYS_IT'),
(1871, 'system', 2, 'Italy', '_DIMS_PAYS_IT'),
(1872, 'system', 1, 'Jama&iuml;que', '_DIMS_PAYS_JM'),
(1873, 'system', 2, 'Jamaica', '_DIMS_PAYS_JM'),
(1874, 'system', 1, 'Japon', '_DIMS_PAYS_JP'),
(1875, 'system', 2, 'Japan', '_DIMS_PAYS_JP'),
(1876, 'system', 1, 'Jordanie', '_DIMS_PAYS_JO'),
(1877, 'system', 2, 'Jordan', '_DIMS_PAYS_JO'),
(1878, 'system', 1, 'Kazakhstan', '_DIMS_PAYS_KZ'),
(1879, 'system', 2, 'Kazakhstan', '_DIMS_PAYS_KZ'),
(1880, 'system', 1, 'Kenya', '_DIMS_PAYS_KE'),
(1881, 'system', 2, 'Kenya', '_DIMS_PAYS_KE'),
(1882, 'system', 1, 'Kirghizistan', '_DIMS_PAYS_KG'),
(1883, 'system', 2, 'Kyrgyzstan', '_DIMS_PAYS_KG'),
(1884, 'system', 1, 'Kiribati', '_DIMS_PAYS_KI'),
(1885, 'system', 2, 'Kiribati', '_DIMS_PAYS_KI'),
(1886, 'system', 1, 'Kowe&iuml;t', '_DIMS_PAYS_KW'),
(1887, 'system', 2, 'Kuwait', '_DIMS_PAYS_KW'),
(1888, 'system', 1, 'Laos', '_DIMS_PAYS_LA'),
(1889, 'system', 2, 'Lao People''s Democratic Republic', '_DIMS_PAYS_LA'),
(1890, 'system', 1, 'Lesotho', '_DIMS_PAYS_LS'),
(1891, 'system', 2, 'Lesotho', '_DIMS_PAYS_LS'),
(1892, 'system', 1, 'Lettonie', '_DIMS_PAYS_LV'),
(1893, 'system', 2, 'Latvia', '_DIMS_PAYS_LV'),
(1894, 'system', 1, 'Liban', '_DIMS_PAYS_LB'),
(1895, 'system', 2, 'Lebanon', '_DIMS_PAYS_LB'),
(1896, 'system', 1, 'Liberia', '_DIMS_PAYS_LR'),
(1897, 'system', 2, 'Liberia', '_DIMS_PAYS_LR'),
(1898, 'system', 1, 'Libye', '_DIMS_PAYS_LY'),
(1899, 'system', 2, 'Libyan Arab Jamahiriya', '_DIMS_PAYS_LY'),
(1900, 'system', 1, 'Liechtenstein', '_DIMS_PAYS_LI'),
(1901, 'system', 2, 'Liechtenstein', '_DIMS_PAYS_LI'),
(1902, 'system', 1, 'Lituanie', '_DIMS_PAYS_LT'),
(1903, 'system', 2, 'Lithuania', '_DIMS_PAYS_LT'),
(1904, 'system', 1, 'Luxembourg', '_DIMS_PAYS_LU'),
(1905, 'system', 2, 'Luxembourg', '_DIMS_PAYS_LU'),
(1906, 'system', 1, 'Macao', '_DIMS_PAYS_MO'),
(1907, 'system', 2, 'Macao, (China)', '_DIMS_PAYS_MO'),
(1908, 'system', 1, 'Madagascar', '_DIMS_PAYS_MG'),
(1909, 'system', 2, 'Madagascar', '_DIMS_PAYS_MG'),
(1910, 'system', 1, 'Malaisie', '_DIMS_PAYS_MY'),
(1911, 'system', 2, 'Malaysia', '_DIMS_PAYS_MY'),
(1912, 'system', 1, 'Malawi', '_DIMS_PAYS_MW'),
(1913, 'system', 2, 'Malawi', '_DIMS_PAYS_MW'),
(1914, 'system', 1, 'Maldives', '_DIMS_PAYS_MV'),
(1915, 'system', 2, 'Maldives', '_DIMS_PAYS_MV'),
(1916, 'system', 1, 'Mali', '_DIMS_PAYS_ML'),
(1917, 'system', 2, 'Mali', '_DIMS_PAYS_ML'),
(1918, 'system', 1, 'Malte', '_DIMS_PAYS_MT'),
(1919, 'system', 2, 'Malta', '_DIMS_PAYS_MT'),
(1920, 'system', 1, 'Mariannes du Nord', '_DIMS_PAYS_MP'),
(1921, 'system', 2, 'Northern Mariana Islands', '_DIMS_PAYS_MP'),
(1922, 'system', 1, 'Maroc', '_DIMS_PAYS_MA'),
(1923, 'system', 2, 'Morocco', '_DIMS_PAYS_MA'),
(1924, 'system', 1, 'Martinique', '_DIMS_PAYS_MQ'),
(1925, 'system', 2, 'Martinique', '_DIMS_PAYS_MQ'),
(1926, 'system', 1, 'Maurice', '_DIMS_PAYS_MU'),
(1927, 'system', 2, 'Mauritius', '_DIMS_PAYS_MU'),
(1928, 'system', 1, 'Mauritanie', '_DIMS_PAYS_MR'),
(1929, 'system', 2, 'Mauritania', '_DIMS_PAYS_MR'),
(1930, 'system', 1, 'Mayotte', '_DIMS_PAYS_YT'),
(1931, 'system', 2, 'Mayotte', '_DIMS_PAYS_YT'),
(1932, 'system', 1, 'Mexique', '_DIMS_PAYS_MX'),
(1933, 'system', 2, 'Mexico', '_DIMS_PAYS_MX'),
(1934, 'system', 1, 'Micron&eacute;sie', '_DIMS_PAYS_FM'),
(1935, 'system', 2, 'Micronesia, Federated States of', '_DIMS_PAYS_FM'),
(1936, 'system', 1, 'Moldavie', '_DIMS_PAYS_MD'),
(1937, 'system', 2, 'Moldova, Republic of', '_DIMS_PAYS_MD'),
(1938, 'system', 1, 'Monaco', '_DIMS_PAYS_MC'),
(1939, 'system', 2, 'Monaco', '_DIMS_PAYS_MC'),
(1940, 'system', 1, 'Mongolie', '_DIMS_PAYS_MN'),
(1941, 'system', 2, 'Mongolia', '_DIMS_PAYS_MN'),
(1942, 'system', 1, 'Montserrat', '_DIMS_PAYS_MS'),
(1943, 'system', 2, 'Montserrat', '_DIMS_PAYS_MS'),
(1944, 'system', 1, 'Mozambique', '_DIMS_PAYS_MZ'),
(1945, 'system', 2, 'Mozambique', '_DIMS_PAYS_MZ'),
(1946, 'system', 1, 'N&eacute;pal', '_DIMS_PAYS_NP'),
(1947, 'system', 2, 'Nepal', '_DIMS_PAYS_NP'),
(1948, 'system', 1, 'Namibie', '_DIMS_PAYS_NA'),
(1949, 'system', 2, 'Namibia', '_DIMS_PAYS_NA'),
(1950, 'system', 1, 'Nauru', '_DIMS_PAYS_NR'),
(1951, 'system', 2, 'Nauru', '_DIMS_PAYS_NR'),
(1952, 'system', 1, 'Nicaragua', '_DIMS_PAYS_NI'),
(1953, 'system', 2, 'Nicaragua', '_DIMS_PAYS_NI'),
(1954, 'system', 1, 'Niger', '_DIMS_PAYS_NE'),
(1955, 'system', 2, 'Niger', '_DIMS_PAYS_NE'),
(1956, 'system', 1, 'Nigeria', '_DIMS_PAYS_NG'),
(1957, 'system', 2, 'Nigeria', '_DIMS_PAYS_NG'),
(1958, 'system', 1, 'Niou&eacute;', '_DIMS_PAYS_NU'),
(1959, 'system', 2, 'Niue', '_DIMS_PAYS_NU'),
(1960, 'system', 1, 'Norv&egrave;ge', '_DIMS_PAYS_NO'),
(1961, 'system', 2, 'Norway', '_DIMS_PAYS_NO'),
(1962, 'system', 1, 'Nouvelle-Cal&eacute;donie', '_DIMS_PAYS_NC'),
(1963, 'system', 2, 'New Caledonia', '_DIMS_PAYS_NC'),
(1964, 'system', 1, 'Nouvelle-Z&eacute;lande', '_DIMS_PAYS_NZ'),
(1965, 'system', 2, 'New Zealand', '_DIMS_PAYS_NZ'),
(1966, 'system', 1, 'Oman', '_DIMS_PAYS_OM'),
(1967, 'system', 2, 'Oman', '_DIMS_PAYS_OM'),
(1968, 'system', 1, 'Ouganda', '_DIMS_PAYS_UG'),
(1969, 'system', 2, 'Uganda', '_DIMS_PAYS_UG'),
(1970, 'system', 1, 'Ouzb&eacute;kistan', '_DIMS_PAYS_UZ'),
(1971, 'system', 2, 'Uzbekistan', '_DIMS_PAYS_UZ'),
(1972, 'system', 1, 'P&eacute;rou', '_DIMS_PAYS_PE'),
(1973, 'system', 2, 'Peru', '_DIMS_PAYS_PE'),
(1974, 'system', 1, 'Pakistan', '_DIMS_PAYS_PK'),
(1975, 'system', 2, 'Pakistan', '_DIMS_PAYS_PK'),
(1976, 'system', 1, 'Panama', '_DIMS_PAYS_PA'),
(1977, 'system', 2, 'Panama', '_DIMS_PAYS_PA'),
(1978, 'system', 1, 'Papouasie-Nouvelle-Guin&eacute;e', '_DIMS_PAYS_PG'),
(1979, 'system', 2, 'Papua New Guinea', '_DIMS_PAYS_PG'),
(1980, 'system', 1, 'Paraguay', '_DIMS_PAYS_PY'),
(1981, 'system', 2, 'Paraguay', '_DIMS_PAYS_PY'),
(1982, 'system', 1, 'Pays-Bas', '_DIMS_PAYS_NL'),
(1983, 'system', 2, 'Netherlands', '_DIMS_PAYS_NL'),
(1984, 'system', 1, 'Philippines', '_DIMS_PAYS_PH'),
(1985, 'system', 2, 'Philippines', '_DIMS_PAYS_PH'),
(1986, 'system', 1, 'Pologne', '_DIMS_PAYS_PL'),
(1987, 'system', 2, 'Poland', '_DIMS_PAYS_PL'),
(1988, 'system', 1, 'Polyn&eacute;sie fran&ccedil;aise', '_DIMS_PAYS_PF'),
(1989, 'system', 2, 'French Polynesia', '_DIMS_PAYS_PF'),
(1990, 'system', 1, 'Porto Rico', '_DIMS_PAYS_PR'),
(1991, 'system', 2, 'Puerto Rico', '_DIMS_PAYS_PR'),
(1992, 'system', 1, 'Portugal', '_DIMS_PAYS_PT'),
(1993, 'system', 2, 'Portugal', '_DIMS_PAYS_PT'),
(1994, 'system', 1, 'Qatar', '_DIMS_PAYS_QA'),
(1995, 'system', 2, 'Qatar', '_DIMS_PAYS_QA'),
(1996, 'system', 1, 'R&eacute;publique centrafricaine', '_DIMS_PAYS_CF'),
(1997, 'system', 2, 'Central African Republic', '_DIMS_PAYS_CF'),
(1998, 'system', 1, 'R&eacute;publique d&eacute;mocratique du Congo', '_DIMS_PAYS_CD'),
(1999, 'system', 2, 'Congo, Democratic Rep. of the', '_DIMS_PAYS_CD'),
(2000, 'system', 1, 'R&eacute;publique dominicaine', '_DIMS_PAYS_DO'),
(2001, 'system', 2, 'Dominican Republic', '_DIMS_PAYS_DO'),
(2002, 'system', 1, 'R&eacute;publique tch&egrave;que', '_DIMS_PAYS_CZ'),
(2003, 'system', 2, 'Czech Republic', '_DIMS_PAYS_CZ'),
(2004, 'system', 1, 'R&eacute;union', '_DIMS_PAYS_RE'),
(2005, 'system', 2, 'Reunion', '_DIMS_PAYS_RE'),
(2006, 'system', 1, 'Roumanie', '_DIMS_PAYS_RO'),
(2007, 'system', 2, 'Romania', '_DIMS_PAYS_RO'),
(2008, 'system', 1, 'Royaume-Uni', '_DIMS_PAYS_GB'),
(2009, 'system', 2, 'Saint Pierre and Miquelon', '_DIMS_PAYS_GB'),
(2010, 'system', 1, 'Russie', '_DIMS_PAYS_RU'),
(2011, 'system', 2, 'Russia (Russian Federation)', '_DIMS_PAYS_RU'),
(2012, 'system', 1, 'Rwanda', '_DIMS_PAYS_RW'),
(2013, 'system', 2, 'Rwanda', '_DIMS_PAYS_RW'),
(2014, 'system', 1, 'S&eacute;n&eacute;gal', '_DIMS_PAYS_SN'),
(2015, 'system', 2, 'Senegal', '_DIMS_PAYS_SN'),
(2016, 'system', 1, 'Sahara occidental', '_DIMS_PAYS_EH'),
(2017, 'system', 2, 'Western Sahara', '_DIMS_PAYS_EH'),
(2018, 'system', 1, 'Saint-Christophe-et-Ni&eacute;v&egrave;s', '_DIMS_PAYS_KN'),
(2019, 'system', 2, 'Saint Kitts and Nevis', '_DIMS_PAYS_KN'),
(2020, 'system', 1, 'Saint-Marin', '_DIMS_PAYS_SM'),
(2021, 'system', 2, 'San Marino', '_DIMS_PAYS_SM'),
(2022, 'system', 1, 'Saint-Pierre-et-Miquelon', '_DIMS_PAYS_PM'),
(2023, 'system', 2, 'Saint Pierre and Miquelon', '_DIMS_PAYS_PM'),
(2024, 'system', 1, 'Saint-Si&egrave;ge', '_DIMS_PAYS_VA'),
(2025, 'system', 2, 'Vatican City State (Holy See)', '_DIMS_PAYS_VA'),
(2026, 'system', 1, 'Saint-Vincent-et-les-Grenadines', '_DIMS_PAYS_VC'),
(2027, 'system', 2, 'Saint Vincent and the Grenadines', '_DIMS_PAYS_VC'),
(2028, 'system', 1, 'Sainte-H&eacute;l&egrave;ne', '_DIMS_PAYS_SH'),
(2029, 'system', 2, 'Saint Helena', '_DIMS_PAYS_SH'),
(2030, 'system', 1, 'Sainte-Lucie', '_DIMS_PAYS_LC'),
(2031, 'system', 2, 'Saint Lucia', '_DIMS_PAYS_LC'),
(2032, 'system', 1, 'Salvador', '_DIMS_PAYS_SV'),
(2033, 'system', 2, 'El Salvador', '_DIMS_PAYS_SV'),
(2034, 'system', 1, 'Samoa', '_DIMS_PAYS_WS'),
(2035, 'system', 2, 'Samoa', '_DIMS_PAYS_WS'),
(2036, 'system', 1, 'Samoa am&eacute;ricaines', '_DIMS_PAYS_AS'),
(2037, 'system', 2, 'American Samoa', '_DIMS_PAYS_AS'),
(2038, 'system', 1, 'Sao Tom&eacute;-et-Principe', '_DIMS_PAYS_ST'),
(2039, 'system', 2, 'Sao Tome and Principe', '_DIMS_PAYS_ST'),
(2040, 'system', 1, 'Seychelles', '_DIMS_PAYS_SC'),
(2041, 'system', 2, 'Seychelles', '_DIMS_PAYS_SC'),
(2042, 'system', 1, 'Sierra Leone', '_DIMS_PAYS_SL'),
(2043, 'system', 2, 'Sierra Leone', '_DIMS_PAYS_SL'),
(2044, 'system', 1, 'Singapour', '_DIMS_PAYS_SG'),
(2045, 'system', 2, 'Singapore', '_DIMS_PAYS_SG'),
(2046, 'system', 1, 'Slov&eacute;nie', '_DIMS_PAYS_SI'),
(2047, 'system', 2, 'Slovenia', '_DIMS_PAYS_SI'),
(2048, 'system', 1, 'Slovaquie', '_DIMS_PAYS_SK'),
(2049, 'system', 2, 'Slovakia', '_DIMS_PAYS_SK'),
(2050, 'system', 1, 'Somalie', '_DIMS_PAYS_SO'),
(2051, 'system', 2, 'Somalia', '_DIMS_PAYS_SO'),
(2052, 'system', 1, 'Soudan', '_DIMS_PAYS_SD'),
(2053, 'system', 2, 'Sudan', '_DIMS_PAYS_SD'),
(2054, 'system', 1, 'Sri Lanka', '_DIMS_PAYS_LK'),
(2055, 'system', 2, 'Sri Lanka (ex-Ceilan)', '_DIMS_PAYS_LK'),
(2056, 'system', 1, 'Su&egrave;de', '_DIMS_PAYS_SE'),
(2057, 'system', 2, 'Sweden', '_DIMS_PAYS_SE'),
(2058, 'system', 1, 'Suisse', '_DIMS_PAYS_CH'),
(2059, 'system', 2, 'Switzerland', '_DIMS_PAYS_CH'),
(2060, 'system', 1, 'Suriname', '_DIMS_PAYS_SR'),
(2061, 'system', 2, 'Suriname', '_DIMS_PAYS_SR'),
(2062, 'system', 1, 'Swaziland', '_DIMS_PAYS_SZ'),
(2063, 'system', 2, 'Swaziland', '_DIMS_PAYS_SZ'),
(2064, 'system', 1, 'Syrie', '_DIMS_PAYS_SY'),
(2065, 'system', 2, 'Syrian Arab Republic', '_DIMS_PAYS_SY'),
(2066, 'system', 1, 'Ta&iuml;wan', '_DIMS_PAYS_TW'),
(2067, 'system', 2, 'Taiwan', '_DIMS_PAYS_TW'),
(2068, 'system', 1, 'Tadjikistan', '_DIMS_PAYS_TJ'),
(2069, 'system', 2, 'Tajikistan', '_DIMS_PAYS_TJ'),
(2070, 'system', 1, 'Tanzanie', '_DIMS_PAYS_TZ'),
(2071, 'system', 2, 'Tanzania, United Republic of', '_DIMS_PAYS_TZ'),
(2072, 'system', 1, 'Tchad', '_DIMS_PAYS_TD'),
(2073, 'system', 2, 'Chad', '_DIMS_PAYS_TD'),
(2074, 'system', 1, 'Terres australes fran&ccedil;aises', '_DIMS_PAYS_TF'),
(2075, 'system', 2, 'French Southern Territories - TF', '_DIMS_PAYS_TF'),
(2076, 'system', 1, 'Territoire britannique de l''Oc&eacute;an Indien', '_DIMS_PAYS_IO'),
(2077, 'system', 2, 'British Indian Ocean Territory', '_DIMS_PAYS_IO'),
(2078, 'system', 1, 'Tha&iuml;lande', '_DIMS_PAYS_TH'),
(2079, 'system', 2, 'Thailand', '_DIMS_PAYS_TH'),
(2080, 'system', 1, 'Timor Oriental', '_DIMS_PAYS_TL'),
(2081, 'system', 2, 'Timor-Leste (East Timor)', '_DIMS_PAYS_TL'),
(2082, 'system', 1, 'Togo', '_DIMS_PAYS_TG'),
(2083, 'system', 2, 'Togo', '_DIMS_PAYS_TG'),
(2084, 'system', 1, 'Tok&eacute;laou', '_DIMS_PAYS_TK'),
(2085, 'system', 2, 'Tokelau', '_DIMS_PAYS_TK'),
(2086, 'system', 1, 'Tonga', '_DIMS_PAYS_TO'),
(2087, 'system', 2, 'Tonga', '_DIMS_PAYS_TO'),
(2088, 'system', 1, 'Trinit&eacute;-et-Tobago', '_DIMS_PAYS_TT'),
(2089, 'system', 2, 'Trinidad & Tobago', '_DIMS_PAYS_TT'),
(2090, 'system', 1, 'Tunisie', '_DIMS_PAYS_TN'),
(2091, 'system', 2, 'Tunisia', '_DIMS_PAYS_TN'),
(2092, 'system', 1, 'Turkm&eacute;nistan', '_DIMS_PAYS_TM'),
(2093, 'system', 2, 'Turkmenistan', '_DIMS_PAYS_TM'),
(2094, 'system', 1, 'Turquie', '_DIMS_PAYS_TR'),
(2095, 'system', 2, 'Turkey', '_DIMS_PAYS_TR'),
(2096, 'system', 1, 'Tuvalu', '_DIMS_PAYS_TV'),
(2097, 'system', 2, 'Tuvalu', '_DIMS_PAYS_TV'),
(2098, 'system', 1, 'Ukraine', '_DIMS_PAYS_UA'),
(2099, 'system', 2, 'Ukraine', '_DIMS_PAYS_UA'),
(2100, 'system', 1, 'Uruguay', '_DIMS_PAYS_UY'),
(2101, 'system', 2, 'Uruguay', '_DIMS_PAYS_UY'),
(2102, 'system', 1, 'Vanuatu', '_DIMS_PAYS_VU'),
(2103, 'system', 2, 'Vanuatu', '_DIMS_PAYS_VU'),
(2104, 'system', 1, 'Venezuela', '_DIMS_PAYS_VE'),
(2105, 'system', 2, 'Venezuela', '_DIMS_PAYS_VE'),
(2106, 'system', 1, 'Vi&ecirc;t Nam', '_DIMS_PAYS_VN'),
(2107, 'system', 2, 'Viet Nam', '_DIMS_PAYS_VN'),
(2108, 'system', 1, 'Wallis-et-Futuna', '_DIMS_PAYS_WF'),
(2109, 'system', 2, 'Wallis and Futuna', '_DIMS_PAYS_WF'),
(2110, 'system', 1, 'Y&eacute;men', '_DIMS_PAYS_YE'),
(2111, 'system', 2, 'Yemen', '_DIMS_PAYS_YE'),
(2112, 'system', 1, 'Yougoslavie', '_DIMS_PAYS_YU'),
(2113, 'system', 2, 'Saint Pierre and Miquelon', '_DIMS_PAYS_YU'),
(2114, 'system', 1, 'Zambie', '_DIMS_PAYS_ZM'),
(2115, 'system', 2, 'Zambia', '_DIMS_PAYS_ZM'),
(2116, 'system', 1, 'Zimbabwe', '_DIMS_PAYS_ZW'),
(2117, 'system', 2, 'Zimbabwe', '_DIMS_PAYS_ZW'),
(2118, 'system', 1, 'ex-R&eacute;publique yougoslave de Mac&eacute;doine', '_DIMS_PAYS_MK'),
(2119, 'system', 2, 'Macedonia, TFYR', '_DIMS_PAYS_MK'),
(2120, 'system', 1, 'Similarit&eacute;s des contacts', '_DIMS_LABEL_SIMILAR_CT'),
(2121, 'system', 1, 'Ev&egrave;nements &agrave; venir', '_DIMS_LABEL_EVENTS_TO_COME'),
(2122, 'system', 1, 'Il n''existe aucune fiche semblable dans la base de donn&eacute;es, vous pouvez valider votre enregistrement.', '_DIMS_LABEL_NO_SIMILAR'),
(2123, 'system', 1, 'Correspondance exacte', '_DIMS_LABEL_EXACT_CORRESP_SING'),
(2124, 'system', 1, 'Correspondances exactes', '_DIMS_LABEL_EXACT_CORRESP_PLUR'),
(2125, 'system', 1, 'Nom identique / Pr&eacute;nom proche', '_DIMS_LABEL_EXACT_NAME'),
(2126, 'system', 1, 'Nom et pr&eacute;nom proches', '_DIMS_LABEL_PROX_NP'),
(2127, 'system', 1, 'Correspondances &eacute;loign&eacute;es', '_DIMS_LABEL_LESS_PROX_PLUR'),
(2128, 'system', 1, 'Correspondance &eacute;loign&eacute;e', '_DIMS_LABEL_LESS_PROX_SING'),
(2129, 'system', 1, 'Forcer l''enregistrement', '_DIMS_LABEL_FORCE_SAVE'),
(2162, 'moduletype', 0, 'value', 'phpvalue'),
(2131, 'system', 1, 'Afficher la fiche de ce contact', '_DIMS_LABEL_AFFICH_INF_CT'),
(2132, 'system', 1, 'Correspondance proche', '_DIMS_LABEL_PROX_CORRESP_SING'),
(2133, 'system', 1, 'Correspondances proches', '_DIMS_LABEL_PROX_CORRESP_PLUR'),
(2134, 'system', 1, 'Admin. &eacute;v&egrave;nements', '_DIMS_EVENT_ADMIN_SELF'),
(2135, 'system', 1, 'Inscription en attente', '_DIMS_LABEL_REGISTRATION_WAIT'),
(2136, 'system', 1, 'Supprimer les valeurs', '_DIMS_LABEL_SUPPR_SEARCH_VAL'),
(2137, 'system', 1, 'Inscription valid&eacute;e', '_DIMS_LABEL_REGISTRATION_VALIDATED'),
(2139, 'system', 1, 'Inscription', '_REGISTRATION'),
(2140, 'system', 1, 'Aucune inscription', '_DIMS_LABEL_NO_REGISTRATION'),
(2141, 'system', 1, 'Inscription annul&eacute;e', '_DIMS_LABEL_REGISTRATION_CANCELED'),
(2142, 'system', 1, 'L&eacute;gende', '_DIMS_LEGEND'),
(2143, 'system', 1, 'Inscription ayant valid&eacute; le niveau 1, et en attente de validation du niveau 2 (&Eacute;v&eacute;nement &agrave; deux niveau uniquement)', '_DIMS_LABEL_REGISTRATION_WAIT_FOR_2'),
(2144, 'system', 1, 'Inscriptions &agrave; deux niveaux', '_EVENT_FORM_2LEVEL'),
(2145, 'system', 1, 'Valider l''inscription', '_DIMS_VALID_REGISTER'),
(2146, 'system', 1, 'Heure d&eacute;but', '_DIMS_LABEL_HEUREDEB_SHORT'),
(2147, 'system', 1, 'Heure fin', '_DIMS_LABEL_HEUREFIN_SHORT'),
(2148, 'system', 1, 'Cliquez ici pour afficher la pr&eacute;sentation de cette personne.', '_DIMS_TITLE_PRES_PERS'),
(2149, 'system', 1, 'Ce contact est un partenaire &eacute;v&egrave;nement.', '_DIMS_TITLE_CT_PART_EVT'),
(2150, 'system', 1, 'Cliquez ici pour afficher la photo de ce contact.', '_DIMS_TITLE_DISP_PHOTO'),
(2151, 'system', 1, 'Mettre cette fiche en veille.', '_DIMS_TITLE_TO_WATCH'),
(2152, 'system', 1, 'Ajouter &agrave; vos contacts personnels.', '_DIMS_TITLE_ADD_TO_CT_PERS'),
(2153, 'system', 1, 'Ajouter un commentaire (le commentaire actuel se trouvera dans la partie historique).', '_DIMS_TITLE_ADD_COMM'),
(2154, 'system', 1, 'Modifier ce commentaire.', '_DIMS_TITLE_MODIFY_COMMENTAIRE'),
(2155, 'system', 1, 'Cette fiche a &eacute;t&eacute; d&eacute;sactiv&eacute;e.', '_DIMS_LABEL_FICHE_SUPPR'),
(2156, 'system', 1, 'D&eacute;sactiver cette fiche', '_DIMS_LABEL_DESACTIV_SHEET'),
(2157, 'system', 1, 'Activer cette fiche', '_DIMS_LABEL_ACTIV_SHEET'),
(2158, 'system', 1, 'Document d''annonce', '_DIMS_EVT_DOC_ANNONCE'),
(2159, 'system', 1, 'Gestion de l''inscription de niveaux deux', '_DIMS_EVT_MANAGE_LEVEL_TWO'),
(2160, 'system', 1, 'Inscription(s)', '_DIMS_LABEL_REGISTRATION_S'),
(2161, 'system', 1, 'Fiche attach&eacute;', '_DIMS_LABEL_FICHE_ATTACHED'),
(2163, 'system', 1, 'AGRICULTURE, SYLVICULTURE ET P&Ecirc;CHE', '_DIMS_NACE_A00'),
(2164, 'system', 1, 'Culture et production animale, chasse et services annexes', '_DIMS_NACE_A01'),
(2165, 'system', 1, 'Sylviculture et exploitation foresti&egrave;re', '_DIMS_NACE_A02'),
(2166, 'system', 1, 'P&ecirc;che et aquaculture', '_DIMS_NACE_A03'),
(2167, 'system', 1, 'INDUSTRIES EXTRACTIVES', '_DIMS_NACE_B00'),
(2168, 'system', 1, 'Extraction de houille et de lignite', '_DIMS_NACE_B05'),
(2169, 'system', 1, 'Extraction d''hydrocarbures', '_DIMS_NACE_B06'),
(2170, 'system', 1, 'Extraction de minerais m&eacute;talliques', '_DIMS_NACE_B07'),
(2171, 'system', 1, 'Autres industries extractives', '_DIMS_NACE_B08'),
(2172, 'system', 1, 'Services de soutien aux industries extractives', '_DIMS_NACE_B09'),
(2173, 'system', 1, 'INDUSTRIE MANUFACTURI&Egrave;RE', '_DIMS_NACE_C00'),
(2174, 'system', 1, 'Industries alimentaires', '_DIMS_NACE_C10'),
(2175, 'system', 1, 'Fabrication de boissons', '_DIMS_NACE_C11'),
(2176, 'system', 1, 'Fabrication de produits &agrave; base de tabac', '_DIMS_NACE_C12'),
(2177, 'system', 1, 'Fabrication de textiles', '_DIMS_NACE_C13'),
(2178, 'system', 1, 'Industrie de l''habillement', '_DIMS_NACE_C14'),
(2179, 'system', 1, 'Industrie du cuir et de la chaussure', '_DIMS_NACE_C15'),
(2180, 'system', 1, 'Travail du bois et fabrication d''articles en bois et en li&egrave;ge, &agrave; l’exception des meubles ; fabrication d’articles en vannerie et sparterie', '_DIMS_NACE_C16'),
(2181, 'system', 1, 'Industrie du papier et du carton', '_DIMS_NACE_C17'),
(2182, 'system', 1, 'Imprimerie et reproduction d''enregistrements', '_DIMS_NACE_C18'),
(2183, 'system', 1, 'Cok&eacute;faction et raffinage', '_DIMS_NACE_C19'),
(2184, 'system', 1, 'Industrie chimique', '_DIMS_NACE_C20'),
(2185, 'system', 1, 'Industrie pharmaceutique', '_DIMS_NACE_C21'),
(2186, 'system', 1, 'Fabrication de produits en caoutchouc et en plastique', '_DIMS_NACE_C22'),
(2187, 'system', 1, 'Fabrication d''autres produits min&eacute;raux non m&eacute;talliques', '_DIMS_NACE_C23'),
(2188, 'system', 1, 'M&eacute;tallurgie', '_DIMS_NACE_C24'),
(2189, 'system', 1, 'Fabrication de produits m&eacute;talliques, &agrave; l’exception des machines et des &eacute;quipements', '_DIMS_NACE_C25'),
(2190, 'system', 1, 'Fabrication de produits informatiques, &eacute;lectroniques et optiques', '_DIMS_NACE_C26'),
(2191, 'system', 1, 'Fabrication d''&eacute;quipements &eacute;lectriques', '_DIMS_NACE_C27'),
(2192, 'system', 1, 'Fabrication de machines et &eacute;quipements n.c.a.', '_DIMS_NACE_C28'),
(2193, 'system', 1, 'Industrie automobile', '_DIMS_NACE_C29'),
(2194, 'system', 1, 'Fabrication d''autres mat&eacute;riels de transport', '_DIMS_NACE_C30'),
(2195, 'system', 1, 'Fabrication de meubles', '_DIMS_NACE_C31'),
(2196, 'system', 1, 'Autres industries manufacturi&egrave;res', '_DIMS_NACE_C32'),
(2197, 'system', 1, 'R&eacute;paration et installation de machines et d''&eacute;quipements', '_DIMS_NACE_C33'),
(2198, 'system', 1, 'PRODUCTION ET DISTRIBUTION D''&Eacute;LECTRICIT&Eacute;, DE GAZ, DE VAPEUR ET D''AIR CONDITIONN&Eacute;', '_DIMS_NACE_D00'),
(2199, 'system', 1, 'Production et distribution d''&eacute;lectricit&eacute;, de gaz, de vapeur et d''air conditionn&eacute;', '_DIMS_NACE_D35'),
(2200, 'system', 1, 'PRODUCTION ET DISTRIBUTION D''EAU; ASSAINISSEMENT, GESTION DES D&Eacute;CHETS ET D&Eacute;POLLUTION', '_DIMS_NACE_E00'),
(2201, 'system', 1, 'Captage, traitement et distribution d''eau', '_DIMS_NACE_E36');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(2202, 'system', 1, 'Collecte et traitement des eaux us&eacute;es', '_DIMS_NACE_E37'),
(2203, 'system', 1, 'Collecte, traitement et &eacute;limination des d&eacute;chets; r&eacute;cup&eacute;ration', '_DIMS_NACE_E38'),
(2204, 'system', 1, 'D&eacute;pollution et autres services de gestion des d&eacute;chets', '_DIMS_NACE_E39'),
(2205, 'system', 1, 'CONSTRUCTION', '_DIMS_NACE_F00'),
(2206, 'system', 1, 'Construction de bâtiments', '_DIMS_NACE_F41'),
(2207, 'system', 1, 'G&eacute;nie civil', '_DIMS_NACE_F42'),
(2208, 'system', 1, 'Travaux de construction sp&eacute;cialis&eacute;s', '_DIMS_NACE_F43'),
(2209, 'system', 1, 'COMMERCE; R&Eacute;PARATION D''AUTOMOBILES ET DE MOTOCYCLES', '_DIMS_NACE_G00'),
(2210, 'system', 1, 'Commerce et r&eacute;paration d''automobiles et de motocycles', '_DIMS_NACE_G45'),
(2211, 'system', 1, 'Commerce de gros, &agrave; l’exception des automobiles et des motocycles', '_DIMS_NACE_G46'),
(2212, 'system', 1, 'Commerce de d&eacute;tail, &agrave; l’exception des automobiles et des motocycles', '_DIMS_NACE_G47'),
(2213, 'system', 1, 'TRANSPORTS ET ENTREPOSAGE', '_DIMS_NACE_H00'),
(2214, 'system', 1, 'Transports terrestres et transport par conduites', '_DIMS_NACE_H49'),
(2215, 'system', 1, 'Transports par eau', '_DIMS_NACE_H50'),
(2216, 'system', 1, 'Transports a&eacute;riens', '_DIMS_NACE_H51'),
(2217, 'system', 1, 'Entreposage et services auxiliaires des transports', '_DIMS_NACE_H52'),
(2218, 'system', 1, 'Activit&eacute;s de poste et de courrier', '_DIMS_NACE_H53'),
(2219, 'system', 1, 'H&Eacute;BERGEMENT ET RESTAURATION', '_DIMS_NACE_I00'),
(2220, 'system', 1, 'H&eacute;bergement', '_DIMS_NACE_I55'),
(2221, 'system', 1, 'Restauration', '_DIMS_NACE_I56'),
(2222, 'system', 1, 'INFORMATION ET COMMUNICATION', '_DIMS_NACE_J00'),
(2223, 'system', 1, '&Eacute;dition', '_DIMS_NACE_J58'),
(2224, 'system', 1, 'Production de films cin&eacute;matographiques, de vid&eacute;o et de programmes de t&eacute;l&eacute;vision; enregistrement sonore et &eacute;dition musicale', '_DIMS_NACE_J59'),
(2225, 'system', 1, 'Programmation et diffusion', '_DIMS_NACE_J60'),
(2226, 'system', 1, 'T&eacute;l&eacute;communications', '_DIMS_NACE_J61'),
(2227, 'system', 1, 'Programmation, conseil et autres activit&eacute;s informatiques', '_DIMS_NACE_J62'),
(2228, 'system', 1, 'Services d''information', '_DIMS_NACE_J63'),
(2229, 'system', 1, 'ACTIVIT&Eacute;S FINANCI&Egrave;RES ET D''ASSURANCE', '_DIMS_NACE_K00'),
(2230, 'system', 1, 'Activit&eacute;s des services financiers, hors assurance et caisses de retraite', '_DIMS_NACE_K64'),
(2231, 'system', 1, 'Assurance', '_DIMS_NACE_K65'),
(2232, 'system', 1, 'Activit&eacute;s auxiliaires de services financiers et d''assurance', '_DIMS_NACE_K66'),
(2233, 'system', 0, 'ACTIVIT&Eacute;S IMMOBILI&Egrave;RES', '_DIMS_NACE_L00'),
(2234, 'system', 1, 'Activit&eacute;s immobili&egrave;res', '_DIMS_NACE_L68'),
(2235, 'system', 1, 'ACTIVIT&Eacute;S SP&Eacute;CIALIS&Eacute;ES, SCIENTIFIQUES ET TECHNIQUES', '_DIMS_NACE_M00'),
(2236, 'system', 1, 'Activit&eacute;s juridiques et comptables', '_DIMS_NACE_M69'),
(2237, 'system', 1, 'Activit&eacute;s des si&egrave;ges sociaux; conseil de gestion', '_DIMS_NACE_M70'),
(2238, 'system', 1, 'Activit&eacute;s d''architecture et d''ing&eacute;nierie; activit&eacute;s de contr&ocirc;le et analyses techniques', '_DIMS_NACE_M71'),
(2239, 'system', 1, 'Recherche d&eacute;veloppement scientifique', '_DIMS_NACE_M72'),
(2240, 'system', 1, 'Publicit&eacute; et &eacute;tudes de march&eacute;', '_DIMS_NACE_M73'),
(2241, 'system', 1, 'Autres activit&eacute;s sp&eacute;cialis&eacute;es, scientifiques et techniques', '_DIMS_NACE_M74'),
(2242, 'system', 1, 'Activit&eacute;s v&eacute;t&eacute;rinaires', '_DIMS_NACE_M75'),
(2243, 'system', 1, 'ACTIVIT&Eacute;S DE SERVICES ADMINISTRATIFS ET DE SOUTIEN', '_DIMS_NACE_N00'),
(2244, 'system', 1, 'Activit&eacute;s de location et location bail', '_DIMS_NACE_N77'),
(2245, 'system', 1, 'Activit&eacute;s li&eacute;es &agrave; l''emploi', '_DIMS_NACE_N78'),
(2246, 'system', 1, 'Activit&eacute;s des agences de voyage, voyagistes, services de r&eacute;servation et activit&eacute;s connexes', '_DIMS_NACE_N79'),
(2247, 'system', 0, 'Enqu&ecirc;tes et s&eacute;curit&eacute;', '_DIMS_NACE_N80'),
(2248, 'system', 1, 'Services relatifs aux bâtiments et am&eacute;nagement paysager', '_DIMS_NACE_N81'),
(2249, 'system', 1, 'Activit&eacute;s administratives et autres activit&eacute;s de soutien aux entreprises', '_DIMS_NACE_N82'),
(2250, 'system', 1, 'ADMINISTRATION PUBLIQUE', '_DIMS_NACE_O00'),
(2251, 'system', 1, 'Administration publique et d&eacute;fense; s&eacute;curit&eacute; sociale obligatoire', '_DIMS_NACE_O84'),
(2252, 'system', 1, 'ENSEIGNEMENT', '_DIMS_NACE_P00'),
(2253, 'system', 1, 'Enseignement', '_DIMS_NACE_P85'),
(2254, 'system', 1, 'SANT&Eacute; HUMAINE ET ACTION SOCIALE', '_DIMS_NACE_Q00'),
(2255, 'system', 1, 'Activit&eacute;s pour la sant&eacute; humaine', '_DIMS_NACE_Q86'),
(2256, 'system', 1, 'H&eacute;bergement m&eacute;dico-social et social', '_DIMS_NACE_Q87'),
(2257, 'system', 1, 'Action sociale sans h&eacute;bergement', '_DIMS_NACE_Q88'),
(2258, 'system', 1, 'ARTS, SPECTACLES ET ACTIVIT&Eacute;S R&Eacute;CR&Eacute;ATIVES', '_DIMS_NACE_R00'),
(2259, 'system', 1, 'Activit&eacute;s cr&eacute;atives, artistiques et de spectacle', '_DIMS_NACE_R90'),
(2260, 'system', 1, 'Biblioth&egrave;ques, archives, mus&eacute;es et autres activit&eacute;s culturelles', '_DIMS_NACE_R91'),
(2261, 'system', 1, 'Organisation de jeux de hasard et d''argent', '_DIMS_NACE_R92'),
(2262, 'system', 0, 'Activit&eacute;s sportives, r&eacute;cr&eacute;atives et de loisirs', '_DIMS_NACE_R93'),
(2263, 'system', 1, 'AUTRES ACTIVIT&Eacute;S DE SERVICES', '_DIMS_NACE_S00'),
(2264, 'system', 1, 'Activit&eacute;s des organisations associatives', '_DIMS_NACE_S94'),
(2265, 'system', 1, 'R&eacute;paration d''ordinateurs et de biens personnels et domestiques', '_DIMS_NACE_S95'),
(2266, 'system', 1, 'Autres services personnels', '_DIMS_NACE_S96'),
(2267, 'system', 1, 'Activit&eacute;s des m&eacute;nages en tant qu''employeurs de personnel domestique', '_DIMS_NACE_S97'),
(2268, 'system', 1, 'Activit&eacute;s indiff&eacute;renci&eacute;es des m&eacute;nages en tant que producteurs de biens et services pour usage propre', '_DIMS_NACE_S98'),
(2269, 'system', 1, 'ACTIVIT&Eacute;S DES M&Eacute;NAGES EN TANT QU''EMPLOYEURS; ACTIVIT&Eacute;S INDIFF&Eacute;RENCI&Eacute;ES DES M&Eacute;NAGES EN TANT QUE PRODUCTEURS DE BIENS ET SERVICES POUR USAGE PROPRE', '_DIMS_NACE_T00'),
(2270, 'system', 1, 'Activit&eacute;s des organisations et organismes extraterritoriaux', '_DIMS_NACE_T99'),
(2271, 'system', 2, 'AGRICULTURE, FORESTRY AND FISHING', '_DIMS_NACE_A00'),
(2272, 'system', 2, 'Crop and animal production, hunting and related service activities', '_DIMS_NACE_A01'),
(2273, 'system', 2, 'Forestry and logging', '_DIMS_NACE_A02'),
(2274, 'system', 2, 'Fishing and aquaculture', '_DIMS_NACE_A03'),
(2275, 'system', 2, 'MINING AND QUARRYING', '_DIMS_NACE_B00'),
(2276, 'system', 2, 'Mining of coal and lignite', '_DIMS_NACE_B05'),
(2277, 'system', 2, 'Extraction of crude petroleum and natural gas', '_DIMS_NACE_B06'),
(2278, 'system', 2, 'Mining of metal ores', '_DIMS_NACE_B07'),
(2279, 'system', 2, 'Other mining and quarrying', '_DIMS_NACE_B08'),
(2280, 'system', 2, 'Mining support service activities', '_DIMS_NACE_B09'),
(2281, 'system', 2, 'MANUFACTURING', '_DIMS_NACE_C00'),
(2282, 'system', 2, 'Manufacture of food products', '_DIMS_NACE_C10'),
(2283, 'system', 2, 'Manufacture of beverages', '_DIMS_NACE_C11'),
(2284, 'system', 2, 'Manufacture of tobacco products', '_DIMS_NACE_C12'),
(2285, 'system', 2, 'Manufacture of textiles', '_DIMS_NACE_C13'),
(2286, 'system', 2, 'Manufacture of wearing apparel', '_DIMS_NACE_C14'),
(2287, 'system', 2, 'Manufacture of leather and related products', '_DIMS_NACE_C15'),
(2288, 'system', 2, 'Manufacture of wood and of products of wood and cork, except furniture; manufacture of articles of straw and plaiting materials', '_DIMS_NACE_C16'),
(2289, 'system', 2, 'Manufacture of paper and paper products', '_DIMS_NACE_C17'),
(2290, 'system', 2, 'Printing and reproduction of recorded media', '_DIMS_NACE_C18'),
(2291, 'system', 2, 'Manufacture of coke and refined petroleum products', '_DIMS_NACE_C19'),
(2292, 'system', 2, 'Manufacture of chemicals and chemical products', '_DIMS_NACE_C20'),
(2293, 'system', 2, 'Manufacture of basic pharmaceutical products and pharmaceutical preparations', '_DIMS_NACE_C21'),
(2294, 'system', 2, 'Manufacture of rubber and plastic products', '_DIMS_NACE_C22'),
(2295, 'system', 2, 'Manufacture of other non-metallic mineral products', '_DIMS_NACE_C23'),
(2296, 'system', 2, 'Manufacture of basic metals', '_DIMS_NACE_C24'),
(2297, 'system', 2, 'Manufacture of fabricated metal products, except machinery and equipment', '_DIMS_NACE_C25'),
(2298, 'system', 2, 'Manufacture of computer, electronic and optical products', '_DIMS_NACE_C26'),
(2299, 'system', 2, 'Manufacture of electrical equipment', '_DIMS_NACE_C27'),
(2300, 'system', 2, 'Manufacture of machinery and equipment n.e.c.', '_DIMS_NACE_C28'),
(2301, 'system', 2, 'Manufacture of motor vehicles, trailers and semi-trailers', '_DIMS_NACE_C29'),
(2302, 'system', 2, 'Manufacture of other transport equipment', '_DIMS_NACE_C30'),
(2303, 'system', 2, 'Manufacture of furniture', '_DIMS_NACE_C31'),
(2304, 'system', 2, 'Other manufacturing', '_DIMS_NACE_C32'),
(2305, 'system', 2, 'Repair and installation of machinery and equipment', '_DIMS_NACE_C33'),
(2306, 'system', 2, 'ELECTRICITY, GAS, STEAM AND AIR CONDITIONING SUPPLY', '_DIMS_NACE_D00'),
(2307, 'system', 2, 'Electricity, gas, steam and air conditioning supply', '_DIMS_NACE_D35'),
(2308, 'system', 2, 'WATER SUPPLY; SEWERAGE, WASTE MANAGEMENT AND REMEDIATION ACTIVITIES', '_DIMS_NACE_E00'),
(2309, 'system', 2, 'Water collection, treatment and supply', '_DIMS_NACE_E36'),
(2310, 'system', 2, 'Sewerage', '_DIMS_NACE_E37'),
(2311, 'system', 2, 'Waste collection, treatment and disposal activities; materials recovery', '_DIMS_NACE_E38'),
(2312, 'system', 2, 'Remediation activities and other waste management services', '_DIMS_NACE_E39'),
(2313, 'system', 2, 'CONSTRUCTION', '_DIMS_NACE_F00'),
(2314, 'system', 2, 'Construction of buildings', '_DIMS_NACE_F41'),
(2315, 'system', 2, 'Civil engineering', '_DIMS_NACE_F42'),
(2316, 'system', 2, 'Specialised construction activities', '_DIMS_NACE_F43'),
(2317, 'system', 2, 'WHOLESALE AND RETAIL TRADE; REPAIR OF MOTOR VEHICLES AND MOTORCYCLES', '_DIMS_NACE_G00'),
(2318, 'system', 2, 'Wholesale and retail trade and repair of motor vehicles and motorcycles', '_DIMS_NACE_G45'),
(2319, 'system', 2, 'Wholesale trade, except of motor vehicles and motorcycles', '_DIMS_NACE_G46'),
(2320, 'system', 2, 'Retail trade, except of motor vehicles and motorcycles', '_DIMS_NACE_G47'),
(2321, 'system', 2, 'TRANSPORTATION AND STORAGE', '_DIMS_NACE_H00'),
(2322, 'system', 2, 'Land transport and transport via pipelines', '_DIMS_NACE_H49'),
(2323, 'system', 2, 'Water transport', '_DIMS_NACE_H50'),
(2324, 'system', 2, 'Air transport', '_DIMS_NACE_H51'),
(2325, 'system', 2, 'Warehousing and support activities for transportation', '_DIMS_NACE_H52'),
(2326, 'system', 2, 'Postal and courier activities', '_DIMS_NACE_H53'),
(2327, 'system', 2, 'ACCOMMODATION AND FOOD SERVICE ACTIVITIES', '_DIMS_NACE_I00'),
(2328, 'system', 2, 'Accommodation', '_DIMS_NACE_I55'),
(2329, 'system', 2, 'Food and beverage service activities', '_DIMS_NACE_I56'),
(2330, 'system', 2, 'INFORMATION AND COMMUNICATION', '_DIMS_NACE_J00'),
(2331, 'system', 2, 'Publishing activities', '_DIMS_NACE_J58'),
(2332, 'system', 2, 'Motion picture, video and television programme production, sound recording and music publishing activities', '_DIMS_NACE_J59'),
(2333, 'system', 2, 'Programming and broadcasting activities', '_DIMS_NACE_J60'),
(2334, 'system', 2, 'Telecommunications', '_DIMS_NACE_J61'),
(2335, 'system', 2, 'Computer programming, consultancy and related activities', '_DIMS_NACE_J62'),
(2336, 'system', 2, 'Information service activities', '_DIMS_NACE_J63'),
(2337, 'system', 2, 'FINANCIAL AND INSURANCE ACTIVITIES', '_DIMS_NACE_K00'),
(2338, 'system', 2, 'Financial service activities, except insurance and pension funding', '_DIMS_NACE_K64'),
(2339, 'system', 2, 'Insurance, reinsurance and pension funding, except compulsory social security', '_DIMS_NACE_K65'),
(2340, 'system', 2, 'Activities auxiliary to financial services and insurance activities', '_DIMS_NACE_K66'),
(2341, 'system', 2, 'REAL ESTATE ACTIVITIES', '_DIMS_NACE_L00'),
(2342, 'system', 2, 'Real estate activities', '_DIMS_NACE_L68'),
(2343, 'system', 2, 'PROFESSIONAL, SCIENTIFIC AND TECHNICAL ACTIVITIES', '_DIMS_NACE_M00'),
(2344, 'system', 2, 'Legal and accounting activities', '_DIMS_NACE_M69'),
(2345, 'system', 2, 'Activities of head offices; management consultancy activities', '_DIMS_NACE_M70'),
(2346, 'system', 2, 'Architectural and engineering activities; technical testing and analysis', '_DIMS_NACE_M71'),
(2347, 'system', 2, 'Scientific research and development', '_DIMS_NACE_M72'),
(2348, 'system', 2, 'Advertising and market research', '_DIMS_NACE_M73'),
(2349, 'system', 2, 'Other professional, scientific and technical activities', '_DIMS_NACE_M74'),
(2350, 'system', 2, 'Veterinary activities', '_DIMS_NACE_M75'),
(2351, 'system', 2, 'ADMINISTRATIVE AND SUPPORT SERVICE ACTIVITIES', '_DIMS_NACE_N00'),
(2352, 'system', 2, 'Rental and leasing activities', '_DIMS_NACE_N77'),
(2353, 'system', 2, 'Employment activities', '_DIMS_NACE_N78'),
(2354, 'system', 2, 'Travel agency, tour operator and other reservation service and related activities', '_DIMS_NACE_N79'),
(2355, 'system', 2, 'Security and investigation activities', '_DIMS_NACE_N80'),
(2356, 'system', 2, 'Services to buildings and landscape activities', '_DIMS_NACE_N81'),
(2357, 'system', 2, 'Office administrative, office support and other business support activities', '_DIMS_NACE_N82'),
(2358, 'system', 2, 'PUBLIC ADMINISTRATION AND DEFENCE; COMPULSORY SOCIAL SECURITY', '_DIMS_NACE_O00'),
(2359, 'system', 2, 'Public administration and defence; compulsory social security', '_DIMS_NACE_O84'),
(2360, 'system', 2, 'EDUCATION', '_DIMS_NACE_P00'),
(2361, 'system', 2, 'Education', '_DIMS_NACE_P85'),
(2362, 'system', 2, 'HUMAN HEALTH AND SOCIAL WORK ACTIVITIES', '_DIMS_NACE_Q00'),
(2363, 'system', 2, 'Human health activities', '_DIMS_NACE_Q86'),
(2364, 'system', 2, 'Residential care activities', '_DIMS_NACE_Q87'),
(2365, 'system', 2, 'Social work activities without accommodation', '_DIMS_NACE_Q88'),
(2366, 'system', 2, 'ARTS, ENTERTAINMENT AND RECREATION', '_DIMS_NACE_R00'),
(2367, 'system', 2, 'Creative, arts and entertainment activities', '_DIMS_NACE_R90'),
(2368, 'system', 2, 'Libraries, archives, museums and other cultural activities', '_DIMS_NACE_R91'),
(2369, 'system', 2, 'Gambling and betting activities', '_DIMS_NACE_R92'),
(2370, 'system', 2, 'Sports activities and amusement and recreation activities', '_DIMS_NACE_R93'),
(2371, 'system', 2, 'OTHER SERVICE ACTIVITIES', '_DIMS_NACE_S00'),
(2372, 'system', 2, 'Activities of membership organisations', '_DIMS_NACE_S94'),
(2373, 'system', 2, 'Repair of computers and personal and household goods', '_DIMS_NACE_S95'),
(2374, 'system', 2, 'Other personal service activities', '_DIMS_NACE_S96'),
(2375, 'system', 2, 'Activities of households as employers of domestic personnel', '_DIMS_NACE_S97'),
(2376, 'system', 2, 'Undifferentiated goods- and services-producing activities of private households for own use', '_DIMS_NACE_S98'),
(2377, 'system', 2, 'ACTIVITIES OF HOUSEHOLDS AS EMPLOYERS; U0NDIFFERENTIATED GOODS- AND SERVICES-PRODUCING ACTIVITIES OF HOUSEHOLDS FOR OWN USE', '_DIMS_NACE_T00'),
(2378, 'system', 2, 'Activities of extraterritorial organisations and bodies', '_DIMS_NACE_T99'),
(2379, 'system', 1, 'Ressources internes', '_DIMS_LABEL_INTERNAL_SOURCES'),
(2380, 'system', 1, 'Gestion des &eacute;tapes', '_DIMS_EVT_STEP_MANAGE'),
(2381, 'system', 1, 'Ajouter une &eacute;tape', '_DIMS_EVT_STEP_ADD_ONE'),
(2382, 'system', 1, 'Ajouter un fichier &agrave; l''&eacute;tape', '_DIMS_EVT_STEP_ATTACH_FILE'),
(2383, 'system', 1, 'Modification de l''&eacute;tape', '_DIMS_EVT_STEP_MODIFY'),
(2384, 'system', 1, 'Dernier import de personnes', '_DIMS_LAST_IMPORT_PERS'),
(2385, 'system', 1, 'Rattacher une personne en tant que cr&eacute;ateur des fiches', '_DIMS_LABEL_PERS_CREATE_IMPPORT'),
(2386, 'system', 1, 'Aucun contact ne correspond &agrave; cette recherche.', '_DIMS_LABEL_NO_RESP_CONT_SEARCH'),
(2387, 'system', 1, 'Import des fiches personnes', '_LABEL_ADMIN_IMPORT_CT'),
(2388, 'system', 1, 'Nouvelle fiches personnes et entreprises', '_LABEL_ADMIN_NEW_CTANDENT'),
(2389, 'system', 1, 'Nouvelles fiches personnes, entreprises connues', '_LABEL_ADMIN_NEW_CT_OLDENT'),
(2390, 'system', 1, 'Nouvelles fiches entreprises, personnes connues', '_LABEL_ADMIN_NEW_ENT_OLDCT'),
(2391, 'system', 1, 'Fiches personnes et entreprises connues', '_LABEL_ADMIN_NEW_OLDCTENT'),
(2392, 'system', 1, 'Envoyer un mail group&eacute;', '_DIMS_LABEL_EMAIL_SEND'),
(2393, 'system', 1, 'Ajouter cette entreprise.', '_DIMS_LABEL_ADD_THIS_ENT'),
(2394, 'system', 1, 'Ajouter tout', '_DIMS_LABEL_ADD_ALL'),
(2395, 'system', 1, 'Nouvelles personnes sans lien avec une entreprise', '_LABEL_ADMIN_NEW_CT_SS_ENT'),
(2396, 'system', 1, 'Personnes connues sans lien avec une entreprise', '_LABEL_ADMIN_OLD_CT_SS_ENT'),
(2397, 'system', 1, 'Cette fiche a été supprimé de l''import.', '_DIMS_LABEL_DELETED_SHEET_IMP'),
(2398, 'system', 1, 'Informations sur la personne issue de l''import', '_DIMS_TITLE_INF_PERS_IMP'),
(2399, 'system', 1, 'Informations sur l''entreprise issue de l''import', '_DIMS_TITLE_COMP_ENT_IMP'),
(2400, 'system', 1, 'Ajouter la personne seulement.', '_DIMS_LABEL_ADD_PERS_ONLY'),
(2401, 'system', 1, 'Mettre &agrave; jour les informations sur l''entreprise seulement.', '_DIMS_LABEL_ADD_OLDENT_ONLY'),
(2402, 'system', 1, 'La fiche a bien été modifiée', '_DIMS_LABEL_MOD_SHEET_IMP'),
(2403, 'system', 1, 'La fiche a bien été ajoutée', '_DIMS_LABEL_ADDED_SHEET_IMP'),
(2404, 'system', 1, 'Mettre &agrave; jour les informations sur la personne seulement.', '_DIMS_LABEL_ADD_OLDPERS_ONLY'),
(2405, 'system', 1, 'Ajouter l''entreprise seulement', '_DIMS_LABEL_ADD_ENT_ONLY'),
(2406, 'system', 1, 'Non trouv&eacute;', '_DIMS_LABEL_NOFOUND'),
(3279, 'system', 2, 'Not found', '_NOFOUND'),
(2408, 'system', 1, 'Inscription ayant valid&eacute; le niveau 1, et en attente de validation du niveau 2', '_DIMS_LABEL_REGISTRATION_WAIT_FOR_2_2'),
(2409, 'system', 1, 'Descriptif', '_DIMS_LABEL_DESCRIPTIF'),
(2410, 'system', 1, 'Validation en cours (tous les documents ne sont pas valid&eacute;s)', '_DIMS_LABEL_RUNNING_REGISTRATION'),
(2411, 'system', 1, '&Eacute;tape valid&eacute;e', '_DIMS_LABEL_VALIDATED_STATE'),
(2412, 'system', 1, '&Eacute;tape annul&eacute;e', '_DIMS_LABEL_CANCELED_STATE'),
(2413, 'system', 1, 'Documents attach&eacute;s', '_DIMS_LABEL_LINKED_DOCS_EVT'),
(2414, 'system', 1, 'provenant de', '_DIMS_LABEL_DOC_FROM'),
(2415, 'system', 1, 'I-Net Portal (front office)', '_DIMS_LABEL_INET'),
(2416, 'system', 1, 'Cl&eacute; USB', '_DIMS_LABEL_USB_KEY'),
(2417, 'system', 1, 'CD Rom / DVD', '_DIMS_LABEL_CD_DVD'),
(2418, 'system', 1, 'Courrier', '_DIMS_LABEL_COURRIER'),
(2419, 'system', 1, 'Document re&ccedil;u', '_DIMS_LABEL_RECEIVED_DOC'),
(2420, 'system', 1, 'Date de r&eacute;ception', '_DIMS_LABEL_DATE_RECEPTION_DOC'),
(2421, 'system', 1, 'Valid&eacute; le', '_DIMS_LABEL_VALIDATE_ON'),
(2422, 'system', 1, 'Annuler la validation.', '_DIMS_LABEL_STOP_DOC_VALIDATION'),
(2423, 'system', 1, 'En attente de r&eacute;ception du document.', '_DIMS_LABEL_WAIT_FOR_DOC'),
(2424, 'system', 1, 'Phases', '_DIMS_LABEL_PHASE'),
(2425, 'system', 1, 'Clore les inscriptions', '_DIMS_LABEL_CLOSE_INSCRIPTION'),
(2426, 'system', 1, '&Eacute;v&egrave;nement clos', '_DIMS_LABEL_CLOSED_EVT'),
(2427, 'system', 1, 'Ouvrir les inscriptions', '_DIMS_LABEL_OPEN_INSCRIPTION'),
(2428, 'system', 1, 'Inscriptions ouvertes', '_DIMS_LABEL_OPENED_INSCR_EVT'),
(2429, 'system', 1, 'Inscriptions ferm&eacute;es', '_DIMS_LABEL_CLOSED_INSCR_EVT'),
(2430, 'system', 1, 'Document en attente', '_DIMS_LABEL_DOCUMENT_WAIT'),
(2431, 'system', 1, 'Document valid&eacute;', '_DIMS_LABEL_VALIDATED_DOC'),
(2432, 'system', 1, 'Import Outlook&copy;', '_LABEL_IMPORT_OUTLOOK'),
(2433, 'system', 1, 'Dernier import personne', '_LABEL_LAST_IMPORT_CT'),
(2434, 'system', 1, 'Vider la table d''import', '_LABEL_CLEAN_IMPORT_TABLE'),
(2435, 'system', 1, 'Rattacher &agrave; un contact', '_DIMS_LINK_CONTACT'),
(2436, 'system', 1, '&Eacute;tape 1 : Importer le fichier Outlook&copy;', '_LABEL_IMPORT_STEP1'),
(2437, 'system', 1, '&Eacute;tape 2 : Les contacts connus', '_LABEL_IMPORT_STEP2'),
(2438, 'system', 1, '&Eacute;tape 3 : Comparaison de contacts', '_LABEL_IMPORT_STEP3'),
(2439, 'system', 1, '&Eacute;tape 4 : Les contacts inconnus', '_LABEL_IMPORT_STEP4'),
(2440, 'system', 1, '&Eacute;tapes de la proc&eacute;dure', '_LABEL_IMPORT_STEPS'),
(2441, 'system', 1, 'Aucune &eacute;tape d&eacute;finie', '_LABEL_NOSTEP'),
(2442, 'system', 1, 'Il y a peut-&ecirc;tre d&eacute;j&agrave; une fiche pour cet utilisateur', '_DIMS_USER_FILE_EXIST'),
(2443, 'system', 1, 'ou', '_DIMS_OR'),
(2444, 'system', 1, 'Cr&eacute;er une nouvelle fiche', '_DIMS_USER_CREATE_NEW_FILE'),
(2445, 'system', 1, 'Souhaitez-vous le rattacher &agrave; un de ces contacts', '_DIMS_USER_WISH_RATTACH_TO_FILE'),
(2446, 'system', 1, 'Ajouter un message', '_DIMS_LABEL_ADD_MESSAGE'),
(2447, 'system', 1, 'Message explicatif (facultatif)', '_DIMS_CONTENT_EXPLAIN_OPTIONAL'),
(2448, 'system', 1, 'Nouveau message', '_DIMS_MSG_CREATE'),
(2449, 'system', 1, 'Boites mails', '_SYSTEM_LABELICON_MAILBOX'),
(2450, 'system', 1, 'Ajouter une boite mails', '_DIMS_LABEL_MAILBOX_ADD'),
(2451, 'system', 1, '&Eacute;tape 5 : Récapulatif', '_LABEL_IMPORT_STEP5'),
(2452, 'system', 1, 'serveur', '_SERVER'),
(2453, 'system', 1, 'protocole', '_DIMS_LABEL_PROTOCOL'),
(2454, 'system', 1, 'port', '_DIMS_LABEL_PORT'),
(2455, 'system', 1, 'm&eacute;thode de cryptage', '_DIMS_LABEL_CRYPTO'),
(2456, 'system', 1, 'Modifier une boite mails', '_DIMS_LABEL_MAILBOX_MODIFY'),
(2457, 'system', 1, 'Relever les mails manuellement', '_DIMS_WEBMAIL_CHECK_MANUALLY'),
(2458, 'system', 1, 'Nombre de mails r&eacute;cup&eacute;r&eacute;', '_DIMS_LABEL_NB_MAIL_RETRIEVE'),
(2459, 'system', 1, 'Décochez les contacts que vous ne souhaitez pas importer.', '_IMPORT_INSTRUCTION_STEP4'),
(2460, 'system', 1, 'Aucun de vos contacts &agrave; importer ne pr&eacute;sente de similitude avec l''un des contacts d&eacute;j&agrave; pr&eacute;sents.', '_IMPORT_NO_SIMILAR_CT'),
(2461, 'system', 1, 'Si vous passez cette &eacute;tape, le nouveau contact sera consid&eacute;r&eacute; comme d&eacute;j&agrave; pr&eacute;sent.', '_IMPORT_WARN_STEP3'),
(2462, 'system', 1, 'Je souhaite passer ce contact.', '_IMPORT_NEXT_SIMILAR_CONTACT'),
(2463, 'system', 1, 'Mon contact n''&eacute;xiste pas je souhaite l''importer sur le site.', '_IMPORT_NEW_SIMILAR_CONTACT'),
(3184, 'system', 2, 'This contact is the contact import', '_DIMS_IMPORT_CT_SAME'),
(2465, 'system', 1, 'Contacts similaires', '_IMPORT_TAB_SIMILAR_CONTACT'),
(2466, 'system', 1, 'Nouveau contact', '_IMPORT_TAB_NEW_CONTACT'),
(2467, 'system', 1, 'Voir la liste', '_IMPORT_VIEW_LISTE'),
(2468, 'system', 1, 'de vos contacts sont d&eacute;j&agrave; pr&eacute;sents sur le site.', '_IMPORT_CONTACTS_ALREADY_EXISTS'),
(2469, 'system', 1, 'Erreur : Le fichier n''est pas correct.', '_IMPORT_ERROR_FILE_NOT_CORRECT'),
(2470, 'system', 1, 'Retour a l''&eacute;tape 1', '_IMPORT_RETURN_TO_STEP1'),
(2471, 'system', 1, 'Contact similaire', '_IMPORT_TAB_SIMILAR_CONTACT_SINGLE'),
(2472, 'system', 1, 'Passer cette &eacute;tape', '_IMPORT_GO_NEXT_STEP'),
(2473, 'system', 1, 'Liste des contacts restants', '_IMPORT_TAB_LAST_CONTACTS'),
(2474, 'system', 1, 'Tous vos contacts sont d&eacute;j&agrave; connus.', '_IMPORT_ALL_CONTACTS_ALREADY_EXISTS'),
(2475, 'system', 1, 'L''import de vos contacts est termin&eacute;', '_IMPORT_COMPLETE'),
(2476, 'system', 1, 'Contacts importés', '_IMPORT_IMPORTED_CONTACTS'),
(2477, 'system', 1, 'Contacts rattach&eacute;s &agrave; une entreprise', '_IMPORT_LINKED_CONTACTS'),
(2478, 'system', 1, 'Entreprise', '_DIMS_LABEL_COMPANY'),
(2479, 'system', 1, 'Validation', '_DIMS_LABEL_VALIDATION'),
(2480, 'system', 1, 'Demande d''accusé de réception', '_TICKET_ENABLE_VALIDATION'),
(2481, 'system', 1, 'Date limite de validation', '_TICKET_LIMIT_TIME_VALIDATION'),
(2482, 'system', 1, 'Destinataires', '_DIMS_LABEL_DESTS'),
(2483, 'system', 1, 'Rechercher un destinataire', '_TICKET_SEARCH_ADDRESSEE'),
(2484, 'system', 1, 'Liste de vos &eacute;v&eacute;nements', '_DIMS_FO_EVT_LIST'),
(2485, 'system', 1, 'État de l''inscription', '_DIMS_LABEL_REGISTRATION_STATE'),
(2486, 'system', 1, 'Inscription refus&eacute;e', '_DIMS_LABEL_REGISTRATION_REFUSED'),
(2487, 'system', 1, 'Inscription en cours', '_DIMS_LABEL_REGISTRATION_CURRENT'),
(2488, 'system', 1, 'Vous n''&ecirc;tes inscrit &agrave; aucun &eacute;v&egrave;nement.', '_DIMS_LABEL_NO_EVENT_REGISTRATION'),
(2489, 'system', 1, 'Vos donn&eacute;es personnelles', '_DIMS_LABEL_PERSONNAL_INFOS'),
(2490, 'system', 1, 'Veuillez vous connecter pour acc&eacute;der &agrave; vos évènements.', '_DIMS_LABEL_LOGGIN_TO_SEE_EVT'),
(2491, 'system', 1, 'Validation de votre insctiption', '_DIMS_LABEL_REGISTRATION_VALIDATION'),
(2492, 'system', 1, 'Afin de valider votre inscription, merci de compl&eacute;ter les documents propos&eacute;s en t&eacute;l&eacute;chargement et de nous les retourner dans les plus brefs d&eacute;lais.<br/> Lorsque les inscriptions seront closes vous ne pourrez plus acc&eacute;der &agrave; cette interface, si toutes les &acute;tapes de l''inscription ne sont pas valid&eacute;es,<br/> votre inscription ne sera pas prise en compte.<br/>', '_DIMS_TEXT_REGISTRATION_FORM_2'),
(2494, 'system', 1, 'D&eacute;posez votre fichier compl&eacute;t&eacute;', '_DIMS_LABEL_DOCUMENT_SEND'),
(2495, 'system', 1, 'Les inscriptions &agrave; cet &eacute;v&egrave;nement sont closes', '_DIMS_LABEL_FO_REGISTRATION_CLOSE'),
(2496, 'system', 1, 'Votre demande d''inscription est en attente de validation, vous recevrez un email prochainement.', '_DIMS_TEXT_REGISTRATION_WAIT_EMAIL'),
(2497, 'system', 1, 'Retour &agrave; la page d''accueil', '_DIMS_RETURN_TO_HOME'),
(2498, 'system', 1, 'L''objet n''existe pas.', '_DIMS_TICKET_NO_OBJECT'),
(3185, 'system', 2, 'This event does not have to step <br/> you must first create them through the schedule before submitting entries', '_DIMS_TEXT_EVT_NO_ETAPS'),
(3471, 'system', 2, 'Module', '_DIMS_LABEL_MODULE_ROLES'),
(2500, 'system', 1, 'Nombres d''inscriptions d&eacute;sir&eacute;es', '_DIMS_LABEL_NO_REGISTRATIONS'),
(2501, 'system', 1, 'Date et heure', '_EVENT_DATE_AND_TIME'),
(2502, 'system', 1, 'D&eacute;tails', '_EVENT_DETAILS'),
(2503, 'system', 1, '&Eacute;tape', '_DIMS_LABEL_STEP'),
(2504, 'system', 1, 'Créé le :', '_DIMS_LABEL_CREATE'),
(2505, 'system', 1, 'Partenaires', '_DIMS_LABEL_PARTNER'),
(2506, 'system', 1, 'Mét.', '_DIMS_LABEL_COM_MET'),
(2507, 'system', 1, 'Souhaitez-vous vraiment supprimer ce document', '_DIMS_ARE_YOU_SURE_TO_DEL'),
(2508, 'system', 1, 'Document en cours de validation', '_DIMS_DOC_VALIDATION_IN_PROGRESS'),
(2509, 'system', 1, 'Vous souhaitez vous inscrire &agrave; cet &eacute;v&egrave;nement.<br />\r\nVous pouvez &eacute;galement inscrire des personnes de votre choix.<br />\r\nLorsque vous aurez indiqu&eacute; le nombre de personnes (vous y compris), vous pourrez renseigner les formulaires.', '_DIMS_TEXT_INTRO_FORM1'),
(2510, 'system', 1, 'Informations complémentaires', '_DIMS_EVT_INFO_COMPL'),
(2511, 'system', 1, 'Votre inscription', '_DIMS_EVT_INSCRIPT_SELF'),
(3259, 'system', 2, 'Nobody', '_OPTION_MODIFY_NOBODY'),
(2513, 'system', 1, 'Informations obligatoires', '_DIMS_EVT_INFO_OBLIG'),
(2514, 'system', 1, 'Faire l''import en tant que', '_IMPORT_USER_WHO_IMPORT'),
(2515, 'system', 1, 'Import du fichier', '_IMPORT_DOWNLOAD_FILE'),
(2516, 'system', 1, 'Contacts connu', '_IMPORT_KNOWN_CONTACTS'),
(2517, 'system', 1, 'Contacts avec similitude', '_IMPORT_CONTACTS_WITH_SIMILAR_PROFIL'),
(2518, 'system', 1, 'Contacts inconnu', '_IMPORT_UNKNOWN_CONTACTS'),
(2519, 'system', 1, 'Récapitulatif', '_IMPORT_SUMMARY'),
(2520, 'system', 1, 'de vos contacts ont &eacute;t&eacute; ignor&eacute;s', '_IMPORT_COUNT_OF_IGNORED_CONTACTS'),
(2521, 'system', 1, 'contacts restant à importer', '_IMPORT_CONTACTS_RESTANT'),
(2522, 'system', 1, 'Faire un rattachement pour l''entreprise', '_IMPORT_ENT_WHO_IMPORT'),
(2523, 'system', 1, 'Nouvelles fiches', '_DIMS_LABEL_NEW_SHEET_SINCE'),
(4732, 'wce', 1, 'Ancienne url', '_WCE_URLREWRITE_OLD'),
(2525, 'system', 1, 'Import missions', '_LABEL_IMPORT_MISSIONS'),
(2526, 'system', 1, 'Voir toutes les fiches', '_DIMS_LABEL_SEE_ALL_SHEET'),
(2527, 'system', 1, 'Dont [x] contacts mis a jour', '_IMPORT_UPDATED_CONTACTS'),
(2528, 'system', 1, 'Cet &eacute;v&egrave;nement ne poss&egrave;de pas d''&eacute;tape, <br/>vous devez d''abord les cr&eacute;er via le planning avant de valider les inscriptions', '_DIMS_TEXT_EVT_NO_ETAPS'),
(3285, 'system', 2, 'Number of entries desired', '_DIMS_LABEL_NO_REGISTRATIONS'),
(3395, 'system', 2, ' Error  directory installed is not writable by Apache!  Submit  Analysis  for more data', '_DIMS_LABEL_ADDNEWMODULE_WARNING'),
(2532, 'system', 1, 'Revenir &agrave; la liste des inscriptions', '_DIMS_EVT_BACK_REGISTRATION'),
(2533, 'system', 1, 'Plus d''options', '_DIMS_MORE_OPTIONS'),
(2534, 'system', 1, 'Réception', '_DIMS_LABEL_RECEPTION'),
(2535, 'system', 1, 'Document de référence', '_DIMS_LABEL_DOC_REFERENCE'),
(2536, 'system', 1, 'Action(s) pour l''étape', '_DIMS_LABEL_ACTIONS_MILESTONE'),
(2537, 'system', 1, '&Eacute;tape suivante', '_DIMS_LABEL_NEXT_MILESTONE'),
(2538, 'system', 1, 'Ajout d''une étape', '_DIMS_EVT_STEP_ADD'),
(3044, 'system', 2, 'Modified at', '_DIMS_LABEL_MODIF_ON_FEM'),
(2541, 'system', 1, 'Modifier', '_MODIFY'),
(3340, 'system', 2, 'Edit an Organizer', '_AGENDA_ORGANIZER_MODIFY'),
(3341, 'system', 2, 'List of organizers', '_AGENDA_ORGANIZER_LIST'),
(2544, 'system', 1, '&Agrave;', '_DIMS_LABEL_MAIL_TO'),
(2545, 'system', 1, 'Copie à', '_DIMS_LABEL_MAIL_CC'),
(3459, 'system', 2, 'Add group', '_DIMS_LABEL_CREATE_GROUP'),
(3460, 'system', 2, 'Delete group', '_DIMS_LABEL_DELETE_GROUP'),
(2549, 'system', 1, 'Fichier(s) joint(s)', '_DIMS_LABEL_MAIL_ATTACHMENT'),
(2550, 'system', 1, 'Aucuns e-mails', '_DIMS_LABEL_MAIL_NONE'),
(3458, 'system', 2, 'Add sub group', '_DIMS_LABEL_CREATE_CHILD'),
(2554, 'system', 1, '&Eacute;tape 1 : Importer le fichier', '_LABEL_IMPORT_MISSION_STEP1'),
(3143, 'system', 2, 'Step 1 : import file', '_LABEL_IMPORT_MISSION_STEP1'),
(3151, 'system', 2, 'Step 4: The unknown contacts', '_LABEL_IMPORT_STEP4'),
(3150, 'system', 2, 'Step 3: Comparison of contact', '_LABEL_IMPORT_MISSION_STEP3'),
(3157, 'system', 2, '', ''),
(3481, 'system', 2, 'Roles list', '_DIMS_LABEL_ROLE_LIST'),
(3478, 'system', 2, 'No modules defined for this group', '_DIMS_LABEL_NO_MODULE_DEFINED'),
(2561, 'system', 1, 'Importer une liste d''inscrits', '_DIMS_LABEL_IMPORT_INSCRIPT'),
(2562, 'system', 1, 'de vos contacts seront rattachés sans intervention de votre part', '_IMPORT_MISSION_CONTACT_KNOW'),
(2563, 'system', 1, 'Contacts rattachés à l''événement', '_IMPORT_MISSION_RATTACHED_CONTACTS'),
(2564, 'system', 1, 'Import entreprises', '_LABEL_IMPORT_ENTREPRISE'),
(2565, 'system', 1, 'Entreprises connus', '_IMPORT_KNOWN_TIER'),
(2566, 'system', 1, 'Entreprises avec similitude', '_IMPORT_TIER_WITH_SIMILAR_PROFIL'),
(2567, 'system', 1, 'Entreprises inconnues', '_IMPORT_UNKNOWN_TIER'),
(3466, 'system', 2, 'Member of mailing list', '_DIMS_LABEL_AVAILABLE_MAILINGLIST_ATTACH'),
(3467, 'system', 2, 'Modules used for this workspace', '_DIMS_LABEL_GROUP_USABLE_MODULES'),
(2570, 'system', 1, '&Eacute;tape 2 : Les entreprises connues', '_LABEL_IMPORT_TIER_STEP2'),
(2571, 'system', 1, '&Eacute;tape 3 : Comparaison d''entreprises', '_LABEL_IMPORT_TIER_STEP3'),
(2572, 'system', 1, '&Eacute;tape 4 : Les entreprises inconnues', '_LABEL_IMPORT_TIER_STEP4'),
(3155, 'system', 2, 'Procedure', '_LABEL_IMPORT_STEPS'),
(3477, 'system', 2, 'Default profile', '_DIMS_LABEL_DEFAULT_PROFILE'),
(3153, 'system', 2, 'Step 5:summary', '_LABEL_IMPORT_STEP5'),
(2576, 'system', 1, 'Je souhaite passer cette entreprise', '_IMPORT_NEXT_SIMILAR_TIER'),
(3474, 'system', 2, 'Module manager', '_DIMS_LABEL_MODULE_ADMINISTRATOR'),
(3475, 'system', 2, 'Available modules', '_DIMS_LABEL_SYSTEM_AVAILABLE_MODULES'),
(2578, 'system', 1, 'Liste des entreprises restantes', '_IMPORT_TAB_LAST_TIER'),
(2579, 'system', 1, 'Mon entreprise n''&eacute;xiste pas je souhaite l''importer sur le site', '_IMPORT_NEW_SIMILAR_TIER'),
(2580, 'system', 1, 'entreprise similaire', '_IMPORT_TAB_SIMILAR_TIER_SINGLE'),
(2581, 'system', 1, 'Entreprises similaires', '_IMPORT_TAB_SIMILAR_TIER'),
(3470, 'system', 2, 'Module settings', '_DIMS_LABEL_MODULE_PARAMS'),
(2583, 'system', 1, 'entreprises restants à importer', '_IMPORT_TIER_RESTANT'),
(2584, 'system', 1, 'Décochez les entreprises que vous ne souhaitez pas importer.', '_IMPORT_INSTRUCTION_TIER_STEP4'),
(2585, 'system', 1, 'L''import de vos entreprises est termin&eacute;', '_IMPORT_TIER_COMPLETE'),
(2586, 'system', 1, 'Entreprises importées', '_IMPORT_IMPORTED_TIER'),
(3286, 's', 2, 'Which [x] contacts updated', '_IMPORT_UPDATED_CONTACTS'),
(2588, 'system', 1, 'de vos entreprises sont d&eacute;j&agrave; pr&eacute;sents sur le site.', '_IMPORT_TIERS_ALREADY_EXISTS'),
(3465, 'system', 2, 'Mailing lists available for this workspace', '_DIMS_LABEL_AVAILABLE_MAILINGLIST'),
(3378, 'system', 2, 'Backoffice access', '_DIMS_LABEL_ADMINDOMAIN'),
(3338, 'system', 2, 'Edit Category', '_AGENDA_CAT_MODIFY'),
(3339, 'system', 2, 'Create an Organizer', '_AGENDA_ORGANIZER_CREATE'),
(3426, 'system', 2, 'Sender', '_DIMS_LABEL_TICKET_EMETTEUR'),
(3427, 'system', 2, 'Posted at ', '_DIMS_LABEL_TICKET_DPOST'),
(3214, 'system', 2, 'Attachement files', '_DIMS_LABEL_MAIL_ATTACHMENT'),
(3215, 'system', 2, 'of your contacts will be attached without your intervention', '_IMPORT_MISSION_CONTACT_KNOW'),
(3216, 'system', 2, 'Contacts associated with the event', '_IMPORT_MISSION_RATTACHED_CONTACTS'),
(3421, 'system', 2, 'Uninstall', '_DIMS_LABEL_UNINSTALL'),
(3422, 'system', 2, 'Add role', '_DIMS_LABEL_ROLEADD'),
(3423, 'system', 2, 'Office phone', '_DIMS_LABEL_TEL_WORK'),
(3424, 'system', 2, 'Siret No.', '_DIMS_LABEL_ENT_SIRET'),
(3425, 'system', 2, 'Choice', '_DIMS_LABEL_CHOOSE'),
(3144, 'system', 2, 'Step 2 : Known contacts', '_LABEL_IMPORT_STEP2'),
(3483, 'system', 2, 'Are you sure you want \\ nDelete this group?', '_SYSTEM_MSG_CONFIRMGROUPDELETE'),
(3145, 'system', 2, 'Step 2: The well-known companies', '_LABEL_IMPORT_TIER_STEP2'),
(3482, 'system', 2, 'Edit roles', '_DIMS_LABEL_MODIFY_ROLE_ASSIGNMENT'),
(3154, 'system', 2, 'Next step', '_DIMS_LABEL_NEXT_MILESTONE'),
(3176, 'system', 2, 'No step defined', '_LABEL_NOSTEP'),
(3152, 'system', 2, 'Step 4: The unknown enterprises', '_LABEL_IMPORT_TIER_STEP4'),
(3452, 'system', 2, 'Enable Backoffice', '_DIMS_LABEL_GROUP_ADMIN'),
(2611, 'system', 1, 'Aucune de vos entreprises &agrave; importer ne pr&eacute;sente de similitude avec l''une des entreprises d&eacute;j&agrave; pr&eacute;sents.', '_IMPORT_NO_SIMILAR_TIERS'),
(3451, 'system', 2, 'Role required for this workspace', '_DIMS_LABEL_GROUP_MUSTDEFINERULE'),
(3287, 'system', 2, 'My company does not exists I want to import it on the site', '_IMPORT_NEW_SIMILAR_TIER'),
(3394, 'system', 2, 'Adds a new module by sending a file. Zip', '_DIMS_LABEL_ADDNEWMODULE_DESC'),
(2616, 'system', 1, 'Cette entreprise correspond à l''entreprise à importer.', '_IMPORT_MY_TIER_IS_SIMILAR'),
(3289, 'system', 2, '2 Uncheck the companies you do not want to import.\r\n', '_IMPORT_INSTRUCTION_TIER_STEP4'),
(3288, 'system', 2, 'remaining companies to import', '_IMPORT_TIER_RESTANT'),
(3290, 'system', 2, 'The import of your business is finished', '_IMPORT_TIER_COMPLETE'),
(2621, 'system', 1, 'Nouvelle entreprise', '_IMPORT_TAB_NEW_COMPANY'),
(2622, 'system', 1, 'Si vous passez cette &eacute;tape, la nouvelle entreprise sera consid&eacute;r&eacute;e comme d&eacute;j&agrave; pr&eacute;sente.', '_IMPORT_TIERS_WARN_STEP3'),
(2623, 'system', 1, 'de vos entreprises ont &eacute;t&eacute; ignor&eacute;s', '_IMPORT_COUNT_OF_IGNORED_TIERS'),
(3392, 'system', 2, 'New modules', '_DIMS_LABEL_UNINSTALLEDMODULES'),
(3393, 'system', 2, 'Add a new module', '_DIMS_LABEL_ADDNEWMODULE'),
(2625, 'system', 1, 'Dont [x] entreprises mis à jour', '_IMPORT_UPDATED_TIERS'),
(2626, 'system', 1, 'Extraction des missions', '_DIMS_IMPORT_MISSIONS_EXTRACT'),
(2627, 'system', 1, 'Mission', '_DIMS_IMPORT_LABEL_MISSION'),
(2628, 'system', 1, 'Import des contacts', '_IMPORT_CONTACTS'),
(2629, 'system', 1, 'ATTENTION : il y a plus de 100 r&eacute;sultats, veuillez utiliser le fichier d''export pour tous les consulter.', '_DIMS_LABEL_TOO_MUCH_RES_SEARCH'),
(2630, 'system', 1, 'Il n''y a aucun contact inconnu', '_DIMS_IMPORT_MISSION_NO_INKNOW'),
(2631, 'system', 1, 'contact(s) inconnu(s) restant', '_DIMS_IMPORT_MISSION_STILL_UNKNOW'),
(2632, 'system', 1, 'Ce contact correspond au contact à importer', '_DIMS_IMPORT_CT_SAME'),
(2633, 'system', 1, 'Créer un nouveau contact à partir de l''import', '_DIMS_IMPORT_CT_NO_SAME'),
(2634, 'system', 1, 'Si vous passez cette &eacute;tape, le nouveau contact ne sera pas attaché à la mission.', '_IMPORT_MISSION_WARN_STEP3'),
(2635, 'system', 1, 'Rattacher des utilisateurs supplémentaires', '_IMPORT_TIERS_ATTACH_MORE'),
(2636, 'system', 1, 'Enregistrement', '_DIMS_REGISTRATION'),
(2637, 'system', 1, 'Contact ignoré pendant l''import', '_IMPORT_MISSION_IGNORED_CONTACT'),
(2638, 'system', 1, 'Ce contact était déjà rattaché à cet événement', '_IMPORT_TEXT_CT_ALREADY_ATTACHED'),
(2640, 'system', 1, 'Sur cette page vous pouvez vous inscrire pour obtenir une invitation à cet événement', '_DIMS_FRONT_TEXT_SUBSCRIPTION'),
(2641, 'system', 1, 'Si vous possédez déjà un compte sur notre système, vous pouvez vous connecter pour accèder à la liste des événements auquelles vous êtes inscrits.<br /> Si vous souhaitez participer à un événement veuillez retourner sur la liste de nos événements.', '_DIMS_FRONT_TEXT_NO_LOGGIN'),
(2642, 'system', 1, 'Aucun événement ne correspond à votre demande.<br />Retournez à la liste des événements.', '_DIMS_FRONT_TEXT_NO_EVENT'),
(2643, 'system', 1, 'Liens', '_DIMS_LABEL_LINKS'),
(2644, 'system', 1, 'Organisateur', '_DIMS_LABEL_ORGANIZER'),
(2645, 'system', 1, 'Responsable', '_DIMS_LABEL_RESPONSIBLE'),
(3035, 'system', 2, 'Date to close inscription', '_DIMS_EVT_DATE_CLOSE_REGISTER'),
(2921, 'system', 2, 'Register several people for this event', '_DIMS_EVT_INSCRIPT_MULTI'),
(2648, 'system', 1, 'Newsletter', '_DIMS_LABEL_NEWSLETTER'),
(2649, 'system', 2, 'Newsletter', '_DIMS_LABEL_NEWSLETTER'),
(2650, 'system', 1, 'Modifier une newsletter', '_DIMS_LABEL_MODIF_NEWSLETTER'),
(2651, 'system', 1, 'Envois rattach&eacute;s', '_DIMS_LABEL_NEWSLETTER_LIST_ARTICLE'),
(2652, 'system', 1, 'Activer cette newsletter', '_DIMS_LABEL_NEWSLETTER_ACTIVE'),
(2653, 'system', 1, 'Liste des newsletter', '_DIMS_LABEL_NEWSLETTER_LIST'),
(2654, 'system', 1, 'Vous ne g&eacute;rez aucune newsletter.', '_DIMS_LABEL_NO_NEWSLETTER'),
(2655, 'system', 1, 'Nbr Newsletter', '_DIMS_LABEL_NEWSLETTER_NB'),
(2656, 'system', 1, 'Nbr inscrits', '_DIMS_LABEL_NEWSLETTER_NBINSC'),
(2657, 'system', 1, 'Nbre demandes inscription', '_DIMS_LABEL_NEWSLETTER_NBDMDINSC'),
(2658, 'system', 1, 'Ajouter un envoi', '_DIMS_LABEL_NEWSLETTER_ADD_ARTICLE'),
(2659, 'system', 1, 'Il n''existe aucun envoi pour cette newsletter.', '_DIMS_LABEL_NEWSLETTER_NO_ARTICLE'),
(2660, 'system', 1, 'Liste des inscrits', '_DIMS_NEWSLETTER_LIST_INSC'),
(2661, 'system', 1, 'Liste des demandes d''inscription', '_DIMS_NEWSLETTER_LIST_DMDINSC'),
(2662, 'system', 1, 'Date d''envoi', '_DIMS_LABEL_SEND_DATE'),
(2663, 'system', 1, 'Pi&egrave;ce jointe', '_DIMS_LABEL_PIECE_JOINTE'),
(2664, 'system', 1, 'Liste des personnes inscrites &agrave; la newsletter', '_DIMS_NEWSLETTER_INSCRITS'),
(2665, 'system', 1, 'Il n''y a aucune inscription &agrave; cette newsletter.', '_DIMS_NEWSLETTER_NO_INSCRITS'),
(2666, 'system', 1, 'Date de d&eacute;sinscription', '_DIMS_LABEL_DATE_UNREGISTRATION'),
(2667, 'system', 1, 'Inscrire ce contact &agrave; la newsletter', '_DIMS_LABEL_NEWSLETTER_RESUBSCRIBE'),
(2668, 'system', 1, 'Vos newsletters', '_DIMS_LABEL_YOUR_NEWSLETTERS'),
(2669, 'system', 1, 'Cliquez ici pour vous d&eacute;sabonner', '_DIMS_LABEL_NEWSLETTER_DESABONNE'),
(2670, 'system', 1, 'Vous n''&ecirc;tes abonn&eacute; &agrave; aucune newsletter.', '_DIMS_LABEL_NO_NEWSLETTER_ABONN'),
(2671, 'system', 2, 'Registration has been cancelled', '_DIMS_LABEL_REGISTRATION_CANCELED'),
(2672, 'system', 2, 'Your registration has been refused', '_DIMS_LABEL_REGISTRATION_REFUSED'),
(2673, 'system', 2, 'Please valide your registration', '_DIMS_LABEL_REGISTRATION_VALIDATION'),
(2674, 'system', 2, 'Save', '_DIMS_SAVE'),
(2675, 'system', 2, 'To update your personal details with us, please enter any changes to fields below.', '_DIMS_LABEL_PERSONNAL_INFOS'),
(2676, 'system', 2, 'Registration is now confirmed', '_DIMS_LABEL_REGISTRATION_VALIDATION'),
(2677, 'system', 2, 'Registration has passed level 1 or/and 2', '_DIMS_LABEL_REGISTRATION_WAIT_FOR_2'),
(2678, 'system', 2, 'Registration is now confirmed', '_DIMS_LABEL_REGISTRATION_VALIDATED'),
(2679, 'system', 1, 'Tous les &eacute;v&egrave;nements', '_DIMS_LABEL_EVT_ALL'),
(2680, 'system', 1, 'Attente validation niveau 2', '_DIMS_LABEL_REGISTRATION_WAIT_LV2'),
(2681, 'system', 1, 'Ajouter une newsletter', '_DIMS_LABEL_ADD_NEWSLETTER'),
(2682, 'system', 1, 'Email 2', '_DIMS_LABEL_EMAIL2'),
(2683, 'system', 1, 'Email 3', '_DIMS_LABEL_EMAIL3'),
(2684, 'system', 2, 'Uploads/attachments', '_EVENT_RESSOURCE_FRONT'),
(2685, 'system', 1, 'Bandeau', '_DIMS_LABEL_BANNER'),
(2686, 'system', 1, 'Apercu du document d''annonce', '_DIMS_EVENT_LABEL_ADERVTISE_DOC_PREVIEW'),
(2687, 'system', 1, 'Se d&eacute;connecter', '_DIMS_LABEL_DISCONNECT'),
(3183, 'system', 2, 'Booklet', '_DIMS_LABEL_BOOKLET'),
(2932, 'system', 1, 'Nous contacter', '_DIMS_CONTACT_US'),
(3346, 'system', 2, 'Install', '_DIMS_LABEL_INSTALLMODULES'),
(3347, 'system', 2, 'Mailing lists', '_DIMS_LABEL_MAILINGLIST'),
(3299, 'system', 2, 'Welcome', '_WELCOME'),
(3300, 'system', 2, 'Workspace settings', '_DIMS_SYSTEM_WORKSPSACE'),
(3301, 'system', 2, 'System settings', '_DIMS_SYSTEM_ADMIN'),
(3302, 'system', 2, 'To do', '_FORM_TASK_TIME_TODO'),
(3303, 'system', 2, 'Task', '_DIMS_TASK'),
(3304, 'system', 2, 'Tasks', '_DIMS_TASKS'),
(3480, 'system', 2, 'No role defined for this workspace', '_DIMS_LABEL_NO_ROLE_DEFINED'),
(3169, 'system', 2, 'Edit newsletter', '_DIMS_LABEL_MODIF_NEWSLETTER'),
(3073, 'system', 1, 'Depuis le ', '_DIMS_LABEL_SINCE'),
(3070, 'system', 2, 'New contact since', '_DIMS_LABEL_NEW_SHEET_SINCE_ONCE'),
(3047, 'system', 1, 'Cr&eacute;er un groupe', '_DIMS_LABEL_GROUP_CREATE'),
(3438, 'system', 2, 'Add sub group', '_DIMS_LABEL_GROUP_ADD'),
(3439, 'system', 2, 'Add sub workspace', '_DIMS_LABEL_WORKSPACE_ADD'),
(3435, 'system', 2, 'Level', '_DIMS_LABEL_LEVEL'),
(3436, 'system', 2, 'Copies of tickets by Mail', '_DIMS_LABEL_TICKETSBYEMAIL'),
(3437, 'system', 2, 'Parents', '_DIMS_LABEL_PARENTS'),
(3429, 'system', 2, 'Login', '_LOGIN'),
(3430, 'system', 2, 'Action', '_LABEL_ACTION'),
(3431, 'system', 2, 'User', '_DIMS_LABEL_USER'),
(3432, 'system', 2, 'Confirm Password', '_DIMS_LABEL_PASSWORD_CONFIRM'),
(3433, 'system', 2, 'Expiration Date', '_DIMS_LABEL_EXPIRATION_DATE'),
(3434, 'system', 2, 'Time zone', '_DIMS_LABEL_TIMEZONE'),
(3390, 'system', 2, 'Installed modules', '_DIMS_LABEL_INSTALLEDMODULES'),
(3389, 'system', 2, 'Value', '_DIMS_LABEL_RULEVALUE'),
(3383, 'system', 2, 'Management Profiles', '_SYSTEM_LABELTAB_PROFILEMANAGEMENT'),
(3384, 'system', 2, 'Add profile', '_SYSTEM_LABELTAB_PROFILEADD'),
(3385, 'system', 2, 'Import', '_SYSTEM_LABELTAB_USERIMPORT'),
(3386, 'system', 2, 'Label', '_DIMS_LABEL_LABEL'),
(3387, 'system', 2, 'Field', '_DIMS_LABEL_RULEFIELD'),
(3376, 'system', 2, 'Restriction SSL', '_DIMS_LABEL_SSLACCESS'),
(3377, 'system', 2, 'Frontoffice access', '_DIMS_LABEL_WEBDOMAIN'),
(3370, 'system', 2, 'List of active domains', '_SYSTEM_DOMAINSLIST'),
(3371, 'system', 2, 'Selected module', '_SYSTEM_MODULESELECTED'),
(3372, 'system', 2, 'Module Settings', '_SYSTEM_MODULEPARAM'),
(3373, 'system', 2, 'No setting for this module', '_DIMS_LABEL_NOMODULEPARAM'),
(3374, 'system', 2, 'Domain name', '_DIMS_LABEL_DOMAIN'),
(2731, 'system', 1, 'Étape 1 : Importer le fichier', '_LABEL_IMPORT_TIER_STEP1'),
(3205, 'system', 2, 'To', '_DIMS_LABEL_MAIL_TO'),
(3368, 'system', 2, 'Enable profile', '_DIMS_LABEL_PROFIL'),
(3360, 'system', 2, 'Contacts Management', '_SYSTEM_MANAGE_CONTACT'),
(3361, 'system', 2, 'Contacts', '_DIMS_LABEL_CONTACTS'),
(3362, 'system', 2, 'Restrict access to https (SSL)', '_DIMS_LABEL_GROUP_SSL'),
(3363, 'system', 2, 'Add domain', '_DIMS_LABEL_DOMAIN_ADD'),
(3364, 'system', 2, 'Domains for backoffice access of', '_DIMS_LABEL_BACKOFFICE_DOMAIN_LIST'),
(3365, 'system', 2, 'Domains for frontoffice access of', '_DIMS_LABEL_FRONTOFFICE_DOMAIN_LIST'),
(3366, 'system', 2, 'Enable Search tab', '_DIMS_LABEL_SEARCH'),
(3329, 'system', 2, 'Contributors', '_DIMS_FAQ_CONTRIB'),
(3330, 'system', 2, 'Can you confirm that sending this question by email?', '_DIMS_FAQ_CONFIRM_SENDMAIL'),
(3331, 'system', 2, 'Message sent', '_FAQ_SEND_MESSAGE'),
(3332, 'system', 2, 'Management Module «LABEL»', '_DIMS_LABEL_PAGE_TITLE'),
(3333, 'system', 2, 'pages founds', '_ARTICLE_LABEL_PAGESFOUND'),
(3334, 'system', 2, 'page found', '_ARTICLE_LABEL_PAGEFOUND'),
(3335, 'system', 2, 'Calendar', '_AGENDA_LABELTAB_LIST'),
(3336, 'system', 2, 'Organisers', '_AGENDA_LABELTAB_ORGANIZERS'),
(3337, 'system', 2, 'Add a category', '_AGENDA_CAT_CREATE'),
(3294, 'system', 2, 'If you skip this step, the new contact will not be attached to the mission.', '_IMPORT_MISSION_WARN_STEP3'),
(3311, 'system', 2, 'Delete entry', '_DIMS_LABEL_SUPPR_SEARCH_VAL'),
(3312, 'system', 2, 'Number of versions stored per page', '_WEBSITE_WATCHER_LABEL_SIZELABEL'),
(3313, 'system', 1, 'Traduction', '_DIMS_LABEL_TRADUCTION'),
(3314, 'system', 2, 'Translation', '_DIMS_LABEL_TRADUCTION'),
(3315, 'system', 1, 'Constante', '_CONSTANT'),
(3316, 'system', 2, 'Constant', '_CONSTANT'),
(3419, 'system', 2, 'Setup', '_DIMS_LABEL_INSTALL'),
(3417, 'system', 2, 'Transversal', '_DIMS_LABEL_TRANSVERSE'),
(3418, 'system', 2, 'Apply rules to sub-groups', '_SYSTEM_APPLYHERITAGE'),
(3397, 'system', 2, 'Update report', '_DIMS_LABEL_UPDATEREPORT'),
(3381, 'system', 2, 'Assigning roles', '_SYSTEM_LABELTAB_ROLEASSIGNMENT'),
(3353, 'system', 2, 'Roles', '_SYSTEM_LABELICON_ROLES'),
(3354, 'system', 2, 'Templates for this workspace', '_DIMS_LABEL_TEMPLATEWORKSPACE_LIST'),
(3355, 'system', 2, 'Back to schedule', '_DIMS_PLANNING_RETURN'),
(3343, 'system', 2, 'event(s) found(s)', '_AGENDA_LABEL_NEWSFOUND'),
(2942, 'system', 2, 'September', '_SEPTEMBER'),
(2944, 'system', 2, 'November', '_NOVEMBER'),
(2945, 'system', 1, 'Pi&egrave;ces jointes', '_EVENT_RESSOURCE_FRONT'),
(2946, 'system', 2, 'December', '_DECEMBER'),
(2936, 'system', 2, 'March', '_MARCH'),
(2937, 'system', 2, 'April', '_APRIL'),
(2938, 'system', 2, 'May', '_MAY'),
(3415, 'system', 2, 'View', '_DIMS_LABEL_VIEWMODE'),
(3414, 'system', 2, 'Assign', '_DIMS_LABEL_ASSIGN'),
(3034, 'system', 1, 'Fermeture des inscriptions', '_DIMS_EVT_DATE_CLOSE_REGISTER'),
(2922, 's', 2, 'Suscribe for this event', '_DIMS_EVT_INSCRIPT'),
(2923, 'system', 2, 'In order to confirm your registration, please type the following symbols into the box.', '_DIMS_TEXT_CAPTCHA'),
(3344, 'system', 2, 'event found', '_AGENDA_LABEL_NEWFOUND'),
(3345, 'system', 2, 'No events found', '_AGENDA_LABEL_NO_NEWFOUND'),
(3291, 'system', 2, 'of your business is already present on the site.', '_IMPORT_TIERS_ALREADY_EXISTS'),
(3297, 'system', 2, 'Contents', '_DIMS_LABEL_CONTENT'),
(3298, 'system', 1, 'Bonjour', '_WELCOME'),
(2947, 'system', 2, 'Registration has passed level 1 or/and 2', '_DIMS_LABEL_REGISTRATION_WAIT_LV2'),
(3209, 'system', 2, 'More options', '_DIMS_MORE_OPTIONS'),
(3210, 'system', 2, 'Reception', '_DIMS_LABEL_RECEPTION'),
(3211, 'system', 2, 'Document Reference', '_DIMS_LABEL_DOC_REFERENCE'),
(3159, 'system', 2, 'Actions', '_DIMS_ACTIONS'),
(3170, 'system', 2, 'Add newsletter', '_DIMS_LABEL_ADD_NEWSLETTER'),
(3163, 'system', 2, 'Adding a step', '_DIMS_EVT_STEP_ADD'),
(3069, 'system', 1, 'Nouvelle fiche', '_DIMS_LABEL_NEW_SHEET_SINCE_ONCE'),
(3046, 'system', 1, 'Groupes de contacts', '_DIMS_LABEL_CONTACT_GOUPS'),
(3045, 'system', 2, 'By', '_DIMS_LABEL_FROM'),
(3212, 'system', 2, 'Number of emails retrieved', '_DIMS_LABEL_NB_MAIL_RETRIEVE'),
(3196, 'system', 2, 'From', '_FROM'),
(3147, 'system', 2, 'To', '_DIMS_LABEL_MAIL_TO'),
(3213, 'system', 2, 'Copy to', '_DIMS_LABEL_MAIL_CC'),
(3197, 'system', 2, 'Subject', '_SUBJECT'),
(3195, 'system', 2, 'Content', '_CONTENT'),
(3461, 'system', 2, 'Clone workspace', '_DIMS_LABEL_CREATE_CLONE_WORKSPACE'),
(3428, 'system', 2, 'Ans.', '_DIMS_LABEL_TICKET_NBREP'),
(3180, 'system', 2, 'No email', '_DIMS_LABEL_MAIL_NONE'),
(3457, 'system', 2, 'Clone this group', '_DIMS_LABEL_CREATE_CLONE'),
(3456, 'system', 2, 'List of Backoffice domains', '_DIMS_LABEL_GROUP_ADMINDOMAINLIST'),
(3455, 'system', 2, 'List of Frontoffice domains', '_DIMS_LABEL_GROUP_WEBDOMAINLIST'),
(3391, 'system', 2, 'New versions', '_DIMS_LABEL_NEWMODULEVERSIONS'),
(3388, 'system', 2, 'Operator', '_DIMS_LABEL_RULEOPERATOR'),
(3379, 'system', 2, 'Attach a group', '_SYSTEM_LABELTAB_GROUPATTACH'),
(3380, 'system', 2, 'Role management', '_SYSTEM_LABELTAB_ROLEMANAGEMENT'),
(3382, 'system', 2, 'Assigning groups', '_SYSTEM_LABELTAB_MULTIPLEROLEASSIGNMENT'),
(3190, 'system', 2, 'Import a registered list', '_DIMS_LABEL_IMPORT_INSCRIPT'),
(3375, 'system', 2, 'Domain already exists', '_DIMS_LABEL_DOMAIN_ALREADYEXISTS'),
(3369, 'system', 2, 'Enable Comments tab', '_DIMS_LABEL_ANNOT'),
(3454, 'system', 2, 'Default profile', '_DIMS_LABEL_USER_PROFILE'),
(3453, 'system', 2, 'Enable Frontoffice', '_DIMS_LABEL_GROUP_WEB'),
(3189, 'system', 2, 'Import companies', '_LABEL_IMPORT_ENTREPRISE'),
(3450, 'system', 2, 'MAC authorized address ', '_DIMS_LABEL_GROUP_ALLOWEDMAC'),
(3146, 'system', 2, 'Step 3: Comparison of Business', '_LABEL_IMPORT_TIER_STEP3'),
(3367, 'system', 2, 'Enable Tickets', '_DIMS_LABEL_TICKET'),
(3317, 'system', 1, 'Francais', '_DIMS_LABEL_FRENCH'),
(3318, 'system', 2, 'French', '_DIMS_LABEL_FRENCH'),
(3319, 'system', 2, 'Login', '_WEBSITE_WATCHER_LABEL_LOGINLABEL');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(3320, 'system', 2, 'p. found(s)', '_WEBSITE_WATCHER_LABEL_SRCFOUND'),
(3321, 'system', 2, 'p. found', '_WEBSITE_WATCHER_LABEL_SRC_ONEFOUND'),
(3322, 'system', 2, 'Domain', '_WEBSITE_WATCHER_LABEL_DOMAIN'),
(3323, 'system', 2, 'Source(s)', '_WEBSITE_WATCHER_LABEL_NBSRC'),
(3324, 'system', 2, 'List of pages', '_WEBSITE_WATCHER_LABEL_LIST_PAGE'),
(3325, 'system', 2, 'History of versions', '_WEBSITE_WATCHER_LABEL_LIST_VERSION'),
(3326, 'system', 2, 'Waiting for reply', '_DIMS_FAQ_WAIT'),
(3327, 'system', 2, 'Published', '_DIMS_FAQ_PUBLISHED'),
(3328, 'system', 2, 'Not published', '_DIMS_FAQ_NOTPUBLISHED'),
(3186, 'system', 2, 'This enterprise is the same as import enterprise', '_IMPORT_MY_TIER_IS_SIMILAR'),
(3359, 'system', 2, 'Enabled contacts', '_DIMS_LABEL_ACTIVE_CONTACT'),
(3358, 'system', 2, 'Enabled schedule', '_DIMS_LABEL_PLANNING'),
(3356, 'system', 2, 'Enable project management', '_DIMS_LABEL_PROJECT'),
(3357, 'system', 2, 'Enabled messages', '_DIMS_LABEL_ACTIVE_TICKETS'),
(3217, 'system', 2, 'New company', 'Nouvelle entreprise'),
(3351, 'system', 2, 'Profiles', '_SYSTEM_LABELICON_PROFILES'),
(3295, 'system', 2, 'of our companies have been\r\nignored', '_IMPORT_COUNT_OF_IGNORED_TIERS'),
(3349, 'system', 2, 'Domains', '_SYSTEM_LABELICON_DOMAINS'),
(3350, 'system', 2, 'Indexing process', '_SYSTEM_LABELICON_INDEX'),
(3348, 'system', 2, 'Settings', '_SYSTEM_LABELICON_PARAMS'),
(3218, 'system', 2, 'Extract missions', '_DIMS_IMPORT_MISSIONS_EXTRACT'),
(3420, 'system', 2, 'Update', '_DIMS_LABEL_UPDATE'),
(3219, 'system', 2, 'Import contacts', '_IMPORT_CONTACTS'),
(3174, 'system', 2, 'Ascendant', '_LABEL_VIEWMODE_ASC'),
(3175, 'system', 2, 'CAUTION: More than 100 results, please\r\nuse the export file for viewing all datas.', '_DIMS_LABEL_TOO_MUCH_RES_SEARCH'),
(3220, 'system', 2, 'There are no known contact', '_DIMS_IMPORT_MISSION_NO_INKNOW'),
(3221, 'system', 2, 'Contact (s) unknown (s) remaining', '_DIMS_IMPORT_MISSION_STILL_UNKNOW'),
(3416, 'system', 2, 'Role', '_DIMS_LABEL_ROLECHOICE'),
(3396, 'system', 2, 'Report Setup', '_DIMS_LABEL_INSTALLREPORT'),
(3284, 'system', 2, 'If you skip this step, the new contact does\r\nnot be attached to the mission', '_IMPORT_MISSION_WARN_STEP3'),
(3222, 'system', 2, 'Attach additional users', '_IMPORT_TIERS_ATTACH_MORE'),
(3223, 'system', 2, 'Registration', '_DIMS_REGISTRATION'),
(3188, 'system', 2, 'Contact ignored during the import', '_IMPORT_MISSION_IGNORED_CONTACT'),
(3342, 'system', 2, 'Name', '_AGENDA_LABEL_LABEL'),
(2902, 'system', 2, 'Subscription', '_DIMS_FRONT_SUBSCRIPTION'),
(2903, 'system', 2, 'Please enter your details.', '_DIMS_FRONT_TEXT_SUBSCRIPTION'),
(2931, 'system', 2, 'Login/Password not correct. Please note that you can access to your access area once you have registered for an event.', '_DIMS_FRONT_TEXT_NO_LOGGIN'),
(2939, 'system', 2, 'June', '_JUNE'),
(2940, 'system', 2, 'July', '_JULY'),
(2941, 'system', 2, 'August', '_AUGUST'),
(2934, 'system', 2, 'January', '_JANUARY'),
(2935, 'system', 2, 'February', '_FEBRUARY'),
(3224, 'system', 2, 'Links', '_DIMS_LABEL_LINKS'),
(3225, 'system', 2, 'Organiser', '_DIMS_LABEL_ORGANIZER'),
(3228, 'system', 2, 'Protocole', '_DIMS_LABEL_PROTOCOL'),
(2860, 'system', 1, 'Date d''ouverture des inscriptions', '_DIMS_EVT_DATE_OPEN_REGISTER'),
(2861, 'system', 1, 'Les inscriptions à cet événement ne\r\nsont pas encore ouvertes.', '_DIMS_FRONT_NOT_OPEN'),
(2862, 'system', 1, 'Aucune newsletter ne correspond à votre demande ou celle-ci à été desactivée.', '_DIMS_FRONT_NL_TEXT_NO_NEWSLETTER'),
(2863, 'system', 1, 'To subscribe to our newsletters, please fill in the form below', '_DIMS_FRONT_NL_TEXT_INSCRIPTION'),
(2864, 'system', 1, 'Vous avez oublié de renseigner le champ ''nom''', '_DIMS_FORM_MISSING_FIRSTNAME'),
(2865, 'system', 1, 'Vous avez oublié de renseigner le champ\r\n''prénom''', '_DIMS_FORM_MISSING_LASTNAME'),
(2866, 'system', 1, 'Vous avez oublié de renseigner le champ\r\n''e-mail''', '_DIMS_FORM_MISSING_EMAIL'),
(2867, 'system', 1, 'Date d''inscription', '_DIMS_LABEL_DATE_REGISTRATION'),
(2868, 'system', 1, 'Refuser l''inscription', '_DIMS_LABEL_REFUSED_REGISTRATION'),
(2869, 'system', 1, 'Aucune newsletter correspondante', '_DIMS_LABEL_NEWSLETTER_NONE'),
(2870, 'system', 1, 'Pour valider l''inscription vous devez rattacher cette\r\ninscription à une fiche contact existante. Si aucun contact ne ressemble à cette inscription, vous pourrez créer un contact à partir de\r\ncelle-ci.', '_DIMS_NEWSLETTER_TEXT_ATTACH'),
(2871, 'system', 1, 'Créer un nouveau contact à partir de l''inscription', '_DIMS_NEWSLETTER_NEW_CONTACT'),
(2872, 'system', 2, 'Your documents are being treated', '_DIMS_DOC_VALIDATION_IN_PROGRESS'),
(2873, 'system', 2, 'Confirmed on', '_DIMS_LABEL_VALIDATE_ON'),
(2874, 'system', 2, 'Document confirmed on', '_DIMS_LABEL_VALIDATED_DOC'),
(2875, 'system', 2, 'Document received', '_DIMS_LABEL_RECEIVED_DOC'),
(2876, 'system', 2, 'on', '_DIMS_LABEL_AT'),
(2877, 'system', 2, 'To finalise your registration, kindly complete the\r\nattached documents and return them to our offices as soon as possible .<br/>\r\nOnce event registrations are closed you will no longer be able to access this\r\npage. In order to get your registration through please make sure that you have\r\ncompleted all the necessary steps. Thank you! <br/>', '_DIMS_TEXT_REGISTRATION_FORM_2'),
(2878, 'system', 2, 'your registration is being treated', '_DIMS_LABEL_REGISTRATION_CURRENT'),
(2943, 'system', 2, 'October', '_OCTOBER'),
(3182, 'system', 2, 'Mood picture', '_DIMS_LABEL_BANNER'),
(3171, 'system', 2, 'Alerting changes', '_DIMS_LABEL_ALERT_MODIF'),
(3309, 'system', 2, 'connected user', '_DIMS_CONNECTED_USER'),
(2883, 'system', 1, 'Brochure', '_DIMS_LABEL_BOOKLET'),
(2933, 'system', 2, 'Contact us', '_DIMS_CONTACT_US'),
(2885, 'system', 2, 'Step', '_DIMS_LABEL_STEP'),
(2886, 'system', 1, 'Champs obligatoires', '_DIMS_LABEL_MANDATORY_FIELDS'),
(2887, 'system', 2, 'Mandatory fields', '_DIMS_LABEL_MANDATORY_FIELDS'),
(2888, 'system', 1, 'Pour valider votre inscription, veuillez écrire\r\nles caractères de l''image dans le champs suivant', '_DIMS_TEXT_CAPTCHA'),
(2889, 'system', 1, 'Le code de confirmation que vous avez entré ne\r\ncorrespond pas à celui de l''image', '_DIMS_FORM_BAD_CAPTCHA'),
(2890, 'system', 2, 'The confirmation code you have entered is not correct.<br/>Please try again.', '_DIMS_FORM_BAD_CAPTCHA'),
(2891, 'system', 2, 'Name', '_DIMS_LABEL_NAME'),
(2894, 'system', 2, 'Nationality', '_DIMS_LABEL_NATIONALITY'),
(2893, 'system', 2, 'First name', '_DIMS_LABEL_FIRSTNAME'),
(2895, 'system', 2, 'Address', '_DIMS_LABEL_ADDRESS'),
(2896, 'system', 2, 'City', '_DIMS_LABEL_CITY'),
(2897, 'system', 2, 'ZIP code', '_DIMS_LABEL_CP'),
(2898, 'system', 2, 'Phone', '_PHONE'),
(2899, 'system', 2, 'Country', '_DIMS_LABEL_COUNTRY'),
(2900, 'system', 2, 'Company', '_DIMS_LABEL_ENTERPRISES'),
(2901, 'system', 2, 'Title', '_DIMS_LABEL_CONT_FUNCTION'),
(2904, 'system', 1, 'galerie d''image', '_DIMS_EVENT_LABEL_IMAGE_GALLERY'),
(2905, 'system', 1, 'Newsletter active', '_DIMS_NEWSLETTER_ACTIVE'),
(2906, 'system', 1, 'Newsletter inactive', '_DIMS_NEWSLETTER_INACTIVE'),
(2907, 'system', 1, 'Editer l''&eacute;v&eacute;nement', '_DIMS_LABEL_EDIT_EVT'),
(2908, 'system', 1, 'Editer', '_DIMS_LABEL_EDIT'),
(2909, 'system', 2, 'Registered', '_DIMS_LABEL_REGISTRATION_WAIT'),
(2913, 'system', 1, 'go', '_DIMS_LABEL_GO'),
(2914, 'system', 1, 'Afin d''obtenir un nouveau mot de passe, veuillez renseigner votre adresse e-mail, un lien vous sera envoy&eacute; pour poursuivre la proc&eacute;dure', '_DIMS_TEXT_FORGOT_PASSWORD'),
(2911, 'system', 1, 'I have forgotten my password', '_DIMS_LABEL_FORGOTTEN_PASSWORD'),
(2912, 'system', 2, 'I have forgotten my password', '_DIMS_LABEL_FORGOTTEN_PASSWORD'),
(2915, 'system', 2, 'To receive a new password, please enter your e-mail and you will shortly receive a link with further instructions.', '_DIMS_TEXT_FORGOT_PASSWORD'),
(2916, 'system', 1, 'Merci', '_DIMS_TEXT_FORGOT_PASSWORD1'),
(2917, 'system', 1, 'Le renouvellement de votre mot de passe &agrave; bien &eacute;t&eacute; effectu&eacute;, vous allez le recevoir par email dans les plus brefs d&eacute;lais.', '_DIMS_TEXT_FORGOT_PASSWORD2'),
(2918, 'system', 2, 'Person', '_DIMS_EVT_INSCRIPT_PERSON'),
(2919, 'system', 2, 'Person', '_DIMS_EVT_INSCRIPT_PERSON'),
(2920, 'system', 2, 'E-mail', '_DIMS_LABEL_EMAIL'),
(2924, 'system', 2, 'Registration for this event are not open', '_DIMS_FRONT_NOT_OPEN'),
(2925, 'system', 2, 'Thank you for your registration! <br/> You will shortly receive an email.', '_DIMS_TEXT_REGISTRATION_WAIT_EMAIL'),
(2926, 'system', 2, 'Please fill in "first name"', '_DIMS_FORM_MISSING_FIRSTNAME'),
(2927, 'system', 2, 'Please fill in "Name"', '_DIMS_FORM_MISSING_LASTNAME'),
(2928, 'system', 2, 'Please fill in "e-mail"', '_DIMS_FORM_MISSING_EMAIL'),
(2929, 'system', 2, 'To subscribe to our newsletters, please fill in the form below', '_DIMS_FRONT_NL_TEXT_INSCRIPTION'),
(2930, 'system', 2, 'No letter available regardind to you request', '_DIMS_FRONT_NL_TEXT_NO_NEWSLETTER'),
(3166, 'system', 2, 'Add event', '_DIMS_LABEL_ADD_EVENT'),
(2949, 'system', 2, 'Registration has passed level 1 or/and 2', '_DIMS_LABEL_REGISTRATION_WAIT_LV2'),
(2950, 'system', 1, 'Ajouter un &eacute;v&eacute;nement', '_DIMS_LABEL_ADD_EVENT'),
(2951, 'system', 2, 'Matchmaking', '_DIMS_LABEL_EVENT_MATCHMAKING'),
(2952, 'system', 1, 'Aucun document n''est rattach&eacute; &agrave; cette &eacute;tape.', '_DIMS_LABEL_EVENT_STEP_NODOC'),
(2953, 'system', 1, 'La taille de l''image est trop importante (20Mo max)', '_DIMS_LABEL_ERROR_TOLARGE_IMG'),
(2954, 'system', 1, 'La taille du fichier est trop importante', '_DIMS_LABEL_ERROR_TOLARGE_FILE'),
(2955, 'system', 1, 'ATTENTION : Ne pas coller un texte Word directement dans cette zone d''&eacute;dition, veuillez tout d''abord le coller dans un bloc note.', '_DIMS_LABEL_DONT_USE_WORD'),
(2956, 'system', 1, 'Mobile', '_DIMS_LABEL_MOBILE'),
(2957, 'system', 2, 'Welcome to your personal login area!<br/><br/>Please find below the summary of your events :', '_DIMS_FO_EVT_LIST'),
(2958, 'system', 2, 'Home', '_DIMS_RETURN_TO_HOME'),
(2959, 'system', 2, 'Logout', '_DIMS_LABEL_DISCONNECT'),
(3399, 'system', 2, 'Registration Numbers', '_DIMS_LABEL_NBATTACH'),
(2962, 'system', 2, 'Title', '_DIMS_LABEL_FUNCTION'),
(2963, 'system', 2, 'Company', '_DIMS_LABEL_COMPANY'),
(2964, 'system', 2, 'Available newsletters', '_DIMS_LABEL_NEWSLETTER_AVAILABLE'),
(2965, 'system', 1, 'Modifier la mailing liste', '_DIMS_LABEL_MODIF_MAILINGLIST'),
(2966, 'system', 2, 'Mobile', '_DIMS_LABEL_MOBILE'),
(2967, 'system', 1, 'Liste rattach&eacute;e &agrave;', '_DIMS_LABEL_NEWSLETTER_LIST_LINKEDTO'),
(2968, 'system', 1, 'Ajouter un email', '_DIMS_LABEL_MAILING_ADD_EMAIL'),
(2969, 'system', 1, 'Liste des emails rattach&eacute;s', '_DIMS_LABEL_MAILING_EMAIL_LIST'),
(2970, 'system', 1, 'Il n''y a aucun email dans cette liste.', '_DIMS_LABEL_MAILING_NO_EMAIL'),
(2971, 'system', 1, 'Mailing lists rattach&eacute;es &agrave; cette newsletter', '_DIMS_LABEL_LIST_MAILING_LINKED'),
(2972, 'system', 1, 'Contacts rattach&eacute;s &agrave; cette newsletter', '_DIMS_LABEL_NEWSLETTER_CT_LINKED'),
(2973, 'system', 1, 'Rattacher une mailing list', '_DIMS_LABEL_MAILING_TO_ATTACH'),
(2974, 'system', 2, 'Please fill in "Title"', '_DIMS_FORM_MISSING_TITLE'),
(2975, 'system', 2, 'Please fill in "Company"', '_DIMS_FORM_MISSING_COMPANY'),
(2976, 'system', 2, 'Please fill in "Country"', '_DIMS_FORM_MISSING_COUNTRY'),
(2977, 'system', 2, 'Please fill in "Phone"', '_DIMS_FORM_MISSING_TEL'),
(2978, 'system', 2, 'Please fill in "Address"', '_DIMS_FORM_MISSING_ADDRESS'),
(2979, 'system', 2, 'Please fill in "City"', '_DIMS_FORM_MISSING_CITY'),
(2980, 'system', 2, 'Please fill in "ZIP Code"', '_DIMS_FORM_MISSING_CP'),
(2981, 'system', 2, 'Please select at least one newsletter.', '_DIMS_FORM_MISSING_NEWS'),
(2982, 'system', 1, 'Gestion des listes d''emails', '_DIMS_NEWSLETTER_GESTION_MAILING'),
(2983, 'system', 1, 'Vos mailing listes', '_DIMS_NEWSLETTER_YOUR_MAILING_LIST'),
(2984, 'system', 1, 'Ajouter une liste', '_DIMS_NEWSLETTER_ADD_LIST_MAILING'),
(2985, 'system', 1, 'Rattacher cette liste &agrave; une newsletter', '_DIMS_LABEL_LINK_NEWSLETTER_MAILING'),
(2996, 'system', 1, 'Envoyer votre document', '_DIMS_LABEL_UPLOAD_DOCUMENT'),
(2995, 'system', 2, 'Back', '_DIMS_BACK'),
(2986, 'system', 2, 'Three', '_DIMS_LABEL_UNIT_3'),
(2987, 'system', 2, 'Four', '_DIMS_LABEL_UNIT_4'),
(2988, 'system', 2, 'One', '_DIMS_LABEL_UNIT_1'),
(2989, 'system', 2, 'Two', '_DIMS_LABEL_UNIT_2'),
(2990, 'sytem', 1, 'Signature', '_DIMS_LABEL_SIGNATURE'),
(2991, 'system', 2, 'Register', '_DIMS_LABEL_REGISTER'),
(2992, 'system', 1, 'Email sans r&eacute;ponse', '_DIMS_LABEL_EMAIL_NOREPLY'),
(2993, 'system', 1, 'Votre demande a d&eacute;ja &eacute;t&eacute; effectu&eacute;e', '_DIMS_LABEL_REQUEST_ALREADY_TREATED'),
(2994, 'system', 2, 'Your request has already been treated', '_DIMS_LABEL_REQUEST_ALREADY_TREATED'),
(2997, 'system', 2, 'Upload your signed document', '_DIMS_LABEL_UPLOAD_DOCUMENT'),
(2998, 'system', 1, 'Envoyer votre document complet&eacute;', '_DIMS_LABEL_UPLOAD_SIGNED_DOCUMENT'),
(2999, 'system', 2, 'Upload your signed document', '_DIMS_LABEL_UPLOAD_SIGNED_DOCUMENT'),
(3280, 'system', 2, 'Send your document', '_DIMS_LABEL_UPLOAD_DOCUMENT'),
(3001, 'system', 2, 'Upload your document', '_DIMS_LABEL_UPLOAD_DOCUMENT'),
(3002, 'system', 2, 'Close', '_DIMS_CLOSE'),
(3003, 'system', 2, 'Upload your signed document', '_DIMS_LABEL_DOCUMENT_SEND'),
(3004, 'system', 1, 'Veuillez saisir votre login et mot de passe', '_DIMS_FRONT_TEXT_LOGGIN'),
(3005, 'system', 2, 'Please fill your login and password', '_DIMS_FRONT_TEXT_LOGGIN'),
(3008, 'system', 1, 'Champ g&eacute;n&eacute;rique', '_DIMS_LABEL_GENFIELD'),
(3009, 'system', 2, 'Generic field', '_DIMS_LABEL_GENFIELD'),
(3010, 'system', 2, 'Mandatory', '_FORMS_OBLIGATORY'),
(3013, 'system', 1, 'T&eacute;l&eacute;phone 2', '_DIMS_LABEL_PHONE2'),
(3014, 'system', 2, 'Phone 2', '_DIMS_LABEL_PHONE2'),
(3015, 'system', 2, 'Title', '_DIMS_LABEL_TITLE'),
(3016, 'system', 2, 'Author', '_DIMS_LABEL_AUTHOR'),
(3017, 'system', 1, 'Non disponible', '_DIMS_LABEL_NOT_AVAILABLE'),
(3018, 'system', 2, 'Not available', '_DIMS_LABEL_NOT_AVAILABLE'),
(3019, 'system', 1, 'Prd&eacute;d&eacute;fini', '_DIMS_LABEL_PREDEFINED'),
(3020, 'system', 2, 'Predefined', '_DIMS_LABEL_PREDEFINED'),
(3021, 'system', 1, 'Aucun partage', '_DIMS_NO_SHARE'),
(3022, 'system', 2, 'No sharing', '_DIMS_NO_SHARE'),
(3023, 'system', 1, 'Priv&eacute;', '_PRIVATE'),
(3024, 'system', 2, 'Private', '_DIMS_LABEL_PRIVATE'),
(3026, 'system', 2, 'Public', '_DIMS_LABEL_PUBLIC'),
(3028, 'system', 2, 'Tags', '_DIMS_LABEL_TAGS'),
(3029, 'system', 2, 'Options', '_DIMS_OPTIONS'),
(3030, 'system', 1, 'Partageable', '_DIMS_LABEL_CAN_SHARE'),
(3031, 'system', 2, 'Can be shared', '_DIMS_LABEL_CAN_SHARE'),
(3032, 'system', 2, 'Matchmaking', '_DIMS_LABEL_EVENT_MATCHMAKING'),
(3033, 'system', 1, 'Mise en relation', '_DIMS_LABEL_EVENT_MATCHMAKING'),
(3036, 'system', 2, 'Language', '_DIMS_LABEL_LANG'),
(3037, 'system', 1, 'D&eacute;sactiver', '_DIMS_LABEL_DISABLED'),
(3038, 'system', 2, 'Disable', '_DIMS_LABEL_DISABLED'),
(3306, 'system', 2, 'From', '_SENDER'),
(3040, 'system', 2, 'Active', '_DIMS_LABEL_ACTIVE'),
(3041, 'system', 1, 'Voir', '_DIMS_LABEL_VIEW'),
(3042, 'system', 2, 'View', '_DIMS_LABEL_VIEW'),
(3043, 'system', 2, 'Origin', '_DIMS_LABEL_ORIGIN'),
(3048, 'system', 1, 'Aucun groupe attach&eacute;', '_DIMS_LABEL_NO_GROUP_ATTACHED'),
(3049, 'system', 1, 'Ajouter un groupe', '_DIMS_LABEL_ADD_CT_GROUP'),
(3050, 'system', 1, 'Ajouter un contact &agrave; un groupe', '_DIMS_LABEL_LINK_CT_GROUP'),
(3051, 'system', 1, 'Liste des groupes', '_DIMS_LABEL_GROUP_LIST'),
(3052, 'system', 1, 'Intitul&eacute; du groupe', '_DIMS_LABEL_CTGROUP_TITLE'),
(3053, 'system', 1, 'Vue du groupe', '_DIMS_LABEL_CTGROUP_VIEW'),
(3054, 'system', 1, 'Liste de groupes priv&eacute;s', '_DIMS_LABEL_LIST_PRIVATE_CT'),
(3055, 'system', 1, 'Liste des groupes de l''espace', '_DIMS_LABEL_LIST_WORKSACE_CT'),
(3056, 'system', 1, 'Similarit&eacute;s des entreprises', '_DIMS_LABEL_SIMILAR_TIERS'),
(3057, 'system', 2, 'Similarit&eacute;s des entreprises', '_DIMS_LABEL_SIMILAR_TIERS'),
(3058, 'system', 1, 'Groupe de contacts', '_DIMS_LABEL_CT_GROUP'),
(3059, 'system', 2, 'Contacts groups', '_DIMS_LABEL_CT_GROUP'),
(3060, 'system', 1, 'Code de conduite', '_DIMS_LABEL_CODE_OF_CONDUCT'),
(3061, 'system', 2, 'Code of conduct', '_DIMS_LABEL_CODE_OF_CONDUCT'),
(3062, 'system', 1, 'Galerie', '_DIMS_EVENT_LABEL_GALLERY'),
(3063, 'system', 2, 'Gallery', '_DIMS_EVENT_LABEL_GALLERY'),
(3064, 'system', 1, 'J''accepte ces conditions', '_DIMS_LABEL_ACCEPT_CONDITION'),
(3065, 'system', 2, 'I accept the terms & conditions', '_DIMS_LABEL_ACCEPT_CONDITION'),
(3066, 'system', 1, 'Etablissements', '_TRANSF_LABEL_ETAB'),
(3274, 'system', 2, 'Private', '_PERSO'),
(3068, 'system', 1, 'Module transfrontalier', '_TRANSF_LABEL_MODULE_NAME'),
(3071, 'system', 1, 'Pas de nouvelle fiche', '_DIMS_LABEL_NO_NEW_SHEET'),
(3072, 'system', 2, 'No new contact', '_DIMS_LABEL_NO_NEW_SHEET'),
(3074, 'system', 2, 'Since', '_DIMS_LABEL_SINCE'),
(3075, 'system', 1, 'Pas de fiche modifi&eacute;e', '_DIMS_LABEL_NO_MODIFY_SHEET'),
(3076, 'system', 2, 'No modify entry', '_DIMS_LABEL_NO_MODIFY_SHEET'),
(3077, 'system', 1, 'Fiche modifi&eacute;', '_DIMS_LABEL_MODIFY_SHEET_SINCE_ONCE'),
(3078, 'system', 2, 'modify entry', '_DIMS_LABEL_MODIFY_SHEET_SINCE_ONCE'),
(3079, 'system', 1, 'fiches modifi&eacute;es', '_DIMS_LABEL_MODIFY_SHEET_SINCE'),
(3080, 'system', 2, 'modify entries', '_DIMS_LABEL_MODIFY_SHEET_SINCE_ONCE'),
(3081, 'system', 2, 'inserted entries', '_DIMS_LABEL_VEILLE_FICH'),
(3082, 'system', 1, 'Fiche surveill&eacute;e', '_DIMS_LABEL_VEILLE_FICH_ONCE'),
(3083, 'system', 2, 'inserted entry', '_DIMS_LABEL_VEILLE_FICH_ONCE'),
(3084, 'system', 1, 'Aucune fiche en veille', '_DIMS_LABEL_VEILLE_NO_FICH'),
(3085, 'system', 2, 'No entry in survey', '_DIMS_LABEL_VEILLE_NO_FICH'),
(3167, 'system', 2, 'Add Contact', '_ADD_CT'),
(3087, 'system', 1, 'Issue de la phase', '_DIMS_LABEL_LINK_PARENT_PHASE'),
(3088, 'system', 1, 'Issue de la phase ou t&acirc;che', '_DIMS_LABEL_LINK_PARENT_TASK'),
(3089, 'system', 1, 'Contact rattach&eacute;s', '_DIMS_LABEL_PROJ_PERS_ATTACHED'),
(3090, 'system', 1, 'Afficher la liste des participants aux phases du projet', '_DIMS_LABEL_PROJ_VIEW_PHASEP'),
(3091, 'system', 1, 'Afficher la liste des participants aux t&acirc;ches du projet', '_DIMS_LABEL_PROJ_VIEW_PHASET'),
(3092, 'system', 1, 'Faire une demande de participation', '_DIMS_LABEL_PROJ_DMD_PARTICIPATE'),
(3093, 'system', 1, 'Demande d''inscription pour un projet', '_DIMS_TITLE_PROJ_TICKET_DMD_INSC'),
(3094, 'system', 1, 'Aucun projet', '_DIMS_LABEL_NO_PROJECT'),
(3095, 'system', 2, 'No project', '_DIMS_LABEL_NO_PROJECT'),
(3096, 'system', 1, 'Actualit&eacute;s', '_DIMS_LABEL_NEWS'),
(3097, 'system', 2, 'News', '_DIMS_LABEL_NEWS'),
(3098, 'system', 1, 'Formulaire', '_DIMS_LABEL_FORM'),
(3099, 'system', 2, 'Form', '_DIMS_LABEL_FORM'),
(3100, 'system', 1, 'Fichiers disponibles', '_DIMS_FAIR_AVAILABLE_FILE'),
(3101, 'system', 1, 'Descriptif de l''&eacute;tape', '_DIMS_FAIR_STEP_DESCRIPTION'),
(3102, 'system', 1, 'Alerter des changements', '_DIMS_LABEL_ALERT_MODIF'),
(3103, 'system', 1, 'La newslettre a bien &eacute;t&eacute; envoy&eacute;e.', '_DIMS_LABEL_NEWSLETTER_SENT'),
(3165, 'system', 2, 'Add this contact to the mailing list', '_DIMS_LABEL_ADDCTTONEWS'),
(3105, 'system', 1, 'Une newsletter de test est envoy&eacute;e.', '_DIMS_LABEL_TEST_NEWSLETTER'),
(3106, 'system', 1, 'Souhaitez-vous effectuer une demande d''inscription pour cette action ?', '_DIMS_LABEL_PROJ_DMD_INSC'),
(3107, 'system', 1, 'Entier', '_DIMS_LABEL_INT'),
(3108, 'system', 2, 'Integer', '_DIMS_LABEL_INT'),
(3109, 'system', 1, 'Réel', '_DIMS_LABEL_FLOAT'),
(3110, 'system', 2, 'Float', '_DIMS_LABEL_FLOAT'),
(3111, 'system', 1, 'Chaîne', '_DIMS_LABEL_STRING'),
(3112, 'system', 2, 'String', '_DIMS_LABEL_STRING'),
(3113, 'system', 1, 'Arborescence', '_LABEL_TREE'),
(3114, 'system', 2, 'Tree', '_DIMS_LABEL_TREE'),
(3115, 'system', 1, 'Liste d''articles', '_DIMS_LABEL_LIST_ARTICLE'),
(3116, 'system', 2, 'Articles', '_DIMS_LABEL_LIST_ARTICLE'),
(3283, 'system', 2, 'Newsletter has been sent.', '_DIMS_LABEL_NEWSLETTER_SENT'),
(3119, 'system', 1, 'Ajouter ce contact &agrave; la mailing liste', '_DIMS_LABEL_ADDCTTONEWS'),
(3281, 'system', 2, 'A newsletter is sent to test.', '_DIMS_LABEL_TEST_NEWSLETTER'),
(3282, 'system', 2, 'Would you like a registration for this action?', '_DIMS_LABEL_PROJ_DMD_INSC'),
(3122, 'system', 1, 'Formation', '_DIMS_PROJECT_TYPE_FORMATION'),
(3123, 'system', 1, 'Inscriptions', '_DIMS_LABEL_INSCRIPTION'),
(3124, 'system', 1, 'Nombre d''intervenants n&eacute;cessaire', '_DIMS_PROJECT_NB_INTERV'),
(3125, 'system', 1, 'Action sans demande d''inscription ni inscription effective', '_DIMS_LABEL_PROJET_NOINSC_OR_DMD'),
(3126, 'system', 1, 'Toutes les inscriptions sont effectives', '_DIMS_LABEL_PROJET_ALLINSCOK'),
(3127, 'system', 1, 'Toutes les inscriptions valid&eacute;es mais pas assez d''inscrits', '_DIMS_LABEL_PROJET_NOTENOUGHTINSC'),
(3128, 'system', 1, 'Pas assez d''inscrits ou des demandes non valid&eacute;es', '_DIMS_LABEL_PROJET_ORANGE'),
(3129, 'system', 1, 'Toutes les inscriptions sont annul&eacute;es par les utilisateurs', '_DIMS_LABEL_PROJET_INSCRED'),
(3130, 'system', 1, 'Une demande d''inscription pour cette action est d&eacute;j&agrave; en cours.', '_DIMS_LABEL_DMD_WAITING'),
(3131, 'system', 1, 'Votre demande a bien &eacute;t&eacute; enregistr&eacute;e.', '_DIMS_LABEL_DMD_REGISTERED'),
(3132, 'system', 1, 'Votre inscription est effective.', '_DIMS_LABEL_YOUR_INSC_OK'),
(3133, 'system', 1, 'Votre demande d''inscription est en cours de validation.', '_DIMS_LABEL_YOUR_INSC_WAIT'),
(3134, 'system', 1, 'Votre inscription est refus&eacute;e.', '_DIMS_LABEL_YOUR_INSC_REFUSED'),
(3135, 'system', 1, 'Vous avez annul&eacute; votre inscription.', '_DIMS_LABEL_YOUR_INSC_DEL'),
(3136, 'system', 1, 'Rechercher une entreprise &agrave; rattacher', '_DIMS_LABEL_FIND_ENT_TO_LINK'),
(3137, 'system', 1, 'Standard', '_DIMS_LABEL_STANDARD'),
(3138, 'system', 1, 'Vous n''avez aucune action &agrave; r&eacute;aliser.', '_DIMS_LABEL_PROJET_NO_ACTION'),
(3139, 'system', 1, 'Actions &agrave; r&eacute;aliser', '_DIMS_LABEL_PROJET_ACTION_TODO'),
(3140, 'system', 1, 'Demandes d''inscription &agrave; valider', '_DIMS_LABEL_PROJET_DMD_TODO'),
(3141, 'system', 1, 'Partage des &eacute;v&egrave;nements ', '_DIMS_LABEL_SHARE_PLANNING'),
(3484, 'system', 2, 'Are you sure you want to \\ n detach this group', '_SYSTEM_MSG_CONFIRMGROUPDETACH'),
(3485, 'system', 2, 'Are you sure you want \\ n to detach this user?', '_SYSTEM_MSG_CONFIRMUSERDETACH'),
(3486, 'system', 2, 'Are you sure you want \\ n to delete this user?', '_SYSTEM_MSG_CONFIRMUSERDELETE'),
(3487, 'system', 2, 'Are you sure you want \\ n delete this role ?', '_SYSTEM_MSG_CONFIRMROLEDELETE'),
(3488, 'system', 2, 'Are you sure you want \\ n delete this rule ?', '_SYSTEM_MSG_CONFIRMRULEDELETE'),
(3489, 'system', 2, 'Are you sure you want \\ n delete this profile ?', '_SYSTEM_MSG_CONFIRMPROFILEDELETE'),
(3490, 'system', 2, 'Are you sure you want \\ n delete logs ?', '_SYSTEM_MSG_CONFIRMLOGDELETE'),
(3491, 'system', 2, 'Error when entering the password. \\ NYou must enter twice the same password', '_SYSTEM_MSG_PASSWORDERROR'),
(3492, 'system', 2, 'Error when creating user. \\ NThis login already exists.', '_SYSTEM_MSG_LOGINERROR'),
(3493, 'system', 2, 'Error when entering the password.\r\nYour password has been rejected by the system', '_SYSTEM_MSG_LOGINPASSWORDERROR'),
(3494, 'system', 2, 'Are you sure you want to remove this Module?', '_SYSTEM_MSG_CONFIRMMODULEDETACH'),
(3495, 'system', 2, 'Are you sure you want to delete this Module?', '_SYSTEM_MSG_CONFIRMMODULEDELETE'),
(3496, 'system', 2, 'Are you sure you want to delete this mailing list?', '_SYSTEM_MSG_CONFIRMMAILINGLISTDELETE'),
(3497, 'system', 2, 'Are you sure you want to delete this item ?', '_SYSTEM_MSG_CONFIRMMAILINGLISTATTACHDELETE'),
(3498, 'system', 2, 'Are you sure you want to uninstall this module?', '_SYSTEM_MSG_CONFIRMMODULEUNINSTAL'),
(3499, 'system', 2, 'Are you sure you want to update the metabase of this module? \\ N (reload the XML file)', '_SYSTEM_MSG_CONFIRMMBUPDATE'),
(3500, 'system', 2, 'You can not delete this group because it contains users', '_SYSTEM_MSG_INFODELETE_USERS'),
(3501, 'system', 2, 'You can not delete this group because it contains sub-groups', '_SYSTEM_MSG_INFODELETE_GROUPS'),
(3502, 'system', 2, 'You can not delete this group because it is protected', '_SYSTEM_MSG_PROTECTED_GROUPS'),
(3503, 'system', 2, 'You don''t have permission to clone this workspace', '_SYSTEM_MSG_CANTCOPYGROUP'),
(3504, 'system', 2, 'PhpInfo displays the phpinfo PHP server configuration', '_SYSTEM_EXPLAIN_PHPINFO'),
(3505, 'system', 2, 'The Diagnostics tool checks a few elements common sources of errors that can cause malfunctions of DIMS', '_SYSTEM_EXPLAIN_DIAGNOSTIC'),
(3506, 'system', 2, 'This tool displays the list of connected users to the site in real time', '_SYSTEM_EXPLAIN_CONNECTEDUSERS'),
(3507, 'system', 2, 'SqlDump tool allows you to download the data in a SQL file', '_SYSTEM_EXPLAIN_SQLDUMP'),
(3508, 'system', 2, 'The Backup tool allows you to create a full backup of the system (data sources) in order to restore at a later date', '_SYSTEM_EXPLAIN_BACKUP'),
(3509, 'system', 2, 'BD Cleaning Tool allows you to clean the database by removing unused data', '_SYSTEM_EXPLAIN_CLEANDB'),
(3510, 'system', 2, 'The History tool allows you to view actions performed by users', '_SYSTEM_EXPLAIN_ACTIONHISTORY'),
(3511, 'system', 2, 'Dims is a product developed by Netlor SAS. The logo and trademark are copyright ', '_SYSTEM_EXPLAIN_ABOUT'),
(3512, 'system', 2, 'Allow  mailing list to accept direct user registration', '_SYSTEM_EXPLAIN_MAILINGLIST_PROTECTED'),
(3513, 'system', 2, 'Allow mailing list to accept not connected users', '_SYSTEM_EXPLAIN_MAILINGLIST_PUBLIC'),
(3514, 'system', 2, 'Name of mailing list', '_SYSTEM_EXPLAIN_MAILINGLISTNAME'),
(3515, 'system', 2, 'Name of module', '_SYSTEM_EXPLAIN_MODULENAME'),
(3516, 'system', 2, 'Define possibilities of module is enabled or not (enabled = usable)', '_SYSTEM_EXPLAIN_ACTIVE'),
(3517, 'system', 2, 'Define possibilities of module is public or not (visible for not connected users)', '_SYSTEM_EXPLAIN_PUBLIC'),
(3518, 'system', 2, 'Define possibilities of module displays by default the user login', '_SYSTEM_EXPLAIN_AUTOCONNECT'),
(3519, 'system', 2, 'Define possibilities of module is shared for subgroups', '_SYSTEM_EXPLAIN_SHARED'),
(3520, 'system', 2, 'Define possibilities of module is automatically inherited to subgroups', '_SYSTEM_EXPLAIN_HERITED'),
(3521, 'system', 2, 'Define possibilities of module is visible', '_SYSTEM_EXPLAIN_VISIBLE'),
(3522, 'system', 2, 'Phpinfo', '_DIMS_LABEL_PHPINFO'),
(3523, 'system', 2, 'Diagnosis', '_DIMS_LABEL_DIAGNOSTIC'),
(3524, 'system', 2, 'User (s) Connected (s)', '_DIMS_LABEL_CONNECTEDUSERS'),
(3525, 'system', 2, 'SqlDump', '_DIMS_LABEL_SQLDUMP'),
(3526, 'system', 2, 'A content management module (WCE) is necessary', '_SYSTEM_LABEL_ALERT_WCE'),
(3527, 'system', 2, 'Backup', '_DIMS_LABEL_BACKUP'),
(3528, 'system', 2, 'BD Cleaning', '_DIMS_LABEL_CLEANDB'),
(3529, 'system', 2, 'Contenu de la Page d''Accuei', '_DIMS_LABEL_HOMEPAGECONTENT'),
(3530, 'system', 2, 'Add line', '_DIMS_LABEL_ADDLINE'),
(3531, 'system', 2, 'Number of Columns', '_DIMS_LABEL_NBCOLUMNS'),
(3532, 'system', 2, 'Module', '_MODULE'),
(3533, 'system', 2, 'Instantiating a module', '_DIMS_LABEL_MODULEINSTANCIATION'),
(3534, 'system', 2, 'Delete module', '_DIMS_LABEL_MODULEDELETE'),
(3535, 'system', 2, 'Uninstall module', '_DIMS_LABEL_MODULEUNINSTALL'),
(3536, 'system', 2, 'Remove user from group', '_DIMS_LABEL_USERDETACH'),
(3537, 'system', 2, 'Delete user', '_DIMS_LABEL_USERDELETE'),
(3538, 'system', 2, 'Accessibility', '_DIMS_LABEL_ACCES'),
(3539, 'system', 2, 'Import source', '_DIMS_LABEL_IMPORTSRC'),
(3540, 'system', 2, 'Start indexing', '_DIMS_LABEL_INDEXRUN'),
(3541, 'system', 2, 'This resets all indexing and thesaurus matches. This process may take a long time.\r\n', '_SYSTEM_EXPLAIN_INDEXRUN'),
(3542, 'system', 2, 'Activities', '_DIMS_LABEL_ACTIVITY'),
(3543, 'system', 2, 'Monthly Activities', '_DIMS_LABEL_ACTIVITY_WORKSPACE'),
(3544, 'system', 2, 'last month', '_DIMS_LABEL_LAST_MONTH'),
(3545, 'system', 2, 'Logs', '_DIMS_ADMIN_LOGS'),
(3546, 'system', 2, 'Keywords indexes', '_DIMS_LABEL_WORDS_INDEXED'),
(3547, 'system', 2, 'Keywords', '_DIMS_LABEL_KEYWORDS'),
(3548, 'system', 2, 'Dictionary of ', '_DIMS_LABEL_DICO'),
(3549, 'system', 2, 'Default workspace', '_DIMS_LABEL_DEFAULTWORKSPACE'),
(3550, 'system', 2, 'View shared modules', '_DIMS_SHAREDMOD_DISPLAY'),
(3551, 'system', 2, 'Hide shared modules', '_DIMS_SHAREDMOD_HIDE'),
(3552, 'system', 2, 'News files', '_DIMS_COLLABORATION'),
(3553, 'system', 2, 'Users workspace', '_DIMS_DESKTOP_USER'),
(3554, 'system', 2, 'To confirm', '_DIMS_TOVALID'),
(3555, 'system', 2, 'Receipts', '_DIMS_TOVIEW'),
(3556, 'system', 2, 'Waiting', '_DIMS_LABEL_SURVEY'),
(3557, 'system', 2, 'News', '_DIMS_NEWS'),
(3558, 'system', 2, 'People involved', '_DIMS_LABEL_CONCERNED'),
(3559, 'system', 2, 'Male', '_DIMS_LABEL_CONT_SEXE_M'),
(3560, 'system', 2, 'News Projects', '_DIMS_PROJECT'),
(3561, 'system', 2, 'Filter on projects', '_DIMS_PROJECT_FILTER'),
(3562, 'system', 2, 'Project List', '_DIMS_PROJECTS'),
(3563, 'system', 2, 'All projects', '_DIMS_ALL_PROJECTS'),
(3564, 'system', 2, 'Steps', '_DIMS_MILESTONE'),
(3565, 'system', 2, 'Add project', '_DIMS_ADDPROJECT'),
(3566, 'system', 2, 'Are you sure you want to \\ nDelete this task ?', '_DIMS_MSG_CONFIRMTASKDELETE'),
(3567, 'system', 2, 'Add task', '_DIMS_ADDTASK'),
(3568, 'system', 2, 'Gantt', '_DIMS_GANTT'),
(3569, 'system', 2, 'Interests', '_DIMS_INTEREST'),
(3570, 'system', 2, 'Intelligence', '_DIMS_LABEL_OPROFIL'),
(3571, 'system', 2, 'Trades', '_DIMS_WORK'),
(3572, 'system', 2, 'Presentation', '_DIMS_PRESENTATION'),
(3573, 'system', 2, '#B00000 ', '_BUSINESS_COLOR_TIERS'),
(3574, 'system', 2, '#0000B0', '_BUSINESS_COLOR_DOSSIER'),
(3575, 'system', 2, '#009000 ', '_BUSINESS_COLOR_INTERLOC'),
(3576, 'system', 2, 'Business Management', '_BUSINESS_PAGE_TITLE'),
(3577, 'system', 2, 'Companies', '_BUSINESS_TIERS'),
(3578, 'system', 2, 'Company', '_BUSINESS_TIER'),
(3579, 'system', 2, 'No company', '_BUSINESS_NO_TIER'),
(3580, 'system', 2, 'Dynamic fields for company form', '_DIMS_LABEL_DYNFIELD_ENT'),
(3581, 'system', 2, 'Appointment', '_BUSINESS_ACTION'),
(3582, 'system', 2, 'No appointment', '_BUSINESS_NO_ACTION'),
(3583, 'system', 2, 'Project', '_BUSINESS_DOSSIER'),
(3584, 'system', 2, 'Edit card', '_BUSINESS_LEGEND_MODIFY'),
(3585, 'system', 2, 'Delete card', '_BUSINESS_LEGEND_DELETE'),
(3586, 'system', 2, 'Delete link', '_BUSINESS_LEGEND_CUT'),
(3587, 'system', 2, 'Go to Detailed', '_BUSINESS_LEGEND_GOTO'),
(3588, 'system', 2, 'Unauthorized', '_BUSINESS_LEGEND_NOTALLOWED'),
(3589, 'system', 2, 'Duplicate', '_BUSINESS_LEGEND_RENEW'),
(3590, 'system', 2, 'Are you sure you want to delete ''"VALUE"?', '_BUSINESS_MSG_CONFIRMDELETE'),
(3591, 'system', 2, 'Are you sure you want to renew ?', '_BUSINESS_MSG_CONFIRMRENEW'),
(3592, 'system', 2, 'Are you sure you want to remove ?', '_BUSINESS_MSG_CONFIRMCUT'),
(3593, 'system', 2, 'Project name', '_PROJECT_LABEL_NAME_PROJECT'),
(3594, 'system', 2, 'Add project', '_PROJECT_LABEL_ADD_PROJECT'),
(3595, 'system', 2, 'Viewing Multiple Projects', '_PROJECT_LABEL_VIEW_MULTI_PROJECT'),
(3596, 'system', 2, 'Viewing Multiple Projects', '_PROJECT_LABEL_VIEW_MULTI_TASK'),
(3597, 'system', 2, 'Viewer', '_PROJECT_LABEL_VISU'),
(3598, 'system', 2, 'Zoom: Week', '_PROJECT_LABEL_ZOOM_WEEK'),
(3599, 'system', 2, 'Zoom : Month ', '_PROJECT_LABEL_ZOOM_MONTH'),
(3600, 'system', 2, 'Project Tracking', '_PROJECT_LABEL_MANAGE_PROJECT'),
(3601, 'system', 2, 'Advancement', '_DIMS_LABEL_PROGRESS'),
(3602, 'system', 2, 'Resp.', '_PROJECT_MENU_PERSON_IN_CHARGE'),
(3603, 'system', 2, 'Normal', '_DIMS_LABEL_CONT_VIP_N'),
(3604, 'system', 2, 'Are you sure you want to close the project ?', '_PROJECT_MSG_2'),
(3605, 'system', 2, 'Are you sure you want open Project', '_PROJECT_MSG_3'),
(3606, 'system', 2, 'Selection', '_FORM_SELECTION'),
(3607, 'system', 2, 'Author :', '_FORM_PROJECT_CREATOR'),
(3608, 'system', 2, 'Begin :', '_FORM_PROJECT_START_DATE'),
(3609, 'system', 2, 'End :', '_FORM_PROJECT_END_DATE'),
(3610, 'system', 2, 'Undo', '_DIMS_LABEL_CANCEL'),
(3611, 'system', 2, 'Made', '_FORM_TASK_TIME'),
(3612, 'system', 2, 'Planned', '_FORM_TASK_TIME_EXPECTED'),
(3613, 'system', 2, 'Possible', '_FORM_TASK_TIME_ENABLED'),
(3614, 'system', 2, 'Period', '_FORM_TASK_DURATION'),
(3615, 'system', 2, 'Priority', '_FORM_TASK_PRIORITY'),
(3616, 'system', 2, 'By default', '_FORM_TASK_PRIORITY_0'),
(3617, 'system', 2, 'Average', '_FORM_TASK_PRIORITY_1'),
(3618, 'system', 2, 'Important', '_FORM_TASK_PRIORITY_2'),
(3619, 'system', 2, 'Add task to project', '_FORM_TASK_OK'),
(3620, 'system', 2, 'Edit task', '_FORM_TASK_MODIFY'),
(3621, 'system', 2, 'Attach', '_FORM_USER_OK'),
(3622, 'system', 2, 'Date :', '_FORM_OBJECTIVE_DATE'),
(3623, 'system', 2, 'Description :', '_FORM_OBJECTIVE_COMMENT'),
(3624, 'system', 2, 'Add this objective', '_FORM_OBJECTIVE_OK'),
(3625, 'system', 2, 'Author :', '_INFOS_CREATOR'),
(3626, 'system', 2, 'Start', '_INFOS_START_DATE'),
(3627, 'system', 2, 'End', '_INFOS_END_DATE'),
(3628, 'system', 2, 'Status', '_INFOS_STATE'),
(3629, 'system', 2, 'Delay', '_INFOS_TOLATE'),
(3630, 'system', 2, 'Monday', '_MONDAY'),
(3631, 'system', 2, 'Tuesday', '_THUESDAY'),
(3632, 'system', 2, 'Wednesday', '_WEDNESDAY'),
(3633, 'system', 2, 'Thursday', '_THIRDAY'),
(3634, 'system', 2, 'Friday', '_FRIDAY'),
(3635, 'system', 2, 'Saturday', '_SATURDAY'),
(3636, 'system', 2, 'Sunday', '_SUNDAY'),
(3637, 'system', 2, 'M', '_MONDAY_SMALL'),
(3638, 'system', 2, 'T', '_THUESDAY_SMALL'),
(3639, 'system', 2, 'W', '_WEDNESDAY_SMALL'),
(3640, 'system', 2, 'T', '_THIRDAY_SMALL'),
(3641, 'system', 2, 'F', '_FRIDAY_SMALL'),
(3642, 'system', 2, 'S', '_SATURDAY_SMALL'),
(3643, 'system', 2, 'S', '_SUNDAY_SMALL'),
(3644, 'system', 2, 'J', '_JANUARY_SMALL'),
(3645, 'system', 2, 'F', '_FEBRUARY_SMALL'),
(3646, 'system', 2, 'M', '_MARCH_SMALL'),
(3647, 'system', 2, 'A', '_APRIL_SMALL'),
(3648, 'system', 2, 'M', '_MAY_SMALL'),
(3649, 'system', 2, 'J', '_JUNE_SMALL'),
(3650, 'system', 2, 'J', '_JULY_SMALL'),
(3651, 'system', 2, 'A', '_AUGUST_SMALL'),
(3652, 'system', 2, 'S', '_SEPTEMBER_SMALL'),
(3653, 'system', 2, 'O', '_OCTOBER_SMALL'),
(3654, 'system', 2, 'N', '_NOVEMBER_SMALL'),
(3655, 'system', 2, 'D', '_DECEMBER_SMALL'),
(3656, 'system', 2, 'Dynamix fields for Contact card', '_DIMS_LABEL_DYNFIELD_CONTACT'),
(3657, 'system', 2, 'Add company card', '_DIMS_LABEL_ENT_CREATE'),
(3658, 'system', 2, 'Definition of uses for field', '_BUSINESS_USE_DEFINEDFIELD'),
(3659, 'system', 2, 'Importance of contact', '_DIMS_LABEL_CONT_VIP'),
(3660, 'system', 2, 'Industry Business Employer', '_DIMS_LABEL_CONT_SECTACT'),
(3661, 'system', 2, 'Create Contact', '_DIMS_LABEL_CONT_CREATE'),
(3662, 'system', 2, 'Contact card', '_DIMS_LABEL_CT_FICHE'),
(3663, 'system', 2, 'Sex', '_DIMS_LABEL_SEXE'),
(3664, 'system', 2, 'Title', '_BUSINESS_FIELD_NAME'),
(3665, 'system', 2, 'Default value', '_BUSINESS_FIELD_DEFAULTVALUE'),
(3666, 'system', 2, 'Created  ', '_SYSTEM_LABEL_FICHCREATED'),
(3667, 'system', 2, 'Answer(s)', '_FORMS_RESPONSE'),
(3668, 'system', 2, 'Name of table', '_FORMS_TABLENAME'),
(3669, 'system', 2, 'Start Date of Publication', '_FORMS_PUBDATESTART'),
(3670, 'system', 2, 'End Date of Publication', '_FORMS_PUBDATEEND'),
(3671, 'system', 2, 'Send email to', '_FORMS_EMAIL'),
(3672, 'system', 2, 'Width (*: variable)', '_FORMS_WIDTH'),
(3673, 'system', 2, 'Change', '_FORMS_MODIFICATION'),
(3674, 'system', 2, 'Fields list', '_FORMS_FIELDLIST'),
(3675, 'system', 2, 'Add field', '_DIMS_LABEL_ADDFIELD'),
(3676, 'system', 2, 'Add space', '_FORMS_ADDSEPARATOR'),
(3677, 'system', 2, 'Forms list', '_FORMS_LIST'),
(3678, 'system', 2, 'Use form', '_FORMS_FILL'),
(3679, 'system', 2, 'Model Display', '_FORMS_MODEL'),
(3680, 'system', 2, 'Form type', '_FORMS_TYPEFORM'),
(3681, 'system', 2, 'Automatic Archiving (In Days)', '_FORMS_AUTOBACKUP'),
(3682, 'system', 2, 'Modification of spacer', '_FORMS_SEPARATORMODIFICATION'),
(3683, 'system', 2, 'Limiting the description to users concerned', '_DIMS_LABEL_LIMIT_ACTION'),
(3684, 'system', 2, 'IP address', '_FORMS_IP'),
(3685, 'system', 2, 'Date Validation', '_FORMS_DATEVALIDATION'),
(3686, 'system', 2, 'Nb Line / Page ', '_FORMS_NBLINE'),
(3687, 'system', 2, 'Preview', '_FORMS_PREVIEW'),
(3688, 'system', 2, 'View results', '_FORMS_VIEWRESULT'),
(3689, 'system', 2, 'Filter', '_FORMS_FILTER'),
(3690, 'system', 2, 'Export', '_FORMS_EXPORT'),
(3691, 'system', 2, 'Title', '_FORMS_FIELD_NAME'),
(3692, 'system', 2, 'Spacing', '_FORMS_FIELD_INTERLINE'),
(3693, 'system', 2, 'Format', '_FIELD_FORMAT'),
(3694, 'system', 2, 'Visible to export', '_FORMS_FIELD_EXPORTVIEW'),
(3695, 'system', 2, 'Visible in list', '_FORMS_FIELD_ARRAYVIEW'),
(3696, 'system', 2, 'Default value', '_FORMS_FIELD_DEFAULTVALUE'),
(3697, 'system', 2, 'Level space', '_FORMS_FIELD_SEPARATOR_LEVEL'),
(3698, 'system', 2, 'Font size (pix)', '_FORMS_FIELD_SEPARATOR_FONTSIZE'),
(3699, 'system', 2, 'Level space', '_FORMS_FIELD_SEPARATOR_DESC'),
(3700, 'system', 2, 'Req.', '_FORMS_FIELD_NEEDED_SHORT'),
(3701, 'system', 2, 'Vis. Exp.', '_FORMS_FIELD_EXPORTVIEW_SHORT'),
(3702, 'system', 2, 'Vis. Lst. ', '_FORMS_FIELD_ARRAYVIEW_SHORT'),
(3703, 'system', 2, 'You have already completed this form', '_FORMS_ALLREADYFILLED'),
(3704, 'system', 2, 'Only one entry per user (eg survey)', '_FORMS_OPTION_ONLYONE'),
(3705, 'system', 2, 'Only one entry per day (eg surveys)', '_FORMS_OPTION_ONLYONEDAY'),
(3706, 'system', 2, 'Edit rule', '_FORMS_OPTION_MODIFY'),
(3707, 'system', 2, 'Owner', '_FORMS_OPTION_MODIFY_USER'),
(3708, 'system', 2, 'Group', '_FORMS_OPTION_MODIFY_GROUP'),
(3709, 'system', 2, 'Everyone', '_FORMS_OPTION_MODIFY_ALL'),
(3710, 'system', 2, 'Export data', '_FORMS_DATA_EXPORT'),
(3711, 'system', 2, 'View entries', '_FORMS_DATA_VIEW'),
(3712, 'system', 2, 'Data view', '_FORMS_OPTION_VIEW'),
(3713, 'system', 2, 'Display Info "User"', '_FORMS_OPTION_DISPLAY_USER'),
(3714, 'system', 2, 'display info Group', '_FORMS_OPTION_DISPLAY_GROUP'),
(3715, 'system', 2, 'Display validation date', '_FORMS_OPTION_DISPLAY_DATE'),
(3716, 'system', 2, 'Display IP address', '_FORMS_OPTION_DISPLAY_IP'),
(3717, 'system', 2, '* Required', '_FORMS_FIELDNEEDED'),
(3718, 'system', 2, 'Completed by', '_FORMS_FILLEDBY'),
(3719, 'system', 2, 'Anonymous', '_FORMS_ANONYMOUS'),
(3720, 'system', 2, 'You can enter multiple addresses separated by ";"', '_FORMS_HELP_EMAIL'),
(3721, 'system', 2, 'Some options are specific to type of form you create', '_FORMS_HELP_TYPEFORM'),
(3722, 'system', 2, 'My contacts', '_DIRECTORY_MYCONTACTS'),
(3723, 'system', 2, 'My workspace', '_DIRECTORY_MYGROUP'),
(3724, 'system', 2, 'Shared contacts', '_DIRECTORY_COMMON'),
(3725, 'system', 2, 'My favorites', '_DIRECTORY_FAVORITES'),
(3726, 'system', 2, 'Add new contact', '_DIRECTORY_ADDNEWCONTACT'),
(3727, 'system', 2, 'Edit contact', '_DIRECTORY_MODIFYCONTACT'),
(3728, 'system', 2, 'Display contact', '_DIRECTORY_VIEWCONTACT'),
(3729, 'system', 2, 'Workspaces', '_DIRECTORY_GROUPS'),
(3730, 'system', 2, 'Phone', '_DIRECTORY_PHONE'),
(3731, 'system', 2, 'Email', '_DIRECTORY_EMAIL'),
(3732, 'system', 2, 'Full text', '_DIRECTORY_FULLTEXT'),
(3733, 'system', 2, 'Legend', '_DIRECTORY_LEGEND'),
(3734, 'system', 2, 'Lines / Page', '_DIRECTORY_LINESPERPAGE'),
(3735, 'system', 2, 'Are you sure you want to delete this contact ?', '_DIRECTORY_CONFIRM_DELETECONTACT'),
(3736, 'system', 2, 'Are you sure you want to delete this bookmark ?', '_DIRECTORY_CONFIRM_DELETEFAVORITES'),
(3737, 'system', 2, 'Delete', '_DIRECTORY_LEGEND_DELETE'),
(3738, 'system', 2, 'Send e-mail', '_DIRECTORY_LEGEND_EMAIL'),
(3739, 'system', 2, 'Company attached', '_DIMS_LABEL_ATTACH_ORGA'),
(3740, 'system', 2, 'Root', '_DOC_ROOT'),
(3741, 'system', 2, 'Events', '_DIMS_LABEL_EVENTS'),
(3742, 'system', 2, 'New file', '_DOC_NEWFILE'),
(3743, 'system', 2, 'Current profile', '_DIMS_LABEL_CURPROFIL'),
(3744, 'system', 2, 'New folder', '_DOC_NEWFOLDER'),
(3745, 'system', 2, 'files found', '_DOC_LABEL_FILESFOUND'),
(3746, 'system', 2, 'file found', '_DOC_LABEL_FILEFOUND'),
(3747, 'system', 2, 'Add another file', '_DOC_LABEL_ADD_OTHER_FILE'),
(3748, 'system', 2, 'Sending in progress', '_DOC_MSG_UPLOAD_FILE'),
(3749, 'system', 2, 'Copy in progress', '_DOC_MSG_COPY_FILE'),
(3750, 'system', 2, 'Waiting for connection', '_DOC_MSG_UPLOAD_WAITING'),
(3751, 'system', 2, 'Error sending file', '_DOC_MSG_UPLOAD_ERROR'),
(3752, 'system', 2, 'This extension is not allowed', '_DOC_MSG_UPLOAD_ERROREXT'),
(3753, 'system', 2, 'Action', '_DOC_LABEL_OPERATION'),
(3754, 'system', 2, 'Move', '_DOC_LABEL_MOVE'),
(3755, 'system', 2, 'Decompress', '_DOC_LABEL_UNCOMPRESS'),
(3756, 'system', 2, 'Error! Save the file is impossible - the file is empty', '_DOC_LABEL_ERROR_EMPTYFILE'),
(3757, 'system', 2, 'Error! Save the file is impossible - the file is not writable', '_DOC_LABEL_ERROR_FILENOTWRITABLE'),
(3758, 'system', 2, 'Error! Save the file impossible - the file is too large', '_DOC_LABEL_ERROR_MAXFILESIZE'),
(3759, 'system', 2, 'Error! Unable to unzip the archive', '_DOC_LABEL_ERROR_FOLDERZIP'),
(3760, 'system', 2, 'You do not have permissions to delete this folder', '_DOC_LABEL_UNAUTHORIZED_DELETEFOLDER'),
(3761, 'system', 2, 'You do not have permissions to delete this file', '_DOC_LABEL_UNAUTHORIZED_DELETEFILE'),
(3762, 'system', 2, 'Already visited Luxembourg', '_DIMS_LABEL_CONT_LUX'),
(3763, 'system', 2, 'Properties', '_WCE_PAGE_PROPERTIES'),
(3764, 'system', 2, 'Top', '_WCE_HEADER'),
(3765, 'system', 2, 'Bottom', '_WCE_FOOTER'),
(3766, 'system', 2, 'Content Left', '_WCE_LEFT_CONTENT'),
(3767, 'system', 2, 'Content Right', '_WCE_RIGHT_CONTENT'),
(3768, 'system', 2, 'Keywords', '_WCE_KEYWORDS_META'),
(3769, 'system', 2, 'Referencing', '_WCE_PAGE_REFER'),
(3770, 'system', 2, 'Page', '_DIMS_LABEL_PAGE'),
(3771, 'system', 2, 'Url rewriting', '_WCE_URLREWRITE'),
(3772, 'system', 2, 'Site preview', '_WCE_ARTICLE_SITEPREVIEW'),
(3773, 'system', 2, 'Reference', '_WCE_ARTICLE_REFERENCE'),
(3774, 'system', 2, 'Publication', '_WCE_ARTICLE_PUBLISH'),
(3775, 'system', 2, 'No domain', '_WCE_NO_DOMAIN'),
(3776, 'system', 2, 'Select Dims object to insert', '_WCE_INSERT_OBJECT_CHOICE'),
(3777, 'system', 2, 'Choice module', '_WCE_INSERT_MODULE_CHOICE'),
(3778, 'system', 2, 'This object is already inserted in this article', '_WCE_OBJECT_ALREADY_EXISTS'),
(3779, 'system', 2, 'Monitoring', '_DIMS_LABEL_VEILLE'),
(3780, 'system', 2, 'News found (s)', '_NEWS_LABEL_NEWSFOUND'),
(3781, 'system', 2, 'No news found', '_NEWS_LABEL_NONEWSFOUND'),
(3782, 'system', 2, 'Write', '_NEWS_LABELTAB_WRITE'),
(3783, 'system', 2, 'Category', '_DIMS_LABEL_CATEGORY'),
(3784, 'system', 2, 'Publication date', '_PUBLISHDATE'),
(3785, 'system', 2, 'Publish time', '_NEWS_LABEL_PUBLISHTIME'),
(3786, 'system', 2, 'Link', '_DIMS_LABEL_URL'),
(3787, 'system', 2, 'Source', '_NEWS_LABEL_SOURCE'),
(3788, 'system', 2, 'Unknown', '_NEWS_LABEL_UNKNOWN'),
(3789, 'system', 2, 'In Focus', '_NEWS_LABEL_HOT'),
(3790, 'system', 2, 'Readings', '_NEWS_LABEL_READS'),
(3791, 'system', 2, 'Write news', '_NEWS_WRITE'),
(3792, 'system', 2, 'Edit news ''LABEL'' ', '_NEWS_MODIFY'),
(3793, 'system', 2, 'News list', '_NEWS_LIST'),
(3794, 'system', 2, 'Deleting News\r\nDeleting Categories\r\nDeleting Settings', '_NEWS_LABEL_DELETE_INSTANCE'),
(3795, 'system', 2, 'Initialization of user data', '_NEWS_LABEL_ATTACH_USER'),
(3796, 'system', 2, 'Uninstalling the Module', '_NEWS_LABEL_UNINSTALL'),
(3797, 'system', 2, 'Categories', '_CATEGORIES'),
(3798, 'system', 2, 'New', '_RSS_LABELTAB_ADD'),
(3799, 'system', 2, 'Refresh', '_RSS_LABELTAB_MODIFY'),
(3800, 'system', 2, 'Explore', '_RSS_LABELTAB_EXPLORER'),
(3801, 'system', 2, 'Fax Office', '_DIMS_LABEL_FAX_WORK'),
(3802, 'system', 2, 'RSS founds', '_RSS_LABEL_NEWSFOUND'),
(3803, 'system', 2, 'RSS found', '_RSS_LABEL_NEWFOUND'),
(3804, 'system', 2, 'No RSS found', '_RSS_LABEL_NONEWSFOUND'),
(3805, 'system', 2, 'Category', '_RSS_LABEL_CATEGORY'),
(3806, 'system', 2, 'Default source', '_RSS_LABEL_DEFAULT'),
(3807, 'system', 2, 'Renewal source', '_RSS_LABEL_FEED_RENEW'),
(3808, 'system', 2, 'Update outdated information sources', '_RSS_LABEL_UPDATE_OUTDATED_FEEDS'),
(3809, 'system', 2, 'Remove invalid information sources', '_RSS_LABEL_DELETE_UNAVAILABLE_FEEDS'),
(3810, 'system', 2, 'Update all information sources', '_RSS_LABEL_UPDATE_ALL_FEEDS'),
(3811, 'system', 2, 'My Searches', '_RSS_LABEL_MYSEARCHES'),
(3812, 'system', 2, 'Request', '_RSS_LABEL_REQUEST'),
(3813, 'system', 2, 'List of Categories', '_CAT_LIST'),
(3814, 'system', 2, 'Add RSS source', '_RSS_ADD'),
(3815, 'system', 2, 'Edit RSS ''LABEL''', '_RSS_MODIFY'),
(3816, 'system', 2, 'List of RSS sources', '_RSS_LIST'),
(3817, 'system', 2, 'Are you sure you want to delete this category ?', '_RSSCAT_LABEL_DELETE_CONFIRM'),
(3818, 'system', 2, 'Remove All', '_DIMS_ALLDELETE'),
(3819, 'system', 2, 'My workspaces', '_DIMS_ENABLED_WORKSPACE'),
(3820, 'system', 2, 'All worskspaces', '_DIMS_ALL_WORKSPACES'),
(3821, 'system', 2, 'End Time', '_DIMS_LABEL_HEUREFIN'),
(3822, 'system', 2, 'Search planning', '_DIMS_SEARCH_PLANNING'),
(3823, 'system', 2, 'Favorites planning', '_DIMS_FAVORITE_PLANNING'),
(3824, 'system', 2, 'View mode', '_DIMS_MODE'),
(3825, 'system', 2, 'History', '_DIMS_HISTORY'),
(3826, 'system', 2, 'Participate', '_DIMS_PARTICIPATES'),
(3827, 'system', 2, 'For information', '_DIMS_TOINFO'),
(3828, 'system', 2, 'Participants', '_DIMS_PARTICIP'),
(3829, 'system', 2, 'Reset', '_DIMS_RESET'),
(3830, 'system', 2, 'Filter', '_DIMS_FILTER'),
(3831, 'system', 2, 'Execute', '_DIMS_EXECUTE'),
(3832, 'system', 2, 'Finish', '_DIMS_COMPLETE'),
(3833, 'system', 2, 'Download', '_DIMS_DOWNLOAD'),
(3834, 'system', 2, 'Send', '_DIMS_SEND'),
(3835, 'system', 2, 'Waiting for validation', '_DIMS_CONFIRM_WAIT'),
(3836, 'system', 2, 'Important', '_DIMS_LABEL_CONT_VIP_T'),
(3837, 'system', 2, 'Reply', '_DIMS_REPLY'),
(3838, 'system', 2, 'Continue', '_DIMS_CONTINUE'),
(3839, 'system', 2, 'Open', '_DIMS_OPEN'),
(3840, 'system', 2, 'List entries', '_DIMS_INPUT_LIST'),
(3841, 'system', 2, 'Confirm', '_DIMS_VALID'),
(3842, 'system', 2, 'Update', '_DIMS_UPDATE'),
(3843, 'system', 2, 'Put on hold', '_DIMS_ADDTO_SURVEY'),
(3844, 'system', 2, 'Pending remove', '_DIMS_REMOVEFROM_SURVEY'),
(3845, 'system', 2, 'Herited', '_DIMS_HERITED'),
(3846, 'system', 2, 'Yes', '_DIMS_YES'),
(3847, 'system', 2, 'No', '_DIMS_NO'),
(3848, 'system', 2, 'Locked', '_DIMS_LOCKED'),
(3849, 'system', 2, 'Position', '_POSITION'),
(3850, 'system', 2, 'Color', '_DIMS_LABEL_COLOR'),
(3851, 'system', 2, 'Needed', '_DIMS_NEEDED'),
(3852, 'system', 2, 'Delay estimate', '_DIMS_TOLATE_PREVIEW'),
(3853, 'system', 2, 'Answer', '_DIMS_ANSWER'),
(3854, 'system', 2, 'Date', '_DIMS_DATE'),
(3855, 'system', 2, 'News Contacts', '_DIMS_LABEL_COLLABORATION_PERS'),
(3856, 'system', 2, 'Woman', '_DIMS_LABEL_CONT_SEXE_F'),
(3857, 'system', 2, 'Updated', '_DIMS_DATE_MODIFY'),
(3858, 'system', 2, 'Description', '_DIMS_LABEL_DESCRIPTION'),
(3859, 'system', 2, 'Search result', '_DIMS_SEARCH_RESULT'),
(3860, 'system', 2, 'Enable validation', '_DIMS_ACTIVATE_CHECK'),
(3861, 'system', 2, 'Enable news', '_DIMS_VALIDATE_CHECK'),
(3862, 'system', 2, 'No tag', '_DIMS_NO_TAGS_SEARCH'),
(3863, 'system', 2, 'No expression', '_DIMS_NO_WORDS_SEARCH'),
(3864, 'system', 2, 'Owner', '_DIMS_OWNER'),
(3865, 'system', 2, 'Object properties', '_DIMS_OBJECT_PROPERTIES'),
(3866, 'system', 2, 'View content', '_DIMS_VIEW_CONTENT'),
(3867, 'system', 2, 'Properties', '_DIMS_PROPERTIES'),
(3868, 'system', 2, 'View details', '_DIMS_OBJECT_DISPLAY'),
(3869, 'system', 2, 'Hide details', '_DIMS_OBJECT_HIDE'),
(3870, 'system', 2, 'View list', '_DIMS_LIST_DISPLAY'),
(3871, 'system', 2, 'Hide properties', '_DIMS_PROPERTIES_PROJECT_HIDE'),
(3872, 'system', 2, 'Summary', '_DIMS_OBJECT_RESUME'),
(3873, 'system', 2, 'Full content', '_DIMS_OBJECT_COMPLETECONTENT'),
(3874, 'system', 2, 'Are you sure you validate the result of campaign ?', '_DIMS_CONFIRM_UPDATE_CAMPAIGN'),
(3875, 'system', 2, 'Are you sure ?', '_DIMS_CONFIRM'),
(3876, 'system', 2, 'Posts', '_DIMS_LABEL_ADMIN_MESSAGES'),
(3877, 'system', 2, 'Workspaces', '_DIMS_ADMIN_WORKSPACES'),
(3878, 'system', 2, 'File', '_DIMS_LABEL_FILE'),
(3879, 'system', 2, 'Unknown User', '_DIMS_UNKNOWNUSER'),
(3880, 'system', 2, 'Administration', '_DIMS_LABEL_ADMIN'),
(3881, 'system', 2, 'Navigation Bar', '_DIMS_LABEL_NAVIGATE'),
(3882, 'system', 2, 'Navigation toolbar', '_DIMS_LABEL_TOOLBARNAVIGATE'),
(3883, 'system', 2, 'News', '_DIMS_LABEL_TOOLBARNEWS'),
(3884, 'system', 2, 'Share', '_DIMS_LABEL_TOOLBARSHARE'),
(3885, 'system', 2, 'Indexing not done', '_DIMS_LABEL_NOINDEX'),
(3886, 'system', 2, 'You must select an element', '_DIMS_MSG_MUSTSELECTEDELEMENT'),
(3887, 'system', 2, 'Modules', '_DIMS_LABEL_MODULES'),
(3888, 'system', 2, 'Comment.', '_DIMS_LABEL_ANNOTATION'),
(3889, 'system', 2, 'Group', '_DIMS_LABEL_GROUP'),
(3890, 'system', 2, 'Connexion form', '_DIMS_LABEL_CONNECTWORKSPACE'),
(3891, 'system', 2, 'No message', '_DIMS_LABEL_NO_TICKET'),
(3892, 'system', 2, 'No current discussion', '_DIMS_LABEL_NO_DISCUSS'),
(3893, 'system', 2, 'No person connected to other Dims', '_DIMS_LABEL_NO_INTERCOM'),
(3894, 'system', 2, 'No object selected', '_DIMS_LABEL_NO_OBJECT'),
(3895, 'system', 2, 'Not used', '_DIMS_LABEL_NOTUSED'),
(3896, 'system', 2, 'Use by workspace(s)', '_DIMS_WORKSPACE_USE'),
(3897, 'system', 2, 'Use', '_DIMS_USE_MODE'),
(3898, 'system', 2, 'Event', '_DIMS_LABEL_EVENT'),
(3899, 'system', 2, 'Choose order\r\ninformation blocks', '_DIMS_LABEL_BLOC_ORDER'),
(3900, 'system', 2, 'Leader', '_DIMS_LABEL_ENT_DIR'),
(3901, 'system', 2, 'Presentation / Activity', '_DIMS_LABEL_ENT_PRES'),
(3902, 'system', 2, 'Web site', '_DIMS_LABEL_ENT_WSITE'),
(3903, 'system', 2, 'Industry', '_DIMS_LABEL_ENT_SECTACT'),
(3904, 'system', 2, 'Headcount', '_DIMS_LABEL_ENT_EFFECTIF'),
(3905, 'system', 2, 'Created on', '_DIMS_LABEL_ENT_DATEC'),
(3906, 'system', 2, 'Capital', '_DIMS_LABEL_ENT_CAPITAL'),
(3907, 'system', 2, 'Business Information', '_FORM_CT_INF_MET'),
(3908, 'system', 2, 'Created', '_DIMS_LABEL_CREATE_ON'),
(3909, 'system', 2, 'There is no contact', '_DIMS_LABEL_NO_RESP'),
(3910, 'system', 2, 'There is no link.', '_DIMS_LABEL_NO_LINK'),
(3911, 'system', 2, 'Private links', '_DIMS_LABEL_LINK_PERSO'),
(3912, 'system', 2, 'IDENTITY', '_DIMS_PERS_IDENTITY'),
(4412, 'system', 1, 'G&eacute;rer vos listes et vos envois', '_DIMS_MAILING_MANAGE_LIST'),
(3914, 'system', 2, 'INFORMATIONS', '_DIMS_PERS_INFOS'),
(3915, 'system', 2, 'Contact cards in survey', '_DIMS_LABEL_WATCH_CONT');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(3916, 'system', 2, 'Company cards', '_DIMS_LABEL_WATCH_ENT'),
(3917, 'system', 2, 'Contacts being monitored', '_DIMS_LABEL_WATCH_SURVENCOURS'),
(3918, 'system', 2, 'Business Description', '_DIMS_LABEL_CONT_DESCM'),
(3919, 'system', 2, 'Company linked', '_DIMS_LABEL_CONT_ENTRAT'),
(3920, 'system', 2, 'Search company', '_DIMS_LABEL_SEARCH_ENT'),
(3921, 'system', 2, 'Professional Information', '_DIMS_LABEL_CONT_INFPRO'),
(3922, 'system', 2, 'Personal Information', '_DIMS_LABEL_CONT_INFPERS'),
(3923, 'system', 2, 'Birthday', '_DIMS_LABEL_CONT_DATE_N'),
(3924, 'system', 2, 'Current company', '_DIMS_LABEL_CONT_ENTACT'),
(3925, 'system', 2, 'Phone', '_DIMS_LABEL_TEL'),
(3926, 'system', 2, 'Fax', '_DIMS_LABEL_FAX'),
(3927, 'system', 2, 'Career', '_DIMS_LABEL_PARCPRO'),
(3928, 'system', 2, 'Marital status', '_DIMS_LABEL_SITFAM'),
(3929, 'system', 2, 'Number of children', '_DIMS_LABEL_NBCHILD'),
(3930, 'system', 2, 'Contact links', '_DIMS_LABEL_LINK_CONT'),
(3931, 'system', 2, 'Company link', '_DIMS_LABEL_LINK_ENT'),
(3932, 'system', 2, 'Public link', '_DIMS_LABEL_LINK_GEN'),
(3933, 'system', 2, 'Workspace link', '_DIMS_LABEL_LINK_MET'),
(3934, 'system', 2, 'Link Type', '_DIMS_LABEL_LINK_TYPE'),
(3935, 'system', 2, 'Add link', '_DIMS_LABEL_ADDLINK'),
(3936, 'system', 2, 'Name or Surname', '_DIMS_LABEL_SEARCH_LPERS'),
(3937, 'system', 2, 'Links to contact', '_DIMS_LABEL_LINK_PSEARCH'),
(3938, 'system', 2, 'Links to company', '_DIMS_LABEL_LINK_TSEARCH'),
(3939, 'system', 2, 'Contact', '_DIMS_LABEL_CONTACT'),
(3940, 'system', 2, 'Contacts', '_DIMS_LABEL_PERSONNE'),
(3941, 'system', 2, 'Documents', '_DOCS'),
(3942, 'system', 2, 'Directory', '_DIMS_LABEL_DIRECTORY'),
(3943, 'system', 2, 'Information Gathering', '_DIMS_LABEL_WATCH'),
(3944, 'system', 2, 'Start Time', '_DIMS_LABEL_HEUREDEB'),
(3945, 'system', 2, '- All -', '_DIMS_ALL'),
(3946, 'system', 2, 'Label', '_DIMS_LABEL'),
(3947, 'system', 2, 'Information as read', 'VALIDATE_VIEW_INFO'),
(3948, 'system', 2, 'Private', '_DIMS_LABEL_VIEWMODE_PRIVATE'),
(3949, 'system', 2, 'Global', '_LABEL_VIEWMODE_GLOBAL'),
(3950, 'system', 2, 'Workspace manager', '_DIMS_LEVEL_GROUPMANAGER'),
(3951, 'system', 2, 'Workspace administrator', '_DIMS_LEVEL_GROUPADMIN'),
(3952, 'system', 2, 'System administrator', '_DIMS_LEVEL_SYSTEMADMIN'),
(3953, 'system', 2, 'Vip', '_DIMS_LABEL_CONT_VIP_V'),
(3954, 'system', 2, 'Type', '_TYPE'),
(3955, 'system', 2, 'Private View: restricion group\r\nDownlink: group and subgroups\r\nAgglomerative: restriction on group and parent groups\r\nGlobal: unrestricted', '_SYSTEM_EXPLAIN_VIEWMODE'),
(3956, 'system', 2, 'In restricted mode, the administrators of sub-groups that use this module will have limited rights', '_SYSTEM_EXPLAIN_ADMINRESTRICTED'),
(3957, 'system', 2, 'Confirm share', '_DIMS_SHARE_VALID'),
(3958, 'system', 2, 'Are you sure you want to delete this campaign ?', '_DIMS_CONFIRM_DELETE_CAMPAIGN'),
(3959, 'system', 2, 'The email address is invalid. \\n There is no @ character \\n A valid email address is like adresse@domaine.com', '_DIMS_JS_EMAIL_ERROR_1'),
(3960, 'system', 2, 'The email address is invalid. \\n It can not be a dot (.) Immediately after @ \\n A valid email address is of type adresse@domaine.com', '_DIMS_JS_EMAIL_ERROR_2'),
(3961, 'system', 2, 'The email address is invalid. \\ N The email address can not end with a dot (.) \\ N A valid email address is of type adresse@domaine.com', '_DIMS_JS_EMAIL_ERROR_3'),
(3962, 'system', 2, 'The email address is invalid. The email address can not contain 2 points (.) follow. A valid email address is of type adresse@domaine.com', '_DIMS_JS_EMAIL_ERROR_4'),
(3963, 'system', 2, 'The field ''<FIELD_LABEL>'' should not be empty', '_DIMS_JS_STRING_ERROR'),
(3964, 'system', 2, 'The field ''<FIELD_LABEL>'' should be a valid integer', '_DIMS_JS_INT_ERROR'),
(3965, 'system', 2, 'The field ''<FIELD_LABEL>'' should be a valid float', '_DIMS_JS_FLOAT_ERROR'),
(3966, 'system', 2, 'The field ''<FIELD_LABEL>'' should be a valid date', '_DIMS_JS_DATE_ERROR'),
(3967, 'system', 2, 'The field ''<FIELD_LABEL>'' should be a valid hour', '_DIMS_JS_TIME_ERROR'),
(3968, 'system', 2, 'You must select a value for field ''<FIELD_LABEL>''', '_DIMS_JS_CHECK_ERROR'),
(3969, 'system', 2, 'The field '''' must have valid color (#ffff00 / yellow / etc.)', '_DIMS_JS_COLOR_ERROR'),
(3970, 'system', 2, 'today', '_DIMS_LABEL_DAY'),
(3971, 'system', 2, 'Tomorow', '_DIMS_LABEL_TOMOROW'),
(3972, 'system', 2, 'yesterday', '_DIMS_LABEL_LASTDAY'),
(3973, 'system', 2, 'Last access', '_DIMS_LABEL_THEREIS'),
(3974, 'system', 2, 'days', '_DIMS_LABEL_DAYS'),
(3975, 'system', 2, 'weeks', '_DIMS_LABEL_WEEKS'),
(3976, 'system', 2, 'At', '_AT'),
(3977, 'system', 2, 'to', '_DIMS_LABEL_A'),
(3978, 'system', 2, 'Security filter - access denied', '_DIMS_SECURITY_ERROR'),
(3979, 'system', 2, 'seconds remaining before the next connection attempt', '_DIMS_REST_SEC'),
(3980, 'system', 2, 'News contacts', '_DIMS_LABEL_NEW_CONT_SINCE'),
(3981, 'system', 2, 'Contacts modify since ', '_DIMS_LABEL_CONT_FICHE_MOD'),
(3982, 'system', 2, 'Event links', '_DIMS_LABEL_LINK_EVT'),
(3983, 'system', 2, 'Generic', '_DIMS_LABEL_LFB_GEN'),
(3984, 'system', 2, 'Business', '_DIMS_LABEL_LFB_MET'),
(3985, 'system', 2, 'Edit link', '_DIMS_LABEL_LFB_MOD_LINK'),
(3986, 'system', 2, 'Link level', '_DIMS_LABEL_LEVEL_LINK'),
(3987, 'system', 2, 'Company card', '_DIMS_LABEL_ENT_FICHE'),
(3988, 'system', 2, 'this link has been deleted', '_DIMS_LABEL_DELETED_LINK'),
(3989, 'system', 2, 'have ', '_DIMS_LABEL_LFB_POSSEDE'),
(3990, 'system', 2, 'field(s)', '_DIMS_LABEL_RULEFIELD_MIN'),
(3991, 'system', 2, 'Send a request for information', '_DIMS_LABEL_DMD_ENVOI'),
(3992, 'system', 2, 'Message', '_DIMS_LABEL_MESSAGE'),
(3993, 'system', 2, 'Information Type', '_DIMS_LABEL_INFO_TYPE'),
(3994, 'system', 2, 'Request information on contact ', '_DIMS_LABEL_DMD_INFO'),
(3995, 'system', 2, 'Categories concerned', '_DIMS_LABEL_CAT_CONC'),
(3996, 'system', 2, 'This contact is now being monitored', '_DIMS_LABEL_WATCH_OK'),
(3997, 'system', 2, 'This contact  is already being monitored', '_DIMS_LABEL_WATCH_EXIST'),
(3998, 'system', 2, 'This personal link already exists', '_DIMS_LABEL_LINK_EXIST'),
(3999, 'system', 2, 'Are you sure ?', '_DIMS_LABEL_CONFIRM_DELETE'),
(4000, 'system', 2, 'Wallpaper', '_DIMS_LABEL_BACKGROUNDIMAGE'),
(4001, 'system', 2, 'Shared', '_DIMS_LABEL_ISSHARED'),
(4002, 'system', 2, 'Tel. Home', '_DIMS_LABEL_TEL_DOMICILE'),
(4003, 'system', 2, 'Photo', '_DIMS_LABEL_PHOTO'),
(4004, 'system', 2, 'business', '_DIMS_MOD_LABEL_BUSINESS'),
(4005, 'system', 2, 'networks', '_DIMS_LABEL_RESEAU'),
(4006, 'system', 2, 'family / friends', '_DIMS_LABEL_FAMILLE_AMI'),
(4007, 'system', 2, 'Other', '_DIMS_LABEL_OTHER'),
(4008, 'system', 2, 'employer', '_DIMS_LABEL_EMPLOYEUR'),
(4009, 'system', 2, 'associate', '_DIMS_LABEL_ASSOCIE'),
(4010, 'system', 2, 'Board of directors', '_DIMS_LABEL_CONSADMIN'),
(4011, 'system', 2, 'department', '_DIMS_LABEL_DEPARTEMENT'),
(4012, 'system', 2, 'Relation', '_DIMS_LABEL_RELATION'),
(4013, 'system', 2, 'linked contact', '_DIMS_LABEL_CT_LINKED'),
(4014, 'system', 2, 'Public comment', '_DIMS_LABEL_COMMENT_GEN'),
(4015, 'system', 2, 'Workspace comment', '_DIMS_LABEL_COMMENT_MET'),
(4016, 'system', 2, 'Private comment', '_DIMS_LABEL_COMMENT_PERS'),
(4017, 'system', 2, 'Comments history', '_DIMS_LABEL_COMMENT_HIST'),
(4018, 'system', 2, 'No comment', '_DIMS_LABEL_NO_COMMENT'),
(4019, 'system', 2, 'Add/Edit comment', '_DIMS_LABEL_ADD_COMMENT'),
(4020, 'system', 2, 'Comment level', '_DIMS_LABEL_COMMENT_LVL'),
(4021, 'system', 2, 'Author', '_DIMS_LABEL_COMMENT_BY'),
(4022, 'system', 2, 'This week', '_DIMS_LABEL_THIS_WEEK'),
(4023, 'system', 2, '2 weeks', '_DIMS_LABEL_15_DAYS'),
(4024, 'system', 2, '1 month', '_DIMS_LABEL_THIS_MONTH'),
(4025, 'system', 2, '3 months', '_DIMS_LABEL_3_MONTHS'),
(4026, 'system', 2, 'Import business cards', '_LABEL_ADMIN_IMPORT_ENT'),
(4027, 'system', 2, 'The first line of the file must be a valid description line.', '_LABEL_ADMIN_IMPORT_ERROR'),
(4028, 'system', 2, 'Number of rows processed', '_LABEL_ADMIN_NBLINES'),
(4029, 'system', 2, 'Contacts created', '_LABEL_ADMIN_NEW_ENT'),
(4030, 'system', 2, 'Existing contacts', '_LABEL_ADMIN_DOUBLE'),
(4031, 'system', 2, 'Contacts not comply', '_LABEL_ADMIN_NB_ERROR'),
(4033, 'system', 2, 'Add appointment', '_DIMS_PLANNING_ADD_RDV'),
(4034, 'system', 2, 'Add event', '_DIMS_PLANNING_ADD_EVT'),
(4035, 'system', 2, 'Edit appointment', '_DIMS_PLANNING_MODIFY_RDV'),
(4036, 'system', 2, 'Edit event', '_DIMS_PLANNING_MODIFY_EVT'),
(4037, 'system', 2, 'Holiday', '_DIMS_LABEL_CONGE'),
(4038, 'system', 2, 'Event properties', '_DIMS_PLANNING_LABEL_EVT'),
(4039, 'system', 2, 'Means of publication', '_DIMS_EVT_RELEASING_SUPPORT'),
(4040, 'system', 2, 'Newsletter category', '_DIMS_EVT_RUB_NEWLETTER'),
(4041, 'system', 2, 'Event with registration', '_DIMS_EVT_ALLOW_FO'),
(4042, 'system', 2, 'Target', '_DIMS_EVT_TARGET'),
(4043, 'system', 2, 'Teaser', '_DIMS_EVT_TEASER'),
(4044, 'system', 2, 'Price', '_DIMS_EVT_PRIX'),
(4045, 'system', 2, 'Conditions for participation', '_DIMS_EVT_CONDITION'),
(4046, 'system', 2, 'Link document', '_DIMS_EVT_DOC'),
(4047, 'system', 2, 'NACE code', '_DIMS_LABEL_ENT_CODE_NACE'),
(4048, 'system', 2, 'List of new companies', '_LABEL_LIST_NEW_ENT'),
(4049, 'system', 2, 'List of known companies', '_LABEL_LIST_ALREADY_KNOWN'),
(4050, 'system', 2, 'Company card', '_LABEL_ENT_FICHE_CORRESP'),
(4051, 'system', 2, 'Contacts concerned', '_DIMS_EVT_CONTACT_PARTICIPATE'),
(4052, 'system', 2, 'Last company import', '_LABEL_LAST_IMPORT'),
(4053, 'system', 2, 'Generic information', '_DIMS_LABEL_INFO_G'),
(4054, 'system', 2, 'Partner', '_DIMS_LABEL_PARTENAIRE'),
(4055, 'system', 2, 'Search contacts', '_DIMS_LABEL_CT_SEARCH_PERS'),
(4056, 'system', 2, 'Meta descriptions', '_DIMS_LABEL_DESCRIPTION_META'),
(4057, 'system', 2, 'found', '_DIMS_LABEL_FOUND'),
(4058, 'system', 2, 'founds', '_DIMS_LABEL_FOUNDS'),
(4059, 'system', 2, 'Search companies', '_DIMS_LABEL_CT_SEARCH_ENT'),
(4060, 'system', 2, 'Contacts registered', '_DIMS_EVT_CONTACT_REGISTER'),
(4061, 'system', 2, 'Event partners', '_DIMS_EVT_PARTNER'),
(4062, 'system', 2, 'Add a meeting', '_DIMS_PLANNING_ADD_RCT'),
(4063, 'system', 2, 'After validation of this event you can assign documents.', '_DIMS_EVT_NO_DOC'),
(4064, 'system', 2, 'Edit meeting', '_DIMS_PLANNING_MODIFY_RCT'),
(4065, 'system', 2, 'Accompanying person', '_DIMS_ACCOMPANY'),
(4066, 'system', 2, 'Met', '_DIMS_MEETED'),
(4067, 'system', 2, 'Please fill at least one search field', '_DIMS_LABEL_ERROR_SCH'),
(4068, 'system', 2, 'No event', '_DIMS_LABEL_NO_EVENT'),
(4069, 'system', 2, 'New tag', '_DIMS_LABEL_NEWTAG'),
(4070, 'system', 2, 'Lines with errors', '_LABEL_ADMIN_IMPORT_LINERROR'),
(4071, 'system', 2, 'Sent', '_DIMS_MSG_SENT'),
(4072, 'system', 2, 'More filters', '_DIMS_LABEL_MORE_FILTERS'),
(4073, 'system', 2, 'No corresponding event', '_DIMS_LABEL_NO_EVENT_CORRESP'),
(4074, 'system', 2, 'Event partner', '_DIMS_LABEL_SPONSOR'),
(4075, 'system', 2, 'Export emails', '_FORMS_EMAIL_EXPORT'),
(4076, 'system', 2, 'Export addresses', '_FORMS_ADR_EXPORT'),
(4077, 'system', 2, 'Used filters', '_DIMS_USED_FILTER'),
(4078, 'system', 2, 'Admin. events', '_DIMS_EVENT_ADMIN'),
(4079, 'system', 2, 'This person has been added to your contacts.', '_DIMS_LABEL_LINK_OK'),
(4080, 'system', 2, 'New companies', '_DIMS_LABEL_NEW_ENT_SINCE'),
(4081, 'system', 2, 'Changed companies', '_DIMS_LABEL_ENT_FICHE_MOD'),
(4082, 'system', 2, 'Access to my personal record', '_DIMS_LABEL_ACCESS_FICHE_PERS'),
(4083, 'system', 2, 'Very important', '_DIMS_LABEL_CONT_VIP_L'),
(4084, 'system', 2, 'Similarities contacts', '_DIMS_LABEL_SIMILAR_CT'),
(4085, 'system', 2, 'Upcoming Events', '_DIMS_LABEL_EVENTS_TO_COME'),
(4086, 'system', 2, 'There is no similar record in the database, you can validate your registration.', '_DIMS_LABEL_NO_SIMILAR'),
(4087, 'system', 2, 'Exact Match', '_DIMS_LABEL_EXACT_CORRESP_SING'),
(4088, 'system', 2, 'Exact Matches', '_DIMS_LABEL_EXACT_CORRESP_PLUR'),
(4089, 'system', 2, 'Same name / Possible surname ', '_DIMS_LABEL_EXACT_NAME'),
(4090, 'system', 2, 'Possible Name and surname', '_DIMS_LABEL_PROX_NP'),
(4091, 'system', 2, 'Matches remote', '_DIMS_LABEL_LESS_PROX_PLUR'),
(4092, 'system', 2, 'Match remote', '_DIMS_LABEL_LESS_PROX_SING'),
(4093, 'system', 2, 'Force saving', '_DIMS_LABEL_FORCE_SAVE'),
(4094, 'system', 2, 'View details about this contact', '_DIMS_LABEL_AFFICH_INF_CT'),
(4095, 'system', 2, 'Match', '_DIMS_LABEL_PROX_CORRESP_SING'),
(4096, 'system', 2, 'Matches', '_DIMS_LABEL_PROX_CORRESP_PLUR'),
(4097, 'system', 2, 'Event administration', '_DIMS_EVENT_ADMIN_SELF'),
(4098, 'system', 2, 'No registration', '_DIMS_LABEL_NO_REGISTRATION'),
(4099, 'system', 2, 'Legend', '_DIMS_LEGEND'),
(4100, 'system', 2, 'Subscribing form with two levels', '_EVENT_FORM_2LEVEL'),
(4101, 'system', 2, 'Confirm registration', '_DIMS_VALID_REGISTER'),
(4102, 'system', 2, 'Begin hour', '_DIMS_LABEL_HEUREDEB_SHORT'),
(4103, 'system', 2, 'End hour', '_DIMS_LABEL_HEUREFIN_SHORT'),
(4104, 'system', 2, 'Click here to view the presentation of this contact.', '_DIMS_TITLE_PRES_PERS'),
(4105, 'system', 2, 'This contact is a partner event.', '_DIMS_TITLE_CT_PART_EVT'),
(4106, 'system', 2, 'Click here to view photo of contact.\r\n', '_DIMS_TITLE_DISP_PHOTO'),
(4107, 'system', 2, 'Keep a close look on this contact', '_DIMS_TITLE_TO_WATCH'),
(4108, 'system', 2, 'Add to your personal contacts.', '_DIMS_TITLE_ADD_TO_CT_PERS'),
(4109, 'system', 2, 'Add a comment (comment will be in historical part).', '_DIMS_TITLE_ADD_COMM'),
(4110, 'system', 2, 'Edit comment', '_DIMS_TITLE_MODIFY_COMMENTAIRE'),
(4111, 'system', 2, 'This contact has been deactivated', '_DIMS_LABEL_FICHE_SUPPR'),
(4112, 'system', 2, 'Deactivate this contact', '_DIMS_LABEL_DESACTIV_SHEET'),
(4113, 'system', 2, 'Activate this contact', '_DIMS_LABEL_ACTIV_SHEET'),
(4114, 'system', 2, 'Announcement document', '_DIMS_EVT_DOC_ANNONCE'),
(4115, 'system', 2, 'Management of registration in two levels', '_DIMS_EVT_MANAGE_LEVEL_TWO'),
(4116, 'system', 2, 'Registration(s)', '_DIMS_LABEL_REGISTRATION_S'),
(4117, 'system', 2, 'Card attached', '_DIMS_LABEL_FICHE_ATTACHED'),
(4118, 'system', 2, 'Ressources internes ', '_DIMS_LABEL_INTERNAL_SOURCES'),
(4121, 'system', 2, 'Add step', '_DIMS_EVT_STEP_ADD_ONE'),
(4120, 'system', 2, 'Management steps', '_DIMS_EVT_STEP_MANAGE'),
(4122, 'system', 2, 'Add file to step', '_DIMS_EVT_STEP_ATTACH_FILE'),
(4123, 'system', 2, 'Edit step', '_DIMS_EVT_STEP_MODIFY'),
(4124, 'system', 2, 'Last import of contacts', '_DIMS_LAST_IMPORT_PERS'),
(4125, 'system', 2, 'Attach a contact as a author of records', '_DIMS_LABEL_PERS_CREATE_IMPPORT'),
(4126, 'system', 2, 'No contact matches this search.', '_DIMS_LABEL_NO_RESP_CONT_SEARCH'),
(4127, 'system', 2, 'Import contacts', '_LABEL_ADMIN_IMPORT_CT'),
(4128, 'system', 2, 'New contacts and companies', '_LABEL_ADMIN_NEW_CTANDENT'),
(4129, 'system', 2, 'New contacts, companies known', '_LABEL_ADMIN_NEW_CT_OLDENT'),
(4130, 'system', 2, 'New companies, contacts known', '_LABEL_ADMIN_NEW_ENT_OLDCT'),
(4131, 'system', 2, 'Contacts and companies known', '_LABEL_ADMIN_NEW_OLDCTENT'),
(4132, 'system', 2, 'Send group mail', '_DIMS_LABEL_EMAIL_SEND'),
(4133, 'system', 2, 'Add this company', '_DIMS_LABEL_ADD_THIS_ENT'),
(4134, 'system', 2, 'Add all', '_DIMS_LABEL_ADD_ALL'),
(4135, 'system', 2, 'New contacts without company links ', '_LABEL_ADMIN_NEW_CT_SS_ENT'),
(4136, 'system', 2, 'Known contacts without company link', '_LABEL_ADMIN_OLD_CT_SS_ENT'),
(4137, 'system', 2, 'This contact has been removed from the import.', '_DIMS_LABEL_DELETED_SHEET_IMP'),
(4138, 'system', 2, 'Information about import contact\r\n', '_DIMS_TITLE_INF_PERS_IMP'),
(4139, 'system', 2, 'Information about import company\r\n', '_DIMS_TITLE_COMP_ENT_IMP'),
(4140, 'system', 2, 'Add contact only', '_DIMS_LABEL_ADD_PERS_ONLY'),
(4141, 'system', 2, 'Update only company information', '_DIMS_LABEL_ADD_OLDENT_ONLY'),
(4142, 'system', 2, 'Contact has been updated', '_DIMS_LABEL_MOD_SHEET_IMP'),
(4143, 'system', 2, 'Contact has been succesfully added', '_DIMS_LABEL_ADDED_SHEET_IMP'),
(4144, 'system', 2, 'Update only Contact information', '_DIMS_LABEL_ADD_OLDPERS_ONLY'),
(4145, 'system', 2, 'Add only company', '_DIMS_LABEL_ADD_ENT_ONLY'),
(4146, 'system', 2, 'not found', '_DIMS_LABEL_NOFOUND'),
(4147, 'system', 2, 'Registration having completed level 1, and pending validation of level 2', '_DIMS_LABEL_REGISTRATION_WAIT_FOR_2_2'),
(4148, 'system', 2, 'Description', '_DIMS_LABEL_DESCRIPTIF'),
(4149, 'system', 2, 'Validating (all documents are not validated)', '_DIMS_LABEL_RUNNING_REGISTRATION'),
(4150, 'system', 2, 'Step validated', '_DIMS_LABEL_VALIDATED_STATE'),
(4151, 'system', 2, 'Step cancelled', '_DIMS_LABEL_CANCELED_STATE'),
(4152, 'system', 2, 'Attachments files', '_DIMS_LABEL_LINKED_DOCS_EVT'),
(4153, 'system', 2, 'From', '_DIMS_LABEL_DOC_FROM'),
(4154, 'system', 2, 'I-Net Portal (front office) ', '_DIMS_LABEL_INET'),
(4155, 'system', 2, 'Usb key', '_DIMS_LABEL_USB_KEY'),
(4156, 'system', 2, 'CD Rom / DVD ', '_DIMS_LABEL_CD_DVD'),
(4157, 'system', 2, 'Email', '_DIMS_LABEL_COURRIER'),
(4158, 'system', 2, 'Date', '_DIMS_LABEL_DATE_RECEPTION_DOC'),
(4159, 'system', 2, 'Cancel validation', '_DIMS_LABEL_STOP_DOC_VALIDATION'),
(4160, 'system', 2, 'Waiting for receipt', '_DIMS_LABEL_WAIT_FOR_DOC'),
(4161, 'system', 2, 'Phases', '_DIMS_LABEL_PHASE'),
(4162, 'system', 2, 'Close registration', '_DIMS_LABEL_CLOSE_INSCRIPTION'),
(4163, 'system', 2, 'Event closed', '_DIMS_LABEL_CLOSED_EVT'),
(4164, 'system', 2, 'Open registration', '_DIMS_LABEL_OPEN_INSCRIPTION'),
(4165, 'system', 2, 'Registration open', '_DIMS_LABEL_OPENED_INSCR_EVT'),
(4166, 'system', 2, 'Registration closed', '_DIMS_LABEL_CLOSED_INSCR_EVT'),
(4167, 'system', 2, 'Document Pending', '_DIMS_LABEL_DOCUMENT_WAIT'),
(4168, 'system', 2, 'Import Outlook© ', '_LABEL_IMPORT_OUTLOOK'),
(4169, 'system', 2, 'Last contact import', '_LABEL_LAST_IMPORT_CT'),
(4170, 'system', 2, 'Empty table import', '_LABEL_CLEAN_IMPORT_TABLE'),
(4171, 'system', 2, 'Attach to a contact', '_DIMS_LINK_CONTACT'),
(4172, 'system', 2, 'Step 1: Import the Outlook © file', '_LABEL_IMPORT_STEP1'),
(4173, 'system', 2, 'Step 3: Comparison of contact', '_LABEL_IMPORT_STEP3'),
(4174, 'system', 2, 'Perhaps there is already a record for this user', '_DIMS_USER_FILE_EXIST'),
(4175, 'system', 2, 'or', '_DIMS_OR'),
(4176, 'system', 2, 'Add new contact', '_DIMS_USER_CREATE_NEW_FILE'),
(4177, 'system', 2, 'Would you attach it to one of these contacts', '_DIMS_USER_WISH_RATTACH_TO_FILE'),
(4178, 'system', 2, 'Add message', '_DIMS_LABEL_ADD_MESSAGE'),
(4179, 'system', 2, 'Text Message (optional)', '_DIMS_CONTENT_EXPLAIN_OPTIONAL'),
(4180, 'system', 2, 'New message', '_DIMS_MSG_CREATE'),
(4181, 'system', 2, 'Mailboxes', '_SYSTEM_LABELICON_MAILBOX'),
(4182, 'system', 2, 'Add mailbox', '_DIMS_LABEL_MAILBOX_ADD'),
(4183, 'system', 2, 'Port', '_DIMS_LABEL_PORT'),
(4184, 'system', 2, 'Edit mailbox', '_DIMS_LABEL_MAILBOX_MODIFY'),
(4185, 'system', 2, 'Addressing messages manually', '_DIMS_WEBMAIL_CHECK_MANUALLY'),
(4186, 'system', 2, 'Uncheck contacts you don''t want to import.', '_IMPORT_INSTRUCTION_STEP4'),
(4187, 'system', 2, 'None of your contacts imported presents similarity with any of existing contacts.', '_IMPORT_NO_SIMILAR_CT'),
(4188, 'system', 2, 'If you skip this step,  new contact will be checked as already present.', '_IMPORT_WARN_STEP3'),
(4189, 'system', 2, 'I want to ignore this contact.', '_IMPORT_NEXT_SIMILAR_CONTACT'),
(4190, 'system', 2, 'My contact does not exists I want to import it.', '_IMPORT_NEW_SIMILAR_CONTACT'),
(4191, 'system', 2, 'Similar contacts', '_IMPORT_TAB_SIMILAR_CONTACT'),
(4192, 'system', 2, 'New contact', '_IMPORT_TAB_NEW_CONTACT'),
(4193, 'system', 2, 'View list', '_IMPORT_VIEW_LISTE'),
(4194, 'system', 2, 'of your contacts are already on the site.', '_IMPORT_CONTACTS_ALREADY_EXISTS'),
(4195, 'system', 2, 'Error : file isn''t correct.', '_IMPORT_ERROR_FILE_NOT_CORRECT'),
(4196, 'system', 2, 'Back to step 1', '_IMPORT_RETURN_TO_STEP1'),
(4197, 'system', 2, 'Similar contact', '_IMPORT_TAB_SIMILAR_CONTACT_SINGLE'),
(4198, 'system', 2, 'Skip this step', '_IMPORT_GO_NEXT_STEP'),
(4199, 'system', 2, 'List of remaining contacts', '_IMPORT_TAB_LAST_CONTACTS'),
(4200, 'system', 2, 'All your contacts are already known.', '_IMPORT_ALL_CONTACTS_ALREADY_EXISTS'),
(4201, 'system', 2, 'Importing your contacts is completed', '_IMPORT_COMPLETE'),
(4202, 'system', 2, 'Contacts imported', '_IMPORT_IMPORTED_CONTACTS'),
(4203, 'system', 2, 'Contacts attached to a company', '_IMPORT_LINKED_CONTACTS'),
(4204, 'system', 2, 'Validation', '_DIMS_LABEL_VALIDATION'),
(4205, 'system', 2, 'Return receipt', '_TICKET_ENABLE_VALIDATION'),
(4206, 'system', 2, 'Deadline validation', '_TICKET_LIMIT_TIME_VALIDATION'),
(4207, 'system', 2, 'Find a delivery address', '_TICKET_SEARCH_ADDRESSEE'),
(4208, 'system', 2, 'State of registration', '_DIMS_LABEL_REGISTRATION_STATE'),
(4209, 'system', 2, 'You are not registered in any event.', '_DIMS_LABEL_NO_EVENT_REGISTRATION'),
(4210, 'system', 2, 'Please log in to access your events.', '_DIMS_LABEL_LOGGIN_TO_SEE_EVT'),
(4211, 'system', 2, 'Registration for this event are closed', '_DIMS_LABEL_FO_REGISTRATION_CLOSE'),
(4212, 'system', 2, 'Subject does not exist.', '_DIMS_TICKET_NO_OBJECT'),
(4213, 'system', 2, 'Date and time', '_EVENT_DATE_AND_TIME'),
(4214, 'system', 2, 'Details', '_EVENT_DETAILS'),
(4215, 'system', 2, 'Created : ', '_DIMS_LABEL_CREATE'),
(4216, 'system', 2, 'Partners', '_DIMS_LABEL_PARTNER'),
(4217, 'system', 2, 'Busin.', '_DIMS_LABEL_COM_MET'),
(4218, 'system', 2, 'Would you want to delete this document ?', '_DIMS_ARE_YOU_SURE_TO_DEL'),
(4219, 'system', 2, 'You want to register for this event.\r\nYou can also register any person of your choice.\r\nWhen you choice the number of people (including yourself), you can fill the forms.', '_DIMS_TEXT_INTRO_FORM1'),
(4220, 'system', 2, 'Additional information', '_DIMS_EVT_INFO_COMPL'),
(4221, 'system', 2, 'Your registration', '_DIMS_EVT_INSCRIPT_SELF'),
(4222, 'system', 2, 'Required Information', '_DIMS_EVT_INFO_OBLIG'),
(4223, 'system', 2, 'To import as', '_IMPORT_USER_WHO_IMPORT'),
(4224, 'system', 2, 'File import', '_IMPORT_DOWNLOAD_FILE'),
(4225, 'system', 2, 'Known contacts', '_IMPORT_KNOWN_CONTACTS'),
(4226, 'system', 2, 'Contacts with similarity', '_IMPORT_CONTACTS_WITH_SIMILAR_PROFIL'),
(4227, 'system', 2, 'Unknown contacts', '_IMPORT_UNKNOWN_CONTACTS'),
(4228, 'system', 2, 'Summary', '_IMPORT_SUMMARY'),
(4229, 'system', 2, 'of your contacts have been ignored', '_IMPORT_COUNT_OF_IGNORED_CONTACTS'),
(4230, 'system', 2, 'remaining import contacts', '_IMPORT_CONTACTS_RESTANT'),
(4231, 'system', 2, 'Make a connection to the company', '_IMPORT_ENT_WHO_IMPORT'),
(4232, 'system', 2, 'New contacts', '_DIMS_LABEL_NEW_SHEET_SINCE'),
(4233, 'system', 2, 'Contacts modify', '_DIMS_LABEL_MODIFIED_SHEET_SINCE'),
(4234, 'system', 2, 'Import missions ', '_LABEL_IMPORT_MISSIONS'),
(4235, 'system', 2, 'View all contacts', '_DIMS_LABEL_SEE_ALL_SHEET'),
(4236, 'system', 2, 'Return to the list of registrations', '_DIMS_EVT_BACK_REGISTRATION'),
(4237, 'system', 2, 'Companies known', '_IMPORT_KNOWN_TIER'),
(4238, 'system', 2, 'Companies with similar', '_IMPORT_TIER_WITH_SIMILAR_PROFIL'),
(4239, 'system', 2, 'Unknown companies', '_IMPORT_UNKNOWN_TIER'),
(4240, 'system', 2, 'I want to skip this company', '_IMPORT_NEXT_SIMILAR_TIER'),
(4241, 'system', 2, 'List of remaining companies', '_IMPORT_TAB_LAST_TIER'),
(4242, 'system', 2, 'similar company', '_IMPORT_TAB_SIMILAR_TIER_SINGLE'),
(4243, 'system', 2, 'Similar Companies', '_IMPORT_TAB_SIMILAR_TIER'),
(4244, 'system', 2, 'Companies imported', '_IMPORT_IMPORTED_TIER'),
(4245, 'system', 2, 'None of your companies to import presents similarity with existing companies.\r\n', '_IMPORT_NO_SIMILAR_TIERS'),
(4246, 'system', 2, 'New company', '_IMPORT_TAB_NEW_COMPANY'),
(4247, 'system', 2, 'If you skip this step, the new company will be checkd as already present.', '_IMPORT_TIERS_WARN_STEP3'),
(4248, 'system', 2, 'Which [x] updated companies', '_IMPORT_UPDATED_TIERS'),
(4249, 'system', 2, 'Mission', '_DIMS_IMPORT_LABEL_MISSION'),
(4250, 'system', 2, 'Create a new contact from import', '_DIMS_IMPORT_CT_NO_SAME'),
(4251, 'system', 2, 'This contact was already linked to this event', '_IMPORT_TEXT_CT_ALREADY_ATTACHED'),
(4252, 'system', 2, 'No events match your request.\r\nBack to list of events.', '_DIMS_FRONT_TEXT_NO_EVENT'),
(4253, 'system', 2, 'Related sending', '_DIMS_LABEL_NEWSLETTER_LIST_ARTICLE'),
(4254, 'system', 2, 'enable this newsletter', '_DIMS_LABEL_NEWSLETTER_ACTIVE'),
(4255, 'system', 2, 'List of Newsletter', '_DIMS_LABEL_NEWSLETTER_LIST'),
(4256, 'system', 2, 'You don''t manage any newsletter.', '_DIMS_LABEL_NO_NEWSLETTER'),
(4257, 'system', 2, 'Number of newsletters', '_DIMS_LABEL_NEWSLETTER_NB'),
(4258, 'system', 2, 'Number of subscribers', '_DIMS_LABEL_NEWSLETTER_NBINSC'),
(4259, 'system', 2, 'Number of subscription', '_DIMS_LABEL_NEWSLETTER_NBDMDINSC'),
(4260, 'system', 2, 'Send another newsletter', '_DIMS_LABEL_NEWSLETTER_ADD_ARTICLE'),
(4261, 'system', 2, 'There is no item', '_DIMS_LABEL_NEWSLETTER_NO_ARTICLE'),
(4262, 'system', 2, 'Existing lists', '_DIMS_NEWSLETTER_LIST_INSC'),
(4263, 'system', 2, 'List of new requests', '_DIMS_NEWSLETTER_LIST_DMDINSC'),
(4264, 'system', 2, 'Sent on', '_DIMS_LABEL_SEND_DATE'),
(4265, 'system', 2, 'Attachment file', '_DIMS_LABEL_PIECE_JOINTE'),
(4266, 'system', 2, 'List of subscribers to newsletter', '_DIMS_NEWSLETTER_INSCRITS'),
(4267, 'system', 2, 'There is no subscriber to this newsletter.', '_DIMS_NEWSLETTER_NO_INSCRITS'),
(4268, 'system', 2, 'Unsubscribe date', '_DIMS_LABEL_DATE_UNREGISTRATION'),
(4269, 'system', 2, 'Subscribe this contact to newsletter', '_DIMS_LABEL_NEWSLETTER_RESUBSCRIBE'),
(4270, 'system', 2, 'Your newsletters', '_DIMS_LABEL_YOUR_NEWSLETTERS'),
(4271, 'system', 2, 'Clic here to unsubscribe', '_DIMS_LABEL_NEWSLETTER_DESABONNE'),
(4272, 'system', 2, 'You are not subscribed to any newsletters.', '_DIMS_LABEL_NO_NEWSLETTER_ABONN'),
(4273, 'system', 2, 'All events', '_DIMS_LABEL_EVT_ALL'),
(4274, 'system', 2, 'Email 2', '_DIMS_LABEL_EMAIL2'),
(4275, 'system', 2, 'Email 3', '_DIMS_LABEL_EMAIL3'),
(4276, 'system', 2, 'Create group', '_DIMS_LABEL_GROUP_CREATE'),
(4277, 'system', 2, 'Step 1: import file', '_LABEL_IMPORT_TIER_STEP1'),
(4278, 'system', 2, 'Group of contacts', '_DIMS_LABEL_CONTACT_GOUPS'),
(4279, 'system', 2, 'Date of starting registration', '_DIMS_EVT_DATE_OPEN_REGISTER'),
(4280, 'system', 2, 'Date of registration', '_DIMS_LABEL_DATE_REGISTRATION'),
(4281, 'system', 2, 'Refuse registration', '_DIMS_LABEL_REFUSED_REGISTRATION'),
(4282, 'system', 2, 'No corresponding newsletter', '_DIMS_LABEL_NEWSLETTER_NONE'),
(4283, 'system', 2, 'To validate the registration you must attach this registration to existing contact. If no contact corresponds, you can create a contact from it.', '_DIMS_NEWSLETTER_TEXT_ATTACH'),
(4284, 'system', 2, 'Create a new contact from the registration', '_DIMS_NEWSLETTER_NEW_CONTACT'),
(4285, 'system', 2, 'gallery', '_DIMS_EVENT_LABEL_IMAGE_GALLERY'),
(4286, 'system', 2, 'Newsletter is enable', '_DIMS_NEWSLETTER_ACTIVE'),
(4287, 'system', 2, 'Newsletter is disabled', '_DIMS_NEWSLETTER_INACTIVE'),
(4288, 'system', 2, 'Edit event', '_DIMS_LABEL_EDIT_EVT'),
(4289, 'system', 2, 'Edit', '_DIMS_LABEL_EDIT'),
(4290, 'system', 2, 'Go', '_DIMS_LABEL_GO'),
(4291, 'system', 2, 'Thank you', '_DIMS_TEXT_FORGOT_PASSWORD1'),
(4292, 'system', 2, 'New password have been succesfully created, you will receive it by email as soon as possible.', '_DIMS_TEXT_FORGOT_PASSWORD2'),
(4293, 'system', 2, 'Thre is no attachement file to this step.', '_DIMS_LABEL_EVENT_STEP_NODOC'),
(4294, 'system', 2, 'The size of the image is too large (20MB max)', '_DIMS_LABEL_ERROR_TOLARGE_IMG'),
(4295, 'system', 2, 'The file size is too large', '_DIMS_LABEL_ERROR_TOLARGE_FILE'),
(4296, 'system', 2, 'CAUTION: Do not paste texts from Microsoft Word directly into the edit box. Please, first paste it into a notepad.', '_DIMS_LABEL_DONT_USE_WORD'),
(4297, 'system', 2, 'Edit mailing list', '_DIMS_LABEL_MODIF_MAILINGLIST'),
(4298, 'system', 2, 'List attached to', '_DIMS_LABEL_NEWSLETTER_LIST_LINKEDTO'),
(4299, 'system', 2, 'Add e-mail', '_DIMS_LABEL_MAILING_ADD_EMAIL'),
(4300, 'system', 2, 'List of attached emails', '_DIMS_LABEL_MAILING_EMAIL_LIST'),
(4301, 'system', 2, 'There is no email for this list', '_DIMS_LABEL_MAILING_NO_EMAIL'),
(4302, 'system', 2, 'Mailing lists attached to this newsletter', '_DIMS_LABEL_LIST_MAILING_LINKED'),
(4303, 'system', 2, 'Contacts linked to this mailing list', '_DIMS_LABEL_NEWSLETTER_CT_LINKED'),
(4304, 'system', 2, 'Attach a mailing list', '_DIMS_LABEL_MAILING_TO_ATTACH'),
(4305, 'system', 2, 'Manage mailing lists', '_DIMS_NEWSLETTER_GESTION_MAILING'),
(4306, 'system', 2, 'Your mailing lists', '_DIMS_NEWSLETTER_YOUR_MAILING_LIST'),
(4307, 'system', 2, 'Add mailing list', '_DIMS_NEWSLETTER_ADD_LIST_MAILING'),
(4308, 'system', 2, 'Attach this list to newsletter', '_DIMS_LABEL_LINK_NEWSLETTER_MAILING'),
(4309, 'system', 2, 'Signature', '_DIMS_LABEL_SIGNATURE'),
(4310, 'system', 2, 'Unanswered email', '_DIMS_LABEL_EMAIL_NOREPLY'),
(4311, 'system', 2, 'No group attached', '_DIMS_LABEL_NO_GROUP_ATTACHED'),
(4312, 'system', 2, 'Add group', '_DIMS_LABEL_ADD_CT_GROUP'),
(4313, 'system', 2, 'Add contact to group', '_DIMS_LABEL_LINK_CT_GROUP'),
(4314, 'system', 2, 'Group list', '_DIMS_LABEL_GROUP_LIST'),
(4315, 'system', 2, 'Name of group', '_DIMS_LABEL_CTGROUP_TITLE'),
(4316, 'system', 2, 'Group view', '_DIMS_LABEL_CTGROUP_VIEW'),
(4317, 'system', 2, 'List of private groups', '_DIMS_LABEL_LIST_PRIVATE_CT'),
(4866, 'system', 1, 'Voir les doublons d''email', '_SEE_DUPLICATE_EMAIL'),
(4319, 'system', 2, 'Establishments', '_TRANSF_LABEL_ETAB'),
(4320, 'system', 2, 'Module transfrontalier ', '_TRANSF_LABEL_MODULE_NAME'),
(4321, 'system', 2, 'Changed contacts', '_DIMS_LABEL_MODIFY_SHEET_SINCE'),
(4322, 'system', 2, 'Link phase', '_DIMS_LABEL_LINK_PARENT_PHASE'),
(4323, 'system', 2, 'Link task', '_DIMS_LABEL_LINK_PARENT_TASK'),
(4324, 'system', 2, 'Contact attached', '_DIMS_LABEL_PROJ_PERS_ATTACHED'),
(4325, 'system', 2, 'View the list of participants in project phases', '_DIMS_LABEL_PROJ_VIEW_PHASEP'),
(4326, 'system', 2, 'View the list of participants in project tasks', '_DIMS_LABEL_PROJ_VIEW_PHASET'),
(4327, 'system', 2, 'Apply for participation', '_DIMS_LABEL_PROJ_DMD_PARTICIPATE'),
(4328, 'system', 2, 'Ask for registration project', '_DIMS_TITLE_PROJ_TICKET_DMD_INSC'),
(4329, 'system', 2, 'Available Files', '_DIMS_FAIR_AVAILABLE_FILE'),
(4330, 'system', 2, 'Step description', '_DIMS_FAIR_STEP_DESCRIPTION'),
(4331, 'system', 2, 'Training', '_DIMS_PROJECT_TYPE_FORMATION'),
(4332, 'system', 2, 'Registrations', '_DIMS_LABEL_INSCRIPTION'),
(4333, 'system', 2, 'Number of speakers required', '_DIMS_PROJECT_NB_INTERV'),
(4334, 'system', 2, 'Action without asked for registration or effective registration', '_DIMS_LABEL_PROJET_NOINSC_OR_DMD'),
(4335, 'system', 2, 'All registration are validated', '_DIMS_LABEL_PROJET_ALLINSCOK'),
(4337, 'system', 2, 'All entries validated but not enough registered', '_DIMS_LABEL_PROJET_NOTENOUGHTINSC'),
(4338, 'system', 2, 'Not enough registration', '_DIMS_LABEL_PROJET_ORANGE'),
(4339, 'system', 2, 'All registrations are canceled by the user', '_DIMS_LABEL_PROJET_INSCRED'),
(4340, 'system', 2, 'An ask for this action is already underway.', '_DIMS_LABEL_DMD_WAITING'),
(4341, 'system', 2, 'Your request has been registered.', '_DIMS_LABEL_DMD_REGISTERED'),
(4342, 'system', 2, 'Your registration is effective.', '_DIMS_LABEL_YOUR_INSC_OK'),
(4343, 'system', 2, 'Your registration is being validated.', '_DIMS_LABEL_YOUR_INSC_WAIT'),
(4344, 'system', 2, 'Your registration is refused.', '_DIMS_LABEL_YOUR_INSC_REFUSED'),
(4345, 'system', 2, 'You canceled your registration.', '_DIMS_LABEL_YOUR_INSC_DEL'),
(4346, 'system', 2, 'Find a company to link', '_DIMS_LABEL_FIND_ENT_TO_LINK'),
(4347, 'system', 2, 'Standard', '_DIMS_LABEL_STANDARD'),
(4348, 'system', 2, 'You have no action to perform.\r\n', '_DIMS_LABEL_PROJET_NO_ACTION'),
(4349, 'system', 2, 'Actions to perform', '_DIMS_LABEL_PROJET_ACTION_TODO'),
(4350, 'system', 2, 'Registration to validate', '_DIMS_LABEL_PROJET_DMD_TODO'),
(4351, 'system', 2, 'Events share', '_DIMS_LABEL_SHARE_PLANNING'),
(4352, 'system', 2, 'CONTACT DETAILS', '_DIMS_PERS_COORD'),
(4353, 'system', 1, 'Inscription', '_DIMS_LABEL_REGISTRATION'),
(4354, 'system', 2, 'Registration', '_DIMS_LABEL_REGISTRATION'),
(4355, 'system', 1, '&Eacute;v&egrave;nements pass&eacute;s', '_DIMS_LABEL_VIEW_PAST_EVENT'),
(4356, 'system', 2, 'View past events', '_DIMS_LABEL_VIEW_PAST_EVENT'),
(4357, 'system', 1, 'Date de publication', '_DIMS_EVT_RELEASING_DATE'),
(4358, 'system', 2, 'Publication date', '_DIMS_EVT_RELEASING_DATE'),
(4361, 'system', 1, 'Suppression l''expression de recherche', '_LABEL_DELETE_QUERY'),
(4362, 'system', 2, 'Delete query', '_LABEL_DELETE_QUERY'),
(4363, 'system', 1, 'Ajouter des parenth&eacute;ses aux mots ou phrases s&eacute;lectionn&eacute;s', '_LABEL_ADD_PARENTHESIS'),
(4364, 'system', 2, 'Adding parenthesis to the words or phrases selected', '_LABEL_ADD_PARENTHESIS'),
(4365, 'system', 1, 'Votre recherche', '_YOUR_SEARCH'),
(4366, 'system', 2, 'Your search', '_YOUR_SEARCH'),
(4367, 'system', 2, 'February', '_FEBRUARY'),
(4368, 'system', 1, 'Champ libre', '_DIMS_FREE_FIELD'),
(4369, 'system', 2, 'Free field', '_DIMS_FREE_FIELD'),
(4370, 'system', 1, 'Redirection vers une url', '_LABEL_REDIRECT_URL'),
(4371, 'system', 2, 'URL redirect', '_LABEL_REDIRECT_URL'),
(4372, 'system', 1, 'Nouvelle fenêtre', '_LABEL_NEW_WINDOW'),
(4373, 'system', 2, 'New window', '_LABEL_NEW_WINDOW'),
(4374, 'system', 1, 'Email d''envoi de la newsletter', '_NEWSLETTER_SENDER_EMAIL'),
(4375, 'system', 2, 'Newsletter sender email', '_NEWSLETTER_SENDER_EMAIL'),
(4376, 'system', 1, 'Entete d''inscription', '_NEWSLETTER_HEADER_REGISTRATION'),
(4377, 'system', 2, 'Header registration', '_NEWSLETTER_HEADER_REGISTRATION'),
(4378, 'system', 1, 'Bas de page d''enregistrement', '_NEWSLETTER_FOOTER_REGISTRATION'),
(4379, 'system', 2, 'Footer registration', '_NEWSLETTER_FOOTER_REGISTRATION'),
(4380, 'system', 1, 'sujet message d''acceptation', '_NEWSLETTER_ACCEPTED_SUBJECT'),
(4381, 'system', 2, 'Accepted response subject', '_NEWSLETTER_ACCEPTED_SUBJECT'),
(4382, 'system', 1, 'Contenu du message d''acceptation', '_NEWSLETTER_ACCEPTED_CONTENT'),
(4383, 'system', 2, 'Accepted response content', '_NEWSLETTER_ACCEPTED_CONTENT'),
(4384, 'system', 1, 'Sujet du message de dÃ©sinscription', '_NEWSLETTER_UNSUBSCRIBE_SUBJECT'),
(4385, 'system', 2, 'Unsubscribe response subject', '_NEWSLETTER_UNSUBSCRIBE_SUBJECT'),
(4386, 'system', 1, 'Contenu du message de desinscription', '_NEWSLETTER_UNSUBSCRIBE_CONTENT'),
(4387, 'system', 2, 'Unsubscribe response content', '_NEWSLETTER_UNSUBSCRIBE_CONTENT'),
(4388, 'system', 1, 'Texte de confirmation d''inscription sur site', '_NEWSLETTER_MESSAGE_REGISTRATION'),
(4389, 'system', 2, 'Confirmation text on site', '_NEWSLETTER_MESSAGE_REGISTRATION'),
(4390, 'system', 1, 'Espace courant', '_DIMS_LABEL_CURRENT_WORKSPACE'),
(4391, 'system', 2, 'Current workspace', '_DIMS_LABEL_CURRENT_WORKSPACE'),
(4392, 'system', 1, 'Mission', '_DIMS_MISSIONS'),
(4393, 'system', 2, 'Trade mission', '_DIMS_MISSIONS'),
(4394, 'system', 1, 'Salon', '_DIMS_PLANNING_SALON'),
(4395, 'system', 2, 'Seminar', '_DIMS_PLANNING_SALON'),
(4396, 'system', 1, 'Conf&eacute;rence', '_DIMS_PLANNING_CONFERENCE'),
(4397, 'system', 2, 'Conference', '_DIMS_PLANNING_CONFERENCE'),
(4398, 'system', 1, 'Foire', '_DIMS_PLANNING_FAIR'),
(4399, 'system', 2, 'Trade fair', '_DIMS_PLANNING_FAIR'),
(4400, 'system', 1, 'Rencontre entreprise', '_DIMS_PLANNING_RENCONTRE_ENT'),
(4401, 'system', 2, 'Meeting', '_DIMS_PLANNING_RENCONTRE_ENT'),
(4402, 'system', 1, 'Rdv interne', '_DIMS_PLANNING_RDV_INT'),
(4403, 'system', 2, 'Internal meeting', '_DIMS_PLANNING_RDV_INT'),
(4404, 'system', 1, 'Visites et d&eacute;l&eacute;gations', '_DIMS_EVENT_VISITS'),
(4405, 'system', 2, 'Visits and delegations', '_DIMS_EVENT_VISITS'),
(4406, 'system', 1, 'Journ&eacute;e d''opportunit&eacute;s', '_DIMS_EVENT_OPPORTUNITIES'),
(4407, 'system', 2, 'Opportunity day', '_DIMS_EVENT_OPPORTUNITIES'),
(4408, 'system', 1, 'CV de votre profile', '_DIMS_CV_FOR_PROFILE'),
(4409, 'system', 2, 'CV for your personal profile', '_DIMS_CV_FOR_PROFILE'),
(4410, 'system', 1, 'Votre CV', '_DIMS_ADD_YOUR_CV'),
(4411, 'system', 2, 'Add CV', '_DIMS_ADD_YOUR_CV'),
(4413, 'system', 2, 'Manage your mailing lists', '_DIMS_MAILING_MANAGE_LIST'),
(4414, 'system', 1, 'Dernier(s) envoi(s) effectu&eacute;(s)', '_DIMS_MAILING_LAST_SENDING'),
(4415, 'system', 2, 'Last sendings', '_DIMS_MAILING_LAST_SENDING'),
(4416, 'system', 1, 'Vous n''avez effectu&eacute; aucun envoi.', '_DIMS_MAILING_NO_SENDING'),
(4417, 'system', 2, 'You have not sent email yet.', '_DIMS_MAILING_NO_SENDING'),
(4418, 'system', 1, 'Envois cr&eacute;&eacute;s', '_DIMS_MAILING_SENDINGS_CREATED'),
(4419, 'system', 1, 'Semaine', '_DIMS_WEEK'),
(4420, 'system', 2, 'Week', '_DIMS_WEEK'),
(4421, 'system', 1, 'Mois', '_DIMS_MONTH'),
(4422, 'system', 2, 'Month', '_DIMS_MONTH'),
(4423, 'system', 1, 'INFOS GENERALES', '_DIMS_PERS_INFOG'),
(4424, 'system', 2, 'GENERERAL INFORMATION', '_DIMS_PERS_INFOG'),
(4425, 'system', 1, 'Il existe des champs partag&eacute;s, acceptez vous le partage ?', '_DIMS_CONTACT_ACCEPT_SHARE'),
(4426, 'system', 2, 'There is shared data available. Would you like to accept it ?', '_DIMS_CONTACT_ACCEPT_SHARE'),
(4427, 'system', 1, 'Il n''y a aucune liste.', '_DIMS_MAILING_NO_LIST'),
(4428, 'system', 2, 'There is no item.', '_DIMS_MAILING_NO_LIST'),
(4429, 'system', 1, 'Attention : le fichier doit obligatoirement être au format text ou excel (.txt, .xls ou .xlsx)', '_DIMS_LABEL_NEWSLETTER_IMPORT_DANGER'),
(4430, 'system', 2, 'Caution : only Outlook files in text or excel (.txt, .xls, xlsx) are supported. The imported contacts will not be saved in the e-mail. So it is recommended that you send your e-mail after importing your contacts.\r\n', '_DIMS_LABEL_NEWSLETTER_IMPORT_DANGER'),
(4431, 'system', 1, 'Envoi li&eacute; &agrave; la newsletter', '_DIMS_NEWSLETTER_MAILLINKED'),
(4432, 'system', 2, 'Mail linked to newsletter', '_DIMS_NEWSLETTER_MAILLINKED'),
(4433, 'system', 1, 'Ajouter &agrave; la To do liste', '_ADDTO_DO'),
(4434, 'system', 2, 'To do list', '_ADDTO_DO'),
(4435, 'system', 1, 'Ajouter un d&eacute;l&eacute;gu&eacute;', '_ADD_DELEGUE_STAND'),
(4436, 'system', 2, 'Add a delegue', '_ADD_DELEGUE_STAND'),
(4437, 'system', 1, 'Importer une liste d''emails', '_DIMS_LABEL_NEWSLETTER_IMPORT'),
(4438, 'system', 2, 'Import a mailing list', '_DIMS_LABEL_NEWSLETTER_IMPORT'),
(4439, 'system', 1, 'Personnel', '_DIMS_LABEL_PERSO'),
(4440, 'system', 2, 'Personal', '_DIMS_LABEL_PERSO'),
(4441, 'system', 1, 'Foires DCE', '_DIMS_PLANNING_FAIR_STEPS'),
(4442, 'system', 2, 'DCE Fairs', '_DIMS_PLANNING_FAIR_STEPS'),
(4443, 'system', 1, 'Zone d''intérêts géographique', '_DIMS_LABEL_GEOGRAPHIC_AREA'),
(4444, 'system', 2, 'Geographic Area of Interest', '_DIMS_LABEL_GEOGRAPHIC_AREA'),
(4445, 'system', 2, 'Delete this contact form the list.', '_IMPORT_SUPPR_SIMILAR_CONTACT'),
(4446, 'system', 2, 'contacts with similarities waiting for your validation.', '_DIMS_LABEL_IMPORT_WITH_SIM'),
(4447, 'system', 2, 'contacts with similarities found in database. You can validate them after the import process.', '_IMPORT_SIMILAR_CT'),
(4448, 'system', 1, 'contacts similaires ont &eacute;t&eacute; trouv&eacute;s dans la base de donn&eacute;es. Vous pourrez les traiter directement &agrave; la fin de cet import ou par la suite &agrave; partir de l''interface "bureau". ', '_IMPORT_SIMILAR_CT'),
(4449, 'system', 1, 'contacts avec similitudes sont en attente de validation.', '_DIMS_LABEL_IMPORT_WITH_SIM'),
(4450, 'system', 1, 'Supprimer ce contact de la liste.', '_IMPORT_SUPPR_SIMILAR_CONTACT'),
(4451, 'system', 1, 'entreprises avec similitudes sont en attente de validation.', '_DIMS_LABEL_IMPORT_ENT_WITH_SIM'),
(4452, 'system', 1, 'Histo. recherche', '_SEARCHHISTORY'),
(4453, 'system', 2, 'Search history', '_SEARCHHISTORY'),
(4454, 'system', 1, 'Histo. des tags', '_TAGHISTORY'),
(4455, 'system', 2, 'Tags history', '_TAGHISTORY'),
(4456, 'system', 1, 'Haute', '_DIMS_HIGH'),
(4457, 'system', 2, 'High', '_DIMS_HIGH'),
(4458, 'system', 1, 'Basse', '_DIMS_LOW'),
(4459, 'system', 2, 'Low', '_DIMS_LOW'),
(4460, 'system', 1, 'Carte g&eacute;ographique', '_DIMS_LABEL_MAP'),
(4461, 'system', 2, 'Map', '_DIMS_LABEL_MAP'),
(4462, 'forms', 1, 'Visible sur le site', '_FORMS_FIELD_CMS_VIEW'),
(4463, 'forms', 2, 'Visible in web site', '_FORMS_FIELD_CMS_VIEW'),
(4464, 'forms', 1, 'Search sur le site', '_FORMS_FIELD_CMS_SEARCH'),
(4465, 'forms', 2, 'Search in web site', '_FORMS_FIELD_CMS_SEARCH'),
(4468, 'system', 1, 'Tous', '_DIMS_ALLS'),
(4469, 'system', 2, 'All', '_DIMS_ALLS'),
(4470, 'system', 1, 'Votre recherche pour {EXPR} a donné {NBRESULT} résultat(s)', '_DIMS_LABEL_EXPRESSION_RESULTAT'),
(4471, 'system', 2, 'Your research for ''{EXPR} has {NBRESULT} result(s)', '_DIMS_LABEL_EXPRESSION_RESULTAT'),
(4472, 'system', 1, 'Admin. Emails', '_DIMS_ADMIN_EVENTS_MAILS'),
(4473, 'system', 2, 'Email management', '_DIMS_ADMIN_EVENTS_MAILS'),
(4474, 'system', 1, 'Mail envoy&eacute; au demandeur &agrave; la fin de sa proc&eacute;dure de demande d''inscription (formulaire niveau 1)', '_EVENTS_MAIL_TO_DMD_INSC_NIV1'),
(4475, 'system', 2, 'Mail sent to subscriber at the end of first level subscription process', '_EVENTS_MAIL_TO_DMD_INSC_NIV1'),
(4476, 'system', 1, 'Email de contact pour les events', '_EVENTS_SENDER_EMAIL'),
(4477, 'system', 2, 'Contact email for events', '_EVENTS_SENDER_EMAIL'),
(4478, 'system', 1, 'Mail envoy&eacute; &agrave; un organisteur lors des demandes d''inscription (formulaire niveau 1)', '_EVENTS_MAIL_TO_ORG_INSC_NIV1'),
(4479, 'system', 2, 'Mail sent to manager for inscription requests (level 1 form)', '_EVENTS_MAIL_TO_ORG_INSC_NIV1'),
(4480, 'system', 1, 'Date limite', '_INFOS_LIMIT_DATE'),
(4481, 'system', 2, 'End of validation', '_INFOS_LIMIT_DATE'),
(4482, 'system', 1, 'Mail envoy&eacute; pour avertir le demandeur que sa demande est accept&eacute;e (niveau 1, un autre mail est envoy&eacute; si l''&eacute;v&egrave;nement a 2 niveaux, voir (*))', '_EVENTS_MAIL_VALID_INSC_NIV1'),
(4483, 'system', 2, 'Mail sent for first level subscription agreement (there is an other mail if it''s a 2 levels event, see (*))', '_EVENTS_MAIL_VALID_INSC_NIV1'),
(4484, 'system', 1, 'Mail envoy&eacute; pour avertir le demandeur que sa demande est refus&eacute;e (niveau 1)', '_EVENTS_MAIL_UNVALID_INSC_NIV1'),
(4485, 'system', 2, 'Mail sent for first level subscription disagreement', '_EVENTS_MAIL_UNVALID_INSC_NIV1'),
(4486, 'system', 1, '(*) Mail envoy&eacute; pour avertir le demandeur que sa demande est accept&eacute;e (email envoy&eacute; si l''&eacute;v&egrave;nement a 2 niveaux d''inscription)', '_EVENTS_MAIL_VALID_INSC_NIV2'),
(4487, 'system', 2, '(*) Mail sent for first level subscription agreement (mail sent if it''s a 2 levels event)', '_EVENTS_MAIL_VALID_INSC_NIV2'),
(4488, 'system', 1, 'Mail envoy&eacute; &agrave; l''organisateur quand un inscrit d&eacute;pose un document (niveau 2)', '_EVENTS_MAIL_DEPOT_DOC'),
(4489, 'system', 2, 'Mail sent to manager when a user uploads a document', '_EVENTS_MAIL_DEPOT_DOC'),
(4490, 'system', 1, 'Mail envoy&eacute; &agrave; l''utilisateur lors du rejet d''un document (niveau 2)', '_EVENTS_MAIL_REJET_DOC'),
(4491, 'system', 2, 'Mail sent to user when a document refused', '_EVENTS_MAIL_REJET_DOC'),
(4492, 'system', 1, 'Mail envoy&eacute; &agrave; l''utilisateur en cas de validation d''une &eacute;tape (niveau 2)', '_EVENTS_MAIL_ACCEPT_STEP'),
(4493, 'system', 2, 'Mail sent to user when a step validated', '_EVENTS_MAIL_ACCEPT_STEP'),
(4494, 'system', 1, 'Mail envoy&eacute; &agrave; l''utilisateur en cas d''invalidation d''une &eacute;tape (niveau 2)', '_EVENTS_MAIL_NOT_ACCEPT_STEP'),
(4495, 'system', 2, 'Mail sent to user when a step unvalidated', '_EVENTS_MAIL_NOT_ACCEPT_STEP'),
(4496, 'system', 1, 'Validation finale d''une inscription de niveau 2', '_EVENTS_MAIL_NIV2_VALIDATION'),
(4497, 'system', 2, 'Final agreement for level 2', '_EVENTS_MAIL_NIV2_VALIDATION'),
(4498, 'system', 1, 'Mail envoy&eacute; &agrave; l''utilisateur en cas d''alerte (changement dans les dates ou le descriptif d''un &eacute;v&egrave;nement)', '_EVENTS_MAIL_ALERTE_MOD'),
(4499, 'system', 2, 'Mail sent to user when an alert occures (when date or description changes)', '_EVENTS_MAIL_ALERTE_MOD'),
(4500, 'system', 1, 'Cl&eacute; d''activation du service HTTP de r&eacute;ception d''emails', '_DIMS_LABEL_ACTIVATED_HTTPEMAIL_KEY'),
(4501, 'system', 2, 'Code to enable input e-mails web service', '_DIMS_LABEL_ACTIVATED_HTTPEMAIL_KEY'),
(4502, 'system', 1, 'heures', '_DIMS_LABEL_HOURS'),
(4503, 'system', 2, 'hours', '_DIMS_LABEL_HOURS'),
(4504, 'system', 1, 'minutes', '_DIMS_LABEL_MINUTES'),
(4505, 'system', 2, 'minutes', '_DIMS_LABEL_MINUTES'),
(4506, 'system', 1, 'jours', '_DIMS_LABEL_DAYS'),
(4507, 'system', 2, 'days', '_DIMS_LABEL_DAYS'),
(4508, 'system', 1, 'semaines', '_DIMS_LABEL_WEEKS'),
(4509, 'system', 2, 'weeks', '_DIMS_LABEL_WEEKS'),
(4510, 'system', 1, 'secondes', '_DIMS_LABEL_SECONDS'),
(4511, 'system', 2, 'seconds', '_DIMS_LABEL_SECONDS'),
(4512, 'system', 1, 'ann&eacute;s', '_DIMS_LABEL_YEARS'),
(4513, 'system', 2, 'years', '_DIMS_LABEL_YEARS'),
(4514, 'system', 1, 'mois', '_DIMS_LABEL_MONTHS'),
(4515, 'system', 2, 'months', '_DIMS_LABEL_MONTHS'),
(4516, 'system', 1, 'Il y a', '_DIMS_LABEL_THERE_IS'),
(4517, 'system', 2, '', '_DIMS_LABEL_THERE_IS'),
(4518, 'system', 1, 'Dans', '_DIMS_LABEL_IN'),
(4519, 'system', 2, '', '_DIMS_LABEL_IN'),
(4520, 'system', 1, '', '_DIMS_LABEL_FROM_NOW'),
(4521, 'system', 2, 'from now', '_DIMS_LABEL_FROM_NOW'),
(4522, 'system', 1, 'A la une', '_DIMS_LABEL_TOP_NEWS'),
(4523, 'system', 2, 'Top news', '_DIMS_LABEL_TOP_NEWS'),
(4524, 'system', 1, 'Par utilisateur', '_DIMS_LABEL_BY_USER'),
(4525, 'system', 2, 'By user', '_DIMS_LABEL_BY_USER'),
(4526, 'system', 1, 'Fil d''actualit&eacute;', '_DIMS_LABEL_NEWS_FEED'),
(4527, 'system', 2, 'News Feed', '_DIMS_LABEL_NEWS_FEED'),
(4528, 'system', 1, 'Connexions r&eacute;centes', '_DIMS_LABEL_LASTCONNECTION'),
(4529, 'system', 2, 'Most recent connections', '_DIMS_LABEL_LASTCONNECTION'),
(4530, 'system', 1, 'Connexions r&eacute;centes', '_DIMS_LABEL_LASTCONNECTION'),
(4531, 'system', 2, 'Recently connected', '_DIMS_LABEL_LASTCONNECTION'),
(4532, 'system', 1, 'Aucun', '_DIMS_LABEL_NONE'),
(4533, 'system', 2, 'None', '_DIMS_LABEL_NONE'),
(4534, 'system', 1, 'Utilisateurs proches', '_DIMS_LABEL_LOCAL_USER'),
(4535, 'system', 2, 'Local users', '_DIMS_LABEL_LOCAL_USER'),
(4536, 'system', 1, 'Graphiques', '_DIMS_LABEL_GRAPHS'),
(4537, 'system', 2, 'Graphics', '_DIMS_LABEL_GRAPHS'),
(4538, 'system', 1, 'a d&eacute;pos&eacute; le fichier', '_DIMS_LABEL_FILE_CREATED'),
(4539, 'system', 2, 'created file ', '_DIMS_LABEL_FILE_CREATED'),
(4540, 'system', 1, 'est en lien avec', '_DIMS_LABEL_IS_LINKING'),
(4541, 'system', 2, 'is linked with', '_DIMS_LABEL_IS_LINKING'),
(4542, 'system', 1, 'Mise à jour du profil', '_DIMS_LABEL_UPDATE_PROFILE'),
(4543, 'system', 2, 'Profile updated', '_DIMS_LABEL_UPDATE_PROFILE'),
(4544, 'system', 1, 'a mis &agrave; jour le fichier', '_DIMS_LABEL_FILE_UPDATED'),
(4545, 'system', 2, 'updated file', '_DIMS_LABEL_FILE_UPDATED'),
(4546, 'system', 1, '', '_DIMS_LABEL_AGO'),
(4547, 'system', 2, 'ago', '_DIMS_LABEL_AGO'),
(4548, 'system', 1, 'autres', '_DIMS_LABEL_OTHERS'),
(4549, 'system', 2, 'others', '_DIMS_LABEL_OTHERS'),
(4550, 'system', 1, 'et', '_DIMS_LABEL_AND'),
(4551, 'system', 2, 'and', '_DIMS_LABEL_AND'),
(4552, 'system', 1, 'connect&eacute;', '_DIMS_LABEL_IS_CONNECTED'),
(4553, 'system', 2, 'connected', '_DIMS_LABEL_IS_CONNECTED'),
(4554, 'system', 1, 'Publier cet &eacute;v&egrave;nement sur le site internet ?', '_DIMS_EVENT_LABEL_PUBLISHED'),
(4555, 'system', 2, 'Publish this event on website ?', '_DIMS_EVENT_LABEL_PUBLISHED'),
(4556, 'system', 1, 'Afficher en priorit&eacute; sur la page d''accueil ?', '_DIMS_EVENT_LABEL_DISP_HP'),
(4557, 'system', 2, 'Display on homepage ?', '_DIMS_EVENT_LABEL_DISP_HP'),
(4558, 'system', 1, 'Utiliser le mod&egrave;le', '_DIMS_FAIRS_USE_MODEL'),
(4559, 'system', 2, 'Using model', '_DIMS_FAIRS_USE_MODEL'),
(4560, 'system', 1, 'Avec accus&eacute; de r&eacute;ception des documents', '_DIMS_FAIRS_ACCUSE_RECEPTION'),
(4561, 'system', 1, 'Avec paiement', '_DIMS_FAIRS_PAIEMENT'),
(4562, 'system', 1, 'S''enregistrer', '_DIMS_LABEL_REGISTER'),
(4566, 'system', 2, 'Own profile updated', '_DIMS_LABEL_UPDATE_OWN_PROFILE'),
(4565, 'system', 1, 'Mise &agrave; jour de son profil', '_DIMS_LABEL_UPDATE_OWN_PROFILE'),
(4567, 'system', 1, 'Raccourcis', '_DIMS_LABEL_SHORCUTS'),
(4568, 'system', 2, 'Shortcuts', '_DIMS_LABEL_SHORCUTS'),
(4569, 'system', 1, 'Recherches r&eacute;centes', '_DIMS_LABEL_RECENTLY_SEARCH'),
(4570, 'system', 2, 'Recently search', '_DIMS_LABEL_RECENTLY_SEARCH'),
(4571, 'system', 1, 'Nombre de connexion', '_DIMS_LABEL_NUMBER_CONNECTION'),
(4572, 'system', 2, 'Number of connections', '_DIMS_LABEL_NUMBER_CONNECTION'),
(4575, 'system', 1, 'Ann&eacute;e', '_DIMS_LABEL_YEAR'),
(4576, 'system', 2, 'Year', '_DIMS_LABEL_YEAR'),
(4577, 'system', 1, 'Texte Simple', '_DIMS_LABEL_SIMPLE_TEXT'),
(4578, 'system', 2, 'Simple text', '_DIMS_LABEL_SIMPLE_TEXT'),
(4579, 'system', 1, 'Texte Simple', '_DIMS_LABEL_SIMPLE_TEXT'),
(4580, 'system', 2, 'Simple text', '_DIMS_LABEL_SIMPLE_TEXT'),
(4581, 'system', 1, 'Texte Avanc&eacute;', '_DIMS_LABEL_ADVANCED_TEXT'),
(4582, 'system', 2, 'Advanced text', '_DIMS_LABEL_ADVANCED_TEXT'),
(4583, 'system', 1, 'Case a Cocher', '_DIMS_LABEL_CHECKBOX'),
(4584, 'system', 2, 'Checkbox', '_DIMS_LABEL_CHECKBOX'),
(4585, 'system', 1, 'Bouton radio', '_DIMS_LABEL_RADIO_BUTTON'),
(4586, 'system', 2, 'Radio button', '_DIMS_LABEL_RADIO_BUTTON'),
(4587, 'system', 1, 'Liste de choix', '_DIMS_LABEL_LIST'),
(4588, 'system', 2, 'List of elements', '_DIMS_LABEL_LIST'),
(4589, 'system', 1, 'Lien formulaire', '_DIMS_LABEL_FORM_LINK'),
(4590, 'system', 2, 'Link form', '_DIMS_LABEL_FORM_LINK'),
(4591, 'system', 1, 'Numeacute;ro auto.', '_DIMS_LABEL_AUTO_NUMBER'),
(4592, 'system', 2, 'Auto. number', '_DIMS_LABEL_AUTO_NUMBER'),
(4593, 'system', 1, 'Chaine de caracteres', '_DIMS_LABEL_STRING'),
(4594, 'system', 2, 'String', '_DIMS_LABEL_STRING'),
(4595, 'system', 1, 'Nombre Entier', '_DIMS_LABEL_INT_NUMBER'),
(4596, 'system', 2, 'Integer', '_DIMS_LABEL_INT_NUMBER'),
(4597, 'system', 1, 'Nombre r&eacute;el', '_DIMS_LABEL_FLOAT_NUMBER'),
(4598, 'system', 2, 'Float', '_DIMS_LABEL_FLOAT_NUMBER'),
(4599, 'system', 1, 'Adresse Internet', '_DIMS_LABEL_WEB_ADDRESS');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES
(4600, 'system', 2, 'URL', '_DIMS_LABEL_WEB_ADDRESS'),
(4601, 'system', 1, 'Contient', '_DIMS_LABEL_CONTAIN'),
(4602, 'system', 2, 'Contains', '_DIMS_LABEL_CONTAIN'),
(4603, 'system', 1, 'Commence par', '_DIMS_LABEL_BEGIN_WITH'),
(4604, 'system', 2, 'begins with', '_DIMS_LABEL_BEGIN_WITH'),
(4605, 'system', 1, 'a m&eacute;difi&eacute; le fichier', '_DIMS_LABEL_FILE_MODIFIED'),
(4606, 'system', 2, 'modified file ', '_DIMS_LABEL_FILE_MODIFIED'),
(4607, 'system', 1, 'a tagg&eacute;', '_DIMS_LABEL_TAG_CREATED'),
(4608, 'system', 2, 'tagged ', '_DIMS_LABEL_TAG_CREATED'),
(4609, 'system', 1, 'Cr&eacute;ation de l''&eacute;&eacute;nement', '_DIMS_LABEL_CREATE_EVENT'),
(4610, 'system', 2, 'Event created', '_DIMS_LABEL_CREATE_EVENT'),
(4611, 'system', 1, 'Mise &agrave; jour de l''&eacute;&eacute;nement', '_DIMS_LABEL_UPDATE_EVENT'),
(4612, 'system', 2, 'Event updated', '_DIMS_LABEL_UPDATE_EVENT'),
(4613, 'system', 1, 'Nouvel &eacute;v&eacute;nement', '_DIMS_LABEL_NEW_EVENT'),
(4614, 'system', 2, 'New event', '_DIMS_LABEL_NEW_EVENT'),
(4615, 'system', 1, 'Propri&eacute;t&eacute;s de l''article', '_DIMS_PROPERTIES_ARTICLE'),
(4616, 'system', 2, 'Article properties', '_DIMS_PROPERTIES_ARTICLE'),
(4617, 'system', 1, 'Propri&eacute;t&eacute;s de la rubrique', '_DIMS_PROPERTIES_HEADING'),
(4618, 'system', 2, 'Heading properties', '_DIMS_PROPERTIES_HEADING'),
(4621, 'system', 1, 'Voir les droits', '_DIMS_LABEL_VIEWRULES'),
(4622, 'system', 2, 'View rules', '_DIMS_LABEL_VIEWRULES'),
(4623, 'system', 1, 'Masquer les droits', '_DIMS_LABEL_HIDERULES'),
(4624, 'system', 2, 'Hide rules', '_DIMS_LABEL_HIDERULES'),
(4625, 'system', 2, 'Added on', '_ADD_ON'),
(4626, 'system', 1, 'Ajout&eacute; le', '_ADD_ON'),
(4627, 'system', 1, 'Créer une copie de cet &eacute;v&egrave;nement.', '_DIMS_LABEL_EVENT_CREATE_COPY'),
(4628, 'system', 2, 'Create a copy of this event', '_DIMS_LABEL_EVENT_CREATE_COPY'),
(4629, 'system', 1, 'Email de contact pour les events', '_EVENTS_SENDER_EMAIL'),
(4630, 'system', 2, 'Contact email for events', '_EVENTS_SENDER_EMAIL'),
(4631, 'system', 1, 'Mail envoy&eacute; &agrave; un organisteur lors des demandes d''inscription (formulaire niveau 1)', '_EVENTS_MAIL_TO_ORG_INSC_NIV1'),
(4632, 'system', 2, 'Mail sent to manager for inscription requests (level 1 form)', '_EVENTS_MAIL_TO_ORG_INSC_NIV1'),
(4633, 'system', 1, 'Cr&eacute;ation du contact', '_DIMS_LABEL_CREATE_PROFILE'),
(4634, 'system', 2, 'Contact created', '_DIMS_LABEL_CREATE_PROFILE'),
(4635, 'system', 1, 'Raccourcis', '_DIMS_LABEL_SHORCUTS'),
(4636, 'system', 2, 'Shortcuts', '_DIMS_LABEL_SHORCUTS'),
(4637, 'system', 1, 'Mod&egrave;les de newsletter', '_DIMS_NEWSLETTER_LIST_MODELS'),
(4638, 'system', 2, 'Newsletter models', '_DIMS_NEWSLETTER_LIST_MODELS'),
(4639, 'system', 1, 'demande(s)', '_DIMS_NEWSLETTER_INSC_REQUEST'),
(4640, 'system', 2, 'Request(s)', '_DIMS_NEWSLETTER_INSC_REQUEST'),
(4641, 'system', 1, 'Gestion des inscriptions', '_DIMS_NEWSLETTER_GEST_INSC'),
(4642, 'system', 2, 'Subscription management', '_DIMS_NEWSLETTER_GEST_INSC'),
(4643, 'system', 1, 'Demandes d''inscription', '_DIMS_NEWSLETTER_DMDINSC'),
(4644, 'system', 2, 'Subscription requests', '_DIMS_NEWSLETTER_DMDINSC'),
(4645, 'system', 1, 'Mod&egrave;les de newsletter', '_DIMS_NEWSLETTER_LIST_MODELS'),
(4782, 'system', 2, 'Social browser : to find', '_DIMS_LABEL_SOCIAL_NETWORK'),
(4647, 'system', 1, 'demande(s)', '_DIMS_NEWSLETTER_INSC_REQUEST'),
(4648, 'system', 2, 'Request(s)', '_DIMS_NEWSLETTER_INSC_REQUEST'),
(4649, 'system', 1, 'Gestion des inscriptions', '_DIMS_NEWSLETTER_GEST_INSC'),
(4650, 'system', 2, 'Subscription management', '_DIMS_NEWSLETTER_GEST_INSC'),
(4651, 'system', 1, 'Demandes d''inscription', '_DIMS_NEWSLETTER_DMDINSC'),
(4652, 'system', 2, 'Subscription requests', '_DIMS_NEWSLETTER_DMDINSC'),
(4653, 'system', 1, 'Rechercher un contact', '_DIMS_LABEL_SEARCH_FOR_CT'),
(4654, 'system', 2, 'Find a contact', '_DIMS_LABEL_SEARCH_FOR_CT'),
(4657, 'system', 1, 'Ajouter un mod&egrave;le', '_DIMS_LABEL_ADD_MODEL'),
(4658, 'system', 2, 'Add model', '_DIMS_LABEL_ADD_MODEL'),
(4659, 'system', 1, 'Le champs intitul&eacute; est vide.', '_DIMS_ERROR_LABEL_EMPTY'),
(4660, 'system', 1, 'Aucun contact attach&eacute;', '_DIMS_LABEL_NO_CT_ATTACHED'),
(4661, 'system', 2, 'Groups List', '_DIMS_LABEL_CT_GROUP_LIST'),
(4662, 'system', 2, 'Create group', '_DIMS_LABEL_GROUP_CREATE'),
(4663, 'system', 2, 'No attached group', '_DIMS_LABEL_NO_GROUP_ATTACHED'),
(4664, 'system', 2, 'Add group', '_DIMS_LABEL_ADD_CT_GROUP'),
(4665, 'system', 2, 'Add contact to a group', '_DIMS_LABEL_LINK_CT_GROUP'),
(4666, 'system', 2, 'Group title', '_DIMS_LABEL_CTGROUP_TITLE'),
(4667, 'system', 2, 'View group', '_DIMS_LABEL_CTGROUP_VIEW'),
(4668, 'system', 2, 'List of private groups', '_DIMS_LABEL_LIST_PRIVATE_CT'),
(4669, 'system', 2, 'List of workspace groups', '_DIMS_LABEL_LIST_WORKSACE_CT'),
(4670, 'system', 1, 'Cr&eacute;er un mod&egrave;le &agrave; partir de cette foire', '_DIMS_CREATE_MODEL_FAIR'),
(4671, 'system', 2, 'Create a model starting from this fair', '_DIMS_CREATE_MODEL_FAIR'),
(4672, 'system', 1, 'Nouveaux documents &agrave; valider', '_DIMS_LABEL_DOCS_UPLOADED'),
(4673, 'system', 2, 'There are new documents', '_DIMS_LABEL_DOCS_UPLOADED'),
(4674, 'system', 1, '&Eacute;crire un email', '_DIMS_MAILING_WRITE_EMAIL'),
(4675, 'system', 2, 'Write an email', '_DIMS_MAILING_WRITE_EMAIL'),
(4676, 'system', 1, 'Rattacher une liste de diffusion', '_DIMS_MAILING_LINK_WITH_LIST'),
(4677, 'system', 2, 'Add a mailing list', '_DIMS_MAILING_LINK_WITH_LIST'),
(4678, 'system', 1, 'Liste(s) de diffusion attach&eacute;e(s)', '_DIMS_MAILING_LIST_LINKED'),
(4679, 'system', 2, 'Mailing list(s) linked', '_DIMS_MAILING_LIST_LINKED'),
(4680, 'system', 1, 'Importer une liste d''emails', '_DIMS_MAILING_IMPORT_EMAIL'),
(4681, 'system', 2, 'Import an email file', '_DIMS_MAILING_IMPORT_EMAIL'),
(4682, 'system', 1, 'la liste ne sera pas sauvegard&eacute;e et l''email doit &ecirc;tre envoy&eacute; juste apr&egrave;s son enregistrement.', '_DIMS_LABEL_NEWSLETTER_TMP_IMPORT'),
(4683, 'system', 2, 'the imported list will not be save so you have to send just after saving your email.', '_DIMS_LABEL_NEWSLETTER_TMP_IMPORT'),
(4684, 'system', 1, 'Liste des emails import&eacute;s', '_DIMS_MAILING_LIST_TMP_MAIL'),
(4685, 'system', 2, 'Imported emails', '_DIMS_MAILING_LIST_TMP_MAIL'),
(4686, 'immo', 1, 'Agriculture', '_DIMS_LABEL_AGRICULTURE'),
(4687, 'immo', 2, 'Agriculture', '_DIMS_LABEL_AGRICULTURE'),
(4688, 'immo', 1, 'Appartement', '_DIMS_LABEL_APPARTMENT'),
(4689, 'immo', 2, 'Appartment', '_DIMS_LABEL_APPARTMENT'),
(4690, 'immo', 1, 'Gastronomie', '_DIMS_LABEL_GASTRONOMY'),
(4691, 'immo', 2, 'Gastronomy', '_DIMS_LABEL_GASTRONOMY'),
(4692, 'i', 1, 'Maison', '_DIMS_LABEL_HOUSE'),
(4693, 'immo', 2, 'House', '_DIMS_LABEL_HOUSE'),
(4694, 'immo', 1, 'Industrie', '_DIMS_LABEL_INDUSTRY'),
(4695, 'immo', 2, 'Industry', '_DIMS_LABEL_INDUSTRY'),
(4696, 'immo', 1, 'Place de parc', '_DIMS_LABEL_PARK'),
(4697, 'immo', 2, 'Parking space', '_DIMS_LABEL_PARK'),
(4698, 'immo', 1, 'Terrain', '_DIMS_LABEL_PLOT'),
(4699, 'immo', 2, 'Plot', '_DIMS_LABEL_PLOT'),
(4700, 'immo', 1, 'Pi&egrave;ces annexes', '_DIMS_LABEL_SECONDARYROOM'),
(4701, 'immo', 2, 'Secondary rooms', '_DIMS_LABEL_SECONDARYROOM'),
(4702, 'immo', 1, 'Jardin', '_DIMS_LABEL_GARDEN'),
(4703, 'immo', 2, 'Garden', '_DIMS_LABEL_GARDEN'),
(4704, 'system', 1, 'Mod&egrave;les de newsletter', '_DIMS_NEWSLETTER_LIST_MODELS'),
(4781, 'system', 1, 'R&eacute;seau social : pour trouver', '_DIMS_LABEL_SOCIAL_NETWORK'),
(4706, 'system', 1, 'demande(s)', '_DIMS_NEWSLETTER_INSC_REQUEST'),
(4707, 'system', 2, 'Request(s)', '_DIMS_NEWSLETTER_INSC_REQUEST'),
(4708, 'system', 1, 'Gestion des inscriptions', '_DIMS_NEWSLETTER_GEST_INSC'),
(4709, 'system', 2, 'Subscription management', '_DIMS_NEWSLETTER_GEST_INSC'),
(4710, 'system', 1, 'Demandes d''inscription', '_DIMS_NEWSLETTER_DMDINSC'),
(4711, 'system', 2, 'Subscription requests', '_DIMS_NEWSLETTER_DMDINSC'),
(4712, 'system', 1, 'Vus r&eacute;cemment', '_DIMS_LABEL_RECENTLY_VIEWED'),
(4713, 'system', 2, 'Recently viewed', '_DIMS_LABEL_RECENTLY_VIEWED'),
(4714, 'system', 1, 'Nouveau(x) mail(s)', '_DIMS_LABEL_NEW_MAIL_RECEIVED'),
(4715, 'system', 2, 'New Email(s)', '_DIMS_LABEL_NEW_MAIL_RECEIVED'),
(4716, 'system', 1, 'Page web', '_DIMS_LABEL_WEB_PAGE'),
(4717, 'system', 2, 'Web page', '_DIMS_LABEL_WEB_PAGE'),
(4718, 'system', 1, 'Téléphone', '_DIMS_LABEL_ADR_HOME'),
(4719, 'system', 2, 'Home', '_DIMS_LABEL_ADR_HOME'),
(4720, 'system', 1, 'Travail', '_DIMS_LABEL_WORK'),
(4721, 'system', 2, 'Work', '_DIMS_LABEL_WORK'),
(4722, 'system', 1, 'vCard suivante', '_DIMS_LABEL_NEXT_VCARD'),
(4723, 'system', 2, 'Next vCard', '_DIMS_LABEL_NEXT_VCARD'),
(4724, 'system', 1, 'vCard précédente', '_DIMS_LABEL_PREVIOUS_VCARD'),
(4725, 'system', 2, 'Previous vCard', '_DIMS_LABEL_PREVIOUS_VCARD'),
(4726, 'system', 1, 'Par usage', '_DIMS_LABEL_BY_USAGE'),
(4727, 'system', 2, 'By usage', '_DIMS_LABEL_BY_USAGE'),
(4728, 'system', 1, 'Par cat&eacute;gorie', '_DIMS_LABEL_BY_CATEGORY'),
(4729, 'system', 2, 'By category', '_DIMS_LABEL_BY_CATEGORY'),
(4730, 'system', 1, 'T&eacute;l&eacute;phone', '_PHONE'),
(4731, 'system', 1, 'T&eacute;l&eacute;phone', '_PHONE'),
(4733, 'wce', 2, 'Old url rewriting', '_WCE_URLREWRITE_OLD'),
(4734, 'system', 2, 'Overview', '_DIMS_LABEL_OVERVIEW'),
(4735, 'system', 1, 'Vue d''ensemble', '_DIMS_LABEL_OVERVIEW'),
(4736, 'system', 2, 'Events management', '_DIMS_LABEL_EVT_MANAGEMENT'),
(4737, 'system', 1, 'Gestion des évènements', '_DIMS_LABEL_EVT_MANAGEMENT'),
(4738, 'system', 2, 'Fairs management', '_DIMS_LABEL_FAIRS_MANAGEMENT'),
(4739, 'system', 1, 'Gestion des foires', '_DIMS_LABEL_FAIRS_MANAGEMENT'),
(4740, 'system', 2, 'Models management', '_DIMS_LABEL_FAIRS_MODELS_MGT'),
(4741, 'system', 1, 'Gestions des modèles', '_DIMS_LABEL_FAIRS_MODELS_MGT'),
(4742, 'system', 2, 'Subscribing requests', '_DIMS_LABEL_EVT_SUB_REQ'),
(4743, 'system', 1, 'Demandes d''inscription', '_DIMS_LABEL_EVT_SUB_REQ'),
(4744, 'system', 2, 'Document validation', '_DIMS_LABEL_EVT_DOC_REQ'),
(4745, 'system', 1, 'Validation des documents', '_DIMS_LABEL_EVT_DOC_REQ'),
(4746, 'system', 2, 'Past events', '_DIMS_LABEL_EVT_PAST'),
(4747, 'system', 1, 'Evènements passés', '_DIMS_LABEL_EVT_PAST'),
(4748, 'system', 2, 'Coming events', '_DIMS_LABEL_EVT_COMING'),
(4749, 'system', 1, 'Evènements à venir', '_DIMS_LABEL_EVT_COMING'),
(4750, 'system', 2, 'E-learning', '_DIMS_LABEL_ELEARNING'),
(4751, 'system', 2, 'training centre', '_DIMS_LABEL_FORMATION_CENTER'),
(4752, 'system', 1, 'Centre de Formation d''I-Net', '_DIMS_LABEL_FORMATION_CENTER'),
(4753, 'system', 1, 'Gestion des doublons', '_MANAGE_DOUBLONS'),
(4754, 'system', 2, 'Duplicate management', '_MANAGE_DOUBLONS'),
(4755, 'system', 2, 'Welcome to', '_WELCOME_TO'),
(4756, 'system', 1, 'Bienvenue sur', '_WELCOME_TO'),
(4757, 'system', 1, 'Partag&eacute; par', '_DIMS_LABEL_SHARED_BY'),
(4758, 'system', 1, 'Shared by', '_DIMS_LABEL_SHARED_BY'),
(4759, 'system', 1, 'Instancier', '_DIMS_LABEL_INSTANCIATE'),
(4760, 'system', 2, 'Instantiate', '_DIMS_LABEL_INSTANCIATE'),
(4761, 'system', 1, 'Anglais', '_DIMS_LABEL_ENGLISH'),
(4762, 'system', 2, 'English', '_DIMS_LABEL_ENGLISH'),
(4763, 'system', 1, 'Libell&eacute; en cas de r&eacute;ponse positive', '_DIMS_FAIR_ETAP_CONDITION_LABEL_YES'),
(4764, 'system', 2, 'Label for positive answer', '_DIMS_FAIR_ETAP_CONDITION_LABEL_YES'),
(4765, 'system', 1, 'Libell&eacute; en cas de r&eacute;ponse n&eacute;gative', '_DIMS_FAIR_ETAP_CONDITION_LABEL_NO'),
(4766, 'system', 2, 'Label for negative answer', '_DIMS_FAIR_ETAP_CONDITION_LABEL_YES'),
(4767, 'system', 1, 'Activer une condition sur l''&eacute;tape', '_DIMS_FAIR_ETAP_CONDITION'),
(4768, 'system', 1, 'Libell&eacute; de la condition', '_DIMS_FAIR_ETAP_CONDITION_LABEL'),
(4769, 'system', 2, 'Put a condition for this step', '_DIMS_FAIR_ETAP_CONDITION'),
(4770, 'system', 2, 'Condition label', '_DIMS_FAIR_ETAP_CONDITION_LABEL'),
(4771, 'doc', 1, 'Dossier', '_DOC_FOLDER'),
(4772, 'doc', 2, 'Folder', '_DOC_FOLDER'),
(4773, 'system', 1, 'Fiches incomplètes', '_INCOMPLETE_RECORDS'),
(4774, 'system', 2, 'Incomplet records', '_INCOMPLETE_RECORDS'),
(4775, 'system', 1, 'Nom de l''entreprise', '_DIMS_LABEL_ENT_RS'),
(4776, 'system', 2, 'Company name', '_DIMS_LABEL_ENT_RS'),
(4777, 'system', 1, 'S', '_WEEK'),
(4778, 'system', 2, 'W', '_WEEK'),
(4779, 'system', 2, 'Nb active mails', '_DIMS_LABEL_NEWSLETTER_NBINSC_ACTIF'),
(4780, 'system', 1, 'Nb mails actifs', '_DIMS_LABEL_NEWSLETTER_NBINSC_ACTIF'),
(4783, 'system', 1, 'Mise Ã  jour des actions', '_DIMS_LABEL_UPDATE_ACTION'),
(4784, 'system', 2, 'Update actions', '_DIMS_LABEL_UPDATE_ACTION'),
(4785, 'system', 1, 'R&eacute;ception d''un e-mail par inscription', '_DIMS_LABEL_EMAIL_REGISTRATION'),
(4786, 'system', 2, 'Receive an email from registration', '_DIMS_LABEL_EMAIL_REGISTRATION'),
(4787, 'system', 1, 'Historique des participations', '_HISTORIC_OF_REGISTRATION'),
(4788, 'system', 2, 'History of your registrations', '_HISTORIC_OF_REGISTRATION'),
(4789, 'system', 2, 'Your registrations', '_DIMS_EVT_INSCRIPT_SELVES'),
(4790, 'system', 1, 'Vos participations', '_DIMS_EVT_INSCRIPT_SELVES'),
(4791, 'system', 1, 'En savoir plus', '_DIMS_READ_MORE'),
(4792, 'system', 2, 'Read more', '_DIMS_READ_MORE'),
(4793, 'system', 1, 'Param&egrave;tres pour le retour des documents', '_DIMS_LABEL_DOC_RETURN_PARAMS'),
(4794, 'system', 2, 'Parameters for documents return', '_DIMS_LABEL_DOC_RETURN_PARAMS'),
(4795, 'system', 1, 'Documents propos&eacute;s', '_DIMS_LABEL_DOCS_PROPOSED'),
(4796, 'system', 2, 'Available files', '_DIMS_LABEL_DOCS_PROPOSED'),
(4797, 'system', 1, 'Aucune description', '_DIMS_LABEL_NO_DESC'),
(4798, 'system', 2, 'No description', '_DIMS_LABEL_NO_DESC'),
(4799, 'system', 1, 'Date(s) de pr&eacute;sence', '_FAIRS_DATE_PRESENCE'),
(4800, 'system', 2, 'Present date', '_FAIRS_DATE_PRESENCE'),
(4801, 'system', 2, 'There is no document to validate.', '_DIMS_LABEL_NO_DOCS_TO_VALID'),
(4802, 'system', 1, 'Il n''y a aucun document à valider.', '_DIMS_LABEL_NO_DOCS_TO_VALID'),
(4803, 'system', 1, 'Valider le paiement', '_DIMS_FAIR_VALID_PAIEMENT'),
(4804, 'system', 2, 'Confirm your payment', '_DIMS_FAIR_VALID_PAIEMENT'),
(4805, 'system', 1, 'Your payment has been validated;', '_DIMS_FAIR_VALIDATED_PAIEMENT'),
(4806, 'system', 2, 'Your payment isn''t confirm;', '_DIMS_FAIR_NO_PAIEMENT'),
(4807, 'system', 1, 'Valid&eacute;', '_DIMS_LABEL_VALIDATED'),
(4808, 'system', 2, 'Validated;', '_DIMS_LABEL_VALIDATED'),
(4809, 'system', 1, 'Refus&eacute;', '_DIMS_LABEL_REFUSED'),
(4810, 'system', 2, 'Refused', '_DIMS_LABEL_REFUSED'),
(4811, 'system', 2, 'Me', '_DIMS_LABEL_ME'),
(4812, 'system', 1, 'Moi', '_DIMS_LABEL_ME'),
(4813, 'system', 2, 'No user is logged', '_DIMS_LABEL_NO_USER_CONNECTED'),
(4814, 'system', 1, 'Aucun utilisateur n''est connectÃ©', '_DIMS_LABEL_NO_USER_CONNECTED'),
(4815, 'system', 1, 'Documents &agrave; retourner', '_DIMS_LABEL_DOCS_TO_RETURN'),
(4816, 'system', 2, 'Documents to give back ', '_DIMS_LABEL_DOCS_TO_RETURN'),
(4817, 'system', 1, 'Validation des inscriptions', '_DIMS_LABEL_VALIDATE_REGISTRATION'),
(4818, 'system', 2, 'Validate registrations', '_DIMS_LABEL_VALIDATE_REGISTRATION'),
(4819, 'system', 1, 'Valider', '_SUBMIT'),
(4820, 'system', 2, 'Submit', '_SUBMIT'),
(4864, 'system', 5, '验证码', '_DIMS_TEXT_CAPTCHA'),
(4863, 'system', 5, '必須入力項目', '_DIMS_LABEL_MANDATORY_FIELDS'),
(4862, 'system', 5, '国', '_DIMS_LABEL_COUNTRY'),
(4861, 'system', 5, '郵便番号', '_DIMS_LABEL_CP'),
(4860, 'system', 5, '都道府県', '_DIMS_LABEL_CITY'),
(4859, 'system', 5, '住所', '_DIMS_LABEL_ADDRESS'),
(4858, 'system', 5, ' 電話', '_PHONE'),
(4857, 'system', 5, 'E-Mail', '_DIMS_LABEL_EMAIL'),
(4854, 'system', 5, '名', '_FIRSTNAME'),
(4853, 'system', 5, '姓', '_DIMS_LABEL_NAME'),
(4856, 'system', 5, '所属', '_DIMS_LABEL_COMPANY'),
(4855, 'system', 5, '役職', '_DIMS_LABEL_FUNCTION'),
(4865, 'system', 5, '送信', '_SUBMIT'),
(4867, 'system', 2, 'See duplicate emails', '_SEE_DUPLICATE_EMAIL'),
(4868, 'system', 1, 'Email multiple', 'DUPLICATE_EMAIL'),
(4869, 'system', 2, 'Duplicate email', 'DUPLICATE_EMAIL'),
(4870, 'system', 1, 'VCard(s)', '_DIMS_LABEL_VCARD'),
(4871, 'system', 2, 'VCard(s)', '_DIMS_LABEL_VCARD'),
(4872, 'system', 1, 'ImportÃ© le', '_DIMS_LABEL_IMPORT_DATE'),
(4873, 'system', 2, 'Imported', '_DIMS_LABEL_IMPORT_DATE'),
(4874, 'system', 1, 'Envoyer à une liste de diffusion', '_DIMS_ADD_TO_NEWSLETTER'),
(4875, 'system', 1, 'Selectionner une liste de diffusion', '_DIMS_SELECT_NEWSLETTER'),
(4876, 'system', 1, 'Selectionner un modèle', '_DIMS_SELECT_MODEL'),
(4877, 'system', 1, 'Aucune liste de diffusion', '_DIMS_NO_NEWSLETTER'),
(4878, 'system', 1, 'Aucune modèle', '_DIMS_NO_MODEL'),
(4879, 'elisath', 1, 'Vous &ecirc;tes d&eacute;j&agrave; inscrit &agrave; cette session.', '_DIMS_RESA_ALREADY_BOOKED'),
(4880, 'elisath', 1, 'Il n''y a plus de place disponible pour cette session.', '_DIMS_SESSION_FULL'),
(4881, 'elisath', 1, 'Cette session est d&eacute;j&agrave pass&eacute;.', '_DIMS_RESA_TOO_LATE'),
(4882, 'elisath', 1, 'Vous &ecirc;tes inscrit &agrave; cette session.', '_DIMS_RESA_BOOKED'),
(4883, 'elisath', 1, 'Historique des r&eacute;servation', '_DIMS_HISTORIC_RESA'),
(4884, 'system', 1, 'R&eacute;servation &agrave; venir', '_DIMS_RESA_TO_COME'),
(4885, 'elisath', 1, '&Ecirc;tes-vous s&ucirc;r de voulais annuler cette r&eacute;servation ?', '_DIMS_SURE_CANCEL_RESA'),
(4886, 'elisath', 1, 'Vous n''avez pas assez de cr&eacute;dits.', '_DIMS_RESA_NOT_ENOUGH_CRED'),
(4887, 'elisath', 1, 'Vous avez atteint la limite de r&eacute;servation pour la semaine.', '_DIMS_RESA_REACH_LIM'),
(4888, 'elisath', 1, 'Erreur inconnue.', '_DIMS_RESA_UNKNOW_ERROR'),
(4889, '142', 1, 'Inactif', '_DIMS_LABEL_NO_ACTIVE'),
(4890, 'system', 1, 'Gestion de Gnu Private Guard', '_DIMS_GNUPG_MANAGEMENT'),
(4891, 'system', 2, 'Gnu Private Guard management', '_DIMS_GNUPG_MANAGEMENT'),
(4892, 'assurance', 1, 'Numéro du contract collectif', '_DIMS_LABEL_ASSUR_CHAMPS_NUM_CONTRAT_COLL'),
(4893, 'assurance', 1, 'Nom du contrat collectif', '_DIMS_LABEL_ASSUR_CHAMPS_NOM_CONTRAT_COLL'),
(4894, 'assurance', 1, 'Numéro de police', '_DIMS_LABEL_ASSUR_CHAMPS_NUM_POLICE'),
(4895, 'assurance', 1, 'Numéro d''assuré', '_DIMS_LABEL_ASSUR_CHAMPS_NUM_ASSURE'),
(4896, 'assurance', 1, 'Nom', '_DIMS_LABEL_ASSUR_CHAMPS_NOM_ASSURE'),
(4897, 'assurance', 1, 'Prénom', '_DIMS_LABEL_ASSUR_CHAMPS_PRENOM_ASSURE'),
(4898, 'assurance', 1, 'Date de naissance', '_DIMS_LABEL_ASSUR_CHAMPS_DATE_NAISSANCE_ASSURE'),
(4899, 'assurance', 1, 'Civilité', '_DIMS_LABEL_ASSUR_CHAMPS_CIVILITE_ASSURE'),
(4900, 'assurance', 1, 'Langue parlée', '_DIMS_LABEL_ASSUR_CHAMPS_LANGUE_ASSURE'),
(4901, 'assurance', 1, 'Nom de l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_NOM_ADDR_CORRESP'),
(4902, 'assurance', 1, 'Prénom de l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_PRENOM_ADDR_CORRESP'),
(4903, 'assurance', 1, 'Complément à l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_COMPL_ADDR_CORRESP'),
(4904, 'assurance', 1, 'Rue de l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_RUE_ADDR_CORRESP'),
(4905, 'assurance', 1, 'Numéro de bâtiment de l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_NUM_ADDR_CORRESP'),
(4906, 'assurance', 1, 'Code postal de l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_CP_ADDR_CORRESP'),
(4907, 'assurance', 1, 'Localité de l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_LOCAL_ADDR_CORRESP'),
(4908, 'assurance', 1, 'Couverture', '_DIMS_LABEL_ASSUR_CHAMPS_COUVERTURE'),
(4909, 'assurance', 1, 'Franchise', '_DIMS_LABEL_ASSUR_CHAMPS_FRANCHISE'),
(4910, 'assurance', 1, 'Somme d''assurance', '_DIMS_LABEL_ASSUR_CHAMPS_SOMME_ASSURANCE'),
(4911, 'assurance', 1, 'Canton', '_DIMS_LABEL_ASSUR_CHAMPS_CANTON'),
(4912, 'assurance', 1, 'Pays', '_DIMS_LABEL_ASSUR_CHAMPS_PAYS'),
(4913, 'assurance', 1, 'Région de prime', '_DIMS_LABEL_ASSUR_CHAMPS_REGION_PRIME'),
(4914, 'assurance', 1, 'Groupe d''âge', '_DIMS_LABEL_ASSUR_CHAMPS_GROUPE_AGE'),
(4915, 'assurance', 1, 'Type de couverture', '_DIMS_LABEL_ASSUR_CHAMPS_TYPE_COUVERTURE'),
(4916, 'assurance', 1, 'Montant de prime brut', '_DIMS_LABEL_ASSUR_CHAMPS_MONTANT_PRIME_BRUT'),
(4917, 'assurance', 1, 'Taxe environnementale', '_DIMS_LABEL_ASSUR_CHAMPS_TAXE_ENVIRONNEMENTALE'),
(4918, 'assurance', 1, 'Rabais', '_DIMS_LABEL_ASSUR_CHAMPS_RABAIS'),
(4919, 'assurance', 1, 'Prime nette', '_DIMS_LABEL_ASSUR_CHAMPS_PRIME_NETTE'),
(4920, 'assurance', 1, 'Début de la couverture', '_DIMS_LABEL_ASSUR_CHAMPS_DEBUT_COUVERTURE'),
(4921, 'assurance', 1, 'Fin de la couverture', '_DIMS_LABEL_ASSUR_CHAMPS_FIN_COUVERTURE'),
(4922, 'assurance', 1, 'Payeur de prime', '_DIMS_LABEL_ASSUR_CHAMPS_PAYEUR_DE_PRIME'),
(4923, 'assurance', 1, 'Nom du compte', '_DIMS_LABEL_ASSUR_CHAMPS_BANK_NAME'),
(4924, 'assurance', 1, 'Numéro du compte', '_DIMS_LABEL_ASSUR_CHAMPS_BANK_NUM'),
(4925, 'assurance', 1, 'Clearing number', '_DIMS_LABEL_ASSUR_CHAMPS_BANK_CLEARING'),
(4926, 'assurance', 1, 'Numéro d''assuré bis', '_DIMS_LABEL_ASSUR_CHAMPS_NUM_ASSURE_BIS'),
(4927, 'assurance', 1, 'Numéro de famille', '_DIMS_LABEL_ASSUR_CHAMPS_NUM_FAMILLE'),
(4928, 'assurance', 1, 'Téléphone professionel', '_DIMS_LABEL_ASSUR_CHAMPS_TEL_FIXE_ASSURE'),
(4929, 'assurance', 1, 'Téléphone mobile', '_DIMS_LABEL_ASSUR_CHAMPS_TEL_MOB_ASSURE'),
(4930, 'assurance', 1, 'Numéro de matricule', '_DIMS_LABEL_ASSUR_CHAMPS_NUM_MATRICULE_ASSURE'),
(4931, 'assurance', 1, 'Code de la couverture', '_DIMS_LABEL_ASSUR_CHAMPS_CODE_COUVERTURE'),
(4932, 'assurance', 1, 'Numéro du contract collectif', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_NUM_CONTRAT_COLL'),
(4933, 'assurance', 1, 'Nom du contrat collectif', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_NOM_CONTRAT_COLL'),
(4934, 'assurance', 1, 'Numéro de police', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_NUM_POLICE'),
(4935, 'assurance', 1, 'Numéro d''assuré', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_NUM_ASSURE'),
(4936, 'assurance', 1, 'Nom', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_NOM_ASSURE'),
(4937, 'assurance', 1, 'Prénom', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_PRENOM_ASSURE'),
(4938, 'assurance', 1, 'Date de naissance', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_DATE_NAISSANCE_ASSURE'),
(4939, 'assurance', 1, 'Civilité', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_CIVILITE_ASSURE'),
(4940, 'assurance', 1, 'Langue parlée', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_LANGUE_ASSURE'),
(4941, 'assurance', 1, 'Nom de l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_NOM_ADDR_CORRESP'),
(4942, 'assurance', 1, 'Prénom de l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_PRENOM_ADDR_CORRESP'),
(4943, 'assurance', 1, 'Complément à l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_COMPL_ADDR_CORRESP'),
(4944, 'assurance', 1, 'Rue de l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_RUE_ADDR_CORRESP'),
(4945, 'assurance', 1, 'Numéro de bâtiment de l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_NUM_ADDR_CORRESP'),
(4946, 'assurance', 1, 'Code postal de l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_CP_ADDR_CORRESP'),
(4947, 'assurance', 1, 'Localité de l''adresse de correspondance', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_LOCAL_ADDR_CORRESP'),
(4948, 'assurance', 1, 'Couverture', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_COUVERTURE'),
(4949, 'assurance', 1, 'Franchise', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_FRANCHISE'),
(4950, 'assurance', 1, 'Somme d''assurance', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_SOMME_ASSURANCE'),
(4951, 'assurance', 1, 'Canton', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_CANTON'),
(4952, 'assurance', 1, 'Pays', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_PAYS'),
(4953, 'assurance', 1, 'Région de prime', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_REGION_PRIME'),
(4954, 'assurance', 1, 'Groupe d''âge', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_GROUPE_AGE'),
(4955, 'assurance', 1, 'Type de couverture', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_TYPE_COUVERTURE'),
(4956, 'assurance', 1, 'Montant de prime brut', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_MONTANT_PRIME_BRUT'),
(4957, 'assurance', 1, 'Taxe environnementale', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_TAXE_ENVIRONNEMENTALE'),
(4958, 'assurance', 1, 'Rabais', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_RABAIS'),
(4959, 'assurance', 1, 'Prime nette', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_PRIME_NETTE'),
(4960, 'assurance', 1, 'Début de la couverture', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_DEBUT_COUVERTURE'),
(4961, 'assurance', 1, 'Fin de la couverture', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_FIN_COUVERTURE'),
(4962, 'assurance', 1, 'Payeur de prime', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_PAYEUR_DE_PRIME'),
(4963, 'assurance', 1, 'Nom du compte', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_BANK_NAME'),
(4964, 'assurance', 1, 'Numéro du compte', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_BANK_NUM'),
(4965, 'assurance', 1, 'Clearing number', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_BANK_CLEARING'),
(4966, 'assurance', 1, 'Numéro d''assuré bis', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_NUM_ASSURE_BIS'),
(4967, 'assurance', 1, 'Numéro de famille', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_NUM_FAMILLE'),
(4968, 'assurance', 1, 'Téléphone professionel', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_TEL_FIXE_ASSURE'),
(4969, 'assurance', 1, 'Téléphone mobile', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_TEL_MOB_ASSURE'),
(4970, 'assurance', 1, 'Numéro de matricule', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_NUM_MATRICULE_ASSURE'),
(4971, 'assurance', 1, 'Code de la couverture', '_DIMS_LABEL_ASSUR_CHAMPS_HELP_CODE_COUVERTURE'),
(4972, 'assurance', 1, 'Rabais', '_DIMS_LABEL_ASSUR_TYPE_CHAMPS_RABAIS'),
(5009, 'assurance', 1, 'A venir', '_DIMS_LABEL_ASSUR_STATUT_POLICE_AVENIR'),
(5010, 'assurance', 1, 'En cours', '_DIMS_LABEL_ASSUR_STATUT_POLICE_ENCOURS'),
(5011, 'assurance', 1, 'Terminée', '_DIMS_LABEL_ASSUR_STATUT_POLICE_TERMINEE'),
(5012, 'assurance', 1, 'Archivée', '_DIMS_LABEL_ASSUR_STATUT_POLICE_ARCHIVEE'),
(5013, 'assurance', 1, 'Gestion des imports et des synchronisations', '_DIMS_LABEL_IMPORT_MANAGEMENT'),
(5014, 'assurance', 2, 'Import management', '_DIMS_LABEL_IMPORT_MANAGEMENT'),
(5015, 'system', 1, 'Importer un nouveau fichier', '_DIMS_LABEL_NEW_FILE_IMPORT'),
(5016, 'system', 2, 'Import new file', '_DIMS_LABEL_NEW_FILE_IMPORT'),
(5017, 'system', 1, 'Gestion des mod&egrave;les', '_DIMS_LABEL_MANAGE_MODEL'),
(5018, 'system', 2, 'Templates management', '_DIMS_LABEL_MANAGE_MODEL'),
(5019, 'system', 1, 'Nouveau mod&egrave;le', '_DIMS_LABEL_NEW_TEMPLATE'),
(5020, 'system', 2, 'New template', '_DIMS_LABEL_NEW_TEMPLATE'),
(5021, 'system', 1, 'enregistrement par page', '_DIMS_LABEL_RECORD_BY_PAGE'),
(5022, 'system', 2, 'records per page', '_DIMS_LABEL_RECORD_BY_PAGE'),
(5023, 'system', 1, 'sur', '_DIMS_LABEL_OF'),
(4997, 'system', 1, 'Anglais', '_DIMS_LABEL_ANGLAIS'),
(4998, 'system', 1, 'Arabe', '_DIMS_LABEL_ARABE'),
(4999, 'system', 1, 'Chinois', '_DIMS_LABEL_CHINOIS'),
(5000, 'system', 1, 'Espagnol', '_DIMS_LABEL_ESPAGNOL'),
(5001, 'system', 1, 'Français', '_DIMS_LABEL_FRANCAIS'),
(5002, 'system', 1, 'Russe', '_DIMS_LABEL_RUSSE'),
(5003, 'system', 1, 'Allemand', '_DIMS_LABEL_ALLEMAND'),
(5004, 'system', 1, 'Japonais', '_DIMS_LABEL_JAPONAIS'),
(5005, 'system', 1, 'Italien', '_DIMS_LABEL_ITALIEN'),
(5006, 'system', 1, 'Portugais', '_DIMS_LABEL_PORTUGAIS'),
(5007, 'system', 1, 'Roumain', '_DIMS_LABEL_ROUMAIN'),
(5008, 'system', 1, 'Luxembourgeois', '_DIMS_LABEL_LUXEMBOURGEOIS'),
(5024, 'system', 2, 'of', '_DIMS_LABEL_OF'),
(5025, 'system', 1, 'enregistrements', '_DIMS_LABEL_RECORDS'),
(5026, 'system', 2, 'records', '_DIMS_LABEL_RECORDS'),
(5027, 'system', 1, 'total', '_DIMS_LABEL_TOTAL'),
(5028, 'system', 2, 'total', '_DIMS_LABEL_TOTAL'),
(5029, 'system', 1, 'filtre de', '_DIMS_LABEL_FILTERED_FROM'),
(5030, 'system', 2, 'filtered from', '_DIMS_LABEL_FILTERED_FROM'),
(5031, 'system', 1, 'Premier', '_FIRST'),
(5032, 'system', 2, 'First', '_FIRST'),
(5033, 'system', 1, 'Pr&eacute;c&eacute;dent', '_PREVIOUS'),
(5034, 'system', 2, 'Previous', '_PREVIOUS'),
(5035, 'system', 1, 'Suivant', '_NEXT'),
(5036, 'system', 2, 'Next', '_NEXT'),
(5037, 'system', 1, 'Dernier', '_LAST'),
(5038, 'system', 2, 'Last', '_LAST'),
(5039, 'system', 1, 'Choix de l''assureur', '_DIMS_LABEL_HEALTHCARE_COMPANY'),
(5040, 'system', 2, 'Choice healthcare company', '_DIMS_LABEL_HEALTHCARE_COMPANY'),
(5041, 'assurance', 1, 'Contrat collectif', '_DIMS_LABEL_ASSUR_TYPE_CHAMPS_CONTRAT_COLLECTIF'),
(5042, 'assurance', 1, 'Police', '_DIMS_LABEL_ASSUR_TYPE_CHAMPS_POLICE'),
(5043, 'assurance', 1, 'Assuré', '_DIMS_LABEL_ASSUR_TYPE_CHAMPS_ASSURE'),
(5044, 'assurance', 1, 'Adresse assuré', '_DIMS_LABEL_ASSUR_TYPE_CHAMPS_ADRESSE_ASSURE'),
(5045, 'assurance', 1, 'Couverture', '_DIMS_LABEL_ASSUR_TYPE_CHAMPS_COUVERTURE'),
(5046, 'assurance', 1, 'Paiement', '_DIMS_LABEL_ASSUR_TYPE_CHAMPS_PAIEMENT'),
(5047, 'assurance', 1, 'Rabais', '_DIMS_LABEL_ASSUR_TYPE_CHAMPS_RABAIS'),
(5048, 'assurance', 1, 'Liste des tâches', '_DIMS_LABEL_ASSUR_TASK_LIST'),
(5049, 'assurance', 1, 'Todo(s)', '_DIMS_LABEL_ASSUR_TASK_NUM'),
(5050, 'assurance', 1, 'Créer une tâche', '_DIMS_LABEL_ASSUR_TASK_CREATE'),
(5051, 'assurance', 1, 'Ajouter un todo', '_DIMS_LABEL_ASSUR_TASK_TITLE'),
(5052, 'assurance', 1, 'Se réfère au dossier consulté', '_DIMS_LABEL_ASSUR_TASK_LINK_FOLDER'),
(5053, 'assurance', 1, 'Destinataire(s)', '_DIMS_LABEL_ASSUR_TASK_RECIPIENTS'),
(5054, 'assurance', 1, 'Commentaires', '_DIMS_LABEL_ASSUR_TASK_COMMENTS'),
(5055, 'assurance', 1, 'Priorité', '_DIMS_LABEL_ASSUR_TASK_PRIORITY'),
(5056, 'assurance', 1, 'Faible', '_DIMS_LABEL_ASSUR_TASK_PRIORITY_LOW'),
(5057, 'assurance', 1, 'Normal', '_DIMS_LABEL_ASSUR_TASK_PRIORITY_NORMAL'),
(5058, 'assurance', 1, 'Haute', '_DIMS_LABEL_ASSUR_TASK_PRIORITY_HIGH'),
(5059, 'assurance', 1, 'Veuillez contrôler les valeurs renseignées', '_DIMS_LABEL_ASSUR_TASK_WARN_VALUE'),
(5060, 'assurance', 1, 'Voir le reste des tâches', '_DIMS_LABEL_ASSUR_TASK_SEE_OTHERS'),
(5061, 'assurance', 1, 'Vous-même', '_DIMS_LABEL_ASSUR_TASK_YOURSELF'),
(5062, 'assurance', 1, 'Valider ce todo', '_DIMS_LABEL_ASSUR_TASK_VALID'),
(5063, 'assurance', 1, 'Chef de famille', '_DIMS_LABEL_CHEF_FAMILLE'),
(5064, 'assurance', 1, 'Membres de famille', '_DIMS_LABEL_MEMBRE_FAMILLE'),
(5065, 'assurance', 1, 'Total des primes pour la famille', '_DIMS_LABEL_TOTAL_PRIME_FAMILLE'),
(5066, 'assurance', 1, 'Numéros de famille', '_DIMS_LABEL_NUM_FAMILLE'),
(5067, 'assurance', 1, 'Police N°', '_DIMS_LABEL_POLICE_NUM'),
(5068, 'assurance', 1, 'Prime', '_DIMS_LABEL_PRIME'),
(5069, 'assurance', 1, 'Numéro assuré', '_DIMS_LABEL_NUM_ASSUREE'),
(5070, 'assurance', 1, 'Numéro assuré bis', '_DIMS_LABEL_NUM_ASSURE_BIS'),
(5071, 'assurance', 1, 'Voir le compte bancaire associé', '_DIMS_LABEL_SEE_BANK_ACCOUNT'),
(5072, 'assurance', 1, 'Voir le détail des couverture', '_DIMS_LABEL_SEE_COVERAGE_DETAIL'),
(5073, 'assurance', 1, 'Informations bancaires', '_DIMS_LABEL_BANK_INFOS'),
(5074, 'assurance', 1, 'Nom compte', '_DIMS_LABEL_NAME_COMPTE'),
(5075, 'assurance', 1, 'N° compte', '_DIMS_LABEL_NUM_COMPTE'),
(5076, 'assurance', 1, 'N° clearing', '_DIMS_LABEL_NUM_CLEARING'),
(5077, 'assurance', 1, 'Détail de la police', '_DIMS_LABEL_DETAIL_POLICE'),
(5078, 'assurance', 1, 'Assureur', '_DIMS_LABEL_ASSUREUR'),
(5079, 'assurance', 1, 'Prime total', '_DIMS_LABEL_TOTAL_PRIME'),
(5080, 'assurance', 1, 'Contrat collectif', '_DIMS_LABEL_CONTRAT_COLLECTIF'),
(5081, 'assurance', 1, 'Couvertures comprises dans la police', '_DIMS_LABEL_COVERAGE_INCLUDE_POLICE'),
(5082, 'assurance', 1, 'Date début validité', '_DIMS_LABEL_DATE_BEGIN_VALID'),
(5083, 'assurance', 1, 'Date fin validité', '_DIMS_LABEL_DATE_END_VALID'),
(5084, 'assurance', 1, 'Franchise', '_DIMS_LABEL_FRANCHISE'),
(5085, 'assurance', 1, 'Liste des rabais', '_DIMS_LABEL_LIST_DISCOUNT'),
(5086, 'assurance', 1, 'Total net', '_DIMS_LABEL_TOTAL_NET'),
(5087, 'assurance', 1, 'Groupe age', '_DIMS_LABEL_GROUP_AGE'),
(5088, 'assurance', 1, 'Somme assurance', '_DIMS_LABEL_SOMME_ASSURANCE'),
(5089, 'assurance', 1, 'Voir les prestations offertes par la couverture', '_DIMS_LINK_PRESTATION_COVER'),
(5090, 'assurance', 1, 'Prime donnée à titre indicatif', '_DIMS_LABEL_WARN_INDICATIF'),
(5091, 'assurance', 1, 'Prestations couvertes', '_DIMS_LABEL_PRESTATION_COVERAGE'),
(5092, 'assurance', 1, 'Couverture', '_DIMS_LABEL_COVERAGE'),
(5093, 'assurance', 1, 'Prestation', '_DIMS_LABEL_PRESTATION'),
(5094, 'assurance', 1, 'Aucunes prestations', '_DIMS_LABEL_NO_PRESTATIONS'),
(5095, 'assurance', 1, 'Pas couvert', '_DIMS_LABEL_NOT_COVER'),
(5096, 'assurance', 1, 'N° assuré courtier', '_DIMS_LABEL_NUM_ASSURE_COURTIER'),
(5097, 'assurance', 1, 'Ajouter la photo', '_DIMS_LABEL_ADD_PHOTO'),
(5098, 'assurance', 1, 'Informations générales', '_DIMS_TAB_INFOS_GENERAL'),
(5099, 'assurance', 1, 'Famille', '_DIMS_TAB_FAMILLE'),
(5100, 'assurance', 1, 'Polices', '_DIMS_TAB_POLICES'),
(5101, 'assurance', 1, 'Historique des polices', '_DIMS_TAB_HIST_POLICES'),
(5102, 'assurance', 1, 'Documents', '_DIMS_TAB_DOCUMENT'),
(5103, 'assurance', 1, 'Historique des activités', '_DIMS_TAB_HIST_ACTIVITE'),
(5104, 'assurance', 1, 'Messagerie', '_DIMS_TAB_MESSAGERIE'),
(5105, 'assurance', 1, 'Contacts', '_DIMS_TAB_CONTACT'),
(5106, 'assurance', 1, 'Collaborateur', '_DIMS_TAB_COLLABORATEUR'),
(5107, 'assurance', 1, 'Historique des contrats collectifs', '_DIMS_TAB_HIST_CC'),
(5108, 'system', 1, 'Mod&egrave;le', '_DIMS_LABEL_TEMPLATE'),
(5109, 'system', 2, 'Template', '_DIMS_LABEL_TEMPLATE'),
(5110, 'assurance', 1, 'Liste des tâches', '_DIMS_LABEL_ASSUR_TASK_LIST'),
(5111, 'assurance', 1, 'Todo(s)', '_DIMS_LABEL_ASSUR_TASK_NUM'),
(5112, 'assurance', 1, 'Créer une tâche', '_DIMS_LABEL_ASSUR_TASK_CREATE'),
(5113, 'assurance', 1, 'Ajouter un todo', '_DIMS_LABEL_ASSUR_TASK_TITLE'),
(5114, 'assurance', 1, 'Se réfère au dossier consulté', '_DIMS_LABEL_ASSUR_TASK_LINK_FOLDER'),
(5115, 'assurance', 1, 'Destinataire(s)', '_DIMS_LABEL_ASSUR_TASK_RECIPIENTS'),
(5116, 'assurance', 1, 'Commentaires', '_DIMS_LABEL_ASSUR_TASK_COMMENTS'),
(5117, 'assurance', 1, 'Priorité', '_DIMS_LABEL_ASSUR_TASK_PRIORITY'),
(5118, 'assurance', 1, 'Faible', '_DIMS_LABEL_ASSUR_TASK_PRIORITY_LOW'),
(5119, 'assurance', 1, 'Normal', '_DIMS_LABEL_ASSUR_TASK_PRIORITY_NORMAL'),
(5120, 'assurance', 1, 'Haute', '_DIMS_LABEL_ASSUR_TASK_PRIORITY_HIGH'),
(5121, 'assurance', 1, 'Veuillez contrôler les valeurs renseignées', '_DIMS_LABEL_ASSUR_TASK_WARN_VALUE'),
(5122, 'assurance', 1, 'Voir le reste des tâches', '_DIMS_LABEL_ASSUR_TASK_SEE_OTHERS'),
(5123, 'assurance', 1, 'Vous-même', '_DIMS_LABEL_ASSUR_TASK_YOURSELF'),
(5124, 'assurance', 1, 'Valider ce todo', '_DIMS_LABEL_ASSUR_TASK_VALID'),
(5125, 'assurance', 1, 'Chef de famille', '_DIMS_LABEL_CHEF_FAMILLE'),
(5126, 'assurance', 1, 'Membres de famille', '_DIMS_LABEL_MEMBRE_FAMILLE'),
(5127, 'assurance', 1, 'Total des primes pour la famille', '_DIMS_LABEL_TOTAL_PRIME_FAMILLE'),
(5128, 'assurance', 1, 'Numéros de famille', '_DIMS_LABEL_NUM_FAMILLE'),
(5129, 'assurance', 1, 'Police N°', '_DIMS_LABEL_POLICE_NUM'),
(5130, 'assurance', 1, 'Prime', '_DIMS_LABEL_PRIME'),
(5131, 'assurance', 1, 'Numéro assuré', '_DIMS_LABEL_NUM_ASSUREE'),
(5132, 'assurance', 1, 'Numéro assuré bis', '_DIMS_LABEL_NUM_ASSURE_BIS'),
(5133, 'assurance', 1, 'Voir le compte bancaire associé', '_DIMS_LABEL_SEE_BANK_ACCOUNT'),
(5134, 'assurance', 1, 'Voir le détail des couverture', '_DIMS_LABEL_SEE_COVERAGE_DETAIL'),
(5135, 'assurance', 1, 'Informations bancaires', '_DIMS_LABEL_BANK_INFOS'),
(5136, 'assurance', 1, 'Nom compte', '_DIMS_LABEL_NAME_COMPTE'),
(5137, 'assurance', 1, 'N° compte', '_DIMS_LABEL_NUM_COMPTE'),
(5138, 'assurance', 1, 'N° clearing', '_DIMS_LABEL_NUM_CLEARING'),
(5139, 'assurance', 1, 'Détail de la police', '_DIMS_LABEL_DETAIL_POLICE'),
(5140, 'assurance', 1, 'Assureur', '_DIMS_LABEL_ASSUREUR'),
(5141, 'assurance', 1, 'Prime total', '_DIMS_LABEL_TOTAL_PRIME'),
(5142, 'assurance', 1, 'Contrat collectif', '_DIMS_LABEL_CONTRAT_COLLECTIF'),
(5143, 'assurance', 1, 'Couvertures comprises dans la police', '_DIMS_LABEL_COVERAGE_INCLUDE_POLICE'),
(5144, 'assurance', 1, 'Date début validité', '_DIMS_LABEL_DATE_BEGIN_VALID'),
(5145, 'assurance', 1, 'Date fin validité', '_DIMS_LABEL_DATE_END_VALID'),
(5146, 'assurance', 1, 'Franchise', '_DIMS_LABEL_FRANCHISE'),
(5147, 'assurance', 1, 'Liste des rabais', '_DIMS_LABEL_LIST_DISCOUNT'),
(5148, 'assurance', 1, 'Total net', '_DIMS_LABEL_TOTAL_NET'),
(5149, 'assurance', 1, 'Groupe age', '_DIMS_LABEL_GROUP_AGE'),
(5150, 'assurance', 1, 'Somme assurance', '_DIMS_LABEL_SOMME_ASSURANCE'),
(5151, 'assurance', 1, 'Voir les prestations offertes par la couverture', '_DIMS_LINK_PRESTATION_COVER'),
(5152, 'assurance', 1, 'Prime donnée à titre indicatif', '_DIMS_LABEL_WARN_INDICATIF'),
(5153, 'assurance', 1, 'Prestations couvertes', '_DIMS_LABEL_PRESTATION_COVERAGE'),
(5154, 'assurance', 1, 'Couverture', '_DIMS_LABEL_COVERAGE'),
(5155, 'assurance', 1, 'Prestation', '_DIMS_LABEL_PRESTATION'),
(5156, 'assurance', 1, 'Aucunes prestations', '_DIMS_LABEL_NO_PRESTATIONS'),
(5157, 'assurance', 1, 'Pas couvert', '_DIMS_LABEL_NOT_COVER'),
(5158, 'assurance', 1, 'N° assuré courtier', '_DIMS_LABEL_NUM_ASSURE_COURTIER'),
(5159, 'assurance', 1, 'Ajouter la photo', '_DIMS_LABEL_ADD_PHOTO'),
(5161, 'assurance', 1, 'Famille', '_DIMS_TAB_FAMILLE'),
(5162, 'assurance', 1, 'Polices', '_DIMS_TAB_POLICES'),
(5163, 'assurance', 1, 'Historique des polices', '_DIMS_TAB_HIST_POLICES'),
(5164, 'assurance', 1, 'Documents', '_DIMS_TAB_DOCUMENT'),
(5165, 'assurance', 1, 'Historique des activités', '_DIMS_TAB_HIST_ACTIVITE'),
(5166, 'assurance', 1, 'Messagerie', '_DIMS_TAB_MESSAGERIE'),
(5167, 'assurance', 1, 'Contacts', '_DIMS_TAB_CONTACT'),
(5168, 'assurance', 1, 'Collaborateur', '_DIMS_TAB_COLLABORATEUR'),
(5169, 'assurance', 1, 'Historique des contrats collectifs', '_DIMS_TAB_HIST_CC'),
(5170, 'assurance', 1, 'Contrat collectif', '_DIMS_TAB_CONTRAT_COLL'),
(5171, 'assurance', 1, 'Date de naissance', '_DIMS_LABEL_BIRTHDATE'),
(5172, 'assurance', 1, 'Nom du compte', '_DIMS_LABEL_NAME_ACCOUNT'),
(5173, 'assurance', 1, 'Code de paiement', '_DIMS_LABEL_PAY_CODE'),
(5174, 'assurance', 1, 'Communication', '_DIMS_LABEL_COMMUNICATION'),
(5175, 'assurance', 1, 'Nombre d''employé', '_DIMS_LABEL_NB_EMPLOYE'),
(5176, 'assurance', 1, 'Nombre d''employé assuré', '_DIMS_LABEL_NB_EMPLOYE_ASSURE'),
(5177, 'assurance', 1, 'Nombre d''assuré', '_DIMS_LABEL_NB_ASSURE'),
(5178, 'assurance', 1, 'Collaborateur', '_DIMS_LABEL_COLLABORATEUR'),
(5179, 'assurance', 1, 'Numéro de contrat', '_DIMS_LABEL_NUM_CONTRAT'),
(5180, 'assurance', 1, 'Date d''entrée en vigueur', '_DIMS_LABEL_DATE_DEB_CONTRAT'),
(5181, 'assurance', 1, 'Date du dernier avenant', '_DIMS_LABEL_DATE_LAST_RIDER'),
(5182, 'assurance', 1, 'Date de dernière adaptation', '_DIMS_LABEL_DATE_LAST_ADAPT'),
(5183, 'assurance', 1, 'Nombre d''employés compris dans le contrat', '_DIMS_LABEL_NB_EMPLOYE_CC'),
(5184, 'assurance', 1, 'Personne assurable', '_DIMS_LABEL_PERSONNE_ASSURABLE'),
(5185, 'assurance', 1, 'Payeur direct', '_DIMS_LABEL_PAYEUR_DIRECT'),
(5186, 'assurance', 1, 'Assurance obligatoire', '_DIMS_LABEL_ASSURANCE_OBLIG'),
(5187, 'assurance', 1, 'Régions de prime', '_DIMS_LABEL_REGION_PRIME'),
(5188, 'assurance', 1, 'Gestion administrative', '_DIMS_LABEL_GESTION_ADMIN'),
(5189, 'assurance', 1, 'Expédition des cartificats', '_DIMS_LABEL_EXPEDITION_CERTIF'),
(5190, 'assurance', 1, 'Facturation mois en cours', '_DIMS_LABEL_FACTURATION_MOIS_ENCOURS'),
(5191, 'assurance', 1, 'Particularités', '_DIMS_LABEL_PARTICULARITE'),
(5192, 'assurance', 1, 'Couvertures comprises', '_DIMS_LABEL_COUVERTURES_COMPRISE'),
(5193, 'assurance', 1, 'Nom', '_DIMS_LABEL_NAME_CONTRAT'),
(5194, 'assurance', 1, 'Code courtier', '_DIMS_LABEL_CODE_COURTIER'),
(5195, 'assurance', 1, 'Nombre de contrats collectifs', '_DIMS_LABEL_NB_CC'),
(5196, 'assurance', 1, 'Nombre de couverture souscrites', '_DIMS_LABEL_NB_COVER_SUBSCRIBE'),
(5197, 'system', 1, 'Fichier incorrect', '_DIMS_LABEL_INCORRECT_FILE'),
(5198, 'system', 2, 'File not correct', '_DIMS_LABEL_INCORRECT_FILE'),
(5199, 'system', 1, 'Fichier import&eacute;', '_DIMS_LABEL_FILE_IMPORTED'),
(5200, 'system', 2, 'File imported', '_DIMS_LABEL_FILE_IMPORTED'),
(5201, 'system', 1, 'Import en cours', '_DIMS_LABEL_IMPORT_IN_PROGRESS'),
(5202, 'system', 2, 'Import in progress', '_DIMS_LABEL_IMPORT_IN_PROGRESS'),
(5203, 'system', 1, 'Import termin&eacute;', '_DIMS_LABEL_IMPORT_FINISHED'),
(5204, 'system', 2, 'Import terminated', '_DIMS_LABEL_IMPORT_FINISHED'),
(5205, 'system', 1, 'Synchroniser', '_LABEL_SYNCHRONIZE'),
(5206, 'system', 2, 'Synchronize', '_LABEL_SYNCHRONIZE'),
(5207, 'system', 1, 'Synchronisation', '_LABEL_SYNCHRO'),
(5208, 'system', 2, 'Synchronization', '_LABEL_SYNCHRO'),
(5209, 'system', 1, 'Script de bas de page', '_WCE_SCRIPT_BOTTOM'),
(5210, 'system', 2, 'Bottom script', '_WCE_SCRIPT_BOTTOM'),
(5211, 'system', 1, 'Dossiers', '_DIMS_DOSSIERS'),
(5212, 'system', 2, 'Folders', '_DIMS_DOSSIERS'),
(5213, 'notaire', 1, 'Interventions', '_DIMS_TITLE_INTERV'),
(5214, 'notaire', 1, 'Enregistrer une intervention', '_DIMS_TITLE_ADD_INTERVENTION'),
(5215, 'notaire', 1, 'Mode d''échange', '_DIMS_LABEL_MODE_COMMUNICATION'),
(5216, 'notaire', 1, 'Personne contacté', '_DIMS_LABEL_PERSONNE_CONTACTED'),
(5217, 'notaire', 1, 'Fichier joint', '_DIMS_LABEL_FICHIER_ATTACHED'),
(5218, 'notaire', 1, 'Téléphonique', '_DIMS_LABEL_INTERV_PHONE'),
(5219, 'notaire', 1, 'E-mail', '_DIMS_LABEL_INTERV_EMAIL'),
(5220, 'notaire', 1, 'Courrier', '_DIMS_LABEL_INTERV_MAIL'),
(5221, 'notaire', 1, 'Chat', '_DIMS_LABEL_INTERV_CHAT'),
(5222, 'notaire', 1, 'Rendez-vous', '_DIMS_LABEL_INTERV_RDV'),
(5223, 'notaire', 1, 'Annuaire', '_DIMS_LABEL_ANNUAIRE'),
(5224, 'notaire', 1, 'Pièces d''identité', '_DIMS_LABEL_IDENT_FILE'),
(5225, 'notaire', 1, 'Votre recherche pour', '_DIMS_TEXT_YOUR_SEARCH_FOR'),
(5226, 'system', 1, 'Etes vous sûr ?', '_DIMS_LABEL_CONFIRM_ACTION'),
(5227, 'system', 2, 'Are you sure ?', '_DIMS_LABEL_CONFIRM_ACTION'),
(5228, 'assurance', 1, 'Entrant', '_TYPE_IN'),
(5229, 'assurance', 2, 'In', '_TYPE_IN'),
(5230, 'assurance', 1, 'Sortant', '_TYPE_OUT'),
(5231, 'assurance', 2, 'Out', '_TYPE_OUT');

--
-- Table structure for table `dims_contact`
--

DROP TABLE IF EXISTS `dims_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_type` int(10) DEFAULT '-1',
  `id_ldap` int(10) unsigned DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `date_creation` varchar(14) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `fax` varchar(32) DEFAULT NULL,
  `comments` text,
  `address` text,
  `mobile` varchar(32) DEFAULT NULL,
  `service` varchar(64) DEFAULT NULL,
  `function` varchar(64) DEFAULT NULL,
  `postalcode` varchar(16) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `timezone` double NOT NULL DEFAULT '0',
  `presentation` text,
  `id_module` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_user_attach` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `lastname` (`lastname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_contact`
--

LOCK TABLES `dims_contact` WRITE;
/*!40000 ALTER TABLE `dims_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_country`
--

DROP TABLE IF EXISTS `dims_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_country` (
  `iso` varchar(2) NOT NULL DEFAULT '',
  `name` varchar(80) DEFAULT NULL,
  `printable_name` varchar(80) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  `phoneprefix` int(10) DEFAULT NULL,
  PRIMARY KEY (`iso`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_country`
--

LOCK TABLES `dims_country` WRITE;
/*!40000 ALTER TABLE `dims_country` DISABLE KEYS */;
INSERT INTO `dims_country` VALUES ('AF','AFGHANISTAN','Afghanistan','AFG',4,93),('AL','ALBANIA','Albania','ALB',8,355),('DZ','ALGERIA','Algeria','DZA',12,213),('AS','AMERICAN SAMOA','American Samoa','ASM',16,1),('AD','ANDORRA','Andorra','AND',20,376),('AO','ANGOLA','Angola','AGO',24,244),('AI','ANGUILLA','Anguilla','AIA',660,1),('AQ','ANTARCTICA','Antarctica',NULL,NULL,672),('AG','ANTIGUA AND BARBUDA','Antigua and Barbuda','ATG',28,1),('AR','ARGENTINA','Argentina','ARG',32,54),('AM','ARMENIA','Armenia','ARM',51,374),('AW','ARUBA','Aruba','ABW',533,297),('AU','AUSTRALIA','Australia','AUS',36,61),('AT','AUSTRIA','Austria','AUT',40,43),('AZ','AZERBAIJAN','Azerbaijan','AZE',31,994),('BS','BAHAMAS','Bahamas','BHS',44,1),('BH','BAHRAIN','Bahrain','BHR',48,973),('BD','BANGLADESH','Bangladesh','BGD',50,880),('BB','BARBADOS','Barbados','BRB',52,1),('BY','BELARUS','Belarus','BLR',112,375),('BE','BELGIUM','Belgium','BEL',56,32),('BZ','BELIZE','Belize','BLZ',84,501),('BJ','BENIN','Benin','BEN',204,229),('BM','BERMUDA','Bermuda','BMU',60,1),('BT','BHUTAN','Bhutan','BTN',64,975),('BO','BOLIVIA','Bolivia','BOL',68,591),('BA','BOSNIA AND HERZEGOVINA','Bosnia and Herzegovina','BIH',70,387),('BW','BOTSWANA','Botswana','BWA',72,267),('BV','BOUVET ISLAND','Bouvet Island',NULL,NULL,0),('BR','BRAZIL','Brazil','BRA',76,55),('IO','BRITISH INDIAN OCEAN TERRITORY','British Indian Ocean Territory',NULL,NULL,0),('BN','BRUNEI DARUSSALAM','Brunei Darussalam','BRN',96,673),('BG','BULGARIA','Bulgaria','BGR',100,359),('BF','BURKINA FASO','Burkina Faso','BFA',854,226),('BI','BURUNDI','Burundi','BDI',108,257),('KH','CAMBODIA','Cambodia','KHM',116,855),('CM','CAMEROON','Cameroon','CMR',120,237),('CA','CANADA','Canada','CAN',124,1),('CV','CAPE VERDE','Cape Verde','CPV',132,238),('KY','CAYMAN ISLANDS','Cayman Islands','CYM',136,1),('CF','CENTRAL AFRICAN REPUBLIC','Central African Republic','CAF',140,236),('TD','CHAD','Chad','TCD',148,235),('CL','CHILE','Chile','CHL',152,56),('CN','CHINA','China','CHN',156,86),('CX','CHRISTMAS ISLAND','Christmas Island',NULL,NULL,61),('CC','COCOS (KEELING) ISLANDS','Cocos (Keeling) Islands',NULL,NULL,61),('CO','COLOMBIA','Colombia','COL',170,57),('KM','COMOROS','Comoros','COM',174,269),('CG','CONGO','Congo','COG',178,0),('CD','CONGO, THE DEMOCRATIC REPUBLIC OF THE','Congo, the Democratic Republic of the','COD',180,0),('CK','COOK ISLANDS','Cook Islands','COK',184,682),('CR','COSTA RICA','Costa Rica','CRI',188,506),('CI','COTE D\'IVOIRE','Cote D\'Ivoire','CIV',384,385),('HR','CROATIA','Croatia','HRV',191,0),('CU','CUBA','Cuba','CUB',192,53),('CY','CYPRUS','Cyprus','CYP',196,357),('CZ','CZECH REPUBLIC','Czech Republic','CZE',203,420),('DK','DENMARK','Denmark','DNK',208,45),('DJ','DJIBOUTI','Djibouti','DJI',262,253),('DM','DOMINICA','Dominica','DMA',212,1),('DO','DOMINICAN REPUBLIC','Dominican Republic','DOM',214,1),('EC','ECUADOR','Ecuador','ECU',218,593),('EG','EGYPT','Egypt','EGY',818,20),('SV','EL SALVADOR','El Salvador','SLV',222,503),('GQ','EQUATORIAL GUINEA','Equatorial Guinea','GNQ',226,240),('ER','ERITREA','Eritrea','ERI',232,291),('EE','ESTONIA','Estonia','EST',233,372),('ET','ETHIOPIA','Ethiopia','ETH',231,251),('FK','FALKLAND ISLANDS (MALVINAS)','Falkland Islands (Malvinas)','FLK',238,500),('FO','FAROE ISLANDS','Faroe Islands','FRO',234,298),('FJ','FIJI','Fiji','FJI',242,679),('FI','FINLAND','Finland','FIN',246,358),('FR','FRANCE','France','FRA',250,33),('GF','FRENCH GUIANA','French Guiana','GUF',254,0),('PF','FRENCH POLYNESIA','French Polynesia','PYF',258,689),('TF','FRENCH SOUTHERN TERRITORIES','French Southern Territories',NULL,NULL,0),('GA','GABON','Gabon','GAB',266,241),('GM','GAMBIA','Gambia','GMB',270,220),('GE','GEORGIA','Georgia','GEO',268,995),('DE','GERMANY','Germany','DEU',276,49),('GH','GHANA','Ghana','GHA',288,233),('GI','GIBRALTAR','Gibraltar','GIB',292,350),('GR','GREECE','Greece','GRC',300,30),('GL','GREENLAND','Greenland','GRL',304,299),('GD','GRENADA','Grenada','GRD',308,1),('GP','GUADELOUPE','Guadeloupe','GLP',312,0),('GU','GUAM','Guam','GUM',316,1671),('GT','GUATEMALA','Guatemala','GTM',320,502),('GN','GUINEA','Guinea','GIN',324,224),('GW','GUINEA-BISSAU','Guinea-Bissau','GNB',624,245),('GY','GUYANA','Guyana','GUY',328,592),('HT','HAITI','Haiti','HTI',332,509),('HM','HEARD ISLAND AND MCDONALD ISLANDS','Heard Island and Mcdonald Islands',NULL,NULL,0),('VA','HOLY SEE (VATICAN CITY STATE)','Holy See (Vatican City State)','VAT',336,39),('HN','HONDURAS','Honduras','HND',340,504),('HK','HONG KONG','Hong Kong','HKG',344,852),('HU','HUNGARY','Hungary','HUN',348,36),('IS','ICELAND','Iceland','ISL',352,354),('IN','INDIA','India','IND',356,91),('ID','INDONESIA','Indonesia','IDN',360,62),('IR','IRAN, ISLAMIC REPUBLIC OF','Iran, Islamic Republic of','IRN',364,98),('IQ','IRAQ','Iraq','IRQ',368,964),('IE','IRELAND','Ireland','IRL',372,353),('IL','ISRAEL','Israel','ISR',376,972),('IT','ITALY','Italy','ITA',380,39),('JM','JAMAICA','Jamaica','JAM',388,1876),('JP','JAPAN','Japan','JPN',392,81),('JO','JORDAN','Jordan','JOR',400,962),('KZ','KAZAKHSTAN','Kazakhstan','KAZ',398,7),('KE','KENYA','Kenya','KEN',404,254),('KI','KIRIBATI','Kiribati','KIR',296,686),('KP','KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF','Korea, Democratic People\'s Republic of','PRK',408,0),('KR','KOREA, REPUBLIC OF','Korea, Republic of','KOR',410,0),('KW','KUWAIT','Kuwait','KWT',414,965),('KG','KYRGYZSTAN','Kyrgyzstan','KGZ',417,996),('LA','LAO PEOPLE\'S DEMOCRATIC REPUBLIC','Lao People\'s Democratic Republic','LAO',418,856),('LV','LATVIA','Latvia','LVA',428,371),('LB','LEBANON','Lebanon','LBN',422,961),('LS','LESOTHO','Lesotho','LSO',426,266),('LR','LIBERIA','Liberia','LBR',430,231),('LY','LIBYAN ARAB JAMAHIRIYA','Libyan Arab Jamahiriya','LBY',434,218),('LI','LIECHTENSTEIN','Liechtenstein','LIE',438,423),('LT','LITHUANIA','Lithuania','LTU',440,370),('LU','LUXEMBOURG','Luxembourg','LUX',442,352),('MO','MACAO','Macao','MAC',446,853),('MK','MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','Macedonia, the Former Yugoslav Republic of','MKD',807,389),('MG','MADAGASCAR','Madagascar','MDG',450,261),('MW','MALAWI','Malawi','MWI',454,265),('MY','MALAYSIA','Malaysia','MYS',458,60),('MV','MALDIVES','Maldives','MDV',462,960),('ML','MALI','Mali','MLI',466,223),('MT','MALTA','Malta','MLT',470,356),('MH','MARSHALL ISLANDS','Marshall Islands','MHL',584,692),('MQ','MARTINIQUE','Martinique','MTQ',474,0),('MR','MAURITANIA','Mauritania','MRT',478,222),('MU','MAURITIUS','Mauritius','MUS',480,230),('YT','MAYOTTE','Mayotte',NULL,NULL,262),('MX','MEXICO','Mexico','MEX',484,52),('FM','MICRONESIA, FEDERATED STATES OF','Micronesia, Federated States of','FSM',583,691),('MD','MOLDOVA, REPUBLIC OF','Moldova, Republic of','MDA',498,373),('MC','MONACO','Monaco','MCO',492,377),('MN','MONGOLIA','Mongolia','MNG',496,976),('MS','MONTSERRAT','Montserrat','MSR',500,1664),('MA','MOROCCO','Morocco','MAR',504,212),('MZ','MOZAMBIQUE','Mozambique','MOZ',508,258),('MM','MYANMAR','Myanmar','MMR',104,0),('NA','NAMIBIA','Namibia','NAM',516,264),('NR','NAURU','Nauru','NRU',520,674),('NP','NEPAL','Nepal','NPL',524,977),('NL','NETHERLANDS','Netherlands','NLD',528,31),('AN','NETHERLANDS ANTILLES','Netherlands Antilles','ANT',530,599),('NC','NEW CALEDONIA','New Caledonia','NCL',540,687),('NZ','NEW ZEALAND','New Zealand','NZL',554,64),('NI','NICARAGUA','Nicaragua','NIC',558,505),('NE','NIGER','Niger','NER',562,227),('NG','NIGERIA','Nigeria','NGA',566,234),('NU','NIUE','Niue','NIU',570,683),('NF','NORFOLK ISLAND','Norfolk Island','NFK',574,672),('MP','NORTHERN MARIANA ISLANDS','Northern Mariana Islands','MNP',580,1670),('NO','NORWAY','Norway','NOR',578,47),('OM','OMAN','Oman','OMN',512,968),('PK','PAKISTAN','Pakistan','PAK',586,92),('PW','PALAU','Palau','PLW',585,680),('PS','PALESTINIAN TERRITORY, OCCUPIED','Palestinian Territory, Occupied',NULL,NULL,0),('PA','PANAMA','Panama','PAN',591,507),('PG','PAPUA NEW GUINEA','Papua New Guinea','PNG',598,675),('PY','PARAGUAY','Paraguay','PRY',600,595),('PE','PERU','Peru','PER',604,51),('PH','PHILIPPINES','Philippines','PHL',608,63),('PN','PITCAIRN','Pitcairn','PCN',612,870),('PL','POLAND','Poland','POL',616,48),('PT','PORTUGAL','Portugal','PRT',620,351),('PR','PUERTO RICO','Puerto Rico','PRI',630,1),('QA','QATAR','Qatar','QAT',634,974),('RE','REUNION','Reunion','REU',638,0),('RO','ROMANIA','Romania','ROM',642,40),('RU','RUSSIAN FEDERATION','Russian Federation','RUS',643,7),('RW','RWANDA','Rwanda','RWA',646,250),('SH','SAINT HELENA','Saint Helena','SHN',654,290),('KN','SAINT KITTS AND NEVIS','Saint Kitts and Nevis','KNA',659,1869),('LC','SAINT LUCIA','Saint Lucia','LCA',662,1758),('PM','SAINT PIERRE AND MIQUELON','Saint Pierre and Miquelon','SPM',666,508),('VC','SAINT VINCENT AND THE GRENADINES','Saint Vincent and the Grenadines','VCT',670,1784),('WS','SAMOA','Samoa','WSM',882,685),('SM','SAN MARINO','San Marino','SMR',674,378),('ST','SAO TOME AND PRINCIPE','Sao Tome and Principe','STP',678,239),('SA','SAUDI ARABIA','Saudi Arabia','SAU',682,966),('SN','SENEGAL','Senegal','SEN',686,221),('CS','SERBIA AND MONTENEGRO','Serbia and Montenegro',NULL,NULL,381),('SC','SEYCHELLES','Seychelles','SYC',690,248),('SL','SIERRA LEONE','Sierra Leone','SLE',694,232),('SG','SINGAPORE','Singapore','SGP',702,65),('SK','SLOVAKIA','Slovakia','SVK',703,421),('SI','SLOVENIA','Slovenia','SVN',705,386),('SB','SOLOMON ISLANDS','Solomon Islands','SLB',90,677),('SO','SOMALIA','Somalia','SOM',706,252),('ZA','SOUTH AFRICA','South Africa','ZAF',710,27),('GS','SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS','South Georgia and the South Sandwich Islands',NULL,NULL,82),('ES','SPAIN','Spain','ESP',724,34),('LK','SRI LANKA','Sri Lanka','LKA',144,94),('SD','SUDAN','Sudan','SDN',736,249),('SR','SURINAME','Suriname','SUR',740,597),('SJ','SVALBARD AND JAN MAYEN','Svalbard and Jan Mayen','SJM',744,0),('SZ','SWAZILAND','Swaziland','SWZ',748,268),('SE','SWEDEN','Sweden','SWE',752,46),('CH','SWITZERLAND','Switzerland','CHE',756,41),('SY','SYRIAN ARAB REPUBLIC','Syrian Arab Republic','SYR',760,963),('TW','TAIWAN, PROVINCE OF CHINA','Taiwan, Province of China','TWN',158,886),('TJ','TAJIKISTAN','Tajikistan','TJK',762,992),('TZ','TANZANIA, UNITED REPUBLIC OF','Tanzania, United Republic of','TZA',834,255),('TH','THAILAND','Thailand','THA',764,66),('TL','TIMOR-LESTE','Timor-Leste',NULL,NULL,670),('TG','TOGO','Togo','TGO',768,228),('TK','TOKELAU','Tokelau','TKL',772,690),('TO','TONGA','Tonga','TON',776,676),('TT','TRINIDAD AND TOBAGO','Trinidad and Tobago','TTO',780,1868),('TN','TUNISIA','Tunisia','TUN',788,216),('TR','TURKEY','Turkey','TUR',792,90),('TM','TURKMENISTAN','Turkmenistan','TKM',795,993),('TC','TURKS AND CAICOS ISLANDS','Turks and Caicos Islands','TCA',796,1649),('TV','TUVALU','Tuvalu','TUV',798,688),('UG','UGANDA','Uganda','UGA',800,256),('UA','UKRAINE','Ukraine','UKR',804,380),('AE','UNITED ARAB EMIRATES','United Arab Emirates','ARE',784,971),('GB','UNITED KINGDOM','United Kingdom','GBR',826,44),('US','UNITED STATES','United States','USA',840,1),('UM','UNITED STATES MINOR OUTLYING ISLANDS','United States Minor Outlying Islands',NULL,NULL,1340),('UY','URUGUAY','Uruguay','URY',858,598),('UZ','UZBEKISTAN','Uzbekistan','UZB',860,998),('VU','VANUATU','Vanuatu','VUT',548,678),('VE','VENEZUELA','Venezuela','VEN',862,58),('VN','VIET NAM','Viet Nam','VNM',704,84),('VG','VIRGIN ISLANDS, BRITISH','Virgin Islands, British','VGB',92,1340),('VI','VIRGIN ISLANDS, U.S.','Virgin Islands, U.s.','VIR',850,1340),('WF','WALLIS AND FUTUNA','Wallis and Futuna','WLF',876,681),('EH','WESTERN SAHARA','Western Sahara','ESH',732,0),('YE','YEMEN','Yemen','YEM',887,967),('ZM','ZAMBIA','Zambia','ZMB',894,260),('ZW','ZIMBABWE','Zimbabwe','ZWE',716,263);
/*!40000 ALTER TABLE `dims_country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_documents_ext`
--

DROP TABLE IF EXISTS `dims_documents_ext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_documents_ext` (
  `ext` varchar(10) DEFAULT NULL,
  `filetype` varchar(16) DEFAULT NULL,
  KEY `ext` (`ext`),
  KEY `filetype` (`filetype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_documents_ext`
--

LOCK TABLES `dims_documents_ext` WRITE;
/*!40000 ALTER TABLE `dims_documents_ext` DISABLE KEYS */;
INSERT INTO `dims_documents_ext` VALUES ('odt','document'),('doc','document'),('xls','spreadsheet'),('mp3','audio'),('wav','audio'),('ogg','audio'),('jpg','image'),('jpeg','image'),('png','image'),('gif','image'),('psd','image'),('xcf','image'),('svg','image'),('pdf','document'),('avi','video'),('wmv','video'),('ogm','video'),('mpg','video'),('mpeg','video'),('zip','archive'),('tgz','archive'),('gz','archive'),('rar','archive'),('bz2','archive'),('ace','archive');
/*!40000 ALTER TABLE `dims_documents_ext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_documents_file`
--

DROP TABLE IF EXISTS `dims_documents_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_documents_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `timestp_create` bigint(14) DEFAULT NULL,
  `timestp_modify` bigint(14) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT '0',
  `extension` varchar(20) DEFAULT NULL,
  `parents` varchar(255) DEFAULT NULL,
  `content` longtext,
  `nbclick` int(10) unsigned DEFAULT '0',
  `id_folder` int(10) unsigned DEFAULT '0',
  `id_user_modify` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `id_record` varchar(255) DEFAULT NULL,
  `id_object` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_group` (`id_workspace`),
  KEY `id_module` (`id_module`),
  KEY `name` (`name`),
  KEY `id_folder` (`id_folder`),
  KEY `extension` (`extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_documents_file`
--

LOCK TABLES `dims_documents_file` WRITE;
/*!40000 ALTER TABLE `dims_documents_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_documents_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_documents_folder`
--

DROP TABLE IF EXISTS `dims_documents_folder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_documents_folder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `parents` varchar(255) DEFAULT NULL,
  `timestp_create` bigint(14) DEFAULT NULL,
  `timestp_modify` bigint(14) DEFAULT NULL,
  `nbelements` int(10) unsigned NOT NULL DEFAULT '0',
  `id_folder` int(10) unsigned DEFAULT '0',
  `id_user_modify` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `id_record` varchar(255) DEFAULT NULL,
  `id_object` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_group` (`id_workspace`),
  KEY `id_module` (`id_module`),
  KEY `id_folder` (`id_folder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_documents_folder`
--

LOCK TABLES `dims_documents_folder` WRITE;
/*!40000 ALTER TABLE `dims_documents_folder` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_documents_folder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_domain`
--

DROP TABLE IF EXISTS `dims_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) DEFAULT NULL,
  `access` tinyint(4) NOT NULL DEFAULT '2',
  `ssl` tinyint(4) NOT NULL DEFAULT '0',
  `webmail_http_code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_domain`
--

LOCK TABLES `dims_domain` WRITE;
/*!40000 ALTER TABLE `dims_domain` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_error`
--

DROP TABLE IF EXISTS `dims_error`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_error` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_create` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `method` varchar(5) DEFAULT NULL,
  `query` longtext,
  `message` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_error`
--

LOCK TABLES `dims_error` WRITE;
/*!40000 ALTER TABLE `dims_error` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_error` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_favorite`
--

DROP TABLE IF EXISTS `dims_favorite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `dims_favorite` (
  `id_user` int(11) NOT NULL,
  `id_globalobject` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `note` float DEFAULT NULL,
  UNIQUE KEY `id_user` (`id_user`,`id_globalobject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_favorite`
--

LOCK TABLES `dims_favorite` WRITE;
/*!40000 ALTER TABLE `dims_favorite` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_favorite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_globalobject`
--

DROP TABLE IF EXISTS `dims_globalobject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_globalobject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_module_type` int(11) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_object` int(11) NOT NULL DEFAULT '0',
  `id_record` int(11) NOT NULL DEFAULT '0',
  `title` varchar(200) DEFAULT NULL,
  `link_title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_module` (`id_module`),
  KEY `id_module_2` (`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_globalobject`
--

LOCK TABLES `dims_globalobject` WRITE;
/*!40000 ALTER TABLE `dims_globalobject` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_globalobject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_group`
--

DROP TABLE IF EXISTS `dims_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_group` int(10) unsigned DEFAULT '0',
  `label` varchar(255) DEFAULT NULL,
  `system` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `protected` tinyint(1) unsigned DEFAULT '0',
  `parents` varchar(100) DEFAULT NULL,
  `depth` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `shared` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `code` varchar(255) DEFAULT NULL,
  `reference` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `system` (`system`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_group`
--

LOCK TABLES `dims_group` WRITE;
/*!40000 ALTER TABLE `dims_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_group_user`
--

DROP TABLE IF EXISTS `dims_group_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_group_user` (
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_group` int(10) unsigned NOT NULL DEFAULT '0',
  `adminlevel` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_group`,`id_user`),
  KEY `id_user` (`id_user`),
  KEY `id_group` (`id_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_group_user`
--

LOCK TABLES `dims_group_user` WRITE;
/*!40000 ALTER TABLE `dims_group_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_group_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_intercom`
--

DROP TABLE IF EXISTS `dims_intercom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_intercom` (
  `host` varchar(45) NOT NULL DEFAULT '',
  `dims` varchar(45) NOT NULL DEFAULT '',
  `ip` varchar(20) NOT NULL DEFAULT '',
  `port` int(10) NOT NULL,
  `securityKey` varchar(45) DEFAULT NULL,
  `timestp_modify` bigint(14) NOT NULL,
  PRIMARY KEY (`host`,`dims`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_intercom`
--

LOCK TABLES `dims_intercom` WRITE;
/*!40000 ALTER TABLE `dims_intercom` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_intercom` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_interest`
--

DROP TABLE IF EXISTS `dims_interest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_interest` (
  `id` tinyint(4) NOT NULL,
  `nom_interest` varchar(50) DEFAULT NULL,
  `definition_int` varchar(255) DEFAULT NULL,
  `id_module` int(11) NOT NULL,
  `id_type_module` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_interest`
--

LOCK TABLES `dims_interest` WRITE;
/*!40000 ALTER TABLE `dims_interest` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_interest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_jabber_connecteduser`
--

DROP TABLE IF EXISTS `dims_jabber_connecteduser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_jabber_connecteduser` (
  `jabberId` varchar(100) NOT NULL DEFAULT '',
  `timestp` bigint(14) NOT NULL,
  `state` tinyint(4) NOT NULL,
  PRIMARY KEY (`jabberId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_jabber_connecteduser`
--

LOCK TABLES `dims_jabber_connecteduser` WRITE;
/*!40000 ALTER TABLE `dims_jabber_connecteduser` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_jabber_connecteduser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_keywords`
--

DROP TABLE IF EXISTS `dims_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_keywords` (
  `id` bigint(20) NOT NULL,
  `word` varchar(64) DEFAULT NULL,
  `length` tinyint(2) NOT NULL DEFAULT '0',
  `count` int(10) NOT NULL DEFAULT '0',
  `soundex` varchar(4) DEFAULT NULL,
  `code` int(4) NOT NULL,
  `stype` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 texte,1 num, 2 float, 3 email, 4 tel,5 date',
  `metaphone` varchar(10) DEFAULT NULL,
  `flascii` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `soundex` (`soundex`),
  KEY `metaphone` (`metaphone`),
  KEY `flascii` (`flascii`),
  KEY `flascii_2` (`flascii`),
  KEY `flascii_3` (`flascii`),
  KEY `code` (`code`),
  KEY `index1` (`word`),
  KEY `index2` (`length`,`word`),
  KEY `stype` (`stype`,`flascii`,`code`,`metaphone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_keywords`
--

LOCK TABLES `dims_keywords` WRITE;
/*!40000 ALTER TABLE `dims_keywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_keywords_blacklist`
--

DROP TABLE IF EXISTS `dims_keywords_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_keywords_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(64) DEFAULT NULL,
  `length` tinyint(2) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_keywords_blacklist`
--

LOCK TABLES `dims_keywords_blacklist` WRITE;
/*!40000 ALTER TABLE `dims_keywords_blacklist` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_keywords_blacklist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_keywords_campaigncache`
--

DROP TABLE IF EXISTS `dims_keywords_campaigncache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_keywords_campaigncache` (
  `id_campaign` int(11) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_object` int(11) NOT NULL DEFAULT '0',
  `id_record` int(11) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  KEY `id_campaign` (`id_campaign`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_keywords_campaigncache`
--

LOCK TABLES `dims_keywords_campaigncache` WRITE;
/*!40000 ALTER TABLE `dims_keywords_campaigncache` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_keywords_campaigncache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_keywords_corresp`
--

DROP TABLE IF EXISTS `dims_keywords_corresp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_keywords_corresp` (
  `k1` bigint(20) NOT NULL,
  `k2` bigint(20) NOT NULL,
  `id_sentence` bigint(20) NOT NULL DEFAULT '0',
  `id_record` int(10) NOT NULL DEFAULT '0',
  `id_object` int(10) NOT NULL DEFAULT '0',
  `id_user` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_module_type` int(10) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  KEY `index2` (`id_sentence`),
  KEY `index3` (`id_workspace`,`k1`,`k2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_keywords_corresp`
--

LOCK TABLES `dims_keywords_corresp` WRITE;
/*!40000 ALTER TABLE `dims_keywords_corresp` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_keywords_corresp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_keywords_index`
--

DROP TABLE IF EXISTS `dims_keywords_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_keywords_index` (
  `id_keyword` bigint(20) NOT NULL,
  `id_metafield` bigint(20) NOT NULL,
  `id_sentence` bigint(20) NOT NULL DEFAULT '0',
  `length` tinyint(2) NOT NULL DEFAULT '0',
  `id_record` int(10) NOT NULL DEFAULT '0',
  `id_object` int(10) NOT NULL DEFAULT '0',
  `id_user` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_module_type` int(10) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  KEY `id_keyword` (`id_keyword`),
  KEY `id_metafield` (`id_metafield`),
  KEY `id_keyword_2` (`id_keyword`,`id_metafield`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_keywords_index`
--

LOCK TABLES `dims_keywords_index` WRITE;
/*!40000 ALTER TABLE `dims_keywords_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_keywords_index` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_keywords_metafield`
--

DROP TABLE IF EXISTS `dims_keywords_metafield`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_keywords_metafield` (
  `id_keyword` int(11) NOT NULL,
  `id_metafield` int(11) NOT NULL,
  PRIMARY KEY (`id_keyword`,`id_metafield`),
  KEY `id_keyword` (`id_keyword`),
  KEY `metafield` (`id_metafield`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_keywords_metafield`
--

LOCK TABLES `dims_keywords_metafield` WRITE;
/*!40000 ALTER TABLE `dims_keywords_metafield` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_keywords_metafield` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_keywords_metaphone`
--

DROP TABLE IF EXISTS `dims_keywords_metaphone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_keywords_metaphone` (
  `id` int(11) NOT NULL,
  `metaphone` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_keywords_metaphone`
--

LOCK TABLES `dims_keywords_metaphone` WRITE;
/*!40000 ALTER TABLE `dims_keywords_metaphone` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_keywords_metaphone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_keywords_ordercache`
--

DROP TABLE IF EXISTS `dims_keywords_ordercache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_keywords_ordercache` (
  `session_id` varchar(64) DEFAULT NULL,
  `id_object` int(11) NOT NULL,
  `id_record` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  KEY `session_id` (`session_id`),
  KEY `id_keyword` (`id_record`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_keywords_ordercache`
--

LOCK TABLES `dims_keywords_ordercache` WRITE;
/*!40000 ALTER TABLE `dims_keywords_ordercache` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_keywords_ordercache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_keywords_preindex`
--

DROP TABLE IF EXISTS `dims_keywords_preindex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_keywords_preindex` (
  `id_record` int(10) NOT NULL DEFAULT '0',
  `id_object` int(10) NOT NULL DEFAULT '0',
  `id_user` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_module_type` int(10) NOT NULL DEFAULT '0',
  `typecontent` varchar(64) DEFAULT NULL,
  `content` longtext,
  `statut` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_keywords_preindex`
--

LOCK TABLES `dims_keywords_preindex` WRITE;
/*!40000 ALTER TABLE `dims_keywords_preindex` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_keywords_preindex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_keywords_sentence`
--

DROP TABLE IF EXISTS `dims_keywords_sentence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_keywords_sentence` (
  `id` bigint(20) NOT NULL,
  `id_metafield` bigint(20) NOT NULL,
  `parag` int(11) NOT NULL DEFAULT '1',
  `content` longtext,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_keywords_sentence`
--

LOCK TABLES `dims_keywords_sentence` WRITE;
/*!40000 ALTER TABLE `dims_keywords_sentence` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_keywords_sentence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_keywords_temp`
--

DROP TABLE IF EXISTS `dims_keywords_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_keywords_temp` (
  `key_from` int(11) NOT NULL,
  `key_to` int(11) NOT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  KEY `key_from` (`key_from`),
  KEY `key_to` (`key_to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_keywords_temp`
--

LOCK TABLES `dims_keywords_temp` WRITE;
/*!40000 ALTER TABLE `dims_keywords_temp` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_keywords_temp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_keywords_usercache`
--

DROP TABLE IF EXISTS `dims_keywords_usercache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_keywords_usercache` (
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_exp` int(11) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_object` int(11) NOT NULL DEFAULT '0',
  `id_record` int(11) NOT NULL DEFAULT '0',
  `id_sentence` int(11) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  KEY `id_user` (`id_user`),
  KEY `id_sentence` (`id_sentence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_keywords_usercache`
--

LOCK TABLES `dims_keywords_usercache` WRITE;
/*!40000 ALTER TABLE `dims_keywords_usercache` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_keywords_usercache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_lang`
--

DROP TABLE IF EXISTS `dims_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) DEFAULT NULL,
  `code_of_conduct` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_lang`
--

LOCK TABLES `dims_lang` WRITE;
/*!40000 ALTER TABLE `dims_lang` DISABLE KEYS */;
INSERT INTO `dims_lang` VALUES (1,'french',''),(2,'english',''),(4,'German',''),(5,'Japanese','');
/*!40000 ALTER TABLE `dims_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_load_balancer`
--

DROP TABLE IF EXISTS `dims_load_balancer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_load_balancer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `time_creation` int(10) NOT NULL,
  `time_modification` int(10) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `server` (`server`),
  KEY `id_worckspace` (`id_workspace`),
  KEY `time_modification` (`time_modification`),
  KEY `time_creation` (`time_creation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_load_balancer`
--

LOCK TABLES `dims_load_balancer` WRITE;
/*!40000 ALTER TABLE `dims_load_balancer` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_load_balancer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_log`
--

DROP TABLE IF EXISTS `dims_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_method` varchar(255) DEFAULT NULL,
  `query_string` varchar(255) DEFAULT NULL,
  `remote_addr` varchar(64) DEFAULT NULL,
  `remote_port` int(10) unsigned DEFAULT NULL,
  `script_filename` varchar(255) DEFAULT NULL,
  `path_translated` varchar(255) DEFAULT NULL,
  `script_name` varchar(255) DEFAULT NULL,
  `request_uri` varchar(255) DEFAULT NULL,
  `dims_userid` int(10) DEFAULT NULL,
  `dims_workspaceid` int(10) DEFAULT NULL,
  `dims_moduleid` int(10) DEFAULT NULL,
  `browser` varchar(64) DEFAULT NULL,
  `system` varchar(64) DEFAULT NULL,
  `date_year` int(10) DEFAULT NULL,
  `date_month` int(10) DEFAULT NULL,
  `date_day` int(10) DEFAULT NULL,
  `date_hour` int(10) DEFAULT NULL,
  `date_minute` int(10) DEFAULT NULL,
  `date_second` int(10) DEFAULT NULL,
  `total_exec_time` int(10) unsigned DEFAULT '0',
  `sql_exec_time` int(10) unsigned DEFAULT '0',
  `sql_percent_time` int(10) unsigned DEFAULT '0',
  `php_percent_time` int(10) unsigned DEFAULT '0',
  `numqueries` int(10) unsigned DEFAULT '0',
  `page_size` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_log`
--

LOCK TABLES `dims_log` WRITE;
/*!40000 ALTER TABLE `dims_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mailing_content`
--

DROP TABLE IF EXISTS `dims_mailing_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mailing_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) DEFAULT NULL,
  `content` longtext,
  `date_create` varchar(14) DEFAULT NULL,
  `date_modif` varchar(14) DEFAULT NULL,
  `date_envoi` varchar(14) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_module` int(11) DEFAULT NULL,
  `template` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mailing_content`
--

LOCK TABLES `dims_mailing_content` WRITE;
/*!40000 ALTER TABLE `dims_mailing_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mailing_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mailing_content_list`
--

DROP TABLE IF EXISTS `dims_mailing_content_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mailing_content_list` (
  `id_content` int(11) NOT NULL,
  `id_list` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mailing_content_list`
--

LOCK TABLES `dims_mailing_content_list` WRITE;
/*!40000 ALTER TABLE `dims_mailing_content_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mailing_content_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mailing_email`
--

DROP TABLE IF EXISTS `dims_mailing_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mailing_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_list` int(11) NOT NULL,
  `id_contact` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `date_creation` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mailing_email`
--

LOCK TABLES `dims_mailing_email` WRITE;
/*!40000 ALTER TABLE `dims_mailing_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mailing_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mailing_list`
--

DROP TABLE IF EXISTS `dims_mailing_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mailing_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `comment` text,
  `date_create` varchar(14) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_module` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mailing_list`
--

LOCK TABLES `dims_mailing_list` WRITE;
/*!40000 ALTER TABLE `dims_mailing_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mailing_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mailinglist`
--

DROP TABLE IF EXISTS `dims_mailinglist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mailinglist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `protected` tinyint(1) unsigned DEFAULT '0',
  `public` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mailinglist`
--

LOCK TABLES `dims_mailinglist` WRITE;
/*!40000 ALTER TABLE `dims_mailinglist` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mailinglist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mailinglist_attach`
--

DROP TABLE IF EXISTS `dims_mailinglist_attach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mailinglist_attach` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_mailinglist` int(10) DEFAULT NULL,
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_group` int(10) unsigned NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_mailinglist` (`id_mailinglist`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mailinglist_attach`
--

LOCK TABLES `dims_mailinglist_attach` WRITE;
/*!40000 ALTER TABLE `dims_mailinglist_attach` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mailinglist_attach` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mb_action`
--

DROP TABLE IF EXISTS `dims_mb_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mb_action` (
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0',
  `id_action` int(10) unsigned NOT NULL DEFAULT '0',
  `label` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `id_workspace` int(10) DEFAULT NULL,
  `id_object` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_action`,`id_module_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mb_action`
--

LOCK TABLES `dims_mb_action` WRITE;
/*!40000 ALTER TABLE `dims_mb_action` DISABLE KEYS */;
INSERT INTO `dims_mb_action` VALUES (1,1,'Installer un Module',NULL,0,0),(1,2,'Désinstaller un Module',NULL,0,0),(1,3,'Modifier les Paramètres d\'un Module',NULL,0,0),(1,4,'Instancier / Utiliser un Module',NULL,0,0),(1,5,'Modifier les Propriétés d\'un Module',NULL,0,0),(1,6,'Modifier la Page d\'Accueil',NULL,0,0),(1,7,'Installer un Skin',NULL,0,0),(1,8,'Désinstaller un Skin',NULL,0,0),(1,9,'Créer un Groupe',NULL,0,0),(1,10,'Modifier un Groupe',NULL,0,0),(1,11,'Supprimer un Groupe',NULL,0,0),(1,12,'Cloner un Groupe',NULL,0,0),(1,13,'Créer un Rôle',NULL,0,0),(1,14,'Modifier un Rôle',NULL,0,0),(1,15,'Supprimer un Rôle',NULL,0,0),(1,16,'Créer un Profil',NULL,0,0),(1,17,'Modifier un Profil',NULL,0,0),(1,18,'Supprimer un Profil',NULL,0,0),(1,19,'Ajouter un Utilisateur',NULL,0,0),(1,20,'Modifier un Utilisateur',NULL,0,0),(1,21,'Supprimer un Utilisateur',NULL,0,0),(1,22,'Détacher un Module',NULL,0,0),(1,23,'Supprimer un Module',NULL,0,0),(1,24,'Mettre à jour la Métabase',NULL,0,0),(1,25,'Connexion Utilisateur',NULL,0,0),(1,26,'Erreur de Connexion',NULL,0,0),(1,27,'Déplacer un Utilisateur',NULL,0,0),(1,28,'Attacher un Utilisateur',NULL,0,0),(1,29,'Détacher un Utilisateur',NULL,0,0),(2,1,'Gérer les Articles',NULL,NULL,0),(2,2,'Publier les Articles',NULL,NULL,0),(2,3,'Gérer les Rubriques',NULL,NULL,0),(2,4,'Gérer les Validateurs',NULL,NULL,0),(3,7,'Gérer les Validateurs',NULL,NULL,0),(3,6,'Supprimer un Fichier',NULL,NULL,1),(3,5,'Supprimer un Dossier',NULL,NULL,1),(3,4,'Modifier un Fichier',NULL,NULL,2),(3,3,'Modifier un Dossier',NULL,NULL,1),(3,2,'Ajouter un Fichier',NULL,NULL,2),(3,1,'Ajouter un Dossier',NULL,NULL,1),(4,1,'Créer des Formulaires',NULL,NULL,0),(4,2,'Saisir des Données',NULL,NULL,0),(4,3,'Exporter des Données',NULL,NULL,0),(4,4,'Filtrer des Données',NULL,NULL,0),(7,1,'Rubrique \"Favoris\"',NULL,NULL,0),(7,2,'Rubrique \"Mes Contacts\"',NULL,NULL,0),(7,3,'Rubrique \"Mon Groupe\"',NULL,NULL,0),(7,4,'Rubrique \"Utilisateurs\"',NULL,NULL,0),(7,5,'Rubrique \"Recherche\"',NULL,NULL,0),(8,1,'Rédiger',NULL,NULL,0),(8,2,'Modifier',NULL,NULL,0),(8,3,'Effacer',NULL,NULL,0),(8,4,'Publier',NULL,NULL,0),(8,5,'Créer Catégorie',NULL,NULL,0),(8,6,'Effacer Catégorie',NULL,NULL,0),(15,1,'Rédiger',NULL,NULL,0),(15,2,'Modifier',NULL,NULL,0),(15,3,'Effacer',NULL,NULL,0),(15,4,'Publier',NULL,NULL,0),(15,5,'Créer Catégorie',NULL,NULL,0),(15,6,'Effacer Catégorie',NULL,NULL,0),(17,1,'Contributeurs',NULL,NULL,1),(1,30,'Attacher un groupe',NULL,0,0),(1,31,'Détacher un groupe',NULL,0,0),(1,32,'Mettre\r\na  jour un module',NULL,0,0),(1,33,'Ajouter\r\nune action',NULL,0,8),(1,34,'Modifier une action',NULL,0,8),(1,35,'Supprimer une action',NULL,0,8),(1,36,'Ajouter une personne',NULL,NULL,7),(1,37,'Modifier une personne',NULL,NULL,7),(1,38,'Supprimer une personne',NULL,NULL,7),(1,39,'Ajouter une entreprise',NULL,NULL,6),(1,40,'Modifier une entreprise',NULL,NULL,6),(1,41,'Supprimer une entreprise',NULL,NULL,6),(1,42,'Export',NULL,0,0);
/*!40000 ALTER TABLE `dims_mb_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mb_field`
--

DROP TABLE IF EXISTS `dims_mb_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mb_field` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tablename` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `visible` tinyint(1) unsigned DEFAULT NULL,
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0',
  `id_object` int(10) NOT NULL DEFAULT '0',
  `indexed` tinyint(4) NOT NULL DEFAULT '0',
  `protected` tinyint(4) NOT NULL DEFAULT '1',
  `level` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `indexed` (`indexed`),
  KEY `name` (`name`),
  KEY `id_object` (`id_object`)
) ENGINE=MyISAM AUTO_INCREMENT=822 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mb_field`
--

LOCK TABLES `dims_mb_field` WRITE;
/*!40000 ALTER TABLE `dims_mb_field` DISABLE KEYS */;
INSERT INTO `dims_mb_field` VALUES (1,'dims_user_type','label','label','varchar(255)',1,1,0,0,1,1),(2,'dims_user_type','id','id','int(10) unsigned',1,1,0,0,1,1),(3,'dims_user_filter_rules','value','value','varchar(255)',1,1,0,0,1,1),(4,'dims_user_filter_rules','operator','operator','varchar(4)',1,1,0,0,1,1),(5,'dims_user_filter_rules','fieldname','fieldname','varchar(255)',1,1,0,0,1,1),(6,'dims_user_filter_rules','condition','condition','varchar(20)',1,1,0,0,1,1),(7,'dims_user_filter_rules','id_workspace','id_workspace','int(10) unsigned',1,1,0,0,1,1),(8,'dims_user_filter_rules','id','id','int(10) unsigned',1,1,0,0,1,1),(9,'dims_user_action_log','timestp','timestp','timestamp(14)',1,1,0,0,1,1),(10,'dims_user_action_log','id_record','id_record','varchar(255)',1,1,0,0,1,1),(11,'dims_user_action_log','id_module','id_module','int(10) unsigned',1,1,0,0,1,1),(12,'dims_user_action_log','id_module_type','id_module_type','int(10) unsigned',1,1,0,0,1,1),(13,'dims_user_action_log','id_user','id_user','int(10) unsigned',1,1,0,0,1,1),(14,'dims_user_action_log','id_action','id_action','int(10) unsigned',1,1,0,0,1,1),(15,'dims_user','address','address','text',1,1,0,1,1,1),(16,'dims_user','comments','comments','text',1,1,0,1,1,1),(17,'dims_user','fax','fax','varchar(32)',1,1,0,1,1,1),(18,'dims_user','phone','phone','varchar(32)',1,1,0,1,1,1),(19,'dims_user','email','email','varchar(255)',1,1,0,1,1,1),(20,'dims_user','date_expire','date_expire','varchar(14)',1,1,0,1,1,1),(21,'dims_user','date_creation','date_creation','timestamp(14)',1,1,0,0,1,1),(22,'dims_user','login','login','varchar(32)',1,1,0,1,1,1),(23,'dims_user','password','password','varchar(32)',1,1,0,1,1,1),(24,'dims_user','firstname','firstname','varchar(100)',1,1,0,1,1,1),(25,'dims_user','lastname','lastname','varchar(100)',1,1,0,1,1,1),(26,'dims_user','id_ldap','id_ldap','int(10) unsigned',1,1,0,0,1,1),(27,'dims_user','id_type','id_type','int(10)',1,1,0,0,1,1),(28,'dims_rule_type','label','label','varchar(100)',1,1,0,0,1,1),(29,'dims_user','id','id','int(10) unsigned',1,1,0,0,1,1),(30,'dims_rule','id_profile','id_profile','int(10) unsigned',1,1,0,0,1,1),(31,'dims_rule_type','id','id','int(10) unsigned',1,1,0,0,1,1),(32,'dims_rule','id_type','id_type','int(10) unsigned',1,1,0,0,1,1),(33,'dims_rule','persistent','persistent','tinyint(1) unsigned',1,1,0,0,1,1),(34,'dims_rule','operator','operator','varchar(25)',1,1,0,0,1,1),(35,'dims_rule','value','value','varchar(255)',1,1,0,0,1,1),(36,'dims_rule','field','field','varchar(255)',1,1,0,0,1,1),(37,'dims_rule','id_workspace','id_workspace','int(10) unsigned',1,1,0,0,1,1),(38,'dims_rule','label','label','varchar(255)',1,1,0,0,1,1),(39,'dims_rule','id','id','int(10)',1,1,0,0,1,1),(40,'dims_role_profile','id_profile','id_profile','int(10) unsigned',1,1,0,0,1,1),(41,'dims_role_profile','id_role','id_role','int(10) unsigned',1,1,0,0,1,1),(42,'dims_role_action','id_module_type','id_module_type','int(10) unsigned',1,1,0,0,1,1),(43,'dims_action','id_module_type','id_module_type','int(10) unsigned',1,1,0,0,1,1),(44,'dims_action','id_action','id_action','int(10) unsigned',1,1,0,0,1,1),(45,'dims_action','description','description','blob',1,1,0,0,1,1),(46,'dims_action','label','label','varchar(255)',1,1,0,0,1,1),(47,'dims_cms_object','id','id','int(11) unsigned',1,1,0,0,1,1),(48,'dims_cms_object','label','label','varchar(255)',1,1,0,0,1,1),(49,'dims_cms_object','id_module_type','id_module_type','int(10)',1,1,0,0,1,1),(50,'dims_cms_object','script','script','varchar(255)',1,1,0,0,1,1),(51,'dims_connecteduser','ip','ip','varchar(255)',1,1,0,0,1,1),(52,'dims_connecteduser','sid','sid','varchar(255)',1,1,0,0,1,1),(53,'dims_connecteduser','user_id','user_id','int(10) unsigned',1,1,0,0,1,1),(54,'dims_connecteduser','group_id','group_id','int(10) unsigned',1,1,0,0,1,1),(55,'dims_connecteduser','module_id','module_id','int(10) unsigned',1,1,0,0,1,1),(56,'dims_connecteduser','timestp','timestp','varchar(14)',1,1,0,0,1,1),(57,'dims_group','id','id','int(10) unsigned',1,1,0,0,1,1),(58,'dims_group','id_workspace','id_workspace','int(10) unsigned',1,1,0,0,1,1),(59,'dims_group','code','code','varchar(64)',1,1,0,1,1,1),(60,'dims_group','system','system','tinyint(1) unsigned',1,1,0,0,1,1),(61,'dims_group','label','label','varchar(255)',1,1,0,1,1,1),(62,'dims_group','protected','protected','tinyint(1) unsigned',1,1,0,0,1,1),(63,'dims_group','iprules','iprules','text',1,1,0,1,1,1),(64,'dims_group','parents','parents','varchar(100)',1,1,0,1,1,1),(65,'dims_group','skin','skin','varchar(255)',1,1,0,1,1,1),(66,'dims_group','macrules','macrules','text',1,1,0,1,1,1),(67,'dims_group','depth','depth','int(10) unsigned',1,1,0,0,1,1),(68,'dims_group','mustdefinerule','mustdefinerule','tinyint(1) unsigned',1,1,0,0,1,1),(69,'dims_group_user','id_workspace','id_workspace','int(10) unsigned',1,1,0,0,1,1),(70,'dims_group_user','id_user','id_user','int(10) unsigned',1,1,0,0,1,1),(71,'dims_group_user','id_profile','id_profile','int(10) unsigned',1,1,0,0,1,1),(72,'dims_group_user','adminlevel','adminlevel','tinyint(3) unsigned',1,1,0,0,1,1),(73,'dims_group_user_role','id_user','id_user','int(10) unsigned',1,1,0,0,1,1),(74,'dims_group_user_role','id_workspace','id_workspace','int(10) unsigned',1,1,0,0,1,1),(75,'dims_group_user_role','id_role','id_role','int(10) unsigned',1,1,0,0,1,1),(76,'dims_homepage_column','id','id','int(10) unsigned',1,1,0,0,1,1),(77,'dims_homepage_column','id_line','id_line','int(10) unsigned',1,1,0,0,1,1),(78,'dims_homepage_column','position','position','int(10) unsigned',1,1,0,0,1,1),(79,'dims_homepage_column','size','size','int(10) unsigned',1,1,0,0,1,1),(80,'dims_homepage_column','id_module','id_module','int(10) unsigned',1,1,0,0,1,1),(81,'dims_homepage_column','border','border','tinyint(1) unsigned',1,1,0,0,1,1),(82,'dims_homepage_column','title','title','varchar(255)',1,1,0,0,1,1),(83,'dims_homepage_line','id','id','int(10) unsigned',1,1,0,0,1,1),(84,'dims_homepage_line','id_workspace','id_workspace','int(10) unsigned',1,1,0,0,1,1),(85,'dims_homepage_line','position','position','int(10) unsigned',1,1,0,0,1,1),(86,'dims_homepage_line','id_user','id_user','int(10) unsigned',1,1,0,0,1,1),(87,'dims_log','id','id','int(10) unsigned',1,1,0,0,1,1),(88,'dims_log','request_method','request_method','varchar(255)',1,1,0,0,1,1),(89,'dims_log','query_string','query_string','varchar(255)',1,1,0,0,1,1),(90,'dims_log','document_root','document_root','varchar(255)',1,1,0,0,1,1),(91,'dims_log','remote_port','remote_port','int(10) unsigned',1,1,0,0,1,1),(92,'dims_log','remote_addr','remote_addr','varchar(255)',1,1,0,0,1,1),(93,'dims_log','path_translated','path_translated','varchar(255)',1,1,0,0,1,1),(94,'dims_log','script_filename','script_filename','varchar(255)',1,1,0,0,1,1),(95,'dims_log','script_name','script_name','varchar(255)',1,1,0,0,1,1),(96,'dims_log','request_uri','request_uri','varchar(255)',1,1,0,0,1,1),(97,'dims_log','dims_groupid','dims_groupid','int(10)',1,1,0,0,1,1),(98,'dims_log','dims_userid','dims_userid','int(10)',1,1,0,0,1,1),(99,'dims_log','dims_moduleid','dims_moduleid','int(10)',1,1,0,0,1,1),(100,'dims_log','system','system','varchar(255)',1,1,0,0,1,1),(101,'dims_log','browser','browser','varchar(255)',1,1,0,0,1,1),(102,'dims_log','date_year','date_year','int(10)',1,1,0,0,1,1),(103,'dims_log','date_month','date_month','int(10)',1,1,0,0,1,1),(104,'dims_log','date_hour','date_hour','int(10)',1,1,0,0,1,1),(105,'dims_log','date_day','date_day','int(10)',1,1,0,0,1,1),(106,'dims_log','date_minute','date_minute','int(10)',1,1,0,0,1,1),(107,'dims_mb_field','tablename','tablename','varchar(100)',1,1,0,0,1,1),(108,'dims_log','date_second','date_second','int(10)',1,1,0,0,1,1),(109,'dims_mb_field','name','name','varchar(100)',1,1,0,0,1,1),(110,'dims_mb_field','type','type','varchar(50)',1,1,0,0,1,1),(111,'dims_mb_field','label','label','varchar(255)',1,1,0,0,1,1),(112,'dims_mb_field','visible','visible','tinyint(1) unsigned',1,1,0,0,1,1),(113,'dims_mb_field','id_module_type','id_module_type','int(10) unsigned',1,1,0,0,1,1),(114,'dims_mb_relation','fieldsrc','fieldsrc','varchar(100)',1,1,0,0,1,1),(115,'dims_mb_relation','tablesrc','tablesrc','varchar(100)',1,1,0,0,1,1),(116,'dims_mb_relation','tabledest','tabledest','varchar(100)',1,1,0,0,1,1),(117,'dims_mb_relation','fielddest','fielddest','varchar(100)',1,1,0,0,1,1),(118,'dims_mb_relation','id_module_type','id_module_type','int(10) unsigned',1,1,0,0,1,1),(119,'dims_mb_schema','tablesrc','tablesrc','varchar(100)',1,1,0,0,1,1),(120,'dims_mb_schema','tabledest','tabledest','varchar(100)',1,1,0,0,1,1),(121,'dims_mb_table','name','name','varchar(100)',1,1,0,0,1,1),(122,'dims_mb_schema','id_module_type','id_module_type','int(10) unsigned',1,1,0,0,1,1),(123,'dims_mb_table','label','label','varchar(255)',1,1,0,0,1,1),(124,'dims_mb_table','visible','visible','tinyint(1) unsigned',1,1,0,0,1,1),(125,'dims_mb_table','id_module_type','id_module_type','int(10) unsigned',1,1,0,0,1,1),(126,'dims_module','label','label','varchar(100)',1,1,0,0,1,1),(127,'dims_module','id','id','int(10) unsigned',1,1,0,0,1,1),(128,'dims_module','id_module_type','id_module_type','int(10) unsigned',1,1,0,0,1,1),(129,'dims_module','id_workspace','id_workspace','int(10) unsigned',1,1,0,0,1,1),(130,'dims_module','active','active','tinyint(1) unsigned',1,1,0,0,1,1),(131,'dims_module','public','public','tinyint(1) unsigned',1,1,0,0,1,1),(132,'dims_module','herited','herited','tinyint(1) unsigned',1,1,0,0,1,1),(133,'dims_module','shared','shared','tinyint(1) unsigned',1,1,0,0,1,1),(134,'dims_module','adminrestricted','adminrestricted','tinyint(1) unsigned',1,1,0,0,1,1),(135,'dims_module','viewmode','viewmode','int(10) unsigned',1,1,0,0,1,1),(136,'dims_module','transverseview','transverseview','tinyint(1) unsigned',1,1,0,0,1,1),(137,'dims_module','autoconnect','autoconnect','tinyint(1) unsigned',1,1,0,0,1,1),(138,'dims_module_group','id_module','id_module','int(10) unsigned',1,1,0,0,1,1),(139,'dims_module_group','id_workspace','id_workspace','int(10) unsigned',1,1,0,0,1,1),(140,'dims_module_group','blockposition','blockposition','char(10)',1,1,0,0,1,1),(141,'dims_module_group','position','position','tinyint(2)',1,1,0,0,1,1),(142,'dims_module_type','id','id','int(10) unsigned',1,1,0,0,1,1),(143,'dims_module_type','label','label','varchar(100)',1,1,0,0,1,1),(144,'dims_module_type','instanciable','instanciable','tinyint(1) unsigned',1,1,0,0,1,1),(145,'dims_module_type','publicparam','publicparam','tinyint(1) unsigned',1,1,0,0,1,1),(146,'dims_module_type','managecontent','managecontent','tinyint(1) unsigned',1,1,0,0,1,1),(147,'dims_module_type','description','description','longtext',1,1,0,0,1,1),(148,'dims_module_type','version','version','varchar(20)',1,1,0,0,1,1),(149,'dims_module_type','author','author','varchar(255)',1,1,0,0,1,1),(150,'dims_param_choice','id','id','int(10) unsigned',1,1,0,0,1,1),(151,'dims_param_choice','id_param_type','id_param_type','int(10) unsigned',1,1,0,0,1,1),(152,'dims_param_choice','value','value','varchar(100)',1,1,0,0,1,1),(153,'dims_param_choice','displayed_value','displayed_value','varchar(100)',1,1,0,0,1,1),(154,'dims_param_default','id_module','id_module','int(10) unsigned',1,1,0,0,1,1),(155,'dims_param_default','id_param_type','id_param_type','int(10) unsigned',1,1,0,0,1,1),(156,'dims_param_default','value','value','varchar(255)',1,1,0,0,1,1),(157,'dims_param_group','id_param_type','id_param_type','int(10) unsigned',1,1,0,0,1,1),(158,'dims_param_group','id_module','id_module','int(10)',1,1,0,0,1,1),(159,'dims_param_group','id_workspace','id_workspace','int(10) unsigned',1,1,0,0,1,1),(160,'dims_param_group','value','value','varchar(255)',1,1,0,0,1,1),(161,'dims_param_type','id','id','int(10) unsigned',1,1,0,0,1,1),(162,'dims_param_type','id_module_type','id_module_type','int(10) unsigned',1,1,0,0,1,1),(163,'dims_param_type','label','label','varchar(100)',1,1,0,0,1,1),(164,'dims_param_type','default_value','default_value','varchar(100)',1,1,0,0,1,1),(165,'dims_param_type','public','public','tinyint(1) unsigned',1,1,0,0,1,1),(166,'dims_param_type','description','description','longtext',1,1,0,0,1,1),(167,'dims_param_type','displayed_label','displayed_label','varchar(100)',1,1,0,0,1,1),(168,'dims_param_user','id_param_type','id_param_type','int(10) unsigned',1,1,0,0,1,1),(169,'dims_param_user','id_module','id_module','int(10) unsigned',1,1,0,0,1,1),(170,'dims_param_user','id_user','id_user','int(10) unsigned',1,1,0,0,1,1),(171,'dims_param_user','value','value','varchar(255)',1,1,0,0,1,1),(172,'dims_profile','id','id','int(10) unsigned',1,1,0,0,1,1),(173,'dims_profile','id_workspace','id_workspace','int(10) unsigned',1,1,0,0,1,1),(174,'dims_profile','label','label','varchar(255)',1,1,0,0,1,1),(175,'dims_profile','description','description','blob',1,1,0,0,1,1),(176,'dims_profile','def','def','tinyint(1) unsigned',1,1,0,0,1,1),(177,'dims_profile','shared','shared','tinyint(1) unsigned',1,1,0,0,1,1),(178,'dims_role','id','id','int(10) unsigned',1,1,0,0,1,1),(179,'dims_role','id_module','id_module','int(10) unsigned',1,1,0,0,1,1),(180,'dims_role','id_workspace','id_workspace','int(10) unsigned',1,1,0,0,1,1),(181,'dims_role','label','label','varchar(255)',1,1,0,0,1,1),(182,'dims_role','description','description','blob',1,1,0,0,1,1),(183,'dims_role','def','def','tinyint(1) unsigned',1,1,0,0,1,1),(184,'dims_role','shared','shared','tinyint(1) unsigned',1,1,0,0,1,1),(185,'dims_role_action','id_role','id_role','int(10) unsigned',1,1,0,0,1,1),(186,'dims_role_action','id_action','id_action','int(10) unsigned',1,1,0,0,1,1),(187,'dims_user_type','displayed_label','displayed_label','varchar(255)',1,1,0,0,1,1),(188,'dims_user_type_fields','id','id','int(10) unsigned',1,1,0,0,1,1),(189,'dims_user_type_fields','id_type','id_type','int(10) unsigned',1,1,0,0,1,1),(190,'dims_user_type_fields','label','label','varchar(100)',1,1,0,0,1,1),(191,'dims_user_type_fields','type_field','type_field','varchar(100)',1,1,0,0,1,1),(192,'dims_user_type_fields','size_field','size_field','int(10) unsigned',1,1,0,0,1,1),(193,'dims_user_type_fields','pos','pos','int(10) unsigned',1,1,0,0,1,1),(194,'dims_user_type_fields','valeurs','valeurs','longtext',1,1,0,0,1,1),(195,'dims_user_type_fields','displayed_label','displayed_label','varchar(255)',1,1,0,0,1,1),(196,'dims_mod_forms','tablename','tablename','varchar(255)',1,4,0,1,1,1),(197,'dims_mod_doctype','name','name','varchar(255)',1,3,0,1,1,1),(198,'dims_mod_doctype','id','id','int(10) unsigned',0,3,0,0,1,1),(199,'dims_mod_docfolder','foldertype','foldertype','int(1) unsigned',1,3,0,0,1,1),(200,'dims_mod_docfolder','rights_all','rights_all','char(3)',1,3,0,1,1,1),(201,'dims_mod_docfolder','rights_user','rights_user','char(3)',1,3,0,1,1,1),(202,'dims_mod_docfolder','id_workspace','id_workspace','int(10) unsigned',0,3,0,0,1,1),(203,'dims_mod_docfolder','rights_group','rights_group','char(3)',1,3,0,1,1,1),(204,'dims_mod_docfolder','date_modify','date_modify','datetime',1,3,0,0,1,1),(205,'dims_mod_docfolder','date_create','date_create','datetime',1,3,0,0,1,1),(206,'dims_mod_docfolder','id_user_modify','id_user_modify','int(10) unsigned',1,3,0,0,1,1),(207,'dims_mod_docfolder','id_module','id_module','int(10) unsigned',0,3,0,0,1,1),(208,'dims_mod_docfolder','id_user_create','id_user_create','int(10) unsigned',1,3,0,0,1,1),(209,'dims_mod_docfolder','parents','parents','varchar(255)',1,3,0,1,1,1),(210,'dims_mod_docfolder','description','description','varchar(255)',1,3,0,1,1,1),(211,'dims_mod_docfolder','name','name','varchar(255)',1,3,0,1,1,1),(212,'dims_mod_docfolder','id','id','int(10) unsigned',0,3,1,0,1,1),(213,'dims_mod_docext','id_doctype','id_doctype','int(10) unsigned',1,3,0,0,1,1),(214,'dims_mod_docext','file','file','varchar(255)',1,3,0,1,1,1),(215,'dims_mod_docext','ext','ext','varchar(20)',1,3,0,1,1,1),(216,'dims_mod_docext','id','id','int(10) unsigned',0,3,0,0,1,1),(217,'dims_mod_doc_param','ext_id','ext_id','int(10)',1,3,0,0,1,1),(218,'dims_mod_doc_param','path','path','varchar(255)',1,3,0,1,1,1),(219,'dims_mod_doc_param','libelle','libelle','varchar(255)',1,3,0,1,1,1),(220,'dims_mod_doc_param','id','id','int(10)',0,3,0,0,1,1),(221,'dims_mod_doc_file','content','content','longtext',1,3,0,1,1,1),(222,'dims_mod_doc_file','extension','extension','varchar(20)',1,3,0,1,1,1),(223,'dims_mod_doc_file','id_docfolder','id_docfolder','int(10) unsigned',1,3,0,0,1,1),(224,'dims_mod_doc_file','id_module','id_module','int(10) unsigned',0,3,0,0,1,1),(225,'dims_mod_doc_file','nbclick','nbclick','int(10) unsigned',1,3,0,0,1,1),(226,'dims_mod_doc_file','id_user','id_user','int(10) unsigned',1,3,0,0,1,1),(227,'dims_mod_forms_reply_field','id_module','id_module','int(10) unsigned',0,4,0,0,1,1),(228,'dims_mod_forms_reply_field','id_workspace','id_workspace','int(10) unsigned',0,4,0,0,1,1),(229,'dims_mod_forms_reply_field','id_field','id_field','int(10) unsigned',1,4,0,0,1,1),(230,'dims_mod_forms_reply_field','value','value','longtext',1,4,0,1,1,1),(231,'dims_mod_forms_reply_field','id_user','id_user','int(10) unsigned',1,4,0,0,1,1),(232,'dims_mod_forms_reply_field','id_forms','id_forms','int(10) unsigned',1,4,0,0,1,1),(233,'dims_mod_forms_reply_field','id_reply','id_reply','int(10) unsigned',1,4,0,0,1,1),(234,'dims_mod_forms_reply_field','id','id','int(10) unsigned',1,4,2,0,1,1),(235,'dims_mod_forms_reply','ip','ip','varchar(15)',1,4,0,1,1,1),(236,'dims_mod_forms_reply','date_validation','date_validation','varchar(14)',1,4,0,1,1,1),(237,'dims_mod_forms_reply','id_user','id_user','int(10) unsigned',0,4,0,0,1,1),(238,'dims_mod_forms_reply','id_workspace','id_workspace','tinyint(3) unsigned',0,4,0,0,1,1),(239,'dims_mod_forms_reply','id_module','id_module','int(10) unsigned',0,4,0,0,1,1),(240,'dims_mod_forms_reply','id_forms','id_forms','int(10) unsigned',1,4,0,0,1,1),(241,'dims_mod_forms_reply','id','id','int(10) unsigned',0,4,0,0,1,1),(242,'dims_mod_forms_field','defaultvalue','defaultvalue','varchar(255)',1,4,0,1,1,1),(243,'dims_mod_forms_field','option_cmsshowfilter','option_cmsshowfilter','tinyint(1) unsigned',1,4,0,0,1,1),(244,'dims_mod_forms_field','option_cmsgroupby','option_cmsgroupby','tinyint(1) unsigned',1,4,0,0,1,1),(245,'dims_mod_forms_field','option_cmsorderby','option_cmsorderby','tinyint(1) unsigned',1,4,0,0,1,1),(246,'dims_mod_forms_field','option_cmsdisplaylabel','option_cmsdisplaylabel','tinyint(1) unsigned',1,4,0,0,1,1),(247,'dims_mod_forms_field','option_arrayview','option_arrayview','tinyint(1) unsigned',1,4,0,0,1,1),(248,'dims_mod_wce_heading','id_user','id_user','int(10) unsigned',0,2,0,0,1,1),(249,'dims_mod_rssrequest','id_workspace','id_workspace','int(10) unsigned',0,6,0,0,1,1),(250,'dims_mod_rssrequest','id_module','id_module','int(10) unsigned',0,6,0,0,1,1),(251,'dims_mod_rssrequest','id_user','id_user','int(10) unsigned',0,6,0,0,1,1),(252,'dims_mod_rssrequest','id_rsscat','id_rsscat','int(10) unsigned',1,6,0,0,1,1),(253,'dims_mod_rssrequest','request','request','varchar(255)',1,6,0,1,1,1),(254,'dims_mod_rssrequest','id','id','int(10) unsigned',0,6,0,0,1,1),(255,'dims_mod_rsscache','id_user','id_user','int(10) unsigned',0,6,0,0,1,1),(256,'dims_mod_rsscache','content','content','longtext',1,6,0,1,1,1),(257,'dims_mod_rsscache','id_workspace','id_workspace','int(10) unsigned',0,6,0,0,1,1),(258,'dims_mod_rsscache','id_module','id_module','int(10) unsigned',0,6,0,0,1,1),(259,'dims_mod_rsscache','author','author','varchar(255)',1,6,0,1,1,1),(260,'dims_mod_rsscache','description','description','longtext',1,6,0,1,1,1),(261,'dims_mod_rsscache','subject','subject','varchar(255)',1,6,0,1,1,1),(262,'dims_mod_rsscache','link','link','varchar(255)',1,6,0,1,1,1),(263,'dims_mod_rsscache','title','title','varchar(255)',1,6,0,1,1,1),(264,'dims_mod_rsscache','timestp','timestp','varchar(14)',0,6,0,1,1,1),(265,'dims_mod_rsscache','id_rssfeed','id_rssfeed','int(10) unsigned',0,6,0,0,1,1),(266,'dims_mod_rsscache','id','id','int(10) unsigned',0,6,2,0,1,1),(267,'dims_mod_rssfeed','updating_cache','updating_cache','tinyint(1) unsigned',1,6,0,0,1,1),(268,'dims_mod_rssfeed','id_rsscat','id_rsscat','int(10) unsigned',1,6,0,0,1,1),(269,'dims_mod_rssfeed','id_user','id_user','int(10) unsigned',0,6,0,0,1,1),(270,'dims_mod_rssfeed','id_module','id_module','int(10) unsigned',0,6,0,0,1,1),(271,'dims_mod_rssfeed','id_workspace','id_workspace','int(10) unsigned',0,6,0,0,1,1),(272,'dims_mod_rssfeed','language','language','varchar(255)',1,6,0,1,1,1),(273,'dims_mod_rssfeed','country','country','varchar(255)',1,6,0,1,1,1),(274,'dims_mod_directory_contact','id','id','int(10) unsigned',1,7,0,0,1,1),(275,'dims_mod_directory_contact','name','name','varchar(255)',1,7,0,1,1,1),(276,'dims_mod_directory_contact','firstname','firstname','varchar(255)',1,7,0,1,1,1),(277,'dims_mod_directory_contact','service','service','varchar(255)',1,7,0,1,1,1),(278,'dims_mod_directory_contact','function','function','varchar(255)',1,7,0,1,1,1),(279,'dims_mod_directory_contact','phone','phone','varchar(32)',1,7,0,1,1,1),(280,'dims_mod_directory_contact','mobile','mobile','varchar(32)',1,7,0,1,1,1),(281,'dims_mod_directory_contact','fax','fax','varchar(32)',1,7,0,1,1,1),(282,'dims_mod_directory_contact','email','email','varchar(255)',1,7,0,1,1,1),(283,'dims_mod_directory_contact','address','address','varchar(255)',1,7,0,1,1,1),(284,'dims_mod_directory_contact','postalcode','postalcode','varchar(32)',1,7,0,1,1,1),(285,'dims_mod_directory_contact','city','city','varchar(64)',1,7,0,1,1,1),(286,'dims_mod_directory_contact','country','country','varchar(64)',1,7,0,1,1,1),(287,'dims_mod_news','id','id','int(10) unsigned',0,8,1,0,1,1),(288,'dims_mod_news','id_newscat','id_newscat','int(10) unsigned',1,8,0,0,1,1),(289,'dims_mod_news','title','title','varchar(100)',1,8,0,1,1,1),(290,'dims_mod_news','resume','resume','varchar(255)',1,8,0,1,1,1),(291,'dims_mod_news','content','content','longtext',1,8,0,1,1,1),(292,'dims_mod_news','url','url','varchar(100)',1,8,0,1,1,1),(293,'dims_mod_news','urltitle','urltitle','varchar(100)',1,8,0,1,1,1),(294,'dims_mod_news','source','source','varchar(100)',1,8,0,1,1,1),(295,'dims_mod_news','published','published','tinyint(1) unsigned',1,8,0,0,1,1),(296,'dims_mod_news','id_module','id_module','int(10) unsigned',0,8,0,0,1,1),(297,'dims_mod_news','id_user','id_user','int(10) unsigned',0,8,0,0,1,1),(298,'dims_mod_news','date_publish','date_publish','varchar(14)',1,8,0,1,1,1),(299,'dims_mod_news','hot','hot','tinyint(1) unsigned',1,8,0,0,1,1),(300,'dims_mod_news','nbclick','nbclick','int(10) unsigned',1,8,0,0,1,1),(301,'dims_mod_news','id_workspace','id_workspace','int(10) unsigned',0,8,0,0,1,1),(302,'dims_mod_newscat','id','id','int(10) unsigned',0,8,2,0,1,1),(303,'dims_mod_newscat','description','description','varchar(255)',1,8,0,1,1,1),(304,'dims_mod_newscat','id_module','id_module','int(10) unsigned',0,8,0,0,1,1),(305,'dims_mod_newscat','id_user','id_user','tinyint(10) unsigned',1,8,0,0,1,1),(306,'dims_mod_newscat','id_user_modify','id_user_modify','tinyint(10) unsigned',1,8,0,0,1,1),(307,'dims_mod_newscat','date_create','date_create','datetime',1,8,0,0,1,1),(308,'dims_mod_newscat','date_modify','date_modify','datetime',1,8,0,0,1,1),(309,'dims_mod_newscat','title','title','varchar(100)',1,8,0,1,1,1),(310,'dims_mod_newscat','id_workspace','id_workspace','int(10) unsigned',0,8,0,0,1,1),(311,'dims_mod_newsparam','id_module','id_module','int(10) unsigned',0,8,0,0,1,1),(312,'dims_mod_newsparam','forumlinked','forumlinked','tinyint(1) unsigned',1,8,0,0,1,1),(313,'dims_mod_doc_file','parents','parents','varchar(255)',1,3,0,1,1,1),(314,'dims_mod_doc_file','size','size','int(10) unsigned',1,3,0,0,1,1),(317,'dims_mod_doc_file','id_workspace','id_workspace','int(10) unsigned',0,3,0,0,1,1),(318,'dims_mod_doc_file','date_modify','date_modify','datetime',1,3,0,0,1,1),(319,'dims_mod_doc_file','date_create','date_create','datetime',1,3,0,0,1,1),(320,'dims_mod_doc_file','id_user_modify','id_user_modify','int(10) unsigned',1,3,0,0,1,1),(321,'dims_mod_wce_heading','id_module','id_module','int(10) unsigned',0,2,0,0,1,1),(322,'dims_mod_wce_heading','free2','free2','varchar(255)',0,2,0,1,1,1),(323,'dims_mod_wce_heading','free1','free1','varchar(255)',0,2,0,1,1,1),(324,'dims_mod_wce_heading','url_window','url_window','tinyint(1)',0,2,0,0,1,1),(325,'dims_mod_wce_heading','url','url','varchar(255)',0,2,0,1,1,1),(326,'dims_mod_wce_heading','linkedpage','linkedpage','int(10) unsigned',0,2,0,0,1,1),(327,'dims_mod_wce_heading','visible','visible','tinyint(1)',0,2,0,0,1,1),(328,'dims_mod_wce_heading','posy','posy','int(10) unsigned',0,2,0,0,1,1),(329,'dims_mod_wce_heading','posx','posx','int(10) unsigned',0,2,0,0,1,1),(330,'dims_mod_wce_heading','timestp_unpublished','timestp_unpublished','bigint(14) unsigned',0,2,0,0,1,1),(331,'dims_mod_wce_heading','color','color','varchar(32)',0,2,0,1,1,1),(332,'dims_mod_wce_heading','position','position','int(10) unsigned',0,2,0,0,1,1),(333,'dims_mod_wce_heading','depth','depth','int(10)',0,2,0,0,1,1),(334,'dims_mod_wce_heading','parents','parents','varchar(255)',0,2,0,1,1,1),(335,'dims_mod_wce_heading','id_heading','id_heading','int(10) unsigned',0,2,0,0,1,1),(336,'dims_mod_wce_heading','template','template','varchar(255)',0,2,0,1,1,1),(337,'dims_mod_wce_heading','description','description','varchar(255)',1,2,0,1,1,1),(338,'dims_mod_wce_heading','label','label','varchar(255)',1,2,0,1,1,1),(339,'dims_mod_wce_heading','id','id','int(10) unsigned',0,2,2,0,1,1),(340,'dims_mod_wce_article','status','status','int(10) unsigned',0,2,0,0,1,1),(341,'dims_mod_wce_article','position','position','int(10) unsigned',0,2,0,0,1,1),(342,'dims_mod_wce_article','id_workspace','id_workspace','int(10) unsigned',0,2,0,0,1,1),(343,'dims_mod_wce_article','id_user','id_user','int(10) unsigned',0,2,0,0,1,1),(344,'dims_mod_wce_article','id_module','id_module','int(10) unsigned',0,2,0,0,1,1),(345,'dims_mod_wce_article','id_heading','id_heading','int(10) unsigned',0,2,0,0,1,1),(346,'dims_mod_wce_article','timestp_unpublished','timestp_unpublished','bigint(14) unsigned',0,2,0,0,1,1),(347,'dims_mod_wce_article','timestp_published','timestp_published','bigint(14) unsigned',0,2,0,0,1,1),(348,'dims_mod_wce_article','timestp','timestp','bigint(14) unsigned',0,2,0,0,1,1),(349,'dims_mod_wce_article','visible','visible','tinyint(1)',0,2,0,0,1,1),(350,'dims_mod_wce_article','version','version','varchar(16)',1,2,0,1,1,1),(351,'dims_mod_wce_article','author','author','longtext',1,2,0,1,1,1),(352,'dims_mod_wce_article','content9','content9','longtext',1,2,0,1,1,1),(353,'dims_mod_wce_article','content8','content8','longtext',1,2,0,1,1,1),(354,'dims_mod_forms_field','option_exportview','option_exportview','tinyint(1) unsigned',1,4,0,0,1,1),(355,'dims_mod_forms_field','option_needed','option_needed','tinyint(1) unsigned',1,4,0,0,1,1),(356,'dims_mod_forms_field','maxlength','maxlength','int(10) unsigned',1,4,0,0,1,1),(357,'dims_mod_forms_field','position','position','int(10) unsigned',1,4,0,0,1,1),(358,'dims_mod_forms_field','description','description','longtext',1,4,0,1,1,1),(359,'dims_mod_forms_field','values','values','longtext',1,4,0,1,1,1),(360,'dims_mod_forms_field','format','format','varchar(16)',1,4,0,1,1,1),(361,'dims_mod_forms_field','type','type','varchar(16)',1,4,0,1,1,1),(362,'dims_mod_forms_field','name','name','varchar(255)',1,4,0,1,1,1),(363,'dims_mod_forms_field','id_forms','id_forms','int(10) unsigned',1,4,0,0,1,1),(364,'dims_mod_forms_field','id','id','int(10) unsigned',0,4,0,0,1,1),(365,'dims_mod_forms','id_module','id_module','int(10)',1,4,0,0,1,1),(366,'dims_mod_forms','id_workspace','id_workspace','int(10)',1,4,0,0,1,1),(367,'dims_mod_forms','id_user','id_user','int(10)',1,4,0,0,1,1),(368,'dims_mod_forms','cms_response','cms_response','longtext',1,4,0,1,1,1),(369,'dims_mod_forms','cms_link','cms_link','tinyint(1)',1,4,0,0,1,1),(370,'dims_mod_forms','autobackup','autobackup','int(10)',1,4,0,0,1,1),(371,'dims_mod_forms','viewed','viewed','int(10)',1,4,0,0,1,1),(372,'dims_mod_forms','option_displayip','option_displayip','tinyint(1)',1,4,0,0,1,1),(373,'dims_mod_forms','option_displaygroup','option_displaygroup','tinyint(1)',1,4,0,0,1,1),(374,'dims_mod_forms','option_displaydate','option_displaydate','tinyint(1)',1,4,0,0,1,1),(375,'dims_mod_forms','option_view','option_view','varchar(16)',1,4,0,1,1,1),(376,'dims_mod_forms','option_displayuser','option_displayuser','tinyint(1)',1,4,0,0,1,1),(377,'dims_mod_forms','option_modify','option_modify','varchar(16)',1,4,0,1,1,1),(378,'dims_mod_forms','typeform','typeform','varchar(16)',1,4,0,1,1,1),(379,'dims_mod_forms','model','model','varchar(32)',1,4,0,1,1,1),(380,'dims_mod_forms','nbline','nbline','int(10)',1,4,0,0,1,1),(381,'dims_mod_forms','width','width','varchar(5)',1,4,0,1,1,1),(382,'dims_mod_forms','option_onlyoneday','option_onlyoneday','tinyint(1)',1,4,0,0,1,1),(383,'dims_mod_forms','option_onlyone','option_onlyone','tinyint(1)',1,4,0,0,1,1),(384,'dims_mod_forms','email','email','varchar(255)',1,4,0,1,1,1),(385,'dims_mod_forms','pubdate_end','pubdate_end','varchar(14)',1,4,0,1,1,1),(386,'dims_mod_forms','pubdate_start','pubdate_start','varchar(14)',1,4,0,1,1,1),(387,'dims_mod_forms','description','description','longtext',1,4,0,1,1,1),(389,'dims_mod_doc_file','id','id','int(10) unsigned',0,3,2,0,1,1),(390,'dims_mod_doc_file','name','name','varchar(100)',1,3,0,1,1,1),(391,'dims_mod_doc_file','description','description','varchar(255)',1,3,0,1,1,1),(392,'dims_mod_forms','label','label','varchar(255)',1,4,0,1,1,1),(393,'dims_mod_forms','id','id','int(10) unsigned',0,4,1,0,1,1),(394,'dims_mod_rssfeed','revisit','revisit','int(10) unsigned',1,6,0,0,1,1),(395,'dims_mod_rssfeed','lastvisit','lastvisit','varchar(14)',1,6,0,1,1,1),(396,'dims_mod_rssfeed','default','default','tinyint(1) unsigned',1,6,0,0,1,1),(397,'dims_mod_rssfeed','description','description','blob',1,6,0,0,1,1),(398,'dims_mod_rssfeed','url','url','varchar(255)',1,6,0,1,1,1),(399,'dims_mod_rssfeed','link','link','varchar(255)',1,6,0,1,1,1),(400,'dims_mod_rssfeed','title','title','varchar(255)',1,6,0,1,1,1),(401,'dims_mod_rssfeed','id','id','int(10) unsigned',0,6,1,0,1,1),(402,'dims_mod_rsscat','id_module','id_module','int(10) unsigned',0,6,0,0,1,1),(403,'dims_mod_rsscat','id_workspace','id_workspace','int(10) unsigned',0,6,0,0,1,1),(404,'dims_mod_rsscat','id_user','id_user','int(10) unsigned',0,6,0,0,1,1),(405,'dims_mod_rsscat','title','title','varchar(100)',1,6,0,1,1,1),(406,'dims_mod_rsscat','timestamp','timestamp','varchar(14)',1,6,0,1,1,1),(407,'dims_mod_rsscat','description','description','varchar(255)',1,6,0,1,1,1),(408,'dims_mod_rsscat','id','id','int(10) unsigned',0,6,0,0,1,1),(409,'dims_mod_wce_article','content7','content7','longtext',1,2,0,1,1,1),(410,'dims_mod_wce_article','content6','content6','longtext',1,2,0,1,1,1),(411,'dims_mod_wce_article','content5','content5','longtext',1,2,0,1,1,1),(412,'dims_mod_wce_article','content4','content4','longtext',1,2,0,1,1,1),(413,'dims_mod_wce_article','content3','content3','longtext',1,2,0,1,1,1),(414,'dims_mod_wce_article','content2','content2','longtext',1,2,0,1,1,1),(415,'dims_mod_wce_article','content1','content1','longtext',1,2,0,1,1,1),(416,'dims_mod_wce_article','title','title','varchar(255)',1,2,0,1,1,1),(417,'dims_mod_wce_article','reference','reference','varchar(255)',1,2,0,1,1,1),(418,'dims_mod_wce_article','id','id','int(10) unsigned',0,2,1,0,1,1),(419,'dims_mod_ww_inbox','id_workspace','id_workspace','int(10) unsigned',0,9,0,0,1,1),(420,'dims_mod_ww_inbox','label','label','varchar(255)',1,9,0,1,1,1),(421,'dims_mod_ww_inbox','lastupdate','lastupdate','datetime',0,9,0,0,1,1),(422,'dims_mod_ww_inbox','crypto','crypto','varchar(20)',1,9,0,1,1,1),(423,'dims_mod_ww_inbox','port','port','int(5)',0,9,0,0,1,1),(424,'dims_mod_ww_inbox','protocol','protocol','varchar(10)',1,9,0,1,1,1),(425,'dims_mod_ww_inbox','password','password','varchar(30)',1,9,0,1,1,1),(426,'dims_mod_ww_inbox','login','login','varchar(30)',1,9,0,1,1,1),(427,'dims_mod_ww_inbox','server','server','varchar(55)',1,9,0,1,1,1),(428,'dims_mod_ww_inbox','id','id','int(10) unsigned',1,9,1,0,1,1),(429,'dims_mod_ww_page','id_user','id_user','int(10) unsigned',1,9,0,0,1,1),(430,'dims_mod_ww_page','id_module','id_module','int(10) unsigned',1,9,0,0,1,1),(431,'dims_mod_ww_page','id_workspace','id_workspace','int(10) unsigned',0,9,0,0,1,1),(432,'dims_mod_ww_page','updated','updated','tinyint(1)',1,9,0,0,1,1),(433,'dims_mod_ww_page','notes','notes','text',1,9,0,1,1,1),(434,'dims_mod_ww_page','url','url','text',1,9,0,1,1,1),(435,'dims_mod_ww_page','name','name','varchar(255)',1,9,0,1,1,1),(436,'dims_mod_ww_page','id_site','id_site','int(10) unsigned',1,9,0,0,1,1),(437,'dims_mod_ww_page','id','id','int(10) unsigned',0,9,3,0,1,1),(438,'dims_mod_ww_site','id_user','id_user','int(10)',0,9,0,0,1,1),(439,'dims_mod_ww_site','id_module','id_module','int(10)',0,9,0,0,1,1),(440,'dims_mod_ww_site','id_workspace','id_workspace','int(10) unsigned',0,9,0,0,1,1),(441,'dims_mod_ww_site','id_inbox','id_inbox','int(10) unsigned',1,9,0,0,1,1),(442,'dims_mod_ww_site','domain','domain','varchar(255)',1,9,0,1,1,1),(443,'dims_mod_ww_site','id','id','int(10) unsigned',0,9,2,0,1,1),(444,'dims_mod_ww_inbox','id_module','id_module','int(10)',0,9,0,0,1,1),(445,'dims_mod_ww_inbox','id_user','id_user','int(10)',0,9,0,0,1,1),(446,'dims_mod_ww_email','id','id','int(10) unsigned',1,9,4,0,1,1),(447,'dims_mod_ww_email','id_page','id_page','int(10) unsigned',0,9,1,0,1,1),(448,'dims_mod_ww_email','content','content','longtext',1,9,0,1,1,1),(449,'dims_mod_ww_email','date','date','datetime',1,9,0,0,1,1),(450,'dims_mod_ww_email','watch','watch','tinyint(1)',0,9,0,0,1,1),(451,'dims_mod_ww_email','subject','subject','varchar(255)',1,9,0,1,1,1),(452,'dims_mod_ww_email','id_module','id_module','int(10) unsigned',1,9,0,0,1,1),(453,'dims_mod_ww_email','id_workspace','id_workspace','int(10) unsigned',1,9,0,0,1,1),(454,'dims_mod_ww_email','id_user','id_user','int(10)',1,9,0,0,1,1),(455,'dims_mod_agenda_organizer','id_workspace','id_workspace','int(10)',1,5,0,0,1,1),(456,'dims_mod_agenda_organizer','id_module','id_module','int(10)',0,5,0,0,1,1),(457,'dims_mod_agenda_organizer','id_user','id_user','int(10)',1,5,0,0,1,1),(458,'dims_mod_agenda_organizer','description','description','varchar(255)',1,5,0,1,1,1),(459,'dims_mod_agenda_organizer','id','id','int(10) unsigned',0,5,3,0,1,1),(460,'dims_mod_agenda_organizer','label','label','varchar(255)',1,5,0,1,1,1),(461,'dims_mod_agenda_cat','id_workspace','id_workspace','int(10)',1,5,0,0,1,1),(462,'dims_mod_agenda_cat','id_user','id_user','int(10)',1,5,0,0,1,1),(463,'dims_mod_agenda_cat','description','description','varchar(255)',1,5,0,1,1,1),(464,'dims_mod_agenda_cat','id_module','id_module','int(10)',0,5,0,0,1,1),(465,'dims_mod_agenda_cat','label','label','varchar(255)',1,5,0,1,1,1),(466,'dims_mod_agenda_cat','id','id','int(10) unsigned',0,5,1,0,1,1),(467,'dims_mod_agenda','id_workspace','id_workspace','int(10) unsigned',0,5,0,0,1,1),(468,'dims_mod_agenda','id_user','id_user','int(10) unsigned',0,5,0,0,1,1),(469,'dims_mod_agenda','id_module','id_module','int(10) unsigned',0,5,0,0,1,1),(470,'dims_mod_agenda','id_organizer','id_organizer','int(10) unsigned',0,5,0,0,1,1),(471,'dims_mod_agenda','id_cat','id_cat','int(10) unsigned',0,5,0,0,1,1),(472,'dims_mod_agenda','price','price','varchar(255)',1,5,0,1,1,1),(473,'dims_mod_agenda','location','location','varchar(255)',1,5,0,1,1,1),(474,'dims_mod_agenda','date_end','date_end','bigint(14)',1,5,0,0,1,1),(475,'dims_mod_agenda','date_start','date_start','bigint(14)',1,5,0,0,1,1),(476,'dims_mod_agenda','speaker','speaker','varchar(255)',1,5,0,1,1,1),(477,'dims_mod_agenda','description','description','text',1,5,0,1,1,1),(478,'dims_mod_agenda','label','label','varchar(255)',1,5,0,1,1,1),(479,'dims_mod_agenda','id','id','int(10) unsigned',0,5,2,0,1,1),(480,'dims_mod_business_action','id','id','int(10) unsigned',0,18,8,0,1,1),(481,'dims_mod_business_action','dossier_id','dossier_id','int(10) unsigned',1,18,0,0,1,1),(482,'dims_mod_business_action','tiers_id','tiers_id','int(10) unsigned',1,18,0,0,1,1),(483,'dims_mod_business_action','typeaction','typeaction','varchar(255)',1,18,0,1,1,1),(484,'dims_mod_business_action','acteur','acteur','int(10) unsigned',1,18,0,0,1,1),(485,'dims_mod_business_action','libelle','libelle','varchar(255)',1,18,0,1,1,1),(486,'dims_mod_business_action','description','description','longtext',1,18,0,1,1,1),(487,'dims_mod_business_action','conges','conges','tinyint(1) unsigned',1,18,0,0,1,1),(488,'dims_mod_business_action','personnel','personnel','tinyint(1) unsigned',1,18,0,0,1,1),(489,'dims_mod_business_action','temps_prevu','temps_prevu','int(5) unsigned',1,18,0,0,1,1),(490,'dims_mod_business_action','temps_passe','temps_passe','int(5) unsigned',1,18,0,0,1,1),(491,'dims_mod_business_action','km','km','int(10) unsigned',1,18,0,0,1,1),(492,'dims_mod_business_action','cout','cout','int(10) unsigned',1,18,0,0,1,1),(493,'dims_mod_business_action','temps_duplique','temps_duplique','char(3)',1,18,0,1,1,1),(494,'dims_mod_business_action','datejour','datejour','date',1,18,0,0,1,1),(495,'dims_mod_business_action','heuredeb','heuredeb','time',1,18,0,0,1,1),(496,'dims_mod_business_action','heurefin','heurefin','time',1,18,0,0,1,1),(497,'dims_mod_business_action','avancement','avancement','int(10) unsigned',1,18,0,0,1,1),(498,'dims_mod_business_action','priorite','priorite','tinyint(2) unsigned',1,18,0,0,1,1),(499,'dims_mod_business_action','id_module','id_module','int(10) unsigned',0,18,0,0,1,1),(500,'dims_mod_business_action','id_user','id_user','int(10) unsigned',1,18,0,0,1,1),(501,'dims_mod_business_action','id_workspace','id_workspace','int(10) unsigned',0,18,0,0,1,1),(502,'dims_mod_business_action','interloc_id','interloc_id','int(10)',1,18,0,0,1,1),(503,'dims_mod_business_action_detail','action_id','action_id','int(10) unsigned',1,1,0,0,1,1),(504,'dims_mod_business_action_detail','tiers_id','tiers_id','int(10) unsigned',1,1,0,0,1,1),(505,'dims_mod_business_action_detail','interlocuteur_id','interlocuteur_id','int(10) unsigned',1,1,0,0,1,1),(506,'dims_mod_business_action_detail','dossier_id','dossier_id','int(10) unsigned',1,1,0,0,1,1),(507,'dims_mod_business_action_detail','duree','duree','int(5) unsigned',1,1,0,0,1,1),(508,'dims_mod_business_action_detail','id_module','id_module','int(10) unsigned',0,1,0,0,1,1),(509,'dims_mod_business_action_detail','id_user','id_user','int(10) unsigned',1,1,0,0,1,1),(510,'dims_mod_business_action_detail','id_group','id_group','int(10) unsigned',0,1,0,0,1,1),(511,'dims_mod_business_action_utilisateur','user_id','user_id','int(10) unsigned',1,1,0,0,1,1),(512,'dims_mod_business_action_utilisateur','action_id','action_id','int(10) unsigned',1,1,0,0,1,1),(513,'dims_mod_business_action_utilisateur','resp','resp','varchar(10)',1,1,0,1,1,1),(514,'dims_mod_business_competence','code','code','varchar(16)',1,1,0,1,1,1),(515,'dims_mod_business_competence','intitule','intitule','varchar(255)',1,1,0,1,1,1),(516,'dims_mod_business_competence','intitule_allemand','intitule_allemand','varchar(255)',1,1,0,1,1,1),(517,'dims_mod_business_dossier','id','id','int(10) unsigned',0,1,0,0,1,1),(518,'dims_mod_business_dossier','procedure','procedure','varchar(255)',1,1,0,1,1,1),(519,'dims_mod_business_dossier','domaine_intervention','domaine_intervention','varchar(255)',1,1,0,1,1,1),(520,'dims_mod_business_dossier','objet_dossier','objet_dossier','varchar(255)',1,1,0,1,1,1),(521,'dims_mod_business_dossier','objet_dossier_search','objet_dossier_search','varchar(255)',1,1,0,1,1,1),(522,'dims_mod_business_dossier','date_debut','date_debut','varchar(10)',1,1,0,1,1,1),(523,'dims_mod_business_dossier','date_fin','date_fin','varchar(10)',1,1,0,1,1,1),(524,'dims_mod_business_dossier','avancement','avancement','varchar(255)',1,1,0,1,1,1),(525,'dims_mod_business_dossier','duree','duree','int(10) unsigned',1,1,0,0,1,1),(526,'dims_mod_business_dossier','termine','termine','char(3)',1,1,0,1,1,1),(527,'dims_mod_business_dossier','commentaire','commentaire','longtext',1,1,0,1,1,1),(528,'dims_mod_business_dossier','commentaire_search','commentaire_search','longtext',1,1,0,1,1,1),(529,'dims_mod_business_dossier','etape','etape','varchar(64)',1,1,0,1,1,1),(530,'dims_mod_business_dossier','id_module','id_module','int(10) unsigned',0,1,0,0,1,1),(531,'dims_mod_business_dossier','id_user','id_user','int(10) unsigned',1,1,0,0,1,1),(532,'dims_mod_business_dossier','id_group','id_group','int(10) unsigned',0,1,0,0,1,1),(533,'dims_mod_business_dossier','resp','resp','varchar(10)',1,1,0,1,1,1),(534,'dims_mod_business_enum','id','id','int(10) unsigned',0,1,0,0,1,1),(535,'dims_mod_business_enum','type','type','varchar(250)',1,1,0,1,1,1),(536,'dims_mod_business_enum','libelle','libelle','varchar(255)',1,1,0,1,1,1),(537,'dims_mod_business_enum','id_enum','id_enum','int(10)',1,1,0,0,1,1),(538,'dims_mod_business_enum_liens','type_parent','type_parent','varchar(250)',1,1,0,1,1,1),(539,'dims_mod_business_enum_liens','type_enfant','type_enfant','varchar(250)',1,1,0,1,1,1),(540,'dims_mod_business_equipement','code','code','varchar(16)',1,1,0,1,1,1),(541,'dims_mod_business_equipement','intitule','intitule','varchar(255)',1,1,0,1,1,1),(542,'dims_mod_business_interlocuteur','id','id','int(10) unsigned',0,1,0,0,1,1),(543,'dims_mod_business_interlocuteur','genre','genre','varchar(16)',1,1,0,1,1,1),(544,'dims_mod_business_interlocuteur','titre','titre','varchar(32)',1,1,0,1,1,1),(545,'dims_mod_business_interlocuteur','nom','nom','varchar(255)',1,1,0,1,1,1),(546,'dims_mod_business_interlocuteur','nom_search','nom_search','varchar(255)',1,1,0,1,1,1),(547,'dims_mod_business_interlocuteur','prenom','prenom','varchar(255)',1,1,0,1,1,1),(548,'dims_mod_business_interlocuteur','prenom_search','prenom_search','varchar(255)',1,1,0,1,1,1),(549,'dims_mod_business_interlocuteur','adresse','adresse','varchar(255)',1,1,0,1,1,1),(550,'dims_mod_business_interlocuteur','codepostal','codepostal','varchar(10)',1,1,0,1,1,1),(551,'dims_mod_business_interlocuteur','ville','ville','varchar(255)',1,1,0,1,1,1),(552,'dims_mod_business_interlocuteur','telephone','telephone','varchar(20)',1,1,0,1,1,1),(553,'dims_mod_business_interlocuteur','telecopie','telecopie','varchar(20)',1,1,0,1,1,1),(554,'dims_mod_business_interlocuteur','telmobile','telmobile','varchar(20)',1,1,0,1,1,1),(555,'dims_mod_business_interlocuteur','mel','mel','varchar(255)',1,1,0,1,1,1),(556,'dims_mod_business_interlocuteur','commentaire','commentaire','longtext',1,1,0,1,1,1),(557,'dims_mod_business_interlocuteur','pays','pays','varchar(255)',1,1,0,1,1,1),(558,'dims_mod_business_interlocuteur','date_creation','date_creation','date',1,1,0,0,1,1),(559,'dims_mod_business_interlocuteur','date_maj','date_maj','date',1,1,0,0,1,1),(560,'dims_mod_business_interlocuteur','id_module','id_module','int(10) unsigned',0,1,0,0,1,1),(561,'dims_mod_business_interlocuteur','id_user','id_user','int(10) unsigned',1,1,0,0,1,1),(562,'dims_mod_business_interlocuteur','id_group','id_group','int(10) unsigned',0,1,0,0,1,1),(563,'dims_mod_business_interlocuteur_categorie','id_interlocuteur','id_interlocuteur','int(10) unsigned',1,1,0,0,1,1),(564,'dims_mod_business_interlocuteur_categorie','categorie','categorie','varchar(64)',1,1,0,1,1,1),(565,'dims_mod_business_params','param','param','varchar(64)',1,1,0,1,1,1),(566,'dims_mod_business_params','value','value','varchar(255)',1,1,0,1,1,1),(567,'dims_mod_business_produit','reference','reference','varchar(16)',1,1,0,1,1,1),(568,'dims_mod_business_produit','libelle','libelle','varchar(255)',1,1,0,1,1,1),(569,'dims_mod_business_produit','libelle_search','libelle_search','varchar(255)',1,1,0,1,1,1),(570,'dims_mod_business_produit','description','description','longtext',1,1,0,1,1,1),(571,'dims_mod_business_produit','prix_achat','prix_achat','double',1,1,0,0,1,1),(572,'dims_mod_business_produit','prix_vente','prix_vente','double',1,1,0,0,1,1),(573,'dims_mod_business_produit','stock','stock','int(10) unsigned',1,1,0,0,1,1),(574,'dims_mod_business_produit','id_user','id_user','int(10) unsigned',1,1,0,0,1,1),(575,'dims_mod_business_produit','id_group','id_group','int(10) unsigned',0,1,0,0,1,1),(576,'dims_mod_business_produit','id_module','id_module','int(10) unsigned',0,1,0,0,1,1),(577,'dims_mod_business_suivi','id','id','int(10) unsigned',0,1,0,0,1,1),(578,'dims_mod_business_suivi','type','type','varchar(32)',1,1,0,1,1,1),(579,'dims_mod_business_suivi','exercice','exercice','varchar(32)',1,1,0,1,1,1),(580,'dims_mod_business_suivi','libelle','libelle','varchar(255)',1,1,0,1,1,1),(581,'dims_mod_business_suivi','description','description','longtext',1,1,0,1,1,1),(582,'dims_mod_business_suivi','datejour','datejour','date',1,1,0,0,1,1),(583,'dims_mod_business_suivi','montantht','montantht','double',1,1,0,0,1,1),(584,'dims_mod_business_suivi','montanttva','montanttva','double',1,1,0,0,1,1),(585,'dims_mod_business_suivi','remise','remise','double',1,1,0,0,1,1),(586,'dims_mod_business_suivi','montantttc','montantttc','double',1,1,0,0,1,1),(587,'dims_mod_business_suivi','tauxtva','tauxtva','double',1,1,0,0,1,1),(588,'dims_mod_business_suivi','solde','solde','double',1,1,0,0,1,1),(589,'dims_mod_business_suivi','dossier_id','dossier_id','int(10) unsigned',1,1,0,0,1,1),(590,'dims_mod_business_suivi','tiers_id','tiers_id','int(10) unsigned',1,1,0,0,1,1),(591,'dims_mod_business_suivi','id_user','id_user','int(10) unsigned',1,1,0,0,1,1),(592,'dims_mod_business_suivi','id_group','id_group','int(10) unsigned',0,1,0,0,1,1),(593,'dims_mod_business_suivi','id_module','id_module','int(10) unsigned',0,1,0,0,1,1),(594,'dims_mod_business_suivi_detail','id','id','int(10) unsigned',0,1,0,0,1,1),(595,'dims_mod_business_suivi_detail','suivi_id','suivi_id','int(10) unsigned',1,1,0,0,1,1),(596,'dims_mod_business_suivi_detail','suivi_type','suivi_type','varchar(32)',1,1,0,1,1,1),(597,'dims_mod_business_suivi_detail','suivi_exercice','suivi_exercice','varchar(32)',1,1,0,1,1,1),(598,'dims_mod_business_suivi_detail','code','code','varchar(32)',1,1,0,1,1,1),(599,'dims_mod_business_suivi_detail','libelle','libelle','varchar(255)',1,1,0,1,1,1),(600,'dims_mod_business_suivi_detail','description','description','longtext',1,1,0,1,1,1),(601,'dims_mod_business_suivi_detail','pu','pu','double',1,1,0,0,1,1),(602,'dims_mod_business_suivi_detail','qte','qte','double',1,1,0,0,1,1),(603,'dims_mod_business_suivi_detail','tauxtva','tauxtva','double',1,1,0,0,1,1),(604,'dims_mod_business_suivi_detail','position','position','int(10) unsigned',1,1,0,0,1,1),(785,'dims_mod_business_contact','id_workspace','id_workspace','int',0,1,0,0,1,1),(784,'dims_mod_business_contact','id_user','id_user','int',0,1,0,0,1,1),(783,'dims_mod_business_contact','id_module','id_module','int',0,1,0,0,1,1),(782,'dims_mod_business_tiers','id_user','id_user','int',0,1,0,0,1,1),(781,'dims_mod_business_tiers','id_workspace','id_workspace','int',0,1,0,0,1,1),(780,'dims_mod_business_tiers','id_module','id_module','int',0,1,0,0,1,1),(779,'dims_mod_business_tiers','presentation','_DIMS_LABEL_ENT_PRES','varchar',1,1,0,1,1,1),(778,'dims_mod_business_tiers','dirigeant','_DIMS_LABEL_ENT_DIR','varchar',1,1,0,1,1,1),(777,'dims_mod_business_tiers','pays','_DIMS_LABEL_COUNTRY','varchar',1,1,0,1,1,1),(776,'dims_mod_business_tiers','ent_activiteprincipale','_DIMS_LABEL_ENT_SECTACT','varchar',1,1,0,1,1,1),(775,'dims_mod_business_tiers','ent_capital','_DIMS_LABEL_ENT_CAPITAL','varchar',1,1,0,1,1,1),(774,'dims_mod_business_tiers','site_web','_DIMS_LABEL_ENT_WSITE','varchar',1,1,0,1,1,1),(773,'dims_mod_business_tiers','telecopie','_DIMS_LABEL_FAX','varchar',1,1,0,1,1,1),(772,'dims_mod_business_tiers','telephone','_DIMS_LABEL_TEL','varchar',1,1,0,1,1,1),(771,'dims_mod_business_tiers','ville','_DIMS_LABEL_CITY','varchar',1,1,0,1,1,1),(770,'dims_mod_business_tiers','codepostal','_DIMS_LABEL_CP','varchar',1,1,0,1,1,1),(769,'dims_mod_business_tiers','adresse','_DIMS_LABEL_ADDRESS','varchar',1,1,0,1,1,1),(768,'dims_mod_business_tiers','intitule','_DIMS_LABEL_ENT_RS','varchar',1,1,0,1,1,1),(767,'dims_mod_business_tiers','id','id','int',1,1,6,1,1,1),(766,'dims_mod_business_contact','professional','_DIMS_LABEL_CONT_SECTACT','varchar',1,1,0,1,1,1),(765,'dims_mod_business_contact','country','_DIMS_LABEL_COUNTRY','varchar',1,1,0,1,1,1),(764,'dims_mod_business_contact','comments','_DIMS_LABEL_COMMENT','varchar',1,1,0,1,1,1),(763,'dims_mod_business_contact','email','_DIMS_LABEL_EMAIL','varchar',1,1,0,1,1,1),(762,'dims_mod_business_contact','mobile','_DIMS_LABEL_MOBILE','varchar',1,1,0,1,1,1),(761,'dims_mod_business_contact','fax','_DIMS_LABEL_FAX_WORK','varchar',1,1,0,1,1,1),(760,'dims_mod_business_contact','phone','_DIMS_LABEL_TEL_WORK','varchar',1,1,0,1,1,1),(759,'dims_mod_business_contact','city','_DIMS_LABEL_CITY','varchar',1,1,0,1,1,1),(758,'dims_mod_business_contact','postalcode','_DIMS_LABEL_CP','varchar',1,1,0,1,1,1),(757,'dims_mod_business_contact','address','_DIMS_LABEL_ADDRESS','varchar',1,1,0,1,1,1),(756,'dims_mod_business_contact','vip','_DIMS_LABEL_CONT_VIP','varchar',1,1,0,1,1,1),(755,'dims_mod_business_contact','pers_phone','_DIMS_LABEL_TEL_DOMICILE','varchar',1,1,0,1,1,1),(754,'dims_mod_business_contact','nationalite','_DIMS_LABEL_NATIONALITY','varchar',1,1,0,1,1,1),(753,'dims_mod_business_contact','sexe','_DIMS_LABEL_SEXE','varchar',1,1,0,1,1,1),(752,'dims_mod_business_contact','civilite','_DIMS_LABEL_TITLE','varchar',1,1,0,1,1,1),(751,'dims_mod_business_contact','firstname','_DIMS_LABEL_FIRSTNAME','varchar',1,1,0,1,1,1),(749,'dims_mod_business_contact','id','id','int',1,1,7,0,1,1),(750,'dims_mod_business_contact','lastname','_DIMS_LABEL_NAME','varchar',1,1,0,1,1,1),(659,'dims_mod_business_tiers_competence','tiers_id','tiers_id','int(10)',1,1,0,0,1,1),(660,'dims_mod_business_tiers_competence','competence_code','competence_code','varchar(16)',1,1,0,1,1,1),(661,'dims_mod_business_tiers_dossier','interlocuteur_id','interlocuteur_id','int(3) unsigned',1,1,0,0,1,1),(662,'dims_mod_business_tiers_dossier','dossier_id','dossier_id','int(3) unsigned',1,1,0,0,1,1),(663,'dims_mod_business_tiers_dossier','tiers_id','tiers_id','int(3) unsigned',1,1,0,0,1,1),(664,'dims_mod_business_tiers_equipement','tiers_id','tiers_id','int(10)',1,1,0,0,1,1),(665,'dims_mod_business_tiers_equipement','equipement_code','equipement_code','varchar(16)',1,1,0,1,1,1),(666,'dims_mod_business_tiers_interlocuteur','tiers_id','tiers_id','int(5) unsigned',1,1,0,0,1,1),(667,'dims_mod_business_tiers_interlocuteur','interlocuteur_id','interlocuteur_id','int(5) unsigned',1,1,0,0,1,1),(668,'dims_mod_business_tiers_interlocuteur','service','service','varchar(255)',1,1,0,1,1,1),(669,'dims_mod_business_tiers_interlocuteur','telephone','telephone','varchar(20)',1,1,0,1,1,1),(670,'dims_mod_business_tiers_interlocuteur','telecopie','telecopie','varchar(20)',1,1,0,1,1,1),(671,'dims_mod_business_tiers_interlocuteur','telmobile','telmobile','varchar(20)',1,1,0,1,1,1),(672,'dims_mod_business_tiers_interlocuteur','mel','mel','varchar(255)',1,1,0,1,1,1),(673,'dims_mod_business_tiers_interlocuteur','fonction','fonction','varchar(255)',1,1,0,1,1,1),(674,'dims_mod_business_tiers_interlocuteur','adresse','adresse','varchar(255)',1,1,0,1,1,1),(675,'dims_mod_business_tiers_interlocuteur','codepostal','codepostal','varchar(16)',1,1,0,1,1,1),(676,'dims_mod_business_tiers_interlocuteur','ville','ville','varchar(64)',1,1,0,1,1,1),(677,'dims_mod_business_tiers_interlocuteur','pays','pays','varchar(64)',1,1,0,1,1,1),(678,'dims_mod_business_tiers_interlocuteur','id_module','id_module','int(10) unsigned',0,1,0,0,1,1),(679,'dims_mod_business_tiers_interlocuteur','id_group','id_group','int(10) unsigned',0,1,0,0,1,1),(680,'dims_mod_business_tiers_interlocuteur','id_user','id_user','int(10) unsigned',1,1,0,0,1,1),(681,'dims_mod_business_versement','id','id','int(10) unsigned',0,1,0,0,1,1),(682,'dims_mod_business_versement','suivi_id','suivi_id','int(10) unsigned',1,1,0,0,1,1),(683,'dims_mod_business_versement','suivi_type','suivi_type','varchar(32)',1,1,0,1,1,1),(684,'dims_mod_business_versement','suivi_exercice','suivi_exercice','varchar(32)',1,1,0,1,1,1),(685,'dims_mod_business_versement','montant','montant','double',1,1,0,0,1,1),(686,'dims_mod_business_versement','date_paiement','date_paiement','varchar(14)',1,1,0,1,1,1),(687,'dims_mod_sharefile_history','id_user','id_user','int(11) unsigned',1,22,0,0,1,1),(688,'dims_mod_sharefile_history','id_workspace','id_workspace','int(11) unsigned',0,22,0,0,1,1),(689,'dims_mod_sharefile_history','id_module','id_module','int(11) unsigned',0,22,0,0,1,1),(690,'dims_mod_sharefile_history','timestp_create','timestp_create','bigint',0,22,0,0,1,1),(691,'dims_mod_sharefile_history','action','action','tinyint',0,22,0,0,1,1),(692,'dims_mod_sharefile_history','id_share','id_share','int(11) unsigned',0,22,0,0,1,1),(693,'dims_mod_sharefile_history','id','id','int(11) unsigned',0,22,0,0,1,1),(694,'dims_mod_sharefile_file','download','download','int(11)',0,22,0,0,1,1),(695,'dims_mod_sharefile_file','id_doc','id_doc','int(11) unsigned',0,22,0,0,1,1),(696,'dims_mod_sharefile_file','id_share','id_share','int(11) unsigned',0,22,0,0,1,1),(697,'dims_mod_sharefile_share','id_workspace','id_workspace','int(11) unsigned',0,22,0,0,1,1),(698,'dims_mod_sharefile_share','id_user','id_user','int(11) unsigned',1,22,0,0,1,1),(699,'dims_mod_sharefile_share','id_module','id_module','int(11) unsigned',0,22,0,0,1,1),(700,'dims_mod_sharefile_share','description','description','text',1,22,0,1,1,1),(701,'dims_mod_sharefile_share','label','label','varchar(255)',1,22,0,1,1,1),(702,'dims_mod_sharefile_share','id','id','int(11) unsigned',0,22,1,0,1,1),(719,'dims_mod_wce_heading','id_workspace','id_workspace','int(10) unsigned',0,2,0,0,1,1),(720,'dims_mod_faq_reponse','id_workspace','id_workspace','int(10) unsigned',0,17,0,0,1,1),(721,'dims_mod_faq_reponse','id_user','id_user','int(10) unsigned',1,17,0,0,1,1),(722,'dims_mod_faq_reponse','id_module','id_module','int(10) unsigned',0,17,0,0,1,1),(723,'dims_mod_faq_reponse','reponse','reponse','longtext',1,17,0,1,1,1),(724,'dims_mod_faq_reponse','id','id','int(10) unsigned',0,17,2,0,1,1),(725,'dims_mod_faq_reponse','id_question','id_question','int(10) unsigned',0,17,0,0,1,1),(726,'dims_mod_faq_question','id_workspace','id_workspace','int(10) unsigned',0,17,0,0,1,1),(727,'dims_mod_faq_question','id_user','id_user','int(10) unsigned',1,17,0,0,1,1),(728,'dims_mod_faq_question','id_module','id_module','int(10) unsigned',0,17,0,0,1,1),(729,'dims_mod_faq_question','question','question','longtext',1,17,0,1,1,1),(730,'dims_mod_faq_question','id','id','int(10) unsigned',0,17,1,0,1,1),(731,'dims_mod_doc_file','content','content','longtext',1,1,0,1,1,1),(732,'dims_mod_doc_file','date_create','date_create','datetime',1,1,0,0,1,1),(733,'dims_mod_doc_file','date_modify','date_modify','datetime',1,1,0,0,1,1),(734,'dims_mod_doc_file','description','description','varchar(255)',1,1,0,1,1,1),(735,'dims_mod_doc_file','extension','extension','varchar(20)',1,1,0,1,1,1),(737,'dims_mod_doc_file','id_docfolder','id_docfolder','int(10) unsigned',1,1,0,0,1,1),(738,'dims_mod_doc_file','id_module','id_module','int(10) unsigned',0,1,0,0,1,1),(739,'dims_mod_doc_file','id_user','id_user','int(10) unsigned',1,1,0,0,1,1),(740,'dims_mod_doc_file','id_user_modify','id_user_modify','int(10) unsigned',1,1,0,0,1,1),(741,'dims_mod_doc_file','id_workspace','id_workspace','int(10) unsigned',0,1,0,0,1,1),(742,'dims_mod_doc_file','name','name','varchar(100)',1,1,0,1,1,1),(743,'dims_mod_doc_file','nbclick','nbclick','int(10) unsigned',1,1,0,0,1,1),(744,'dims_mod_doc_file','parents','parents','varchar(255)',1,1,0,1,1,1),(745,'dims_mod_doc_file','rights_all','rights_all','char(3)',1,1,0,1,1,1),(746,'dims_mod_doc_file','rights_group','rights_group','char(3)',1,1,0,1,1,1),(747,'dims_mod_doc_file','rights_user','rights_user','char(3)',1,1,0,1,1,1),(748,'dims_mod_doc_file','size','size','int(10) unsigned',1,1,0,0,1,1),(786,'dims_mod_business_contact','email2','_DIMS_LABEL_EMAIL2','varchar',1,1,0,1,1,1),(787,'dims_mod_business_contact','email3','_DIMS_LABEL_EMAIL3','varchar',1,1,0,1,1,1),(789,'dims_mod_business_contact','phone2','_DIMS_LABEL_PHONE2','varchar',1,1,0,1,1,1),(790,'dims_mod_business_contact','photo','_DIMS_LABEL_PHOTO','varchar',1,1,0,1,1,1),(791,'dims_mod_business_contact','partenaire','_DIMS_LABEL_SPONSOR','varchar',1,1,0,0,1,1),(798,'dims_mod_business_contact','field1','Motivation','varchar(255)',1,1,0,0,0,1),(799,'dims_mod_business_contact','field2','Position','varchar(255)',1,1,0,0,0,1),(800,'dims_mod_business_contact','field3','Carte de voeux','varchar(255)',1,1,0,0,0,1),(801,'dims_mod_business_contact','field4','Cadeaux','varchar(255)',1,1,0,0,0,1),(802,'dims_mod_business_contact','field5','Suivi ECOGEST','varchar(255)',1,1,0,0,0,1),(803,'dims_mod_business_contact','field6','Projet de création','varchar(255)',1,1,0,0,0,1),(810,'dims_mod_business_contact','field8','Partenaire événément','varchar(255)',1,1,0,0,0,1),(809,'dims_mod_business_contact','field7','Déjà visité le Luxembourg ','varchar(255)',1,1,0,0,0,1),(812,'dims_mod_business_tiers','field1','Projet en cours','varchar(255)',1,1,0,0,0,1),(813,'dims_mod_business_tiers','field2','_DIMS_LABEL_ZONE_GEO_INT','varchar(255)',1,1,0,0,0,1),(814,'dims_mod_business_tiers','field3','Financements reçus','varchar(255)',1,1,0,0,0,1),(815,'dims_mod_business_tiers','field4','Lien cours bourse','varchar(255)',1,1,0,0,0,1),(816,'dims_mod_business_tiers','photo','_DIMS_LABEL_PHOTO','varchar(255)',1,1,0,1,1,1),(820,'dims_mod_business_contact','field9','Address 2','varchar(255)',1,1,0,1,0,1),(821,'dims_mod_business_contact','field10','State','varchar(255)',1,1,0,1,0,1);
/*!40000 ALTER TABLE `dims_mb_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mb_object`
--

DROP TABLE IF EXISTS `dims_mb_object`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mb_object` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `label` varchar(255) DEFAULT NULL,
  `script` varchar(255) DEFAULT NULL,
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`id_module_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mb_object`
--

LOCK TABLES `dims_mb_object` WRITE;
/*!40000 ALTER TABLE `dims_mb_object` DISABLE KEYS */;
INSERT INTO `dims_mb_object` VALUES (2,'Groupe d\'Utilisateur','dims_mainmenu=1&dims_moduleid=1&dims_action=admin&system_level=org&groupid=<IDRECORD>',1),(1,'Espace de Travail','dims_mainmenu=1&dims_moduleid=1&dims_action=admin&system_level=work&groupid=<IDRECORD>',1),(2,'Article','dims_mainmenu=1&dims_moduleid=<IDMODULE>&dims_action=admin&op=article_modify&type=draft&articleid=<IDRECORD>',2),(1,'Article','dims_mainmenu=1&dims_moduleid=<IDMODULE>&dims_action=admin&op=article_modify&type=draft&articleid=<IDRECORD>',2),(1,'Dossier','dims_mainmenu=1&dims_moduleid=<IDMODULE>&dims_action=public&op=browser&currentfolder=<IDRECORD>',3),(1,'News','dims_mainmenu=1&dims_moduleid=<IDMODULE>&dims_action=public&news_id=<IDRECORD>',8),(2,'Fichier','dims_mainmenu=1&dims_moduleid=<IDMODULE>&dims_action=public&op=file_modify&docfile_id=<IDRECORD>',3),(2,'Page','dims_mainmenu=1&dims_moduleid=<IDMODULE>&dims_action=public&op=page&id_page=<IDRECORD>',9),(1,'Site','dims_mainmenu=1&dims_moduleid=<IDMODULE>&dims_action=public&op=site&id_site=<IDRECORD>',9),(2,'Réponse','dims_mainmenu=1&dims_moduleid=<IDMODULE>&dims_action=public&op=view_reponse&id_r=<IDRECORD>',17),(1,'Question','dims_mainmenu=1&dims_moduleid=<IDMODULE>&dims_action=public&op=view_question&id_q=<IDRECORD>',17),(7,'Fiche personne','?dims_mainmenu=9&dims_moduleid=<IDMODULE>&dims_actions=public&action=301&contact_id=<IDRECORD>',1),(6,'Fiche entreprise','?dims_mainmenu=9&dims_moduleid=<IDMODULE>&dims_actions=public&action=401&id_ent=<IDRECORD>',1),(11,'Newsletter','?dims_mainmenu=11&dims_moduleid=<IDMODULE>&dims_actions=public&action=modif_news&id_newsletter=<IDRECORD>',1);
/*!40000 ALTER TABLE `dims_mb_object` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mb_relation`
--

DROP TABLE IF EXISTS `dims_mb_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mb_relation` (
  `tablesrc` varchar(100) DEFAULT NULL,
  `fieldsrc` varchar(100) DEFAULT NULL,
  `tabledest` varchar(100) DEFAULT NULL,
  `fielddest` varchar(100) DEFAULT NULL,
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mb_relation`
--

LOCK TABLES `dims_mb_relation` WRITE;
/*!40000 ALTER TABLE `dims_mb_relation` DISABLE KEYS */;
INSERT INTO `dims_mb_relation` VALUES ('dims_mb_relation','tabledest','dims_mb_field','tablename',1),('dims_mb_relation','fieldsrc','dims_mb_field','name',1),('dims_mb_relation','tablesrc','dims_mb_field','tablename',1),('dims_user_filter_rules','id_group','dims_group','id',1),('dims_user_type_fields','id_type','dims_type','id',1),('dims_user_action_log','id_user','dims_user','id',1),('dims_user_action_log','id_record','dims_record','id',1),('dims_user_action_log','id_module_type','dims_module_type','id',1),('dims_user_action_log','id_module','dims_module','id',1),('dims_user','id_type','dims_type','id',1),('dims_user_action_log','id_action','dims_action','id',1),('dims_user','id_ldap','dims_ldap','id',1),('dims_mod_news','id_newscat','dims_newscat','id',1),('dims_role_action','id_action','dims_action','id',1),('dims_role_action','id_role','dims_role','id',1),('dims_role_action','id_module_type','dims_module_type','id',1),('dims_role','id_module','dims_module','id',1),('dims_role','id_group','dims_group','id',1),('dims_profile','id_group','dims_group','id',1),('dims_param_user','id_param_type','dims_param_type','id',1),('dims_param_user','id_module','dims_module','id',1),('dims_param_user','id_user','dims_user','id',1),('dims_param_type','id_module_type','dims_module_type','id',1),('dims_param_group','id_group','dims_group','id',1),('dims_param_group','id_param_type','dims_param_type','id',1),('dims_param_group','id_module','dims_module','id',1),('dims_param_default','id_module','dims_module','id',1),('dims_param_default','id_param_type','dims_param_type','id',1),('dims_param_choice','id_param_type','dims_param_type','id',1),('dims_module_group','id_group','dims_group','id',1),('dims_module_group','id_module','dims_module','id',1),('dims_module','id_group','dims_group','id',1),('dims_module','id_module_type','dims_module_type','id',1),('dims_mb_table','id_module_type','dims_module_type','id',1),('dims_mb_schema','id_module_type','dims_module_type','id',1),('dims_mb_relation','id_module_type','dims_module_type','id',1),('dims_mb_field','id_module_type','dims_module_type','id',1),('dims_homepage_line','id_group','dims_group','id',1),('dims_homepage_line','id_user','dims_user','id',1),('dims_homepage_column','id_line','dims_line','id',1),('dims_homepage_column','id_module','dims_module','id',1),('dims_group_user_role','id_group','dims_group','id',1),('dims_group_user_role','id_role','dims_role','id',1),('dims_group_user_role','id_user','dims_user','id',1),('dims_group_user','id_group','dims_group','id',1),('dims_group_user','id_profile','dims_profile','id',1),('dims_group_user','id_user','dims_user','id',1),('dims_cms_object','id_module_type','dims_module_type','id',1),('dims_action','id_module_type','dims_module_type','id',1),('dims_role_profile','id_profile','dims_profile','id',1),('dims_role_profile','id_role','dims_role','id',1),('dims_rule','id_group','dims_group','id',1),('dims_rule','id_profile','dims_profile','id',1),('dims_rule','id_type','dims_type','id',1),('dims_mb_relation','fielddest','dims_mb_field','name',1),('dims_mb_field','tablename','dims_mb_table','name',1),('dims_mb_schema','tablesrc','dims_mb_table','name',1),('dims_mb_schema','tabledest','dims_mb_table','name',1),('dims_mod_docfolder','id_workspace','dims_workspace','id',3),('dims_mod_docfolder','id_user_create','dims_user','id',3),('dims_mod_docfolder','id_module','dims_module','id',3),('dims_mod_docext','id_doctype','dims_mod_doctype','id',3),('dims_mod_doc_param','ext_id','dims_mod_docext','id',3),('dims_mod_doc_file','id_workspace','dims_workspace','id',3),('dims_mod_doc_file','id_user_create','dims_user','id',3),('dims_mod_forms_reply_field','id_forms','dims_mod_forms','id',4),('dims_mod_forms_reply_field','id_field','dims_mod_forms_field','id',4),('dims_mod_forms_reply_field','id_reply','dims_mod_forms_reply','id',4),('dims_mod_forms_reply','id_module','dims_module','id',4),('dims_mod_business_action','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_rssrequest','id_module','dims_module','id',6),('dims_mod_rssrequest','id_workspace','dims_workspace','id',6),('dims_mod_rssrequest','id_user','dims_user','id',6),('dims_mod_rssrequest','id_rsscat','dims_mod_rsscat','id',6),('dims_mod_rssfeed','id_module','dims_module','id',6),('dims_mod_rssfeed','id_workspace','dims_workspace','id',6),('dims_mod_rssfeed','id_user','dims_user','id',6),('dims_mod_directory_contact','id_user','dims_user','id',7),('dims_mod_directory_contact','id_group','dims_group','id',7),('dims_mod_directory_contact','id_module','dims_module','id',7),('dims_mod_directory_favorites','id_contact','dims_mod_directory_contact','id',7),('dims_mod_directory_favorites','id_user','dims_user','id',7),('dims_mod_news','id_newscat','dims_mod_newscat','id',8),('dims_mod_news','id_module','dims_module','id',8),('dims_mod_news','id_user','dims_user','id',8),('dims_mod_news','id_group','dims_group','id',8),('dims_mod_newscat','id_module','dims_module','id',8),('dims_mod_newscat','id_user_create','dims_mod_newsuser_create','id',8),('dims_mod_newscat','id_user_modify','dims_mod_newsuser_modify','id',8),('dims_mod_newscat','id_group','dims_group','id',8),('dims_mod_newsparam','id_module','dims_module','id',8),('dims_mod_doc_file','id_module','dims_module','id',3),('dims_mod_doc_file','id_docfolder','dims_mod_docfolder','id',3),('dims_mod_forms_reply','id_group','dims_group','id',4),('dims_mod_forms_reply','id_forms','dims_mod_forms','id',4),('dims_mod_forms_reply','id_user','dims_user','id',4),('dims_mod_forms_field','id_forms','dims_mod_forms','id',4),('dims_mod_rsscat','id_module','dims_module','id',6),('dims_mod_rssfeed','id_rsscat','dims_mod_rsscat','id',6),('dims_mod_rsscat','id_workspace','dims_workspace','id',6),('dims_mod_rsscat','id_user','dims_user','id',6),('dims_mod_rsscache','id_rssfeed','dims_mod_rssfeed','id',6),('dims_mod_business_action','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_action','id_module','dims_module','id',0),('dims_mod_business_action','id_user','dims_user','id',0),('dims_mod_business_action','id_group','dims_group','id',0),('dims_mod_business_action','interloc_id','dims_mod_business_interloc','id',0),('dims_mod_business_action_detail','action_id','dims_mod_business_action','id',0),('dims_mod_business_action_detail','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_action_detail','interlocuteur_id','dims_mod_business_interlocuteur','id',0),('dims_mod_business_action_detail','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_action_detail','id_module','dims_module','id',0),('dims_mod_business_action_detail','id_user','dims_user','id',0),('dims_mod_business_action_detail','id_group','dims_group','id',0),('dims_mod_business_action_utilisateur','user_id','dims_mod_business_user','id',0),('dims_mod_business_action_utilisateur','action_id','dims_mod_business_action','id',0),('dims_mod_business_dossier','id_module','dims_module','id',0),('dims_mod_business_dossier','id_user','dims_user','id',0),('dims_mod_business_dossier','id_group','dims_group','id',0),('dims_mod_business_enum','id_enum','dims_mod_business_enum','id',0),('dims_mod_business_interlocuteur','id_module','dims_module','id',0),('dims_mod_business_interlocuteur','id_user','dims_user','id',0),('dims_mod_business_interlocuteur','id_group','dims_group','id',0),('dims_mod_business_interlocuteur_categorie','id_interlocuteur','dims_mod_business_interlocuteur','id',0),('dims_mod_business_produit','id_user','dims_user','id',0),('dims_mod_business_produit','id_group','dims_group','id',0),('dims_mod_business_produit','id_module','dims_module','id',0),('dims_mod_business_suivi','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_suivi','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_suivi','id_user','dims_user','id',0),('dims_mod_business_suivi','id_group','dims_group','id',0),('dims_mod_business_suivi','id_module','dims_module','id',0),('dims_mod_business_suivi_detail','suivi_id','dims_mod_business_suivi','id',0),('dims_mod_business_tiers','id_module','dims_module','id',0),('dims_mod_business_tiers','id_user','dims_user','id',0),('dims_mod_business_tiers','id_group','dims_group','id',0),('dims_mod_business_tiers','id_tiers','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_competence','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_dossier','interlocuteur_id','dims_mod_business_interlocuteur','id',0),('dims_mod_business_tiers_dossier','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_tiers_dossier','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_equipement','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_interlocuteur','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_interlocuteur','interlocuteur_id','dims_mod_business_interlocuteur','id',0),('dims_mod_business_tiers_interlocuteur','id_module','dims_module','id',0),('dims_mod_business_tiers_interlocuteur','id_group','dims_group','id',0),('dims_mod_business_tiers_interlocuteur','id_user','dims_user','id',0),('dims_mod_business_versement','suivi_id','dims_mod_business_suivi','id',0),('dims_mod_business_action','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_action','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_action','id_module','dims_module','id',0),('dims_mod_business_action','id_user','dims_user','id',0),('dims_mod_business_action','id_group','dims_group','id',0),('dims_mod_business_action','interloc_id','dims_mod_business_interloc','id',0),('dims_mod_business_action_detail','action_id','dims_mod_business_action','id',0),('dims_mod_business_action_detail','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_action_detail','interlocuteur_id','dims_mod_business_interlocuteur','id',0),('dims_mod_business_action_detail','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_action_detail','id_module','dims_module','id',0),('dims_mod_business_action_detail','id_user','dims_user','id',0),('dims_mod_business_action_detail','id_group','dims_group','id',0),('dims_mod_business_action_utilisateur','user_id','dims_mod_business_user','id',0),('dims_mod_business_action_utilisateur','action_id','dims_mod_business_action','id',0),('dims_mod_business_dossier','id_module','dims_module','id',0),('dims_mod_business_dossier','id_user','dims_user','id',0),('dims_mod_business_dossier','id_group','dims_group','id',0),('dims_mod_business_enum','id_enum','dims_mod_business_enum','id',0),('dims_mod_business_interlocuteur','id_module','dims_module','id',0),('dims_mod_business_interlocuteur','id_user','dims_user','id',0),('dims_mod_business_interlocuteur','id_group','dims_group','id',0),('dims_mod_business_interlocuteur_categorie','id_interlocuteur','dims_mod_business_interlocuteur','id',0),('dims_mod_business_produit','id_user','dims_user','id',0),('dims_mod_business_produit','id_group','dims_group','id',0),('dims_mod_business_produit','id_module','dims_module','id',0),('dims_mod_business_suivi','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_suivi','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_suivi','id_user','dims_user','id',0),('dims_mod_business_suivi','id_group','dims_group','id',0),('dims_mod_business_suivi','id_module','dims_module','id',0),('dims_mod_business_suivi_detail','suivi_id','dims_mod_business_suivi','id',0),('dims_mod_business_tiers','id_module','dims_module','id',0),('dims_mod_business_tiers','id_user','dims_user','id',0),('dims_mod_business_tiers','id_group','dims_group','id',0),('dims_mod_business_tiers','id_tiers','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_competence','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_dossier','interlocuteur_id','dims_mod_business_interlocuteur','id',0),('dims_mod_business_tiers_dossier','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_tiers_dossier','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_equipement','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_interlocuteur','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_interlocuteur','interlocuteur_id','dims_mod_business_interlocuteur','id',0),('dims_mod_business_tiers_interlocuteur','id_module','dims_module','id',0),('dims_mod_business_tiers_interlocuteur','id_group','dims_group','id',0),('dims_mod_business_tiers_interlocuteur','id_user','dims_user','id',0),('dims_mod_business_versement','suivi_id','dims_mod_business_suivi','id',0),('dims_mod_business_action','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_action','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_action','id_module','dims_module','id',0),('dims_mod_business_action','id_user','dims_user','id',0),('dims_mod_business_action','id_group','dims_group','id',0),('dims_mod_business_action','interloc_id','dims_mod_business_interloc','id',0),('dims_mod_business_action_detail','action_id','dims_mod_business_action','id',0),('dims_mod_business_action_detail','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_action_detail','interlocuteur_id','dims_mod_business_interlocuteur','id',0),('dims_mod_business_action_detail','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_action_detail','id_module','dims_module','id',0),('dims_mod_business_action_detail','id_user','dims_user','id',0),('dims_mod_business_action_detail','id_group','dims_group','id',0),('dims_mod_business_action_utilisateur','user_id','dims_mod_business_user','id',0),('dims_mod_business_action_utilisateur','action_id','dims_mod_business_action','id',0),('dims_mod_business_dossier','id_module','dims_module','id',0),('dims_mod_business_dossier','id_user','dims_user','id',0),('dims_mod_business_dossier','id_group','dims_group','id',0),('dims_mod_business_enum','id_enum','dims_mod_business_enum','id',0),('dims_mod_business_interlocuteur','id_module','dims_module','id',0),('dims_mod_business_interlocuteur','id_user','dims_user','id',0),('dims_mod_business_interlocuteur','id_group','dims_group','id',0),('dims_mod_business_interlocuteur_categorie','id_interlocuteur','dims_mod_business_interlocuteur','id',0),('dims_mod_business_produit','id_user','dims_user','id',0),('dims_mod_business_produit','id_group','dims_group','id',0),('dims_mod_business_produit','id_module','dims_module','id',0),('dims_mod_business_suivi','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_suivi','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_suivi','id_user','dims_user','id',0),('dims_mod_business_suivi','id_group','dims_group','id',0),('dims_mod_business_suivi','id_module','dims_module','id',0),('dims_mod_business_suivi_detail','suivi_id','dims_mod_business_suivi','id',0),('dims_mod_business_tiers','id_module','dims_module','id',0),('dims_mod_business_tiers','id_user','dims_user','id',0),('dims_mod_business_tiers','id_group','dims_group','id',0),('dims_mod_business_tiers','id_tiers','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_competence','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_dossier','interlocuteur_id','dims_mod_business_interlocuteur','id',0),('dims_mod_business_tiers_dossier','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_tiers_dossier','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_equipement','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_interlocuteur','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_interlocuteur','interlocuteur_id','dims_mod_business_interlocuteur','id',0),('dims_mod_business_tiers_interlocuteur','id_module','dims_module','id',0),('dims_mod_business_tiers_interlocuteur','id_group','dims_group','id',0),('dims_mod_business_tiers_interlocuteur','id_user','dims_user','id',0),('dims_mod_business_versement','suivi_id','dims_mod_business_suivi','id',0),('dims_mod_business_action','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_action','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_action','id_module','dims_module','id',0),('dims_mod_business_action','id_user','dims_user','id',0),('dims_mod_business_action','id_group','dims_group','id',0),('dims_mod_business_action','interloc_id','dims_mod_business_interloc','id',0),('dims_mod_business_action_detail','action_id','dims_mod_business_action','id',0),('dims_mod_business_action_detail','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_action_detail','interlocuteur_id','dims_mod_business_interlocuteur','id',0),('dims_mod_business_action_detail','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_action_detail','id_module','dims_module','id',0),('dims_mod_business_action_detail','id_user','dims_user','id',0),('dims_mod_business_action_detail','id_group','dims_group','id',0),('dims_mod_business_action_utilisateur','user_id','dims_mod_business_user','id',0),('dims_mod_business_action_utilisateur','action_id','dims_mod_business_action','id',0),('dims_mod_business_dossier','id_module','dims_module','id',0),('dims_mod_business_dossier','id_user','dims_user','id',0),('dims_mod_business_dossier','id_group','dims_group','id',0),('dims_mod_business_enum','id_enum','dims_mod_business_enum','id',0),('dims_mod_business_interlocuteur','id_module','dims_module','id',0),('dims_mod_business_interlocuteur','id_user','dims_user','id',0),('dims_mod_business_interlocuteur','id_group','dims_group','id',0),('dims_mod_business_interlocuteur_categorie','id_interlocuteur','dims_mod_business_interlocuteur','id',0),('dims_mod_business_produit','id_user','dims_user','id',0),('dims_mod_business_produit','id_group','dims_group','id',0),('dims_mod_business_produit','id_module','dims_module','id',0),('dims_mod_business_suivi','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_suivi','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_suivi','id_user','dims_user','id',0),('dims_mod_business_suivi','id_group','dims_group','id',0),('dims_mod_business_suivi','id_module','dims_module','id',0),('dims_mod_business_suivi_detail','suivi_id','dims_mod_business_suivi','id',0),('dims_mod_business_tiers','id_module','dims_module','id',0),('dims_mod_business_tiers','id_user','dims_user','id',0),('dims_mod_business_tiers','id_group','dims_group','id',0),('dims_mod_business_tiers','id_tiers','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_competence','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_dossier','interlocuteur_id','dims_mod_business_interlocuteur','id',0),('dims_mod_business_tiers_dossier','dossier_id','dims_mod_business_dossier','id',0),('dims_mod_business_tiers_dossier','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_equipement','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_interlocuteur','tiers_id','dims_mod_business_tiers','id',0),('dims_mod_business_tiers_interlocuteur','interlocuteur_id','dims_mod_business_interlocuteur','id',0),('dims_mod_business_tiers_interlocuteur','id_module','dims_module','id',0),('dims_mod_business_tiers_interlocuteur','id_group','dims_group','id',0),('dims_mod_business_tiers_interlocuteur','id_user','dims_user','id',0),('dims_mod_business_versement','suivi_id','dims_mod_business_suivi','id',0),('dims_mod_business_tiers_interlocuteur','id_user','dims_user','id',14),('dims_mod_business_tiers_interlocuteur','id_group','dims_group','id',14),('dims_mod_business_tiers_interlocuteur','id_module','dims_module','id',14),('dims_mod_business_tiers_interlocuteur','interlocuteur_id','dims_mod_business_interlocuteur','id',14),('dims_mod_business_tiers_interlocuteur','tiers_id','dims_mod_business_tiers','id',14),('dims_mod_business_tiers_equipement','tiers_id','dims_mod_business_tiers','id',14),('dims_mod_business_tiers_dossier','dossier_id','dims_mod_business_dossier','id',14),('dims_mod_business_tiers_dossier','tiers_id','dims_mod_business_tiers','id',14),('dims_mod_business_tiers_dossier','interlocuteur_id','dims_mod_business_interlocuteur','id',14),('dims_mod_business_tiers_competence','tiers_id','dims_mod_business_tiers','id',14),('dims_mod_business_tiers','id_tiers','dims_mod_business_tiers','id',14),('dims_mod_business_tiers','id_module','dims_module','id',14),('dims_mod_business_tiers','id_user','dims_user','id',14),('dims_mod_business_tiers','id_group','dims_group','id',14),('dims_mod_business_suivi_detail','suivi_id','dims_mod_business_suivi','id',14),('dims_mod_business_suivi','id_user','dims_user','id',14),('dims_mod_business_suivi','id_group','dims_group','id',14),('dims_mod_business_suivi','id_module','dims_module','id',14),('dims_mod_business_suivi','tiers_id','dims_mod_business_tiers','id',14),('dims_mod_business_produit','id_module','dims_module','id',14),('dims_mod_business_suivi','dossier_id','dims_mod_business_dossier','id',14),('dims_mod_business_produit','id_group','dims_group','id',14),('dims_mod_business_interlocuteur_categorie','id_interlocuteur','dims_mod_business_interlocuteur','id',14),('dims_mod_business_produit','id_user','dims_user','id',14),('dims_mod_business_interlocuteur','id_group','dims_group','id',14),('dims_mod_business_interlocuteur','id_module','dims_module','id',14),('dims_mod_business_interlocuteur','id_user','dims_user','id',14),('dims_mod_business_enum','id_enum','dims_mod_business_enum','id',14),('dims_mod_business_dossier','id_group','dims_group','id',14),('dims_mod_business_dossier','id_user','dims_user','id',14),('dims_mod_business_dossier','id_module','dims_module','id',14),('dims_mod_business_action_utilisateur','action_id','dims_mod_business_action','id',14),('dims_mod_business_action_utilisateur','user_id','dims_mod_business_user','id',14),('dims_mod_business_action_detail','id_module','dims_module','id',14),('dims_mod_business_action_detail','id_user','dims_user','id',14),('dims_mod_business_action_detail','id_group','dims_group','id',14),('dims_mod_business_action_detail','dossier_id','dims_mod_business_dossier','id',14),('dims_mod_business_action_detail','interlocuteur_id','dims_mod_business_interlocuteur','id',14),('dims_mod_business_action_detail','tiers_id','dims_mod_business_tiers','id',14),('dims_mod_business_action_detail','action_id','dims_mod_business_action','id',14),('dims_mod_business_action','interloc_id','dims_mod_business_interloc','id',14),('dims_mod_business_action','id_group','dims_group','id',14),('dims_mod_business_action','id_user','dims_user','id',14),('dims_mod_business_action','id_module','dims_module','id',14),('dims_mod_business_action','tiers_id','dims_mod_business_tiers','id',14),('dims_mod_business_action','dossier_id','dims_mod_business_dossier','id',14),('dims_mod_business_versement','suivi_id','dims_mod_business_suivi','id',14);
/*!40000 ALTER TABLE `dims_mb_relation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mb_schema`
--

DROP TABLE IF EXISTS `dims_mb_schema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mb_schema` (
  `tablesrc` varchar(100) NOT NULL DEFAULT '',
  `tabledest` varchar(100) NOT NULL DEFAULT '',
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tabledest`,`tablesrc`,`id_module_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mb_schema`
--

LOCK TABLES `dims_mb_schema` WRITE;
/*!40000 ALTER TABLE `dims_mb_schema` DISABLE KEYS */;
INSERT INTO `dims_mb_schema` VALUES ('dims_role_action','dims_action',1),('dims_user_action_log','dims_action',1),('dims_group_user','dims_group',1),('dims_group_user_role','dims_group',1),('dims_homepage_line','dims_group',1),('dims_module','dims_group',1),('dims_module_group','dims_group',1),('dims_mod_business_action','dims_group',0),('dims_mod_business_action','dims_group',14),('dims_mod_business_action_detail','dims_group',0),('dims_mod_business_action_detail','dims_group',14),('dims_mod_business_dossier','dims_group',0),('dims_mod_business_dossier','dims_group',14),('dims_mod_business_interlocuteur','dims_group',0),('dims_mod_business_interlocuteur','dims_group',14),('dims_mod_business_produit','dims_group',0),('dims_mod_business_produit','dims_group',14),('dims_mod_business_suivi','dims_group',0),('dims_mod_business_suivi','dims_group',14),('dims_mod_business_tiers','dims_group',0),('dims_mod_business_tiers','dims_group',14),('dims_mod_business_tiers_interlocuteur','dims_group',0),('dims_mod_business_tiers_interlocuteur','dims_group',14),('dims_mod_directory_contact','dims_group',7),('dims_mod_docfolder','dims_group',3),('dims_mod_forms_reply','dims_group',4),('dims_mod_news','dims_group',8),('dims_mod_newscat','dims_group',8),('dims_mod_rsscat','dims_group',6),('dims_mod_rssfeed','dims_group',6),('dims_mod_rssrequest','dims_group',6),('dims_param_group','dims_group',1),('dims_profile','dims_group',1),('dims_role','dims_group',1),('dims_rule','dims_group',1),('dims_user_filter_rules','dims_group',1),('dims_user','dims_ldap',1),('dims_homepage_column','dims_line',1),('dims_homepage_column','dims_module',1),('dims_module_group','dims_module',1),('dims_mod_business_action','dims_module',0),('dims_mod_business_action','dims_module',14),('dims_mod_business_action_detail','dims_module',0),('dims_mod_business_action_detail','dims_module',14),('dims_mod_business_dossier','dims_module',0),('dims_mod_business_dossier','dims_module',14),('dims_mod_business_interlocuteur','dims_module',0),('dims_mod_business_interlocuteur','dims_module',14),('dims_mod_business_produit','dims_module',0),('dims_mod_business_produit','dims_module',14),('dims_mod_business_suivi','dims_module',0),('dims_mod_business_suivi','dims_module',14),('dims_mod_business_tiers','dims_module',0),('dims_mod_business_tiers','dims_module',14),('dims_mod_business_tiers_interlocuteur','dims_module',0),('dims_mod_business_tiers_interlocuteur','dims_module',14),('dims_mod_directory_contact','dims_module',7),('dims_mod_docfolder','dims_module',3),('dims_mod_doc_file','dims_module',3),('dims_mod_forms_reply','dims_module',4),('dims_mod_news','dims_module',8),('dims_mod_newscat','dims_module',8),('dims_mod_newsparam','dims_module',8),('dims_mod_rsscat','dims_module',6),('dims_mod_rssfeed','dims_module',6),('dims_mod_rssrequest','dims_module',6),('dims_param_default','dims_module',1),('dims_param_group','dims_module',1),('dims_param_user','dims_module',1),('dims_role','dims_module',1),('dims_user_action_log','dims_module',1),('dims_action','dims_module_type',1),('dims_cms_object','dims_module_type',1),('dims_mb_field','dims_module_type',1),('dims_mb_relation','dims_module_type',1),('dims_mb_schema','dims_module_type',1),('dims_mb_table','dims_module_type',1),('dims_module','dims_module_type',1),('dims_param_type','dims_module_type',1),('dims_role_action','dims_module_type',1),('dims_user_action_log','dims_module_type',1),('dims_mod_business_action_detail','dims_mod_business_action',0),('dims_mod_business_action_detail','dims_mod_business_action',14),('dims_mod_business_action_utilisateur','dims_mod_business_action',0),('dims_mod_business_action_utilisateur','dims_mod_business_action',14),('dims_mod_business_action','dims_mod_business_dossier',0),('dims_mod_business_action','dims_mod_business_dossier',14),('dims_mod_business_action_detail','dims_mod_business_dossier',0),('dims_mod_business_action_detail','dims_mod_business_dossier',14),('dims_mod_business_suivi','dims_mod_business_dossier',0),('dims_mod_business_suivi','dims_mod_business_dossier',14),('dims_mod_business_tiers_dossier','dims_mod_business_dossier',0),('dims_mod_business_tiers_dossier','dims_mod_business_dossier',14),('dims_mod_business_enum','dims_mod_business_enum',0),('dims_mod_business_enum','dims_mod_business_enum',14),('dims_mod_business_action','dims_mod_business_interloc',0),('dims_mod_business_action','dims_mod_business_interloc',14),('dims_mod_business_action_detail','dims_mod_business_interlocuteur',0),('dims_mod_business_action_detail','dims_mod_business_interlocuteur',14),('dims_mod_business_interlocuteur_categorie','dims_mod_business_interlocuteur',0),('dims_mod_business_interlocuteur_categorie','dims_mod_business_interlocuteur',14),('dims_mod_business_tiers_dossier','dims_mod_business_interlocuteur',0),('dims_mod_business_tiers_dossier','dims_mod_business_interlocuteur',14),('dims_mod_business_tiers_interlocuteur','dims_mod_business_interlocuteur',0),('dims_mod_business_tiers_interlocuteur','dims_mod_business_interlocuteur',14),('dims_mod_business_suivi_detail','dims_mod_business_suivi',0),('dims_mod_business_suivi_detail','dims_mod_business_suivi',14),('dims_mod_business_versement','dims_mod_business_suivi',0),('dims_mod_business_versement','dims_mod_business_suivi',14),('dims_mod_business_action','dims_mod_business_tiers',0),('dims_mod_business_action','dims_mod_business_tiers',14),('dims_mod_business_action_detail','dims_mod_business_tiers',0),('dims_mod_business_action_detail','dims_mod_business_tiers',14),('dims_mod_business_suivi','dims_mod_business_tiers',0),('dims_mod_business_suivi','dims_mod_business_tiers',14),('dims_mod_business_tiers','dims_mod_business_tiers',0),('dims_mod_business_tiers','dims_mod_business_tiers',14),('dims_mod_business_tiers_competence','dims_mod_business_tiers',0),('dims_mod_business_tiers_competence','dims_mod_business_tiers',14),('dims_mod_business_tiers_dossier','dims_mod_business_tiers',0),('dims_mod_business_tiers_dossier','dims_mod_business_tiers',14),('dims_mod_business_tiers_equipement','dims_mod_business_tiers',0),('dims_mod_business_tiers_equipement','dims_mod_business_tiers',14),('dims_mod_business_tiers_interlocuteur','dims_mod_business_tiers',0),('dims_mod_business_tiers_interlocuteur','dims_mod_business_tiers',14),('dims_mod_business_action_utilisateur','dims_mod_business_user',0),('dims_mod_business_action_utilisateur','dims_mod_business_user',14),('dims_mod_directory_favorites','dims_mod_directory_contact',7),('dims_mod_doc_param','dims_mod_docext',3),('dims_mod_doc_file','dims_mod_docfolder',3),('dims_mod_docext','dims_mod_doctype',3),('dims_mod_forms_field','dims_mod_forms',4),('dims_mod_forms_reply','dims_mod_forms',4),('dims_mod_forms_reply_field','dims_mod_forms',4),('dims_mod_forms_reply_field','dims_mod_forms_field',4),('dims_mod_forms_reply_field','dims_mod_forms_reply',4),('dims_mod_news','dims_mod_newscat',8),('dims_mod_newscat','dims_mod_newsuser_create',8),('dims_mod_newscat','dims_mod_newsuser_modify',8),('dims_mod_rssfeed','dims_mod_rsscat',6),('dims_mod_rssrequest','dims_mod_rsscat',6),('dims_mod_rsscache','dims_mod_rssfeed',6),('dims_mod_news','dims_newscat',1),('dims_param_choice','dims_param_type',1),('dims_param_default','dims_param_type',1),('dims_param_group','dims_param_type',1),('dims_param_user','dims_param_type',1),('dims_group_user','dims_profile',1),('dims_role_profile','dims_profile',1),('dims_rule','dims_profile',1),('dims_user_action_log','dims_record',1),('dims_group_user_role','dims_role',1),('dims_role_action','dims_role',1),('dims_role_profile','dims_role',1),('dims_rule','dims_type',1),('dims_user','dims_type',1),('dims_user_type_fields','dims_type',1),('dims_group_user','dims_user',1),('dims_group_user_role','dims_user',1),('dims_homepage_line','dims_user',1),('dims_mod_business_action','dims_user',0),('dims_mod_business_action','dims_user',14),('dims_mod_business_action_detail','dims_user',0),('dims_mod_business_action_detail','dims_user',14),('dims_mod_business_dossier','dims_user',0),('dims_mod_business_dossier','dims_user',14),('dims_mod_business_interlocuteur','dims_user',0),('dims_mod_business_interlocuteur','dims_user',14),('dims_mod_business_produit','dims_user',0),('dims_mod_business_produit','dims_user',14),('dims_mod_business_suivi','dims_user',0),('dims_mod_business_suivi','dims_user',14),('dims_mod_business_tiers','dims_user',0),('dims_mod_business_tiers','dims_user',14),('dims_mod_business_tiers_interlocuteur','dims_user',0),('dims_mod_business_tiers_interlocuteur','dims_user',14),('dims_mod_directory_contact','dims_user',7),('dims_mod_directory_favorites','dims_user',7),('dims_mod_docfolder','dims_user',3),('dims_mod_doc_file','dims_user',3),('dims_mod_forms_reply','dims_user',4),('dims_mod_news','dims_user',8),('dims_mod_rsscat','dims_user',6),('dims_mod_rssfeed','dims_user',6),('dims_mod_rssrequest','dims_user',6),('dims_param_user','dims_user',1),('dims_user_action_log','dims_user',1),('dims_mod_doc_file','dims_workspace',3);
/*!40000 ALTER TABLE `dims_mb_schema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mb_table`
--

DROP TABLE IF EXISTS `dims_mb_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mb_table` (
  `name` varchar(100) NOT NULL DEFAULT '',
  `label` varchar(255) DEFAULT NULL,
  `visible` tinyint(1) unsigned DEFAULT '1',
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0',
  `db` varchar(200) DEFAULT NULL,
  `sql` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`name`,`id_module_type`),
  UNIQUE KEY `nom` (`name`),
  KEY `nom_2` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mb_table`
--

LOCK TABLES `dims_mb_table` WRITE;
/*!40000 ALTER TABLE `dims_mb_table` DISABLE KEYS */;
INSERT INTO `dims_mb_table` VALUES ('dims_user','dims_user',1,1,'',''),('dims_rule_type','dims_rule_type',0,1,'',''),('dims_rule','dims_rule',0,1,'',''),('dims_action','dims_action',0,1,'',''),('dims_role_profile','dims_role_profile',0,1,'',''),('dims_cms_object','dims_cms_object',0,1,'',''),('dims_group','dims_group',1,1,'',''),('dims_connecteduser','dims_connecteduser',0,1,'',''),('dims_group_user','dims_group_user',0,1,'',''),('dims_group_user_role','dims_group_user_role',0,1,'',''),('dims_homepage_column','dims_homepage_column',0,1,'',''),('dims_homepage_line','dims_homepage_line',0,1,'',''),('dims_log','dims_log',0,1,'',''),('dims_mb_field','dims_mb_field',0,1,'',''),('dims_mb_relation','dims_mb_relation',0,1,'',''),('dims_mb_schema','dims_mb_schema',0,1,'',''),('dims_module','dims_module',0,1,'',''),('dims_mb_table','dims_mb_table',0,1,'',''),('dims_module_group','dims_module_group',0,1,'',''),('dims_module_type','dims_module_type',0,1,'',''),('dims_param_choice','dims_param_choice',0,1,'',''),('dims_param_default','dims_param_default',0,1,'',''),('dims_param_group','dims_param_group',0,1,'',''),('dims_param_type','dims_param_type',0,1,'',''),('dims_param_user','dims_param_user',0,1,'',''),('dims_profile','dims_profile',0,1,'',''),('dims_role','dims_role',0,1,'',''),('dims_role_action','dims_role_action',0,1,'',''),('dims_user_action_log','dims_user_action_log',0,1,'',''),('dims_user_filter_rules','dims_user_filter_rules',0,1,'',''),('dims_user_type','dims_user_type',0,1,'',''),('dims_user_type_fields','dims_user_type_fields',0,1,'',''),('dims_mod_doctype','type',1,3,'',''),('dims_mod_docfolder','folder',1,3,'',''),('dims_mod_doc_param','_param',1,3,'',''),('dims_mod_forms_reply_field','reply_field',1,4,'',''),('dims_mod_forms_reply','reply',1,4,'',''),('dims_mod_rssrequest','request',1,6,'',''),('dims_mod_rssfeed','feed',1,6,'',''),('dims_mod_directory_contact','Annuaire / Contacts',1,7,'',''),('dims_mod_directory_favorites','Annuaire / Favoris',1,7,'',''),('dims_mod_news','news',1,8,'',''),('dims_mod_newscat','cat',1,8,'',''),('dims_mod_newsparam','param',1,8,'',''),('dims_mod_wce_heading','ext',1,2,'',''),('dims_mod_wce_article','',1,2,'',''),('dims_mod_forms_field','field',1,4,'',''),('dims_mod_forms','forms',1,4,'',''),('dims_mod_docext','ext',1,3,'',''),('dims_mod_doc_file','',1,3,'',''),('dims_mod_rsscat','cat',1,6,'',''),('dims_mod_rsscache','cache',1,6,'',''),('dims_mod_ww_page','ext',1,9,'',''),('dims_mod_ww_inbox','_param',1,9,'',''),('dims_mod_ww_site','',1,9,'',''),('dims_mod_ww_email','folder',1,9,'',''),('dims_mod_agenda_organizer','organizer',1,5,'',''),('dims_mod_agenda','agenda',1,5,'',''),('dims_mod_agenda_cat','cat',1,5,'',''),('dims_mod_business_suivi','suivi',1,14,'',''),('dims_mod_business_produit','produit',1,14,'',''),('dims_mod_business_params','params',1,14,'',''),('dims_mod_business_interlocuteur_categorie','interlocuteur_categorie',1,14,'',''),('dims_mod_business_interlocuteur','interlocuteur',1,14,'',''),('dims_mod_business_equipement','equipement',1,14,'',''),('dims_mod_business_enum_liens','enum_liens',1,14,'',''),('dims_mod_business_enum','enum',1,14,'',''),('dims_mod_business_dossier','dossier',1,14,'',''),('dims_mod_business_competence','competence',1,14,'',''),('dims_mod_business_action_utilisateur','action_utilisateur',1,14,'',''),('dims_mod_business_action_detail','action_detail',1,14,'',''),('dims_mod_business_action','action',1,14,'',''),('dims_mod_business_suivi_detail','suivi_detail',1,14,'',''),('dims_mod_business_tiers','tiers',1,14,'',''),('dims_mod_business_tiers_competence','tiers_competence',1,14,'',''),('dims_mod_business_tiers_dossier','tiers_dossier',1,14,'',''),('dims_mod_business_tiers_equipement','tiers_equipement',1,14,'',''),('dims_mod_business_tiers_interlocuteur','tiers_interlocuteur',1,14,'',''),('dims_mod_business_versement','versement',1,14,'',''),('dims_mod_faq_question','Questions',1,17,'',''),('dims_mod_faq_reponse','Reponses',1,17,'',''),('dims_mod_business_contact','dims_mod_business_contact',1,1,'','');
/*!40000 ALTER TABLE `dims_mb_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mb_wce_object`
--

DROP TABLE IF EXISTS `dims_mb_wce_object`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mb_wce_object` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `id_module_type` int(10) DEFAULT NULL,
  `script` varchar(255) DEFAULT NULL,
  `select_id` varchar(64) DEFAULT NULL,
  `select_label` varchar(64) DEFAULT NULL,
  `select_table` varchar(64) DEFAULT NULL,
  `select_params` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mb_wce_object`
--

LOCK TABLES `dims_mb_wce_object` WRITE;
/*!40000 ALTER TABLE `dims_mb_wce_object` DISABLE KEYS */;
INSERT INTO `dims_mb_wce_object` VALUES (17,'Affichage Formulaire',4,'?op=\'display\'','id','label','dims_mod_forms',''),(47,'Affichage Calendrier',5,'?op=\'display_calendar\'',NULL,NULL,NULL,''),(37,'Request',6,'?op=\'request\'','id','request','dims_mod_rssrequest',''),(7,'Affichage des News',8,'?op=\'display\'',NULL,NULL,NULL,''),(36,'Flux',6,'?op=\'display\'','id','title','dims_mod_rssfeed',''),(49,'Recherche',5,'?op=\'display_search\'',NULL,NULL,NULL,''),(48,'Affichage Liste',5,'?op=\'display_list\'',NULL,NULL,NULL,''),(50,'Affichage Galerie',3,'?op=\'display_gallery\'','id','name','dims_mod_doc_gallery',''),(51,'Affichage dossier',3,'?op=\'browse\'','id','name','dims_mod_doc_folder','foldertype=\'public\''),(59,'faq',17,'?op=\'faq\'',NULL,NULL,NULL,NULL),(60,'events',1,'?op=\'event\'',NULL,NULL,NULL,NULL),(61,'newsletter',1,'?op=\'newsletter\'',NULL,NULL,NULL,NULL),(62,'Affichage Articles',2,'?op=\'display_object\'','id','label','dims_mod_wce_object',NULL),(63,'events',18,'?op=\'event\'',NULL,NULL,NULL,NULL),(64,'Elearning',19,'?op=\'display\'',NULL,NULL,NULL,NULL),(65,'Fairs',18,'?op=\'fairs\'',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `dims_mb_wce_object` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_milestone`
--

DROP TABLE IF EXISTS `dims_milestone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_milestone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(100) DEFAULT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `description` text,
  `date` varchar(10) DEFAULT NULL,
  `id_project` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_project` (`id_project`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_milestone`
--

LOCK TABLES `dims_milestone` WRITE;
/*!40000 ALTER TABLE `dims_milestone` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_milestone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_agenda`
--

DROP TABLE IF EXISTS `dims_mod_agenda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_agenda` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `description` text,
  `speaker` varchar(255) DEFAULT NULL,
  `date_start` bigint(14) unsigned NOT NULL DEFAULT '0',
  `date_end` bigint(14) unsigned NOT NULL DEFAULT '0',
  `location` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `id_cat` int(10) unsigned NOT NULL DEFAULT '0',
  `id_organizer` int(10) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_agenda`
--

LOCK TABLES `dims_mod_agenda` WRITE;
/*!40000 ALTER TABLE `dims_mod_agenda` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_agenda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_agenda_cat`
--

DROP TABLE IF EXISTS `dims_mod_agenda_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_agenda_cat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_agenda_cat`
--

LOCK TABLES `dims_mod_agenda_cat` WRITE;
/*!40000 ALTER TABLE `dims_mod_agenda_cat` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_agenda_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_agenda_organizer`
--

DROP TABLE IF EXISTS `dims_mod_agenda_organizer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_agenda_organizer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_agenda_organizer`
--

LOCK TABLES `dims_mod_agenda_organizer` WRITE;
/*!40000 ALTER TABLE `dims_mod_agenda_organizer` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_agenda_organizer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_article`
--

DROP TABLE IF EXISTS `dims_mod_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` longtext,
  `author` longtext,
  `version` varchar(16) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT '0',
  `timestp` varchar(14) DEFAULT NULL,
  `id_heading` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `isdefault` tinyint(1) unsigned DEFAULT '0',
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `nbclicks` int(10) unsigned DEFAULT '0',
  `note` int(10) unsigned DEFAULT '0',
  `nbvotes` int(10) unsigned DEFAULT '0',
  `hot` tinyint(1) unsigned DEFAULT '0',
  `language` varchar(32) DEFAULT NULL,
  `status` varchar(16) DEFAULT NULL,
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_article`
--

LOCK TABLES `dims_mod_article` WRITE;
/*!40000 ALTER TABLE `dims_mod_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_article_draft`
--

DROP TABLE IF EXISTS `dims_mod_article_draft`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_article_draft` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` longtext,
  `author` longtext,
  `version` varchar(16) DEFAULT NULL,
  `timestp` varchar(14) DEFAULT NULL,
  `id_heading` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_article_draft`
--

LOCK TABLES `dims_mod_article_draft` WRITE;
/*!40000 ALTER TABLE `dims_mod_article_draft` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_article_draft` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_article_heading`
--

DROP TABLE IF EXISTS `dims_mod_article_heading`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_article_heading` (
  `id` tinyint(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `template` varchar(255) DEFAULT NULL,
  `id_heading` int(10) unsigned NOT NULL DEFAULT '0',
  `parents` varchar(255) DEFAULT NULL,
  `depth` int(10) unsigned NOT NULL DEFAULT '0',
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(11) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_article_heading`
--

LOCK TABLES `dims_mod_article_heading` WRITE;
/*!40000 ALTER TABLE `dims_mod_article_heading` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_article_heading` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_action`
--

DROP TABLE IF EXISTS `dims_mod_business_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) NOT NULL DEFAULT '0',
  `id_task` int(11) DEFAULT NULL,
  `dossier_id` int(10) unsigned NOT NULL DEFAULT '0',
  `tiers_id` int(10) unsigned DEFAULT '0',
  `typeaction` varchar(255) DEFAULT NULL,
  `acteur` int(10) unsigned NOT NULL DEFAULT '0',
  `libelle` varchar(255) DEFAULT NULL,
  `description` longtext,
  `conges` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `personnel` tinyint(1) unsigned DEFAULT '0',
  `interne` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `temps_prevu` int(5) unsigned DEFAULT NULL,
  `temps_passe` int(5) unsigned DEFAULT NULL,
  `km` int(10) unsigned DEFAULT '0',
  `cout` int(10) unsigned NOT NULL DEFAULT '0',
  `temps_duplique` varchar(3) DEFAULT NULL,
  `datejour` date DEFAULT '0000-00-00',
  `datefin` date DEFAULT '0000-00-00',
  `heuredeb` time DEFAULT NULL,
  `heurefin` time DEFAULT NULL,
  `avancement` int(10) unsigned NOT NULL DEFAULT '0',
  `priorite` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT NULL,
  `id_user` int(10) unsigned DEFAULT NULL,
  `id_workspace` int(10) unsigned DEFAULT NULL,
  `interloc_id` int(10) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timestamp_release` bigint(14) unsigned DEFAULT '0',
  `supportrelease` int(10) unsigned DEFAULT '0',
  `rub_nl` int(10) unsigned DEFAULT '0',
  `allow_fo` tinyint(1) DEFAULT NULL,
  `target` varchar(255) DEFAULT NULL,
  `teaser` text,
  `lieu` varchar(255) DEFAULT NULL,
  `prix` int(11) DEFAULT '0',
  `conditions` varchar(255) DEFAULT NULL,
  `niveau` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `close` tinyint(1) NOT NULL DEFAULT '0',
  `timestp_open` bigint(20) unsigned NOT NULL DEFAULT '0',
  `id_organizer` int(10) unsigned DEFAULT '0',
  `id_responsible` int(10) unsigned DEFAULT '0',
  `banner_path` text,
  `preview_path` text,
  `matchmaking_path` text,
  `datefin_insc` varchar(32) DEFAULT NULL,
  `language` varchar(100) DEFAULT NULL,
  `alert_modif` tinyint(1) DEFAULT NULL,
  `is_model` int(11) DEFAULT '0',
  `booking` text,
  `participations` text,
  `display_hp` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_action`
--

LOCK TABLES `dims_mod_business_action` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_action_detail`
--

DROP TABLE IF EXISTS `dims_mod_business_action_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_action_detail` (
  `action_id` int(10) unsigned NOT NULL DEFAULT '0',
  `tiers_id` int(10) unsigned NOT NULL DEFAULT '0',
  `contact_id` int(10) unsigned NOT NULL DEFAULT '0',
  `dossier_id` int(10) unsigned NOT NULL DEFAULT '0',
  `duree` int(5) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT NULL,
  `id_user` int(10) unsigned DEFAULT NULL,
  `id_workspace` int(10) unsigned DEFAULT NULL,
  `participate` tinyint(4) NOT NULL DEFAULT '1',
  KEY `action_id` (`action_id`),
  KEY `tiers_id` (`tiers_id`),
  KEY `interlocuteur_id` (`contact_id`),
  KEY `dossier_id` (`dossier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_action_detail`
--

LOCK TABLES `dims_mod_business_action_detail` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_action_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_action_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_action_resp`
--

DROP TABLE IF EXISTS `dims_mod_business_action_resp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_action_resp` (
  `id_action` int(11) NOT NULL,
  `id_object` int(11) NOT NULL,
  `id_record` int(11) NOT NULL,
  PRIMARY KEY (`id_action`,`id_object`,`id_record`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_action_resp`
--

LOCK TABLES `dims_mod_business_action_resp` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_action_resp` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_action_resp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_action_utilisateur`
--

DROP TABLE IF EXISTS `dims_mod_business_action_utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_action_utilisateur` (
  `user_id` int(10) unsigned DEFAULT '0',
  `action_id` int(10) unsigned DEFAULT '0',
  `resp` varchar(10) DEFAULT NULL,
  `participate` tinyint(4) NOT NULL DEFAULT '1',
  KEY `user_id` (`user_id`),
  KEY `action_id` (`action_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_action_utilisateur`
--

LOCK TABLES `dims_mod_business_action_utilisateur` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_action_utilisateur` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_action_utilisateur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_activity_contact`
--

DROP TABLE IF EXISTS `dims_mod_business_activity_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_activity_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `date_modify` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_activity_contact`
--

LOCK TABLES `dims_mod_business_activity_contact` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_activity_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_activity_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_commentaire`
--

DROP TABLE IF EXISTS `dims_mod_business_commentaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_commentaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) NOT NULL,
  `id_object` int(11) NOT NULL,
  `commentaire` text,
  `com_level` int(1) NOT NULL COMMENT '1: generique; 2: metier; 3:perso',
  `date_create` varchar(14) DEFAULT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_user_ct` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_commentaire`
--

LOCK TABLES `dims_mod_business_commentaire` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_commentaire` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_commentaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_competence`
--

DROP TABLE IF EXISTS `dims_mod_business_competence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_competence` (
  `code` varchar(16) DEFAULT NULL,
  `intitule` varchar(255) DEFAULT NULL,
  `intitule_allemand` varchar(255) DEFAULT NULL,
  KEY `CODE_COMPETENCE` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_competence`
--

LOCK TABLES `dims_mod_business_competence` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_competence` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_competence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_contact`
--

DROP TABLE IF EXISTS `dims_mod_business_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(16) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `civilite` varchar(10) DEFAULT NULL,
  `sexe` varchar(10) DEFAULT NULL,
  `nationalite` varchar(25) DEFAULT NULL,
  `lux` tinyint(1) NOT NULL,
  `lux_comm` text,
  `photo` varchar(14) DEFAULT NULL,
  `pers_phone` varchar(20) DEFAULT NULL,
  `vip` varchar(20) DEFAULT NULL,
  `address` text,
  `postalcode` varchar(16) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `fax` varchar(32) DEFAULT NULL,
  `mobile` varchar(32) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `comments` longtext,
  `country` varchar(100) DEFAULT NULL,
  `family` text,
  `professional` text,
  `birthday` bigint(14) NOT NULL,
  `children` tinyint(10) NOT NULL,
  `partenaire` tinyint(1) NOT NULL,
  `date_create` bigint(14) DEFAULT NULL,
  `id_user_create` int(11) NOT NULL,
  `timestp_modify` bigint(14) DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT NULL,
  `id_user` int(10) unsigned DEFAULT NULL,
  `id_workspace` int(10) unsigned DEFAULT NULL,
  `inactif` tinyint(1) NOT NULL DEFAULT '0',
  `phone2` varchar(32) DEFAULT NULL,
  `email2` varchar(150) DEFAULT NULL,
  `email3` varchar(150) DEFAULT NULL,
  `field1` varchar(255) DEFAULT NULL,
  `field2` varchar(255) DEFAULT NULL,
  `field3` varchar(255) DEFAULT NULL,
  `field4` varchar(255) DEFAULT NULL,
  `field5` varchar(255) DEFAULT NULL,
  `field6` varchar(255) DEFAULT NULL,
  `field7` varchar(255) DEFAULT NULL,
  `field8` varchar(255) DEFAULT NULL,
  `field9` varchar(255) DEFAULT NULL,
  `field10` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_contact`
--

LOCK TABLES `dims_mod_business_contact` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_contact_field`
--

DROP TABLE IF EXISTS `dims_mod_business_contact_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_contact_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) NOT NULL,
  `id_metafield` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `value` text,
  `timestp_modify` bigint(14) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `lastmodify` tinyint(1) NOT NULL DEFAULT '0',
  `id_lang` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_contact` (`id_contact`),
  KEY `id_contact_2` (`id_contact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_contact_field`
--

LOCK TABLES `dims_mod_business_contact_field` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_contact_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_contact_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_contact_group`
--

DROP TABLE IF EXISTS `dims_mod_business_contact_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_contact_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(150) DEFAULT NULL,
  `view` int(1) NOT NULL,
  `date_create` varchar(14) DEFAULT NULL,
  `id_user_create` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_contact_group`
--

LOCK TABLES `dims_mod_business_contact_group` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_contact_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_contact_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_contact_group_link`
--

DROP TABLE IF EXISTS `dims_mod_business_contact_group_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_contact_group_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) NOT NULL,
  `id_group_ct` int(11) NOT NULL,
  `date_create` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_contact_group_link`
--

LOCK TABLES `dims_mod_business_contact_group_link` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_contact_group_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_contact_group_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_contact_import`
--

DROP TABLE IF EXISTS `dims_mod_business_contact_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_contact_import` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `company` varchar(150) DEFAULT NULL,
  `jobtitle` varchar(50) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `email2` varchar(200) DEFAULT NULL,
  `email3` varchar(200) DEFAULT NULL,
  `titre` varchar(10) DEFAULT NULL,
  `comment` text,
  `exist` varchar(15) DEFAULT NULL,
  `exist_ent` varchar(15) DEFAULT NULL,
  `id_user_create` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_importer` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_importer` (`id_importer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_contact_import`
--

LOCK TABLES `dims_mod_business_contact_import` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_contact_import` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_contact_import` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_contact_import_ent_similar`
--

DROP TABLE IF EXISTS `dims_mod_business_contact_import_ent_similar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_contact_import_ent_similar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) NOT NULL,
  `ent_intitule` varchar(255) DEFAULT NULL,
  `id_ent_similar` int(11) NOT NULL,
  `intitule_ent_similar` varchar(255) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_contact_import_ent_similar`
--

LOCK TABLES `dims_mod_business_contact_import_ent_similar` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_contact_import_ent_similar` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_contact_import_ent_similar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_contact_incomplete`
--

DROP TABLE IF EXISTS `dims_mod_business_contact_incomplete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_contact_incomplete` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `timestp` varchar(14) DEFAULT NULL,
  `id_workspace` int(10) NOT NULL,
  `nb_incomplete` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_contact_incomplete`
--

LOCK TABLES `dims_mod_business_contact_incomplete` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_contact_incomplete` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_contact_incomplete` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_contact_layer`
--

DROP TABLE IF EXISTS `dims_mod_business_contact_layer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_contact_layer` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `type_layer` tinyint(4) NOT NULL,
  `id_layer` int(11) NOT NULL DEFAULT '0',
  `type` varchar(16) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `civilite` varchar(10) DEFAULT NULL,
  `sexe` varchar(10) DEFAULT NULL,
  `nationalite` varchar(25) DEFAULT NULL,
  `lux` tinyint(1) DEFAULT NULL,
  `lux_comm` text,
  `photo` varchar(14) DEFAULT NULL,
  `pers_phone` varchar(20) DEFAULT NULL,
  `vip` varchar(20) DEFAULT NULL,
  `address` text,
  `postalcode` varchar(16) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `fax` varchar(32) DEFAULT NULL,
  `mobile` varchar(32) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `comments` longtext,
  `country` varchar(100) DEFAULT NULL,
  `family` text,
  `professional` text,
  `birthday` bigint(14) DEFAULT NULL,
  `children` tinyint(10) DEFAULT NULL,
  `partenaire` tinyint(1) DEFAULT NULL,
  `date_create` bigint(14) DEFAULT NULL,
  `id_user_create` int(11) NOT NULL,
  `timestp_modify` bigint(14) DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT NULL,
  `id_user` int(10) unsigned DEFAULT NULL,
  `id_workspace` int(10) unsigned DEFAULT NULL,
  `inactif` tinyint(1) NOT NULL DEFAULT '0',
  `phone2` varchar(32) DEFAULT NULL,
  `email2` varchar(150) DEFAULT NULL,
  `email3` varchar(150) DEFAULT NULL,
  `field1` varchar(255) DEFAULT NULL,
  `field2` varchar(255) DEFAULT NULL,
  `field3` varchar(255) DEFAULT NULL,
  `field4` varchar(255) DEFAULT NULL,
  `field5` varchar(255) DEFAULT NULL,
  `field6` varchar(255) DEFAULT NULL,
  `field7` varchar(255) DEFAULT NULL,
  `field8` varchar(255) DEFAULT NULL,
  `field9` varchar(255) DEFAULT NULL,
  `field10` varchar(255) DEFAULT NULL,
  `field11` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`type_layer`,`id_layer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_contact_layer`
--

LOCK TABLES `dims_mod_business_contact_layer` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_contact_layer` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_contact_layer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_contact_mail`
--

DROP TABLE IF EXISTS `dims_mod_business_contact_mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_contact_mail` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_email` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_contact_mail`
--

LOCK TABLES `dims_mod_business_contact_mail` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_contact_mail` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_contact_mail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_contact_mbfield`
--

DROP TABLE IF EXISTS `dims_mod_business_contact_mbfield`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_contact_mbfield` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) NOT NULL,
  `id_mbfield` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `value` text,
  `timestp_modify` bigint(14) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `lastmodify` tinyint(1) NOT NULL DEFAULT '0',
  `id_lang` int(11) NOT NULL,
  `type_layer` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_contact` (`id_contact`),
  KEY `id_contact_2` (`id_contact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_contact_mbfield`
--

LOCK TABLES `dims_mod_business_contact_mbfield` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_contact_mbfield` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_contact_mbfield` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_contact_profil`
--

DROP TABLE IF EXISTS `dims_mod_business_contact_profil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_contact_profil` (
  `id_contact` int(4) NOT NULL,
  `id_profil` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_contact_profil`
--

LOCK TABLES `dims_mod_business_contact_profil` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_contact_profil` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_contact_profil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_ct_link`
--

DROP TABLE IF EXISTS `dims_mod_business_ct_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_ct_link` (
  `id` bigint(14) NOT NULL AUTO_INCREMENT,
  `id_contact1` int(11) NOT NULL,
  `id_contact2` int(11) NOT NULL,
  `id_object` int(11) NOT NULL,
  `type_link` varchar(25) DEFAULT NULL,
  `link_level` int(1) NOT NULL DEFAULT '3' COMMENT '1 : generique, 2: metier, 3:personnel',
  `time_create` bigint(14) NOT NULL,
  `id_ct_user_create` int(11) NOT NULL,
  `date_deb` bigint(14) NOT NULL,
  `date_fin` bigint(14) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `commentaire` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_ct_link`
--

LOCK TABLES `dims_mod_business_ct_link` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_ct_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_ct_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_ct_watch`
--

DROP TABLE IF EXISTS `dims_mod_business_ct_watch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_ct_watch` (
  `id_user` int(11) NOT NULL,
  `id_personne` int(11) DEFAULT NULL,
  `id_tiers` int(11) DEFAULT NULL,
  `time_lastseen` bigint(14) NOT NULL,
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_ct_watch`
--

LOCK TABLES `dims_mod_business_ct_watch` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_ct_watch` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_ct_watch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_dossier`
--

DROP TABLE IF EXISTS `dims_mod_business_dossier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_dossier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `procedure` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `domaine_intervention` varchar(255) DEFAULT NULL,
  `objet_dossier` varchar(255) DEFAULT NULL,
  `objet_dossier_search` varchar(255) DEFAULT NULL,
  `date_debut` varchar(10) DEFAULT NULL,
  `date_fin` varchar(10) DEFAULT NULL,
  `avancement` varchar(255) DEFAULT NULL,
  `duree` int(10) unsigned DEFAULT '0',
  `termine` varchar(3) DEFAULT NULL,
  `commentaire` longtext,
  `commentaire_search` longtext,
  `etape` varchar(64) DEFAULT NULL,
  `id_module` int(10) unsigned DEFAULT NULL,
  `id_user` int(10) unsigned DEFAULT NULL,
  `id_workspace` int(10) unsigned DEFAULT NULL,
  `resp` varchar(10) DEFAULT NULL,
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_dossier`
--

LOCK TABLES `dims_mod_business_dossier` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_dossier` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_dossier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_enum`
--

DROP TABLE IF EXISTS `dims_mod_business_enum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_enum` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `id_enum` int(10) DEFAULT NULL,
  `value` varchar(100) DEFAULT NULL,
  `lang` int(11) NOT NULL DEFAULT '1',
  `code` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=3269 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_enum`
--

LOCK TABLES `dims_mod_business_enum` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_enum` DISABLE KEYS */;
INSERT INTO `dims_mod_business_enum` VALUES (2,'genre','Monsieur',0,'',1,''),(3,'genre','Madame',0,'',1,''),(4,'genre','Mademoiselle',0,'',1,''),(12,'statutjuridique','SARL',0,'',1,''),(13,'statutjuridique','SA',0,'',1,''),(14,'statutjuridique','EI',0,'',1,''),(15,'booleen','oui',0,'',1,''),(16,'booleen','non',0,'',1,''),(763,'typedossier','Projet',NULL,'',1,''),(764,'typesuivi','Devis',0,'',1,''),(22,'typeclient','Entreprise',0,'',1,''),(24,'typeclient','Institution',0,'',1,''),(787,'typeaction','_DIMS_MISSIONS',0,'#D04C2F',1,''),(41,'fonction','Responsable Commnication',0,'',1,''),(40,'fonction','GÃ©rant',0,'',1,''),(42,'typeaction','_DIMS_PLANNING_SALON',0,'#64644C',1,''),(765,'typesuivi','Facture',0,'',1,''),(766,'typesuivi','Avoir',0,'',1,''),(768,'categorie_interlocuteur','hÃ©bergement',0,'',1,''),(769,'categorie_interlocuteur','voeux',0,'',1,''),(770,'fonction','Responsable Informatique',0,'',1,''),(771,'fonction','Responsable Commercial',0,'',1,''),(61,'typeaction','_DIMS_PLANNING_CONFERENCE',0,'#F6D41A',1,''),(760,'statutjuridique','EURL',0,'',1,''),(761,'statutjuridique','I',0,'',1,''),(762,'typedossier','Affaire commerciale',0,'',1,''),(774,'typeaction','Foire',0,'#16A5F1',1,''),(775,'modele_suivi','modele.odt',0,'',1,''),(776,'modele_suivi','modele_graphique.odt',0,'',1,''),(782,'typeclient','Particulier',0,'',1,''),(783,'domaine_intervention','HÃ©bergement',0,'',1,''),(784,'domaine_intervention','Vente de MatÃ©riel',0,'',1,''),(785,'domaine_intervention','DÃ©veloppement d\'Applications',0,'',1,''),(786,'domaine_intervention','Formation',0,'',1,''),(788,'typeaction','_DIMS_PLANNING_RENCONTRE_ENT',0,'#1348C8',1,''),(789,'typeaction','_DIMS_PLANNING_RDV_INT',0,'#C1C991',1,''),(791,'typeaction_evt','_DIMS_PLANNING_FAIR',0,'',1,''),(792,'typeaction_evt','_DIMS_PLANNING_CONFERENCE',0,'',1,''),(796,'typeaction_evt','_DIMS_MISSIONS',0,'',1,''),(799,'typeaction_evt','_DIMS_EVENT_VISITS',0,'',1,''),(800,'typeaction_evt','_DIMS_PLANNING_SALON',0,'',1,''),(801,'typeaction_evt','_DIMS_EVENT_OPPORTUNITIES',0,'',1,''),(1927,'pays','Afghanistan',NULL,'',1,''),(1928,'pays','Afghanistan',NULL,'',2,''),(1929,'pays','Afrique du Sud',NULL,'',1,''),(1930,'pays','South Africa',NULL,'',2,''),(1931,'pays','Albanie',NULL,'',1,''),(1932,'pays','Albania',NULL,'',2,''),(1933,'pays','Alg&eacute;rie',NULL,'',1,''),(1934,'pays','Algeria',NULL,'',2,''),(1935,'pays','Allemagne',NULL,'',1,''),(1936,'pays','Germany',NULL,'',2,''),(1937,'pays','Andorre',NULL,'',1,''),(1938,'pays','Andorra',NULL,'',2,''),(1939,'pays','Angola',NULL,'',1,''),(1940,'pays','Angola',NULL,'',2,''),(1941,'pays','Anguilla',NULL,'',1,''),(1942,'pays','Anguilla',NULL,'',2,''),(1943,'pays','Antarctique',NULL,'',1,''),(1944,'pays','Antarctica',NULL,'',2,''),(1945,'pays','Antigua-et-Barbuda',NULL,'',1,''),(1946,'pays','Antigua & Barbuda',NULL,'',2,''),(1947,'pays','Antilles n&eacute;erlandaises',NULL,'',1,''),(1948,'pays','Netherlands Antilles',NULL,'',2,''),(1949,'pays','Arabie saoudite',NULL,'',1,''),(1950,'pays','Saudi Arabia',NULL,'',2,''),(1951,'pays','Argentine',NULL,'',1,''),(1952,'pays','Argentina',NULL,'',2,''),(1953,'pays','Arm&eacute;nie',NULL,'',1,''),(1954,'pays','Armenia',NULL,'',2,''),(1955,'pays','Aruba',NULL,'',1,''),(1956,'pays','Aruba',NULL,'',2,''),(1957,'pays','Australie',NULL,'',1,''),(1958,'pays','Australia',NULL,'',2,''),(1959,'pays','Autriche',NULL,'',1,''),(1960,'pays','Austria',NULL,'',2,''),(1961,'pays','Azerba&iuml;djan',NULL,'',1,''),(1962,'pays','Azerbaijan',NULL,'',2,''),(1963,'pays','B&eacute;nin',NULL,'',1,''),(1964,'pays','Benin',NULL,'',2,''),(1965,'pays','Bahamas',NULL,'',1,''),(1966,'pays','Bahamas, The',NULL,'',2,''),(1967,'pays','Bahre&iuml;n',NULL,'',1,''),(1968,'pays','Bahrain',NULL,'',2,''),(1969,'pays','Bangladesh',NULL,'',1,''),(1970,'pays','Bangladesh',NULL,'',2,''),(1971,'pays','Barbade',NULL,'',1,''),(1972,'pays','Barbados',NULL,'',2,''),(1973,'pays','Belau',NULL,'',1,''),(1974,'pays','Palau',NULL,'',2,''),(1975,'pays','Belgique',NULL,'',1,''),(1976,'pays','Belgium',NULL,'',2,''),(1977,'pays','Belize',NULL,'',1,''),(1978,'pays','Belize',NULL,'',2,''),(1979,'pays','Bermudes',NULL,'',1,''),(1980,'pays','Bermuda',NULL,'',2,''),(1981,'pays','Bhoutan',NULL,'',1,''),(1982,'pays','Bhutan',NULL,'',2,''),(1983,'pays','Bi&eacute;lorussie',NULL,'',1,''),(1984,'pays','Belarus',NULL,'',2,''),(1985,'pays','Birmanie',NULL,'',1,''),(1986,'pays','Myanmar (ex-Burma)',NULL,'',2,''),(1987,'pays','Bolivie',NULL,'',1,''),(1988,'pays','Bolivia',NULL,'',2,''),(1989,'pays','Bosnie-Herz&eacute;govine',NULL,'',1,''),(1990,'pays','Bosnia and Herzegovina',NULL,'',2,''),(1991,'pays','Botswana',NULL,'',1,''),(1992,'pays','Botswana',NULL,'',2,''),(1993,'pays','Br&eacute;sil',NULL,'',1,''),(1994,'pays','Brazil',NULL,'',2,''),(1995,'pays','Brunei',NULL,'',1,''),(1996,'pays','Brunei Darussalam',NULL,'',2,''),(1997,'pays','Bulgarie',NULL,'',1,''),(1998,'pays','Bulgaria',NULL,'',2,''),(1999,'pays','Burkina Faso',NULL,'',1,''),(2000,'pays','Burkina Faso',NULL,'',2,''),(2001,'pays','Burundi',NULL,'',1,''),(2002,'pays','Burundi',NULL,'',2,''),(2003,'pays','C&ocirc;te d\'Ivoire',NULL,'',1,''),(2004,'pays','Ivory Coast (see Cote d\'Ivoire)',NULL,'',2,''),(2005,'pays','Cambodge',NULL,'',1,''),(2006,'pays','Cambodia',NULL,'',2,''),(2007,'pays','Cameroun',NULL,'',1,''),(2008,'pays','Cameroon',NULL,'',2,''),(2009,'pays','Canada',NULL,'',1,''),(2010,'pays','Canada',NULL,'',2,''),(2011,'pays','Cap-Vert',NULL,'',1,''),(2012,'pays','Cape Verde',NULL,'',2,''),(2013,'pays','Chili',NULL,'',1,''),(2014,'pays','Chile',NULL,'',2,''),(2015,'pays','Chine',NULL,'',1,''),(2016,'pays','China',NULL,'',2,''),(2017,'pays','Chypre',NULL,'',1,''),(2018,'pays','Cyprus',NULL,'',2,''),(2019,'pays','Colombie',NULL,'',1,''),(2020,'pays','Colombia',NULL,'',2,''),(2021,'pays','Comores',NULL,'',1,''),(2022,'pays','Comoros',NULL,'',2,''),(2023,'pays','Congo',NULL,'',1,''),(2024,'pays','Congo',NULL,'',2,''),(2025,'pays','Cor&eacute;e du Nord',NULL,'',1,''),(2026,'pays','Korea, Demo. People\'s Rep. of',NULL,'',2,''),(2027,'pays','Cor&eacute;e du Sud',NULL,'',1,''),(2028,'pays','Korea, (South) Republic of',NULL,'',2,''),(2029,'pays','Costa Rica',NULL,'',1,''),(2030,'pays','Costa Rica',NULL,'',2,''),(2031,'pays','Croatie',NULL,'',1,''),(2032,'pays','Croatia',NULL,'',2,''),(2033,'pays','Cuba',NULL,'',1,''),(2034,'pays','Cuba',NULL,'',2,''),(2035,'pays','Danemark',NULL,'',1,''),(2036,'pays','Denmark',NULL,'',2,''),(2037,'pays','Djibouti',NULL,'',1,''),(2038,'pays','Djibouti',NULL,'',2,''),(2039,'pays','Dominique',NULL,'',1,''),(2040,'pays','Dominica',NULL,'',2,''),(2041,'pays','&Eacute;gypte',NULL,'',1,''),(2042,'pays','Egypt',NULL,'',2,''),(2043,'pays','&Eacute;mirats arabes unis',NULL,'',1,''),(2044,'pays','United Arab Emirates',NULL,'',2,''),(2045,'pays','&Eacute;quateur',NULL,'',1,''),(2046,'pays','Ecuador',NULL,'',2,''),(2047,'pays','&Eacute;rythr&eacute;e',NULL,'',1,''),(2048,'pays','Eritrea',NULL,'',2,''),(2049,'pays','Espagne',NULL,'',1,''),(2050,'pays','Spain',NULL,'',2,''),(2051,'pays','Estonie',NULL,'',1,''),(2052,'pays','Estonia',NULL,'',2,''),(2053,'pays','&Eacute;tats-Unis',NULL,'',1,''),(2054,'pays','United States',NULL,'',2,''),(2055,'pays','&Eacute;thiopie',NULL,'',1,''),(2056,'pays','Ethiopia',NULL,'',2,''),(2057,'pays','Finlande',NULL,'',1,''),(2058,'pays','Finland',NULL,'',2,''),(2059,'pays','France',NULL,'',1,''),(2060,'pays','France',NULL,'',2,''),(2061,'pays','G&eacute;orgie',NULL,'',1,''),(2062,'pays','Georgia',NULL,'',2,''),(2063,'pays','Gabon',NULL,'',1,''),(2064,'pays','Gabon',NULL,'',2,''),(2065,'pays','Gambie',NULL,'',1,''),(2066,'pays','Gambia, the',NULL,'',2,''),(2067,'pays','Ghana',NULL,'',1,''),(2068,'pays','Ghana',NULL,'',2,''),(2069,'pays','Gibraltar',NULL,'',1,''),(2070,'pays','Gibraltar',NULL,'',2,''),(2071,'pays','Gr&egrave;ce',NULL,'',1,''),(2072,'pays','Greece',NULL,'',2,''),(2073,'pays','Grenade',NULL,'',1,''),(2074,'pays','Grenada',NULL,'',2,''),(2075,'pays','Groenland',NULL,'',1,''),(2076,'pays','Greenland',NULL,'',2,''),(2077,'pays','Guadeloupe',NULL,'',1,''),(2078,'pays','Guinea, Equatorial',NULL,'',2,''),(2079,'pays','Guam',NULL,'',1,''),(2080,'pays','Guam',NULL,'',2,''),(2081,'pays','Guatemala',NULL,'',1,''),(2082,'pays','Guatemala',NULL,'',2,''),(2083,'pays','Guin&eacute;e',NULL,'',1,''),(2084,'pays','Guinea',NULL,'',2,''),(2085,'pays','Guin&eacute;e &eacute;quatoriale',NULL,'',1,''),(2086,'pays','Equatorial Guinea',NULL,'',2,''),(2087,'pays','Guin&eacute;e-Bissao',NULL,'',1,''),(2088,'pays','Guinea-Bissau',NULL,'',2,''),(2089,'pays','Guyana',NULL,'',1,''),(2090,'pays','Guyana',NULL,'',2,''),(2091,'pays','Guyane fran&ccedil;aise',NULL,'',1,''),(2092,'pays','Guiana, French',NULL,'',2,''),(2093,'pays','Ha&iuml;ti',NULL,'',1,''),(2094,'pays','Haiti',NULL,'',2,''),(2095,'pays','Honduras',NULL,'',1,''),(2096,'pays','Honduras',NULL,'',2,''),(2097,'pays','Hong Kong',NULL,'',1,''),(2098,'pays','Hong Kong, (China)',NULL,'',2,''),(2099,'pays','Hongrie',NULL,'',1,''),(2100,'pays','Hungary',NULL,'',2,''),(2101,'pays','Ile Bouvet',NULL,'',1,''),(2102,'pays','Bouvet Island',NULL,'',2,''),(2103,'pays','Ile Christmas',NULL,'',1,''),(2104,'pays','Christmas Island',NULL,'',2,''),(2105,'pays','Ile Norfolk',NULL,'',1,''),(2106,'pays','Norfolk Island',NULL,'',2,''),(2107,'pays','Iles Cayman',NULL,'',1,''),(2108,'pays','Cayman Islands',NULL,'',2,''),(2109,'pays','Iles Cook',NULL,'',1,''),(2110,'pays','Cook Islands',NULL,'',2,''),(2111,'pays','Iles F&eacute;ro&eacute;',NULL,'',1,''),(2112,'pays','Faroe Islands',NULL,'',2,''),(2113,'pays','Iles Falkland',NULL,'',1,''),(2114,'pays','Falkland Islands (Malvinas)',NULL,'',2,''),(2115,'pays','Iles Fidji',NULL,'',1,''),(2116,'pays','Fiji',NULL,'',2,''),(2117,'pays','Iles G&eacute;orgie du Sud et Sandwich du Sud',NULL,'',1,''),(2118,'pays','S. Georgia and S. Sandwich Is.',NULL,'',2,''),(2119,'pays','Iles Heard et McDonald',NULL,'',1,''),(2120,'pays','Heard and McDonald Islands',NULL,'',2,''),(2121,'pays','Iles Marshall',NULL,'',1,''),(2122,'pays','Marshall Islands',NULL,'',2,''),(2123,'pays','Iles Pitcairn',NULL,'',1,''),(2124,'pays','Pitcairn Island',NULL,'',2,''),(2125,'pays','Iles Salomon',NULL,'',1,''),(2126,'pays','Solomon Islands',NULL,'',2,''),(2127,'pays','Iles Svalbard et Jan Mayen',NULL,'',1,''),(2128,'pays','Svalbard and Jan Mayen Islands',NULL,'',2,''),(2129,'pays','Iles Turks-et-Caicos',NULL,'',1,''),(2130,'pays','Turks and Caicos Islands',NULL,'',2,''),(2131,'pays','Iles Vierges am&eacute;ricaines',NULL,'',1,''),(2132,'pays','Virgin Islands, U.S.',NULL,'',2,''),(2133,'pays','Iles Vierges britanniques',NULL,'',1,''),(2134,'pays','Virgin Islands, British',NULL,'',2,''),(2135,'pays','Iles des Cocos (Keeling)',NULL,'',1,''),(2136,'pays','Cocos (Keeling) Islands',NULL,'',2,''),(2137,'pays','Iles mineures &eacute;loign&eacute;es des &Eacute;tats-Unis',NULL,'',1,''),(2138,'pays','US Minor Outlying Islands',NULL,'',2,''),(2139,'pays','Inde',NULL,'',1,''),(2140,'pays','India',NULL,'',2,''),(2141,'pays','Indon&eacute;sie',NULL,'',1,''),(2142,'pays','Indonesia',NULL,'',2,''),(2143,'pays','Iran',NULL,'',1,''),(2144,'pays','Iran, Islamic Republic of',NULL,'',2,''),(2145,'pays','Iraq',NULL,'',1,''),(2146,'pays','Iraq',NULL,'',2,''),(2147,'pays','Irlande',NULL,'',1,''),(2148,'pays','Ireland',NULL,'',2,''),(2149,'pays','Islande',NULL,'',1,''),(2150,'pays','Iceland',NULL,'',2,''),(2151,'pays','Isra&euml;l',NULL,'',1,''),(2152,'pays','Israel',NULL,'',2,''),(2153,'pays','Italie',NULL,'',1,''),(2154,'pays','Italy',NULL,'',2,''),(2155,'pays','Jama&iuml;que',NULL,'',1,''),(2156,'pays','Jamaica',NULL,'',2,''),(2157,'pays','Japon',NULL,'',1,''),(2158,'pays','Japan',NULL,'',2,''),(2159,'pays','Jordanie',NULL,'',1,''),(2160,'pays','Jordan',NULL,'',2,''),(2161,'pays','Kazakhstan',NULL,'',1,''),(2162,'pays','Kazakhstan',NULL,'',2,''),(2163,'pays','Kenya',NULL,'',1,''),(2164,'pays','Kenya',NULL,'',2,''),(2165,'pays','Kirghizistan',NULL,'',1,''),(2166,'pays','Kyrgyzstan',NULL,'',2,''),(2167,'pays','Kiribati',NULL,'',1,''),(2168,'pays','Kiribati',NULL,'',2,''),(2169,'pays','Kowe&iuml;t',NULL,'',1,''),(2170,'pays','Kuwait',NULL,'',2,''),(2171,'pays','Laos',NULL,'',1,''),(2172,'pays','Lao People\'s Democratic Republic',NULL,'',2,''),(2173,'pays','Lesotho',NULL,'',1,''),(2174,'pays','Lesotho',NULL,'',2,''),(2175,'pays','Lettonie',NULL,'',1,''),(2176,'pays','Latvia',NULL,'',2,''),(2177,'pays','Liban',NULL,'',1,''),(2178,'pays','Lebanon',NULL,'',2,''),(2179,'pays','Liberia',NULL,'',1,''),(2180,'pays','Liberia',NULL,'',2,''),(2181,'pays','Libye',NULL,'',1,''),(2182,'pays','Libyan Arab Jamahiriya',NULL,'',2,''),(2183,'pays','Liechtenstein',NULL,'',1,''),(2184,'pays','Liechtenstein',NULL,'',2,''),(2185,'pays','Lituanie',NULL,'',1,''),(2186,'pays','Lithuania',NULL,'',2,''),(2187,'pays','Luxembourg',NULL,'',1,''),(2188,'pays','Luxembourg',NULL,'',2,''),(2189,'pays','Macao',NULL,'',1,''),(2190,'pays','Macao, (China)',NULL,'',2,''),(2191,'pays','Madagascar',NULL,'',1,''),(2192,'pays','Madagascar',NULL,'',2,''),(2193,'pays','Malaisie',NULL,'',1,''),(2194,'pays','Malaysia',NULL,'',2,''),(2195,'pays','Malawi',NULL,'',1,''),(2196,'pays','Malawi',NULL,'',2,''),(2197,'pays','Maldives',NULL,'',1,''),(2198,'pays','Maldives',NULL,'',2,''),(2199,'pays','Mali',NULL,'',1,''),(2200,'pays','Mali',NULL,'',2,''),(2201,'pays','Malte',NULL,'',1,''),(2202,'pays','Malta',NULL,'',2,''),(2203,'pays','Mariannes du Nord',NULL,'',1,''),(2204,'pays','Northern Mariana Islands',NULL,'',2,''),(2205,'pays','Maroc',NULL,'',1,''),(2206,'pays','Morocco',NULL,'',2,''),(2207,'pays','Martinique',NULL,'',1,''),(2208,'pays','Martinique',NULL,'',2,''),(2209,'pays','Maurice',NULL,'',1,''),(2210,'pays','Mauritius',NULL,'',2,''),(2211,'pays','Mauritanie',NULL,'',1,''),(2212,'pays','Mauritania',NULL,'',2,''),(2213,'pays','Mayotte',NULL,'',1,''),(2214,'pays','Mayotte',NULL,'',2,''),(2215,'pays','Mexique',NULL,'',1,''),(2216,'pays','Mexico',NULL,'',2,''),(2217,'pays','Micron&eacute;sie',NULL,'',1,''),(2218,'pays','Micronesia, Federated States of',NULL,'',2,''),(2219,'pays','Moldavie',NULL,'',1,''),(2220,'pays','Moldova, Republic of',NULL,'',2,''),(2221,'pays','Monaco',NULL,'',1,''),(2222,'pays','Monaco',NULL,'',2,''),(2223,'pays','Mongolie',NULL,'',1,''),(2224,'pays','Mongolia',NULL,'',2,''),(2225,'pays','Montserrat',NULL,'',1,''),(2226,'pays','Montserrat',NULL,'',2,''),(2227,'pays','Mozambique',NULL,'',1,''),(2228,'pays','Mozambique',NULL,'',2,''),(2229,'pays','N&eacute;pal',NULL,'',1,''),(2230,'pays','Nepal',NULL,'',2,''),(2231,'pays','Namibie',NULL,'',1,''),(2232,'pays','Namibia',NULL,'',2,''),(2233,'pays','Nauru',NULL,'',1,''),(2234,'pays','Nauru',NULL,'',2,''),(2235,'pays','Nicaragua',NULL,'',1,''),(2236,'pays','Nicaragua',NULL,'',2,''),(2237,'pays','Niger',NULL,'',1,''),(2238,'pays','Niger',NULL,'',2,''),(2239,'pays','Nigeria',NULL,'',1,''),(2240,'pays','Nigeria',NULL,'',2,''),(2241,'pays','Niou&eacute;',NULL,'',1,''),(2242,'pays','Niue',NULL,'',2,''),(2243,'pays','Norv&egrave;ge',NULL,'',1,''),(2244,'pays','Norway',NULL,'',2,''),(2245,'pays','Nouvelle-Cal&eacute;donie',NULL,'',1,''),(2246,'pays','New Caledonia',NULL,'',2,''),(2247,'pays','Nouvelle-Z&eacute;lande',NULL,'',1,''),(2248,'pays','New Zealand',NULL,'',2,''),(2249,'pays','Oman',NULL,'',1,''),(2250,'pays','Oman',NULL,'',2,''),(2251,'pays','Ouganda',NULL,'',1,''),(2252,'pays','Uganda',NULL,'',2,''),(2253,'pays','Ouzb&eacute;kistan',NULL,'',1,''),(2254,'pays','Uzbekistan',NULL,'',2,''),(2255,'pays','P&eacute;rou',NULL,'',1,''),(2256,'pays','Peru',NULL,'',2,''),(2257,'pays','Pakistan',NULL,'',1,''),(2258,'pays','Pakistan',NULL,'',2,''),(2259,'pays','Panama',NULL,'',1,''),(2260,'pays','Panama',NULL,'',2,''),(2261,'pays','Papouasie-Nouvelle-Guin&eacute;e',NULL,'',1,''),(2262,'pays','Papua New Guinea',NULL,'',2,''),(2263,'pays','Paraguay',NULL,'',1,''),(2264,'pays','Paraguay',NULL,'',2,''),(2265,'pays','Pays-Bas',NULL,'',1,''),(2266,'pays','Netherlands',NULL,'',2,''),(2267,'pays','Philippines',NULL,'',1,''),(2268,'pays','Philippines',NULL,'',2,''),(2269,'pays','Pologne',NULL,'',1,''),(2270,'pays','Poland',NULL,'',2,''),(2271,'pays','Polyn&eacute;sie fran&ccedil;aise',NULL,'',1,''),(2272,'pays','French Polynesia',NULL,'',2,''),(2273,'pays','Porto Rico',NULL,'',1,''),(2274,'pays','Puerto Rico',NULL,'',2,''),(2275,'pays','Portugal',NULL,'',1,''),(2276,'pays','Portugal',NULL,'',2,''),(2277,'pays','Qatar',NULL,'',1,''),(2278,'pays','Qatar',NULL,'',2,''),(2279,'pays','R&eacute;publique centrafricaine',NULL,'',1,''),(2280,'pays','Central African Republic',NULL,'',2,''),(2281,'pays','R&eacute;publique d&eacute;mocratique du Congo',NULL,'',1,''),(2282,'pays','Congo, Democratic Rep. of the',NULL,'',2,''),(2283,'pays','R&eacute;publique dominicaine',NULL,'',1,''),(2284,'pays','Dominican Republic',NULL,'',2,''),(2285,'pays','R&eacute;publique tch&egrave;que',NULL,'',1,''),(2286,'pays','Czech Republic',NULL,'',2,''),(2287,'pays','R&eacute;union',NULL,'',1,''),(2288,'pays','Reunion',NULL,'',2,''),(2289,'pays','Roumanie',NULL,'',1,''),(2290,'pays','Romania',NULL,'',2,''),(2291,'pays','Royaume-Uni',NULL,'',1,''),(2292,'pays','Saint Pierre and Miquelon',NULL,'',2,''),(2293,'pays','Russie',NULL,'',1,''),(2294,'pays','Russia (Russian Federation)',NULL,'',2,''),(2295,'pays','Rwanda',NULL,'',1,''),(2296,'pays','Rwanda',NULL,'',2,''),(2297,'pays','S&eacute;n&eacute;gal',NULL,'',1,''),(2298,'pays','Senegal',NULL,'',2,''),(2299,'pays','Sahara occidental',NULL,'',1,''),(2300,'pays','Western Sahara',NULL,'',2,''),(2301,'pays','Saint-Christophe-et-Ni&eacute;v&egrave;s',NULL,'',1,''),(2302,'pays','Saint Kitts and Nevis',NULL,'',2,''),(2303,'pays','Saint-Marin',NULL,'',1,''),(2304,'pays','San Marino',NULL,'',2,''),(2305,'pays','Saint-Pierre-et-Miquelon',NULL,'',1,''),(2306,'pays','Saint Pierre and Miquelon',NULL,'',2,''),(2307,'pays','Saint-Si&egrave;ge',NULL,'',1,''),(2308,'pays','Vatican City State (Holy See)',NULL,'',2,''),(2309,'pays','Saint-Vincent-et-les-Grenadines',NULL,'',1,''),(2310,'pays','Saint Vincent and the Grenadines',NULL,'',2,''),(2311,'pays','Sainte-H&eacute;l&egrave;ne',NULL,'',1,''),(2312,'pays','Saint Helena',NULL,'',2,''),(2313,'pays','Sainte-Lucie',NULL,'',1,''),(2314,'pays','Saint Lucia',NULL,'',2,''),(2315,'pays','Salvador',NULL,'',1,''),(2316,'pays','El Salvador',NULL,'',2,''),(2317,'pays','Samoa',NULL,'',1,''),(2318,'pays','Samoa',NULL,'',2,''),(2319,'pays','Samoa am&eacute;ricaines',NULL,'',1,''),(2320,'pays','American Samoa',NULL,'',2,''),(2321,'pays','Sao Tom&eacute;-et-Principe',NULL,'',1,''),(2322,'pays','Sao Tome and Principe',NULL,'',2,''),(2323,'pays','Seychelles',NULL,'',1,''),(2324,'pays','Seychelles',NULL,'',2,''),(2325,'pays','Sierra Leone',NULL,'',1,''),(2326,'pays','Sierra Leone',NULL,'',2,''),(2327,'pays','Singapour',NULL,'',1,''),(2328,'pays','Singapore',NULL,'',2,''),(2329,'pays','Slov&eacute;nie',NULL,'',1,''),(2330,'pays','Slovenia',NULL,'',2,''),(2331,'pays','Slovaquie',NULL,'',1,''),(2332,'pays','Slovakia',NULL,'',2,''),(2333,'pays','Somalie',NULL,'',1,''),(2334,'pays','Somalia',NULL,'',2,''),(2335,'pays','Soudan',NULL,'',1,''),(2336,'pays','Sudan',NULL,'',2,''),(2337,'pays','Sri Lanka',NULL,'',1,''),(2338,'pays','Sri Lanka (ex-Ceilan)',NULL,'',2,''),(2339,'pays','Su&egrave;de',NULL,'',1,''),(2340,'pays','Sweden',NULL,'',2,''),(2341,'pays','Suisse',NULL,'',1,''),(2342,'pays','Switzerland',NULL,'',2,''),(2343,'pays','Suriname',NULL,'',1,''),(2344,'pays','Suriname',NULL,'',2,''),(2345,'pays','Swaziland',NULL,'',1,''),(2346,'pays','Swaziland',NULL,'',2,''),(2347,'pays','Syrie',NULL,'',1,''),(2348,'pays','Syrian Arab Republic',NULL,'',2,''),(2349,'pays','Ta&iuml;wan',NULL,'',1,''),(2350,'pays','Taiwan',NULL,'',2,''),(2351,'pays','Tadjikistan',NULL,'',1,''),(2352,'pays','Tajikistan',NULL,'',2,''),(2353,'pays','Tanzanie',NULL,'',1,''),(2354,'pays','Tanzania, United Republic of',NULL,'',2,''),(2355,'pays','Tchad',NULL,'',1,''),(2356,'pays','Chad',NULL,'',2,''),(2357,'pays','Terres australes fran&ccedil;aises',NULL,'',1,''),(2358,'pays','French Southern Territories - TF',NULL,'',2,''),(2359,'pays','Territoire britannique de l\'Oc&eacute;an Indien',NULL,'',1,''),(2360,'pays','British Indian Ocean Territory',NULL,'',2,''),(2361,'pays','Tha&iuml;lande',NULL,'',1,''),(2362,'pays','Thailand',NULL,'',2,''),(2363,'pays','Timor Oriental',NULL,'',1,''),(2364,'pays','Timor-Leste (East Timor)',NULL,'',2,''),(2365,'pays','Togo',NULL,'',1,''),(2366,'pays','Togo',NULL,'',2,''),(2367,'pays','Tok&eacute;laou',NULL,'',1,''),(2368,'pays','Tokelau',NULL,'',2,''),(2369,'pays','Tonga',NULL,'',1,''),(2370,'pays','Tonga',NULL,'',2,''),(2371,'pays','Trinit&eacute;-et-Tobago',NULL,'',1,''),(2372,'pays','Trinidad & Tobago',NULL,'',2,''),(2373,'pays','Tunisie',NULL,'',1,''),(2374,'pays','Tunisia',NULL,'',2,''),(2375,'pays','Turkm&eacute;nistan',NULL,'',1,''),(2376,'pays','Turkmenistan',NULL,'',2,''),(2377,'pays','Turquie',NULL,'',1,''),(2378,'pays','Turkey',NULL,'',2,''),(2379,'pays','Tuvalu',NULL,'',1,''),(2380,'pays','Tuvalu',NULL,'',2,''),(2381,'pays','Ukraine',NULL,'',1,''),(2382,'pays','Ukraine',NULL,'',2,''),(2383,'pays','Uruguay',NULL,'',1,''),(2384,'pays','Uruguay',NULL,'',2,''),(2385,'pays','Vanuatu',NULL,'',1,''),(2386,'pays','Vanuatu',NULL,'',2,''),(2387,'pays','Venezuela',NULL,'',1,''),(2388,'pays','Venezuela',NULL,'',2,''),(2389,'pays','Vi&ecirc;t Nam',NULL,'',1,''),(2390,'pays','Viet Nam',NULL,'',2,''),(2391,'pays','Wallis-et-Futuna',NULL,'',1,''),(2392,'pays','Wallis and Futuna',NULL,'',2,''),(2393,'pays','Y&eacute;men',NULL,'',1,''),(2394,'pays','Yemen',NULL,'',2,''),(2395,'pays','Yougoslavie',NULL,'',1,''),(2396,'pays','Saint Pierre and Miquelon',NULL,'',2,''),(2397,'pays','Zambie',NULL,'',1,''),(2398,'pays','Zambia',NULL,'',2,''),(2399,'pays','Zimbabwe',NULL,'',1,''),(2400,'pays','Zimbabwe',NULL,'',2,''),(2401,'pays','ex-R&eacute;publique yougoslave de Mac&eacute;doine',NULL,'',1,''),(2402,'pays','Macedonia, TFYR',NULL,'',2,''),(3051,'codenace','AGRICULTURE, SYLVICULTURE ET P&Ecirc;CHE',NULL,'',1,'A00'),(3052,'codenace','Culture et production animale, chasse et services annexes',NULL,'',1,'A01'),(3053,'codenace','Sylviculture et exploitation foresti&egrave;re',NULL,'',1,'A02'),(3054,'codenace','P&ecirc;che et aquaculture',NULL,'',1,'A03'),(3055,'codenace','INDUSTRIES EXTRACTIVES',NULL,'',1,'B00'),(3056,'codenace','Extraction de houille et de lignite',NULL,'',1,'B05'),(3057,'codenace','Extraction d\'hydrocarbures',NULL,'',1,'B06'),(3058,'codenace','Extraction de minerais m&eacute;talliques',NULL,'',1,'B07'),(3059,'codenace','Autres industries extractives',NULL,'',1,'B08'),(3060,'codenace','Services de soutien aux industries extractives',NULL,'',1,'B09'),(3061,'codenace','INDUSTRIE MANUFACTURI&Egrave;RE',NULL,'',1,'C00'),(3062,'codenace','Industries alimentaires',NULL,'',1,'C10'),(3063,'codenace','Fabrication de boissons',NULL,'',1,'C11'),(3064,'codenace','Fabrication de produits &agrave; base de tabac',NULL,'',1,'C12'),(3065,'codenace','Fabrication de textiles',NULL,'',1,'C13'),(3066,'codenace','Industrie de l\'habillement',NULL,'',1,'C14'),(3067,'codenace','Industrie du cuir et de la chaussure',NULL,'',1,'C15'),(3068,'codenace','Travail du bois et fabrication d\'articles en bois et en li&egrave;ge, &agrave; l’exception des meubles ; fabrication d’articles en vannerie et sparterie',NULL,'',1,'C16'),(3069,'codenace','Industrie du papier et du carton',NULL,'',1,'C17'),(3070,'codenace','Imprimerie et reproduction d\'enregistrements',NULL,'',1,'C18'),(3071,'codenace','Cok&eacute;faction et raffinage',NULL,'',1,'C19'),(3072,'codenace','Industrie chimique',NULL,'',1,'C20'),(3073,'codenace','Industrie pharmaceutique',NULL,'',1,'C21'),(3074,'codenace','Fabrication de produits en caoutchouc et en plastique',NULL,'',1,'C22'),(3075,'codenace','Fabrication d\'autres produits min&eacute;raux non m&eacute;talliques',NULL,'',1,'C23'),(3076,'codenace','M&eacute;tallurgie',NULL,'',1,'C24'),(3077,'codenace','Fabrication de produits m&eacute;talliques, &agrave; l’exception des machines et des &eacute;quipements',NULL,'',1,'C25'),(3078,'codenace','Fabrication de produits informatiques, &eacute;lectroniques et optiques',NULL,'',1,'C26'),(3079,'codenace','Fabrication d\'&eacute;quipements &eacute;lectriques',NULL,'',1,'C27'),(3080,'codenace','Fabrication de machines et &eacute;quipements n.c.a.',NULL,'',1,'C28'),(3081,'codenace','Industrie automobile',NULL,'',1,'C29'),(3082,'codenace','Fabrication d\'autres mat&eacute;riels de transport',NULL,'',1,'C30'),(3083,'codenace','Fabrication de meubles',NULL,'',1,'C31'),(3084,'codenace','Autres industries manufacturi&egrave;res',NULL,'',1,'C32'),(3085,'codenace','R&eacute;paration et installation de machines et d\'&eacute;quipements',NULL,'',1,'C33'),(3086,'codenace','PRODUCTION ET DISTRIBUTION D\'&Eacute;LECTRICIT&Eacute;, DE GAZ, DE VAPEUR ET D\'AIR CONDITIONN&Eacute;',NULL,'',1,'D00'),(3087,'codenace','Production et distribution d\'&eacute;lectricit&eacute;, de gaz, de vapeur et d\'air conditionn&eacute;',NULL,'',1,'D35'),(3088,'codenace','PRODUCTION ET DISTRIBUTION D\'EAU; ASSAINISSEMENT, GESTION DES D&Eacute;CHETS ET D&Eacute;POLLUTION',NULL,'',1,'E00'),(3089,'codenace','Captage, traitement et distribution d\'eau',NULL,'',1,'E36'),(3090,'codenace','Collecte et traitement des eaux us&eacute;es',NULL,'',1,'E37'),(3091,'codenace','Collecte, traitement et &eacute;limination des d&eacute;chets; r&eacute;cup&eacute;ration',NULL,'',1,'E38'),(3092,'codenace','D&eacute;pollution et autres services de gestion des d&eacute;chets',NULL,'',1,'E39'),(3093,'codenace','CONSTRUCTION',NULL,'',1,'F00'),(3094,'codenace','Construction de bâtiments',NULL,'',1,'F41'),(3095,'codenace','G&eacute;nie civil',NULL,'',1,'F42'),(3096,'codenace','Travaux de construction sp&eacute;cialis&eacute;s',NULL,'',1,'F43'),(3097,'codenace','COMMERCE; R&Eacute;PARATION D\'AUTOMOBILES ET DE MOTOCYCLES',NULL,'',1,'G00'),(3098,'codenace','Commerce et r&eacute;paration d\'automobiles et de motocycles',NULL,'',1,'G45'),(3099,'codenace','Commerce de gros, &agrave; l’exception des automobiles et des motocycles',NULL,'',1,'G46'),(3100,'codenace','Commerce de d&eacute;tail, &agrave; l’exception des automobiles et des motocycles',NULL,'',1,'G47'),(3101,'codenace','TRANSPORTS ET ENTREPOSAGE',NULL,'',1,'H00'),(3102,'codenace','Transports terrestres et transport par conduites',NULL,'',1,'H49'),(3103,'codenace','Transports par eau',NULL,'',1,'H50'),(3104,'codenace','Transports a&eacute;riens',NULL,'',1,'H51'),(3105,'codenace','Entreposage et services auxiliaires des transports',NULL,'',1,'H52'),(3106,'codenace','Activit&eacute;s de poste et de courrier',NULL,'',1,'H53'),(3107,'codenace','H&Eacute;BERGEMENT ET RESTAURATION',NULL,'',1,'I00'),(3108,'codenace','H&eacute;bergement',NULL,'',1,'I55'),(3109,'codenace','Restauration',NULL,'',1,'I56'),(3110,'codenace','INFORMATION ET COMMUNICATION',NULL,'',1,'J00'),(3111,'codenace','&Eacute;dition',NULL,'',1,'J58'),(3112,'codenace','Production de films cin&eacute;matographiques, de vid&eacute;o et de programmes de t&eacute;l&eacute;vision; enregistrement sonore et &eacute;dition musicale',NULL,'',1,'J59'),(3113,'codenace','Programmation et diffusion',NULL,'',1,'J60'),(3114,'codenace','T&eacute;l&eacute;communications',NULL,'',1,'J61'),(3115,'codenace','Programmation, conseil et autres activit&eacute;s informatiques',NULL,'',1,'J62'),(3116,'codenace','Services d\'information',NULL,'',1,'J63'),(3117,'codenace','ACTIVIT&Eacute;S FINANCI&Egrave;RES ET D\'ASSURANCE',NULL,'',1,'K00'),(3118,'codenace','Activit&eacute;s des services financiers, hors assurance et caisses de retraite',NULL,'',1,'K64'),(3119,'codenace','Assurance',NULL,'',1,'K65'),(3120,'codenace','Activit&eacute;s auxiliaires de services financiers et d\'assurance',NULL,'',1,'K66'),(3121,'codenace','ACTIVIT&Eacute;S IMMOBILI&Egrave;RES',NULL,'',0,'L00'),(3122,'codenace','Activit&eacute;s immobili&egrave;res',NULL,'',1,'L68'),(3123,'codenace','ACTIVIT&Eacute;S SP&Eacute;CIALIS&Eacute;ES, SCIENTIFIQUES ET TECHNIQUES',NULL,'',1,'M00'),(3124,'codenace','Activit&eacute;s juridiques et comptables',NULL,'',1,'M69'),(3125,'codenace','Activit&eacute;s des si&egrave;ges sociaux; conseil de gestion',NULL,'',1,'M70'),(3126,'codenace','Activit&eacute;s d\'architecture et d\'ing&eacute;nierie; activit&eacute;s de contr&ocirc;le et analyses techniques',NULL,'',1,'M71'),(3127,'codenace','Recherche d&eacute;veloppement scientifique',NULL,'',1,'M72'),(3128,'codenace','Publicit&eacute; et &eacute;tudes de march&eacute;',NULL,'',1,'M73'),(3129,'codenace','Autres activit&eacute;s sp&eacute;cialis&eacute;es, scientifiques et techniques',NULL,'',1,'M74'),(3130,'codenace','Activit&eacute;s v&eacute;t&eacute;rinaires',NULL,'',1,'M75'),(3131,'codenace','ACTIVIT&Eacute;S DE SERVICES ADMINISTRATIFS ET DE SOUTIEN',NULL,'',1,'N00'),(3132,'codenace','Activit&eacute;s de location et location bail',NULL,'',1,'N77'),(3133,'codenace','Activit&eacute;s li&eacute;es &agrave; l\'emploi',NULL,'',1,'N78'),(3134,'codenace','Activit&eacute;s des agences de voyage, voyagistes, services de r&eacute;servation et activit&eacute;s connexes',NULL,'',1,'N79'),(3135,'codenace','Enqu&ecirc;tes et s&eacute;curit&eacute;',NULL,'',0,'N80'),(3136,'codenace','Services relatifs aux bâtiments et am&eacute;nagement paysager',NULL,'',1,'N81'),(3137,'codenace','Activit&eacute;s administratives et autres activit&eacute;s de soutien aux entreprises',NULL,'',1,'N82'),(3138,'codenace','ADMINISTRATION PUBLIQUE',NULL,'',1,'O00'),(3139,'codenace','Administration publique et d&eacute;fense; s&eacute;curit&eacute; sociale obligatoire',NULL,'',1,'O84'),(3140,'codenace','ENSEIGNEMENT',NULL,'',1,'P00'),(3141,'codenace','Enseignement',NULL,'',1,'P85'),(3142,'codenace','SANT&Eacute; HUMAINE ET ACTION SOCIALE',NULL,'',1,'Q00'),(3143,'codenace','Activit&eacute;s pour la sant&eacute; humaine',NULL,'',1,'Q86'),(3144,'codenace','H&eacute;bergement m&eacute;dico-social et social',NULL,'',1,'Q87'),(3145,'codenace','Action sociale sans h&eacute;bergement',NULL,'',1,'Q88'),(3146,'codenace','ARTS, SPECTACLES ET ACTIVIT&Eacute;S R&Eacute;CR&Eacute;ATIVES',NULL,'',1,'R00'),(3147,'codenace','Activit&eacute;s cr&eacute;atives, artistiques et de spectacle',NULL,'',1,'R90'),(3148,'codenace','Biblioth&egrave;ques, archives, mus&eacute;es et autres activit&eacute;s culturelles',NULL,'',1,'R91'),(3149,'codenace','Organisation de jeux de hasard et d\'argent',NULL,'',1,'R92'),(3150,'codenace','Activit&eacute;s sportives, r&eacute;cr&eacute;atives et de loisirs',NULL,'',0,'R93'),(3151,'codenace','AUTRES ACTIVIT&Eacute;S DE SERVICES',NULL,'',1,'S00'),(3152,'codenace','Activit&eacute;s des organisations associatives',NULL,'',1,'S94'),(3153,'codenace','R&eacute;paration d\'ordinateurs et de biens personnels et domestiques',NULL,'',1,'S95'),(3154,'codenace','Autres services personnels',NULL,'',1,'S96'),(3155,'codenace','Activit&eacute;s des m&eacute;nages en tant qu\'employeurs de personnel domestique',NULL,'',1,'S97'),(3156,'codenace','Activit&eacute;s indiff&eacute;renci&eacute;es des m&eacute;nages en tant que producteurs de biens et services pour usage propre',NULL,'',1,'S98'),(3157,'codenace','ACTIVIT&Eacute;S DES M&Eacute;NAGES EN TANT QU\'EMPLOYEURS; ACTIVIT&Eacute;S INDIFF&Eacute;RENCI&Eacute;ES DES M&Eacute;NAGES EN TANT QUE PRODUCTEURS DE BIENS ET SERVICES POUR USAGE PROPRE',NULL,'',1,'T00'),(3158,'codenace','Activit&eacute;s des organisations et organismes extraterritoriaux',NULL,'',1,'T99'),(3159,'codenace','AGRICULTURE, FORESTRY AND FISHING',NULL,'',2,'A00'),(3160,'codenace','Crop and animal production, hunting and related service activities',NULL,'',2,'A01'),(3161,'codenace','Forestry and logging',NULL,'',2,'A02'),(3162,'codenace','Fishing and aquaculture',NULL,'',2,'A03'),(3163,'codenace','MINING AND QUARRYING',NULL,'',2,'B00'),(3164,'codenace','Mining of coal and lignite',NULL,'',2,'B05'),(3165,'codenace','Extraction of crude petroleum and natural gas',NULL,'',2,'B06'),(3166,'codenace','Mining of metal ores',NULL,'',2,'B07'),(3167,'codenace','Other mining and quarrying',NULL,'',2,'B08'),(3168,'codenace','Mining support service activities',NULL,'',2,'B09'),(3169,'codenace','MANUFACTURING',NULL,'',2,'C00'),(3170,'codenace','Manufacture of food products',NULL,'',2,'C10'),(3171,'codenace','Manufacture of beverages',NULL,'',2,'C11'),(3172,'codenace','Manufacture of tobacco products',NULL,'',2,'C12'),(3173,'codenace','Manufacture of textiles',NULL,'',2,'C13'),(3174,'codenace','Manufacture of wearing apparel',NULL,'',2,'C14'),(3175,'codenace','Manufacture of leather and related products',NULL,'',2,'C15'),(3176,'codenace','Manufacture of wood and of products of wood and cork, except furniture; manufacture of articles of straw and plaiting materials',NULL,'',2,'C16'),(3177,'codenace','Manufacture of paper and paper products',NULL,'',2,'C17'),(3178,'codenace','Printing and reproduction of recorded media',NULL,'',2,'C18'),(3179,'codenace','Manufacture of coke and refined petroleum products',NULL,'',2,'C19'),(3180,'codenace','Manufacture of chemicals and chemical products',NULL,'',2,'C20'),(3181,'codenace','Manufacture of basic pharmaceutical products and pharmaceutical preparations',NULL,'',2,'C21'),(3182,'codenace','Manufacture of rubber and plastic products',NULL,'',2,'C22'),(3183,'codenace','Manufacture of other non-metallic mineral products',NULL,'',2,'C23'),(3184,'codenace','Manufacture of basic metals',NULL,'',2,'C24'),(3185,'codenace','Manufacture of fabricated metal products, except machinery and equipment',NULL,'',2,'C25'),(3186,'codenace','Manufacture of computer, electronic and optical products',NULL,'',2,'C26'),(3187,'codenace','Manufacture of electrical equipment',NULL,'',2,'C27'),(3188,'codenace','Manufacture of machinery and equipment n.e.c.',NULL,'',2,'C28'),(3189,'codenace','Manufacture of motor vehicles, trailers and semi-trailers',NULL,'',2,'C29'),(3190,'codenace','Manufacture of other transport equipment',NULL,'',2,'C30'),(3191,'codenace','Manufacture of furniture',NULL,'',2,'C31'),(3192,'codenace','Other manufacturing',NULL,'',2,'C32'),(3193,'codenace','Repair and installation of machinery and equipment',NULL,'',2,'C33'),(3194,'codenace','ELECTRICITY, GAS, STEAM AND AIR CONDITIONING SUPPLY',NULL,'',2,'D00'),(3195,'codenace','Electricity, gas, steam and air conditioning supply',NULL,'',2,'D35'),(3196,'codenace','WATER SUPPLY; SEWERAGE, WASTE MANAGEMENT AND REMEDIATION ACTIVITIES',NULL,'',2,'E00'),(3197,'codenace','Water collection, treatment and supply',NULL,'',2,'E36'),(3198,'codenace','Sewerage',NULL,'',2,'E37'),(3199,'codenace','Waste collection, treatment and disposal activities; materials recovery',NULL,'',2,'E38'),(3200,'codenace','Remediation activities and other waste management services',NULL,'',2,'E39'),(3201,'codenace','CONSTRUCTION',NULL,'',2,'F00'),(3202,'codenace','Construction of buildings',NULL,'',2,'F41'),(3203,'codenace','Civil engineering',NULL,'',2,'F42'),(3204,'codenace','Specialised construction activities',NULL,'',2,'F43'),(3205,'codenace','WHOLESALE AND RETAIL TRADE; REPAIR OF MOTOR VEHICLES AND MOTORCYCLES',NULL,'',2,'G00'),(3206,'codenace','Wholesale and retail trade and repair of motor vehicles and motorcycles',NULL,'',2,'G45'),(3207,'codenace','Wholesale trade, except of motor vehicles and motorcycles',NULL,'',2,'G46'),(3208,'codenace','Retail trade, except of motor vehicles and motorcycles',NULL,'',2,'G47'),(3209,'codenace','TRANSPORTATION AND STORAGE',NULL,'',2,'H00'),(3210,'codenace','Land transport and transport via pipelines',NULL,'',2,'H49'),(3211,'codenace','Water transport',NULL,'',2,'H50'),(3212,'codenace','Air transport',NULL,'',2,'H51'),(3213,'codenace','Warehousing and support activities for transportation',NULL,'',2,'H52'),(3214,'codenace','Postal and courier activities',NULL,'',2,'H53'),(3215,'codenace','ACCOMMODATION AND FOOD SERVICE ACTIVITIES',NULL,'',2,'I00'),(3216,'codenace','Accommodation',NULL,'',2,'I55'),(3217,'codenace','Food and beverage service activities',NULL,'',2,'I56'),(3218,'codenace','INFORMATION AND COMMUNICATION',NULL,'',2,'J00'),(3219,'codenace','Publishing activities',NULL,'',2,'J58'),(3220,'codenace','Motion picture, video and television programme production, sound recording and music publishing activities',NULL,'',2,'J59'),(3221,'codenace','Programming and broadcasting activities',NULL,'',2,'J60'),(3222,'codenace','Telecommunications',NULL,'',2,'J61'),(3223,'codenace','Computer programming, consultancy and related activities',NULL,'',2,'J62'),(3224,'codenace','Information service activities',NULL,'',2,'J63'),(3225,'codenace','FINANCIAL AND INSURANCE ACTIVITIES',NULL,'',2,'K00'),(3226,'codenace','Financial service activities, except insurance and pension funding',NULL,'',2,'K64'),(3227,'codenace','Insurance, reinsurance and pension funding, except compulsory social security',NULL,'',2,'K65'),(3228,'codenace','Activities auxiliary to financial services and insurance activities',NULL,'',2,'K66'),(3229,'codenace','REAL ESTATE ACTIVITIES',NULL,'',2,'L00'),(3230,'codenace','Real estate activities',NULL,'',2,'L68'),(3231,'codenace','PROFESSIONAL, SCIENTIFIC AND TECHNICAL ACTIVITIES',NULL,'',2,'M00'),(3232,'codenace','Legal and accounting activities',NULL,'',2,'M69'),(3233,'codenace','Activities of head offices; management consultancy activities',NULL,'',2,'M70'),(3234,'codenace','Architectural and engineering activities; technical testing and analysis',NULL,'',2,'M71'),(3235,'codenace','Scientific research and development',NULL,'',2,'M72'),(3236,'codenace','Advertising and market research',NULL,'',2,'M73'),(3237,'codenace','Other professional, scientific and technical activities',NULL,'',2,'M74'),(3238,'codenace','Veterinary activities',NULL,'',2,'M75'),(3239,'codenace','ADMINISTRATIVE AND SUPPORT SERVICE ACTIVITIES',NULL,'',2,'N00'),(3240,'codenace','Rental and leasing activities',NULL,'',2,'N77'),(3241,'codenace','Employment activities',NULL,'',2,'N78'),(3242,'codenace','Travel agency, tour operator and other reservation service and related activities',NULL,'',2,'N79'),(3243,'codenace','Security and investigation activities',NULL,'',2,'N80'),(3244,'codenace','Services to buildings and landscape activities',NULL,'',2,'N81'),(3245,'codenace','Office administrative, office support and other business support activities',NULL,'',2,'N82'),(3246,'codenace','PUBLIC ADMINISTRATION AND DEFENCE; COMPULSORY SOCIAL SECURITY',NULL,'',2,'O00'),(3247,'codenace','Public administration and defence; compulsory social security',NULL,'',2,'O84'),(3248,'codenace','EDUCATION',NULL,'',2,'P00'),(3249,'codenace','Education',NULL,'',2,'P85'),(3250,'codenace','HUMAN HEALTH AND SOCIAL WORK ACTIVITIES',NULL,'',2,'Q00'),(3251,'codenace','Human health activities',NULL,'',2,'Q86'),(3252,'codenace','Residential care activities',NULL,'',2,'Q87'),(3253,'codenace','Social work activities without accommodation',NULL,'',2,'Q88'),(3254,'codenace','ARTS, ENTERTAINMENT AND RECREATION',NULL,'',2,'R00'),(3255,'codenace','Creative, arts and entertainment activities',NULL,'',2,'R90'),(3256,'codenace','Libraries, archives, museums and other cultural activities',NULL,'',2,'R91'),(3257,'codenace','Gambling and betting activities',NULL,'',2,'R92'),(3258,'codenace','Sports activities and amusement and recreation activities',NULL,'',2,'R93'),(3259,'codenace','OTHER SERVICE ACTIVITIES',NULL,'',2,'S00'),(3260,'codenace','Activities of membership organisations',NULL,'',2,'S94'),(3261,'codenace','Repair of computers and personal and household goods',NULL,'',2,'S95'),(3262,'codenace','Other personal service activities',NULL,'',2,'S96'),(3263,'codenace','Activities of households as employers of domestic personnel',NULL,'',2,'S97'),(3264,'codenace','Undifferentiated goods- and services-producing activities of private households for own use',NULL,'',2,'S98'),(3265,'codenace','ACTIVITIES OF HOUSEHOLDS AS EMPLOYERS; U0NDIFFERENTIATED GOODS- AND SERVICES-PRODUCING ACTIVITIES OF HOUSEHOLDS FOR OWN USE',NULL,'',2,'T00'),(3266,'codenace','Activities of extraterritorial organisations and bodies',NULL,'',2,'T99'),(3267,'typeaction_evt','_DIMS_PLANNING_FAIR_STEPS',NULL,'',1,''),(3268,'typeaction','_DIMS_PLANNING_FAIR_STEPS',0,'#A3FAF1',1,'');
/*!40000 ALTER TABLE `dims_mod_business_enum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_enum_liens`
--

DROP TABLE IF EXISTS `dims_mod_business_enum_liens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_enum_liens` (
  `type_parent` varchar(250) NOT NULL DEFAULT '',
  `type_enfant` varchar(250) CHARACTER SET latin1 NOT NULL DEFAULT '0',
  PRIMARY KEY (`type_parent`,`type_enfant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_enum_liens`
--

LOCK TABLES `dims_mod_business_enum_liens` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_enum_liens` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_enum_liens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_equipement`
--

DROP TABLE IF EXISTS `dims_mod_business_equipement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_equipement` (
  `code` varchar(16) DEFAULT NULL,
  `intitule` varchar(255) DEFAULT NULL,
  KEY `CODE_EQUIPEMENT` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_equipement`
--

LOCK TABLES `dims_mod_business_equipement` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_equipement` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_equipement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_event_etap`
--

DROP TABLE IF EXISTS `dims_mod_business_event_etap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_event_etap` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_action` int(10) NOT NULL DEFAULT '0',
  `label` varchar(200) DEFAULT NULL,
  `position` tinyint(4) NOT NULL,
  `type_etape` int(11) DEFAULT '0' COMMENT '0 : default; 1: resume; 2: accuse reception doc; 3: condition; 4:formulaire; 5:paiement;',
  `description` text,
  `id_model` int(11) NOT NULL DEFAULT '0',
  `condition` int(1) NOT NULL DEFAULT '0',
  `condition_content` varchar(200) DEFAULT NULL,
  `condition_label_yes` varchar(255) DEFAULT NULL,
  `condition_label_no` varchar(255) DEFAULT NULL,
  `paiement` int(1) unsigned NOT NULL DEFAULT '0',
  `date_fin` varchar(14) DEFAULT NULL,
  `label_en` varchar(200) DEFAULT NULL,
  `description_en` text,
  `condition_content_en` text,
  `condition_label_yes_en` text,
  `condition_label_no_en` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_event_etap`
--

LOCK TABLES `dims_mod_business_event_etap` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_event_etap` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_event_etap` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_event_etap_delegue`
--

DROP TABLE IF EXISTS `dims_mod_business_event_etap_delegue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_event_etap_delegue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_action` int(11) NOT NULL,
  `id_etap` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `date_inscr` varchar(14) DEFAULT NULL,
  `date_presence` varchar(14) DEFAULT NULL,
  `date_presence_fin` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_event_etap_delegue`
--

LOCK TABLES `dims_mod_business_event_etap_delegue` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_event_etap_delegue` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_event_etap_delegue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_event_etap_file`
--

DROP TABLE IF EXISTS `dims_mod_business_event_etap_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_event_etap_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_etape` int(11) NOT NULL,
  `id_action` int(11) NOT NULL,
  `id_doc` int(11) DEFAULT '0',
  `label` varchar(100) DEFAULT NULL,
  `content` text,
  `label_en` text,
  `content_en` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_event_etap_file`
--

LOCK TABLES `dims_mod_business_event_etap_file` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_event_etap_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_event_etap_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_event_etap_file_user`
--

DROP TABLE IF EXISTS `dims_mod_business_event_etap_file_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_event_etap_file_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_etape` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `id_action` int(11) NOT NULL,
  `id_doc` int(11) DEFAULT NULL,
  `valide` tinyint(1) NOT NULL DEFAULT '0',
  `id_doc_frontoffice` int(11) NOT NULL DEFAULT '0',
  `provenance` varchar(50) DEFAULT NULL,
  `date_validation` varchar(14) DEFAULT NULL,
  `date_reception` varchar(14) DEFAULT NULL,
  `invalid_content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_event_etap_file_user`
--

LOCK TABLES `dims_mod_business_event_etap_file_user` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_event_etap_file_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_event_etap_file_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_event_etap_user`
--

DROP TABLE IF EXISTS `dims_mod_business_event_etap_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_event_etap_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_etape` int(10) NOT NULL DEFAULT '0',
  `id_ee_contact` int(11) NOT NULL,
  `valide_etape` tinyint(1) NOT NULL DEFAULT '0',
  `date_validation_etape` varchar(14) DEFAULT NULL,
  `condition` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_event_etap_user`
--

LOCK TABLES `dims_mod_business_event_etap_user` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_event_etap_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_event_etap_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_event_inscription`
--

DROP TABLE IF EXISTS `dims_mod_business_event_inscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_event_inscription` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_action` int(10) unsigned NOT NULL,
  `id_contact` int(10) unsigned DEFAULT NULL,
  `validate` tinyint(3) NOT NULL DEFAULT '0',
  `lastname` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `address` text,
  `city` varchar(64) DEFAULT NULL,
  `postalcode` varchar(16) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `company` varchar(64) DEFAULT NULL,
  `function` varchar(64) DEFAULT NULL,
  `date_validate` varchar(14) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  `paiement` int(1) unsigned NOT NULL DEFAULT '0',
  `date_facturation` varchar(14) DEFAULT NULL,
  `date_paiement` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_contact`),
  KEY `id_action` (`id_action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_event_inscription`
--

LOCK TABLES `dims_mod_business_event_inscription` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_event_inscription` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_event_inscription` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_event_model`
--

DROP TABLE IF EXISTS `dims_mod_business_event_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_event_model` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_event_model`
--

LOCK TABLES `dims_mod_business_event_model` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_event_model` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_event_model` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_interlocuteur`
--

DROP TABLE IF EXISTS `dims_mod_business_interlocuteur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_interlocuteur` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `genre` varchar(16) DEFAULT NULL,
  `titre` varchar(32) DEFAULT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `nom_search` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `prenom_search` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `codepostal` varchar(10) DEFAULT NULL,
  `ville` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `telecopie` varchar(20) DEFAULT NULL,
  `telmobile` varchar(20) DEFAULT NULL,
  `mel` varchar(255) DEFAULT NULL,
  `commentaire` longtext,
  `pays` varchar(255) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `date_maj` date DEFAULT NULL,
  `id_module` int(10) unsigned DEFAULT NULL,
  `id_user` int(10) unsigned DEFAULT NULL,
  `id_workspace` int(10) unsigned DEFAULT NULL,
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  `field1` varchar(255) DEFAULT NULL,
  `timestp_field1` bigint(14) DEFAULT '0',
  `field2` varchar(255) DEFAULT NULL,
  `timestp_field2` bigint(14) DEFAULT '0',
  `field3` varchar(255) DEFAULT NULL,
  `timestp_field3` bigint(14) DEFAULT '0',
  `field4` varchar(255) DEFAULT NULL,
  `timestp_field4` bigint(14) DEFAULT '0',
  `field5` varchar(255) DEFAULT NULL,
  `timestp_field5` bigint(14) DEFAULT '0',
  `field6` varchar(255) DEFAULT NULL,
  `timestp_field6` bigint(14) DEFAULT '0',
  `field7` varchar(255) DEFAULT NULL,
  `timestp_field7` bigint(14) DEFAULT '0',
  `field8` varchar(255) DEFAULT NULL,
  `timestp_field8` bigint(14) DEFAULT '0',
  `field9` varchar(255) DEFAULT NULL,
  `timestp_field9` bigint(14) DEFAULT '0',
  `field10` varchar(255) DEFAULT NULL,
  `timestp_field10` bigint(14) DEFAULT '0',
  `field11` varchar(255) DEFAULT NULL,
  `timestp_field11` bigint(14) DEFAULT '0',
  `field12` varchar(255) DEFAULT NULL,
  `timestp_field12` bigint(14) DEFAULT '0',
  `field13` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field13` bigint(14) DEFAULT '0',
  `field14` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field14` bigint(14) DEFAULT '0',
  `field15` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field15` bigint(14) DEFAULT '0',
  `field16` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field16` bigint(14) DEFAULT '0',
  `field17` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field17` bigint(14) DEFAULT '0',
  `field18` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field18` bigint(14) DEFAULT '0',
  `field19` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field19` bigint(14) DEFAULT '0',
  `field20` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field20` bigint(14) DEFAULT '0',
  `field21` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field21` bigint(14) DEFAULT '0',
  `field22` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field22` bigint(14) DEFAULT '0',
  `field23` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field23` bigint(14) DEFAULT '0',
  `field24` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field24` bigint(14) DEFAULT '0',
  `field25` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field25` bigint(14) DEFAULT '0',
  `field26` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field26` bigint(14) DEFAULT '0',
  `field27` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field27` bigint(14) DEFAULT '0',
  `field28` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field28` bigint(14) DEFAULT '0',
  `field29` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field29` bigint(14) DEFAULT '0',
  `field30` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field30` bigint(14) DEFAULT '0',
  `field31` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field31` bigint(14) DEFAULT '0',
  `field32` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field32` bigint(14) DEFAULT '0',
  `field33` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field33` bigint(14) DEFAULT '0',
  `field34` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field34` bigint(14) DEFAULT '0',
  `field35` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field35` bigint(14) DEFAULT '0',
  `field36` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field36` bigint(14) DEFAULT '0',
  `field37` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field37` bigint(14) DEFAULT '0',
  `field38` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field38` bigint(14) DEFAULT '0',
  `field39` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field39` bigint(14) DEFAULT '0',
  `field40` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field40` bigint(14) DEFAULT '0',
  `field41` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field41` bigint(14) DEFAULT '0',
  `field42` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field42` bigint(14) DEFAULT '0',
  `field43` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field43` bigint(14) DEFAULT '0',
  `field44` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field44` bigint(14) DEFAULT '0',
  `field45` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field45` bigint(14) DEFAULT '0',
  `field46` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field46` bigint(14) DEFAULT '0',
  `field47` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field47` bigint(14) DEFAULT '0',
  `field48` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field48` bigint(14) DEFAULT '0',
  `field49` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field49` bigint(14) DEFAULT '0',
  `field50` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field50` bigint(14) DEFAULT '0',
  `field51` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field51` bigint(14) DEFAULT '0',
  `field52` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field52` bigint(14) DEFAULT '0',
  `field53` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field53` bigint(14) DEFAULT '0',
  `field54` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field54` bigint(14) DEFAULT '0',
  `field55` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field55` bigint(14) DEFAULT '0',
  `field56` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field56` bigint(14) DEFAULT '0',
  `field57` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field57` bigint(14) DEFAULT '0',
  `field58` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field58` bigint(14) DEFAULT '0',
  `field59` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field59` bigint(14) DEFAULT '0',
  `field60` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field60` bigint(14) DEFAULT '0',
  `field61` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field61` bigint(14) DEFAULT '0',
  `field62` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field62` bigint(14) DEFAULT '0',
  `field63` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field63` bigint(14) DEFAULT '0',
  `field64` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field64` bigint(14) DEFAULT '0',
  `field65` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field65` bigint(14) DEFAULT '0',
  `field66` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field66` bigint(14) DEFAULT '0',
  `field67` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field67` bigint(14) DEFAULT '0',
  `field68` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field68` bigint(14) DEFAULT '0',
  `field69` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field69` bigint(14) DEFAULT '0',
  `field70` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field70` bigint(14) DEFAULT '0',
  `field71` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field71` bigint(14) DEFAULT '0',
  `field72` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field72` bigint(14) DEFAULT '0',
  `field73` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field73` bigint(14) DEFAULT '0',
  `field74` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field74` bigint(14) DEFAULT '0',
  `field75` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field75` bigint(14) DEFAULT '0',
  `field76` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field76` bigint(14) DEFAULT '0',
  `field77` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field77` bigint(14) DEFAULT '0',
  `field78` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field78` bigint(14) DEFAULT '0',
  `field79` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field79` bigint(14) DEFAULT '0',
  `field80` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field80` bigint(14) DEFAULT '0',
  `field81` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field81` bigint(14) DEFAULT '0',
  `field82` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field82` bigint(14) DEFAULT '0',
  `field83` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field83` bigint(14) DEFAULT '0',
  `field84` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field84` bigint(14) DEFAULT '0',
  `field85` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field85` bigint(14) DEFAULT '0',
  `field86` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field86` bigint(14) DEFAULT '0',
  `field87` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field87` bigint(14) DEFAULT '0',
  `field88` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field88` bigint(14) DEFAULT '0',
  `field89` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field89` bigint(14) DEFAULT '0',
  `field90` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field90` bigint(14) DEFAULT '0',
  `field91` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field91` bigint(14) DEFAULT '0',
  `field92` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field92` bigint(14) DEFAULT '0',
  `field93` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field93` bigint(14) DEFAULT '0',
  `field94` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field94` bigint(14) DEFAULT '0',
  `field95` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field95` bigint(14) DEFAULT '0',
  `field96` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field96` bigint(14) DEFAULT '0',
  `field97` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field97` bigint(14) DEFAULT '0',
  `field98` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field98` bigint(14) DEFAULT '0',
  `field99` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field99` bigint(14) DEFAULT '0',
  `field100` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field100` bigint(14) DEFAULT '0',
  `field101` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field101` bigint(14) DEFAULT '0',
  `field102` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field102` bigint(14) DEFAULT '0',
  `field103` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field103` bigint(14) DEFAULT '0',
  `field104` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field104` bigint(14) DEFAULT '0',
  `field105` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field105` bigint(14) DEFAULT '0',
  `field106` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field106` bigint(14) DEFAULT '0',
  `field107` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field107` bigint(14) DEFAULT '0',
  `field108` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field108` bigint(14) DEFAULT '0',
  `field109` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field109` bigint(14) DEFAULT '0',
  `field110` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field110` bigint(14) DEFAULT '0',
  `field111` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field111` bigint(14) DEFAULT '0',
  `field112` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field112` bigint(14) DEFAULT '0',
  `field113` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field113` bigint(14) DEFAULT '0',
  `field114` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field114` bigint(14) DEFAULT '0',
  `field115` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field115` bigint(14) DEFAULT '0',
  `field116` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field116` bigint(14) DEFAULT '0',
  `field117` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field117` bigint(14) DEFAULT '0',
  `field118` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field118` bigint(14) DEFAULT '0',
  `field119` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field119` bigint(14) DEFAULT '0',
  `field120` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field120` bigint(14) DEFAULT '0',
  `field121` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field121` bigint(14) DEFAULT '0',
  `field122` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field122` bigint(14) DEFAULT '0',
  `field123` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field123` bigint(14) DEFAULT '0',
  `field124` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field124` bigint(14) DEFAULT '0',
  `field125` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field125` bigint(14) DEFAULT '0',
  `field126` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field126` bigint(14) DEFAULT '0',
  `field127` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field127` bigint(14) DEFAULT '0',
  `field128` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field128` bigint(14) DEFAULT '0',
  `field129` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field129` bigint(14) DEFAULT '0',
  `field130` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field130` bigint(14) DEFAULT '0',
  `field131` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field131` bigint(14) DEFAULT '0',
  `field132` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field132` bigint(14) DEFAULT '0',
  `field133` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field133` bigint(14) DEFAULT '0',
  `field134` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field134` bigint(14) DEFAULT '0',
  `field135` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field135` bigint(14) DEFAULT '0',
  `field136` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field136` bigint(14) DEFAULT '0',
  `field137` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field137` bigint(14) DEFAULT '0',
  `field138` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field138` bigint(14) DEFAULT '0',
  `field139` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field139` bigint(14) DEFAULT '0',
  `field140` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field140` bigint(14) DEFAULT '0',
  `field141` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field141` bigint(14) DEFAULT '0',
  `field142` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field142` bigint(14) DEFAULT '0',
  `field143` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field143` bigint(14) DEFAULT '0',
  `field144` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field144` bigint(14) DEFAULT '0',
  `field145` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field145` bigint(14) DEFAULT '0',
  `field146` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field146` bigint(14) DEFAULT '0',
  `field147` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field147` bigint(14) DEFAULT '0',
  `field148` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field148` bigint(14) DEFAULT '0',
  `field149` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field149` bigint(14) DEFAULT '0',
  `field150` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field150` bigint(14) DEFAULT '0',
  `field151` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field151` bigint(14) DEFAULT '0',
  `field152` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field152` bigint(14) DEFAULT '0',
  `field153` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field153` bigint(14) DEFAULT '0',
  `field154` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field154` bigint(14) DEFAULT '0',
  `field155` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field155` bigint(14) DEFAULT '0',
  `field156` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field156` bigint(14) DEFAULT '0',
  `field157` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field157` bigint(14) DEFAULT '0',
  `field158` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field158` bigint(14) DEFAULT '0',
  `field159` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field159` bigint(14) DEFAULT '0',
  `field160` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field160` bigint(14) DEFAULT '0',
  `field161` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field161` bigint(14) DEFAULT '0',
  `field162` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field162` bigint(14) DEFAULT '0',
  `field163` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field163` bigint(14) DEFAULT '0',
  `field164` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field164` bigint(14) DEFAULT '0',
  `field165` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field165` bigint(14) DEFAULT '0',
  `field166` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field166` bigint(14) DEFAULT '0',
  `field167` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field167` bigint(14) DEFAULT '0',
  `field168` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field168` bigint(14) DEFAULT '0',
  `field169` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field169` bigint(14) DEFAULT '0',
  `field170` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field170` bigint(14) DEFAULT '0',
  `field171` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field171` bigint(14) DEFAULT '0',
  `field172` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field172` bigint(14) DEFAULT '0',
  `field173` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field173` bigint(14) DEFAULT '0',
  `field174` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field174` bigint(14) DEFAULT '0',
  `field175` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field175` bigint(14) DEFAULT '0',
  `field176` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field176` bigint(14) DEFAULT '0',
  `field177` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field177` bigint(14) DEFAULT '0',
  `field178` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field178` bigint(14) DEFAULT '0',
  `field179` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field179` bigint(14) DEFAULT '0',
  `field180` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field180` bigint(14) DEFAULT '0',
  `field181` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field181` bigint(14) DEFAULT '0',
  `field182` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field182` bigint(14) DEFAULT '0',
  `field183` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field183` bigint(14) DEFAULT '0',
  `field184` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field184` bigint(14) DEFAULT '0',
  `field185` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field185` bigint(14) DEFAULT '0',
  `field186` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field186` bigint(14) DEFAULT '0',
  `field187` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field187` bigint(14) DEFAULT '0',
  `field188` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field188` bigint(14) DEFAULT '0',
  `field189` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field189` bigint(14) DEFAULT '0',
  `field190` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field190` bigint(14) DEFAULT '0',
  `field191` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field191` bigint(14) DEFAULT '0',
  `field192` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field192` bigint(14) DEFAULT '0',
  `field193` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field193` bigint(14) DEFAULT '0',
  `field194` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field194` bigint(14) DEFAULT '0',
  `field195` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field195` bigint(14) DEFAULT '0',
  `field196` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field196` bigint(14) DEFAULT '0',
  `field197` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field197` bigint(14) DEFAULT '0',
  `field198` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field198` bigint(14) DEFAULT '0',
  `field199` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field199` bigint(14) DEFAULT '0',
  `field200` varchar(255) CHARACTER SET latin1 NOT NULL,
  `timestp_field200` bigint(14) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_interlocuteur`
--

LOCK TABLES `dims_mod_business_interlocuteur` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_interlocuteur` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_interlocuteur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_interlocuteur_categorie`
--

DROP TABLE IF EXISTS `dims_mod_business_interlocuteur_categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_interlocuteur_categorie` (
  `id_interlocuteur` int(10) unsigned DEFAULT '0',
  `categorie` varchar(64) DEFAULT NULL,
  KEY `id_interlocuteur` (`id_interlocuteur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_interlocuteur_categorie`
--

LOCK TABLES `dims_mod_business_interlocuteur_categorie` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_interlocuteur_categorie` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_interlocuteur_categorie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_list_profil`
--

DROP TABLE IF EXISTS `dims_mod_business_list_profil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_list_profil` (
  `id_profil` int(4) NOT NULL,
  `id_mailing` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_list_profil`
--

LOCK TABLES `dims_mod_business_list_profil` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_list_profil` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_list_profil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_meta_categ`
--

DROP TABLE IF EXISTS `dims_mod_business_meta_categ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_meta_categ` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `label` varchar(200) DEFAULT NULL,
  `position` tinyint(4) NOT NULL,
  `admin` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_meta_categ`
--

LOCK TABLES `dims_mod_business_meta_categ` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_meta_categ` DISABLE KEYS */;
INSERT INTO `dims_mod_business_meta_categ` VALUES (1,'COORDONNEES',2,1),(2,'INFOS GENERALES',3,1),(3,'IDENTITE',1,1),(6,'INFOS FINANCIERES',4,0),(7,'INFOS JURIDIQUES',5,0);
/*!40000 ALTER TABLE `dims_mod_business_meta_categ` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_meta_field`
--

DROP TABLE IF EXISTS `dims_mod_business_meta_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_meta_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_object` int(10) unsigned DEFAULT '0',
  `name` varchar(150) DEFAULT NULL,
  `fieldname` int(255) NOT NULL,
  `separator` tinyint(1) unsigned DEFAULT '0',
  `separator_level` int(10) unsigned DEFAULT '0',
  `separator_fontsize` int(10) unsigned DEFAULT '0',
  `type` varchar(16) DEFAULT NULL,
  `format` varchar(16) DEFAULT NULL,
  `values` longtext CHARACTER SET latin1,
  `description` longtext,
  `position` int(10) unsigned DEFAULT '0',
  `maxlength` int(10) unsigned DEFAULT '0',
  `cols` int(10) unsigned DEFAULT '0',
  `option_needed` tinyint(1) unsigned DEFAULT '0',
  `option_search` tinyint(4) NOT NULL DEFAULT '0',
  `option_arrayview` tinyint(1) unsigned DEFAULT '1',
  `option_exportview` tinyint(1) unsigned DEFAULT '1',
  `option_cmsgroupby` tinyint(1) unsigned DEFAULT '0',
  `option_cmsorderby` tinyint(1) unsigned DEFAULT '0',
  `option_cmsdisplaylabel` tinyint(1) unsigned DEFAULT '0',
  `option_cmsshowfilter` tinyint(1) unsigned DEFAULT '0',
  `defaultvalue` varchar(255) DEFAULT NULL,
  `interline` int(10) unsigned NOT NULL DEFAULT '0',
  `id_metacateg` int(11) NOT NULL DEFAULT '0',
  `used` tinyint(1) NOT NULL DEFAULT '1',
  `id_mbfield` int(11) NOT NULL DEFAULT '0',
  `enum` varchar(200) DEFAULT NULL,
  `mode` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_object` (`id_object`),
  KEY `id_metacateg` (`id_metacateg`)
) ENGINE=MyISAM AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_meta_field`
--

LOCK TABLES `dims_mod_business_meta_field` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_meta_field` DISABLE KEYS */;
INSERT INTO `dims_mod_business_meta_field` VALUES (32,7,'Importance du contact',0,0,0,0,'select','string','Normal||Important||Very important','',3,0,1,0,0,0,1,0,0,0,0,'',0,2,1,756,'',1),(33,7,'Carte de voeux',3,0,0,0,'radio','string','oui||non','',7,0,2,0,0,1,1,0,0,0,0,'',0,2,1,800,'',1),(34,7,'Cadeaux',4,0,0,0,'radio','string','oui||non','',8,0,2,0,0,1,1,0,0,0,0,'',0,2,1,801,'',1),(27,7,'Code Postal',0,0,0,0,'text','string','','',3,10,1,0,1,0,1,0,0,0,0,'',0,1,1,758,'',1),(39,7,'Tel portable',0,0,0,0,'text','string','','',11,0,1,0,0,1,1,0,0,0,0,'',0,1,1,762,'',1),(99,6,'Financial support received',3,0,0,0,'select','string','Yes||No','',1,0,1,0,0,0,1,0,0,0,0,'Non',0,6,1,814,'',1),(26,7,'Adresse',0,0,0,0,'text','string','','',1,255,1,0,1,0,1,0,0,0,0,'',0,1,1,757,'',1),(28,7,'Ville',0,0,0,0,'text','string','','',4,200,1,0,1,0,1,0,0,0,0,'',0,1,1,759,'',1),(29,7,'Pays',0,0,0,0,'select','string','','',6,0,1,0,1,0,1,0,0,0,0,'',0,1,1,765,'pays',1),(30,7,'Tel. Bureau',0,0,0,0,'text','string','','',7,25,1,0,0,0,1,0,0,0,0,'',0,1,1,760,'',1),(31,7,'Fax Bureau',0,0,0,0,'text','string','','',10,14,1,0,0,1,1,0,0,0,0,'',0,1,1,761,'',1),(35,7,'Suivi ECOGEST',5,0,0,0,'radio','string','oui||non','',9,0,2,0,0,1,1,0,0,0,0,'',0,2,1,802,'',1),(36,7,'Motivation',1,0,0,0,'textarea','string','','',5,0,1,0,0,1,1,0,0,0,0,'',0,2,1,798,'',1),(37,7,'Position',2,0,0,0,'textarea','string','','',6,0,1,0,0,1,1,0,0,0,0,'',0,2,1,799,'',1),(46,7,'Creation project',6,0,0,0,'radio','string','Yes||No','',10,0,2,0,0,0,1,0,0,0,0,'',0,2,1,803,'',1),(98,6,'Geographical area of interest',2,0,0,0,'text','string','','',4,0,1,0,0,0,1,0,0,0,0,'',0,2,1,813,'',1),(97,6,'Current projects',1,0,0,0,'textarea','string','','',5,0,1,0,0,0,1,0,0,0,0,'',0,2,1,812,'',1),(53,7,'firstname',0,0,0,0,'text','string','','',3,0,1,1,0,0,1,0,0,0,0,'',0,3,1,751,'',0),(54,7,'lastname',0,0,0,0,'text','string','','',2,0,1,1,0,0,1,0,0,0,0,'',0,3,1,750,'',0),(55,6,'intitule',0,0,0,0,'text','string','','',1,0,1,1,0,0,1,0,0,0,0,'',0,3,1,768,'',0),(56,7,'professional',0,0,0,0,'select','string','','',2,0,1,0,0,0,1,0,0,0,0,'',0,2,1,766,'codenace',1),(59,7,'email',0,0,0,0,'text','email','','',12,0,1,0,0,0,1,0,0,0,0,'',0,1,1,763,'',1),(88,7,'Photo',0,0,0,0,'file','string','','',6,0,1,0,0,0,0,0,0,0,0,'',0,3,1,790,'',0),(89,7,'Event partner',8,0,0,0,'select','string','Yes||No','',4,0,1,0,0,0,1,0,0,0,0,'',0,2,1,810,'',1),(87,7,'Phone 2',0,0,0,0,'text','string','','',7,25,1,0,0,0,1,0,0,0,0,'',0,1,1,789,'',1),(66,7,'pers_phone',0,0,0,0,'text','string','','',9,0,1,0,0,0,1,0,0,0,0,'',0,1,1,755,'',1),(67,7,'nationalite',0,0,0,0,'text','string','','',5,0,1,0,0,0,1,0,0,0,0,'',0,3,1,754,'',0),(68,7,'sexe',0,0,0,0,'select','string','M||F','',4,0,1,0,0,0,1,0,0,0,0,'',0,3,1,753,'',0),(69,7,'civilite',0,0,0,0,'select','string','Mr.||Mrs.||Ms.||Dr||Pr||Me||S.E.||S.A.R.||S.E.||S.H.||S.M.','',1,0,1,1,0,0,1,0,0,0,0,'',0,3,1,752,'',0),(70,7,'email2',0,0,0,0,'text','email','','',13,0,1,0,0,0,1,0,0,0,0,'',0,1,1,786,'',1),(71,7,'email3',0,0,0,0,'text','email','','',14,0,1,0,0,0,1,0,0,0,0,'',0,1,1,787,'',1),(72,6,'presentation',0,0,0,0,'textarea','string','','',2,0,1,0,0,0,1,0,0,0,0,'',0,2,1,779,'',1),(73,6,'dirigeant',0,0,0,0,'text','string','','',2,0,1,0,0,0,1,0,0,0,0,'',0,3,1,778,'',0),(74,6,'pays',0,0,0,0,'select','string','','',4,0,1,0,0,0,1,0,0,0,0,'',0,1,1,777,'pays',1),(75,6,'ent_activiteprincipale',0,0,0,0,'select','string','','',3,0,1,0,0,0,1,0,0,0,0,'',0,2,1,776,'codenace',1),(76,6,'ent_capital',0,0,0,0,'text','string','','',1,0,1,0,0,0,1,0,0,0,0,'',0,2,1,775,'',1),(77,6,'site_web',0,0,0,0,'text','string','','',7,0,1,0,0,0,1,0,0,0,0,'',0,1,1,774,'',1),(78,6,'telecopie',0,0,0,0,'text','string','','',6,0,1,0,0,0,1,0,0,0,0,'',0,1,1,773,'',1),(79,6,'telephone',0,0,0,0,'text','string','','',5,0,1,0,0,0,1,0,0,0,0,'',0,1,1,772,'',1),(80,6,'ville',0,0,0,0,'text','string','','',3,0,1,0,1,0,1,0,0,0,0,'',0,1,1,771,'',1),(81,6,'codepostal',0,0,0,0,'text','string','','',2,0,1,0,1,0,1,0,0,0,0,'',0,1,1,770,'',1),(82,6,'adresse',0,0,0,0,'text','string','','',1,0,1,0,0,0,1,0,0,0,0,'',0,1,1,769,'',1),(95,7,'Already visited Luxembourg',7,0,0,0,'radio','string','Yes||No','',1,0,2,0,0,0,1,0,0,0,0,'',0,2,1,809,'',1),(100,6,'Stock exchange link',4,0,0,0,'text','url','','',2,0,1,0,0,0,1,0,0,0,0,'',0,6,1,815,'',1),(101,6,'Photo',5,0,0,0,'file','string','','',3,0,1,0,0,0,0,0,0,0,0,'',0,3,1,816,'',0),(105,7,'Address 2',9,0,0,0,'text','string','','',2,0,1,0,0,0,1,0,0,0,0,'',0,1,1,820,'',1),(106,7,'State',10,0,0,0,'text','string','','',5,0,1,0,0,0,1,0,0,0,0,'',0,1,1,821,'',1);
/*!40000 ALTER TABLE `dims_mod_business_meta_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_meta_use`
--

DROP TABLE IF EXISTS `dims_mod_business_meta_use`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_meta_use` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_metafield` int(11) NOT NULL,
  `id_object` int(11) NOT NULL,
  `sharemode` tinyint(4) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=262 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_meta_use`
--

LOCK TABLES `dims_mod_business_meta_use` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_meta_use` DISABLE KEYS */;
INSERT INTO `dims_mod_business_meta_use` VALUES (47,23,6,1,5),(5,13,7,1,62),(4,13,7,1,5),(7,16,7,1,5),(26,17,7,1,64),(25,17,7,1,63),(56,20,7,2,0),(12,18,7,1,61),(14,21,7,2,0),(45,10,7,1,64),(44,10,7,1,63),(43,10,7,1,62),(24,17,7,1,61),(23,17,7,1,62),(27,12,7,2,0),(35,11,7,1,63),(34,11,7,1,61),(33,11,7,1,62),(32,11,7,1,5),(36,11,7,1,64),(42,10,7,1,5),(48,23,6,1,63),(49,23,6,1,64),(50,24,6,1,5),(51,24,6,1,62),(52,24,6,1,63),(96,25,6,1,62),(95,25,6,1,5),(72,28,7,2,0),(59,29,7,2,0),(60,30,7,2,0),(61,31,7,2,0),(197,36,7,1,64),(63,33,7,1,62),(64,34,7,1,62),(65,35,7,1,62),(70,37,7,1,64),(195,36,7,1,66),(88,38,7,1,62),(196,36,7,1,65),(189,87,7,2,0),(213,95,7,1,62),(194,36,7,1,63),(193,36,7,1,61),(192,36,7,1,67),(191,36,7,1,62),(190,36,7,1,5),(89,38,7,1,61),(90,38,7,1,63),(91,38,7,1,65),(92,38,7,1,64),(97,25,6,1,61),(98,25,6,1,63),(99,25,6,1,65),(100,25,6,1,64),(181,27,7,2,0),(184,88,7,2,0),(182,26,7,2,0),(121,44,6,1,62),(119,43,6,1,62),(118,43,6,1,5),(120,43,6,1,64),(122,44,6,1,64),(123,41,6,1,62),(124,41,6,1,61),(125,41,6,1,63),(126,41,6,1,65),(127,41,6,1,64),(128,42,6,1,62),(129,42,6,1,61),(130,42,6,1,63),(131,42,6,1,65),(132,42,6,1,64),(200,46,7,1,64),(199,46,7,1,65),(198,46,7,1,62),(144,48,7,2,0),(145,49,7,2,0),(146,50,7,2,0),(147,51,7,2,0),(148,52,7,2,0),(183,53,7,2,0),(150,54,7,2,0),(151,55,6,2,0),(188,56,7,2,0),(153,57,7,2,0),(154,58,7,2,0),(155,59,7,2,0),(156,60,7,2,0),(157,61,7,2,0),(158,62,7,2,0),(159,63,7,2,0),(160,64,7,2,0),(161,65,7,2,0),(162,66,7,2,0),(163,67,7,2,0),(165,69,7,2,0),(166,70,7,2,0),(207,97,6,2,0),(168,72,6,2,0),(169,73,6,2,0),(170,74,6,2,0),(171,75,6,2,0),(172,76,6,2,0),(173,77,6,2,0),(174,78,6,2,0),(175,79,6,2,0),(176,80,6,2,0),(177,81,6,2,0),(178,82,6,2,0),(179,83,6,2,0),(180,39,7,2,0),(206,71,7,2,0),(208,98,6,2,0),(209,99,6,2,0),(210,100,6,2,0),(211,101,6,2,0),(214,95,7,1,67),(215,95,7,1,61),(216,95,7,1,63),(217,95,7,1,66),(218,95,7,1,65),(219,95,7,1,64),(220,95,7,1,72),(221,95,7,1,73),(258,32,7,2,0),(231,102,7,1,71),(261,106,7,2,0),(260,105,7,2,0),(259,89,7,2,0),(242,103,7,1,71),(257,68,7,2,0),(254,104,7,1,71),(255,104,7,1,75),(256,104,7,1,76);
/*!40000 ALTER TABLE `dims_mod_business_meta_use` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_netlor_domain`
--

DROP TABLE IF EXISTS `dims_mod_business_netlor_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_netlor_domain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_server` int(10) unsigned NOT NULL DEFAULT '0',
  `id_client` int(10) unsigned NOT NULL DEFAULT '0',
  `domain` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_server` (`id_server`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_netlor_domain`
--

LOCK TABLES `dims_mod_business_netlor_domain` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_netlor_domain` DISABLE KEYS */;
INSERT INTO `dims_mod_business_netlor_domain` VALUES (2,1,168,'netlor.fr'),(5,1,425,'baliston.com'),(6,1,392,'infinirouge.com'),(7,1,365,'c2isante.com'),(8,1,498,'arcessor.fr'),(9,1,365,'elan-medical.com'),(10,1,254,'rosepassion.fr');
/*!40000 ALTER TABLE `dims_mod_business_netlor_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_netlor_server`
--

DROP TABLE IF EXISTS `dims_mod_business_netlor_server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_netlor_server` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('ftp','mail') CHARACTER SET latin1 NOT NULL DEFAULT 'ftp',
  `dbname` varchar(50) DEFAULT NULL,
  `address` varchar(15) DEFAULT NULL,
  `login` varchar(25) DEFAULT NULL,
  `passwd` varchar(25) DEFAULT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_netlor_server`
--

LOCK TABLES `dims_mod_business_netlor_server` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_netlor_server` DISABLE KEYS */;
INSERT INTO `dims_mod_business_netlor_server` VALUES (2,'mail','maildb','localhost','root','*sps*',1);
/*!40000 ALTER TABLE `dims_mod_business_netlor_server` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_params`
--

DROP TABLE IF EXISTS `dims_mod_business_params`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_params` (
  `param` varchar(64) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`param`),
  UNIQUE KEY `param` (`param`),
  KEY `param_2` (`param`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_params`
--

LOCK TABLES `dims_mod_business_params` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_params` DISABLE KEYS */;
INSERT INTO `dims_mod_business_params` VALUES ('exercice','2008'),('datedeb','2008-01-01'),('datefin','2008-12-31'),('conditionpaiement','Virement ou chèque à réception de facture'),('pays','France');
/*!40000 ALTER TABLE `dims_mod_business_params` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_produit`
--

DROP TABLE IF EXISTS `dims_mod_business_produit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_produit` (
  `reference` varchar(16) NOT NULL DEFAULT '',
  `libelle` varchar(255) DEFAULT NULL,
  `libelle_search` varchar(255) DEFAULT NULL,
  `description` longtext,
  `prix_achat` double DEFAULT '0',
  `prix_vente` double DEFAULT NULL,
  `stock` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`reference`),
  UNIQUE KEY `reference` (`reference`),
  KEY `reference_2` (`reference`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_produit`
--

LOCK TABLES `dims_mod_business_produit` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_produit` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_produit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_profil`
--

DROP TABLE IF EXISTS `dims_mod_business_profil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_profil` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_profil`
--

LOCK TABLES `dims_mod_business_profil` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_profil` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_profil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_suivi`
--

DROP TABLE IF EXISTS `dims_mod_business_suivi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_suivi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(32) NOT NULL DEFAULT '',
  `exercice` varchar(32) NOT NULL DEFAULT '',
  `libelle` varchar(255) DEFAULT NULL,
  `description` longtext,
  `datejour` date NOT NULL DEFAULT '0000-00-00',
  `montantht` double NOT NULL DEFAULT '0',
  `montanttva` double NOT NULL DEFAULT '0',
  `remise` double NOT NULL DEFAULT '0',
  `montantttc` double NOT NULL DEFAULT '0',
  `tauxtva` double NOT NULL DEFAULT '0',
  `solde` double DEFAULT NULL,
  `periode` varchar(10) DEFAULT NULL,
  `dossier_id` int(10) unsigned NOT NULL DEFAULT '0',
  `tiers_id` int(10) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`exercice`,`type`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_suivi`
--

LOCK TABLES `dims_mod_business_suivi` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_suivi` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_suivi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_suivi_detail`
--

DROP TABLE IF EXISTS `dims_mod_business_suivi_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_suivi_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `suivi_id` int(10) unsigned DEFAULT '0',
  `suivi_type` varchar(32) DEFAULT NULL,
  `suivi_exercice` varchar(32) DEFAULT NULL,
  `code` varchar(32) DEFAULT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `description` longtext,
  `pu` double DEFAULT NULL,
  `qte` double DEFAULT NULL,
  `tauxtva` double DEFAULT NULL,
  `position` int(10) unsigned DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_user` int(10) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_suivi_detail`
--

LOCK TABLES `dims_mod_business_suivi_detail` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_suivi_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_suivi_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_tiers`
--

DROP TABLE IF EXISTS `dims_mod_business_tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_tiers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_creation` bigint(14) DEFAULT '0',
  `date_maj` date DEFAULT NULL,
  `intitule` varchar(255) DEFAULT NULL,
  `intitule_search` varchar(255) DEFAULT NULL,
  `abrege` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `codepostal` varchar(16) DEFAULT NULL,
  `ville` varchar(64) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `telecopie` varchar(20) DEFAULT NULL,
  `telmobile` varchar(20) DEFAULT NULL,
  `mel` varchar(255) DEFAULT NULL,
  `internet` varchar(255) DEFAULT NULL,
  `heberge` varchar(4) DEFAULT NULL,
  `orig_contact` varchar(255) DEFAULT NULL,
  `ent_siren` varchar(20) DEFAULT NULL,
  `ent_capital` varchar(20) DEFAULT NULL,
  `ent_activiteprincipale` varchar(255) DEFAULT NULL,
  `ent_effectif` int(10) unsigned DEFAULT NULL,
  `ent_effectif_annee` int(10) DEFAULT NULL,
  `ent_ca` int(10) unsigned DEFAULT NULL,
  `ent_ca_annee` int(10) DEFAULT NULL,
  `ent_statut` varchar(64) DEFAULT NULL,
  `ent_hebergee` varchar(3) DEFAULT NULL,
  `ent_datecreation` varchar(10) DEFAULT NULL,
  `ent_associes` varchar(255) DEFAULT NULL,
  `commentaire` longtext,
  `commentaire_search` longtext,
  `motscles` varchar(255) DEFAULT NULL,
  `motscles_search` varchar(255) DEFAULT NULL,
  `typeclient` varchar(255) DEFAULT NULL,
  `pays` varchar(255) DEFAULT NULL,
  `ent_codenace` varchar(255) DEFAULT NULL,
  `ent_statutjuridique` varchar(255) DEFAULT NULL,
  `id_user_create` int(10) NOT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `dirigeant` varchar(255) DEFAULT NULL,
  `presentation` longtext,
  `partenaire` tinyint(1) NOT NULL,
  `id_module` int(10) unsigned DEFAULT NULL,
  `id_user` int(10) unsigned DEFAULT NULL,
  `id_workspace` int(10) unsigned DEFAULT NULL,
  `cre_heb_libreservice` varchar(3) DEFAULT NULL,
  `cre_heb_bureau` varchar(3) DEFAULT NULL,
  `cre_heb_pepiniere` varchar(3) DEFAULT NULL,
  `cre_autresservices` varchar(255) DEFAULT NULL,
  `cre_datenaissance` date NOT NULL DEFAULT '0000-00-00',
  `cre_origine_univ` varchar(255) DEFAULT NULL,
  `cre_diplome` varchar(255) DEFAULT NULL,
  `cre_exper_prof` varchar(255) DEFAULT NULL,
  `cre_activite` varchar(255) DEFAULT NULL,
  `cre_issu_recherche` varchar(3) DEFAULT NULL,
  `cre_statut_createur` varchar(64) DEFAULT NULL,
  `id_tiers` int(10) unsigned DEFAULT '0',
  `resp` varchar(5) DEFAULT NULL,
  `inactif` tinyint(1) DEFAULT '0',
  `origine_contact` varchar(64) DEFAULT NULL,
  `origine_contact_detail` varchar(64) DEFAULT NULL,
  `photo` varchar(14) DEFAULT NULL,
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  `field1` varchar(255) DEFAULT NULL,
  `field2` varchar(255) DEFAULT NULL,
  `field3` varchar(255) DEFAULT NULL,
  `field4` varchar(255) DEFAULT NULL,
  `field5` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_tiers`
--

LOCK TABLES `dims_mod_business_tiers` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_tiers` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_tiers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_tiers_competence`
--

DROP TABLE IF EXISTS `dims_mod_business_tiers_competence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_tiers_competence` (
  `tiers_id` int(10) DEFAULT NULL,
  `competence_code` varchar(16) DEFAULT NULL,
  KEY `competence_code` (`competence_code`),
  KEY `tiers_id` (`tiers_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_tiers_competence`
--

LOCK TABLES `dims_mod_business_tiers_competence` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_tiers_competence` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_tiers_competence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_tiers_contact`
--

DROP TABLE IF EXISTS `dims_mod_business_tiers_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_tiers_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tiers` int(11) DEFAULT '0',
  `id_contact` int(11) DEFAULT '0',
  `type_lien` varchar(50) DEFAULT NULL,
  `service` varchar(255) DEFAULT NULL,
  `function` varchar(100) DEFAULT NULL,
  `departement` varchar(150) DEFAULT NULL,
  `id_workspace` int(10) unsigned DEFAULT NULL,
  `id_user` int(10) unsigned DEFAULT NULL,
  `date_create` bigint(14) NOT NULL,
  `link_since` bigint(14) NOT NULL,
  `link_level` int(1) NOT NULL,
  `id_ct_user_create` varchar(11) DEFAULT NULL,
  `date_deb` bigint(14) NOT NULL,
  `date_fin` bigint(14) NOT NULL,
  `commentaire` text,
  PRIMARY KEY (`id`),
  KEY `tiers_id` (`id_tiers`),
  KEY `interlocuteur_id` (`id_contact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_tiers_contact`
--

LOCK TABLES `dims_mod_business_tiers_contact` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_tiers_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_tiers_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_tiers_dossier`
--

DROP TABLE IF EXISTS `dims_mod_business_tiers_dossier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_tiers_dossier` (
  `interlocuteur_id` int(3) unsigned NOT NULL DEFAULT '0',
  `dossier_id` int(3) unsigned NOT NULL DEFAULT '0',
  `tiers_id` int(3) unsigned NOT NULL DEFAULT '0',
  KEY `interlocuteur_id` (`interlocuteur_id`),
  KEY `dossier_id` (`dossier_id`),
  KEY `tiers_id` (`tiers_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_tiers_dossier`
--

LOCK TABLES `dims_mod_business_tiers_dossier` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_tiers_dossier` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_tiers_dossier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_tiers_equipement`
--

DROP TABLE IF EXISTS `dims_mod_business_tiers_equipement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_tiers_equipement` (
  `tiers_id` int(10) DEFAULT NULL,
  `equipement_code` varchar(16) DEFAULT NULL,
  KEY `tiers_id` (`tiers_id`),
  KEY `equipement_code` (`equipement_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_tiers_equipement`
--

LOCK TABLES `dims_mod_business_tiers_equipement` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_tiers_equipement` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_tiers_equipement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_tiers_field`
--

DROP TABLE IF EXISTS `dims_mod_business_tiers_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_tiers_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tiers` int(11) NOT NULL,
  `id_metafield` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `value` text,
  `timestp_modify` bigint(14) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `lastmodify` tinyint(1) NOT NULL DEFAULT '0',
  `id_lang` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_contact` (`id_tiers`),
  KEY `id_contact_2` (`id_tiers`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_tiers_field`
--

LOCK TABLES `dims_mod_business_tiers_field` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_tiers_field` DISABLE KEYS */;
INSERT INTO `dims_mod_business_tiers_field` VALUES (1,550,25,5,2,1,'test',20090514142552,0,0,1),(2,550,23,5,2,1,'test',20090520153643,0,1,1),(3,550,24,5,2,1,'test',20090520153643,0,1,1),(4,550,25,5,2,1,'test3',20090520153643,0,1,1),(5,575,43,62,2,1,'Oui',20090723235458,0,1,1),(6,575,43,5,112,1,'Oui',20090723235917,0,0,1),(7,569,43,5,112,1,'Oui',20090724000225,0,1,1),(8,569,41,5,112,1,'Maroc',20090724000558,0,1,1);
/*!40000 ALTER TABLE `dims_mod_business_tiers_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_tiers_import`
--

DROP TABLE IF EXISTS `dims_mod_business_tiers_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_tiers_import` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `date_creation` varchar(14) DEFAULT NULL,
  `date_maj` varchar(14) DEFAULT NULL,
  `intitule` varchar(255) DEFAULT NULL,
  `intitule_search` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `codepostal` varchar(16) DEFAULT NULL,
  `ville` varchar(64) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `telecopie` varchar(20) DEFAULT NULL,
  `mel` varchar(255) DEFAULT NULL,
  `ent_capital` varchar(20) DEFAULT NULL,
  `ent_activiteprincipale` varchar(255) DEFAULT NULL,
  `ent_effectif` int(10) unsigned DEFAULT NULL,
  `ent_datecreation` varchar(14) DEFAULT NULL,
  `pays` varchar(255) DEFAULT NULL,
  `ent_codenace` varchar(255) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `dirigeant` varchar(255) DEFAULT NULL,
  `presentation` longtext,
  `id_module` int(10) unsigned DEFAULT NULL,
  `id_user` int(10) unsigned DEFAULT NULL,
  `id_workspace` int(10) unsigned DEFAULT NULL,
  `exist` varchar(255) DEFAULT NULL,
  `id_ct_create` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_tiers_import`
--

LOCK TABLES `dims_mod_business_tiers_import` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_tiers_import` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_tiers_import` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_tiers_interlocuteur`
--

DROP TABLE IF EXISTS `dims_mod_business_tiers_interlocuteur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_tiers_interlocuteur` (
  `tiers_id` int(5) unsigned NOT NULL DEFAULT '0',
  `interlocuteur_id` int(5) unsigned NOT NULL DEFAULT '0',
  `service` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `telecopie` varchar(20) DEFAULT NULL,
  `telmobile` varchar(20) DEFAULT NULL,
  `mel` varchar(255) DEFAULT NULL,
  `fonction` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `codepostal` varchar(16) DEFAULT NULL,
  `ville` varchar(64) DEFAULT NULL,
  `pays` varchar(64) DEFAULT NULL,
  `id_module` int(10) unsigned DEFAULT NULL,
  `id_workspace` int(10) unsigned DEFAULT NULL,
  `id_user` int(10) unsigned DEFAULT NULL,
  KEY `tiers_id` (`tiers_id`),
  KEY `interlocuteur_id` (`interlocuteur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_tiers_interlocuteur`
--

LOCK TABLES `dims_mod_business_tiers_interlocuteur` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_tiers_interlocuteur` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_tiers_interlocuteur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_tiers_layer`
--

DROP TABLE IF EXISTS `dims_mod_business_tiers_layer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_tiers_layer` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `type_layer` tinyint(4) NOT NULL,
  `id_layer` int(11) NOT NULL DEFAULT '0',
  `date_creation` bigint(14) DEFAULT '0',
  `date_maj` date DEFAULT NULL,
  `intitule` varchar(255) DEFAULT NULL,
  `intitule_search` varchar(255) DEFAULT NULL,
  `abrege` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `codepostal` varchar(16) DEFAULT NULL,
  `ville` varchar(64) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `telecopie` varchar(20) DEFAULT NULL,
  `telmobile` varchar(20) DEFAULT NULL,
  `mel` varchar(255) DEFAULT NULL,
  `internet` varchar(255) DEFAULT NULL,
  `heberge` varchar(4) DEFAULT NULL,
  `orig_contact` varchar(255) DEFAULT NULL,
  `ent_siren` varchar(20) DEFAULT NULL,
  `ent_capital` varchar(20) DEFAULT NULL,
  `ent_activiteprincipale` varchar(255) DEFAULT NULL,
  `ent_effectif` int(10) unsigned DEFAULT NULL,
  `ent_effectif_annee` int(10) DEFAULT NULL,
  `ent_ca` int(10) unsigned DEFAULT NULL,
  `ent_ca_annee` int(10) DEFAULT NULL,
  `ent_statut` varchar(64) DEFAULT NULL,
  `ent_hebergee` varchar(3) DEFAULT NULL,
  `ent_datecreation` varchar(10) DEFAULT NULL,
  `ent_associes` varchar(255) DEFAULT NULL,
  `commentaire` longtext,
  `commentaire_search` longtext,
  `motscles` varchar(255) DEFAULT NULL,
  `motscles_search` varchar(255) DEFAULT NULL,
  `typeclient` varchar(255) DEFAULT NULL,
  `pays` varchar(255) DEFAULT NULL,
  `ent_codenace` varchar(255) DEFAULT NULL,
  `ent_statutjuridique` varchar(255) DEFAULT NULL,
  `id_user_create` int(10) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `dirigeant` varchar(255) DEFAULT NULL,
  `presentation` longtext,
  `partenaire` tinyint(1) DEFAULT NULL,
  `id_module` int(10) unsigned DEFAULT NULL,
  `id_user` int(10) unsigned DEFAULT NULL,
  `id_workspace` int(10) unsigned DEFAULT NULL,
  `cre_heb_libreservice` varchar(3) DEFAULT NULL,
  `cre_heb_bureau` varchar(3) DEFAULT NULL,
  `cre_heb_pepiniere` varchar(3) DEFAULT NULL,
  `cre_autresservices` varchar(255) DEFAULT NULL,
  `cre_datenaissance` date NOT NULL DEFAULT '0000-00-00',
  `cre_origine_univ` varchar(255) DEFAULT NULL,
  `cre_diplome` varchar(255) DEFAULT NULL,
  `cre_exper_prof` varchar(255) DEFAULT NULL,
  `cre_activite` varchar(255) DEFAULT NULL,
  `cre_issu_recherche` varchar(3) DEFAULT NULL,
  `cre_statut_createur` varchar(64) DEFAULT NULL,
  `id_tiers` int(10) unsigned DEFAULT '0',
  `resp` varchar(5) DEFAULT NULL,
  `inactif` tinyint(1) DEFAULT '0',
  `origine_contact` varchar(64) DEFAULT NULL,
  `origine_contact_detail` varchar(64) DEFAULT NULL,
  `photo` varchar(14) DEFAULT NULL,
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  `field1` varchar(255) DEFAULT NULL,
  `field2` varchar(255) DEFAULT NULL,
  `field3` varchar(255) DEFAULT NULL,
  `field4` varchar(255) DEFAULT NULL,
  `field5` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`type_layer`,`id_layer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_tiers_layer`
--

LOCK TABLES `dims_mod_business_tiers_layer` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_tiers_layer` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_tiers_layer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_tiers_mbfield`
--

DROP TABLE IF EXISTS `dims_mod_business_tiers_mbfield`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_tiers_mbfield` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tiers` int(11) NOT NULL,
  `id_mbfield` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `value` text,
  `timestp_modify` bigint(14) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `lastmodify` tinyint(1) NOT NULL DEFAULT '0',
  `id_lang` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_contact` (`id_tiers`),
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_tiers_mbfield`
--

LOCK TABLES `dims_mod_business_tiers_mbfield` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_tiers_mbfield` DISABLE KEYS */;
INSERT INTO `dims_mod_business_tiers_mbfield` VALUES (1,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090823235420,0,0,1),(2,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090823235420,0,0,1),(3,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090823235622,0,0,1),(4,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090823235622,0,0,1),(5,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090823235716,0,0,1),(6,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090823235716,0,0,1),(7,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090823235729,0,0,1),(8,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090823235729,0,0,1),(9,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090823235744,0,0,1),(10,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090823235744,0,0,1),(11,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090823235749,0,0,1),(12,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090823235749,0,0,1),(13,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090823235814,0,0,1),(14,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090823235814,0,0,1),(15,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090823235839,0,0,1),(16,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090823235839,0,0,1),(17,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090823235843,0,0,1),(18,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090823235843,0,0,1),(19,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090823235925,0,0,1),(20,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090823235925,0,0,1),(21,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090824000200,0,0,1),(22,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090824000200,0,0,1),(23,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090824000222,0,0,1),(24,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090824000222,0,0,1),(25,579,768,5,2,1,'Ministère de l\'Economie et du Commerce Extérieur',20090824000359,0,0,1),(26,579,778,5,2,1,'Ministre de l\' Economie et du Commerce (Jeannot Krecké)',20090824000359,0,0,1),(27,550,779,5,2,1,'Spécialisée en ingénierie des systèmes d\'information, NETLOR vous accompagne tout au long du processus de conception, de déploiement et d\'évolution de vos services en ligne;',20090824000523,0,0,1),(28,550,777,5,2,1,'',20090824000523,0,0,1),(29,583,768,64,128,1,'BED Trade and Investment Office-Dubai',20090827085621,0,0,1),(30,583,777,64,128,1,'',20090827085621,0,0,1),(31,584,768,64,128,1,'BED Trade and Investment Office-New Delhi',20090827090000,0,0,1),(32,583,778,64,128,1,'M.Albert PANSIN',20090827090019,0,0,1),(33,583,777,64,128,1,'',20090827090019,0,0,1),(34,587,768,61,127,1,'BED- Trade and Investment Offoce- New York',20090827101743,0,0,1),(35,587,768,61,127,1,'BED- Trade and Investment Office- New York',20090827101803,0,0,1),(36,587,768,61,127,1,'BED Trade and Investment Office- New York',20090827102111,0,0,1),(37,588,768,61,127,1,'BED Trade and Investment Office- San Fransisco',20090827102136,0,0,1),(38,588,770,61,127,1,'CA 94104',20090827103353,0,0,1),(39,587,770,61,127,1,'NY 10022',20090827103937,0,0,1),(40,776,768,64,102,1,'Institut de Formation Sectoriel du Bâtiment - IFSB S.A.',20091012083210,0,0,1),(41,776,777,64,102,1,'',20091012083210,0,0,1);
/*!40000 ALTER TABLE `dims_mod_business_tiers_mbfield` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_tiers_project`
--

DROP TABLE IF EXISTS `dims_mod_business_tiers_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_tiers_project` (
  `id_tiers` int(11) NOT NULL,
  `id_project` int(11) NOT NULL,
  `id_interlocuteur` int(11) NOT NULL,
  `timestp` bigint(14) NOT NULL,
  PRIMARY KEY (`id_tiers`,`id_project`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_tiers_project`
--

LOCK TABLES `dims_mod_business_tiers_project` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_tiers_project` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_tiers_project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_tiers_services`
--

DROP TABLE IF EXISTS `dims_mod_business_tiers_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_tiers_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_ent` int(11) NOT NULL DEFAULT '0',
  `id_service` int(10) unsigned DEFAULT '0',
  `label` varchar(255) DEFAULT NULL,
  `parents` varchar(200) DEFAULT NULL,
  `depth` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_service` (`id_service`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_tiers_services`
--

LOCK TABLES `dims_mod_business_tiers_services` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_tiers_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_tiers_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_user_planning`
--

DROP TABLE IF EXISTS `dims_mod_business_user_planning`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_user_planning` (
  `id_user` int(11) NOT NULL,
  `id_user_sel` int(11) NOT NULL,
  `display` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_user`,`id_user_sel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_user_planning`
--

LOCK TABLES `dims_mod_business_user_planning` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_user_planning` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_user_planning` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_business_versement`
--

DROP TABLE IF EXISTS `dims_mod_business_versement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_business_versement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `suivi_id` int(10) unsigned DEFAULT '0',
  `suivi_type` varchar(32) DEFAULT NULL,
  `suivi_exercice` varchar(32) DEFAULT NULL,
  `montant` double DEFAULT NULL,
  `date_paiement` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_business_versement`
--

LOCK TABLES `dims_mod_business_versement` WRITE;
/*!40000 ALTER TABLE `dims_mod_business_versement` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_business_versement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_directory_contact`
--

DROP TABLE IF EXISTS `dims_mod_directory_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_directory_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `service` varchar(255) DEFAULT NULL,
  `function` varchar(255) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `mobile` varchar(32) DEFAULT NULL,
  `fax` varchar(32) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postalcode` varchar(32) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `commentary` longtext,
  `level` varchar(10) DEFAULT NULL,
  `id_dims_user` int(10) unsigned DEFAULT NULL,
  `date_create` varchar(14) DEFAULT NULL,
  `date_modify` varchar(14) DEFAULT NULL,
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FT` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_directory_contact`
--

LOCK TABLES `dims_mod_directory_contact` WRITE;
/*!40000 ALTER TABLE `dims_mod_directory_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_directory_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_directory_favorites`
--

DROP TABLE IF EXISTS `dims_mod_directory_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_directory_favorites` (
  `id_contact` int(10) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_dims_user` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_contact`,`id_user`,`id_dims_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_directory_favorites`
--

LOCK TABLES `dims_mod_directory_favorites` WRITE;
/*!40000 ALTER TABLE `dims_mod_directory_favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_directory_favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_doc_ext`
--

DROP TABLE IF EXISTS `dims_mod_doc_ext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_doc_ext` (
  `ext` varchar(10) DEFAULT NULL,
  `filetype` varchar(16) DEFAULT NULL,
  KEY `ext` (`ext`),
  KEY `filetype` (`filetype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_doc_ext`
--

LOCK TABLES `dims_mod_doc_ext` WRITE;
/*!40000 ALTER TABLE `dims_mod_doc_ext` DISABLE KEYS */;
INSERT INTO `dims_mod_doc_ext` VALUES ('odt','document'),('doc','document'),('xls','spreadsheet'),('mp3','audio'),('wav','audio'),('ogg','audio'),('jpg','image'),('jpeg','image'),('png','image'),('gif','image'),('psd','image'),('xcf','image'),('svg','image'),('pdf','document'),('avi','video'),('wmv','video'),('ogm','video'),('mpg','video'),('mpeg','video'),('zip','archive'),('tgz','archive'),('gz','archive'),('rar','archive'),('bz2','archive'),('ace','archive');
/*!40000 ALTER TABLE `dims_mod_doc_ext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_doc_file`
--

DROP TABLE IF EXISTS `dims_mod_doc_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_doc_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `md5id` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `timestp_create` bigint(14) DEFAULT NULL,
  `timestp_modify` bigint(14) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT '0',
  `extension` varchar(20) DEFAULT NULL,
  `parents` varchar(255) DEFAULT NULL,
  `content` longtext,
  `nbclick` int(10) unsigned DEFAULT '0',
  `version` int(10) NOT NULL DEFAULT '1',
  `id_folder` int(10) unsigned DEFAULT '0',
  `id_user_modify` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `id_record` int(11) NOT NULL DEFAULT '0',
  `id_object` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_group` (`id_workspace`),
  KEY `id_module` (`id_module`),
  KEY `name` (`name`),
  KEY `id_folder` (`id_folder`),
  KEY `extension` (`extension`),
  KEY `md5id` (`md5id`),
  KEY `key_search` (`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_doc_file`
--

LOCK TABLES `dims_mod_doc_file` WRITE;
/*!40000 ALTER TABLE `dims_mod_doc_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_doc_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_doc_file_draft`
--

DROP TABLE IF EXISTS `dims_mod_doc_file_draft`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_doc_file_draft` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `md5id` varchar(32) DEFAULT NULL,
  `id_docfile` int(10) unsigned DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `timestp_create` bigint(14) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT '0',
  `extension` varchar(20) DEFAULT NULL,
  `parents` varchar(255) DEFAULT NULL,
  `id_folder` int(10) unsigned DEFAULT '0',
  `id_user_modify` int(10) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `md5id` (`md5id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_doc_file_draft`
--

LOCK TABLES `dims_mod_doc_file_draft` WRITE;
/*!40000 ALTER TABLE `dims_mod_doc_file_draft` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_doc_file_draft` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_doc_file_history`
--

DROP TABLE IF EXISTS `dims_mod_doc_file_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_doc_file_history` (
  `id_docfile` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `timestp_create` bigint(14) DEFAULT NULL,
  `timestp_modify` bigint(14) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT '0',
  `version` int(10) NOT NULL DEFAULT '1',
  `extension` varchar(20) DEFAULT NULL,
  `id_user_modify` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_docfile`,`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_doc_file_history`
--

LOCK TABLES `dims_mod_doc_file_history` WRITE;
/*!40000 ALTER TABLE `dims_mod_doc_file_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_doc_file_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_doc_folder`
--

DROP TABLE IF EXISTS `dims_mod_doc_folder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_doc_folder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `foldertype` varchar(16) DEFAULT NULL,
  `readonly` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `readonly_content` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `parents` varchar(255) DEFAULT NULL,
  `networkpath` varchar(255) DEFAULT '',
  `timestp_create` bigint(14) DEFAULT NULL,
  `timestp_modify` bigint(14) DEFAULT NULL,
  `nbelements` int(10) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `id_folder` int(10) unsigned DEFAULT '0',
  `id_user_modify` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `isvirtual` tinyint(1) NOT NULL DEFAULT '0',
  `id_globalobject` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_group` (`id_workspace`),
  KEY `id_module` (`id_module`),
  KEY `id_folder` (`id_folder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_doc_folder`
--

LOCK TABLES `dims_mod_doc_folder` WRITE;
/*!40000 ALTER TABLE `dims_mod_doc_folder` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_doc_folder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_doc_gallery`
--

DROP TABLE IF EXISTS `dims_mod_doc_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_doc_gallery` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `timestp_create` bigint(14) DEFAULT NULL,
  `timestp_modify` bigint(14) DEFAULT NULL,
  `nb_row` int(10) NOT NULL DEFAULT '5',
  `nb_column` int(10) NOT NULL DEFAULT '5',
  `small_width` int(10) NOT NULL DEFAULT '50',
  `s_w_format` varchar(2) DEFAULT NULL,
  `small_height` int(10) NOT NULL DEFAULT '50',
  `s_h_format` varchar(2) DEFAULT NULL,
  `big_width` int(10) NOT NULL DEFAULT '90',
  `b_w_format` varchar(2) DEFAULT NULL,
  `big_height` int(10) NOT NULL DEFAULT '90',
  `b_h_format` varchar(2) DEFAULT NULL,
  `show_picture` varchar(3) DEFAULT NULL,
  `show_textfile` varchar(3) DEFAULT NULL,
  `show_compressfile` varchar(3) DEFAULT NULL,
  `show_photography` varchar(3) DEFAULT NULL,
  `id_folder` int(10) unsigned DEFAULT '0',
  `id_user_modify` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_group` (`id_workspace`),
  KEY `id_module` (`id_module`),
  KEY `name` (`name`),
  KEY `id_folder` (`id_folder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_doc_gallery`
--

LOCK TABLES `dims_mod_doc_gallery` WRITE;
/*!40000 ALTER TABLE `dims_mod_doc_gallery` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_doc_gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_doc_param`
--

DROP TABLE IF EXISTS `dims_mod_doc_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_doc_param` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `ext` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_doc_param`
--

LOCK TABLES `dims_mod_doc_param` WRITE;
/*!40000 ALTER TABLE `dims_mod_doc_param` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_doc_param` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_elearning_video`
--

DROP TABLE IF EXISTS `dims_mod_elearning_video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_elearning_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_name` varchar(250) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `timestp_create` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_elearning_video`
--

LOCK TABLES `dims_mod_elearning_video` WRITE;
/*!40000 ALTER TABLE `dims_mod_elearning_video` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_elearning_video` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_elearning_video_details`
--

DROP TABLE IF EXISTS `dims_mod_elearning_video_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_elearning_video_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_video` int(11) NOT NULL,
  `lang` tinyint(3) NOT NULL,
  `title` varchar(250) DEFAULT NULL,
  `descriptif_court` varchar(250) DEFAULT NULL,
  `descriptif_long` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_elearning_video_details`
--

LOCK TABLES `dims_mod_elearning_video_details` WRITE;
/*!40000 ALTER TABLE `dims_mod_elearning_video_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_elearning_video_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_faq_affectation`
--

DROP TABLE IF EXISTS `dims_mod_faq_affectation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_faq_affectation` (
  `id_question` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id_question`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_faq_affectation`
--

LOCK TABLES `dims_mod_faq_affectation` WRITE;
/*!40000 ALTER TABLE `dims_mod_faq_affectation` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_faq_affectation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_faq_question`
--

DROP TABLE IF EXISTS `dims_mod_faq_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_faq_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` longtext,
  `id_user` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `date_create` bigint(20) NOT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '0',
  `id_rubrique` int(11) NOT NULL DEFAULT '0',
  `date_modify` bigint(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_rubrique` (`id_rubrique`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_faq_question`
--

LOCK TABLES `dims_mod_faq_question` WRITE;
/*!40000 ALTER TABLE `dims_mod_faq_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_faq_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_faq_reponse`
--

DROP TABLE IF EXISTS `dims_mod_faq_reponse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_faq_reponse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reponse` longtext,
  `id_question` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `date_create` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_faq_reponse`
--

LOCK TABLES `dims_mod_faq_reponse` WRITE;
/*!40000 ALTER TABLE `dims_mod_faq_reponse` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_faq_reponse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_faq_rubriques`
--

DROP TABLE IF EXISTS `dims_mod_faq_rubriques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_faq_rubriques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `position` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_faq_rubriques`
--

LOCK TABLES `dims_mod_faq_rubriques` WRITE;
/*!40000 ALTER TABLE `dims_mod_faq_rubriques` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_faq_rubriques` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_forms`
--

DROP TABLE IF EXISTS `dims_mod_forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `tablename` varchar(255) DEFAULT NULL,
  `description` longtext,
  `pubdate_start` varchar(14) DEFAULT NULL,
  `pubdate_end` varchar(14) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `option_onlyone` tinyint(1) unsigned DEFAULT '0',
  `option_onlyoneday` tinyint(1) unsigned DEFAULT '0',
  `width` varchar(5) DEFAULT NULL,
  `nbline` int(10) unsigned DEFAULT '25',
  `model` varchar(32) DEFAULT NULL,
  `typeform` varchar(16) DEFAULT NULL,
  `option_modify` varchar(16) DEFAULT NULL,
  `option_view` varchar(16) DEFAULT NULL,
  `option_displayuser` tinyint(1) unsigned DEFAULT '0',
  `option_displaygroup` tinyint(1) unsigned DEFAULT '0',
  `option_displaydate` tinyint(1) unsigned DEFAULT '0',
  `option_displayip` tinyint(1) unsigned DEFAULT '0',
  `viewed` int(10) unsigned DEFAULT '0',
  `autobackup` int(10) unsigned DEFAULT '0',
  `cms_response` longtext,
  `cms_link` tinyint(1) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_forms`
--

LOCK TABLES `dims_mod_forms` WRITE;
/*!40000 ALTER TABLE `dims_mod_forms` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_forms_field`
--

DROP TABLE IF EXISTS `dims_mod_forms_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_forms_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_forms` int(10) unsigned DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `fieldname` varchar(255) DEFAULT NULL,
  `separator` tinyint(1) unsigned DEFAULT '0',
  `separator_level` int(10) unsigned DEFAULT '0',
  `separator_fontsize` int(10) unsigned DEFAULT '0',
  `type` varchar(16) DEFAULT NULL,
  `format` varchar(16) DEFAULT NULL,
  `values` longtext CHARACTER SET latin1,
  `description` longtext,
  `position` int(10) unsigned DEFAULT '0',
  `maxlength` int(10) unsigned DEFAULT '0',
  `cols` int(10) unsigned DEFAULT '0',
  `option_needed` tinyint(1) unsigned DEFAULT '0',
  `option_arrayview` tinyint(1) unsigned DEFAULT '1',
  `option_exportview` tinyint(1) unsigned DEFAULT '1',
  `option_cmsgroupby` tinyint(1) unsigned DEFAULT '0',
  `option_cmsorderby` tinyint(1) unsigned DEFAULT '0',
  `option_cmsdisplaylabel` tinyint(1) unsigned DEFAULT '0',
  `option_cmsshowfilter` tinyint(1) unsigned DEFAULT '0',
  `defaultvalue` varchar(255) DEFAULT NULL,
  `interline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_forms` (`id_forms`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_forms_field`
--

LOCK TABLES `dims_mod_forms_field` WRITE;
/*!40000 ALTER TABLE `dims_mod_forms_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_forms_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_forms_reply`
--

DROP TABLE IF EXISTS `dims_mod_forms_reply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_forms_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_forms` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` tinyint(3) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT NULL,
  `date_validation` varchar(14) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_forms_reply`
--

LOCK TABLES `dims_mod_forms_reply` WRITE;
/*!40000 ALTER TABLE `dims_mod_forms_reply` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_forms_reply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_forms_reply_field`
--

DROP TABLE IF EXISTS `dims_mod_forms_reply_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_forms_reply_field` (
  `id_reply` int(10) unsigned DEFAULT '0',
  `id_forms` int(10) unsigned DEFAULT '0',
  `id_field` int(10) unsigned DEFAULT '0',
  `value` longtext,
  `id_user` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_reply` (`id_reply`),
  KEY `id_forms` (`id_forms`),
  KEY `id_field` (`id_field`),
  KEY `id_workspace` (`id_workspace`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_forms_reply_field`
--

LOCK TABLES `dims_mod_forms_reply_field` WRITE;
/*!40000 ALTER TABLE `dims_mod_forms_reply_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_forms_reply_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_news`
--

DROP TABLE IF EXISTS `dims_mod_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_newscat` int(10) unsigned DEFAULT '0',
  `title` varchar(100) DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `content` longtext,
  `url` varchar(100) DEFAULT NULL,
  `urltitle` varchar(100) DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  `published` tinyint(1) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `date_publish` varchar(14) DEFAULT NULL,
  `hot` tinyint(1) unsigned DEFAULT '0',
  `nbclick` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_news`
--

LOCK TABLES `dims_mod_news` WRITE;
/*!40000 ALTER TABLE `dims_mod_news` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_newscat`
--

DROP TABLE IF EXISTS `dims_mod_newscat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_newscat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `id_module` int(10) unsigned DEFAULT '0',
  `id_user` tinyint(10) unsigned DEFAULT '0',
  `date_create` datetime DEFAULT NULL,
  `date_modify` datetime DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `id_workspace` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_newscat`
--

LOCK TABLES `dims_mod_newscat` WRITE;
/*!40000 ALTER TABLE `dims_mod_newscat` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_newscat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_newsletter`
--

DROP TABLE IF EXISTS `dims_mod_newsletter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `descriptif` text,
  `etat` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: inactif; 1: actif',
  `timestp_create` varchar(14) DEFAULT NULL,
  `timestp_modif` varchar(14) DEFAULT NULL,
  `id_user_create` int(11) NOT NULL,
  `id_user_modif` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_user_responsible` int(11) NOT NULL,
  `template` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_newsletter`
--

LOCK TABLES `dims_mod_newsletter` WRITE;
/*!40000 ALTER TABLE `dims_mod_newsletter` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_newsletter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_newsletter_content`
--

DROP TABLE IF EXISTS `dims_mod_newsletter_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_newsletter_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_newsletter` int(11) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `date_create` varchar(14) DEFAULT NULL,
  `date_modif` varchar(14) DEFAULT NULL,
  `id_user_create` int(11) NOT NULL,
  `id_doc` int(11) NOT NULL,
  `content` text,
  `date_envoi` varchar(14) DEFAULT NULL,
  `id_lang` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_newsletter_content`
--

LOCK TABLES `dims_mod_newsletter_content` WRITE;
/*!40000 ALTER TABLE `dims_mod_newsletter_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_newsletter_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_newsletter_inscription`
--

DROP TABLE IF EXISTS `dims_mod_newsletter_inscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_newsletter_inscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) DEFAULT NULL,
  `prenom` varchar(200) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `entreprise` varchar(255) DEFAULT NULL,
  `fonction` varchar(255) DEFAULT NULL,
  `date_inscription` varchar(14) DEFAULT NULL,
  `id_newsletter` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_newsletter_inscription`
--

LOCK TABLES `dims_mod_newsletter_inscription` WRITE;
/*!40000 ALTER TABLE `dims_mod_newsletter_inscription` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_newsletter_inscription` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_newsletter_mailing_ct`
--

DROP TABLE IF EXISTS `dims_mod_newsletter_mailing_ct`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_newsletter_mailing_ct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mailing` int(11) NOT NULL,
  `nom` varchar(150) DEFAULT NULL,
  `prenom` varchar(150) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `date_creation` varchar(14) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_newsletter_mailing_ct`
--

LOCK TABLES `dims_mod_newsletter_mailing_ct` WRITE;
/*!40000 ALTER TABLE `dims_mod_newsletter_mailing_ct` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_newsletter_mailing_ct` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_newsletter_mailing_list`
--

DROP TABLE IF EXISTS `dims_mod_newsletter_mailing_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_newsletter_mailing_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_workspace` int(11) NOT NULL,
  `date_create` varchar(14) DEFAULT NULL,
  `id_user_create` int(11) NOT NULL,
  `label` varchar(200) DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_newsletter_mailing_list`
--

LOCK TABLES `dims_mod_newsletter_mailing_list` WRITE;
/*!40000 ALTER TABLE `dims_mod_newsletter_mailing_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_newsletter_mailing_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_newsletter_mailing_news`
--

DROP TABLE IF EXISTS `dims_mod_newsletter_mailing_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_newsletter_mailing_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mailing` int(11) NOT NULL,
  `id_newsletter` int(11) NOT NULL,
  `id_user_create` int(11) NOT NULL,
  `date_create` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_newsletter_mailing_news`
--

LOCK TABLES `dims_mod_newsletter_mailing_news` WRITE;
/*!40000 ALTER TABLE `dims_mod_newsletter_mailing_news` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_newsletter_mailing_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_newsletter_subscribed`
--

DROP TABLE IF EXISTS `dims_mod_newsletter_subscribed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_newsletter_subscribed` (
  `id_newsletter` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `date_inscription` varchar(14) DEFAULT NULL,
  `date_desinscription` varchar(14) DEFAULT NULL,
  `etat` tinyint(4) NOT NULL COMMENT '0:desinscrit; 1:inscrit',
  `commentaire` text,
  `des_comment` text,
  `des_cause1` tinyint(1) NOT NULL,
  `des_cause2` tinyint(1) NOT NULL,
  `des_cause3` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_newsletter`,`id_contact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_newsletter_subscribed`
--

LOCK TABLES `dims_mod_newsletter_subscribed` WRITE;
/*!40000 ALTER TABLE `dims_mod_newsletter_subscribed` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_newsletter_subscribed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_rsscache`
--

DROP TABLE IF EXISTS `dims_mod_rsscache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_rsscache` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_rssfeed` int(10) NOT NULL DEFAULT '0',
  `timestp` varchar(14) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `description` longtext,
  `content` longtext,
  `id_user` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `link` (`link`),
  KEY `id_rssfeed` (`id_rssfeed`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_rsscache`
--

LOCK TABLES `dims_mod_rsscache` WRITE;
/*!40000 ALTER TABLE `dims_mod_rsscache` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_rsscache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_rsscat`
--

DROP TABLE IF EXISTS `dims_mod_rsscat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_rsscat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `timestamp` varchar(14) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_workspace` (`id_workspace`),
  KEY `id_module` (`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_rsscat`
--

LOCK TABLES `dims_mod_rsscat` WRITE;
/*!40000 ALTER TABLE `dims_mod_rsscat` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_rsscat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_rssfeed`
--

DROP TABLE IF EXISTS `dims_mod_rssfeed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_rssfeed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `description` blob NOT NULL,
  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lastvisit` varchar(14) DEFAULT NULL,
  `revisit` int(10) unsigned NOT NULL DEFAULT '0',
  `country` varchar(255) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `updating_cache` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `error` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_rsscat` int(10) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_workspace` (`id_workspace`),
  KEY `id_module` (`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_rssfeed`
--

LOCK TABLES `dims_mod_rssfeed` WRITE;
/*!40000 ALTER TABLE `dims_mod_rssfeed` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_rssfeed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_rsspref`
--

DROP TABLE IF EXISTS `dims_mod_rsspref`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_rsspref` (
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_feed` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_rsspref`
--

LOCK TABLES `dims_mod_rsspref` WRITE;
/*!40000 ALTER TABLE `dims_mod_rsspref` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_rsspref` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_rssrequest`
--

DROP TABLE IF EXISTS `dims_mod_rssrequest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_rssrequest` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request` varchar(255) DEFAULT NULL,
  `id_rsscat` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_rssrequest`
--

LOCK TABLES `dims_mod_rssrequest` WRITE;
/*!40000 ALTER TABLE `dims_mod_rssrequest` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_rssrequest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_vcard`
--

DROP TABLE IF EXISTS `dims_mod_vcard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_vcard` (
  `id_docfile` int(10) NOT NULL,
  `name_vcard` varchar(200) NOT NULL,
  `id_contact` varchar(10) NOT NULL DEFAULT '0',
  `num` int(100) NOT NULL,
  `date_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_docfile`,`num`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_vcard`
--

LOCK TABLES `dims_mod_vcard` WRITE;
/*!40000 ALTER TABLE `dims_mod_vcard` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_vcard` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_article`
--

DROP TABLE IF EXISTS `dims_mod_wce_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_wce_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content1` longtext,
  `author` longtext,
  `version` varchar(16) DEFAULT NULL,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timestp` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_published` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_unpublished` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_heading` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(16) DEFAULT NULL,
  `meter` bigint(14) NOT NULL DEFAULT '0',
  `template_id` int(10) NOT NULL DEFAULT '0',
  `content2` longtext,
  `content3` longtext,
  `content4` longtext,
  `content5` longtext,
  `content6` longtext,
  `content7` longtext,
  `content8` longtext,
  `content9` longtext,
  `content10` longtext,
  `meta_description` longtext,
  `meta_keywords` longtext,
  `model` varchar(255) DEFAULT NULL,
  `lastupdate_timestp` bigint(14) NOT NULL DEFAULT '0',
  `lastupdate_id_user` bigint(14) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  `urlrewrite` varchar(255) DEFAULT NULL,
  `draftcontent1` longtext,
  `draftcontent2` longtext,
  `draftcontent3` longtext,
  `draftcontent4` longtext,
  `draftcontent5` longtext,
  `draftcontent6` longtext,
  `draftcontent7` longtext,
  `draftcontent8` longtext,
  `draftcontent9` longtext,
  `topcontent` longtext,
  `bottomcontent` longtext,
  `leftcontent` longtext,
  `rightcontent` longtext,
  `id_article_link` int(11) NOT NULL DEFAULT '0',
  `priority` float NOT NULL DEFAULT '0.5',
  `issitemap` tinyint(2) NOT NULL DEFAULT '1',
  `changefreq` varchar(20) DEFAULT NULL,
  `description` text,
  `picto` varchar(255) DEFAULT NULL,
  `edito` tinyint(4) NOT NULL DEFAULT '0',
  `url` varchar(255) DEFAULT NULL,
  `url_window` tinyint(4) NOT NULL DEFAULT '0',
  `first_page` tinyint(4) NOT NULL DEFAULT '0',
  `timestp_modify_first` bigint(14) NOT NULL DEFAULT '0',
  `actu` tinyint(4) NOT NULL DEFAULT '0',
  `id_redirect_domain` int(11) NOT NULL DEFAULT '0',
  `prepublish` tinyint(4) NOT NULL DEFAULT '0',
  `urlrewriteold` varchar(255) DEFAULT NULL,
  `id_lang` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_heading` (`id_heading`),
  KEY `id_module` (`id_module`),
  KEY `lastupdate_timestp` (`lastupdate_timestp`),
  KEY `id_article_link` (`id_article_link`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_article`
--

LOCK TABLES `dims_mod_wce_article` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_article_block`
--

DROP TABLE IF EXISTS `dims_mod_wce_article_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_wce_article_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) DEFAULT NULL,
  `title` longtext,
  `author` longtext,
  `version` varchar(16) DEFAULT NULL,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timestp` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_article` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(16) DEFAULT NULL,
  `content1` longtext,
  `content2` longtext,
  `content3` longtext,
  `content4` longtext,
  `content5` longtext,
  `content6` longtext,
  `content7` longtext,
  `content8` longtext,
  `content9` longtext,
  `content10` longtext,
  `id_model` int(11) NOT NULL DEFAULT '0',
  `lastupdate_timestp` bigint(14) NOT NULL DEFAULT '0',
  `lastupdate_id_user` bigint(14) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  `draftcontent1` longtext,
  `draftcontent2` longtext,
  `draftcontent3` longtext,
  `draftcontent4` longtext,
  `draftcontent5` longtext,
  `draftcontent6` longtext,
  `draftcontent7` longtext,
  `draftcontent8` longtext,
  `draftcontent9` longtext,
  `draftcontent10` longtext,
  `display_title` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_article` (`id_article`),
  KEY `id_module` (`id_module`),
  KEY `lastupdate_timestp` (`lastupdate_timestp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_article_block`
--

LOCK TABLES `dims_mod_wce_article_block` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_article_block` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_article_block` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_article_block_model`
--

DROP TABLE IF EXISTS `dims_mod_wce_article_block_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_wce_article_block_model` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `present` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_article_block_model`
--

LOCK TABLES `dims_mod_wce_article_block_model` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_article_block_model` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_article_block_model` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_article_block_version`
--

DROP TABLE IF EXISTS `dims_mod_wce_article_block_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_wce_article_block_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blockid` int(11) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  `draftversion` int(10) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `author` longtext,
  `id_module` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `content1` longtext,
  `content2` longtext,
  `content3` longtext,
  `content4` longtext,
  `content5` longtext,
  `content6` longtext,
  `content7` longtext,
  `content8` longtext,
  `content9` longtext,
  `content10` longtext,
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `blockid` (`blockid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_article_block_version`
--

LOCK TABLES `dims_mod_wce_article_block_version` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_article_block_version` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_article_block_version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_article_draft`
--

DROP TABLE IF EXISTS `dims_mod_wce_article_draft`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_wce_article_draft` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content1` longtext,
  `author` longtext,
  `version` varchar(16) DEFAULT NULL,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timestp` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_published` bigint(14) unsigned NOT NULL DEFAULT '0',
  `timestp_unpublished` bigint(14) unsigned NOT NULL DEFAULT '0',
  `id_heading` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(16) DEFAULT NULL,
  `meter` bigint(14) NOT NULL DEFAULT '0',
  `template_id` int(10) NOT NULL DEFAULT '0',
  `content2` longtext,
  `content3` longtext,
  `content4` longtext,
  `content5` longtext,
  `content6` longtext,
  `content7` longtext,
  `content8` longtext,
  `content9` longtext,
  `content10` longtext,
  `meta_description` longtext,
  `meta_keywords` longtext,
  `model` varchar(255) DEFAULT NULL,
  `lastupdate_timestp` int(10) NOT NULL DEFAULT '0',
  `lastupdate_id_user` int(10) NOT NULL DEFAULT '0',
  `urlrewrite` varchar(255) DEFAULT NULL,
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_article_draft`
--

LOCK TABLES `dims_mod_wce_article_draft` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_article_draft` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_article_draft` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_article_meter`
--

DROP TABLE IF EXISTS `dims_mod_wce_article_meter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_wce_article_meter` (
  `id_article` int(10) NOT NULL DEFAULT '0',
  `timestp` bigint(20) NOT NULL,
  `email` varchar(60) NOT NULL DEFAULT '',
  `meter` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_article`,`timestp`,`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_article_meter`
--

LOCK TABLES `dims_mod_wce_article_meter` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_article_meter` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_article_meter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_article_version`
--

DROP TABLE IF EXISTS `dims_mod_wce_article_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_wce_article_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `articleid` int(11) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  `draftversion` int(10) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `author` longtext,
  `id_module` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `content1` longtext,
  `content2` longtext,
  `content3` longtext,
  `content4` longtext,
  `content5` longtext,
  `content6` longtext,
  `content7` longtext,
  `content8` longtext,
  `content9` longtext,
  `content10` longtext,
  `meta_description` longtext,
  `meta_keywords` longtext,
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `articleid` (`articleid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_article_version`
--

LOCK TABLES `dims_mod_wce_article_version` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_article_version` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_article_version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_heading`
--

DROP TABLE IF EXISTS `dims_mod_wce_heading`;
CREATE TABLE `dims_mod_wce_heading` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `template` varchar(255) DEFAULT NULL,
  `id_heading` int(10) unsigned NOT NULL DEFAULT '0',
  `parents` varchar(255) DEFAULT NULL,
  `depth` int(10) unsigned NOT NULL DEFAULT '0',
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `color` varchar(32) DEFAULT NULL,
  `posx` int(10) unsigned NOT NULL DEFAULT '0',
  `posy` int(10) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `visible_if_connected` tinyint(1) unsigned NOT NULL,
  `linkedpage` int(10) unsigned NOT NULL DEFAULT '0',
  `url` varchar(255) DEFAULT NULL,
  `url_window` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `free1` varchar(255) DEFAULT NULL,
  `free2` varchar(255) DEFAULT NULL,
  `id_module` int(10) unsigned DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  `urlrewrite` varchar(100) DEFAULT NULL,
  `fckeditor` varchar(255) DEFAULT NULL,
  `picto` varchar(255) DEFAULT NULL,
  `colour` varchar(10) DEFAULT NULL,
  `linkedheading` int(11) NOT NULL DEFAULT '0',
  `id_lang` int(10) NOT NULL DEFAULT '1',
  `is_sitemap` tinyint(4) NOT NULL DEFAULT '1',
  `private` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_heading` (`id_heading`),
  KEY `id_workspace` (`id_workspace`),
  KEY `id_module` (`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_heading`
--

LOCK TABLES `dims_mod_wce_heading` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_heading` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_heading` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_object`
--

DROP TABLE IF EXISTS `dims_mod_wce_object`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_wce_object` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `mode` tinyint(4) NOT NULL DEFAULT '0',
  `mode_dyn` tinyint(4) NOT NULL DEFAULT '0',
  `date_start` bigint(20) NOT NULL,
  `date_end` bigint(20) NOT NULL,
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_object`
--

LOCK TABLES `dims_mod_wce_object` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_object` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_object` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_object_corresp`
--

DROP TABLE IF EXISTS `dims_mod_wce_object_corresp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_wce_object_corresp` (
  `id_object` int(11) NOT NULL,
  `id_article` int(11) NOT NULL DEFAULT '0',
  `id_heading` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_object`,`id_article`,`id_heading`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_object_corresp`
--

LOCK TABLES `dims_mod_wce_object_corresp` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_object_corresp` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_object_corresp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_rule`
--

DROP TABLE IF EXISTS `dims_mod_wce_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_wce_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `id_type` tinyint(4) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_rule`
--

LOCK TABLES `dims_mod_wce_rule` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_rule_corresp`
--

DROP TABLE IF EXISTS `dims_mod_wce_rule_corresp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_wce_rule_corresp` (
  `id_rule` int(11) NOT NULL,
  `id_heading` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_rule`,`id_heading`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_rule_corresp`
--

LOCK TABLES `dims_mod_wce_rule_corresp` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_rule_corresp` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_rule_corresp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_wce_template`
--

DROP TABLE IF EXISTS `dims_mod_wce_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_wce_template` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_user` int(10) NOT NULL DEFAULT '0',
  `share` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_wce_template`
--

LOCK TABLES `dims_mod_wce_template` WRITE;
/*!40000 ALTER TABLE `dims_mod_wce_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_wce_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_webmail_email`
--

DROP TABLE IF EXISTS `dims_mod_webmail_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_webmail_email` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_inbox` int(10) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `date` bigint(14) NOT NULL DEFAULT '0',
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `attached` tinyint(4) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_user` int(10) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_webmail_email`
--

LOCK TABLES `dims_mod_webmail_email` WRITE;
/*!40000 ALTER TABLE `dims_mod_webmail_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_webmail_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_webmail_email_adresse`
--

DROP TABLE IF EXISTS `dims_mod_webmail_email_adresse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_webmail_email_adresse` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_mail` int(10) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '1=from/2=to/3=cc',
  `mail` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_webmail_email_adresse`
--

LOCK TABLES `dims_mod_webmail_email_adresse` WRITE;
/*!40000 ALTER TABLE `dims_mod_webmail_email_adresse` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_webmail_email_adresse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_webmail_email_docfile`
--

DROP TABLE IF EXISTS `dims_mod_webmail_email_docfile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_webmail_email_docfile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_email` int(11) NOT NULL DEFAULT '0',
  `id_docfile` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_webmail_email_docfile`
--

LOCK TABLES `dims_mod_webmail_email_docfile` WRITE;
/*!40000 ALTER TABLE `dims_mod_webmail_email_docfile` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_webmail_email_docfile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_webmail_email_link`
--

DROP TABLE IF EXISTS `dims_mod_webmail_email_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_webmail_email_link` (
  `id_mail` int(10) NOT NULL,
  `id_contact` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_webmail_email_link`
--

LOCK TABLES `dims_mod_webmail_email_link` WRITE;
/*!40000 ALTER TABLE `dims_mod_webmail_email_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_webmail_email_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_webmail_inbox`
--

DROP TABLE IF EXISTS `dims_mod_webmail_inbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_webmail_inbox` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `server` varchar(55) DEFAULT NULL,
  `login` varchar(30) DEFAULT NULL,
  `password` varchar(30) DEFAULT NULL,
  `protocol` varchar(10) DEFAULT NULL,
  `port` int(5) NOT NULL DEFAULT '110',
  `crypto` varchar(20) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_user` int(10) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_webmail_inbox`
--

LOCK TABLES `dims_mod_webmail_inbox` WRITE;
/*!40000 ALTER TABLE `dims_mod_webmail_inbox` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_webmail_inbox` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_ww_email`
--

DROP TABLE IF EXISTS `dims_mod_ww_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_ww_email` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_page` int(10) NOT NULL DEFAULT '0',
  `content` longtext,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `watch` tinyint(1) NOT NULL DEFAULT '0',
  `subject` varchar(255) DEFAULT NULL,
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_user` int(10) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_ww_email`
--

LOCK TABLES `dims_mod_ww_email` WRITE;
/*!40000 ALTER TABLE `dims_mod_ww_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_ww_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_ww_inbox`
--

DROP TABLE IF EXISTS `dims_mod_ww_inbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_ww_inbox` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `server` varchar(55) DEFAULT NULL,
  `login` varchar(30) DEFAULT NULL,
  `password` varchar(30) DEFAULT NULL,
  `protocol` varchar(10) DEFAULT NULL,
  `port` int(5) NOT NULL DEFAULT '110',
  `crypto` varchar(20) DEFAULT NULL,
  `lastupdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `label` varchar(255) DEFAULT NULL,
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_user` int(10) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  `number` int(5) NOT NULL DEFAULT '20',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_ww_inbox`
--

LOCK TABLES `dims_mod_ww_inbox` WRITE;
/*!40000 ALTER TABLE `dims_mod_ww_inbox` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_ww_inbox` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_ww_page`
--

DROP TABLE IF EXISTS `dims_mod_ww_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_ww_page` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_site` int(10) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `url` text,
  `notes` text,
  `updated` tinyint(1) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_user` int(10) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_ww_page`
--

LOCK TABLES `dims_mod_ww_page` WRITE;
/*!40000 ALTER TABLE `dims_mod_ww_page` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_ww_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_mod_ww_site`
--

DROP TABLE IF EXISTS `dims_mod_ww_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_mod_ww_site` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) DEFAULT NULL,
  `id_inbox` int(10) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_workspace` mediumint(10) NOT NULL DEFAULT '0',
  `id_user` int(10) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_mod_ww_site`
--

LOCK TABLES `dims_mod_ww_site` WRITE;
/*!40000 ALTER TABLE `dims_mod_ww_site` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_mod_ww_site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_module`
--

DROP TABLE IF EXISTS `dims_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_module` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(100) DEFAULT NULL,
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `public` tinyint(1) unsigned DEFAULT '0',
  `shared` tinyint(1) unsigned DEFAULT '0',
  `herited` tinyint(1) unsigned DEFAULT '0',
  `adminrestricted` tinyint(1) unsigned DEFAULT '0',
  `viewmode` int(10) unsigned DEFAULT '1',
  `transverseview` tinyint(1) unsigned DEFAULT '0',
  `autoconnect` tinyint(1) unsigned DEFAULT '0',
  `sitemap` longtext,
  PRIMARY KEY (`id`),
  KEY `id_module_type` (`id_module_type`),
  KEY `id_module_type_2` (`id_module_type`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_module`
--

LOCK TABLES `dims_module` WRITE;
/*!40000 ALTER TABLE `dims_module` DISABLE KEYS */;
INSERT INTO `dims_module` VALUES (1,'system',1,NULL,1,0,0,0,0,1,0,0,NULL),(2,'CMS',2,2,1,0,0,0,0,1,0,0,NULL),(3,'Docs',3,2,1,0,0,0,0,1,0,0,NULL),(4,'Forms',4,2,1,0,0,0,0,1,0,0,NULL);
/*!40000 ALTER TABLE `dims_module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_module_type`
--

DROP TABLE IF EXISTS `dims_module_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_module_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(100) DEFAULT NULL,
  `system` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `publicparam` tinyint(1) unsigned DEFAULT '0',
  `description` longtext,
  `version` varchar(32) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `date` varchar(14) DEFAULT NULL,
  `contenttype` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contenttype` (`contenttype`),
  KEY `label` (`label`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_module_type`
--

LOCK TABLES `dims_module_type` WRITE;
/*!40000 ALTER TABLE `dims_module_type` DISABLE KEYS */;
INSERT INTO `dims_module_type` VALUES (1,'system',1,0,NULL,'5.02','Wave Software','20110101000000','system'),(2,'wce',0,1,'Web Content Editor','3','Wave Software','20101220000000','content'),(3,'doc',0,1,'Gestion de Documents','5','Wave Software','20101220000000','doc'),(4,'forms',0,1,'Gestion de Formulaires','3','Wave Software','20101220000000','content'),(5,'agenda',0,1,'agenda','2','Wave Software','20101220000000','content'),(6,'rss',0,1,'Gestion de Flux RSS','2','Wave Software','20101220000000','content'),(7,'directory',0,1,'Annuaire des Utilisateurs','1.1','Wave Software','20101220000000','directory'),(8,'news',0,1,'Gestion d\'Actualités','3.2','Wave Software','20101220000000','content'),(9,'website_watcher',0,1,'veille','0.2','Netlor Concept','20070502000000','watch'),(14,'business',0,1,'business','1.2','Netlor','20070816000000','business'),(15,'article',0,1,'wiki','3.1','Wave Software','20101220000000','content'),(17,'faq',0,1,'FAQ','1.1','Netlor','20081006000000','content'),(18,'events',0,1,'Administration des Events','1.1','Netlor','20100719000000','events'),(20,'importVcard',0,1,'Import des e-mails et vcards','0.4','Netlor','20101026000000','importVcard');
/*!40000 ALTER TABLE `dims_module_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_module_workspace`
--

DROP TABLE IF EXISTS `dims_module_workspace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_module_workspace` (
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `position` tinyint(2) NOT NULL DEFAULT '0',
  `blockposition` varchar(10) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `autoconnect` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_workspace`,`id_module`),
  KEY `id_module` (`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_module_workspace`
--

LOCK TABLES `dims_module_workspace` WRITE;
/*!40000 ALTER TABLE `dims_module_workspace` DISABLE KEYS */;
INSERT INTO `dims_module_workspace` VALUES (2,2,1,'left',1,0),(3,2,2,'left',1,0),(4,2,3,'left',1,0);
/*!40000 ALTER TABLE `dims_module_workspace` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_param_block_user`
--

DROP TABLE IF EXISTS `dims_param_block_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_param_block_user` (
  `id_user` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_column` int(10) NOT NULL DEFAULT '0',
  `position` varchar(10) DEFAULT NULL,
  `state` int(2) NOT NULL DEFAULT '0',
  `date_lastvalidate` bigint(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`id_workspace`,`id_module`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_param_block_user`
--

LOCK TABLES `dims_param_block_user` WRITE;
/*!40000 ALTER TABLE `dims_param_block_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_param_block_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_param_choice`
--

DROP TABLE IF EXISTS `dims_param_choice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_param_choice` (
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `displayed_value` varchar(100) DEFAULT NULL,
  KEY `id_module_type` (`id_module_type`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_param_choice`
--

LOCK TABLES `dims_param_choice` WRITE;
/*!40000 ALTER TABLE `dims_param_choice` DISABLE KEYS */;
INSERT INTO `dims_param_choice` VALUES (1,'system_usemacrules','0','non'),(1,'system_usemacrules','1','oui'),(1,'system_recordstats','0','non'),(1,'system_recordstats','1','oui'),(1,'system_set_cache','0','non'),(1,'system_set_cache','1','oui'),(1,'system_generate_htpasswd','0','non'),(1,'system_generate_htpasswd','1','oui'),(1,'system_use_profiles','0','non'),(1,'system_use_profiles','1','oui'),(1,'system_same_login','1','oui'),(1,'system_same_login','0','non'),(2,'showmenu','1','oui'),(2,'showmenu','0','non'),(2,'showblock','1','oui'),(2,'showblock','0','non'),(3,'showmenu','0','non'),(3,'showmenu','1','oui'),(3,'showblock','1','oui'),(3,'showblock','0','non'),(4,'showmenu','1','oui'),(4,'showmenu','0','non'),(4,'showblock','1','oui'),(4,'showblock','0','non'),(5,'showmenu','1','oui'),(5,'showmenu','0','non'),(5,'showblock','1','oui'),(5,'showblock','0','non'),(6,'showmenu','1','oui'),(6,'showmenu','0','non'),(6,'showblock','1','oui'),(6,'showblock','0','non'),(7,'showmenu','1','oui'),(7,'showmenu','0','non'),(7,'showblock','1','oui'),(7,'showblock','0','non'),(8,'showmenu','1','oui'),(8,'showmenu','0','non'),(8,'showblock','1','oui'),(8,'showblock','0','non'),(3,'doc_viewfoldersinblock','1','oui'),(3,'doc_viewfoldersinblock','0','non'),(9,'showmenu','1','oui'),(9,'showmenu','0','non'),(9,'showblock','1','oui'),(9,'showblock','0','non'),(14,'showblock','0','non'),(14,'showmenu','1','oui'),(14,'showmenu','0','non'),(14,'showblock','1','oui'),(15,'showmenu','1','oui'),(15,'showmenu','0','non'),(15,'showblock','1','oui'),(15,'showblock','0','non'),(17,'showmenu','0','non'),(17,'showmenu','1','oui'),(17,'showblock','1','oui'),(17,'showblock','0','non'),(18,'showmenu','1','oui'),(18,'showmenu','0','non'),(18,'showblock','1','oui'),(18,'showblock','0','non'),(19,'showmenu','1','oui'),(19,'showmenu','0','non'),(19,'showblock','1','oui'),(19,'showblock','0','non'),(20,'showmenu','1','oui'),(20,'showmenu','0','non'),(20,'showblock','1','oui'),(20,'showblock','0','non');
/*!40000 ALTER TABLE `dims_param_choice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_param_default`
--

DROP TABLE IF EXISTS `dims_param_default`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_param_default` (
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_module`,`name`),
  KEY `id_module_type` (`id_module_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_param_default`
--

LOCK TABLES `dims_param_default` WRITE;
/*!40000 ALTER TABLE `dims_param_default` DISABLE KEYS */;
INSERT INTO `dims_param_default` VALUES (1,'system_recordstats','0',1),(1,'system_usemacrules','0',1),(1,'system_set_cache','0',1),(1,'system_generate_htpasswd','0',1),(1,'system_use_profiles','1',1),(1,'system_language','french',1),(1,'system_same_login','0',1),(2,'showblock','1',2),(2,'showmenu','1',2),(3,'doc_viewfoldersinblock','1',3),(3,'showblock','1',3),(3,'showmenu','1',3),(4,'forms_export_csvextension','csv',4),(4,'forms_export_fieldseparator',';',4),(4,'forms_export_lineseparator','(cr)(lf)',4),(4,'forms_export_textseparator','\"',4),(4,'showblock','1',4),(4,'showmenu','1',4),(5,'http_timeout','3',6),(5,'nbitemdisplay','20',6),(5,'proxy_host','',6),(5,'proxy_login','',6),(5,'proxy_password','',6),(5,'proxy_port','',6),(5,'showblock','1',6),(5,'showmenu','1',6),(6,'nbnewsdisplay','5',8),(6,'showblock','1',8),(6,'showmenu','1',8),(7,'showblock','1',5),(7,'showmenu','1',5),(11,'showblock','1',9),(11,'showmenu','1',9),(12,'showblock','1',14),(12,'showmenu','1',14),(14,'showblock','1',15),(14,'showmenu','1',15),(18,'doc_viewfoldersinblock','1',3),(18,'showblock','1',3),(18,'showmenu','1',3),(270,'forms_export_csvextension','csv',4),(253,'showblock','1',2),(253,'showmenu','1',2),(276,'forms_export_fieldseparator',';',4),(276,'forms_export_csvextension','csv',4),(277,'doc_viewfoldersinblock','1',3),(259,'showblock','1',2),(259,'showmenu','1',2),(260,'showblock','1',2),(260,'showmenu','1',2),(261,'showblock','1',2),(261,'showmenu','1',2),(265,'showblock','1',2),(265,'showmenu','1',2),(266,'showblock','1',2),(266,'showmenu','1',2),(267,'showblock','1',2),(267,'showmenu','1',2),(268,'showblock','1',2),(268,'showmenu','1',2),(270,'forms_export_fieldseparator',';',4),(270,'forms_export_lineseparator','(cr)(lf)',4),(270,'forms_export_textseparator','\"',4),(270,'showblock','1',4),(270,'showmenu','1',4),(271,'doc_viewfoldersinblock','1',3),(271,'showblock','1',3),(271,'showmenu','1',3),(272,'showblock','1',5),(272,'showmenu','1',5),(277,'showblock','1',3),(274,'doc_viewfoldersinblock','1',3),(274,'showblock','1',3),(274,'showmenu','1',3),(275,'forms_export_csvextension','csv',4),(275,'forms_export_fieldseparator',';',4),(275,'forms_export_lineseparator','(cr)(lf)',4),(275,'forms_export_textseparator','\"',4),(275,'showblock','1',4),(275,'showmenu','1',4),(276,'forms_export_lineseparator','(cr)(lf)',4),(276,'forms_export_textseparator','\"',4),(276,'showblock','1',4),(276,'showmenu','1',4),(277,'showmenu','1',3),(281,'showblock','1',2),(281,'showmenu','1',2),(282,'doc_viewfoldersinblock','1',3),(282,'showblock','1',3),(282,'showmenu','1',3),(286,'doc_viewfoldersinblock','1',3),(286,'showblock','1',3),(286,'showmenu','1',3),(303,'showblock','1',18),(288,'showblock','1',2),(288,'showmenu','1',2),(289,'showblock','1',2),(289,'showmenu','1',2),(290,'forms_export_csvextension','csv',4),(290,'forms_export_fieldseparator',';',4),(290,'forms_export_lineseparator','(cr)(lf)',4),(290,'forms_export_textseparator','\"',4),(290,'showblock','1',4),(290,'showmenu','1',4),(291,'doc_viewfoldersinblock','1',3),(291,'showblock','1',3),(291,'showmenu','1',3),(295,'showblock','1',2),(293,'showblock','1',2),(293,'showmenu','1',2),(294,'showblock','1',2),(294,'showmenu','1',2),(295,'showmenu','1',2),(302,'showmenu','1',2),(302,'showblock','1',2),(303,'showmenu','1',18),(298,'showblock','1',2),(298,'showmenu','1',2),(299,'showblock','1',2),(299,'showmenu','1',2),(304,'doc_viewfoldersinblock','1',3),(301,'showblock','1',2),(301,'showmenu','1',2),(304,'showblock','1',3),(304,'showmenu','1',3),(305,'showblock','1',18),(305,'showmenu','1',18),(306,'doc_viewfoldersinblock','1',3),(306,'showblock','1',3),(306,'showmenu','1',3),(317,'showmenu','1',20),(317,'showblock','1',20),(308,'showblock','1',2),(308,'showmenu','1',2),(309,'showblock','1',18),(309,'showmenu','1',18),(310,'showblock','0',19),(310,'showmenu','0',19),(311,'showblock','1',18),(311,'showmenu','1',18),(312,'showblock','1',18),(312,'showmenu','1',18),(313,'showblock','1',18),(313,'showmenu','1',18),(314,'showblock','1',18),(314,'showmenu','1',18),(315,'doc_viewfoldersinblock','1',3),(315,'showblock','1',3),(315,'showmenu','1',3),(316,'showblock','1',18),(316,'showmenu','1',18);
/*!40000 ALTER TABLE `dims_param_default` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_param_group`
--

DROP TABLE IF EXISTS `dims_param_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_param_group` (
  `id_module` int(10) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `id_group` int(10) unsigned NOT NULL DEFAULT '0',
  `value` varchar(255) DEFAULT NULL,
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_module`,`name`,`id_group`),
  KEY `id_module_type` (`id_module_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_param_group`
--

LOCK TABLES `dims_param_group` WRITE;
/*!40000 ALTER TABLE `dims_param_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_param_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_param_type`
--

DROP TABLE IF EXISTS `dims_param_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_param_type` (
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `default_value` varchar(100) DEFAULT NULL,
  `public` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `description` longtext,
  `label` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_module_type`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_param_type`
--

LOCK TABLES `dims_param_type` WRITE;
/*!40000 ALTER TABLE `dims_param_type` DISABLE KEYS */;
INSERT INTO `dims_param_type` VALUES (1,'system_recordstats','1',0,'','Enregistrement des stats'),(1,'system_usemacrules','0',0,'','Activer le filtrage par Adresse MAC'),(1,'system_language_default','french',0,'','Langage par défaut'),(1,'system_set_cache','0',0,'','Activer le Cache'),(1,'system_groupdepth0_label','',0,'','Intitulé des Groupes de Profondeur 0'),(1,'system_groupdepth1_label','',0,'','Intitulé des Groupes de Profondeur 1'),(1,'system_groupdepth2_label','',0,'','Intitulé des Groupes de Profondeur 2'),(1,'system_groupdepth3_label','',0,'','Intitulé des Groupes de Profondeur 3'),(1,'system_groupdepth4_label','',0,'','Intitulé des Groupes de Profondeur 4'),(1,'system_groupdepth5_label','',0,'','Intitulé des Groupes de Profondeur 5'),(1,'system_groupdepth6_label','',0,'','Intitulé des Groupes de Profondeur 6'),(1,'system_groupdepth7_label','',0,'','Intitulé des Groupes de Profondeur 7'),(1,'system_groupdepth8_label','',0,'','Intitulé des Groupes de Profondeur 8'),(1,'system_groupdepth9_label','',0,'','Intitulé des Groupes de Profondeur 9'),(1,'system_generate_htpasswd','1',0,'','Générer un fichier htpasswd'),(1,'showblock','1',1,'','Afficher le bloc'),(1,'showmenu','1',1,'','Visible dans les modules'),(1,'system_use_profiles','0',0,NULL,'Utiliser les Profils (Utilisateurs)'),(1,'system_language',NULL,1,NULL,'Langue du système'),(1,'system_same_login','0',0,NULL,'Utiliser des logins identiques (fortement déconseillé)'),(2,'showmenu','1',1,NULL,'Visible dans les modules'),(2,'showblock','1',1,NULL,'Afficher le bloc'),(3,'showmenu','1',1,NULL,'Visible dans les modules'),(3,'showblock','1',1,NULL,'Afficher le bloc'),(3,'doc_viewfoldersinblock','1',1,NULL,'Afficher les Dossiers dans le menu'),(4,'showmenu','1',1,NULL,'Visible dans les modules'),(4,'showblock','1',1,NULL,'Afficher le bloc'),(4,'forms_export_csvextension','csv',0,NULL,'Extension du Fichier'),(4,'forms_export_fieldseparator',';',0,NULL,'SÃ©parateur de Champs'),(4,'forms_export_lineseparator','(cr)(lf)',0,NULL,'SÃ©parateur de Ligne'),(4,'forms_export_textseparator','\"',0,NULL,'SÃ©parateur Texte'),(5,'showmenu','1',1,NULL,'Visible dans les modules'),(5,'showblock','1',1,NULL,'Afficher le bloc'),(6,'showmenu','1',1,NULL,'Visible dans les modules'),(6,'showblock','1',1,NULL,'Afficher le bloc'),(6,'nbitemdisplay','5',1,NULL,'Nombre de liens affichÃ©s'),(6,'proxy_host',NULL,0,NULL,'Proxy Host'),(6,'proxy_port',NULL,0,NULL,'Proxy Port'),(6,'proxy_login',NULL,0,NULL,'Proxy Login'),(6,'proxy_password',NULL,0,NULL,'Proxy Password'),(6,'http_timeout','3',0,NULL,'Http Timeout'),(7,'showmenu','1',1,NULL,'Visible dans les modules'),(7,'showblock','1',1,NULL,'Afficher le bloc'),(7,'directory_label_mycontacts',NULL,1,NULL,'LibellÃ© \"Mes Contacts\"'),(7,'directory_label_myfavorites',NULL,1,NULL,'LibellÃ© \"Mes Favoris\"'),(7,'directory_label_mygroup',NULL,1,NULL,'LibellÃ© \"Mon Groupe\"'),(7,'directory_label_search',NULL,1,NULL,'LibellÃ© \"Recherche\"'),(7,'directory_label_users',NULL,1,NULL,'LibellÃ© \"Utilisateurs\"'),(8,'showmenu','1',1,NULL,'Visible dans les modules'),(8,'showblock','1',1,NULL,'Afficher le bloc'),(8,'nbnewsdisplay','5',1,NULL,'Nombre d\'actualitÃ©s affichÃ©es'),(9,'showmenu','1',1,NULL,'Visible dans les modules'),(9,'showblock','1',1,NULL,'Afficher le bloc'),(14,'showmenu','1',1,NULL,'Visible dans les modules'),(14,'showblock','1',1,NULL,'Afficher le bloc'),(15,'showmenu','1',1,NULL,'Visible dans les modules'),(15,'showblock','1',1,NULL,'Afficher le bloc'),(17,'showmenu','1',1,NULL,'Visible dans les modules'),(17,'showblock','1',1,NULL,'Afficher le bloc'),(18,'showmenu','1',1,NULL,'Visible dans les modules'),(18,'showblock','1',1,NULL,'Afficher le bloc'),(19,'showmenu','0',1,NULL,'Visible dans les modules'),(19,'showblock','0',1,NULL,'Afficher le bloc'),(20,'showmenu','1',1,NULL,'Visible dans les modules'),(20,'showblock','1',1,NULL,'Afficher le bloc');
/*!40000 ALTER TABLE `dims_param_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_param_user`
--

DROP TABLE IF EXISTS `dims_param_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_param_user` (
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `value` varchar(255) DEFAULT NULL,
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_module`,`name`,`id_user`),
  KEY `id_module_type` (`id_module_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_param_user`
--

LOCK TABLES `dims_param_user` WRITE;
/*!40000 ALTER TABLE `dims_param_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_param_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_profile`
--

DROP TABLE IF EXISTS `dims_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_profile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_workspace` int(10) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `description` blob,
  `def` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shared` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_profile`
--

LOCK TABLES `dims_profile` WRITE;
/*!40000 ALTER TABLE `dims_profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_project`
--

DROP TABLE IF EXISTS `dims_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(100) DEFAULT NULL,
  `state` tinyint(4) DEFAULT '1',
  `description` text,
  `date_start` bigint(14) NOT NULL DEFAULT '0',
  `date_end` bigint(14) NOT NULL DEFAULT '0',
  `id_create` int(11) NOT NULL DEFAULT '0',
  `id_resp` int(11) NOT NULL DEFAULT '0',
  `id_resp2` int(11) DEFAULT NULL,
  `id_resp3` int(11) DEFAULT NULL,
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `progress` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_module` (`id_module`),
  KEY `id_workspace` (`id_workspace`),
  KEY `id_workspace_2` (`id_workspace`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_project`
--

LOCK TABLES `dims_project` WRITE;
/*!40000 ALTER TABLE `dims_project` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_project_milestone`
--

DROP TABLE IF EXISTS `dims_project_milestone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_project_milestone` (
  `id_project` int(11) NOT NULL DEFAULT '0',
  `id_milestone` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_project`,`id_milestone`),
  KEY `id_project` (`id_project`),
  KEY `id_milestone` (`id_milestone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_project_milestone`
--

LOCK TABLES `dims_project_milestone` WRITE;
/*!40000 ALTER TABLE `dims_project_milestone` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_project_milestone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_project_user`
--

DROP TABLE IF EXISTS `dims_project_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_project_user` (
  `id_project` int(11) NOT NULL DEFAULT '0',
  `id_ref` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_project`,`type`,`id_ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_project_user`
--

LOCK TABLES `dims_project_user` WRITE;
/*!40000 ALTER TABLE `dims_project_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_project_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_role`
--

DROP TABLE IF EXISTS `dims_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_module` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `description` blob,
  `def` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shared` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_workspace` (`id_workspace`)
) ENGINE=MyISAM AUTO_INCREMENT=301 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_role`
--

LOCK TABLES `dims_role` WRITE;
/*!40000 ALTER TABLE `dims_role` DISABLE KEYS */;
INSERT INTO `dims_role` VALUES (1,8,3,'Gestionnaire du Module','',0,0),(2,7,3,'Gestionnaire du Module','',0,0),(4,10,6,'user','',0,1),(5,15,6,'admin','',0,1),(6,16,6,'admin','',0,0),(8,17,5,'gestion rubriques','',0,1),(9,18,5,'admin','',0,1),(10,18,5,'users','',0,1),(11,19,5,'admin','',0,1),(12,10,5,'admin2','',0,1),(13,25,6,'admin','',0,1),(14,27,25,'admin','',0,1),(15,28,25,'admin','',0,1),(16,28,25,'user','',0,1),(17,29,25,'admin','',0,1),(18,29,25,'user','',0,0),(19,30,25,'admin','',0,1),(20,30,25,'user','',0,1),(21,31,25,'admin','',0,1),(22,31,25,'user','',0,1),(23,32,25,'admin','',0,1),(24,32,25,'user','',0,1),(25,33,25,'admin','',0,1),(26,34,25,'admin','',0,1),(27,35,25,'admin','',0,1),(28,34,25,'usertest','',0,1),(29,36,8,'admin','',0,1),(30,37,8,'admin','',0,1),(31,37,8,'user','',0,0),(32,38,8,'admin','',0,1),(33,39,8,'admin','',0,1),(34,40,8,'admin','',0,1),(35,41,9,'admin','',0,0),(36,42,9,'admin','',0,0),(37,43,9,'admin','',0,0),(38,44,9,'admin','',0,0),(39,45,9,'admin','',0,0),(40,46,9,'admin','',0,0),(41,47,10,'admin','',0,0),(42,48,10,'Admin','',0,0),(43,49,10,'Admin','',0,0),(44,50,10,'Admin','',0,0),(45,51,10,'Admin','',0,0),(46,52,11,'Admin','',0,1),(47,53,11,'Admin','',0,0),(48,54,11,'Admin','',0,1),(49,55,11,'Admin','',0,1),(50,58,13,'admin','',0,0),(51,59,13,'admin','',0,0),(52,64,15,'admin','',0,0),(53,65,15,'admin','',0,0),(54,66,15,'admin','',0,0),(55,72,19,'admin','',0,1),(56,73,19,'admin','',0,1),(57,74,19,'admin','',0,1),(58,78,18,'admin','',0,1),(60,80,18,'user GCOB','',0,1),(61,81,18,'admin','',0,1),(62,82,18,'user GCOB','',0,1),(63,83,18,'user','',0,1),(64,76,19,'admin','',0,1),(65,77,19,'admin','',0,1),(66,84,20,'admin','',0,1),(67,85,20,'admin','',0,0),(68,86,21,'admin','',0,1),(69,87,21,'admin','',0,1),(70,88,22,'admin','',0,1),(71,89,22,'admin','',0,1),(72,90,23,'ged admin','',0,1),(73,91,23,'wce GCOB admin','',0,1),(74,92,26,'admin','',0,1),(75,92,26,'user','',0,1),(76,93,26,'admin','',0,1),(77,93,26,'user','',0,1),(78,94,27,'admin','',0,1),(79,95,27,'admin','',0,1),(80,96,27,'admin','',0,1),(81,97,27,'admin','',0,1),(82,98,27,'admin','',0,1),(83,99,27,'admin','',0,1),(84,100,27,'admin','',0,1),(85,101,28,'admin','',0,1),(86,102,28,'admin','',0,1),(87,103,18,'admin','',0,1),(88,103,18,'user  GCOB','',0,1),(89,11,2,'admin','',0,1),(90,6,2,'admin','',0,1),(91,104,18,'admin','',0,1),(92,105,23,'admin','',0,1),(111,86,21,'user','',0,1),(94,105,18,'admin','',0,1),(95,20,5,'admin','',0,1),(96,21,5,'admin','',0,1),(97,22,5,'admin','',0,1),(98,23,5,'admin','',0,1),(99,24,5,'admin','',0,1),(100,80,18,'admin','',0,1),(101,81,18,'user GCOB','',0,1),(102,83,18,'admin','',0,1),(103,104,18,'user','',0,1),(104,105,18,'user','',0,1),(105,82,18,'admin','',0,1),(106,76,19,'user','',0,1),(107,77,19,'user','',0,1),(109,88,22,'user','',0,1),(110,89,22,'user','',0,1),(112,87,21,'user','',0,1),(113,106,5,'admin','',0,1),(114,107,29,'admin','',0,1),(115,107,29,'user','',0,1),(116,108,29,'admin','',0,1),(117,108,29,'user','',0,1),(118,109,30,'admin','',0,1),(119,110,30,'admin','',0,1),(120,30,32,'admin','',0,1),(121,111,32,'admin','',0,1),(122,111,32,'user GCOB','',0,1),(123,112,32,'admin','',0,1),(124,112,32,'user GCOB','',0,1),(125,113,32,'admin','',0,1),(126,113,32,'user GCOB','',0,1),(127,114,32,'admin','',0,1),(128,114,32,'user GCOB','',0,1),(129,115,33,'admin','',0,1),(130,116,33,'admin','',0,1),(131,117,33,'admin','',0,1),(132,118,33,'admin','',0,1),(133,119,33,'admin','',0,1),(134,120,33,'admin','',0,1),(135,30,32,'user GCOB','',0,1),(136,121,34,'admin','',0,1),(137,122,34,'admin','',0,1),(138,123,34,'admin','',0,1),(139,124,34,'admin','',0,1),(140,125,34,'admin','',0,1),(141,126,34,'admin','',0,1),(142,127,34,'admin','',0,1),(143,128,35,'admin','',0,1),(144,129,35,'admin','',0,1),(145,130,35,'admin','',0,1),(146,131,35,'admin','',0,1),(147,132,35,'admin','',0,1),(148,133,35,'admin','',0,1),(149,134,36,'admin','',0,1),(150,135,36,'admin','',0,1),(151,136,36,'admin','',0,1),(152,137,37,'admin','',0,1),(153,138,37,'admin','',0,1),(154,139,37,'admin','',0,1),(155,140,38,'admin','',0,1),(156,141,38,'admin','',0,1),(157,142,38,'admin','',0,1),(158,143,39,'admin','',0,1),(159,144,39,'admin','',0,1),(160,145,39,'admin','',0,1),(161,158,44,'admin','',0,1),(162,159,44,'admin','',0,1),(163,160,44,'admin','',0,1),(164,161,44,'admin','',0,1),(165,158,44,'user','',0,1),(166,159,44,'user','',0,1),(167,160,44,'user','',0,1),(168,161,44,'user','',0,1),(169,162,46,'admin','',0,1),(170,163,46,'admin','',0,1),(171,164,46,'admin','',0,1),(172,165,46,'admin','',0,1),(173,166,47,'admin','',0,1),(174,152,43,'admin','',0,1),(175,153,43,'admin','',0,1),(176,154,43,'admin','',0,1),(177,155,43,'admin','',0,1),(178,156,43,'admin','',0,1),(179,167,45,'admin','',0,1),(180,168,48,'admin','',0,1),(181,169,48,'admin','',0,1),(182,170,48,'admin','',0,1),(183,168,48,'user','',0,1),(184,169,48,'user','',0,1),(185,170,48,'user','',0,1),(186,34,25,'user','',0,1),(187,171,18,'admin','',0,1),(188,171,18,'user GCOB','',0,0),(189,200,19,'admin','',0,1),(190,201,19,'admin','',0,1),(191,186,52,'admin','',0,1),(192,187,52,'admin','',0,1),(193,188,52,'admin','',0,1),(194,189,52,'admin','',0,1),(195,190,52,'admin','',0,1),(196,191,52,'admin','',0,1),(197,192,52,'admin','',0,1),(198,186,52,'GCOB user','',0,1),(199,187,52,'GCOB user','',0,1),(200,188,52,'GCOB user','',0,1),(201,189,52,'GCOB user','',0,1),(202,191,52,'GCOB user','',0,1),(203,192,52,'GCOB user','',0,1),(204,190,52,'GCOB user','',0,0),(205,193,53,'GCOB user','',0,1),(206,194,53,'GCOB user','',0,1),(207,195,53,'GCOB user','',0,1),(208,196,53,'GCOB user','',0,1),(209,197,53,'GCOB user','',0,1),(210,198,53,'GCOB user','',0,1),(211,199,53,'GCOB user','',0,1),(212,193,53,'admin','',0,1),(213,194,53,'admin','',0,1),(214,195,53,'admin','',0,1),(215,196,53,'admin','',0,1),(216,197,53,'admin','',0,1),(217,198,53,'admin','',0,1),(218,199,53,'admin','',0,1),(219,179,51,'admin','',0,1),(220,180,51,'admin','',0,1),(221,181,51,'admin','',0,1),(222,182,51,'admin','',0,1),(223,183,51,'admin','',0,1),(224,184,51,'admin','',0,1),(225,185,51,'admin','',0,1),(226,179,51,'GCOB user','',0,1),(227,180,51,'GCOB user','',0,1),(228,181,51,'GCOB user','',0,1),(229,182,51,'GCOB user','',0,1),(230,183,51,'GCOB user','',0,1),(231,184,51,'GCOB user','',0,1),(232,185,51,'GCOB user','',0,1),(233,202,54,'admin','',0,1),(234,203,54,'admin','',0,1),(235,204,54,'admin','',0,1),(236,205,54,'admin','',0,1),(237,206,54,'admin','',0,1),(238,207,54,'admin','',0,1),(239,208,54,'admin','',0,1),(240,209,55,'admin','',0,1),(241,210,55,'admin','',0,1),(242,211,55,'admin','',0,1),(243,212,55,'admin','',0,1),(244,213,55,'admin','',0,1),(245,214,55,'admin','',0,1),(246,215,55,'admin','',0,1),(247,216,55,'admin','',0,1),(248,222,57,'admin','',0,1),(249,223,57,'admin','',0,1),(250,224,57,'admin','',0,1),(251,225,57,'admin','',0,1),(252,226,57,'admin','',0,1),(253,227,57,'admin','',0,1),(254,228,57,'admin','',0,1),(255,229,58,'admin','',0,1),(256,230,58,'admin','',0,1),(257,231,58,'admin','',0,1),(258,232,58,'admin','',0,1),(259,233,58,'admin','',0,1),(260,234,58,'admin','',0,1),(261,235,58,'admin','',0,1),(262,217,56,'admin','',0,1),(263,218,56,'admin','',0,1),(264,219,56,'admin','',0,1),(265,220,56,'admin','',0,1),(266,221,56,'admin','',0,1),(267,236,59,'admin','',0,1),(268,237,59,'admin','',0,1),(269,238,59,'admin','',0,1),(270,239,59,'admin','',0,1),(271,240,59,'admin','',0,1),(272,241,59,'admin','',0,1),(273,242,59,'admin','',0,1),(274,243,60,'admin','',0,1),(275,244,60,'admin','',0,1),(276,245,60,'admin','',0,1),(277,246,60,'admin','',0,0),(278,247,60,'admin','',0,1),(279,248,60,'admin','',0,1),(280,248,60,'user GCOB','',0,0),(281,248,60,'user GCOB','',0,0),(282,245,60,'user GCOB','',0,1),(283,250,61,'Modérateurs','',0,0),(284,250,61,'Contributeurs','',0,0),(285,269,61,'Wiki CASES ','Wiki CASES ',0,0),(286,270,61,'Forms CASES','Forms CASES',0,0),(287,271,61,'Docts CASES ','Docts CASES ',0,0),(288,272,64,'Agenda LFB ','Agenda LFB ',0,0),(289,273,64,'Wiki LFB','Wiki LFB',0,0),(290,274,64,'Docts LFB','Docts LFB',0,0),(291,275,64,'Forms LFB ','Forms LFB ',0,0),(292,262,64,'WCE LFB','WCE LFB',0,0),(293,276,5,'Module Forms LFB','Module Forms LFB',0,0),(294,252,62,'Module Docts BED','',0,0),(295,253,62,'Module Web BED','',0,0),(296,276,5,'Forms LFB','',0,0),(297,276,5,'Forms LFB','',0,0),(298,277,63,'Doc CdC','',0,0),(299,284,65,'WCE','',0,0),(300,258,63,'test partage','',0,1);
/*!40000 ALTER TABLE `dims_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_role_action`
--

DROP TABLE IF EXISTS `dims_role_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_role_action` (
  `id_role` int(10) unsigned NOT NULL DEFAULT '0',
  `id_action` int(10) unsigned NOT NULL DEFAULT '0',
  `id_module_type` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_action`,`id_module_type`,`id_role`),
  KEY `id_role` (`id_role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_role_action`
--

LOCK TABLES `dims_role_action` WRITE;
/*!40000 ALTER TABLE `dims_role_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_role_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_role_profile`
--

DROP TABLE IF EXISTS `dims_role_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_role_profile` (
  `id_role` int(10) unsigned NOT NULL DEFAULT '0',
  `id_profile` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_profile`,`id_role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_role_profile`
--

LOCK TABLES `dims_role_profile` WRITE;
/*!40000 ALTER TABLE `dims_role_profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_role_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_security_filter`
--

DROP TABLE IF EXISTS `dims_security_filter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_security_filter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) DEFAULT NULL,
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `datefilter` varchar(14) DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `query` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_security_filter`
--

LOCK TABLES `dims_security_filter` WRITE;
/*!40000 ALTER TABLE `dims_security_filter` DISABLE KEYS */;
INSERT INTO `dims_security_filter` VALUES (1,'127.0.0.1',3,'20110109095330','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(2,'127.0.0.1',3,'20110109095330','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(3,'127.0.0.1',3,'20110109095345','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(4,'127.0.0.1',3,'20110109095345','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(5,'127.0.0.1',3,'20110109095400','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(6,'127.0.0.1',3,'20110109095400','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(7,'127.0.0.1',3,'20110109095415','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(8,'127.0.0.1',3,'20110109095415','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(9,'127.0.0.1',3,'20110109095430','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(10,'127.0.0.1',3,'20110109095430','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(11,'127.0.0.1',3,'20110109095445','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(12,'127.0.0.1',3,'20110109095445','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(13,'127.0.0.1',3,'20110109095500','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(14,'127.0.0.1',3,'20110109095500','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(15,'127.0.0.1',3,'20110109095513','GET','/admin.php => url :'),(16,'127.0.0.1',3,'20110109095515','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(17,'127.0.0.1',3,'20110109095515','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(18,'127.0.0.1',3,'20110109095530','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(19,'127.0.0.1',3,'20110109095530','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(20,'127.0.0.1',3,'20110109095545','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(21,'127.0.0.1',3,'20110109095545','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(22,'127.0.0.1',3,'20110109095600','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(23,'127.0.0.1',3,'20110109095600','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(24,'127.0.0.1',3,'20110109095615','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(25,'127.0.0.1',3,'20110109095615','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(26,'127.0.0.1',3,'20110109095630','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(27,'127.0.0.1',3,'20110109095630','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(28,'127.0.0.1',3,'20110109095645','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(29,'127.0.0.1',3,'20110109095645','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(30,'127.0.0.1',3,'20110109095700','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(31,'127.0.0.1',3,'20110109095700','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(32,'127.0.0.1',3,'20110109095713','GET','/admin.php => url :'),(33,'127.0.0.1',3,'20110109095715','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(34,'127.0.0.1',3,'20110109095715','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(35,'127.0.0.1',3,'20110109095730','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(36,'127.0.0.1',3,'20110109095730','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(37,'127.0.0.1',3,'20110109095745','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(38,'127.0.0.1',3,'20110109095745','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(39,'127.0.0.1',3,'20110109095800','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(40,'127.0.0.1',3,'20110109095800','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(41,'127.0.0.1',3,'20110109095815','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(42,'127.0.0.1',3,'20110109095815','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(43,'127.0.0.1',3,'20110109095830','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(44,'127.0.0.1',3,'20110109095830','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(45,'127.0.0.1',3,'20110109095845','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(46,'127.0.0.1',3,'20110109095845','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(47,'127.0.0.1',3,'20110109095900','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(48,'127.0.0.1',3,'20110109095900','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(49,'127.0.0.1',3,'20110109095914','GET','/admin.php => url :'),(50,'127.0.0.1',3,'20110109095915','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(51,'127.0.0.1',3,'20110109095915','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(52,'127.0.0.1',3,'20110109095930','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(53,'127.0.0.1',3,'20110109095930','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(54,'127.0.0.1',3,'20110109095945','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(55,'127.0.0.1',3,'20110109095945','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(56,'127.0.0.1',3,'20110109100000','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(57,'127.0.0.1',3,'20110109100000','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(58,'127.0.0.1',3,'20110109100015','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(59,'127.0.0.1',3,'20110109100015','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(60,'127.0.0.1',3,'20110109100030','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(61,'127.0.0.1',3,'20110109100030','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(62,'127.0.0.1',3,'20110109100045','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(63,'127.0.0.1',3,'20110109100045','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(64,'127.0.0.1',3,'20110109100100','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(65,'127.0.0.1',3,'20110109100100','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(66,'127.0.0.1',3,'20110109100114','GET','/admin.php => url :'),(67,'127.0.0.1',3,'20110109100115','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(68,'127.0.0.1',3,'20110109100116','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(69,'127.0.0.1',3,'20110109100130','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(70,'127.0.0.1',3,'20110109100131','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(71,'127.0.0.1',3,'20110109100145','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(72,'127.0.0.1',3,'20110109100146','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(73,'127.0.0.1',3,'20110109100200','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(74,'127.0.0.1',3,'20110109100201','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(75,'127.0.0.1',3,'20110109100215','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(76,'127.0.0.1',3,'20110109100216','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(77,'127.0.0.1',3,'20110109100230','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(78,'127.0.0.1',3,'20110109100231','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(79,'127.0.0.1',3,'20110109100246','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(80,'127.0.0.1',3,'20110109100246','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(81,'127.0.0.1',3,'20110109100300','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(82,'127.0.0.1',3,'20110109100301','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(83,'127.0.0.1',3,'20110109100314','GET','/admin.php => url :'),(84,'127.0.0.1',3,'20110109100316','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(85,'127.0.0.1',3,'20110109100316','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(86,'127.0.0.1',3,'20110109100331','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(87,'127.0.0.1',3,'20110109100331','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(88,'127.0.0.1',3,'20110109100346','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(89,'127.0.0.1',3,'20110109100346','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(90,'127.0.0.1',3,'20110109100401','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(91,'127.0.0.1',3,'20110109100401','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(92,'127.0.0.1',3,'20110109100416','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(93,'127.0.0.1',3,'20110109100416','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(94,'127.0.0.1',3,'20110109100431','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(95,'127.0.0.1',3,'20110109100431','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(96,'127.0.0.1',3,'20110109100446','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(97,'127.0.0.1',3,'20110109100446','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(98,'127.0.0.1',3,'20110109100501','POST','/admin.php?dims_op=chat_actions&refresh=1 => url :'),(99,'127.0.0.1',3,'20110109100501','POST','/admin.php?dims_op=chat_actions&refresh=2 => url :'),(100,'127.0.0.1',3,'20110109101428','GET','/admin.php?dims_mainmenu=9&cat=0&dims_desktop=block&dims_action=public&init=1&dims_moduleid=1 => url :'),(101,'127.0.0.1',3,'20110109101656','GET','/admin.php => url :');
/*!40000 ALTER TABLE `dims_security_filter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_share`
--

DROP TABLE IF EXISTS `dims_share`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_share` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `id_record` int(10) unsigned NOT NULL DEFAULT '0',
  `id_sub_record` int(11) NOT NULL DEFAULT '0',
  `id_object` int(10) unsigned NOT NULL DEFAULT '0',
  `type_share` tinyint(16) DEFAULT '0',
  `id_share` int(10) unsigned DEFAULT '0',
  `level_share` tinyint(4) NOT NULL DEFAULT '0',
  `id_module_type` int(10) DEFAULT '0',
  `type_from` tinyint(4) NOT NULL DEFAULT '0',
  `id_from` int(11) NOT NULL DEFAULT '0',
  `level_from` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `search` (`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_share`
--

LOCK TABLES `dims_share` WRITE;
/*!40000 ALTER TABLE `dims_share` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_share` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_tag`
--

DROP TABLE IF EXISTS `dims_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `tag` varchar(64) DEFAULT NULL,
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `group` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_workspace` (`id_workspace`)
) ENGINE=MyISAM AUTO_INCREMENT=281 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_tag`
--

LOCK TABLES `dims_tag` WRITE;
/*!40000 ALTER TABLE `dims_tag` DISABLE KEYS */;
INSERT INTO `dims_tag` VALUES (36,3,'_DIMS_PAYS_AF',0,0,0,0),(37,3,'_DIMS_PAYS_ZA',0,0,0,0),(38,3,'_DIMS_PAYS_AL',0,0,0,0),(39,3,'_DIMS_PAYS_DZ',0,0,0,0),(40,3,'_DIMS_PAYS_AD',0,0,0,0),(41,3,'_DIMS_PAYS_AO',0,0,0,0),(42,3,'_DIMS_PAYS_AI',0,0,0,0),(43,3,'_DIMS_PAYS_AQ',0,0,0,0),(44,3,'_DIMS_PAYS_AG',0,0,0,0),(45,3,'_DIMS_PAYS_AN',0,0,0,0),(46,3,'_DIMS_PAYS_SA',0,0,0,0),(47,3,'_DIMS_PAYS_AR',0,0,0,0),(48,3,'_DIMS_PAYS_AM',0,0,0,0),(49,3,'_DIMS_PAYS_AW',0,0,0,0),(50,3,'_DIMS_PAYS_AU',0,0,0,0),(51,3,'_DIMS_PAYS_AT',0,0,0,0),(52,3,'_DIMS_PAYS_AZ',0,0,0,0),(53,3,'_DIMS_PAYS_BJ',0,0,0,0),(54,3,'_DIMS_PAYS_BS',0,0,0,0),(55,3,'_DIMS_PAYS_BH',0,0,0,0),(56,3,'_DIMS_PAYS_BD',0,0,0,0),(57,3,'_DIMS_PAYS_BB',0,0,0,0),(58,3,'_DIMS_PAYS_PW',0,0,0,0),(59,3,'_DIMS_PAYS_BE',0,0,0,0),(60,3,'_DIMS_PAYS_BZ',0,0,0,0),(61,3,'_DIMS_PAYS_BM',0,0,0,0),(62,3,'_DIMS_PAYS_BT',0,0,0,0),(63,3,'_DIMS_PAYS_BY',0,0,0,0),(64,3,'_DIMS_PAYS_MM',0,0,0,0),(65,3,'_DIMS_PAYS_BO',0,0,0,0),(66,3,'_DIMS_PAYS_BA',0,0,0,0),(67,3,'_DIMS_PAYS_BW',0,0,0,0),(68,3,'_DIMS_PAYS_BR',0,0,0,0),(69,3,'_DIMS_PAYS_BN',0,0,0,0),(70,3,'_DIMS_PAYS_BG',0,0,0,0),(71,3,'_DIMS_PAYS_BF',0,0,0,0),(72,3,'_DIMS_PAYS_BI',0,0,0,0),(73,3,'_DIMS_PAYS_CI',0,0,0,0),(74,3,'_DIMS_PAYS_KH',0,0,0,0),(75,3,'_DIMS_PAYS_CM',0,0,0,0),(76,3,'_DIMS_PAYS_CA',0,0,0,0),(77,3,'_DIMS_PAYS_CV',0,0,0,0),(78,3,'_DIMS_PAYS_CL',0,0,0,0),(79,3,'_DIMS_PAYS_CN',0,0,0,0),(80,3,'_DIMS_PAYS_CY',0,0,0,0),(81,3,'_DIMS_PAYS_CO',0,0,0,0),(82,3,'_DIMS_PAYS_KM',0,0,0,0),(83,3,'_DIMS_PAYS_CG',0,0,0,0),(84,3,'_DIMS_PAYS_KP',0,0,0,0),(85,3,'_DIMS_PAYS_KR',0,0,0,0),(86,3,'_DIMS_PAYS_CR',0,0,0,0),(87,3,'_DIMS_PAYS_HR',0,0,0,0),(88,3,'_DIMS_PAYS_CU',0,0,0,0),(89,3,'_DIMS_PAYS_DK',0,0,0,0),(90,3,'_DIMS_PAYS_DJ',0,0,0,0),(91,3,'_DIMS_PAYS_DM',0,0,0,0),(92,3,'_DIMS_PAYS_EG',0,0,0,0),(93,3,'_DIMS_PAYS_AE',0,0,0,0),(94,3,'_DIMS_PAYS_EC',0,0,0,0),(95,3,'_DIMS_PAYS_ER',0,0,0,0),(96,3,'_DIMS_PAYS_ES',0,0,0,0),(97,3,'_DIMS_PAYS_EE',0,0,0,0),(98,3,'_DIMS_PAYS_US',0,0,0,0),(99,3,'_DIMS_PAYS_ET',0,0,0,0),(100,3,'_DIMS_PAYS_FI',0,0,0,0),(101,3,'_DIMS_PAYS_FR',0,0,0,0),(102,3,'_DIMS_PAYS_GE',0,0,0,0),(103,3,'_DIMS_PAYS_GA',0,0,0,0),(104,3,'_DIMS_PAYS_GM',0,0,0,0),(105,3,'_DIMS_PAYS_GH',0,0,0,0),(106,3,'_DIMS_PAYS_GI',0,0,0,0),(107,3,'_DIMS_PAYS_GR',0,0,0,0),(108,3,'_DIMS_PAYS_GD',0,0,0,0),(109,3,'_DIMS_PAYS_GL',0,0,0,0),(110,3,'_DIMS_PAYS_GP',0,0,0,0),(111,3,'_DIMS_PAYS_GU',0,0,0,0),(112,3,'_DIMS_PAYS_GT',0,0,0,0),(113,3,'_DIMS_PAYS_GN',0,0,0,0),(114,3,'_DIMS_PAYS_GQ',0,0,0,0),(115,3,'_DIMS_PAYS_GW',0,0,0,0),(116,3,'_DIMS_PAYS_GY',0,0,0,0),(117,3,'_DIMS_PAYS_GF',0,0,0,0),(118,3,'_DIMS_PAYS_HT',0,0,0,0),(119,3,'_DIMS_PAYS_HN',0,0,0,0),(120,3,'_DIMS_PAYS_HK',0,0,0,0),(121,3,'_DIMS_PAYS_HU',0,0,0,0),(122,3,'_DIMS_PAYS_BV',0,0,0,0),(123,3,'_DIMS_PAYS_CX',0,0,0,0),(124,3,'_DIMS_PAYS_NF',0,0,0,0),(125,3,'_DIMS_PAYS_KY',0,0,0,0),(126,3,'_DIMS_PAYS_CK',0,0,0,0),(127,3,'_DIMS_PAYS_FO',0,0,0,0),(128,3,'_DIMS_PAYS_FK',0,0,0,0),(129,3,'_DIMS_PAYS_FJ',0,0,0,0),(130,3,'_DIMS_PAYS_GS',0,0,0,0),(131,3,'_DIMS_PAYS_HM',0,0,0,0),(132,3,'_DIMS_PAYS_MH',0,0,0,0),(133,3,'_DIMS_PAYS_PN',0,0,0,0),(134,3,'_DIMS_PAYS_SB',0,0,0,0),(135,3,'_DIMS_PAYS_SJ',0,0,0,0),(136,3,'_DIMS_PAYS_TC',0,0,0,0),(137,3,'_DIMS_PAYS_VI',0,0,0,0),(138,3,'_DIMS_PAYS_VG',0,0,0,0),(139,3,'_DIMS_PAYS_CC',0,0,0,0),(140,3,'_DIMS_PAYS_UM',0,0,0,0),(141,3,'_DIMS_PAYS_IN',0,0,0,0),(142,3,'_DIMS_PAYS_ID',0,0,0,0),(143,3,'_DIMS_PAYS_IR',0,0,0,0),(144,3,'_DIMS_PAYS_IQ',0,0,0,0),(145,3,'_DIMS_PAYS_IE',0,0,0,0),(146,3,'_DIMS_PAYS_IS',0,0,0,0),(147,3,'_DIMS_PAYS_IL',0,0,0,0),(148,3,'_DIMS_PAYS_IT',0,0,0,0),(149,3,'_DIMS_PAYS_JM',0,0,0,0),(150,3,'_DIMS_PAYS_JP',0,0,0,0),(151,3,'_DIMS_PAYS_JO',0,0,0,0),(152,3,'_DIMS_PAYS_KZ',0,0,0,0),(153,3,'_DIMS_PAYS_KE',0,0,0,0),(154,3,'_DIMS_PAYS_KG',0,0,0,0),(155,3,'_DIMS_PAYS_KI',0,0,0,0),(156,3,'_DIMS_PAYS_KW',0,0,0,0),(157,3,'_DIMS_PAYS_LA',0,0,0,0),(158,3,'_DIMS_PAYS_LS',0,0,0,0),(159,3,'_DIMS_PAYS_LV',0,0,0,0),(160,3,'_DIMS_PAYS_LB',0,0,0,0),(161,3,'_DIMS_PAYS_LR',0,0,0,0),(162,3,'_DIMS_PAYS_LY',0,0,0,0),(163,3,'_DIMS_PAYS_LI',0,0,0,0),(164,3,'_DIMS_PAYS_LT',0,0,0,0),(165,3,'_DIMS_PAYS_LU',0,0,0,0),(166,3,'_DIMS_PAYS_MO',0,0,0,0),(167,3,'_DIMS_PAYS_MG',0,0,0,0),(168,3,'_DIMS_PAYS_MY',0,0,0,0),(169,3,'_DIMS_PAYS_MW',0,0,0,0),(170,3,'_DIMS_PAYS_MV',0,0,0,0),(171,3,'_DIMS_PAYS_ML',0,0,0,0),(172,3,'_DIMS_PAYS_MT',0,0,0,0),(173,3,'_DIMS_PAYS_MP',0,0,0,0),(174,3,'_DIMS_PAYS_MA',0,0,0,0),(175,3,'_DIMS_PAYS_MQ',0,0,0,0),(176,3,'_DIMS_PAYS_MU',0,0,0,0),(177,3,'_DIMS_PAYS_MR',0,0,0,0),(178,3,'_DIMS_PAYS_YT',0,0,0,0),(179,3,'_DIMS_PAYS_MX',0,0,0,0),(180,3,'_DIMS_PAYS_FM',0,0,0,0),(181,3,'_DIMS_PAYS_MD',0,0,0,0),(182,3,'_DIMS_PAYS_MC',0,0,0,0),(183,3,'_DIMS_PAYS_MN',0,0,0,0),(184,3,'_DIMS_PAYS_MS',0,0,0,0),(185,3,'_DIMS_PAYS_MZ',0,0,0,0),(186,3,'_DIMS_PAYS_NP',0,0,0,0),(187,3,'_DIMS_PAYS_NA',0,0,0,0),(188,3,'_DIMS_PAYS_NR',0,0,0,0),(189,3,'_DIMS_PAYS_NI',0,0,0,0),(190,3,'_DIMS_PAYS_NE',0,0,0,0),(191,3,'_DIMS_PAYS_NG',0,0,0,0),(192,3,'_DIMS_PAYS_NU',0,0,0,0),(193,3,'_DIMS_PAYS_NO',0,0,0,0),(194,3,'_DIMS_PAYS_NC',0,0,0,0),(195,3,'_DIMS_PAYS_NZ',0,0,0,0),(196,3,'_DIMS_PAYS_OM',0,0,0,0),(197,3,'_DIMS_PAYS_UG',0,0,0,0),(198,3,'_DIMS_PAYS_UZ',0,0,0,0),(199,3,'_DIMS_PAYS_PE',0,0,0,0),(200,3,'_DIMS_PAYS_PK',0,0,0,0),(201,3,'_DIMS_PAYS_PA',0,0,0,0),(202,3,'_DIMS_PAYS_PG',0,0,0,0),(203,3,'_DIMS_PAYS_PY',0,0,0,0),(204,3,'_DIMS_PAYS_NL',0,0,0,0),(205,3,'_DIMS_PAYS_PH',0,0,0,0),(206,3,'_DIMS_PAYS_PL',0,0,0,0),(207,3,'_DIMS_PAYS_PF',0,0,0,0),(208,3,'_DIMS_PAYS_PR',0,0,0,0),(209,3,'_DIMS_PAYS_PT',0,0,0,0),(210,3,'_DIMS_PAYS_QA',0,0,0,0),(211,3,'_DIMS_PAYS_CF',0,0,0,0),(212,3,'_DIMS_PAYS_CD',0,0,0,0),(213,3,'_DIMS_PAYS_DO',0,0,0,0),(214,3,'_DIMS_PAYS_CZ',0,0,0,0),(215,3,'_DIMS_PAYS_RE',0,0,0,0),(216,3,'_DIMS_PAYS_RO',0,0,0,0),(217,3,'_DIMS_PAYS_GB',0,0,0,0),(218,3,'_DIMS_PAYS_RU',0,0,0,0),(219,3,'_DIMS_PAYS_RW',0,0,0,0),(220,3,'_DIMS_PAYS_SN',0,0,0,0),(221,3,'_DIMS_PAYS_EH',0,0,0,0),(222,3,'_DIMS_PAYS_KN',0,0,0,0),(223,3,'_DIMS_PAYS_SM',0,0,0,0),(224,3,'_DIMS_PAYS_PM',0,0,0,0),(225,3,'_DIMS_PAYS_VA',0,0,0,0),(226,3,'_DIMS_PAYS_VC',0,0,0,0),(227,3,'_DIMS_PAYS_SH',0,0,0,0),(228,3,'_DIMS_PAYS_LC',0,0,0,0),(229,3,'_DIMS_PAYS_SV',0,0,0,0),(230,3,'_DIMS_PAYS_WS',0,0,0,0),(231,3,'_DIMS_PAYS_AS',0,0,0,0),(232,3,'_DIMS_PAYS_ST',0,0,0,0),(233,3,'_DIMS_PAYS_SC',0,0,0,0),(234,3,'_DIMS_PAYS_SL',0,0,0,0),(235,3,'_DIMS_PAYS_SG',0,0,0,0),(236,3,'_DIMS_PAYS_SI',0,0,0,0),(237,3,'_DIMS_PAYS_SK',0,0,0,0),(238,3,'_DIMS_PAYS_SO',0,0,0,0),(239,3,'_DIMS_PAYS_SD',0,0,0,0),(240,3,'_DIMS_PAYS_LK',0,0,0,0),(241,3,'_DIMS_PAYS_SE',0,0,0,0),(242,3,'_DIMS_PAYS_CH',0,0,0,0),(243,3,'_DIMS_PAYS_SR',0,0,0,0),(244,3,'_DIMS_PAYS_SZ',0,0,0,0),(245,3,'_DIMS_PAYS_SY',0,0,0,0),(246,3,'_DIMS_PAYS_TW',0,0,0,0),(247,3,'_DIMS_PAYS_TJ',0,0,0,0),(248,3,'_DIMS_PAYS_TZ',0,0,0,0),(249,3,'_DIMS_PAYS_TD',0,0,0,0),(250,3,'_DIMS_PAYS_TF',0,0,0,0),(251,3,'_DIMS_PAYS_IO',0,0,0,0),(252,3,'_DIMS_PAYS_TH',0,0,0,0),(253,3,'_DIMS_PAYS_TL',0,0,0,0),(254,3,'_DIMS_PAYS_TG',0,0,0,0),(255,3,'_DIMS_PAYS_TK',0,0,0,0),(256,3,'_DIMS_PAYS_TO',0,0,0,0),(257,3,'_DIMS_PAYS_TT',0,0,0,0),(258,3,'_DIMS_PAYS_TN',0,0,0,0),(259,3,'_DIMS_PAYS_TM',0,0,0,0),(260,3,'_DIMS_PAYS_TR',0,0,0,0),(261,3,'_DIMS_PAYS_TV',0,0,0,0),(262,3,'_DIMS_PAYS_UA',0,0,0,0),(263,3,'_DIMS_PAYS_UY',0,0,0,0),(264,3,'_DIMS_PAYS_VU',0,0,0,0),(265,3,'_DIMS_PAYS_VE',0,0,0,0),(266,3,'_DIMS_PAYS_VN',0,0,0,0),(267,3,'_DIMS_PAYS_WF',0,0,0,0),(268,3,'_DIMS_PAYS_YE',0,0,0,0),(269,3,'_DIMS_PAYS_YU',0,0,0,0),(270,3,'_DIMS_PAYS_ZM',0,0,0,0),(271,3,'_DIMS_PAYS_ZW',0,0,0,0),(272,3,'_DIMS_PAYS_MK',0,0,0,0),(273,4,'2010',0,64,0,0),(280,4,'2011',0,64,0,0);
/*!40000 ALTER TABLE `dims_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_tag_index`
--

DROP TABLE IF EXISTS `dims_tag_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_tag_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tag` bigint(20) NOT NULL,
  `id_record` int(10) NOT NULL DEFAULT '0',
  `id_object` int(10) NOT NULL DEFAULT '0',
  `id_user` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_module` int(10) NOT NULL DEFAULT '0',
  `id_module_type` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_global` (`id_module`),
  KEY `index2` (`id_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_tag_index`
--

LOCK TABLES `dims_tag_index` WRITE;
/*!40000 ALTER TABLE `dims_tag_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_tag_index` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_task`
--

DROP TABLE IF EXISTS `dims_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(10) NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `nb_place` int(11) DEFAULT '1',
  `priority` tinyint(4) NOT NULL DEFAULT '0',
  `description` text,
  `date_start` bigint(14) NOT NULL DEFAULT '0',
  `date_end` bigint(14) NOT NULL DEFAULT '0',
  `heuredeb` time DEFAULT NULL,
  `heurefin` time DEFAULT NULL,
  `id_project` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_tiers` int(11) DEFAULT NULL,
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  `progress` tinyint(4) NOT NULL DEFAULT '0',
  `time` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_project` (`id_project`),
  KEY `id_parent` (`id_parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_task`
--

LOCK TABLES `dims_task` WRITE;
/*!40000 ALTER TABLE `dims_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_task_action`
--

DROP TABLE IF EXISTS `dims_task_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_task_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_task` int(10) NOT NULL,
  `id_project` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `time` double NOT NULL DEFAULT '0',
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `priority` tinyint(4) NOT NULL DEFAULT '0',
  `comment` text,
  `date_create` bigint(14) NOT NULL DEFAULT '0',
  `date_action` bigint(14) NOT NULL DEFAULT '0',
  `date_validate` bigint(14) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL,
  `id_workspace` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_task` (`id_task`),
  KEY `id_project` (`id_project`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_task_action`
--

LOCK TABLES `dims_task_action` WRITE;
/*!40000 ALTER TABLE `dims_task_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_task_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_task_task`
--

DROP TABLE IF EXISTS `dims_task_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_task_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_task` int(11) NOT NULL DEFAULT '0',
  `id_task_needed` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_task_task`
--

LOCK TABLES `dims_task_task` WRITE;
/*!40000 ALTER TABLE `dims_task_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_task_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_task_user`
--

DROP TABLE IF EXISTS `dims_task_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_task_user` (
  `id_task` int(11) NOT NULL DEFAULT '0',
  `id_ref` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_task`,`id_ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_task_user`
--

LOCK TABLES `dims_task_user` WRITE;
/*!40000 ALTER TABLE `dims_task_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_task_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_ticket`
--

DROP TABLE IF EXISTS `dims_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_ticket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `message` longtext,
  `needed_validation` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `time_limit` varchar(14) DEFAULT NULL,
  `delivery_notification` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  `object_label` varchar(255) DEFAULT NULL,
  `timestp` varchar(14) DEFAULT NULL,
  `lastreply_timestp` varchar(14) DEFAULT NULL,
  `count_read` int(10) unsigned NOT NULL DEFAULT '0',
  `count_replies` int(10) unsigned NOT NULL DEFAULT '0',
  `id_object` int(10) DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `id_record` int(11) NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `root_id` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `id_module_type` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_ticket`
--

LOCK TABLES `dims_ticket` WRITE;
/*!40000 ALTER TABLE `dims_ticket` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_ticket_dest`
--

DROP TABLE IF EXISTS `dims_ticket_dest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_ticket_dest` (
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_ticket` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  KEY `id_user` (`id_user`),
  KEY `id_ticket` (`id_ticket`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_ticket_dest`
--

LOCK TABLES `dims_ticket_dest` WRITE;
/*!40000 ALTER TABLE `dims_ticket_dest` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_ticket_dest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_ticket_status`
--

DROP TABLE IF EXISTS `dims_ticket_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_ticket_status` (
  `id_ticket` int(10) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timestp` varchar(14) DEFAULT NULL,
  KEY `id_ticket` (`id_ticket`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_ticket_status`
--

LOCK TABLES `dims_ticket_status` WRITE;
/*!40000 ALTER TABLE `dims_ticket_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_ticket_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_ticket_watch`
--

DROP TABLE IF EXISTS `dims_ticket_watch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_ticket_watch` (
  `id_ticket` int(10) unsigned NOT NULL DEFAULT '0',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `notify` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_ticket`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_ticket_watch`
--

LOCK TABLES `dims_ticket_watch` WRITE;
/*!40000 ALTER TABLE `dims_ticket_watch` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_ticket_watch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_todo`
--

DROP TABLE IF EXISTS `dims_todo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `id_record` int(11) DEFAULT NULL COMMENT 'id appelé dans l''objet associé',
  `id_object` int(11) DEFAULT NULL COMMENT 'Objet lié',
  `id_parent` int(11) DEFAULT NULL COMMENT 'tache associée',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_module_type` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`user_from`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_todo`
--

LOCK TABLES `dims_todo` WRITE;
/*!40000 ALTER TABLE `dims_todo` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_todo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_todo_dest`
--

DROP TABLE IF EXISTS `dims_todo_dest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_todo_dest` (
  `id_todo` int(11) NOT NULL,
  `id_object` int(11) NOT NULL,
  `id_record` int(11) NOT NULL,
  PRIMARY KEY (`id_todo`,`id_object`,`id_record`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_todo_dest`
--

LOCK TABLES `dims_todo_dest` WRITE;
/*!40000 ALTER TABLE `dims_todo_dest` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_todo_dest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user`
--

DROP TABLE IF EXISTS `dims_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_type` int(10) DEFAULT '-1',
  `id_ldap` int(10) unsigned DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `login` varchar(32) DEFAULT NULL,
  `password` varchar(48) DEFAULT NULL,
  `date_creation` varchar(14) DEFAULT NULL,
  `date_expire` varchar(14) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `fax` varchar(32) DEFAULT NULL,
  `comments` text,
  `address` text,
  `mobile` varchar(32) DEFAULT NULL,
  `service` varchar(64) DEFAULT NULL,
  `function` varchar(64) DEFAULT NULL,
  `postalcode` varchar(16) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `ticketsbyemail` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `color` varchar(16) DEFAULT NULL,
  `timezone` double NOT NULL DEFAULT '0',
  `defaultworkspace` int(11) NOT NULL DEFAULT '0',
  `presentation` text,
  `jabberId` varchar(100) DEFAULT NULL,
  `id_contact` int(11) NOT NULL,
  `lastconnexion` bigint(14) NOT NULL,
  `beforelastconnexion` bigint(14) NOT NULL,
  `background` varchar(50) DEFAULT NULL,
  `lang` int(11) NOT NULL DEFAULT '1',
  `id_group` int(11) NOT NULL,
  `code_of_conduct` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login_unique` (`login`),
  KEY `lastname` (`lastname`),
  KEY `FT` (`city`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user`
--

LOCK TABLES `dims_user` WRITE;
/*!40000 ALTER TABLE `dims_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user_abonnt`
--

DROP TABLE IF EXISTS `dims_user_abonnt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user_abonnt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_facture` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date_deb` varchar(20) DEFAULT NULL,
  `date_fin` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user_abonnt`
--

LOCK TABLES `dims_user_abonnt` WRITE;
/*!40000 ALTER TABLE `dims_user_abonnt` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user_abonnt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user_action_log`
--

DROP TABLE IF EXISTS `dims_user_action_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user_action_log` (
  `id_user` int(10) unsigned DEFAULT '0',
  `id_action` int(10) unsigned DEFAULT '0',
  `id_module_type` int(10) unsigned DEFAULT '0',
  `id_module` int(10) unsigned DEFAULT '0',
  `id_record` int(11) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `timestp` varchar(14) DEFAULT NULL,
  `id_object` int(11) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `content` text,
  KEY `id_user` (`id_user`),
  KEY `id_module` (`id_module`),
  KEY `id_user_2` (`id_user`),
  KEY `id_module_2` (`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user_action_log`
--

LOCK TABLES `dims_user_action_log` WRITE;
/*!40000 ALTER TABLE `dims_user_action_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user_action_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user_address`
--

DROP TABLE IF EXISTS `dims_user_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user_address` (
  `id_users` int(11) NOT NULL,
  `id_address` int(11) NOT NULL,
  PRIMARY KEY (`id_users`,`id_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user_address`
--

LOCK TABLES `dims_user_address` WRITE;
/*!40000 ALTER TABLE `dims_user_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user_ask_password`
--

DROP TABLE IF EXISTS `dims_user_ask_password`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user_ask_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestp_create` int(11) NOT NULL,
  `timestp_modify` int(11) NOT NULL,
  `id_ask` int(11) NOT NULL,
  `mail` text,
  `link` text,
  `validated` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user_ask_password`
--

LOCK TABLES `dims_user_ask_password` WRITE;
/*!40000 ALTER TABLE `dims_user_ask_password` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user_ask_password` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user_car`
--

DROP TABLE IF EXISTS `dims_user_car`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user_car` (
  `id_car` int(11) NOT NULL,
  `id_users` int(11) NOT NULL,
  PRIMARY KEY (`id_car`,`id_users`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user_car`
--

LOCK TABLES `dims_user_car` WRITE;
/*!40000 ALTER TABLE `dims_user_car` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user_car` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user_facture`
--

DROP TABLE IF EXISTS `dims_user_facture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user_facture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `date_paiement` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user_facture`
--

LOCK TABLES `dims_user_facture` WRITE;
/*!40000 ALTER TABLE `dims_user_facture` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user_facture` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user_filter_rules`
--

DROP TABLE IF EXISTS `dims_user_filter_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user_filter_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_workspace` int(10) DEFAULT NULL,
  `condition` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `fieldname` varchar(255) DEFAULT NULL,
  `operator` varchar(4) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user_filter_rules`
--

LOCK TABLES `dims_user_filter_rules` WRITE;
/*!40000 ALTER TABLE `dims_user_filter_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user_filter_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user_history_object`
--

DROP TABLE IF EXISTS `dims_user_history_object`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user_history_object` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_object` int(11) NOT NULL DEFAULT '0',
  `id_record` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user_history_object`
--

LOCK TABLES `dims_user_history_object` WRITE;
/*!40000 ALTER TABLE `dims_user_history_object` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user_history_object` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user_interest`
--

DROP TABLE IF EXISTS `dims_user_interest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user_interest` (
  `id_user` int(11) NOT NULL,
  `id_int` int(11) NOT NULL,
  PRIMARY KEY (`id_user`,`id_int`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user_interest`
--

LOCK TABLES `dims_user_interest` WRITE;
/*!40000 ALTER TABLE `dims_user_interest` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user_interest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user_type`
--

DROP TABLE IF EXISTS `dims_user_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `displayed_label` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user_type`
--

LOCK TABLES `dims_user_type` WRITE;
/*!40000 ALTER TABLE `dims_user_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user_type_fields`
--

DROP TABLE IF EXISTS `dims_user_type_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user_type_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_type` int(10) unsigned NOT NULL DEFAULT '0',
  `label` varchar(100) DEFAULT NULL,
  `type_field` varchar(100) DEFAULT NULL,
  `size_field` int(10) unsigned DEFAULT NULL,
  `pos` int(10) unsigned DEFAULT '0',
  `valeurs` longtext,
  `displayed_label` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user_type_fields`
--

LOCK TABLES `dims_user_type_fields` WRITE;
/*!40000 ALTER TABLE `dims_user_type_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user_type_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_user_works`
--

DROP TABLE IF EXISTS `dims_user_works`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_user_works` (
  `id_user` int(11) NOT NULL,
  `id_works` int(11) NOT NULL,
  PRIMARY KEY (`id_user`,`id_works`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_user_works`
--

LOCK TABLES `dims_user_works` WRITE;
/*!40000 ALTER TABLE `dims_user_works` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_user_works` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_workflow`
--

DROP TABLE IF EXISTS `dims_workflow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_workflow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_module` int(10) unsigned NOT NULL DEFAULT '0',
  `id_record` int(10) unsigned NOT NULL DEFAULT '0',
  `id_object` int(10) unsigned NOT NULL DEFAULT '0',
  `type_workflow` varchar(16) DEFAULT NULL,
  `id_workflow` int(10) unsigned DEFAULT '0',
  `id_module_type` int(10) DEFAULT '0',
  `id_action` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `search` (`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_workflow`
--

LOCK TABLES `dims_workflow` WRITE;
/*!40000 ALTER TABLE `dims_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_workflow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_works`
--

DROP TABLE IF EXISTS `dims_works`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_works` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `label_works` text,
  `id_module` int(11) NOT NULL,
  `id_type_module` int(11) NOT NULL,
  `niveau_qualif` varchar(11) DEFAULT NULL,
  `competence` mediumtext,
  `secteur_activité_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_works`
--

LOCK TABLES `dims_works` WRITE;
/*!40000 ALTER TABLE `dims_works` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_works` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_workspace`
--

DROP TABLE IF EXISTS `dims_workspace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_workspace` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_workspace` int(10) unsigned DEFAULT '0',
  `label` varchar(255) DEFAULT NULL,
  `code` varchar(64) DEFAULT NULL,
  `system` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `protected` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `parents` varchar(255) DEFAULT NULL,
  `iprules` text,
  `macrules` text,
  `admin_template` varchar(255) DEFAULT NULL,
  `web_template` varchar(255) DEFAULT NULL,
  `depth` int(10) NOT NULL DEFAULT '0',
  `mustdefinerule` tinyint(1) unsigned DEFAULT '0',
  `typegroup` varchar(4) DEFAULT NULL,
  `admin` tinyint(1) unsigned DEFAULT '1',
  `public` tinyint(1) unsigned DEFAULT '0',
  `web` tinyint(1) unsigned DEFAULT '0',
  `admin_domainlist` longtext,
  `title` varchar(255) DEFAULT NULL,
  `meta_description` longtext,
  `meta_keywords` longtext,
  `meta_author` varchar(255) DEFAULT NULL,
  `meta_copyright` varchar(255) DEFAULT NULL,
  `meta_robots` varchar(255) DEFAULT NULL,
  `web_domainlist` longtext,
  `ssl` tinyint(1) NOT NULL DEFAULT '0',
  `project` tinyint(4) NOT NULL DEFAULT '0',
  `planning` tinyint(4) NOT NULL DEFAULT '0',
  `contact` tinyint(4) NOT NULL DEFAULT '0',
  `tickets` tinyint(4) NOT NULL DEFAULT '1',
  `newsletter` tinyint(4) NOT NULL DEFAULT '0',
  `background` varchar(50) DEFAULT NULL,
  `sitemap` longtext,
  `email` varchar(255) DEFAULT NULL,
  `signature` text,
  `email_noreply` varchar(255) DEFAULT NULL,
  `id_tiers` int(11) NOT NULL DEFAULT '0',
  `contact_intel` tinyint(4) NOT NULL DEFAULT '1',
  `contact_docs` tinyint(4) NOT NULL DEFAULT '1',
  `contact_tags` tinyint(4) NOT NULL DEFAULT '1',
  `contact_comments` tinyint(4) NOT NULL DEFAULT '1',
  `contact_activeent` tinyint(4) NOT NULL DEFAULT '1',
  `contact_outlook` tinyint(4) NOT NULL DEFAULT '1',
  `code_of_conduct` tinyint(4) NOT NULL DEFAULT '0',
  `share_info` tinyint(1) DEFAULT '1',
  `id_lang` int(11) NOT NULL DEFAULT '1',
  `newsletter_sender_email` varchar(200) DEFAULT NULL,
  `newsletter_id_domain` int(11) NOT NULL DEFAULT '0',
  `newsletter_header_registration` text,
  `newsletter_footer_registration` text,
  `newsletter_accepted_subject` text,
  `newsletter_accepted_content` text,
  `newsletter_unsubscribe_subject` text,
  `newsletter_unsubscribe_content` text,
  `newsletter_message_registration` text,
  `events_sender_email` varchar(200) DEFAULT NULL,
  `events_signature` text,
  `events_mail2_subject` varchar(255) DEFAULT NULL,
  `events_mail2_content` text,
  `events_mail3_subject` varchar(255) DEFAULT NULL,
  `events_mail3_content` text,
  `events_mail4_subject` varchar(255) DEFAULT NULL,
  `events_mail4_content` text,
  `events_mail5_subject` varchar(255) DEFAULT NULL,
  `events_mail5_content` text,
  `events_mail6_subject` varchar(255) DEFAULT NULL,
  `events_mail6_content` text,
  `events_mail7_subject` varchar(255) DEFAULT NULL,
  `events_mail7_content` text,
  `events_mail8_subject` varchar(255) DEFAULT NULL,
  `events_mail8_content` text,
  `events_mail9_subject` varchar(255) DEFAULT NULL,
  `events_mail9_content` text,
  `events_mail10_subject` varchar(255) DEFAULT NULL,
  `events_mail10_content` text,
  `events_mail11_subject` varchar(255) DEFAULT NULL,
  `events_mail11_content` text,
  `events_mail1_subject` varchar(255) DEFAULT NULL,
  `events_mail1_content` text,
  `id_workspace_feedback` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `system` (`system`),
  KEY `depth` (`depth`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_workspace`
--

LOCK TABLES `dims_workspace` WRITE;
/*!40000 ALTER TABLE `dims_workspace` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_workspace` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_workspace_domain`
--

DROP TABLE IF EXISTS `dims_workspace_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_workspace_domain` (
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  `id_domain` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(4) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id_workspace`,`id_domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_workspace_domain`
--

LOCK TABLES `dims_workspace_domain` WRITE;
/*!40000 ALTER TABLE `dims_workspace_domain` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_workspace_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_workspace_group`
--

DROP TABLE IF EXISTS `dims_workspace_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_workspace_group` (
  `id_group` int(10) NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_profile` int(10) unsigned DEFAULT '0',
  `adminlevel` tinyint(3) unsigned DEFAULT '0',
  `activesearch` tinyint(4) NOT NULL DEFAULT '1',
  `activeticket` tinyint(4) NOT NULL DEFAULT '1',
  `activeprofil` tinyint(4) NOT NULL DEFAULT '1',
  `activeannot` tinyint(4) NOT NULL DEFAULT '1',
  `activecontact` tinyint(4) NOT NULL DEFAULT '1',
  `activeproject` tinyint(4) NOT NULL DEFAULT '1',
  `activeplanning` tinyint(4) NOT NULL DEFAULT '1',
  `activenewsletter` tinyint(4) NOT NULL DEFAULT '0',
  `activeevent` tinyint(1) NOT NULL DEFAULT '0',
  `activeeventstep` int(11) NOT NULL DEFAULT '0',
  `activeeventemail` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_workspace`,`id_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_workspace_group`
--

LOCK TABLES `dims_workspace_group` WRITE;
/*!40000 ALTER TABLE `dims_workspace_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_workspace_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_workspace_group_role`
--

DROP TABLE IF EXISTS `dims_workspace_group_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_workspace_group_role` (
  `id_group` int(10) DEFAULT NULL,
  `id_workspace` int(10) DEFAULT NULL,
  `id_role` int(10) unsigned DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_workspace_group_role`
--

LOCK TABLES `dims_workspace_group_role` WRITE;
/*!40000 ALTER TABLE `dims_workspace_group_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_workspace_group_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_workspace_share`
--

DROP TABLE IF EXISTS `dims_workspace_share`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_workspace_share` (
  `id_from` int(11) NOT NULL DEFAULT '0',
  `id_to` int(11) NOT NULL DEFAULT '0',
  `id_object` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id_from`,`id_to`,`id_object`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_workspace_share`
--

LOCK TABLES `dims_workspace_share` WRITE;
/*!40000 ALTER TABLE `dims_workspace_share` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_workspace_share` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_workspace_template`
--

DROP TABLE IF EXISTS `dims_workspace_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_workspace_template` (
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `template` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_workspace`,`template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_workspace_template`
--

LOCK TABLES `dims_workspace_template` WRITE;
/*!40000 ALTER TABLE `dims_workspace_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_workspace_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_workspace_user`
--

DROP TABLE IF EXISTS `dims_workspace_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_workspace_user` (
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) unsigned NOT NULL DEFAULT '0',
  `id_profile` int(10) unsigned NOT NULL DEFAULT '0',
  `adminlevel` tinyint(3) unsigned DEFAULT '0',
  `activesearch` tinyint(4) NOT NULL DEFAULT '1',
  `activeticket` tinyint(4) NOT NULL DEFAULT '1',
  `activeprofil` tinyint(4) NOT NULL DEFAULT '1',
  `activeannot` tinyint(4) NOT NULL DEFAULT '1',
  `activecontact` tinyint(4) NOT NULL DEFAULT '1',
  `activeproject` tinyint(4) NOT NULL DEFAULT '1',
  `activeplanning` tinyint(4) NOT NULL DEFAULT '1',
  `activenewsletter` tinyint(4) NOT NULL DEFAULT '0',
  `activeevent` tinyint(4) NOT NULL DEFAULT '0',
  `activeeventstep` int(11) NOT NULL DEFAULT '0',
  `activeeventemail` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`id_workspace`),
  KEY `id_profile` (`id_profile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_workspace_user`
--

LOCK TABLES `dims_workspace_user` WRITE;
/*!40000 ALTER TABLE `dims_workspace_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `dims_workspace_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dims_workspace_user_role`
--

DROP TABLE IF EXISTS `dims_workspace_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dims_workspace_user_role` (
  `id_user` int(10) unsigned NOT NULL DEFAULT '0',
  `id_workspace` int(10) NOT NULL DEFAULT '0',
  `id_role` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`id_workspace`,`id_role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dims_workspace_user_role`
--

LOCK TABLES `dims_workspace_user_role` WRITE;
/*!40000 ALTER TABLE `dims_workspace_user_role` DISABLE KEYS */;
INSERT INTO `dims_workspace_user_role` VALUES (0,64,288),(0,64,290),(0,64,291),(0,64,292),(2,3,1),(2,3,2),(2,5,9),(2,5,10),(2,5,293),(2,5,296),(2,18,9),(2,18,10),(2,18,58),(2,18,59),(2,18,60),(2,18,61),(2,18,62),(2,18,63),(2,23,62),(2,23,72),(2,23,73),(2,61,285),(2,61,286),(2,61,287),(2,62,294),(2,62,295),(2,63,298),(2,64,288),(2,64,290),(2,64,291),(2,64,292),(13,8,29),(13,8,30),(13,8,32),(13,8,33),(13,8,34),(13,9,35),(13,9,36),(13,9,37),(13,9,38),(13,9,39),(13,9,40),(14,9,35),(14,9,36),(14,9,37),(14,9,38),(14,9,39),(14,9,40),(16,18,58),(16,18,59),(16,18,60),(16,18,61),(16,18,62),(16,18,63),(16,20,58),(16,20,66),(16,20,67),(16,21,58),(16,21,63),(16,21,68),(16,21,69),(16,22,58),(16,22,59),(16,22,63),(16,22,70),(16,22,71),(16,22,93),(16,23,62),(16,23,72),(16,23,73),(16,23,91),(16,23,92),(17,18,58),(17,18,59),(17,18,60),(17,18,61),(17,18,62),(17,18,63),(17,20,58),(17,20,66),(17,20,67),(17,21,58),(17,21,63),(17,21,68),(17,21,69),(17,22,58),(17,22,59),(17,22,63),(17,22,70),(17,22,71),(17,22,93),(17,23,62),(17,23,72),(17,23,73),(17,23,91),(17,23,92),(19,2,3),(19,2,89),(19,2,90),(19,5,7),(19,5,9),(19,5,11),(19,5,12),(19,5,95),(19,5,96),(19,5,97),(19,5,98),(19,5,99),(19,5,113),(19,8,29),(19,8,30),(19,8,32),(19,8,33),(19,8,34),(19,9,35),(19,9,36),(19,9,37),(19,9,38),(19,9,39),(19,9,40),(19,11,46),(19,11,47),(19,11,48),(19,11,49),(19,18,9),(19,18,58),(19,18,59),(19,18,60),(19,18,61),(19,18,62),(19,18,63),(19,18,87),(19,18,91),(19,18,94),(19,19,58),(19,19,59),(19,19,62),(19,19,63),(19,19,64),(19,19,65),(19,20,58),(19,20,66),(19,20,67),(19,21,58),(19,21,63),(19,21,68),(19,21,69),(19,22,58),(19,22,59),(19,22,63),(19,22,70),(19,22,71),(19,22,93),(19,23,62),(19,23,72),(19,23,73),(19,23,91),(19,23,92),(91,5,9),(91,5,10),(91,5,293),(91,5,296),(93,63,298),(94,5,9),(94,5,10),(94,5,293),(94,5,296),(94,62,294),(94,62,295),(96,61,285),(96,61,286),(96,61,287),(96,62,294),(96,62,295),(96,64,288),(96,64,290),(96,64,291),(96,64,292),(101,62,294),(101,62,295),(102,5,9),(102,5,10),(102,5,293),(102,5,296),(102,62,294),(102,62,295),(102,64,288),(102,64,290),(102,64,291),(102,64,292),(103,5,9),(103,5,10),(103,5,293),(103,5,296),(103,61,285),(103,61,286),(103,61,287),(104,5,9),(104,5,10),(104,5,293),(104,5,296),(104,5,297),(104,61,285),(104,61,286),(104,61,287),(104,62,294),(104,62,295),(104,63,298),(104,64,288),(104,64,290),(104,64,291),(104,64,292),(105,5,9),(105,5,10),(105,5,293),(105,5,296),(105,5,297),(105,61,285),(105,61,286),(105,61,287),(105,63,298),(107,61,285),(107,61,286),(107,61,287),(109,63,298),(110,63,298),(111,63,298),(112,62,294),(112,62,295),(113,62,294),(113,62,295),(114,62,294),(114,62,295),(115,61,285),(115,61,286),(115,61,287),(116,65,299),(117,65,299),(118,65,299),(120,65,299);
/*!40000 ALTER TABLE `dims_workspace_user_role` ENABLE KEYS */;
UNLOCK TABLES;

ALTER TABLE `dims_user` ADD `gnupg_fingerprint` CHAR( 255 ) NOT NULL;

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'Gestion de Gnu Private Guard', '_DIMS_GNUPG_MANAGEMENT'), (NULL, 'system', '2', 'Gnu Private Guard management', '_DIMS_GNUPG_MANAGEMENT');

ALTER TABLE `dims_mod_wce_article_block_model` ADD INDEX ( `id_module` , `present` ) ;
ALTER TABLE `dims_mod_wce_article` ADD INDEX ( `id_workspace` , `urlrewrite` ) ;
ALTER TABLE `dims_workspace` ADD `frontaccess_limited` TINYINT NOT NULL DEFAULT '0';

update `dims_mb_field` set id_module_type=18 WHERE `tablename` LIKE 'dims_mod_business_action';
update `dims_mb_field` set id_object=10 WHERE `tablename` LIKE 'dims_mod_business_action' AND `name` LIKE 'id';
update `dims_mb_field` set name='id_workspace',label='id_workspace' WHERE `tablename` LIKE 'dims_mod_business_action' AND `name` LIKE 'id_group';

ALTER TABLE `dims_mod_wce_article` ADD `type` TINYINT UNSIGNED NOT NULL DEFAULT '0';

-- Ajout d'un identifiant skin sur l'user
ALTER TABLE `dims_user` ADD `id_skin` INT( 4 ) NOT NULL ;


--
-- Structure de la table `dims_skin`
--
drop table if exists dims_skin;
CREATE TABLE IF NOT EXISTS `dims_skin` (
  `id_skin` int(14) NOT NULL AUTO_INCREMENT,
  `nom_skin` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id_skin`)
) ENGINE=MyISAM;

--
-- Contenu de la table `dims_skin`
--

INSERT INTO `dims_skin` (`id_skin`, `nom_skin`, `id_user`) VALUES
(2, 'darkness', 0),
(1, 'lightness', 0),
(24, 'smoothness', 0),
(3, 'start', 0),
(4, 'redmond', 0),
(5, 'sunny', 0),
(6, 'overcast', 0),
(7, 'le-frog', 0),
(8, 'flick', 0),
(9, 'pepper-grinder', 0),
(10, 'eggplant', 0),
(11, 'dark-hive', 0),
(12, 'cupertino', 0),
(13, 'south-street', 0),
(14, 'blitzer', 0),
(15, 'humanity', 0),
(16, 'hot-sneaks', 0),
(17, 'excite-bike', 0),
(18, 'vader', 0),
(19, 'dot-luv', 0),
(20, 'mint-choco', 0),
(21, 'black-tie', 0),
(22, 'trontastic', 0),
(23, 'swanky-purse', 0);


--
-- Structure de la table `dims_globalobject`
--

DROP TABLE IF EXISTS `dims_globalobject`;
CREATE TABLE IF NOT EXISTS `dims_globalobject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_module_type` int(11) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_object` int(11) NOT NULL DEFAULT '0',
  `id_record` int(11) NOT NULL DEFAULT '0',
  `title` char(200) NOT NULL DEFAULT '',
  `link_title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_module` (`id_module`),
  KEY `id_module_2` (`id_module`,`id_object`,`id_record`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Structure de la table `dims_globalobject_link`
--

DROP TABLE IF EXISTS `dims_globalobject_link`;
CREATE TABLE IF NOT EXISTS `dims_globalobject_link` (
  `id_module_type_from` int(11) NOT NULL,
  `id_globalobject_from` int(11) NOT NULL,
  `id_module_type_to` int(11) NOT NULL,
  `id_globalobject_to` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `dims_mod_business_contact` ADD `id_globalobject` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_business_tiers` ADD `id_globalobject` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_newsletter` ADD `id_globalobject` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_webmail_email` ADD `id_globalobject` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_doc_file` ADD `id_globalobject` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_rssfeed` ADD `id_globalobject` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_rsscache` ADD `id_globalobject` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_wce_object` ADD `id_globalobject` INT( 11 ) NOT NULL DEFAULT '0';

ALTER TABLE `dims_mod_wce_object` ADD `type` INT( 2 ) NOT NULL DEFAULT '0';

ALTER TABLE `dims_mod_rssfeed` ADD `max` INT( 4 ) NOT NULL DEFAULT '0' AFTER `description`;
ALTER TABLE `dims_mod_rssfeed` ADD `isauto` BOOLEAN NOT NULL DEFAULT '0';

ALTER TABLE `dims_mod_business_contact` CHANGE `lux` `lux` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_business_contact` CHANGE `birthday` `birthday` BIGINT( 14 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_business_contact` CHANGE `children` `children` TINYINT( 10 ) NULL DEFAULT '0';
ALTER TABLE `dims_mod_business_contact` CHANGE `partenaire` `partenaire` TINYINT( 1 ) NULL DEFAULT '0';
ALTER TABLE `dims_mod_business_contact` CHANGE `id_user_create` `id_user_create` INT( 11 ) NOT NULL DEFAULT '0';

ALTER TABLE `dims_mod_business_tiers` CHANGE `partenaire` `partenaire` TINYINT( 1 ) NULL DEFAULT '0';

INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES (NULL, 'Plan de site', '2', '?op=''sitemap''', NULL, NULL, NULL, NULL);

# 18/02/2011
ALTER TABLE `dims_mod_wce_object` ADD `template` CHAR( 100 ) NOT NULL DEFAULT '' AFTER `label` ;

ALTER TABLE `dims_mod_wce_object` ADD `content` TEXT NOT NULL AFTER `label` ;

INSERT INTO `dims_mb_field` (`id`, `tablename`, `name`, `label`, `type`, `visible`, `id_module_type`, `id_object`, `indexed`, `protected`, `level`) VALUES (NULL, 'dims_mod_wce_article', 'description', 'description', 'longtext', '1', '2', '0', '1', '0', '1');

# 05/04/2011
ALTER TABLE `dims_mod_wce_article` ADD `is_sitemap` TINYINT NOT NULL DEFAULT '1';
#ALTER TABLE `dims_mod_wce_heading` ADD `is_sitemap` TINYINT NOT NULL DEFAULT '1';

#14/04/2011
DROP TABLE IF EXISTS `dims_spool_mail`;
CREATE TABLE `dims_spool_mail` (
  `id` int(11) NOT NULL auto_increment,
  `from` varchar(100) default NULL,
  `to` varchar(1000) default NULL,
  `cc` varchar(1000) default NULL,
  `bcc` varchar(1000) default NULL,
  `subject` varchar(1000) default NULL,
  `content` longtext,
  `date_creation` datetime default NULL,
  `spool` tinyint(1) default '1',
  PRIMARY KEY  (`id`),
  KEY `spool` (`spool`,`id`)
) ENGINE=MyISAM;

# 28/04/2011
ALTER TABLE `dims_mailinglist` ADD `query` TEXT  NULL ;
ALTER TABLE `dims_mailinglist_attach` ADD `deleted` TINYINT NOT NULL DEFAULT '0';

ALTER TABLE `dims_mailinglist` ADD `query_delete` TEXT NOT NULL DEFAULT '';

ALTER TABLE `dims_mod_business_tiers_contact` CHANGE `date_deb` `date_deb` BIGINT( 14 ) NULL;
ALTER TABLE `dims_mod_business_tiers_contact` CHANGE `date_fin` `date_fin` BIGINT( 14 ) NULL;


INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES (NULL, 'Project', '1', '?op=''projects''', NULL, NULL, NULL, NULL);
ALTER TABLE `dims_project` ADD `type` VARCHAR( 150 ) NOT NULL AFTER `state` ;

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'Voir aussi', '_LABEL_SEE_ALSO'), (NULL, 'system', '2', 'See also', '_LABEL_SEE_ALSO');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'Mot', '_LABEL_WORD'), (NULL, 'system', '2', 'Word', '_LABEL_WORD');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'Tag', '_DIMS_LABEL_TAG'), (NULL, 'system', '2', 'Tag', '_DIMS_LABEL_TAG');

ALTER TABLE `dims_mod_business_action` ADD `dateextended` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `dims_mod_business_action` ADD `max_allowed` INT( 11 ) NOT NULL DEFAULT '0';

INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '2', 'Unique date or period', '_LABEL_UNIQUE_DATE'), (NULL, 'system', '1', 'P&eacute;riode ou date unique', '_LABEL_UNIQUE_DATE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '2', 'Several dates', '_LABEL_SEVERAL_DATE'), (NULL, 'system', '1', 'Plusieurs dates', '_LABEL_SEVERAL_DATE');
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '2', 'Only month and year', '_LABEL_ONLY_MONTH_DATE'), (NULL, 'system', '1', 'Uniquement mois et ann&eacute;e', '_LABEL_ONLY_MONTH_DATE');

ALTER TABLE `dims_mod_business_event_inscription` ADD `id_action_parent` INT( 11 ) NOT NULL DEFAULT '0' AFTER `id_action` , ADD INDEX ( `id_action_parent` ) ;

# 17/06/2011 - synchro mutli dims
CREATE TABLE `dims_mb_table_synchro` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`tablename` CHAR( 150 ) NOT NULL DEFAULT '',
`destination` CHAR( 150 ) NOT NULL DEFAULT ''
) ENGINE = InnoDB;

INSERT INTO `dims_constant` (`id` ,`moduletype` ,`id_lang` ,`value` ,`phpvalue`) VALUES (NULL , 'system', '1', 'Synchroniser', '_LABEL_SYNCHRONIZE'), (NULL , 'system', '2', 'Synchronize', '_LABEL_SYNCHRONIZE');
INSERT INTO `dims_constant` (`id` ,`moduletype` ,`id_lang` ,`value` ,`phpvalue`) VALUES (NULL , 'system', '1', 'Synchronisation', '_LABEL_SYNCHRO'), (NULL , 'system', '2', 'Synchronization', '_LABEL_SYNCHRO');

#23/06/2011
INSERT INTO `dims_constant` (`id`, `moduletype`, `id_lang`, `value`, `phpvalue`) VALUES (NULL, 'system', '1', 'Script de bas de page', '_WCE_SCRIPT_BOTTOM'), (NULL, 'system', '2', 'Bottom script', '_WCE_SCRIPT_BOTTOM');

#26/06/2011
ALTER TABLE `dims_domain` ADD `mobile` TINYINT NOT NULL DEFAULT '0';

ALTER TABLE `dims_mod_forms_reply` ADD `id_site` INT( 11 ) NOT NULL DEFAULT '1' AFTER `id` ;
ALTER TABLE `dims_mod_forms_reply_field` ADD `id_site` INT( 2 ) NULL DEFAULT '1' AFTER `id` ;
ALTER TABLE `dims_mod_forms_reply` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` , `id_site` ) ;
ALTER TABLE `dims_mod_forms_reply_field` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` , `id_site` ) ;

ALTER TABLE `dims_project` ADD `id_tiers` INT( 11 ) NOT NULL DEFAULT '0';

ALTER TABLE `dims_project` CHANGE `type` `type` VARCHAR( 150 ) NULL ;

ALTER TABLE `dims_mod_wce_article` ADD `source` VARCHAR( 255 ) CHARACTER SET utf8 NULL DEFAULT '';
ALTER TABLE `dims_mod_wce_article`
CHANGE `source` `source` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `id_lang`;
ALTER TABLE `dims_mod_wce_article`
ADD `private` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `visible`;

ALTER TABLE `dims_mod_wce_article_block` ADD `page_break` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `status` ;


# Create domain '*'
truncate dims_domain;
INSERT INTO `dims_domain` (`id`, `domain`, `access`, `ssl`, `webmail_http_code`) VALUES (NULL, '*', '2', '0', NULL);

# Create workspaces
truncate dims_workspace;
INSERT INTO `dims_workspace` (`id`, `id_workspace`, `label`, `code`, `system`, `protected`, `parents`, `iprules`, `macrules`, `admin_template`, `web_template`, `depth`, `mustdefinerule`, `typegroup`, `admin`, `public`, `web`, `admin_domainlist`, `title`, `meta_description`, `meta_keywords`, `meta_author`, `meta_copyright`, `meta_robots`, `web_domainlist`, `ssl`, `project`, `planning`, `contact`, `tickets`, `newsletter`, `background`, `sitemap`, `email`, `signature`, `email_noreply`, `id_tiers`, `contact_intel`, `contact_docs`, `contact_tags`, `contact_comments`, `contact_activeent`, `contact_outlook`, `code_of_conduct`, `share_info`, `id_lang`, `newsletter_sender_email`, `newsletter_id_domain`, `newsletter_header_registration`, `newsletter_footer_registration`, `newsletter_accepted_subject`, `newsletter_accepted_content`, `newsletter_unsubscribe_subject`, `newsletter_unsubscribe_content`, `newsletter_message_registration`, `events_sender_email`, `events_signature`, `events_mail2_subject`, `events_mail2_content`, `events_mail3_subject`, `events_mail3_content`, `events_mail4_subject`, `events_mail4_content`, `events_mail5_subject`, `events_mail5_content`, `events_mail6_subject`, `events_mail6_content`, `events_mail7_subject`, `events_mail7_content`, `events_mail8_subject`, `events_mail8_content`, `events_mail9_subject`, `events_mail9_content`, `events_mail10_subject`, `events_mail10_content`, `events_mail11_subject`, `events_mail11_content`, `events_mail1_subject`, `events_mail1_content`, `id_workspace_feedback`) VALUES (NULL, '0', 'system', NULL, '1', '0', '0', NULL, NULL, NULL, NULL, '1', '0', NULL, '1', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '0', '0', '1', '0', NULL, NULL, NULL, NULL, NULL, '0', '1', '1', '1', '1', '1', '1', '0', '1', '1', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO `dims_workspace` (`id`, `id_workspace`, `label`, `code`, `system`, `protected`, `parents`, `iprules`, `macrules`, `admin_template`, `web_template`, `depth`, `mustdefinerule`, `typegroup`, `admin`, `public`, `web`, `admin_domainlist`, `title`, `meta_description`, `meta_keywords`, `meta_author`, `meta_copyright`, `meta_robots`, `web_domainlist`, `ssl`, `project`, `planning`, `contact`, `tickets`, `newsletter`, `background`, `sitemap`, `email`, `signature`, `email_noreply`, `id_tiers`, `contact_intel`, `contact_docs`, `contact_tags`, `contact_comments`, `contact_activeent`, `contact_outlook`, `code_of_conduct`, `share_info`, `id_lang`, `newsletter_sender_email`, `newsletter_id_domain`, `newsletter_header_registration`, `newsletter_footer_registration`, `newsletter_accepted_subject`, `newsletter_accepted_content`, `newsletter_unsubscribe_subject`, `newsletter_unsubscribe_content`, `newsletter_message_registration`, `events_sender_email`, `events_signature`, `events_mail2_subject`, `events_mail2_content`, `events_mail3_subject`, `events_mail3_content`, `events_mail4_subject`, `events_mail4_content`, `events_mail5_subject`, `events_mail5_content`, `events_mail6_subject`, `events_mail6_content`, `events_mail7_subject`, `events_mail7_content`, `events_mail8_subject`, `events_mail8_content`, `events_mail9_subject`, `events_mail9_content`, `events_mail10_subject`, `events_mail10_content`, `events_mail11_subject`, `events_mail11_content`, `events_mail1_subject`, `events_mail1_content`, `id_workspace_feedback`) VALUES ('2', '1', 'Dims', NULL, '0', '1', '0;1', NULL, NULL, NULL, NULL, '2', '0', NULL, '1', '0', '0', NULL, 'Dims Portal', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '0', '1', '1', '0', NULL, NULL, NULL, NULL, NULL, '0', '1', '1', '1', '1', '1', '1', '0', '1', '1', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0');


# Activate workspace and domain
truncate dims_workspace_domain;
INSERT INTO `dims_workspace_domain` (`id_workspace`, `id_domain`, `access`) VALUES ('2', '1', '2');

# Create group user
truncate dims_group;
INSERT INTO `dims_group` (`id`, `id_group`, `label`, `system`, `protected`, `parents`, `depth`, `id_workspace`, `shared`, `code`, `reference`) VALUES (NULL, '0', 'system', '1', '1', '0', '1', '0', '0', NULL, NULL), (NULL, '1', 'Users', '0', '1', '0;1', '2', '1', '0', NULL, NULL);

# Create admin account
truncate dims_user;
INSERT INTO `dims_user` (`id`, `id_type`, `id_ldap`, `lastname`, `firstname`, `login`, `password`, `date_creation`, `date_expire`, `email`, `phone`, `fax`, `comments`, `address`, `mobile`, `service`, `function`, `postalcode`, `city`, `country`, `ticketsbyemail`, `color`, `timezone`, `defaultworkspace`, `presentation`, `jabberId`, `id_contact`, `lastconnexion`, `beforelastconnexion`, `background`, `lang`, `id_group`, `code_of_conduct`) VALUES (NULL, '-1', NULL, 'Admin', 'user', 'admin', MD5('admin'), NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, '0', '0', '0', NULL, '1', '0', '0');

# Associate admin user and group
truncate dims_group_user;
INSERT INTO `dims_group_user` (`id_user`, `id_group`, `adminlevel`) VALUES ('1', '2', '99');

# Associate admin user and workspace
truncate dims_workspace_user;
INSERT INTO `dims_workspace_user` (`id_user`, `id_workspace`, `id_profile`, `adminlevel`, `activesearch`, `activeticket`, `activeprofil`, `activeannot`, `activecontact`, `activeproject`, `activeplanning`, `activenewsletter`, `activeevent`, `activeeventstep`, `activeeventemail`) VALUES ('1', '2', '0', '99', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0');

# Create modules
TRUNCATE TABLE `dims_module`;
INSERT INTO `dims_module` (`id`, `label`, `id_module_type`, `id_workspace`, `active`, `public`, `shared`, `herited`, `adminrestricted`, `viewmode`, `transverseview`, `autoconnect`, `sitemap`) VALUES (NULL, 'system', '1', NULL, '1', '0', '0', '0', '0', '1', '0', '0', NULL), (NULL, 'CMS', '2', '2', '1', '0', '0', '0', '0', '1', '0', '0', NULL);
INSERT INTO `dims_module` (`id`, `label`, `id_module_type`, `id_workspace`, `active`, `public`, `shared`, `herited`, `adminrestricted`, `viewmode`, `transverseview`, `autoconnect`, `sitemap`) VALUES (NULL, 'Docs', '3', '2', '1', '0', '0', '0', '0', '1', '0', '0', NULL), (NULL, 'Forms', '4', '2', '1', '0', '0', '0', '0', '1', '0', '0', NULL);

# module workspace
TRUNCATE dims_module_workspace;
INSERT INTO `dims_module_workspace` (`id_module`, `id_workspace`, `position`, `blockposition`, `visible`, `autoconnect`) VALUES ('2', '2', '1', 'left', '1', '0'), ('3', '2', '2', 'left', '1', '0');
INSERT INTO `dims_module_workspace` (`id_module`, `id_workspace`, `position`, `blockposition`, `visible`, `autoconnect`) VALUES ('4', '2', '3', 'left', '1', '0');

# Activate default WCE heading
TRUNCATE dims_mod_wce_heading;
INSERT INTO `dims_mod_wce_heading` (`id`, `label`, `description`, `template`, `id_heading`, `parents`, `depth`, `position`, `color`, `posx`, `posy`, `visible`, `linkedpage`, `url`, `url_window`, `free1`, `free2`, `id_module`, `id_user`, `id_workspace`, `timestp_modify`, `urlrewrite`, `fckeditor`, `picto`, `colour`, `linkedheading`, `id_lang`) VALUES (NULL, 'Root', NULL, NULL, '0', '0', '1', '1', NULL, '0', '0', '0', '0', NULL, '0', NULL, NULL, '2', '1', '2', '0', NULL, NULL, NULL, NULL, '0', '1');

# migration du template
update `dims_workspace` set admin_template='dims_v5';
# Update skin default template
update `dims_user` set id_skin=24;

ALTER TABLE `dims_user`
CHANGE `id_skin` `id_skin` int(4) NOT NULL DEFAULT '24';

# 18/10/2011
#ALTER TABLE `dims_mod_wce_heading` ADD `private` tinyint(1) unsigned NOT NULL DEFAULT '0' ;

#27/10/2011
ALTER TABLE `dims_mod_business_tiers` ADD `type_tiers` int(11) NOT NULL DEFAULT '0' AFTER `date_maj` ;

DROP TABLE IF EXISTS `dims_mod_wce_site`;
CREATE TABLE `dims_mod_wce_site` (
  `id_module` int(11) NOT NULL default '0',
  `robots` text NOT NULL,
  PRIMARY KEY  (`id_module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dims_mod_wce_slideshow`;
CREATE TABLE `dims_mod_wce_slideshow` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`nom` varchar(255) DEFAULT NULL,
	`description` text,
	`template` varchar(255) DEFAULT NULL,
	`id_user` int(10) unsigned NOT NULL,
	`id_module` int(10) unsigned NOT NULL,
	`id_workspace` int(10) unsigned NOT NULL,
	`timestp_create` bigint(20) unsigned NOT NULL,
	`timestp_modify` bigint(20) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dims_mod_wce_slideshow_element`;
CREATE TABLE `dims_mod_wce_slideshow_element` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`id_slideshow` int(10) unsigned NOT NULL,
	`connected_only` tinyint(3) unsigned NOT NULL DEFAULT '0',
	`titre` varchar(255) DEFAULT NULL,
	`descr_courte` text,
	`descr_longue` text,
	`descr_position` char(10) DEFAULT NULL,
	`lien` varchar(255) DEFAULT NULL,
	`image` varchar(255) DEFAULT NULL,
	`miniature` varchar(255) DEFAULT NULL,
	`position` int(10) unsigned NOT NULL DEFAULT '0',
	`id_user` int(10) unsigned NOT NULL,
	`id_module` int(10) unsigned NOT NULL,
	`id_workspace` int(10) unsigned NOT NULL,
	`timestp_create` bigint(20) unsigned NOT NULL,
	`timestp_modify` bigint(20) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `dims_mod_wce_article` ADD `id_globalobject` INT( 11 ) NOT NULL DEFAULT '0';

ALTER TABLE `dims_lang` ADD `ref` VARCHAR( 5 ) NULL ,
ADD `isactive` BOOLEAN NULL DEFAULT '1';
UPDATE `dims_lang` SET `ref` = 'fr' WHERE `dims_lang`.`id` =1;
UPDATE `dims_lang` SET `ref` = 'en' WHERE `dims_lang`.`id` =2;
UPDATE `dims_lang` SET `ref` = 'de' WHERE `dims_lang`.`id` =4;
UPDATE `dims_lang` SET `ref` = 'ja' WHERE `dims_lang`.`id` =5;


DROP TABLE IF EXISTS `dims_category`;
CREATE TABLE `dims_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) DEFAULT NULL,
  `root_reference` int(11) DEFAULT '0',
  `label` varchar(250) NOT NULL DEFAULT '',
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 : public 1 : prive 2 : universel',
  `position` int(11) NOT NULL DEFAULT '0',
  `timestp_create` bigint(14) NOT NULL DEFAULT '0',
  `timestp_modify` bigint(14) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `id_globalobject` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_dims_category_dims_category` (`id_parent`),
  KEY `fk_dims_category_dims_globalobject1` (`id_globalobject`),
  KEY `fk_dims_category_dims_workspace1` (`id_workspace`),
  KEY `fk_dims_category_dims_module1` (`id_module`),
  KEY `root_reference` (`root_reference`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dims_category_module`;
CREATE TABLE `dims_category_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_dims_category_module_dims_category1` (`id_category`),
  KEY `fk_dims_category_module_dims_module1` (`id_module`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `dims_category_module` (`id`, `id_category`, `id_module`) VALUES
(1,	1,	8),
(2,	1,	3);

DROP TABLE IF EXISTS `dims_category_module_type`;
CREATE TABLE `dims_category_module_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL,
  `id_module_type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_dims_category_workspace_dims_category1` (`id_category`),
  KEY `fk_dims_category_module_type_dims_module_type1` (`id_module_type`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dims_category_object`;
CREATE TABLE `dims_category_object` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL,
  `id_object` int(11) NOT NULL,
  `object_id_module_type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_dims_category_object_dims_category1` (`id_category`),
  KEY `fk_dims_category_object_dims_mb_object1` (`id_object`,`object_id_module_type`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `dims_case` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`label` VARCHAR( 200 ) NOT NULL ,
`datestart` BIGINT( 14 ) NOT NULL DEFAULT '0',
`dateend` BIGINT( 14 ) NOT NULL DEFAULT '0',
`id_module` INT( 11 ) NOT NULL DEFAULT '0',
`id_workspace` INT( 11 ) NOT NULL DEFAULT '0',
`id_user` INT NOT NULL
) ENGINE = InnoDB;

ALTER TABLE `dims_todo` ADD `id_user` INT( 11 ) NOT NULL DEFAULT '0' AFTER `content`  ;

DROP TABLE IF EXISTS `dims_todo_dest`;
CREATE TABLE `dims_todo_dest` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `id_todo` int(11) NOT NULL,
   `id_user` int(11) NOT NULL,
   `type` TINYINT( 2 ) NOT NULL,
   `flag` tinyint(1) DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#Correctif script
ALTER TABLE `dims_user` ADD `status` TINYINT( 1 ) NOT NULL DEFAULT '1';


-- phpMyAdmin SQL Dump
-- version 3.4.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 01, 2011 at 11:02 AM
-- Server version: 5.1.57
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `dimsportal_courrier`
--

-- --------------------------------------------------------

--
-- Table structure for table `dims_intervention`
--

DROP TABLE IF EXISTS `dims_intervention`;
CREATE TABLE IF NOT EXISTS `dims_intervention` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_type_intervention` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `tmstp_realized` bigint(14) NOT NULL DEFAULT '0',
  `intervention_ground` text,
  `status` int(11) NOT NULL DEFAULT '0',
  `inout` tinyint(1) NOT NULL DEFAULT '1',
  `id_contact` int(11) NOT NULL DEFAULT '0',
  `id_case` int(11) NOT NULL DEFAULT '0',
  `id_todo` int(11) NOT NULL DEFAULT '0',
  `id_globalobject` int(11) NOT NULL DEFAULT '0',
  `id_globalobject_ref` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `dims_intervention_counter`
--

DROP TABLE IF EXISTS `dims_intervention_counter`;
CREATE TABLE IF NOT EXISTS `dims_intervention_counter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_type_intervention` int(11) NOT NULL DEFAULT '0',
  `id_globalobject` int(11) NOT NULL DEFAULT '0',
  `cmpt` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  ;

-- --------------------------------------------------------

--
-- Table structure for table `dims_intervention_linked`
--

DROP TABLE IF EXISTS `dims_intervention_linked`;
CREATE TABLE IF NOT EXISTS `dims_intervention_linked` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_globalobject` int(11) NOT NULL DEFAULT '0',
  `id_intervention` int(11) NOT NULL DEFAULT '0',
  `id_type_link` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dims_intervention_link_type`
--

DROP TABLE IF EXISTS `dims_intervention_link_type`;
CREATE TABLE IF NOT EXISTS `dims_intervention_link_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `php_value` varchar(120) DEFAULT NULL,
  `libelle` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dims_intervention_type`
--

DROP TABLE IF EXISTS `dims_intervention_type`;
CREATE TABLE IF NOT EXISTS `dims_intervention_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `php_value` varchar(120) DEFAULT NULL,
  `libelle` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dims_case`;
CREATE TABLE IF NOT EXISTS `dims_case` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(200) NOT NULL,
  `datestart` bigint(14) NOT NULL DEFAULT '0',
  `dateend` bigint(14) NOT NULL DEFAULT '0',
  `id_module` int(11) NOT NULL DEFAULT '0',
  `id_workspace` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '0',
  `id_globalobject` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE  `dims_mod_business_action` ADD  `id_globalobject` INT NOT NULL DEFAULT  '0';

UPDATE `dims_mb_table` SET `sql` = 'from dims_mod_business_action where id_parent=0' WHERE `dims_mb_table`.`name` = 'dims_mod_business_action' AND `dims_mb_table`.`id_module_type` =18;

ALTER TABLE `dims_mod_business_contact` ADD `nickname` varchar(100) COLLATE 'utf8_general_ci' NULL AFTER `firstname`, COMMENT='' ;

