<link type="text/css" rel="stylesheet" href="./common/modules/system/desktopV2/templates/planning/include/styles.css" media="screen" />
<link type="text/css" rel="stylesheet" href="./common/modules/system/desktopV2/templates/planning/include/planning.css" media="screen" />

<?php
require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
require_once(DIMS_APP_PATH . '/modules/system/class_action_detail.php');

switch($op) {
	default:
		// ob_start();
		include DIMS_APP_PATH.'modules/system/desktopV2/templates/planning/public_planning_display.php';
		// ob_clean();
		break;
	case 'xml_planning':
		ob_end_clean();
		include DIMS_APP_PATH.'modules/system/desktopV2/templates/planning/xml_planning.php';
		// include DIMS_APP_PATH.'modules/system/desktopV2/templates/planning/xml_planning_new.php';
		die();
		break;
	// case 'xml_refresh_planning':
	//	   ob_end_clean();
	//	   $datestart=dims_load_securvalue('start',dims_const::_DIMS_CHAR_INPUT,true,true);
	//	   //echo $datestart;
	//	   include DIMS_APP_PATH.'modules/system/desktopV2/templates/planning/xml_planning_refresh.php';
	//	   die();
	//	   break;
	case 'planning_scroll':
		ob_end_clean();
		$_SESSION['dims']['planning_scroll'] = 8 * 42 + 1;//8h sur le planning
		$scroll = dims_load_securvalue('value',dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['dims']['planning_scroll']);
		die();
		break;
}
?>
