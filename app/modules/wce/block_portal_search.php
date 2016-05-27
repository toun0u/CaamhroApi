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
$ok=false;
$workspaceid=$_SESSION['dims']['workspaceid'];
require_once(DIMS_APP_PATH . '/modules/system/class_block_user.php');
$blockuser = new block_user();
$blockuser->open($_SESSION['dims']['userid'],$moduleid,$workspaceid);
$datelastvalidate=$blockuser->fields['date_lastvalidate'];
$module=$dims->getModule($moduleid);
$moduletype=$module['label'];

$params = array();
switch($dims_op) {
	case 'searchfavorites':
		$sql="	SELECT		art.*, u.lastname,u.firstname
				FROM		dims_favorite as f
				INNER JOIN	dims_user as u
				ON			u.id=f.id_user_from
				AND			id_user=:id_user
				AND			id_module=:id_module
				AND			id_workspace=:id_workspace
				AND			f.type=:type
				AND			f.id_object= :idobject
				INNER JOIN	dims_mod_wce_article as art
				ON			art.id=f.id_record
				GROUP BY	f.id_module, f.id_object, f.id_record";
		$params[':id_user'] =		array('value'=>$_SESSION['dims']['userid'],'type'=>PDO::PARAM_INT);
		$params[':id_module'] =		array('value'=>$moduleid,'type'=>PDO::PARAM_INT);
		$params[':id_workspace'] =	array('value'=>$workspaceid,'type'=>PDO::PARAM_INT);
		$params[':type'] =			array('value'=>$_SESSION['dims']['favorites'],'type'=>PDO::PARAM_INT);
		$params[':idobject'] =		array('value'=>_WCE_OBJECT_ARTICLE,'type'=>PDO::PARAM_INT);
		$ok=true;
		break;
	case 'searchannot':
	case 'searchnewscontent':
	case 'searchnews':

		$sql =	"
			SELECT		art.*, h.label as labelheading, h.id as idheading, u.lastname,u.firstname
			FROM		dims_mod_wce_article as art
			INNER JOIN	dims_mod_wce_heading as h
			ON			h.id = art.id_heading
			AND			h.id_module=:id_module1
			INNER JOIN	dims_workspace w
			ON			art.id_workspace = w.id
			AND 		w.id IN ( :workspaces )
			AND			art.id_module=:id_module2
			INNER JOIN	dims_user u
			ON 			art.id_user = u.id ";
		$params[':id_module1'] = array('value'=>$moduleid,'type'=>PDO::PARAM_INT);
		$params[':id_module2'] = array('value'=>$moduleid,'type'=>PDO::PARAM_INT);
		$params[':workspaces'] = array('value'=>$workspaces,'type'=>PDO::PARAM_INT);

		if ($datelastvalidate>0){
			 $sql.=" AND art.timestp_modify>:timestp_modify";
			$params[':timestp_modify'] = array('value'=>$datelastvalidate,'type'=>PDO::PARAM_INT);
		}

		$sql.="
			ORDER BY	art.timestp_modify DESC";
		$ok=true;

		break;
	default:
		$ok=false;
		if (isset($_GET['campaignid']) && is_numeric($_GET['campaignid']))
			$tabcorresp=getSearchReponse(_WCE_OBJECT_ARTICLE,$moduleid, dims_load_securvalue('campaignid', dims_const::_DIMS_NUM_INPUT, true, true, true));
		else
			$tabcorresp=getSearchReponse(_WCE_OBJECT_ARTICLE,$moduleid);

		if (sizeof($tabcorresp)>0) $ok=true;

		$sql =	"
			SELECT		art.*, h.label as labelheading, h.id as idheading, u.lastname,u.firstname
			FROM		dims_mod_wce_article as art
			INNER JOIN	dims_mod_wce_heading as h
			ON			h.id = art.id_heading
			AND			art.id in (".$db->getParamsFromArray($tabcorresp,'id',$params).")
			AND			h.id_module=:id_module1
			INNER JOIN	dims_workspace w
			ON			art.id_workspace = w.id
			AND 		w.id IN ( :workspaces )
			AND			art.id_module=:id_module2
			INNER JOIN	dims_user u
			ON 			art.id_user = u.id
			ORDER BY	art.title
			";
		$params[':id_module1'] = array('value'=>$moduleid,'type'=>PDO::PARAM_INT);
		$params[':id_module2'] = array('value'=>$moduleid,'type'=>PDO::PARAM_INT);
		$params[':workspaces'] = array('value'=>$workspaces,'type'=>PDO::PARAM_INT);
		break;
}

