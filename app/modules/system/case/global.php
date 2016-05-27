<?php
define('_OP_VIEW_CASE',				1);
define('_OP_EDIT_CASE',				2);
define('_OP_CREATE_CASE',			3);
define('_OP_SAVE_CASE',				4);
define('_OP_ADD_FILE_CASE',			5);
define('_OP_DELETE_CASE',			6);

require_once DIMS_APP_PATH.'/modules/system/case/view/view_case_list.php';
require_once DIMS_APP_PATH.'/modules/system/case/view/view_case_edit.php';
require_once DIMS_APP_PATH.'/modules/system/case/view/view_case.php';

require_once DIMS_APP_PATH.'/modules/system/case/controller_case.php';
require_once DIMS_APP_PATH.'/modules/system/case/class_case.php';
?>
<script type="text/javascript" src="modules/system/case/functions.js"></script>