<?php
$view = view::getInstance();
$tarifs = $view->get('tarifs');

$additional_js = <<< ADDITIONAL_JS
// boutons ON / OFF
$('#gestion_prix_net').buttonset();
$('#gestion_escompte').buttonset();

// fermeture de tous les popups
function closeAllPopups() {
    $('#popup_default_tva').fadeOut();
    $('#popup_remise_web').fadeOut();
    $('#popup_devise').fadeOut();
    $('#popup_command_mini').fadeOut();
    $('#popup_franco_port').fadeOut();
    $('#popup_gestion_prix_net').fadeOut();
    $('#popup_gestion_escompte').fadeOut();
    $('#popup_regles_remises').fadeOut();
}

// popups info
$('#info_default_tva').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_default_tva'));
    $('#popup_default_tva').fadeToggle('fast');
});
$('#info_remise_web').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_remise_web'));
    $('#popup_remise_web').fadeToggle('fast');
});
$('#info_devise').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_devise'));
    $('#popup_devise').fadeToggle('fast');
});
$('#info_command_mini').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_command_mini'));
    $('#popup_command_mini').fadeToggle('fast');
});
$('#info_franco_port').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_franco_port'));
    $('#popup_franco_port').fadeToggle('fast');
});
$('#info_gestion_prix_net').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_gestion_prix_net'));
    $('#popup_gestion_prix_net').fadeToggle('fast');
});
$('#info_gestion_escompte').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_gestion_escompte'));
    $('#popup_gestion_escompte').fadeToggle('fast');
});
$('#info_regles_remises').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_regles_remises'));
    $('#popup_regles_remises').fadeToggle('fast');
});
ADDITIONAL_JS;

// formulaire
$form = new Dims\form(array(
    'name'          => 'f_tarifs',
    'action'        => get_path('params', 'save_tarif'),
    'submit_value'  => dims_constant::getVal('_DIMS_SAVE'),
    'back_name'     => dims_constant::getVal('_DIMS_RESET'),
    'back_url'      => get_path('params', 'tarif'),
    'additional_js' => $additional_js,
    'validation'        => true
));
$form->addBlock('default', dims_constant::getVal('CATA_PRICES_MANAGEMENT'), $this->getTemplatePath('params/tarifs_block.tpl.php'));

$form->add_text_field(array(
    'id'        => 'default_tva',
    'name'      => 'default_tva',
    'value'     => $tarifs['default_tva']->getValue(),
    'label'     => dims_constant::getVal('_VAT_RATE_BY_DEFAULT'),
    'classes'   => 'w80',
    'revision'  => 'number'
));

$form->add_text_field(array(
    'id'        => 'remise_web',
    'name'      => 'remise_web',
    'value'     => $tarifs['remise_web']->getValue(),
    'label'     => dims_constant::getVal('_WEB_DISCOUNT_RATE'),
    'classes'   => 'w80',
    'revision'  => 'number'
));

$form->add_text_field(array(
    'id'        => 'devise',
    'name'      => 'devise',
    'value'     => $tarifs['devise']->getValue(),
    'label'     => dims_constant::getVal('_CURRENCY'),
    'classes'   => 'w80'
));

$form->add_text_field(array(
    'id'        => 'command_mini',
    'name'      => 'command_mini',
    'value'     => $tarifs['command_mini']->getValue(),
    'label'     => dims_constant::getVal('_MINIMUM_ORDER_VALUE_VAT')." (".$tarifs['devise']->getValue().")",
    'classes'   => 'w80',
    'revision'  => 'number'
));

$form->add_text_field(array(
    'id'        => 'franco_port',
    'name'      => 'franco_port',
    'value'     => $tarifs['franco_port']->getValue(),
    'label'     => dims_constant::getVal('FRANCO_DE_PORT')." (".$tarifs['devise']->getValue().")",
    'classes'   => 'w80',
    'revision'  => 'number'
));

$form->add_text_field(array(
    'id'        => 'supplement_hayon',
    'name'      => 'supplement_hayon',
    'value'     => $tarifs['supplement_hayon']->getValue(),
    'label'     => dims_constant::getVal('SUPPLEMENT_HAYON')." (".$tarifs['devise']->getValue().")",
    'classes'   => 'w80',
    'revision'  => 'number'
));

$form->add_radio_field(array(
    'id'        => 'gestion_prix_net_1',
    'name'      => 'gestion_prix_net',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $tarifs['gestion_prix_net']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'gestion_prix_net_0',
    'name'      => 'gestion_prix_net',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$tarifs['gestion_prix_net']->getValue()
));

$form->add_radio_field(array(
    'id'        => 'gestion_escompte_1',
    'name'      => 'gestion_escompte',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $tarifs['gestion_escompte']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'gestion_escompte_0',
    'name'      => 'gestion_escompte',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$tarifs['gestion_escompte']->getValue()
));

$form->add_select_field(array(
    'id'        => 'regles_remises',
    'name'      => 'regles_remises',
    'value'     => $tarifs['regles_remises']->getValue(),
    'options'   => cata_param::getSelReglesRemises(),
    'label'     => dims_constant::getVal('_APPLICATION_RULE_DISCOUNTS'),
    'classes'   => 'w80',
));

$form->add_file_field(array(
    'name'      => 'tarif_transporteur',
    'label'     => dims_constant::getVal('_TARIF_TRANSPORTEUR')
    ));

$form->build();
?>
