<?php $view = view::getInstance(); ?>
<a href="<?= get_path('articles', 'index'); ?>" class="a_h1">
	<h1>
		<img src="<?= $view->getTemplateWebPath('gfx/promos50x30.png'); ?>">
		<?= dims_constant::getVal('CATA_PROMOTIONS_MANAGEMENT'); ?>
	</h1>
</a>

<?php
$additional_js = <<< ADDITIONAL_JS
$("select#pays").chosen({allow_single_deselect:true});
$("select#ville").chosen({allow_single_deselect:true});
$("select#means_of_payment").chosen({allow_single_deselect:true});
$("select#niveau_select").chosen({allow_single_deselect:true});




$("#identification_client select").attr('onchange', 'javascript:document.filter_articles.submit();');
ADDITIONAL_JS;

$form = new Dims\form(array(
		'name' 				=> 'identification_client',
		'action'			=> dims::getInstance()->getScriptEnv() . '?c=clients&a=save',
		'back_name'			=> dims_constant::getVal('_DIMS_LABEL_CANCEL'),
		'back_url'			=> dims::getInstance()->getScriptEnv() . '?c=clients&a=index',
		'submit_value'		=> dims_constant::getVal('_DIMS_SAVE'),
		'continue'			=> true,
		'include_actions' 	=> true,
		'additional_js'		=> $additional_js,
	));

$form->addBlock ('informations_principales',dims_constant::getVal('MAIN_INFORMATION'),$view->getTemplatePath('promotions/main_information_block.tpl.php'));

$form->add_text_field(array(
		'block'			=> 'informations_principales',
		'name'			=> 'intitule',
		'label'			=> dims_constant::getVal('_BUSINESS_FIELD_NAME'),
	));

$form->add_text_field(array(
		'block'			=> 'informations_principales',
		'name'			=> 'code_activation',
		'label'			=> dims_constant::getVal('ACTIVATION_CODE'),
	));

$form->add_text_field(array(
		'block'			=> 'informations_principales',
		'name'			=> 'usage_unique',
		'label'			=> dims_constant::getVal('DISPOSABLE'),
	));

$form->add_checkbox_field(array(
	'block'			=> 'informations_principales',
	'name'			=> 'usage_active',
	'id'			=> 'usage_active',
	'label'			=> dims_constant::getVal('DISPOSABLE'),
	'value'			=> 1,

));

$selected_pays = $view->get('pays');
$pays = $view->get('pays');

$form->add_select_field(array(
	'block'			=> 'informations_principales',
	'name' 			=> 'pays',
	'label'			=> dims_constant::getVal('POUR'),
	'options'		=> $pays,
	'classes'		=> 'pays_select'
));

$form->add_text_field(array(
		'block'			=> 'informations_principales',
		'name'			=> 'du',
		'label'			=> dims_constant::getVal('DATE_FROM'),
	));

$form->add_text_field(array(
		'block'			=> 'informations_principales',
		'name'			=> 'au',
		'label'			=> dims_constant::getVal('DATE_TO_THE'),
	));

$form->add_text_field(array(
		'block'			=> 'informations_principales',
		'name'			=> 'default_remise',
		'label'			=> dims_constant::getVal('DEFAULT_DELIVERY'),
	));

$concerned_block = $form->addBlock ('familles_concernees',dims_constant::getVal('AFFECTED_FAMILIES'),$view->getTemplatePath('promotions/affected_families_block.tpl.php'));
$concerned_block->setForm($form);

$concerned_block = $form->addBlock ('articles_concernes',dims_constant::getVal('ARTICLES_CONCERNED'),$view->getTemplatePath('promotions/articles_concerned_block.tpl.php'));
$concerned_block->setForm($form);

$concerned_block = $form->addBlock ('resteindre_clients',dims_constant::getVal('RESTRICT_TO_CERTAIN_CUSTOMERS'),$view->getTemplatePath('promotions/restrict_customers_block.tpl.php'));
$concerned_block->setForm($form);
$form->build();


?>