<?php
$categs = tag_category::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'type_tag'=>tag_category::_TYPE_DEFAULT),' ORDER BY label ');
$tags = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'type'=>tag::TYPE_DEFAULT), ' ORDER BY tag ');
?>
	<form method="POST" action="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=admin&o=tag&action=save" name="form_tag" id="form_tag">
	<table cellpadding="0" cellspacing="0" class="tag-categ-obj">
		<tr class="type">
			<td></td>
			<?php
			foreach($categs as $c){
				?>
				<th><?= $c->get('label'); ?></th>
				<?php
			}
			?>
			<th><?= ucfirst($_SESSION['cste']['NO_ELEMENT_FEMININ']); ?></th>
			<td></td>
		</tr>
		<?php
		foreach($tags as $t){
			?>
			<tr>
				<td class="label-tag" dims-data-value="<?= $t->get('id'); ?>"><?= $t->get('tag'); ?></td>
				<?php
				foreach($categs as $c){
					if($c->get('id') == $t->get('id_category')){
						?>
						<td><input name="obj_val_<?= $t->get('id'); ?>" type="radio" value="<?= $c->get('id'); ?>" checked=true /></td>
						<?php
					}else{
						$all = false;
						?>
						<td><input name="obj_val_<?= $t->get('id'); ?>" type="radio" value="<?= $c->get('id'); ?>" /></td>
						<?php
					}
				}
				?>
				<td><input name="obj_val_<?= $t->get('id'); ?>" type="radio" value="0" <?= ($t->get('id_category')==0)?'checked=true':""; ?> /></td>
				<td>
					<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=tag&action=delete&id=".$t->get('id'); ?>','<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/remove_black.png" /></a>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td><input type="text" value="" name="new_label" rel="requis" /></td>
			<?php
			foreach($categs as $c){
				?>
				<td><input type="radio" value="<?= $c->get('id'); ?>" name="new_obj" /></td>
				<?php
			}
			?>
			<td>
				<input type="radio" checked=true name="new_obj" value="0" />
			</td>
			<td></td>
		</tr>
	</table>
	<div class="actions" style="float:none;">
		<input type="submit" value="<?= $_SESSION['cste']['_DIMS_SAVE']; ?>" />
		<?= $_SESSION['cste']['_DIMS_OR']; ?>
		<a href="<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=tag"; ?>">
			<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
		</a>
	</div>
</form>
<script type="text/javascript">
$(document).ready(function(){
	var openedVal = "";
	$('table.tag-categ-obj input.check-all').change(function(){
		if($(this).is(':checked')){
			$('input[type="checkbox"]',$(this).parents('tr:first')).attr('checked',true);
		}else{
			$('input[type="checkbox"]',$(this).parents('tr:first')).attr('checked',false);
		}
	});
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
						'o': 'tag',
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
