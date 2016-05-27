<?php
define ('_SYSTEM_ICON_SYSTEM_INSTALLMODULES',		'system_installmodules');
define ('_SYSTEM_ICON_SYSTEM_PARAMS',				'system_params');
define ('_SYSTEM_ICON_SYSTEM_DOMAINS',				'system_domains');
define ('_SYSTEM_ICON_SYSTEM_USERS',				'system_users');
define ('_SYSTEM_ICON_SYSTEM_TOOLS',				'system_tools');
define ('_SYSTEM_ICON_SYSTEM_LOGS',					'system_logs');
define ('_SYSTEM_ICON_SYSTEM_INDEX',				'system_index');
define ('_SYSTEM_ICON_SYSTEM_MAILBOX',				'system_mailbox');
define ('_SYSTEM_ICON_SYSTEM_LANG',					'system_lang');
define ('_SYSTEM_ICON_SYSTEM_INSTALLSKINS',			'system_installskins');
define ('_SYSTEM_ICON_SYSTEM_TRADUCTION',			'system_traduction');
define ('_SYSTEM_ICON_SYSTEM_JABBER',				'system_jabber');
define ('_SYSTEM_ICON_SYSTEM_CATEGORY',				'system_category');
define ('_SYSTEM_ICON_SYSTEM_SERVER',				'system_server');
define ('_SYSTEM_ICON_SYSTEM_SYNCHRO',				'system_synchro');


define ('_SYSTEM_ICON_GROUP',						'group');
define ('_SYSTEM_ICON_MAILINGLIST',					'mailinglist');
define ('_SYSTEM_ICON_MODULES',						'modules');
define ('_SYSTEM_ICON_PARAMS',						'params');
define ('_SYSTEM_ICON_DOMAINS',						'domains');
define ('_SYSTEM_ICON_ROLES',						'roles');
define ('_SYSTEM_ICON_PROFILES',					'profiles');
define ('_SYSTEM_ICON_USERS',						'users');
define ('_SYSTEM_ICON_HOMEPAGE',					'homepage');

define ('_SYSTEM_TAB_GROUPLIST',					'grouplist');
define ('_SYSTEM_TAB_USERLIST',						'userlist');
define ('_SYSTEM_TAB_USERADD',						'useradd');
define ('_SYSTEM_TAB_USERATTACH',					'userattach');
define ('_SYSTEM_TAB_GROUPATTACH',					'groupattach');
define ('_SYSTEM_TAB_USERMOVE',						'usermove');
define ('_SYSTEM_TAB_RULELIST',						'rulelist');
define ('_SYSTEM_TAB_RULEADD',						'ruleadd');
define ('_SYSTEM_TAB_USERIMPORT',					'userimport');

define ('_SYSTEM_TAB_ROLEMANAGEMENT',				'rolemanagement');
define ('_SYSTEM_TAB_ROLEASSIGNMENT',				'roleassignment');
define ('_SYSTEM_TAB_MULTIPLEROLEASSIGNMENT',		'multipleroleassignment');

define ('_SYSTEM_TAB_PROFILEMANAGEMENT',			'profilemanagement');
define ('_SYSTEM_TAB_PROFILEADD',					'profileadd');
define ('_SYSTEM_TAB_PROFILEASSIGNMENT',			'profileassignment');

define ('_SYSTEM_TAB_MESSAGEINBOX',					'messageinbox');
define ('_SYSTEM_TAB_MESSAGEOUTBOX',				'messageoutbox');


$system_zip_unauthorized =							array("^[a-z0-9]+[-_]*[a-z0-9]*(\.(zip|rar|gz|tar|sql|bak))$");
$system_zip_unauthorizedpath =						array("data/");

define ('_SYSTEM_ACTION_INSTALLMODULE',		1);
define ('_SYSTEM_ACTION_UNINSTALLMODULE',	2);
define ('_SYSTEM_ACTION_UPDATEMODULE',		1);
define ('_SYSTEM_ACTION_USEMODULE',			4);
define ('_SYSTEM_ACTION_CONFIGUREMODULE',	5);
define ('_SYSTEM_ACTION_MODIFYHOMEPAGE',	6);
define ('_SYSTEM_ACTION_INSTALLSKIN',		7);
define ('_SYSTEM_ACTION_UNINSTALLSKIN',		8);
define ('_SYSTEM_ACTION_CREATEGROUP',		9);
define ('_SYSTEM_ACTION_MODIFYGROUP',		10);
define ('_SYSTEM_ACTION_DELETEGROUP',		11);
define ('_SYSTEM_ACTION_CLONEGROUP',		12);
define ('_SYSTEM_ACTION_CREATEROLE',		13);
define ('_SYSTEM_ACTION_MODIFYROLE',		14);
define ('_SYSTEM_ACTION_DELETEROLE',		15);
define ('_SYSTEM_ACTION_CREATEPROFIL',		16);
define ('_SYSTEM_ACTION_MODIFYPROFIL',		17);
define ('_SYSTEM_ACTION_DELETEPROFIL',		18);
define ('_SYSTEM_ACTION_CREATEUSER',		19);
define ('_SYSTEM_ACTION_MODIFYUSER',		20);
define ('_SYSTEM_ACTION_DELETEUSER',		21);
define ('_SYSTEM_ACTION_UNLINKMODULE',		22);
define ('_SYSTEM_ACTION_DELETEMODULE',		23);
define ('_SYSTEM_ACTION_UPDATEMETABASE',	24);
define ('_SYSTEM_ACTION_MOVEUSER',			27);
define ('_SYSTEM_ACTION_ATTACHUSER',		28);
define ('_SYSTEM_ACTION_DETACHUSER',		29);
define ('_SYSTEM_ACTION_ATTACHGROUP',		30);
define ('_SYSTEM_ACTION_DETACHGROUP',		31);
define ('_SYSTEM_ACTION_PARAMMODULE',		32);
define ('_SYSTEM_ACTION_ADDACTION',			33);
define ('_SYSTEM_ACTION_MODIFYACTION',		34);
define ('_SYSTEM_ACTION_DELETEACTION',		35);
define ('_SYSTEM_ACTION_ADDPERS',			36);
define ('_SYSTEM_ACTION_MODIFYPERS',		37);
define ('_SYSTEM_ACTION_DELETEPERS',		38);
define ('_SYSTEM_ACTION_ADDENT',			39);
define ('_SYSTEM_ACTION_MODIFYENT',			40);
define ('_SYSTEM_ACTION_DELETEENT',			41);
define ('_SYSTEM_ACTION_EXPORT',			42);

define ('_DIMS_ACTION_MAIN',				0);
define ('_DIMS_ACTION_ETAP',				1);

define ('_DIMS_NEWSLETTER_DESCRIPTION',		1);
define ('_DIMS_NEWSLETTER_INSCR',			2);
define ('_DIMS_NEWSLETTER_NEWSLETTER',	   3);

require_once(DIMS_APP_PATH . "/modules/system/include/functions.php");
?>
