<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!isset($_SESSION['currentshare']['id_share'])) $_SESSION['currentshare']['id_share']=0;
if (!isset($_SESSION['currentshare']['id_user'])) $_SESSION['currentshare']['id_user']=0;
if (!isset($_SESSION['currentshare']['id_contact'])) $_SESSION['currentshare']['id_contact']=0;

$id_share=dims_load_securvalue("id_share",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['currentshare']['id_share']);
$id_user=dims_load_securvalue("id_user",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['currentshare']['id_user']);
$id_contact=dims_load_securvalue("id_contact",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['currentshare']['id_contact']);
if (!isset($usercode)) $usercode="";

if ($_SESSION['currentshare']['id_share']==0) {
	die();
}
else {
	$share = new sharefile_share();
	$share->open($id_share);

	// verification de l'existance du partage
	if ($share->fields['label']=="" || $share->fields['id_module']=='') {
		dims_redirect($dims->getScriptEnv()."?op=share&action=sharefile_deleted",true);
	}

	// active par d�faut le code vierge
	if (!isset($_SESSION['sharecodes'][$id_share])) $_SESSION['sharecodes'][$id_share]="";

	// 2 tests : soit proprio, soit je fais partie de la liste

	$enabled=$share->isEnabled($id_user,$id_contact,$usercode) || $share->isOwner($_SESSION['dims']['userid'] || dims_isadmin());

	if ($enabled) {
		// verification du code
		if (isset($_SESSION['dims']['userid']) && !$share->isOwner($_SESSION['dims']['userid'])) {
			if (($share->fields['code']!="" && $share->fields['code']!=$_SESSION['sharecodes'][$id_share]) || ($usercode!="" && $usercode!=$_SESSION['sharecodes'][$id_share]) ) {
				//dims_print_r($_SESSION['currentshare']);die();
				dims_redirect($dims->getScriptEnv()."?op=share&action=sharefile_codecheck",true);
			}
		}
		// code active
		// controle si date deja d�pass�e ou non
		$maxtoday = mktime(0,0,0,date('n'),date('j')+$sharefile_param->fields['nbdays'],date('Y'));
		$dateday=date('d/m/Y',$maxtoday);
		$maxtoday=dims_local2timestamp($dateday);

		if ($share->fields['timestp_finished']>$maxtoday && !isset($_SESSION['dims']['userid']) && !$share->isOwner($_SESSION['dims']['userid'])) {
			dims_redirect($dims->getScriptEnv()."?op=share&action=sharefile_maxdate",true);
		}

		// controle du nombre de consultation
		if (!isset($_SESSION['dims']['userid']) || (isset($_SESSION['dims']['userid']) && !$share->isOwner($_SESSION['dims']['userid']))) {
			if (!isset($_SESSION['sharecount'][$id_share])) {
				// on le cr�� en comptant au pr�alable le nbre de download
				if ($_SESSION['currentshare']['id_user']>0)
					$res=$db->query("SELECT * FROM dims_mod_sharefile_user WHERE id_share= :idshare AND id_user= :iduser ",
									array(':idshare' => $share->fields['id'], ':iduser' => $_SESSION['currentshare']['id_user']));
				else
					$res=$db->query("SELECT * FROM dims_mod_sharefile_user WHERE id_share= :idshare AND id_contact= :idcontact ",
									array(':idshare' => $share->fields['id'], ':idcontact' => $_SESSION['currentshare']['id_contact']));

				$cpte=0;
				$idfileuser=0;

				if ($db->numrows($res)>0) {
					$f=$db->fetchrow($res);
					$cpte=$f['view'];
					$idfileuser=$f['id'];
				}

				if ($cpte<$sharefile_param->fields['nbdownload'] || $sharefile_param->fields['nbdownload']==0) {
					$file_user = new sharefile_user();

					if ($idfileuser>0) {
						$file_user->open($idfileuser);
						$file_user->fields['view']++;
						$file_user->save();
					}
					$cpte++;
					// creation du log de consultation
					$share->createHistory($_SESSION['currentshare']['id_user'],$_SESSION['currentshare']['id_contact']);
				}
				else {
					dims_redirect($dims->getScriptEnv()."?op=share&action=sharefile_maxdownload",true);
				}
				$_SESSION['sharecount'][$id_share]=true;
			}
		}
	}
	else dims_redirect($dims->getScriptEnv()."?op=share&action=sharefile_codecheck",true);
}

