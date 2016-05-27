<?php
	echo $skin->open_simplebloc($_DIMS['cste']['_MODIFY']);
?>
<table>
	<form name="form_inbox" action="<?php echo $scriptenv ?>" method="post">
    <input type="hidden" name="op" value="save_lang" />
    <input type="hidden" name="id_lang" value="<?php echo $objlang->fields['id']; ?>" />

	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?>:&nbsp;</td>
		<td align=left><?php echo $objlang->fields['label']; ?></td>
	</tr>

	<tr>
		<td align=right><?php echo $_DIMS['cste']['_DIMS_LABEL_CODE_OF_CONDUCT']; ?>:&nbsp;</td>
		<td align=left>
			<?
			dims_fckeditor("lang_code_of_conduct", $objlang->fields['code_of_conduct'],"800","500");
			?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td align=right>
			<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" style="width:100%;" >
				<span class="ui-button-text"><? echo $_DIMS['cste']['_DIMS_SAVE']; ?></span>
			</button>
		</td>
	</tr>
	<?
		// Sécurisation du formulaire
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op",		"save_lang");
		$token->field("id_lang",$objlang->fields['id']);
		$token->field("fck_lang_code_of_conduct");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	</form>
</table>

<?php
    echo $skin->close_simplebloc();
?>
