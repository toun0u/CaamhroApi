function show_response_form(todo_id, go_object, keep_context){
	var prefix_id = '#todo_list';
	$('.ajax_form').remove();
	$(prefix_id+' #todo_'+todo_id+' #actions_'+todo_id).after('<div class="ajax_form"></div>');
	$(prefix_id+' #todo_'+todo_id+ ' div.ajax_form').load('admin.php?dims_op=todos&todo_op=loadResponseForm&id='+todo_id+'&go_object='+go_object+'&keep_context='+keep_context);
}
function validate_todo(todo_id, from, go_object, keep_context, redirect_on){
	$('.ajax_form').remove();
	var prefix_id = '#todo_list';
	if(from == 'desktop'){
		prefix_id = '#todos';
	}
	$(prefix_id+' #todo_'+todo_id+' #actions_'+todo_id).after('<div class="ajax_form"></div>');
	$(prefix_id+' #todo_'+todo_id+ ' div.ajax_form').load('admin.php?dims_op=todos&todo_op=loadValidForm&id='+todo_id+'&from='+from+'&go_object='+go_object+'&keep_context='+keep_context+'&redirect_on='+redirect_on, function(){
		if(from == 'desktop'){
			$('div#todos').animate({scrollTop: $("#valid_"+todo_id).offset().top - $('div#todos').offset().top},'slow');
		}
	});
}
