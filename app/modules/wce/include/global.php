<?php
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

require_once DIMS_APP_PATH."modules/wce/include/classes/class_module_wce.php";
//require_once DIMS_APP_PATH."modules/wce/include/classes/class_wce_site.php";
//require_once DIMS_APP_PATH."modules/system/class_lang.php";

define ("_WCE_ACTION_ARTICLE_EDIT",			1);
define ("_WCE_ACTION_ARTICLE_PUBLISH",		2);
define ("_WCE_ACTION_CATEGORY_EDIT",		3);
define ("_WCE_ACTION_WORKFLOW_MANAGE",		4);
define ("_WCE_ACTION_ARTICLE_PREPUBLISH",	5);

define ('_WCE_TEMPLATES_PATH',	'./common/templates/frontoffice');

define ("_WCE_OBJECT_ARTICLE",				1);
define ("_WCE_OBJECT_HEADING",				2);

define ("_WCE_MENU_PREVIEW",					1);
define ("_WCE_MENU_PROPERTIES",					2);
define ("_WCE_MENU_REFERENCEMENT",				3);
define ("_WCE_MENU_LIST_DIFFUSION",				4);
define ("_WCE_MENU_LISTARTICLE",				5);

define ('WCE_BREVE',					1);
define ('WCE_ARTICLE',					2);
define ('WCE_RSS',						3);
define ('WCE_MAILINGLIST',				4);
define ('WCE_MAILINGLIST_SIGN',			5);
define ('WCE_ALL_BREVE',				6);
define ('WCE_ALERTE',					7);

// types des wce_object
define ('WCE_OBJECT_TYPE_NEWS',			1);
define ('WCE_OBJECT_TYPE_UNE',			2);
define ('WCE_OBJECT_TYPE_NEWSLETTER',	3);
define ('WCE_OBJECT_TYPE_SONDAGE',		4);
define ('WCE_OBJECT_TYPE_ALL_BREVES',	5);
define ('WCE_OBJECT_TYPE_ALERTES',		6);

// tab RSS
define ('WCE_RSS_ALL',			1);
define ('WCE_RSS_SELECTED',		2);

//mode de visualisation des articles_objects en fonction des rss
define ('_WCE_OBJECT_VIEW_FRONT',			1);//on affiche tout, y compris les rss
define ('_WCE_OBJECT_VIEW_NO_RSS',			2);//que les brèves classiques
define ('_WCE_OBJECT_VIEW_ONLY_RSS',		3);//que les brèves générées sur la base des RSS

global $article_status;

$article_status = array(	'edit' => 'Modifiable',
							'wait' => 'A Valider'
						);

global $_DIMS;
global $months;
$months = array();

$months['-1']['label']	  = $_DIMS['cste']['_DIMS_ALLS'];//'Tous les mois';
$months['-1']['small']	  = $_DIMS['cste']['_DIMS_ALLS'];//'Tous les mois';
$months['01']['label']	  = $_DIMS['cste']['_JANUARY'];
$months['01']['small']	  = $_DIMS['cste']['_JANUARY_SHORT'];
$months['02']['label']	  = $_DIMS['cste']['_FEBRUARY'];
$months['02']['small']	  = $_DIMS['cste']['_FEBRUARY_SHORT'];
$months['03']['label']	  = $_DIMS['cste']['_MARCH'];
$months['03']['small']	  = $_DIMS['cste']['_MARCH_SHORT'];
$months['04']['label']	  = $_DIMS['cste']['_APRIL'];
$months['04']['small']	  = $_DIMS['cste']['_APRIL_SHORT'];
$months['05']['label']	  = $_DIMS['cste']['_MAY'];
$months['05']['small']	  = $_DIMS['cste']['_MAY_SHORT'];
$months['06']['label']	  = $_DIMS['cste']['_JUNE'];
$months['06']['small']	  = $_DIMS['cste']['_JUNE_SHORT'];
$months['07']['label']	  = $_DIMS['cste']['_JULY'];
$months['07']['small']	  = $_DIMS['cste']['_JULY_SHORT'];
$months['08']['label']	  = $_DIMS['cste']['_AUGUST'];
$months['08']['small']	  = $_DIMS['cste']['_AUGUST_SHORT'];
$months['09']['label']	  = $_DIMS['cste']['_SEPTEMBER'];
$months['09']['small']	  = $_DIMS['cste']['_SEPTEMBER_SHORT'];
$months['10']['label']	  = $_DIMS['cste']['_OCTOBER'];
$months['10']['small']	  = $_DIMS['cste']['_OCTOBER_SHORT'];
$months['11']['label']	  = $_DIMS['cste']['_NOVEMBER'];
$months['11']['small']	  = $_DIMS['cste']['_NOVEMBER_SHORT'];
$months['12']['label']	  = $_DIMS['cste']['_DECEMBER'];
$months['12']['small']	  = $_DIMS['cste']['_DECEMBER_SHORT'];

function wce_getlastupdate($moduleid = -1) {
	$db = dims::getInstance()->getDb();

	if ($moduleid == -1 && isset($_SESSION['dims']['moduleid']) ) $moduleid = $_SESSION['dims']['moduleid'];
	if (isset($moduleid) && is_numeric($moduleid)) {
		$select =	"
					SELECT		MAX(lastupdate_timestp) as maxtimestp
					FROM		dims_mod_wce_article a
					WHERE		a.id_module = :id_module";

		$res=$db->query($select,array(':id_module'=>array('value'=>$moduleid,'type'=>PDO::PARAM_INT)));

		if ($row = $db->fetchrow($res)) return($row['maxtimestp']);
		else return(0);
	}
	else return(0);
}

