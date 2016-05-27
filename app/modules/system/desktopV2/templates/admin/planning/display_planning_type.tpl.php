<?php
$types = activity_type::getAllTypes();
?>
<form method="POST" action="<?= dims::getInstance()->getScriptEnv(); ?>">
	<input type="hidden" name="submenu" value="1" />
	<input type="hidden" name="mode" value="admin" />
	<input type="hidden" name="o" value="planning" />
	<input type="hidden" name="action" value="save" />
	<table cellpadding="0" cellspacing="0" class="tag-categ-obj">
		<tr class="type">
			<th><?= $_SESSION['cste']['_DIMS_LABEL']; ?></th>
			<th><?= $_SESSION['cste']['_DIMS_LABEL_COLOR']; ?></th>
			<th></th>
		</tr>
		<?php
		foreach($types as $t){
			?>
			<tr>
				<td class="label-tag" dims-data-value="<?= $t->get('id'); ?>"><?= $t->get('label'); ?></td>
				<td class="color-type" style="cursor:pointer;<?= ($t->get('color')!='')?'background-color:'.$t->get('color'):''; ?>" dims-data-value="<?= $t->get('id'); ?>"><?= $t->get('color'); ?></td>
				<td>
					<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=planning&action=delete&id=".$t->get('id'); ?>','<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/remove_black.png" /></a>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td>
				<input name="label" type="text" placeholder="<?= $_SESSION['cste']['_DIMS_LABEL']; ?>" />
			</td>
			<td>
				<input name="color" class="input-color" type="text" placeholder="<?= $_SESSION['cste']['_DIMS_LABEL_COLOR']; ?>" />
			</td>
			<td></td>
		</tr>
	</table>
	<div class="actions" style="float:none;">
		<input type="submit" value="<?= $_SESSION['cste']['_DIMS_SAVE']; ?>" />
		<?= $_SESSION['cste']['_DIMS_OR']; ?>
		<a href="<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=planning"; ?>">
			<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
		</a>
	</div>
</form>
<link type="text/css" rel="stylesheet" href="./common/js/colorpicker/css/colorpicker.css" />
<script type="text/javascript" language="javascript" src="./common/js/colorpicker/js/colorpicker.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('input.input-color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).css('backgroundColor', '#'+hex).val('#'+hex);
			$(el).ColorPickerHide();
		},
		onChange: function(hsb,hex,rgb){
			$('input.input-color').css('backgroundColor', '#'+hex).val('#'+hex);
		},
	}).focus(function(){
		$(this).click();
	});
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
						'o': 'planning',
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
	// color
	var orCol = "",
		myEl = "";
	$('table.tag-categ-obj td.color-type').ColorPicker({
		onBeforeShow: function () {
			$(this).ColorPickerSetColor($(this).text());
		},
		onShow: function(colpkr){
			myEl = $(this),
			orCol = myEl.text();
		},
		onChange: function(hsb,hex,rgb){
			myEl.css('backgroundColor', '#'+hex).text('#'+hex);
		},
		onSubmit: function(hsb, hex, rgb, el) {
			orCol = '#'+hex;
			$(el).css('backgroundColor', orCol).text(orCol);
			$(el).ColorPickerHide();
			// appel ajax
			$.ajax({
				type: "POST",
				url: "<?= dims::getInstance()->getScriptEnv(); ?>",
				data: {
					'submenu': '1',
					'mode': 'admin',
					'o': 'planning',
					'action' : 'save_color',
					'id' : myEl.attr('dims-data-value'),
					'color': orCol,
				},
				dataType: "html",
				async: false,
			});
		},
		onHide: function(color){
			$(myEl).css('backgroundColor', orCol).text(orCol);
			orCol = "";
			myEl = ""
		}
	});
});
</script>
