<form name="form_modify_presentation" action="<? echo $scriptenv ?>" method="POST" enctype="multipart/form-data" onsubmit="javascript:return user_validate(this)">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op", "save_user");
		$token->field("fck_user_presentation");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	<input type="hidden" name="op" value="save_user">
	<table>
		<tr>
			<td><? echo $_DIMS['cste']['_DIMS_ADD_YOUR_CV']; ?>:</td>
			<td><? dims_fckeditor("user_presentation",$user->fields['presentation'],"800","500"); ?></td>
		</tr>
		<tr>
			<td><?php echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","javascript:form_modify_presentation.submit();","enreg"); ?></td>
		</tr>
	</table>
</form>
