<?php
require_once(DIMS_APP_PATH . '/modules/system/class_workspace.php');

$workspace = new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);


?>
<form id="form_workspace" name="form_workspace" action="admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public" method="POST" enctype="multipart/form-data">
<input type="hidden" name="action" value="save_workspace_events">
<input type="hidden" name="workspace_id" value="<?php echo $workspace->fields['id']; ?>">
	<div class="dims_form" style="clear:both;padding:2px;">
		<p>
			<label><?php echo $_DIMS['cste']['_EVENTS_SENDER_EMAIL']; ?></label>
			<input type="text" class="text" name="workspace_events_sender_email"  value="<?php echo $workspace->fields['events_sender_email']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_DIMS_LABEL_SIGNATURE']; ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_signature"><?php echo html_entity_decode($workspace->fields['events_signature']); ?></textarea>
		</p>
				<p>
			<label><?php echo $_DIMS['cste']['_EVENTS_SENDER_EMAIL'].' Fairs'; ?></label>
			<input type="text" class="text" name="workspace_events_sender_email_fairs"	value="<?php echo $workspace->fields['events_sender_email_fairs']; ?>">
		</p>
				<p>
			<label><?php echo $_DIMS['cste']['_DIMS_LABEL_SIGNATURE'].' Fairs'; ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_signature_fairs"><?php echo html_entity_decode($workspace->fields['events_signature_fairs']); ?></textarea>
		</p>
		<p>
			<label style="width:95%;text-align:left;font-weight:bold;"><?php echo $_DIMS['cste']['_EVENTS_MAIL_TO_ORG_INSC_NIV1']  ?></label>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_SUBJECT'] ?></label>
			<input type="text" class="text" name="workspace_events_mail1_subject"  value="<?php echo $workspace->fields['events_mail1_subject']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_CONTENT'] ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_mail1_content"><?php echo html_entity_decode($workspace->fields['events_mail1_content']); ?></textarea>
		</p>
		<p>
			<label style="width:95%;text-align:left;font-weight:bold;"><?php echo $_DIMS['cste']['_EVENTS_MAIL_TO_DMD_INSC_NIV1']  ?></label>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_SUBJECT'] ?></label>
			<input type="text" class="text" name="workspace_events_mail2_subject"  value="<?php echo $workspace->fields['events_mail2_subject']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_CONTENT'] ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_mail2_content"><?php echo html_entity_decode($workspace->fields['events_mail2_content']); ?></textarea>
		</p>
		<p>
			<label style="width:95%;text-align:left;font-weight:bold;"><?php echo $_DIMS['cste']['_EVENTS_MAIL_VALID_INSC_NIV1']  ?></label>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_SUBJECT'] ?></label>
			<input type="text" class="text" name="workspace_events_mail3_subject"  value="<?php echo $workspace->fields['events_mail3_subject']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_CONTENT'] ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_mail3_content"><?php echo html_entity_decode($workspace->fields['events_mail3_content']); ?></textarea>
		</p>
		<p>
			<label style="width:95%;text-align:left;font-weight:bold;"><?php echo $_DIMS['cste']['_EVENTS_MAIL_UNVALID_INSC_NIV1']	?></label>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_SUBJECT'] ?></label>
			<input type="text" class="text" name="workspace_events_mail4_subject"  value="<?php echo $workspace->fields['events_mail4_subject']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_CONTENT'] ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_mail4_content"><?php echo html_entity_decode($workspace->fields['events_mail4_content']); ?></textarea>
		</p>
		<p>
			<label style="width:95%;text-align:left;font-weight:bold;"><?php echo $_DIMS['cste']['_EVENTS_MAIL_VALID_INSC_NIV2']  ?></label>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_SUBJECT'] ?></label>
			<input type="text" class="text" name="workspace_events_mail5_subject"  value="<?php echo $workspace->fields['events_mail5_subject']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_CONTENT'] ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_mail5_content"><?php echo html_entity_decode($workspace->fields['events_mail5_content']); ?></textarea>
		</p>
		<p>
			<label style="width:95%;text-align:left;font-weight:bold;"><?php echo $_DIMS['cste']['_EVENTS_MAIL_DEPOT_DOC']	?></label>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_SUBJECT'] ?></label>
			<input type="text" class="text" name="workspace_events_mail6_subject"  value="<?php echo $workspace->fields['events_mail6_subject']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_CONTENT'] ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_mail6_content"><?php echo html_entity_decode($workspace->fields['events_mail6_content']); ?></textarea>
		</p>
		<p>
			<label style="width:95%;text-align:left;font-weight:bold;"><?php echo $_DIMS['cste']['_EVENTS_MAIL_REJET_DOC']	?></label>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_SUBJECT'] ?></label>
			<input type="text" class="text" name="workspace_events_mail7_subject"  value="<?php echo $workspace->fields['events_mail7_subject']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_CONTENT'] ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_mail7_content"><?php echo html_entity_decode($workspace->fields['events_mail7_content']); ?></textarea>
		</p>
		<p>
			<label style="width:95%;text-align:left;font-weight:bold;"><?php echo $_DIMS['cste']['_EVENTS_MAIL_ACCEPT_STEP']  ?></label>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_SUBJECT'] ?></label>
			<input type="text" class="text" name="workspace_events_mail8_subject"  value="<?php echo $workspace->fields['events_mail8_subject']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_CONTENT'] ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_mail8_content"><?php echo html_entity_decode($workspace->fields['events_mail8_content']); ?></textarea>
		</p>
		<p>
			<label style="width:95%;text-align:left;font-weight:bold;"><?php echo $_DIMS['cste']['_EVENTS_MAIL_NOT_ACCEPT_STEP']  ?></label>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_SUBJECT'] ?></label>
			<input type="text" class="text" name="workspace_events_mail9_subject"  value="<?php echo $workspace->fields['events_mail9_subject']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_CONTENT'] ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_mail9_content"><?php echo html_entity_decode($workspace->fields['events_mail9_content']); ?></textarea>
		</p>
		<p>
			<label style="width:95%;text-align:left;font-weight:bold;"><?php echo $_DIMS['cste']['_EVENTS_MAIL_NIV2_VALIDATION']  ?></label>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_SUBJECT'] ?></label>
			<input type="text" class="text" name="workspace_events_mail10_subject"	value="<?php echo $workspace->fields['events_mail10_subject']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_CONTENT'] ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_mail10_content"><?php echo html_entity_decode($workspace->fields['events_mail10_content']); ?></textarea>
		</p>
		<p>
			<label style="width:95%;text-align:left;font-weight:bold;"><?php echo $_DIMS['cste']['_EVENTS_MAIL_ALERTE_MOD']  ?></label>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_SUBJECT'] ?></label>
			<input type="text" class="text" name="workspace_events_mail11_subject"	value="<?php echo $workspace->fields['events_mail11_subject']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_CONTENT'] ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_events_mail11_content"><?php echo html_entity_decode($workspace->fields['events_mail11_content']); ?></textarea>
		</p>
	</div>
	<div style="clear:both;float:right;padding:4px;">
		<input type="submit" class="flatbutton" value="<?php echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
	</div>
</form>
