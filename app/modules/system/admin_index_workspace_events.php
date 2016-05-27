<form id="form_workspace" name="form_workspace" action="<?php echo $scriptenv; ?>" method="POST" enctype="multipart/form-data">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",				"save_workspace_events");
	$token->field("workspace_id",	$workspace->fields['id']);
	$token->field("workspace_events_sender_email");
	$token->field("workspace_events_mail1_subject");
	$token->field("fck_workspace_events_mail1_content");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="hidden" name="op" value="save_workspace_events">
<input type="hidden" name="workspace_id" value="<?php echo $workspace->fields['id']; ?>">

	<div class="dims_form_title">
		<?php echo $workspace->fields['label']; ?> &raquo;
		<?php
		echo $_DIMS['cste']['_DIMS_LABEL_WORKSPACE_MODIFY'];
		?>
	</div>
	<div class="dims_form" style="clear:both;padding:2px;">
		<p>
			<label><?php echo $_DIMS['cste']['_EVENTS_SENDER_EMAIL']; ?></label>
			<input type="text" class="text" name="workspace_events_sender_email"  value="<?php echo $workspace->fields['events_sender_email']; ?>">
		</p>
		<p>
			<label style="width:100%;text-align:left;"><?php echo $_DIMS['cste']['_EVENTS_MAIL_TO_ORG_INSC_NIV1']  ?></label>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_SUBJECT'] ?></label>
			<input type="text" class="text" name="workspace_events_mail1_subject"  value="<?php echo $workspace->fields['events_mail1_subject']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_CONTENT'] ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_mail1_content"><?php echo html_entity_decode($workspace->fields['events_mail1_content']); ?></textarea>
		</p>
	</div>
	<div style="clear:both;float:right;padding:4px;">
		<input type="submit" class="flatbutton" value="<?php echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
	</div>
</form>
