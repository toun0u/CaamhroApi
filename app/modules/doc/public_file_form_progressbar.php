<link type="text/css" rel="stylesheet" href="./common/modules/doc/include/styles.css" media="screen" />
<?
require_once DIMS_APP_PATH . '/include/functions/shares.php';
require_once DIMS_APP_PATH . '/modules/doc/public_folder_info.php';
?>

<div class="doc_fileform">
<?

// on supprime ce qu'il peut y avoir en temporary
$sid = sha1(uniqid(""). MD5(microtime()));
$temp_dir = DIMS_TMP_PATH;
$session_dir = $temp_dir."/".$sid;

if (file_exists($session_dir)) dims_deletedir($session_dir);
dims_makedir($session_dir);

$upload_dir = _DIMS_PATHDATA."/uploads/".$sid."/";
if (!is_dir($upload_dir)) dims_makedir ($upload_dir);

$_SESSION['dims']['uploaded_sid']=$sid;

$upload_size_file = $session_dir."/upload_size";
$upload_finished_file = $session_dir."/upload_finished";

if (file_exists($upload_size_file)) unlink($upload_size_file);
if (file_exists($upload_finished_file)) unlink($upload_finished_file);



$docfile = new docfile();

if ($op == 'file_add') {

	$docfile->init_description();
	?>
	<div class="doc_fileform_title">Nouveau Fichier</div>
	<?
}
else {
	$docfile_id=dims_load_securvalue('docfile_id',dims_const::_DIMS_NUM_INPUT,true,true);
	if (isset($docfile_id)) $docfile->open($docfile_id);

	// on v�rifie que l'utilisateur a bien le droit de supprimer ce dossier (en fonction du statut du dossier et du dossier parent)
	$docfolder_readonly_content = false;

	if (!empty($docfile->fields['id_folder'])) {
		$docfolder_parent = new docfolder();
		$docfolder_parent->open($docfile->fields['id_folder']);
		$docfolder_readonly_content = ($docfolder_parent->fields['readonly_content'] && $docfolder_parent->fields['id_user'] != $_SESSION['dims']['userid']);
	}

	$readonly = !(dims_isactionallowed(_DOC_ACTION_MODIFYFILE) && (!$docfolder_readonly_content || $docfile->fields['id_user'] == $_SESSION['dims']['userid']));
	if ($readonly)
	{
		?>
		<div class="doc_fileform_title">Consultation d'un Fichier (lecture seule)</div>
		<?
	}
	else
	{
		?>
		<div class="doc_fileform_title">Modification d'un Fichier</div>
		<?
	}
	?>
	<div class="doc_versionlog_main">

		<div class="doc_versionlog_maintitle">Historique des versions</div>
		<div class="doc_versionlog_main2">
			<div style="right:20px;" class="doc_versionlog_column"></div>
			<div style="right:100px;" class="doc_versionlog_column"></div>
			<div style="right:200px;" class="doc_versionlog_column"></div>
			<div style="right:340px;" class="doc_versionlog_column"></div>

			<div style="left:35px;" class="doc_versionlog_column"></div>

			<div style="position:relative;">
				<div class="doc_versionlog_title">
					<a href="" style="width:20px;float:right;" class="doc_versionlog_element">&nbsp;</a>
					<a href="" style="width:80px;float:right;" class="doc_versionlog_element">Taille</a>
					<a href="" style="width:100px;float:right;" class="doc_versionlog_element">Par</a>
					<a href="" style="width:140px;float:right;" class="doc_versionlog_element">Modifi� le</a>
					<a href="" style="width:40px;float:left;" class="doc_versionlog_element">Vers.</a>
					<a href="" style="overflow:auto;" class="doc_versionlog_element">Fichier</a>
				</div>
			</div>
			<?
			$history = $docfile->gethistory();

			foreach($history as $row) {
				$color = (!isset($color) || $color == 2) ? 1 : 2;
				$ldate_modify = (!empty($row['timestp_modify'])) ? dims_timestamp2local($row['timestp_modify']) : array('date' => '', 'time' => '');
				?>
				<div class="doc_versionlog_line">
					<div class="doc_versionlog_tools" style="width:20px;">&nbsp;</div>
					<a class="doc_versionlog_link_<? echo $color; ?>" title="<? echo ("{$row['name']} ({$row['version']})"); ?>" href="<? echo "{$scriptenv}?op=file_download&docfile_md5id={$row['md5id']}&version={$row['version']}"; ?>">

						<div style="width:80px;float:right;" class="doc_versionlog_element"><? printf("%0.2f kio", ($row['size']/1024)); ?></div>
						<div style="width:100px;float:right;" class="doc_versionlog_element"><? echo $row['login']; ?></div>
						<div style="width:140px;float:right;" class="doc_versionlog_element"><? echo "{$ldate_modify['date']} {$ldate_modify['time']}"; ?></div>

						<div style="width:40px;float:left;text-align:left;" class="doc_versionlog_element"><? echo $row['version']; ?></div>

						<div style="overflow:auto;" class="doc_versionlog_element"><? echo $row['name']; ?></div>
					</a>
				</div>
				<?
			}
			?>
		</div>
	</div>
	<?
}
?>

	<div class="doc_fileform_main">
		<?
		if (!$readonly && $_SESSION['dims']['connected']) {
			?>
			<script type="text/javascript">
				var uploads = new Array();
				var upload_cell, file_name;
				var count=0;
				var checkCount = 0;
				var check_file_extentions = true;
				var sid = '<? echo $_SESSION['dims']['uploaded_sid'] ; ?>';
				var page_elements = ["toolbar","page_status_bar"];
				var img_path = "../common/img/";
				var path = "";
				var bg_color = false;
				var status;
				var debug = false;
				var param1=<? echo ($op == 'file_add') ? 'true' : 'false'; ?>;
				var param2=<? echo (!empty($wfusers) && !$wf_validator) ? 'true' : 'false'; ?>;
			</script>
			<script type="text/javascript" src="/common/js/upload/javascript/uploader.js"></script>
			<script type="text/javascript">
				//window.onresize = function() { checkPage("ScrollBox",page_elements);}
				//document.oncontextmenu = document.onselectstart = function () { return false; }
			</script>

			<form id="docfile_add" name="docfile_add" action="<? echo $scriptenv; ?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="op" value="file_save_progress">
			<input type="hidden" name="currentfolder" value="<? echo $currentfolder; ?>">
			<input type="hidden" name="docfile_id" value="<? echo $docfile->fields['id']; ?>">
			<?
		}
		?>

		<div class="dims_form" style="padding:2px;">
			<?
			if ($op == 'file_add') {
				?>
				<input type="button" class="flatbutton" style="width:200px;" name="addfile" onclick="javascript:createFileInput();" value="<? echo $_DIMS['cste']['_DOC_LABEL_ADD_OTHER_FILE']; ?>">

				<div id="ScrollBox" style="overflow:auto;">
					<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
					<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:visible;height:250px;width:750px;" src=""></iframe>
				</div>

				<?
			}
			else {
				require_once DIMS_APP_PATH . '/modules/system/class_user.php';

				$user = new user();
				$user->open($docfile->fields['id_user']);

				$user_modify = new user();
				$user_modify->open($docfile->fields['id_user_modify']);
				$ldate_modify = (!empty($docfile->fields['timestp_modify'])) ? dims_timestamp2local($docfile->fields['timestp_modify']) : array('date' => '', 'time' => '');
				?>
				<p>
					<label>Nom du Fichier:</label>
					<?
					if ($readonly) echo ($docfile->fields['name']);
					else {
						?>
						<input type="text" class="text" name="docfile_name" value="<? echo ($docfile->fields['name']); ?>">
						<?
					}
					?>
				</p>
				<p>
					<label>Version:</label>
					<span><? echo $docfile->fields['version']; ?></span>
				</p>
				<p>
					<label>Taille:</label>
					<span><? printf("%0.2f kio", ($docfile->fields['size']/1024)); ?></span>
				</p>
				<p>
					<label>Propri&eacute;taire:</label>
					<span><? echo $user->fields['login']; ?></span>
				</p>
				<p>
					<label>Modifi&eacute; par:</label>
					<span><? echo $user_modify->fields['login']; ?></span>
				</p>
				<p>
					<label>Derniere modification:</label>
					<span><? echo "{$ldate_modify['date']} {$ldate_modify['time']}"; ?></span>
				</p>
				<p>
					<label>Commentaire:</label>
					<?
					if ($readonly) echo dims_nl2br(($docfile->fields['description']));
					else {
						?>
						<textarea class="text" name="docfile_description"><? echo ($docfile->fields['description']); ?></textarea>
						<?
					}
					?>
				</p>
				<?
				if (!$readonly) {
					?>
					<p>
						<label>D&eacute;poser une nouvelle Version:</label>

						<div id="ScrollBox" style="overflow:auto;">
							<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody><tr><td></td></tr></tbody></table>
							<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:hidden;display:none;" src=""></iframe>
						</div>
					</p>
					<?
				}
			}
			?>
		</div>
	</div>
