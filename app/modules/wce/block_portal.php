<?php
/*
 * Created on 7 ao�t 2007
 *
 * Dims Version 3.0.x - See the ./include/config.php file for the full version number.
 * This program is provided WITHOUT warranty under the GNU/GPL license.
 * See the LICENSE file for more information about the GNU/GPL license.
 * Contributors are listed in the CREDITS and CHANGELOG files in this package.
 * Copyright (C) 2000 - 2009, SARL Netlor, http://www.netlor.fr/
 * Do NOT edit or remove this copyright or licence information upon redistribution.
 *
 */

dims_init_module('wce');

$workspaces = dims_viewworkspaces(dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true));
switch($dims_op)
{
	case 'content':
		// on affiche les propri�t�s par d�faut de l'objet
		$moduleid=$_GET['moduleid'];

		if($idobject==_WCE_OBJECT_ARTICLE) {
			require_once(DIMS_APP_PATH . '/modules/wce/include/classes/class_article.php');
			$obj=new wce_article();
			$obj->open($idrecord);
			$label=$obj->fields['title'];
		}
		else {
			require_once(DIMS_APP_PATH . '/modules/wce/include/classes/class_article_heading.php');
			$obj=new article_heading();
			$obj->open($idrecord);
			$label=$obj->fields['label'];
		}

		echo dims_getContent($moduleid,$idobject,$idrecord,$obj,$label);

		break;
	case 'searchnews':
	default:
		if (isset($_GET['moduleid'])) $moduleid= dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true);
		require_once(DIMS_APP_PATH . '/modules/wce/block_portal_search.php');
		break;
}

?>

