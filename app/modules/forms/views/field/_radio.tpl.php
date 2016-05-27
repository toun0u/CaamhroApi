<?php
$options = explode('||',$this->get('values'));
$f->add_select_field(array(
	'name' 						=> 'values',
	'id' 						=> 'values',
	'label'						=> $_SESSION['cste']['_FIELD_VALUES'],
	'options'					=> array_combine($options, $options),
	'additionnal_attributes' 	=> 'size="6" style="width:250px;display: inline-block;float:left;"',
));
$f->add_text_field(array(
	'id' 						=> 'edit_value',
	'label'						=> '&nbsp;',
	'additionnal_attributes' 	=> 'size="6" style="width:250px;"',
));
$f->add_hidden_field(array(
	'name' 						=> 'field_values',
	'id' 						=> 'field_values',
	'value' 					=> $this->get('values'),
));
$f->add_text_field(array(
	'name' 						=> 'field_cols',
	'db_field'					=> 'cols',
	'label'						=> $_SESSION['cste']['_FIELD_MULTICOLDISPLAY'],
	'revision' 					=> 'number',
	'additionnal_attributes' 	=> 'style="width:75px;"',
));

?>
<script type="text/javascript">
$(document).ready(function(){
	$("#values").after('&nbsp;<input type="button" id="value-up" value="+" style="width:27px;display: inline-block;" /><br />\
		&nbsp;<input type="button" id="value-down" value="-" style="width:27px;display: inline-block;" />');
	$('#edit_value').after('&nbsp;<input type="button" id="value-add" value="<?= $_SESSION['cste']['_DIMS_ADD']; ?>" style="width:auto;" />\
		<input type="button" id="value-modify" value="<?= $_SESSION['cste']['_MODIFY']; ?>" style="width:auto;" />\
		<input type="button" id="value-delete" value="<?= $_SESSION['cste']['_DELETE']; ?>" style="width:auto;" />');
	$("#values").change(function(){
		$('#edit_value').val($(this).val()).focus();
	});
	$('input[type="button"][id^="value-"]').click(function(){
		var t = $(this).attr('id').substring(6);
		switch(t){
			case 'up':
				if($("#values").val() != null){
					var selected = $("#values").find(":selected");
					var before = selected.prev();
					if (before.length > 0)
						selected.detach().insertBefore(before);
				}
				break;
			case 'down':
				if($("#values").val() != null){
					var selected = $("#values").find(":selected");
					var next = selected.next();
					if (next.length > 0)
						selected.detach().insertAfter(next);
				}
				break;
			case 'add':
				var v = jQuery.trim($('#edit_value').val());
				if(v != ''){
					$("#values").append('<option value="'+v+'">'+v+'</option>');
					$('#edit_value').val('');
				}
				break;
			case 'modify':
				var selected = $("#values").find(":selected");
				var v = jQuery.trim($('#edit_value').val());
				if(selected.length > 0 && v != ''){
					selected.val(v).html(v);
				}
				break;
			case 'delete':
				var selected = $("#values").find(":selected");
				if(selected.length > 0){
					selected.remove();
					$('#edit_value').val('');
				}
				break;
		}
		var list = new Array();
		$("#values option").each(function(){
			list.push($(this).val());
		});
		$('#field_values').val(list.join('||'));
	});
});
</script>
