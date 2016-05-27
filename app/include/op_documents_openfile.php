<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$documentsfile = new documentsfile();

if (empty($_GET['documentsfile_id'])) {
	$documentsfile->init_description();
?>
	<div class="documents_formtitle">Nouveau Fichier</div>
<?
} else {
	$documentsfile->open(dims_load_securvalue('documentsfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
?>
	<div class="documents_formtitle">Modification du Fichier</div>
<?
}
?>
<form id="documents_folderform" action="admin-light.php" method="post" target="documents_fileform_iframe" enctype="multipart/form-data" onsubmit="javascript:return dims_documents_validate(this)">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("dims_op",		"documents_savefile");
		$token->field("currentfolder",	dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true));
		$token->field("documentsfile_label");
		$token->field("documentsfile_description");
	?>
	<input type="hidden" name="dims_op" value="documents_savefile">
	<input type="hidden" name="currentfolder" value="<? echo dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true); ?>">
<?
if (!empty($_GET['documentsfile_id'])) {
?>
	<input type="hidden" name="documentsfile_id" value="<? echo dims_load_securvalue('documentsfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true); ?>">
	<?
		$token->field("documentsfile_id", dims_load_securvalue('documentsfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
	?>
<?
}
?>
<div class="dims_form">
	<div class="documents_formcontent">
<?
if (empty($_GET['documentsfile_id'])) {
?>
	<p>
		<label>Fichier:</label>
		<input type="file" class="text" name="documentsfile_file">
		<?
			$token->field("documentsfile_file");
		?>
	</p>
<?
} else {
?>
	<p>
		<label>Nom du Fichier:</label>
		<input type="input" class="text" name="documentsfile_name" value="<? echo ($documentsfile->fields['name']); ?>">
		<?
			$token->field("documentsfile_name");
		?>
	</p>
<?
}
?>
<p>
	<label>Libell:</label>
	<input class="text" name="documentsfile_label" value="<? echo ($documentsfile->fields['label']); ?>">
</p>
<p>
	<label>Commentaire:</label>
	<textarea class="text" name="documentsfile_description"><? echo ($documentsfile->fields['description']); ?></textarea>
</p>
</div>
<div class="documents_formcontent" style="text-align:right;padding:4px;">
<input type="button" class="flatbutton" style="width:100px;" value="<? echo $_DIMS['cste']['_DIMS_LABEL_CANCEL']; ?>" onclick="javascript:dims_hidepopup();">
<input type="submit" class="flatbutton" style="width:100px;" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
</div>
</div>
<?
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
</form>
<iframe name="documents_fileform_iframe" src="./common/img/blank.gif" style="width:0;height:0;visibility:hidden;display:none;"></iframe>

