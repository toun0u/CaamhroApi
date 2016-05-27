<?
/*
 *      Copyright 2000-2009  Netlor Concept <contact@netlor.fr>
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

dims_init_module('wce');

require_once DIMS_APP_PATH.'modules/wce/include/classes/class_article.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_heading.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_block.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_block_model.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_wce_site.php';
require_once DIMS_APP_PATH.'modules/wce/include/global.php';
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_module_wce.php';
if (empty($op)) {
	if ($_SESSION['dims']['currentworkspace']['web'] == 1) {
		$op = 'wce';
	}
	else {
		$op = 'wiki';
	}
}

$wce_site = new wce_site($db);
$wce_site->loadBlockModels();

switch($op) {
	case 'wce':
		include DIMS_APP_PATH."modules/wce/wce/admin.php";
		break;
	case 'wiki':
		if(defined('_DISPLAY_WIKI') && _DISPLAY_WIKI)
			include DIMS_APP_PATH."modules/wce/wiki/wiki.php";
		else
			dims_redirect('/admin.php?op=wce');
		break;
}
