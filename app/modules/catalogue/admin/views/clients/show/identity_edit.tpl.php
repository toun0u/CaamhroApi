
<script type="text/javascript">
	function pwgen() {
		var length = 6,
			charset = "abcdefghijklnopqrstuvwxyz0123456789",
			retVal = "";
		for (var i = 0, n = charset.length; i < length; ++i) {
			retVal += charset.charAt(Math.floor(Math.random() * n));
		}
		return retVal;
	}

	function generatePassword() {
		pwd = pwgen();
		$('#user_password').val(pwd);
		$('#user_password_confirmation').val(pwd);
		$('#visible_password').val(pwd);
	}
</script>

<?php

$view = view::getInstance();
$client = $view->get('client');
$user = $view->get('mainuser');
$contact = $user->getContact();


$additional_js = <<< ADDITIONAL_JS
function refreshCityOfCountry(id,idCity){
	tmpSearchOpp = null;
	if(tmpSearchOpp != null) clearInterval(tmpSearchOpp);
	$.ajax({
		type: "POST",
		url: "/admin.php",
		data: {
			'dims_op': 'desktopv2',
			'action' : 'client_refresh_city',
			'ref': idCity,
			'id': id
		},
		dataType: "text",
		async: false,
		success: function(data){
			$('#'+idCity).html(data).trigger("liszt:updated");
		},
		error: function(data){}
	});
}

var alreadyCity = false;
function addNewCity(idSelect,idCountry){
	if (!alreadyCity){
		alreadyCity = true;
		var mId = document.getElementById(idCountry).options[document.getElementById(idCountry).selectedIndex].value;
		var val = $("#"+idSelect+" div.chzn-search input").val();
		$.ajax({
			type: "POST",
			url: "./admin.php",
			data: {
				'dims_op' : 'add_new_city',
				'val': val,
				'id': mId
			},
			dataType: "json",
			success: function(data){
				if(data != null){
					$("#"+idSelect+" select").append('<option value="'+data['id']+'" selected=true>'+data['label']+'</option>').trigger("liszt:updated");
				}
				alreadyCity = false;
			},
			error: function(data){
				alreadyCity = false;
			}
		});
	}
}

$("select#company_country")
	.chosen({allow_single_deselect:true})
	.change(function(){
		if($(this).val() != '') {
			$('#city').removeAttr('disabled');
		}
		else {
			$('#city').attr('disabled','disabled');
		}
		refreshCityOfCountry($(this).val(),'city');
});

$("select#company_city").chosen({
	allow_single_deselect:true,
	no_results_text: "<div class=\"button_add_city\" style=\"float:right;color:#690;cursor:pointer;\"><img style=\"float:left;\" src=\"/modules/catalogue/admin/views/gfx/ajouter16.png\" /><div style=\"float:right;margin-top:3px;\">{$_SESSION['cste']['ADD_IT_LA']}</div></div>{$_SESSION['cste']['NO_RESULT']}"
});
$('div.button_add_city').live('click',function(){
	$(this).die('click');
	addNewCity('company_city_block','company_country');
});

$("select#user_level").chosen({allow_single_deselect:true});
ADDITIONAL_JS;


$form = new Dims\form(array(
		'name' 					=> 'f_client',
		'action'				=> get_path('clients', 'save'),
		'back_name'				=> dims_constant::getVal('_DIMS_LABEL_CANCEL'),
		'back_url'				=> get_path('clients', 'index'),
		'submit_value'			=> dims_constant::getVal('_DIMS_SAVE'),
		'continue'				=> true,
		'include_actions' 		=> true,
		'additional_js'			=> $additional_js,
	));

// Configuration du compte
$form->addBlock ('account_configuration', dims_constant::getVal('CATA_ACCOUNT_CONFIGURATION'), $this->getTemplatePath('clients/account_configuration_block.tpl.php'));

$form->add_hidden_field(array(
	'block'						=> 'account_configuration',
	'name' 						=> 'id_client',
	'value'						=> $client->get('id_client')
));

$form->add_text_field(array(
	'block'						=> 'account_configuration',
	'name' 						=> 'code_client',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_GROUP_CODE'),
	'value'						=> $client->getCode(),
	'additionnal_attributes' 	=> 'readonly="readonly"'
));

$form->add_radio_field(array(
	'block'						=> 'account_configuration',
	'id'						=> 'type_0',
	'name'						=> 'type',
	'value'						=> 0,
	'label'						=> dims_constant::getVal('CATA_LEGAL_ENTITY'),
	'checked'					=> $client->isProfessional()
	));
$form->add_radio_field(array(
	'block'						=> 'account_configuration',
	'id'						=> 'type_1',
	'name'						=> 'type',
	'value'						=> 1,
	'label'						=> dims_constant::getVal('CATA_NATURAL_PERSON'),
	'checked'					=> $client->isParticular()
	));

