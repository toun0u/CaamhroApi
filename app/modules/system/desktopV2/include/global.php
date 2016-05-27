<?php
$db = dims::getInstance()->getDb();

require_once DIMS_APP_PATH."/modules/system/desktopV2/include/class_desktopv2.php";
require_once DIMS_APP_PATH."/modules/system/class_tiers.php";
require_once DIMS_APP_PATH."/modules/system/class_action.php";
require_once DIMS_APP_PATH."/modules/system/class_tag.php";
require_once DIMS_APP_PATH."/modules/system/desktopV2/include/functions.php";
require_once DIMS_APP_PATH.'modules/system/class_favorite.php';
require_once DIMS_APP_PATH.'modules/system/class_ct_group.php';
require_once DIMS_APP_PATH."/modules/system/case/class_case.php";
require_once DIMS_APP_PATH."/modules/system/suivi/class_suivi.php";
require_once DIMS_APP_PATH."/modules/system/suivi/class_suividetail.php";

require_once DIMS_APP_PATH.'modules/system/desktopV2/include/class_const_desktopv2.php';

require_once DIMS_APP_PATH."modules/system/class_selection_categ.php";
require_once DIMS_APP_PATH."modules/system/class_selection.php";

define('_DESKTOP_V2_DESKTOP',		1);
define('_DESKTOP_V2_CONCEPTS',		2);

define('_DESKTOP_V2_LIMIT_CONNEXION',		3);
define('_DESKTOP_V2_LIMIT_COMPANIES',		5);

define('_DESKTOP_V2_LIMIT_ACTIVITIES',	5);
define('_DESKTOP_V2_LIMIT_OPPORTUNITIES',	5);
define('_DESKTOP_V2_LIMIT_TAGS',			30);

define ('_ACTIVITY_AVATAR_MAX_SIZE',		2 * 1024 * 1024);									// avatar max filesize (1Mo)
define ('_ACTIVITY_AVATAR_MAX_WIDTH',	60);												// avatar max width (px)
define ('_ACTIVITY_AVATAR_MAX_HEIGHT',	60);												// avatar max height (px)
define ('_ACTIVITY_AVATAR_WEB_PATH',		'/data/activities/avatars/');					// avatar web path
define ('_ACTIVITY_AVATAR_FILE_PATH',	realpath('.')._ACTIVITY_AVATAR_WEB_PATH);		// avatar file path

define ('_OPPORTUNITY_AVATAR_MAX_SIZE',		2 * 1024 * 1024);									// avatar max filesize (1Mo)
define ('_OPPORTUNITY_AVATAR_MAX_WIDTH',	60);												// avatar max width (px)
define ('_OPPORTUNITY_AVATAR_MAX_HEIGHT',	60);												// avatar max height (px)
define ('_OPPORTUNITY_AVATAR_WEB_PATH',		'/data/opportunities/avatars/');					// avatar web path
define ('_OPPORTUNITY_AVATAR_FILE_PATH',	realpath('.')._OPPORTUNITY_AVATAR_WEB_PATH);		// avatar file path

define ('_DESKTOP_V2_ADDRESS_BOOK_ALL_CONTACT',		-1);
define ('_DESKTOP_V2_ADDRESS_BOOK_LAST_LINKED',		-2);
define ('_DESKTOP_V2_ADDRESS_BOOK_FAVORITES',		-3);
define ('_DESKTOP_V2_ADDRESS_BOOK_MONITORED',		-4);


define ('_TIER_DONT_LINK', 	1);
define ('_TIER_LINK', 		2);

?>
