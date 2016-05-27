<?php
$template = $this->get('template');
$languages = $this->get('languages');
$translations = $this->get('translations');

$languages_options = "";
foreach($languages as $id_lang => $lang) {
    $languages_options .= '<option value="'.$id_lang.'">'.$lang.'</option>';
}
$languages_infos = dims_constant::getVal('_CHOOSE_LANGUAGE_TO_TRANSLATE');

$additional_js = <<< ADDITIONAL_JS
$('div#default h3:first')
	.append('<select name="lang" style="margin-left: 10px;">$languages_options</select>')
	.append('<span class="infos">$languages_infos</span>');
$('div#default h3:first select').change(function() {
	$('.template_title').hide();
	$('.title_'+$(this).val()).show();
});
ADDITIONAL_JS;
?>

<div class="params_content">
	<h2><?= ($template->isNew()) ? dims_constant::getVal('CATA_NEW_TEMPLATE') : dims_constant::getVal('CATA_TEMPLATE_EDITION'); ?></h2>

	<?php
	$form = new Dims\form(array(
		'name' 				=> 'form_template',
		'action'			=> get_path('params', 'selections_templates', array('sa' => 'save')),
		'object'			=> $template,
		'validation'		=> true,
		'back_name'			=> dims_constant::getVal('_DIMS_LABEL_CANCEL'),
		'back_url'			=> get_path('params', 'selections_templates'),
		'submit_value'		=> dims_constant::getVal('CATA_SAVE_THE_TEMPLATE'),
		'continue'			=> true,
		'additional_js' 	=> $additional_js
	));

	$form->addBlock('default', dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'), $this->getTemplatePath('params/selections_templates_edit_form.tpl'));

	if (!$template->isNew() ){
		// valeurs initiales pour les clefs primaires
		$form->add_hidden_field(array(
			'name'			=> 'id',
			'db_field'		=> 'id'
		));
	}
	// champ lang ajouté en JS pour le token validator
	$form->add_hidden_field(array(
		'name'			=> 'lang'
	));

	foreach ($languages as $id_lang => $lang) {
		$form->add_text_field(array(
			'name'				=> 'title_'.$id_lang,
			'label'				=> dims_constant::getVal('_DIMS_LABEL_TITLE'),
			'value' 			=> (isset($translations[$id_lang])) ? $translations[$id_lang]->get('title') : ''
		));
	}

	$form->add_file_field(array(
		'name'				=> 'template_doc',
		'label'				=> dims_constant::getVal('_TEMPLATE')
	));

	$form->build();
	?>
</div>