function wce_getheadings($moduleid = -1) {
	$db = dims::getInstance()->getDb();

	if ($moduleid == -1)
		if (isset($_SESSION['dims']['moduleid']) )
			$moduleid = $_SESSION['dims']['moduleid'];
		else{
			$moduleid = current(dims::getInstance()->getWceModules());
			$_SESSION['dims']['moduleid']=$moduleid;
		}
	elseif(!isset($_SESSION['dims']['moduleid']))
		$_SESSION['dims']['moduleid']=$moduleid;

	$headings = array('list' => array(), 'tree' => array());

	if(!isset($_SESSION['dims']['wce_default_lg'])){
		if (!isset($site)) {
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_wce_site.php";
			$site = new wce_site(dims::getInstance()->getDb(),$moduleid);
			$_SESSION['dims']['wce_default_lg'] = $site->getDefaultLanguage();
		}
	}

	if (!isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])) $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']=$_SESSION['dims']['wce_default_lg'];

	if (isset($moduleid) && is_numeric($moduleid)) {
		/*$select = "	SELECT		x.*
					FROM		(SELECT distinct h.*, a.urlrewrite as article_urlrewrite
								FROM		(SELECT		h.*
											FROM		dims_mod_wce_heading as h
											WHERE		h.id_module = {$moduleid}
											AND			h.id_lang IN (".$_SESSION['dims']['wce_default_lg'].",".$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'].", null)
											OR			type=1
											ORDER BY	h.id_lang ".(($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC')."
											) as h
								LEFT JOIN	(SELECT		a.*
											FROM		dims_mod_wce_article as a
											WHERE		a.id_lang IN (0,".$_SESSION['dims']['wce_default_lg'].",".$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'].", null)
											AND			a.id_heading>0
											ORDER BY	a.id_lang ".(($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC')."
											) as a
								ON			(a.id=h.linkedpage OR (h.linkedpage='' AND a.position=1 ))
								AND			a.id_heading=h.id
								GROUP BY	h.id

								) as x
					GROUP BY	x.id
					ORDER BY	x.type, x.depth, x.position";*/


		$select = "	SELECT		DISTINCT h.*, a.urlrewrite as article_urlrewrite
					FROM		dims_mod_wce_heading as h
					LEFT JOIN	dims_mod_wce_article as a
					ON			a.id_lang IN (0,:id_lang1,:id_lang2, null)
					AND			a.id_heading>0
					AND			(a.id=h.linkedpage OR (h.linkedpage='' AND a.position=1 ))
					AND			a.id_heading=h.id
					WHERE		h.id_module = :id_module
					AND			h.id_lang IN (:id_lang3,:id_lang4, null)
					OR			h.type=1
					ORDER BY	h.type, h.depth, h.position,
								h.id_lang ".(($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC').",
								a.id_lang ".(($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC')."";
		$params = array(':id_lang1'=>array('value'=>$_SESSION['dims']['wce_default_lg'],'type'=>PDO::PARAM_INT),
						':id_lang2'=>array('value'=>$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'],'type'=>PDO::PARAM_INT),
						':id_module'=>array('value'=>$moduleid,'type'=>PDO::PARAM_INT),
						':id_lang3'=>array('value'=>$_SESSION['dims']['wce_default_lg'],'type'=>PDO::PARAM_INT),
						':id_lang4'=>array('value'=>$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'],'type'=>PDO::PARAM_INT));

		$headingExists=array();
		$result = $db->query($select,$params);
		while ($fields = $db->fetchrow($result)) {
			if (!isset($headingExists[$fields['id']])) {
				$headingExists[$fields['id']]=$fields['id'];// on positionne pour ne pas ecraser si on a deja une langue etrangere
				if (isset($headings['list'][$fields['id_heading']]) || $fields['id_heading']==0) {
					$fields['headingrewrite']="";
					// construction du heading rewriting
					if (isset($headings['list'][$fields['id_heading']]['headingrewrite'])) {
						if ($headings['list'][$fields['id_heading']]['headingrewrite']!="")
							$fields['headingrewrite']=$headings['list'][$fields['id_heading']]['headingrewrite'];
					}

					if ($fields['urlrewrite']!= "") {
						if ($fields['headingrewrite']!="") $fields['headingrewrite'].=_DIMS_SEP_URLREWRITE;
						$fields['headingrewrite'].=$fields['urlrewrite'];
					}

					$headings['list'][$fields['id']] = $fields;
					$headings['tree'][$fields['id_heading']][] = $fields['id'];

					if (!empty($headings['list'][$fields['id']]['parents'])) {
						$id=strpos($headings['list'][$fields['id']]['parents'],';');
						$headings['list'][$fields['id']]['parents']=substr($headings['list'][$fields['id']]['parents'],$id+1);
						$headings['list'][$fields['id']]['parents'].= ';'.$fields['id'];
						$headings['list'][$fields['id']]['nav'] = str_replace(';','-',$headings['list'][$fields['id']]['parents']);
					}
					else {

						//$parents[] = $fields['id'];
						//$headings['list'][$fields['id']]['nav'] = implode('-',$parents);
					}
					/*
					$parents = explode(';',$headings['list'][$fields['id']]['parents']);
					if (isset($parents[0])) unset($parents[0]);
					$parents[] = $fields['id'];
					$headings['list'][$fields['id']]['nav'] = implode('-',$parents);
					*/

					// nouveau test pour etre
					if ($headings['list'][$fields['id']]['template']!='' &&  !file_exists(DIMS_APP_PATH."templates/frontoffice/".$headings['list'][$fields['id']]['template'])) {
						require_once DIMS_APP_PATH.'modules/wce/include/classes/class_heading.php';
						$headings['list'][$fields['id']]['template']='';
						$head = new wce_heading();
						$head->open($fields['id']);
						$head->fields['template']='';
						$head->save();

					}

					if ($headings['list'][$fields['id']]['template'] == '' && isset($headings['list'][$fields['id_heading']]) && $headings['list'][$fields['id_heading']]['template'] != '') {
						$headings['list'][$fields['id']]['template'] = $headings['list'][$fields['id_heading']]['template'];
						$headings['list'][$fields['id']]['herited_template'] = 1;
					}

					// update fckeditor
					if ($headings['list'][$fields['id']]['fckeditor'] == '' && isset($headings['list'][$fields['id_heading']]) && $headings['list'][$fields['id_heading']]['fckeditor'] != '') {
						$headings['list'][$fields['id']]['fckeditor'] = $headings['list'][$fields['id_heading']]['fckeditor'];
						$headings['list'][$fields['id']]['herited_fckeditor'] = 1;
					}


				}
			}
		}
		// desalloue
		unset($headingExists);
	}
	return($headings);
}

function wce_getarticles($moduleid = -1) {
	$db = dims::getInstance()->getDb();

	if ($moduleid == -1)
		if(isset($_SESSION['dims']['moduleid']))
			$moduleid = $_SESSION['dims']['moduleid'];
		else
			$moduleid = current(dims::getInstance()->getWceModules());

	$bloc_articles = array();

	$select =	"SELECT			ab.*
				FROM			dims_mod_wce_article_block as ab
				WHERE			ab.id_module = :id_module
				ORDER BY		ab.id_article,ab.position";

	$resultat = $db->query($select,array(':id_module'=>array('value'=>$moduleid,'type'=>PDO::PARAM_INT)));
	while ($f = $db->fetchrow($resultat)) {
		if (!isset($bloc_articles[$f['id_article']])) {
			$bloc_articles[$f['id_article']]=0;
		}

		for ($i=1;$i<=9;$i++) {
			if (strcmp($f['draftcontent'.$i],$f['content'.$i])!=0) {
				$result=1;
				$bloc_articles[$f['id_article']]=1;
			}
		}
	}

	$articles = array();

	if (isset($moduleid) && is_numeric($moduleid)) {
		$select = "	SELECT		x.*
					FROM		(SELECT		*
								FROM		".wce_article::TABLE_NAME."
								WHERE		id_heading > 0
								AND			type = 0
								AND			id_module = :id_module
								AND			id_lang IN (:id_lang1,:id_lang2)
								ORDER BY	id_lang ".(($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC')."
								) as x
					GROUP BY	x.id
					ORDER BY	x.position";
		$params = array(':id_module'=>array('value'=>$moduleid,'type'=>PDO::PARAM_INT),
						':id_lang1'=>array('value'=>$_SESSION['dims']['wce_default_lg'],'type'=>PDO::PARAM_INT),
						':id_lang2'=>array('value'=>$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'],'type'=>PDO::PARAM_INT));
		$resultat = $db->query($select,$params);
		while ($fields = $db->fetchrow($resultat)) {
			$result=0;
			if ($fields['model']=='') {
				for ($i=1;$i<=9;$i++) {
					if (strcmp($fields['draftcontent'.$i],$fields['content'.$i])!=0) {
						$result=1;
					}
				}
			}
			else {
				if (isset($bloc_articles[$fields['id']])) {
					$result=$bloc_articles[$fields['id']];
				}
			}
			$fields['new_version']=$result;
			$articles['list'][$fields['id']] = $fields;
			$articles['tree'][$fields['id_heading']][] = $fields['id'];

		}
	}
	return($articles);
}

function wce_setheadingid($headings) {
	//global $headingid;

	//if (!isset($headingid)) // look if isset headingid, ifnot look for a valid headingid
	//{
		if (!isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['headingid'])) {
			$_SESSION['wce'][$_SESSION['dims']['moduleid']]['headingid'] = $headings['tree'][0][0];
		}
		$headingid = $_SESSION['wce'][$_SESSION['dims']['moduleid']]['headingid'];
	//}

	if (!isset($headings['list'][$headingid])) // heading is not allowed here !
	{
		$headings['tree'][0][0];
		$headingid = $_SESSION['wce'][$_SESSION['dims']['moduleid']]['headingid'];
	}

	return($headingid);
}

/**
* build recursively the whole heading tree
*
*/
// old version $headings, $articles, $fromhid = 0, $str = '', $depth = 1, $option = '',$alldisplay=false)
function wce_build_tree($headings, $articles, $fromhid = 0, $str = '', $depth = 1, $option = '', $headingid=0,$articleid=0,$selectedheading=array(),$selectheading=array(),$alldisplay=false, $default_inputid= 'wce_article_id_article_link', $linkedpage_displayed= 'linkedpage_displayed') {
	global $scriptenv;
	$html = '';

	if ($alldisplay && $fromhid==0) {
		$html= '<div style="float:right;width:24px;">Map</div><div style="float:right;width:20px;">Vis.</div><div style="clear:both;"></div>';
	}
	if (empty($selectheading)) $selectheading=array();
	if (empty($selectedheading)) $selectedheading=array();

	switch($option) {
		// used for fckeditor and link redirect on heading
		case 'selectredirect':
		case 'selectlink':
		case 'selectlinkarticle':
			if (isset($headings['tree'][0][0]) && isset($headings['list'][$headings['tree'][0][0]]))
				$headingsel = $headings['list'][$headings['tree'][0][0]];
			else $headingsel=0;
		break;
		default:
			if (isset($headings['list'][$headingid])) $headingsel = $headings['list'][$headingid];
			else $headingsel=0;
		break;
	}

	if (!isset($selectheading) || empty($selectheading)) $selectheading=array();
		if (!isset($selectedheading) || empty($selectedheading)) $selectedheading=array();

	if (isset($headings['tree'][$fromhid])) {
		$c=0;
		foreach($headings['tree'][$fromhid] as $hid) {
			if (!in_array($hid,$selectheading)) {
				$heading = $headings['list'][$hid];
				$isheadingsel = ($headingid == $hid && $option == '');

				$hselparents = explode(';',$headingsel['parents']);
				$testparents = explode(';',$heading['parents']);
				//$testparents[] = $heading['id'];

				// heading opened if parents array intersects
				$hasarticles = !empty($articles['tree'][$hid]);
				$isheadingopened = sizeof(array_intersect ($hselparents, $testparents)) == sizeof($testparents);
				// last node or not ?
				$islast = ((!isset($headings['tree'][$fromhid]) || $c == sizeof($headings['tree'][$fromhid])-1) && empty($articles['tree'][$fromhid]));

				$decalage = str_replace("(b)", "<img src=\"./common/modules/wce/img/empty.png\" />", $str);
				$decalage = str_replace("(s)", "<img src=\"./common/modules/wce/img/line.png\" />", $decalage);
				$style_sel = ($isheadingsel) ? 'bold' : 'none';

				if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['authheadings'][$heading['id']]) || dims_isadmin() || dims_isactionallowed(0)) {
					$icon = 'dossier16';
				}
				else {
					$icon = 'dossier16_disabled';
				}

				$new_str = ''; // decalage pour les noeuds suivants
				if ($depth == 1 || $heading['id'] == $fromhid) $icon = 'racine16';
				else {
					if (!$islast) $new_str = $str.'(s)'; // |
					else $new_str = $str.'(b)';  // (vide)
				}
				$linkheading='';

				switch($option) {
					// used for fckeditor and link redirect on heading
					case 'selectredirectall':
						$link = "&nbsp;<a name=\"heading{$aid}\" href=\"javascript:void(0);\" onclick=\"javascript:dims_getelem('id_linkedheading').value = '{$hid}';dims_getelem('linkedheading_displayed').value = '".addslashes($heading['label'])."';dims_getelem('id_article_link').value = '';dims_getelem('".$linkedpage_displayed."').value = '';\"><img src=\"./common/modules/wce/img/publish.png\" style=\"border:0\">";
						break;
					case 'selectheading':
						$link = $link_div = "<a href=\"javascript:void(0);\" onclick=\"javascript:document.getElementById('iddestheading').value=".$hid.";document.listart.submit();\">";
						break;
					case 'selectlinkarticle':
					case 'selectredirect':
					case 'selectlink':
						$link = $link_div ="<a name=\"heading{$hid}\" onclick=\"javascript:wce_showheading('{$hid}','{$new_str}&option={$option}');\" href=\"javascript:void(0);\">";
						break;
					case 'selectredirectheading':
						$link = $link_div ="<a name=\"heading{$hid}\" onclick=\"javascript:wce_showheading('{$hid}','{$new_str}&option={$option}');\" href=\"javascript:void(0);\">";
						$linkheading = "&nbsp;<a name=\"heading{$hid}\" href=\"javascript:void(0);\" onclick=\"javascript:dims_getelem('wce_heading_linkedheading').value = '{$hid}';dims_getelem('linkedheading_displayed').value = '".addslashes($heading['label'])."';dims_hidepopup();\"><img src=\"./common/modules/wce/img/publish.png\" style=\"border:0\"></a>";
						break;
					default:
						$link_div ="<a name=\"heading{$hid}\" onclick=\"javascript:wce_showheading('{$hid}','{$new_str}');\" href=\"javascript:void(0);\">";
						$link = "<a title=\"".$heading['label']."\" style=\"font-weight:{$style_sel}\" href=\"admin.php?headingid={$heading['id']}\">";
					break;
				}

				if ($depth > 1) {
					$last = 'joinbottom';
					if ($islast) $last = 'join';

					if (isset($headings['tree'][$hid]) || $hasarticles) {
						if ($islast) $last = ($isheadingsel || $isheadingopened || $alldisplay) ? 'minus' : 'plus';
						else  $last = ($isheadingsel || $isheadingopened || $alldisplay) ? 'minusbottom' : 'plusbottom';
					}
					$decalage .= "<div style=\"float:left;\" id=\"{$heading['id']}_plus\">{$link_div}<img src=\"./common/modules/wce/img/{$last}.png\" /></a></div>";
				}

				$html_rec = '';
				if ($isheadingsel || $isheadingopened || $depth == 1 || !empty($headings['tree'][$hid]) || !empty($articles['tree'][$hid]) || $alldisplay)
					$html_rec = wce_build_tree($headings, $articles, $hid, $new_str, $depth+1, $option, $headingid,$articleid,$selectedheading,$selectheading,$alldisplay,$default_inputid, $linkedpage_displayed);

				$display = ($isheadingopened || $isheadingsel || $depth == 1 || $alldisplay) ? 'block' : 'none';
				$additionnal_linkheading='';

				if (!isset($heading['visible'])) $heading['visible']=0;
				/*if($heading['linkedheading']>0 && $option!='') {
					$link_article = "&nbsp;<a style=\"font-weight:{$style_sel}\" href=\"admin.php?op=heading_modify&headingid={$heading['linkedheading']}\">";
					$additionnal_linkheading=$link_article.'<img src="./common/modules/wce/img/shortcut.gif"></a>';
				}*/

				$blockvisible='<a href="javascript:void(0);" onclick="updateStateWce(1,0,'.$heading['id'].','.$heading['visible'].')">';
				if ($heading['visible']) {
					$blockvisible.='<img src="./common/img/bullet_sel.png">';
				}
				else {
					$blockvisible.='<img src="./common/img/bullet.png">';
				}
				$blockvisible.='</a>';

				$blockmap='<a href="javascript:void(0);" onclick="updateStateWce(1,1,'.$heading['id'].','.$heading['is_sitemap'].')"   >';
				if ($heading['is_sitemap']) {
					$blockmap.='<img src="./common/img/bullet_sel.png">';
				}
				else {
					$blockmap.='<img src="./common/img/bullet.png">';
				}
				$blockmap.='</a>';

				$blockparam='<div style="float:right;width:20px;"  id="wce_1_1_'.$heading['id'].'">'.$blockmap.'</div>';
				$blockparam.='<div style="float:right;width:20px;" id="wce_1_0_'.$heading['id'].'">'.$blockvisible.'</div>';

				if (in_array($heading['id'],$selectedheading))	{
					if ($html_rec!="") {
						$html .=	"
							<div class=\"wce_tree_node\">
								{$decalage}<img src=\"".module_wce::getTemplateWebPath("gfx/$icon.png")."\" />
								".dims_strcut($heading['label'],45-($depth*5)).$linkheading.$additionnal_linkheading;

						if ($alldisplay) {
							$html .= $blockparam;
						}

						$html.="
							</div>
							<div style=\"clear:left;display:$display;height:auto !important;\" id=\"{$heading['id']}\">$html_rec</div>
							";
					}
				}
				else {
					$html .=	"
							<div class=\"wce_tree_node\">
								{$decalage}<img style=\"margin-right:4px;\" src=\"".module_wce::getTemplateWebPath("gfx/$icon.png")."\" />
								{$link}".dims_strcut($heading['label'],45-($depth*5)).$linkheading.$additionnal_linkheading."</a>";

					if ($alldisplay) {
						$html .= $blockparam;
					}

					$html.="
							</div>
							<div style=\"clear:left;display:$display;height:auto !important;\" id=\"{$heading['id']}\">$html_rec</div>
							";
				}
				$c++;
			}
		}
	}

	// ARTICLES
	if (!empty($articles['tree'][$fromhid])) {
		$c=0;
		foreach($articles['tree'][$fromhid] as $aid) {
			$article = $articles['list'][$aid];

			$islast = ($c == sizeof($articles['tree'][$fromhid])-1);
			$isarticlesel = ($articleid == $aid);

			$decalage = str_replace("(b)", "<img src=\"./common/modules/wce/img/empty.png\" />", $str);
			$decalage = str_replace("(s)", "<img src=\"./common/modules/wce/img/line.png\" />", $decalage);
			$style_sel = 'none';

			switch($option) {
				// used for fckeditor and link redirect on heading
				case 'selectredirectall':
					$link = "<a name=\"article{$aid}\" href=\"javascript:void(0);\" onclick=\"javascript:dims_getelem('id_article_link').value = '{$aid}';dims_getelem('".$linkedpage_displayed."').value = '".addslashes($article['title'])."';dims_getelem('id_linkedheading').value = '';dims_getelem('linkedheading_displayed').value = '';\"><img src=\"./common/modules/wce/img/publish.png\" style=\"border:0\">";
					break;
				case 'selectlinkarticle':
					$link = "<a name=\"article{$aid}\" href=\"javascript:void(0);\" onclick=\"javascript:dims_getelem('".$default_inputid."').value = '{$aid}';dims_getelem('".$linkedpage_displayed."').value = '".addslashes($article['title'])."';dims_hidepopup();\">";
				break;
				case 'selectredirect':
					$link = "<a name=\"article{$aid}\" href=\"javascript:void(0);\" onclick=\"javascript:dims_getelem('wce_heading_linkedpage').value = '{$aid}';dims_getelem('".$linkedpage_displayed."').value = '".addslashes($article['title'])."';dims_hidepopup();\">";
				break;
				case 'selectredirectheading':
					$link = "";
				break;
				case 'selectlink':
					$link = "<a name=\"article{$aid}\" href=\"javascript:void(0);\" onclick=\"javascript:dims_getelem('txtArticle',parent.document).value='index.php?articleid={$aid}';\">";
				break;

				default:
					$style_sel =  ($isarticlesel) ? 'bold' : 'none';
					$link = "<a style=\"font-weight:{$style_sel}\" href=\"admin.php?headingid={$fromhid}&op=article_modify&articleid={$aid}\">";

				break;
			}

			$last = ($islast) ? 'join' : 'joinbottom';
			$decalage .= "<img src=\"./common/modules/wce/img/{$last}.png\">";

			$blockvisible='<a href="javascript:void(0);" onclick="updateStateWce(2,0,'.$article['id'].','.$article['visible'].')">';
			if ($article['visible']) {
				$blockvisible.='<img src="./common/img/bullet_sel.png">';
			}
			else {
				$blockvisible.='<img src="./common/img/bullet.png">';
			}
			$blockvisible.='</a>';

			$blockmap='<a href="javascript:void(0);" onclick="updateStateWce(2,1,'.$article['id'].','.$article['is_sitemap'].')"   >';
			if ($article['is_sitemap']) {
				$blockmap.='<img src="./common/img/bullet_sel.png">';
			}
			else {
				$blockmap.='<img src="./common/img/bullet.png">';
			}
			$blockmap.='</a>';

			$blockparam='<div style="float:right;width:20px;"  id="wce_2_1_'.$article['id'].'">'.$blockmap.'</div>';
			$blockparam.='<div style="float:right;width:20px;" id="wce_2_0_'.$article['id'].'">'.$blockvisible.'</div>';

			$status = ($article['status'] == 'wait') ? ' *' : '';
			if($article['id_article_link']>0) {
				$link_article = "<a style=\"font-weight:{$style_sel}\" href=\"admin.php?op=article_modify&articleid={$article['id_article_link']}\">";
				$html .=	"
						<div class=\"wce_tree_node\">
							{$decalage}<img src=\"./common/modules/wce/img/doc_shorcut.png\">
							{$link}".dims_strcut($article['title'],30-($depth*3)).$status."</a>
						$link_article<img src=\"./common/modules/wce/img/shortcut.gif\"></a>";

				if ($alldisplay) {
					$html .= $blockparam;
				}
				$html.="
						</div>
						";
			}
			else {
				$html .=	"
						<div class=\"wce_tree_node\">
							{$decalage}<img src=\"./common/modules/wce/img/doc{$article['new_version']}.png\">
							{$link}".dims_strcut($article['title'],30-($depth*3)).$status."</a>";

				if ($alldisplay) {
					$html .= $blockparam;
				}

				$html.="
						</div>
						";
			}
			$c++;
		}
	}

	return $html;
}

//fonction de generation des menus avec smarty
function smarty_template_assign($smarty, &$smarty_heading, &$headings, &$nav, $hid, $var = '', $link = '', $rootpath='', $web_root_path='', $adminedit='') {
	global $recursive_mode;
	global $wce_mode;
	global $scriptenv;

	if (isset($headings['tree'][$hid])) {
		if (isset($headings['list'][$hid])) {
			if ($headings['list'][$hid]['depth'] == 0) $localvar = "sw_root{$headings['list'][$hid]['position']}";
			else $localvar = "{$var}sw_heading{$headings['list'][$hid]['depth']}";
		}
		$selprec=0;
		$counttab=1;

		foreach($headings['tree'][$hid] as $id) {
			$detail = $headings['list'][$id];

			$depth = $detail['depth'] - 1;
			if ($depth == 0) {
				$localvar = "root{$detail['position']}";
				$rootpath=$localvar;
			}
			else {
				$localvar = "heading{$depth}";
			}
			$locallink = ($link!='') ? "{$link}-{$id}" : "{$id}";
			$modifheading="";
			$mouseover="";
			if ($adminedit) {
				$mouseover="javascript:activeEditHeading(event,".$id.");";
			}
			switch($wce_mode) {
				case 'edit':
					$wiki = dims_load_securvalue('op',dims_const::_DIMS_CHAR_INPUT,true,true,false);
					if ($wiki == 'wiki') {
						$script = "javascript:window.parent.document.location.href='".module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_ACTION_SHOW_ARTICLE)."&headingid=".$id."&wce_mode=edit';";
					} else {
						$script = "javascript:window.parent.document.location.href='".module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_EDIT."&headingid={$id}';";
					}
					break;

				case 'render':
					$script = $web_root_path."/index.php?wce_mode=render&moduleid={$_SESSION['dims']['moduleid']}&headingid={$id}";
					break;

				default:
				case 'display':
					if (dims::getInstance()->getScriptEnv() == 'admin.php'){
						$wiki = dims_load_securvalue('op',dims_const::_DIMS_CHAR_INPUT,true,true,false);
						if ($wiki == 'wiki') {
							$script = "javascript:window.parent.document.location.href='".module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_ACTION_SHOW_ARTICLE)."&headingid=".$id."&wce_mode=render';";
						} else {
							$script = "javascript:window.parent.document.location.href='".module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_DEF."&headingid={$id}';";
						}
					} else {
						// test if url rewrite activated
						$urlrewrite=$web_root_path;

						if ($detail["headingrewrite"]!="") $urlrewrite.="/".$detail["headingrewrite"];
						if ($detail["article_urlrewrite"]!="") $urlrewrite.="/".$detail["article_urlrewrite"];

						if ($urlrewrite!="" && $detail["article_urlrewrite"]!="") {
							$script = $urlrewrite.".html";
							if ($adminedit!='') $script.="?adminedit=1";
						} else {
							if($detail['linkedpage'] != '' && $detail['linkedpage'] > 0){
								$art = new wce_article();
								$art->open($detail['linkedpage'],$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
								if (isset($art->fields['urlrewrite']) && trim($art->fields['urlrewrite']) != '') {
									$script = $web_root_path."/".$art->fields['urlrewrite'].".html";
								} else {
									$script = $web_root_path."/index.php?articleid=".$detail['linkedpage'];
								}
							} elseif($detail['linkedheading'] != '' && $detail['linkedheading'] > 0) {
								$script = $web_root_path."/index.php?headingid=".$detail['linkedheading'];
							} else {
								$script = $web_root_path."/index.php?headingid=".$id;
							}
							if ($adminedit!='') $script.="&adminedit=1";
						}
					}
					break;
			}

			$sel = '';

			if (isset($nav[$depth]) && $nav[$depth] == $id) {
				$tpl_path = array(
					'DEPTH' => $depth,
					'LABEL' => $modifheading.$detail['label'],
					'LINK'  => $script,
				);

				$smarty->assign("path",$tpl_path);

				$tpl_headcur = array(
					'ID'                => $id,
					'TITLE'             => $modifheading.$detail['label'],
					'POSITION'          => $detail['position'],
					'DESCRIPTION'       => $detail['description'],
					'FREE1'             => $detail['free1'],
					'FREE2'             => $detail['free2'],
					'LINKEDPAGE'        => $detail['linkedpage'],
					'LINKEDHEADING'     => $detail['linkedheading'],
					'ARRAY_POSITION'    => $counttab,
					'LENGTH'            => strlen($detail['label']),
				);

				$smarty->assign("HEADING{$depth}",$tpl_headcur);

				$sel = 'selected';
				$selprec = $id;
			}

			if (($detail['visible']) || (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (isset($detail['visible_if_connected']) && $detail['visible_if_connected']))) {
				if (!empty($detail['url'])) $script = $detail['url'];
				if ($depth > 0) {
					if ($selprec>0 && $selprec!=$detail['id']) {
						// on a le suivant
						$detail['selprec']="selected";
						$selprec=0;
					} else {
						$detail['selprec']="";
					}

					$smarty_heading[$localvar][$detail['id']]=array(
						'DEPTH'             => $depth,
						'ID'                => $detail['id'],
						'LABEL'             => $modifheading.$detail['label'],
						'POSITION'          => $detail['position'],
						'DESCRIPTION'       => $detail['description'],
						'LINK'              => $script,
						'ONMOUSEOVER'       => $mouseover,
						'LINK_TARGET'       => ($detail['url_window']) ? 'target="_blank"' : '',
						'SEL'               => $sel,
						'SELPREC'           => $detail['selprec'],
						'POSX'              => $detail['posx'],
						'POSY'              => $detail['posy'],
						'COLOR'             => $detail['color'],
						'FREE1'             => $detail['free1'],
						'FREE2'             => $detail['free2'],
						'LINKEDPAGE'        => $detail['linkedpage'],
						'LINKEDHEADING'     => $detail['linkedheading'],
						'ARRAY_POSITION'    => $counttab,
						'VISIBLE'           => $detail['visible'],
						'LENGTH'            => strlen($detail['label']),
					);

					if ($sel=="selected") {
						$smarty_heading['SELECTEDHEADING']= array(
							'DEPTH'         => $depth,
							'ID'            => $detail['id'],
							'LABEL'         => $detail['label'],
							'POSITION'      => $detail['position'],
							'DESCRIPTION'   => $detail['description'],
							'LINKEDPAGE'    => $detail['linkedpage'],
							'LINKEDHEADING' => $detail['linkedheading'],
							'LINK'          => $script,
							'ONMOUSEOVER'   => $mouseover,
							'LENGTH'        => strlen($detail['label']),
						);
					}
				}
				else {
					$smarty_heading[$localvar]=array(
						'DEPTH'             => $depth,
						'ID'                => $detail['id'],
						'LABEL'             => $detail['label'],
						'POSITION'          => $detail['position'],
						'DESCRIPTION'       => $detail['description'],
						'LINK'              => $script,
						'ONMOUSEOVER'       => $mouseover,
						'LINK_TARGET'       => ($detail['url_window']) ? 'target="_blank"' : '',
						'SEL'               => $sel,
						'POSX'              => $detail['posx'],
						'POSY'              => $detail['posy'],
						'COLOR'             => $detail['color'],
						'FREE1'             => $detail['free1'],
						'FREE2'             => $detail['free2'],
						'LINKEDPAGE'        => $detail['linkedpage'],
						'LINKEDHEADING'     => $detail['linkedheading'],
						'ARRAY_POSITION'    => $counttab,
						'VISIBLE'           => $detail['visible'],
						'LENGTH'            => strlen($detail['label']),
					);
				}

				$counttab++;
				if ($depth == 0 || (isset($recursive_mode[$depth]) && $recursive_mode[$depth] == 'prof')) {
					if (isset($headings['tree'][$id])) {
						if ($depth > 0) {
							smarty_template_assign($smarty,$smarty_heading[$localvar][$detail['id']],$headings, $nav, $id, "{$localvar}.", $locallink,$rootpath,$web_root_path,$adminedit);
						}
						else {
							smarty_template_assign($smarty,$smarty_heading[$localvar],$headings, $nav, $id, "{$localvar}.", $locallink,$rootpath,$web_root_path,$adminedit);
						}
					}
				}
			}
		}

		if (isset($headings['list'][$hid])) {
			$depth = $headings['list'][$hid]['depth'];
			if ($depth > 0 && isset($nav[$depth-1]) && $nav[$depth-1] == $hid && !(isset($recursive_mode[$depth]) && $recursive_mode[$depth] == 'prof')) {
				if ($link!='' && isset($nav[$depth])) {
					$link .= "-$nav[$depth]";
				} elseif (isset($nav[$depth])) {
					$link = "$nav[$depth]";
				}

				if (isset($nav[$depth]) && isset($headings['tree'][$nav[$depth]])) {
					smarty_template_assign($smarty,$smarty_heading,$headings, $nav, $nav[$depth], '', $link,$rootpath,'',$adminedit);
				}
			}
		}
	}
}

