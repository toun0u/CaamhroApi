<?php
$dims = dims::getInstance();
$view = view::getInstance();
$config = $view->get('config');

$additional_js = <<< ADDITIONAL_JS
// boutons ON / OFF
$('#cata_synchronized').buttonset();
$('#cata_mode_B2C').buttonset();
$('#cata_visible_not_connected').buttonset();
$('#cata_active_marques').buttonset();
$('#cata_permit_horscata').buttonset();
$('#cata_negative_stocks').buttonset();
$('#cata_show_stocks').buttonset();
$('#cata_detail_reliquats').buttonset();
$('#cata_alert_stock_mini').buttonset();
$('#cata_nav_style').buttonset();
$('#cata_base_TTC').buttonset();
$('#cart_management').buttonset();
$('#cata_default_show_families').buttonset();
$('#cata_infos_persos_editable').buttonset();
$('#cata_filters_view').buttonset();
$('#cata_edit_quotelines').buttonset();

// fermeture de tous les popups
function closeAllPopups() {
	$('#popup_cata_synchronized').fadeOut();
	$('#popup_cata_mode_B2C').fadeOut();
	$('#popup_cata_visible_not_connected').fadeOut();
	$('#popup_cata_active_marques').fadeOut();
	$('#popup_cata_base_TTC').fadeOut();
	$('#popup_cata_permit_horscata').fadeOut();
	$('#popup_cata_negative_stocks').fadeOut();
	$('#popup_cata_show_stocks').fadeOut();
	$('#popup_cata_detail_reliquats').fadeOut();
	$('#popup_cata_alert_stock_mini').fadeOut();
	$('#popup_cata_nav_style').fadeOut();
	$('#popup_cart_management').fadeOut();
	$('#popup_cata_default_show_families').fadeOut();
	$('#popup_cata_infos_persos_editable').fadeOut();
	$('#popup_cata_filters_view').fadeOut();
	$('#popup_cata_edit_quotelines').fadeOut();
	$('#popup_cata_label_pattern').fadeOut();
	$('#popup_cata_fiscal_year').fadeOut();
	$('#popup_cata_documents_template').fadeOut();
}

// popups info
$('#info_cata_synchronized').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_synchronized'));
	$('#popup_cata_synchronized').fadeToggle('fast');
});
$('#info_cata_mode_B2C').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_mode_B2C'));
	$('#popup_cata_mode_B2C').fadeToggle('fast');
});
$('#info_cata_visible_not_connected').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_visible_not_connected'));
	$('#popup_cata_visible_not_connected').fadeToggle('fast');
});
$('#info_cata_active_marques').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_active_marques'));
	$('#popup_cata_active_marques').fadeToggle('fast');
});
$('#info_cata_base_TTC').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_base_TTC'));
	$('#popup_cata_base_TTC').fadeToggle('fast');
});
$('#info_cata_permit_horscata').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_permit_horscata'));
	$('#popup_cata_permit_horscata').fadeToggle('fast');
});
$('#info_cata_negative_stocks').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_negative_stocks'));
	$('#popup_cata_negative_stocks').fadeToggle('fast');
});
$('#info_cata_show_stocks').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_show_stocks'));
	$('#popup_cata_show_stocks').fadeToggle('fast');
});
$('#info_cata_detail_reliquats').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_detail_reliquats'));
	$('#popup_cata_detail_reliquats').fadeToggle('fast');
});
$('#info_cata_alert_stock_mini').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_alert_stock_mini'));
	$('#popup_cata_alert_stock_mini').fadeToggle('fast');
});
$('#info_cata_nav_style').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_nav_style'));
	$('#popup_cata_nav_style').fadeToggle('fast');
});
$('#info_cart_management').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cart_management'));
	$('#popup_cart_management').fadeToggle('fast');
});
$('#info_cata_default_show_families').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_default_show_families'));
	$('#popup_cata_default_show_families').fadeToggle('fast');
});
$('#info_cata_infos_persos_editable').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_infos_persos_editable'));
	$('#popup_cata_infos_persos_editable').fadeToggle('fast');
});
$('#info_cata_filters_view').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_filters_view'));
	$('#popup_cata_filters_view').fadeToggle('fast');
});
$('#info_cata_edit_quotelines').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_edit_quotelines'));
	$('#popup_cata_edit_quotelines').fadeToggle('fast');
});
$('#info_cata_label_pattern').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_label_pattern'));
	$('#popup_cata_label_pattern').fadeToggle('fast');
});
$('#info_cata_fiscal_year').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_fiscal_year'));
	$('#popup_cata_fiscal_year').fadeToggle('fast');
});
$('#info_cata_documents_template').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_cata_documents_template'));
	$('#popup_cata_documents_template').fadeToggle('fast');
});
ADDITIONAL_JS;

