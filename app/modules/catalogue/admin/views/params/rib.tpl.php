<?php
$view = view::getInstance();
$tiers = $view->get('tiers');

$js = <<< ADD_JS

ADD_JS;

$form = new Dims\form(array(
	'name'              => 'f_rib',
	'action'            => get_path('params', 'save_rib'),
	'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
	'back_name'         => dims_constant::getVal('_DIMS_RESET'),
	'back_url'          => get_path('params', 'rib'),
	'additional_js'     => $js,
	'validation'        => true
));
$form->addBlock('default', dims_constant::getVal('CATA_BANK_INFORMATIONS'), $this->getTemplatePath('params/rib_block.tpl.php'));

$form->add_text_field(array(
	'id'        => 'tiers_bank',
	'name'      => 'tiers_bank',
	'value'     => $tiers->fields['bank'],
	'label'     => dims_constant::getVal('_BANK'),
	'mandatory' => true
));
$form->add_text_field(array(
	'id'        => 'tiers_bank_domici',
	'name'      => 'tiers_bank_domici',
	'value'     => $tiers->fields['bank_domici'],
	'label'     => dims_constant::getVal('_DOMICILE'),
	'mandatory' => true
));

$form->add_text_field(array(
	'id'        => 'tiers_rib_b',
	'name'      => 'tiers_rib_b',
	'value'     => $tiers->fields['rib_b'],
	'label'     => dims_constant::getVal('_BANK_CODE'),
	'classes'   => 'w100p',
	'mandatory' => true,
	'revision'  => 'rib_banque'
));

$form->add_text_field(array(
	'id'        => 'tiers_rib_g',
	'name'      => 'tiers_rib_g',
	'value'     => $tiers->fields['rib_g'],
	'label'     => dims_constant::getVal('_COUNTER_CODE'),
	'classes'   => 'w100p',
	'mandatory' => true,
	'revision'  => 'rib_guichet'
));

$form->add_text_field(array(
	'id'        => 'tiers_rib_c',
	'name'      => 'tiers_rib_c',
	'value'     => $tiers->fields['rib_c'],
	'label'     => dims_constant::getVal('_ACCOUNT_NUMBER'),
	'classes'   => 'w150p',
	'mandatory' => true,
	'revision'  => 'rib_compte'
));

$form->add_text_field(array(
	'id'        => 'tiers_rib_r',
	'name'      => 'tiers_rib_r',
	'value'     => $tiers->fields['rib_r'],
	'label'     => dims_constant::getVal('_RIB_KEY'),
	'classes'   => 'w50p',
	'mandatory' => true,
	'revision'  => 'rib_clef'
));

$form->add_text_field(array(
	'id'        => 'tiers_iban',
	'name'      => 'tiers_iban',
	'value'     => $tiers->fields['iban'],
	'label'     => dims_constant::getVal('_IBAN'),
	'mandatory' => true,
	'revision'  => 'iban:'.$view->get('isoCountry')
));

$form->add_text_field(array(
	'id'        => 'tiers_bics',
	'name'      => 'tiers_bics',
	'value'     => $tiers->fields['bics'],
	'label'     => dims_constant::getVal('_BIC'),
	'mandatory' => true,
	'revision'  => 'bic'
));

$form->build();
?>