function wce_getrootid() {
	$db = dims::getInstance()->getDb();

	$select = "SELECT * FROM dims_mod_wce_heading WHERE id_module = :id_module AND id_heading = 0";
	$res=$db->query($select,array(':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT)));

	if ($row = $db->fetchrow($res)) return($row['id']);
	else return(0);
}

function wce_gettemplates() {
	clearstatcache();
	//$rootdir = DIMS_APP_PATH . '/modules/wce/templates';

	$wce_templates = array();
	$pdir = @opendir(_WCE_TEMPLATES_PATH);

	while ($tpl = @readdir($pdir)) {
		if ($tpl != '.' && $tpl != '..' && is_dir(_WCE_TEMPLATES_PATH."/{$tpl}")) {
			$wce_templates[] = $tpl;
		}
	}

	return($wce_templates);
}

function wce_getmodels() {
	//clearstatcache();

	$wce_models = array();
	// Mod?les globaux Â tous les espaces

	if (is_dir(_WCE_MODELS_PATH."/pages_publiques/")) {
		$pdir = @opendir(_WCE_MODELS_PATH."/pages_publiques/");
		while ($tpl = @readdir($pdir)) {
			if ($tpl != '.' && $tpl != '..' && is_dir(_WCE_MODELS_PATH."/pages_publiques/{$tpl}")) {
				$wce_models["pages_publiques"][] = $tpl;
			}
		}
	}
	// Mod?les propres Â l'espace courant
	if(is_dir(_WCE_MODELS_PATH."/{$_SESSION['dims']['workspaceid']}/")) {
		$pdir = @opendir(_WCE_MODELS_PATH."/{$_SESSION['dims']['workspaceid']}/");

		while ($tpl = @readdir($pdir)) {
			if ($tpl != '.' && $tpl != '..' && is_dir(_WCE_MODELS_PATH."/{$_SESSION['dims']['workspaceid']}/{$tpl}")) {
				$wce_models["workspace"][] = $tpl;
			}
		}
	}
	return($wce_models);
}

function wce_getobjectcontent($matches) {
	global $_DIMS; // Récupération de la "superglobal" $_DIMS
	global $dims;
	global $articleid; // Récupération de l'id de l'article courant.
	$db = dims::getInstance()->getDb();

	if (isset($_SESSION['dims']['currentarticleid']) && $_SESSION['dims']['currentarticleid'] > 0)
		$articleid = $_SESSION['dims']['currentarticleid'];
	$content = '';

	if (!empty($matches[1])) {
		$key = explode('/',trim($matches[1]));
		$id_object = explode(',',$key[0]);
		$params = explode('&gt;',$key[1]);
		$params = trim($params[count($params)-1]);

		if (sizeof($id_object) == 2 || sizeof($id_object) == 3) {// normal size !
			$module_id_cms = $id_object[1];

			$queryobj = "SELECT * FROM dims_mb_wce_object WHERE id=:id";

			$resobj = $db->query($queryobj,array(':id'=>array('value'=>$id_object[0],'type'=>PDO::PARAM_INT)));
			if($obj = $db->fetchrow($resobj)) {
				$obj['module_id'] = $module_id_cms;
				if (isset($id_object[2])) $obj['object_id'] = $id_object[2];
				if(strpos($params, "params:") === 0){
					$params = substr($params, 7);
					$params = explode("&amp;",$params);
					foreach($params as $p){
						$p = explode("=", $p);
						if(count($p) == 2){
							$obj['params'][$p[0]] = $p[1];
						}
					}
				}

				if ($obj['script']!="") {
					$tab = explode("&",trim($obj['script'],"?"));
					foreach ($tab as $key => $value) {
						eval("$".$value.";");
						$for_get = explode('=', $value);
						if(count($for_get) == 2){
							$_GET[$for_get[0]] = str_replace("'", "", $for_get[1]);
						}
					}
				}
				ob_start();
				require_once(DIMS_APP_PATH . '/modules/system/class_module.php');
				$currentmod= new module();
				$curlabels=$currentmod->getLabels($obj['module_id']);

				//Passage d'informations sur le module instancé en session
				$oldmoduleid = $_SESSION['dims']['moduleid'];
				$_SESSION['dims']['moduleid'] = $obj['module_id']; //Pour compatibilité avec dims_data_object::setugm()

				// Restore old wce module id on scirpt termination.
				register_shutdown_function(function($oldmoduleid) {
					$_SESSION['dims']['moduleid'] = $oldmoduleid;
				} , $oldmoduleid);
				$_SESSION['dims']['wce_object']['moduleid']		= $obj['module_id'];

				$_SESSION['dims']['wce_object']['moduletype']	= $curlabels['moduletype'];
				$_SESSION['dims']['wce_object']['adminedit']	= dims_load_securvalue('adminedit', dims_const::_DIMS_NUM_INPUT, true, true);

				include(DIMS_APP_PATH . "modules/".$curlabels['moduletype']."/cms.php");
				$content .= ob_get_contents();
				ob_end_clean();

				$_SESSION['dims']['moduleid'] = $oldmoduleid; // Reset old module id.
			}
		}
	}
	return($content);
}

// fonction permettant de gÃ©nÃ©rer le sitemap d'un idmodule donne
function wce_getSiteMap($db,$wce_module_id) {
	$headings = wce_getheadings($wce_module_id);
	$content="";
	$maxupdatetime=0;
	$today = dims_createtimestamp();

	$select =	"
				SELECT		a.id,a.id_heading,a.urlrewrite,a.position,a.title,a.priority,a.timestp_modify,a.changefreq,a.type
				FROM		dims_mod_wce_article as a
				WHERE		a.id_module = :id_module
				AND			(a.timestp_published <= :timestp_published OR a.timestp_published = 0)
				AND			(a.timestp_unpublished >= :timestp_unpublished OR a.timestp_unpublished = 0)
				AND			(a.is_sitemap=1 or (a.uptodate=1 or a.version>1))
				ORDER BY	a.timestp_published  DESC";


	$res=$db->query($select,array(':id_module'=>array('value'=>$wce_module_id,'type'=>PDO::PARAM_INT),
									':timestp_published'=>array('value'=>$today,'type'=>PDO::PARAM_INT),
									':timestp_unpublished'=>array('value'=>$today,'type'=>PDO::PARAM_INT)));
		//die('ii'.$db->numrows($res));
	if ($db->numrows($res)>0) {
		while ($art = $db->fetchrow($res)) {
			// verification de l'existence de l'ensemble des rubriques
			$insert=true;

			if (isset($headings['list'][$art['id_heading']]) || $art['type']==1) {
				if ($art['type']==1) $insert=1;
				else {
					foreach ($headings['list'][$art['id_heading']]['parents'] as $k=>$parent) {
						if ($parent>0) {
							$insert=$insert && isset($headings['list'][$parent]);
						}
					}
				}

				if ($insert) {
					$updatedate=$art['timestp_modify'];
					$updatedate=substr($updatedate,0,4)."-".substr($updatedate,4,2)."-".substr($updatedate,6,2);
					if ($updatedate>$maxupdatetime) $maxupdatetime=$updatedate;

					if ($art["urlrewrite"]!="") {
						// on a la liste des parents avec lui
						if (isset($headings['list'][$art['id_heading']]['parents'])) {
							$lsth=$headings['list'][$art['id_heading']]['parents'].";".$art['id_heading'];
							$lsth=explode(";",$lsth);
						}
						else {
							$lsth=array();
						}

						$url="<HOSTNAME>";

						foreach ($lsth as $h) {
							if (isset($headings['list'][$h])) {
								$chpath=$headings['list'][$h]['urlrewrite'];
								if ($chpath!="") $url.="/".$chpath;
							}
						}
						// on ajoute l'article
								$url.=dims_convertaccents("/".$art["urlrewrite"].".html");
					}
					else {
						$url="<HOSTNAME>/index.php?articleid=".$art['id'];
					}
					if ($art['changefreq']=="") $art['changefreq']="weekly";
					if ($art['priority']==0) $art['priority']="0.9"	; // PZ le 14/10/2010 ajout guillemets sinon la virgule se met dans le fichier xml et non un point
					$content.="<url><loc>".$url."</loc><lastmod>".$updatedate."</lastmod><changefreq>".$art['changefreq']."</changefreq><priority>".$art['priority']."</priority></url>\n";
				}
			}
		}

		// generation de la page d'accueil
		//$content.="<url><loc><HOSTNAME></loc><lastmod>".$maxupdatetime."</lastmod><changefreq>weekly</changefreq><priority>1</priority></url>\n";
	}

	return $content;
}

function wce_getRecursiveSiteStructure($headings,$idcour,$level,&$result,$articles,$path) {
	global $articleschecked;
		// recuperation de l'objet
	if (isset($headings['list'][$idcour])) {
		$heading=array();
		$heading['id']=$idcour;
		$heading['label']=$headings['list'][$idcour]['label'];
		$heading['position']=$headings['list'][$idcour]['position'];
		$heading['depth']=$headings['list'][$idcour]['depth'];
		$heading['is_sitemap']=$headings['list'][$idcour]['is_sitemap'];

		// calcul du rewrite
		$url=$path;
		$linkedart=$headings['list'][$idcour]['linkedpage'];
		if ($linkedart>0 && isset($articles['list'][$linkedart])) {
			$url=$articles['list'][$linkedart];
			if (!isset($articleschecked[$linkedart])) {
				$articleschecked[$linkedart]=$linkedart;
			}
		}
		else {
			if ($headings['list'][$idcour]['headingrewrite']!='') {
				$url .= "/".$headings['list'][$idcour]['headingrewrite']."/index.html";
			}
			else {
				if($linkedart > 0)
					$url .= "/index.php?articleid={$linkedart}";
				else
					$url .= "/index.php?headingid={$idcour}";
			}
		}

		$heading['url']=$url;
		$heading['articles']=array();

		if (isset($articles[$idcour])) {
			foreach ($articles[$idcour] as $ar) {
				if ($ar['id']!=$linkedart && !isset($articleschecked[$ar['id']])) { // on enleve la page d'accueil
					$heading['articles'][]=$ar;
					$articleschecked[$ar['id']]=$ar['id'];
				}
			}
		}

		// on stocke
		$result[]=$heading;
	}

	//echo str_repeat("&nbsp;",$level*2).$idcour."<br>";
	if (isset($headings['tree'][$idcour])) {

		foreach ($headings['tree'][$idcour] as $idh) {
			wce_getRecursiveSiteStructure($headings,$idh,($level+1),$result,$articles,$path);
		}
	}


}

// fonction permettant de generer le sitemap d'un idmodule donne
function wce_getSiteStructure($db,$wce_module_id) {
	global $dims;
	$headings = wce_getheadings($wce_module_id);
	$content="";
	$maxupdatetime=0;
	$result=array();
	$result=array();
	$articles=array();
	$articles['list']=array();

	$today = dims_createtimestamp();

	$path=$dims->getProtocol().$dims->getHttpHost();
	// construction des articles
	$select =	"
				SELECT		a.id,a.id_heading,a.urlrewrite,a.position,a.title, a.timestp_modify
				FROM		dims_mod_wce_article as a
				WHERE		a.id_module = :id_module
				AND			(a.timestp_published <= :timestp_published OR a.timestp_published = 0)
				AND			(a.timestp_unpublished >= :timestp_unpublished OR a.timestp_unpublished = 0)
				AND			a.is_sitemap=1
				ORDER BY	a.timestp_published DESC";

	$res=$db->query($select,array(':id_module'=>array('value'=>$wce_module_id,'type'=>PDO::PARAM_INT),
									':timestp_published'=>array('value'=>$today,'type'=>PDO::PARAM_INT),
									':timestp_unpublished'=>array('value'=>$today,'type'=>PDO::PARAM_INT)));

	if ($db->numrows($res)>0) {
		while ($art = $db->fetchrow($res)) {
			$updatedate=$art['timestp_modify'];
			$updatedate=substr($updatedate,0,4)."-".substr($updatedate,4,2)."-".substr($updatedate,6,2);
			if ($updatedate>$maxupdatetime) $maxupdatetime=$updatedate;
			$url=$path;
			if ($art["urlrewrite"]!="") {
				// on a la liste des parents avec lui
				if (isset($headings['list'][$art['id_heading']]['parents'])) {
					$lsth=$headings['list'][$art['id_heading']]['parents'];//.";".$art['id_heading'];
					$lsth=explode(";",$lsth);
				}
				else {
					$lsth=array();
				}

				foreach ($lsth as $h) {
					if (isset($headings['list'][$h])) {
						$chpath=$headings['list'][$h]['urlrewrite'];
						if ($chpath!="") $url.="/".$chpath;
					}
				}
				// on ajoute l'article
				$url.="/".$art["urlrewrite"].".html";
			}
			else {
				$url.="/index.php?articleid=".$art['id'];
			}
			$art['url']=$url;
			// ajoute des articles à la structure articles
			$articles[$art['id_heading']][]=$art;
			$articles['list'][$art['id']]=$url;
		}
	}

	$articleschecked=array();
	if (isset($headings['tree'][0])) {
		wce_getRecursiveSiteStructure($headings,0,1,$result,$articles,$path);
	}
	unset($articles);
	unset($headings);
	return $result;
}

function generateValideUrl($text){
	$text = htmlentities($text, ENT_NOQUOTES, 'utf-8');
	$text = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $text);
	$text = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $text);
	$text = preg_replace('#&[^;]+;#', '', $text);
	$text = str_replace(array(' ','-','/','\\',"'"),'-',trim($text));
	$text = preg_replace('#[^-a-zA-Z0-9_]#',"",$text);
	$text = preg_replace('#-+#',"-",$text);
	$text = preg_replace('/-+$/i',"",$text);
	return strtolower($text);
}
?>
