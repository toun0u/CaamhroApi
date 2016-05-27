<?php
$dims = dims::getInstance();
$view = view::getInstance();
$config = $view->get('config');
$tiers = $view->get('tiers');

$js = <<< ADD_JS
$("select#tiers_id_country")
	.chosen({allow_single_deselect:true})
	.change(function(){
		$('#city').attr('disabled',($(this).val() != '' || $(this).val() == 0));
		refreshCityOfCountry($(this).val(),'city');
	});
$("select#tiers_id_city").chosen({allow_single_deselect:true});

var search = '';

$('.add-city').live('click',function(){
	$(this).die('click');
	addNewCity('tiers_id_city','tiers_id_country');
});

var alreadyCity = false;
function addNewCity(idSelect,idCountry){
	if (!alreadyCity){
		alreadyCity = true;
		var mId = document.getElementById(idCountry).options[document.getElementById(idCountry).selectedIndex].value;
		var val = search;

		$.ajax({
			type: "POST",
			url: "./admin.php",
			data: {
				'dims_op' : 'desktopv2',
				'action' : 'add_new_city',
				'val': val,
				'id': mId
			},
			dataType: "json",
			success: function(data){
				if(data != null){
					$("#"+idSelect).append('<option value="'+data['id']+'" selected=true>'+data['label']+'</option>').trigger("liszt:updated");
				}
				alreadyCity = false;
			},
			error: function(data){
				alreadyCity = false;
			}
		});
	}
}

$(document).ready(function() {
	var tempo = null;

	if(window['refreshCityOfCountry'] == undefined){
		window['refreshCityOfCountry'] = function refreshCityOfCountry(id) {
			if ($.trim(search) != '') {
				$.ajax({
					type: 'GET',
					url: '/admin.php',
					data: {
						dims_op: 'desktopv2',
						action: 'client_refresh_city_by_label',
						id: id,
						search: search
					},
					dataType: 'text',
					async: false,
					success: function(data) {
						if (data.length) {
							$('#tiers_id_city').html(data);
							$('#tiers_id_city option[value="{$tiers->fields['id_city']}"]').attr('selected',true);
							$('#tiers_id_city').trigger("liszt:updated");
							$('#tiers_id_city_chzn div.chzn-search input:first').val(search);
						}
					}
				});
			}

			clearInterval(tempo);
			tempo = null;
		}
	}

	$('#tiers_id_city_chzn div.chzn-search input:first').keyup(function(event) {
		if (event.keyCode != null) {
			if (event.keyCode != 16 && event.keyCode != 38 && event.keyCode != 40 && event.keyCode != 39 &&
				event.keyCode != 37 && event.keyCode != 20 && event.keyCode != 17 && event.keyCode != 18 &&
				event.keyCode != 13) {
				if ($(this).val().length >= 2) {
					if (tempo != null) {
						clearInterval(tempo);
					}
					search = $('#tiers_id_city_chzn div.chzn-search input:first').val();
					tempo = setInterval("refreshCityOfCountry($('select#tiers_id_country').val());", 1200);
				}
			}
			else if (event.keyCode == 13) {
				if (tempo != null) {
					clearInterval(tempo);
					tempo = null;
				}
				search = $('#tiers_id_city_chzn div.chzn-search input:first').val();
				refreshCityOfCountry($('select#tiers_id_country').val());
			}
		}
	});
});
ADD_JS;

// formulaire
$form = new Dims\form(array(
	'name'				=> 'f_params',
	'action'			=> get_path('params', 'save_identity'),
	'submit_value'		=> dims_constant::getVal('_DIMS_SAVE'),
	'back_name'			=> dims_constant::getVal('_DIMS_RESET'),
	'back_url'			=> get_path('params', 'identity'),
	'additional_js'		=> $js
	));
$form->addBlock('identity', dims_constant::getVal('CATA_YOUR_CORPORATE_IDENTITY'), $this->getTemplatePath('params/identity_block.tpl.php'));

