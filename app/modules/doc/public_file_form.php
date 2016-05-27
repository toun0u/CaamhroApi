<?
if (!isset($_SESSION['dims']['uploadfile']['id_record']))
	require_once DIMS_APP_PATH . '/modules/doc/public_folder_info.php';
//else {
//	$currentfolder=-1;
//}
?>

<div class="doc_fileform">
<?
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

	if (!empty($docfile->fields['id_folder']))
	{
		$docfolder_parent = new docfolder();
		$docfolder_parent->open($docfile->fields['id_folder']);
		$docfolder_readonly_content = ($docfolder_parent->fields['readonly_content'] && $docfolder_parent->fields['id_user'] != $_SESSION['dims']['userid']);
	}

	$readonly = !(dims_isadmin() || dims_isactionallowed(0) || dims_isactionallowed(_DOC_ACTION_MODIFYFILE) && (!$docfolder_readonly_content || $docfile->fields['id_user'] == $_SESSION['dims']['userid']));
	if ($readonly) {
		?>
		<div class="doc_fileform_title">Consultation d'un Fichier (lecture seule)</div>
		<?
	}
	else {
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

			<div style="left:40px;" class="doc_versionlog_column"></div>

			<div style="position:relative;">
				<div class="doc_versionlog_title">
					<a href="" style="width:20px;float:right;" class="doc_versionlog_element"><p>&nbsp;</p></a>
					<a href="" style="width:80px;float:right;" class="doc_versionlog_element"><p>Taille</p></a>
					<a href="" style="width:100px;float:right;" class="doc_versionlog_element"><p>Par</p></a>
					<a href="" style="width:140px;float:right;" class="doc_versionlog_element"><p>Modifie le</p></a>
					<a href="" style="width:40px;float:left;" class="doc_versionlog_element"><p>Vers.</p></a>
					<a href="" style="overflow:auto;" class="doc_versionlog_element"><p>Fichier</p></a>
				</div>
			</div>

			<?
			//$select = "SELECT * FROM dims_mod_doc_file_history";
			$history = $docfile->gethistory();

			//dims_print_r($history);
			foreach($history as $row) {
				$color = (!isset($color) || $color == 2) ? 1 : 2;
				$ldate_modify = (!empty($row['timestp_modify'])) ? dims_timestamp2local($row['timestp_modify']) : array('date' => '', 'time' => '');
				?>
				<div class="doc_versionlog_line">
					<div class="doc_versionlog_tools" style="width:20px;">&nbsp;</div>
					<a class="doc_versionlog_link_<? echo $color; ?>" title="<? echo ("{$row['name']} ({$row['version']})"); ?>" href="<? echo "{$scriptenv}?op=file_download&docfile_md5id={$row['md5id']}&version={$row['version']}"; ?>">

						<div style="width:80px;float:right;" class="doc_versionlog_element"><p><? printf("%0.2f kio", ($row['size']/1024)); ?></p></div>
						<div style="width:100px;float:right;" class="doc_versionlog_element"><p><? echo $row['login']; ?></p></div>
						<div style="width:140px;float:right;" class="doc_versionlog_element"><p><? echo "{$ldate_modify['date']} {$ldate_modify['time']}"; ?></p></div>

						<div style="width:40px;float:left;text-align:right;" class="doc_versionlog_element"><p><? echo $row['version']; ?></p></div>

						<div style="overflow:auto;" class="doc_versionlog_element"><p><? echo $row['name']; ?></p></div>
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
		if (!isset($readonly) || !$readonly) {
			?>
			<form name="docfile_add" action="<? echo $scriptenv; ?>" method="post" enctype="multipart/form-data"  onsubmit="javascript:return doc_file_validate(this,<? echo ($op == 'file_add') ? 'true' : 'false'; ?>,<? echo (!empty($wfusers) && !$wf_validator) ? 'true' : 'false'; ?>);">
			<input type="hidden" name="op" value="file_save">
			<input type="hidden" name="currentfolder" value="<? echo $currentfolder; ?>">
			<input type="hidden" name="docfile_id" value="<? echo $docfile->fields['id']; ?>">
			<?
		}
		?>
		<div class="dims_form" style="padding:2px;">
			<?
			if ($op == 'file_add') {
				?>
				<p>
					<label>Fichier:</label>
					<input type="file" class="text" name="docfile_file">
					<span>&nbsp;(Version 1)</span>
				</p>
				<p>
					<label>Commentaire:</label>
					<textarea class="text" name="docfile_description"><? echo ($docfile->fields['description']); ?></textarea>
				</p>
				<?
			}
			else {
				require_once DIMS_APP_PATH . '/modules/system/class_user.php';

				$user = new user();
				$user->open($docfile->fields['id_user']);

				$user_modify = new user();
				$user_modify->open($docfile->fields['id_user_modify']);
				$ldate_modify = (!empty($docfile->fields['timestp_modify'])) ? dims_timestamp2local($docfile->fields['timestp_modify']) : array('date' => '', 'time' => '');
				//echo $user->fields['login'];
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
					<label>Propri&acute;taire:</label>
					<span><? echo $user->fields['login']; ?></span>
				</p>
				<p>
					<label>Modifi&eacute; par:</label>
					<span><? echo $user_modify->fields['login']; ?></span>
				</p>
				<p>
					<label>Derni&egrave;re modification:</label>
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
						<input type="file" class="text" name="docfile_file">
					</p>
					<?
				}
			}
			?>
		</div>
		<div style="clear:both;float:right;padding:4px;">
			<?
			if (!isset($_SESSION['dims']['uploadfile']['id_record'])) {
			?>
			<input type="button" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_BACK']; ?>" onclick="javavscript:document.location.href='<? echo "{$scriptenv}?op=browser&currentfolder={$currentfolder}"; ?>';">
			<?
			}
			else {
			?>
			<input type="button" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_CLOSE']; ?>" onclick="javavscript:dims_hidepopup();">
			<?
			}
			if (!isset($readonly) || !$readonly) {
				?>
				<input type="submit" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
				<?
			}
			?>
		</div>
		<?
		if (!isset($readonly) || !$readonly) {
			?>
			</form>
			<?
		}
		?>
	</div>
</div>

<?
if ($op != 'file_add') {
	?>
	<div class="doc_folderannotations">
	<?
		require_once DIMS_APP_PATH.'include/functions/annotations.php';
		dims_annotation(_DOC_OBJECT_FILE, $docfile->fields['id'], $docfile->fields['name']);
		?>
	</div>
	<?
}
?>
