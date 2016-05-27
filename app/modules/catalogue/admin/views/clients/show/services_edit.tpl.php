<?php
$view = view::getInstance();
$client = $view->get('client');
$group = $view->get('group');

$additional_js = <<< ADDITIONAL_JS

ADDITIONAL_JS;
$form = new Dims\form(array(
    'name'              => 'service',
    'action'            => get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services','sa'=>'save')),
    'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
    'back_url'          => get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services')),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'include_actions'   => true,
    'validation'        => false,
    'additional_js'     => $additional_js,
));

$form->addBlock ('default', ($group->isNew())?dims_constant::getVal('_OEUVRE_CREATION_OF_A_DEPARTMENT'):dims_constant::getVal('_OEUVRE_EDITION_OF_A_DEPARTMENT'));

$form->add_hidden_field(array(
    'name'          => 'gr_id_group',
    'id'            => 'gr_id_group',
    'value'         => $group->fields['id_group'],
));
if(!$group->isNew())
    $form->add_hidden_field(array(
        'name'          => 'grid',
        'value'         => $group->get('id'),
    ));
$form->add_hidden_field(array(
    'name'          => 'gr_id_group',
    'value'         => $group->fields['id_group'],
));

$form->add_text_field(array(
    'name'          => 'gr_label',
    'value'         => $group->fields['label'],
    'label'         => dims_constant::getVal('_DIMS_LABEL')
));

$lstAdr = array();
foreach($view->get('addresses') as $adr){
    $lstAdr[$adr->get('id')] = $adr->fields['adr1'].(($adr->fields['adr2'] != '')?" ".$adr->fields['adr2']:"").(($adr->fields['adr3'] != '')?" ".$adr->fields['adr3']:"")." - ".$adr->fields['cp']." ".$adr->getCity()->getLabel()." - ".$adr->getCountry()->getLabel();
}
$form->add_select_field(array(
    'name'          => 'id_adr',
    'value'         => $group->getIdAdr(),
    'options'       => $lstAdr,
    'label'         => dims_constant::getVal('_DELIVERY_ADDRESS')
));

$form->build();
?>
