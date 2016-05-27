<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_const
 *
 * @author patricknourrissier
 */

 class dims_const {
	const _DIMS_VERSION                             ='v6.0';
	const _DIMS_MODULE_SITE                         =1;
	const _DIMS_COPYRIGHT                           ='<a target="_blank" class="copyright" href="http://www.dims.fr">DIMS Portal v6.0 - Netlor Concept 2000-2013</a>';
	const _DIMS_NUM_INPUT                           =0;
	const _DIMS_CHAR_INPUT                          =1;
	const _DIMS_MAIL_INPUT                          =2;
	const _DIMS_NB_ELEM_PAGE                        =8;
	const _DIMS_SECURITY_LEVEL_CRITICAL             =3;
	const _ACTION_COMMENT=				1;
	const _ACTION_LINK=					4;
	const _ACTION_UPDATE_LINK=			41;
	const _ACTION_CREATE=				5;
	const _ACTION_TAG=					6;
	const _ACTION_UPDATE=				7;

	const _ACTION_CREATE_EVENT=			10;
	const _ACTION_MODIFY_EVENT=			11;
	const _ACTION_DELETE_EVENT=			12;

	const _ACTION_CREATE_DOC=			21;
	const _ACTION_UPDATE_DOC=			22;
	const _ACTION_DELETE_DOC=			23;

	const _ACTION_CREATE_CONTACT=		70;
	const _ACTION_UPDATE_CONTACT=		71;
	const _ACTION_UPDATE_OWN_CONTACT=		72;

	const _ACTION_CREATE_TIERS=			60;
	const _ACTION_UPDATE_TIERS=			61;

	const _ACTION_DOSSIER_CREATE_DOSSIER=		100;
	const _ACTION_DOSSIER_CLOSE_DOSSIER=		101;
	const _ACTION_DOSSIER_DEPOT_DOCUMENT=		102;
	const _ACTION_DOSSIER_RECEIVE_MAIL=			103;
	const _ACTION_DOSSIER_SEND_MAIL=			104;
	const _ACTION_DOSSIER_SEND_CALL=			105;
	const _ACTION_DOSSIER_RECEIVE_CALL=			106;
	const _ACTION_DOSSIER_WORKFLOW_STATE_CHANGED=107;
	const _ACTION_DOSSIER_DEFINE_STAR=			108;
	const _ACTION_DOSSIER_RETRAIT_DOC=			109;

	const _DIMS_LABEL_EMAIL_ADD=		80;
	const _DIMS_LABEL_EMAIL_DELETE=		81;

	const _PLANNING_H_START=			7;		// heure de d�but
	const _PLANNING_H_END=				23;	// heure de fin
	const _DIMS_UNIDENTIFIEDUSER=			"0";
	const _DIMS_UNIDENTIFIEDGROUP=			"0";
	const _DIMS_SYSTEMGROUP=				"1"; // virtual system group

	const _SYSTEM_OBJECT_WORKSPACE=         1;
	const _SYSTEM_OBJECT_GROUP= 			2;
	const _SYSTEM_OBJECT_USER=	 			3;
	const _SYSTEM_OBJECT_PROJECT=  			4;
	const _SYSTEM_OBJECT_TASK=  			5;
	const _SYSTEM_OBJECT_TIERS=				6;
	const _SYSTEM_OBJECT_CONTACT=			7;
	const _SYSTEM_OBJECT_ACTION=			8;
	const _SYSTEM_OBJECT_DOCFILE=			2; // changement pour etre coherent avec la ged
	const _SYSTEM_OBJECT_EVENT=				10;
	const _SYSTEM_OBJECT_NEWSLETTER=        11;
	const _SYSTEM_OBJECT_EVENT_PARTNERS=    12;
	const _SYSTEM_OBJECT_LIST_DIFF=         13;
	const _SYSTEM_OBJECT_FAVORITES=			14;
	const _SYSTEM_OBJECT_IMPORT=			15;
	const _SYSTEM_OBJECT_LINK=				16;
	const _SYSTEM_OBJECT_MAIL=				17;
	const _SYSTEM_OBJECT_RSS=				18;
	const _SYSTEM_OBJECT_WCE_OBJECT=		19;
	const _SYSTEM_OBJECT_RSS_ARTICLE=		20;
	const _SYSTEM_OBJECT_WCE_ARTICLE=		21;
	const _SYSTEM_OBJECT_WCE_MAILLING_SEND=	22;
	const _SYSTEM_OBJECT_WCE_MAILLING_MAIL=	23;
	const _SYSTEM_OBJECT_WCE_ARTICLE_BLOCK= 3;//Cyril - La constante est issue de Pat, projet URPS
	const _SYSTEM_OBJECT_DOCFOLDER=			1; // changement pour docfolder
	const _SYSTEM_OBJECT_TAG=				25;
	const _SYSTEM_OBJECT_TAG_CATEGORY=			250;
	const _SYSTEM_OBJECT_CATEGORY=			26;
	const _SYSTEM_OBJECT_CASE=				27;
	const _SYSTEM_OBJECT_FAQ=				28;
	const _SYSTEM_OBJECT_GLOSSAIRE=			29;
	const _SYSTEM_OBJECT_ACTIVITY=			30;
	const _SYSTEM_OBJECT_SUIVI=             31;
	const _SYSTEM_OBJECT_OPPORTUNITY=		32;
	const _SYSTEM_OBJECT_TODO=				33;
	const _SYSTEM_OBJECT_SUIVI_DETAIL=		34;
	const _SYSTEM_OBJECT_APPOINTMENT_OFFER=	35;

	const _SYSTEM_OBJECT_CPE_CLIENT=				225;
	const _SYSTEM_OBJECT_CPE_VERSION_CONTRAT=		226;
	const _SYSTEM_OBJECT_CPE_DOCUMENT=				227;


	const _SYSTEM_OBJECT_CONTACT_WORKSPACE=	71;
	const _SYSTEM_OBJECT_CONTACT_USER=		711;

	const _SYSTEM_OBJECT_TIERS_WORKSPACE=	61;
	const _SYSTEM_OBJECT_TIERS_USER=		611;
	const _SYSTEM_OBJECT_TIERS_SERVICE =	62;

	const _SYSTEM_SYSTEMADMIN=				0;
	const _SYSTEM_WORKSPACES=				'work';
	const _SYSTEM_GROUPS=					'org';

	const _SYSTEM_LANG_FR=				1;
	const _SYSTEM_LANG_EN=				2;

	const _DIMS_MSG_DBERROR=			"Database connection error, please contact administrator";

	const _DIMS_CACHE_DEFAULT_LIFETIME=	"600";

	const _DIMS_MODULE_SYSTEM=			"1";
	const _DIMS_SUBMENU_MESSAGE=		"1";
	const _DIMS_SUBMENU_ACTIVITIES=		"2";
	const _DIMS_SUBMENU_COLLABORATION_PERS="3";
	const _DIMS_SUBMENU_PROJECT=		"4";
	const _DIMS_SUBMENU_CONTACT=		"5";
	const _DIMS_SUBMENU_VEILLE=			"6";
	const _DIMS_SUBMENU_SEARCH=			"7";
	const _DIMS_SUBMENU_EVENT=			"8";
	const _DIMS_SUBMENU_TODO=			"9";
	const _DIMS_SUBMENU_HISTO=			"10";
	const _DIMS_SUBMENU_MSG=			"11";
	const _DIMS_SUBMENU_TAGS=			"12";
	const _DIMS_SUBMENU_DETAIL=			"13";
	const _DIMS_SUBMENU_PREVIEW=		"14";
	const _DIMS_SUBMENU_DIFFUSION_LIST=	"15";
	const _DIMS_SUBMENU_COMMENT_GEN=	"16";
	const _DIMS_SUBMENU_FAVORITES=		"17";
	const _DIMS_SUBMENU_USERS=			"18";
	const _DIMS_SUBMENU_TODOSEND=		"19";
	const _DIMS_SUBMENU_DOCS=			"20";
	const _DIMS_SUBMENU_EVENTCONTACT=	"21";
	const _DIMS_SUBMENU_IMMO=			"22";
	const _DIMS_SUBMENU_EMAIL=			"23";
	const _DIMS_SUBMENU_VCARD=			"24";

	const _DIMS_PROJECTMENU_RESUME=		"1";
	const _DIMS_PROJECTMENU_PROJECT=	"2";
	const _DIMS_PROJECTMENU_TASK=		"3";
	const _DIMS_PROJECTMENU_CURRENTPROJECT="4";
	const _DIMS_PROJECTMENU_ADD_PROJECT="5";

	const _DIMS_MENU_HOME=				"0";
	const _DIMS_MENU_MYGROUPS=			"1";
	const _DIMS_MENU_PROFILE=			"2";
	const _DIMS_MENU_ABOUT=				"3";
	const _DIMS_MENU_ANNOTATIONS=		"4";
	const _DIMS_MENU_TICKETS=			"5";
	const _DIMS_MENU_SEARCH=			"6";
	const _DIMS_MENU_PROJECTS=			"7";
	const _DIMS_MENU_PLANNING=			"8";
	const _DIMS_MENU_CONTACT=			"9";
	const _DIMS_MENU_NEWSLETTER=					   "11";

	const _DIMS_MENU_MODULEDOC=			"doc";
	const _DIMS_MENU_MODULECONTENT=		"content";
	const _DIMS_MENU_MODULEWATCH=		"watch";
	const _DIMS_MENU_MODULEDIRECTORY=	"directory";

	const _DIMS_DATE_YEAR=				"1";
	const _DIMS_DATE_MONTH=				"2";
	const _DIMS_DATE_DAY=				"3";
	const _DIMS_DATE_HOUR=				"4";
	const _DIMS_DATE_MINUTE=			"5";
	const _DIMS_DATE_SECOND=			"6";

	const _DIMS_TICKETS_NONE=				0;
	const _DIMS_TICKETS_OPENED=				1;
	const _DIMS_TICKETS_DONE=				2;

	// DO NOT MODIFY !
	const DIMS_DATEFORMAT_FR=			"d/m/Y";
	const DIMS_DATEFORMAT_US=			"Y-m-d";

	const DIMS_DATEFORMAT_EREG_FR=		"/^([0-9]{1,2})[-\/\.]([0-9]{1,2})[-\/\.]([0-9]{4})$/";
	const DIMS_DATEFORMAT_EREG_US=		"/^([0-9]{4})[-\/\.]([0-9]{1,2})[-\/\.]([0-9]{1,2})$/";

	// Depuis PHP 5.3.0, les regex posix sont obsolètes (fonction ereg*, split/spliti, sql_regcase)
	// l'appel à n'importe quelle fonction de cette extension émettra une alerte de type E_DEPRECATED.
	// http://www.php.net/manual/fr/intro.regex.php
	const _DIMS_TIMEFORMAT=				"H:i:s";
	const _DIMS_TIMEFORMAT_EREG=		"([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})"; // DEPRECATED
	const _DIMS_TIMEFORMAT_PREG=		"/([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/";
	const _DIMS_TIMEFORMATDISP=			"H:i";
	const _DIMS_TIMEFORMATDISP_EREG=		"([0-9]{1,2})[:,h]([0-9]{1,2})"; // DEPRECATED
	const _DIMS_TIMEFORMATDISP_PREG=		"/([0-9]{1,2})[:,h]([0-9]{1,2})/";
	const _DIMS_DATETIMEFORMAT_MYSQL=		"Y-m-d H:i:s";
	const _DIMS_DATETIMEFORMAT_MYSQL_EREG=	"([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})";// DEPRECATED
	const _DIMS_DATETIMEFORMAT_MYSQL_PREG=	"/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/";
	const _DIMS_TIMESTAMPFORMAT_MYSQL=		"YmdHis";
	const _DIMS_TIMESTAMPFORMAT_MYSQL_EREG="([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})"; // DEPRECATED
	const _DIMS_TIMESTAMPFORMAT_MYSQL_PREG="/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/";

	// NOW YOU CAN MODIFY !
	const DIMS_DATEFORMAT=				self::DIMS_DATEFORMAT_FR;

	// PREDEFINED ACTIONS
	const _SYSTEM_ACTION_LOGIN_OK=					25;
	const _SYSTEM_ACTION_LOGIN_ERR=					26;

	const _SYSTEM_ACTION_ACTIVITY_CREATE=			43;
	const _SYSTEM_ACTION_ACTIVITY_VIEW_OWNS=		44;
	const _SYSTEM_ACTION_ACTIVITY_VIEW_OTHERS=		45;
	const _SYSTEM_ACTION_ACTIVITY_MODIFY_OWNS=		46;
	const _SYSTEM_ACTION_ACTIVITY_MODIFY_OTHERS=	47;
	const _SYSTEM_ACTION_ACTIVITY_CANCEL_OWNS=		48;
	const _SYSTEM_ACTION_ACTIVITY_CANCEL_OTHERS=	49;
	const _SYSTEM_ACTION_ACTIVITY_DELETE_OWNS=		50;
	const _SYSTEM_ACTION_ACTIVITY_DELETE_OTHERS=	51;

	const _SYSTEM_ACTION_LEAD_CREATE=			52;
	const _SYSTEM_ACTION_LEAD_VIEW_OWNS=		53;
	const _SYSTEM_ACTION_LEAD_VIEW_OTHERS=		54;
	const _SYSTEM_ACTION_LEAD_MODIFY_OWNS=		55;
	const _SYSTEM_ACTION_LEAD_MODIFY_OTHERS=	56;
	const _SYSTEM_ACTION_LEAD_CANCEL_OWNS=		57;
	const _SYSTEM_ACTION_LEAD_CANCEL_OTHERS=	58;
	const _SYSTEM_ACTION_LEAD_DELETE_OWNS=		59;
	const _SYSTEM_ACTION_LEAD_DELETE_OTHERS=	60;

	// MODULE VIEW MODE
	const _DIMS_VIEWMODE_UNDEFINED=		0;
	const _DIMS_VIEWMODE_PRIVATE=		1;
	const _DIMS_VIEWMODE_DESC=			2;
	const _DIMS_VIEWMODE_ASC=			3;
	const _DIMS_VIEWMODE_GLOBAL=		4;
	const _DIMS_VIEWMODE_VERTICAL=		5; // Agrégation de DESC & ASC

	// USER LEVEL
	const _DIMS_ID_LEVEL_USER=			10;
	const _DIMS_ID_LEVEL_GROUPMANAGER=	15;
	const _DIMS_ID_LEVEL_GROUPADMIN=	20;
	const _DIMS_ID_LEVEL_SYSTEMADMIN=	99;

	const _DIMS_CSTE_FAVORITE=			0;
	const _DIMS_CSTE_ACTIVEFILE=		1;
	const _DIMS_CSTE_TOVALID=			1;
	const _DIMS_CSTE_TOVIEW=			2;
	const _DIMS_CSTE_TONEWS=			3;
	const _DIMS_CSTE_SURVEY=			4;
	const _DIMS_CSTE_TOCONFIRM=			5;
	const _DIMS_CSTE_SEND=				6;
	const _DIMS_CSTE_CREATE=			7;


	const _DIMS_MENU_MAIL_RECEIVED=		1;
	const _DIMS_MENU_MAIL_SENT=			2;
	const _DIMS_MENU_MAIL_READ=			3;
	const _DIMS_MENU_IMPORT_VCF=		4;

	//Types d'actions (sur planning)
	const _PLANNING_ACTION_RDV=					1;
	const _PLANNING_ACTION_EVT=					2;
	const _PLANNING_ACTION_RCT=					3;
	const _PLANNING_ACTION_TSK=					4;
	const _PLANNING_ACTION_ACTIVITY=			5;
	const _PLANNING_ACTION_OPPORTUNITY=			6;
	const _PLANNING_ACTION_APPOINTMENT_OFFER=	7;
	const _PLANNING_ACTION_INVITATION=			8;

	const _DIMS_CSTE_CURRENTPROJECT =		0;
	const _DIMS_CSTE_MILESTONE =			1;
	const _DIMS_CSTE_TASK =					2;
	const _DIMS_CSTE_ACTION =			3;
	const _DIMS_CSTE_ADDPROJECT =		4;
	const _DIMS_CSTE_ANNOT =				5;
	const _DIMS_CSTE_GANTT =				6;
	const _DIMS_CSTE_ADDTASK =			7;
	const _DIMS_CSTE_USERAFFECT =		8;
	const _DIMS_CSTE_PROPERTIES =		9;
	const _DIMS_CSTE_PHASE =				10;
	const _DIMS_CSTE_DOC =				11;
	const _DIMS_CSTE_PERS_CONC =			12;

	// CONSTANTES POUR LES TODOS / COLLABORATION
	const _SHOW_COLLABORATION =			'show_collaboration';
	const _EDIT_INTERVENTION =			'edit_intervention';
	const _SAVE_INTERVENTION =			'save_intervention';

	// URL Réseaux sociaux
	const _RS_TWITTER =				'https://twitter.com/';
	const _RS_FACEBOOK =			'https://facebook.com/';
	const _RS_GOOGLE_PLUS =			'https://plus.google.com/';
	const _RS_YOUTUBE =				'https://www.youtube.com/user/';

	// generic exit codes
	const EXIT_SUCCESS	= 0;
	const EXIT_ERROR	= 1;
}

?>
