<div class="doc_fileform">
<?
$docfile = new docfile();

if ($op == 'file_add') {
	$docfile->init_description();
	?>
	<div class="doc_fileform_title"><?php echo $_DIMS['cste']['_DOC_NEWFILE']; ?></div>
	<?
}
else {
	if (isset($docfile_id)) $docfile->open($docfile_id);
	?>
	<div class="doc_fileform_title">Modification d'un Fichier</div>
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
					<a href="" style="width:140px;float:right;" class="doc_versionlog_element"><p>Modifi&eacute; le</p></a>
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
					<a class="doc_versionlog_link_<? echo $color; ?>" title="<? echo ("{$row['name']} ({$row['version']})"); ?>" href="<? echo "{$scriptenv}?op=file_download&docfile_id={$row['id_docfile']}&version={$row['version']}"; ?>">

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
			<input type="hidden" name="dims_op" value="doc_uploadsave_file">
			<input type="hidden" name="docfile_id" value="<? echo $docfile->fields['id']; ?>">
			<?
						$id_module=dims_load_securvalue('id_module',dims_const::_DIMS_NUM_INPUT,true,true);

						if ($id_module>0) {
							$_SESSION['dims']['tempdoc_moduleid']=$id_module;
						}
		}
		?>
		<div class="dims_form" style="padding:2px;">
			<?
			if ($op == 'file_add') {
				$folders = doc_getfolders($_SESSION['dims']['uploadfile']['id_module']);
				function echo_folder_select($fold,$parent,$decal){
					$select = '';
					foreach($fold['tree'][$parent] as $num => $key){
						$select .= '<option value="'.$key.'">'.$decal.'&nbsp;'.$fold['list'][$key]['name'].'</option>';
						$select .= '<option value="new_'.$key.'">'.$decal.$decal.'&nbsp; Nouveau dans '.$fold['list'][$key]['name'].'</option>';
						if (isset($fold['tree'][$key]))
							$select .= echo_folder_select($fold,$key,$decal.'&#151;');
					}
					return $select ;
				}
				//dims_print_r($folders);
				$select = '<select name="docfile_id_folder" id="docfile_id_folder" onchange="javascript: if (this.options[this.selectedIndex].value.substr(0,4) == \'new_\') document.getElementById(\'new_type_doc\').style.display=\'block\'; else document.getElementById(\'new_type_doc\').style.display=\'none\';">';
				foreach($folders['tree'][-1] as $tree){
					$select .= '<option value="'.$tree.'">'.$folders['list'][$tree]['name'].'</option>';
					$select .= '<option value="new_'.$tree.'">'.$decal.'&#151; Nouveau dans '.$folders['list'][$tree]['name'].'</option>';
					if (isset($folders['tree'][$tree]))
						$select .= echo_folder_select($folders,$tree,'&#151;');
				}
				$select .= '</select>';
				//dims_print_r($folders);
				?>
				<p>
					<label><?php echo $_DIMS['cste']['_DIMS_LABEL_FILE']; ?> :</label>
					<input type="file" class="text" name="docfile_file">
					&nbsp;(Version 1)
					<div id="select_type_fold">
						<label><? echo $_DIMS['cste']['_DOC_FOLDER']; ?> :</label>
						<? echo $select ; ?>
						<div id="new_type_doc" style="display:none;margin-left:39%;">
							<input type="text" id="val_new_type_doc"value="" style="width:175px;">
							<img onclick="javascript:if (document.getElementById('val_new_type_doc').value != '') dims_xmlhttprequest_todiv('admin.php','dims_op=doc_add_virtual_folder&name_fold='+document.getElementById('val_new_type_doc').value+'&parent='+document.getElementById('docfile_id_folder').options[document.getElementById('docfile_id_folder').selectedIndex].value.substr(4),'','select_type_fold');" src="./common/img/add.gif" title="Ajouter type" alt="Ajouter type" style="cursor:pointer;">
						</div>
					</div>
				</p>
				<p>
					<label><?php echo $_DIMS['cste']['_DIMS_COMMENTS']; ?> :</label>
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
