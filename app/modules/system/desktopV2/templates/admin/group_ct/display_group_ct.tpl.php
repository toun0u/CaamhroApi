<?php
$types = ct_group::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid']),' ORDER BY label ');
?>
<form method="POST" action="<?= dims::getInstance()->getScriptEnv(); ?>">
	<input type="hidden" name="submenu" value="1" />
	<input type="hidden" name="mode" value="admin" />
	<input type="hidden" name="o" value="grct" />
	<input type="hidden" name="action" value="save" />
	<table cellpadding="0" cellspacing="0" class="tag-categ-obj">
		<tr class="type">
			<th><?= $_SESSION['cste']['_DIMS_LABEL']; ?></th>
			<th></th>
		</tr>
		<?php
		foreach($types as $t){
			?>
			<tr>
				<td class="label-tag" dims-data-value="<?= $t->get('id'); ?>"><?= $t->get('label'); ?></td>
				<td>
					<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=grct&action=delete&id=".$t->get('id'); ?>','<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/remove_black.png" /></a>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td>
				<input name="label" type="text" placeholder="<?= $_SESSION['cste']['_DIMS_LABEL']; ?>" />
			</td>
			<td></td>
		</tr>
	</table>
	<div class="actions" style="float:none;">
		<input type="submit" value="<?= $_SESSION['cste']['_DIMS_SAVE']; ?>" />
		<?= $_SESSION['cste']['_DIMS_OR']; ?>
		<a href="<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=grct"; ?>">
			<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
		</a>
	</div>
</form>
<script type="text/javascript">
$(document).ready(function(){
	var openedVal = "";
	// label
	$('table.tag-categ-obj td.label-tag').click(function(){
		if(!$('input',$(this)).length){
			openedVal = $(this).text();
			$(this).html('<input type="text" value="" />').addClass('opened');
			$('input',$(this)).focus().val(openedVal);
		}
	});
	$('table.tag-categ-obj td.label-tag').delegate('input','focusout',function(){
		var td = $(this).parents('td.label-tag:first'),
			val = jQuery.trim($(this).val()),
			id = td.attr('dims-data-value');
		if(val != ''){
			if(val != openedVal){
				$.ajax({
					type: "POST",
					url: "<?= dims::getInstance()->getScriptEnv(); ?>",
					data: {
						'submenu': '1',
						'mode': 'admin',
						'o': 'grct',
						'action' : 'save_label',
						'id' : id,
						'label': val,
					},
					dataType: "html",
					async: false,
				});
			}
		}else{
			val = openedVal;
		}
		td.text(val).removeClass('opened');
		openedVal = "";
	}).delegate('input','keydown',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
		}
	}).delegate('input','keyup',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
			$(this).focusout();
		}
	});
});
</script>
