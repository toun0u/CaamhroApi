<?php
/*echo "<script type=\"text/javascript\">";
require_once DIMS_WEB_PATH . "/common/modules/doc/include/javascript.php";
echo "</script>";*/

$docfolder_readonly_content = false;

if (!empty($currentfolder)) {
	$docfolder->open($currentfolder);
	$docfolder_readonly_content = ($docfolder->fields['readonly_content'] && $docfolder->fields['id_user'] != $_SESSION['dims']['userid']);
}

require_once DIMS_APP_PATH . '/modules/doc/public_folder_info.php';
?>

<form name="listdoc" method="POST">
<table cellpadding="2" cellspacing="1" width="100%" bgcolor="#ffffff">
	<tr>
		<td style="width:47%"><? echo $_DIMS['cste']['_DIMS_LABEL'];?></td>
		<td style="width:15%"><? echo $_DIMS['cste']['_SIZE'];?></td>
		<td style="width:10%"><? echo $_DIMS['cste']['_DIMS_OWNER'];?></td>
		<td style="width:18%"><? echo $_DIMS['cste']['_DIMS_DATE'];?></td>
		<td style="width:10%"><? echo $_DIMS['cste']['_DIMS_ACTIONS'];?></td>
	</tr>
		<?
		$shares = dims_shares_get($_SESSION['dims']['userid']);

		$dims_favorites=$dims_user->getFavorites($_SESSION['dims']['moduleid']);

		$list_sharedfolder = array();
		$list_sharedfile = array();

		foreach($shares as $sh) {
			if ($sh['id_object'] == _DOC_OBJECT_FOLDER) $list_sharedfolder[] = $sh['id_record'];
			if ($sh['id_object'] == _DOC_OBJECT_FILE) $list_sharedfile[$sh['id_record']] = $sh['id_record'];
		}

		// DISPLAY FOLDERSx
		$param = array();
		$where = (!empty($list_sharedfolder)) ? ' OR f.id IN ('.$db->getParamsFromArray($list_sharedfolder, 'sharedfolder', $param).')' : '';
		$sql =	"
				SELECT		f.*,
							u.login,
							w.label,
							g.id as gal_id
				FROM		dims_mod_doc_folder f
				LEFT JOIN	dims_mod_doc_gallery g
				ON			g.id_folder=f.id
				INNER JOIN	dims_user u
				ON			f.id_user = u.id
				INNER JOIN	dims_workspace w
				ON			f.id_workspace = w.id
				WHERE		f.id_folder = :idfolder
				AND			f.id_module = :idmodule
				AND			f.published = 1";
		$param[':idfolder'] = $currentfolder;
		$param[':idmodule'] = $_SESSION['dims']['moduleid'];
		if (!dims_isadmin()) {
			$sql.= "
					AND			(f.id_user = :userid
								OR (f.foldertype = 'public' AND f.id_workspace IN (".dims_viewworkspaces()."))
					$where
							)";
			$param['userid'] =$_SESSION['dims']['userid'];
		}

		$sql.="
				ORDER BY	f.name
				";

		$res=$db->query($sql, $param);

		$nbfolderscheck=0;
		while ($row = $db->fetchrow($res)) {
			$ldate = dims_timestamp2local($row['timestp_modify']);
			$readonly = (($row['readonly'] && $row['id_user'] != $_SESSION['dims']['userid']) || $docfolder_readonly_content);
			$color = (!isset($color) || $color == 2) ? 1 : 2;
			?>
			<tr class="trl<? echo $color; ?>">
				<td>
				<?

					if (dims_isadmin() || dims_isactionallowed(0) || (dims_isactionallowed(_DOC_ACTION_DELETEFILE) && !$docfolder_readonly_content) || $row['id_user'] == $_SESSION['dims']['userid']) {
							echo "<input type=\"checkbox\" id=\"selfolder".$nbfolderscheck."\" name=\"selfolder[]\" value=\"".$row['id']."\">";
							$nbfolderscheck++;
					}
					?>
					<img src="./common/modules/doc/img/ico_folder<? if ($row['foldertype'] == 'shared') echo '_shared'; ?><? if ($row['foldertype'] == 'public') echo '_public'; ?><? if ($row['readonly']) echo '_locked'; ?>.png" />
					<a class="doc_explorer_link_<? echo $color; ?>" title="<? echo ($row['description']); ?>" href="<? echo dims_urlencode("{$scriptenv}?op=browse&currentfolder={$row['id']}"); ?>">
					<? echo $row['name']; ?>
					</a>
				</td>
				<td><? echo $row['nbelements']; ?> element(s)</td>
				<td><? echo $row['login']; ?></td>
				<td><? echo $ldate['date']; ?> <? echo substr($ldate['time'],0,5); ?></td>
				<td>
					<?
					// create object options
					dims_createOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FOLDER,$row['id'],$row['name'],$row['id_user']);

					if (dims_isadmin() ||  dims_isactionallowed(0) ||  dims_isactionallowed(_DOC_ACTION_DELETEFOLDER) && (!$readonly)) {
						if ($row['gal_id']>0) {
							dims_addOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FOLDER,$row['id'],dims_urlencode("{$scriptenv}?op=folder_gallery_modify&currentfolder={$row['id']}&currentgallery={$row['gal_id']}"),"","Modifier la galerie","","./common/modules/doc/img/gallery_created.png");
						} else {
							dims_addOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FOLDER,$row['id'],dims_urlencode("{$scriptenv}?op=folder_gallery_create&currentfolder={$row['id']}&currentgallery={$row['gal_id']}"),"","Cr&eacute;er la galerie","","./common/modules/doc/img/gallery_not_created.png");
						}
					}
					// modify folder
					if (dims_isadmin() || dims_isactionallowed(0) || dims_isactionallowed(_DOC_ACTION_MODIFYFOLDER) || $row['id_user']==$_SESSION['dims']['userid']) {
						$href=dims_urlencode("{$scriptenv}?op=folder_modify&currentfolder={$row['id']}");
						dims_addOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FOLDER,$row['id'],$href,"","","modify","");
					}
					// delete folder
					if (dims_isadmin() || dims_isactionallowed(0) || dims_isactionallowed(_DOC_ACTION_DELETEFOLDER) && (!$readonly) && ($row['nbelements'] == 0)  || $row['id_user']==$_SESSION['dims']['userid']) {
						$href="javascript:dims_confirmlink('".dims_urlencode("$scriptenv?op=folder_delete&currentfolder=".$currentfolder."&docfolder_id=".$row['id'])."','".$_DIMS['cste']['_DIMS_CONFIRM']."');";

						dims_addOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FOLDER,$row['id'],$href,"","","delete","");
					}
					else {
						$link="javascript:alert('".$_DIMS['cste']['_DOC_LABEL_UNAUTHORIZED_DELETEFOLDER']."');";
						dims_addOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FOLDER,$row['id'],"",$link,"","delete","");
					}

					// construction des favoris
					echo dims_displayOptions($_DIMS,$row['id_workspace'],$row['id_module'],_DOC_OBJECT_FOLDER,$row['id'],-250);
					?>
				</td>

			</tr>
			<?
		}
		// DISPLAY FILES
		$param = array();
		$where = (!empty($list_sharedfile)) ? ' OR f.id IN ('.$db->getParamsFromArray($list_sharedfolder, 'sharedfolder', $param).')' : '';
		$sql =	"
				SELECT		f.id,
							f.timestp_create,
							f.id_module,
							f.version,
							f.extension,
							f.size,
							f.name,
							f.description,
							f.id_folder,
							f.id_workspace,
							f.id_user,
							f.md5id,
							f.timestp_modify,

							u.login,
							e.filetype

				FROM		dims_mod_doc_file f

				LEFT JOIN	dims_user u
				ON			f.id_user = u.id

				LEFT JOIN	dims_mod_doc_ext e
				ON			e.ext = f.extension

				WHERE		f.id_folder = :idfolder
				AND			f.id_module = :idmodule";
		$param[':idfolder'] = $currentfolder;
		$param[':idmodule'] = $_SESSION['dims']['moduleid'];

		if (!dims_isadmin()) {
			$sql.="
				AND			((f.id_user = :userid AND f.id_folder = 0) OR f.id_folder!=0 {$where})";
			$param['userid'] = $_SESSION['dims']['userid'];
		}

		$sql.="
				ORDER BY	f.name
				";

		// poursuivre sur test si folder partage ou non + filtre sur files $docfolder->
		$res=$db->query($sql, $param);

		$nbfilescheck=0;

		while ($row = $db->fetchrow($res)) {
			// test si fichier existe
			$doc= new docfile();
			$doc->fields['id']=$row['id'];
			$doc->fields['timestp_create']=$row['timestp_create'];
			$doc->fields['timestp_create']=$row['timestp_create'];
			$doc->fields['id_module']=$row['id_module'];
			$doc->fields['version']=$row['version'];
			$doc->fields['extension']=$row['extension'];

			if (file_exists($doc->getfilepath())) {
				$ksize = sprintf("%.02f",$row['size']/1024);
				$ldate = dims_timestamp2local($row['timestp_modify']);

				$color = (!isset($color) || $color == 2) ? 1 : 2;

				?>
				<tr class="trl<? echo $color; ?>">
					<td>
						<?

						if (dims_isadmin() || dims_isactionallowed(0) || (dims_isactionallowed(_DOC_ACTION_DELETEFILE) && !$docfolder_readonly_content) || $row['id_user'] == $_SESSION['dims']['userid']) {
							echo "<input type=\"checkbox\" id=\"seldoc".$nbfilescheck."\" name=\"seldoc[]\" value=\"".$row['id']."\">";
							$nbfilescheck++;
						}
							if (file_exists(DIMS_APP_PATH . '/modules/doc/img/mimetypes/ico_'.$row['filetype'].'.png'))
							{
								?><img src="./common/modules/doc/img/mimetypes/ico_<? echo $row['filetype']; ?>.png" /><?
							}
							else
							{
								?><img src="./common/modules/doc/img/mimetypes/ico_default.png" /><?
							}
							?>
						<a class="doc_explorer_link_1" title="<? echo ($row['description']); ?>" href="<? echo dims_urlencode("{$scriptenv}?op=file_download&docfile_md5id={$row['md5id']}"); ?>">
						<? echo $row['name']; ?></a>
					</td>

					<td><? echo $ksize; ?> ko</td>
					<td><? echo $row['login']; ?></td>
					<td><? echo $ldate['date']; ?> <? echo substr($ldate['time'],0,5); ?></td>
					<td>
						<?
						// create object options
						dims_createOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FILE,$row['id'],$row['name'],$row['id_user']);

						// download
						$href=dims_urlencode("{$scriptenv}?op=file_download&docfile_md5id={$row['md5id']}");
						dims_addOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FILE,$row['id'],$href,"",$_DIMS['cste']['_DIMS_DOWNLOAD'],"","./common/img/save.gif");

						dims_addOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FILE,$row['id'],"","javascript:displayPreview("._DOC_OBJECT_FILE.",".$row['id'].",".$row['id_module'].");",$_DIMS['cste']['_PREVIEW'],"","./common/img/view.png");

						// test si fichier compresse
						if ($row['extension']=="zip" || $row['extension']=="gz" || $row['extension']=="tgz") {
							$href="javascript:dims_confirmlink('".dims_urlencode("$scriptenv?op=file_extract&docfile_id={$row['id']}")."','".$_DIMS['cste']['_DIMS_CONFIRM']."');";
							dims_addOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FILE,$row['id'],$href,"",$_DIMS['cste']['_DOC_LABEL_UNCOMPRESS'],"","./common/modules/doc/img/ico_extract.png");
						}
						// modify file
						if (dims_isadmin() || dims_isactionallowed(0) || (dims_isactionallowed(_DOC_ACTION_MODIFYFILE) && !$docfolder_readonly_content) || $row['id_user'] == $_SESSION['dims']['userid']) {
							$href=dims_urlencode("$scriptenv?op=file_modify&currentfolder=".$currentfolder."&docfile_id=".$row['id']);
							dims_addOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FILE,$row['id'],$href,"","","modify","");
						}

						if (dims_isadmin() || dims_isactionallowed(0) || (dims_isactionallowed(_DOC_ACTION_DELETEFILE) && !$docfolder_readonly_content) || $row['id_user'] == $_SESSION['dims']['userid']) {
							$href="javascript:dims_confirmlink('".dims_urlencode("$scriptenv?op=file_delete&currentfolder=".$currentfolder."&docfile_id=".$row['id'])."','".$_DIMS['cste']['_DIMS_CONFIRM']."');";

							dims_addOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FILE,$row['id'],$href,"","","delete","");
						}
						else {
							$link="javascript:alert('".$_DIMS['cste']['_DOC_LABEL_UNAUTHORIZED_DELETEFILE']."');";
							dims_addOptions($row['id_workspace'],$row['id_module'],_DOC_OBJECT_FILE,$row['id'],"",$link,"","delete","");
						}


						// construction des favoris
						echo dims_displayOptions($_DIMS,$row['id_workspace'],$row['id_module'],_DOC_OBJECT_FILE,$row['id'],-250);

						// test si fichier compresse
						/*
						if ($row['extension']=="zip")
							echo "<a title=\"Extraire\" style=\"display:block;float:right;\" href=\"\"><img src=\"./common/modules/doc/img/ico_extract.png\" /></a>";
						else
							echo "<a title=\"T&eacute;l&eacute;charger (ZIP)\" style=\"display:block;float:right;\" href=\"".dims_urlencode("$scriptenv?op=file_download_zip&docfile_id={$row['id']}")."\"><img src=\"./common/modules/doc/img/ico_download_zip.png\" /></a>";
						*/

						?>
					</td>
				</tr>
			<?
			}
		}

		// DISPLAY DRAFT FOLDERS
		$draft_title = false;
		$param = array(':idfolder' => $currentfolder, ':idmodule' => $_SESSION['dims']['moduleid']);
		$where = (!empty($list_sharedfolder)) ? ' OR f.id IN ('.$db->getParamsFromArray($list_sharedfolder, 'sharedfolder', $param).')' : '';

		if (!$wf_validator) {
			$where .= " OR f.id_user = :userid ";
			$param[':userid'] = $_SESSION['dims']['userid'];
		} else {
			$where = '';
		}

		$sql =	"
				SELECT		f.*,
							u.login
				FROM		dims_mod_doc_folder f
				LEFT JOIN	dims_user u
				ON			f.id_user = u.id
				WHERE		f.id_folder = :idfolder
				AND			f.id_module = :idmodule
				AND			f.published = 0

				AND			(
							(f.foldertype = 'public' AND f.id_workspace IN (".dims_viewworkspaces()."))
							$where
							)
				ORDER BY	f.name
				";


		$res=$db->query($sql, $param );

		if ($db->numrows($res)) {
			$draft_title = true;
			?>
			<tr><td colspan="5" style="padding:2px 4px;background-color:#ffe0e0;font-weight:bold;">
			Fichiers en attente de publication:
			</td></tr>
			<?
		}

		while ($row = $db->fetchrow($res)) {
			$ldate = dims_timestamp2local($row['timestp_modify']);
			$readonly = (($row['readonly'] && $row['id_user'] != $_SESSION['dims']['userid']) || $docfolder_readonly_content);
			$color = (!isset($color) || $color == 2) ? 1 : 2;
			?>
			<tr class="trl<? echo $color; ?>">
				<td onclick="javascript:document.location.href='<? echo dims_urlencode("{$scriptenv}?op=browse&currentfolder={$row['id']}"); ?>'"><? echo $row['name']; ?></td>
				<td><? echo ($row['description']); ?></td>
				<td><img src="./common/modules/doc/img/ico_folder<? if ($row['foldertype'] == 'shared') echo '_shared'; ?><? if ($row['foldertype'] == 'public') echo '_public'; ?><? if ($row['readonly']) echo '_locked'; ?>.png" /></td>
				<td><? echo $row['nbelements']; ?> element(s)</td>
				<td><? echo $row['login']; ?></td>
				<td><? echo $ldate['date']; ?> <? echo $ldate['time']; ?></td>
				<td>
					<?
					if (dims_isadmin() || dims_isactionallowed(0) ||  dims_isactionallowed(_DOC_ACTION_DELETEFOLDER) && (!$readonly) && ($row['nbelements'] == 0))
					{
						?>
						<a title="Supprimer" style="display:block;float:right;" href="javascript:dims_confirmlink('<? echo dims_urlencode("{$scriptenv}?op=folder_delete&currentfolder={$currentfolder}&docfolder_id={$row['id']}"); ?>','<? echo $_DIMS['cste']['_DIMS_CONFIRM']; ?>');"><img src="./common/modules/doc/img/ico_trash.png" /></a>
						<?
					}
					else
					{
						?>
						<a style="display:block;float:right;" href="javascript:void(0);" onclick="javascript:alert('Vous ne disposez pas des autorisations n&eacute;cessaires pour supprimer ce dossier');"><img src="./common/img/delete.gif" /></a>
						<?
					}
					?>
					<?
					if ($wf_validator)
					{
						?>
						<a title="Publier" style="display:block;float:right;" href="javascript:dims_confirmlink('<? echo dims_urlencode("{$scriptenv}?op=folder_publish&currentfolder={$currentfolder}&docfolder_id={$row['id']}"); ?>','&eacute;tes-vous certain de vouloir publier ce dossier ?');"><img src="./common/modules/doc/img/ico_validate.png" /></a>
						<?
					}
					?>
					<a title="Modifier" style="display:block;float:right;" href="<? echo dims_urlencode("{$scriptenv}?op=folder_modify&currentfolder={$row['id']}"); ?>"><img src="./common/img/edit.gif" /></a>
				</td>
			</tr>
			<?
		}

		// DISPLAY DRAFT FILES
		if (!$wf_validator) $where = " AND f.id_user = {$_SESSION['dims']['userid']} ";
		else $where = '';

		$sql =	"
				SELECT		f.*,
							u.login,
							e.filetype,
							df.name as dfname

				FROM		dims_mod_doc_file_draft f

				LEFT JOIN	dims_user u
				ON			f.id_user = u.id

				LEFT JOIN	dims_mod_doc_ext e
				ON			e.ext = f.extension

				LEFT JOIN	dims_mod_doc_file df
				ON			df.id = f.id_docfile

				WHERE		f.id_folder = :idfolder
				AND			f.id_module = :idmodule
				$where

				ORDER BY	f.name
				";

		$res=$db->query($sql, array(':idfolder' => $currentfolder, ':idmodule' => $_SESSION['dims']['moduleid']) );

		if ($db->numrows($res) && !$draft_title) {
		?>
			<tr><td colspan="5" style="padding:2px 4px;background-color:#ffe0e0;font-weight:bold;">
			Fichiers en attente de publication:
			</td></tr>
		<?
		}

		while ($row = $db->fetchrow($res)) {
			// test si fichier existe
			$doc= new docfile();
			$doc->fields['id']=$row['id'];
			$doc->fields['timestp_create']=$row['timestp_create'];
			$doc->fields['timestp_create']=$row['timestp_create'];
			$doc->fields['id_module']=$row['id_module'];
			$doc->fields['version']=$row['version'];
			$doc->fields['extension']=$row['extension'];


			if (file_exists($doc->getfilepath())) {
				$ksize = sprintf("%.02f",$row['size']/1024);
				$ldate = dims_timestamp2local($row['timestp_create']);

				$color = (!isset($color) || $color == 2) ? 1 : 2;
				?>
				<tr class="trl<? echo $color; ?>">
				<td>
					<?
					if (file_exists(DIMS_APP_PATH . '/modules/doc/img/mimetypes/ico_'.$row['filetype'].'.png')) {
						?>
						<img src="./common/modules/doc/img/mimetypes/ico_<? echo $row['filetype']; ?>.png" />
						<?
					}
					else {
						?>
						<img src="./common/modules/doc/img/mimetypes/ico_default.png" />
						<?
					}
					?>
				</td>
				<td>
						<?
						echo $row['name'];
						if ($row['id_docfile']) {
							if ($row['dfname'] != $row['name']) echo " (nouvelle version de &laquo; {$row['dfname']} &raquo;)";
							else echo ' (nouvelle version)';
						}
						?>
				</td>
				<td style="text-align:right;"><? echo $ksize; ?> ko</td>
				<td><? echo $row['login']; ?></td>
				<td><? echo $ldate['date']; ?> <? echo $ldate['time']; ?></td>
				<td>
					<a title="Effacer" style="display:block;float:right;" href="javascript:dims_confirmlink('<? echo dims_urlencode("{$scriptenv}?op=filedraft_delete&currentfolder={$currentfolder}&docfiledraft_id={$row['id']}"); ?>','<? echo $_DIMS['cste']['_DIMS_CONFIRM']; ?>');"><img src="./common/modules/doc/img/ico_trash.png" /></a>
					<?
					if ($wf_validator)
					{
						?>
						<a title="Publier" style="display:block;float:right;" href="javascript:dims_confirmlink('<? echo dims_urlencode("{$scriptenv}?op=file_publish&currentfolder={$currentfolder}&docfile_id={$row['id']}"); ?>','&eacute;tes-vous certain de vouloir publier ce document ?');"><img src="./common/modules/doc/img/ico_validate.png" /></a>
						<?
					}
					?>
					<a title="T&eacute;l&eacute;charger" style="display:block;float:right;" href="<? echo dims_urlencode("{$scriptenv}?op=file_download&docfiledraft_id={$row['id']}"); ?>"><img src="./common/modules/doc/img/ico_download.png" /></a>
					<a title="T&eacute;l&eacute;charger (ZIP)" style="display:block;float:right;" href="<? echo dims_urlencode("{$scriptenv}?op=file_download_zip&docfiledraft_id={$row['id']}"); ?>"><img src="./common/modules/doc/img/ico_download_zip.png" /></a>
				</td>
				<a class="doc_explorer_link_<? echo $color; ?>" title="<? echo ($row['description']); ?>" href="<? echo dims_urlencode("{$scriptenv}?op=file_download&docfiledraft_id={$row['id']}"); ?>"></a>
				</tr>
				<?
			}
		}
	?>
