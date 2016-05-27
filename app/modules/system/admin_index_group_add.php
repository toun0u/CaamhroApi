<?
echo $skin->open_simplebloc();

if (empty($group)) {
	$group = new group();
	$group->init_description();
	$parentlabel = $workspace->fields['label'];
}
else {
	$parentlabel = $group->fields['label'];
}

$token = new FormToken\TokenField;
$token->field("op",				"save_group");
$token->field("group_id_group",	$group->fields['id']);
$token->field("group_label");
$token->field("group_shared");

?>

<form name="form_group" action="<? echo $scriptenv; ?>" method="POST" onsubmit="javascript:return system_group_validate(this);">
<?php echo $token->generate(); ?>
<input type="hidden" name="op" value="save_group">
<input type="hidden" name="group_id_group" value="<? echo $group->fields['id']; ?>">

<div class="dims_form_title">
	<? echo $parentlabel; ?> &raquo; <? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_ADD']; ?>
</div>
<div class="dims_form" style="clear:both;padding:2px">
	<p>
		<label><? echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>:</label>
		<input type="text" class="text" name="group_label"  value="<? echo "fils de {$parentlabel}"; ?>">
	</p>
	<p>
		<label><? echo $_DIMS['cste']['_SHARE']; ?>:</label>
		<input style="width:16px;" type="checkbox" name="group_shared" value="1">(disponible pour les sous-espaces)
	</p>
</div>
<div style="clear:both;float:right;padding:4px;">
    <?php
        echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","javascript:forms.form_group.submit();");
    ?>
</div>
</form>
<? echo $skin->close_simplebloc(); ?>
