<?php
$view = view::getInstance();
$tva = $view->get('tva');
?>

<div class="params_content">
	<h2><?= ($tva->isNew()) ? dims_constant::getVal('NEW_TAXE') : dims_constant::getVal('TAXE_EDITION'); ?></h2>
	<?php
	#Création du formulaire
	$ad_js = <<< ADDITIONAL_JS
	$("select#tva_id_pays").chosen({allow_single_deselect:true});
ADDITIONAL_JS;


	$form = new Dims\form(array(
		'name' 				=> 'form_tva',
		'action'			=> get_path('params','tva_save'),
		'object'			=> $tva,
		'validation'		=> true,
		'back_name'			=> dims_constant::getVal('_DIMS_LABEL_CANCEL'),
		'back_url'			=> get_path('params','tva_index'),
		'submit_value'		=> dims_constant::getVal('SAVE_THE_TAXE'),
		'continue'			=> true,
		'additional_js'		=> $ad_js
	));

	if( ! $tva->isNew() ){
		#valeurs initiales pour les clefs primaires
		$form->add_hidden_field(array(
			'name'			=> 'init_id_tva',
			'db_field'		=> 'id_tva'
		));
		$form->add_hidden_field(array(
			'name'			=> 'init_id_pays',
			'db_field'		=> 'id_pays'
		));
	}

	$form->add_text_field(array(
		'name'				=> 'tva_id_tva',
		'label'				=> dims_constant::getVal('_DIMS_LABEL_GROUP_CODE'),
		'db_field'			=> 'id_tva',
		'mandatory'			=> true,
		'revision'			=> 'number'
	));

	$default_country = country::findByISO('FR');
	$form->add_select_field(array(
		'name'				=> 'tva_id_pays',
		'label'				=> dims_constant::getVal('_DIMS_LABEL_COUNTRY'),
		'db_field'			=> 'id_pays',
		'mandatory'			=> true,
		'options'			=> $view->get('countries'),
		'empty_message'		=> dims_constant::getVal('SELECT_A_COUNTRY'),
		'value'				=> ($tva->isNew() && empty($tva->fields['id_pays']) && isset($default_country)) ? $default_country->get('id') : null
	));

	$form->add_text_field(array(
		'name'				=> 'tva_tx_tva',
		'label'				=> dims_constant::getVal('RATE_TVA'),
		'db_field'			=> 'tx_tva',
		'mandatory'			=> true,
		'revision'			=> 'number'
	));

	$form->build();

	?>
</div>
