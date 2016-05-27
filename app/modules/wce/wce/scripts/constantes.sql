-- Thomas -- 20/08/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Paramètres du site', `dims_constant`.`phpvalue` = '_SITE_SETTINGS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Website Settings', `dims_constant`.`phpvalue` = '_SITE_SETTINGS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Gestion du site', `dims_constant`.`phpvalue` = '_SITE_MANAGEMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Site management', `dims_constant`.`phpvalue` = '_SITE_MANAGEMENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Gestion des articles', `dims_constant`.`phpvalue` = '_MANAGEMENT_ARTICLES';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Management of articles', `dims_constant`.`phpvalue` = '_MANAGEMENT_ARTICLES';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Objets dynamiques', `dims_constant`.`phpvalue` = '_DYNAMIC_OBJECTS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Dynamic objects', `dims_constant`.`phpvalue` = '_DYNAMIC_OBJECTS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Trackers', `dims_constant`.`phpvalue` = '_TRACKERS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Trackers', `dims_constant`.`phpvalue` = '_TRACKERS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Accèder au wiki', `dims_constant`.`phpvalue` = '_GO_TO_WIKI';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Go to wiki', `dims_constant`.`phpvalue` = '_GO_TO_WIKI';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Votre site web', `dims_constant`.`phpvalue` = '_YOUR_WEBSITE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Your website', `dims_constant`.`phpvalue` = '_YOUR_WEBSITE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Derniers articles mis à jour', `dims_constant`.`phpvalue` = '_LAST_UPDATED_ARTICLES';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Last updated articles', `dims_constant`.`phpvalue` = '_LAST_UPDATED_ARTICLES';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Accéder à la liste des articles', `dims_constant`.`phpvalue` = '_GO_LIST_ARTICLES';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Go to the list of articles', `dims_constant`.`phpvalue` = '_GO_LIST_ARTICLES';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Voir toutes les statistiques', `dims_constant`.`phpvalue` = '_SEE_ALL_STATS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'See all statistics', `dims_constant`.`phpvalue` = '_SEE_ALL_STATS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Configuration avancée', `dims_constant`.`phpvalue` = '_ADVANCED_CONFIGURATION';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Advanced configuration', `dims_constant`.`phpvalue` = '_ADVANCED_CONFIGURATION';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Paramètres généraux', `dims_constant`.`phpvalue` = '_GENERAL_SETTINGS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'General settings', `dims_constant`.`phpvalue` = '_GENERAL_SETTINGS';
UPDATE dims_constant SET value='Informations générales', phpvalue='_INFOS_LABEL' WHERE id_lang=1 AND phpvalue='_INFOS_LABEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Noms de domaines', `dims_constant`.`phpvalue` = '_DOMAIN_NAMES';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Domain names', `dims_constant`.`phpvalue` = '_DOMAIN_NAMES';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Importer le site', `dims_constant`.`phpvalue` = '_IMPORT_SITE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Import site', `dims_constant`.`phpvalue` = '_IMPORT_SITE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Exporter le site', `dims_constant`.`phpvalue` = '_EXPORT_SITE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Export site', `dims_constant`.`phpvalue` = '_EXPORT_SITE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Actualiser le sitemap', `dims_constant`.`phpvalue` = '_UPDATE_SITEMAP';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Update sitemap', `dims_constant`.`phpvalue` = '_UPDATE_SITEMAP';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Affichage', `dims_constant`.`phpvalue` = '_DISPLAY';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Display', `dims_constant`.`phpvalue` = '_DISPLAY';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Arborescence du site', `dims_constant`.`phpvalue` = '_SITE_TREE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Site tree', `dims_constant`.`phpvalue` = '_SITE_TREE';

-- Thomas -- 22/08/2012 --
UPDATE dims_constant SET value='Prévisualiser', phpvalue='_PREVIEW' WHERE id_lang=1 AND phpvalue='_PREVIEW';
UPDATE dims_constant SET value='Propriétés de l\'article', phpvalue='_DIMS_PROPERTIES_ARTICLE' WHERE id_lang=1 AND phpvalue='_DIMS_PROPERTIES_ARTICLE';
UPDATE dims_constant SET value='Référencement', phpvalue='_WCE_PAGE_REFER' WHERE id_lang=1 AND phpvalue='_WCE_PAGE_REFER';

