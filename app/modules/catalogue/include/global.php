<?php

include_once DIMS_APP_PATH."modules/system/case/class_case.php";

require_once DIMS_APP_PATH.'modules/catalogue/include/functions.php';
require_once DIMS_APP_PATH.'include/functions/image.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_market.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_market_restriction.php';

if ( defined('_CATA_VARIANTE') ) {
	if (file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/global.php')) {
		require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/global.php';
	}
	if (file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/functions.php')) {
		require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/functions.php';
	}
}

// photos - taille max en pixels
define ('_CATA_PHOTO_MAX_WIDTH',		500);
define ('_CATA_PHOTO_MAX_HEIGHT',		500);

// upload photo - taille max en octets
define ("_CATA_PHOTO_MAX_UPLOAD_SIZE",	8 * 1024 * 1024); // 8Mo
// upload photo - codes d'erreur
define ("_CATA_PHOTO_TRANSFERT_ERROR",	1);
define ("_CATA_PHOTO_EMPTY_DOC",		2);
define ("_CATA_PHOTO_HUGE_DOC",			3);
define ("_CATA_PHOTO_ALREADY_EXISTS",	4);
define ("_CATA_PHOTO_COPY_ERROR",		5);

define ('_CATA_CMD_DIR', DIMS_APP_PATH.'data/synchro/output/');

define('_CLIENTS_GROUP',	'6');
define('_INTERNET_GROUP',	7);

define ('_CATALOGUE_ICON_GROUP',		'group');
define ('_CATALOGUE_ICON_USERS',		'users');
define ('_CATALOGUE_ICON_LIVRAISON',	'livraison');
define ('_CATALOGUE_ICON_HISTORY',		'history');

define ('_CATALOGUE_TAB_USERLIST',		'userlist');
define ('_CATALOGUE_TAB_USERADD',		'useradd');
define ('_CATALOGUE_TAB_USERATTACH',	'userattach');
define ('_CATALOGUE_TAB_RULELIST',		'rulelist');
define ('_CATALOGUE_TAB_RULEADD',		'ruleadd');

define('_CATALOGUE_TAB_RUBRIQUES',			0);
define('_CATALOGUE_TAB_ARTICLES',			1);
define('_CATALOGUE_TAB_ARTICLES_NORATT',	2);

// familles
define('_CATA_TOOLBAR_BTN_FAMILY_CHILD',	0);
define('_CATA_TOOLBAR_BTN_FAMILY_CLONE',	1);
define('_CATA_TOOLBAR_BTN_FAMILY_DELETE',	2);

//catalogue
define('_ADMIN_TAB_CATA_RESUME',			0);
define('_ADMIN_TAB_CATA_ARTEDIT',			1);
define('_ADMIN_TAB_CATA_ARTPUB',			2);
define('_ADMIN_TAB_CATA_MODELE',			3);
define('_ADMIN_TAB_CATA_TYPES',				4);
define('_ADMIN_TAB_CATA_FAMILIES',			5);
define('_ADMIN_TAB_CATA_FICHE',				6);
define('_ADMIN_TAB_CATA_PARAM_RECH',		7);
define('_ADMIN_TAB_CATA_PARAM',				8);
define('_ADMIN_TAB_CATA_COORD',				9);
define('_ADMIN_TAB_CATA_TOOLS',				10);
define('_ADMIN_TAB_CATA_CDE',				11);
define('_ADMIN_TAB_CATA_CLI',				12);
define('_ADMIN_TAB_CATA_GROUP',				13);
define('_ADMIN_TAB_CATA_PORT',				14);
define('_ADMIN_TAB_CATA_PAYS',				15);
define('_ADMIN_TAB_CATA_SELCHPS',			16);
define('_ADMIN_TAB_CATA_CHPDYN',			17);
define('_ADMIN_TAB_CATA_ECOLABEL',			18);
define('_ADMIN_TAB_CATA_MARQUE',			19);
define('_ADMIN_TAB_CATA_CHPDYN_GROUP',		20);
define('_ADMIN_TAB_CATA_FOURNISSEUR',		21);
define('_ADMIN_TAB_CATA_CONDITIONNEMENT',	22);
define('_ADMIN_TAB_CATA_EXPORTS',			23);
define('_ADMIN_TAB_CATA_SYNCHRO',			24);
define('_ADMIN_TAB_CATA_ARTRECH',			25);
define('_ADMIN_TAB_CATA_CARTOUCHES',		26);
define('_ADMIN_TAB_CATA_GESTCHPS',			27);

define('DANGEROUSNESS_WEIGHT_LIMIT', 50);

// Valeurs par défaut pour les frais de port et le franco
// Utilisés s'il n'y a pas de tarifs définis pour le département
// define("_DEFAULT_FRAIS_PORT",	25);
// define("_DEFAULT_FRANCO",		150);

global $specialchars; $specialchars = '';
for ($i=0;$i<=32;$i++) if ($i!=10 && $i!=13) $specialchars .= chr($i);

// Activation des plugins
define('_PLUGIN_AUTOCONNECT',true);
define('_PLUGIN_MAJPRIXNETS',false);
define('_PLUGIN_EPAYMENT',false);
define('_EPAYMENT_MODULE','');
define('_MODULE_NEWSLETTER',true);
define('_PLUGIN_FP_SCHALLER',false);

// Affichage des prix
define('_SHOW_PRICES',true);

// Factures disponibles
define('_ACTIVE_FACTURES',true);

// Delai d'affichage de la boite de connexion (ms)
define('_CONBOX_TIMEOUT',3000);

// Nombre d'articles qui s'affichent dans le panier
define('_NB_ART_PANIER',6);

// Affichage detaille des articles
define('_FICHE_DETAILLEE',true);

// id_grp pour 'Articles de remplacement' dans dims_mod_vpc_article_ratt_grp
define('_ID_GRP_REMPLACEMENT', 9);

define('_ID_ADHERENT', '1,5');
define('_ADHERENT','Bureau Store 82');

// valeurs par defaut de pagination
global $a_pagination_per_page;
$a_pagination_per_page = array(10, 20, 30, 50);

// define('_PDF_ADRESSE1','Buro Store 82');
// define('_PDF_ADRESSE2','');
// define('_PDF_ADRESSE3','');
// define('_MAIL_FROM','Site Buro Store 82');
// define('_MAIL_ADDRESS','ne_pas_repondre@burostore82.com');
define('_MAIL_SUBJECT','Commande Internet');
define('_ACCOUNT_DISABLED',"Vous ne pouvez pas vous connecter actuellement sur le site.<br>Veuillez contacter votre assistance commerciale.");

define('_LABEL_SAISIERAPIDE','Saisie Rapide');
define('_LABEL_MONPANIER','Mon Panier');
define('_LABEL_MESPANIERSTYPES','Mes Paniers Types');
define('_LABEL_MESLISTESSCOLAIRE','Mes Listes scolaire');

define('_LABEL_COMMANDESENATTENTECHIFFRAGE','Paniers en attente de chiffrage de port');
define('_LABEL_COMMANDESENCOURS','Paniers en attente de validation');

define('_LABEL_HISTORIQUE','Commandes en cours');
define('_LABEL_HORSCATALOGUE','Commandes hors catalogue');
define('_LABEL_FACTURES','Factures');
define('_LABEL_ADMINISTRATION','Administration');
define('_LABEL_STATISTIQUES','Statistiques');
define('_LABEL_IMPORTSELECTION','Import de la S&eacute;lection');
define('_LABEL_IMPRIMERSELECTION_PDF','Imprimer la S&eacute;lection (PDF)');
define('_LABEL_EXPORTERCATALOGUE_PDF','Imprimer votre tarif (PDF)');
define('_LABEL_RETOURADMINISTRATION','Retour &agrave; l\'administration');
define('_LABEL_PANIERTYPE','Panier Type');
define('_LABEL_LISTSCOLAIRE','Liste scolaire');
define('_LABEL_COMMANDER','Commander');
define('_LABEL_PDF_BUTTON','Imprimer');
define('_LABEL_SAVENEWPANIERTYPE','Enregistrer un nouveau Panier Type');
define('_LABEL_SAVENEWSCHOOLLIST','Enregistrer une nouvelle liste scolaire');
define('_LABEL_SAVEADDPANIERTYPE','Ajouter &agrave; un Panier Type existant');
define('_LABEL_SAVEADDSCHOOLLIST','Ajouter &agrave; une liste scolaire existante');
define('_LABEL_EXPORTERCATALOGUE_XLS', 'Exporter au format XLS');
define('_LABEL_EXPORTERCATALOGUE_CSV', 'Exporter au format CSV');
define('_DESC_EXPORTERCATALOGUE_XLS', 'Exporter au format XLS');
define('_DESC_EXPORTERCATALOGUE_CSV', 'Exporter au format CSV');