$form->add_checkbox_field(array(
	'block'						=> 'account_configuration',
	'name'						=> 'blocked',
	'id'						=> 'blocked',
	'label'						=> dims_constant::getVal('_DIMS_LOCKED'),
	'value'						=> 1,
	'checked'					=> $client->isBlocked()
));

$form->add_textarea_field(array(
	'block'						=> 'account_configuration',
	'name'						=> 'commentaire',
	'label'						=> dims_constant::getVal('_DIMS_COMMENTS'),
	'value'						=> $client->getCommentRaw()
));

// Informations sur la société
$form->addBlock ('info_societe',dims_constant::getVal('COMPANY_INFORMATION'),$view->getTemplatePath('clients/info_company_block.tpl.php'));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_name',
		'label'					=> dims_constant::getVal('_DIMS_LABEL_ENT_NAME'),
		'value'					=> $client->getName()
	));

// $form->add_text_field(array(
// 		'block'					=> 'info_societe',
// 		'name'					=> 'company_add_photo',
// 		'label'					=> dims_constant::getVal('_DIMS_LABEL_PHOTO'),
// 	));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_email',
		'label'					=> dims_constant::getVal('_DIMS_LABEL_EMAIL'),
		'value'					=> $client->getEmail()
	));


$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_number_siren',
		'label'					=> dims_constant::getVal('NUMBER_SIREN'),
		'value'					=> $client->getSiren()
	));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_nic',
		'label'					=> dims_constant::getVal('NIC'),
		'value'					=> $client->getNic()
	));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_ape_code',
		'label'					=> dims_constant::getVal('APE_CODE'),
		'value'					=> $client->getApe()
	));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_address_1',
		'label'					=> dims_constant::getVal('NO.,_STREET'),
		'value'					=> $client->getAddress()
	));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_address_2',
		'label'					=> dims_constant::getVal('_DIMS_LABEL_ADDRESS_2'),
		'value'					=> $client->getAddress2()
	));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_address_3',
		'label'					=> dims_constant::getVal('_DIMS_LABEL_ADDRESS_3'),
		'value'					=> $client->getAddress3()
	));

$a_countries = $view->get('a_countries');
$form->add_select_field(array(
	'block'						=> 'info_societe',
	'name' 						=> 'company_country',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_COUNTRY'),
	'options'					=> $a_countries,
	'classes'					=> 'pays_select',
	'value'						=> $client->getCountryId(),
	'empty_message'				=> dims_constant::getVal('SELECT_A_COUNTRY')
));


$form->add_text_field(array(
	'block'						=> 'info_societe',
	'name'						=> 'company_postalcode',
	'label'						=> dims_constant::getVal('ZIP___CITY'),
	'value'						=> $client->getPostalCode()
));

$a_cities = $view->get('a_cities');
$form->add_select_field(array(
	'block'						=> 'info_societe',
	'name' 						=> 'company_city',
	'options'					=> $a_cities,
	'value'						=> $client->getCityId(),
	'empty_message'				=> dims_constant::getVal('_SELECT_A_CITY')
));


// Contact / 1er utilisateur
$form->addBlock ('contact_first_user',dims_constant::getVal('CONTACT___FIRST_USER'),$view->getTemplatePath('clients/clients_contact_first_user.tpl.php'));

$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_firstname',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_FIRSTNAME'),
	'value'						=> $contact->getFirstname()
));

$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_lastname',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_NAME'),
	'value'						=> $contact->getLastname()
));


$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_email',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_EMAIL'),
	'value'						=> $contact->getEmail()
));

$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_phone',
	'label'						=> dims_constant::getVal('_DIRECTORY_PHONE'),
	'value'						=> $contact->getPhone()
));

$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_login',
	'label'						=> dims_constant::getVal('_LOGIN'),
	'value'						=> $user->getLogin()
));

$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_fax',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_FAX'),
	'value'						=> $contact->getFax()
));

$form->add_password_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_password',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_PASSWORD'),
	'additionnal_attributes' 	=> 'rev="dims_pwd"'
));

$form->add_password_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_password_confirmation',
	'label'						=> dims_constant::getVal('CONFIRM'),
	'additionnal_attributes' 	=> 'rev="dims_pwd_confirm"'
));

$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_add_photo',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_PHOTO'),
));

$levels = $view->get('levels');
$selected_level = $view->get('selected_level');
if(!empty($selected_level)){
	$form->add_select_field(array(
		'block'						=> 'contact_first_user',
		'name' 						=> 'user_level',
		'label'						=> dims_constant::getVal('_DIMS_LABEL_LEVEL'),
		'options'					=> $levels,
		'value' 					=> $selected_level
	));
}

$form->build();
