<?php
$client         = $this->get('client');
$quotation      = $this->get('quotation');
$clientslist    = $this->get('clientslist');
$case           = $this->get('case');

$caseinitsel = '';
if(isset($case) && !$case->isNew()) {
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
						codeclient: '{$client->getCode()}',
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
	'action'            => get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'save', 'dims_mainmenu' => 'catalogue')),
	'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
	'back_url'          => (
		$quotation->isNew() ?
		get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'list', 'dims_mainmenu' => 'catalogue')) :
		get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'show', 'quotationid' => $quotation->getId(), 'dims_mainmenu' => 'catalogue'))
	),
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

$form->add_simple_text(array(
	'name'                      => 'quotation_id_client',
	'value'                     => $client->getCode().' - '.$client->get('nom'),
	'label'                     => dims_constant::getVal('CLIENT'),
	'additionnal_attributes'    => ' style="width: 100%" ',
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
