<?php
$categs = tag_category::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid']),' ORDER BY label ');
$lk = tag_category_object::getObjByCateg();
$lstObj = tag_category_object::getListObjectLabel();
?>
<form method="POST" action="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=admin&o=ctag&action=save" name="form_categ_tag" id="form_categ_tag">
	<table cellpadding="0" cellspacing="0" class="tag-categ-obj">
		<tr class="type">
			<td></td>
			<?php
			foreach($lstObj as $label){
				?>
				<th><?= $label; ?></th>
				<?php
			}
			?>
			<th style="border-right:1px solid #DEDEDE;"><?= $_SESSION['cste']['_ALLCHECK']; ?></th>
			<th><?= $_SESSION['cste']['_GEOGRAPHICAL_TAGS']; ?></th>
			<th><?= $_SESSION['cste']['_TEMPORAL_TAGS']; ?></th>
			<th></th>
		</tr>
		<?php
		foreach($categs as $c){
			$all = true;
			$disabled = $c->get('type_tag')!=tag_category::_TYPE_DEFAULT;
			?>
			<tr>
				<td class="label-tag" dims-data-value="<?= $c->get('id'); ?>"><?= $c->get('label'); ?></td>
				<?php
				$hasCheck = false;
				foreach($lstObj as $id => $label){
					if($disabled){
						?>
						<td><input class="tag-default" name="obj_val_<?= $c->get('id'); ?>[]" type="checkbox" value="<?= $id; ?>" disabled=true /></td>
						<?php
					}elseif(isset($lk[$c->get('id')][$id])){
						$hasCheck = true;
						?>
						<td><input class="tag-default" name="obj_val_<?= $c->get('id'); ?>[]" type="checkbox" value="<?= $id; ?>" checked=true /></td>
						<?php
					}else{
						$all = false;
						?>
						<td><input class="tag-default" name="obj_val_<?= $c->get('id'); ?>[]" type="checkbox" value="<?= $id; ?>" /></td>
						<?php
					}
				}
				?>
				<td style="border-right:1px solid #DEDEDE;">
					<input type="checkbox" class="check-all" <?= ($disabled)?'disabled=true':(($all)?'checked=true':''); ?> />
				</td>
				<td>
					<input type="checkbox" name="obj_type_tag_<?= $c->get('id'); ?>" <?= ($hasCheck||$c->get('type_tag')==tag_category::_TYPE_DURATION)?'disabled=true':(($c->get('type_tag')==tag_category::_TYPE_GEO)?'checked=true':''); ?> class="tag-geo" value="<?= tag_category::_TYPE_GEO; ?>" />
				</td>
				<td>
					<input type="checkbox" name="obj_type_tag_<?= $c->get('id'); ?>" <?= ($hasCheck||$c->get('type_tag')==tag_category::_TYPE_GEO)?'disabled=true':(($c->get('type_tag')==tag_category::_TYPE_DURATION)?'checked=true':''); ?> class="tag-tmp" value="<?= tag_category::_TYPE_DURATION; ?>" />
				</td>
				<td>
					<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=ctag&action=delete&id=".$c->get('id'); ?>','<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/remove_black.png" /></a>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td><input type="text" value="" name="new_label" rel="requis" /></td>
			<?php
			foreach($lstObj as $id => $label){
				?>
				<td><input type="checkbox" value="<?= $id; ?>" class="tag-default" name="new_obj[]" /></td>
				<?php
			}
			?>
			<td style="border-right:1px solid #DEDEDE;">
				<input type="checkbox" class="check-all" />
			</td>
			<td>
				<input type="checkbox" name="new_type_tag" class="tag-geo" value="<?= tag_category::_TYPE_GEO; ?>" />
			</td>
			<td>
				<input type="checkbox" name="new_type_tag" class="tag-tmp" value="<?= tag_category::_TYPE_DURATION; ?>" />
			</td>
			<td></td>
		</tr>
	</table>
	<div class="actions" style="float:none;">
		<input type="submit" value="<?= $_SESSION['cste']['_DIMS_SAVE']; ?>" />
		<?= $_SESSION['cste']['_DIMS_OR']; ?>
		<a href="<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=ctag"; ?>">
			<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
		</a>
	</div>
</form>
<script type="text/javascript">
$(document).ready(function(){
	$("#form_categ_tag").submit(function(event){
		if($('input[name^="new_obj"]:checked',$(this)).length){
			return (jQuery.trim($('input[name="new_label"]',$(this)).val()) != '');
		}else{
			return true;
		}
	});
	var openedVal = "";
	$('table.tag-categ-obj input.check-all').change(function(){
		if($(this).is(':checked')){
			$('input[type="checkbox"]',$(this).parents('tr:first')).attr('checked',true);
			$('input.tag-geo, input.tag-tmp',$(this).parents('tr:first')).attr({'checked':false,'disabled':true});
		}else{
			$('input[type="checkbox"]',$(this).parents('tr:first')).attr('checked',false);
			$('input.tag-geo, input.tag-tmp',$(this).parents('tr:first')).attr({'disabled':false});
		}
	});
	$('table.tag-categ-obj input.tag-default').change(function(){
		if($(this).is(':checked')){
			if(!$('input.tag-default:not(:checked)',$(this).parents('tr:first')).length){
				$('input.check-all',$(this).parents('tr:first')).attr('checked',true);
			}
			$('input.tag-geo, input.tag-tmp',$(this).parents('tr:first')).attr({'checked':false,'disabled':true});
		}else{
			if(!$('input.tag-default:checked',$(this).parents('tr:first')).length){
				$('input.tag-geo, input.tag-tmp',$(this).parents('tr:first')).attr({'disabled':false});
			}
			$('input.check-all',$(this).parents('tr:first')).attr('checked',false);
		}
	});
	$('table.tag-categ-obj input.tag-geo, input.tag-tmp').change(function(){
		if($(this).is(':checked')){
			$('input[type="checkbox"]',$(this).parents('tr:first')).attr({'checked':false,'disabled':true});
			$(this).attr({'checked':true,'disabled':false});
		}else{
			$('input[type="checkbox"]',$(this).parents('tr:first')).attr({'disabled':false});
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
						'o': 'ctag',
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