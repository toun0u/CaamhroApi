<?php
echo $skin->open_simplebloc($_DIMS['cste']['_MODIFY']);

// recherche de la constante non traduite
$res=$db->query("SELECT id,value
				FROM dims_constant
				WHERE id_lang=1
				AND phpvalue NOT IN (SELECT phpvalue FROM dims_constant WHERE id_lang= :idlang ) limit 0,1",
				array(':idlang' => $_SESSION['dims']['current_adminlang'] ));
if ($db->numrows($res)>0) {
	$f=$db->fetchrow($res);
	$cstelang = new cstelang();
	$cstelang->open($f['id']);
	$frenchvalue=$f['value'];

?>
<table style="width:100%;">
	<form name="form_inbox" action="<?php echo $scriptenv ?>" method="post">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op",					"save_check_cstelang");
		$token->field("cstelang_moduletype","system");
		$token->field("cstelang_phpvalue");
		$token->field("cstelang_value");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
    <input type="hidden" name="op" value="save_check_cstelang" />
	<input type="hidden" name="cstelang_moduletype" value="system" />
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?>:&nbsp;</td>
		<td align=left><input style="width:50%;" type="text" name="cstelang_phpvalue" value="<?php echo $cstelang->fields['phpvalue']; ?>"></td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_FRENCH']; ?>:&nbsp;</td>
		<td align=left>
				<? echo $frenchvalue;?>
		</td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_TRADUCTION']; ?>:&nbsp;</td>
		<td align=left>
			<textarea style="width:50%;" id="cstelang_value" name="cstelang_value"></textarea>
		</td>
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
		$token->field("op");
		$token->field("id_lang");
		$token->field("cstelang_phpvalue");
		$token->field("cstelang_value");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	</form>
</table>
<script language="javascript">
	$('cstelang_value').focus();
</script>
<?php
}
else {
	echo "No traduction available";
}
    echo $skin->close_simplebloc();
?>