// date de cr�ation
$datecreate= dims_timestamp2local($share->fields['timestp_create']);
$user = new user();
$user->open($share->fields['id_user']);
$properties=$share->getFilesProperties();

?>

<div style="clear:both;float:left;width:100%;border:0px;border-bottom:1px;border-color:#dadada;border-style:solid;font-size:12px;">
	<div class="share_bg"><span style="font-weight:bold;float:left;margin-left:65px;width:140px;margin-top:20px"><? echo $share->fields['label']; ?></span></div>
	<div class="share_bg_right" style="background-color:#CCCCCC">
		<table style="width:100%"><tr>
				<td style="width:60%"><span style="margin-left:10px;float:left;"><font style="float:left;width:120px;text-decoration: underline;">Dossier cr&eacute;&eacute; le : </font><? echo $datecreate['date']; ?></span>
			<span style="margin-left:10px;clear:both;float:left;"><font style="float:left;width:120px;;text-decoration: underline;">Par : </font><font style="float:left;"><? echo $user->fields['firstname']." ".$user->fields['lastname']; ?></font></span>
			<span style="margin-left:10px;clear:both;float:left;"><font style="float:left;width:120px;text-decoration: underline;">Contenant : </font><? echo $properties['nbfiles']; ?> fichier(s)</span>
			<span style="margin-left:10px;clear:both;float:left;"><font style="float:left;width:120px;text-decoration: underline;">Poids total : </font><? echo sprintf("%.02f",$properties['size']/1048576); ?> Mo</span>
			</td><td style="valign:top;"><span style="float:left;">Descriptif : <br><br>
			<? echo $share->fields['description']; ?>
		 </span></td></tr>
		</table>
	</div>
</div>
<div style="clear:both;float:left;padding-top:10px;width:100%;padding-bottom:20px;">
	<?
	// collecte des fichiers
	$lstfiles=$share->getFiles();

	if (!empty($lstfiles)) {
		foreach ($lstfiles as $file) {

			echo "<div style=\"float:left;width:140px;cursor: default;padding-left:10px;text-align:center;\" onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;\">
					<a style=\"text-decoration:none;\" href=".$file['downloadlink']." title=\"Voir le document.\">";

			switch ($file['extension']) {
				case "ppt":
					$namefile="ppt.png";
					break;
				case "pdf":
					$namefile="pdf.png";
					break;
				case "wav":
					$namefile="sound.png";
					break;
				case "doc":
				case "docx":
				case "odt":
					$namefile="doc.png";
					break;
				case "xls":
					$namefile="xls.png";
					break;
				case "jpg":
				case "png":
				case "bmp":
				case "gif":
				case "xcf":
				case "psd":
					$namefile="img.png";
					break;
				case "avi":
				case "mpg":
				case "mpeg":
				case "mpeg4":
					$namefile="video.png";
					break;
				default :
					$namefile="file.png";
					break;
			}

			if (strlen($file['name'])>15) {
				$title=dims_strcut($file['name'],15);
				$title.=" .".$file['extension'];
			}
			else $title=$file['name'];

			echo "<img style=\"\" src=\"./common/modules/sharefile/img/".$namefile."\" border=\"0\">
				<br><font style=\"font-size:12px;\">".$title."<br>".sprintf("%.02f",$file['size']/1048576)." Mo</font></a></div>";
		}
		echo "</span>";
	}
	?>
</div>
<div style="clear:both;float:left;padding-top:10px;width:100%;padding-bottom:20px;">
	<?
	$zip_path = realpath('./').'/data/sharefiles';
	$zip_file=$zip_path."/".$share->fields['id']."_".$share->fields['timestp_create'].".zip";
	$zip_file_web="./data/sharefiles/".$share->fields['id']."_".$share->fields['timestp_create'].".zip";
	if (file_exists($zip_file)) {
		$size=filesize($zip_file);
		echo "<div style=\"width:100%;text-align:center;\"><a href=\"".$zip_file_web."\"><img style=\"\" src=\"./common/modules/sharefile/img/archive.png\" border=\"0\">
			   <br><font style=\"font-size:12px;\">Fichier zip<br>".sprintf("%.02f",$size/1048576)." Mo</font></a>";
	}
	?>
</div>
