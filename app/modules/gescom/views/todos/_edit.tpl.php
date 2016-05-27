<?php
$todo = $this->get('todo');
$id_popup = $this->get('id_popup');
$return = $this->get('return');

$destinataires = $todo->getListDestinataires();
if(empty($destinataires)){
	$destinataires[$_SESSION['dims']['userid']] = $_SESSION['dims']['userid'];
}
$dest = user::find_by(array('id'=>array_keys($destinataires)),' ORDER BY firstname, lastname ');

$back_url = $return;
$closePopup = "";
if(!empty($id_popup)){
	$back_url = "javascript:void(0);\" onclick=\"javascript:dims_closeOverlayedPopup('$id_popup');";
	$closePopup = '<a class="icon-close right mt10" href="javascript:void(0);" onclick="javascript:dims_closeOverlayedPopup(\''.$id_popup.'\');"></a>';
}


$form = new Dims\form(array(
	'name' 			=> "edit_todo",
	'object'		=> $todo,
	'action'		=> Gescom\get_path(array('c'=>'todo','a'=>'save')),
	'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
	'back_name'		=> $_SESSION['cste']['_DIMS_LABEL_CANCEL'],
	'back_url'		=> $back_url,
));

$form->addBlock('default',($todo->isNew()?'Création d\'un todo':'Modification d\'un todo').$closePopup);

$form->add_hidden_field(array(
	'name' => 'id',
	'db_field' => 'id',
));

$form->add_hidden_field(array(
	'name' => 'return',
	'value' => $return,
));
$form->add_hidden_field(array(
	'name' => 'todo_id_globalobject_ref',
	'db_field' => 'id_globalobject_ref',
));

$form->add_hidden_field(array(
	'name' => 'id_go',
	'value' => $this->get('id_go'),
));

$form->add_textarea_field(array(
	'name' => 'todo_content',
	'db_field' => 'content',
	'mandatory' => true,
	'label' => 'Contenu',
	'classes' => 'w100',
));

$form->add_text_field(array(
	'name' => 'todo_date',
	'value' => ($todo->get('date')!='0000-00-00 00:00:00'&&$todo->get('date')!='')?date('d/m/Y',strtotime($todo->get('date'))):'',
	'label' => 'Échéance',
	'revision' => 'date_jj/mm/yyyy',
	'additionnal_attributes' => 'style="width:85px;"',
));

$added = "";
foreach($dest as $d){
	$added .= '<li dims-data-value="'.$d->get('id').'">'.$d->get('firstname').' '.$d->get('lastname').($d->get('id')==$_SESSION['dims']['userid']?'':'<a class="right icon-remove" href="javascript:void(0);"></a>').'</li>';
	$form->add_hidden_field(array(
		'name' => 'user_added[]',
		'id' => 'user_added_'.$d->get('id'),
		'value' => $d->get('id'),
	));
}

$form->add_text_field(array(
	'name' => 'search-user',
	'label' => 'Destinataires',
	'classes' => 'search-user',
	'dom_extension' => '<ul class="result-search-todo"></ul><ul class="added-ct-todo">'.$added.'</ul>',
	'additionnal_attributes' => 'placeholder="Rechercher un utilisateur"'
));

$form->build();
?>
<script type="text/javascript">
if(typeof(window['searchUserTodo']) == "undefined"){
	var tmpSearchTodo = null;
	window['searchUserTodo'] = function searchUserTodo(label){
		clearTimeout(tmpSearchTodo);
		var lst = [];
		$('input[id^="user_added_"]').each(function(i){
			lst[i] = $(this).val();
		});
		$.ajax({
			type: "POST",
			url: "<?= Gescom\get_path(array('c'=>'todo','a'=>'search_user')); ?>",
			data: {
				'val': label,
				'lu[]': lst
			},
			dataType: 'json',
			success: function(data){
				var cont = "";
				for(i=0;i<data.length;i++){
					cont += '<li dims-data-value="'+data[i]['id']+'">'+data[i]['firstname']+' '+data[i]['lastname']+'<a class="right icon-plus-alt" href="javascript:void(0);"></a></li>';
				}
				$('ul.result-search-todo').html(cont);
			}
		});
	}
}
$(function(){
	$('#todo_date').datepicker();
	$('input#search-user').keyup(function(e){
		if(jQuery.trim($(this).val()) != ''){
			clearTimeout(tmpSearchTodo);
			tmpSearchTodo = setTimeout('searchUserTodo("'+jQuery.trim($(this).val())+'")' , 2000);
		}
		if(e.keyCode == 13){ // enter
			e.preventDefault();
			clearTimeout(tmpSearchTodo);
			searchUserTodo(jQuery.trim($(this).val()));
		}
	}).keydown(function(e){
		if(e.keyCode == 13){ // enter
			e.preventDefault();
		}
	});
	$('ul.added-ct-todo').delegate('a.icon-remove','click',function(){
		var id = $(this).parents('li:first').attr('dims-data-value');
		$(this).parents('li:first').remove();
		$('input#user_added_'+id).remove();
		if(jQuery.trim($('input#search-user').val()) != ''){
			searchUserTodo(jQuery.trim($('input#search-user').val()));
		}
	});
	$('ul.result-search-todo').delegate('a.icon-plus-alt','click',function(){
		var elem = $(this).parents('li:first').clone(),
			id = elem.attr('dims-data-value'),
			input = $('input[id^="user_added_"]:last'),
			input2 = $('input[id^="user_added_"]:last').clone();
		$('a.icon-plus-alt',$(elem)).removeClass('icon-plus-alt').addClass('icon-remove');
		$('ul.added-ct-todo').append(elem);
		$(this).parents('li:first').remove();
		$(input2).val(id).attr('id','user_added_'+id);
		$(input).after(input2);
	});
});
</script>
