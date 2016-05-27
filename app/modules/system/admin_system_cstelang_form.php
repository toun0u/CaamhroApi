<?php
	echo $skin->open_simplebloc($_DIMS['cste']['_MODIFY']);
?>
<table>
	<form name="form_inbox" action="<?php echo $scriptenv ?>" method="post">
    <input type="hidden" name="op" value="save_cstelang" />
    <input type="hidden" name="id_lang" value="<?php echo $cstelang->fields['id']; ?>" />
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?>:&nbsp;</td>
		<td align=left><?php echo $cstelang->fields['phpvalue']; ?></td>
	</tr>
	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_TRADUCTION']; ?>:&nbsp;</td>
		<td align=left>
			<input type="text" style="width:600px;" value="<? echo $cstelang->fields['value']; ?>">
		</td>
	</tr>
	<tr>
		<td></td>
		<td align=right>
			<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;">
				<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_SAVE']; ?></span>
			</button
		</td>
	</tr>
	<?
		// Sécurisation du formulaire
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op",		"save_cstelang");
		$token->field("id_lang",$cstelang->fields['id']);
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	</form>
</table>

<?php
    echo $skin->close_simplebloc();
?>
