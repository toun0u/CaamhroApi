<?php
$idForm = 'edit_doc_'.$this->get('id');
$img_add = _DESKTOP_TPL_PATH;

$fold = docfolder::find_by(array('id'=>$this->get('id_folder')),null,1);
$idFold = 0;
$optionsFold = array();
if(!empty($fold)){
	if($fold->get('id_folder') != '' && $fold->get('id_folder') > 0){
		$folders = docfolder::find_by(array('id_folder'=>$fold->get('id_folder')),' ORDER BY name ');
		$idFold = $fold->get('id_folder');
	}else{
		$folders = docfolder::find_by(array('id_folder'=>$fold->get('id')),' ORDER BY name ');
		$idFold = $fold->get('id');
	}
	$optionsFold[$idFold] = "";
	foreach($folders as $fold){
		$optionsFold[$fold->get('id')] = $fold->get('name');
	}
}

$lstUsed = array();
$myTags = $this->getMyTags();
foreach($myTags as $t)
	$lstUsed[$t->get('id')] = $t->get('id');
$lstCateg = tag_category::getForObject(docfile::MY_GLOBALOBJECT_CODE);
$optionsTags = array();
$addTagOptions = '<option value="0">'.$_SESSION['cste']['_UNCATEGORIZED'].'</option>';
foreach($lstCateg as $cat){
	$lstTag = $cat->getTagLink();
	if(count($lstTag)){
		$opt = array();
		foreach($lstTag as $tag){
			$opt[$tag->get('id')] = $tag->get('tag');
		}
		$optionsTags[$cat->get('label')] = $opt;
	}
	$addTagOptions .= '<option value="'.$cat->get('id').'">'.$cat->get('label').'</option>';
}
$lstTag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'id_category'=>0, 'type'=>tag::TYPE_DEFAULT),' ORDER BY tag ');
if(count($lstTag)){
	$opt = array();
	foreach($lstTag as $tag){
		$opt[$tag->get('id')] = $tag->get('tag');
	}
	$optionsTags[$_SESSION['cste']['_UNCATEGORIZED']] = $opt;
}
$typeobj = docfile::MY_GLOBALOBJECT_CODE;
$scriptenv = dims::getInstance()->getScriptEnv();

