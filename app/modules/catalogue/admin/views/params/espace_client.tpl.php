<?php
$view = view::getInstance();
$espace_client = $view->get('espace_client');

$additional_js = <<< ADDITIONAL_JS
// boutons ON / OFF
$('#active_cart').buttonset();
$('#personal_informations').buttonset();
$('#wait_commandes').buttonset();
$('#history_cmd').buttonset();
$('#exceptional_orders').buttonset();
$('#bon_livraison').buttonset();
$('#remainings').buttonset();
$('#invoices').buttonset();
$('#account_statements').buttonset();
$('#saisie_rapide').buttonset();
$('#panier_type').buttonset();
$('#school_lists').buttonset();
$('#statistics').buttonset();
$('#hierarchy_validation').buttonset();

// fermeture de tous les popups
function closeAllPopups() {
    $('#popup_active_cart').fadeOut();
    $('#popup_personal_informations').fadeOut();
    $('#popup_wait_commandes').fadeOut();
    $('#popup_history_cmd').fadeOut();
    $('#popup_exceptional_orders').fadeOut();
    $('#popup_bon_livraison').fadeOut();
    $('#popup_remainings').fadeOut();
    $('#popup_invoices').fadeOut();
    $('#popup_account_statements').fadeOut();
    $('#popup_saisie_rapide').fadeOut();
    $('#popup_panier_type').fadeOut();
    $('#popup_school_lists').fadeOut();
    $('#popup_statistics').fadeOut();
    $('#popup_hierarchy_validation').fadeOut();
}

// popups info
$('#info_active_cart').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_active_cart'));
    $('#popup_active_cart').fadeToggle('fast');
});
$('#info_personal_informations').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_personal_informations'));
    $('#popup_personal_informations').fadeToggle('fast');
});
$('#info_wait_commandes').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_wait_commandes'));
    $('#popup_wait_commandes').fadeToggle('fast');
});
$('#info_history_cmd').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_history_cmd'));
    $('#popup_history_cmd').fadeToggle('fast');
});
$('#info_exceptional_orders').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_exceptional_orders'));
    $('#popup_exceptional_orders').fadeToggle('fast');
});
$('#info_bon_livraison').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_bon_livraison'));
    $('#popup_bon_livraison').fadeToggle('fast');
});
$('#info_remainings').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_remainings'));
    $('#popup_remainings').fadeToggle('fast');
});
$('#info_invoices').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_invoices'));
    $('#popup_invoices').fadeToggle('fast');
});
$('#info_account_statements').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_account_statements'));
    $('#popup_account_statements').fadeToggle('fast');
});
$('#info_saisie_rapide').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_saisie_rapide'));
    $('#popup_saisie_rapide').fadeToggle('fast');
});
$('#info_panier_type').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_panier_type'));
    $('#popup_panier_type').fadeToggle('fast');
});
$('#info_school_lists').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_school_lists'));
    $('#popup_school_lists').fadeToggle('fast');
});
$('#info_statistics').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_statistics'));
    $('#popup_statistics').fadeToggle('fast');
});
$('#info_hierarchy_validation').click(function(e) {
    closeAllPopups();
    setPopupPosition(e, $('#popup_hierarchy_validation'));
    $('#popup_hierarchy_validation').fadeToggle('fast');
});
ADDITIONAL_JS;

// formulaire
$form = new Dims\form(array(
    'name'      => 'f_espace',
    'action'    => get_path('params', 'espace_save'),
    'submit_value'  => dims_constant::getVal('_DIMS_SAVE'),
    'back_name' => dims_constant::getVal('_DIMS_RESET'),
    'back_url'  => get_path('params', 'espace'),
    'additional_js' => $additional_js
));
$form->addBlock('default', dims_constant::getVal('CATA_CUSTOMER_SPACES'), $this->getTemplatePath('params/espace_client_block.tpl.php'));

// Mon panier
$form->add_radio_field(array(
    'id'        => 'active_cart_1',
    'name'      => 'active_cart',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['active_cart']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'active_cart_0',
    'name'      => 'active_cart',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['active_cart']->getValue()
));

// Informations personnelles
$form->add_radio_field(array(
    'id'        => 'personal_informations_1',
    'name'      => 'personal_informations',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['personal_informations']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'personal_informations_0',
    'name'      => 'personal_informations',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['personal_informations']->getValue()
));

// Mise en attente des commandes
$form->add_radio_field(array(
    'id'        => 'wait_commandes_1',
    'name'      => 'wait_commandes',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['wait_commandes']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'wait_commandes_0',
    'name'      => 'wait_commandes',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['wait_commandes']->getValue()
));

// Historique des commandes
$form->add_radio_field(array(
    'id'        => 'history_cmd_1',
    'name'      => 'history_cmd',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['history_cmd']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'history_cmd_0',
    'name'      => 'history_cmd',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['history_cmd']->getValue()
));

// Commandes exceptionnelles
$form->add_radio_field(array(
    'id'        => 'exceptional_orders_1',
    'name'      => 'exceptional_orders',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['exceptional_orders']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'exceptional_orders_0',
    'name'      => 'exceptional_orders',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['exceptional_orders']->getValue()
));

// Bons de livraison
$form->add_radio_field(array(
    'id'        => 'bon_livraison_1',
    'name'      => 'bon_livraison',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['bon_livraison']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'bon_livraison_0',
    'name'      => 'bon_livraison',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['bon_livraison']->getValue()
));

// Reliquats
$form->add_radio_field(array(
    'id'        => 'remainings_1',
    'name'      => 'remainings',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['remainings']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'remainings_0',
    'name'      => 'remainings',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['remainings']->getValue()
));

// Factures
$form->add_radio_field(array(
    'id'        => 'invoices_1',
    'name'      => 'invoices',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['invoices']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'invoices_0',
    'name'      => 'invoices',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['invoices']->getValue()
));

// Extraits de compte
$form->add_radio_field(array(
    'id'        => 'account_statements_1',
    'name'      => 'account_statements',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['account_statements']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'account_statements_0',
    'name'      => 'account_statements',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['account_statements']->getValue()
));

// Saisie rapide
$form->add_radio_field(array(
    'id'        => 'saisie_rapide_1',
    'name'      => 'saisie_rapide',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['saisie_rapide']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'saisie_rapide_0',
    'name'      => 'saisie_rapide',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['saisie_rapide']->getValue()
));

// Paniers types
$form->add_radio_field(array(
    'id'        => 'panier_type_1',
    'name'      => 'panier_type',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['panier_type']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'panier_type_0',
    'name'      => 'panier_type',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['panier_type']->getValue()
));

// Listes scolaires
$form->add_radio_field(array(
    'id'        => 'school_lists_1',
    'name'      => 'school_lists',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['school_lists']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'school_lists_0',
    'name'      => 'school_lists',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['school_lists']->getValue()
));

// Statistiques
$form->add_radio_field(array(
    'id'        => 'statistics_1',
    'name'      => 'statistics',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['statistics']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'statistics_0',
    'name'      => 'statistics',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['statistics']->getValue()
));

// Validation par la hiérarchie
$form->add_radio_field(array(
    'id'        => 'hierarchy_validation_1',
    'name'      => 'hierarchy_validation',
    'value'     => 1,
    'label'     => 'On',
    'checked'   => $espace_client['hierarchy_validation']->getValue()
));
$form->add_radio_field(array(
    'id'        => 'hierarchy_validation_0',
    'name'      => 'hierarchy_validation',
    'value'     => 0,
    'label'     => 'Off',
    'checked'   => !$espace_client['hierarchy_validation']->getValue()
));

$form->build();
