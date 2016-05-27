<form id="form_workspace" name="form_workspace" action="<?php echo $scriptenv; ?>" method="POST" enctype="multipart/form-data">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",				"save_workspace_newsletter");
	$token->field("workspace_id",	$workspace->fields['id']);
	$token->field("workspace_newsletter_id_domain");
	$token->field("workspace_newsletter_sender_email");
	$token->field("fck_workspace_newsletter_header_registration");
	$token->field("fck_workspace_newsletter_footer_registration");
	$token->field("fck_workspace_newsletter_message_registration");
	$token->field("fck_workspace_newsletter_accepted_subject");
	$token->field("fck_workspace_newsletter_accepted_content");
	$token->field("fck_workspace_newsletter_unsubscribe_subject");
	$token->field("fck_workspace_newsletter_unsubscribe_content");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="hidden" name="op" value="save_workspace_newsletter">
<input type="hidden" name="workspace_id" value="<?php echo $workspace->fields['id']; ?>">

	<div class="dims_form_title">
		<?php echo $workspace->fields['label']; ?> &raquo;
		<?php
		echo $_DIMS['cste']['_DIMS_LABEL_WORKSPACE_MODIFY'];
		?>
	</div>
	<div class="dims_form" style="clear:both;padding:2px;">
		<p>
			<label><?php echo $_DIMS['cste']['_NEWSLETTER_CHOOSE_DOMAIN']; ?></label>
			<select class="text" name="workspace_newsletter_id_domain">
				<option value="">--</option>
				<?php
					$res=$db->query("SELECT dims_domain.*
									FROM dims_domain
									INNER JOIN dims_workspace_domain
									ON dims_workspace_domain.id_domain=dims_domain.id
									AND id_workspace= :workspaceid
									AND (dims_workspace_domain.access=1 or dims_workspace_domain.access=2)",
									array(':workspaceid' => $workspace->fields['id']) );
					while($tab_dom = $db->fetchrow($res)) {
						$sel = '';
						if($workspace->fields['newsletter_id_domain'] == $tab_dom['id']) $sel = 'selected="selected"';
						echo '<option value="'.$tab_dom['id'].'" '.$sel.'>'.$tab_dom['domain'].'</option>';
					}
				?>
			</select>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_NEWSLETTER_SENDER_EMAIL']; ?></label>
			<input type="text" class="text" name="workspace_newsletter_sender_email"  value="<?php echo $workspace->fields['newsletter_sender_email']; ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_NEWSLETTER_HEADER_REGISTRATION']; ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_newsletter_header_registration"><?php echo html_entity_decode($workspace->fields['newsletter_header_registration']); ?></textarea>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_NEWSLETTER_FOOTER_REGISTRATION']; ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_newsletter_footer_registration"><?php echo html_entity_decode($workspace->fields['newsletter_footer_registration']); ?></textarea>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_NEWSLETTER_MESSAGE_REGISTRATION']; ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_newsletter_message_registration"><?php echo html_entity_decode($workspace->fields['newsletter_message_registration']); ?></textarea>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_NEWSLETTER_ACCEPTED_SUBJECT']; ?></label>
			<input type="text" class="text" name="fck_workspace_newsletter_accepted_subject"  value="<?php echo html_entity_decode($workspace->fields['newsletter_accepted_subject']); ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_NEWSLETTER_ACCEPTED_CONTENT']; ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_newsletter_accepted_content"><?php echo html_entity_decode($workspace->fields['newsletter_accepted_content']); ?></textarea>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_NEWSLETTER_UNSUBSCRIBE_SUBJECT']; ?></label>
			<input type="text" class="text" name="fck_workspace_newsletter_unsubscribe_subject"  value="<?php echo html_entity_decode($workspace->fields['newsletter_unsubscribe_subject']); ?>">
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_NEWSLETTER_UNSUBSCRIBE_CONTENT']; ?></label>
			<textarea type="text" style="height:80px;width:60%;" class="text" name="fck_workspace_newsletter_unsubscribe_content"><?php echo html_entity_decode($workspace->fields['newsletter_unsubscribe_content']); ?></textarea>
		</p>
	</div>
	<div style="clear:both;float:right;padding:4px;">
		<input type="submit" class="flatbutton" value="<?php echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
	</div>
</form>