define('_DESC_PANIERSTYPES',"Vous pouvez enregistrer des &laquo; Paniers Type &raquo; afin de les utiliser lors d'une prochaine commande.<br/>Les &laquo; Paniers Type &raquo; vous permettent d'acc&eacute;l&eacute;rer vos demandes d'achat avec des listes d'articles pr&eacute;-enregistr&eacute;es.");
define('_DESC_LISTSCOLAIRE',"Vous pouvez enregistrer des &laquo; Listes scolaires &raquo; que vous pourrez proposer à d'autres clients.<br/>Les &laquo; listes scolaire &raquo; permettent d'acc&eacute;l&eacute;rer les demandes d'achat avec des listes d'articles pr&eacute;-enregistr&eacute;es.<br />Un code g&eacute;n&eacute;r&eacute; automatiquement, que vous pourrez distribuer, vous sera fournit.");
define('_DESC_PANIERSTYPES_LISTSCOLAIRE',"Votre liste scolaire a bien été créée.<br />Le code associé est :<br />");
define('_DESC_PANIERSTYPES_LISTSCOLAIRE_NOTFIND',"Ce code ne correspond à aucune liste scolaire.<br />Veuillez réessayer : <br />");
define('_DESC_SAISIERAPIDE',"Vous permet de saisir rapidement une commande en ins&eacute;rant les r&eacute;f&eacute;rences que vous connaissez.");
define('_DESC_MONPANIER',"Vous renvoie directement vers le panier en cours.");
define('_DESC_MESPANIERSTYPES',"Vous renvoie vers la liste de vos paniers types que vous pouvez utiliser pour cr&eacute;er une nouvelle commande.");
define('_DESC_COMMANDESENCOURS',"Vous renvoie vers la liste de vos commandes en attente de validation.");
define('_DESC_HISTORIQUE',"Vous renvoie vers l'historique de vos commandes.");
define('_DESC_FACTURES',"Vous permet d'&eacute;diter vos factures.");
define('_DESC_HORSCATALOGUE',"Vous permet de saisir des r&eacute;f&eacute;rences qui n'apparaissent pas sur le catalogue.");
define('_DESC_ADMINISTRATION',"Vous permet d'administrer les groupes, utilisateurs et adresses de livraison par d&eacute;faut, ainsi que les budgets que vous pouvez affecter aux groupes que vous avez cr&eacute;&eacute;.");
define('_DESC_STATISTIQUES',"Le module Statistiques facilite l'export de donn&eacute;es en proposant une interface simple et conviviale de choix des donn&eacute;es &agrave; collecter.");
define('_DESC_IMPORTSELECTION',"Vous permet d'importer une liste d'articles &agrave; ajouter la s&eacute;lection.");
define('_DESC_IMPRIMERSELECTION_PDF',"Vous permet d'imprimer la s&eacute;lection. Cela correspond au catalogue restreint si vous fonctionnez dans ce mode.");
define('_DESC_EXPORTERCATALOGUE_PDF',"Vous permet d'exporter le catalogue au format PDF. Si vous ne poss&eacute;dez pas Acrobat Reader, vous pouvez le t&eacute;l&eacute;charger <a href=\"http://www.adobe.fr/products/acrobat/readstep2.html\"><u>ici</u></a>");
define('_DESC_RETOURADMINISTRATION',"Vous permet de retourner au menu d'administration original.");

define('_NO_COMMANDESENCOURS',"Il n'y a actuellement pas de commande en cours");
define('_NO_MESPANIERSTYPES',"Vous n'avez pas de panier type");
define('_NO_HISTORIQUE',"Il n'y a actuellement pas de commande archiv&eacute;e");
define('_NO_FACTURE',"Il n'y a actuellement pas de facture");

define ('_CONFIRM_PT_DEL_ART','Etes-vous s&ucirc;r(e) de vouloir effacer cet article du panier type ?');
define('_CMD_COLOR_ENTETE','#eeeeee');
define('_CMD_COLOR_BORDER','#805F94');
define('_DEV_DURABLE_BGCOLOR','#DEF7D7');