-- Thomas -- 23/08/2012 --
UPDATE dims_constant SET value='Propriétés de la rubrique', phpvalue='_DIMS_PROPERTIES_HEADING' WHERE id_lang=1 AND phpvalue='_DIMS_PROPERTIES_HEADING';
UPDATE dims_constant SET value='Propriétés', phpvalue='_DIMS_PROPERTIES' WHERE id_lang=1 AND phpvalue='_DIMS_PROPERTIES';
UPDATE dims_constant SET value='Nouvelle fenêtre', phpvalue='_LABEL_NEW_WINDOW' WHERE id_lang=1 AND phpvalue='_LABEL_NEW_WINDOW';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Mise en ligne le', `dims_constant`.`phpvalue` = '_POSTED_ON';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Posted on', `dims_constant`.`phpvalue` = '_POSTED_ON';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Mise hors ligne le', `dims_constant`.`phpvalue` = '_TAKEN_OFFLINE_THE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Taken offline the', `dims_constant`.`phpvalue` = '_TAKEN_OFFLINE_THE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Lien vers l\'article', `dims_constant`.`phpvalue` = '_LINK_TO_ARTICLE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Link to article', `dims_constant`.`phpvalue` = '_LINK_TO_ARTICLE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Picto', `dims_constant`.`phpvalue` = '_PICTO';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Picto', `dims_constant`.`phpvalue` = '_PICTO';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Visible dans le menu', `dims_constant`.`phpvalue` = '_VISIBLE_IN_THE_MENU';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Visible in the menu', `dims_constant`.`phpvalue` = '_VISIBLE_IN_THE_MENU';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Présent dans le sitemap', `dims_constant`.`phpvalue` = '_PRESENT_IN_THE_SITEMAP';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Present in the sitemap', `dims_constant`.`phpvalue` = '_PRESENT_IN_THE_SITEMAP';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Édito', `dims_constant`.`phpvalue` = '_EDITORIAL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Editorial', `dims_constant`.`phpvalue` = '_EDITORIAL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Visible si connecté', `dims_constant`.`phpvalue` = '_VISIBLE_IF_CONNECTED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Visible if connected', `dims_constant`.`phpvalue` = '_VISIBLE_IF_CONNECTED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Template', `dims_constant`.`phpvalue` = '_TEMPLATE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Template', `dims_constant`.`phpvalue` = '_TEMPLATE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Redirection vers un article', `dims_constant`.`phpvalue` = '_REDIRECT_TO_ARTICLE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Redirect to an article', `dims_constant`.`phpvalue` = '_REDIRECT_TO_ARTICLE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Redirection vers une rubrique', `dims_constant`.`phpvalue` = '_REDIRECT_TO_TOPIC';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Redirection to a topic', `dims_constant`.`phpvalue` = '_REDIRECT_TO_TOPIC';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Fréquence de changement', `dims_constant`.`phpvalue` = '_FREQUENCY_CHANGE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Change frequency', `dims_constant`.`phpvalue` = '_FREQUENCY_CHANGE';

-- Thomas -- 03/09/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Ajouter racine', `dims_constant`.`phpvalue` = '_WCE_ADD_ROOT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Add root', `dims_constant`.`phpvalue` = '_WCE_ADD_ROOT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Ajouter rubrique', `dims_constant`.`phpvalue` = '_ADD_REVIEW';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Add review', `dims_constant`.`phpvalue` = '_ADD_REVIEW';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Modifier les informations de référencement', `dims_constant`.`phpvalue` = '_CHANGE_REFERENCING_INFORMATION';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Change referencing information', `dims_constant`.`phpvalue` = '_CHANGE_REFERENCING_INFORMATION';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Aucun article n\'est sélectionné !', `dims_constant`.`phpvalue` = '_ALERT_NO_ARTICLE_SELECTED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'No article is selected !', `dims_constant`.`phpvalue` = '_ALERT_NO_ARTICLE_SELECTED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Êtes-vous sûr de vouloir supprimer cet article ?', `dims_constant`.`phpvalue` = '_CONFIRM_DELETE_ARTICLE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Are you sure you want to delete this article ?', `dims_constant`.`phpvalue` = '_CONFIRM_DELETE_ARTICLE';

-- Thomas -- 04/09/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Sélectionner une date', `dims_constant`.`phpvalue` = '_OEUVRE_SELECT_DATE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'system', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Select a date', `dims_constant`.`phpvalue` = '_OEUVRE_SELECT_DATE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Style', `dims_constant`.`phpvalue` = '_STYLE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Style', `dims_constant`.`phpvalue` = '_STYLE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Famille', `dims_constant`.`phpvalue` = '_FAMILY';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Family', `dims_constant`.`phpvalue` = '_FAMILY';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Taille', `dims_constant`.`phpvalue` = '_WEIGHT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Weight', `dims_constant`.`phpvalue` = '_WEIGHT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Gras', `dims_constant`.`phpvalue` = '_BOLD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Bold', `dims_constant`.`phpvalue` = '_BOLD';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Bloc', `dims_constant`.`phpvalue` = '_BLOCK';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Block', `dims_constant`.`phpvalue` = '_BLOCK';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Afficher le titre dans le contenu', `dims_constant`.`phpvalue` = '_SHOW_TITLE_IN_CONTENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Show title in content', `dims_constant`.`phpvalue` = '_SHOW_TITLE_IN_CONTENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Saut de page', `dims_constant`.`phpvalue` = '_PAGE_BREAK';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Page break', `dims_constant`.`phpvalue` = '_PAGE_BREAK';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Largeur', `dims_constant`.`phpvalue` = '_WIDTH';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Width', `dims_constant`.`phpvalue` = '_WIDTH';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Hauteur', `dims_constant`.`phpvalue` = '_HEIGHT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Height', `dims_constant`.`phpvalue` = '_HEIGHT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Marge', `dims_constant`.`phpvalue` = '_MARGIN';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Margin', `dims_constant`.`phpvalue` = '_MARGIN';