// formulaire
$form = new Dims\form(array(
	'name'		=> 'f_params',
	'action'	=> get_path('params', 'save'),
	'submit_value'	=> 'Enregistrer',
	'back_name'	=> 'Réinitialiser',
	'back_url'	=> get_path('params', 'edit'),
	'additional_js'	=> $additional_js
	));
$form->addBlock('main_options', dims_constant::getVal('CATA_CATALOG_CONFIGURATION'), $this->getTemplatePath('params/main_options_block.tpl.php'));

// Catalogue synchronisé
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_synchronized_1',
	'name'		=> 'cata_synchronized',
	'value'		=> 1,
	'label'		=> 'On',
	'checked'	=> $config->isSynchronized()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_synchronized_0',
	'name'		=> 'cata_synchronized',
	'value'		=> 0,
	'label'		=> 'Off',
	'checked'	=> !$config->isSynchronized()
	));

// Catalogue B2C
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_mode_B2C_1',
	'name'		=> 'cata_mode_B2C',
	'value'		=> 1,
	'label'		=> 'On',
	'checked'	=> $config->modeB2C()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_mode_B2C_0',
	'name'		=> 'cata_mode_B2C',
	'value'		=> 0,
	'label'		=> 'Off',
	'checked'	=> !$config->modeB2C()
	));

// Catalogue visible si non connecté
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_visible_not_connected_1',
	'name'		=> 'cata_visible_not_connected',
	'value'		=> 1,
	'label'		=> 'On',
	'checked'	=> $config->visibleNotConnected()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_visible_not_connected_0',
	'name'		=> 'cata_visible_not_connected',
	'value'		=> 0,
	'label'		=> 'Off',
	'checked'	=> !$config->visibleNotConnected()
	));

// Gestion des marques
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_active_marques_1',
	'name'		=> 'cata_active_marques',
	'value'		=> 1,
	'label'		=> 'On',
	'checked'	=> $config->activeMarques()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_active_marques_0',
	'name'		=> 'cata_active_marques',
	'value'		=> 0,
	'label'		=> 'Off',
	'checked'	=> !$config->activeMarques()
	));

// Base d'affichage des tarifs
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_base_ttc_0',
	'name'		=> 'cata_base_ttc',
	'value'		=> 0,
	'label'		=> dims_constant::getVal('DIMS_HT'),
	'checked'	=> $config->baseHT()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_base_ttc_1',
	'name'		=> 'cata_base_ttc',
	'value'		=> 1,
	'label'		=> dims_constant::getVal('DIMS_TTC'),
	'checked'	=> $config->baseTTC()
	));

// Autoriser les commandes hors catalogue
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_permit_horscata_1',
	'name'		=> 'cata_permit_horscata',
	'value'		=> 1,
	'label'		=> 'On',
	'checked'	=> $config->permitHorscata()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_permit_horscata_0',
	'name'		=> 'cata_permit_horscata',
	'value'		=> 0,
	'label'		=> 'Off',
	'checked'	=> !$config->permitHorscata()
	));

// Activer les stocks négatifs
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_negative_stocks_1',
	'name'		=> 'cata_negative_stocks',
	'value'		=> 1,
	'label'		=> 'On',
	'checked'	=> $config->negativeStocks()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_negative_stocks_0',
	'name'		=> 'cata_negative_stocks',
	'value'		=> 0,
	'label'		=> 'Off',
	'checked'	=> !$config->negativeStocks()
	));

// Afficher la qté en stock
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_show_stocks_1',
	'name'		=> 'cata_show_stocks',
	'value'		=> 1,
	'label'		=> 'On',
	'checked'	=> $config->showStocks()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_show_stocks_0',
	'name'		=> 'cata_show_stocks',
	'value'		=> 0,
	'label'		=> 'Off',
	'checked'	=> !$config->showStocks()
	));

