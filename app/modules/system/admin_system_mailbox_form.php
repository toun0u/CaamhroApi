<?php
	if($op == 'modify_mailbox') {
		echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_MAILBOX_MODIFY']);
	} else {
		echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_DOMAIN_ADD']);
	}
?>
<table>
	<form name="form_inbox" action="<?php echo $scriptenv ?>" method="post">
    <input type="hidden" name="op" value="save_mailbox" />
    <input type="hidden" name="id_mailbox" value="<?php echo $mailBox->fields['id']; ?>" />

	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?>:&nbsp;</td>
		<td align=left><input class="text" type="text" size=20 maxlength=100 name="inbox_label" value="<?php echo $mailBox->fields['label']; ?>"></td>
	</tr>

	<tr>
		<td align=right><?php echo $_DIMS['cste']['_SERVER']; ?>:&nbsp;</td>
		<td align=left><input class="text" type="text" size=20 maxlength=100 name="inbox_server" value="<?php echo $mailBox->fields['server']; ?>"></td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_PROTOCOL'];; ?>:&nbsp;</td>
		<td align=left>	<input  <?php if($mailBox->fields['protocol'] == 'pop3' ) echo "checked='1'" ?> type="radio" name="inbox_protocol" value="pop3">pop3
						<input   <?php if($mailBox->fields['protocol'] == 'imap' ) echo "checked='1'" ?>type="radio" name="inbox_protocol" value="imap">imap
		</td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_CRYPTO'];; ?>:&nbsp;</td>
		<td align=left>
				<select class="select" name="inbox_crypto">
						<option value="">aucune</option>
						<option <?php if($mailBox->fields['crypto'] == 'tls' ) echo "selected=selected" ?> value="tls">tls</option>
						<option <?php if($mailBox->fields['crypto'] == 'ssl' ) echo "selected=selected" ?> value="ssl">ssl</option>
						<option <?php if($mailBox->fields['crypto'] == 'ssl/novalidate-cert' ) echo "selected=selected" ?> value="ssl/novalidate-cert">ssl (self-signed)</option>
				</select>
		</td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_PORT'];; ?>:&nbsp;</td>
		<td align=left><input class="text" type="text" size=5 maxlength=5 name="inbox_port" value="<?php echo $mailBox->fields['port']; ?>"></td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_LOGIN'];; ?>:&nbsp;</td>
		<td align=left><input class="text" type="text" size=20 maxlength=100 name="inbox_login" value="<?php echo $mailBox->fields['login']; ?>"></td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_PASSWORD'];; ?>:&nbsp;</td>
		<td align=left><input class="text" type="password" size=20 maxlength=100 name="inbox_password"></td>
	</tr>
	<tr>
		<td></td>
		<td align=right>
			<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
				<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_SAVE']; ?></span>
			</button>
		</td>
	</tr>
	<?
		// Sécurisation du formulaire
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op",			"save_mailbox");
		$token->field("id_mailbox",	$mailBox->fields['id']);
		$token->field("inbox_label");
		$token->field("inbox_server");
		$token->field("inbox_protocol");
		$token->field("inbox_crypto");
		$token->field("inbox_port");
		$token->field("inbox_login");
		$token->field("inbox_password");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	</form>
</table>

<?php
    echo $skin->close_simplebloc();
?>
