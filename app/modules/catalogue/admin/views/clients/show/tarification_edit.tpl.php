<?php
$view = view::getInstance();
$client = $view->get('client');

$additional_js = <<< ADDITIONAL_JS

ADDITIONAL_JS;

$form = new Dims\form(array(
    'name'              => 'c_tarification',
    'action'            => get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'tarification','sa'=>'save')),
    'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
    'back_url'          => get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'tarification')),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'include_actions'   => true,
    'validation'        => false,
    'additional_js'     => $additional_js,
));
$form->addBlock ('tarification', dims_constant::getVal('PRICING'), $this->getTemplatePath('clients/show/tarification_block1.tpl.php'));
$form->add_text_field(array(
    'block'         => 'tarification',
    'name'          => 'cli_escompte',
    'label'         => dims_constant::getVal('_DISCOUNT'),
    'value'         => $client->fields['escompte'],
    'classes'       => 'w300p'
));
$form->add_text_field(array(
    'block'         => 'tarification',
    'name'          => 'franco',
    'label'         => dims_constant::getVal('FRANCO_DE_PORT'),
    'value'         => $client->fields['franco'],
    'classes'       => 'w300p'
));
$form->add_text_field(array(
    'block'         => 'tarification',
    'name'          => 'cli_minimum_cde',
    'label'         => dims_constant::getVal('MINIMUM_ORDER'),
    'value'         => $client->fields['minimum_cde'],
    'classes'       => 'w300p'
));

$client_payment_means = $view->get('client_payment_means');
$means_of_payment = $view->get('means_of_payment');
$form->add_select_field(array(
    'block'                     => 'tarification',
    'name'                      => 'means_of_payment[]',
    'label'                     => dims_constant::getVal('MEANS_OF_PAYMENT'),
    'options'                   => $means_of_payment,
    'additionnal_attributes'    => 'multiple="multiple"',
    'value'                     => $client_payment_means
));

$form->addBlock ('prix_nets', dims_constant::getVal('NET_PRICES'), $this->getTemplatePath('clients/show/tarification_block2.tpl.php'));
$form->add_text_field(array(
    'block'         => 'prix_nets',
    'name'          => 'ref',
    'value'         => dims_constant::getVal('_REF_ARTICLE'),
    'classes'       => 'temp_message w300p'
));
$form->add_hidden_field(array(
    'block'         => 'prix_nets',
    'name'          => 'ref_article',
    'value'         => ""
));

$form->add_text_field(array(
    'block'         => 'prix_nets',
    'name'          => 'prix_net',
    'value'         => dims_constant::getVal('NET_PRICE_HT'),
    'classes'       => 'temp_message w80p'
));
$form->add_text_field(array(
    'block'         => 'prix_nets',
    'name'          => 'deduction',
    'value'         => dims_constant::getVal('_DEDUCTION_POURC'),
    'classes'       => 'temp_message w80p'
));
$form->add_hidden_field(array(
    'block'         => 'prix_nets',
    'name'          => 'kit_composition'
));

$form->build();
?>
