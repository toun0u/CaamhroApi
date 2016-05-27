<?
/*
 *		Copyright 2000-2009  Netlor Concept <contact@netlor.fr>
 *
 *		This program is free software; you can redistribute it and/or modify
 *		it under the terms of the GNU General Public License as published by
 *		the Free Software Foundation; either version 2 of the License, or
 *		(at your option) any later version.
 *
 *		This program is distributed in the hope that it will be useful,
 *		but WITHOUT ANY WARRANTY; without even the implied warranty of
 *		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *		GNU General Public License for more details.
 *
 *		You should have received a copy of the GNU General Public License
 *		along with this program; if not, write to the Free Software
 *		Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

// par defaut on prend le module WCE sur lequel on est deja 
if (isset($_SESSION['dims']['moduletype']) && $_SESSION['dims']['moduletype'] == 'wce') {
	$wce_idm = $_SESSION['dims']['moduleid'];
}
else {
	// sinon on va chercher le 1er dispo dans les modules accessibles depuis l'espace de travail courant.
	$wce_idm = 0;
	foreach($_SESSION['dims']['currentworkspace']['modules'] as $idm) {
		if (isset($_SESSION['dims']['modules'][$idm]['active']) && $_SESSION['dims']['modules'][$idm]['active'] && $_SESSION['dims']['modules'][$idm]['label'] == 'WCE') $wce_idm = $idm;
	}
}

if ($wce_idm) {
	dims_init_module('wce');

	require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_article.php';
	require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_heading.php';

	$headings = wce_getheadings($wce_idm);
	$articles = wce_getarticles($wce_idm);

	switch($dims_op) {
		case 'wce_detail_heading';
			echo wce_build_tree($headings, $articles, dims_load_securvalue('hid', dims_const::_DIMS_NUM_INPUT, true, true, true), dims_load_securvalue('str', dims_const::_DIMS_CHAR_INPUT, true, true, true), 2, (isset($_GET['option'])) ? $_GET['option'] : '');
			die();
		break;

		case 'wce_selectlink':
			?>
			<script type="text/javascript">
			function wce_showheading(hid,str) {
				elt = document.getElementById(hid+'_plus');
				if (elt.innerHTML.indexOf('plusbottom') != -1) elt.innerHTML = elt.innerHTML.replace('plusbottom', 'minusbottom');
				else  if (elt.innerHTML.indexOf('minusbottom')	!= -1) elt.innerHTML = elt.innerHTML.replace('minusbottom', 'plusbottom');
				else  if (elt.innerHTML.indexOf('plus')  != -1) elt.innerHTML = elt.innerHTML.replace('plus', 'minus');
				else  if (elt.innerHTML.indexOf('minus')  != -1) elt.innerHTML = elt.innerHTML.replace('minus', 'plus');

				if (elt = document.getElementById(hid)) {
					if (elt.style.display == 'none') {
						if (elt.innerHTML.length < 20) dims_xmlhttprequest_todiv('<? echo $scriptenv; ?>','dims_op=wce_detail_heading&hid='+hid+'&str='+str,'',hid);
						document.getElementById(hid).style.display='block';
					}
					else {
						document.getElementById(hid).style.display='none';
					}
				}
			}
			</script>
			<?
			echo wce_build_tree($headings, $articles, 0, '', 1, 'selectlink');
		break;
	}
}
else echo "Aucun module WCE disponible";
?>
