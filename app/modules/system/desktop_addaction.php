<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<?
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_ANNOTATION']);
?>
<div style="width:100%;">
	<form action="" method="post" name="form_action">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("dims_op", "action_save");
		$token->field("action_content");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	<input type="hidden" name="dims_op" value="action_save">

	<div style="padding:2px 4px;"><textarea class="text" style="width:99%;" tabindex="1" rows="5" id="action_content" name="action_content"></textarea></div>

	<div style="padding:2px 4px;text-align:right;">
		<input type="button" onclick="javascript:dims_getelem('dims_popup').style.visibility='hidden';" value="<?php echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" class="flatbutton"/>
		<input type="submit" class="flatbutton" value="<?php echo $_SESSION['cste']['_DIMS_SAVE']; ?>">
	</div>
	</form>
	<script type="text/javascript">
		 document.form_action.action_content.focus();
	</script>
</div>
<?
	echo $skin->close_simplebloc();
?>