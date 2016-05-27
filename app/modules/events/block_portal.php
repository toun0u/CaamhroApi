<?php
/*
 * Created on 7 août 2007
 *
 * Dims Version 3.0.x - See the ./include/config.php file for the full version number.
 * This program is provided WITHOUT warranty under the GNU/GPL license.
 * See the LICENSE file for more information about the GNU/GPL license.
 * Contributors are listed in the CREDITS and CHANGELOG files in this package.
 * Copyright (C) 2000 - 2009, SARL Netlor, http://www.netlor.fr/
 * Do NOT edit or remove this copyright or licence information upon redistribution.
 *
 */
dims_init_module('system');
require_once(DIMS_APP_PATH . "/modules/system/include/business.php");
$moduleid=dims_load_securvalue('moduleid',dims_const::_DIMS_NUM_INPUT,true,false,false);
$workspaces = dims_viewworkspaces(dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true));

switch($dims_op) {
	case 'title':
		$moduleid=dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true);
		switch($idobject) {
			case dims_const::_SYSTEM_OBJECT_ACTION:
				require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
				$obj=new action();
				$obj->open($idrecord);
				$label=$obj->fields['libelle'];
				break;

		}
		break;
	case 'content':
		// on affiche les propriétés par défaut de l'objet
		$moduleid=dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true);
		switch($idobject) {
			case dims_const::_SYSTEM_OBJECT_ACTION:
				require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
				$obj=new action();
				$obj->open($idrecord);
				$label=$obj->fields['libelle'];
				break;
		}
		echo dims_getContent($moduleid,$idobject,$idrecord,$obj,$label);
		break;
	case 'searchfavorites':
	case 'search':
	case 'newsearch':
	case 'searchnews':
		require_once(DIMS_APP_PATH . '/modules/events/block_portal_search.php');
		break;

	default:
		//require_once(DIMS_APP_PATH . '/modules/agenda/block_portal_search.php');
		break;
}
?>
