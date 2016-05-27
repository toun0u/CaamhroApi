<?php
$moduleid=$_SESSION['dims']['current_object']['id_module'];
$objectid=$_SESSION['dims']['current_object']['id_object'];
$recordid=$_SESSION['dims']['current_object']['id_record'];

global $obj;

if (isset($_SESSION['dims']['current_object'])) {
	if ($dims->isModuleEnabled($moduleid)) {
		$mod=$dims->getModule($moduleid);
		if ($mod['active']) {
			$dims_mod_opfile = DIMS_APP_PATH . "/modules/{$mod['label']}/op.php";
			if (file_exists($dims_mod_opfile)) {
				include $dims_mod_opfile;
			}
		}
	}
	$workspaceid=$_SESSION['dims']['workspaceid'];
	$moduletype=$mod['label'];

	require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
	$dims_user = new user();
	$dims_user->open($_SESSION['dims']['userid']);

	// construction des favoris
	$favorites=$dims_user->getFavorites($moduleid);

	$label=$_SESSION['dims']['current_object']['label'];

	$title=str_replace("<OBJECT>","",$_DIMS['cste']['_DIMS_OBJECT_PROPERTIES']);

	//echo $skin->open_widgetbloc( $title, 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#cccccc;', '','26px', '26px', '-15px', '-7px', '', '', '');
	?>
	<table style="width:100%">
		<tr>
			<td><? echo $_DIMS['cste']['_DIMS_LABEL']; ?></td>
			<td><? echo $_SESSION['dims']['current_object']['label']; ?>
		</tr>
		<tr>
			<td><? echo $_DIMS['cste']['_AUTHOR']; ?></td>
			<td>
			<?
				$usr= new user();
				$usr->open($_SESSION['dims']['current_object']['id_user']);
				echo $usr->fields['firstname']." ".$usr->fields['lastname'];
			?>
		</tr>
		<tr>
			<td><? echo $_DIMS['cste']['_DIMS_DATE_MODIFY']; ?></td>
			<td>
			<?
			$datvar=dims_timestamp2local($_SESSION['dims']['current_object']['timestp_modify']);
			$chdate=$datvar['date'];
			echo $chdate;
			?>
			</td>
		</tr>

	</table>
	<div style="text-align:right">
		<?php
		$value=0;
		$elem=array();
		// gestion des fonctions standards
		// on regarde maintenant les favoris / en veille
		if (isset($favorites['access'][$workspaceid][$moduleid][$objectid][$recordid]) && $favorites['access'][$workspaceid][$moduleid][$objectid][$recordid]['type']>0) {
			$idfav=$favorites['access'][$workspaceid][$moduleid][$objectid][$recordid]['id'];
			$value=$favorites['access'][$workspaceid][$moduleid][$objectid][$recordid]['type'];
		}
		else {
			$idfav=0;
			$value=0;
		}

		if (isset($_SESSION['dims']['desktop_collab']) && ($_SESSION['dims']['desktop_collab']== dims_const::_DIMS_CSTE_FAVORITE || $_SESSION['dims']['desktop_collab']==dims_const::_DIMS_CSTE_SURVEY))
			$refresh=1;
		else
			$refresh=0;

		// on traite le en veille
		if ($value!=2) {
			// add favor
			$elem['name']=$_DIMS['cste']['_ADDTO_FAVORITES'];
			$elem['src']="./common/img/fav1.png";
			$elem['link']= "";
			$elem['width']= "width:125px";
			$elem['script']= "refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$moduleid.",".$workspaceid.",".$objectid.",".$recordid.",2,".$obj->fields['id_user'].",0,".$refresh.");";
			$elem['script'].="viewPropertiesObject($objectid,$recordid,$moduleid,1);";
			$_SESSION['dims']['current_object']['cmd'][]=$elem;

			if ($value==0) {
				// add wait
				$elem['name']=$_DIMS['cste']['_DIMS_ADDTO_SURVEY'];
				$elem['src']="./common/img/view.png";
				$elem['link']= "";
				$elem['width']= "width:125px";
				$elem['script']= "refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$moduleid.",".$workspaceid.",".$objectid.",".$recordid.",1,".$obj->fields['id_user'].",0,".$refresh.");";
				$elem['script'].="viewPropertiesObject($objectid,$recordid,$moduleid,1);";
				$_SESSION['dims']['current_object']['cmd'][]=$elem;
			}
			else {
				// remove from wait
				$elem['name']=$_DIMS['cste']['_DIMS_REMOVEFROM_SURVEY'];
				$elem['src']="./common/img/delete.png";
				$elem['link']= "";
				$elem['width']= "width:150px";
				$elem['script']= "refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$moduleid.",".$workspaceid.",".$objectid.",".$recordid.",0,".$obj->fields['id_user'].",0,".$refresh.");";
				$elem['script'].="viewPropertiesObject($objectid,$recordid,$moduleid,1);";
				$_SESSION['dims']['current_object']['cmd'][]=$elem;
			}
		}
		else {
			// on peut annuler le favoris
			$elem['name']=$_DIMS['cste']['_REMOVEFROM_FAVORITES'];
			$elem['src']="./common/img/delete.png";
			$elem['link']= "";
			$elem['width']= "width:150px";
			$elem['script']= "refreshFavorites(".$idfav.",".$_SESSION['dims']['userid'].",".$moduleid.",".$workspaceid.",".$objectid.",".$recordid.",0,".$obj->fields['id_user'].",0,".$refresh.");";
			$elem['script'].="viewPropertiesObject($objectid,$recordid,$moduleid,1);";
			$_SESSION['dims']['current_object']['cmd'][]=$elem;
		}

		//echo "<table width=\"100%\" align=\"right\"><tr>";
		// construction de la liste des commandes possibles � faire
		foreach ($_SESSION['dims']['current_object']['cmd'] as $elem) {
			if (!isset($elem['script'])) $elem['script']="";
			if (!isset($elem['link']) || $elem['link']=="") $elem['link']="void(0);";
			if (!isset($elem['width']) || $elem['width']=="") $elem['width']="width:70px;";
			echo dims_create_button($elem['name'],$elem['src'],$elem['script'],'',$elem['width'],$elem['link'],'','');
		}
		//echo "</table>";
		?>
		</div>
	</div>
<?php
	// affichage des tags utilis�s pour cet objet
	echo $skin->open_widgetbloc('Tags', 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#cccccc;', '','26px', '26px', '-15px', '-7px', '', '', '');
	// selection des tags
	echo "<div id=\"tagblockdisplay\">";
	echo dims_getBlockTag($dims,$_DIMS,$moduleid,$objectid,$recordid);
	echo "</div>";
	echo $skin->close_widgetbloc();
}
else {
	echo "<p style=\"text-align:center;font-weight:bold;\">".$_DIMS['cste']['_DIMS_LABEL_NO_OBJECT']."</p>";
}
?>
