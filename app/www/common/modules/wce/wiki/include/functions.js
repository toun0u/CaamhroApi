function show_response_form(todo_id){
	$('.ajax_form').remove();
	$('#todo_'+todo_id+' #actions_'+todo_id).after('<div class="ajax_form"></div>');
	$('#todo_'+todo_id+ ' div.ajax_form').load('admin.php?dims_op=wiki&op_wiki=loadResponseForm&id='+todo_id);
}
function validate_todo(todo_id, from){
	$('.ajax_form').remove();
	$('#todo_'+todo_id+' #actions_'+todo_id).after('<div class="ajax_form"></div>');
	$('#todo_'+todo_id+ ' div.ajax_form').load('admin.php?dims_op=wiki&op_wiki=loadValidForm&id='+todo_id+'&from='+from, function(){
		if(from == 'desktop'){
			$('div#todos').animate({scrollTop: $("#valid_"+todo_id).offset().top - $('div#todos').offset().top},'slow');
		}
	});

}
function importNewLang(id_article) {
    dims_showcenteredpopup("",700,150,'dims_popup');
    dims_xmlhttprequest_todiv('admin.php','dims_op=wiki&op_wiki=article_import_xml&id_article='+id_article,'','dims_popup');
}
function propertiesArticleWiki(id_article) {
    dims_showcenteredpopup("",700,150,'dims_popup');
    if( arguments[0] == null) id_article=0;
    dims_xmlhttprequest_todiv('admin.php','dims_op=wiki&op_wiki=properties_article&id_article='+id_article,'','dims_popup');
}

function changeDivTypeLink() {
    var value=$('#reference_typelink').val();
    if (value==0) {
        $('#switchUrl').css('display','block');
        $('#switchUrl').css('visibility','visible');
        $('#switchDoc').css('display','none');
        $('#switchDoc').css('visibility','hidden');
    }
    else {
        $('#switchUrl').css('display','none');
        $('#switchUrl').css('visibility','hidden');
        $('#switchDoc').css('display','block');
        $('#switchDoc').css('visibility','visible');
    }
}

function wikiSelectDoc() {
    var id_popup = dims_openOverlayedPopup(700,500);
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=doc_selectfile&mode=simple&id_popup='+id_popup,'','p'+id_popup);

}

function setDocUrl(id_doc,name,id_popup) {
    dims_closeOverlayedPopup(id_popup);
    $("#reference_id_doc_link").val(id_doc);
    var ext=name.indexOf(".", 0);
    if (ext==0) $("#reference_label").val(name.substring(0));
    else $("#reference_label").val(name.substring(0,ext));
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=wiki&op_wiki=updateDocRef&id_doc='+id_doc,'','descFileRef');
}

function initDocLink() {
    $("#id_doc_link").val(0);
    $("#descFileRef").html('');
}

function duplicateRef(event,id){
    dims_showpopup('',100,event,'click','dims_popup');
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=wiki&op_wiki=duplicate_ref&id_ref='+id,'','dims_popup');
}