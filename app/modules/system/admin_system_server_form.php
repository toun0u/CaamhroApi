<?php

global $skin;

if($this->isNew()) {
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_SERVER_ADD']);
}
else {
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_SERVER_MODIFIY']);
}
?>
<table>
	<form name="form_inbox" action="<?php echo dims::getInstance()->getScriptEnv() ?>" method="post">
    <input type="hidden" name="op" value="save_server" />
    <input type="hidden" name="id_server" value="<?php echo $this->fields['id']; ?>" />

	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?>:&nbsp;</td>
		<td align=left><input class="text" type="text" size=20 maxlength=100 name="server_label" value="<?php echo $this->fields['label']; ?>"></td>
	</tr>

	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_ADDRESS']; ?>:&nbsp;</td>
		<td align=left><input class="text" type="text" size=20 maxlength=100 name="server_address" value="<?php echo $this->fields['address']; ?>"></td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_LOGIN'];; ?>:&nbsp;</td>
		<td align=left><input class="text" type="text" size=20 maxlength=100 name="server_login" value="<?php echo $this->fields['login']; ?>"></td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_PORT'];; ?>:&nbsp;</td>
		<td align=left><input class="text" type="text" size=20 maxlength=100 name="server_port" value="<?php echo $this->fields['port']; ?>"></td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_SSH_IDENTITY_FILE'];; ?>:&nbsp;</td>
		<td align=left><input class="text" type="text" size=20 maxlength=100 name="server_identity_file" value="<?php echo $this->fields['identity_file']; ?>"></td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_SSH'];; ?>:&nbsp;</td>
		<td align=left><input class="checkbox" type="checkbox" name="server_ssh" value="<?php echo dims_server::SSH_ENABLE; ?>" <?php echo ($this->fields['ssh']) ? 'checked="checked"' : ''; ?>></td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_SSL'];; ?>:&nbsp;</td>
		<td align=left><input class="checkbox" type="checkbox" name="server_ssl" value="<?php echo dims_server::SSL_ENABLE; ?>" <?php echo ($this->fields['ssl']) ? 'checked="checked"' : ''; ?>></td>
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
		$token->field("op",			"save_server");
		$token->field("id_server",	$this->fields['id']);
		$token->field("server_label");
		$token->field("server_address");
		$token->field("server_login");
		$token->field("server_port");
		$token->field("server_identity_file");
		$token->field("server_ssh");
		$token->field("server_ssl");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	</form>
</table>

<?php
    echo $skin->close_simplebloc();
?>