global $dims;
define('_MAIL_ACCOUNT_SUBJECT',"Cr&eacute;ation de votre compte Internet");
define('_MAIL_ACCOUNT_CONTENT',"
<HTML>
<HEAD>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8;\">
<STYLE></STYLE>
</HEAD>
<BODY>
<TABLE cellSpacing=0 cellPadding=2 width=\"100%\" border=0>
<TBODY>
<TR>
<TD style=\"FONT-SIZE: 12pt; CURSOR: auto; FONT-FAMILY: Arial\" width=\"100%\">
<DIV>&nbsp;</DIV>
<DIV>
<DIV>
<DIV>Votre compte a bien été activé sur le site <A href=\"".$dims->getProtocol().$dims->getHttpHost()."/\">".$dims->getHttpHost()."</A>. Ci-dessous vos identifiants de connexion</DIV>
<DIV>afin de vous permettre de vous connecter sur notre site : <A href=\"".$dims->getProtocol().$dims->getHttpHost()."/\">".$dims->getHttpHost()."</A></DIV>
<DIV>&nbsp;</DIV>
<DIV>- identifiant : <B><LOGIN></B></DIV>
<DIV>&nbsp;</DIV>
<DIV>- mot de passe : <B><PASSWD></B></DIV>
<DIV>&nbsp;</DIV>
<DIV>Salutations distingu&eacute;es</DIV>
<DIV>&nbsp;</DIV>
<DIV>Bonne r&eacute;ception</DIV>
</DIV>
</DIV>
<DIV>&nbsp;</DIV>
</TD>
</TR>
</TBODY>
</TABLE>
</BODY>
</HTML>");

define('_MAIL_NEW_PASSWORD',"
<HTML>
<HEAD>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8;\">
<STYLE></STYLE>
</HEAD>
<BODY>
<TABLE cellSpacing=0 cellPadding=2 width=\"100%\" border=0>
<TBODY>
<TR>
<TD style=\"FONT-SIZE: 12pt; CURSOR: auto; FONT-FAMILY: Arial\" width=\"100%\">
<DIV>&nbsp;</DIV>
<DIV>
<DIV>
<DIV>Vos informations ont &eacute;t&eacute; mises &agrave; jour sur notre site Internet : <A href=\"".$dims->getProtocol().$dims->getHttpHost()."/\">".$dims->getHttpHost()."</A></DIV>
<DIV>&nbsp;</DIV>
<DIV>- Identifiant : <B><LOGIN></B></DIV>
<DIV>- Mot de passe : <B><PASSWD></B></DIV>
<DIV>- Nom : <B><NOM></B></DIV>
<DIV>- Pr&eacute;nom : <B><PRENOM></B></DIV>
<DIV>- Email : <B><EMAIL></B></DIV>
<DIV>- T&eacute;l&eacute;phone : <B><TELEPHONE></B></DIV>
<DIV>- Fax : <B><FAX></B></DIV>
<DIV>- Adresse : <B><ADRESSE></B></DIV>
<DIV>&nbsp;</DIV>
<DIV>Salutations distingu&eacute;es</DIV>
<DIV>&nbsp;</DIV>
<DIV>Bonne r&eacute;ception</DIV>
</DIV>
</DIV>
<DIV>&nbsp;</DIV>
</TD>
</TR>
</TBODY>
</TABLE>
</BODY>
</HTML>
");

//Apel global : Utilisation de la variable initialisé par le WCE
global $tpl_site;

// Mail de demande de mot de passe
define('_MAIL_DMD_PWD_CONTENT',"
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8;\">
<link rel=\"StyleSheet\" href=\"".$tpl_site['TEMPLATE_ROOT_PATH']."/styles.css\" type=\"text/css\">
</head>
<body>
	<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\">
	<tr>
		<td>
			Madame, Monsieur,<br><br>
			Vous recevez ce message parce qu'une demande de mot de passe a été effectuée sur notre site internet.<br/><br/>
			Si vous êtes à l'origine de cette demande et que vous souhaitez toujours changer votre mot de passe,<br/>
			cliquez sur le lien ci-dessous ou recopiez-le dans votre navigateur <br/><br/>
			<u><a href=\"<DMD_PWD_LINK>\"><DMD_PWD_LINK></a></u><br/><br/>
			Dans le cas contraire, vous pouvez ignorer ce message.
		</td>
	</tr>
	</table>
</body>
</html>");

// Mail d'activation de compte
define('_MAIL_ACTIVATION_CONTENT',"
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8;\">
<link rel=\"StyleSheet\" href=\"".$tpl_site['TEMPLATE_ROOT_PATH']."/styles.css\" type=\"text/css\">
</head>
<body>
	<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\">
	<tr>
		<td>
			Madame, Monsieur,<br><br>
			Vous recevez ce message parcequ'une demande de création de compte a été effectuée sur notre site internet.<br/><br/>
			Si vous êtes à l'origine de cette demande et que vous souhaitez activer votre compte,<br/>
			cliquez sur le lien ci-dessous ou recopiez-le dans votre navigateur <br/><br/>
			<u><a href=\"<ACTIVATION_LINK>\"><ACTIVATION_LINK></a></u><br/><br/>
			Dans le cas contraire, vous pouvez ignorer ce message.
		</td>
	</tr>
	</table>
</body>
</html>");

global $msgs_confirm;
$msgs_confirm = array(
	0 => "Votre commande a bien &eacute;t&eacute; enregistr&eacute;e.<br/>Elle vous sera confirm&eacute;e par retour d'email<br/>et sera trait&eacute;e dans les plus brefs d&eacute;lais.",
	1 => "Votre commande a bien &eacute;t&eacute; enregistr&eacute;e<br/>et est en attente de validation par <SERVICE_RESP>.",
	2 => "Votre commande a bien &eacute;t&eacute; enregistr&eacute;e<br/>et est en attente de validation par <PURCHASE_RESP>.",
	3 => "Une erreur est survenue pendant<br>l'enregistrement de votre commande.",
	4 => "Vous ne disposez pas d'un cr&eacute;dit suffisant pour commander.",
	5 => "Vous n'avez pas de responsable des achats.<br/>Contactez votre administrateur pour r&eacute;soudre ce probl&egrave;me.",
	6 => "Une erreur est survenue lors de votre commande.",
	7 => "Votre demande de retour a bien &eacute;t&eacute; enregistr&eacute;e et sera trait&eacute;e dans les plus brefs d&eacute;lais.",
	8 => "Votre commande a bien &eacute;t&eacute; enregistr&eacute;e<br/>et est en attente de validation par ".$_SERVER['HTTP_HOST'].'.'
);

// Modes de paiement
global $a_modes_paiement;
$a_modes_paiement = array(
	'compte' => 'En Compte',
	'cheque' => 'Chèque',
	'cb' => 'Carte Bleue'
);


//TRANSLATE
define ('_CATA_CATALOGUE',		'Catalogue');
define ('_CATA_CAMPAGNES',		'Campagnes');
define ('_CATA_ART_PARATTACH',	'Articles non rattachés');
define ('_CATA_CLIENTS',		'Clients');
define ('_CATA_COMMANDES',		'Commandes');
define ('_CATA_PARAM',			'Paramètres');
define ('_CATA_ENREG',			'ENREGISTREMENT EFFECTUE');

define ('_PAGE_TITLE',			'Gestion du Module');
define ('_CHOOSE',				'--- Choisissez ---');
define ('_NONE',				' ---');
define ('_MONEY',				'&euro;');
define ('_WEIGHT',				'gramme');
define ('_RETURN',				'Retour');
define ('_NO_RESULT',			'<table style="text-align:center;border:0;width:100%"><tr><td style="width:100%;text-align:center;font-style:italic;font-size:12pt;">Aucun résultat</td></tr></table>');
define ('_RECH_OK',				'Ok');
define ('_RECH_CANCEL',			'Annuler');
define ('_SELECT_FAMILLE',		'--- LISTE DES FAMILLES ---');
define ('_CATA_MESS_ERREUR_COOKIES',	'Ce site nécessite l\'utilisation des Cookies pour pouvoir commander');
define ('_CATA_SEND',			'Envoyer');
define ('_CATA_CHEQUE',			'Chèque');
define ('_CATA_CB',				'Carte bancaire');
define ('_CATA_CPT',			'Compte');


/*
define ('_CATA_CONNECT',		'Se connecter');
define ('_CATA_NEW_CPT',		'Créer un nouveau compte');
*/
define ('_CATA_EMPTY',			'Vide');
define ('_CATA_NEW',			'Nouveau');
define ('_CATA_SAVE_SHORT',		'Enr.');
define ('_CATA_DELETE',			'Supprimer');
define ('_CATA_DELETE_SHORT',	'Suppr.');
define ('_CATA_UNKNOW',			'- Non renseigné -');



define ('_ADMIN_TAB_CATA_FAMILIES_LABEL',		'Famille');
define ('_ADMIN_TAB_CATA_ARTEDIT_LABEL',		'Liste des articles');
define ('_ADMIN_TAB_CATA_ARTRECH_LABEL',		'Recherche');
define ('_ADMIN_TAB_CATA_ARTPUB_LABEL',			'Publier les articles');
define ('_ADMIN_TAB_CATA_MODELE_LABEL',			'Liste des modèles');
define ('_ADMIN_TAB_CATA_FICHE_LABEL',			'Fiche article');
define ('_ADMIN_TAB_CATA_TYPES_LABEL',			'Gestion des types');
define ('_ADMIN_TAB_CATA_PARAM_RECH_LABEL',		'Champs de recherche');
define ('_ADMIN_TAB_CATA_PARAM_DIVERS',			'Paramètres / Préférences');
define ('_ADMIN_TAB_CATA_PARAM_LABEL',			'Paramètres');
define ('_ADMIN_TAB_CATA_COORD_LABEL',			'Coordonnées');
define ('_ADMIN_TAB_CATA_TOOLS_LABEL',			'Outils');
define ('_ADMIN_TAB_CATA_GROUP_LABEL',			'Gestion des Groupes');
define ('_ADMIN_TAB_CATA_PORT_LABEL',			'Frais de port');
define ('_ADMIN_TAB_CATA_PAYS_LABEL',			'Pays');
define ('_ADMIN_TAB_CATA_SELCHPS_LABEL',		'Séléction des champs');
define ('_ADMIN_TAB_CATA_GESTCHPS_LABEL',		'Gestion des champs');

define ('_CATA_TITLE_ADMIN_CATA',				'Administration de Catalogue');
define ('_CATA_TITLE_ADMIN_CDE',				'Administration des Commandes');
define ('_CATA_TITLE_ADMIN_CLI',				'Administration des clients');
define ('_CATA_TITLE_ADMIN_PARAM',				'Paramétrage du catalogue');
define ('_CATA_TITLE_ADMIN_PORT',				'Gestion des frais de port');

//ARTICLE
define ('_CATA_SUBTITLE_ART_MODEL_CHOOSE',		'Choix des modèles utilisés');
define ('_CATA_SUBTITLE_ART_EDIT',				'Edition des articles');
define ('_CATA_SUBTITLE_ART_LIST',				'Liste des articles');
define ('_CATA_SUBTITLE_ART_RATTACH',			'Rattacher des articles');

define('_CATA_ARTICLE_LABEL_CHOOSE_MODEL',		'Modèle de fiche');
define('_CATA_ARTICLE_LABEL_CHOOSE_MODEL_LIST',	'Modèle de liste');
define('_CATA_ARTICLE_LABEL_MODEL_APPLICATED',	'Modèle appliqué');
define('_CATA_ARTICLE_LABEL_FAMILY',			'Famille');
define('_CATA_ARTICLE_LABEL_TITLE',				'Titre');
define('_CATA_ARTICLE_LABEL_DESIGNATION',		'Désignation');
define('_CATA_ARTICLE_LABEL_REF',				'Référence');
define('_CATA_ARTICLE_LABEL_FAM',				'Famille');
define('_CATA_ARTICLE_LABEL_SSFAM',				'Ss-famille');
define('_CATA_ARTICLE_LABEL_RECH',				'Recherche');
define('_CATA_ARTICLE_LABEL_DATE_CREE',			'Date de création');
define('_CATA_ARTICLE_LABEL_DATE_MODIF',		'Date de modification');
define('_CATA_ARTICLE_LABEL_DESCRIPT',			'Description');
define('_CATA_ARTICLE_LABEL_LONGDESCRIPT',		'Description 2');
define('_CATA_ARTICLE_LABEL_PUHT',				'Prix Unitaire HT');
define('_CATA_ARTICLE_LABEL_TVA',				'Taux de TVA');
define('_CATA_ARTICLE_LABEL_PUTTC',				'Prix Unitaire TTC');
define('_CATA_ARTICLE_LABEL_QTE',				'Quantité');
define('_CATA_ARTICLE_LABEL_QTE_SHORT',			'Qte');
define('_CATA_ARTICLE_LABEL_QTE_MINI',			'Seuil Stock Mini');
define('_CATA_ARTICLE_LABEL_WEIGHT',			'Poids');
define('_CATA_ARTICLE_LABEL_PORT',				'Frais de port');
define('_CATA_ARTICLE_LABEL_ADD_PICTURE',		'Ajouter une photo');
define('_CATA_ARTICLE_LABEL_PICTURE',			'Photo');
define('_CATA_ARTICLE_LABEL_PICTURE_LEGEND',	'Légende');
define('_CATA_ARTICLE_LABEL_ADD_ART',			'Ajouter un article');
define('_CATA_ARTICLE_LABEL_ATTACH_ART',		'Rattacher des articles');
define('_CATA_ARTICLE_LABEL_ATTACH_SELECTED',	'Rattacher les articles sélectionnés');
define('_CATA_ARTICLE_LABEL_CREATE',			'Créé');
define('_CATA_ARTICLE_LABEL_CREATE_LE',			'Créé le');
define('_CATA_ARTICLE_LABEL_CREATE_BY',			'Créé par');
define('_CATA_ARTICLE_LABEL_MODIFY',			'Modifié');
define('_CATA_ARTICLE_LABEL_MODIFY_LE',			'Modifié le');
define('_CATA_ARTICLE_LABEL_MODIFY_BY',			'Modifié par');
define('_CATA_ARTICLE_LABEL_NO_ART',			'Il n\'y a aucun article.');
define('_CATA_ARTICLE_LABEL_ART_NO_CORRESP',	'Cette référence ne correspond à aucun article.');
define('_CATA_ARTICLE_LABEL_ART_HIMSELF',		'La référence choisie est celle de la fiche elle-même.');
define('_CATA_ARTICLE_LABEL_ART_PRESENT',		'Cette référence est déjà présente');
define('_CATA_ARTICLE_LABEL_ART_PRESENT_IN_GROUP',	'Cette référence est déjà présente dans ce groupe');
define('_CATA_ARTICLE_LABEL_CREATE_SOUS_REF',	'Ajouter des référence à l\'article');
define('_CATA_ARTICLE_LABEL_CREATE_LINK',		'Créer des rattachements');
define('_CATA_ARTICLE_LABEL_ADD_REF',			'Ajouter une référence');
define('_CATA_ARTICLE_LABEL_MOD_REF',			'Modifier/Traduire une référence');
define('_CATA_ARTICLE_LABEL_LIST_REF',			'Liste des références');
define('_CATA_ARTICLE_LABEL_NAME_SOUS_REF',		'Libellé des sous-références');
define('_CATA_ARTICLE_LABEL_SOUS_REF_REF',		'Référence');
define('_CATA_ARTICLE_LABEL_SOUS_REF_DESIGN',	'Désignation');
define('_CATA_ARTICLE_LABEL_SOUS_REF_DEGRESS',	'Dégr.');
define('_CATA_ARTICLE_LABEL_TYPE_SOUS_REF',		'Type de selecteur');
define('_CATA_ARTICLE_LABEL_TYPE_1_SOUS_REF',	'Liste');
define('_CATA_ARTICLE_LABEL_TYPE_2_SOUS_REF',	'Selecteur Vertical');
define('_CATA_ARTICLE_LABEL_TYPE_3_SOUS_REF',	'Selecteur Horizontal');
define('_CATA_ARTICLE_LABEL_MOD_SOUS_REF',		'Modifier/Traduire un libellé de référence');
define('_CATA_ARTICLE_LABEL_SOUS_REF_PRESENT_IN_GROUP',	'Cette référence est déjà présente');
define('_CATA_ARTICLE_LABEL_ART_NO_RATTACH',	'Il n\'y a aucun article rattaché.');
define('_CATA_ARTICLE_LABEL_FREE',				'Libre');
define('_CATA_ARTICLE_LABEL_FICHE',				'Fiche');
define('_CATA_ARTICLE_LABEL_LISTE',				'Liste');
define('_CATA_ARTICLE_LABEL_TAB_RATTACHE',		'Articles conseillées');
define('_CATA_ARTICLE_LABEL_TAB_SOUS_REF',		'Sous-Référence(s) article');
define('_CATA_ARTICLE_LABEL_GROUPE',			'Groupe');
define('_CATA_ARTICLE_LABEL_POSITION_SHORT',	'Pos.');
define('_CATA_ARTICLE_LABEL_POSITION',			'Position');
define('_CATA_ARTICLE_LABEL_EDIT',				'Ouvrir la fiche de cet article');
define('_CATA_ARTICLE_LABEL_RATT',				'Editer les rattachements à d\'autres articles');
define('_CATA_ARTICLE_LABEL_SREF',				'Editer les sous-references');
define('_CATA_ARTICLE_LABEL_DEGR',				'Editer les tarifs dégressifs');
define('_CATA_ARTICLE_LABEL_SUPPR',				'Supprimer l\'article');
define('_CATA_ARTICLE_LABEL_SUPPR_CONF',		'Etes-vous sûr(e) de vouloir supprimer cet article ?');

//FICHE ARTICLE
define('_CATA_LABEL_AFF_STD',					'Basculer en affichage standard');
define('_CATA_LABEL_AFF_LIGHT',					'Basculer en affichage réduit');
define('_CATA_LABEL_QTE_DISPO',					'Disponible');
define('_CATA_LABEL_QTE_0',						'Epuisé');

//TARIF DEGRESSIF
define('_CATA_SUBTITLE_DEGRESS',				'Tarifs dégressifs');
define('_CATA_ARTICLE_DEGRESS_GRILLE',			'Grille des tarifs dégressifs');
define('_CATA_LABEL_DEGR_QTE',					'Qte');
define('_CATA_LABEL_DEGR_TARIF',				'Tarif');
define('_CATA_LABEL_DEGR_PHT',					'PUHT');
define('_CATA_LABEL_DEGR_PTTC',					'PUTTC');

//MODELE
define('_CATA_SUBTITLE_MODELE_LIST',			'Liste des modeles');
define('_CATA_SUBTITLE_MODELE_PRESENTATION',	'Présentation');
define('_CATA_SUBTITLE_MODELE_MODIFY',			'Modifier la fiche article');

define('_CATA_MODELE_LABEL_ADD',				'Ajouter un modèle');
define('_CATA_MODELE_LABEL_NAME',				'Nom du modèle');
define('_CATA_MODELE_LABEL_TYPE_MODELE',		'Type de modèle');
define('_CATA_MODELE_LABEL_DEFAULT',			'Défaut');
define('_CATA_MODELE_LABEL_PHOTO_SIZE_MAX',		'Format maxi. photo (pixel)');
define('_CATA_MODELE_LABEL_AFF_PAR_PAGE',		'Nombre par page');
define('_CATA_MODELE_LABEL_CREE',				'Date de création');
define('_CATA_MODELE_LABEL_MODIF',				'Date de modification');
define('_CATA_MODELE_LABEL_NB_MOD',				'Nombre de fiche par page');
define('_CATA_MODELE_LABEL_NB_MOD_INFO',		'(0 = pas de limite)');
define('_CATA_MODELE_LABEL_EDIT',				'Editer le modèle');
define('_CATA_MODELE_LABEL_SHOW',				'Afficher');
define('_CATA_MODELE_LABEL_TYPE',				'Type');
define('_CATA_MODELE_LABEL_DATA_TYPE',			'Type de données');
define('_CATA_MODELE_LABEL_LABEL',				'Libellé');
define('_CATA_MODELE_LABEL_MODIF_NO',			'Non modifiable');
define('_CATA_MODELE_LABEL_FREE_TEXT',			'--- Texte Libre ---');
define('_CATA_MODELE_LABEL_EDIT_FIC',			'Editer la fiche');
define('_CATA_MODELE_LABEL_EMPTY',				'Il n\'y a aucun modèle.');
define('_CATA_MODELE_LABEL_OPEN',				'Ouvrir la fiche de ce modèle');
define('_CATA_MODELE_LABEL_OPENED',				'Edition du modèle');
define('_CATA_MODELE_LABEL_LIST_TAG',			'Liste des champs à insérer');
define('_CATA_MODELE_LABEL_TAG_LEFT',			'Photo précédente');
define('_CATA_MODELE_LABEL_TAG_RIGHT',			'Photo suivante');
define('_CATA_MODELE_LABEL_TAG_PHOTO_THUMB',	'Vignette');
define('_CATA_MODELE_LABEL_TAG_LEFT_THUMB',		'Vignette précédente');
define('_CATA_MODELE_LABEL_TAG_RIGHT_THUMB',	'Vignette suivante');
define('_CATA_MODELE_LABEL_TAG_ZOOM',			'<img src="./common/modules/catalogue/img/zoom.gif" alt="Zoom" border="0">');
define('_CATA_MODELE_LABEL_TAG_MOREPICT',		'<img src="./common/modules/catalogue/img/more_pict.gif" alt="+ d\'images" border="0">');
define('_CATA_MODELE_LABEL_TAG_MOREINFO',		'<img src="./common/modules/catalogue/img/more_info.gif" alt="+ d\'info" border="0">');
define('_CATA_MODELE_LABEL_TAG_DEGR',			'Tarif dégressif');
//define('_CATA_MODELE_LABEL_TAG_MOREINFO',		'+ d\'info');
define('_CATA_MODELE_LABEL_PUCE_QTE',			'Puce Quantité');
define('_CATA_MODELE_LABEL_PUHT_FR',			'P.U. HT -> Fr.');
define('_CATA_MODELE_LABEL_PUTTC_FR',			'P.U. TTC -> Fr.');
define('_CATA_MODELE_LABEL_CADDIE',				'Ajouter au panier');
define('_CATA_MODELE_TYPE_LIST',				'Liste');
define('_CATA_MODELE_TYPE_FIC',					'Fiche Article');
define('_CATA_MODELE_TYPE_ENTETE',				'Entete de liste');


//ENUM
define('_CATA_SUBTITLE_ENUM_ADD',				'Ajouter un type');
define('_CATA_SUBTITLE_ENUM_MODIFY',			'Modifier un type');
define('_CATA_SUBTITLE_ENUM_MANAGE',			'Gestion des types');

define('_CATA_ENUM_LABEL_TYPE_EXIST',			'Type existant');
define('_CATA_ENUM_LABEL_TYPE_NEW',				'Nouveau type');
define('_CATA_ENUM_LABEL_LABEL',				'Libellé');
define('_CATA_ENUM_LABEL_LIMITED_VIEW',			'Limiter la vue');
define('_CATA_ENUM_LABEL_TYPE',					'Type');

//GROUP
define('_CATA_SUBTITLE_GROUP_ADD',				'Ajouter un groupe');
define('_CATA_SUBTITLE_GROUP_MODIFY',			'Modifier/Traduire un groupe');
define('_CATA_SUBTITLE_GROUP_MANAGE',			'Gestion des groupes');

define('_CATA_GROUP_LABEL_LABEL',				'Libellé');

//FAMILLE
define ('_CATA_MANAGE_FAMILY_MODIFY',			'Modification d\'une famille');
define ('_CATA_MANAGE_FAMILY_FORM_LABEL',		'Nom de la famille');
define ('_CATA_MANAGE_FAMILY_FORM_DESCRIPTION',	'Description');
define ('_CATA_MANAGE_FAMILY_FORM_PARENT',		'Famille parente');
define ('_CATA_MANAGE_FAMILY_POSITION',			'Position');
define ('_CATA_ACTION_FAMILY_MODIFY',			'Modifier');
define ('_CATA_MANAGE_FAMILY_FORM_SUBMIT',		'Valider');
define ('_CATA_MANAGE_FAMILY_VISIBLE',			'Visible');
define ('_CATA_MANAGE_FAMILY_NB_COL',			'Nb de fiche(s) en largeur');
define ('_CATA_MANAGE_FAMILY_MODELE_LIST',		'Modèle de liste');
define ('_CATA_MANAGE_FAMILY_MODELE',			'Modèle de fiche');
define ('_CATA_MANAGE_FAMILY_MODELE_ENTETE',	'Modèle d\'entête de famille');
define ('_CATA_MANAGE_FAMILY_ROOT',				'Racine');
define ('_CATA_MANAGE_FAMILY_LOGO',				'Logo');

define ('_CATA_MANAGE_FAMILYS_CHILD',			'Créer une sous-famille');
define ('_CATA_MANAGE_FAMILYS_CLONE',			'Dupliquer cette famille');
define ('_CATA_MANAGE_FAMILYS_DELETE',			'Supprimer cette famille');

define('_CATA_LABEL_DELETE_FAMILY',					'Suppression d\'une famille');
define('_CATA_LABEL_CONFIRMDELFAMILY_ALL',			'Oui, avec les articles');
define('_CATA_LABEL_CONFIRMDELFAMILY_CHILD',		'Oui, avec les sous-familles');
define('_CATA_LABEL_CONFIRMDELFAMILY_CHILD_ALL',	'Oui, avec les sous-familles ET les articles');

//ADMIN COMMANDES
define('_CATA_LABEL_CDE_MOD_ETAT',				'Etat de la commande');
define('_CATA_LABEL_CDE_MOD_ATTENTE',			'En attente');
define('_CATA_LABEL_CDE_MOD_TRAITE',			'Traitée');
define('_CATA_LABEL_CDE_MOD_IMPAYE',			'Impayée');
define('_CATA_LABEL_CDE_CB',					'<img src="./common/modules/catalogue/img/cb.gif" ALT="Carte Bancaire" border="0">');
define('_CATA_LABEL_CDE_CHQ',					'<img src="./common/modules/catalogue/img/cheque.gif" ALT="Chèque" border="0">');
define('_CATA_LABEL_CDE_VIR',					'<img src="./common/modules/catalogue/img/virement.gif" ALT="Virement" border="0">');
define('_CATA_LABEL_CDE_DATE',					'Date');
define('_CATA_LABEL_CDE_NUM_CDE',				'N°Cde');
define('_CATA_LABEL_CDE_CODE_CLI',				'Code Client');
define('_CATA_LABEL_CDE_SOCIETE',				'Société');
define('_CATA_LABEL_CDE_NOM',					'Nom');
define('_CATA_LABEL_CDE_PRENOM',				'Prénom');
define('_CATA_LABEL_CDE_MAIL',					'Mail');
define('_CATA_LABEL_CDE_CREATE',				'<img src="./common/modules/catalogue/img/ico_add.gif" ALT="Créer" border="0">');
define('_CATA_LABEL_CDE_EDIT',					'<img src="./common/modules/catalogue/img/crayon.gif" ALT="Editer" border="0">');
define('_CATA_LABEL_CDE_VIEW',					'<img src="./common/modules/catalogue/img/ico_eye.gif" ALT="Visualiser" border="0">');
define('_CATA_LABEL_CDE_PRINT',					'<img src="./common/modules/catalogue/img/ico_print.gif" ALT="Imprimer" border="0">');
define('_CATA_LABEL_CDE_DEL',					'<img src="./common/modules/catalogue/img/supprimer.gif" ALT="Supprimer" border="0">');
define('_CATA_LABEL_CDE_CONFIRM_SUPPR',			'Confirmez-vous la suppression de cette commande ?');
define('_CATA_LABEL_CDE_CONFIRM_SUPPR_LIGNE',	'Confirmez-vous la suppression de cette ligne de commande ?');
define('_CATA_LABEL_CDE_CONFIRM_TRAITE',		'Confirmez-vous le traitement de cette commande ?');
define('_CATA_LABEL_CDE_RECH_NUM_CDE',			'N° Commande');
define('_CATA_LABEL_CDE_RECH_CODE_CLI',			'Code Client');
define('_CATA_LABEL_CDE_RECH_SOCIETE',			'Société');
define('_CATA_LABEL_CDE_RECH_NOM',				'Nom');
define('_CATA_LABEL_CDE_RECH_MAIL',				'Mail');
define('_CATA_LABEL_CDE_RECH_PAIEMENT',			'Paiement');
define('_CATA_LABEL_CDE_RECH_PAIEMENT_1',		'--- Tous ---');
define('_CATA_LABEL_CDE_RECH_PAIEMENT_2',		'Chèque');
define('_CATA_LABEL_CDE_RECH_PAIEMENT_3',		'Carte Bancaire');
define('_CATA_LABEL_CDE_ETAT_0',				'<img src="./common/modules/catalogue/img/btn_bleu.png" ALT="En attente" border="0">');
define('_CATA_LABEL_CDE_ETAT_1',				'<img src="./common/modules/catalogue/img/btn_vert.png" ALT="Traitée" border="0">');
define('_CATA_LABEL_CDE_ETAT_2',				'<img src="./common/modules/catalogue/img/btn_rouge.png" ALT="Impayée" border="0">');
define('_CATA_LABEL_TRI_ASC',					'<img src="./common/modules/catalogue/img/ico_asc.png" ALT="" border="0">');
define('_CATA_LABEL_TRI_DESC',					'<img src="./common/modules/catalogue/img/ico_desc.png" ALT="" border="0">');
define('_CATA_LABEL_CDE_MOD',					'Modification d\'une commande');
define('_CATA_LABEL_CDE_MOD_NUM_CDE',			'COMMANDE N°');
define('_CATA_LABEL_CDE_MOD_ENR',				' (Modification enregistrées)');
define('_CATA_LABEL_CDE_MOD_DATE_CREE',			'Commande du ');
define('_CATA_LABEL_CDE_MOD_FACT',				'Adresse de Facturation');
define('_CATA_LABEL_CDE_MOD_LIV',				'Adresse de Livraison');
define('_CATA_LABEL_CDE_MOD_DETAIL',			'Détail de la commande');
define('_CATA_LABEL_CDE_MOD_SOCIETE',			'Société');
define('_CATA_LABEL_CDE_MOD_NOM',				'Nom');
define('_CATA_LABEL_CDE_MOD_PRENOM',			'Prénom');
define('_CATA_LABEL_CDE_MOD_ADR',				'Adresse');
define('_CATA_LABEL_CDE_MOD_CP_VILLE',			'Cp / Ville');
define('_CATA_LABEL_CDE_MOD_PAYS',				'Pays');
define('_CATA_LABEL_CDE_MOD_TEL',				'Téléphone');
define('_CATA_LABEL_CDE_MOD_PORT',				'Portable');
define('_CATA_LABEL_CDE_MOD_FAX',				'Fax');
define('_CATA_LABEL_CDE_MOD_EMAIL',				'EMail');
define('_CATA_LABEL_CDE_MOD_VIDE',				'Aucune de ligne de commande');
define('_CATA_LABEL_CDE_TOTAUX',				'TOTAUX');
define('_CATA_LABEL_CDE_SS_TOT_HT',				'Sous Total HT');
define('_CATA_LABEL_CDE_TOT_HT',				'Total HT');
define('_CATA_LABEL_CDE_TOT_TTC',				'Total TTC');
define('_CATA_LABEL_CDE_TOT_TVA',				'Total TVA');
define('_CATA_LABEL_CDE_TOT_FRAIS_PORT_TTC',	'Frais de port (TTC)');
define('_CATA_LABEL_CDE_TOT_FRAIS_PORT_HT',		'Frais de port (HT)');
define('_CATA_LABEL_CDE_RETURN',				'Retour à  la liste');
define('_CATA_LABEL_CDE_ADD',					'Ajouter des articles');
define('_CATA_LABEL_CDE_REF',					'Référence');
define('_CATA_LABEL_CDE_LABEL',					'Label');
define('_CATA_LABEL_CDE_QTE',					'Quantité');
define('_CATA_LABEL_CDE_MOD_CODE_CLI',			'Code Client');
define('_CATA_LABEL_CDE_SANS_TVA',				'(Sans TVA)');

//CLIENT
define('_CATA_LABEL_CLI_MOD',					'Modification d\'une fiche client');
define('_CATA_LABEL_CLI_CODE',					'CODE CLIENT : ');
define('_CATA_LABEL_CLI_DATE_CREE',				'Client créé le ');
define('_CATA_LABEL_CLI_LOG_PASS',				'Login & mot de passe');
define('_CATA_LABEL_CLI_LOG',					'Login');
define('_CATA_LABEL_CLI_PASS',					'Pass');

//PAYS
define('_CATA_SUBTITLE_PAYS_ADD',				'Ajouter un pays');
define('_CATA_SUBTITLE_PAYS_MODIFY',			'Modifier/Traduire un pays');
define('_CATA_SUBTITLE_PAYS_LIST',				'Liste des pays gérés dans les commandes');
define('_CATA_LABEL_PAYS_NAME',					'Nom');
define('_CATA_LABEL_PAYS_DEVISE',				'Devise');
define('_CATA_LABEL_PAYS_SYMB_DEVISE',			'Symbole Devise');
define('_CATA_LABEL_PAYS_TX_CHANGE',			'Taux de change');
define('_CATA_LABEL_PAYS_PAIEMENT_OBLIG',		'Mode de réglement obligatoire');
define('_CATA_LABEL_PAYS_PAIEMENT_NONE',		'- Aucun -');
define('_CATA_LABEL_PAYS_PAIEMENT_CHQ',			'Chèque');
define('_CATA_LABEL_PAYS_PAIEMENT_CB',			'Carte bancaire');
define('_CATA_LABEL_PAYS_PAIEMENT_VIR',			'Virement');
define('_CATA_LABEL_PAYS_TVA',					'TVA');
define('_CATA_LABEL_PAYS_AVERT_TVA',			'0=>non assujetti à la tva');


//FRAIS DE PORT
define('_CATA_ERREUR_PORT',						'Aucun secteur de port définie (Pays)');
define('_CATA_SUBTITLE_PORT',					'Frais de port');
define('_CATA_LABEL_PORT_FORMULE',				'Formule');
define('_CATA_LABEL_PORT_LIBELLE',				'Libellé');
define('_CATA_LABEL_PORT_USE_DEFAULT',			'Utiliser par defaut');
define('_CATA_MESS_PORT_ALERT',					'<b>Attention</b> : Si "par defaut" n\'est pas renseigné,<br /> il risque d\'y avoir des destinations avec des frais de port nuls');
define('_CATA_LABEL_PORT_PAYS',					'Pays');
define('_CATA_LABEL_PORT_POIDS',				'Poids');
define('_CATA_LABEL_PORT_TARIF',				'Tarif');
define('_CATA_LABEL_PORT_PHT',					'PHT');
define('_CATA_LABEL_PORT_TVA',					'TVA');
define('_CATA_LABEL_PORT_PTTC',					'PTTC');


//PARAMETRE
define('_CATA_SUBTITLE_PARAM_LIST_RECH',		'Liste des champs de la recherche');
define('_CATA_LABEL_PARAM_MESS_ALERT',			'/!\ ATTENTION /!\ l\'enregistrement lancera des traitements pouvant durer plusieurs minutes');


//OUTILS
define('_CATA_SUBTITLE_TOOLS_RESULT',			'Résultat');
define('_CATA_SUBTITLE_TOOLS',					'Outils');

define('_CATA_LABEL_TOOLS_ID_FAMILLE',			'Id_famille');
define('_CATA_LABEL_TOOLS_ID_PARENT',			'Id_parent');
define('_CATA_LABEL_TOOLS_PARENTS',				'Parents');
define('_CATA_LABEL_TOOLS_DEPTH',				'Depth');
define('_CATA_LABEL_TOOLS_POSITION_SHORT',		'Pos.');
define('_CATA_LABEL_TOOLS_OLD',					'Ancien');
define('_CATA_LABEL_TOOLS_NEW_SHORT',			'Nouv.');
define('_CATA_LABEL_TOOLS_GENERE_ID_PARENT',	'Gérération des familles a partir de id_parent');

//CONFIRM SUPPRESSION
define('_CATA_CONFIRM_SUPPR_PHOTO',				'Etes-vous sûr(e) de vouloir enlever cette photo ?');
define('_CATA_CONFIRM_SUPPR_RATTACH',			'Etes-vous sûr(e) de vouloir supprimer ce rattachement ?');
define('_CATA_LABEL_CONFIRMDELFAMILY',			'Etes vous sûr de vouloir supprimer cette famille ?');
define('_CATA_LABEL_CONFIRM_DEL_MODELE',		'Etes-vous sûr(e) de vouloir supprimer ce modèle ?');
define('_CATA_LABEL_CONFIRM_DEL_PORT_TARIF',	'Etes-vous sûr(e) de vouloir supprimer ce tarif ?');
define('_CATA_LABEL_CONFIRM_DEL_PORT_TARIF_GRILLE',	'Etes-vous sûr(e) de vouloir supprimer cette grille de tarifs ?');
define('_CATA_LABEL_CONFIRM_DEL_PORT_FORMULE',	'Etes-vous sûr(e) de vouloir supprimer cette formule tarifaire ?');
define('_CATA_LABEL_CONFIRM_DEL_TARIF_DEGR',	'Etes-vous sûr(e) de vouloir supprimer ce tarif ?');

//PICTURES
define('_CATA_ICO_ADD',				'./common/modules/catalogue/img/ico_green_cross.gif');
define('_CATA_ICO_MODELE',			'./common/modules/catalogue/img/modele.gif');
define('_CATA_ICO_MODELE_LIST',		'./common/modules/catalogue/img/modele_list.gif');
define('_CATA_ICO_ATTACH',			'./common/modules/catalogue/img/attach.gif');
define('_CATA_ICO_GROUP',			'./common/modules/catalogue/img/ico_cube.gif');
define('_CATA_ICO_SOUS_REF',		'./common/modules/catalogue/img/ico_herited.gif');
define('_CATA_ICO_TARIF',			'./common/modules/catalogue/img/ico_tarif.gif');
define('_ICO_DESC',					'./common/modules/catalogue/img/ico_desc.png');
define('_ICO_ASC',					'./common/modules/catalogue/img/ico_asc.png');
define('_ICO_QTE_0',				'./common/modules/catalogue/img/stock_out.gif');
define('_ICO_QTE_1',				'./common/modules/catalogue/img/stock_limit.gif');
define('_ICO_QTE_2',				'./common/modules/catalogue/img/stock_ok.gif');
define('_ICO_MINI_QTE_0',			'./common/modules/catalogue/img/mini_stock_out.gif');
define('_ICO_MINI_QTE_1',			'./common/modules/catalogue/img/mini_stock_limit.gif');
define('_ICO_MINI_QTE_2',			'./common/modules/catalogue/img/mini_stock_ok.gif');

//OPTIONS
define('_CATA_OPTION_TXT_IP',		'Votre adresse IP est :');
define('_CATA_OPTION_TXT_0',		'Seuil quantité par défaut');
define('_CATA_OPTION_TXT_1',		'Taille maximum des vignettes (pixel)');
define('_CATA_OPTION_TXT_2',		'Après clic sur commander');
define('_CATA_OPTION_TXT_2_1',		'&nbsp;Rester sur la fiche article');
define('_CATA_OPTION_TXT_2_2',		'&nbsp;Visualiser le panier');
define('_CATA_OPTION_TXT_3',		'Afficher des vignettes dans la popup "zoom" des photos');
define('_CATA_OPTION_TXT_3_1',		'&nbsp;Oui');
define('_CATA_OPTION_TXT_3_2',		'&nbsp;Non');
define('_CATA_OPTION_TXT_5',		'Gérer la TVA dans les commandes');
define('_CATA_OPTION_TXT_5_1',		'&nbsp;Oui');
define('_CATA_OPTION_TXT_5_2',		'&nbsp;Non');
define('_CATA_OPTION_TXT_6',		'Utiliser une confirmation visuelle a l\'inscription');
define('_CATA_OPTION_TXT_6_1',		'&nbsp;Oui');
define('_CATA_OPTION_TXT_6_2',		'&nbsp;Non');
define('_CATA_OPTION_TXT_7',		'Recevoir un mail pour prévenir d\'une commande sur le site');
define('_CATA_OPTION_TXT_7_1',		'&nbsp;Oui');
define('_CATA_OPTION_TXT_7_2',		'&nbsp;Non');
define('_CATA_OPTION_TXT_9',		'Code client par defaut');
define('_CATA_OPTION_TXT_10',		'Taux de tva par defaut');

//COORDONNEES
define('_CATA_COORD_1',			'Société');
define('_CATA_COORD_2',			'Nom');
define('_CATA_COORD_3',			'Prénom');
define('_CATA_COORD_4',			'Adresse');
define('_CATA_COORD_5',			'');
define('_CATA_COORD_6',			'Code postal');
define('_CATA_COORD_7',			'Ville');
define('_CATA_COORD_8',			'Pays');
define('_CATA_COORD_9',			'Tel.1');
define('_CATA_COORD_10',		'Tel.2');
define('_CATA_COORD_11',		'Portable');
define('_CATA_COORD_12',		'Fax');
define('_CATA_COORD_13',		'Email');
define('_CATA_COORD_14',		'Site');
define('_CATA_COORD_15',		'Complément');
define('_CATA_COORD_16',		'Email d\'alerte commande (séparées par ",")');
define('_CATA_COORD_17',		'POUR LES PAIEMENTS');
define('_CATA_COORD_18',		'Ordre des chèques');
define('_CATA_COORD_19',		'Etablissement Bancaire');
define('_CATA_COORD_20',		'Code Banque');
define('_CATA_COORD_21',		'Code Guichet');
define('_CATA_COORD_22',		'Compte');
define('_CATA_COORD_23',		'Cle RIB');
define('_CATA_COORD_24',		'NUM TVA Intra');

//CMS
define('_CATA_CMS_QTE_PLUS',		'<img src="./common/modules/catalogue/img/quantite_plus.gif" width="14" height="10" border="0" alt="Ajouter">');
define('_CATA_CMS_QTE_MOINS',		'<img src="./common/modules/catalogue/img/quantite_moins.gif" width="14" height="10" border="0" alt="Retirer">');
define('_CATA_CMS_COMMANDER',		'<input type="image" src="./common/modules/catalogue/img/bouton_commander.gif" width="85" height="24" border="0" alt="Commander ce produit" align="absmiddle">');

//PANIER
define('_CATA_CMS_PANIER',			'Panier');
define('_CATA_CMS_PANIER_ART',		'article');
define('_CATA_CMS_PANIER_ARTS',		'articles');
define('_CATA_CMS_PANIER_REF',		'Ref.');
define('_CATA_CMS_PANIER_DESIGN',	'Désignation');
define('_CATA_CMS_PANIER_PUHT',		'P.U.HT');
define('_CATA_CMS_PANIER_PUTTC',	'P.U.TTC');
define('_CATA_CMS_PANIER_TX_TVA',	'Taux TVA');
define('_CATA_CMS_PANIER_QTE',		'Qté');
define('_CATA_CMS_PANIER_DISPO',	'Dispo.');
define('_CATA_CMS_PANIER_THT',		'Somme HT');
define('_CATA_CMS_PANIER_TTTC',		'Somme TTC');
define('_CATA_CMS_PANIER_SUPPR',	'Suppr');
define('_CATA_CMS_PANIER_ICO_SUPPR','<img src="./common/modules/catalogue/img/ico_delete2.gif" width="14" height="16" border="0" alt="Supprimer">');
define('_CATA_CMS_SS_TOTAL_HT',		'Sous-total HT :');
define('_CATA_CMS_TVA',				'TVA :');
define('_CATA_CMS_TVA_DONT',		'dont TVA :');
define('_CATA_CMS_SS_TOTAL_TTC',	'Sous-total TTC :');
define('_CATA_CMS_FRAIS_PORT',		'Frais de port :');
define('_CATA_CMS_TOTAL_TTC',		'TOTAL TTC:');
define('_CATA_CMS_TOTAL_HT',		'TOTAL HT:');

define('_CATA_CMS_INSCR_ERREUR_1',	'Cet identifiant est déjà utilisé');
define('_CATA_CMS_INSCR_ERREUR_2',	'Confirmation visuelle erronée');
define('_CATA_CMS_INSCR_ERREUR_3',	'Cette adresse mail est déjà utilisée');

//Etape CDE 3 verif et mode de paiement
define('_CATA_CDE_ETAP3_TEXT1',		'ADRESSE DE FACTURATION');
define('_CATA_CDE_ETAP3_TEXT2',		'ADRESSE DE LIVRAISON');
define('_CATA_CDE_ETAP3_TEXT3',		'COMMANDE');
define('_CATA_CDE_ETAP3_TEXT4',		'MODE DE PAIEMENT');
define('_CATA_CDE_ETAP3_TEXT5',		'Carte Bancaire');
define('_CATA_CDE_ETAP3_TEXT6',		'Chèque');
define('_CATA_CDE_ETAP3_TEXT7',		'J\'accepte les conditions générales de vente du site.');
define('_CATA_CDE_ETAP3_TEXT10',	'Virement');

//Mon Compte
define('_CATA_LABEL_MY_CPT',		'Mon Compte');
define('_CATA_LABEL_INFOSPERSO',	'Informations personnelles');
define('_CATA_LABEL_COMMANDES',		'Commandes');
define('_CATA_LABEL_HISTORIQUE',	'Historique');
define('_CATA_LABEL_SAISIERAPIDE',	'Saisie par référence');
define('_CATA_DESC_INFOSPERSO',		'Editer vos informations personnelles');
define('_CATA_DESC_COMMANDES',		'Voir l\'état de vos commandes en cours');
define('_CATA_DESC_HISTORIQUE',		'Voir l\'historique de vos commandes');
define('_CATA_DESC_SAISIERAPIDE',	'Saisir rapidement une commande en insérant les références que vous connaissez');

//Title (haut de corp) pour les pages de panier
define('_CATA_TITLE_PANIER_0',		'Edition du panier');
define('_CATA_TITLE_PANIER_1',		'Connection / Nouvelle inscription');
define('_CATA_TITLE_PANIER_2',		'Nouvelle inscription');
define('_CATA_TITLE_PANIER_3',		'Contrôle des coordonnées');
define('_CATA_TITLE_PANIER_4',		'Validation de la commande');
define('_CATA_TITLE_PANIER_5',		'Commande');

//Lost password
define('_CATA_LOST_PASS_ERREUR1',	'Cette adresse email est inconnue');
define('_CATA_LOST_PASS_ERREUR2',	'Ce compte n\'a pas d\'adresse email renseignée');
define('_CATA_LOST_PASS_EXPED',		'Un email a été envoyé à l\'adresse suivante');

//MAIL
define('_SUBJECT_CDE',				'Confirmation de commande');
define('_SUBJECT_INSCR',			'Confirmation d\'inscription site ');
define('_SUBJECT_PASS',				'Nouveau mot de passe site ');

// Administration du client
define ("_CATALOGUE_LABELICON_USERS", "Utilisateurs");
define ("_CATALOGUE_LABELICON_GROUP", "Service");
define ("_CATALOGUE_LABELICON_LIVRAISON", "Livraison");
define ("_CATALOGUE_LABELICON_HISTORY", "Historique des budgets");

define ("_CATALOGUE_LABEL_GROUP_MANAGEMENT", "Gestion du Service <LABEL> [ <GROUP> ]");
define ("_CATALOGUE_LABEL_GROUP_INFORMATION", "Informations sur le Service");
define ("_CATALOGUE_LABEL_GROUP_MODIFY", "Modifier le Service");
define ("_CATALOGUE_LABEL_MANAGEMENT", "Gestion");

define ("_CATALOGUE_LABEL_GROUP_NAME", 		"Nom");
define ("_CATALOGUE_LABEL_CREATE_CHILD",		"Créer un sous-service <LABEL>");
define ("_CATALOGUE_LABEL_DELETE_GROUP",		"Supprimer ce service <LABEL>");
define ("_CATALOGUE_LABEL_GROUP_FATHER", 		"Groupe Père");
define ("_CATALOGUE_LABEL_USER_PROFILE",		"Profil par défaut");
define ("_CATALOGUE_LABEL_USER_UNDEFINED",	"Non défini");

define ("_CATALOGUE_LABELTAB_USERLIST", "Liste des Utilisateurs");
define ("_CATALOGUE_LABELTAB_USERATTACH", "Rattacher un Utilisateur");
define ("_CATALOGUE_LABELTAB_USERADD", "Ajouter un Utilisateur");

define ("_CATALOGUE_LABELTAB_RULESLIST", "Liste des règles");
define ("_CATALOGUE_LABELTAB_RULESADD", "Ajouter une règle");

define ("_CATALOGUE_LABEL_MODIFY", "Modifier");
define ("_CATALOGUE_LABEL_DELETE", "Supprimer");

define ("_CATALOGUE_LABEL_TYPE", "Type");
define ("_CATALOGUE_LABEL_LASTNAME", "Nom");
define ("_CATALOGUE_LABEL_FIRSTNAME", "Prénom");
define ("_CATALOGUE_LABEL_LOGIN", "Login");
define ("_CATALOGUE_LABEL_ACTION", "Action");
define ("_CATALOGUE_LABEL_USER", "Utilisateur");
define ("_CATALOGUE_LABEL_PASSWORD", "Mot de Passe");
define ("_CATALOGUE_LABEL_PASSWORD_CONFIRM", "Confirmation du Mot de Passe");
define ("_CATALOGUE_LABEL_EXPIRATION_DATE", "Date d'Expiration");
define ("_CATALOGUE_LABEL_ORIGIN", "Origine");
define ("_CATALOGUE_LABEL_BUDGET", "Budget");
define ("_CATALOGUE_LABEL_LEVEL", "Niveau");
define ("_CATALOGUE_LABEL_COMMENTS", "Commentaires");
define ("_CATALOGUE_LABEL_ADDRESS", "Adresse");
define ("_CATALOGUE_LABEL_PHONE", "Téléphone");
define ("_CATALOGUE_LABEL_FAX", "Fax");
define ("_CATALOGUE_LABEL_EMAIL", "Mail");
define ("_CATALOGUE_LABEL_DETACH", "Détacher");

define ("_CATALOGUE_MSG_CONFIRMUSERDETACH", 'Êtes-vous certain de vouloir\ndétacher cet Utilisateur ?');
define ("_CATALOGUE_MSG_CONFIRMUSERDELETE", 'Êtes-vous certain de vouloir\nsupprimer cet Utilisateur ?');
define ("_CATALOGUE_MSG_PASSWORDERROR", 'Erreur lors de la saisie du mot de passe.<BR>Vous devez saisir deux fois le mot de passe.');
define ("_CATALOGUE_MSG_LOGINERROR", "Erreur lors de la création de l'utilisateur.<BR>Ce login existe déjà.");
define ("_CATALOGUE_MSG_LOGINPASSWORDERROR", 'Erreur lors de la saisie du mot de passe.<BR>Votre mot de passe a été rejeté par le système');

define ("_CATALOGUE_PANIER_TYPE_LIST_CLASSIQUE", 'Panier type');
define ("_CATALOGUE_PANIER_TYPE_LIST_SCOLAIRE", 'Liste scolaire');

// Messages d'erreur
global $err_msg;
$err_msg = array(
	0 => "Impossible de supprimer cette adresse de livraison<BR>car elle est rattachée à un service.",
	1 => "Vous n'avez pas le budget suffisant pour affecter une telle somme.",
	2 => "La somme des budgets répartis dans les sous-services<BR>et la somme des commandes passées<BR>est supérieure à la valeur que vous avez rentré.<BR><BR>La valeur a été ajustée au plus bas.",
	3 => "Erreur ! Ce login existe déjà.",
	4 => "Vous devez sélectionner une adresse de livraison dans la liste ou en saisir une nouvelle.",
	5 => "Vous devez saisi une valeur supérieure<br>au budget à répartir.<br>La valeur a été ajustée au plus haut."
);

// taux de TVA
global $db;
global $a_tva;
$a_tva = array();
$rs = $db->query('SELECT * FROM `dims_mod_cata_tva`');
while ($row = $db->fetchrow($rs)) {
	$a_tva[$row['id_tva']] = $row['tx_tva'];
}

// Départements
global $a_depts;
$a_depts = array(
'01' => 'Ain',
'02' => 'Aisne',
'03' => 'Allier',
'04' => 'Alpes-de-Haute-Provence',
'05' => 'Hautes-Alpes',
'06' => 'Alpes-Maritimes',
'07' => 'Ardèche',
'08' => 'Ardennes',
'09' => 'Ariège',
'10' => 'Aube',
'11' => 'Aude',
'12' => 'Aveyron',
'13' => 'Bouches-du-Rhône',
'14' => 'Calvados',
'15' => 'Cantal',
'16' => 'Charente',
'17' => 'Charente-Maritime',
'18' => 'Cher',
'19' => 'Corrèze',
'2A' => 'Corse-du-Sud',
'2B' => 'Haute-Corse',
'21' => 'Côte-d\'Or',
'22' => 'Côtes-d\'Armor',
'23' => 'Creuse',
'24' => 'Dordogne',
'25' => 'Doubs',
'26' => 'Drôme',
'27' => 'Eure',
'28' => 'Eure-et-Loir',
'29' => 'Finistère',
'30' => 'Gard',
'31' => 'Haute-Garonne',
'32' => 'Gers',
'33' => 'Gironde',
'34' => 'Hérault',
'35' => 'Ille-et-Vilaine',
'36' => 'Indre',
'37' => 'Indre-et-Loire',
'38' => 'Isère',
'39' => 'Jura',
'40' => 'Landes',
'41' => 'Loir-et-Cher',
'42' => 'Loire',
'43' => 'Haute-Loire',
'44' => 'Loire-Atlantique',
'45' => 'Loiret',
'46' => 'Lot',
'47' => 'Lot-et-Garonne',
'48' => 'Lozère',
'49' => 'Maine-et-Loire',
'50' => 'Manche',
'51' => 'Marne',
'52' => 'Haute-Marne',
'53' => 'Mayenne',
'54' => 'Meurthe-et-Moselle',
'55' => 'Meuse',
'56' => 'Morbihan',
'57' => 'Moselle',
'58' => 'Nièvre',
'59' => 'Nord',
'60' => 'Oise',
'61' => 'Orne',
'62' => 'Pas-de-Calais',
'63' => 'Puy-de-Dôme',
'64' => 'Pyrénées-Atlantiques',
'65' => 'Hautes-Pyrénées',
'66' => 'Pyrénées-Orientales',
'67' => 'Bas-Rhin',
'68' => 'Haut-Rhin',
'69' => 'Rhône',
'70' => 'Haute-Saône',
'71' => 'Saône-et-Loire',
'72' => 'Sarthe',
'73' => 'Savoie',
'74' => 'Haute-Savoie',
'75' => 'Paris',
'76' => 'Seine-Maritime',
'77' => 'Seine-et-Marne',
'78' => 'Yvelines',
'79' => 'Deux-Sèvres',
'80' => 'Somme',
'81' => 'Tarn',
'82' => 'Tarn-et-Garonne',
'83' => 'Var',
'84' => 'Vaucluse',
'85' => 'Vendée',
'86' => 'Vienne',
'87' => 'Haute-Vienne',
'88' => 'Vosges',
'89' => 'Deux-Sèvres',
'90' => 'Territoire de Belfort',
'91' => 'Essonne',
'92' => 'Hauts-de-Seine',
'93' => 'Seine-Saint-Denis',
'94' => 'Val-de-Marne',
'95' => 'Val-d\'Oise',
'97-98' => 'DOM / TOM'
);
