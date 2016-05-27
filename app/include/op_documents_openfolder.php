<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$documentsfolder = new documentsfolder();

if (empty($_GET['documentsfolder_id'])) {
	$documentsfolder->init_description();
?>
	<div class="documents_formtitle">Nouveau Dossier</div>
<?
} else {
	$documentsfolder->open(dims_load_securvalue('documentsfolder_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
?>
	<div class="documents_formtitle">Modification du Dossier</div>
<?
}
?>
<form id="documents_folderform" action="admin-light.php" method="post" target="documents_folderform_iframe" enctype="multipart/form-data">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("dims_op",		"documents_savefolder");
	$token->field("currentfolder",	dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true));
	$token->field("documentsfolder_name");
	$token->field("documentsfolder_description");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="hidden" name="dims_op" value="documents_savefolder">
<input type="hidden" name="currentfolder" value="<? echo dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true); ?>">
<?
if (!empty($_GET['documentsfolder_id'])) {
?>
	<input type="hidden" name="documentsfolder_id" value="<? echo dims_load_securvalue('documentsfolder_id', dims_const::_DIMS_NUM_INPUT, true, true, true); ?>">
	<?
		$token->field("documentsfolder_id", dims_load_securvalue('documentsfolder_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
	?>
<?
}
?>

<div class="dims_form">
	<div class="documents_formcontent">
		<p>
			<label>Libell:</label>
			<input type="text" class="text" name="documentsfolder_name" value="<? echo ($documentsfolder->fields['name']); ?>">
		</p>
		<p>
			<label>Commentaire:</label>
			<textarea class="text" name="documentsfolder_description"><? echo ($documentsfolder->fields['description']); ?></textarea>
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
<iframe name="documents_folderform_iframe" src="./common/img/blank.gif" style="width:0;height:0;visibility:hidden;display:none;"></iframe>

