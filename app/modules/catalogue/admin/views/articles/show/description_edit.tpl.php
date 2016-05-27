<?php $view = view::getInstance(); ?>

<?php
$article = $view->get('article');
$langs = $view->get('langs');

$additional_js = "
$('select#families').chosen({allow_single_deselect:true});
$('div.traduction').each(function(){
	if($(this).attr('id') != 'traduction_'+$('#pick_language').val()){
		$(this).hide();
	}
});
$('div.champs_libres').each(function(){
	if($(this).attr('id') != 'champslibres_'+$('#pick_language').val()){
		$(this).hide();
	}
});

$('#pick_language').change(function(){
	$('div.traduction').hide();
	$('#traduction_'+$(this).val()).show();
	$('div.champs_libres').hide();
	$('#champslibres_'+$(this).val()).show();
});

";

foreach($langs as $id => $label){
	$additional_js .= "
		$(document).ready(function(){
			try{
				var instance=CKEDITOR.replace('description_".$id."',
					{
						customConfig : '/assets/javascripts/libs/ckeditor/ckeditor_config_simple_fr.js',
						stylesSet:'default:/common/templates/frontoffice/default/ckstyles.js',
						contentsCss:'/common/templates/frontoffice/default/ckeditorarea.css'
					});
			}
			catch(e){

			}
		});";
}

$form = new Dims\form(array(
		'name' 				=> 'description_article',
		'object'			=> $article,
		'action'			=> get_path('articles', 'show', array('sc' => 'description', 'sa' => 'update', 'id' => $article->get('id'))),
		'back_name'			=> dims_constant::getVal('REINITIALISER'),
		'back_url'			=> get_path('articles', 'show', array('sc' => 'description', 'sa' => 'edit', 'id' => $article->get('id'))),
		'submit_value'		=> dims_constant::getVal('_DIMS_SAVE'),
		'additional_js'		=> $additional_js,
	));

$form->addBlock ('main_info',dims_constant::getVal('MAIN_INFORMATION'));
$families = $view->get('cata_familles');

$form->add_select_field(array(
	'block'						=> 'main_info',
	'name' 						=> 'families[]',
	'id'						=> 'families',
	'label'						=> dims_constant::getVal('_FAMILY'),
	'options'					=> $families,
	'value'						=> $article->getFamilles($_SESSION['dims']['currentlang']),
	'classes'					=> 'family_select',
	'additionnal_attributes'	=> 'multiple'
));

$form->add_checkbox_field(array(
	'block'			=> 'main_info',
	'name'			=> 'article_published',
	'id'			=> 'article_published',
	'db_field'		=> 'published',
	'label'			=> dims_constant::getVal('_DIMS_LABEL_ACTIVE'),
	'value'			=> 1,
));

$form->add_text_field(array(
	'block'			=> 'main_info',
	'name'			=> 'article_reference',
	'classes'		=> 'w50',
	'label'			=> dims_constant::getVal('_WCE_ARTICLE_REFERENCE'),
	'db_field'		=> 'reference',
	'mandatory'		=> true
));

$pickup = $form->addBlock ('languages','', $view->getTemplatePath('/articles/show/pick_lang_block.tpl.php') );
$pickup->setForm($form);
$form->add_select_field(array(
	'block'						=> 'languages',
	'name' 						=> 'pick_language',
	'options'					=> $langs,
	'value'						=> ($view->has('pick_language')) ? $view->get('pick_language') : cata_param::getDefaultLang()
));

$form->add_hidden_field(array(
	'block'						=> 'languages',
	'name'						=> 'fields_scope',
	'value'						=> ( $article->isFullScope() ) ? 'full' : 'family'
));

$translations = $view->get('translations');
$form->add_hidden_field(array(
		'name'			=> 'fck_translation'
	));
$form->add_hidden_field(array(
		'name'			=> 'champs_libres'
	));

foreach($langs as $id => $label){
	##Gestion de la traduction des labels et description
	$traduction = $form->addBlock('traduction_'.$id, dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'), '', 'traduction');
	$traduction->setForm($form);

	$form->add_text_field(array(
		'block'			=> 'traduction_'.$id,
		'name'			=> 'fck_translation['.$id.'][designation]',
		'id'			=> 'designation_'.$id,
		'label'			=> dims_constant::getVal('DESIGNATION'),
		'value'			=> ( ! empty($translations[$id]) ) ? $translations[$id]->fields['label'] : ''
	));

	$form->add_textarea_field(array(
		'block'			=> 'traduction_'.$id,
		'name'			=> 'fck_translation['.$id.'][description]',
		'id'			=> 'description_'.$id,
		'label'			=> dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'),
		'value'			=> ( ! empty($translations[$id]) ) ? $translations[$id]->fields['description'] : ''
	));

	##Gestion des champs libres
	$cl_block = $form->addBlock ('champslibres_'.$id, dims_constant::getVal('FREE_FIELDS'), $view->getTemplatePath('articles/show/champs_libres_block.tpl.php'), 'champs_libres');
	$cl_block->setForm($form);
}

// Meta informations
$form->addBlock ('meta_infos',dims_constant::getVal('_DIMS_LABEL_META'));

$form->add_text_field(array(
	'block'			=> 'meta_infos',
	'name'			=> 'article_meta_title',
	'label'			=> dims_constant::getVal('_META_TITLE'),
	'db_field'		=> 'meta_title'
));

$form->add_text_field(array(
	'block'			=> 'meta_infos',
	'name'			=> 'article_meta_description',
	'label'			=> dims_constant::getVal('_META_DESCRIPTION'),
	'db_field'		=> 'meta_description'
));


$form->build();

?>
<script type="text/javascript">
function selectScope(scope){
	if(scope=='full'){
		$('a.family').removeClass('selected');
		$('a.full').addClass('selected');

		$('div.families_scope').hide();
		$('div.full_scope').show();
	}
	else{
		$('a.full').removeClass('selected');
		$('a.family').addClass('selected');

		$('div.full_scope').hide();
		$('div.families_scope').show();
	}
	$('#fields_scope').val(scope);
}
</script>
