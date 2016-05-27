<div style="">
<?php
$view = view::getInstance();
$elem = $view->get('sel_elem');

$langs = $view->get('languages');
$languages = "";
foreach($langs as $idLg => $lg)
	$languages .= '<option value="'.$idLg.'">'.$lg.'</option>';
$firstLg = $elem->fields['id_lang'];
$infoLg = dims_constant::getVal('_CHOOSE_LANGUAGE_TO_TRANSLATE_FAMILY');

$selbutton = dims_constant::getVal('_FORM_SELECTION');
$delbutton = dims_constant::getVal('_DIRECTORY_LEGEND_DELETE');
$url = dims::getInstance()->getScriptEnv();

$additional_js = <<< ADDITIONAL_JS
$.fn.changeBgColor = function(){
	$('#fam_color, #fam_color2, #fam_color3, #fam_color4').each(function(){
		$(this).next().css("background-color", $(this).val())
				.attr('id', 'ico_'+$(this).attr('id'));
	});
};
$('#fam_color, #fam_color2, #fam_color3, #fam_color4')
.ColorPicker({
	onBeforeShow: function () {
		$(this).ColorPickerSetColor(this.value);
		$(this).next().css("background-color",this.value);
	},
	onChange: function(hsb, hex, rgb){
		$(this).next().css("background-color",hex);
	},
	onSubmit: function(hsb, hex, rgb, el){
		$(el).next().css("background-color","#"+hex);
		$(el).val("#"+hex);
		$(el).ColorPickerHide();
	}
})
.bind('keyup', function(){
	$(this).ColorPickerSetColor(this.value);
})
.css({'width':'50px', "float": 'left'})
.after('<div></div>')
.next()
	.css({"width":"20px", "height":"20px", "margin-left": "5px", "float":"left", "cursor":"pointer", "background": "url(/common/js/colorpicker/images/select2.png) no-repeat", "background-position":"-2px -2px", "background-size": "24px 24px"})
	.click(function(){
		$(this).prev().focus().click();
	})
	.changeBgColor();
var lstCk = ['{$firstLg}'];
CKEDITOR.replace('fam_description_{$firstLg}',
	{
		customConfig : '/assets/javascripts/libs/ckeditor/ckeditor_config_simple_fr.js',
		stylesSet:'default:/common/templates/frontoffice/default/ckstyles.js',
		contentsCss:'/common/templates/frontoffice/default/ckeditorarea.css'
	});
$('div#description h3:first')
	.append('<select style="margin-left:10px;" name="lang">$languages</select>')
	.append('<span class="infos">$infoLg</span>');
$('div#description h3:first select').change(function(){
	if(lstCk.indexOf($(this).val())<0){
		CKEDITOR.replace('fam_description_'+$(this).val(),
		{
			customConfig : '/assets/javascripts/libs/ckeditor/ckeditor_config_simple_fr.js',
			stylesSet:'default:/common/templates/frontoffice/default/ckstyles.js',
			contentsCss:'/common/templates/frontoffice/default/ckeditorarea.css'
		});
		lstCk[lstCk.length] = $(this).val();
	}
	$('.fam_desc').hide();
	$('.desc_'+$(this).val()).show();
	$('.champs_libres').hide();
	$('.champs_'+$(this).val()).show();
});
$('div#arborescence input#id_parent_display')
	.after('<input type="button" value="{$selbutton}" style="width:75px;" />')
	.next()
		.click(function(){
			var id_popup = dims_openOverlayedPopup(500,150);
			dims_xmlhttprequest_todiv('{$url}','{$view->get('param_open_arbo')}&input=id_parent&id_popup='+id_popup,'','p'+id_popup);
		})
		.after('<input type="button" value="{$delbutton}" style="width:75px;" />')
		.next()
		.click(function(){
			$('div#arborescence input#id_parent_display').val('');
			$('div#arborescence input#id_parent_hidden').val('');
		});
$('#id_parent_hidden').bind('change',function(){
	var valHidden = $(this).val();
	$.ajax({
		type: "GET",
		url: "{$view->get('select_sub_fam')}",
		async: false,
		dataType : "json",
		data : {
			"fam" : valHidden
		},
		success : function(data){
			var options = "";
			for(i=1;i<=data[0];i++){
				if(data[1] == i)
					options += '<option value="'+i+'" selected=true>'+i+'</option>';
				else
					options += '<option value="'+i+'">'+i+'</option>';
			}
			$('select#fam_position').html(options);
		}
	});
});
ADDITIONAL_JS;

$form = new Dims\form(array(
		'name'              => 'properties_family',
		'action'            => $view->get('action_path'),
		'validation'        => false,
		'back_name'         => dims_constant::getVal('REINITIALISER'),
		'back_url'          => $view->get('back_path'),
		'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
		'include_actions'   => true,
		'enctype'           => true,
		'additional_js'     => $additional_js,
		'object' 			=> $elem
));

$form->add_hidden_field(array(
	'name'                  => 'lang'
	));