// Raison sociale
$form->add_text_field(array(
	'block'		=> 'identity',
	'id'		=> 'tiers_intitule',
	'name'		=> 'tiers_intitule',
	'value'		=> $tiers->fields['intitule'],
	'label'		=> dims_constant::getVal('_DIMS_LABEL_ENT_NAME')
	));

// SIREN
$form->add_text_field(array(
	'block'		=> 'identity',
	'id'		=> 'tiers_ent_siren',
	'name'		=> 'tiers_ent_siren',
	'value'		=> $tiers->fields['ent_siren'],
	'label'		=> dims_constant::getVal('CATA_COMPANY_SIREN')
	));

// NIC
$form->add_text_field(array(
	'block'		=> 'identity',
	'id'		=> 'tiers_ent_nic',
	'name'		=> 'tiers_ent_nic',
	'value'		=> $tiers->fields['ent_nic'],
	'label'		=> dims_constant::getVal('CATA_COMPANY_NIC')
	));

// Code APE
$form->add_text_field(array(
	'block'		=> 'identity',
	'id'		=> 'tiers_ent_ape',
	'name'		=> 'tiers_ent_ape',
	'value'		=> $tiers->fields['ent_ape'],
	'label'		=> dims_constant::getVal('CATA_COMPANY_APE')
	));

// Adresse
$form->add_text_field(array(
	'block'		=> 'identity',
	'id'		=> 'tiers_adresse',
	'name'		=> 'tiers_adresse',
	'value'		=> $tiers->fields['adresse'],
	'label'		=> dims_constant::getVal('_DIMS_LABEL_ADDRESS')
	));

// Adresse 2
$form->add_text_field(array(
	'block'		=> 'identity',
	'id'		=> 'tiers_adresse2',
	'name'		=> 'tiers_adresse2',
	'value'		=> $tiers->fields['adresse2'],
	'label'		=> dims_constant::getVal('_DIMS_LABEL_ADDRESS_2')
	));

// Adresse 3
$form->add_text_field(array(
	'block'		=> 'identity',
	'id'		=> 'tiers_adresse3',
	'name'		=> 'tiers_adresse3',
	'value'		=> $tiers->fields['adresse3'],
	'label'		=> dims_constant::getVal('_DIMS_LABEL_ADDRESS_3')
	));

// Pays
$form->add_select_field(array(
	'block'		=> 'identity',
	'id'		=> 'tiers_id_country',
	'name'		=> 'tiers_id_country',
	'options'	=> $this->get('lst_country'),
	'value'		=> $tiers->fields['id_country'],
	'label'		=> dims_constant::getVal('_DIMS_LABEL_COUNTRY')
	));

// Ville
$lstCity = array('dims_nan' => '');
if ($tiers->fields['id_city'] > 0) {
	include_once DIMS_APP_PATH.'modules/system/class_city.php';
	$city = new city();
	if ($city->open($tiers->fields['id_city'])) {
		$lstCity[$city->getId()] = $city->getLabel();
	}
}
$disabled = "";
$form->add_select_field(array(
	'block'		=> 'identity',
	'id'		=> 'tiers_id_city',
	'name'		=> 'tiers_id_city',
	'options'	=> $lstCity,
	'value'		=> $tiers->fields['id_city'],
	'label'		=> dims_constant::getVal('_DIMS_LABEL_CITY'),
	'additionnal_attributes' => $disabled.' style="width: 225px;"'
	));

// Code postal
$form->add_text_field(array(
	'block'		=> 'identity',
	'id'		=> 'tiers_codepostal',
	'name'		=> 'tiers_codepostal',
	'value'		=> $tiers->fields['codepostal'],
	'label'		=> dims_constant::getVal('_DIMS_LABEL_CP')
	));

// Logo
$form->add_file_field(array(
	'block'		=> 'identity',
	'id'		=> 'photo',
	'name'		=> 'photo',
	'label'		=> dims_constant::getVal('CATA_COMPANY_LOGO')
	));
$form->build();