$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['id_object'] =_WCE_OBJECT_ARTICLE;
$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['label'] =$_DIMS['cste']['_DIMS_LABEL_PAGE'];
$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['tabid']=array();
$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['tabresult']=array();

if ($ok) {
	$res=$db->query($sql,$params);

	if ($db->numrows($res)) {
		if ($db->numrows($res)>1) {
			$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['title'] = "<font class=\"fontgray\">".$_DIMS['cste']['_DOC_LABEL_FILESNOFOUND']."</font>";
			$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['nb'] = $db->numrows($res);
			$firstpart = $db->numrows($res)." ".$_DIMS['cste']['_DIMS_LABEL_FOUNDS'];
		}
		else {
			$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['title'] = "1 ".$_DIMS['cste']['_DIMS_LABEL_FOUND'];
			$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['nb'] = 1;
		}
	}
	else {
		$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['title'] = $_DIMS['cste']['_DIMS_LABEL_NOFOUND'];
		$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['nb'] = 0;
	}
	/*

	$nb_elem_page = dims_const::_DIMS_NB_ELEM_PAGE;
	$numrows = $db->numrows($res);
	$nbpage = ($numrows - $numrows % $nb_elem_page) / $nb_elem_page + ($numrows % $nb_elem_page > 0);
	$page=dims_load_securvalue("page",dims_const::_DIMS_NUM_INPUT,true,false);
	if ($page>0) {
		$_SESSION['dims']['search']['page'][$moduleid]=$page;
	}
	else {
		if (isset($_SESSION['dims']['search']['page'][$moduleid])) $page=$_SESSION['dims']['search']['page'][$moduleid];
		else {
			$page = 1;
			$_SESSION['dims']['search']['page'][$moduleid]=$page;
		}
	}
	if ($nbpage>0) {
	?>
		<div style="float:right;">
			<div style="float:left;">page :&nbsp;</div>
			<?
			for ($p = 1; $p <= $nbpage; $p++) {
				?>
				<a class="system_page<? if ($p==$page) echo '_sel'; ?>" href="javascript:void(0)" onclick="refreshDesktopPage(<? echo $moduleid.",'".$dims_op."',".$p; ?>);"><? echo $p; ?></a>
				<?
			}
			?>
		</div>
		<?
	}*/
	$tabresult=array();
	$tabid=array();
	$lastchange=0;
	$lastuser="";

	$cpte=0;
	while ($row = $db->fetchrow($res) ) {
		// test si non d�passement
			$elem=array();
			$tabid[$row['id']]=$row['id'];
			$elem['id']=$row['id'];
			$elem['title']=$row['labelheading'];
			$elem['titlelink']=$row['labelheading'];
			$elem['view']=$row['meter']." fois";
			$elem['link']= dims_urlencode("admin.php?dims_moduleid={$row['id_module']}&dims_desktop=block&dims_action=public&wce_mode=render&headingid={$row['idheading']}&articleid={$row['id']}");
			$tempch="";

			for ($i=1;$i<=9;$i++) {
				if ($row['content'.$i]!="") {
					$tempch.="<br>".$row['content'.$i];
				}
			}

			$elem['content']=dims_strcut(strtolower(dims_convertaccents(html_entity_decode(strip_tags($tempch)))),300);
			$elem['author']=strtoupper(substr($row['firstname'],0,1)).". ".$row['lastname'];
			$elem['timestp_modify']=$row['timestp_modify'];
			$elem['annot']=0;
			$tabresult[$elem['id']]=$elem;

			if($lastchange<$row['timestp_modify']) {
				$lastchange=$row['timestp_modify'];
				$lastuser=$elem['author'];
			}
			$cpte++;
	}

	// calcul de la derni�re maj
	/*
	if ($lastchange!=0 && $dims_op=='searchnews') {
		$firstpart.=" <font class=\"fontgray\"> - ".dims_getLastModify($lastchange);
		if ($lastuser!="") $firstpart.=" ".$_DIMS['cste']['_DIMS_LABEL_FROM']." ".$lastuser;
		$firstpart.="</font>";
	}*/

	$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['tabid']=$tabid;
	$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['tabresult']=$tabresult;
	// echo getSearchcontent(_WCE_OBJECT_ARTICLE,$moduleid,$tabcorresp,$tabresult,$dims_op=='searchnews');
}
else {
	$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['title'] = $_DIMS['cste']['_DIMS_LABEL_NOFOUND'];
	$dims_searchresult[$moduleid][_WCE_OBJECT_ARTICLE]['nb'] = 0;
}
?>
