<?php
class dims_const_desktopv2 extends dims_const {
	const DESKTOP_V2_DESKTOP =		1;
	const DESKTOP_V2_CONCEPTS =		2;

	const DESKTOP_V2_LIMIT_CONNEXION =		3;
	const DESKTOP_V2_LIMIT_COMPANIES =		5;

	const DESKTOP_V2_LIMIT_ACTIVITIES =	5;
	const DESKTOP_V2_LIMIT_OPPORTUNITIES =	5;
	const DESKTOP_V2_LIMIT_TAGS =			10;

	const _ACTIVITY_AVATAR_MAX_SIZE =		2097152; //2 * 1024 * 1024;									// avatar max filesize (1Mo)
	const _ACTIVITY_AVATAR_MAX_WIDTH =	60;												// avatar max width (px)
	const _ACTIVITY_AVATAR_MAX_HEIGHT =	60;												// avatar max height (px)
	const _ACTIVITY_AVATAR_WEB_PATH =		'/data/activities/avatars/';					// avatar web path
	const _ACTIVITY_AVATAR_FILE_PATH =		_ACTIVITY_AVATAR_WEB_PATH; // realpath('.')._ACTIVITY_AVATAR_WEB_PATH;		// avatar file path

	const _OPPORTUNITY_AVATAR_MAX_SIZE =		2097152; //2 * 1024 * 1024;									// avatar max filesize (1Mo)
	const _OPPORTUNITY_AVATAR_MAX_WIDTH =	60;												// avatar max width (px)
	const _OPPORTUNITY_AVATAR_MAX_HEIGHT =	60;												// avatar max height (px)
	const _OPPORTUNITY_AVATAR_WEB_PATH =		'/data/opportunities/avatars/';					// avatar web path
	const _OPPORTUNITY_AVATAR_FILE_PATH =		_OPPORTUNITY_AVATAR_WEB_PATH;

	const _DESKTOP_V2_ADDRESS_BOOK_ALL_CONTACT =		-1;
	const _DESKTOP_V2_ADDRESS_BOOK_LAST_LINKED =		-2;
	const _DESKTOP_V2_ADDRESS_BOOK_FAVORITES =		-3;
	const _DESKTOP_V2_ADDRESS_BOOK_MONITORED =		-4;

	const _DESKTOP_V2_EXCEL_NEW = 		1;
	const _DESKTOP_V2_EXCEL_UPDATE = 	2;
	const _DESKTOP_V2_EXCEL_CREATE = 	3;

	const DESKTOP_V2_CONCEPTS_INFOS_GENERALES = "infos_generales";
	const DESKTOP_V2_CONCEPTS_EVENTS_ACTIVITIES = "events_activities";
	const DESKTOP_V2_CONCEPTS_SUIVIS = "suivis";
	const DESKTOP_V2_CONCEPTS_CONTACTS_COMPANIES = "contacts_companies";
	const DESKTOP_V2_CONCEPTS_DOCUMENTS = "documents";
	const DESKTOP_V2_CONCEPTS_TODOS = "todos";
	const DESKTOP_V2_CONCEPTS_COMMENTS = "commentaires";

	const _NEWSLETTERS_DESKTOP              = 1;
	const _FICHE_NEWSLETTER        			= 2;
	const _NEWS_DESCRIPTION        			= 3;
	const _NEWS_RECIPIENTS        			= 4;
	const _NEWS_SENDINGS        			= 5;
	const _NEWS_TAGS        			= 55;
	const _NEWS_DELETE_NEWSLETTER                   =6;
	const _NEWS_ADD_NEWSLETTER_MODEL                =7;
	const _NEWS_SAVE_NEWSLETTER_MODEL               =8;
	const _NEWS_SAVE_TAG                           =88;
	const _NEWSLETTERS_MAILINGLIST                  =9;
	const _NEWSLETTERS_MAILINGLIST_DETAIL           =10;
	const _NEWSLETTERS_MAILINGLIST_NEWSLINKED       =11;
	const _NEWSLETTERS_MAILINGLIST_ADD		=12;
	const _NEWSLETTER_ACTION_CHG_EMAIL_STATE        =32;
	const _NEWSLETTER_SAVE_RATTACH_EMAIL            =33;
	const _NEWSLETTER_ACTION_SUPP_EMAIL             =34;
	const _NEWSLETTER_ACTION_SUPP_LIST              =35;
	const _NEWSLETTER_SAVE_RATTACH_NEWS             =36;
	const _NEWSLETTER_SAVE_LIST_EMAIL               =37;
	const _NEWSLETTER_DELETE_MAILING_LIST           =38;
	const _NEWSLETTER_DELETE_INSC                   =39;
	const _NEWSLETTER_RECREATE_INSC                 =40;
	const _NEWSLETTER_INSC_FROMBACK                 =41;
	const _NEWSLETTER_SEND_ARTICLE                  =42;
	const _NEWSLETTER_TEST_SENDING                  =43;
	const _NEWS_RECIPIENTS_RESET_FILTER             =44;
	const _NEWSLETTER_ATTACH_REGISTRATION           =45;
	const _NEWSLETTER_ATTACH_REGISTRATION_SAVE      =46;
	const _NEWSLETTER_ATTACH_REGISTRATION_DELETE    =47;
	const _NEWSLETTER_TAG_DELETE                    =48;
	const _NEWSLETTER_ARTICLE_DELETE                =480;
	const _NEWSLETTER_ARTICLE_SAVE                  =49;
	const _NEWSLETTER_ARTICLE_EDIT                  =50;
	const _NEWSLETTER_ARTICLE_NEW                   =51;
	const _NEWSLETTER_GET_EMAILS                   =52;
	const _DOCUMENTS_EDIT_FOLDER					=101;
	const _DOCUMENTS_ADD_FOLDER						=102;
	const _DOCUMENTS_EDIT_FILE						=103;
	const _DOCUMENTS_EXTRACT_FILE					=104;
	const _CONCEPTS_DOCUMENTS_VIEW					= "preview";

	public static function set_OPPORTUNITY_AVATAR_FILE_PATH($val){
		$_OPPORTUNITY_AVATAR_FILE_PATH = $val;
	}
}
?>
