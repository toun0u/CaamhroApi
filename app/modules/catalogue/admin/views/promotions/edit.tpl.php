<?php $view = view::getInstance(); ?>
<a href="<?= get_path('promotions', 'index'); ?>" class="a_h1">
	<h1>
		<img src="<?= $view->getTemplateWebPath('gfx/promos50x30.png'); ?>">
		<?= dims_constant::getVal('CATA_PROMOTIONS_MANAGEMENT'); ?>
	</h1>
</a>

<?php
$promo = $view->get('promo');


$form = new Dims\form(array(
		'name' 			=> 'f_promotion',
		'action'		=> get_path('promotions', 'save'),
		'back_name'		=> dims_constant::getVal('_DIMS_LABEL_CANCEL'),
		'back_url'		=> get_path('promotions', 'index'),
		'submit_value'		=> dims_constant::getVal('_DIMS_SAVE'),
		'continue'		=> true,
		'object'		=> $promo
	));


$form->addBlock ('informations_principales', dims_constant::getVal('MAIN_INFORMATION'), $view->getTemplatePath('promotions/main_information_block.tpl.php'));

$form->add_hidden_field(array(
		'block'			=> 'informations_principales',
		'name'			=> 'id',
		'db_field'			=> 'id'
	));

$form->add_text_field(array(
		'block'			=> 'informations_principales',
		'name'			=> 'intitule',
		'label'			=> dims_constant::getVal('_BUSINESS_FIELD_NAME'),
		'db_field'			=> 'libelle'
	));

$form->add_text_field(array(
		'block'			=> 'informations_principales',
		'name'			=> 'code_activation',
		'label'			=> dims_constant::getVal('ACTIVATION_CODE'),
		'db_field'			=> 'code'
	));


$form->add_text_field(array(
		'block'			=> 'informations_principales',
		'name'			=> 'date_debut',
		'label'			=> dims_constant::getVal('DATE_FROM'),
		'value'			=> $promo->getDateDebutFormatted()
	));

$form->add_text_field(array(
		'block'			=> 'informations_principales',
		'name'			=> 'date_fin',
		'label'			=> dims_constant::getVal('DATE_TO_THE'),
		'value'			=> $promo->getDateFinFormatted()
	));

$form->add_file_field(array(
		'block'			=> 'informations_principales',
		'name'			=> 'image',
		'label'			=> dims_constant::getVal('PICTURE_SINGULIER')
	));

$concerned_block = $form->addBlock ('articles_concernes', dims_constant::getVal('ARTICLES_CONCERNED'), $view->getTemplatePath('promotions/articles_concerned_block.tpl.php'));

$form->add_file_field(array(
	'block'			=> 'articles_concernes',
	'name'			=> 'articles_file',
	'label'			=> dims_constant::getVal('CATA_IMPORT_A_FILE')
));

$form->add_checkbox_field(array(
	'block'			=> 'articles_concernes',
	'name'			=> 'articles_keep',
	'id'			=> 'articles_keep',
	'label'			=> dims_constant::getVal('CATA_KEEP_CURRENT_ARTICLES'),
	'value'			=> 1,
	'checked'		=> true
));

$concerned_block->setForm($form);

$form->build();
