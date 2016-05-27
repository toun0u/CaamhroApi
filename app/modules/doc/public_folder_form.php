<?
require_once DIMS_APP_PATH . '/include/functions/shares.php';
require_once DIMS_APP_PATH . '/modules/doc/public_folder_info.php';
?>

<div class="doc_fileform">
	<?
	$docfolder = new docfolder();

	if ($op == 'folder_add') {
		$docfolder->init_description();
		$docfolder->fields['foldertype'] = 'public';
		$readonly = false;
		?>
		<div class="doc_fileform_title">Nouveau Dossier</div>
		<?
	}
	else {
		if (isset($currentfolder)) $docfolder->open($currentfolder);

		// on vérifie que l'utilisateur a bien le droit de modifier ce dossier (en fonction du statut du dossier et du dossier parent)
		$docfolder_readonly_content = false;

		if (!empty($docfolder->fields['id_folder'])) {
			$docfolder_parent = new docfolder();
			$docfolder_parent->open($docfolder->fields['id_folder']);
			$docfolder_readonly_content = ($docfolder_parent->fields['readonly_content'] && $docfolder_parent->fields['id_user'] != $_SESSION['dims']['userid']);
		}

		$readonly = (($docfolder->fields['readonly'] && $docfolder->fields['id_user'] != $_SESSION['dims']['userid']) || $docfolder_readonly_content || !(dims_isactionallowed(_DOC_ACTION_MODIFYFOLDER)  || dims_isactionallowed(0) || dims_isadmin()));

		if (dims_isactionallowed(0) || dims_isadmin()) {
			$readonly=false;
		}

		if ($readonly)
		{
			?>
			<div class="doc_fileform_title">Consultation d'un Dossier (lecture seule)</div>
			<?
		}
		else
		{
			?>
			<div class="doc_fileform_title">Modification d'un Dossier</div>
			<?
		}
	}
	?>

	<div class="doc_fileform_main">
		<?
		if (!$readonly)
		{
			?>
			<form name="docfolder_form" action="<? echo $scriptenv; ?>" method="post" enctype="multipart/form-data" onsubmit="javascript:return doc_folder_validate(this, <? echo (!empty($wfusers) && !$wf_validator) ? 'true' : 'false'; ?>);">
			<input type="hidden" name="op" value="folder_save">
			<input type="hidden" name="currentfolder" value="<? echo $currentfolder; ?>">
			<?
			if ($op == 'folder_modify')
			{
				?>
				<input type="hidden" name="docfolder_id" value="<? echo $currentfolder; ?>">
				<?
			}
		}
		?>

		<div class="dims_form" style="float:left;width:40%;">
			<div style="padding:2px;">
				<p>
					<label>Nom du Dossier:</label>
					<?
					if ($readonly)
					{
						?>
						<span><? echo ($docfolder->fields['name']); ?></span>
						<?
					}
					else
					{
						?>
						<input type="text" class="text" name="docfolder_name" id="docfolder_name" value="<? echo ($docfolder->fields['name']); ?>">
						<?
					}
					?>
				</p>
				<p>
					<label>Type de Dossier:</label>
					<?
					if ($readonly)
					{
						?>
						<span><? echo ($foldertypes[$docfolder->fields['foldertype']]); ?></span>
						<?
					}
					else {

						// on verifie le type
						if ($docfolder->fields['id']>0) {
							$res=$db->query("select id from dims_mod_doc_gallery where id_folder= :idfolder", array(':idfolder' => $docfolder->fields['id']) );

							if ($db->numrows($res)>0) $typef='gallery';
							else $typef=$docfolder->fields['foldertype'];
						}
						else {
							$typef=$docfolder->fields['foldertype'];
						}
						?>
						<select onchange="javascript:docFolderCheck();" class="select" id="docfolder_foldertype" name="docfolder_foldertype" onchange="javascript:dims_getelem('doc_share').style.display = (this.value == 'shared') ? 'block' : 'none'; dims_getelem('doc_workflow').style.display = (this.value == 'private') ? 'none' : 'block';">
							<?
							foreach($foldertypes as $key => $value) {
							?>
								<option <? if ($typef == $key) echo 'selected'; ?> value="<? echo $key; ?>"><? echo $value; ?></option>
							<?
							}
							?>
						</select>
						<?
					}
					?>
				</p>
								<div id="div_networkpath" style="display:none;">
									<p>
											<label>Chemin réseau :</label>
											<?
											if ($readonly) {
													?>
													<span><? echo ($docfolder->fields['networkpath']); ?></span>
													<?
											}
											else
											{
													?>
													<input type="text" class="text" name="docfolder_networkpath" value="<? echo ($docfolder->fields['networkpath']); ?>">
													<?
											}
											?>
									</p>
								</div>
				<p>
					<label>Conteneur en Lecture seule:</label>
					<?
					if ($readonly)
					{
						?>
						<span><? echo ($docfolder->fields['readonly']) ? 'oui' : 'non'; ?></span>
						<?
					}
					else
					{
						?>
						<input type="checkbox" name="docfolder_readonly" value="1" <? if ($docfolder->fields['readonly']) echo 'checked'; ?>>
						<?
					}
					?>
				</p>
				<p>
					<label>Contenu en Lecture seule:</label>
					<?
					if ($readonly)
					{
						?>
						<span><? echo ($docfolder->fields['readonly_content']) ? 'oui' : 'non'; ?></span>
						<?
					}
					else
					{
						?>
						<input type="checkbox" name="docfolder_readonly_content" value="1" <? if ($docfolder->fields['readonly_content']) echo 'checked'; ?>>
						<?
					}
					?>
				</p>
			</div>
		</div>
		<div class="dims_form" style="float:left;width:58%;">
			<div style="padding:2px;">
				<p>
					<label>Commentaire:</label>
					<?
					if ($readonly)
					{
						?>
						<span><? echo dims_nl2br(($docfolder->fields['description'])); ?></span>
						<?
					}
					else
					{
						?>
						<textarea class="text" name="docfolder_description"><? echo ($docfolder->fields['description']); ?></textarea>
						<?
					}
					?>
				</p>
			</div>
		</div>

		<?
		if (!$readonly && dims_isactionallowed(_DOC_ACTION_WORKFLOW_MANAGE))
		{

			?>
			<div id="doc_workflow" style="clear:both;<? echo ($docfolder->fields['foldertype'] == 'private') ? 'display:none;' : 'display:block;'; ?>">
				<?
				//require_once(DIMS_APP_PATH . '/include/functions/workflow.php');
				if ($docfolder->fields['id']>0) {
					require_once DIMS_APP_PATH . '/include/functions/workflow.php';
					dims_workflow_selectusers(_DOC_OBJECT_FOLDER, ($op == 'folder_add') ? '' : $docfolder->fields['id']);
				}
				?>
			</div>
			<?
		}
		else echo '<div id="doc_workflow" style="margin:0;padding:0;visibility:hidden;"></div>';

		if (!$readonly)
		{
			?>
			<div id="doc_share" style="clear:both;<? echo ($docfolder->fields['foldertype'] == 'shared') ? 'display:block;' : 'display:none;'; ?>">
				<?
				if ($docfolder->fields['id']>0)
					dims_shares_selectusers(_DOC_OBJECT_FOLDER, $docfolder->fields['id']);
				else
					dims_shares_selectusers(_DOC_OBJECT_FOLDER, 0);
				?>
			</div>
			<?
		}
		else echo '<div id="doc_share" style="margin:0;padding:0;visibility:hidden;"></div>';
		?>

		<div style="clear:both;float:right;padding:4px;">
			<input type="button" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_BACK']; ?>" onclick="javavscript:document.location.href='<? echo "{$scriptenv}?op=browser&currentfolder={$currentfolder}"; ?>';">
			<?
			if (!$readonly)
			{
				?>
				<input type="submit" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
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
		?>
	</div>
</div>
<script language="JavaScript" type="text/JavaScript">

function docFolderCheck() {

	if ($('#docfolder_foldertype').val()=='network') $('#div_networkpath').css('display','block');
	else $('#div_networkpath').css('display', 'none');
}
$("#docfolder_name").focus();
docFolderCheck();
</script>
<?
if ($op != 'folder_add') {
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
