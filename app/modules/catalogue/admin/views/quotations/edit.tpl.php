<?php
$case           = $this->get('case');
$client         = $this->get('client');
$quotation      = $this->get('quotation');
$clientslist    = $this->get('clientslist');

$clientinitsel = '';
if(!$client->isNew()) {
	$clientcode     = $client->getCode();
	$clientlabel    = $clientcode.' - '.$client->get('nom');
	$clientinitsel  = <<< CLIENTINITSEL
	initSelection: function(element, callback) {
		callback({ id: '{$clientcode}', label: '{$clientlabel}' });
	},
CLIENTINITSEL;
}

$caseinitsel = '';
if(!$case->isNew()) {
	$caseid     = $case->getId();
	$caselabel  = $case->get('label');
	$caseinitsel= <<< CASEINITSEL
	initSelection: function(element, callback) {
		callback({ id: '{$caseid}', label: '{$caselabel}' });
	},
CASEINITSEL;
}

$additional_js = <<< ADDITIONAL_JS
	$(document).ready(function() {
		$('#date').datepicker();

		$('#codeclient').select2({
			minimumInputLength: 2,
			ajax: {
				url: "admin.php",
				dataType: 'json',
				data: function (term, page) {
					return {
						// FIXME : dims_mainmenu set to catalogue to avoid env mangling caused by case selector.
						dims_mainmenu: 'catalogue',
						c: 'clients',
						a: 'ac_clients',
						text: term
					}
				},
				results: function (data, page) {
					return { results: data, text: 'label'};
				},
			},
			formatSelection: function (client) {
				return client.label;
			},
			formatResult: function (client) {
				return client.label;
			},
			{$clientinitsel}
		})
		.on("change", function(e) {
			$('#quotation_id_case').select2('val', '');
		});

		$('#quotation_id_case').select2({
			minimumInputLength: 2,
			ajax: {
				url: "admin.php",
				dataType: 'json',
				data: function (term, page) {
					return {
						dims_mainmenu: 'gescom',
						c: 'dossier',
						a: 'json_cases',
						text: term,
						codeclient: $('codeclient').val(),
					}
				},
				results: function (data, page) {
					return { results: data, text: 'label'};
				},
			},
			formatSelection: function (dimscase) {
				return dimscase.label;
			},
			formatResult: function (dimscase) {
				return dimscase.label;
			},
			{$caseinitsel}
		})
	});
ADDITIONAL_JS;

$form = new Dims\form(array(
	'name'              => 'service',
	// FIXME : dims_mainmenu set to catalogue to avoid env mangling caused by case selector.
	'action'            => get_path('quotations', 'save', array('dims_mainmenu' => 'catalogue')),
	'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
	// FIXME : dims_mainmenu set to catalogue to avoid env mangling caused by case selector.
	'back_url'          => get_path('quotations', 'list', array('dims_mainmenu' => 'catalogue')),
	'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
	'include_actions'   => true,
	'validation'        => false,
	'additional_js'     => $additional_js,
));

$form->addBlock('default', ($quotation->isNew()) ? dims_constant::getVal('NEW_QUOTATION') : dims_constant::getVal('EDIT_QUOTATION'));

$form->add_hidden_field(array(
	'name'          => 'quotationid',
	'value'         => $quotation->get('id'),
));

$form->add_text_field(array(
	'name'                      => 'codeclient',
	'value'                     => $client->getCode(),
	'label'                     => dims_constant::getVal('CLIENT'),
	'additionnal_attributes'    => 'style="width: 100%"',
));

$localdate = array('date' => date('d/m/Y'), 'time' => '');
if($quotation->get('date_cree') > 0) {
	$localdate = dims_timestamp2local($quotation->get('date_cree'));
}

$form->add_text_field(array(
	'name'                      => 'date',
	'value'                     => $localdate['date'],
	'label'                     => dims_constant::getVal('_DIMS_DATE'),
	'mandatory'                 => true,
	'additionnal_attributes'    => 'autocomplete="off"',
));

$form->add_text_field(array(
	'name'          => 'quotation_libelle',
	'value'         => (
		$quotation->isNew() ?
		cata_facture::generatelabel(cata_facture::TYPE_QUOTATION) :
		$quotation->get('libelle')
	),
	'label'         => dims_constant::getVal('_DIMS_LABEL'),
	'mandatory'     => true,
));

$form->add_text_field(array(
	'name'          => 'quotation_discount',
	'value'         => $quotation->get('discount'),
	'label'         => dims_constant::getVal('_DISCOUNT'),
));

$form->add_textarea_field(array(
	'name'          => 'quotation_commentaire',
	'value'         => $quotation->get('commentaire'),
	'label'         => dims_constant::getVal('REMARK'),
));

$form->build();