-- Thomas -- 05/09/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Pages consultées', `dims_constant`.`phpvalue` = '_CONSULTED_PAGES';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Consulted pages', `dims_constant`.`phpvalue` = '_CONSULTED_PAGES';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Nb visiteurs différents', `dims_constant`.`phpvalue` = '_NB_UNIQUE_VISITORS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Nb of unique visitors', `dims_constant`.`phpvalue` = '_NB_UNIQUE_VISITORS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Naviguer dans le site', `dims_constant`.`phpvalue` = '_BROWSE_THE_SITE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Browse the site', `dims_constant`.`phpvalue` = '_BROWSE_THE_SITE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Objets actifs', `dims_constant`.`phpvalue` = '_ACTIVE_OBJECTS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Active objects', `dims_constant`.`phpvalue` = '_ACTIVE_OBJECTS';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Ajouter objet', `dims_constant`.`phpvalue` = '_ADD_OBJECT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Add object', `dims_constant`.`phpvalue` = '_ADD_OBJECT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Nom de l\'objet', `dims_constant`.`phpvalue` = '_OBJECT_NAME';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Object name', `dims_constant`.`phpvalue` = '_OBJECT_NAME';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Aucun objet présent', `dims_constant`.`phpvalue` = '_NO_OBJECT_PRESENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'No object present', `dims_constant`.`phpvalue` = '_NO_OBJECT_PRESENT';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Brève', `dims_constant`.`phpvalue` = '_NEWS_IN_BRIEF';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'News in brief', `dims_constant`.`phpvalue` = '_NEWS_IN_BRIEF';

-- Thomas -- 06/09/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Source de l\'information', `dims_constant`.`phpvalue` = '_INFORMATION_SOURCE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Information source', `dims_constant`.`phpvalue` = '_INFORMATION_SOURCE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Choix d\'une rubrique ou article', `dims_constant`.`phpvalue` = '_CHOOSING_TOPIC_OR_ARTICLE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Choosing a topic or article', `dims_constant`.`phpvalue` = '_CHOOSING_TOPIC_OR_ARTICLE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Rubrique sélectionnée', `dims_constant`.`phpvalue` = '_SELECTED_TOPIC';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Selected topic', `dims_constant`.`phpvalue` = '_SELECTED_TOPIC';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Article sélectionné', `dims_constant`.`phpvalue` = '_SELECTED_ARTICLE';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Selected article', `dims_constant`.`phpvalue` = '_SELECTED_ARTICLE';

-- Thomas -- 10/09/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Article sauvegardé !', `dims_constant`.`phpvalue` = '_ARTICLE_SAVED';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Article saved !', `dims_constant`.`phpvalue` = '_ARTICLE_SAVED';

-- Thomas -- 11/09/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Rubrique', `dims_constant`.`phpvalue` = '_RUBRIC';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Rubric', `dims_constant`.`phpvalue` = '_RUBRIC';

-- Cyril -- 24/19/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Niveau d\'alerte', `dims_constant`.`phpvalue` = 'ALERT_LEVEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Alert level', `dims_constant`.`phpvalue` = 'ALERT_LEVEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Niveau faible', `dims_constant`.`phpvalue` = 'LOW_LEVEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Low level', `dims_constant`.`phpvalue` = 'LOW_LEVEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Niveau moyen', `dims_constant`.`phpvalue` = 'MIDDLE_LEVEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Middle level', `dims_constant`.`phpvalue` = 'MIDDLE_LEVEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Niveau élevé', `dims_constant`.`phpvalue` = 'HIGH_LEVEL';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'High level', `dims_constant`.`phpvalue` = 'HIGH_LEVEL';

-- Thomas -- 03/10/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Monter', `dims_constant`.`phpvalue` = '_DIMS_UP';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Up', `dims_constant`.`phpvalue` = '_DIMS_UP';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Descendre', `dims_constant`.`phpvalue` = '_DIMS_DOWN';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Down', `dims_constant`.`phpvalue` = '_DIMS_DOWN';

-- Thomas -- 08/11/2012 --
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Template par défaut du WIKI', `dims_constant`.`phpvalue` = '_DEFAULT_TEMPLATE_WIKI';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Default template WIKI', `dims_constant`.`phpvalue` = '_DEFAULT_TEMPLATE_WIKI';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '1', `dims_constant`.`value` = 'Nom de template', `dims_constant`.`phpvalue` = '_TEMPLATE_NAME';
INSERT INTO `dims_constant` SET `dims_constant`.`id` = null, `dims_constant`.`moduletype` = 'wce', `dims_constant`.`id_lang` = '2', `dims_constant`.`value` = 'Template name', `dims_constant`.`phpvalue` = '_TEMPLATE_NAME';