$js = <<<JS
	$('form#$idForm select#tags').chosen({width: "80%"}).parent().append('<img style="cursor:pointer;" src="$img_add/gfx/common/add.png" class="add-tag" />');
	$('form#$idForm').delegate('img.add-tag','click',function(){
		$(this).parents('tr:first').after('<tr><td></td><td><select>$addTagOptions</select><input class="label-tag-add" style="width:33%;" type="text" /><img style="cursor:pointer;" src="$img_add/gfx/contact/check16.png" class="tag-valid" /><img style="cursor:pointer;" src="$img_add/gfx/contact/croix16.png" class="tag-undo" /></td><td></td><td></td></tr>');
		$('input:last',$(this).parents('tr:first').next()).focus();
		$(this).remove();
	}).delegate('img.tag-undo','click',function(){
		$('td.value_field:first',$(this).parents('tr:first').prev()).append('<img style="cursor:pointer;" src="$img_add/gfx/common/add.png" class="add-tag" />');
		$(this).parents('tr:first').remove();
	}).delegate('img.tag-valid','click',function(){
		var sel = $('form#$idForm select#tags').val(),
			value = $('input:last',$(this).parent()).val(),
			id_cat = $('select:first',$(this).parent()).val();
		$.ajax({
			type: "POST",
			url: '$scriptenv',
			data: {
				dims_op: 'desktopv2',
				action: 'add_new_tag_categ',
				val: value,
				typeobj : $typeobj,
				id_cat: id_cat,
			},
			dataType: 'html',
			success: function(data){
				var selected = $('form#$idForm select#tags').val();
				if(selected == null) selected = new Array();
				$('form#$idForm select#tags').html(data);
				var selected2 = $('form#$idForm select#tags').val();
				$('form#$idForm select#tags').val($.merge(selected,selected2)).trigger('liszt:updated');
			},
		});
		$('td.value_field:first',$(this).parents('tr:first').prev()).append('<img style="cursor:pointer;" src="$img_add/gfx/common/add.png" class="add-tag" />');
		$(this).parents('tr:first').remove();
	}).delegate('input.label-tag-add','keydown',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
		}
	}).delegate('input.label-tag-add','keyup',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
			$('form#$idForm img.tag-valid').click();
		}
	});
	$('form#$idForm select#id_folder').chosen({width: "80%",allow_single_deselect: true}).parent().append('<a href="javascript:void(0);" class="add-directory"><img src="$img_add/gfx/common/add.png" /></a>');
	$('form#$idForm').delegate('a.add-directory','click',function(){
		var input = '	<tr><td></td><td><input type="text" style="width:150px;" class="value-directory" />\
						<a href="javascript:void(0);" class="undo-directory"><img src="$img_add/gfx/common/delete16.png" /></a>\
						<a href="javascript:void(0);" class="valid-directory"><img src="$img_add/gfx/contact/check16.png" /></a></td></tr>';
		$(this).parents('tr:first').after(input);
		$('input:last',$(this).parents('tr:first').next('tr:first')).focus();
		$(this).remove();
	}).delegate('a.undo-directory','click',function(){
		var div = $(this).parents('tr:first');
		$('td:last',$(this).parents('tr:first').prev('tr:first')).append('<a href="javascript:void(0);" class="add-directory"><img src="$img_add/gfx/common/add.png" /></a>');
		$(this).parents('tr:first').remove();
	}).delegate('a.valid-directory','click',function(){
		var div = $(this).parents('tr:first'),
			val = jQuery.trim($('input:last',div).val()),
			sel = $('select.directories',$(this).parents('tr:first').prev('tr:first'));
		if(val != ''){
			$.ajax({
				type: "POST",
		        url: "$scriptenv",
		        data: {
		            'dims_op': 'desktopv2',
		            'action': 'add_folder',
		            'id' : '$idFold',
		            'value' : val,
		        },
		        dataType: "html",
		        async: false,
		        success: function(data){
		        	var data2 = data;
		        	$(data2).find('option').attr('selected',true);
		        	sel.append(data2)
		        	var idD = $('option:last',sel).val();
		        	sel.val(idD).trigger('liszt:updated');
		        },
		        error: function(data){},
			});
		}
		$('td:last',$(this).parents('tr:first').prev('tr:first')).append('<a href="javascript:void(0);" class="add-directory"><img src="$img_add/gfx/common/add.png" /></a>');
		div.remove();
	});
JS;

$form = new Dims\form(array(
	'name' 			=> $idForm,
	'object'		=> $this,
	'action'		=> dims::getInstance()->getScriptEnv()."?submenu=1&mode=doc&action=save",
	'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
	'back_name'		=> $_SESSION['cste']['_DIMS_LABEL_CANCEL'],
	'back_url'		=> dims::getInstance()->getScriptEnv()."?submenu=1&mode=doc&action=show&id=".$this->get('id'),
	'additional_js'	=> $js,
));
$default = $form->getBlock('default');
$default->setTitle(ucfirst(strtolower($_SESSION['cste']['SHORT_EDITION']))." : ".$this->get('name'));

$form->add_file_field(array(
	'name'		=> 'file',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_FILE'],
));

$form->add_select_field(array(
	'name'						=> 'doc_id_folder',
	'id'						=> 'id_folder',
	'label' 					=> $_SESSION['cste']['_DIRECTORY'],
	'options'					=> $optionsFold,
	'db_field'					=> 'id_folder',
	'additionnal_attributes'	=> 'style="width:50%;" class="directories"',
));

$form->add_textarea_field(array(
	'name'		=> 'doc_description',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION'],
	'db_field'	=> 'description',
));

$form->add_select_field(array(
	'name'						=> 'tags[]',
	'id'						=> 'tags',
	'label' 					=> $_SESSION['cste']['_DIMS_LABEL_TAGS'],
	'options'					=> $optionsTags,
	'value'						=> $lstUsed,
	'additionnal_attributes'	=> 'multiple="multiple" style="width:50%;"',
));

$form->build();