// Détailler les reliquats
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_detail_reliquats_1',
	'name'		=> 'cata_detail_reliquats',
	'value'		=> 1,
	'label'		=> 'On',
	'checked'	=> $config->detailReliquats()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_detail_reliquats_0',
	'name'		=> 'cata_detail_reliquats',
	'value'		=> 0,
	'label'		=> 'Off',
	'checked'	=> !$config->detailReliquats()
	));

// Alerter si stock mini atteint
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_alert_stock_mini_1',
	'name'		=> 'cata_alert_stock_mini',
	'value'		=> 1,
	'label'		=> 'On',
	'checked'	=> $config->alertStockMini()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_alert_stock_mini_0',
	'name'		=> 'cata_alert_stock_mini',
	'value'		=> 0,
	'label'		=> 'Off',
	'checked'	=> !$config->alertStockMini()
	));

// Affichage des familles
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_nav_style_finder',
	'name'		=> 'cata_nav_style',
	'value'		=> 'finder',
	'label'		=> dims_constant::getVal('DIMS_HORIZONTAL'),
	'checked'	=> $config->navStyleHorizontal()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_nav_style_arbo',
	'name'		=> 'cata_nav_style',
	'value'		=> 'arbo',
	'label'		=> dims_constant::getVal('DIMS_VERTICAL'),
	'checked'	=> $config->navStyleVertical()
	));

// Gestion du panier
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cart_management_0',
	'name'		=> 'cart_management',
	'value'		=> 'bdd',
	'label'		=> 'BDD',
	'checked'	=> $config->getCartManagement() == 'bdd'
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cart_management_1',
	'name'		=> 'cart_management',
	'value'		=> 'cookie',
	'label'		=> 'Cookie',
	'checked'	=> $config->getCartManagement() == 'cookie'
	));

// Afficher les familles par défaut
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_default_show_families_0',
	'name'		=> 'cata_default_show_families',
	'value'		=> 0,
	'label'		=> 'Off',
	'checked'	=> !$config->getDefaultShowFamilies()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_default_show_families_1',
	'name'		=> 'cata_default_show_families',
	'value'		=> 1,
	'label'		=> 'On',
	'checked'	=> $config->getDefaultShowFamilies()
	));

// Permettre de modifier les informations personnelles
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_infos_persos_editable_0',
	'name'		=> 'cata_infos_persos_editable',
	'value'		=> 0,
	'label'		=> 'Off',
	'checked'	=> !$config->getInfosPersosEditable()
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_infos_persos_editable_1',
	'name'		=> 'cata_infos_persos_editable',
	'value'		=> 1,
	'label'		=> 'On',
	'checked'	=> $config->getInfosPersosEditable()
	));

// Mode de fonctionnement des filtres
// Vue filtrée ou globale
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_filters_view_0',
	'name'		=> 'cata_filters_view',
	'value'		=> 'filtered',
	'label'		=> dims_constant::getVal('CATA_FILTERS_VIEW_FILTERED'),
	'checked'	=> $config->getFiltersView() == 'filtered'
	));
$form->add_radio_field(array(
	'block'		=> 'main_options',
	'id'		=> 'cata_filters_view_1',
	'name'		=> 'cata_filters_view',
	'value'		=> 'global',
	'label'		=> dims_constant::getVal('CATA_FILTERS_VIEW_GLOBAL'),
	'checked'	=> $config->getFiltersView() == 'global'
	));

$form->add_radio_field(array(
	'block'     => 'main_options',
	'id'        => 'cata_edit_quotelines_0',
	'name'      => 'cata_edit_quotelines',
	'value'     => '0',
	'label'     => 'Off',
	'checked'   => !$config->geteditquotelines(),
));
$form->add_radio_field(array(
	'block'     => 'main_options',
	'id'        => 'cata_edit_quotelines_1',
	'name'      => 'cata_edit_quotelines',
	'value'     => '1',
	'label'     => 'On',
	'checked'   => $config->geteditquotelines(),
));
$form->add_text_field(array(
	'block'     => 'main_options',
	'name'        => 'cata_label_pattern',
	'value'     => $config->getlabelpattern(),
	'classes'   => 'w80p',
));
$form->add_text_field(array(
	'block'     => 'main_options',
	'name'      => 'cata_fiscal_year',
	'value'     => $config->getfiscalyear(),
	'classes'   => 'w80p',
));

$form->add_file_field(array(
	'block'		=> 'main_options',
	'name'		=> 'cata_documents_template',
	'value'		=> $config->getDocumentsTemplate()
	));

$form->build();