$form->addBlock('arborescence',dims_constant::getVal('_LABEL_TREE'));
$form->addBlock('description',dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'));

// Bloc Arborescence
$form->add_hidden_field(array(
		'name'          => 'fam_id_parent',
		'id'            => 'id_parent_hidden',
		'value'         => $elem->fields['id_parent'],
		'block'         => 'arborescence'
	));
$valDispl = "";
if($elem->fields['id_parent'] != '' && $elem->fields['id_parent'] > 0){
	$par = new cata_famille();
	$par->open($elem->fields['id_parent']);
	if($par->fields['id_parent'] == 0)
		$valDispl = dims_constant::getVal('_DOC_ROOT');
	else
		$valDispl = $par->getLabel();
}
$form->add_text_field(array(
		'id'                        => 'id_parent_display',
		'label'                     => dims_constant::getVal('_SUBFAMILY_OF'),
		'value'                     => $valDispl,
		'block'                     => 'arborescence',
		'additionnal_attributes'    => 'disabled=true style="width:250px;"'
	));
$positions = array();
for($i=1;$i<=$view->get('nb_brothers');$i++)
	$positions[$i] = $i;
$form->add_select_field(array(
		'name'          => 'fam_position',
		'id'            => 'fam_position',
		'label'         => dims_constant::getVal('_POSITION'),
		'options'       => $positions,
		'value'         => $elem->fields['position'],
		'block'         => 'arborescence'
	));

// Bloc Description
foreach($langs as $idLg => $lg){
	$form->add_text_field(array(
			'name'          => 'label_'.$idLg,
			'label'         => dims_constant::getVal('_FAMILY_NAME'),
			'value'         => $elem->getLabel($idLg),
			'block'         => 'description'
		));
	$form->add_textarea_field(array(
			'name'          => 'fck_description_'.$idLg,
			'label'         => dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'),
			'value'         => $elem->getDescription($idLg),
			'block'         => 'description',
			'id'            => 'fam_description_'.$idLg
		));
}

$display_modes = array(
	cata_famille::DISPLAY_MODE_LIST         => dims_constant::getVal('_DIMS_LIST'),
	cata_famille::DISPLAY_MODE_COMPARATOR   => dims_constant::getVal('CATA_COMPARATOR'),
	cata_famille::DISPLAY_MODE_CMS          => dims_constant::getVal('CATA_CMS')
	);
$form->add_select_field(array(
		'name'          => 'display_mode',
		'id'            => 'display_mode',
		'label'         => dims_constant::getVal('CATA_DISPLAY_MODE'),
		'options'       => $display_modes,
		'value'         => $elem->fields['display_mode'],
		'block'         => 'description'
	));

$desc_block = $form->getBlock('description');
$desc_block->setForm($form);
$desc_block->setLayout($view->getTemplatePath('familles/famille_properties_description.tpl.php'));

// Bloc champs libres
$sel_champs = $view->get('sel_champs');
if (sizeof($sel_champs)) {
	$form->add_hidden_field(array(
			'name' => 'champs_libres'
		));

	$first = true;
	foreach($langs as $id => $label){
		##Gestion des champs libres
		$classes = 'champs_libres';
		if (!$first) $classes .= ' display_none';

		$cl_block = $form->addBlock ('champslibres_'.$id, dims_constant::getVal('FREE_FIELDS'), $view->getTemplatePath('familles/champs_libres_block.tpl.php'), $classes);
		$cl_block->setForm($form);
		$first = false;
	}
}

// Bloc Propriétés d'affichage
$form->addBlock('properties',dims_constant::getVal('_DISPLAY_PROPERTIES'));

$form->add_checkbox_field(array(
		'name'          => 'fam_visible',
		'label'         => dims_constant::getVal('_DIMS_LABEL_VISIBLE'),
		'checked'       => $elem->fields['visible'],
		'value'         => 1,
		'block'         => 'properties'
	));
$form->add_text_field(array(
		'name'          => 'fam_color',
		'label'         => dims_constant::getVal('_DIMS_LABEL_COLOR')." 1",
		'id'            => 'fam_color',
		'value'         => $elem->fields['color'],
		'block'         => 'properties'
	));
$form->add_text_field(array(
		'name'          => 'fam_color2',
		'label'         => dims_constant::getVal('_DIMS_LABEL_COLOR')." 2",
		'id'            => 'fam_color2',
		'value'         => $elem->fields['color2'],
		'block'         => 'properties'
	));
$form->add_text_field(array(
		'name'          => 'fam_color3',
		'label'         => dims_constant::getVal('_DIMS_LABEL_COLOR')." 3",
		'id'            => 'fam_color3',
		'value'         => $elem->fields['color3'],
		'block'         => 'properties'
	));
$form->add_text_field(array(
		'name'          => 'fam_color4',
		'label'         => dims_constant::getVal('_DIMS_LABEL_COLOR')." 4",
		'id'            => 'fam_color4',
		'value'         => $elem->fields['color4'],
		'block'         => 'properties'
	));
$form->add_file_field(array(
		'name'          => 'bg_image',
		'label'         => dims_constant::getVal('_BACKGROUND_IMAGE'),
		'block'         => 'properties'
	));

// Meta informations
$form->addBlock ('meta_infos',dims_constant::getVal('_DIMS_LABEL_META'));

$form->add_text_field(array(
	'block'			=> 'meta_infos',
	'name'			=> 'fam_meta_title',
	'label'			=> dims_constant::getVal('_META_TITLE'),
	'db_field'		=> 'meta_title'
));

$form->add_text_field(array(
	'block'			=> 'meta_infos',
	'name'			=> 'fam_meta_description',
	'label'			=> dims_constant::getVal('_META_DESCRIPTION'),
	'db_field'		=> 'meta_description'
));

$form->build();
?>
</div>