</table>
<div class="">
	<div style="float:left;"><img src="./common/img/arrow_ltr.png" border="0" alt="0"></div>
	<div style="float:left;margin-top:4px;"><a href="#" onclick="checkAllFiles(<? echo $nbfilescheck; ?>);checkAllFolders(<? echo $nbfolderscheck; ?>);"><? echo $_DIMS['cste']['_ALLCHECK']; ?></a>
	&nbsp;/&nbsp;<a href="#" onclick="uncheckAllFiles(<? echo $nbfilescheck; ?>);uncheckAllFolders(<? echo $nbfolderscheck; ?>);"><? echo $_DIMS['cste']['_ALLUNCHECK']; ?></a></div>
	<div style="float:left;margin:0px 0px 0px 10px;"><? echo $_DIMS['cste']['_DOC_LABEL_OPERATION'];?>&nbsp;
		<select name="op" id="op" onchange="validCommand(event,<? echo $currentfolder; ?>,<? echo $nbfilescheck; ?>,<? echo $nbfolderscheck; ?>);">
			<option value=""></option>
			<option value="move"><? echo $_DIMS['cste']['_DOC_LABEL_MOVE'];?></option>
			<option value="delete"><? echo $_DIMS['cste']['_DELETE'];?></option>
		</select>
		<input type="hidden" id="iddestfolder" name="iddestfolder" value="0">
	</div>
</div>
</form>
<?
//require_once DIMS_APP_PATH . '/modules/doc/public_legend.php';
if (!empty($currentfolder))
{
	?>
	<div class="doc_folderannotations">
	<?
		require_once DIMS_APP_PATH.'include/functions/annotations.php';
		dims_annotation(_DOC_OBJECT_FOLDER, $docfolder->fields['id'], $docfolder->fields['name']);
		?>
	</div>
	<?
}
?>
