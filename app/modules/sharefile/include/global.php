<?php
define ('_SHAREFILE_ACTION_MANAGE',		1);
define ('_SHAREFILE_OBJECT_SHARE',		1);

global $_DIMS;

require_once(DIMS_APP_PATH . '/modules/sharefile/include/classes/class_sharefile_history.php');
require_once(DIMS_APP_PATH . '/modules/sharefile/include/classes/class_sharefile_share.php');
require_once(DIMS_APP_PATH . '/modules/sharefile/include/classes/class_sharefile_param.php');
require_once(DIMS_APP_PATH . '/modules/sharefile/include/classes/class_sharefile_file.php');
require_once(DIMS_APP_PATH . '/modules/sharefile/include/classes/class_sharefile_user.php');
require_once(DIMS_APP_PATH . "/modules/sharefile/include/classes/class_sharefile_contact.php");
require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