</div>
		<?
		if (!$readonly) {
		?>
		<div id="doc_share" style="clear:both;">
			<?
			if ($docfile->fields['id']>0) {
				dims_shares_selectusers(_DOC_OBJECT_FILE, $docfile->fields['id']);
				echo "<div><input type=\"submit\" class=\"flatbutton\" value=\"".$_DIMS['cste']['_DIMS_SAVE']."\"></div>";
			//else
			//	  dims_shares_selectusers(_DOC_OBJECT_FILE, 0);
			}
			?>
		</div>

		<?
		}
		else echo '<div id="doc_share" style="margin:0;padding:0;visibility:hidden;"></div>';
		?>

		<div style="clear:both;float:right;padding:4px;">
			<input type="button" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_BACK']; ?>" onclick="javavscript:document.location.href='<? echo "{$scriptenv}?op=browser&currentfolder={$currentfolder}"; ?>';">
			<?
			if (!$readonly) {
				?>
				<span id="btn_upload"><input type="button" class="flatbutton" onclick="javascript:upload();" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>"></span>
				<?
			}
			?>
		</div>
<?
if (!$readonly)
{
	?>
	</form>
	<?
}

$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';

if ($op != 'file_add') {
?>
	<div class="doc_folderannotations" style="clear:both;width:100%;">
	<?
		require_once DIMS_APP_PATH.'include/functions/annotations.php';
		dims_annotation(_DOC_OBJECT_FILE, $docfile->fields['id'], $docfile->fields['name']);
		?>
	</div>
<?
}
global $dims;
$rootpath=$dims->getProtocol().$http_host;
echo "<script type=\"text/javascript\">status = document.getElementById(\"status\");setVariables(\"$rootpath\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_FILE']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_WAITING']."\",\"".$_DIMS['cste']['_DOC_MSG_COPY_FILE']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_ERROR']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_ERROREXT']."\");createFileInput(path);</script>";

?>
